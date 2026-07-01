<?php
/**
 * Category archive → topic hub redirects (P40).
 *
 * WordPress categories use category_base `topics`; publishing topic hubs use `briefing-topic`.
 *
 * @package msrsandbox
 */

/**
 * Explicit category slug → topic slug map (empty string = insights hub).
 *
 * @return array<string, string>
 */
function msr_publishing_get_category_topic_redirect_map() {
	return array(
		'editorial'         => 'workforce',
		'workforce'         => 'workforce',
		'resilience'        => 'resilience',
		'sponsored-content' => '',
	);
}

/**
 * Resolve a 301 target for a WP category archive slug.
 *
 * @param string $category_slug Category nicename.
 * @return string Redirect URL or empty when no redirect should run.
 */
function msr_publishing_resolve_category_redirect_url( $category_slug ) {
	$category_slug = sanitize_title( (string) $category_slug );
	if ( $category_slug === '' ) {
		return '';
	}

	$map        = msr_publishing_get_category_topic_redirect_map();
	$topic_slug = array_key_exists( $category_slug, $map )
		? (string) $map[ $category_slug ]
		: $category_slug;

	if ( $topic_slug === '' ) {
		return msr_publishing_get_page_url( 'insights', '/insights/' );
	}

	$term = get_term_by( 'slug', $topic_slug, 'topic' );
	if ( $term instanceof WP_Term ) {
		$link = get_term_link( $term );
		if ( ! is_wp_error( $link ) ) {
			return (string) $link;
		}
	}

	return msr_publishing_get_page_url( 'insights', '/insights/' );
}

/**
 * Primary topic hub link for a post (breadcrumbs / JSON-LD).
 *
 * @param int $post_id Post ID.
 * @return array{name: string, url: string}|null
 */
function msr_publishing_get_post_topic_hub_link( $post_id = 0 ) {
	$post_id = $post_id ? (int) $post_id : get_the_ID();
	if ( ! $post_id ) {
		return null;
	}

	$topics = get_the_terms( $post_id, 'topic' );
	if ( ! $topics || is_wp_error( $topics ) ) {
		return null;
	}

	$topic = $topics[0];
	$link  = get_term_link( $topic );
	if ( is_wp_error( $link ) ) {
		return null;
	}

	return array(
		'name' => $topic->name,
		'url'  => (string) $link,
	);
}

/**
 * 301 redirect legacy category archives to topic hubs (or insights).
 *
 * @return void
 */
function msr_publishing_maybe_redirect_category_archive() {
	if ( ! is_category() ) {
		return;
	}

	$category = get_queried_object();
	if ( ! $category instanceof WP_Term ) {
		return;
	}

	$target = msr_publishing_resolve_category_redirect_url( $category->slug );
	if ( $target === '' ) {
		return;
	}

	wp_safe_redirect( $target, 301 );
	exit;
}
add_action( 'template_redirect', 'msr_publishing_maybe_redirect_category_archive', 5 );
