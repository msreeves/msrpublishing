<?php
/**
 * Register styles and scripts for WP Theme.
 *
 * Bootstrap, Font Awesome, and theme fonts are bundled via Vite (dist/app.css / dist/app.js).
 * No third-party CDN enqueues on the front end (audit P0 stack hygiene).
 */

/**
 * @param string $relative Path under theme root.
 * @return int|null
 */
function msrsandbox_asset_version( $relative ) {
	$path  = get_template_directory() . '/' . ltrim( $relative, '/' );
	$mtime = @filemtime( $path );
	return $mtime ? (int) $mtime : null;
}

/**
 * @return void
 */
function theme_scripts() {
	$app_css_ver = msrsandbox_asset_version( 'dist/app.css' );
	$app_js_ver  = msrsandbox_asset_version( 'dist/app.js' );

	wp_enqueue_style(
		'appcss',
		get_template_directory_uri() . '/dist/app.css',
		array(),
		$app_css_ver
	);

	wp_enqueue_script(
		'appjs',
		get_template_directory_uri() . '/dist/app.js',
		array(),
		$app_js_ver,
		array(
			'in_footer' => true,
			'strategy'  => 'defer',
		)
	);
}
add_action( 'wp_enqueue_scripts', 'theme_scripts' );

/**
 * Vite bundle uses dynamic import + import.meta — must load as ES module.
 *
 * @param string $tag    Script tag HTML.
 * @param string $handle Script handle.
 * @param string $src    Script URL.
 * @return string
 */
function msrsandbox_appjs_module_tag( $tag, $handle, $src ) {
	if ( 'appjs' !== $handle || strpos( $tag, 'type=' ) !== false ) {
		return $tag;
	}

	return str_replace( '<script ', '<script type="module" ', $tag );
}
add_filter( 'script_loader_tag', 'msrsandbox_appjs_module_tag', 10, 3 );

/**
 * Remove front-end emoji script/styles (audit P0 perf).
 *
 * @return void
 */
function msrsandbox_disable_emoji_assets() {
	if ( is_admin() ) {
		return;
	}
	remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	remove_action( 'wp_print_styles', 'print_emoji_styles' );
	remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
	remove_action( 'admin_print_styles', 'print_emoji_styles' );
}
add_action( 'init', 'msrsandbox_disable_emoji_assets' );
