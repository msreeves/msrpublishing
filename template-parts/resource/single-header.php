<?php
/**
 * Shared resource single header.
 *
 * @package msrsandbox
 */

$post_id = get_the_ID();
$hero_id = function_exists( 'get_field' ) ? (int) get_field( 'hero_image', $post_id ) : 0;
if ( ! $hero_id ) {
	$hero_id = (int) get_post_thumbnail_id( $post_id );
}
if ( function_exists( 'msr_publishing_sanitize_hero_attachment_id' ) ) {
	$hero_id = msr_publishing_sanitize_hero_attachment_id( $hero_id );
}
$ext_url = function_exists( 'get_field' ) ? (string) get_field( 'external_url', $post_id ) : '';
$is_ext  = function_exists( 'get_field' ) && get_field( 'is_external', $post_id );
$terms   = get_the_terms( $post_id, 'resource_type' );
$primary = msr_publishing_get_primary_resource_type( $post_id );
?>
<header class="resource-single__header mb-4">
	<?php if ( $primary ) : ?>
		<div class="resource-single__types mb-2">
			<?php msr_publishing_render_format_badge( $primary ); ?>
			<?php
			if ( $terms && ! is_wp_error( $terms ) ) {
				foreach ( $terms as $term ) {
					if ( $primary->term_id === $term->term_id ) {
						continue;
					}
					printf(
						'<a class="resource-card__type badge text-decoration-none" href="%s">%s</a>',
						esc_url( get_term_link( $term ) ),
						esc_html( $term->name )
					);
				}
			}
			?>
		</div>
	<?php endif; ?>
	<?php msr_publishing_render_resource_topic_badges( $post_id ); ?>
	<?php msr_publishing_render_resource_series_context( $post_id ); ?>
	<?php the_title( '<h1 class="entry-title msr-reveal">', '</h1>' ); ?>
	<?php msr_publishing_render_content_byline( $post_id, false ); ?>
</header>
<?php if ( $hero_id ) : ?>
	<figure class="resource-single__hero msr-single-media mb-4">
		<div class="msr-single-media__frame">
			<?php
			echo wp_get_attachment_image(
				$hero_id,
				'large',
				false,
				array(
					'class'   => 'msr-single-media__img img-fluid w-100',
					'loading' => 'eager',
				)
			);
			?>
		</div>
	</figure>
<?php endif; ?>
<?php if ( $is_ext && $ext_url !== '' ) : ?>
	<p class="resource-single__external mb-4">
		<a class="btn btn-primary" href="<?php echo esc_url( $ext_url ); ?>" target="_blank" rel="noopener noreferrer">
			<?php esc_html_e( 'Read on external site', 'msrsandbox' ); ?>
			<i class="fa-solid fa-arrow-up-right-from-square ms-1" aria-hidden="true"></i>
		</a>
	</p>
<?php endif; ?>
