<?php
/**
 * Resource archive card.
 *
 * @package msrsandbox
 */

$card_args = isset( $args ) && is_array( $args ) ? $args : array();
$layout    = isset( $card_args['layout'] ) ? (string) $card_args['layout'] : '';
$featured  = ( 'featured' === $layout );

$post_id = isset( $card_args['post_id'] ) ? (int) $card_args['post_id'] : 0;
if ( $post_id <= 0 ) {
	$post_id = get_the_ID();
}
$classes = array( 'msr-card', 'resource-card', 'card', 'h-100' );
if ( $featured ) {
	$classes = array( 'msr-card', 'resource-card', 'resource-card--featured', 'card', 'h-100' );
}
$terms   = get_the_terms( $post_id, 'resource_type' );
$primary = msr_publishing_get_primary_resource_type( $post_id );
if ( $primary ) {
	$classes[] = 'resource-card--' . sanitize_html_class( $primary->slug );
}
$external = function_exists( 'get_field' ) && get_field( 'is_external', $post_id );
$url      = function_exists( 'get_field' ) ? (string) get_field( 'external_url', $post_id ) : '';
$href     = ( $external && $url !== '' ) ? $url : get_permalink( $post_id );
$newtab   = ( $external && $url !== '' );
$card_title = get_the_title( $post_id );
$media_label = sprintf(
	/* translators: %s: resource title */
	__( 'View resource: %s', 'msrsandbox' ),
	$card_title
);

$media_args = array(
	'post_id'    => $post_id,
	'href'       => $href,
	'newtab'     => $newtab,
	'title'      => $card_title,
	'aria_label' => $media_label,
);

$render_body = static function () use ( $primary, $terms, $href, $newtab, $featured, $post_id ) {
	$programme = msr_publishing_get_resource_programme( $post_id );
	?>
	<div class="card-body d-flex flex-column">
		<?php if ( $primary ) : ?>
			<div class="resource-card__types mb-2">
				<?php msr_publishing_render_format_badge( $primary ); ?>
			</div>
		<?php elseif ( $terms && ! is_wp_error( $terms ) ) : ?>
			<div class="resource-card__types mb-2">
				<?php foreach ( $terms as $term ) : ?>
					<a class="resource-card__type badge text-decoration-none" href="<?php echo esc_url( get_term_link( $term ) ); ?>"><?php echo esc_html( $term->name ); ?></a>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
		<?php if ( $programme ) : ?>
			<p class="resource-card__programme small text-muted mb-2">
				<i class="fa-solid fa-link me-1" aria-hidden="true"></i>
				<?php echo esc_html( $programme['label'] ); ?>
			</p>
		<?php endif; ?>
		<h2 class="<?php echo $featured ? 'h4' : 'h5'; ?> card-title msr-reveal">
			<a href="<?php echo esc_url( $href ); ?>" <?php echo $newtab ? ' target="_blank" rel="noopener noreferrer"' : ''; ?>>
				<?php echo esc_html( get_the_title( $post_id ) ); ?>
				<?php if ( $newtab ) : ?>
					<span class="visually-hidden"><?php esc_html_e( '(opens in new tab)', 'msrsandbox' ); ?></span>
					<i class="fa-solid fa-arrow-up-right-from-square ms-1 small" aria-hidden="true"></i>
				<?php endif; ?>
			</a>
		</h2>
		<?php if ( has_excerpt( $post_id ) ) : ?>
			<p class="card-text small<?php echo $featured ? '' : ''; ?>"><?php echo esc_html( wp_strip_all_tags( get_the_excerpt( $post_id ) ) ); ?></p>
		<?php endif; ?>
		<?php msr_publishing_render_resource_card_meta( $post_id ); ?>
	</div>
	<?php
};
?>
<article class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>">
	<?php if ( $featured ) : ?>
		<div class="row g-0 align-items-stretch h-100">
			<div class="col-md-5 col-lg-5 resource-card__media-col d-flex">
				<?php msr_publishing_render_card_media( $media_args ); ?>
			</div>
			<div class="col-md-7 col-lg-7 d-flex">
				<?php $render_body(); ?>
			</div>
		</div>
	<?php else : ?>
		<?php msr_publishing_render_card_media( $media_args ); ?>
		<?php $render_body(); ?>
	<?php endif; ?>
</article>
