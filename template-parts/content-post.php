<?php
/**
 * Single blog post (publishing chrome).
 *
 * @package msrsandbox
 */

$post_id = get_the_ID();
$topics  = get_the_terms( $post_id, 'topic' );
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'resource-single post-single container py-4' ); ?>>
	<header class="resource-single__header post-single__header mb-4">
		<?php msr_publishing_render_post_topic_badges( $post_id ); ?>
		<?php if ( msr_publishing_post_is_sponsored( $post_id ) ) : ?>
			<p class="small text-muted mb-2"><em><?php esc_html_e( 'Sponsored content', 'msrsandbox' ); ?></em></p>
		<?php endif; ?>
		<?php the_title( '<h1 class="entry-title msr-reveal">', '</h1>' ); ?>
		<?php msr_publishing_render_content_byline( $post_id, true ); ?>
	</header>

	<?php msr_publishing_render_single_utility_band( $post_id ); ?>
	<?php msr_publishing_render_key_takeaways( $post_id ); ?>

	<?php get_template_part( 'templates/partials/featured-image' ); ?>

	<div class="entry-content resource-single__body post-single__body">
		<?php
		the_content(
			sprintf(
				wp_kses(
					__( 'Continue reading<span class="screen-reader-text"> "%s"</span>', 'msrsandbox' ),
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
				'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'msrsandbox' ),
				'after'  => '</div>',
			)
		);
		?>
	</div>

	<?php
	if ( $topics && ! is_wp_error( $topics ) ) :
		$primary_topic = $topics[0];
		$related       = msr_publishing_get_related_commentary_by_topic( (int) $primary_topic->term_id, $post_id );
		if ( $related->have_posts() ) :
			?>
			<section class="resource-single__related post-single__related mt-5 pt-4 border-top">
				<h2 class="h4 mb-4">
					<?php
					echo esc_html(
						sprintf(
							/* translators: %s: topic name */
							__( 'More commentary on %s', 'msrsandbox' ),
							$primary_topic->name
						)
					);
					?>
				</h2>
				<div class="row g-4">
					<?php
					while ( $related->have_posts() ) {
						$related->the_post();
						echo '<div class="col-md-6 col-lg-4">';
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
</article>
