<?php
/**
 * Publishing home — commentary feed (URL discovery; no in-page tabs).
 *
 * @package msrpublishing
 */

$insights_url = msr_publishing_insights_url();
?>
<section class="publishing-commentary publishing-home-commentary-preview">
	<div class="container">
		<?php
		msr_publishing_render_section_header(
			__( 'Commentary &', 'msrpublishing' ),
			__( 'insights.', 'msrpublishing' ),
			__( 'Analysis and editorial posts alongside the resource library.', 'msrpublishing' )
		);
		?>
	</div>
	<?php if ( $insights_url && function_exists( 'msr_publishing_render_commentary_discovery_panel' ) ) : ?>
		<?php msr_publishing_render_commentary_discovery_panel( 'insights' ); ?>
	<?php endif; ?>
	<div class="container">
		<?php
		msr_publishing_render_commentary_post_grid(
			new WP_Query( msr_publishing_get_commentary_query_args( 0, 6 ) ),
			true
		);
		?>

		<?php if ( $insights_url ) : ?>
			<p class="text-center mt-4 mb-0">
				<a class="btn btn-outline-primary" href="<?php echo esc_url( $insights_url ); ?>"><?php esc_html_e( 'View all insights', 'msrpublishing' ); ?></a>
			</p>
		<?php endif; ?>
	</div>
</section>
