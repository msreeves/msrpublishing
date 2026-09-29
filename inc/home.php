<?php
/**
 * Publishing home — section headings, promo bands, advert queries.
 *
 * @package msrpublishing
 */

/**
 * Home commentary cards (insights preview grid).
 *
 * @return int
 */
function msr_publishing_get_home_commentary_count() {
	return 6;
}

/**
 * Home resource library grid size (format-diverse picks).
 *
 * @return int
 */
function msr_publishing_get_home_library_count() {
	return 3;
}

/**
 * Resources per format highlight row on the home page.
 *
 * @return int
 */
function msr_publishing_get_home_format_row_count() {
	return 4;
}

/**
 * Headline links beside the home hero (desktop rail — not full cards).
 *
 * @return int
 */
function msr_publishing_get_home_hero_headline_count() {
	return 6;
}

/**
 * Latest commentary IDs for the hero headline rail (cached per request).
 *
 * @return int[]
 */
function msr_publishing_get_home_hero_headline_ids() {
	static $ids = null;

	if ( is_array( $ids ) ) {
		return $ids;
	}

	$query = new WP_Query(
		msr_publishing_get_commentary_query_args(
			0,
			msr_publishing_get_home_hero_headline_count()
		)
	);

	$ids = array();
	if ( $query->have_posts() ) {
		foreach ( $query->posts as $post ) {
			$ids[] = (int) $post->ID;
		}
	}

	return $ids;
}

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
			'before' => __( 'Latest', 'msrpublishing' ),
			'accent' => __( 'whitepapers.', 'msrpublishing' ),
			'lead'   => __( 'Long-form PDF assets and gated downloads from Atlas Briefing.', 'msrpublishing' ),
		),
		array(
			'slug'   => 'webinar',
			'before' => __( 'Webinar', 'msrpublishing' ),
			'accent' => __( 'replays.', 'msrpublishing' ),
			'lead'   => __( 'On-demand sessions and registration-ready replays.', 'msrpublishing' ),
		),
		array(
			'slug'   => 'video',
			'before' => __( 'Video', 'msrpublishing' ),
			'accent' => __( 'explainers.', 'msrpublishing' ),
			'lead'   => __( 'Short-form explainers and embedded replays from Atlas Briefing.', 'msrpublishing' ),
		),
		array(
			'slug'   => 'podcast',
			'before' => __( 'Podcast', 'msrpublishing' ),
			'accent' => __( 'episodes.', 'msrpublishing' ),
			'lead'   => __( 'Audio briefings and interview clips from Atlas Briefing.', 'msrpublishing' ),
		),
		array(
			'slug'   => 'briefing',
			'before' => __( 'Executive', 'msrpublishing' ),
			'accent' => __( 'briefings.', 'msrpublishing' ),
			'lead'   => __( 'One-page leadership scans and committee-ready summaries.', 'msrpublishing' ),
		),
		array(
			'slug'   => 'playbook',
			'before' => __( 'Field', 'msrpublishing' ),
			'accent' => __( 'playbooks.', 'msrpublishing' ),
			'lead'   => __( 'Checklists and cadence guides for practitioners.', 'msrpublishing' ),
		),
		array(
			'slug'   => 'case-study',
			'before' => __( 'Customer', 'msrpublishing' ),
			'accent' => __( 'case studies.', 'msrpublishing' ),
			'lead'   => __( 'Problem–solution stories with measurable outcomes (demonstration composites).', 'msrpublishing' ),
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

	$scan = max( (int) $limit * 12, 24 );

	return new WP_Query(
		msr_publishing_append_portfolio_surface_excludes(
			array(
				'post_type'           => 'resource',
				'posts_per_page'      => min( 60, $scan ),
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
 * Cached home resource picks — library, bento, and format-row excludes (one query set per request).
 *
 * @return array{featured_id: int, library_ids: int[], bento_ids: int[], format_exclude: int[]}
 */
function msr_publishing_get_home_curated_resource_layout() {
	static $layout = null;

	if ( is_array( $layout ) ) {
		return $layout;
	}

	$featured_id = (int) msr_publishing_get_site_featured_resource_id();
	$exclude     = $featured_id > 0 ? array( $featured_id ) : array();

	$library_ids = msr_publishing_get_home_library_resource_ids( msr_publishing_get_home_library_count(), $exclude );
	$exclude     = array_values( array_unique( array_merge( $exclude, $library_ids ) ) );

	$bento_ids = msr_publishing_get_home_bento_resource_ids( 4, $exclude );

	$layout = array(
		'featured_id'    => $featured_id,
		'library_ids'    => $library_ids,
		'bento_ids'      => $bento_ids,
		// Format rows need their own picks — do not starve rows with library/bento excludes.
		'format_exclude' => $featured_id > 0 ? array( $featured_id ) : array(),
	);

	return $layout;
}

/**
 * Home bento band — newest resources after hero + library (lead card uses featured layout).
 *
 * @param int   $limit   Max resources (default 4: 1 lead + 3 stack).
 * @param int[] $exclude Post IDs to skip.
 * @return int[]
 */
function msr_publishing_get_home_bento_resource_ids( $limit = 4, $exclude = array() ) {
	$limit   = max( 1, (int) $limit );
	$exclude = array_values( array_unique( array_filter( array_map( 'intval', (array) $exclude ) ) ) );

	msr_publishing_init_home_surface_media_registry();

	return msr_publishing_query_pick_resources_unique_card_media(
		array(
			'post_type'           => 'resource',
			'post_status'         => 'publish',
			'post__not_in'        => $exclude,
			'ignore_sticky_posts' => true,
			'orderby'             => 'date',
			'order'               => 'DESC',
		),
		$limit,
		24
	);
}

/**
 * Format-row resource picks with unique card media on the home surface.
 *
 * @param string $type_slug resource_type slug.
 * @param int    $limit     Max posts.
 * @param int[]  $exclude   Post IDs to skip.
 * @return int[]
 */
function msr_publishing_get_home_format_row_resource_ids( $type_slug, $limit = 4, $exclude = array() ) {
	$type_slug = sanitize_title( (string) $type_slug );
	$term      = get_term_by( 'slug', $type_slug, 'resource_type' );
	$limit     = max( 1, (int) $limit );
	$exclude   = array_values( array_unique( array_filter( array_map( 'intval', (array) $exclude ) ) ) );

	$query = msr_publishing_get_home_format_highlight_query( $type_slug, $limit, $exclude );
	if ( ! $query->have_posts() ) {
		return array();
	}

	$picked = array();
	foreach ( $query->posts as $post ) {
		$post_id = is_object( $post ) ? (int) $post->ID : (int) $post;
		if ( $post_id <= 0 ) {
			continue;
		}
		if ( function_exists( 'msr_publishing_is_curated_surface_noise_post' ) && msr_publishing_is_curated_surface_noise_post( $post_id ) ) {
			continue;
		}
		$picked[] = $post_id;
		if ( count( $picked ) >= $limit ) {
			break;
		}
	}

	return array_slice( array_values( array_unique( $picked ) ), 0, $limit );
}

/**
 * Bootstrap column classes for a home format row based on card count (avoids empty grid slots).
 *
 * @param int $count Cards in the row (1–4).
 * @return string
 */
function msr_publishing_get_home_format_row_col_class( $count ) {
	$count = max( 1, min( 4, (int) $count ) );

	switch ( $count ) {
		case 1:
			return 'col-12 col-sm-8 col-lg-6 mx-sm-auto';
		case 2:
			return 'col-6 col-md-6 col-lg-6';
		case 3:
			return 'col-6 col-md-4 col-lg-4';
		default:
			return 'col-6 col-md-4 col-lg-3';
	}
}

/**
 * Bento editorial band on the home page (1 featured + 3-card stack).
 *
 * @return void
 */
function msr_publishing_render_home_bento() {
	if ( ! is_front_page() ) {
		return;
	}

	get_template_part( 'template-parts/sections/home', 'bento' );
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

	return __( 'Connected MSR programme spotlight', 'msrpublishing' );
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

	return __( 'Portfolio spotlight', 'msrpublishing' );
}

/**
 * Latest site update for home hero freshness signal.
 *
 * @return array{label: string, url: string, datetime: string}|null
 */
function msr_publishing_get_home_freshness_signal() {
	$latest_resource = get_posts(
		msr_publishing_append_portfolio_surface_excludes(
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
		)
	);
	$latest_post = get_posts(
		msr_publishing_append_portfolio_surface_excludes(
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
		)
	);

	$candidate = null;
	foreach ( array_merge( $latest_resource, $latest_post ) as $post ) {
		if ( ! $post instanceof WP_Post ) {
			continue;
		}
		if ( function_exists( 'msr_publishing_is_curated_surface_noise_post' ) && msr_publishing_is_curated_surface_noise_post( $post->ID ) ) {
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
		return __( 'Atlas Briefing editorial', 'msrpublishing' );
	}

	$query->the_post();
	$author = msr_publishing_get_content_author_meta( get_the_ID() );
	wp_reset_postdata();

	if ( $author['name'] !== '' ) {
		return $author['name'];
	}

	return __( 'Atlas Briefing editorial', 'msrpublishing' );
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
	<aside class="publishing-subscribe-sticky d-lg-none" aria-label="<?php esc_attr_e( 'Quick subscribe', 'msrpublishing' ); ?>" aria-hidden="true" inert hidden>
		<a class="btn btn-primary publishing-subscribe-sticky__cta" href="#publishing-home-subscribe" tabindex="-1">
			<?php esc_html_e( 'Subscribe to Atlas Briefing', 'msrpublishing' ); ?>
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

	msr_publishing_init_home_surface_media_registry();

	$types = msr_publishing_get_resource_type_nav_terms( array( 'hide_empty' => true ) );
	foreach ( $types as $type ) {
		if ( count( $picked ) >= $limit ) {
			break;
		}

		$type_pick = msr_publishing_query_pick_resources_unique_card_media(
			array(
				'post_type'           => 'resource',
				'post_status'         => 'publish',
				'post__not_in'        => array_merge( $exclude, $picked ),
				'ignore_sticky_posts' => true,
				'orderby'             => 'date',
				'order'               => 'DESC',
				'tax_query'           => array(
					array(
						'taxonomy' => 'resource_type',
						'field'    => 'term_id',
						'terms'    => array( (int) $type->term_id ),
					),
				),
			),
			1,
			12
		);

		if ( $type_pick ) {
			$picked[] = (int) $type_pick[0];
		}
	}

	if ( count( $picked ) < $limit ) {
		$fill = msr_publishing_query_pick_resources_unique_card_media(
			array(
				'post_type'           => 'resource',
				'post_status'         => 'publish',
				'post__not_in'        => array_merge( $exclude, $picked ),
				'ignore_sticky_posts' => true,
				'orderby'             => 'date',
				'order'               => 'DESC',
			),
			$limit - count( $picked ),
			40
		);
		$picked = array_merge( $picked, $fill );
	}

	return array_slice( array_values( array_unique( $picked ) ), 0, $limit );
}

/**
 * Recruiter-facing note — this programme is the portfolio’s current-standard WordPress exemplar.
 *
 * @return void
 */
function msr_publishing_render_home_standards_callout() {
	if ( ! is_front_page() || ! msr_publishing_show_home_standards_callout() ) {
		return;
	}

	$text = msr_publishing_get_home_standards_callout();
	if ( $text === '' ) {
		return;
	}

	get_template_part(
		'template-parts/sections/home',
		'standards',
		array(
			'text' => $text,
		)
	);
}
