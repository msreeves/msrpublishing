<?php
/**
 * Related resources block for resource singles.
 *
 * @package msrsandbox
 */

$post_id = get_the_ID();
$related = msr_publishing_get_related_resources( $post_id, 3 );
if ( ! $related->have_posts() ) {
	return;
}
?>
<section class="resource-single__related mt-5 pt-4 border-top">
	<h2 class="h4 mb-4"><?php esc_html_e( 'Related resources', 'msrsandbox' ); ?></h2>
	<div class="row g-4">
		<?php
		while ( $related->have_posts() ) {
			$related->the_post();
			echo '<div class="col-md-6 col-lg-4">';
			get_template_part( 'template-parts/cards/resource', 'card' );
			echo '</div>';
		}
		wp_reset_postdata();
		?>
	</div>
</section>
