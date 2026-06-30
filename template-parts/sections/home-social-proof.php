<?php
/**
 * Home social proof — stats strip + programme logo row.
 *
 * @package msrsandbox
 *
 * @var array $args {
 *     @type string $eyebrow Eyebrow label.
 *     @type array  $stats   Stat rows.
 *     @type array  $logos   Logo rows.
 * }
 */

$eyebrow = isset( $args['eyebrow'] ) ? trim( (string) $args['eyebrow'] ) : '';
$stats   = isset( $args['stats'] ) && is_array( $args['stats'] ) ? $args['stats'] : array();
$logos   = isset( $args['logos'] ) && is_array( $args['logos'] ) ? $args['logos'] : array();

if ( ! $stats && ! $logos ) {
	return;
}
?>
<section class="publishing-home-social-proof msr-reveal" aria-labelledby="publishing-home-social-proof-heading">
	<div class="container">
		<?php if ( $eyebrow !== '' ) : ?>
			<p id="publishing-home-social-proof-heading" class="publishing-home-social-proof__eyebrow mb-0"><?php echo esc_html( $eyebrow ); ?></p>
		<?php endif; ?>

		<?php if ( $stats ) : ?>
			<ul class="publishing-home-social-proof__stats list-unstyled mb-0" aria-label="<?php esc_attr_e( 'Atlas Briefing at a glance', 'msrsandbox' ); ?>">
				<?php foreach ( $stats as $stat ) : ?>
					<?php
					if ( empty( $stat['value'] ) || empty( $stat['label'] ) ) {
						continue;
					}
					?>
					<li class="publishing-home-social-proof__stat">
						<p class="publishing-home-social-proof__stat-value mb-0"><?php echo esc_html( (string) $stat['value'] ); ?></p>
						<p class="publishing-home-social-proof__stat-label mb-0"><?php echo esc_html( (string) $stat['label'] ); ?></p>
					</li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>

		<?php if ( $logos ) : ?>
			<ul class="publishing-home-social-proof__logos list-unstyled mb-0" aria-label="<?php esc_attr_e( 'Connected MSR programmes', 'msrsandbox' ); ?>">
				<?php foreach ( $logos as $logo ) : ?>
					<?php
					$name    = isset( $logo['name'] ) ? trim( (string) $logo['name'] ) : '';
					$url     = isset( $logo['url'] ) ? trim( (string) $logo['url'] ) : '';
					$logo_id = isset( $logo['logo_id'] ) ? (int) $logo['logo_id'] : 0;
					if ( $name === '' ) {
						continue;
					}
					?>
					<li class="publishing-home-social-proof__logo-item">
						<?php if ( $url !== '' ) : ?>
							<a class="publishing-home-social-proof__logo-link" href="<?php echo esc_url( $url ); ?>">
						<?php else : ?>
							<span class="publishing-home-social-proof__logo-link">
						<?php endif; ?>
							<?php if ( $logo_id ) : ?>
								<?php
								echo wp_get_attachment_image(
									$logo_id,
									'medium',
									false,
									array(
										'class' => 'publishing-home-social-proof__logo-img',
										'alt'   => $name,
									)
								);
								?>
							<?php else : ?>
								<span class="publishing-home-social-proof__logo-text"><?php echo esc_html( $name ); ?></span>
							<?php endif; ?>
						<?php if ( $url !== '' ) : ?>
							</a>
						<?php else : ?>
							</span>
						<?php endif; ?>
					</li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>
	</div>
</section>
