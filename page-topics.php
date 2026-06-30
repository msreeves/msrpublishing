<?php
/**
 * Topics hub (slug: topics) — browse Workforce, Resilience, and other briefing topics.
 *
 * @package msrsandbox
 */

get_header();
?>
<main id="site-content" class="publishing-topics-page">
	<div class="container py-4">
		<header class="publishing-section-header text-center mb-4">
			<h1 class="entry-title msr-reveal"><?php esc_html_e( 'Topics', 'msrsandbox' ); ?></h1>
			<p class="text-muted msr-reveal">
				<?php echo esc_html( msr_publishing_get_topics_hub_intro() ); ?>
			</p>
		</header>
		<?php msr_publishing_render_topics_hub(); ?>
	</div>
	<?php msr_publishing_render_subscribe_cta( 'topics' ); ?>
</main>
<?php
get_footer();
