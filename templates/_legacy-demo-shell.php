<?php
/**
 * Shared shell for legacy demo page templates (redirect handled in legacy-guard).
 *
 * @package msrpublishing
 */

get_header();

$archive = get_post_type_archive_link( 'resource' );
if ( ! $archive ) {
	$archive = home_url( '/' );
}
?>
<main id="site-content" class="container py-5">
	<p><?php esc_html_e( 'This demo page has moved to the resource library.', 'msrpublishing' ); ?></p>
	<p><a class="btn btn-primary" href="<?php echo esc_url( $archive ); ?>"><?php esc_html_e( 'Browse resources', 'msrpublishing' ); ?></a></p>
</main>
<?php
get_footer();
