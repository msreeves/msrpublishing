<?php
/**
 * Case study single resource.
 *
 * @package msrsandbox
 */

$post_id = get_the_ID();
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'resource-single resource-single--case-study container py-4' ); ?>>
	<?php get_template_part( 'template-parts/resource/single', 'header' ); ?>
	<?php msr_publishing_render_single_utility_band( $post_id ); ?>
	<?php msr_publishing_render_resource_programme_cta( $post_id ); ?>
	<?php msr_publishing_render_case_study_industry( $post_id ); ?>
	<?php msr_publishing_render_case_study_metrics( $post_id ); ?>
	<?php msr_publishing_render_key_takeaways( $post_id ); ?>
	<?php msr_publishing_render_case_study_narrative( $post_id ); ?>

	<div class="entry-content resource-single__body">
		<?php the_content(); ?>
	</div>
	<?php get_template_part( 'template-parts/resource/single', 'related' ); ?>
	<?php msr_publishing_render_resource_commentary_crosslink( $post_id ); ?>
	<?php msr_publishing_render_resource_single_subscribe(); ?>
</article>
