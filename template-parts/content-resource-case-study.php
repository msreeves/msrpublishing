<?php
/**
 * Case study — outcome metrics + three-act narrative (Instant Press-style).
 *
 * @package msrpublishing
 */

$post_id = get_the_ID();
msr_publishing_resource_single_open( 'case-study' );
msr_publishing_resource_single_render_chrome( $post_id );
msr_publishing_resource_single_preface_open( 'case-study' );
?>
<div class="resource-case-study__summary-band">
	<?php
	msr_publishing_render_case_study_industry( $post_id );
	msr_publishing_render_case_study_metrics( $post_id );
	?>
</div>
<?php
msr_publishing_render_case_study_narrative( $post_id );
msr_publishing_render_key_takeaways( $post_id );
if ( ! msr_publishing_case_study_has_psr_content( $post_id ) ) {
	msr_publishing_render_resource_format_preview(
		$post_id,
		__( 'At a glance', 'msrpublishing' )
	);
}
msr_publishing_resource_single_preface_close();
msr_publishing_resource_single_render_body( $post_id, false );
msr_publishing_resource_single_render_footer();
msr_publishing_resource_single_close();
