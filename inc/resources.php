<?php
/**
 * Resources CPT + resource_type taxonomy (ACF Free ecosystem plan).
 *
 * @package msrsandbox
 */

/**
 * Rewrite slug for publishing `topic` taxonomy (WP `category` uses category_base `topics`).
 *
 * @return string
 */
function msr_publishing_topic_rewrite_slug() {
	return 'briefing-topic';
}

/**
 * Rewrite slug for resource_type archives (nested under /resources/{format}/).
 *
 * @return string
 */
function msr_publishing_resource_type_rewrite_slug() {
	return 'resources';
}

/**
 * Register publishing resources.
 */
function msr_publishing_register_resources() {
	register_post_type(
		'resource',
		array(
			'labels'             => array(
				'name'          => 'Resources',
				'singular_name' => 'Resource',
				'add_new_item'  => 'Add New Resource',
				'edit_item'     => 'Edit Resource',
			),
			'public'             => true,
			'has_archive'        => true,
			'show_in_rest'       => true,
			'menu_icon'          => 'dashicons-media-document',
			'supports'           => array( 'title', 'editor', 'thumbnail', 'excerpt' ),
			'rewrite'            => array(
				'slug'       => 'resources',
				'with_front' => false,
			),
			'show_in_nav_menus'  => true,
			'publicly_queryable' => true,
		)
	);

	register_taxonomy(
		'resource_type',
		array( 'resource' ),
		array(
			'labels'            => array(
				'name'          => 'Resource types',
				'singular_name' => 'Resource type',
			),
			'hierarchical'      => true,
			'public'            => true,
			'show_ui'           => true,
			'show_admin_column' => true,
			'show_in_rest'      => true,
			'rewrite'           => false,
		)
	);

	register_taxonomy(
		'topic',
		array( 'resource', 'post' ),
		array(
			'labels'            => array(
				'name'          => 'Topics',
				'singular_name' => 'Topic',
			),
			'hierarchical'      => true,
			'public'            => true,
			'show_ui'           => true,
			'show_admin_column' => true,
			'show_in_rest'      => true,
			'rewrite'           => array( 'slug' => msr_publishing_topic_rewrite_slug() ),
		)
	);

	register_taxonomy(
		'resource_series',
		array( 'resource' ),
		array(
			'labels'            => array(
				'name'          => 'Resource series',
				'singular_name' => 'Resource series',
				'add_new_item'  => 'Add new series',
				'edit_item'     => 'Edit series',
			),
			'hierarchical'      => false,
			'public'            => true,
			'show_ui'           => true,
			'show_admin_column' => true,
			'show_in_rest'      => true,
			'rewrite'           => array( 'slug' => msr_publishing_series_rewrite_slug() ),
		)
	);
}
add_action( 'init', 'msr_publishing_register_resources', 11 );

/**
 * Explicit /resources/{format}/ rewrite rules (CPT singles share the resources base).
 *
 * @return void
 */
function msr_publishing_register_format_archive_rewrites() {
	if ( ! function_exists( 'msr_publishing_get_format_slugs' ) ) {
		return;
	}

	$base = msr_publishing_resource_type_rewrite_slug();
	foreach ( msr_publishing_get_format_slugs() as $slug ) {
		$slug = sanitize_title( $slug );
		if ( $slug === '' ) {
			continue;
		}

		add_rewrite_rule(
			'^' . preg_quote( $base, '/' ) . '/' . preg_quote( $slug, '/' ) . '/?$',
			'index.php?resource_type=' . $slug,
			'top'
		);
		add_rewrite_rule(
			'^' . preg_quote( $base, '/' ) . '/' . preg_quote( $slug, '/' ) . '/page/?([0-9]{1,})/?$',
			'index.php?resource_type=' . $slug . '&paged=$matches[1]',
			'top'
		);
	}
}
add_action( 'init', 'msr_publishing_register_format_archive_rewrites', 20 );

/**
 * @param string  $termlink Term URL.
 * @param WP_Term $term     Term.
 * @param string  $taxonomy Taxonomy slug.
 * @return string
 */
function msr_publishing_resource_type_term_link( $termlink, $term, $taxonomy ) {
	if ( 'resource_type' !== $taxonomy || ! $term instanceof WP_Term ) {
		return $termlink;
	}

	$slug = function_exists( 'msr_publishing_resolve_format_slug' )
		? msr_publishing_resolve_format_slug( $term->slug )
		: $term->slug;

	return home_url( user_trailingslashit( msr_publishing_resource_type_rewrite_slug() . '/' . $slug ) );
}
add_filter( 'term_link', 'msr_publishing_resource_type_term_link', 10, 3 );

/**
 * @param WP_Query $query Main query.
 */
function msr_publishing_pre_get_posts_resources( $query ) {
	if ( is_admin() || ! $query->is_main_query() ) {
		return;
	}
	if ( $query->is_post_type_archive( 'resource' ) || $query->is_tax( 'resource_type' ) || $query->is_tax( 'topic' ) || $query->is_tax( 'resource_series' ) ) {
		$query->set( 'posts_per_page', 12 );
	}
	if ( $query->is_tax( 'resource_type' ) || $query->is_tax( 'topic' ) || $query->is_tax( 'resource_series' ) ) {
		$query->set( 'post_type', 'resource' );
	}
	if ( $query->is_tax( 'resource_series' ) ) {
		$query->set( 'posts_per_page', -1 );
		$query->set( 'meta_key', 'series_reading_order' );
		$query->set(
			'orderby',
			array(
				'meta_value_num' => 'ASC',
				'date'           => 'ASC',
			)
		);
		$query->set( 'order', 'ASC' );
	}
}
add_action( 'pre_get_posts', 'msr_publishing_pre_get_posts_resources' );
