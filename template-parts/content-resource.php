<?php
/**
 * Default single resource body (fallback).
 *
 * @package msrpublishing
 */

$post_id = get_the_ID();
msr_publishing_resource_single_open( '' );
msr_publishing_resource_single_render_chrome(
	$post_id,
	array(
		'utility' => false,
	)
);
msr_publishing_resource_single_render_body( $post_id, false );
msr_publishing_resource_single_render_footer();
msr_publishing_resource_single_close();
