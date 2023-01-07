<?php
/**
 * Plugin Name: Ajax Posts With Category Filter
 * Plugin URI: https://github.com/HeyMehedi/ajax-posts-with-category-filter
 * Description: Ajax Posts With Category Filter
 * Author: HalalBrains
 * Author URI: https://profiles.wordpress.org/heymehedi/
 * version: 0.9
 * License: GPLv2 or later
 * Text Domain: ajax-posts-with-category-filter
 * Domain Path: /languages
 */

namespace HeyMehedi;

use WP_Query;

defined( 'ABSPATH' ) || die( 'No direct script access allowed!' );

if ( ! class_exists( 'Ajax_Posts_With_Category_Filter' ) ) {
	class Ajax_Posts_With_Category_Filter {

		private static $instance;
		public static $base_dir;
		public static $base_url;
		public static $inc_dir;
		public static $version;
		public static $author_uri;
		public static $prefix;

		public static function instance() {

			if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Ajax_Posts_With_Category_Filter ) ) {
				self::$instance = new Ajax_Posts_With_Category_Filter();
				self::$instance->init();
			}

			return self::$instance;
		}

		private function __construct() {}

		public function init() {
			add_action( 'plugins_loaded', array( $this, 'load_textdomain' ), 20 );

			self::$base_dir   = plugin_dir_path( __FILE__ );
			self::$base_url   = plugin_dir_url( __FILE__ );
			self::$inc_dir    = self::$base_dir . '/inc/';
			$data             = $this->get_data();
			self::$version    = time();
			self::$author_uri = $data['AuthorURI'];
			self::$prefix     = 'ajax-posts-with-category-filter';

			// Public Assets
			add_action( 'wp_enqueue_scripts', array( $this, 'register_scripts' ), 10 );

			add_action( 'wp_ajax_ajax_load_more', array( $this, 'ajax_load_more' ) );
			add_action( 'wp_ajax_nopriv_ajax_load_more', array( $this, 'ajax_load_more' ) );

			add_action( 'wp_ajax_ajax_sortby_category', array( $this, 'ajax_sortby_category' ) );
			add_action( 'wp_ajax_nopriv_ajax_sortby_category', array( $this, 'ajax_sortby_category' ) );

			add_shortcode( 'ajax_post_with_category_filter', array( $this, 'main' ) );
		}

		public function get_data() {
			$file_path = self::$base_dir . '/ajax-posts-with-category-filter.php';

			return get_file_data(
				$file_path,
				array(
					'Version'   => 'Version',
					'AuthorURI' => 'Author URI',
				)
			);
		}

		public function load_textdomain() {
			load_plugin_textdomain( 'ajax-posts-with-category-filter', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
		}

		public function register_scripts() {
			// Styles
			wp_register_style( 'ajax-posts-with-category-filter', self::$base_url . 'main.css', array(), self::$version );

			// Scripts
			wp_register_script( 'ajax-posts-with-category-filter', self::$base_url . 'main.js', array( 'jquery' ), self::$version, true );

			$data = array(
				'ajaxurl'     => admin_url( 'admin-ajax.php' ),
				'contextmenu' => 'Y',
				'drag'        => 'Y',
				'diskey'      => 'Y',
			);

			wp_localize_script( 'ajax-posts-with-category-filter', 'all_in_one_content_restriction_main_localize_data', apply_filters( 'all_in_one_content_restriction_main_localize_data', $data ) );
		}

		public function get_categories() {

			$args = array(
				'taxonomy'   => 'category',
				'orderby'    => 'name',
				'order'      => 'ASC',
				'fields'     => 'id=>name',
				'hide_empty' => false,
			);

			$term_query = new \WP_Term_Query( $args );

			if ( ! $term_query->terms ) {
				return;
			}

			return $term_query;
		}

		public function ajax_load_more() {
			$posts = new WP_Query( array(
				'post_type'      => 'post',
				'posts_per_page' => 6,
				'orderby'        => 'date',
				'order'          => 'DESC',
				'paged'          => $_POST['paged'],
			) );

			$response  = '';
			$max_pages = $posts->max_num_pages;

			if ( $posts->have_posts() ) {
				ob_start();
				while ( $posts->have_posts() ): $posts->the_post();
					$response .= $this->posts_html();
				endwhile;
				$output = ob_get_contents();
				ob_end_clean();
			} else {
				$response = '';
			}

			$result = array(
				'max'  => $max_pages,
				'html' => $output,
			);

			wp_send_json_success( $result );
		}

		public function ajax_sortby_category() {
			$query = array(
				'post_type'      => 'post',
				'posts_per_page' => 6,
				'orderby'        => 'date',
				'order'          => 'DESC',
				'paged'          => 1,
			) ;

			
			if ( isset( $_POST['category'] ) && 'all' !=  $_POST['category'] ) {
				$query['category__in']  = sanitize_text_field( $_POST['category'] );
			}
			
			$posts = new WP_Query( $query);
			$response  = '';
			$max_pages = $posts->max_num_pages;

			if ( $posts->have_posts() ) {
				ob_start();
				while ( $posts->have_posts() ): $posts->the_post();
					$response .= $this->posts_html();
				endwhile;
				$output = ob_get_contents();
				ob_end_clean();
			} else {
				$response = '';
			}

			$result = array(
				'max'  => $max_pages,
				'html' => $output,
			);

			wp_send_json_success( $result );
		}


		public function posts_html() {
			require self::$base_dir . 'single-post-content.php';
		}

		public function generate_category_select() {
			$categories_obj = $this->get_categories()->terms;
			$categories     = '';

			if ( $categories_obj ) {
				$categories .= '<div class="ajax-category-wrap"><p>Filter by </p><select name="ajax_category" id="ajax_category">';
				$categories .= sprintf( '<option value="%s">%s</option>', 'all', esc_html__( 'CATEGORY', 'ajax-posts-with-category-filter' ) );

				foreach ( $categories_obj as $key => $value ) {
					$categories .= sprintf( '<option value="%s">%s</option>', esc_attr( $key ), esc_html( $value ) );
				}
				$categories .= '</select></div>';
			}

			return $categories;
		}

		public function generate_posts_html() {
			$posts = new WP_Query( array(
				'post_type'      => 'post',
				'posts_per_page' => 6,
				'orderby'        => 'date',
				'order'          => 'DESC',
				'paged'          => 1,
			) );
			?>

			<?php if ( $posts->have_posts() ): ?>
				<div class="post-list">
					<?php while ( $posts->have_posts() ): $posts->the_post();?>
						<?php echo $this->posts_html(); ?>
					<?php endwhile;?>
				</div>
			<?php endif;?>

			<?php wp_reset_postdata();?>
		<?php }

		public function get_load_more_button_html() {?>
			<div class="btn__wrapper">
				<a href="#!" class="btn btn__primary" id="load-more"><?php esc_html_e( 'View More', 'ajax-posts-with-category-filter' )?> </a>
			</div>
		<?php }

		public function main() {
			wp_enqueue_style( 'ajax-posts-with-category-filter' );
			wp_enqueue_script( 'ajax-posts-with-category-filter' );
			wp_enqueue_script( 'jquery-masonry' );
			
			ob_start();
			echo '<div id="ajax-posts-with-category-filter">';
			echo $this->generate_category_select();
			$this->generate_posts_html();
			echo $this->get_load_more_button_html();
			echo '</div>';
			$output = ob_get_contents();
			ob_end_clean();

			return $output;
		}
	}

	Ajax_Posts_With_Category_Filter::instance();
}