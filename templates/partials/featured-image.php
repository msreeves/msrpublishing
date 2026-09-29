<?php
/**
 * Displays the featured image (publishing singles — constrained).
 *
 * @package msrpublishing
 */

$post_id = get_the_ID();
$thumb_id = function_exists( 'msr_publishing_get_card_hero_attachment_id' )
	? (int) msr_publishing_get_card_hero_attachment_id( $post_id )
	: (int) get_post_thumbnail_id( $post_id );
if ( function_exists( 'msr_publishing_sanitize_hero_attachment_id' ) ) {
	$thumb_id = msr_publishing_sanitize_hero_attachment_id( $thumb_id );
}

if ( ! $thumb_id || post_password_required() ) {
	return;
}
?>
<figure class="featured-media post-single__featured msr-single-media msr-single-media--contain">
	<div class="msr-single-media__frame msr-single-media__frame--contain">
		<?php
		echo wp_get_attachment_image(
			$thumb_id,
			'large',
			false,
			array(
				'class' => 'msr-single-media__img',
			)
		);
		?>
	</div>
	<?php
	$caption = wp_get_attachment_caption( $thumb_id );
	if ( $caption ) {
		?>
		<figcaption class="wp-caption-text small text-muted mt-2"><?php echo esc_html( $caption ); ?></figcaption>
		<?php
	}
	?>
</figure>
