<?php
/**
 * Topic taxonomy archive (resource library hubs).
 *
 * @package msrsandbox
 */

get_header();

$term = get_queried_object();
?>
<main id="site-content" class="taxonomy-topic">
	<header class="page-header container py-4">
		<h1 class="page-title msr-reveal"><?php single_term_title(); ?></h1>
		<?php if ( $term instanceof WP_Term ) : ?>
			<div class="taxonomy-description publishing-topic-hub__intro msr-reveal">
				<?php echo wp_kses_post( wpautop( msr_publishing_get_topic_hub_intro( $term ) ) ); ?>
			</div>
		<?php endif; ?>
		<?php
		msr_publishing_render_resource_type_nav( 'all' );
		msr_publishing_render_topic_nav( 'term', $term instanceof WP_Term ? $term : null );
		?>
	</header>
	<?php msr_publishing_render_ecosystem_band_for_archive( 'before' ); ?>
	<?php
	$featured_id = msr_publishing_render_hub_featured_pick( $term instanceof WP_Term ? $term : null, 'topic' );
	msr_publishing_render_hub_commentary_strip( $term instanceof WP_Term ? $term : null );
	msr_publishing_render_resource_grid(
		__( 'No resources for this topic yet.', 'msrsandbox' ),
		$featured_id ? array( $featured_id ) : array()
	);
	msr_publishing_render_ecosystem_band_for_archive( 'after' );
	?>
	<?php msr_publishing_render_archive_subscribe_band(); ?>
</main>
<?php
get_footer();
