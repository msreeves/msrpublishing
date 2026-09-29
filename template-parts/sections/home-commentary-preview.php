<?php
/**
 * Publishing home — latest commentary preview (no in-page tabs).
 *
 * @package msrpublishing
 */

$insights_url = msr_publishing_insights_url();
?>
<section class="publishing-home-commentary-preview" aria-labelledby="publishing-home-commentary-heading">
	<div class="container">
		<?php
		msr_publishing_render_section_header(
			__( 'Latest', 'msrpublishing' ),
			__( 'insights.', 'msrpublishing' ),
			__( 'Fresh analysis and editorial posts from Atlas Briefing.', 'msrpublishing' ),
			'publishing-home-commentary-heading'
		);
		?>

		<?php
		$commentary_ids = msr_publishing_get_commentary_ids_for_surface(
			msr_publishing_get_home_commentary_count()
		);
		msr_publishing_render_commentary_post_grid_from_ids( $commentary_ids, true );
		?>

		<?php if ( $insights_url ) : ?>
			<p class="text-center mt-4 mb-0">
				<a class="btn btn-outline-primary" href="<?php echo esc_url( $insights_url ); ?>"><?php esc_html_e( 'View all insights', 'msrpublishing' ); ?></a>
			</p>
		<?php endif; ?>
	</div>
</section>
