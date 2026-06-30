<?php
/**
 * Generic page template (publishing chrome).
 *
 * @package msrsandbox
 */

get_header();
?>
<main id="site-content" class="publishing-page">
	<div class="container py-5">
		<?php
		while ( have_posts() ) {
			the_post();
			?>
			<article <?php post_class( 'publishing-page__article mx-auto' ); ?>>
				<header class="mb-4 text-center">
					<h1 class="entry-title"><?php the_title(); ?></h1>
					<?php if ( has_excerpt() ) : ?>
						<p class="text-muted"><?php echo esc_html( wp_strip_all_tags( get_the_excerpt() ) ); ?></p>
					<?php endif; ?>
				</header>
				<div class="entry-content publishing-page__body">
					<?php the_content(); ?>
				</div>
			</article>
			<?php
		}
		?>
	</div>
</main>
<?php
get_footer();
