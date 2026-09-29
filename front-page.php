<?php
/**
 * Publishing home — hero, library, insights, formats, bento, topic discovery, network, subscribe.
 *
 * @package msrpublishing
 */

get_header();
?>
<main id="site-content" class="front-page publishing-home">
	<?php
	get_template_part( 'template-parts/sections/home', 'hero' );
	get_template_part( 'template-parts/sections/featured', 'resources' );
	get_template_part( 'template-parts/sections/home', 'commentary-preview' );
	msr_publishing_render_home_format_highlights();
	msr_publishing_render_home_bento();
	get_template_part( 'template-parts/sections/home', 'discover' );
	msr_publishing_render_ecosystem_band();
	msr_publishing_render_home_subscribe_band();
	msr_publishing_render_home_more_bands();
	?>
</main>
<?php
get_footer();
