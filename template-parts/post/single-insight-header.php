<?php
/**
 * Commentary / insight single — header band (distinct from resource singles).
 *
 * @package msrpublishing
 */

$post_id = get_the_ID();
$mins    = function_exists( 'get_field' ) ? (int) get_field( 'read_time_minutes', $post_id ) : 0;
$dek     = has_excerpt( $post_id ) ? wp_strip_all_tags( get_the_excerpt( $post_id ) ) : '';
?>
<header class="post-single__header post-single__header--insight mb-4">
	<div class="post-single__masthead">
		<p class="post-single__format-label text-uppercase small fw-semibold mb-0">
			<?php esc_html_e( 'Insight', 'msrpublishing' ); ?>
			<?php if ( $mins > 0 ) : ?>
				<span class="post-single__format-sep" aria-hidden="true"> · </span>
				<span class="post-single__reading-time">
					<?php
					echo esc_html(
						sprintf(
							/* translators: %d: minutes */
							_n( '%d min read', '%d min read', $mins, 'msrpublishing' ),
							$mins
						)
					);
					?>
				</span>
			<?php endif; ?>
		</p>
		<div class="post-single__masthead-chips">
			<?php msr_publishing_render_post_topic_badges( $post_id ); ?>
		</div>
	</div>
	<?php if ( msr_publishing_post_is_sponsored( $post_id ) ) : ?>
		<p class="small text-muted mb-2"><em><?php esc_html_e( 'Sponsored content', 'msrpublishing' ); ?></em></p>
	<?php endif; ?>
	<?php the_title( '<h1 class="entry-title msr-reveal">', '</h1>' ); ?>
	<?php if ( $dek !== '' ) : ?>
		<p class="post-single__dek lead"><?php echo esc_html( $dek ); ?></p>
	<?php endif; ?>
	<?php msr_publishing_render_content_byline( $post_id, true ); ?>
</header>
