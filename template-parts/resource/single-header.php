<?php
/**
 * Shared resource single header.
 *
 * @package msrpublishing
 */

$post_id = get_the_ID();
$hero_id = 0;
if ( function_exists( 'msr_publishing_resource_single_show_hero_banner' ) && msr_publishing_resource_single_show_hero_banner( $post_id ) ) {
	$hero_id = (int) msr_publishing_get_card_hero_attachment_id( $post_id );
}
if ( function_exists( 'msr_publishing_sanitize_hero_attachment_id' ) ) {
	$hero_id = msr_publishing_sanitize_hero_attachment_id( $hero_id );
}
$ext_url = function_exists( 'get_field' ) ? (string) get_field( 'external_url', $post_id ) : '';
$is_ext  = function_exists( 'get_field' ) && get_field( 'is_external', $post_id );
?>
<header class="resource-single__header mb-4">
	<?php msr_publishing_render_resource_single_masthead( $post_id ); ?>
	<?php the_title( '<h1 class="entry-title msr-reveal">', '</h1>' ); ?>
	<?php msr_publishing_render_content_byline( $post_id, false ); ?>
</header>
<?php if ( $hero_id ) : ?>
	<figure class="resource-single__hero msr-single-media msr-single-media--feature">
		<div class="msr-single-media__frame msr-single-media__frame--feature">
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
			<?php esc_html_e( 'Read on external site', 'msrpublishing' ); ?>
			<i class="fa-solid fa-arrow-up-right-from-square ms-1" aria-hidden="true"></i>
		</a>
	</p>
<?php endif; ?>
