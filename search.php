<?php
/**
 * Search results — publishing chrome.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#search-result
 *
 * @package msrpublishing
 */

get_header();
?>
<main id="site-content" class="publishing-search-page">
	<div class="container py-4">
		<header class="publishing-section-header text-center">
			<h1 class="page-title msr-reveal">
				<?php
				if ( get_search_query() ) {
					printf(
						/* translators: %s: search query */
						esc_html__( 'Search results for "%s"', 'msrpublishing' ),
						esc_html( get_search_query() )
					);
				} else {
					esc_html_e( 'Search Atlas Briefing', 'msrpublishing' );
				}
				?>
			</h1>
			<?php if ( have_posts() ) : ?>
				<p class="text-muted mb-0 msr-reveal publishing-search-page__results-meta">
					<?php
					global $wp_query;
					printf(
						/* translators: %d: number of results */
						esc_html( _n( '%d result found', '%d results found', (int) $wp_query->found_posts, 'msrpublishing' ) ),
						(int) $wp_query->found_posts
					);
					?>
				</p>
			<?php endif; ?>
		</header>
		<?php msr_publishing_render_search_page_form(); ?>
	</div>
	<?php msr_publishing_render_search_discovery_panel(); ?>
	<div class="container pb-4">
		<?php msr_publishing_render_search_active_filters(); ?>
		<?php if ( have_posts() ) : ?>
			<div class="row g-4 publishing-search-page__grid">
				<?php
				global $wp_query;
				$boost_posts = function_exists( 'msr_publishing_get_search_commentary_boost_posts' )
					? msr_publishing_get_search_commentary_boost_posts( $wp_query )
					: array();
				foreach ( $boost_posts as $boost_post ) {
					$post = $boost_post; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
					setup_postdata( $post );
					get_template_part( 'template-parts/content', 'search' );
				}
				wp_reset_postdata();

				while ( have_posts() ) {
					the_post();
					if ( $boost_posts ) {
						$skip = false;
						foreach ( $boost_posts as $boost_post ) {
							if ( (int) get_the_ID() === (int) $boost_post->ID ) {
								$skip = true;
								break;
							}
						}
						if ( $skip ) {
							continue;
						}
					}
					get_template_part( 'template-parts/content', 'search' );
				}
				?>
			</div>
			<div class="mt-4">
				<?php msr_publishing_render_search_posts_pagination(); ?>
			</div>
		<?php else : ?>
			<?php get_template_part( 'template-parts/content', 'none' ); ?>
		<?php endif; ?>
	</div>
	<?php msr_publishing_render_search_subscribe_band(); ?>
</main>
<?php
get_footer();
