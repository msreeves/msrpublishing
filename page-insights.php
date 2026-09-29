<?php
/**
 * Commentary & insights landing (slug: insights).
 *
 * @package msrpublishing
 */

get_header();
?>
<main id="site-content" class="publishing-insights-page">
	<div class="container py-4">
		<header class="publishing-section-header text-center mb-4">
			<h1 class="entry-title msr-reveal"><?php esc_html_e( 'Commentary & insights', 'msrpublishing' ); ?></h1>
			<p class="text-muted msr-reveal">
				<?php echo esc_html( msr_publishing_get_insights_hub_intro() ); ?>
			</p>
		</header>
	</div>
	<?php msr_publishing_render_commentary_discovery_panel( 'insights' ); ?>
	<div class="container pb-4">
		<?php
		msr_publishing_render_insights_feed( array() );
		?>
	</div>
	<?php msr_publishing_render_subscribe_cta( 'insights' ); ?>
</main>
<?php
get_footer();
