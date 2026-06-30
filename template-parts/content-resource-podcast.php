<?php
/**
 * Podcast single resource.
 *
 * @package msrsandbox
 */

$post_id        = get_the_ID();
$audio_url      = msr_publishing_get_podcast_audio_url( $post_id );
$episode_number = function_exists( 'get_field' ) ? (int) get_field( 'podcast_episode', $post_id ) : 0;
$duration_mins  = function_exists( 'get_field' ) ? (int) get_field( 'duration_minutes', $post_id ) : 0;
$feed_url       = msr_publishing_get_podcast_feed_url();
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'resource-single resource-single--podcast container py-4' ); ?>>
	<?php get_template_part( 'template-parts/resource/single', 'header' ); ?>
	<?php msr_publishing_render_resource_programme_cta( $post_id ); ?>

	<?php if ( $audio_url !== '' ) : ?>
		<div class="resource-single__podcast-player msr-podcast-player mb-4" role="region" aria-label="<?php esc_attr_e( 'Podcast player', 'msrsandbox' ); ?>">
			<?php echo msr_publishing_render_podcast_player( $post_id ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</div>
	<?php endif; ?>

	<?php if ( $episode_number > 0 || $duration_mins > 0 ) : ?>
		<p class="resource-single__podcast-meta text-muted mb-4">
			<?php if ( $episode_number > 0 ) : ?>
				<span class="resource-single__podcast-episode me-3">
					<i class="fa-solid fa-podcast me-2" aria-hidden="true"></i>
					<?php
					echo esc_html(
						sprintf(
							/* translators: %d: episode number */
							__( 'Episode %d', 'msrsandbox' ),
							$episode_number
						)
					);
					?>
				</span>
			<?php endif; ?>
			<?php if ( $duration_mins > 0 ) : ?>
				<span class="resource-single__podcast-duration">
					<i class="fa-solid fa-clock me-2" aria-hidden="true"></i>
					<?php
					echo esc_html(
						sprintf(
							/* translators: %d: duration in minutes */
							_n( '%d minute episode', '%d minute episode', $duration_mins, 'msrsandbox' ),
							$duration_mins
						)
					);
					?>
				</span>
			<?php endif; ?>
		</p>
	<?php endif; ?>

	<?php if ( $feed_url !== '' ) : ?>
		<p class="resource-single__podcast-feed mb-4">
			<a class="msr-podcast-rss-link" href="<?php echo esc_url( $feed_url ); ?>">
				<i class="fa-solid fa-rss me-2" aria-hidden="true"></i>
				<?php esc_html_e( 'Subscribe via RSS', 'msrsandbox' ); ?>
			</a>
		</p>
	<?php endif; ?>

	<div class="entry-content resource-single__body">
		<?php the_content(); ?>
	</div>
	<?php get_template_part( 'template-parts/resource/single', 'related' ); ?>
	<?php msr_publishing_render_resource_commentary_crosslink( $post_id ); ?>
	<?php msr_publishing_render_resource_single_subscribe(); ?>
</article>
