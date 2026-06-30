<?php
/**
 * Publishing home — latest commentary preview (no in-page tabs).
 *
 * @package msrsandbox
 */

$insights_url = msr_publishing_insights_url();
?>
<section class="publishing-home-commentary-preview" aria-labelledby="publishing-home-commentary-heading">
	<div class="container">
		<?php
		msr_publishing_render_section_header(
			__( 'Latest', 'msrsandbox' ),
			__( 'insights.', 'msrsandbox' ),
			__( 'Fresh analysis and editorial posts from Atlas Briefing.', 'msrsandbox' ),
			'publishing-home-commentary-heading'
		);
		?>

		<?php
		msr_publishing_render_commentary_post_grid(
			new WP_Query( msr_publishing_get_commentary_query_args( 0, 3 ) ),
			true
		);
		?>

		<?php if ( $insights_url ) : ?>
			<p class="text-center mt-4 mb-0">
				<a class="btn btn-outline-primary" href="<?php echo esc_url( $insights_url ); ?>"><?php esc_html_e( 'View all insights', 'msrsandbox' ); ?></a>
			</p>
		<?php endif; ?>
	</div>
</section>
