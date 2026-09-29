<?php
/**
 * Resource archive helpers — format/topic filters, ecosystem band, grid.
 *
 * @package msrpublishing
 */

/**
 * @param string       $mode   all|term
 * @param WP_Term|null $active_term Active resource_type when mode is term.
 * @param string       $layout standalone|panel
 * @return void
 */
function msr_publishing_render_resource_type_nav( $mode = 'all', $active_term = null, $layout = 'standalone' ) {
	$types = msr_publishing_get_resource_type_nav_terms( array( 'hide_empty' => true ) );

	if ( ! $types ) {
		return;
	}

	$archive = get_post_type_archive_link( 'resource' );
	$variant = 'panel' === $layout ? 'chips' : 'underline';

	$render_bar = static function () use ( $mode, $active_term, $types, $archive, $variant ) {
		msr_publishing_filter_bar_open( __( 'Resource formats', 'msrpublishing' ), false, $variant );
		if ( 'all' === $mode ) {
			if ( is_front_page() && $archive ) {
				msr_publishing_filter_bar_link( __( 'All formats', 'msrpublishing' ), $archive, false );
			} else {
				msr_publishing_filter_bar_link( __( 'All formats', 'msrpublishing' ), '', true );
			}
		} elseif ( $archive ) {
			msr_publishing_filter_bar_link(
				__( 'All formats', 'msrpublishing' ),
				msr_publishing_browse_url_preserve_sort( $archive ),
				false
			);
		}
		foreach ( $types as $type ) {
			$is_active = ( 'term' === $mode && $active_term instanceof WP_Term && (int) $active_term->term_id === (int) $type->term_id );
			if ( ! $is_active && $active_term instanceof WP_Term ) {
				$is_active = msr_publishing_resolve_format_slug( $active_term->slug ) === $type->slug;
			}
			$link = get_term_link( $type );
			if ( ! is_wp_error( $link ) ) {
				msr_publishing_filter_bar_link( $type->name, msr_publishing_browse_url_preserve_sort( $link ), $is_active );
			}
		}
		msr_publishing_filter_bar_close();
	};

	if ( 'panel' === $layout ) {
		msr_publishing_discovery_row_open( __( 'Format', 'msrpublishing' ) );
		$render_bar();
		msr_publishing_discovery_row_close();
		return;
	}

	?>
	<div class="resource-type-nav mt-3">
		<p class="resource-archive-nav__label small text-uppercase fw-semibold text-muted mb-2"><?php esc_html_e( 'Format', 'msrpublishing' ); ?></p>
		<?php $render_bar(); ?>
	</div>
	<?php
}

/**
 * Topic terms linked to resources in a format hub (for scoped filter bars).
 *
 * @param WP_Term $format_term resource_type term.
 * @return WP_Term[]
 */
function msr_publishing_get_topics_for_format_term( $format_term ) {
	if ( ! $format_term instanceof WP_Term ) {
		return array();
	}

	$resource_ids = get_posts(
		array(
			'post_type'              => 'resource',
			'post_status'            => 'publish',
			'posts_per_page'         => 100,
			'fields'                 => 'ids',
			'no_found_rows'          => true,
			'update_post_meta_cache' => false,
			'tax_query'              => array(
				array(
					'taxonomy' => 'resource_type',
					'field'    => 'term_id',
					'terms'    => array( (int) $format_term->term_id ),
				),
			),
		)
	);

	if ( ! $resource_ids ) {
		return array();
	}

	$topics = wp_get_object_terms(
		$resource_ids,
		'topic',
		array(
			'orderby'    => 'name',
			'hide_empty' => true,
		)
	);

	return ( $topics && ! is_wp_error( $topics ) ) ? $topics : array();
}

/**
 * @param string       $mode         all|term
 * @param WP_Term|null $active_term  Active topic when mode is term.
 * @param WP_Term|null $format_scope Limit topics to those used by this format (format archives).
 * @param string       $layout       standalone|panel
 * @return void
 */
function msr_publishing_render_topic_nav( $mode = 'all', $active_term = null, $format_scope = null, $layout = 'standalone' ) {
	if ( $format_scope instanceof WP_Term ) {
		$topics = msr_publishing_get_topics_for_format_term( $format_scope );
	} else {
		$topics = get_terms(
			array(
				'taxonomy'   => 'topic',
				'hide_empty' => true,
			)
		);
	}

	if ( ! $topics || is_wp_error( $topics ) ) {
		return;
	}

	$archive    = get_post_type_archive_link( 'resource' );
	$topics_url = function_exists( 'msr_publishing_get_page_url' )
		? msr_publishing_get_page_url( 'topics', '/topics/' )
		: '';
	$variant    = 'panel' === $layout ? 'chips' : 'underline';

	$render_bar = static function () use ( $mode, $active_term, $topics, $archive, $topics_url, $variant ) {
		msr_publishing_filter_bar_open( __( 'Resource topics', 'msrpublishing' ), false, $variant );
		if ( 'all' === $mode ) {
			if ( is_front_page() && $topics_url ) {
				msr_publishing_filter_bar_link( __( 'All topics', 'msrpublishing' ), $topics_url, false );
			} else {
				msr_publishing_filter_bar_link( __( 'All topics', 'msrpublishing' ), '', true );
			}
		} elseif ( $archive ) {
			msr_publishing_filter_bar_link(
				__( 'All topics', 'msrpublishing' ),
				msr_publishing_browse_url_preserve_sort( $archive ),
				false
			);
		}
		foreach ( $topics as $topic ) {
			$is_active = ( 'term' === $mode && $active_term instanceof WP_Term && (int) $active_term->term_id === (int) $topic->term_id );
			$link      = get_term_link( $topic );
			if ( ! is_wp_error( $link ) ) {
				msr_publishing_filter_bar_link( $topic->name, msr_publishing_browse_url_preserve_sort( $link ), $is_active );
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

	?>
	<div class="resource-topic-nav mt-3">
		<p class="resource-archive-nav__label small text-uppercase fw-semibold text-muted mb-2"><?php esc_html_e( 'Topic', 'msrpublishing' ); ?></p>
		<?php $render_bar(); ?>
	</div>
	<?php
}

/**
 * Whether the main query is a resource library browse surface (archive or topic/format hub).
 *
 * @return bool
 */
function msr_publishing_is_resource_browse() {
	return is_post_type_archive( 'resource' ) || is_tax( array( 'resource_type', 'topic' ) );
}

/**
 * @return string newest|oldest|title
 */
function msr_publishing_get_browse_sort() {
	if ( ! msr_publishing_is_resource_browse() ) {
		return 'newest';
	}

	if ( ! isset( $_GET['msr_sort'] ) ) {
		return 'newest';
	}

	$sort    = sanitize_key( wp_unslash( (string) $_GET['msr_sort'] ) );
	$allowed = array_keys( msr_publishing_get_browse_sort_options() );

	return in_array( $sort, $allowed, true ) ? $sort : 'newest';
}

/**
 * @return array<string, string> slug => label
 */
function msr_publishing_get_browse_sort_options() {
	return array(
		'newest' => __( 'Newest', 'msrpublishing' ),
		'oldest' => __( 'Oldest', 'msrpublishing' ),
		'title'  => __( 'Title A–Z', 'msrpublishing' ),
	);
}

/**
 * Canonical URL for the current resource browse view (no sort query arg).
 *
 * @return string
 */
function msr_publishing_get_browse_base_url() {
	if ( is_post_type_archive( 'resource' ) ) {
		$archive = get_post_type_archive_link( 'resource' );
		return $archive ? $archive : '';
	}

	if ( is_tax( array( 'resource_type', 'topic' ) ) ) {
		$term = get_queried_object();
		if ( $term instanceof WP_Term ) {
			$link = get_term_link( $term );
			return is_wp_error( $link ) ? '' : $link;
		}
	}

	return '';
}

/**
 * Build browse URL with optional sort override (shareable filter state).
 *
 * @param string|null $sort newest|oldest|title|null = current.
 * @return string
 */
function msr_publishing_build_browse_url( $sort = null ) {
	$base = msr_publishing_get_browse_base_url();
	if ( $base === '' ) {
		return '';
	}

	if ( null === $sort ) {
		$sort = msr_publishing_get_browse_sort();
	}

	if ( 'newest' === $sort ) {
		return $base;
	}

	if ( ! in_array( $sort, array_keys( msr_publishing_get_browse_sort_options() ), true ) ) {
		return $base;
	}

	return add_query_arg( 'msr_sort', $sort, $base );
}

/**
 * Sort pills for resource archive and topic/format hubs (B2B library browse).
 *
 * @param string $layout standalone|panel
 * @return void
 */
function msr_publishing_render_resource_browse_sort_nav( $layout = 'standalone' ) {
	if ( ! msr_publishing_is_resource_browse() ) {
		return;
	}

	$active  = msr_publishing_get_browse_sort();
	$options = msr_publishing_get_browse_sort_options();

	$render_bar = static function () use ( $active, $options, $layout ) {
		$variant = 'panel' === $layout ? 'chips' : 'underline';
		msr_publishing_filter_bar_open( __( 'Sort resources', 'msrpublishing' ), false, $variant );
		foreach ( $options as $slug => $label ) {
			msr_publishing_filter_bar_link( $label, msr_publishing_build_browse_url( $slug ), $active === $slug );
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
	<div class="publishing-browse__sort resource-archive-nav mt-3">
		<p class="resource-archive-nav__label small text-uppercase fw-semibold text-muted mb-2"><?php esc_html_e( 'Sort', 'msrpublishing' ); ?></p>
		<?php $render_bar(); ?>
	</div>
	<?php
}

/**
 * @return void
 */
function msr_publishing_render_ecosystem_band() {
	$programmes = msr_publishing_get_ecosystem_programmes();
	if ( ! $programmes ) {
		return;
	}
	?>
	<section class="publishing-ecosystem msr-reveal msr-reveal--up" aria-labelledby="publishing-ecosystem-heading">
		<div class="container">
			<?php if ( is_front_page() ) : ?>
				<?php
				msr_publishing_render_section_header(
					__( 'MSR programme', 'msrpublishing' ),
					__( 'network.', 'msrpublishing' ),
					__( 'Atlas Briefing connects to the MSR Events, Awards, and Seminars programmes — explore the connected portfolio.', 'msrpublishing' ),
					'publishing-ecosystem-heading'
				);
				?>
			<?php else : ?>
				<header class="publishing-ecosystem__header text-center mb-4">
					<h2 id="publishing-ecosystem-heading" class="h4 publishing-ecosystem__title mb-2">
						<?php esc_html_e( 'MSR programme network', 'msrpublishing' ); ?>
					</h2>
					<p class="text-muted mb-0 publishing-ecosystem__lead">
						<?php esc_html_e( 'Atlas Briefing connects to the MSR Events, Awards, and Seminars programmes — explore the connected portfolio.', 'msrpublishing' ); ?>
					</p>
				</header>
			<?php endif; ?>
			<div class="row g-3 justify-content-center msr-reveal-stagger">
				<?php foreach ( $programmes as $programme ) : ?>
					<div class="col-md-4">
						<div class="publishing-ecosystem__card h-100">
							<?php if ( is_front_page() && ! empty( $programme['icon'] ) ) : ?>
								<div class="publishing-ecosystem__card-head">
									<span class="publishing-ecosystem__icon" aria-hidden="true">
										<i class="<?php echo esc_attr( $programme['icon'] ); ?>"></i>
									</span>
									<?php if ( ! empty( $programme['meta'] ) ) : ?>
										<span class="publishing-ecosystem__meta"><?php echo esc_html( $programme['meta'] ); ?></span>
									<?php endif; ?>
								</div>
							<?php endif; ?>
							<h3 class="h6 publishing-ecosystem__card-title mb-2"><?php echo esc_html( $programme['label'] ); ?></h3>
							<p class="small text-muted mb-3"><?php echo esc_html( $programme['description'] ); ?></p>
							<a class="btn btn-outline-primary btn-sm" href="<?php echo esc_url( $programme['url'] ); ?>">
								<?php
								echo esc_html(
									sprintf(
										/* translators: %s: programme name */
										__( 'Visit %s', 'msrpublishing' ),
										$programme['label']
									)
								);
								?>
							</a>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
	</section>
	<?php
}

/**
 * Ecosystem band placement on resource archives (admin option).
 *
 * @param 'before'|'after' $position When to render relative to listings.
 * @return void
 */
function msr_publishing_render_ecosystem_band_for_archive( $position = 'before' ) {
	$after = msr_publishing_ecosystem_band_after_grid();
	if ( ( 'before' === $position && $after ) || ( 'after' === $position && ! $after ) ) {
		return;
	}
	msr_publishing_render_ecosystem_band();
}

/**
 * @param string $empty_message Message when no posts.
 * @param int[]  $exclude_ids   Post IDs to skip (e.g. hub featured pick).
 * @return void
 */
function msr_publishing_render_resource_grid( $empty_message = '', $exclude_ids = array() ) {
	if ( '' === $empty_message ) {
		$empty_message = __( 'No resources found.', 'msrpublishing' );
	}

	$exclude_ids = array_filter( array_map( 'intval', (array) $exclude_ids ) );
	?>
	<div class="container pb-5">
		<div class="row g-4">
			<?php
			if ( have_posts() ) {
				while ( have_posts() ) {
					the_post();
					if ( 'resource' !== get_post_type() ) {
						continue;
					}
					if ( $exclude_ids && in_array( (int) get_the_ID(), $exclude_ids, true ) ) {
						continue;
					}
					echo '<div class="col-md-6 col-lg-4">';
					get_template_part( 'template-parts/cards/resource', 'card' );
					echo '</div>';
				}
				?>
				<div class="col-12">
					<?php the_posts_pagination(); ?>
				</div>
				<?php
			} else {
				echo '<div class="col-12"><p class="text-muted mb-0">' . esc_html( $empty_message ) . '</p></div>';
			}
			?>
		</div>
	</div>
	<?php
}
