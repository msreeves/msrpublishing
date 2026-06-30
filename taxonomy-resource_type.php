<?php
/**
 * Resource type taxonomy archive.
 *
 * @package msrsandbox
 */

get_header();

$term = get_queried_object();
?>
<main id="site-content" class="taxonomy-resource_type">
	<header class="page-header container py-4">
		<h1 class="page-title msr-reveal"><?php single_term_title(); ?></h1>
		<?php
		$desc = term_description();
		if ( $desc ) {
			echo '<div class="taxonomy-description msr-reveal">' . wp_kses_post( $desc ) . '</div>';
		} else {
			?>
			<p class="taxonomy-format-intro text-muted msr-reveal mb-0">
				<?php esc_html_e( 'Browse resources in this format. The featured pick is the best entry point; the grid below lists every matching asset.', 'msrsandbox' ); ?>
			</p>
			<?php
		}
		?>
		<?php
		msr_publishing_render_resource_type_nav( 'term', $term instanceof WP_Term ? $term : null );
		msr_publishing_render_topic_nav( 'all', null, $term instanceof WP_Term ? $term : null );
		?>
	</header>
	<?php
	$featured_id = msr_publishing_render_hub_featured_pick( $term instanceof WP_Term ? $term : null, 'resource_type' );
	msr_publishing_render_resource_grid(
		__( 'No resources in this format yet.', 'msrsandbox' ),
		$featured_id ? array( $featured_id ) : array()
	);
	?>
	<?php msr_publishing_render_archive_subscribe_band(); ?>
</main>
<?php
get_footer();
