<?php
/**
 * Whitepaper single resource.
 *
 * @package msrsandbox
 */

$post_id      = get_the_ID();
$gate         = function_exists( 'get_field' ) && get_field( 'gate_download', $post_id );
$pdf_id       = function_exists( 'get_field' ) ? (int) get_field( 'pdf_file', $post_id ) : 0;
$pdf_url      = $pdf_id ? wp_get_attachment_url( $pdf_id ) : '';
$sticky_toc   = msr_publishing_should_render_sticky_toc( $post_id );
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'resource-single resource-single--whitepaper container py-4' ); ?>>
	<?php get_template_part( 'template-parts/resource/single', 'header' ); ?>
	<?php msr_publishing_render_single_utility_band( $post_id ); ?>
	<?php msr_publishing_render_resource_programme_cta( $post_id ); ?>
	<?php msr_publishing_render_whitepaper_preview( $post_id ); ?>
	<?php msr_publishing_render_key_takeaways( $post_id ); ?>

	<?php
	if ( $gate ) {
		get_template_part(
			'template-parts/leadgen/gate',
			'form',
			array(
				'pdf_url' => $pdf_url,
			)
		);
	} elseif ( $pdf_url ) {
		?>
		<p class="resource-single__download mb-4">
			<a class="btn btn-primary" href="<?php echo esc_url( $pdf_url ); ?>" download>
				<i class="fa-solid fa-file-pdf me-1" aria-hidden="true"></i>
				<?php esc_html_e( 'Download PDF', 'msrsandbox' ); ?>
			</a>
		</p>
		<?php
	} else {
		?>
		<p class="resource-single__download mb-4">
			<span class="btn btn-outline-primary disabled" aria-disabled="true">
				<i class="fa-solid fa-file-pdf me-1" aria-hidden="true"></i>
				<?php esc_html_e( 'Download PDF', 'msrsandbox' ); ?>
			</span>
			<span class="d-block small text-muted mt-2"><?php esc_html_e( 'Attach a PDF in the resource editor to enable download.', 'msrsandbox' ); ?></span>
		</p>
		<?php
	}
	?>

	<div class="resource-single__layout row g-4">
		<div class="<?php echo esc_attr( $sticky_toc ? 'col-lg-8' : 'col-12' ); ?>">
			<div class="entry-content resource-single__body">
				<?php the_content(); ?>
			</div>
		</div>
		<?php if ( $sticky_toc ) : ?>
			<aside class="col-lg-4">
				<?php msr_publishing_render_sticky_toc( $post_id ); ?>
			</aside>
		<?php endif; ?>
	</div>
	<?php get_template_part( 'template-parts/resource/single', 'related' ); ?>
	<?php msr_publishing_render_resource_commentary_crosslink( $post_id ); ?>
	<?php msr_publishing_render_resource_single_subscribe(); ?>
</article>
