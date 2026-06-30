<?php
/**
 * Resource series hubs — taxonomy queries and reading-order UI (P43).
 *
 * @package msrsandbox
 */

/**
 * Rewrite slug for resource series archives.
 *
 * @return string
 */
function msr_publishing_series_rewrite_slug() {
	return 'resource-series';
}

/**
 * @param int|WP_Term $term Series term or term ID.
 * @return WP_Query
 */
function msr_publishing_get_series_resources_query( $term ) {
	if ( is_numeric( $term ) ) {
		$term = get_term( (int) $term, 'resource_series' );
	}

	if ( ! $term instanceof WP_Term || is_wp_error( $term ) ) {
		return new WP_Query( array( 'post__in' => array( 0 ) ) );
	}

	return new WP_Query(
		array(
			'post_type'           => 'resource',
			'posts_per_page'      => -1,
			'post_status'         => 'publish',
			'ignore_sticky_posts' => true,
			'no_found_rows'       => true,
			'tax_query'           => array(
				array(
					'taxonomy' => 'resource_series',
					'field'    => 'term_id',
					'terms'    => array( (int) $term->term_id ),
				),
			),
			'meta_key'            => 'series_reading_order',
			'orderby'             => array(
				'meta_value_num' => 'ASC',
				'date'           => 'ASC',
			),
			'order'               => 'ASC',
		)
	);
}

/**
 * Reading order position for a resource within its primary series.
 *
 * @param int $post_id Resource ID.
 * @return int 0 when not in a series.
 */
function msr_publishing_get_resource_series_position( $post_id ) {
	$post_id = (int) $post_id;
	if ( $post_id <= 0 ) {
		return 0;
	}

	$terms = get_the_terms( $post_id, 'resource_series' );
	if ( ! $terms || is_wp_error( $terms ) ) {
		return 0;
	}

	$term  = $terms[0];
	$query = msr_publishing_get_series_resources_query( $term );
	if ( ! $query->have_posts() ) {
		return 0;
	}

	$position = 0;
	while ( $query->have_posts() ) {
		$query->the_post();
		++$position;
		if ( get_the_ID() === $post_id ) {
			wp_reset_postdata();
			return $position;
		}
	}
	wp_reset_postdata();

	return 0;
}

/**
 * Primary series term for a resource (first assigned term).
 *
 * @param int $post_id Resource ID.
 * @return WP_Term|null
 */
function msr_publishing_get_resource_primary_series( $post_id ) {
	$terms = get_the_terms( (int) $post_id, 'resource_series' );
	if ( ! $terms || is_wp_error( $terms ) ) {
		return null;
	}

	return $terms[0];
}

/**
 * Compact series context on resource singles.
 *
 * @param int $post_id Resource ID.
 * @return void
 */
function msr_publishing_render_resource_series_context( $post_id = 0 ) {
	$post_id = $post_id ? (int) $post_id : get_the_ID();
	$term    = msr_publishing_get_resource_primary_series( $post_id );
	if ( ! $term ) {
		return;
	}

	$position = msr_publishing_get_resource_series_position( $post_id );
	$total    = (int) msr_publishing_get_series_resources_query( $term )->post_count;
	$link     = get_term_link( $term );
	if ( is_wp_error( $link ) ) {
		return;
	}
	?>
	<p class="resource-single__series small mb-3">
		<span class="resource-single__series-label fw-semibold"><?php esc_html_e( 'Series', 'msrsandbox' ); ?></span>
		<a class="resource-single__series-link" href="<?php echo esc_url( $link ); ?>"><?php echo esc_html( $term->name ); ?></a>
		<?php if ( $position > 0 && $total > 0 ) : ?>
			<span class="resource-single__series-meta text-muted" aria-hidden="true"> · </span>
			<span class="resource-single__series-meta text-muted">
				<?php
				printf(
					/* translators: 1: position in series, 2: total parts */
					esc_html__( 'Part %1$d of %2$d', 'msrsandbox' ),
					(int) $position,
					(int) $total
				);
				?>
			</span>
		<?php endif; ?>
	</p>
	<?php
}

/**
 * Ordered reading list for a series hub.
 *
 * @param WP_Term $term        Series term.
 * @param int     $current_id  Highlight this resource (0 on hub).
 * @return void
 */
function msr_publishing_render_series_reading_order( WP_Term $term, $current_id = 0 ) {
	$query = msr_publishing_get_series_resources_query( $term );
	if ( ! $query->have_posts() ) {
		echo '<p class="text-muted mb-0">';
		esc_html_e( 'No resources in this series yet.', 'msrsandbox' );
		echo '</p>';
		return;
	}

	$current_id = (int) $current_id;
	$total      = (int) $query->post_count;
	?>
	<ol class="publishing-series-reading-order list-unstyled mb-0" aria-label="<?php esc_attr_e( 'Reading order', 'msrsandbox' ); ?>">
		<?php
		$index = 0;
		while ( $query->have_posts() ) {
			$query->the_post();
			++$index;
			$post_id   = get_the_ID();
			$is_active = $current_id > 0 && $post_id === $current_id;
			$order     = function_exists( 'get_field' ) ? (int) get_field( 'series_reading_order', $post_id ) : 0;
			if ( $order <= 0 ) {
				$order = $index;
			}
			$primary = msr_publishing_get_primary_resource_type( $post_id );
			$item_class = 'publishing-series-reading-order__item';
			if ( $is_active ) {
				$item_class .= ' is-current';
			}
			?>
			<li class="<?php echo esc_attr( $item_class ); ?>">
				<div class="publishing-series-reading-order__marker" aria-hidden="true">
					<span class="publishing-series-reading-order__step"><?php echo esc_html( (string) $order ); ?></span>
				</div>
				<div class="publishing-series-reading-order__body">
					<p class="publishing-series-reading-order__meta small text-muted mb-1">
						<?php
						printf(
							/* translators: 1: step number, 2: total parts */
							esc_html__( 'Part %1$d of %2$d', 'msrsandbox' ),
							(int) $order,
							(int) $total
						);
						?>
					</p>
					<h2 class="publishing-series-reading-order__title h5 mb-1">
						<?php if ( $is_active ) : ?>
							<span aria-current="page"><?php the_title(); ?></span>
						<?php else : ?>
							<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
						<?php endif; ?>
					</h2>
					<?php if ( $primary ) : ?>
						<div class="publishing-series-reading-order__format mb-2">
							<?php msr_publishing_render_format_badge( $primary ); ?>
						</div>
					<?php endif; ?>
					<?php if ( has_excerpt() ) : ?>
						<p class="publishing-series-reading-order__excerpt small text-muted mb-0"><?php echo esc_html( wp_strip_all_tags( get_the_excerpt() ) ); ?></p>
					<?php endif; ?>
				</div>
			</li>
			<?php
		}
		wp_reset_postdata();
		?>
	</ol>
	<?php
}
