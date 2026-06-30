<?php
/**
 * Resource library section for the publishing home.
 *
 * Purpose: showcase a diverse slice of the library (one per format when possible).
 * The hero holds the single editorial featured pick; this band is the browseable grid.
 *
 * @package msrsandbox
 */

$exclude_id = msr_publishing_get_site_featured_resource_id();
$archive    = get_post_type_archive_link( 'resource' );
$post_ids   = msr_publishing_get_home_library_resource_ids( 6, $exclude_id ? array( $exclude_id ) : array() );

if ( ! $post_ids ) {
	return;
}
?>
<section class="publishing-featured-resources" aria-labelledby="publishing-home-library-heading">
	<div class="container">
		<?php
		msr_publishing_render_section_header(
			__( 'Latest from the', 'msrsandbox' ),
			__( 'library.', 'msrsandbox' ),
			__( 'A format-diverse sample of Atlas Briefing resources — separate from the hero featured pick above. Browse the full archive for every whitepaper, webinar, and briefing.', 'msrsandbox' ),
			'publishing-home-library-heading'
		);
		?>

		<div class="publishing-home-library msr-reveal-stagger" data-msr-reveal-stagger="library">
			<?php
			$index = 0;
			foreach ( $post_ids as $post_id ) {
				$is_lead = 0 === $index;
				$slot    = $is_lead ? 'publishing-home-library__lead' : 'publishing-home-library__item';
				echo '<div class="' . esc_attr( $slot ) . '">';
				get_template_part(
					'template-parts/cards/resource',
					'card',
					array(
						'layout'  => $is_lead ? 'featured' : '',
						'post_id' => (int) $post_id,
					)
				);
				echo '</div>';
				++$index;
			}
			?>
		</div>

		<?php if ( $archive ) : ?>
			<p class="text-center mt-4 mb-0">
				<a class="btn btn-outline-primary" href="<?php echo esc_url( $archive ); ?>"><?php esc_html_e( 'View all resources', 'msrsandbox' ); ?></a>
			</p>
		<?php endif; ?>
	</div>
</section>
