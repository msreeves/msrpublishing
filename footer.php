<?php
/**
 * The template for displaying the footer
 *
 * @package msrsandbox
 */

?>

	<?php
	if ( msr_publishing_show_leaderboard_ads() ) {
		get_template_part( 'templates/partials/leaderboard/footer' );
	}
	?>

	<footer id="colophon" class="site-footer publishing-site-footer">
		<?php msr_publishing_render_site_footer(); ?>
		<?php msr_publishing_render_footer_social(); ?>
		<?php if ( msr_publishing_show_footer_demo_note() ) : ?>
		<p class="site-footer__demo-note container text-center small mb-0 pb-3">
			<?php echo esc_html( msr_publishing_get_footer_demo_note() ); ?>
		</p>
		<?php endif; ?>
	</footer><!-- #colophon -->
<?php wp_footer(); ?>
</body>
</html>
