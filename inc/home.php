<?php
/**
 * Publishing home — section headings, promo bands, advert queries.
 *
 * @package msrsandbox
 */

/**
 * Editorial section header (italic accent word — agency-style publishing UX).
 *
 * @param string $before   Text before accent.
 * @param string $accent   Italic accent fragment (include trailing punctuation if desired).
 * @param string $lead       Optional supporting copy.
 * @param string $heading_id Optional heading id for aria-labelledby.
 * @return void
 */
function msr_publishing_render_section_header( $before, $accent, $lead = '', $heading_id = '' ) {
	$before = trim( (string) $before );
	$accent = trim( (string) $accent );
	$heading_id = sanitize_html_class( (string) $heading_id );
	$id_attr    = $heading_id !== '' ? ' id="' . esc_attr( $heading_id ) . '"' : '';
	?>
	<header class="publishing-section-header text-center mb-4">
		<h2<?php echo $id_attr; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> class="publishing-section-header__title msr-reveal msr-reveal--up">
			<?php
			if ( $before !== '' ) {
				echo esc_html( $before );
				echo ' ';
			}
			if ( $accent !== '' ) {
				printf( '<em class="publishing-section-header__accent">%s</em>', esc_html( $accent ) );
			}
			?>
		</h2>
		<?php if ( $lead !== '' ) : ?>
			<p class="publishing-section-header__lead text-muted msr-reveal msr-reveal--up"><?php echo esc_html( $lead ); ?></p>
		<?php endif; ?>
	</header>
	<?php
}

/**
 * Published adverts for a location taxonomy slug.
 *
 * @param string $location_slug location term slug (header, billboard, footer).
 * @return WP_Post[]
 */
function msr_publishing_get_adverts_by_location( $location_slug ) {
	$location_slug = sanitize_title( $location_slug );
	if ( $location_slug === '' ) {
		return array();
	}

	$query = new WP_Query(
		array(
			'post_type'      => 'advert',
			'post_status'    => 'publish',
			'posts_per_page' => 6,
			'no_found_rows'  => true,
			'tax_query'      => array(
				array(
					'taxonomy' => 'location',
					'field'    => 'slug',
					'terms'    => $location_slug,
				),
			),
		)
	);

	return $query->have_posts() ? $query->posts : array();
}

/**
 * Editorial promo band on the home page (billboard adverts → modern CTA strip).
 *
 * @return void
 */
function msr_publishing_render_home_promo_band() {
	if ( ! is_front_page() ) {
		return;
	}

	$adverts = msr_publishing_get_adverts_by_location( 'billboard' );
	if ( ! $adverts ) {
		return;
	}

	get_template_part(
		'template-parts/sections/home',
		'promo',
		array(
			'adverts' => $adverts,
		)
	);
}

/**
 * Format rows for the home page (whitepapers, webinars).
 *
 * @return array<int, array{slug: string, before: string, accent: string, lead: string}>
 */
function msr_publishing_get_home_format_highlight_rows() {
	return array(
		array(
			'slug'   => 'whitepaper',
			'before' => __( 'Latest', 'msrsandbox' ),
			'accent' => __( 'whitepapers.', 'msrsandbox' ),
			'lead'   => __( 'Long-form PDF assets and gated downloads from Atlas Briefing.', 'msrsandbox' ),
		),
		array(
			'slug'   => 'webinar',
			'before' => __( 'Webinar', 'msrsandbox' ),
			'accent' => __( 'replays.', 'msrsandbox' ),
			'lead'   => __( 'On-demand sessions and registration-ready replays.', 'msrsandbox' ),
		),
		array(
			'slug'   => 'video',
			'before' => __( 'Video', 'msrsandbox' ),
			'accent' => __( 'explainers.', 'msrsandbox' ),
			'lead'   => __( 'Short-form explainers and embedded replays from Atlas Briefing.', 'msrsandbox' ),
		),
		array(
			'slug'   => 'podcast',
			'before' => __( 'Podcast', 'msrsandbox' ),
			'accent' => __( 'episodes.', 'msrsandbox' ),
			'lead'   => __( 'Audio briefings and interview clips from Atlas Briefing.', 'msrsandbox' ),
		),
	);
}

/**
 * Resources for a home format highlight row.
 *
 * @param string $type_slug resource_type slug.
 * @param int    $limit     Max posts.
 * @param int[]  $exclude   Post IDs to skip.
 * @return WP_Query
 */
function msr_publishing_get_home_format_highlight_query( $type_slug, $limit = 2, $exclude = array() ) {
	$type_slug = sanitize_title( (string) $type_slug );
	$term      = get_term_by( 'slug', $type_slug, 'resource_type' );

	return new WP_Query(
		array(
			'post_type'           => 'resource',
			'posts_per_page'      => max( 1, (int) $limit ),
			'post_status'         => 'publish',
			'post__not_in'        => array_filter( array_map( 'intval', (array) $exclude ) ),
			'ignore_sticky_posts' => true,
			'no_found_rows'       => true,
			'orderby'             => 'date',
			'order'               => 'DESC',
			'tax_query'           => $term instanceof WP_Term
				? array(
					array(
						'taxonomy' => 'resource_type',
						'field'    => 'term_id',
						'terms'    => array( (int) $term->term_id ),
					),
				)
				: array(),
		)
	);
}

/**
 * Home format highlight bands (whitepapers + webinars).
 *
 * @return void
 */
function msr_publishing_render_home_format_highlights() {
	if ( ! is_front_page() ) {
		return;
	}

	get_template_part( 'template-parts/sections/home', 'format-highlights' );
}

/**
 * About / methodology teaser on the home page.
 *
 * @return void
 */
function msr_publishing_render_home_about_teaser() {
	if ( ! is_front_page() ) {
		return;
	}

	get_template_part( 'template-parts/sections/home', 'about-teaser' );
}

/**
 * Editorial promo title — prefer link label, avoid placeholder advert post titles.
 *
 * @param WP_Post              $advert Advert post.
 * @param array<string, mixed> $link   ACF link field.
 * @return string
 */
function msr_publishing_get_advert_promo_title( WP_Post $advert, $link ) {
	$link_title = is_array( $link ) && ! empty( $link['title'] ) ? trim( (string) $link['title'] ) : '';
	$post_title = trim( get_the_title( $advert->ID ) );

	if ( $link_title !== '' && strlen( $link_title ) >= 12 ) {
		return $link_title;
	}

	if ( $post_title !== '' && strlen( $post_title ) >= 12 && ! preg_match( '/^(mid sale|sale|demo|test|advert)/i', $post_title ) ) {
		return $post_title;
	}

	if ( $link_title !== '' ) {
		return $link_title;
	}

	return __( 'Connected MSR programme spotlight', 'msrsandbox' );
}

/**
 * Promo eyebrow from advert excerpt or default portfolio label.
 *
 * @param WP_Post $advert Advert post.
 * @return string
 */
function msr_publishing_get_advert_promo_eyebrow( WP_Post $advert ) {
	$excerpt = trim( wp_strip_all_tags( get_the_excerpt( $advert->ID ) ) );
	if ( $excerpt !== '' && strlen( $excerpt ) <= 48 && ! preg_match( '/\b(lorem|demonstration sponsor)\b/i', $excerpt ) ) {
		return $excerpt;
	}

	return __( 'Portfolio spotlight', 'msrsandbox' );
}

/**
 * Latest site update for home hero freshness signal.
 *
 * @return array{label: string, url: string, datetime: string}|null
 */
function msr_publishing_get_home_freshness_signal() {
	$latest_resource = get_posts(
		array(
			'post_type'              => 'resource',
			'posts_per_page'         => 1,
			'post_status'            => 'publish',
			'orderby'                => 'date',
			'order'                  => 'DESC',
			'no_found_rows'          => true,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
		)
	);
	$latest_post = get_posts(
		array(
			'post_type'              => 'post',
			'posts_per_page'         => 1,
			'post_status'            => 'publish',
			'orderby'                => 'date',
			'order'                  => 'DESC',
			'no_found_rows'          => true,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
		)
	);

	$candidate = null;
	foreach ( array_merge( $latest_resource, $latest_post ) as $post ) {
		if ( ! $post instanceof WP_Post ) {
			continue;
		}
		$ts = strtotime( $post->post_date_gmt . ' GMT' );
		if ( ! $candidate || $ts > $candidate['ts'] ) {
			$candidate = array(
				'ts'       => $ts,
				'label'    => get_the_title( $post ),
				'url'      => get_permalink( $post ),
				'datetime' => get_post_time( 'c', true, $post ),
			);
		}
	}

	if ( ! $candidate ) {
		return null;
	}

	return array(
		'label'    => $candidate['label'],
		'url'      => $candidate['url'],
		'datetime' => $candidate['datetime'],
	);
}

/**
 * Editorial voice line for the home hero (curated featured author, else latest commentary).
 *
 * @return string
 */
function msr_publishing_get_home_editor_voice() {
	$featured_id = msr_publishing_get_site_featured_resource_id();
	if ( $featured_id ) {
		$author = msr_publishing_get_content_author_meta( $featured_id );
		if ( $author['name'] !== '' ) {
			return $author['name'];
		}
	}

	$query = new WP_Query( msr_publishing_get_commentary_query_args( 0, 1 ) );
	if ( ! $query->have_posts() ) {
		return __( 'Atlas Briefing editorial', 'msrsandbox' );
	}

	$query->the_post();
	$author = msr_publishing_get_content_author_meta( get_the_ID() );
	wp_reset_postdata();

	if ( $author['name'] !== '' ) {
		return $author['name'];
	}

	return __( 'Atlas Briefing editorial', 'msrsandbox' );
}

/**
 * Collapsible lower home bands on narrow viewports (format rows, promo, about).
 *
 * @return void
 */
function msr_publishing_render_home_more_bands() {
	if ( ! is_front_page() ) {
		return;
	}

	get_template_part( 'template-parts/sections/home', 'more-bands' );
}

/**
 * Mobile sticky subscribe bar (home only).
 *
 * @return void
 */
function msr_publishing_render_home_subscribe_sticky() {
	if ( ! is_front_page() ) {
		return;
	}
	?>
	<aside class="publishing-subscribe-sticky d-lg-none" aria-label="<?php esc_attr_e( 'Quick subscribe', 'msrsandbox' ); ?>" aria-hidden="true" inert>
		<a class="btn btn-primary publishing-subscribe-sticky__cta" href="#publishing-home-subscribe">
			<?php esc_html_e( 'Subscribe to Atlas Briefing', 'msrsandbox' ); ?>
		</a>
	</aside>
	<?php
}

/**
 * Home library grid — diverse format mix, then latest fill (excludes hero featured pick).
 *
 * @param int   $limit   Max resources.
 * @param int[] $exclude Post IDs to skip.
 * @return int[]
 */
function msr_publishing_get_home_library_resource_ids( $limit = 6, $exclude = array() ) {
	$limit   = max( 1, (int) $limit );
	$exclude = array_values( array_unique( array_filter( array_map( 'intval', (array) $exclude ) ) ) );
	$picked  = array();

	$types = msr_publishing_get_resource_type_nav_terms( array( 'hide_empty' => true ) );
	foreach ( $types as $type ) {
		if ( count( $picked ) >= $limit ) {
			break;
		}

		$query = new WP_Query(
			array(
				'post_type'           => 'resource',
				'posts_per_page'      => 1,
				'post_status'         => 'publish',
				'post__not_in'        => array_merge( $exclude, $picked ),
				'ignore_sticky_posts' => true,
				'no_found_rows'       => true,
				'fields'              => 'ids',
				'orderby'             => 'date',
				'order'               => 'DESC',
				'tax_query'           => array(
					array(
						'taxonomy' => 'resource_type',
						'field'    => 'term_id',
						'terms'    => array( (int) $type->term_id ),
					),
				),
			)
		);

		if ( ! empty( $query->posts ) ) {
			$picked[] = (int) $query->posts[0];
		}
	}

	if ( count( $picked ) < $limit ) {
		$fill = new WP_Query(
			array(
				'post_type'           => 'resource',
				'posts_per_page'      => $limit - count( $picked ),
				'post_status'         => 'publish',
				'post__not_in'        => array_merge( $exclude, $picked ),
				'ignore_sticky_posts' => true,
				'no_found_rows'       => true,
				'fields'              => 'ids',
				'orderby'             => 'date',
				'order'               => 'DESC',
			)
		);

		foreach ( $fill->posts as $post_id ) {
			$picked[] = (int) $post_id;
		}
	}

	return array_slice( $picked, 0, $limit );
}
