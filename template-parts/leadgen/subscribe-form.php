<?php
/**
 * Demo subscribe form (no ESP — client-side thank-you only).
 *
 * @package msrsandbox
 */
?>
<div class="publishing-subscribe-form card border-0 shadow-sm">
	<div class="card-body p-4">
		<form class="msr-subscribe-demo-form" data-subscribe-demo-form novalidate>
			<div class="mb-3">
				<label class="form-label" for="msr-subscribe-email"><?php esc_html_e( 'Work email', 'msrsandbox' ); ?></label>
				<input type="email" class="form-control" id="msr-subscribe-email" name="email" required autocomplete="email" placeholder="<?php esc_attr_e( 'you@organisation.com', 'msrsandbox' ); ?>" />
			</div>
			<?php msr_publishing_render_subscribe_preferences(); ?>
			<button type="submit" class="btn btn-primary"><?php esc_html_e( 'Subscribe', 'msrsandbox' ); ?></button>
			<p class="form-text small text-muted mt-3 mb-0"><?php esc_html_e( 'Portfolio demonstration only — no data is stored or emailed.', 'msrsandbox' ); ?></p>
		</form>
		<div class="msr-subscribe-thanks d-none mt-3" data-subscribe-thanks role="status">
			<p class="mb-0 fw-semibold"><?php esc_html_e( 'Thank you — demonstration signup complete.', 'msrsandbox' ); ?></p>
			<p class="small text-muted mb-0 d-none" data-subscribe-prefs-note></p>
			<p class="small text-muted mb-0"><?php esc_html_e( 'Connect Mailchimp, ConvertKit, or WPForms on production.', 'msrsandbox' ); ?></p>
		</div>
	</div>
</div>
