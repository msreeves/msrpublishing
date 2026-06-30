<?php
/**
 * Search results — publishing chrome.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#search-result
 *
 * @package msrsandbox
 */

get_header();
?>
<main id="site-content" class="publishing-search-page">
	<div class="container py-4">
		<header class="publishing-section-header text-center mb-4">
			<h1 class="page-title msr-reveal">
				<?php
				printf(
					/* translators: %s: search query */
					esc_html__( 'Search results for "%s"', 'msrsandbox' ),
					esc_html( get_search_query() )
				);
				?>
			</h1>
			<?php if ( have_posts() ) : ?>
				<p class="text-muted mb-0 msr-reveal">
					<?php
					global $wp_query;
					printf(
						/* translators: %d: number of results */
						esc_html( _n( '%d result found', '%d results found', (int) $wp_query->found_posts, 'msrsandbox' ) ),
						(int) $wp_query->found_posts
					);
					?>
				</p>
			<?php endif; ?>
		</header>

		<?php msr_publishing_render_search_type_nav(); ?>
		<?php msr_publishing_render_search_facet_nav(); ?>
		<?php msr_publishing_render_search_sort_nav(); ?>

		<?php if ( have_posts() ) : ?>
			<div class="row g-4">
				<?php
				while ( have_posts() ) {
					the_post();
					get_template_part( 'template-parts/content', 'search' );
				}
				?>
			</div>
			<div class="mt-4">
				<?php
				the_posts_pagination(
					array(
						'mid_size'  => 2,
						'prev_text' => __( 'Previous', 'msrsandbox' ),
						'next_text' => __( 'Next', 'msrsandbox' ),
					)
				);
				?>
			</div>
		<?php else : ?>
			<?php get_template_part( 'template-parts/content', 'none' ); ?>
		<?php endif; ?>
	</div>
	<?php msr_publishing_render_search_subscribe_band(); ?>
</main>
<?php
get_footer();
