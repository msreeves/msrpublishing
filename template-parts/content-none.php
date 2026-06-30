<?php
/**
 * Template part for displaying a message that posts cannot be found
 *
 * @package msrsandbox
 */

if ( is_search() ) {
	msr_publishing_render_empty_state(
		array(
			'context' => 'search',
			'search'  => true,
		)
	);
	msr_publishing_render_search_popular_terms();
	return;
}

$context = is_archive() ? 'archive' : 'listing';
msr_publishing_render_empty_state(
	array(
		'context' => $context,
	)
);
