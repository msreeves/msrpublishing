<?php
/**
 * Search result card — resource or commentary (`post-card` / `resource-card`).
 *
 * @package msrsandbox
 */

$post_type = get_post_type();

if ( ! in_array( $post_type, array( 'post', 'resource' ), true ) ) {
	return;
}
?>
<div class="col-md-6 col-lg-4">
	<?php
	if ( 'resource' === $post_type ) {
		get_template_part( 'template-parts/cards/resource', 'card' );
	} else {
		get_template_part( 'template-parts/cards/post', 'card' );
	}
	?>
</div>
