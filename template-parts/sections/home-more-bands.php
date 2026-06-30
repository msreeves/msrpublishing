<?php
/**
 * Lower home bands — collapsed on narrow viewports (P51 mobile density).
 *
 * @package msrsandbox
 */
?>
<details class="publishing-home-more" id="publishing-home-more">
	<summary class="publishing-home-more__toggle">
		<span class="publishing-home-more__toggle-label"><?php esc_html_e( 'More from Atlas Briefing', 'msrsandbox' ); ?></span>
		<span class="publishing-home-more__toggle-hint"><?php esc_html_e( 'Spotlight and methodology', 'msrsandbox' ); ?></span>
	</summary>
	<div class="publishing-home-more__content">
		<?php
		msr_publishing_render_home_promo_band();
		msr_publishing_render_home_about_teaser();
		?>
	</div>
</details>
