<?php
/**
 * Video single — player-first with duration aside.
 *
 * @package msrpublishing
 */

$post_id       = get_the_ID();
$video_url     = function_exists( 'get_field' ) ? (string) get_field( 'video_url', $post_id ) : '';
$duration_mins = function_exists( 'get_field' ) ? (int) get_field( 'duration_minutes', $post_id ) : 0;
$embed_html    = msr_publishing_get_video_embed_html( $video_url, $post_id );

msr_publishing_resource_single_open( 'video' );
msr_publishing_resource_single_render_chrome(
	$post_id,
	array(
		'utility' => true,
	)
);
msr_publishing_resource_single_preface_open( 'video' );

if ( $embed_html !== '' ) {
	msr_publishing_resource_single_render_media_panel(
		$embed_html,
		__( 'Video player', 'msrpublishing' ),
		'resource-single__media-panel--video'
	);
}

msr_publishing_resource_single_format_layout_open( 'split' );
msr_publishing_render_resource_format_preview(
	$post_id,
	__( 'In this video', 'msrpublishing' )
);
msr_publishing_render_key_takeaways( $post_id );
msr_publishing_resource_single_format_main_close_aside_open();
?>
<div class="resource-single__meta-panel resource-single__video-meta card">
	<div class="card-body">
		<h2 class="h6 text-uppercase fw-semibold text-muted mb-2"><?php esc_html_e( 'Watch time', 'msrpublishing' ); ?></h2>
		<?php if ( $duration_mins > 0 ) : ?>
			<p class="resource-single__video-duration mb-0">
				<i class="fa-solid fa-clock me-2 text-muted" aria-hidden="true"></i>
				<?php
				echo esc_html(
					sprintf(
						/* translators: %d: duration in minutes */
						_n( '%d minute video', '%d minute video', $duration_mins, 'msrpublishing' ),
						$duration_mins
					)
				);
				?>
			</p>
		<?php else : ?>
			<p class="text-muted small mb-0"><?php esc_html_e( 'Duration will appear when set in the editor.', 'msrpublishing' ); ?></p>
		<?php endif; ?>
	</div>
</div>
<?php
msr_publishing_resource_single_format_layout_close();
msr_publishing_resource_single_preface_close();
msr_publishing_resource_single_render_body( $post_id, false );
msr_publishing_resource_single_render_footer();
msr_publishing_resource_single_close();
