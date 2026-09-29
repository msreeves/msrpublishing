<?php
/**
 * Resource library section for the publishing home.
 *
 * Purpose: showcase a diverse slice of the library (one per format when possible).
 * The hero holds the single editorial featured pick; this band is the browseable grid.
 *
 * @package msrpublishing
 */

$layout   = msr_publishing_get_home_curated_resource_layout();
$archive  = get_post_type_archive_link( 'resource' );
$post_ids = isset( $layout['library_ids'] ) ? (array) $layout['library_ids'] : array();
$post_ids = array_values( array_filter( array_map( 'intval', $post_ids ) ) );

if ( ! $post_ids ) {
	return;
}
?>
<section class="publishing-featured-resources" aria-labelledby="publishing-home-library-heading">
	<div class="container">
		<?php
		msr_publishing_render_section_header(
			__( 'Latest from the', 'msrpublishing' ),
			__( 'library.', 'msrpublishing' ),
			__( 'Three editorial picks across formats — open the library for whitepapers, webinars, briefings, and more.', 'msrpublishing' ),
			'publishing-home-library-heading'
		);
		?>

		<div class="publishing-home-library msr-reveal-stagger" data-msr-reveal-stagger="library">
			<?php
			foreach ( $post_ids as $post_id ) {
				echo '<div class="publishing-home-library__item">';
				get_template_part(
					'template-parts/cards/resource',
					'card',
					array(
						'post_id' => (int) $post_id,
					)
				);
				echo '</div>';
			}
			?>
		</div>

		<?php if ( $archive ) : ?>
			<p class="text-center mt-4 mb-0">
				<a class="btn btn-outline-primary" href="<?php echo esc_url( $archive ); ?>"><?php esc_html_e( 'View all resources', 'msrpublishing' ); ?></a>
			</p>
		<?php endif; ?>
	</div>
</section>
