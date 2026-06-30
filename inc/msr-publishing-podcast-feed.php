<?php
/**
 * Podcast RSS feed for resource_type=podcast episodes (P47).
 *
 * @package msrsandbox
 */

/**
 * Register custom feed at /feed/podcast/.
 *
 * @return void
 */
function msr_publishing_register_podcast_feed() {
	add_feed( 'podcast', 'msr_publishing_render_podcast_rss_feed' );
}
add_action( 'init', 'msr_publishing_register_podcast_feed' );

/**
 * Podcast feed URL.
 *
 * @return string
 */
function msr_publishing_get_podcast_feed_url() {
	return (string) home_url( '/feed/podcast/' );
}

/**
 * Published podcast resources for the feed.
 *
 * @param int $limit Max episodes.
 * @return WP_Post[]
 */
function msr_publishing_get_podcast_feed_posts( $limit = 20 ) {
	$query = new WP_Query(
		array(
			'post_type'           => 'resource',
			'post_status'         => 'publish',
			'posts_per_page'      => max( 1, (int) $limit ),
			'ignore_sticky_posts' => true,
			'no_found_rows'       => true,
			'tax_query'           => array(
				array(
					'taxonomy' => 'resource_type',
					'field'    => 'slug',
					'terms'    => array( 'podcast' ),
				),
			),
		)
	);

	return $query->have_posts() ? $query->posts : array();
}

/**
 * Output RSS 2.0 feed with enclosures for podcast episodes.
 *
 * @return void
 */
function msr_publishing_render_podcast_rss_feed() {
	$posts     = msr_publishing_get_podcast_feed_posts( 20 );
	$site_name = get_bloginfo( 'name' );
	$site_url  = home_url( '/' );
	$feed_url  = msr_publishing_get_podcast_feed_url();
	$charset   = get_bloginfo( 'charset' );
	$desc      = __( 'Atlas Briefing podcast episodes — audio briefings for portfolio demonstration.', 'msrsandbox' );

	header( 'Content-Type: application/rss+xml; charset=' . $charset, true );

	echo '<?xml version="1.0" encoding="' . esc_attr( $charset ) . '"?' . '>';
	?>
<rss version="2.0"
	xmlns:itunes="http://www.itunes.com/dtds/podcast-1.0.dtd"
	xmlns:content="http://purl.org/rss/1.0/modules/content/">
<channel>
	<title><?php echo esc_html( $site_name . ' — Podcast' ); ?></title>
	<link><?php echo esc_url( $site_url ); ?></link>
	<description><?php echo esc_html( $desc ); ?></description>
	<language><?php echo esc_html( get_bloginfo( 'language' ) ); ?></language>
	<lastBuildDate><?php echo esc_html( gmdate( 'r' ) ); ?></lastBuildDate>
	<atom:link href="<?php echo esc_url( $feed_url ); ?>" rel="self" type="application/rss+xml" xmlns:atom="http://www.w3.org/2005/Atom" />
	<itunes:author><?php echo esc_html( $site_name ); ?></itunes:author>
	<itunes:summary><?php echo esc_html( $desc ); ?></itunes:summary>
	<itunes:explicit>no</itunes:explicit>
	<itunes:category text="Business" />
	<?php foreach ( $posts as $post ) : ?>
		<?php
		$post_id   = (int) $post->ID;
		$permalink = get_permalink( $post_id );
		$audio_url = msr_publishing_get_podcast_audio_url( $post_id );
		$excerpt   = get_the_excerpt( $post );
		$pub_date  = get_post_time( 'r', true, $post );
		$guid      = $permalink ? $permalink : (string) $post_id;
		?>
	<item>
		<title><?php echo esc_html( get_the_title( $post ) ); ?></title>
		<link><?php echo esc_url( $permalink ); ?></link>
		<guid isPermaLink="true"><?php echo esc_url( $guid ); ?></guid>
		<pubDate><?php echo esc_html( $pub_date ); ?></pubDate>
		<description><![CDATA[<?php echo wp_kses_post( $excerpt ); ?>]]></description>
		<?php if ( $audio_url !== '' ) : ?>
		<enclosure url="<?php echo esc_url( $audio_url ); ?>" length="0" type="audio/mpeg" />
		<?php endif; ?>
	</item>
	<?php endforeach; ?>
</channel>
</rss>
	<?php
	exit;
}
