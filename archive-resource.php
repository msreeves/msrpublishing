<?php
/**
 * Resource CPT archive.
 *
 * @package msrsandbox
 */

get_header();
?>
<main id="site-content" class="archive-resource">
	<header class="page-header container py-4">
		<h1 class="page-title msr-reveal"><?php esc_html_e( 'Resources', 'msrsandbox' ); ?></h1>
		<p class="taxonomy-description msr-reveal">
			<?php echo esc_html( msr_publishing_get_resources_archive_intro() ); ?>
		</p>
		<?php
		msr_publishing_render_resource_type_nav( 'all' );
		msr_publishing_render_topic_nav( 'all' );
		?>
	</header>
	<?php msr_publishing_render_ecosystem_band_for_archive( 'before' ); ?>
	<?php
	$featured_id = msr_publishing_render_hub_featured_pick();
	$topic_term  = msr_publishing_get_hub_commentary_topic();
	msr_publishing_render_hub_commentary_strip( $topic_term );
	msr_publishing_render_resource_grid( '', $featured_id ? array( $featured_id ) : array() );
	msr_publishing_render_ecosystem_band_for_archive( 'after' );
	?>
	<?php msr_publishing_render_archive_subscribe_band(); ?>
</main>
<?php
get_footer();
