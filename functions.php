<?php
/**
 * msrsandbox functions and definitions
 *
 * @package msrsandbox
 */

if ( ! defined( '_S_VERSION' ) ) {
	define( '_S_VERSION', '1.0.0' );
}

require_once get_template_directory() . '/inc/controllers/cpt.php';
require_once get_template_directory() . '/inc/controllers/cpt-admin.php';
require_once get_template_directory() . '/inc/controllers/wp-menus.php';
require_once get_template_directory() . '/inc/controllers/script-styles.php';
require_once get_template_directory() . '/inc/setup.php';
require_once get_template_directory() . '/inc/msr-publishing-brand.php';
require_once get_template_directory() . '/inc/media.php';
require_once get_template_directory() . '/inc/msr-publishing-resources.php';
require_once get_template_directory() . '/inc/msr-publishing-series.php';
require_once get_template_directory() . '/inc/msr-publishing-formats.php';
require_once get_template_directory() . '/inc/msr-publishing-podcast-feed.php';
require_once get_template_directory() . '/inc/msr-publishing-ecosystem.php';
require_once get_template_directory() . '/inc/msr-publishing-filter-bar.php';
require_once get_template_directory() . '/inc/msr-publishing-archive.php';
require_once get_template_directory() . '/inc/msr-publishing-hubs.php';
require_once get_template_directory() . '/inc/msr-publishing-legacy-guard.php';
require_once get_template_directory() . '/inc/msr-publishing-acf.php';
require_once get_template_directory() . '/inc/msr-publishing-breadcrumbs.php';
require_once get_template_directory() . '/inc/msr-publishing-helpers.php';
require_once get_template_directory() . '/inc/msr-publishing-options.php';
require_once get_template_directory() . '/inc/msr-publishing-authors.php';
require_once get_template_directory() . '/inc/msr-publishing-experts.php';
require_once get_template_directory() . '/inc/msr-publishing-single-ux.php';
require_once get_template_directory() . '/inc/msr-publishing-cards.php';
require_once get_template_directory() . '/inc/msr-publishing-admin.php';
require_once get_template_directory() . '/inc/msr-publishing-leadgen.php';
require_once get_template_directory() . '/inc/msr-publishing-subscribe.php';
require_once get_template_directory() . '/inc/msr-publishing-commentary.php';
require_once get_template_directory() . '/inc/msr-publishing-search.php';
require_once get_template_directory() . '/inc/msr-publishing-nav.php';
require_once get_template_directory() . '/inc/msr-publishing-category-redirects.php';
require_once get_template_directory() . '/inc/msr-publishing-footer.php';
require_once get_template_directory() . '/inc/msr-publishing-seo.php';
require_once get_template_directory() . '/inc/msr-publishing-home.php';
require_once get_template_directory() . '/inc/msr-publishing-social-proof.php';
require_once get_template_directory() . '/inc/msr-publishing-blocks.php';

/**
 * ACF local JSON — field group definitions in acf-json/ for reproducible deploys.
 * Sync via WP Admin → Custom Fields when DB drifts from files.
 */
add_filter(
	'acf/settings/save_json',
	static function () {
		return get_stylesheet_directory() . '/acf-json';
	}
);

add_filter(
	'acf/settings/load_json',
	static function ( $paths ) {
		$paths[] = get_stylesheet_directory() . '/acf-json';
		return $paths;
	}
);
