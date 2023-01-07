<article id="post-<?php the_ID(); ?>" <?php post_class( 'ajax-blog-each' ); ?>>

	<div class="ajax-blog-card blog-grid-card">

		<?php if ( has_post_thumbnail() ): ?>

			<div class="ajax-blog-card__thumbnail">

				<a href="<?php the_permalink(); ?>"><?php the_post_thumbnail( $thumb_size ); ?></a>

			</div>

		<?php endif; ?>

		<div class="ajax-blog-card__meta">

			<?php if ( has_category() ): ?>
				<? the_category();?>
			<?php endif; ?>

		</div>


		<div class="ajax-blog-card__details">

			<div class="ajax-blog-card__content">

				<h4 class="ajax-blog-card__title">

					<a href="<?php the_permalink(); ?>" class="entry-title" rel="bookmark"><?php the_title(); ?></a>

				</h4>

				<div class="ajax-blog-card__summary entry-summary"><?php the_excerpt(); ?></div>

			</div>

		</div>

	</div>
</article>