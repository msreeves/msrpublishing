<?php
/**
 * Briefing single — executive scan (narrow measure, takeaways before deck copy).
 *
 * @package msrpublishing
 */

$post_id = get_the_ID();
msr_publishing_resource_single_open( 'briefing' );
msr_publishing_resource_single_render_chrome( $post_id );
msr_publishing_resource_single_preface_open( 'briefing' );
msr_publishing_render_key_takeaways( $post_id );
msr_publishing_render_resource_format_preview(
	$post_id,
	__( 'In this briefing', 'msrpublishing' )
);
msr_publishing_resource_single_preface_close();
msr_publishing_resource_single_render_body( $post_id, false );
msr_publishing_resource_single_render_footer();
msr_publishing_resource_single_close();
