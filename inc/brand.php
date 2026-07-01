<?php
/**
 * Atlas Briefing brand name + tagline (shell copy independent of WP site title).
 *
 * @package msrsandbox
 */

/**
 * Display name for header, hero, footer, and SEO site_name.
 *
 * @return string
 */
function msr_publishing_brand_name() {
	$name = get_bloginfo( 'name', 'display' );
	if ( '' === trim( $name ) || false !== stripos( $name, 'sandbox' ) ) {
		return __( 'Atlas Briefing', 'msrsandbox' );
	}
	if ( false !== stripos( $name, 'montreal' ) || 'Atlas Briefing' !== $name ) {
		return __( 'Atlas Briefing', 'msrsandbox' );
	}

	return $name;
}

/**
 * Editorial tagline — replaces demo blogdescription in user-facing shell.
 *
 * @return string
 */
function msr_publishing_brand_tagline() {
	$desc  = get_bloginfo( 'description', 'display' );
	$demos = array( 'demonstration', 'portfolio purposes', 'portfolio review', 'portfolio only' );

	foreach ( $demos as $marker ) {
		if ( $desc !== '' && false !== stripos( $desc, $marker ) ) {
			return __( 'Curated briefings, whitepapers, and analysis for workforce and resilience leaders.', 'msrsandbox' );
		}
	}

	if ( '' === trim( $desc ) ) {
		return __( 'Curated briefings, whitepapers, and analysis for workforce and resilience leaders.', 'msrsandbox' );
	}

	return $desc;
}

/**
 * Whether the custom logo should be hidden in favour of a text wordmark.
 *
 * @return bool
 */
function msr_publishing_use_text_wordmark() {
	return true;
}
