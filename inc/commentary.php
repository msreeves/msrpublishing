<?php
/**
 * Commentary archive helpers — topic pills, grids, related posts.
 *
 * @package msrpublishing
 */

/**
 * Topic pill nav for commentary surfaces (archive — links to topic hubs).
 *
 * @param bool   $show_all_active Highlight "All topics" as current page.
 * @param string $layout          standalone|panel
 * @return void
 */
function msr_publishing_render_commentary_topic_nav( $show_all_active = false, $layout = 'standalone' ) {
	$topics = get_terms(
		array(
			'taxonomy'   => 'topic',
			'hide_empty' => false,
		)
	);

	if ( ! $topics || is_wp_error( $topics ) ) {
		return;
	}

	$insights = msr_publishing_insights_url();
	$variant  = 'panel' === $layout ? 'chips' : 'underline';

	$render_bar = static function () use ( $show_all_active, $insights, $topics, $variant ) {
		msr_publishing_filter_bar_open( __( 'Insight topics', 'msrpublishing' ), false, $variant );
		if ( $show_all_active ) {
			msr_publishing_filter_bar_link( __( 'All topics', 'msrpublishing' ), '', true );
		} else {
			msr_publishing_filter_bar_link( __( 'All insights', 'msrpublishing' ), $insights, false );
		}
		foreach ( $topics as $topic ) {
			$link = get_term_link( $topic );
			if ( ! is_wp_error( $link ) ) {
				msr_publishing_filter_bar_link( $topic->name, $link, false );
			}
		}
		msr_publishing_filter_bar_close();
	};

	if ( 'panel' === $layout ) {
		msr_publishing_discovery_row_open( __( 'Topic', 'msrpublishing' ) );
		$render_bar();
		msr_publishing_discovery_row_close();
		return;
	}

	$render_bar();
}

/**
 * Base WP_Query args for commentary posts.
 *
 * @param int  $topic_id        Topic term ID (0 = all topics).
 * @param int  $posts_per_page  Max posts.
 * @param int  $paged           Page number.
 * @param bool $count_total     Set true to enable found_posts / max_num_pages.
 * @return array
 */
function msr_publishing_get_commentary_query_args( $topic_id = 0, $posts_per_page = 6, $paged = 1, $count_total = false ) {
	$args = array(
		'post_type'           => 'post',
		'posts_per_page'      => max( 1, (int) $posts_per_page ),
		'paged'               => max( 1, (int) $paged ),
		'orderby'             => 'date',
		'order'               => 'DESC',
		'ignore_sticky_posts' => true,
		'no_found_rows'       => ! $count_total,
		'category__not_in'    => msr_publishing_get_excluded_sponsored_category_ids(),
	);

	$topic_id = (int) $topic_id;
	if ( $topic_id > 0 ) {
		$args['tax_query'] = array(
			array(
				'taxonomy' => 'topic',
				'field'    => 'term_id',
				'terms'    => array( $topic_id ),
			),
		);
	}

	return function_exists( 'msr_publishing_append_portfolio_surface_excludes' )
		? msr_publishing_append_portfolio_surface_excludes( $args )
		: $args;
}

/**
 * Default grid size for insights landing, hub strips, and related commentary blocks.
 *
 * @return int
 */
function msr_publishing_get_insights_feed_per_page() {
	return 24;
}

/**
 * Related commentary cards on hubs and resource singles.
 *
 * @return int
 */
function msr_publishing_get_commentary_related_count() {
	return 9;
}

/**
 * Post-card grid for commentary queries.
 *
 * @param WP_Query $query Commentary query.
 * @return void
 */
function msr_publishing_render_commentary_post_grid( WP_Query $query, $stagger = false ) {
	if ( ! $query->have_posts() ) {
		echo '<p class="text-center text-muted mb-0">';
		esc_html_e( 'No commentary posts for this topic yet.', 'msrpublishing' );
		echo '</p>';
		return;
	}

	msr_publishing_render_commentary_post_grid_from_ids( wp_list_pluck( $query->posts, 'ID' ), $stagger );
	wp_reset_postdata();
}

/**
 * Render commentary cards from explicit post IDs (title/media order preserved).
 *
 * @param int[] $post_ids Post IDs.
 * @param bool  $stagger  Reveal stagger wrapper.
 * @return void
 */
function msr_publishing_render_commentary_post_grid_from_ids( array $post_ids, $stagger = false ) {
	$post_ids = array_values( array_filter( array_map( 'intval', $post_ids ) ) );
	if ( ! $post_ids ) {
		echo '<p class="text-center text-muted mb-0">';
		esc_html_e( 'No commentary posts for this topic yet.', 'msrpublishing' );
		echo '</p>';
		return;
	}

	$row_class = 'row g-4';
	if ( $stagger ) {
		$row_class .= ' msr-reveal-stagger';
	}
	?>
	<div class="<?php echo esc_attr( $row_class ); ?>"<?php echo $stagger ? ' data-msr-reveal-stagger="grid"' : ''; ?>>
		<?php
		foreach ( $post_ids as $post_id ) {
			echo '<div class="col-sm-6 col-lg-4 publishing-commentary-grid__cell">';
			get_template_part(
				'template-parts/cards/post',
				'card',
				array(
					'post_id'   => (int) $post_id,
					'no_reveal' => (bool) $stagger,
				)
			);
			echo '</div>';
		}
		?>
	</div>
	<?php
}

/**
 * Home / surface commentary IDs — unique card media, optional post excludes.
 *
 * @param int   $limit         Target count.
 * @param int[] $exclude_posts Post IDs to skip.
 * @return int[]
 */
function msr_publishing_get_commentary_ids_for_surface( $limit, $exclude_posts = array() ) {
	$limit         = max( 1, (int) $limit );
	$exclude_posts = array_values( array_unique( array_filter( array_map( 'intval', (array) $exclude_posts ) ) ) );

	$blocked_media = array();
	$featured_id   = msr_publishing_get_site_featured_resource_id();
	if ( $featured_id ) {
		$blocked_media[] = msr_publishing_get_card_hero_attachment_id( $featured_id );
	}

	$pool_args = msr_publishing_get_commentary_query_args( 0, max( $limit * 4, 48 ) );
	if ( $exclude_posts ) {
		$pool_args['post__not_in'] = $exclude_posts;
	}

	$query = new WP_Query( $pool_args );
	if ( empty( $query->posts ) ) {
		return array();
	}

	$candidate_ids = wp_list_pluck( $query->posts, 'ID' );
	if ( function_exists( 'msr_publishing_is_curated_surface_noise_post' ) ) {
		$candidate_ids = array_values(
			array_filter(
				$candidate_ids,
				static function ( $id ) {
					return ! msr_publishing_is_curated_surface_noise_post( (int) $id );
				}
			)
		);
	}

	$picked = msr_publishing_pick_post_ids_unique_card_media_local( $candidate_ids, $limit, $blocked_media );
	if ( count( $picked ) >= $limit ) {
		return $picked;
	}

	foreach ( $candidate_ids as $candidate_id ) {
		$candidate_id = (int) $candidate_id;
		if ( $candidate_id <= 0 || in_array( $candidate_id, $picked, true ) ) {
			continue;
		}
		if ( function_exists( 'msr_publishing_is_curated_surface_noise_post' ) && msr_publishing_is_curated_surface_noise_post( $candidate_id ) ) {
			continue;
		}
		$picked[] = $candidate_id;
		if ( count( $picked ) >= $limit ) {
			break;
		}
	}

	return array_slice( $picked, 0, $limit );
}

/**
 * Commentary topic strip — URL pills to `/insights/` (no in-page tab panes).
 *
 * @param array $args {
 *   @type int $posts_per_page Posts in the grid below the pills.
 * }
 * @return void
 */
function msr_publishing_render_commentary_topic_filter( array $args = array() ) {
	$args = wp_parse_args(
		$args,
		array(
			'posts_per_page' => 6,
		)
	);

	if ( function_exists( 'msr_publishing_render_insights_topic_nav' ) ) {
		msr_publishing_render_insights_topic_nav();
	}

	msr_publishing_render_commentary_post_grid(
		new WP_Query( msr_publishing_get_commentary_query_args( 0, (int) $args['posts_per_page'] ) ),
		true
	);
}

/**
 * Topic slug from insights page query (`?topic=`).
 *
 * @return string
 */
function msr_publishing_get_insights_topic_slug() {
	if ( ! is_page( 'insights' ) || ! isset( $_GET['topic'] ) ) {
		return '';
	}

	return sanitize_title( wp_unslash( (string) $_GET['topic'] ) );
}

/**
 * Build insights hub URL with optional topic + page.
 *
 * @param string|null $topic_slug Topic slug; empty string clears; null = current.
 * @param int|null    $paged      Page number; null = omit.
 * @return string
 */
function msr_publishing_build_insights_url( $topic_slug = null, $paged = null ) {
	$args = array();

	if ( null === $topic_slug ) {
		$topic_slug = msr_publishing_get_insights_topic_slug();
	}
	if ( $topic_slug !== '' ) {
		$args['topic'] = $topic_slug;
	}

	if ( null !== $paged && (int) $paged > 1 ) {
		$args['paged'] = (int) $paged;
	}

	if ( ! $args ) {
		return msr_publishing_insights_url();
	}

	return add_query_arg( $args, msr_publishing_insights_url() );
}

/**
 * Current insights list page number (`paged` query arg).
 *
 * @return int
 */
function msr_publishing_get_insights_paged() {
	if ( ! is_page( 'insights' ) ) {
		return 1;
	}

	$paged = isset( $_GET['paged'] ) ? (int) $_GET['paged'] : 0;
	if ( $paged < 1 ) {
		$paged = (int) get_query_var( 'page' );
	}

	return max( 1, $paged );
}

/**
 * Topic pill nav for the insights landing (URL filters, not in-page tabs).
 *
 * @return void
 */
function msr_publishing_render_insights_topic_nav( $layout = 'standalone' ) {
	$topics = get_terms(
		array(
			'taxonomy'   => 'topic',
			'hide_empty' => false,
		)
	);

	if ( ! $topics || is_wp_error( $topics ) ) {
		return;
	}

	$active_topic = msr_publishing_get_insights_topic_slug();
	$variant      = 'panel' === $layout ? 'chips' : 'underline';

	$render_bar = static function () use ( $active_topic, $topics, $variant ) {
		msr_publishing_filter_bar_open( __( 'Filter commentary by topic', 'msrpublishing' ), false, $variant );
		msr_publishing_filter_bar_link(
			__( 'All insights', 'msrpublishing' ),
			msr_publishing_build_insights_url( '', null ),
			$active_topic === ''
		);
		foreach ( $topics as $topic ) {
			msr_publishing_filter_bar_link(
				$topic->name,
				msr_publishing_build_insights_url( $topic->slug, null ),
				$active_topic === $topic->slug
			);
		}
		msr_publishing_filter_bar_close();
	};

	if ( 'panel' === $layout ) {
		msr_publishing_discovery_row_open( __( 'Topic', 'msrpublishing' ) );
		$render_bar();
		msr_publishing_discovery_row_close();
		return;
	}

	$render_bar();
}

/**
 * Paginated commentary feed for `/insights/` (audit P3).
 *
 * @param array $args {
 *   @type int $posts_per_page Posts per page.
 * }
 * @return void
 */
function msr_publishing_render_insights_feed( array $args = array() ) {
	$args = wp_parse_args(
		$args,
		array(
			'posts_per_page' => msr_publishing_get_insights_feed_per_page(),
		)
	);

	$topic_slug = msr_publishing_get_insights_topic_slug();
	$topic_id   = 0;
	if ( $topic_slug !== '' ) {
		$term = get_term_by( 'slug', $topic_slug, 'topic' );
		if ( $term && ! is_wp_error( $term ) ) {
			$topic_id = (int) $term->term_id;
		}
	}

	$paged = msr_publishing_get_insights_paged();
	$query = new WP_Query(
		msr_publishing_get_commentary_query_args(
			$topic_id,
			(int) $args['posts_per_page'],
			$paged,
			true
		)
	);
	?>
	<div class="publishing-insights__feed" data-insights-feed>
		<?php msr_publishing_render_commentary_post_grid( $query ); ?>

		<?php if ( $query->max_num_pages > 1 ) : ?>
			<nav class="pagination mt-4" aria-label="<?php esc_attr_e( 'Insights pages', 'msrpublishing' ); ?>">
				<?php
				$base_args = array( 'paged' => '%#%' );
				if ( $topic_slug !== '' ) {
					$base_args['topic'] = $topic_slug;
				}
				echo paginate_links(
					array(
						'base'      => add_query_arg( $base_args, msr_publishing_insights_url() ),
						'format'    => '',
						'current'   => $paged,
						'total'     => $query->max_num_pages,
						'mid_size'  => 2,
						'prev_text' => __( 'Previous', 'msrpublishing' ),
						'next_text' => __( 'Next', 'msrpublishing' ),
						'type'      => 'list',
					)
				);
				?>
			</nav>
		<?php endif; ?>
	</div>
	<?php
}

/**
 * @param int $topic_id Topic term ID.
 * @param int $exclude  Post ID to exclude.
 * @param int $limit    Max posts.
 * @return WP_Query
 */
function msr_publishing_get_related_commentary_by_topic( $topic_id, $exclude = 0, $limit = 0 ) {
	$topic_id = (int) $topic_id;
	$exclude  = (int) $exclude;
	$limit    = $limit > 0 ? (int) $limit : msr_publishing_get_commentary_related_count();

	if ( ! $topic_id ) {
		return new WP_Query( array( 'post__in' => array( 0 ) ) );
	}

	$args = array(
		'post_type'           => 'post',
		'posts_per_page'      => $limit,
		'post__not_in'        => $exclude ? array( $exclude ) : array(),
		'orderby'             => 'date',
		'order'               => 'DESC',
		'no_found_rows'       => true,
		'ignore_sticky_posts' => true,
		'category__not_in'    => msr_publishing_get_excluded_sponsored_category_ids(),
		'tax_query'           => array(
			array(
				'taxonomy' => 'topic',
				'field'    => 'term_id',
				'terms'    => array( $topic_id ),
			),
		),
	);

	if ( function_exists( 'msr_publishing_append_portfolio_surface_excludes' ) ) {
		$args = msr_publishing_append_portfolio_surface_excludes( $args );
	}

	return new WP_Query( $args );
}

/**
 * Subscribe band on commentary archives (footer suppressed).
 *
 * @return void
 */
function msr_publishing_render_commentary_subscribe_band() {
	if ( ! is_category() ) {
		return;
	}
	msr_publishing_render_subscribe_cta( 'commentary' );
}

/**
 * Render topic badges for a post (publishing `topic` taxonomy).
 *
 * @param int $post_id Post ID.
 * @return void
 */
function msr_publishing_render_post_topic_badges( $post_id = 0 ) {
	$post_id = $post_id ? (int) $post_id : get_the_ID();
	$topics  = get_the_terms( $post_id, 'topic' );

	if ( ! $topics || is_wp_error( $topics ) ) {
		return;
	}
	?>
	<div class="post-card__topics mb-2">
		<?php foreach ( $topics as $topic ) : ?>
			<a class="resource-card__type badge text-decoration-none" href="<?php echo esc_url( get_term_link( $topic ) ); ?>">
				<?php echo esc_html( $topic->name ); ?>
			</a>
		<?php endforeach; ?>
	</div>
	<?php
}

/**
 * Topic badges for resource singles (links to topic hubs).
 *
 * @param int $post_id Resource post ID.
 * @return void
 */
function msr_publishing_render_resource_topic_badges( $post_id = 0 ) {
	msr_publishing_render_post_topic_badges( $post_id );
}

/**
 * Commentary cross-link block on resource singles (shared topic).
 *
 * @param int $post_id Resource post ID.
 * @return void
 */
function msr_publishing_render_resource_commentary_crosslink( $post_id = 0 ) {
	$post_id = $post_id ? (int) $post_id : get_the_ID();
	$topics  = get_the_terms( $post_id, 'topic' );

	if ( ! $topics || is_wp_error( $topics ) ) {
		return;
	}

	$primary_topic = $topics[0];
	$related       = msr_publishing_get_related_commentary_by_topic( (int) $primary_topic->term_id, 0, 3 );
	if ( ! $related->have_posts() ) {
		return;
	}
	?>
	<section class="resource-single__commentary resource-single__crosslink mt-5 pt-4 border-top">
		<h2 class="h4 mb-4">
			<?php
			echo esc_html(
				sprintf(
					/* translators: %s: topic name */
					__( 'Related commentary on %s', 'msrpublishing' ),
					$primary_topic->name
				)
			);
			?>
		</h2>
		<div class="row g-4">
			<?php
			while ( $related->have_posts() ) {
				$related->the_post();
				echo '<div class="col-md-6 col-lg-4">';
				get_template_part( 'template-parts/cards/post', 'card' );
				echo '</div>';
			}
			wp_reset_postdata();
			?>
		</div>
	</section>
	<?php
}

/**
 * Resource cross-link block on post singles (shared topic).
 *
 * @param int $post_id Post ID.
 * @return void
 */
function msr_publishing_render_post_resource_crosslink( $post_id = 0 ) {
	$post_id = $post_id ? (int) $post_id : get_the_ID();
	$topics  = get_the_terms( $post_id, 'topic' );

	if ( ! $topics || is_wp_error( $topics ) ) {
		return;
	}

	$primary_topic = $topics[0];
	$related       = msr_publishing_get_related_resources_by_topic( (int) $primary_topic->term_id, 0, 3 );
	if ( ! $related->have_posts() ) {
		return;
	}
	?>
	<section class="post-single__resources post-single__crosslink mt-5 pt-4 border-top">
		<h2 class="h4 mb-4">
			<?php
			echo esc_html(
				sprintf(
					/* translators: %s: topic name */
					__( 'Resources on %s', 'msrpublishing' ),
					$primary_topic->name
				)
			);
			?>
		</h2>
		<div class="row g-4">
			<?php
			while ( $related->have_posts() ) {
				$related->the_post();
				echo '<div class="col-md-6 col-lg-4">';
				get_template_part( 'template-parts/cards/resource', 'card' );
				echo '</div>';
			}
			wp_reset_postdata();
			?>
		</div>
	</section>
	<?php
}
