<?php
/**
 * Breadcrumbs for Atlas Briefing routes (single system — header only).
 *
 * @package msrsandbox
 */

/**
 * Breadcrumb trail for the current request.
 *
 * @return array<int, array{label: string, url: string, current: bool}>
 */
function msr_publishing_get_breadcrumb_trail() {
	if ( is_front_page() ) {
		return array();
	}

	$trail = array(
		array(
			'label'   => __( 'Home', 'msrsandbox' ),
			'url'     => home_url( '/' ),
			'current' => false,
		),
	);

	$push = static function ( $label, $url = '', $current = false ) use ( &$trail ) {
		$trail[] = array(
			'label'   => (string) $label,
			'url'     => (string) $url,
			'current' => (bool) $current,
		);
	};

	if ( is_post_type_archive( 'resource' ) ) {
		$push( __( 'Resources', 'msrsandbox' ), '', true );
		return $trail;
	}

	if ( is_tax( 'resource_type' ) ) {
		$archive = get_post_type_archive_link( 'resource' );
		if ( $archive ) {
			$push( __( 'Resources', 'msrsandbox' ), $archive );
		}
		$push( single_term_title( '', false ), '', true );
		return $trail;
	}

	if ( is_tax( 'topic' ) ) {
		$topics_hub = function_exists( 'msr_publishing_get_topic_hub_url' )
			? msr_publishing_get_topic_hub_url()
			: '';
		if ( $topics_hub ) {
			$push( __( 'Topics', 'msrsandbox' ), $topics_hub );
		}
		$push( single_term_title( '', false ), '', true );
		return $trail;
	}

	if ( is_tax( 'resource_series' ) ) {
		$archive = get_post_type_archive_link( 'resource' );
		if ( $archive ) {
			$push( __( 'Resources', 'msrsandbox' ), $archive );
		}
		$push( single_term_title( '', false ), '', true );
		return $trail;
	}

	if ( is_singular( 'resource' ) ) {
		$archive = get_post_type_archive_link( 'resource' );
		if ( $archive ) {
			$push( __( 'Resources', 'msrsandbox' ), $archive );
		}
		$term = msr_publishing_get_primary_resource_type( get_the_ID() );
		if ( $term ) {
			$link = get_term_link( $term );
			if ( ! is_wp_error( $link ) ) {
				$push( $term->name, $link );
			}
		}
		$series = function_exists( 'msr_publishing_get_resource_primary_series' )
			? msr_publishing_get_resource_primary_series( get_the_ID() )
			: null;
		if ( $series ) {
			$link = get_term_link( $series );
			if ( ! is_wp_error( $link ) ) {
				$push( $series->name, $link );
			}
		}
		$push( get_the_title(), '', true );
		return $trail;
	}

	if ( is_category() ) {
		$push( single_cat_title( '', false ), '', true );
		return $trail;
	}

	if ( is_singular( 'post' ) ) {
		$hub = function_exists( 'msr_publishing_get_post_topic_hub_link' )
			? msr_publishing_get_post_topic_hub_link( get_the_ID() )
			: null;
		if ( $hub ) {
			$push( $hub['name'], $hub['url'] );
		} else {
			$cats = get_the_category();
			if ( ! empty( $cats ) ) {
				$push( $cats[0]->name, get_category_link( $cats[0]->term_id ) );
			}
		}
		$push( get_the_title(), '', true );
		return $trail;
	}

	if ( is_author() ) {
		$insights = function_exists( 'msr_publishing_insights_url' ) ? msr_publishing_insights_url() : '';
		if ( $insights ) {
			$push( __( 'Commentary & insights', 'msrsandbox' ), $insights );
		}
		$author = get_queried_object();
		$label  = $author instanceof WP_User ? $author->display_name : __( 'Author', 'msrsandbox' );
		if ( function_exists( 'msr_publishing_get_expert_from_author_object' ) && $author instanceof WP_User ) {
			$expert = msr_publishing_get_expert_from_author_object( $author );
			if ( $expert && ! empty( $expert['name'] ) ) {
				$label = (string) $expert['name'];
			}
		}
		$push( $label, '', true );
		return $trail;
	}

	if ( is_search() ) {
		$query = get_search_query( false );
		$label = $query !== ''
			? sprintf(
				/* translators: %s: search query */
				__( 'Search: %s', 'msrsandbox' ),
				$query
			)
			: __( 'Search', 'msrsandbox' );
		$push( $label, '', true );
		return $trail;
	}

	if ( is_page() ) {
		$push( get_the_title(), '', true );
		return $trail;
	}

	if ( is_404() ) {
		$push( __( 'Page not found', 'msrsandbox' ), '', true );
		return $trail;
	}

	$trail[ count( $trail ) - 1 ]['current'] = true;
	return $trail;
}

/**
 * @return void
 */
function msr_publishing_the_breadcrumbs() {
	$trail = msr_publishing_get_breadcrumb_trail();
	if ( ! $trail ) {
		return;
	}

	echo '<nav class="msr-breadcrumbs" aria-label="' . esc_attr__( 'Breadcrumb', 'msrsandbox' ) . '">';
	echo '<div class="container">';
	echo '<ol class="msr-breadcrumbs__list">';

	foreach ( $trail as $crumb ) {
		echo '<li class="msr-breadcrumbs__item"';
		if ( ! empty( $crumb['current'] ) ) {
			echo ' aria-current="page"';
		}
		echo '>';
		if ( ! empty( $crumb['current'] ) || $crumb['url'] === '' ) {
			echo esc_html( $crumb['label'] );
		} else {
			echo '<a href="' . esc_url( $crumb['url'] ) . '">' . esc_html( $crumb['label'] ) . '</a>';
		}
		echo '</li>';
	}

	echo '</ol>';
	echo '</div>';
	echo '</nav>';
}
