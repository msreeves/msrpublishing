<?php
/**
 * Demo / portfolio external URL fixes (audit + broken social links).
 *
 * @package msrpublishing
 */

/**
 * Normalize known-bad demo URLs before output (DB seeds may contain typos).
 *
 * @param string $url Raw URL from menus or ACF.
 * @return string
 */
function msr_publishing_normalize_external_url( $url ) {
	$url = is_string( $url ) ? trim( $url ) : '';
	if ( $url === '' ) {
		return '';
	}

	$replacements = array(
		'https://www.youtube.co.uk' => 'https://www.youtube.com',
		'http://www.youtube.co.uk'  => 'https://www.youtube.com',
		'//www.youtube.co.uk'       => 'https://www.youtube.com',
	);

	foreach ( $replacements as $bad => $good ) {
		if ( stripos( $url, $bad ) === 0 ) {
			return $good . substr( $url, strlen( $bad ) );
		}
	}

	return $url;
}
