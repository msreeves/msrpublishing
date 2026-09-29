<?php
/**
 * Canonical not-found route for portfolio demos (/not-found/).
 *
 * @package msrpublishing
 */

/**
 * @return void
 */
function msr_publishing_register_error_routes() {
	add_rewrite_rule( '^not-found/?$', 'index.php?msr_pub_not_found=1', 'top' );
}

add_action( 'init', 'msr_publishing_register_error_routes' );

/**
 * @param string[] $vars Query vars.
 * @return string[]
 */
function msr_publishing_error_query_vars( $vars ) {
	$vars[] = 'msr_pub_not_found';
	return $vars;
}

add_filter( 'query_vars', 'msr_publishing_error_query_vars' );

/**
 * Render 404 template for /not-found/.
 *
 * @return void
 */
function msr_publishing_serve_not_found_route() {
	if ( (int) get_query_var( 'msr_pub_not_found' ) !== 1 ) {
		return;
	}

	global $wp_query;
	$wp_query->set_404();
	status_header( 404 );
	nocache_headers();

	$template = get_query_template( '404' );
	if ( $template ) {
		include $template;
	}
	exit;
}

add_action( 'template_redirect', 'msr_publishing_serve_not_found_route', 1 );

/**
 * Send unknown URLs to the canonical not-found path (clean address bar for demos).
 *
 * @return void
 */
function msr_publishing_redirect_unknown_to_not_found() {
	if ( ! is_404() || (int) get_query_var( 'msr_pub_not_found' ) === 1 ) {
		return;
	}

	$path = isset( $_SERVER['REQUEST_URI'] ) ? (string) wp_parse_url( $_SERVER['REQUEST_URI'], PHP_URL_PATH ) : '';
	$path = trim( $path, '/' );
	if ( $path === '' ) {
		return;
	}

	$not_found_path = trim( (string) wp_parse_url( home_url( '/not-found/' ), PHP_URL_PATH ), '/' );
	if ( $path === $not_found_path || str_ends_with( $path, '/not-found' ) ) {
		return;
	}

	wp_safe_redirect( home_url( '/not-found/' ), 302 );
	exit;
}

add_action( 'template_redirect', 'msr_publishing_redirect_unknown_to_not_found', 20 );
