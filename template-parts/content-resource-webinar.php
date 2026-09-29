<?php
/**
 * Webinar single — replay-first with meta aside (Thoughtworks-style).
 *
 * @package msrpublishing
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

msr_publishing_resource_single_open( 'webinar' );
msr_publishing_resource_single_render_chrome(
	$post_id,
	array(
		'utility' => true,
	)
);
msr_publishing_resource_single_preface_open( 'webinar' );

if ( $replay_embed !== '' ) {
	msr_publishing_resource_single_render_media_panel(
		$replay_embed,
		__( 'Webinar replay', 'msrpublishing' ),
		'resource-single__media-panel--webinar'
	);
}

msr_publishing_resource_single_format_layout_open( 'split' );
msr_publishing_render_resource_format_preview(
	$post_id,
	__( 'In this webinar', 'msrpublishing' )
);
msr_publishing_render_key_takeaways( $post_id );
msr_publishing_render_webinar_speakers( $post_id );
msr_publishing_resource_single_format_main_close_aside_open();
?>
<div class="resource-single__meta-panel resource-single__webinar-meta card">
	<div class="card-body">
		<h2 class="h6 text-uppercase fw-semibold text-muted mb-3"><?php esc_html_e( 'Session details', 'msrpublishing' ); ?></h2>
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
							_n( '%d minute session', '%d minute session', $duration_mins, 'msrpublishing' ),
							$duration_mins
						)
					);
					?>
				</li>
			<?php endif; ?>
		</ul>
		<div class="d-grid gap-2">
			<?php if ( $register_url !== '' ) : ?>
				<a class="btn btn-primary" href="<?php echo esc_url( $register_url ); ?>" target="_blank" rel="noopener noreferrer">
					<?php esc_html_e( 'Register for webinar', 'msrpublishing' ); ?>
				</a>
			<?php endif; ?>
			<?php if ( $replay_url !== '' ) : ?>
				<a class="btn <?php echo $register_url !== '' ? 'btn-outline-primary' : 'btn-primary'; ?>" href="<?php echo esc_url( $replay_url ); ?>" target="_blank" rel="noopener noreferrer">
					<?php esc_html_e( 'Watch replay', 'msrpublishing' ); ?>
				</a>
			<?php endif; ?>
		</div>
	</div>
</div>
<?php
msr_publishing_resource_single_format_layout_close();
msr_publishing_resource_single_preface_close();
msr_publishing_resource_single_render_body( $post_id, false );
msr_publishing_resource_single_render_footer();
msr_publishing_resource_single_close();
