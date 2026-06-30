<?php
/**
 * Subscribe CTA (split from site-header-actions for programme-health P14-03).
 *
 * @package msrsandbox
 */

$subscribe_url = msr_publishing_subscribe_url();
if ( ! $subscribe_url ) {
	return;
}
?>
<a class="btn btn-sm btn-primary site-header__subscribe d-none d-md-inline-flex align-items-center" href="<?php echo esc_url( $subscribe_url ); ?>">
	<?php esc_html_e( 'Subscribe', 'msrsandbox' ); ?>
</a>
