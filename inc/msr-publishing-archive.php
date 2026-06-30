<?php
/**
 * Resource archive helpers — format/topic filters, ecosystem band, grid.
 *
 * @package msrsandbox
 */

/**
 * @param string       $mode all|term
 * @param WP_Term|null $active_term Active resource_type when mode is term.
 * @return void
 */
function msr_publishing_render_resource_type_nav( $mode = 'all', $active_term = null ) {
	$types = msr_publishing_get_resource_type_nav_terms( array( 'hide_empty' => true ) );

	if ( ! $types ) {
		return;
	}

	$archive = get_post_type_archive_link( 'resource' );
	?>
	<div class="resource-type-nav mt-3">
		<p class="resource-archive-nav__label small text-uppercase fw-semibold text-muted mb-2"><?php esc_html_e( 'Format', 'msrsandbox' ); ?></p>
		<?php
		msr_publishing_filter_bar_open( __( 'Resource formats', 'msrsandbox' ) );
		if ( 'all' === $mode ) {
			if ( is_front_page() && $archive ) {
				msr_publishing_filter_bar_link( __( 'All formats', 'msrsandbox' ), $archive, false );
			} else {
				msr_publishing_filter_bar_link( __( 'All formats', 'msrsandbox' ), '', true );
			}
		} elseif ( $archive ) {
			msr_publishing_filter_bar_link( __( 'All formats', 'msrsandbox' ), $archive, false );
		}
		foreach ( $types as $type ) {
			$is_active = ( 'term' === $mode && $active_term instanceof WP_Term && (int) $active_term->term_id === (int) $type->term_id );
			if ( ! $is_active && $active_term instanceof WP_Term ) {
				$is_active = msr_publishing_resolve_format_slug( $active_term->slug ) === $type->slug;
			}
			msr_publishing_filter_bar_link( $type->name, get_term_link( $type ), $is_active );
		}
		msr_publishing_filter_bar_close();
		?>
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
 * @param string       $mode all|term
 * @param WP_Term|null $active_term Active topic when mode is term.
 * @param WP_Term|null $format_scope Limit topics to those used by this format (format archives).
 * @return void
 */
function msr_publishing_render_topic_nav( $mode = 'all', $active_term = null, $format_scope = null ) {
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
	?>
	<div class="resource-topic-nav mt-3">
		<p class="resource-archive-nav__label small text-uppercase fw-semibold text-muted mb-2"><?php esc_html_e( 'Topic', 'msrsandbox' ); ?></p>
		<?php
		msr_publishing_filter_bar_open( __( 'Resource topics', 'msrsandbox' ) );
		if ( 'all' === $mode ) {
			if ( is_front_page() && $topics_url ) {
				msr_publishing_filter_bar_link( __( 'All topics', 'msrsandbox' ), $topics_url, false );
			} else {
				msr_publishing_filter_bar_link( __( 'All topics', 'msrsandbox' ), '', true );
			}
		} elseif ( $archive ) {
			msr_publishing_filter_bar_link( __( 'All topics', 'msrsandbox' ), $archive, false );
		}
		foreach ( $topics as $topic ) {
			$is_active = ( 'term' === $mode && $active_term instanceof WP_Term && (int) $active_term->term_id === (int) $topic->term_id );
			msr_publishing_filter_bar_link( $topic->name, get_term_link( $topic ), $is_active );
		}
		msr_publishing_filter_bar_close();
		?>
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
					__( 'MSR programme', 'msrsandbox' ),
					__( 'network.', 'msrsandbox' ),
					__( 'Atlas Briefing connects to the MSR Events, Awards, and Seminars programmes — explore the connected portfolio.', 'msrsandbox' ),
					'publishing-ecosystem-heading'
				);
				?>
			<?php else : ?>
				<header class="publishing-ecosystem__header text-center mb-4">
					<h2 id="publishing-ecosystem-heading" class="h4 publishing-ecosystem__title mb-2">
						<?php esc_html_e( 'MSR programme network', 'msrsandbox' ); ?>
					</h2>
					<p class="text-muted mb-0 publishing-ecosystem__lead">
						<?php esc_html_e( 'Atlas Briefing connects to the MSR Events, Awards, and Seminars programmes — explore the connected portfolio.', 'msrsandbox' ); ?>
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
										__( 'Visit %s', 'msrsandbox' ),
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
		$empty_message = __( 'No resources found.', 'msrsandbox' );
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
