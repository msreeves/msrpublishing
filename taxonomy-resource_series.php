<?php
/**
 * Resource series hub — ordered reading path.
 *
 * @package msrsandbox
 */

get_header();

$term = get_queried_object();
if ( ! $term instanceof WP_Term ) {
	return;
}
?>
<main id="site-content" class="taxonomy-resource-series publishing-series-hub">
	<header class="page-header container py-4">
		<p class="publishing-series-hub__eyebrow small text-uppercase fw-semibold text-muted mb-2"><?php esc_html_e( 'Resource series', 'msrsandbox' ); ?></p>
		<h1 class="page-title msr-reveal"><?php single_term_title(); ?></h1>
		<?php
		$desc = term_description();
		if ( $desc ) {
			echo '<div class="taxonomy-description msr-reveal">' . wp_kses_post( $desc ) . '</div>';
		} else {
			?>
			<p class="taxonomy-description msr-reveal">
				<?php esc_html_e( 'Follow the recommended reading order across formats in this Atlas Briefing series.', 'msrsandbox' ); ?>
			</p>
			<?php
		}
		?>
	</header>

	<section class="container pb-5" aria-labelledby="publishing-series-reading-heading">
		<h2 id="publishing-series-reading-heading" class="h4 mb-4"><?php esc_html_e( 'Reading order', 'msrsandbox' ); ?></h2>
		<?php msr_publishing_render_series_reading_order( $term ); ?>
	</section>

	<?php msr_publishing_render_archive_subscribe_band(); ?>
</main>
<?php
get_footer();
