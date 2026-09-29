<?php
/**
 * Lower home bands — optional depth (collapsed by default on all viewports).
 *
 * @package msrpublishing
 */
?>
<details class="publishing-home-more" id="publishing-home-more">
	<summary class="publishing-home-more__toggle">
		<span class="publishing-home-more__toggle-label"><?php esc_html_e( 'More from Atlas Briefing', 'msrpublishing' ); ?></span>
		<span class="publishing-home-more__toggle-hint"><?php esc_html_e( 'Spotlights, methodology, and portfolio notes', 'msrpublishing' ); ?></span>
	</summary>
	<div class="publishing-home-more__content">
		<?php
		msr_publishing_render_home_promo_band();
		msr_publishing_render_home_about_teaser();
		msr_publishing_render_home_standards_callout();
		?>
	</div>
</details>
