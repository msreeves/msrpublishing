<?php
/**
 * Commentary archive helpers — topic pills, grids, related posts.
 *
 * @package msrsandbox
 */

/**
 * Topic pill nav for commentary surfaces (archive — links to topic hubs).
 *
 * @param bool $show_all_active Highlight "All topics" as current page.
 * @return void
 */
function msr_publishing_render_commentary_topic_nav( $show_all_active = false ) {
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
	msr_publishing_filter_bar_open( __( 'Commentary topics', 'msrsandbox' ) );
	if ( $show_all_active ) {
		msr_publishing_filter_bar_link( __( 'All topics', 'msrsandbox' ), '', true );
	} else {
		msr_publishing_filter_bar_link( __( 'All insights', 'msrsandbox' ), $insights, false );
	}
	foreach ( $topics as $topic ) {
		msr_publishing_filter_bar_link( $topic->name, get_term_link( $topic ), false );
	}
	msr_publishing_filter_bar_close();
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

	return $args;
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
		esc_html_e( 'No commentary posts for this topic yet.', 'msrsandbox' );
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
		while ( $query->have_posts() ) {
			$query->the_post();
			echo '<div class="col-md-6 col-lg-4">';
			get_template_part(
				'template-parts/cards/post',
				'card',
				array(
					'no_reveal' => (bool) $stagger,
				)
			);
			echo '</div>';
		}
		wp_reset_postdata();
		?>
	</div>
	<?php
}

/**
 * In-page topic filter (Bootstrap tabs) — filters grid below without navigation.
 *
 * @param array $args {
 *   @type string $id_prefix      Unique prefix for tab pane IDs.
 *   @type int    $posts_per_page Posts per tab.
 *   @type string $all_label      Label for the “all topics” tab.
 * }
 * @return void
 */
function msr_publishing_render_commentary_topic_filter( array $args = array() ) {
	$args = wp_parse_args(
		$args,
		array(
			'id_prefix'      => 'commentary',
			'posts_per_page' => 6,
			'all_label'      => __( 'All insights', 'msrsandbox' ),
		)
	);

	$topics = get_terms(
		array(
			'taxonomy'   => 'topic',
			'hide_empty' => false,
		)
	);

	if ( ! $topics || is_wp_error( $topics ) ) {
		$query = new WP_Query( msr_publishing_get_commentary_query_args( 0, (int) $args['posts_per_page'] ) );
		msr_publishing_render_commentary_post_grid( $query );
		return;
	}

	$prefix   = sanitize_html_class( (string) $args['id_prefix'] );
	$per_page = (int) $args['posts_per_page'];
	$all_id   = $prefix . '-commentary-all';
	?>
	<div class="publishing-commentary__filter" data-commentary-filter data-msr-filter-tabs>
		<?php
		msr_publishing_filter_bar_open( __( 'Filter commentary by topic', 'msrsandbox' ), true );
		msr_publishing_filter_bar_tab( $args['all_label'], $all_id . '-tab', $all_id, true );
		foreach ( $topics as $topic ) {
			$pane_id = $prefix . '-commentary-' . $topic->slug;
			msr_publishing_filter_bar_tab( $topic->name, $pane_id . '-tab', $pane_id, false );
		}
		msr_publishing_filter_bar_close();
		?>

		<div class="tab-content publishing-commentary__panes">
			<div
				class="tab-pane fade show active"
				id="<?php echo esc_attr( $all_id ); ?>"
				role="tabpanel"
				aria-labelledby="<?php echo esc_attr( $all_id ); ?>-tab"
				tabindex="0"
			>
				<?php
				msr_publishing_render_commentary_post_grid(
					new WP_Query( msr_publishing_get_commentary_query_args( 0, $per_page ) )
				);
				?>
			</div>
			<?php foreach ( $topics as $topic ) : ?>
				<?php $pane_id = $prefix . '-commentary-' . $topic->slug; ?>
				<div
					class="tab-pane fade"
					id="<?php echo esc_attr( $pane_id ); ?>"
					role="tabpanel"
					aria-labelledby="<?php echo esc_attr( $pane_id ); ?>-tab"
					tabindex="0"
				>
					<?php
					msr_publishing_render_commentary_post_grid(
						new WP_Query( msr_publishing_get_commentary_query_args( (int) $topic->term_id, $per_page ) )
					);
					?>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
	<?php
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
function msr_publishing_render_insights_topic_nav() {
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

	msr_publishing_filter_bar_open( __( 'Filter commentary by topic', 'msrsandbox' ) );
	msr_publishing_filter_bar_link(
		__( 'All insights', 'msrsandbox' ),
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
			'posts_per_page' => 9,
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
			<nav class="pagination mt-4" aria-label="<?php esc_attr_e( 'Insights pages', 'msrsandbox' ); ?>">
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
						'prev_text' => __( 'Previous', 'msrsandbox' ),
						'next_text' => __( 'Next', 'msrsandbox' ),
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
function msr_publishing_get_related_commentary_by_topic( $topic_id, $exclude = 0, $limit = 3 ) {
	$topic_id = (int) $topic_id;
	$exclude  = (int) $exclude;

	if ( ! $topic_id ) {
		return new WP_Query( array( 'post__in' => array( 0 ) ) );
	}

	return new WP_Query(
		array(
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
		)
	);
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
					__( 'Related commentary on %s', 'msrsandbox' ),
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
					__( 'Resources on %s', 'msrsandbox' ),
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
