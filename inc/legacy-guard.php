<?php
/**
 * Block or redirect legacy sandbox routes on the publishing front end.
 *
 * @package msrsandbox
 */

/**
 * Legacy page templates that should not be public on Atlas Briefing.
 *
 * @return string[]
 */
function msr_publishing_legacy_page_templates() {
	return array(
		'templates/template-events.php',
		'templates/template-members.php',
		'templates/template-partners.php',
		'templates/template-posts.php',
	);
}

/**
 * Redirect legacy page templates to the resource library.
 *
 * @return void
 */
function msr_publishing_redirect_legacy_page_templates() {
	if ( is_admin() || ! is_page() || is_front_page() ) {
		return;
	}

	$template = get_page_template_slug();
	if ( ! $template || ! in_array( $template, msr_publishing_legacy_page_templates(), true ) ) {
		return;
	}

	$archive = get_post_type_archive_link( 'resource' );
	if ( $archive ) {
		wp_safe_redirect( $archive, 302 );
		exit;
	}
}
add_action( 'template_redirect', 'msr_publishing_redirect_legacy_page_templates', 1 );

/**
 * Legacy demo CPTs that should not be public on Atlas Briefing.
 *
 * @return string[]
 */
function msr_publishing_legacy_cpt_types() {
	return array( 'event', 'member', 'partner', 'publication', 'advert' );
}

/**
 * Redirect legacy CPT singles to the resource library.
 *
 * @return void
 */
function msr_publishing_redirect_legacy_cpt_singles() {
	if ( is_admin() || ! is_singular( msr_publishing_legacy_cpt_types() ) ) {
		return;
	}

	$archive = get_post_type_archive_link( 'resource' );
	if ( $archive ) {
		wp_safe_redirect( $archive, 302 );
		exit;
	}
}
add_action( 'template_redirect', 'msr_publishing_redirect_legacy_cpt_singles', 1 );

/**
 * Whether Fancybox assets are needed on this request.
 *
 * @return bool
 */
function msr_publishing_needs_fancybox() {
	if ( is_admin() ) {
		return false;
	}

	if ( msr_publishing_show_leaderboard_ads() ) {
		return true;
	}

	if ( is_singular( array( 'event', 'member', 'partner', 'publication' ) ) ) {
		return true;
	}

	return (bool) apply_filters( 'msr_publishing_needs_fancybox', false );
}
