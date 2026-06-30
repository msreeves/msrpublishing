<?php
/**
 * Privacy notice (demo) — slug: privacy.
 *
 * @package msrsandbox
 */

get_header();
?>
<main id="site-content" class="publishing-privacy-page">
	<div class="container py-5">
		<div class="row justify-content-center">
			<div class="col-lg-8">
				<header class="mb-4">
					<h1 class="entry-title"><?php esc_html_e( 'Privacy notice (demonstration)', 'msrsandbox' ); ?></h1>
					<p class="text-muted"><?php esc_html_e( 'Portfolio placeholder — not legal advice. Replace before production launch.', 'msrsandbox' ); ?></p>
				</header>
				<?php if ( have_posts() ) : ?>
					<?php
					while ( have_posts() ) {
						the_post();
						if ( get_the_content() !== '' ) {
							?>
							<div class="entry-content">
								<?php the_content(); ?>
							</div>
							<?php
						}
					}
					?>
				<?php endif; ?>
				<div class="publishing-privacy-page__demo small text-muted">
					<p><?php esc_html_e( 'Atlas Briefing is a demonstration site. Subscribe forms do not store or transmit personal data. Connect a privacy policy and consent flow when wiring a live ESP.', 'msrsandbox' ); ?></p>
					<p class="mb-0">
						<a href="<?php echo esc_url( msr_publishing_about_url() ); ?>"><?php esc_html_e( 'About Atlas Briefing', 'msrsandbox' ); ?></a>
						<span aria-hidden="true"> · </span>
						<a href="<?php echo esc_url( msr_publishing_subscribe_url() ); ?>"><?php esc_html_e( 'Subscribe', 'msrsandbox' ); ?></a>
					</p>
				</div>
			</div>
		</div>
	</div>
</main>
<?php
get_footer();
