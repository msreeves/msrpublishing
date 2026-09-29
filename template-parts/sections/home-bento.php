<?php
/**
 * Home bento — four equal editorial resource cards (no hero duplicate featured layout).
 *
 * @package msrpublishing
 */

$layout   = msr_publishing_get_home_curated_resource_layout();
$post_ids = isset( $layout['bento_ids'] ) ? (array) $layout['bento_ids'] : array();
$post_ids = array_values( array_filter( array_map( 'intval', $post_ids ) ) );

if ( count( $post_ids ) < 2 ) {
	return;
}

$post_ids = array_slice( $post_ids, 0, 4 );
?>
<section class="publishing-home-bento-band" aria-labelledby="publishing-home-bento-heading">
	<div class="container">
		<?php
		msr_publishing_render_section_header(
			__( 'Curated', 'msrpublishing' ),
			__( 'spotlight.', 'msrpublishing' ),
			__( 'Four recent resources in a balanced grid — editorial picks from the library, separate from the format rows above.', 'msrpublishing' ),
			'publishing-home-bento-heading'
		);
		?>

		<div class="publishing-home-bento msr-reveal-stagger" data-msr-reveal-stagger="bento">
			<?php foreach ( $post_ids as $index => $post_id ) : ?>
				<div class="publishing-home-bento__cell<?php echo 0 === (int) $index ? ' publishing-home-bento__cell--lead' : ''; ?>">
					<?php
					get_template_part(
						'template-parts/cards/resource',
						'card',
						array(
							'post_id' => (int) $post_id,
						)
					);
					?>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>
