<?php
/**
 * Theme setup and query defaults.
 *
 * @package msrsandbox
 */

/**
 * @return void
 */
function msr_publishing_theme_setup() {
	add_theme_support( 'post-thumbnails' );
	set_post_thumbnail_size( 1200, 9999 );

	add_theme_support( 'title-tag' );

	add_theme_support(
		'html5',
		array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
			'style',
			'script',
		)
	);

	add_theme_support(
		'custom-background',
		apply_filters(
			'msr_publishing_custom_background_args',
			array(
				'default-color' => 'ffffff',
				'default-image' => '',
			)
		)
	);

	add_theme_support( 'customize-selective-refresh-widgets' );

	add_theme_support(
		'custom-logo',
		array(
			'height'      => 250,
			'width'       => 250,
			'flex-width'  => true,
			'flex-height' => true,
		)
	);

	add_theme_support( 'editor-styles' );
	add_theme_support( 'wp-block-styles' );
	add_theme_support( 'align-wide' );

	$editor_css = get_template_directory() . '/dist/app.css';
	if ( file_exists( $editor_css ) ) {
		add_editor_style( 'dist/app.css' );
	}
}
add_action( 'after_setup_theme', 'msr_publishing_theme_setup' );

/**
 * Drop "Category:", "Tag:", etc. from archive headings.
 */
add_filter( 'get_the_archive_title_prefix', '__return_empty_string' );

/** @deprecated Use msr_publishing_theme_setup */
function msrsandbox_setup() {
	msr_publishing_theme_setup();
}

/**
 * @param string[] $classes Body classes.
 * @return string[]
 */
function msr_publishing_body_classes( $classes ) {
	$classes[] = 'msr-publishing';
	return $classes;
}
add_filter( 'body_class', 'msr_publishing_body_classes' );

/** @deprecated */
function msrsandbox_body_classes( $classes ) {
	return msr_publishing_body_classes( $classes );
}

/**
 * @param WP_Query $query Main query.
 * @return void
 */
function msr_publishing_archive_posts_per_page( $query ) {
	if ( ! is_admin() && $query->is_main_query() && $query->is_archive() ) {
		$query->set( 'posts_per_page', 18 );
	}
}
add_action( 'pre_get_posts', 'msr_publishing_archive_posts_per_page' );

/**
 * Legacy excerpt trim (deprecated listing partials).
 *
 * @param int $limit Word count.
 * @return string
 */
function msr_publishing_trim_excerpt( $limit ) {
	return wp_trim_words( get_the_excerpt(), $limit, '[...]' );
}

/** @deprecated */
function wpse_custom_excerpts( $limit ) {
	return msr_publishing_trim_excerpt( $limit );
}
