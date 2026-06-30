<?php
/**
 * Resource hub enrichment — featured picks and related commentary strips.
 *
 * @package msrsandbox
 */

/**
 * Curated topic hub intro (ACF hub_intro → term description → fallback).
 *
 * @param WP_Term|null $term Topic term.
 * @return string Plain intro text.
 */
function msr_publishing_get_topic_hub_intro( $term ) {
	if ( ! $term instanceof WP_Term ) {
		return '';
	}

	$intro = '';
	if ( function_exists( 'get_field' ) ) {
		$intro = trim( (string) get_field( 'hub_intro', $term ) );
	}

	if ( $intro === '' ) {
		$intro = trim( (string) term_description( $term ) );
	}

	if ( $intro === '' ) {
		return sprintf(
			/* translators: %s: topic name */
			__( 'Resources and briefings on %s from Atlas Briefing.', 'msrsandbox' ),
			$term->name
		);
	}

	return $intro;
}

/**
 * Curated featured resource ID for a topic term (ACF pick).
 *
 * @param WP_Term $term Topic term.
 * @return int Post ID or 0.
 */
function msr_publishing_get_topic_hub_featured_resource_id( $term ) {
	if ( ! $term instanceof WP_Term || ! function_exists( 'get_field' ) ) {
		return 0;
	}

	$picked = (int) get_field( 'hub_featured_resource', 'topic_' . (int) $term->term_id );
	if ( $picked <= 0 ) {
		return 0;
	}

	$post = get_post( $picked );
	if ( ! $post || 'resource' !== $post->post_type || 'publish' !== $post->post_status ) {
		return 0;
	}

	return $picked;
}

/**
 * Featured resource ID for a hub context (featured meta in term, else latest in scope).
 *
 * @param WP_Term|null $term      Active taxonomy term when on a term archive.
 * @param string       $taxonomy  topic|resource_type|'' for main archive.
 * @return int Post ID or 0.
 */
function msr_publishing_get_hub_featured_resource_id( $term = null, $taxonomy = '' ) {
	$base = array(
		'post_type'           => 'resource',
		'posts_per_page'      => 1,
		'post_status'         => 'publish',
		'ignore_sticky_posts' => true,
		'no_found_rows'       => true,
		'fields'              => 'ids',
	);

	if ( $term instanceof WP_Term && 'topic' === $taxonomy ) {
		$curated = msr_publishing_get_topic_hub_featured_resource_id( $term );
		if ( $curated > 0 ) {
			return $curated;
		}
	}

	if ( $term instanceof WP_Term && $taxonomy ) {
		$base['tax_query'] = array(
			array(
				'taxonomy' => $taxonomy,
				'field'    => 'term_id',
				'terms'    => array( (int) $term->term_id ),
			),
		);
	}

	$featured = new WP_Query(
		array_merge(
			$base,
			array(
				'meta_query' => array(
					array(
						'key'     => 'featured',
						'value'   => '1',
						'compare' => '=',
					),
				),
			)
		)
	);

	if ( ! empty( $featured->posts ) ) {
		$candidate = (int) $featured->posts[0];
		if (
			$term instanceof WP_Term
			&& $taxonomy
			&& ! has_term( (int) $term->term_id, $taxonomy, $candidate )
		) {
			$candidate = 0;
		}
		if ( $candidate > 0 ) {
			return $candidate;
		}
	}

	$latest = new WP_Query(
		array_merge(
			$base,
			array(
				'orderby' => 'date',
				'order'   => 'DESC',
			)
		)
	);

	return ! empty( $latest->posts ) ? (int) $latest->posts[0] : 0;
}

/**
 * Topic term for related commentary on a hub (topic archive uses term; others infer from featured).
 *
 * @param WP_Term|null $term     Active term.
 * @param string       $taxonomy topic|resource_type|''.
 * @return WP_Term|null
 */
function msr_publishing_get_hub_commentary_topic( $term = null, $taxonomy = '' ) {
	if ( $term instanceof WP_Term && 'topic' === $taxonomy ) {
		return $term;
	}

	$featured_id = msr_publishing_get_hub_featured_resource_id( $term, $taxonomy );
	if ( ! $featured_id ) {
		return null;
	}

	$topics = get_the_terms( $featured_id, 'topic' );
	if ( ! $topics || is_wp_error( $topics ) ) {
		return null;
	}

	return $topics[0];
}

/**
 * Featured pick row above the resource grid.
 *
 * @param WP_Term|null $term     Active term.
 * @param string       $taxonomy topic|resource_type|''.
 * @return int Featured post ID (for grid exclusion).
 */
function msr_publishing_render_hub_featured_pick( $term = null, $taxonomy = '' ) {
	$featured_id = msr_publishing_get_hub_featured_resource_id( $term, $taxonomy );
	if ( ! $featured_id ) {
		return 0;
	}

	$post = get_post( $featured_id );
	if ( ! $post || 'resource' !== $post->post_type || 'publish' !== $post->post_status ) {
		return 0;
	}
	?>
	<section class="publishing-hub-featured container pb-4" aria-labelledby="publishing-hub-featured-heading">
		<h2 id="publishing-hub-featured-heading" class="publishing-featured-hero__label text-uppercase small fw-semibold text-muted mb-2">
			<?php esc_html_e( 'Featured pick', 'msrsandbox' ); ?>
		</h2>
		<div class="publishing-featured-hero publishing-hub-featured__card">
			<?php
			get_template_part(
				'template-parts/cards/resource',
				'card',
				array(
					'layout'  => 'featured',
					'post_id' => $featured_id,
				)
			);
			?>
		</div>
	</section>
	<?php

	return $featured_id;
}

/**
 * Related commentary strip for topic-linked hubs.
 *
 * @param WP_Term|null $topic_term Topic for the query.
 * @return void
 */
function msr_publishing_render_hub_commentary_strip( $topic_term = null ) {
	if ( ! $topic_term instanceof WP_Term ) {
		return;
	}

	$related = msr_publishing_get_related_commentary_by_topic( (int) $topic_term->term_id, 0, 3 );
	if ( ! $related->have_posts() ) {
		return;
	}

	$insights = msr_publishing_insights_url();
	?>
	<section class="publishing-hub-commentary container py-4 border-top" aria-labelledby="publishing-hub-commentary-heading">
		<header class="d-flex flex-wrap justify-content-between align-items-end gap-2 mb-4">
			<div>
				<h2 id="publishing-hub-commentary-heading" class="h4 mb-1">
					<?php
					echo esc_html(
						sprintf(
							/* translators: %s: topic name */
							__( 'Commentary on %s', 'msrsandbox' ),
							$topic_term->name
						)
					);
					?>
				</h2>
				<p class="text-muted small mb-0"><?php esc_html_e( 'Editorial analysis linked to this hub.', 'msrsandbox' ); ?></p>
			</div>
			<?php if ( $insights ) : ?>
				<a class="btn btn-sm btn-outline-primary" href="<?php echo esc_url( $insights ); ?>">
					<?php esc_html_e( 'All insights', 'msrsandbox' ); ?>
				</a>
			<?php endif; ?>
		</header>
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
 * Topics hub — card grid linking to topic archives (P26).
 *
 * @return void
 */
function msr_publishing_render_topics_hub() {
	$topics = get_terms(
		array(
			'taxonomy'   => 'topic',
			'hide_empty' => false,
			'orderby'    => 'name',
			'order'      => 'ASC',
		)
	);

	if ( ! $topics || is_wp_error( $topics ) ) {
		echo '<p class="text-center text-muted">';
		esc_html_e( 'No topics are configured yet.', 'msrsandbox' );
		echo '</p>';
		return;
	}
	?>
	<div class="row g-4 publishing-topics-hub">
		<?php foreach ( $topics as $topic ) : ?>
			<?php
			$link = get_term_link( $topic );
			if ( is_wp_error( $link ) ) {
				continue;
			}
			$description = msr_publishing_get_topic_hub_intro( $topic );
			if ( $description === '' ) {
				$description = sprintf(
					/* translators: %s: topic name */
					__( 'Resources and commentary on %s.', 'msrsandbox' ),
					$topic->name
				);
			}
			$count_label = sprintf(
				/* translators: %d: number of resources */
				_n( '%d resource', '%d resources', (int) $topic->count, 'msrsandbox' ),
				(int) $topic->count
			);
			?>
			<div class="col-md-6">
				<article class="publishing-topics-hub__card card h-100 msr-reveal">
					<div class="card-body d-flex flex-column">
						<h2 class="h4 card-title">
							<a href="<?php echo esc_url( $link ); ?>"><?php echo esc_html( $topic->name ); ?></a>
						</h2>
						<p class="card-text text-muted flex-grow-1"><?php echo esc_html( wp_strip_all_tags( $description ) ); ?></p>
						<p class="card-meta small mb-3">
							<span class="card-meta__count"><?php echo esc_html( $count_label ); ?></span>
						</p>
						<a class="btn btn-outline-primary align-self-start" href="<?php echo esc_url( $link ); ?>">
							<?php
							echo esc_html(
								sprintf(
									/* translators: %s: topic name */
									__( 'Browse %s', 'msrsandbox' ),
									$topic->name
								)
							);
							?>
						</a>
					</div>
				</article>
			</div>
		<?php endforeach; ?>
	</div>
	<?php
}
