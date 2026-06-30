<?php
/**
 * Compact inline subscribe form for home conversion band.
 *
 * @package msrsandbox
 */
?>
<form class="publishing-subscribe-inline msr-subscribe-demo-form" data-subscribe-demo-form novalidate>
	<label class="visually-hidden" for="msr-home-subscribe-email"><?php esc_html_e( 'Work email', 'msrsandbox' ); ?></label>
	<div class="publishing-subscribe-inline__row">
		<input
			type="email"
			class="form-control publishing-subscribe-inline__input"
			id="msr-home-subscribe-email"
			name="email"
			required
			autocomplete="email"
			placeholder="<?php esc_attr_e( 'Work email', 'msrsandbox' ); ?>"
		/>
		<button type="submit" class="btn btn-primary publishing-subscribe-inline__submit"><?php esc_html_e( 'Subscribe', 'msrsandbox' ); ?></button>
	</div>
	<p class="publishing-subscribe-inline__note small text-muted mb-0 mt-2"><?php esc_html_e( 'Curated briefings — demo signup only, no data stored.', 'msrsandbox' ); ?></p>
</form>
<div class="msr-subscribe-thanks d-none mt-3" data-subscribe-thanks role="status">
	<p class="mb-0 fw-semibold"><?php esc_html_e( 'Thank you — you are on the list.', 'msrsandbox' ); ?></p>
</div>
