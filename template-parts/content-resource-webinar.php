<?php
/**
 * Webinar single resource.
 *
 * @package msrsandbox
 */

$post_id       = get_the_ID();
$webinar_date  = function_exists( 'get_field' ) ? (string) get_field( 'webinar_date', $post_id ) : '';
$register_url  = function_exists( 'get_field' ) ? (string) get_field( 'register_url', $post_id ) : '';
$replay_url    = function_exists( 'get_field' ) ? (string) get_field( 'replay_url', $post_id ) : '';
$duration_mins = function_exists( 'get_field' ) ? (int) get_field( 'duration_minutes', $post_id ) : 0;
$replay_embed  = msr_publishing_get_webinar_replay_embed_html( $replay_url, $post_id );
$date_display  = '';
if ( $webinar_date !== '' ) {
	$timestamp    = strtotime( $webinar_date );
	$date_display = $timestamp ? wp_date( get_option( 'date_format' ), $timestamp ) : $webinar_date;
}
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'resource-single resource-single--webinar container py-4' ); ?>>
	<?php get_template_part( 'template-parts/resource/single', 'header' ); ?>
	<?php msr_publishing_render_resource_programme_cta( $post_id ); ?>

	<?php if ( $replay_embed !== '' ) : ?>
		<div class="resource-single__webinar-replay msr-video-embed mb-4" role="region" aria-label="<?php esc_attr_e( 'Webinar replay', 'msrsandbox' ); ?>">
			<?php echo $replay_embed; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</div>
	<?php endif; ?>

	<div class="resource-single__webinar-meta card mb-4">
		<div class="card-body">
			<h2 class="h5 card-title"><?php esc_html_e( 'Webinar details', 'msrsandbox' ); ?></h2>
			<ul class="list-unstyled mb-3 resource-single__webinar-facts">
				<?php if ( $date_display !== '' ) : ?>
					<li>
						<i class="fa-solid fa-calendar-days me-2 text-muted" aria-hidden="true"></i>
						<?php echo esc_html( $date_display ); ?>
					</li>
				<?php endif; ?>
				<?php if ( $duration_mins > 0 ) : ?>
					<li>
						<i class="fa-solid fa-clock me-2 text-muted" aria-hidden="true"></i>
						<?php
						echo esc_html(
							sprintf(
								/* translators: %d: duration in minutes */
								_n( '%d minute session', '%d minute session', $duration_mins, 'msrsandbox' ),
								$duration_mins
							)
						);
						?>
					</li>
				<?php endif; ?>
			</ul>
			<div class="d-flex flex-wrap gap-2">
				<?php if ( $register_url !== '' ) : ?>
					<a class="btn btn-primary" href="<?php echo esc_url( $register_url ); ?>" target="_blank" rel="noopener noreferrer">
						<?php esc_html_e( 'Register for webinar', 'msrsandbox' ); ?>
					</a>
				<?php endif; ?>
				<?php if ( $replay_url !== '' ) : ?>
					<a class="btn btn-outline-primary" href="<?php echo esc_url( $replay_url ); ?>" target="_blank" rel="noopener noreferrer">
						<?php esc_html_e( 'Watch replay', 'msrsandbox' ); ?>
					</a>
				<?php endif; ?>
			</div>
		</div>
	</div>

	<?php msr_publishing_render_webinar_speakers( $post_id ); ?>

	<div class="entry-content resource-single__body">
		<?php the_content(); ?>
	</div>
	<?php get_template_part( 'template-parts/resource/single', 'related' ); ?>
	<?php msr_publishing_render_resource_commentary_crosslink( $post_id ); ?>
	<?php msr_publishing_render_resource_single_subscribe(); ?>
</article>
