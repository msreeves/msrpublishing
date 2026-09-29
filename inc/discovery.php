<?php
/**
 * Unified discovery panel — format/topic/search filters in one modern layout.
 *
 * @package msrpublishing
 */

/**
 * Open discovery panel wrapper.
 *
 * @param string $legend Optional visible legend (empty = aria-only region).
 * @return void
 */
function msr_publishing_discovery_panel_open( $legend = '' ) {
	$legend = trim( (string) $legend );
	echo '<div class="publishing-discovery-band"><div class="container publishing-discovery-band__inner">';
	echo '<aside class="publishing-discovery" role="region"';
	if ( $legend !== '' ) {
		$id = 'publishing-discovery-' . wp_unique_id();
		printf( ' aria-labelledby="%s"', esc_attr( $id ) );
		echo '>';
		printf(
			'<p id="%s" class="publishing-discovery__legend">%s</p>',
			esc_attr( $id ),
			esc_html( $legend )
		);
	} else {
		echo ' aria-label="' . esc_attr__( 'Refine content', 'msrpublishing' ) . '">';
	}
}

/**
 * Close discovery panel.
 *
 * @return void
 */
function msr_publishing_discovery_panel_close() {
	echo '</aside></div></div>';
}

/**
 * Open one labeled row (label + horizontal chip track).
 *
 * @param string $label Row label (Format, Topic, etc.).
 * @return void
 */
function msr_publishing_discovery_row_open( $label ) {
	echo '<div class="publishing-discovery__row">';
	printf(
		'<span class="publishing-discovery__label" id="%s">%s</span>',
		esc_attr( 'publishing-discovery-label-' . wp_unique_id() ),
		esc_html( $label )
	);
	echo '<div class="publishing-discovery__track">';
}

/**
 * Close discovery row.
 *
 * @return void
 */
function msr_publishing_discovery_row_close() {
	echo '</div></div>';
}

/**
 * Append current browse sort query arg to a hub URL when on resource browse.
 *
 * @param string $url Term or archive URL.
 * @return string
 */
function msr_publishing_browse_url_preserve_sort( $url ) {
	if ( ! function_exists( 'msr_publishing_is_resource_browse' ) || ! msr_publishing_is_resource_browse() ) {
		return $url;
	}

	$sort = msr_publishing_get_browse_sort();
	if ( 'newest' === $sort ) {
		return $url;
	}

	return add_query_arg( 'msr_sort', $sort, $url );
}

/**
 * Resource library discovery (archive + format/topic hubs).
 *
 * @param string       $format_mode  all|term
 * @param WP_Term|null $format_term  Active format term.
 * @param string       $topic_mode   all|term
 * @param WP_Term|null $topic_term   Active topic term.
 * @param WP_Term|null $format_scope Scope topics on format archives.
 * @return void
 */
function msr_publishing_render_resource_discovery_panel(
	$format_mode = 'all',
	$format_term = null,
	$topic_mode = 'all',
	$topic_term = null,
	$format_scope = null
) {
	msr_publishing_discovery_panel_open();
	msr_publishing_render_resource_type_nav( $format_mode, $format_term, 'panel' );
	msr_publishing_render_topic_nav( $topic_mode, $topic_term, $format_scope, 'panel' );
	msr_publishing_render_resource_browse_sort_nav( 'panel' );
	msr_publishing_discovery_panel_close();
}

/**
 * Insights + commentary topic discovery.
 *
 * @param string $context insights|commentary
 * @return void
 */
function msr_publishing_render_commentary_discovery_panel( $context = 'insights' ) {
	msr_publishing_discovery_panel_open();
	if ( 'insights' === $context ) {
		msr_publishing_render_insights_topic_nav( 'panel' );
	} else {
		msr_publishing_render_commentary_topic_nav( false, 'panel' );
	}
	msr_publishing_discovery_panel_close();
}

/**
 * Search results discovery (type, topic, format, sort).
 *
 * @return void
 */
function msr_publishing_render_search_discovery_panel() {
	msr_publishing_discovery_panel_open( __( 'Refine results', 'msrpublishing' ) );
	msr_publishing_render_search_type_nav( 'panel' );
	msr_publishing_render_search_facet_nav( 'panel' );
	msr_publishing_render_search_sort_nav( 'panel' );
	msr_publishing_discovery_panel_close();
}

/**
 * Home discover strip — format + topic in one panel (no sort).
 *
 * @return void
 */
function msr_publishing_render_home_discover_panel() {
	msr_publishing_discovery_panel_open();
	msr_publishing_render_topic_nav( 'all', null, null, 'panel' );
	msr_publishing_discovery_panel_close();
}
