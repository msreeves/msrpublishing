<?php
/**
 * Publishing home hero — mission, single CTA, flagship resource visual.
 *
 * @package msrpublishing
 */

$brand   = msr_publishing_brand_name();
$tagline = msr_publishing_brand_tagline();

$resources_url = get_post_type_archive_link( 'resource' );
$insights_url  = msr_publishing_insights_url();

$freshness    = msr_publishing_get_home_freshness_signal();
$editor_voice = msr_publishing_get_home_editor_voice();

$featured_id = (int) msr_publishing_get_site_featured_resource_id();
$has_visual  = $featured_id > 0;

msr_publishing_init_home_surface_media_registry();
msr_publishing_get_home_curated_resource_layout();

$hero_class = 'publishing-home-hero publishing-home-hero--editorial';
if ( $has_visual ) {
	$hero_class .= ' publishing-home-hero--has-visual';
} else {
	$hero_class .= ' publishing-home-hero--no-feature';
}
?>
<section class="<?php echo esc_attr( $hero_class ); ?>" aria-labelledby="publishing-home-hero-title">
	<div class="container py-4 py-lg-5">
		<div class="publishing-home-hero__layout">
			<div class="publishing-home-hero__mission publishing-home-hero__copy">
				<div class="publishing-home-hero__copy-panel msr-reveal-stagger" data-msr-reveal-stagger="hero">
					<p class="publishing-home-hero__eyebrow"><?php esc_html_e( 'Workforce and resilience insights', 'msrpublishing' ); ?></p>
					<h1 id="publishing-home-hero-title" class="publishing-home-hero__title"><?php echo esc_html( $tagline ); ?></h1>
					<p class="publishing-home-hero__purpose text-muted">
						<?php
						echo esc_html(
							sprintf(
								/* translators: %s: site brand name */
								__( '%s is the B2B resource desk for briefings, whitepapers, webinars, and commentary — browse by topic or format in the library.', 'msrpublishing' ),
								$brand
							)
						);
						?>
					</p>
					<ul class="publishing-home-hero__trust list-unstyled mb-0">
						<?php if ( $freshness ) : ?>
							<li class="publishing-home-hero__trust-item">
								<?php esc_html_e( 'Updated', 'msrpublishing' ); ?>
								<time class="publishing-home-hero__trust-date" datetime="<?php echo esc_attr( $freshness['datetime'] ); ?>">
									<?php echo esc_html( wp_date( get_option( 'date_format' ), strtotime( $freshness['datetime'] ) ) ); ?>
								</time>
								<?php if ( ! empty( $freshness['url'] ) ) : ?>
									<span class="publishing-home-hero__trust-sep" aria-hidden="true"> · </span>
									<a class="publishing-home-hero__trust-link" href="<?php echo esc_url( $freshness['url'] ); ?>">
										<?php echo esc_html( wp_trim_words( $freshness['label'], 6, '…' ) ); ?>
									</a>
								<?php endif; ?>
							</li>
						<?php endif; ?>
						<li class="publishing-home-hero__trust-item">
							<?php
							echo esc_html(
								sprintf(
									/* translators: %s: author or editorial desk name */
									__( 'Curated by %s', 'msrpublishing' ),
									$editor_voice
								)
							);
							?>
						</li>
					</ul>
					<?php if ( $resources_url ) : ?>
						<p class="publishing-home-hero__cta d-flex flex-wrap gap-2 align-items-center">
							<a class="btn btn-primary" href="<?php echo esc_url( $resources_url ); ?>"><?php esc_html_e( 'Browse resources', 'msrpublishing' ); ?></a>
							<?php if ( $insights_url ) : ?>
								<a class="publishing-home-hero__secondary-link" href="<?php echo esc_url( $insights_url ); ?>"><?php esc_html_e( 'View insights', 'msrpublishing' ); ?></a>
							<?php endif; ?>
						</p>
					<?php endif; ?>
				</div>
			</div>

			<?php if ( $has_visual ) : ?>
				<?php
				$featured_title = get_the_title( $featured_id );
				$featured_url   = get_permalink( $featured_id );
				$hero_id        = msr_publishing_get_site_featured_resource_hero_id();
				$format         = function_exists( 'msr_publishing_get_primary_resource_type' )
					? msr_publishing_get_primary_resource_type( $featured_id )
					: null;
				$excerpt        = has_excerpt( $featured_id ) ? get_the_excerpt( $featured_id ) : '';
				?>
				<div class="publishing-home-hero__feature">
					<a class="publishing-home-hero__visual" href="<?php echo esc_url( $featured_url ); ?>">
						<span class="publishing-home-hero__visual-scrim" aria-hidden="true"></span>
						<?php if ( $hero_id ) : ?>
							<?php
							echo wp_get_attachment_image(
								$hero_id,
								'large',
								false,
								array(
									'class'   => 'publishing-home-hero__visual-img w-100 h-100 object-fit-cover',
									'loading' => 'eager',
									'alt'     => '',
								)
							);
							?>
						<?php endif; ?>
						<span class="publishing-home-hero__visual-meta">
							<span class="publishing-home-hero__visual-label publishing-home-hero__visual-format">
								<?php esc_html_e( 'Featured resource', 'msrpublishing' ); ?>
							</span>
							<?php if ( $format instanceof WP_Term ) : ?>
								<span class="publishing-home-hero__visual-label publishing-home-hero__visual-format">
									<?php echo esc_html( $format->name ); ?>
								</span>
							<?php endif; ?>
							<span class="publishing-home-hero__visual-title"><?php echo esc_html( $featured_title ); ?></span>
						</span>
					</a>
					<?php if ( $excerpt !== '' ) : ?>
						<p class="publishing-home-hero__feature-excerpt text-muted mb-0"><?php echo esc_html( wp_trim_words( $excerpt, 28, '…' ) ); ?></p>
						<p class="publishing-home-hero__feature-action mb-0">
							<a class="publishing-home-hero__feature-link" href="<?php echo esc_url( $featured_url ); ?>">
								<?php esc_html_e( 'Read the featured briefing', 'msrpublishing' ); ?>
							</a>
						</p>
					<?php endif; ?>
				</div>
			<?php endif; ?>
		</div>
	</div>
</section>
