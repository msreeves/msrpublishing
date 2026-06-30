<?php
/**
 * Publishing home — format + topic discover strip.
 *
 * @package msrsandbox
 */

$resources_url = get_post_type_archive_link( 'resource' );
$topics_url    = function_exists( 'msr_publishing_get_page_url' )
	? msr_publishing_get_page_url( 'topics', '/topics/' )
	: '';
?>
<section class="publishing-home-discover" aria-labelledby="publishing-home-discover-heading">
	<div class="container py-4">
		<header class="publishing-home-discover__header text-center mb-3">
			<h2 id="publishing-home-discover-heading" class="h4 publishing-home-discover__title mb-2">
				<?php esc_html_e( 'Explore by format and topic', 'msrsandbox' ); ?>
			</h2>
			<p class="publishing-home-discover__lead text-muted small mb-0 mx-auto">
				<?php esc_html_e( 'Jump to a format hub for webinars, whitepapers, and briefings — or open a topic collection for workforce and resilience coverage.', 'msrsandbox' ); ?>
			</p>
		</header>
		<div class="publishing-home-discover__grid">
			<?php msr_publishing_render_resource_type_nav( 'all' ); ?>
			<?php msr_publishing_render_topic_nav( 'all' ); ?>
		</div>
		<?php if ( $resources_url || $topics_url ) : ?>
			<p class="publishing-home-discover__actions text-center mt-3 mb-0">
				<?php if ( $resources_url ) : ?>
					<a class="btn btn-sm btn-outline-primary me-2" href="<?php echo esc_url( $resources_url ); ?>">
						<?php esc_html_e( 'All resources', 'msrsandbox' ); ?>
					</a>
				<?php endif; ?>
				<?php if ( $topics_url ) : ?>
					<a class="btn btn-sm btn-outline-secondary" href="<?php echo esc_url( $topics_url ); ?>">
						<?php esc_html_e( 'All topics', 'msrsandbox' ); ?>
					</a>
				<?php endif; ?>
			</p>
		<?php endif; ?>
	</div>
</section>
