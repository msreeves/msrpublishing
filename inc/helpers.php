<?php
/**
 * Publishing helpers (category slugs, leaderboard gate, related resources).
 *
 * @package msrpublishing
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
function msrpublishing_get_category_id_by_slug( $slug ) {
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
function msrpublishing_get_sponsored_category_id() {
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
function msrpublishing_get_excluded_sponsored_category_ids() {
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
function msrpublishing_post_is_sponsored( $post_id = null ) {
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

	$args = array(
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
	);

	if ( function_exists( 'msr_publishing_append_portfolio_surface_excludes' ) ) {
		$args = msr_publishing_append_portfolio_surface_excludes( $args );
	}

	return new WP_Query( $args );
}

/**
 * Related resource cards at the bottom of format singles.
 *
 * @return int
 */
function msr_publishing_get_resource_related_count() {
	return 3;
}

/**
 * Related resources — prefer shared topic, then shared format.
 *
 * @param int $post_id Resource post ID.
 * @param int $limit   Max posts.
 * @return WP_Query
 */
function msr_publishing_get_related_resources( $post_id = 0, $limit = 0 ) {
	$post_id = $post_id ? (int) $post_id : get_the_ID();
	$limit   = $limit > 0 ? (int) $limit : msr_publishing_get_resource_related_count();
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

	$args = array(
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
	);

	if ( function_exists( 'msr_publishing_append_portfolio_surface_excludes' ) ) {
		$args = msr_publishing_append_portfolio_surface_excludes( $args );
	}

	return new WP_Query( $args );
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
 * Extract YouTube video id from a watch or share URL.
 *
 * @param string $url Video URL.
 * @return string
 */
function msr_publishing_parse_youtube_video_id( $url ) {
	$url = (string) $url;
	if ( $url === '' ) {
		return '';
	}

	if ( preg_match( '#youtu\.be/([a-zA-Z0-9_-]{6,})#', $url, $matches ) ) {
		return (string) $matches[1];
	}

	if ( preg_match( '#(?:youtube\.com|youtube-nocookie\.com)/embed/([a-zA-Z0-9_-]{6,})#', $url, $matches ) ) {
		return (string) $matches[1];
	}

	if ( preg_match( '#[?&]v=([a-zA-Z0-9_-]{6,})#', $url, $matches ) ) {
		return (string) $matches[1];
	}

	return '';
}

/**
 * YouTube still URL (16:9 — avoids hqdefault letterboxing on widescreen embeds).
 *
 * @param string $video_id YouTube video ID.
 * @return string
 */
function msr_publishing_youtube_still_url( $video_id ) {
	$video_id = (string) $video_id;
	if ( $video_id === '' ) {
		return '';
	}

	return sprintf( 'https://i.ytimg.com/vi/%s/mqdefault.jpg', rawurlencode( $video_id ) );
}

/**
 * Normalize oEmbed/provider thumb URLs to crop-safe 16:9 where possible.
 *
 * @param string $thumb_url Remote image URL.
 * @return string
 */
function msr_publishing_normalize_video_thumbnail_url( $thumb_url ) {
	$thumb_url = (string) $thumb_url;
	if ( $thumb_url === '' ) {
		return '';
	}

	if ( preg_match( '#(?:img\.youtube\.com|i\.ytimg\.com)/vi(?:_webp)?/([a-zA-Z0-9_-]{6,})/#', $thumb_url, $matches ) ) {
		return msr_publishing_youtube_still_url( $matches[1] );
	}

	return $thumb_url;
}

/**
 * Remote still frame for oEmbed video URLs (card preview — not random editorial stock).
 *
 * @param string $url YouTube/Vimeo URL.
 * @return string Image URL or empty.
 */
function msr_publishing_get_oembed_video_thumbnail_url( $url ) {
	$url = (string) $url;
	if ( $url === '' || ! msr_publishing_is_oembed_video_url( $url ) ) {
		return '';
	}

	$youtube_id = msr_publishing_parse_youtube_video_id( $url );
	if ( $youtube_id !== '' ) {
		return msr_publishing_youtube_still_url( $youtube_id );
	}

	if ( ! function_exists( '_wp_oembed_get_object' ) ) {
		require_once ABSPATH . WPINC . '/class-oembed.php';
	}

	$data = _wp_oembed_get_object()->get_data(
		$url,
		array(
			'width'  => 640,
			'height' => 360,
		)
	);

	if ( is_object( $data ) && ! empty( $data->thumbnail_url ) ) {
		return msr_publishing_normalize_video_thumbnail_url( (string) $data->thumbnail_url );
	}

	return '';
}

/**
 * Fixed editorial placeholder attachment for format cards (no art-direction roulette).
 *
 * @param string $format_slug Canonical format slug.
 * @return int Attachment ID or 0.
 */
function msr_publishing_get_format_card_placeholder_attachment_id( $format_slug ) {
	$format_slug = msr_publishing_resolve_format_slug( (string) $format_slug );
	$slug_map    = array(
		'video'   => 'photo-webinar-01',
		'webinar' => 'photo-webinar-02',
		'podcast' => 'photo-podcast-01',
	);

	if ( ! isset( $slug_map[ $format_slug ] ) ) {
		return 0;
	}

	if ( ! function_exists( 'msr_publishing_get_attachment_id_by_editorial_slug' ) ) {
		return 0;
	}

	return (int) msr_publishing_get_attachment_id_by_editorial_slug( $slug_map[ $format_slug ] );
}

/**
 * Card media preview for video/webinar resources (platform thumb → format placeholder).
 *
 * @param int $post_id Resource ID.
 * @return array{type: string, attachment_id?: int, preview_url?: string}|null Null when not a playback format.
 */
function msr_publishing_get_resource_card_media_preview( $post_id ) {
	$post_id = (int) $post_id;
	if ( $post_id < 1 || 'resource' !== get_post_type( $post_id ) || ! function_exists( 'get_field' ) ) {
		return null;
	}

	$format = msr_publishing_resolve_format_slug( msr_publishing_get_primary_resource_type_slug( $post_id ) );
	if ( ! in_array( $format, array( 'video', 'webinar' ), true ) ) {
		return null;
	}

	// Editorial hero / featured image wins on cards (oEmbed thumbs are often generic or shared across seeds).
	if ( function_exists( 'msr_publishing_get_card_hero_attachment_id' ) ) {
		$hero_id = (int) msr_publishing_get_card_hero_attachment_id( $post_id );
		if ( $hero_id > 0 ) {
			return array(
				'type'            => 'attachment',
				'attachment_id'   => $hero_id,
				'preview_url'     => '',
			);
		}
	}

	$source_url = '';
	if ( 'video' === $format ) {
		$source_url = (string) get_field( 'video_url', $post_id );
	} else {
		$source_url = (string) get_field( 'replay_url', $post_id );
	}

	$thumb = msr_publishing_get_oembed_video_thumbnail_url( $source_url );
	if ( $thumb !== '' ) {
		return array(
			'type'         => 'url',
			'preview_url'  => $thumb,
			'attachment_id' => 0,
		);
	}

	$placeholder_id = msr_publishing_get_format_card_placeholder_attachment_id( $format );
	if ( $placeholder_id ) {
		return array(
			'type'            => 'attachment',
			'attachment_id'   => $placeholder_id,
			'preview_url'     => '',
		);
	}

	return array(
		'type' => 'placeholder',
	);
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
 * Whether a resource single will render an in-page video player (hero would duplicate).
 *
 * @param int $post_id Resource ID.
 * @return bool
 */
function msr_publishing_resource_has_inline_playback( $post_id = 0 ) {
	$post_id = $post_id ? (int) $post_id : (int) get_the_ID();
	if ( $post_id < 1 || 'resource' !== get_post_type( $post_id ) || ! function_exists( 'get_field' ) ) {
		return false;
	}

	$format = msr_publishing_resolve_format_slug( msr_publishing_get_primary_resource_type_slug( $post_id ) );

	if ( 'video' === $format ) {
		$url = (string) get_field( 'video_url', $post_id );
		return $url !== '' && msr_publishing_get_video_embed_html( $url, $post_id ) !== '';
	}

	if ( 'webinar' === $format ) {
		$url = (string) get_field( 'replay_url', $post_id );
		return msr_publishing_get_webinar_replay_embed_html( $url, $post_id ) !== '';
	}

	if ( 'podcast' === $format ) {
		return msr_publishing_get_podcast_audio_url( $post_id ) !== '';
	}

	return false;
}

/**
 * Show hero banner image on resource singles (suppressed when inline player is primary).
 *
 * @param int $post_id Resource ID.
 * @return bool
 */
function msr_publishing_resource_single_show_hero_banner( $post_id = 0 ) {
	$post_id = $post_id ? (int) $post_id : (int) get_the_ID();
	if ( msr_publishing_resource_has_inline_playback( $post_id ) ) {
		return false;
	}

	return (bool) msr_publishing_get_card_hero_attachment_id( $post_id );
}

/**
 * Whether card media should show a playback affordance (video/webinar with thumbnail).
 *
 * @param int $post_id Post or resource ID.
 * @return bool
 */
function msr_publishing_card_media_is_playback_format( $post_id ) {
	$post_id = (int) $post_id;
	if ( $post_id < 1 || 'resource' !== get_post_type( $post_id ) ) {
		return false;
	}

	$format = msr_publishing_resolve_format_slug( msr_publishing_get_primary_resource_type_slug( $post_id ) );

	return in_array( $format, array( 'video', 'webinar' ), true );
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
			<h2 class="h5 card-title"><?php esc_html_e( 'Speakers', 'msrpublishing' ); ?></h2>
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
		esc_html__( 'Download audio', 'msrpublishing' )
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
 * Resource single masthead — format pill + topic chips (not stacked above title).
 *
 * @param int $post_id Resource ID.
 * @return void
 */
function msr_publishing_render_resource_single_masthead( $post_id = 0 ) {
	$post_id = $post_id ? (int) $post_id : get_the_ID();
	$primary = msr_publishing_get_primary_resource_type( $post_id );
	?>
	<div class="resource-single__masthead">
		<?php if ( $primary ) : ?>
			<div class="resource-single__masthead-primary">
				<?php msr_publishing_render_format_badge( $primary ); ?>
			</div>
		<?php endif; ?>
		<div class="resource-single__masthead-chips">
			<?php msr_publishing_render_resource_topic_badges( $post_id ); ?>
		</div>
	</div>
	<?php
	msr_publishing_render_resource_series_context( $post_id );
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
 * Density filler posts (PUB-UX-6c) — hidden from portfolio surfaces, not deleted.
 *
 * @param int $post_id Post or resource ID.
 * @return bool
 */
function msr_publishing_is_density_filler_post( $post_id ) {
	$post_id = (int) $post_id;
	if ( ! $post_id ) {
		return false;
	}

	if ( get_post_meta( $post_id, '_msr_density_seed', true ) ) {
		return true;
	}

	$slug = (string) get_post_field( 'post_name', $post_id );
	return (bool) preg_match( '/^density-/', $slug );
}

/**
 * Seed or numbered demo titles that should not headline curated surfaces.
 *
 * @param int $post_id Post or resource ID.
 * @return bool
 */
function msr_publishing_is_curated_surface_noise_post( $post_id ) {
	$post_id = (int) $post_id;
	if ( ! $post_id || msr_publishing_is_density_filler_post( $post_id ) ) {
		return (bool) $post_id && msr_publishing_is_density_filler_post( $post_id );
	}

	$title = trim( (string) get_the_title( $post_id ) );
	if ( $title === '' ) {
		return false;
	}

	$patterns = array(
		'/library seed/i',
		'/^Density /i',
		'/^Whitepaper library seed/i',
		'/^Webinar library seed/i',
		'/^Video library seed/i',
		'/— (Whitepaper|Webinar|Briefing|Playbook|Video|Podcast|Case study) \(\d+\)/i',
		'/· (Talent|Programmes) \d+/i',
		'/^Programme portfolio view ·/i',
		'/^Talent mobility outlook ·/i',
		'/^Cross-programme dependencies ·/i',
		'/^Atlas Briefing — Q1 sector scan #\d+/i',
		'/^Atlas Briefing — workshop takeaways #\d+/i',
		'/^Atlas Briefing — research hygiene #\d+/i',
		'/^Atlas Briefing — field operations digest #\d+/i',
	);

	foreach ( $patterns as $pattern ) {
		if ( preg_match( $pattern, $title ) ) {
			return true;
		}
	}

	return false;
}

/**
 * Published density filler IDs (cached per request).
 *
 * @return int[]
 */
function msr_publishing_get_density_filler_post_ids() {
	static $cached = null;

	if ( null !== $cached ) {
		return $cached;
	}

	$cached = get_posts(
		array(
			'post_type'              => array( 'post', 'resource' ),
			'post_status'            => 'publish',
			'posts_per_page'         => -1,
			'fields'                 => 'ids',
			'no_found_rows'          => true,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
			'meta_query'             => array(
				array(
					'key'     => '_msr_density_seed',
					'compare' => 'EXISTS',
				),
			),
		)
	);

	$cached = array_map( 'intval', $cached );

	return $cached;
}

/**
 * Published posts/resources with demo/seed titles — hidden from curated surfaces.
 *
 * @return int[]
 */
function msr_publishing_get_curated_surface_noise_post_ids() {
	static $cached = null;

	if ( null !== $cached ) {
		return $cached;
	}

	$ids = get_posts(
		array(
			'post_type'              => array( 'post', 'resource' ),
			'post_status'            => 'publish',
			'posts_per_page'         => -1,
			'fields'                 => 'ids',
			'no_found_rows'          => true,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
		)
	);

	$cached = array();
	foreach ( $ids as $id ) {
		$id = (int) $id;
		if ( $id && msr_publishing_is_curated_surface_noise_post( $id ) ) {
			$cached[] = $id;
		}
	}

	return $cached;
}

/**
 * Merge density filler IDs into WP_Query post__not_in.
 *
 * @param array<string, mixed> $query_args Query args.
 * @return array<string, mixed>
 */
function msr_publishing_append_portfolio_surface_excludes( $query_args ) {
	$exclude = array_merge(
		msr_publishing_get_density_filler_post_ids(),
		msr_publishing_get_curated_surface_noise_post_ids()
	);
	$exclude = array_values( array_unique( array_filter( array_map( 'intval', $exclude ) ) ) );
	if ( ! $exclude ) {
		return $query_args;
	}

	$existing = isset( $query_args['post__not_in'] ) ? (array) $query_args['post__not_in'] : array();
	$query_args['post__not_in'] = array_values(
		array_unique(
			array_merge(
				array_map( 'intval', $existing ),
				$exclude
			)
		)
	);

	return $query_args;
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

/**
 * Focal map for editorial stock (card vs single/banner crops).
 *
 * @return array<string, array{card?: string, single?: string}>
 */
function msr_publishing_editorial_object_position_map() {
	static $map = null;
	if ( null !== $map ) {
		return $map;
	}

	$map   = array();
	$paths = array();
	if ( function_exists( 'get_template_directory' ) ) {
		$repo_root = dirname( get_template_directory(), 6 );
		if ( $repo_root && is_dir( $repo_root ) ) {
			$paths[] = $repo_root . '/scripts/seeds/publishing/assets/editorial/object-positions.json';
		}
	}

	foreach ( $paths as $path ) {
		$real = realpath( $path );
		if ( $real && is_readable( $real ) ) {
			$data = json_decode( file_get_contents( $real ), true );
			if ( is_array( $data ) ) {
				foreach ( $data as $slug => $positions ) {
					if ( str_starts_with( (string) $slug, '_' ) || ! is_array( $positions ) ) {
						continue;
					}
					$map[ sanitize_title( (string) $slug ) ] = $positions;
				}
				return $map;
			}
		}
	}

	return $map;
}

/**
 * CSS object-position for a hero attachment in a crop context.
 *
 * @param int    $attachment_id Attachment ID.
 * @param string $context       card|single.
 * @return string e.g. 50% 40% or empty.
 */
function msr_publishing_get_attachment_object_position( $attachment_id, $context = 'card' ) {
	$attachment_id = (int) $attachment_id;
	$context       = ( 'single' === $context ) ? 'single' : 'card';
	if ( ! $attachment_id ) {
		return '';
	}

	$meta_key = ( 'single' === $context ) ? '_msr_object_position_single' : '_msr_object_position_card';
	$stored   = (string) get_post_meta( $attachment_id, $meta_key, true );
	if ( $stored !== '' && preg_match( '/^\d{1,3}%\s+\d{1,3}%$/', trim( $stored ) ) ) {
		return trim( $stored );
	}

	if ( msr_publishing_is_editorial_stock_attachment( $attachment_id ) ) {
		$slug = sanitize_title( (string) get_post_field( 'post_name', $attachment_id ) );
		$map  = msr_publishing_editorial_object_position_map();
		if ( $slug !== '' && isset( $map[ $slug ][ $context ] ) ) {
			return (string) $map[ $slug ][ $context ];
		}
	}

	return '50% 50%';
}

/**
 * Merge object-position into attachment image attributes for publishing crops.
 *
 * @param array<string, string> $attr       Image attributes.
 * @param WP_Post               $attachment Attachment post.
 * @return array<string, string>
 */
function msr_publishing_attachment_image_crop_attrs( $attr, $attachment ) {
	if ( empty( $attr['class'] ) ) {
		return $attr;
	}

	$context = '';
	if ( str_contains( $attr['class'], 'publishing-home-promo__img' ) ) {
		return $attr;
	}

	if ( str_contains( $attr['class'], 'msr-card-media__img' ) ) {
		$context = 'card';
		unset( $attr['width'], $attr['height'] );
	} elseif ( str_contains( $attr['class'], 'msr-single-media__img' ) ) {
		$context = 'single';
	} elseif ( str_contains( $attr['class'], 'featured-media-inner' ) ) {
		$context = 'single';
	}

	if ( $context === '' ) {
		return $attr;
	}

	$position = msr_publishing_get_attachment_object_position( (int) $attachment->ID, $context );
	if ( $position === '' ) {
		return $attr;
	}

	$style = isset( $attr['style'] ) ? rtrim( (string) $attr['style'], ';' ) . ';' : '';
	$attr['style'] = $style . 'object-position:' . $position . ';';

	return $attr;
}
add_filter( 'wp_get_attachment_image_attributes', 'msr_publishing_attachment_image_crop_attrs', 20, 2 );
