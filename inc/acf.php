<?php
/**
 * ACF options page for Atlas Briefing (publishing install).
 *
 * Field groups load from theme acf-json/ (see functions.php save/load filters).
 * Re-export after schema changes:
 *   wp eval-file scripts/msr-publishing-acf-json-export.php (from sites/wp/msrpublishing)
 *
 * @package msrsandbox
 */

/**
 * ACF options page — programme URLs and site settings (admin-first).
 *
 * @return void
 */
function msr_publishing_register_acf_options_page() {
	if ( ! function_exists( 'acf_add_options_page' ) ) {
		return;
	}

	acf_add_options_page(
		array(
			'page_title' => __( 'Atlas Briefing settings', 'msrsandbox' ),
			'menu_title' => __( 'Atlas Briefing', 'msrsandbox' ),
			'menu_slug'  => 'msr-publishing-settings',
			'capability' => 'edit_posts',
			'redirect'   => false,
			'icon_url'   => 'dashicons-book-alt',
			'position'   => 58,
		)
	);
}
add_action( 'acf/init', 'msr_publishing_register_acf_options_page' );
