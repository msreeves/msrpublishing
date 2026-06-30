<?php
/**
 * Publishing home hero — Atlas Briefing.
 *
 * @package msrsandbox
 */

$brand   = msr_publishing_brand_name();
$tagline = msr_publishing_brand_tagline();

$resources_url = get_post_type_archive_link( 'resource' );
$insights_url  = msr_publishing_insights_url();

$featured_id    = msr_publishing_get_site_featured_resource_id();
$featured_hero  = msr_publishing_get_site_featured_resource_hero_id();
$featured_title = $featured_id ? get_the_title( $featured_id ) : '';
$featured_href  = $featured_id ? get_permalink( $featured_id ) : '';
$featured_type  = $featured_id ? msr_publishing_get_primary_resource_type( $featured_id ) : null;
$has_visual     = $featured_id && $featured_href;
$freshness      = msr_publishing_get_home_freshness_signal();
$editor_voice   = msr_publishing_get_home_editor_voice();

$hero_class = 'publishing-home-hero';
if ( $has_visual ) {
	$hero_class .= ' publishing-home-hero--has-visual';
}
?>
<section class="<?php echo esc_attr( $hero_class ); ?>">
	<div class="container py-5">
		<div class="row align-items-center g-4 g-lg-5 justify-content-center">
			<div class="<?php echo $has_visual ? 'col-lg-6 text-center text-lg-start' : 'col-12 col-lg-8 text-center'; ?>">
				<div class="publishing-home-hero__copy-panel msr-reveal-stagger" data-msr-reveal-stagger="hero">
					<p class="publishing-home-hero__eyebrow"><?php esc_html_e( 'Workforce and resilience insights', 'msrsandbox' ); ?></p>
					<h1 class="publishing-home-hero__title"><?php echo esc_html( $brand ); ?></h1>
					<p class="publishing-home-hero__lead"><?php echo esc_html( $tagline ); ?></p>
					<ul class="publishing-home-hero__trust list-unstyled mb-0">
						<?php if ( $freshness ) : ?>
							<li class="publishing-home-hero__trust-item">
								<?php esc_html_e( 'Updated', 'msrsandbox' ); ?>
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
									__( 'Curated by %s', 'msrsandbox' ),
									$editor_voice
								)
							);
							?>
						</li>
					</ul>
					<?php if ( $resources_url || $insights_url ) : ?>
						<p class="publishing-home-hero__cta d-flex flex-wrap gap-2 justify-content-center<?php echo $has_visual ? ' justify-content-lg-start' : ''; ?>">
							<?php if ( $resources_url ) : ?>
								<a class="btn btn-primary" href="<?php echo esc_url( $resources_url ); ?>"><?php esc_html_e( 'Browse resources', 'msrsandbox' ); ?></a>
							<?php endif; ?>
							<?php if ( $insights_url ) : ?>
								<a class="btn btn-outline-primary" href="<?php echo esc_url( $insights_url ); ?>"><?php esc_html_e( 'View insights', 'msrsandbox' ); ?></a>
							<?php endif; ?>
						</p>
					<?php endif; ?>
				</div>
			</div>
			<?php if ( $has_visual ) : ?>
				<div class="col-lg-6">
					<a class="publishing-home-hero__visual msr-card-media msr-reveal msr-reveal--up d-block<?php echo $featured_hero ? '' : ' msr-card-media--placeholder'; ?>" href="<?php echo esc_url( $featured_href ); ?>" aria-label="<?php echo esc_attr( sprintf( /* translators: %s: resource title */ __( 'Featured pick: %s', 'msrsandbox' ), $featured_title ) ); ?>">
						<?php if ( $featured_hero ) : ?>
							<?php
							echo wp_get_attachment_image(
								$featured_hero,
								'large',
								false,
								array(
									'class'   => 'publishing-home-hero__visual-img msr-card-media__img',
									'loading' => 'eager',
									'alt'     => $featured_title,
								)
							);
							?>
						<?php else : ?>
							<span class="msr-card-media__placeholder" aria-hidden="true">
								<i class="fa-solid <?php echo esc_attr( msr_publishing_get_card_placeholder_icon( $featured_id ) ); ?>"></i>
							</span>
						<?php endif; ?>
						<span class="publishing-home-hero__visual-scrim" aria-hidden="true"></span>
						<span class="publishing-home-hero__visual-meta">
							<span class="publishing-home-hero__visual-label small text-uppercase fw-semibold"><?php esc_html_e( 'Featured pick', 'msrsandbox' ); ?></span>
							<?php if ( $featured_type ) : ?>
								<span class="publishing-home-hero__visual-format badge"><?php echo esc_html( $featured_type->name ); ?></span>
							<?php endif; ?>
							<span class="publishing-home-hero__visual-title"><?php echo esc_html( $featured_title ); ?></span>
						</span>
					</a>
				</div>
			<?php endif; ?>
		</div>
	</div>
</section>
