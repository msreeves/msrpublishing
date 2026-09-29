<?php
/**
 * Publishing home — browse-by-topic strip.
 *
 * @package msrpublishing
 */
?>
<section class="publishing-home-topics" aria-labelledby="publishing-home-topics-heading">
	<div class="container py-3">
		<h2 id="publishing-home-topics-heading" class="visually-hidden"><?php esc_html_e( 'Browse by topic', 'msrpublishing' ); ?></h2>
		<?php msr_publishing_render_topic_nav( 'all' ); ?>
	</div>
</section>
