<?php
/**
 * Publishing helpers (category slugs, leaderboard gate, related resources).
 *
 * @package msrsandbox
 */

/**
 * Whether legacy leaderboard advert carousels should render.
 *
 * Atlas Briefing publishing routes never load header/footer advert carousels.
 *
 * @return bool
 */
function msr_publishing_show_leaderboard_ads() {
	if ( is_admin() ) {
		return false;
	}

	if ( msr_publishing_is_atlas_briefing_route() ) {
		return false;
	}

	$default = false;

	if ( is_page() && ! is_front_page() ) {
		$template = get_page_template_slug();
		if ( $template && 0 === strpos( $template, 'templates/template-' ) ) {
			$default = true;
		}
	}

	/**
	 * Filter leaderboard advert partials on publishing routes.
	 *
	 * @param bool $show Whether to show header/footer advert carousels.
	 */
	return (bool) apply_filters( 'msr_publishing_show_leaderboard_ads', $default );
}

/**
 * Modern Atlas Briefing front-end routes (no legacy advert carousels).
 *
 * @return bool
 */
function msr_publishing_is_atlas_briefing_route() {
	if ( is_front_page() ) {
		return true;
	}

	if ( is_post_type_archive( 'resource' ) || is_singular( 'resource' ) ) {
		return true;
	}

	if ( is_tax( array( 'topic', 'resource_type', 'resource_series' ) ) ) {
		return true;
	}

	if ( is_singular( 'post' ) || is_author() || is_search() ) {
		return true;
	}

	if ( is_page() ) {
		$template = get_page_template_slug();
		if ( ! $template || 0 !== strpos( $template, 'templates/template-' ) ) {
			return true;
		}
	}

	return (bool) apply_filters( 'msr_publishing_is_atlas_briefing_route', false );
}

/**
 * @param string $slug Category slug.
 * @return int Term ID or 0.
 */
function msr_publishing_get_category_id_by_slug( $slug ) {
	static $cache = array();
	$slug         = sanitize_title( $slug );
	if ( isset( $cache[ $slug ] ) ) {
		return $cache[ $slug ];
	}
	$term           = get_category_by_slug( $slug );
	$cache[ $slug ] = $term ? (int) $term->term_id : 0;
	return $cache[ $slug ];
}

/** @deprecated */
function msrsandbox_get_category_id_by_slug( $slug ) {
	return msr_publishing_get_category_id_by_slug( $slug );
}

/**
 * Sponsored-content category (slug-based, not hard-coded ID).
 *
 * @return int
 */
function msr_publishing_get_sponsored_category_id() {
	return msr_publishing_get_category_id_by_slug( 'sponsored-content' );
}

/** @deprecated */
function msrsandbox_get_sponsored_category_id() {
	return msr_publishing_get_sponsored_category_id();
}

/**
 * Category IDs to exclude from general editorial lists.
 *
 * @return int[]
 */
function msr_publishing_get_excluded_sponsored_category_ids() {
	$id = msr_publishing_get_sponsored_category_id();
	return $id ? array( $id ) : array();
}

/** @deprecated */
function msrsandbox_get_excluded_sponsored_category_ids() {
	return msr_publishing_get_excluded_sponsored_category_ids();
}

/**
 * @param int|null $post_id Post ID.
 * @return bool
 */
function msr_publishing_post_is_sponsored( $post_id = null ) {
	$post_id      = $post_id ? (int) $post_id : get_the_ID();
	$sponsored_id = msr_publishing_get_sponsored_category_id();
	if ( ! $sponsored_id ) {
		return false;
	}
	return has_category( $sponsored_id, $post_id );
}

/** @deprecated */
function msrsandbox_post_is_sponsored( $post_id = null ) {
	return msr_publishing_post_is_sponsored( $post_id );
}

/**
 * Related resources sharing a topic with the current post.
 *
 * @param int $topic_id Topic term ID.
 * @param int $exclude  Post ID to exclude.
 * @param int $limit    Max posts.
 * @return WP_Query
 */
function msr_publishing_get_related_resources_by_topic( $topic_id, $exclude = 0, $limit = 3 ) {
	$topic_id = (int) $topic_id;
	$exclude  = (int) $exclude;

	if ( ! $topic_id ) {
		return new WP_Query( array( 'post__in' => array( 0 ) ) );
	}

	return new WP_Query(
		array(
			'post_type'           => 'resource',
			'posts_per_page'      => $limit,
			'post__not_in'        => $exclude ? array( $exclude ) : array(),
			'orderby'             => 'date',
			'order'               => 'DESC',
			'no_found_rows'       => true,
			'ignore_sticky_posts' => true,
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
 * Related resources — prefer shared topic, then shared format.
 *
 * @param int $post_id Resource post ID.
 * @param int $limit   Max posts.
 * @return WP_Query
 */
function msr_publishing_get_related_resources( $post_id = 0, $limit = 3 ) {
	$post_id = $post_id ? (int) $post_id : get_the_ID();
	$topics  = get_the_terms( $post_id, 'topic' );

	if ( $topics && ! is_wp_error( $topics ) ) {
		$by_topic = msr_publishing_get_related_resources_by_topic( (int) $topics[0]->term_id, $post_id, $limit );
		if ( $by_topic->have_posts() ) {
			return $by_topic;
		}
	}

	$terms = get_the_terms( $post_id, 'resource_type' );

	if ( ! $terms || is_wp_error( $terms ) ) {
		return new WP_Query(
			array(
				'post__in' => array( 0 ),
			)
		);
	}

	return new WP_Query(
		array(
			'post_type'           => 'resource',
			'posts_per_page'      => $limit,
			'post__not_in'        => array( $post_id ),
			'orderby'             => 'date',
			'order'               => 'DESC',
			'no_found_rows'       => true,
			'ignore_sticky_posts' => true,
			'tax_query'           => array(
				array(
					'taxonomy' => 'resource_type',
					'field'    => 'term_id',
					'terms'    => wp_list_pluck( $terms, 'term_id' ),
				),
			),
		)
	);
}

/**
 * Primary resource_type term for format badges (whitepaper/webinar preferred).
 *
 * @param int $post_id Resource ID.
 * @return WP_Term|null
 */
function msr_publishing_get_primary_resource_type( $post_id = 0 ) {
	$post_id = $post_id ? (int) $post_id : get_the_ID();
	$terms   = get_the_terms( $post_id, 'resource_type' );

	if ( ! $terms || is_wp_error( $terms ) ) {
		return null;
	}

	$priority = msr_publishing_get_format_slugs();
	foreach ( $priority as $slug ) {
		foreach ( $terms as $term ) {
			$resolved = msr_publishing_resolve_format_slug( $term->slug );
			if ( $resolved === $slug || $term->slug === $slug ) {
				return $term;
			}
		}
	}

	return $terms[0];
}

/**
 * @param int $post_id Resource ID.
 * @return string
 */
function msr_publishing_get_primary_resource_type_slug( $post_id = 0 ) {
	$term = msr_publishing_get_primary_resource_type( $post_id );
	return $term ? $term->slug : '';
}

/**
 * @param string $slug resource_type slug.
 * @return string Font Awesome class.
 */
function msr_publishing_resource_format_icon_class( $slug ) {
	return msr_publishing_get_format_icon_class( $slug );
}

/**
 * Responsive oEmbed markup for video resources.
 *
 * @param string $url     YouTube/Vimeo URL.
 * @param int    $post_id Resource ID (optional context).
 * @return string HTML or empty.
 */
function msr_publishing_get_video_embed_html( $url, $post_id = 0 ) {
	$url = esc_url( (string) $url );
	if ( $url === '' ) {
		return '';
	}

	$embed = wp_oembed_get(
		$url,
		array(
			'width'  => 960,
			'height' => 540,
		)
	);

	if ( ! $embed ) {
		return '';
	}

	return sprintf(
		'<div class="msr-video-embed__frame">%s</div>',
		$embed // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- oEmbed HTML from core
	);
}

/**
 * Whether a URL is likely supported by WordPress oEmbed (YouTube/Vimeo).
 *
 * @param string $url URL to test.
 * @return bool
 */
function msr_publishing_is_oembed_video_url( $url ) {
	$url = (string) $url;
	if ( $url === '' ) {
		return false;
	}

	return (bool) preg_match( '#(youtube\.com|youtu\.be|youtube-nocookie\.com|vimeo\.com)#i', $url );
}

/**
 * Embedded webinar replay when replay_url is YouTube/Vimeo.
 *
 * @param string $url     Replay URL.
 * @param int    $post_id Resource ID (optional context).
 * @return string HTML or empty.
 */
function msr_publishing_get_webinar_replay_embed_html( $url, $post_id = 0 ) {
	if ( ! msr_publishing_is_oembed_video_url( $url ) ) {
		return '';
	}

	return msr_publishing_get_video_embed_html( $url, $post_id );
}

/**
 * Render webinar speakers repeater.
 *
 * @param int $post_id Resource ID.
 * @return void
 */
function msr_publishing_render_webinar_speakers( $post_id = 0 ) {
	$post_id = $post_id ? (int) $post_id : (int) get_the_ID();
	if ( $post_id < 1 || ! function_exists( 'get_field' ) ) {
		return;
	}

	$rows = get_field( 'webinar_speakers', $post_id );
	if ( ! is_array( $rows ) || ! $rows ) {
		return;
	}

	$speakers = array();
	foreach ( $rows as $row ) {
		if ( ! is_array( $row ) ) {
			continue;
		}
		$name = trim( (string) ( $row['name'] ?? '' ) );
		if ( $name === '' ) {
			continue;
		}
		$speakers[] = array(
			'name'  => $name,
			'title' => trim( (string) ( $row['title'] ?? '' ) ),
		);
	}

	if ( ! $speakers ) {
		return;
	}
	?>
	<div class="resource-single__webinar-speakers webinar-speakers card mb-4">
		<div class="card-body">
			<h2 class="h5 card-title"><?php esc_html_e( 'Speakers', 'msrsandbox' ); ?></h2>
			<ul class="list-unstyled mb-0 webinar-speakers__list">
				<?php foreach ( $speakers as $speaker ) : ?>
					<li class="webinar-speakers__item mb-2">
						<strong class="webinar-speakers__name"><?php echo esc_html( $speaker['name'] ); ?></strong>
						<?php if ( $speaker['title'] !== '' ) : ?>
							<span class="webinar-speakers__title text-muted d-block small"><?php echo esc_html( $speaker['title'] ); ?></span>
						<?php endif; ?>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
	</div>
	<?php
}

/**
 * Podcast audio URL for a resource.
 *
 * @param int $post_id Resource ID.
 * @return string Escaped URL or empty.
 */
function msr_publishing_get_podcast_audio_url( $post_id = 0 ) {
	$post_id = $post_id ? (int) $post_id : (int) get_the_ID();
	if ( $post_id < 1 ) {
		return '';
	}

	$url = function_exists( 'get_field' ) ? (string) get_field( 'podcast_audio_url', $post_id ) : '';

	return esc_url( $url );
}

/**
 * HTML5 podcast player markup.
 *
 * @param int $post_id Resource ID.
 * @return string HTML or empty.
 */
function msr_publishing_render_podcast_player( $post_id = 0 ) {
	$url = msr_publishing_get_podcast_audio_url( $post_id );
	if ( $url === '' ) {
		return '';
	}

	return sprintf(
		'<div class="msr-podcast-player__frame"><audio class="msr-podcast-player__audio" controls preload="none" src="%1$s"><a href="%1$s">%2$s</a></audio></div>',
		esc_url( $url ),
		esc_html__( 'Download audio', 'msrsandbox' )
	);
}

/**
 * @param WP_Term $term Resource type term.
 * @param bool    $link Whether to link to term archive.
 * @return void
 */
function msr_publishing_render_format_badge( $term, $link = true ) {
	$icon = msr_publishing_resource_format_icon_class( $term->slug );
	$label = sprintf(
		'<i class="fa-solid %1$s me-1" aria-hidden="true"></i><span>%2$s</span>',
		esc_attr( $icon ),
		esc_html( $term->name )
	);

	if ( $link ) {
		printf(
			'<a class="resource-card__type resource-card__format badge text-decoration-none" href="%s">%s</a>',
			esc_url( get_term_link( $term ) ),
			$label // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		);
		return;
	}

	printf(
		'<span class="resource-card__type resource-card__format badge">%s</span>',
		$label // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	);
}

/**
 * @param int $post_id Resource ID.
 * @return bool
 */
function msr_publishing_resource_is_featured( $post_id = 0 ) {
	$post_id = $post_id ? (int) $post_id : get_the_ID();
	if ( function_exists( 'get_field' ) ) {
		return (bool) get_field( 'featured', $post_id );
	}
	return (bool) get_post_meta( $post_id, 'featured', true );
}

/**
 * Partner / event brand marks — not for resource or commentary heroes.
 *
 * @param int $attachment_id Attachment ID.
 * @return bool
 */
function msr_publishing_is_partner_brand_attachment( $attachment_id ) {
	$attachment_id = (int) $attachment_id;
	if ( ! $attachment_id ) {
		return false;
	}

	$mime = (string) get_post_mime_type( $attachment_id );
	if ( 'image/svg+xml' === $mime ) {
		return true;
	}

	$slug  = (string) get_post_field( 'post_name', $attachment_id );
	$title = (string) get_the_title( $attachment_id );

	if ( preg_match( '/-(colored|black)$/i', $slug ) ) {
		return true;
	}
	if ( preg_match( '/onevent|black-friday|montreal-white/i', $slug . ' ' . $title ) ) {
		return true;
	}

	return false;
}

/**
 * Raster editorial stock safe for heroes and post cards (pexels / photo uploads).
 *
 * @param int $attachment_id Attachment ID.
 * @return bool
 */
function msr_publishing_is_editorial_stock_attachment( $attachment_id ) {
	$attachment_id = (int) $attachment_id;
	if ( ! $attachment_id || msr_publishing_is_partner_brand_attachment( $attachment_id ) ) {
		return false;
	}

	$mime = (string) get_post_mime_type( $attachment_id );
	if ( ! in_array( $mime, array( 'image/jpeg', 'image/jpg', 'image/png', 'image/webp' ), true ) ) {
		return false;
	}

	$slug = (string) get_post_field( 'post_name', $attachment_id );
	return (bool) preg_match( '/^(pexels-|photo-)/i', $slug );
}

/**
 * Editorial stock attachment IDs for seeds (newest first).
 *
 * @param int $limit Max IDs.
 * @return int[]
 */
function msr_publishing_get_editorial_stock_image_ids( $limit = 12 ) {
	$limit = max( 1, (int) $limit );
	$candidates = get_posts(
		array(
			'post_type'      => 'attachment',
			'post_mime_type' => array( 'image/jpeg', 'image/png', 'image/webp' ),
			'posts_per_page' => 50,
			'orderby'        => 'ID',
			'order'          => 'DESC',
			'fields'         => 'ids',
		)
	);

	$safe = array();
	foreach ( $candidates as $attachment_id ) {
		if ( msr_publishing_is_editorial_stock_attachment( (int) $attachment_id ) ) {
			$safe[] = (int) $attachment_id;
		}
		if ( count( $safe ) >= $limit ) {
			break;
		}
	}

	return $safe;
}

/**
 * Resolve a hero/thumbnail attachment ID; 0 when partner logo or invalid.
 *
 * @param int $attachment_id Candidate attachment.
 * @return int
 */
function msr_publishing_sanitize_hero_attachment_id( $attachment_id ) {
	$attachment_id = (int) $attachment_id;
	if ( ! $attachment_id || msr_publishing_is_partner_brand_attachment( $attachment_id ) ) {
		return 0;
	}

	$mime = (string) get_post_mime_type( $attachment_id );
	if ( in_array( $mime, array( 'image/jpeg', 'image/jpg', 'image/png', 'image/webp', 'image/gif' ), true ) ) {
		return $attachment_id;
	}

	return 0;
}

/**
 * Whether the site logo needs a darkening filter on the light header.
 *
 * @param int $logo_id Attachment ID.
 * @return bool
 */
function msr_publishing_site_logo_needs_invert( $logo_id = 0 ) {
	$logo_id = $logo_id ? (int) $logo_id : (int) get_theme_mod( 'custom_logo' );
	if ( ! $logo_id ) {
		return false;
	}
	$slug  = (string) get_post_field( 'post_name', $logo_id );
	$title = (string) get_the_title( $logo_id );
	return (bool) preg_match( '/white/i', $slug . ' ' . $title );
}
