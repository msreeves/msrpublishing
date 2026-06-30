<?php
/**
 * Publishing search — query scope, filter pills, faceted topic/format filters.
 *
 * @package msrsandbox
 */

/**
 * Limit front-end search to commentary + resources.
 *
 * @param WP_Query $query Main query.
 * @return void
 */
function msr_publishing_pre_get_posts_search( $query ) {
	if ( is_admin() || ! $query->is_main_query() || ! $query->is_search() ) {
		return;
	}

	$type = isset( $_GET['post_type'] ) ? sanitize_key( wp_unslash( $_GET['post_type'] ) ) : '';

	if ( in_array( $type, array( 'post', 'resource' ), true ) ) {
		$query->set( 'post_type', $type );
	} else {
		$query->set( 'post_type', array( 'post', 'resource' ) );
	}

	$format_slug = msr_publishing_get_search_format_slug();
	if ( $format_slug !== '' ) {
		$query->set( 'post_type', 'resource' );
	}

	$tax_query = array();
	$topic_slug = msr_publishing_get_search_topic_slug();
	if ( $topic_slug !== '' ) {
		$tax_query[] = array(
			'taxonomy' => 'topic',
			'field'    => 'slug',
			'terms'    => array( $topic_slug ),
		);
	}
	if ( $format_slug !== '' ) {
		$format_slug = msr_publishing_resolve_format_slug( $format_slug );
		$tax_query[] = array(
			'taxonomy' => 'resource_type',
			'field'    => 'slug',
			'terms'    => array( $format_slug ),
		);
	}
	if ( $tax_query ) {
		$query->set(
			'tax_query',
			array_merge( array( 'relation' => 'AND' ), $tax_query )
		);
	}

	$sort = msr_publishing_get_search_sort();
	if ( 'newest' === $sort ) {
		$query->set( 'orderby', 'date' );
		$query->set( 'order', 'DESC' );
	} elseif ( 'oldest' === $sort ) {
		$query->set( 'orderby', 'date' );
		$query->set( 'order', 'ASC' );
	} elseif ( 'title' === $sort ) {
		$query->set( 'orderby', 'title' );
		$query->set( 'order', 'ASC' );
	}
}
add_action( 'pre_get_posts', 'msr_publishing_pre_get_posts_search', 20 );

/**
 * @return string all|post|resource
 */
function msr_publishing_get_search_filter() {
	$type = isset( $_GET['post_type'] ) ? sanitize_key( wp_unslash( $_GET['post_type'] ) ) : '';
	return in_array( $type, array( 'post', 'resource' ), true ) ? $type : 'all';
}

/**
 * @return string Topic slug or empty.
 */
function msr_publishing_get_search_topic_slug() {
	if ( ! isset( $_GET['topic'] ) ) {
		return '';
	}

	return sanitize_title( wp_unslash( (string) $_GET['topic'] ) );
}

/**
 * @return string Resource format slug or empty.
 */
function msr_publishing_get_search_format_slug() {
	if ( ! isset( $_GET['resource_type'] ) ) {
		return '';
	}

	return msr_publishing_resolve_format_slug(
		sanitize_title( wp_unslash( (string) $_GET['resource_type'] ) )
	);
}

/**
 * @return string relevance|newest|oldest|title
 */
function msr_publishing_get_search_sort() {
	if ( ! isset( $_GET['msr_sort'] ) ) {
		return 'relevance';
	}

	$sort = sanitize_key( wp_unslash( (string) $_GET['msr_sort'] ) );
	$allowed = array_keys( msr_publishing_get_search_sort_options() );

	return in_array( $sort, $allowed, true ) ? $sort : 'relevance';
}

/**
 * @return array<string, string> slug => label
 */
function msr_publishing_get_search_sort_options() {
	return array(
		'relevance' => __( 'Relevance', 'msrsandbox' ),
		'newest'    => __( 'Newest', 'msrsandbox' ),
		'oldest'    => __( 'Oldest', 'msrsandbox' ),
		'title'     => __( 'Title A–Z', 'msrsandbox' ),
	);
}

/**
 * Build search URL with optional overrides (empty string clears a facet).
 *
 * @param string|null $type_filter  all|post|resource|null = current.
 * @param string|null $topic_slug   Topic slug; empty string clears.
 * @param string|null $format_slug  Format slug; empty string clears.
 * @param string|null $sort         relevance|newest|oldest|title|null = current.
 * @return string
 */
function msr_publishing_build_search_url( $type_filter = null, $topic_slug = null, $format_slug = null, $sort = null ) {
	$args = array(
		's' => get_search_query(),
	);

	if ( null === $type_filter ) {
		$type_filter = msr_publishing_get_search_filter();
	}
	if ( 'all' !== $type_filter && in_array( $type_filter, array( 'post', 'resource' ), true ) ) {
		$args['post_type'] = $type_filter;
	}

	if ( null === $topic_slug ) {
		$topic_slug = msr_publishing_get_search_topic_slug();
	}
	if ( $topic_slug !== '' ) {
		$args['topic'] = $topic_slug;
	}

	if ( null === $format_slug ) {
		$format_slug = msr_publishing_get_search_format_slug();
	}
	if ( $format_slug !== '' ) {
		$args['resource_type'] = msr_publishing_resolve_format_slug( $format_slug );
	}

	if ( null === $sort ) {
		$sort = msr_publishing_get_search_sort();
	}
	if ( 'relevance' !== $sort && in_array( $sort, array_keys( msr_publishing_get_search_sort_options() ), true ) ) {
		$args['msr_sort'] = $sort;
	}

	return add_query_arg( $args, home_url( '/' ) );
}

/**
 * @param string $sort relevance|newest|oldest|title
 * @return string
 */
function msr_publishing_search_sort_url( $sort ) {
	return msr_publishing_build_search_url( null, null, null, $sort );
}

/**
 * @param string $filter all|post|resource
 * @return string
 */
function msr_publishing_search_filter_url( $filter ) {
	return msr_publishing_build_search_url( $filter, null, null );
}

/**
 * @return void
 */
function msr_publishing_render_search_type_nav() {
	$active = msr_publishing_get_search_filter();
	$items  = array(
		'all'      => __( 'All results', 'msrsandbox' ),
		'resource' => __( 'Resources', 'msrsandbox' ),
		'post'     => __( 'Commentary', 'msrsandbox' ),
	);
	?>
	<div class="publishing-search__filters mb-4">
		<?php
		msr_publishing_filter_bar_open( __( 'Filter search results', 'msrsandbox' ) );
		foreach ( $items as $slug => $label ) {
			msr_publishing_filter_bar_link( $label, msr_publishing_search_filter_url( $slug ), $active === $slug );
		}
		msr_publishing_filter_bar_close();
		?>
	</div>
	<?php
}

/**
 * Topic + format facet pills on search results (audit P3).
 *
 * @return void
 */
function msr_publishing_render_search_facet_nav() {
	$topics = get_terms(
		array(
			'taxonomy'   => 'topic',
			'hide_empty' => true,
		)
	);
	$formats = msr_publishing_get_resource_type_nav_terms( array( 'hide_empty' => true ) );

	if ( ( ! $topics || is_wp_error( $topics ) ) && empty( $formats ) ) {
		return;
	}

	$active_topic  = msr_publishing_get_search_topic_slug();
	$active_format = msr_publishing_get_search_format_slug();
	?>
	<div class="publishing-search__facets mb-4">
		<?php if ( $topics && ! is_wp_error( $topics ) ) : ?>
			<div class="publishing-search__facet-group resource-topic-nav">
				<p class="resource-archive-nav__label small text-uppercase fw-semibold text-muted mb-2"><?php esc_html_e( 'Topic', 'msrsandbox' ); ?></p>
				<?php
				msr_publishing_filter_bar_open( __( 'Filter by topic', 'msrsandbox' ) );
				msr_publishing_filter_bar_link(
					__( 'All topics', 'msrsandbox' ),
					msr_publishing_build_search_url( null, '', null ),
					$active_topic === ''
				);
				foreach ( $topics as $topic ) {
					msr_publishing_filter_bar_link(
						$topic->name,
						msr_publishing_build_search_url( null, $topic->slug, null ),
						$active_topic === $topic->slug
					);
				}
				msr_publishing_filter_bar_close();
				?>
			</div>
		<?php endif; ?>

		<?php if ( ! empty( $formats ) ) : ?>
			<div class="publishing-search__facet-group resource-type-nav mt-3">
				<p class="resource-archive-nav__label small text-uppercase fw-semibold text-muted mb-2"><?php esc_html_e( 'Format', 'msrsandbox' ); ?></p>
				<?php
				msr_publishing_filter_bar_open( __( 'Filter by format', 'msrsandbox' ) );
				msr_publishing_filter_bar_link(
					__( 'All formats', 'msrsandbox' ),
					msr_publishing_build_search_url( null, null, '' ),
					$active_format === ''
				);
				foreach ( $formats as $format ) {
					$is_active = $active_format === $format->slug
						|| ( $active_format !== '' && msr_publishing_resolve_format_slug( $active_format ) === $format->slug );
					msr_publishing_filter_bar_link(
						$format->name,
						msr_publishing_build_search_url( 'resource', null, $format->slug ),
						$is_active
					);
				}
				msr_publishing_filter_bar_close();
				?>
			</div>
		<?php endif; ?>
	</div>
	<?php
}

/**
 * Sort pills for search results (P56).
 *
 * @return void
 */
function msr_publishing_render_search_sort_nav() {
	$active  = msr_publishing_get_search_sort();
	$options = msr_publishing_get_search_sort_options();
	?>
	<div class="publishing-search__sort mb-4">
		<p class="resource-archive-nav__label small text-uppercase fw-semibold text-muted mb-2"><?php esc_html_e( 'Sort', 'msrsandbox' ); ?></p>
		<?php
		msr_publishing_filter_bar_open( __( 'Sort search results', 'msrsandbox' ) );
		foreach ( $options as $slug => $label ) {
			msr_publishing_filter_bar_link( $label, msr_publishing_search_sort_url( $slug ), $active === $slug );
		}
		msr_publishing_filter_bar_close();
		?>
	</div>
	<?php
}

/**
 * Default helpful links for empty states.
 *
 * @return array<int, array{title: string, url: string}>
 */
function msr_publishing_get_empty_state_default_links() {
	$links = array();

	$resources = get_post_type_archive_link( 'resource' );
	if ( $resources ) {
		$links[] = array(
			'title' => __( 'Browse resources', 'msrsandbox' ),
			'url'   => $resources,
		);
	}

	$insights = msr_publishing_insights_url();
	if ( $insights ) {
		$links[] = array(
			'title' => __( 'Insights hub', 'msrsandbox' ),
			'url'   => $insights,
		);
	}

	$topics = msr_publishing_topics_url();
	if ( $topics ) {
		$links[] = array(
			'title' => __( 'Topics', 'msrsandbox' ),
			'url'   => $topics,
		);
	}

	return $links;
}

/**
 * Unified empty-state panel (search + archives).
 *
 * @param array $args {
 *     @type string $context search|archive|listing.
 *     @type string $title   Heading.
 *     @type string $message Lead copy.
 *     @type bool   $search  Show site search form.
 *     @type array  $links   Helpful link buttons.
 * }
 * @return void
 */
function msr_publishing_render_empty_state( $args = array() ) {
	$context = isset( $args['context'] ) ? sanitize_key( (string) $args['context'] ) : 'listing';
	$title   = isset( $args['title'] ) ? (string) $args['title'] : '';
	$message = isset( $args['message'] ) ? (string) $args['message'] : '';
	$search  = ! empty( $args['search'] );
	$links   = isset( $args['links'] ) && is_array( $args['links'] ) ? $args['links'] : array();

	if ( '' === $title ) {
		switch ( $context ) {
			case 'search':
				$title = __( 'No results for that search', 'msrsandbox' );
				break;
			case 'archive':
				$title = __( 'Nothing published here yet', 'msrsandbox' );
				break;
			default:
				$title = __( 'Nothing to show yet', 'msrsandbox' );
		}
	}

	if ( '' === $message ) {
		switch ( $context ) {
			case 'search':
				$message = __( 'Try different keywords, browse popular searches below, or explore resources and commentary from the hubs.', 'msrsandbox' );
				$search  = true;
				break;
			case 'archive':
				$message = __( 'Check back when new commentary is published, or return to the insights hub.', 'msrsandbox' );
				break;
			default:
				$message = __( 'Content will appear here when published in the admin.', 'msrsandbox' );
		}
	}

	if ( ! $links ) {
		$links = msr_publishing_get_empty_state_default_links();
	}

	get_template_part(
		'template-parts/components/empty-state',
		null,
		array(
			'context' => $context,
			'title'   => $title,
			'message' => $message,
			'search'  => $search,
			'links'   => $links,
		)
	);
}

/**
 * Popular demo searches for empty search state.
 *
 * @return void
 */
function msr_publishing_render_search_popular_terms() {
	if ( ! is_search() ) {
		return;
	}

	$terms = array( 'workforce', 'resilience', 'webinar', 'distributed' );
	?>
	<nav class="publishing-search-popular text-center mt-4" aria-label="<?php esc_attr_e( 'Popular searches', 'msrsandbox' ); ?>">
		<p class="small text-muted mb-2"><?php esc_html_e( 'Popular searches', 'msrsandbox' ); ?></p>
		<div class="d-flex flex-wrap gap-2 justify-content-center">
			<?php foreach ( $terms as $term ) : ?>
				<a class="btn btn-outline-primary btn-sm publishing-search-popular__term" href="<?php echo esc_url( add_query_arg( 's', $term, home_url( '/' ) ) ); ?>">
					<?php echo esc_html( $term ); ?>
				</a>
			<?php endforeach; ?>
		</div>
	</nav>
	<?php
}

/**
 * @return void
 */
function msr_publishing_render_search_subscribe_band() {
	if ( ! is_search() ) {
		return;
	}
	msr_publishing_render_subscribe_cta( 'search' );
}
