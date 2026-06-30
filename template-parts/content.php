<?php
/**
 * Fallback post template part — delegates to publishing post card.
 *
 * @package msrsandbox
 */

if ( is_singular( 'post' ) ) {
	get_template_part( 'template-parts/content', 'post' );
	return;
}
?>
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<?php get_template_part( 'template-parts/cards/post', 'card' ); ?>
</article>
