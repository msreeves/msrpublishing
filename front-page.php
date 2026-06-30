<?php
/**
 * Publishing home.
 *
 * @package msrsandbox
 */

get_header();
?>
<main id="site-content" class="front-page publishing-home">
	<?php
	get_template_part( 'template-parts/sections/home', 'hero' );
	msr_publishing_render_home_social_proof();
	get_template_part( 'template-parts/sections/home', 'commentary-preview' );
	get_template_part( 'template-parts/sections/home', 'discover' );
	get_template_part( 'template-parts/sections/featured', 'resources' );
	msr_publishing_render_home_format_highlights();
	msr_publishing_render_ecosystem_band();
	msr_publishing_render_home_subscribe_band();
	msr_publishing_render_home_more_bands();
	?>
</main>
<?php
msr_publishing_render_home_subscribe_sticky();
get_footer();
