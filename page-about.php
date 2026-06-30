<?php
/**
 * About / methodology page (slug: about).
 *
 * @package msrsandbox
 */

get_header();

$resources     = get_post_type_archive_link( 'resource' );
$insights      = msr_publishing_insights_url();
$subscribe     = msr_publishing_subscribe_url();
$lead          = msr_publishing_get_about_page_lead();
$page_content  = '';
$bullets       = msr_publishing_get_about_methodology_bullets();
$formats       = msr_publishing_get_about_format_cards();

if ( have_posts() ) {
	while ( have_posts() ) {
		the_post();
		if ( has_excerpt() ) {
			$lead = wp_strip_all_tags( get_the_excerpt() );
		}
		if ( get_the_content() !== '' ) {
			ob_start();
			the_content();
			$page_content = ob_get_clean();
		}
	}
}
?>
<main id="site-content" class="publishing-about-page">
	<div class="container py-5">
		<header class="publishing-section-header text-center mb-5">
			<h1 class="entry-title msr-reveal"><?php esc_html_e( 'About Atlas Briefing', 'msrsandbox' ); ?></h1>
			<p class="lead text-muted msr-reveal mb-0"><?php echo esc_html( $lead ); ?></p>
		</header>

		<div class="publishing-about-page__body">
				<?php if ( $page_content !== '' ) : ?>
				<div class="entry-content publishing-about-page__intro mb-5">
					<?php echo $page_content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- filtered post content. ?>
				</div>
				<?php endif; ?>

				<section class="publishing-about-page__methodology mb-5" aria-labelledby="about-methodology-heading">
					<h2 id="about-methodology-heading" class="h3 mb-3"><?php esc_html_e( 'Methodology', 'msrsandbox' ); ?></h2>
					<p class="text-muted"><?php echo esc_html( msr_publishing_get_about_methodology_intro() ); ?></p>
					<?php if ( ! empty( $bullets ) ) : ?>
					<ul class="publishing-about-page__standards">
						<?php foreach ( $bullets as $bullet ) : ?>
							<li><?php echo esc_html( $bullet ); ?></li>
						<?php endforeach; ?>
					</ul>
					<?php endif; ?>
				</section>

				<section class="publishing-about-page__formats mb-5" aria-labelledby="about-formats-heading">
					<h2 id="about-formats-heading" class="h3 mb-3"><?php esc_html_e( 'What we publish', 'msrsandbox' ); ?></h2>
					<div class="row g-3">
						<?php foreach ( $formats as $format ) : ?>
						<div class="col-md-6">
							<div class="publishing-about-page__format-card h-100">
								<h3 class="h6 mb-2"><?php echo esc_html( $format['title'] ); ?></h3>
								<p class="small text-muted mb-0"><?php echo esc_html( $format['text'] ); ?></p>
							</div>
						</div>
						<?php endforeach; ?>
					</div>
				</section>

				<?php msr_publishing_render_about_experts_grid(); ?>

				<?php if ( msr_publishing_show_about_demo_notice() ) : ?>
				<section class="publishing-about-page__disclaimer mb-5" aria-labelledby="about-disclaimer-heading">
					<h2 id="about-disclaimer-heading" class="h3 mb-3"><?php esc_html_e( 'Demonstration notice', 'msrsandbox' ); ?></h2>
					<p class="text-muted mb-0"><?php echo esc_html( msr_publishing_get_about_disclaimer() ); ?></p>
				</section>
				<?php endif; ?>

				<nav class="publishing-about-page__actions d-flex flex-wrap gap-2 justify-content-center" aria-label="<?php esc_attr_e( 'Explore Atlas Briefing', 'msrsandbox' ); ?>">
					<?php if ( $resources ) : ?>
						<a class="btn btn-primary" href="<?php echo esc_url( $resources ); ?>"><?php esc_html_e( 'Browse resources', 'msrsandbox' ); ?></a>
					<?php endif; ?>
					<a class="btn btn-outline-primary" href="<?php echo esc_url( $insights ); ?>"><?php esc_html_e( 'View insights', 'msrsandbox' ); ?></a>
					<a class="btn btn-outline-primary" href="<?php echo esc_url( $subscribe ); ?>"><?php esc_html_e( 'Subscribe', 'msrsandbox' ); ?></a>
				</nav>
		</div>
	</div>
	<?php msr_publishing_render_subscribe_cta( 'about' ); ?>
</main>
<?php
get_footer();
