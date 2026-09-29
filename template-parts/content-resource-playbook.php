<?php
/**
 * Playbook single — checklist preface + longform body with sticky TOC.
 *
 * @package msrpublishing
 */

$post_id = get_the_ID();
msr_publishing_resource_single_open( 'playbook' );
msr_publishing_resource_single_render_chrome( $post_id );
msr_publishing_resource_single_preface_open( 'playbook' );
?>
<div class="resource-single__playbook-intro">
	<?php
	msr_publishing_render_resource_format_preview(
		$post_id,
		__( 'In this playbook', 'msrpublishing' )
	);
	msr_publishing_render_key_takeaways( $post_id );
	?>
</div>
<?php
msr_publishing_resource_single_preface_close();
msr_publishing_resource_single_render_body( $post_id, true );
msr_publishing_resource_single_render_footer();
msr_publishing_resource_single_close();
