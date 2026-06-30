<?php
/**
 * Commentary post card (publishing chrome).
 *
 * @package msrsandbox
 */

$post_id    = get_the_ID();
$card_title = get_the_title( $post_id );
$topics     = get_the_terms( $post_id, 'topic' );
$media_label = sprintf(
	/* translators: %s: post title */
	__( 'View insight: %s', 'msrsandbox' ),
	$card_title
);

$card_args = isset( $args ) && is_array( $args ) ? $args : array();
$reveal    = empty( $card_args['no_reveal'] ) ? ' msr-reveal msr-reveal--up' : '';
?>
<article <?php post_class( 'msr-card post-card card h-100' . $reveal ); ?>>
	<?php
	msr_publishing_render_card_media(
		array(
			'post_id'    => $post_id,
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
			<p class="small text-muted mb-2"><em><?php esc_html_e( 'Sponsored content', 'msrsandbox' ); ?></em></p>
		<?php endif; ?>
		<h3 class="h5 card-title">
			<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
		</h3>
		<?php if ( has_excerpt() ) : ?>
			<p class="card-text small text-muted flex-grow-1"><?php echo esc_html( wp_strip_all_tags( get_the_excerpt() ) ); ?></p>
		<?php endif; ?>
		<?php msr_publishing_render_post_card_meta( $post_id ); ?>
	</div>
</article>
