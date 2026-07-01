<?php
/**
 * Home social proof band — stats + programme logos (P50).
 *
 * @package msrsandbox
 */

/**
 * Published count for a post type.
 *
 * @param string $post_type Post type slug.
 * @return int
 */
function msr_publishing_count_published( $post_type ) {
	$counts = wp_count_posts( $post_type );
	if ( ! $counts || ! isset( $counts->publish ) ) {
		return 0;
	}

	return (int) $counts->publish;
}

/**
 * Default stats when ACF repeater is empty.
 *
 * @return array<int, array{value: string, label: string}>
 */
function msr_publishing_get_home_social_proof_stats_defaults() {
	$resources   = msr_publishing_count_published( 'resource' );
	$commentary  = msr_publishing_count_published( 'post' );
	$programmes  = count( msr_publishing_get_ecosystem_programmes() );

	return array(
		array(
			'value' => $resources > 0 ? (string) $resources : '20',
			'label' => __( 'Resources', 'msrsandbox' ),
		),
		array(
			'value' => '6',
			'label' => __( 'Formats', 'msrsandbox' ),
		),
		array(
			'value' => $commentary > 0 ? (string) $commentary : '12',
			'label' => __( 'Insights', 'msrsandbox' ),
		),
		array(
			'value' => $programmes > 0 ? (string) $programmes : '3',
			'label' => __( 'MSR programmes', 'msrsandbox' ),
		),
	);
}

/**
 * Home social proof stats from ACF or computed defaults.
 *
 * @return array<int, array{value: string, label: string}>
 */
function msr_publishing_get_home_social_proof_stats() {
	if ( ! function_exists( 'get_field' ) ) {
		return msr_publishing_get_home_social_proof_stats_defaults();
	}

	$rows = get_field( 'home_social_proof_stats', 'option' );
	if ( ! is_array( $rows ) || ! $rows ) {
		return msr_publishing_get_home_social_proof_stats_defaults();
	}

	$stats = array();
	foreach ( $rows as $row ) {
		if ( ! is_array( $row ) ) {
			continue;
		}
		$value = isset( $row['stat_value'] ) ? trim( (string) $row['stat_value'] ) : '';
		$label = isset( $row['stat_label'] ) ? trim( (string) $row['stat_label'] ) : '';
		if ( $value === '' || $label === '' ) {
			continue;
		}
		$stats[] = array(
			'value' => $value,
			'label' => $label,
		);
	}

	return $stats ? $stats : msr_publishing_get_home_social_proof_stats_defaults();
}

/**
 * Default programme logos when ACF repeater is empty.
 *
 * @return array<int, array{name: string, url: string, logo_id: int}>
 */
function msr_publishing_get_home_social_proof_logos_defaults() {
	$logos = array();
	foreach ( msr_publishing_get_ecosystem_programmes() as $programme ) {
		if ( empty( $programme['label'] ) ) {
			continue;
		}
		$logos[] = array(
			'name'    => (string) $programme['label'],
			'url'     => isset( $programme['url'] ) ? (string) $programme['url'] : '',
			'logo_id' => 0,
		);
	}

	if ( $logos ) {
		return $logos;
	}

	return array(
		array(
			'name'    => __( 'MSR Events hub', 'msrsandbox' ),
			'url'     => msr_publishing_resolve_programme_url( 'http://msrevents.local:8888/' ),
			'logo_id' => 0,
		),
		array(
			'name'    => __( 'MSR Awards', 'msrsandbox' ),
			'url'     => msr_publishing_resolve_programme_url( 'http://msrevents.local:8888/msrawards/' ),
			'logo_id' => 0,
		),
		array(
			'name'    => __( 'MSR Seminars', 'msrsandbox' ),
			'url'     => msr_publishing_resolve_programme_url( 'http://msrevents.local:8888/msrseminars/' ),
			'logo_id' => 0,
		),
	);
}

/**
 * Home social proof logos from ACF or programme registry defaults.
 *
 * @return array<int, array{name: string, url: string, logo_id: int}>
 */
function msr_publishing_get_home_social_proof_logos() {
	if ( ! function_exists( 'get_field' ) ) {
		return msr_publishing_get_home_social_proof_logos_defaults();
	}

	$rows = get_field( 'home_social_proof_logos', 'option' );
	if ( ! is_array( $rows ) || ! $rows ) {
		return msr_publishing_get_home_social_proof_logos_defaults();
	}

	$logos = array();
	foreach ( $rows as $row ) {
		if ( ! is_array( $row ) ) {
			continue;
		}
		$name = isset( $row['org_name'] ) ? trim( (string) $row['org_name'] ) : '';
		if ( $name === '' ) {
			continue;
		}
		$url     = isset( $row['link_url'] ) ? trim( (string) $row['link_url'] ) : '';
		$logo_id = isset( $row['logo'] ) ? (int) $row['logo'] : 0;
		$logos[] = array(
			'name'    => $name,
			'url'     => $url,
			'logo_id' => $logo_id,
		);
	}

	return $logos ? $logos : msr_publishing_get_home_social_proof_logos_defaults();
}

/**
 * Whether the home social proof band should render.
 *
 * @return bool
 */
function msr_publishing_home_social_proof_enabled() {
	return msr_publishing_get_option_bool( 'home_social_proof_enabled', true );
}

/**
 * Eyebrow for the home social proof band.
 *
 * @return string
 */
function msr_publishing_get_home_social_proof_eyebrow() {
	return msr_publishing_get_option_string(
		'home_social_proof_eyebrow',
		__( 'Programme reach', 'msrsandbox' )
	);
}

/**
 * Render home social proof band (stats + logos).
 *
 * @return void
 */
function msr_publishing_render_home_social_proof() {
	if ( ! is_front_page() || ! msr_publishing_home_social_proof_enabled() ) {
		return;
	}

	$stats = msr_publishing_get_home_social_proof_stats();
	$logos = msr_publishing_get_home_social_proof_logos();
	if ( ! $stats && ! $logos ) {
		return;
	}

	get_template_part(
		'template-parts/sections/home',
		'social-proof',
		array(
			'eyebrow' => msr_publishing_get_home_social_proof_eyebrow(),
			'stats'   => $stats,
			'logos'   => $logos,
		)
	);
}
