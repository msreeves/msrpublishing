<?php
/**
 * 404 — publishing chrome with helpful links.
 *
 * @package msrsandbox
 */

get_header();

$resources = get_post_type_archive_link( 'resource' );
$insights  = msr_publishing_insights_url();
$home      = home_url( '/' );
?>
<main id="site-content" class="publishing-error-page">
	<div class="container py-5 text-center">
		<p class="publishing-error-page__code display-1 mb-2" aria-hidden="true">404</p>
		<h1 class="h2 mb-3"><?php esc_html_e( 'Page not found', 'msrsandbox' ); ?></h1>
		<p class="text-muted mb-4 publishing-error-page__lead">
			<?php esc_html_e( 'That URL is not part of the Atlas Briefing demonstration site, or it may have moved.', 'msrsandbox' ); ?>
		</p>
		<nav class="d-flex flex-wrap gap-2 justify-content-center" aria-label="<?php esc_attr_e( 'Helpful links', 'msrsandbox' ); ?>">
			<a class="btn btn-primary" href="<?php echo esc_url( $home ); ?>"><?php esc_html_e( 'Home', 'msrsandbox' ); ?></a>
			<?php if ( $resources ) : ?>
				<a class="btn btn-outline-primary" href="<?php echo esc_url( $resources ); ?>"><?php esc_html_e( 'Resources', 'msrsandbox' ); ?></a>
			<?php endif; ?>
			<a class="btn btn-outline-primary" href="<?php echo esc_url( $insights ); ?>"><?php esc_html_e( 'Insights', 'msrsandbox' ); ?></a>
			<a class="btn btn-outline-primary" href="<?php echo esc_url( home_url( '/?s=' ) ); ?>"><?php esc_html_e( 'Search', 'msrsandbox' ); ?></a>
		</nav>
	</div>
</main>
<?php
get_footer();
