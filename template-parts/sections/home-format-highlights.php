<?php
/**
 * Home format highlight rows — whitepapers, webinars, video, and podcast.
 *
 * @package msrpublishing
 */

$layout     = msr_publishing_get_home_curated_resource_layout();
$exclude_id = isset( $layout['featured_id'] ) ? (int) $layout['featured_id'] : 0;
$exclude    = isset( $layout['format_exclude'] ) ? (array) $layout['format_exclude'] : array();
$exclude    = array_values( array_unique( array_filter( array_map( 'intval', $exclude ) ) ) );

$rows         = msr_publishing_get_home_format_highlight_rows();
$format_limit = msr_publishing_get_home_format_row_count();
$has_rows     = false;

foreach ( $rows as $row ) {
	$row_ids = msr_publishing_get_home_format_row_resource_ids( $row['slug'], $format_limit, $exclude );
	if ( ! $row_ids ) {
		$fallback_exclude = $exclude_id ? array( $exclude_id ) : array();
		$row_ids          = msr_publishing_get_home_format_row_resource_ids( $row['slug'], $format_limit, $fallback_exclude );
	}
	if ( $row_ids ) {
		$has_rows = true;
		break;
	}
}

if ( ! $has_rows ) {
	return;
}
?>
<section class="publishing-home-formats" aria-labelledby="publishing-home-formats-heading">
	<div class="container">
		<header class="publishing-section-header text-center mb-4">
			<h2 id="publishing-home-formats-heading" class="publishing-section-header__title msr-reveal msr-reveal--up">
				<?php esc_html_e( 'Browse by', 'msrpublishing' ); ?>
				<em class="publishing-section-header__accent"><?php esc_html_e( 'format.', 'msrpublishing' ); ?></em>
			</h2>
			<p class="publishing-section-header__lead text-muted msr-reveal msr-reveal--up">
				<?php esc_html_e( 'Recent picks per format — open a row’s hub for the full archive.', 'msrpublishing' ); ?>
			</p>
		</header>
		<?php foreach ( $rows as $row_index => $row ) : ?>
			<?php
			$row_ids = msr_publishing_get_home_format_row_resource_ids( $row['slug'], $format_limit, $exclude );
			if ( ! $row_ids ) {
				$fallback_exclude = $exclude_id ? array( $exclude_id ) : array();
				$row_ids          = msr_publishing_get_home_format_row_resource_ids( $row['slug'], $format_limit, $fallback_exclude );
			}
			if ( ! $row_ids ) {
				continue;
			}

			$term       = get_term_by( 'slug', $row['slug'], 'resource_type' );
			$view_all   = $term instanceof WP_Term ? get_term_link( $term ) : get_post_type_archive_link( 'resource' );
			$heading_id = 'publishing-home-format-' . sanitize_html_class( $row['slug'] );
			$row_class  = 'publishing-home-formats__row publishing-home-formats__row--' . sanitize_html_class( $row['slug'] );
			$row_class .= ' publishing-home-format-' . sanitize_html_class( $row['slug'] );
			if ( $row_index > 0 ) {
				$row_class .= ' publishing-home-formats__row--spaced';
			}
			?>
			<div class="<?php echo esc_attr( $row_class ); ?>" aria-labelledby="<?php echo esc_attr( $heading_id ); ?>">
				<div class="publishing-home-formats__row-head d-flex flex-wrap align-items-end justify-content-between gap-2 mb-3">
					<div class="publishing-home-formats__row-copy">
						<h3 id="<?php echo esc_attr( $heading_id ); ?>" class="publishing-home-formats__row-title h5 mb-1">
							<?php
							echo esc_html( trim( $row['before'] . ' ' . wp_strip_all_tags( $row['accent'] ) ) );
							?>
						</h3>
						<?php if ( ! empty( $row['lead'] ) ) : ?>
							<p class="publishing-home-formats__row-lead small text-muted mb-0"><?php echo esc_html( $row['lead'] ); ?></p>
						<?php endif; ?>
					</div>
					<?php if ( $view_all && ! is_wp_error( $view_all ) ) : ?>
						<a class="btn btn-sm btn-outline-primary publishing-home-formats__row-link flex-shrink-0" href="<?php echo esc_url( $view_all ); ?>">
							<?php
							echo esc_html(
								sprintf(
									/* translators: %s: resource format name */
									__( 'All %s', 'msrpublishing' ),
									$term instanceof WP_Term ? $term->name : $row['slug']
								)
							);
							?>
						</a>
					<?php endif; ?>
				</div>
				<div class="publishing-home-formats__track">
					<?php
					$row_col_class = msr_publishing_get_home_format_row_col_class( count( $row_ids ) );
					?>
					<div class="row g-3 msr-reveal-stagger publishing-home-formats__grid publishing-home-formats__grid--count-<?php echo (int) count( $row_ids ); ?>">
						<?php
						foreach ( $row_ids as $resource_id ) {
							echo '<div class="' . esc_attr( $row_col_class ) . ' publishing-home-formats__cell">';
							get_template_part(
								'template-parts/cards/resource',
								'card',
								array(
									'post_id' => (int) $resource_id,
									'layout'  => 'compact',
								)
							);
							echo '</div>';
						}
						?>
					</div>
				</div>
			</div>
		<?php endforeach; ?>
	</div>
</section>
