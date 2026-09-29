<?php
/**
 * Commentary permalink recovery — insights slug and bare post-name 404s.
 *
 * @package msrpublishing
 */

/**
 * Path relative to this WP install (no leading/trailing slash).
 *
 * @return string
 */
function msr_publishing_request_path_relative() {
	if ( empty( $_SERVER['REQUEST_URI'] ) ) {
		return '';
	}

	$path = (string) wp_parse_url( $_SERVER['REQUEST_URI'], PHP_URL_PATH );
	$path = trim( $path, '/' );
	$home = (string) wp_parse_url( home_url( '/' ), PHP_URL_PATH );
	$home = trim( $home, '/' );

	if ( $home !== '' && str_starts_with( $path, $home . '/' ) ) {
		$path = substr( $path, strlen( $home ) + 1 );
	} elseif ( $home !== '' && $path === $home ) {
		$path = '';
	}

	return trim( $path, '/' );
}

/**
 * Resolve a published commentary post from a slug (blogs/{slug} or {slug}).
 *
 * @param string $slug Post name.
 * @return WP_Post|null
 */
function msr_publishing_get_commentary_post_by_slug( $slug ) {
	$slug = sanitize_title( (string) $slug );
	if ( $slug === '' ) {
		return null;
	}

	$post = get_page_by_path( 'blogs/' . $slug, OBJECT, 'post' );
	if ( $post instanceof WP_Post && 'publish' === $post->post_status ) {
		return $post;
	}

	$post = get_page_by_path( $slug, OBJECT, 'post' );
	if ( $post instanceof WP_Post && 'publish' === $post->post_status ) {
		return $post;
	}

	$found = get_posts(
		array(
			'post_type'      => 'post',
			'name'           => $slug,
			'post_status'    => 'publish',
			'posts_per_page' => 1,
		)
	);

	return $found ? $found[0] : null;
}

/**
 * Redirect common broken commentary URLs to canonical permalinks.
 *
 * @return void
 */
function msr_publishing_maybe_redirect_commentary_permalink() {
	if ( is_admin() ) {
		return;
	}

	$path = msr_publishing_request_path_relative();
	if ( $path === '' ) {
		return;
	}

	$slug = '';
	if ( preg_match( '#^insights/([^/]+)/?$#', $path, $matches ) ) {
		$slug = (string) $matches[1];
	} elseif ( preg_match( '#^news/([^/]+)/?$#', $path, $matches ) ) {
		$slug = (string) $matches[1];
	} elseif ( is_404() && preg_match( '#^([^/]+)/?$#', $path, $matches ) ) {
		$slug = (string) $matches[1];
	}

	if ( $slug === '' || in_array( $slug, array( 'insights', 'resources', 'about', 'subscribe', 'topics', 'privacy' ), true ) ) {
		return;
	}

	$post = msr_publishing_get_commentary_post_by_slug( $slug );
	if ( ! $post ) {
		return;
	}

	$target = get_permalink( $post );
	if ( ! $target || is_wp_error( $target ) ) {
		return;
	}

	$current = home_url( '/' . $path . '/' );
	if ( trailingslashit( $current ) === trailingslashit( $target ) ) {
		return;
	}

	wp_safe_redirect( $target, 301 );
	exit;
}
add_action( 'template_redirect', 'msr_publishing_maybe_redirect_commentary_permalink', 0 );
