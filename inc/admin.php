<?php
/**
 * Admin-first helpers — resolve URLs and nav from WP (menus, pages, terms), not hardcoded paths.
 *
 * @package msrsandbox
 */

/**
 * Published page permalink by slug, with optional path fallback.
 *
 * @param string $slug     Page post_name.
 * @param string $fallback Relative path if page missing (e.g. `/insights/`).
 * @return string
 */
function msr_publishing_get_page_url( $slug, $fallback = '' ) {
	$page = get_page_by_path( $slug, OBJECT, 'page' );
	if ( $page instanceof WP_Post && 'publish' === $page->post_status ) {
		return (string) get_permalink( $page );
	}
	if ( '' !== $fallback ) {
		return home_url( $fallback );
	}
	return '';
}

/**
 * Topic hub archive URL — prefers a named slug, else first topic with posts.
 *
 * @param string $preferred_slug Topic slug (empty = first non-empty topic).
 * @return string
 */
function msr_publishing_get_topic_hub_url( $preferred_slug = '' ) {
	$topics_page = msr_publishing_get_page_url( 'topics', '/topics/' );
	if ( $topics_page ) {
		return $topics_page;
	}

	if ( '' !== $preferred_slug ) {
		$term = get_term_by( 'slug', $preferred_slug, 'topic' );
		if ( $term instanceof WP_Term ) {
			$link = get_term_link( $term );
			if ( ! is_wp_error( $link ) ) {
				return (string) $link;
			}
		}
	}

	$topics = get_terms(
		array(
			'taxonomy'   => 'topic',
			'hide_empty' => true,
			'number'     => 1,
			'orderby'    => 'count',
			'order'      => 'DESC',
		)
	);

	if ( $topics && ! is_wp_error( $topics ) ) {
		$link = get_term_link( $topics[0] );
		if ( ! is_wp_error( $link ) ) {
			return (string) $link;
		}
	}

	$archive = get_post_type_archive_link( 'resource' );
	return $archive ? $archive : home_url( '/resources/' );
}

/**
 * Nav links from a registered theme location (Appearance → Menus).
 *
 * @param string $location Theme location slug.
 * @return array<int, array{title: string, url: string}>
 */
function msr_publishing_get_nav_links_from_location( $location ) {
	$locations = get_nav_menu_locations();
	if ( empty( $locations[ $location ] ) ) {
		return array();
	}

	$items = wp_get_nav_menu_items( (int) $locations[ $location ] );
	if ( ! $items ) {
		return array();
	}

	$links = array();
	foreach ( $items as $item ) {
		if ( empty( $item->url ) || 'publish' !== $item->post_status ) {
			continue;
		}
		$links[] = array(
			'title' => $item->title,
			'url'   => $item->url,
		);
	}

	return $links;
}

/**
 * Fallback primary IA when no menu is assigned to menu-1.
 *
 * @return array<int, array{title: string, url: string}>
 */
function msr_publishing_get_primary_nav_fallback_links() {
	$links = array(
		array(
			'title' => __( 'Home', 'msrsandbox' ),
			'url'   => home_url( '/' ),
		),
	);

	$resources = get_post_type_archive_link( 'resource' );
	if ( $resources ) {
		$links[] = array(
			'title' => __( 'Resources', 'msrsandbox' ),
			'url'   => $resources,
		);
	}

	$links[] = array(
		'title' => __( 'Topics', 'msrsandbox' ),
		'url'   => msr_publishing_get_topic_hub_url(),
	);

	$insights = msr_publishing_get_page_url( 'insights', '/insights/' );
	if ( $insights ) {
		$links[] = array(
			'title' => __( 'Insights', 'msrsandbox' ),
			'url'   => $insights,
		);
	}

	$about = msr_publishing_get_page_url( 'about', '/about/' );
	if ( $about ) {
		$links[] = array(
			'title' => __( 'About', 'msrsandbox' ),
			'url'   => $about,
		);
	}

	$subscribe = msr_publishing_get_page_url( 'subscribe', '/subscribe/' );
	if ( $subscribe ) {
		$links[] = array(
			'title' => __( 'Subscribe', 'msrsandbox' ),
			'url'   => $subscribe,
		);
	}

	return $links;
}

/**
 * Exclude sponsored category terms on the front end (slug-based, not hardcoded ID).
 *
 * @param WP_Term[]|false $terms    Terms.
 * @param int             $post_id  Post ID.
 * @param string          $taxonomy Taxonomy.
 * @return WP_Term[]|false
 */
function msr_publishing_filter_sponsored_category_terms( $terms, $post_id, $taxonomy ) {
	if ( is_admin() || ! is_array( $terms ) || 'category' !== $taxonomy ) {
		return $terms;
	}

	$exclude = msr_publishing_get_excluded_sponsored_category_ids();
	if ( ! $exclude ) {
		return $terms;
	}

	foreach ( $terms as $key => $term ) {
		if ( $term instanceof WP_Term && in_array( (int) $term->term_id, $exclude, true ) ) {
			unset( $terms[ $key ] );
		}
	}

	return $terms;
}
add_filter( 'get_the_terms', 'msr_publishing_filter_sponsored_category_terms', 100, 3 );
