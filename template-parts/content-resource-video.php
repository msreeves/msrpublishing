<?php
/**
 * Video single resource.
 *
 * @package msrsandbox
 */

$post_id      = get_the_ID();
$video_url    = function_exists( 'get_field' ) ? (string) get_field( 'video_url', $post_id ) : '';
$duration_mins = function_exists( 'get_field' ) ? (int) get_field( 'duration_minutes', $post_id ) : 0;
$embed_html   = msr_publishing_get_video_embed_html( $video_url, $post_id );
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'resource-single resource-single--video container py-4' ); ?>>
	<?php get_template_part( 'template-parts/resource/single', 'header' ); ?>
	<?php msr_publishing_render_resource_programme_cta( $post_id ); ?>

	<?php if ( $embed_html !== '' ) : ?>
		<div class="resource-single__video-embed msr-video-embed mb-4" role="region" aria-label="<?php esc_attr_e( 'Video player', 'msrsandbox' ); ?>">
			<?php echo $embed_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</div>
	<?php endif; ?>

	<?php if ( $duration_mins > 0 ) : ?>
		<p class="resource-single__video-duration text-muted mb-4">
			<i class="fa-solid fa-clock me-2" aria-hidden="true"></i>
			<?php
			echo esc_html(
				sprintf(
					/* translators: %d: duration in minutes */
					_n( '%d minute video', '%d minute video', $duration_mins, 'msrsandbox' ),
					$duration_mins
				)
			);
			?>
		</p>
	<?php endif; ?>

	<div class="entry-content resource-single__body">
		<?php the_content(); ?>
	</div>
	<?php get_template_part( 'template-parts/resource/single', 'related' ); ?>
	<?php msr_publishing_render_resource_commentary_crosslink( $post_id ); ?>
	<?php msr_publishing_render_resource_single_subscribe(); ?>
</article>
