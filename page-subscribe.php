<?php
/**
 * Subscribe landing page template (slug: subscribe).
 *
 * @package msrsandbox
 */

get_header();

$resources = get_post_type_archive_link( 'resource' );
$insights  = msr_publishing_insights_url();
?>
<main id="site-content" class="publishing-subscribe-page">
	<div class="container py-5">
		<header class="text-center mb-4">
			<h1 class="entry-title"><?php the_title(); ?></h1>
			<?php if ( has_excerpt() ) : ?>
				<p class="text-muted publishing-subscribe-page__lead"><?php echo esc_html( wp_strip_all_tags( get_the_excerpt() ) ); ?></p>
			<?php else : ?>
				<p class="text-muted publishing-subscribe-page__lead">
					<?php echo esc_html( msr_publishing_get_subscribe_page_lead() ); ?>
				</p>
			<?php endif; ?>
		</header>

		<section class="publishing-subscribe-value-prop mb-5" aria-labelledby="subscribe-value-heading">
			<div class="row justify-content-center">
				<div class="col-lg-8">
					<h2 id="subscribe-value-heading" class="h4 text-center mb-4"><?php esc_html_e( 'What you receive', 'msrsandbox' ); ?></h2>
					<div class="row g-3">
						<div class="col-md-6">
							<div class="publishing-subscribe-value-prop__item h-100">
								<h3 class="h6 mb-2"><?php esc_html_e( 'Workforce & resilience briefings', 'msrsandbox' ); ?></h3>
								<p class="small text-muted mb-0"><?php esc_html_e( 'Topic hubs curate resources and commentary for hiring, planning, and distributed operations.', 'msrsandbox' ); ?></p>
							</div>
						</div>
						<div class="col-md-6">
							<div class="publishing-subscribe-value-prop__item h-100">
								<h3 class="h6 mb-2"><?php esc_html_e( 'All resource formats', 'msrsandbox' ); ?></h3>
								<p class="small text-muted mb-0">
									<?php
									printf(
										/* translators: %s: comma-separated list of resource formats */
										esc_html__( 'Gated downloads, replays, and singles across %s — each with topic hubs and programme cross-links.', 'msrsandbox' ),
										esc_html( msr_publishing_get_subscribe_formats_summary() )
									);
									?>
								</p>
							</div>
						</div>
						<div class="col-md-6">
							<div class="publishing-subscribe-value-prop__item h-100">
								<h3 class="h6 mb-2"><?php esc_html_e( 'Commentary & insights', 'msrsandbox' ); ?></h3>
								<p class="small text-muted mb-0"><?php esc_html_e( 'Editorial analysis cross-linked to the resource library on every topic.', 'msrsandbox' ); ?></p>
							</div>
						</div>
						<div class="col-md-6">
							<div class="publishing-subscribe-value-prop__item h-100">
								<h3 class="h6 mb-2"><?php esc_html_e( 'MSR programme network', 'msrsandbox' ); ?></h3>
								<p class="small text-muted mb-0"><?php esc_html_e( 'Occasional previews connecting Atlas Briefing to Events, Awards, and Seminars demonstrations.', 'msrsandbox' ); ?></p>
							</div>
						</div>
					</div>
					<?php if ( $resources || $insights ) : ?>
						<p class="text-center small text-muted mt-4 mb-0 publishing-subscribe-value-prop__explore">
							<?php esc_html_e( 'Explore before you subscribe:', 'msrsandbox' ); ?>
							<?php if ( $resources ) : ?>
								<a href="<?php echo esc_url( $resources ); ?>"><?php esc_html_e( 'Resource library', 'msrsandbox' ); ?></a>
							<?php endif; ?>
							<?php if ( $resources && $insights ) : ?>
								<span aria-hidden="true"> · </span>
							<?php endif; ?>
							<?php if ( $insights ) : ?>
								<a href="<?php echo esc_url( $insights ); ?>"><?php esc_html_e( 'Insights', 'msrsandbox' ); ?></a>
							<?php endif; ?>
						</p>
					<?php endif; ?>
				</div>
			</div>
		</section>

		<div class="row justify-content-center">
			<div class="col-lg-7">
				<?php get_template_part( 'template-parts/leadgen/subscribe', 'form' ); ?>
				<?php if ( get_the_content() !== '' ) : ?>
					<div class="entry-content mt-4 small text-muted">
						<?php the_content(); ?>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
</main>
<?php
get_footer();
