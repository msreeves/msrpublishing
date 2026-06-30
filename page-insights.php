<?php
/**
 * Commentary & insights landing (slug: insights).
 *
 * @package msrsandbox
 */

get_header();
?>
<main id="site-content" class="publishing-insights-page">
	<div class="container py-4">
		<header class="publishing-section-header text-center mb-4">
			<h1 class="entry-title msr-reveal"><?php esc_html_e( 'Commentary & insights', 'msrsandbox' ); ?></h1>
			<p class="text-muted msr-reveal">
				<?php echo esc_html( msr_publishing_get_insights_hub_intro() ); ?>
			</p>
		</header>

		<?php msr_publishing_render_insights_topic_nav(); ?>
		<?php
		msr_publishing_render_insights_feed(
			array(
				'posts_per_page' => 9,
			)
		);
		?>
	</div>
	<?php msr_publishing_render_subscribe_cta( 'insights' ); ?>
</main>
<?php
get_footer();
