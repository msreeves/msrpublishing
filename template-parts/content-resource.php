<?php
/**
 * Default single resource body (fallback).
 *
 * @package msrsandbox
 */
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'resource-single container py-4' ); ?>>
	<?php get_template_part( 'template-parts/resource/single', 'header' ); ?>
	<?php msr_publishing_render_resource_programme_cta( get_the_ID() ); ?>
	<div class="entry-content resource-single__body">
		<?php the_content(); ?>
	</div>
	<?php get_template_part( 'template-parts/resource/single', 'related' ); ?>
	<?php msr_publishing_render_resource_commentary_crosslink( get_the_ID() ); ?>
	<?php msr_publishing_render_resource_single_subscribe(); ?>
</article>
