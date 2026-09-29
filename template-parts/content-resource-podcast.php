<?php
/**
 * Podcast single — player-first, episode meta + RSS aside.
 *
 * @package msrpublishing
 */

$post_id        = get_the_ID();
$episode_number = function_exists( 'get_field' ) ? (int) get_field( 'podcast_episode', $post_id ) : 0;
$duration_mins  = function_exists( 'get_field' ) ? (int) get_field( 'duration_minutes', $post_id ) : 0;
$feed_url       = msr_publishing_get_podcast_feed_url();

msr_publishing_resource_single_open( 'podcast' );
msr_publishing_resource_single_render_chrome(
	$post_id,
	array(
		'utility' => true,
	)
);
msr_publishing_resource_single_preface_open( 'podcast' );

msr_publishing_resource_single_render_podcast_panel( $post_id );

msr_publishing_resource_single_format_layout_open( 'split' );
msr_publishing_render_resource_format_preview(
	$post_id,
	__( 'In this episode', 'msrpublishing' )
);
msr_publishing_render_key_takeaways( $post_id );
msr_publishing_resource_single_format_main_close_aside_open();
?>
<div class="resource-single__meta-panel resource-single__podcast-meta card">
	<div class="card-body">
		<h2 class="h6 text-uppercase fw-semibold text-muted mb-3"><?php esc_html_e( 'Episode details', 'msrpublishing' ); ?></h2>
		<ul class="list-unstyled mb-3 resource-single__podcast-facts">
			<?php if ( $episode_number > 0 ) : ?>
				<li>
					<i class="fa-solid fa-podcast me-2 text-muted" aria-hidden="true"></i>
					<?php
					echo esc_html(
						sprintf(
							/* translators: %d: episode number */
							__( 'Episode %d', 'msrpublishing' ),
							$episode_number
						)
					);
					?>
				</li>
			<?php endif; ?>
			<?php if ( $duration_mins > 0 ) : ?>
				<li>
					<i class="fa-solid fa-clock me-2 text-muted" aria-hidden="true"></i>
					<?php
					echo esc_html(
						sprintf(
							/* translators: %d: duration in minutes */
							_n( '%d minute episode', '%d minute episode', $duration_mins, 'msrpublishing' ),
							$duration_mins
						)
					);
					?>
				</li>
			<?php endif; ?>
		</ul>
		<?php if ( $feed_url !== '' ) : ?>
			<a class="btn btn-outline-primary w-100 msr-podcast-rss-link" href="<?php echo esc_url( $feed_url ); ?>">
				<i class="fa-solid fa-rss me-2" aria-hidden="true"></i>
				<?php esc_html_e( 'Subscribe via RSS', 'msrpublishing' ); ?>
			</a>
		<?php endif; ?>
	</div>
</div>
<?php
msr_publishing_resource_single_format_layout_close();
msr_publishing_resource_single_preface_close();
msr_publishing_resource_single_render_body( $post_id, false );
msr_publishing_resource_single_render_footer();
msr_publishing_resource_single_close();
