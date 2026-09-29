<?php
/**
 * Home standards callout — portfolio current-standard WordPress exemplar.
 *
 * @package msrpublishing
 */

$text = isset( $args['text'] ) ? trim( (string) $args['text'] ) : '';
if ( $text === '' ) {
	return;
}
?>
<section id="publishing-home-standards" class="publishing-home-standards msr-reveal msr-reveal--up" aria-labelledby="publishing-home-standards-heading">
	<div class="container">
		<div class="publishing-home-standards__inner">
			<p id="publishing-home-standards-heading" class="publishing-home-standards__eyebrow"><?php esc_html_e( 'Current WordPress standard', 'msrpublishing' ); ?></p>
			<p class="publishing-home-standards__body"><?php echo esc_html( $text ); ?></p>
		</div>
	</div>
</section>
