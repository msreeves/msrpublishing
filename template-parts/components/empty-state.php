<?php
/**
 * Unified empty-state panel for archives, listings, and search.
 *
 * @package msrsandbox
 *
 * @var array $args {
 *     @type string $context search|archive|listing.
 *     @type string $title   Heading.
 *     @type string $message Lead copy.
 *     @type bool   $search  Show site search form.
 *     @type array  $links   Helpful link buttons.
 * }
 */

$context = isset( $args['context'] ) ? sanitize_key( (string) $args['context'] ) : 'listing';
$title   = isset( $args['title'] ) ? (string) $args['title'] : '';
$message = isset( $args['message'] ) ? (string) $args['message'] : '';
$search  = ! empty( $args['search'] );
$links   = isset( $args['links'] ) && is_array( $args['links'] ) ? $args['links'] : array();
?>
<div class="msr-empty-state" data-msr-empty-state="<?php echo esc_attr( $context ); ?>" role="status">
	<div class="panel text-center">
		<?php if ( $title ) : ?>
			<p class="msr-empty-state__title h5 mb-2"><?php echo esc_html( $title ); ?></p>
		<?php endif; ?>
		<?php if ( $message ) : ?>
			<p class="msr-empty-state__message lead mb-0"><?php echo esc_html( $message ); ?></p>
		<?php endif; ?>
		<?php if ( $search ) : ?>
			<?php get_template_part( 'template-parts/forms/site-search' ); ?>
		<?php endif; ?>
		<?php if ( $links ) : ?>
			<nav class="msr-empty-state__links d-flex flex-wrap gap-2 justify-content-center mt-3" aria-label="<?php esc_attr_e( 'Helpful links', 'msrsandbox' ); ?>">
				<?php foreach ( $links as $link ) : ?>
					<?php if ( empty( $link['url'] ) ) { continue; } ?>
					<a class="btn btn-outline-primary btn-sm" href="<?php echo esc_url( $link['url'] ); ?>">
						<?php echo esc_html( $link['title'] ?? '' ); ?>
					</a>
				<?php endforeach; ?>
			</nav>
		<?php endif; ?>
	</div>
</div>
