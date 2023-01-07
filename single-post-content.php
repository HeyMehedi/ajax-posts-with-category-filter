<article id="post-<?php the_ID(); ?>" <?php post_class( 'theme-blog-each' ); ?>>

	<div class="theme-blog-card blog-grid-card">

		<?php if ( has_post_thumbnail() ): ?>

			<div class="theme-blog-card__thumbnail">

				<a href="<?php the_permalink(); ?>"><?php the_post_thumbnail( $thumb_size ); ?></a>

			</div>

		<?php endif; ?>

		<div class="theme-blog-card__details">

			<div class="theme-blog-card__content">

				<h4 class="theme-blog-card__title">

					<a href="<?php the_permalink(); ?>" class="entry-title" rel="bookmark"><?php the_title(); ?></a>

				</h4>

				<div class="theme-blog-card__summary entry-summary"><?php the_excerpt(); ?></div>

			</div>

			<div class="theme-blog-card__meta">

				<div class="theme-blog-card__meta-list">

					<ul>

						<?php if ( has_category() ): ?>

							<li class="theme-blog-category-meta">

								<?php if ( ! empty( $get_cat_ob ) ) {

									$term_link	= isset(  $get_cat_ob[0] ) ? get_category_link( $get_cat_ob[0]->cat_ID ) : '';
									$cat_name	= isset( $get_cat_ob[0] ) ? $get_cat_ob[0]->name : '';
									$total_term	= count( $get_cat_ob );

									printf( '<a href="%s"><span>%s</span> %s</a>', esc_url( $term_link ), esc_html__( 'In', 'ajax-posts-with-category-filter' ) , esc_html( $cat_name ) );

									if ( $total_term > 1 ) {
										$total_term = $total_term - 1; 
										?>

										<div class="theme-blog-category-meta__popup">

											<?php printf( '<span class="theme-blog-category-meta__extran-count">%s %s</span>', esc_html( '+', 'ajax-posts-with-category-filter' ), esc_html( $total_term ) ); ?>
											
											<div class="theme-blog-category-meta__popup__content">
												
												<?php 
												foreach ( array_slice($get_cat_ob, 1) as $cat ) {
													$term_label = trim( "{$cat->name}" );
													$term_link  = get_category_link( $cat->cat_ID);;

													printf( '<a href="%s">%s</a>', esc_url( $term_link ), esc_html( $term_label ) );
												}
												?>

											</div>

										</div>
										
										<?php
									}
								}
								?>

							</li>
							
						<?php endif; ?>

					</ul>

				</div>

			</div>

		</div>

	</div>
</article>