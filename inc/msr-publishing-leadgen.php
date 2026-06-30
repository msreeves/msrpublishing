<?php
/**
 * Lead generation helpers (subscribe page URL, demo forms).
 *
 * @package msrsandbox
 */

/**
 * @return string Permalink or fallback path.
 */
function msr_publishing_subscribe_url() {
	return msr_publishing_get_page_url( 'subscribe', '/subscribe/' );
}

/**
 * @return bool Whether to render a duplicate subscribe band in the footer.
 * @deprecated 2026-06-15 Footer column handles subscribe; band removed to avoid colour clash.
 */
function msr_publishing_show_footer_subscribe_cta() {
	return false;
}

/**
 * Subscribe band on resource library routes (footer CTA suppressed to avoid duplicate H2).
 *
 * @return void
 */
function msr_publishing_render_archive_subscribe_band() {
	if ( ! is_post_type_archive( 'resource' ) && ! is_tax( array( 'resource_type', 'topic' ) ) ) {
		return;
	}
	msr_publishing_render_subscribe_cta( 'archive' );
}

/**
 * Home subscribe band — conversion module above commentary (audit P2).
 *
 * @return void
 */
function msr_publishing_render_home_subscribe_band() {
	if ( ! is_front_page() ) {
		return;
	}
	msr_publishing_render_subscribe_cta( 'home' );
}

/**
 * Subscribe band on resource singles (P26).
 *
 * @return void
 */
function msr_publishing_render_resource_single_subscribe() {
	if ( ! is_singular( 'resource' ) ) {
		return;
	}
	msr_publishing_render_subscribe_cta( 'resource' );
}

/**
 * @return void
 */
function msr_publishing_render_subscribe_cta( $context = 'band' ) {
	$url = msr_publishing_subscribe_url();
	$formats = msr_publishing_get_subscribe_formats_summary();
	/* translators: %s: comma-separated list of resource formats */
	$text = sprintf(
		__( 'Atlas Briefing updates — %s, plus commentary matched to your topic hubs.', 'msrsandbox' ),
		$formats
	);
	if ( 'home' === $context ) {
		$text = __( 'Get workforce and resilience briefings in your inbox — curated resources and commentary from Atlas Briefing.', 'msrsandbox' );
	} elseif ( 'resource' === $context ) {
		$text = __( 'Get the next briefing in your inbox — curated resources and commentary matched to Atlas Briefing readers.', 'msrsandbox' );
	} elseif ( 'about' === $context ) {
		$text = __( 'Follow methodology updates and new formats from Atlas Briefing — briefings, whitepapers, and commentary.', 'msrsandbox' );
	} elseif ( 'topics' === $context ) {
		$text = __( 'Get topic-matched briefings — workforce, resilience, and editorial hubs from Atlas Briefing.', 'msrsandbox' );
	} elseif ( 'archive' === $context ) {
		$text = __( 'New resources and commentary in your inbox — curated from the Atlas Briefing library.', 'msrsandbox' );
	}
	?>
	<section<?php echo 'home' === $context ? ' id="publishing-home-subscribe"' : ''; ?> class="publishing-subscribe-cta publishing-subscribe-cta--<?php echo esc_attr( $context ); ?>" aria-label="<?php esc_attr_e( 'Newsletter signup', 'msrsandbox' ); ?>">
		<div class="container">
			<div class="publishing-subscribe-cta__inner text-center">
				<h2 class="h4 publishing-subscribe-cta__title mb-2"><?php esc_html_e( 'Stay briefed', 'msrsandbox' ); ?></h2>
				<p class="text-muted mb-3 publishing-subscribe-cta__text">
					<?php echo esc_html( $text ); ?>
				</p>
				<?php if ( 'home' === $context ) : ?>
					<div class="publishing-subscribe-cta__form mx-auto">
						<?php get_template_part( 'template-parts/leadgen/subscribe', 'inline' ); ?>
					</div>
				<?php else : ?>
					<a class="btn btn-primary publishing-subscribe-cta__link" href="<?php echo esc_url( $url ); ?>">
						<?php esc_html_e( 'Subscribe to Atlas Briefing', 'msrsandbox' ); ?>
					</a>
				<?php endif; ?>
			</div>
		</div>
	</section>
	<?php
}
