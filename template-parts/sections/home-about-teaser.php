<?php
/**
 * Home — About Atlas Briefing one-liner band.
 *
 * @package msrsandbox
 */

$about_url = msr_publishing_about_url();
if ( ! $about_url ) {
	return;
}
?>
<section class="publishing-home-about-teaser msr-reveal msr-reveal--up" aria-labelledby="publishing-home-about-teaser-heading">
	<div class="container text-center">
		<h2 id="publishing-home-about-teaser-heading" class="visually-hidden"><?php esc_html_e( 'About Atlas Briefing', 'msrsandbox' ); ?></h2>
		<p class="publishing-home-about-teaser__lead mb-3">
			<?php echo esc_html( msr_publishing_get_home_about_teaser_lead() ); ?>
		</p>
		<a class="btn btn-outline-primary btn-sm" href="<?php echo esc_url( $about_url ); ?>">
			<?php esc_html_e( 'About Atlas Briefing', 'msrsandbox' ); ?>
		</a>
	</div>
</section>
