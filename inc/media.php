<?php
/**
 * Media helpers — SVG logo, uploads.
 *
 * @package msrsandbox
 */

/**
 * @param array|false $image          Image data.
 * @param int         $attachment_id  Attachment ID.
 * @param string      $size           Size.
 * @param bool        $icon           Icon flag.
 * @return array|false
 */
function msr_publishing_fix_attachment_image_src_for_svg( $image, $attachment_id, $size, $icon ) {
	if ( $image ) {
		return $image;
	}
	$mime = get_post_mime_type( (int) $attachment_id );
	if ( $mime && false !== strpos( $mime, 'svg' ) ) {
		$url = wp_get_attachment_url( (int) $attachment_id );
		if ( $url ) {
			return array( $url, 0, 0, false );
		}
	}
	return $image;
}
add_filter( 'wp_get_attachment_image_src', 'msr_publishing_fix_attachment_image_src_for_svg', 10, 4 );

/** @deprecated */
function msrsandbox_fix_attachment_image_src_for_svg( $image, $attachment_id, $size, $icon ) {
	return msr_publishing_fix_attachment_image_src_for_svg( $image, $attachment_id, $size, $icon );
}

/**
 * @return void
 */
function msr_publishing_render_site_logo() {
	if ( function_exists( 'msr_publishing_use_text_wordmark' ) && msr_publishing_use_text_wordmark() ) {
		printf(
			'<span class="site-logo-wordmark">%s</span>',
			esc_html( msr_publishing_brand_name() )
		);
		return;
	}

	$logo_id = (int) get_theme_mod( 'custom_logo' );
	if ( ! $logo_id ) {
		echo '<span class="site-logo-wordmark">' . esc_html( msr_publishing_brand_name() ) . '</span>';
		return;
	}
	$mime = get_post_mime_type( $logo_id );
	$url  = wp_get_attachment_image_url( $logo_id, 'full' );
	if ( ! $url ) {
		$url = wp_get_attachment_url( $logo_id );
	}
	if ( ! $url ) {
		echo '<span class="site-title">' . esc_html( get_bloginfo( 'name' ) ) . '</span>';
		return;
	}
	$alt        = get_bloginfo( 'name', 'display' );
	$logo_class = 'custom-logo';
	if ( function_exists( 'msr_publishing_site_logo_needs_invert' ) && msr_publishing_site_logo_needs_invert( $logo_id ) ) {
		$logo_class .= ' custom-logo--on-light';
	}
	if ( $mime && false !== strpos( $mime, 'svg' ) ) {
		printf(
			'<img src="%s" class="%s" alt="%s" width="250" height="60" decoding="async" loading="eager" />',
			esc_url( $url ),
			esc_attr( $logo_class ),
			esc_attr( $alt )
		);
		return;
	}
	echo wp_get_attachment_image(
		$logo_id,
		'full',
		false,
		array(
			'class' => $logo_class,
			'alt'   => $alt,
		)
	);
}

/** @deprecated */
function msrsandbox_render_site_logo() {
	msr_publishing_render_site_logo();
}

/**
 * @param array<string, string> $upload_mimes Mime map.
 * @return array<string, string>
 */
function msr_publishing_enable_svg_upload( $upload_mimes ) {
	$upload_mimes['svg']  = 'image/svg+xml';
	$upload_mimes['svgz'] = 'image/svg+xml';
	return $upload_mimes;
}
add_filter( 'upload_mimes', 'msr_publishing_enable_svg_upload', 10, 1 );

/** @deprecated */
function msrsandbox_enable_svg_upload( $upload_mimes ) {
	return msr_publishing_enable_svg_upload( $upload_mimes );
}
