<?php
/**
 * Single blog post — insight / commentary template (HBR-style article).
 *
 * @package msrpublishing
 */

$post_id = get_the_ID();
$topics  = get_the_terms( $post_id, 'topic' );
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'post-single post-single--insight container py-4 py-lg-5' ); ?>>
	<div class="post-single__layout post-single__layout--insight">
		<?php get_template_part( 'template-parts/post/single', 'insight-header' ); ?>

		<?php msr_publishing_render_single_utility_band( $post_id ); ?>

		<?php get_template_part( 'templates/partials/featured-image' ); ?>

		<?php msr_publishing_render_key_takeaways( $post_id ); ?>

		<div class="entry-content post-single__body post-single__body--insight">
			<?php
			the_content(
				sprintf(
					wp_kses(
						__( 'Continue reading<span class="screen-reader-text"> "%s"</span>', 'msrpublishing' ),
						array(
							'span' => array(
								'class' => array(),
							),
						)
					),
					wp_kses_post( get_the_title() )
				)
			);

			wp_link_pages(
				array(
					'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'msrpublishing' ),
					'after'  => '</div>',
				)
			);
			?>
		</div>

		<?php if ( $topics && ! is_wp_error( $topics ) ) : ?>
			<footer class="post-single__topic-footer mt-4 pt-3 border-top">
				<p class="small text-uppercase fw-semibold text-muted mb-2"><?php esc_html_e( 'Topics', 'msrpublishing' ); ?></p>
				<?php msr_publishing_render_post_topic_badges( $post_id ); ?>
			</footer>
		<?php endif; ?>

		<?php
		if ( $topics && ! is_wp_error( $topics ) ) :
			$primary_topic = $topics[0];
			$related       = msr_publishing_get_related_commentary_by_topic( (int) $primary_topic->term_id, $post_id );
			if ( $related->have_posts() ) :
				?>
				<section class="post-single__related mt-5 pt-4 border-top">
					<h2 class="h4 mb-4">
						<?php
						echo esc_html(
							sprintf(
								/* translators: %s: topic name */
								__( 'More commentary on %s', 'msrpublishing' ),
								$primary_topic->name
							)
						);
						?>
					</h2>
					<div class="row g-4">
						<?php
						while ( $related->have_posts() ) {
							$related->the_post();
							echo '<div class="col-sm-6 col-lg-4 publishing-commentary-grid__cell">';
							get_template_part( 'template-parts/cards/post', 'card' );
							echo '</div>';
						}
						wp_reset_postdata();
						?>
					</div>
				</section>
				<?php
			endif;
		endif;

		msr_publishing_render_post_resource_crosslink( $post_id );
		?>
	</div>
</article>
