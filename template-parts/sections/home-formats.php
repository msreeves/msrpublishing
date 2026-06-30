<?php
/**
 * Publishing home — browse-by-format strip.
 *
 * @package msrsandbox
 */
?>
<section class="publishing-home-formats" aria-labelledby="publishing-home-formats-heading">
	<div class="container py-3">
		<h2 id="publishing-home-formats-heading" class="visually-hidden"><?php esc_html_e( 'Browse by format', 'msrsandbox' ); ?></h2>
		<?php msr_publishing_render_resource_type_nav( 'all' ); ?>
	</div>
</section>
