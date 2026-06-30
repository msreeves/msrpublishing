<?php
/**
 * Fallback template when no more specific template matches.
 *
 * @package msrsandbox
 */

get_header();
?>
<main id="site-content">
	<section>
		<div class="container">
			<div class="panel">
				<?php
				if ( have_posts() ) {
					while ( have_posts() ) {
						the_post();
						the_title( '<h1 class="entry-title">', '</h1>' );
						the_content();
					}
				}
				?>
			</div>
		</div>
	</section>
</main>
<?php
get_footer();
