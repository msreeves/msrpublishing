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
require_once get_template_directory() . '/inc/brand.php';
require_once get_template_directory() . '/inc/media.php';
require_once get_template_directory() . '/inc/resources.php';
require_once get_template_directory() . '/inc/series.php';
require_once get_template_directory() . '/inc/formats.php';
require_once get_template_directory() . '/inc/podcast-feed.php';
require_once get_template_directory() . '/inc/ecosystem.php';
require_once get_template_directory() . '/inc/filter-bar.php';
require_once get_template_directory() . '/inc/archive.php';
require_once get_template_directory() . '/inc/hubs.php';
require_once get_template_directory() . '/inc/legacy-guard.php';
require_once get_template_directory() . '/inc/acf.php';
require_once get_template_directory() . '/inc/breadcrumbs.php';
require_once get_template_directory() . '/inc/helpers.php';
require_once get_template_directory() . '/inc/options.php';
require_once get_template_directory() . '/inc/authors.php';
require_once get_template_directory() . '/inc/experts.php';
require_once get_template_directory() . '/inc/single-ux.php';
require_once get_template_directory() . '/inc/cards.php';
require_once get_template_directory() . '/inc/admin.php';
require_once get_template_directory() . '/inc/leadgen.php';
require_once get_template_directory() . '/inc/subscribe.php';
require_once get_template_directory() . '/inc/commentary.php';
require_once get_template_directory() . '/inc/search.php';
require_once get_template_directory() . '/inc/nav.php';
require_once get_template_directory() . '/inc/category-redirects.php';
require_once get_template_directory() . '/inc/footer.php';
require_once get_template_directory() . '/inc/seo.php';
require_once get_template_directory() . '/inc/home.php';
require_once get_template_directory() . '/inc/social-proof.php';
require_once get_template_directory() . '/inc/blocks.php';

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
