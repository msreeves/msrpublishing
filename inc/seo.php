<?php
/**
 * Publishing SEO — meta description, Open Graph, Twitter, JSON-LD.
 *
 * Option A (theme hooks). Supersedes tenweb_meta_description on publishing routes.
 *
 * @package msrsandbox
 */

/**
 * Front-end routes that use Atlas Briefing SEO output.
 *
 * @return bool
 */
function msr_publishing_is_seo_route() {
	if ( is_admin() ) {
		return false;
	}

	if ( is_front_page()
		|| is_home()
		|| is_page( array( 'subscribe', 'insights', 'about', 'privacy', 'topics' ) )
		|| is_singular( array( 'resource', 'post' ) )
		|| is_post_type_archive( 'resource' )
		|| is_tax( array( 'resource_type', 'topic', 'resource_series' ) )
		|| is_category()
		|| is_search()
	) {
		return true;
	}

	return (bool) apply_filters( 'msr_publishing_is_seo_route', false );
}

/**
 * @return string
 */
function msr_publishing_seo_site_name() {
	return function_exists( 'msr_publishing_brand_name' )
		? msr_publishing_brand_name()
		: get_bloginfo( 'name', 'display' );
}

/**
 * @return string
 */
function msr_publishing_seo_canonical_url() {
	if ( is_singular() ) {
		return get_permalink();
	}
	if ( is_post_type_archive( 'resource' ) ) {
		return (string) get_post_type_archive_link( 'resource' );
	}
	if ( is_tax() || is_category() ) {
		$term = get_queried_object();
		if ( $term instanceof WP_Term ) {
			$link = get_term_link( $term );
			return is_wp_error( $link ) ? home_url( '/' ) : (string) $link;
		}
	}
	if ( is_search() ) {
		return get_search_link();
	}
	return home_url( '/' );
}

/**
 * @return string
 */
function msr_publishing_seo_title() {
	if ( is_singular() ) {
		$post_title = single_post_title( '', false );
		if ( $post_title !== '' ) {
			return $post_title . ' — ' . msr_publishing_seo_site_name();
		}
	}
	if ( is_post_type_archive( 'resource' ) ) {
		return __( 'Resources', 'msrsandbox' ) . ' — ' . msr_publishing_seo_site_name();
	}
	if ( is_tax() || is_category() ) {
		return single_term_title( '', false ) . ' — ' . msr_publishing_seo_site_name();
	}
	if ( is_search() ) {
		return sprintf(
			/* translators: %s: search query */
			__( 'Search results for "%s"', 'msrsandbox' ),
			get_search_query()
		) . ' — ' . msr_publishing_seo_site_name();
	}
	if ( is_front_page() || is_home() ) {
		return msr_publishing_seo_site_name() . ' — ' . get_bloginfo( 'description', 'display' );
	}
	if ( is_page() ) {
		return single_post_title( '', false ) . ' — ' . msr_publishing_seo_site_name();
	}

	return msr_publishing_seo_site_name();
}

/**
 * @return string
 */
function msr_publishing_seo_description() {
	if ( is_front_page() || is_home() ) {
		$desc = get_bloginfo( 'description', 'display' );
		if ( $desc !== '' && false === stripos( $desc, 'portfolio' ) ) {
			return $desc;
		}
		return msr_publishing_get_seo_description_option(
			'seo_home_description',
			__( 'Atlas Briefing — workforce briefings, whitepapers, webinars, and commentary on hiring and resilience.', 'msrsandbox' )
		);
	}
	if ( is_page( 'topics' ) ) {
		return msr_publishing_get_seo_description_option(
			'seo_topics_description',
			__( 'Browse Atlas Briefing topics — workforce, resilience, and related demonstration hubs.', 'msrsandbox' )
		);
	}
	if ( is_page( 'insights' ) ) {
		return msr_publishing_get_seo_description_option(
			'seo_insights_description',
			__( 'Commentary and editorial insights from Atlas Briefing — workforce and resilience analysis for portfolio review.', 'msrsandbox' )
		);
	}
	if ( is_page( 'about' ) ) {
		return msr_publishing_get_seo_description_option(
			'seo_about_description',
			__( 'About Atlas Briefing — methodology, formats, and demonstration disclaimer for portfolio review.', 'msrsandbox' )
		);
	}
	if ( is_page( 'privacy' ) ) {
		return __( 'Privacy notice (demonstration) for Atlas Briefing — portfolio placeholder, not legal advice.', 'msrsandbox' );
	}
	if ( is_page( 'subscribe' ) ) {
		return msr_publishing_get_seo_description_option(
			'seo_subscribe_description',
			__( 'Subscribe to Atlas Briefing — workforce briefings, whitepapers, webinars, and commentary. Demonstration signup for portfolio review.', 'msrsandbox' )
		);
	}
	if ( is_singular( 'resource' ) ) {
		$excerpt = get_the_excerpt();
		if ( $excerpt !== '' ) {
			return wp_strip_all_tags( $excerpt );
		}
	}
	if ( is_singular( 'post' ) ) {
		$excerpt = get_the_excerpt();
		if ( $excerpt !== '' ) {
			return wp_strip_all_tags( $excerpt );
		}
	}
	if ( is_singular() ) {
		$content = get_post_field( 'post_content', get_the_ID() );
		return wp_trim_words( wp_strip_all_tags( (string) $content ), 40, '…' );
	}
	if ( is_post_type_archive( 'resource' ) ) {
		return msr_publishing_get_seo_description_option(
			'seo_resources_archive_description',
			__( 'Atlas Briefing resource library — demonstration insights for portfolio review.', 'msrsandbox' )
		);
	}
	if ( is_tax() || is_category() ) {
		$term = get_queried_object();
		if ( $term instanceof WP_Term && $term->description !== '' ) {
			return wp_strip_all_tags( $term->description );
		}
	}
	if ( is_search() ) {
		return __( 'Search Atlas Briefing resources and commentary.', 'msrsandbox' );
	}
	return get_bloginfo( 'description', 'display' );
}

/**
 * @param string $url Attachment URL.
 * @return bool
 */
function msr_publishing_is_raster_image_url( $url ) {
	if ( '' === trim( (string) $url ) ) {
		return false;
	}

	$path = (string) parse_url( $url, PHP_URL_PATH );

	return ! preg_match( '/\.svg$/i', $path );
}

/**
 * Default social image from the featured resource hero (home / fallbacks).
 *
 * @return string
 */
function msr_publishing_seo_default_resource_image_url() {
	$query = new WP_Query(
		array(
			'post_type'           => 'resource',
			'posts_per_page'      => 1,
			'post_status'         => 'publish',
			'ignore_sticky_posts' => true,
			'no_found_rows'       => true,
			'meta_query'          => array(
				array(
					'key'     => 'featured',
					'value'   => '1',
					'compare' => '=',
				),
			),
		)
	);

	if ( ! $query->have_posts() ) {
		return '';
	}

	$query->the_post();
	$post_id = get_the_ID();
	$hero_id = function_exists( 'get_field' ) ? (int) get_field( 'hero_image', $post_id ) : 0;
	if ( ! $hero_id ) {
		$hero_id = (int) get_post_thumbnail_id( $post_id );
	}
	wp_reset_postdata();

	if ( ! $hero_id ) {
		return '';
	}

	$url = wp_get_attachment_image_url( $hero_id, 'large' );

	return ( $url && msr_publishing_is_raster_image_url( $url ) ) ? $url : '';
}

/**
 * Attachment ID for the current route's social image, or 0.
 *
 * @return int
 */
function msr_publishing_seo_image_attachment_id() {
	if ( is_singular( 'resource' ) ) {
		$post_id = get_the_ID();
		$hero_id = function_exists( 'get_field' ) ? (int) get_field( 'hero_image', $post_id ) : 0;
		if ( ! $hero_id ) {
			$hero_id = (int) get_post_thumbnail_id( $post_id );
		}
		return $hero_id;
	}
	if ( is_singular( 'post' ) && has_post_thumbnail() ) {
		return (int) get_post_thumbnail_id( get_the_ID() );
	}
	$logo_id = (int) get_theme_mod( 'custom_logo' );
	if ( $logo_id ) {
		return $logo_id;
	}
	return 0;
}

/**
 * Social image metadata for OG/Twitter + JSON-LD.
 *
 * @return array{url: string, alt: string, width: int, height: int}
 */
function msr_publishing_seo_image_meta() {
	$empty = array(
		'url'    => '',
		'alt'    => '',
		'width'  => 0,
		'height' => 0,
	);

	$attachment_id = msr_publishing_seo_image_attachment_id();
	if ( $attachment_id ) {
		$url = wp_get_attachment_image_url( $attachment_id, 'full' );
		if ( $url && msr_publishing_is_raster_image_url( $url ) ) {
			$meta = wp_get_attachment_metadata( $attachment_id );
			$alt  = trim( (string) get_post_meta( $attachment_id, '_wp_attachment_image_alt', true ) );
			if ( $alt === '' ) {
				$alt = get_the_title( $attachment_id );
			}
			return array(
				'url'    => $url,
				'alt'    => $alt,
				'width'  => isset( $meta['width'] ) ? (int) $meta['width'] : 0,
				'height' => isset( $meta['height'] ) ? (int) $meta['height'] : 0,
			);
		}
	}

	$default_url = msr_publishing_seo_default_resource_image_url();
	if ( $default_url !== '' ) {
		return array(
			'url'    => $default_url,
			'alt'    => __( 'Atlas Briefing featured resource', 'msrsandbox' ),
			'width'  => 0,
			'height' => 0,
		);
	}

	return $empty;
}

/**
 * @return string Image URL or empty.
 */
function msr_publishing_seo_image_url() {
	$meta = msr_publishing_seo_image_meta();
	return $meta['url'];
}

/**
 * @return array<int, array<string, mixed>>
 */
function msr_publishing_seo_breadcrumb_schema_items() {
	if ( ! function_exists( 'msr_publishing_get_breadcrumb_trail' ) ) {
		return array();
	}

	$trail = msr_publishing_get_breadcrumb_trail();
	if ( ! $trail ) {
		return array();
	}

	$items    = array();
	$position = 1;
	$canonical = function_exists( 'msr_publishing_seo_canonical_url' ) ? msr_publishing_seo_canonical_url() : '';

	foreach ( $trail as $crumb ) {
		$item = array(
			'@type'    => 'ListItem',
			'position' => $position++,
			'name'     => $crumb['label'],
		);

		if ( ! empty( $crumb['current'] ) ) {
			$item['item'] = $canonical !== '' ? $canonical : ( $crumb['url'] !== '' ? $crumb['url'] : home_url( '/' ) );
		} elseif ( $crumb['url'] !== '' ) {
			$item['item'] = $crumb['url'];
		}

		$items[] = $item;
	}

	return $items;
}

/**
 * JSON-LD ImageObject for the current route when a raster image exists.
 *
 * @return array<string, mixed>|null
 */
function msr_publishing_seo_schema_image() {
	$meta = msr_publishing_seo_image_meta();
	if ( $meta['url'] === '' ) {
		return null;
	}

	$image = array(
		'@type' => 'ImageObject',
		'url'   => $meta['url'],
	);
	if ( $meta['alt'] !== '' ) {
		$image['caption'] = $meta['alt'];
	}
	if ( $meta['width'] > 0 ) {
		$image['width'] = $meta['width'];
	}
	if ( $meta['height'] > 0 ) {
		$image['height'] = $meta['height'];
	}

	return $image;
}

/**
 * @return array<string, mixed>|null
 */
function msr_publishing_seo_primary_schema() {
	if ( is_singular( 'resource' ) ) {
		$post_id = get_the_ID();
		$schema  = array(
			'@context'    => 'https://schema.org',
			'@type'       => 'CreativeWork',
			'name'        => get_the_title(),
			'description' => msr_publishing_seo_description(),
			'url'         => get_permalink(),
			'datePublished' => get_the_date( 'c' ),
			'dateModified'  => get_the_modified_date( 'c' ),
			'publisher'   => array(
				'@type' => 'Organization',
				'name'  => msr_publishing_seo_site_name(),
			),
		);
		$guest = function_exists( 'get_field' ) ? (string) get_field( 'guest_author_name', $post_id ) : '';
		$schema_image = msr_publishing_seo_schema_image();
		if ( $schema_image ) {
			$schema['image'] = $schema_image;
		}
		if ( $guest !== '' ) {
			$schema['author'] = array(
				'@type' => 'Person',
				'name'  => $guest,
			);
		} else {
			$author_meta = msr_publishing_get_content_author_meta( $post_id );
			if ( $author_meta['name'] !== '' ) {
				$schema['author'] = array(
					'@type' => 'Person',
					'name'  => $author_meta['name'],
				);
			}
		}
		return $schema;
	}

	if ( is_singular( 'post' ) ) {
		$schema = array(
			'@context'      => 'https://schema.org',
			'@type'         => 'Article',
			'headline'      => get_the_title(),
			'description'   => msr_publishing_seo_description(),
			'url'           => get_permalink(),
			'datePublished' => get_the_date( 'c' ),
			'dateModified'  => get_the_modified_date( 'c' ),
			'publisher'     => array(
				'@type' => 'Organization',
				'name'  => msr_publishing_seo_site_name(),
			),
		);
		$author_meta = msr_publishing_get_content_author_meta( get_the_ID() );
		if ( $author_meta['name'] !== '' ) {
			$schema['author'] = array(
				'@type' => 'Person',
				'name'  => $author_meta['name'],
			);
		}
		$schema_image = msr_publishing_seo_schema_image();
		if ( $schema_image ) {
			$schema['image'] = $schema_image;
		}
		return $schema;
	}

	if ( is_page( array( 'about', 'insights', 'subscribe', 'privacy', 'topics' ) ) ) {
		return array(
			'@context'    => 'https://schema.org',
			'@type'       => 'WebPage',
			'name'        => wp_strip_all_tags( msr_publishing_seo_title() ),
			'description' => msr_publishing_seo_description(),
			'url'         => msr_publishing_seo_canonical_url(),
		);
	}

	if ( is_front_page() ) {
		return array(
			'@context'    => 'https://schema.org',
			'@type'       => 'WebSite',
			'name'        => msr_publishing_seo_site_name(),
			'description' => msr_publishing_seo_description(),
			'url'         => home_url( '/' ),
		);
	}

	return null;
}

/**
 * @return void
 */
function msr_publishing_output_seo_tags() {
	if ( ! msr_publishing_is_seo_route() ) {
		return;
	}

	$title       = msr_publishing_seo_title();
	$description = msr_publishing_seo_description();
	$url         = msr_publishing_seo_canonical_url();
	$image_meta  = msr_publishing_seo_image_meta();
	$image       = $image_meta['url'];
	$og_type     = is_singular( 'resource' ) ? 'article' : ( is_singular( 'post' ) ? 'article' : 'website' );

	echo '<meta name="description" content="' . esc_attr( $description ) . '" />' . "\n";
	echo '<link rel="canonical" href="' . esc_url( $url ) . '" />' . "\n";

	echo '<meta property="og:site_name" content="' . esc_attr( msr_publishing_seo_site_name() ) . '" />' . "\n";
	echo '<meta property="og:title" content="' . esc_attr( wp_strip_all_tags( $title ) ) . '" />' . "\n";
	echo '<meta property="og:description" content="' . esc_attr( $description ) . '" />' . "\n";
	echo '<meta property="og:url" content="' . esc_url( $url ) . '" />' . "\n";
	echo '<meta property="og:type" content="' . esc_attr( $og_type ) . '" />' . "\n";
	echo '<meta property="og:locale" content="' . esc_attr( str_replace( '_', '-', get_locale() ) ) . '" />' . "\n";

	if ( is_singular( array( 'resource', 'post' ) ) ) {
		$post_id = get_the_ID();
		echo '<meta property="article:published_time" content="' . esc_attr( get_the_date( 'c', $post_id ) ) . '" />' . "\n";
		echo '<meta property="article:modified_time" content="' . esc_attr( get_the_modified_date( 'c', $post_id ) ) . '" />' . "\n";
	}

	if ( $image !== '' ) {
		echo '<meta property="og:image" content="' . esc_url( $image ) . '" />' . "\n";
		if ( $image_meta['alt'] !== '' ) {
			echo '<meta property="og:image:alt" content="' . esc_attr( $image_meta['alt'] ) . '" />' . "\n";
		}
		if ( $image_meta['width'] > 0 ) {
			echo '<meta property="og:image:width" content="' . esc_attr( (string) $image_meta['width'] ) . '" />' . "\n";
		}
		if ( $image_meta['height'] > 0 ) {
			echo '<meta property="og:image:height" content="' . esc_attr( (string) $image_meta['height'] ) . '" />' . "\n";
		}
	}

	echo '<meta name="twitter:card" content="' . esc_attr( $image !== '' ? 'summary_large_image' : 'summary' ) . '" />' . "\n";
	echo '<meta name="twitter:title" content="' . esc_attr( wp_strip_all_tags( $title ) ) . '" />' . "\n";
	echo '<meta name="twitter:description" content="' . esc_attr( $description ) . '" />' . "\n";
	if ( $image !== '' ) {
		echo '<meta name="twitter:image" content="' . esc_url( $image ) . '" />' . "\n";
	}

	$schemas = array();
	$primary = msr_publishing_seo_primary_schema();
	if ( $primary ) {
		$schemas[] = $primary;
	}

	$crumbs = msr_publishing_seo_breadcrumb_schema_items();
	if ( count( $crumbs ) > 1 ) {
		$schemas[] = array(
			'@context'        => 'https://schema.org',
			'@type'           => 'BreadcrumbList',
			'itemListElement' => $crumbs,
		);
	}

	foreach ( $schemas as $schema ) {
		echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>' . "\n";
	}
}
add_action( 'wp_head', 'msr_publishing_output_seo_tags', 1 );

/**
 * Use publishing SEO titles in the document <title> (title-tag support).
 *
 * @param string $title Default document title.
 * @return string
 */
function msr_publishing_filter_document_title( $title ) {
	if ( msr_publishing_is_seo_route() ) {
		return msr_publishing_seo_title();
	}
	return $title;
}
add_filter( 'pre_get_document_title', 'msr_publishing_filter_document_title' );
