<?php
/**
 * Shared layout shell for resource format singles.
 *
 * @package msrpublishing
 */

/**
 * @param string $format_slug resource_type slug for BEM modifier.
 * @return void
 */
function msr_publishing_resource_single_open( $format_slug = '' ) {
	$format_slug = sanitize_title( (string) $format_slug );
	$extra       = array( 'resource-single', 'container', 'py-4', 'py-lg-5' );
	if ( $format_slug !== '' ) {
		$extra[] = 'resource-single--' . $format_slug;
	}

	echo '<article id="post-' . esc_attr( (string) get_the_ID() ) . '" class="' . esc_attr( implode( ' ', get_post_class( $extra ) ) ) . '">';
}

/**
 * @return void
 */
function msr_publishing_resource_single_close() {
	echo '</article>';
}

/**
 * Header, optional utility band, programme CTA.
 *
 * @param int                  $post_id Post ID.
 * @param array<string, mixed> $args    utility, programme (bool).
 * @return void
 */
function msr_publishing_resource_single_render_chrome( $post_id = 0, array $args = array() ) {
	$post_id = $post_id ? (int) $post_id : get_the_ID();
	$args    = wp_parse_args(
		$args,
		array(
			'utility'   => true,
			'programme' => true,
		)
	);

	get_template_part( 'template-parts/resource/single', 'header' );

	if ( $args['utility'] ) {
		msr_publishing_render_single_utility_band( $post_id );
	}

}

/**
 * @return void
 */
function msr_publishing_resource_single_preface_open( $format_slug = '' ) {
	$format_slug = sanitize_title( (string) $format_slug );
	$class       = 'resource-single__preface resource-single__preface--stack';
	if ( $format_slug !== '' ) {
		$class .= ' resource-single__preface--' . $format_slug;
	}
	echo '<div class="' . esc_attr( $class ) . '">';
}

/**
 * @return void
 */
function msr_publishing_resource_single_preface_close() {
	echo '</div>';
}

/**
 * Two-column format layout (main + sticky aside) — webinar meta, video duration, etc.
 *
 * @param string $modifier BEM suffix e.g. split, media-first.
 * @return void
 */
function msr_publishing_resource_single_format_layout_open( $modifier = 'split' ) {
	$modifier = sanitize_html_class( (string) $modifier );
	$class    = 'resource-single__format-layout';
	if ( $modifier !== '' ) {
		$class .= ' resource-single__format-layout--' . $modifier;
	}
	echo '<div class="' . esc_attr( $class ) . '">';
	echo '<div class="resource-single__format-main">';
}

/**
 * @return void
 */
function msr_publishing_resource_single_format_main_close_aside_open() {
	echo '</div><aside class="resource-single__format-aside">';
}

/**
 * @return void
 */
function msr_publishing_resource_single_format_layout_close() {
	echo '</aside></div>';
}

/**
 * Whitepaper / gated download rail for the preface aside column.
 *
 * @param int $post_id Resource ID.
 * @return void
 */
function msr_publishing_resource_single_render_download_rail( $post_id = 0 ) {
	$post_id = $post_id ? (int) $post_id : get_the_ID();
	$gate    = function_exists( 'get_field' ) && get_field( 'gate_download', $post_id );
	$pdf_id  = function_exists( 'get_field' ) ? (int) get_field( 'pdf_file', $post_id ) : 0;
	$pdf_url = $pdf_id ? wp_get_attachment_url( $pdf_id ) : '';

	if ( $gate ) {
		get_template_part(
			'template-parts/leadgen/gate',
			'form',
			array(
				'pdf_url' => $pdf_url,
			)
		);
		return;
	}
	?>
	<div class="resource-single__download-panel" role="region" aria-label="<?php esc_attr_e( 'Download', 'msrpublishing' ); ?>">
		<h2 class="h6 text-uppercase fw-semibold text-muted mb-2"><?php esc_html_e( 'Download', 'msrpublishing' ); ?></h2>
		<?php if ( $pdf_url ) : ?>
			<p class="resource-single__download mb-0">
				<a class="btn btn-primary w-100" href="<?php echo esc_url( $pdf_url ); ?>" download>
					<i class="fa-solid fa-file-pdf me-1" aria-hidden="true"></i>
					<?php esc_html_e( 'Download PDF', 'msrpublishing' ); ?>
				</a>
			</p>
		<?php else : ?>
			<p class="resource-single__download mb-0">
				<span class="btn btn-outline-primary disabled w-100" aria-disabled="true">
					<i class="fa-solid fa-file-pdf me-1" aria-hidden="true"></i>
					<?php esc_html_e( 'Download PDF', 'msrpublishing' ); ?>
				</span>
				<span class="d-block small text-muted mt-2 mb-0"><?php esc_html_e( 'Attach a PDF in the resource editor to enable download.', 'msrpublishing' ); ?></span>
			</p>
		<?php endif; ?>
	</div>
	<?php
}

/**
 * Primary media / player panel (video, webinar replay, podcast).
 *
 * @param string $html            Raw embed HTML.
 * @param string $aria_label      Accessible region label.
 * @param string $modifier_class  e.g. resource-single__media-panel--video.
 * @return void
 */
function msr_publishing_resource_single_render_media_panel( $html, $aria_label, $modifier_class = '' ) {
	$html = trim( (string) $html );
	if ( $html === '' ) {
		return;
	}

	$class = 'resource-single__media-panel msr-video-embed';
	if ( $modifier_class !== '' ) {
		$class .= ' ' . sanitize_html_class( $modifier_class );
	}
	?>
	<div class="<?php echo esc_attr( $class ); ?>" role="region" aria-label="<?php echo esc_attr( $aria_label ); ?>">
		<?php echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	</div>
	<?php
}

/**
 * Podcast player panel.
 *
 * @param int $post_id Resource ID.
 * @return void
 */
function msr_publishing_resource_single_render_podcast_panel( $post_id = 0 ) {
	$post_id = $post_id ? (int) $post_id : get_the_ID();
	if ( ! msr_publishing_get_podcast_audio_url( $post_id ) ) {
		return;
	}
	?>
	<div class="resource-single__media-panel resource-single__media-panel--podcast msr-podcast-player" role="region" aria-label="<?php esc_attr_e( 'Podcast player', 'msrpublishing' ); ?>">
		<?php echo msr_publishing_render_podcast_player( $post_id ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	</div>
	<?php
}

/**
 * @param int  $post_id   Resource ID.
 * @param bool $longform  Use TOC column when eligible.
 * @return void
 */
function msr_publishing_resource_single_render_body( $post_id = 0, $longform = false ) {
	$post_id    = $post_id ? (int) $post_id : get_the_ID();
	$sticky_toc = $longform && msr_publishing_should_render_sticky_toc( $post_id );

	if ( $sticky_toc ) {
		?>
		<div class="resource-single__layout resource-single__layout--longform row g-4 g-lg-5">
			<div class="col-lg-8">
				<div class="entry-content resource-single__body resource-single__body--longform">
					<?php the_content(); ?>
				</div>
			</div>
			<aside class="col-lg-4 resource-single__toc-col">
				<?php msr_publishing_render_sticky_toc( $post_id ); ?>
			</aside>
		</div>
		<?php
		return;
	}

	$body_class = 'entry-content resource-single__body';
	if ( $longform ) {
		$body_class .= ' resource-single__body--longform';
	}
	?>
	<div class="resource-single__layout resource-single__layout--simple">
		<div class="<?php echo esc_attr( $body_class ); ?>">
			<?php the_content(); ?>
		</div>
	</div>
	<?php
}

/**
 * Related resources, commentary crosslink, subscribe.
 *
 * @return void
 */
function msr_publishing_resource_single_render_footer() {
	msr_publishing_render_resource_programme_cta( get_the_ID() );
	get_template_part( 'template-parts/resource/single', 'related' );
	msr_publishing_render_resource_commentary_crosslink( get_the_ID() );
	msr_publishing_render_resource_single_subscribe();
}
