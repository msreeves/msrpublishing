<?php
/**
 * Whitepaper single resource — HubSpot-style preface: preview + takeaways | download rail.
 *
 * @package msrpublishing
 */

$post_id = get_the_ID();

msr_publishing_resource_single_open( 'whitepaper' );
msr_publishing_resource_single_render_chrome( $post_id );
msr_publishing_resource_single_preface_open( 'whitepaper' );
?>
<div class="resource-single__preface-main">
	<?php
	msr_publishing_render_whitepaper_preview( $post_id );
	msr_publishing_render_key_takeaways( $post_id );
	?>
</div>
<div class="resource-single__preface-aside">
	<?php msr_publishing_resource_single_render_download_rail( $post_id ); ?>
</div>
<?php
msr_publishing_resource_single_preface_close();
msr_publishing_resource_single_render_body( $post_id, true );
msr_publishing_resource_single_render_footer();
msr_publishing_resource_single_close();
