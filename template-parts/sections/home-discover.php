<?php
/**
 * Publishing home — topic discovery strip (formats live in the library).
 *
 * @package msrpublishing
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
				<?php esc_html_e( 'Explore by topic', 'msrpublishing' ); ?>
			</h2>
			<p class="publishing-home-discover__lead text-muted small mb-0 mx-auto">
				<?php esc_html_e( 'Topic hubs group workforce, resilience, and leadership coverage — open a collection or browse the full resource library.', 'msrpublishing' ); ?>
			</p>
		</header>
		<?php if ( $resources_url || $topics_url ) : ?>
			<p class="publishing-home-discover__actions text-center mt-3 mb-0">
				<?php if ( $topics_url ) : ?>
					<a class="btn btn-sm btn-outline-primary me-2" href="<?php echo esc_url( $topics_url ); ?>">
						<?php esc_html_e( 'All topics', 'msrpublishing' ); ?>
					</a>
				<?php endif; ?>
				<?php if ( $resources_url ) : ?>
					<a class="btn btn-sm btn-outline-secondary" href="<?php echo esc_url( $resources_url ); ?>">
						<?php esc_html_e( 'All resources', 'msrpublishing' ); ?>
					</a>
				<?php endif; ?>
			</p>
		<?php endif; ?>
	</div>
	<?php msr_publishing_render_home_discover_panel(); ?>
</section>
