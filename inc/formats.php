<?php
/**
 * Canonical resource format vocabulary (P48) — single source for nav, filters, badges.
 *
 * @package msrsandbox
 */

/**
 * Canonical format registry.
 *
 * @return array<string, array{
 *   label: string,
 *   description: string,
 *   icon: string,
 *   order: int,
 *   nav: bool,
 *   legacy_slugs?: string[]
 * }>
 */
function msr_publishing_get_format_registry() {
	return array(
		'whitepaper' => array(
			'label'       => __( 'Whitepaper', 'msrsandbox' ),
			'description' => __( 'Long-form PDF assets with optional gated download flows.', 'msrsandbox' ),
			'icon'        => 'fa-file-pdf',
			'order'       => 10,
			'nav'         => true,
		),
		'webinar'    => array(
			'label'       => __( 'Webinar', 'msrsandbox' ),
			'description' => __( 'On-demand session recaps with registration, embedded replay, and speaker rosters.', 'msrsandbox' ),
			'icon'        => 'fa-video',
			'order'       => 20,
			'nav'         => true,
		),
		'briefing'   => array(
			'label'       => __( 'Briefing', 'msrsandbox' ),
			'description' => __( 'Executive summaries and market commentary for leadership readers.', 'msrsandbox' ),
			'icon'        => 'fa-briefcase',
			'order'       => 30,
			'nav'         => true,
			'legacy_slugs' => array( 'market-pulse', 'executive-briefing' ),
		),
		'playbook'   => array(
			'label'       => __( 'Playbook', 'msrsandbox' ),
			'description' => __( 'Operational guides, checklists, and field-ready how-to assets.', 'msrsandbox' ),
			'icon'        => 'fa-list-check',
			'order'       => 40,
			'nav'         => true,
			'legacy_slugs' => array( 'field-playbook' ),
		),
		'case-study' => array(
			'label'       => __( 'Case study', 'msrsandbox' ),
			'description' => __( 'Client outcomes and programme proof points for portfolio demonstrations.', 'msrsandbox' ),
			'icon'        => 'fa-chart-line',
			'order'       => 45,
			'nav'         => true,
		),
		'video'      => array(
			'label'       => __( 'Video', 'msrsandbox' ),
			'description' => __( 'Short-form explainers and embedded replays for portfolio demos.', 'msrsandbox' ),
			'icon'        => 'fa-circle-play',
			'order'       => 50,
			'nav'         => true,
		),
		'podcast'    => array(
			'label'       => __( 'Podcast', 'msrsandbox' ),
			'description' => __( 'Audio episodes and interview clips for Atlas Briefing demos.', 'msrsandbox' ),
			'icon'        => 'fa-podcast',
			'order'       => 60,
			'nav'         => true,
		),
	);
}

/**
 * Ordered canonical slugs (registry order).
 *
 * @return string[]
 */
function msr_publishing_get_format_slugs() {
	$registry = msr_publishing_get_format_registry();
	uasort(
		$registry,
		static function ( $a, $b ) {
			return ( $a['order'] ?? 0 ) <=> ( $b['order'] ?? 0 );
		}
	);

	return array_keys( $registry );
}

/**
 * Map legacy resource_type slugs to canonical slugs.
 *
 * @return array<string, string>
 */
function msr_publishing_get_legacy_format_slug_map() {
	$map = array();
	foreach ( msr_publishing_get_format_registry() as $slug => $config ) {
		foreach ( (array) ( $config['legacy_slugs'] ?? array() ) as $legacy ) {
			$map[ $legacy ] = $slug;
		}
	}

	return $map;
}

/**
 * Resolve a resource_type slug (legacy or canonical).
 *
 * @param string $slug Term slug.
 * @return string Canonical slug or original when unknown.
 */
function msr_publishing_resolve_format_slug( $slug ) {
	$slug = sanitize_title( (string) $slug );
	if ( $slug === '' ) {
		return '';
	}

	$map = msr_publishing_get_legacy_format_slug_map();
	if ( isset( $map[ $slug ] ) ) {
		return $map[ $slug ];
	}

	return $slug;
}

/**
 * Font Awesome icon class for a format slug.
 *
 * @param string $slug Canonical or legacy slug.
 * @return string
 */
function msr_publishing_get_format_icon_class( $slug ) {
	$slug     = msr_publishing_resolve_format_slug( $slug );
	$registry = msr_publishing_get_format_registry();

	if ( isset( $registry[ $slug ]['icon'] ) ) {
		return (string) $registry[ $slug ]['icon'];
	}

	return 'fa-file-lines';
}

/**
 * resource_type terms for nav/filter bars — registry order, optional hide_empty.
 *
 * @param array{hide_empty?: bool, nav_only?: bool, limit?: int} $args Options.
 * @return WP_Term[]
 */
function msr_publishing_get_resource_type_nav_terms( $args = array() ) {
	$hide_empty = array_key_exists( 'hide_empty', $args ) ? (bool) $args['hide_empty'] : true;
	$nav_only   = array_key_exists( 'nav_only', $args ) ? (bool) $args['nav_only'] : true;
	$limit      = isset( $args['limit'] ) ? max( 0, (int) $args['limit'] ) : 0;

	$registry = msr_publishing_get_format_registry();
	$terms    = array();

	foreach ( msr_publishing_get_format_slugs() as $slug ) {
		$config = $registry[ $slug ] ?? null;
		if ( ! $config ) {
			continue;
		}
		if ( $nav_only && empty( $config['nav'] ) ) {
			continue;
		}

		$term = get_term_by( 'slug', $slug, 'resource_type' );
		if ( ! $term || is_wp_error( $term ) ) {
			continue;
		}
		if ( $hide_empty && (int) $term->count < 1 ) {
			continue;
		}

		$terms[] = $term;
		if ( $limit > 0 && count( $terms ) >= $limit ) {
			break;
		}
	}

	return $terms;
}

/**
 * 301 redirect legacy resource_type archives to canonical terms.
 *
 * @return void
 */
function msr_publishing_maybe_redirect_legacy_format_archive() {
	if ( ! is_tax( 'resource_type' ) ) {
		return;
	}

	$term = get_queried_object();
	if ( ! $term instanceof WP_Term ) {
		return;
	}

	$canonical = msr_publishing_resolve_format_slug( $term->slug );
	if ( $canonical === $term->slug ) {
		return;
	}

	$target_term = get_term_by( 'slug', $canonical, 'resource_type' );
	if ( ! $target_term || is_wp_error( $target_term ) ) {
		return;
	}

	$target = get_term_link( $target_term );
	if ( is_wp_error( $target ) ) {
		return;
	}

	wp_safe_redirect( $target, 301 );
	exit;
}
add_action( 'template_redirect', 'msr_publishing_maybe_redirect_legacy_format_archive', 5 );

/**
 * 301 redirect legacy /resource-type/{slug}/ paths to /resources/{slug}/.
 *
 * @return void
 */
function msr_publishing_redirect_legacy_resource_type_path() {
	if ( is_admin() ) {
		return;
	}

	$path = (string) wp_parse_url( msr_publishing_get_current_request_url(), PHP_URL_PATH );
	if ( $path === '' || ! preg_match( '#/resource-type/([^/]+)/?$#', $path, $matches ) ) {
		return;
	}

	$slug = sanitize_title( $matches[1] );
	if ( $slug === '' ) {
		return;
	}

	$canonical = msr_publishing_resolve_format_slug( $slug );
	$term      = get_term_by( 'slug', $canonical, 'resource_type' );
	if ( ! $term || is_wp_error( $term ) ) {
		return;
	}

	$target = get_term_link( $term );
	if ( is_wp_error( $target ) ) {
		return;
	}

	wp_safe_redirect( $target, 301 );
	exit;
}
add_action( 'template_redirect', 'msr_publishing_redirect_legacy_resource_type_path', 4 );
