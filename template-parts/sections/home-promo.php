<?php
/**
 * Home editorial promo — billboard advert as split CTA band.
 *
 * @package msrpublishing
 */

$adverts = isset( $args['adverts'] ) && is_array( $args['adverts'] ) ? $args['adverts'] : array();
if ( ! $adverts ) {
	return;
}

$slides = array();
foreach ( $adverts as $advert ) {
	if ( ! $advert instanceof WP_Post ) {
		continue;
	}

	$link = function_exists( 'get_field' ) ? get_field( 'link', $advert->ID ) : null;
	if ( ! is_array( $link ) || empty( $link['url'] ) ) {
		continue;
	}

	$thumb_id = (int) get_post_thumbnail_id( $advert->ID );
	if ( ! $thumb_id ) {
		continue;
	}

	$body = trim( wp_strip_all_tags( get_the_excerpt( $advert->ID ) ) );
	if ( '' === $body || preg_match( '/\b(demonstration sponsor|lorem)\b/i', $body ) ) {
		$body = __( 'Explore connected programmes, resources, and editorial spotlights from the MSR portfolio.', 'msrpublishing' );
	} elseif ( strlen( $body ) > 160 ) {
		$body = wp_trim_words( $body, 24, '…' );
	}

	$slides[] = array(
		'id'      => (int) $advert->ID,
		'title'   => msr_publishing_get_advert_promo_title( $advert, $link ),
		'eyebrow' => msr_publishing_get_advert_promo_eyebrow( $advert ),
		'url'     => (string) $link['url'],
		'target'  => ! empty( $link['target'] ) ? (string) $link['target'] : '_blank',
		'thumb'   => $thumb_id,
		'body'    => $body,
	);
}

if ( ! $slides ) {
	return;
}

$multi        = count( $slides ) > 1;
$carousel_id  = 'publishing-home-promo-carousel';
$carousel_cls = 'publishing-home-promo__carousel';
if ( $multi ) {
	$carousel_cls .= ' carousel slide publishing-home-promo__carousel--multi';
}
?>
<section class="publishing-home-promo msr-reveal msr-reveal--up" aria-labelledby="publishing-home-promo-heading">
	<div class="container">
		<div
			<?php echo $multi ? 'id="' . esc_attr( $carousel_id ) . '"' : ''; ?>
			class="<?php echo esc_attr( $carousel_cls ); ?>"
			<?php echo $multi ? ' data-bs-ride="carousel" data-bs-interval="7000"' : ''; ?>
			<?php echo $multi ? ' role="region" aria-roledescription="carousel" aria-label="' . esc_attr__( 'Sponsored spotlights', 'msrpublishing' ) . '"' : ''; ?>
		>
			<?php if ( $multi ) : ?>
				<div class="carousel-inner">
			<?php endif; ?>
			<?php foreach ( $slides as $index => $slide ) : ?>
				<?php
				$item_class = 'publishing-home-promo__slide';
				if ( $multi ) {
					$item_class .= 0 === $index ? ' carousel-item active' : ' carousel-item';
				}
				?>
				<article class="<?php echo esc_attr( $item_class ); ?>">
					<div class="publishing-home-promo__grid">
						<a class="publishing-home-promo__media" href="<?php echo esc_url( $slide['url'] ); ?>" target="<?php echo esc_attr( $slide['target'] ); ?>" rel="noopener noreferrer" aria-label="<?php echo esc_attr( $slide['title'] ); ?>">
							<span class="publishing-home-promo__media-frame">
								<?php
								echo wp_get_attachment_image(
									$slide['thumb'],
									'large',
									false,
									array(
										'class'   => 'publishing-home-promo__img',
										'loading' => 0 === $index ? 'eager' : 'lazy',
										'alt'     => $slide['title'],
									)
								);
								?>
							</span>
						</a>
						<div class="publishing-home-promo__body">
							<p id="<?php echo 0 === $index ? 'publishing-home-promo-heading' : ''; ?>" class="publishing-home-promo__eyebrow">
								<span class="publishing-home-promo__eyebrow-text"><?php echo esc_html( $slide['eyebrow'] ); ?></span>
								<span class="publishing-home-promo__sponsored"><?php esc_html_e( 'Sponsored', 'msrpublishing' ); ?></span>
							</p>
							<h3 class="publishing-home-promo__title h3"><?php echo esc_html( $slide['title'] ); ?></h3>
							<p class="publishing-home-promo__text text-muted mb-0">
								<?php echo esc_html( $slide['body'] ); ?>
							</p>
							<a class="btn btn-primary publishing-home-promo__cta align-self-start" href="<?php echo esc_url( $slide['url'] ); ?>" target="<?php echo esc_attr( $slide['target'] ); ?>" rel="noopener noreferrer">
								<?php esc_html_e( 'View spotlight', 'msrpublishing' ); ?>
							</a>
						</div>
					</div>
				</article>
			<?php endforeach; ?>
			<?php if ( $multi ) : ?>
				</div>
				<button class="carousel-control-prev publishing-home-promo__control" type="button" data-bs-target="#<?php echo esc_attr( $carousel_id ); ?>" data-bs-slide="prev">
					<span class="carousel-control-prev-icon" aria-hidden="true"></span>
					<span class="visually-hidden"><?php esc_html_e( 'Previous spotlight', 'msrpublishing' ); ?></span>
				</button>
				<button class="carousel-control-next publishing-home-promo__control" type="button" data-bs-target="#<?php echo esc_attr( $carousel_id ); ?>" data-bs-slide="next">
					<span class="carousel-control-next-icon" aria-hidden="true"></span>
					<span class="visually-hidden"><?php esc_html_e( 'Next spotlight', 'msrpublishing' ); ?></span>
				</button>
			<?php endif; ?>
		</div>
	</div>
</section>
