<?php
/**
 * Atlas Briefing ACF options — admin-first site copy and layout flags.
 *
 * @package msrsandbox
 */

/**
 * @param string $field ACF field name.
 * @param string $default Fallback when empty.
 * @return string
 */
function msr_publishing_get_option_string( $field, $default = '' ) {
	if ( ! function_exists( 'get_field' ) ) {
		return $default;
	}
	$value = get_field( $field, 'option' );
	if ( ! is_string( $value ) || '' === trim( $value ) ) {
		return $default;
	}
	return trim( $value );
}

/**
 * @param string $field ACF field name.
 * @param bool   $default Fallback.
 * @return bool
 */
function msr_publishing_get_option_bool( $field, $default = false ) {
	if ( ! function_exists( 'get_field' ) ) {
		return $default;
	}
	$value = get_field( $field, 'option' );
	if ( null === $value || '' === $value ) {
		return $default;
	}
	return (bool) $value;
}

/**
 * Option lines as a trimmed string list (one item per line).
 *
 * @param string   $field   ACF field name.
 * @param string[] $default Fallback lines.
 * @return string[]
 */
function msr_publishing_get_option_lines( $field, $default = array() ) {
	$raw = msr_publishing_get_option_string( $field, '' );
	if ( '' === $raw ) {
		return $default;
	}

	$lines = preg_split( '/\r\n|\r|\n/', $raw );
	if ( ! is_array( $lines ) ) {
		return $default;
	}

	return array_values(
		array_filter(
			array_map( 'trim', $lines ),
			static function ( $line ) {
				return $line !== '';
			}
		)
	);
}

/**
 * Resource archive intro (Appearance → Atlas Briefing → Site copy).
 *
 * @return string
 */
function msr_publishing_get_resources_archive_intro() {
	return msr_publishing_get_option_string(
		'resources_archive_intro',
		__( 'Curated briefings, whitepapers, webinars, and playbooks from Atlas Briefing.', 'msrsandbox' )
	);
}

/**
 * Topics hub intro line.
 *
 * @return string
 */
function msr_publishing_get_topics_hub_intro() {
	return msr_publishing_get_option_string(
		'topics_hub_intro',
		__( 'Browse Atlas Briefing by theme — workforce, resilience, and editorial hubs.', 'msrsandbox' )
	);
}

/**
 * Insights hub intro.
 *
 * @return string
 */
function msr_publishing_get_insights_hub_intro() {
	return msr_publishing_get_option_string(
		'insights_hub_intro',
		__( 'Editorial analysis from Atlas Briefing — workforce, resilience, and market context alongside the resource library.', 'msrsandbox' )
	);
}

/**
 * About page lead (below H1).
 *
 * @return string
 */
function msr_publishing_get_about_page_lead() {
	return msr_publishing_get_option_string(
		'about_page_lead',
		__( 'A demonstration B2B insights publisher — methodology, formats, and editorial standards for portfolio review.', 'msrsandbox' )
	);
}

/**
 * About methodology intro paragraph.
 *
 * @return string
 */
function msr_publishing_get_about_methodology_intro() {
	return msr_publishing_get_option_string(
		'about_methodology_intro',
		__( 'Atlas Briefing models how a workforce and resilience publisher connects formats, commentary, and programme context. Resources are organised by topic hubs and format archives; editorial posts share the same topic taxonomy so readers can move between briefings and analysis.', 'msrsandbox' )
	);
}

/**
 * About methodology bullet list.
 *
 * @return string[]
 */
function msr_publishing_get_about_methodology_bullets() {
	return msr_publishing_get_option_lines(
		'about_methodology_bullets',
		array(
			__( 'Topic-first hubs link resources and commentary on workforce and resilience.', 'msrsandbox' ),
			__( 'Format-specific templates cover whitepapers, webinars, field playbooks, and executive briefings.', 'msrsandbox' ),
			__( 'Programme CTAs connect demonstration assets to the MSR Events, Awards, and Seminars estate.', 'msrsandbox' ),
		)
	);
}

/**
 * About format highlight cards.
 *
 * @return array<int, array{title: string, text: string}>
 */
function msr_publishing_get_about_format_cards() {
	return array(
		array(
			'title' => msr_publishing_get_option_string(
				'about_format_1_title',
				__( 'Resource library', 'msrsandbox' )
			),
			'text'  => msr_publishing_get_option_string(
				'about_format_1_text',
				__( 'Downloadable and on-demand assets — whitepapers, webinar replays, briefings, and playbooks.', 'msrsandbox' )
			),
		),
		array(
			'title' => msr_publishing_get_option_string(
				'about_format_2_title',
				__( 'Commentary & insights', 'msrsandbox' )
			),
			'text'  => msr_publishing_get_option_string(
				'about_format_2_text',
				__( 'Editorial analysis tagged by topic, cross-linked to related resources on every single.', 'msrsandbox' )
			),
		),
	);
}

/**
 * About demonstration disclaimer copy.
 *
 * @return string
 */
function msr_publishing_get_about_disclaimer() {
	return msr_publishing_get_option_string(
		'about_disclaimer',
		__( 'Demonstration property for portfolio purposes. Copy, imagery, and signup flows are illustrative — connect production ESP, analytics, and legal review before launch.', 'msrsandbox' )
	);
}

/**
 * Whether the About demo disclaimer block is shown.
 *
 * @return bool
 */
function msr_publishing_show_about_demo_notice() {
	return msr_publishing_get_option_bool( 'show_about_demo_notice', true );
}

/**
 * Subscribe page lead fallback.
 *
 * @return string
 */
function msr_publishing_get_subscribe_page_lead() {
	return msr_publishing_get_option_string(
		'subscribe_page_lead',
		__( 'Curated updates across whitepapers, webinars, briefings, playbooks, videos, and podcasts — demonstration signup for portfolio review.', 'msrsandbox' )
	);
}

/**
 * Footer demo disclaimer line.
 *
 * @return string
 */
function msr_publishing_get_footer_demo_note() {
	return msr_publishing_get_option_string(
		'footer_demo_note',
		__( 'Demonstration property for portfolio purposes.', 'msrsandbox' )
	);
}

/**
 * SEO meta description from options.
 *
 * @param string $field   ACF option field.
 * @param string $default Fallback copy.
 * @return string
 */
function msr_publishing_get_seo_description_option( $field, $default ) {
	return msr_publishing_get_option_string( $field, $default );
}

/**
 * Whether the footer demo disclaimer line is shown.
 *
 * @return bool
 */
function msr_publishing_show_footer_demo_note() {
	return msr_publishing_get_option_bool( 'show_footer_demo_note', true );
}

/**
 * Resource archive: render ecosystem band after the grid (recommended UX).
 *
 * @return bool
 */
function msr_publishing_ecosystem_band_after_grid() {
	return msr_publishing_get_option_bool( 'ecosystem_band_after_grid', true );
}

/**
 * Home about teaser one-liner.
 *
 * @return string
 */
function msr_publishing_get_home_about_teaser_lead() {
	return msr_publishing_get_option_string(
		'home_about_teaser_lead',
		__( 'Methodology, formats, and editorial standards for workforce and resilience publishers.', 'msrsandbox' )
	);
}

/**
 * Block pattern subscribe blurb.
 *
 * @return string
 */
function msr_publishing_get_block_pattern_subscribe_text() {
	return msr_publishing_get_option_string(
		'block_pattern_subscribe_text',
		__( 'Curated briefings, whitepapers, and commentary — portfolio demo signup.', 'msrsandbox' )
	);
}

/**
 * Block pattern resource CTA blurb.
 *
 * @return string
 */
function msr_publishing_get_block_pattern_resource_text() {
	return msr_publishing_get_option_string(
		'block_pattern_resource_text',
		__( 'Market pulse, playbooks, whitepapers, and webinar replays from Atlas Briefing.', 'msrsandbox' )
	);
}

/**
 * Block pattern hub hero blurb.
 *
 * @return string
 */
function msr_publishing_get_block_pattern_hub_hero_text() {
	return msr_publishing_get_option_string(
		'block_pattern_hub_hero_text',
		__( 'Workforce and resilience insights — curated resources and editorial commentary.', 'msrsandbox' )
	);
}
