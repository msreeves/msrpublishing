<?php
/**
 * Home format highlight rows — whitepapers, webinars, video, and podcast.
 *
 * @package msrsandbox
 */

$exclude_id = msr_publishing_get_site_featured_resource_id();
$exclude    = $exclude_id ? array( $exclude_id ) : array();

$bento_preview = new WP_Query(
	array(
		'post_type'           => 'resource',
		'posts_per_page'      => 4,
		'post_status'         => 'publish',
		'post__not_in'        => $exclude,
		'ignore_sticky_posts' => true,
		'no_found_rows'       => true,
		'fields'              => 'ids',
	)
);
if ( $bento_preview->have_posts() ) {
	$exclude = array_merge( $exclude, $bento_preview->posts );
}
$exclude = array_values( array_unique( array_filter( array_map( 'intval', $exclude ) ) ) );

$rows     = msr_publishing_get_home_format_highlight_rows();
$has_rows = false;

foreach ( $rows as $row ) {
	$query = msr_publishing_get_home_format_highlight_query( $row['slug'], 2, $exclude );
	if ( ! $query->have_posts() ) {
		$query = msr_publishing_get_home_format_highlight_query( $row['slug'], 2, $exclude_id ? array( $exclude_id ) : array() );
	}
	if ( $query->have_posts() ) {
		$has_rows = true;
		break;
	}
}

if ( ! $has_rows ) {
	return;
}
?>
<section class="publishing-home-formats" aria-label="<?php esc_attr_e( 'Format highlights', 'msrsandbox' ); ?>">
	<div class="container">
		<?php foreach ( $rows as $row_index => $row ) : ?>
			<?php
			$query = msr_publishing_get_home_format_highlight_query( $row['slug'], 2, $exclude );
			if ( ! $query->have_posts() ) {
				$query = msr_publishing_get_home_format_highlight_query( $row['slug'], 2, $exclude_id ? array( $exclude_id ) : array() );
			}
			if ( ! $query->have_posts() ) {
				continue;
			}

			$term      = get_term_by( 'slug', $row['slug'], 'resource_type' );
			$view_all  = $term instanceof WP_Term ? get_term_link( $term ) : get_post_type_archive_link( 'resource' );
			$heading_id = 'publishing-home-format-' . sanitize_html_class( $row['slug'] );
			?>
			<div class="publishing-home-formats__row publishing-home-formats__row--<?php echo esc_attr( sanitize_html_class( $row['slug'] ) ); ?><?php echo $row_index > 0 ? ' publishing-home-formats__row--spaced' : ''; ?>">
				<?php
				msr_publishing_render_section_header(
					$row['before'],
					$row['accent'],
					$row['lead'],
					$heading_id
				);
				?>
				<div class="row g-3 msr-reveal-stagger">
					<?php
					while ( $query->have_posts() ) {
						$query->the_post();
						echo '<div class="col-md-6">';
						get_template_part( 'template-parts/cards/resource', 'card' );
						echo '</div>';
					}
					wp_reset_postdata();
					?>
				</div>
				<?php if ( $view_all && ! is_wp_error( $view_all ) ) : ?>
					<p class="text-center mt-3 mb-0">
						<a class="btn btn-sm btn-outline-primary" href="<?php echo esc_url( $view_all ); ?>">
							<?php
							echo esc_html(
								sprintf(
									/* translators: %s: resource format name */
									__( 'View all %s', 'msrsandbox' ),
									$term instanceof WP_Term ? $term->name : $row['slug']
								)
							);
							?>
						</a>
					</p>
				<?php endif; ?>
			</div>
		<?php endforeach; ?>
	</div>
</section>
