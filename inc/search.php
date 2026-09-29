<?php
/**
 * Publishing search — query scope, filter pills, faceted topic/format filters.
 *
 * @package msrpublishing
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

	if ( function_exists( 'msr_publishing_get_density_filler_post_ids' ) ) {
		$exclude = msr_publishing_get_density_filler_post_ids();
		if ( $exclude ) {
			$query->set( 'post__not_in', array_merge( (array) $query->get( 'post__not_in' ), $exclude ) );
		}
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
		'relevance' => __( 'Relevance', 'msrpublishing' ),
		'newest'    => __( 'Newest', 'msrpublishing' ),
		'oldest'    => __( 'Oldest', 'msrpublishing' ),
		'title'     => __( 'Title A–Z', 'msrpublishing' ),
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
 * Query args to preserve on search pagination links.
 *
 * @return array<string, string>
 */
function msr_publishing_get_search_pagination_add_args() {
	if ( ! is_search() ) {
		return array();
	}

	$args = array(
		's' => get_search_query(),
	);

	$type = msr_publishing_get_search_filter();
	if ( 'all' !== $type ) {
		$args['post_type'] = $type;
	}

	$topic = msr_publishing_get_search_topic_slug();
	if ( $topic !== '' ) {
		$args['topic'] = $topic;
	}

	$format = msr_publishing_get_search_format_slug();
	if ( $format !== '' ) {
		$args['resource_type'] = $format;
	}

	$sort = msr_publishing_get_search_sort();
	if ( 'relevance' !== $sort ) {
		$args['msr_sort'] = $sort;
	}

	return $args;
}

/**
 * Whether any search facet (besides the keyword) is active.
 *
 * @return bool
 */
function msr_publishing_search_has_active_filters() {
	return 'all' !== msr_publishing_get_search_filter()
		|| msr_publishing_get_search_topic_slug() !== ''
		|| msr_publishing_get_search_format_slug() !== ''
		|| 'relevance' !== msr_publishing_get_search_sort();
}

/**
 * URL with keyword only (clears facets).
 *
 * @return string
 */
function msr_publishing_search_clear_filters_url() {
	return add_query_arg(
		array(
			's' => get_search_query(),
		),
		home_url( '/' )
	);
}

/**
 * Prominent search form on the search results page.
 *
 * @return void
 */
function msr_publishing_render_search_page_form() {
	if ( ! is_search() ) {
		return;
	}
	?>
	<div class="publishing-search-page__form msr-reveal">
		<?php
		get_template_part(
			'template-parts/forms/site-search',
			null,
			array(
				'input_id' => 'msr-site-search-page-input',
				'layout'   => 'page',
			)
		);
		?>
	</div>
	<?php
}

/**
 * Active facet chips + clear control above results.
 *
 * @return void
 */
function msr_publishing_render_search_active_filters() {
	if ( ! is_search() || ! msr_publishing_search_has_active_filters() ) {
		return;
	}

	$chips = array();

	$type = msr_publishing_get_search_filter();
	if ( 'resource' === $type ) {
		$chips[] = array(
			'label' => __( 'Resources only', 'msrpublishing' ),
			'url'   => msr_publishing_search_filter_url( 'all' ),
		);
	} elseif ( 'post' === $type ) {
		$chips[] = array(
			'label' => __( 'Insights only', 'msrpublishing' ),
			'url'   => msr_publishing_search_filter_url( 'all' ),
		);
	}

	$topic_slug = msr_publishing_get_search_topic_slug();
	if ( $topic_slug !== '' ) {
		$term = get_term_by( 'slug', $topic_slug, 'topic' );
		$chips[] = array(
			'label' => $term && ! is_wp_error( $term ) ? $term->name : $topic_slug,
			'url'   => msr_publishing_build_search_url( null, '', null ),
		);
	}

	$format_slug = msr_publishing_get_search_format_slug();
	if ( $format_slug !== '' ) {
		$term = get_term_by( 'slug', $format_slug, 'resource_type' );
		if ( ! $term || is_wp_error( $term ) ) {
			$term = get_term_by( 'slug', msr_publishing_resolve_format_slug( $format_slug ), 'resource_type' );
		}
		$chips[] = array(
			'label' => $term && ! is_wp_error( $term ) ? $term->name : $format_slug,
			'url'   => msr_publishing_build_search_url( null, null, '' ),
		);
	}

	$sort = msr_publishing_get_search_sort();
	if ( 'relevance' !== $sort ) {
		$labels = msr_publishing_get_search_sort_options();
		$chips[] = array(
			'label' => isset( $labels[ $sort ] ) ? $labels[ $sort ] : $sort,
			'url'   => msr_publishing_search_sort_url( 'relevance' ),
		);
	}

	if ( ! $chips ) {
		return;
	}
	?>
	<div class="publishing-search-page__active-filters" aria-label="<?php esc_attr_e( 'Active search filters', 'msrpublishing' ); ?>">
		<ul class="publishing-search-page__active-list">
			<?php foreach ( $chips as $chip ) : ?>
				<li>
					<a class="publishing-search-page__active-chip" href="<?php echo esc_url( $chip['url'] ); ?>">
						<span><?php echo esc_html( $chip['label'] ); ?></span>
						<span class="publishing-search-page__active-remove" aria-hidden="true">&times;</span>
						<span class="screen-reader-text"><?php esc_html_e( 'Remove filter', 'msrpublishing' ); ?></span>
					</a>
				</li>
			<?php endforeach; ?>
		</ul>
		<a class="publishing-search-page__clear-filters small" href="<?php echo esc_url( msr_publishing_search_clear_filters_url() ); ?>">
			<?php esc_html_e( 'Clear all filters', 'msrpublishing' ); ?>
		</a>
	</div>
	<?php
}

/**
 * @return void
 */
function msr_publishing_render_search_posts_pagination() {
	the_posts_pagination(
		array(
			'mid_size'  => 2,
			'prev_text' => __( 'Previous', 'msrpublishing' ),
			'next_text' => __( 'Next', 'msrpublishing' ),
			'add_args'  => msr_publishing_get_search_pagination_add_args(),
		)
	);
}

/**
 * @return void
 */
function msr_publishing_render_search_type_nav( $layout = 'standalone' ) {
	$active = msr_publishing_get_search_filter();
	$items  = array(
		'all'      => __( 'All results', 'msrpublishing' ),
		'resource' => __( 'Resources', 'msrpublishing' ),
		'post'     => __( 'Insights', 'msrpublishing' ),
	);
	$variant = 'panel' === $layout ? 'chips' : 'underline';

	$render_bar = static function () use ( $items, $active, $variant ) {
		msr_publishing_filter_bar_open( __( 'Filter search results', 'msrpublishing' ), false, $variant );
		foreach ( $items as $slug => $label ) {
			msr_publishing_filter_bar_link( $label, msr_publishing_search_filter_url( $slug ), $active === $slug );
		}
		msr_publishing_filter_bar_close();
	};

	if ( 'panel' === $layout ) {
		msr_publishing_discovery_row_open( __( 'Show', 'msrpublishing' ) );
		$render_bar();
		msr_publishing_discovery_row_close();
		return;
	}

	?>
	<div class="publishing-search__filters mb-4">
		<?php $render_bar(); ?>
	</div>
	<?php
}

/**
 * Topic + format facet pills on search results (audit P3).
 *
 * @return void
 */
function msr_publishing_render_search_facet_nav( $layout = 'standalone' ) {
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
	$variant       = 'panel' === $layout ? 'chips' : 'underline';

	if ( $topics && ! is_wp_error( $topics ) ) {
		$row = static function () use ( $topics, $active_topic, $variant ) {
			msr_publishing_filter_bar_open( __( 'Filter by topic', 'msrpublishing' ), false, $variant );
			msr_publishing_filter_bar_link(
				__( 'All topics', 'msrpublishing' ),
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
		};
		if ( 'panel' === $layout ) {
			msr_publishing_discovery_row_open( __( 'Topic', 'msrpublishing' ) );
			$row();
			msr_publishing_discovery_row_close();
		} else {
			?>
			<div class="publishing-search__facet-group resource-topic-nav mb-4">
				<p class="resource-archive-nav__label small text-uppercase fw-semibold text-muted mb-2"><?php esc_html_e( 'Topic', 'msrpublishing' ); ?></p>
				<?php $row(); ?>
			</div>
			<?php
		}
	}

	$show_formats = ! empty( $formats ) && 'post' !== msr_publishing_get_search_filter();

	if ( $show_formats ) {
		$row = static function () use ( $formats, $active_format, $variant ) {
			msr_publishing_filter_bar_open( __( 'Filter by format', 'msrpublishing' ), false, $variant );
			msr_publishing_filter_bar_link(
				__( 'All formats', 'msrpublishing' ),
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
		};
		if ( 'panel' === $layout ) {
			msr_publishing_discovery_row_open( __( 'Format', 'msrpublishing' ) );
			$row();
			msr_publishing_discovery_row_close();
		} else {
			?>
			<div class="publishing-search__facet-group resource-type-nav mb-4">
				<p class="resource-archive-nav__label small text-uppercase fw-semibold text-muted mb-2"><?php esc_html_e( 'Format', 'msrpublishing' ); ?></p>
				<?php $row(); ?>
			</div>
			<?php
		}
	}
}

/**
 * Sort pills for search results (P56).
 *
 * @return void
 */
function msr_publishing_render_search_sort_nav( $layout = 'standalone' ) {
	$active  = msr_publishing_get_search_sort();
	$options = msr_publishing_get_search_sort_options();
	$variant = 'panel' === $layout ? 'chips' : 'underline';

	$render_bar = static function () use ( $options, $active, $variant ) {
		msr_publishing_filter_bar_open( __( 'Sort search results', 'msrpublishing' ), false, $variant );
		foreach ( $options as $slug => $label ) {
			msr_publishing_filter_bar_link( $label, msr_publishing_search_sort_url( $slug ), $active === $slug );
		}
		msr_publishing_filter_bar_close();
	};

	if ( 'panel' === $layout ) {
		msr_publishing_discovery_row_open( __( 'Sort', 'msrpublishing' ) );
		$render_bar();
		msr_publishing_discovery_row_close();
		return;
	}

	?>
	<div class="publishing-search__sort mb-4">
		<p class="resource-archive-nav__label small text-uppercase fw-semibold text-muted mb-2"><?php esc_html_e( 'Sort', 'msrpublishing' ); ?></p>
		<?php $render_bar(); ?>
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
			'title' => __( 'Browse resources', 'msrpublishing' ),
			'url'   => $resources,
		);
	}

	$insights = msr_publishing_insights_url();
	if ( $insights ) {
		$links[] = array(
			'title' => __( 'Insights hub', 'msrpublishing' ),
			'url'   => $insights,
		);
	}

	$topics = msr_publishing_topics_url();
	if ( $topics ) {
		$links[] = array(
			'title' => __( 'Topics', 'msrpublishing' ),
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
				$title = __( 'No results for that search', 'msrpublishing' );
				break;
			case 'archive':
				$title = __( 'Nothing published here yet', 'msrpublishing' );
				break;
			default:
				$title = __( 'Nothing to show yet', 'msrpublishing' );
		}
	}

	if ( '' === $message ) {
		switch ( $context ) {
			case 'search':
				$message = __( 'Try different keywords or browse resources, topics, and insights using the links below.', 'msrpublishing' );
				$search  = true;
				break;
			case 'archive':
				$message = __( 'Check back when new commentary is published, or return to the insights hub.', 'msrpublishing' );
				break;
			default:
				$message = __( 'Content will appear here when published in the admin.', 'msrpublishing' );
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

	$links = array();

	$resources = get_post_type_archive_link( 'resource' );
	if ( $resources ) {
		$links[] = array(
			'label' => __( 'All resources', 'msrpublishing' ),
			'url'   => $resources,
		);
	}

	$topics = function_exists( 'msr_publishing_topics_url' ) ? msr_publishing_topics_url() : '';
	if ( $topics ) {
		$links[] = array(
			'label' => __( 'Topics', 'msrpublishing' ),
			'url'   => $topics,
		);
	}

	$topic_terms = get_terms(
		array(
			'taxonomy'   => 'topic',
			'hide_empty' => false,
			'number'     => 4,
		)
	);
	if ( $topic_terms && ! is_wp_error( $topic_terms ) ) {
		foreach ( $topic_terms as $topic ) {
			$link = get_term_link( $topic );
			if ( is_wp_error( $link ) ) {
				continue;
			}
			$links[] = array(
				'label' => $topic->name,
				'url'   => $link,
			);
		}
	}

	$insights = function_exists( 'msr_publishing_insights_url' ) ? msr_publishing_insights_url() : '';
	if ( $insights ) {
		$links[] = array(
			'label' => __( 'Insights hub', 'msrpublishing' ),
			'url'   => $insights,
		);
	}

	if ( ! $links ) {
		return;
	}
	?>
	<nav class="publishing-search-popular text-center mt-4" aria-label="<?php esc_attr_e( 'Browse hubs', 'msrpublishing' ); ?>">
		<p class="small text-muted mb-2"><?php esc_html_e( 'Browse instead', 'msrpublishing' ); ?></p>
		<div class="d-flex flex-wrap gap-2 justify-content-center">
			<?php foreach ( $links as $link ) : ?>
				<a class="btn btn-outline-primary btn-sm publishing-search-popular__term" href="<?php echo esc_url( $link['url'] ); ?>">
					<?php echo esc_html( $link['label'] ); ?>
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

/**
 * Mixed search can rank resources above commentary on page 1 — boost up to N posts for the grid.
 *
 * @param WP_Query $main_query Main search query.
 * @return WP_Post[]
 */
function msr_publishing_get_search_commentary_boost_posts( WP_Query $main_query ) {
	if ( ! $main_query->is_search() || msr_publishing_get_search_filter() !== 'all' ) {
		return array();
	}

	foreach ( (array) $main_query->posts as $post ) {
		if ( $post instanceof WP_Post && 'post' === $post->post_type ) {
			return array();
		}
	}

	$search = get_search_query();
	if ( $search === '' ) {
		return array();
	}

	$exclude = wp_list_pluck( (array) $main_query->posts, 'ID' );
	if ( function_exists( 'msr_publishing_get_density_filler_post_ids' ) ) {
		$exclude = array_merge( $exclude, msr_publishing_get_density_filler_post_ids() );
	}

	$boost = new WP_Query(
		array(
			'post_type'              => 'post',
			'post_status'            => 'publish',
			's'                      => $search,
			'posts_per_page'         => 2,
			'post__not_in'           => array_map( 'intval', $exclude ),
			'ignore_sticky_posts'    => true,
			'no_found_rows'          => true,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => true,
		)
	);

	return $boost->posts ? $boost->posts : array();
}
