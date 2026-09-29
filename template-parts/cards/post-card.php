<?php
/**
 * Commentary post card (publishing chrome).
 *
 * @package msrpublishing
 */

$card_args = isset( $args ) && is_array( $args ) ? $args : array();
$post_id   = isset( $card_args['post_id'] ) ? (int) $card_args['post_id'] : (int) get_the_ID();
if ( $post_id <= 0 ) {
	return;
}

$card_title  = get_the_title( $post_id );
$topics      = get_the_terms( $post_id, 'topic' );
$permalink   = get_permalink( $post_id );
$media_label = sprintf(
	/* translators: %s: post title */
	__( 'View insight: %s', 'msrpublishing' ),
	$card_title
);
$reveal      = empty( $card_args['no_reveal'] ) ? ' msr-reveal msr-reveal--up' : '';
?>
<article class="<?php echo esc_attr( implode( ' ', get_post_class( 'msr-card post-card card h-100' . $reveal, $post_id ) ) ); ?>">
	<?php
	msr_publishing_render_card_media(
		array(
			'post_id'    => $post_id,
			'href'       => $permalink ? $permalink : '',
			'title'      => $card_title,
			'aria_label' => $media_label,
		)
	);
	?>
	<div class="card-body d-flex flex-column">
		<?php if ( $topics && ! is_wp_error( $topics ) ) : ?>
			<div class="post-card__topics mb-2">
				<?php foreach ( $topics as $topic ) : ?>
					<a class="resource-card__type badge text-decoration-none" href="<?php echo esc_url( get_term_link( $topic ) ); ?>">
						<?php echo esc_html( $topic->name ); ?>
					</a>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
		<?php if ( msr_publishing_post_is_sponsored( $post_id ) ) : ?>
			<p class="small text-muted mb-2"><em><?php esc_html_e( 'Sponsored content', 'msrpublishing' ); ?></em></p>
		<?php endif; ?>
		<h3 class="h5 card-title">
			<?php if ( $permalink ) : ?>
				<a href="<?php echo esc_url( $permalink ); ?>"><?php echo esc_html( $card_title ); ?></a>
			<?php else : ?>
				<?php echo esc_html( $card_title ); ?>
			<?php endif; ?>
		</h3>
		<?php
		$card_teaser = has_excerpt( $post_id )
			? wp_strip_all_tags( get_the_excerpt( $post_id ) )
			: wp_trim_words( wp_strip_all_tags( (string) get_post_field( 'post_content', $post_id ) ), 28, '…' );
		if ( $card_teaser !== '' ) :
			?>
			<p class="card-text small text-muted flex-grow-1"><?php echo esc_html( $card_teaser ); ?></p>
		<?php else : ?>
			<p class="card-text small text-muted flex-grow-1 card-text--placeholder" aria-hidden="true">&nbsp;</p>
		<?php endif; ?>
		<?php msr_publishing_render_post_card_meta( $post_id ); ?>
	</div>
</article>
