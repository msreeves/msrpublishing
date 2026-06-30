<?php
/**
 * Whitepaper gated download demo form.
 *
 * @package msrsandbox
 */

$subscribe_url = msr_publishing_subscribe_url();
$pdf_url       = isset( $args['pdf_url'] ) ? (string) $args['pdf_url'] : '';
?>
<div class="resource-single__gate alert alert-light border mb-4" role="region" aria-label="<?php esc_attr_e( 'Gated download', 'msrsandbox' ); ?>" data-gate-panel>
	<h2 class="h5"><?php esc_html_e( 'Gated download', 'msrsandbox' ); ?></h2>
	<p class="mb-3 small text-muted"><?php esc_html_e( 'Enter your email to unlock this demonstration whitepaper.', 'msrsandbox' ); ?></p>

	<form class="msr-gate-form" data-gate-form novalidate>
		<div class="row g-2 align-items-end">
			<div class="col-md-8">
				<label class="form-label" for="msr-gate-email"><?php esc_html_e( 'Work email', 'msrsandbox' ); ?></label>
				<input type="email" class="form-control" id="msr-gate-email" name="email" required autocomplete="email" />
			</div>
			<div class="col-md-4">
				<button type="submit" class="btn btn-primary w-100"><?php esc_html_e( 'Request the PDF', 'msrsandbox' ); ?></button>
			</div>
		</div>
	</form>

	<div class="msr-gate-unlocked d-none mt-4" data-gate-unlocked role="status">
		<p class="fw-semibold mb-2"><?php esc_html_e( 'Thank you — your download is ready.', 'msrsandbox' ); ?></p>
		<?php if ( $pdf_url !== '' ) : ?>
			<p class="resource-single__download mb-3">
				<a class="btn btn-primary" href="<?php echo esc_url( $pdf_url ); ?>" download>
					<i class="fa-solid fa-file-pdf me-1" aria-hidden="true"></i>
					<?php esc_html_e( 'Download PDF', 'msrsandbox' ); ?>
				</a>
			</p>
		<?php endif; ?>
		<p class="small text-muted mb-0">
			<?php esc_html_e( 'Want ongoing updates?', 'msrsandbox' ); ?>
			<a href="<?php echo esc_url( $subscribe_url ); ?>"><?php esc_html_e( 'Subscribe to Atlas Briefing', 'msrsandbox' ); ?></a>
		</p>
	</div>
</div>
