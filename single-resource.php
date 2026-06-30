<?php
/**
 * Single resource.
 *
 * @package msrsandbox
 */

get_header();
?>
<main id="site-content" class="single-resource">
	<?php
	while ( have_posts() ) {
		the_post();
		$format_slug = msr_publishing_get_primary_resource_type_slug( get_the_ID() );
		$part        = 'resource';
		if ( in_array( $format_slug, array( 'whitepaper', 'webinar', 'video', 'podcast', 'case-study' ), true ) ) {
			$part = 'resource-' . $format_slug;
		}
		get_template_part( 'template-parts/content', $part );
	}
	?>
</main>
<?php
get_footer();
