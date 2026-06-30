<?php
/**
 * Publishing home — commentary feed with in-page topic filter tabs.
 *
 * @package msrsandbox
 */

$insights_url = msr_publishing_insights_url();
?>
<section class="publishing-commentary">
	<div class="container">
		<?php
		msr_publishing_render_section_header(
			__( 'Commentary &', 'msrsandbox' ),
			__( 'insights.', 'msrsandbox' ),
			__( 'Analysis and editorial posts alongside the resource library.', 'msrsandbox' )
		);
		?>

		<?php
		msr_publishing_render_commentary_topic_filter(
			array(
				'id_prefix'      => 'home',
				'posts_per_page' => 6,
				'all_label'      => __( 'All insights', 'msrsandbox' ),
			)
		);
		?>

		<?php if ( $insights_url ) : ?>
			<p class="text-center mt-4 mb-0">
				<a class="btn btn-outline-primary" href="<?php echo esc_url( $insights_url ); ?>"><?php esc_html_e( 'View all insights', 'msrsandbox' ); ?></a>
			</p>
		<?php endif; ?>
	</div>
</section>
