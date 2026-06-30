<?php
/**
 * Block editor support: patterns, Classic Editor coexistence for publishing CPTs.
 *
 * @package msrsandbox
 */

/**
 * Enable block editor for Atlas Briefing editorial types while Classic Editor remains for legacy CPTs.
 *
 * @param bool   $use       Whether the block editor is used.
 * @param string $post_type Post type slug.
 * @return bool
 */
function msr_publishing_use_block_editor_for_types( $use, $post_type ) {
	if ( in_array( $post_type, array( 'resource', 'post' ), true ) ) {
		return true;
	}

	return $use;
}
add_filter( 'use_block_editor_for_post_type', 'msr_publishing_use_block_editor_for_types', 101, 2 );

/**
 * @return void
 */
function msr_publishing_register_block_pattern_category() {
	register_block_pattern_category(
		'msr-publishing',
		array(
			'label' => __( 'Atlas Briefing', 'msrsandbox' ),
		)
	);
}
add_action( 'init', 'msr_publishing_register_block_pattern_category', 9 );

/**
 * @return void
 */
function msr_publishing_register_block_patterns() {
	$subscribe_url   = home_url( '/subscribe/' );
	$resources_url   = home_url( '/resources/' );
	$subscribe_blurb = esc_html( msr_publishing_get_block_pattern_subscribe_text() );
	$resource_blurb  = esc_html( msr_publishing_get_block_pattern_resource_text() );
	$hub_blurb       = esc_html( msr_publishing_get_block_pattern_hub_hero_text() );

	register_block_pattern(
		'msrsandbox/subscribe-band',
		array(
			'title'       => __( 'Subscribe band', 'msrsandbox' ),
			'description' => __( 'Newsletter signup band with Atlas Briefing copy.', 'msrsandbox' ),
			'categories'  => array( 'msr-publishing', 'call-to-action' ),
			'content'     => '<!-- wp:group {"align":"full","style":{"color":{"background":"#f3efe8"},"spacing":{"padding":{"top":"2.5rem","bottom":"2.5rem","left":"1.5rem","right":"1.5rem"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull has-background" style="background-color:#f3efe8;padding-top:2.5rem;padding-right:1.5rem;padding-bottom:2.5rem;padding-left:1.5rem"><!-- wp:heading {"textAlign":"center","level":2,"fontFamily":"display"} -->
<h2 class="wp-block-heading has-text-align-center has-display-font-family">Stay briefed</h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center","textColor":"text-secondary"} -->
<p class="has-text-align-center has-text-secondary-color has-text-color">' . $subscribe_blurb . '</p>
<!-- /wp:paragraph -->

<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"}} -->
<div class="wp-block-buttons"><!-- wp:button {"backgroundColor":"accent"} -->
<div class="wp-block-button"><a class="wp-block-button__link has-accent-background-color has-background wp-element-button" href="' . esc_url( $subscribe_url ) . '">Subscribe to Atlas Briefing</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:group -->',
		)
	);

	register_block_pattern(
		'msrsandbox/resource-cta',
		array(
			'title'       => __( 'Resource library CTA', 'msrsandbox' ),
			'description' => __( 'Call-to-action band linking to the resource archive.', 'msrsandbox' ),
			'categories'  => array( 'msr-publishing', 'call-to-action' ),
			'content'     => '<!-- wp:group {"align":"wide","style":{"border":{"width":"1px","color":"#ddd5c8","radius":"2px"},"spacing":{"padding":{"top":"2rem","bottom":"2rem","left":"2rem","right":"2rem"}}},"backgroundColor":"surface","layout":{"type":"constrained"}} -->
<div class="wp-block-group alignwide has-border-color has-surface-background-color has-background" style="border-color:#ddd5c8;border-width:1px;border-radius:2px;padding-top:2rem;padding-right:2rem;padding-bottom:2rem;padding-left:2rem"><!-- wp:columns {"verticalAlignment":"center"} -->
<div class="wp-block-columns are-vertically-aligned-center"><!-- wp:column {"verticalAlignment":"center"} -->
<div class="wp-block-column is-vertically-aligned-center"><!-- wp:heading {"level":3,"fontFamily":"display"} -->
<h3 class="wp-block-heading has-display-font-family">Explore the resource library</h3>
<!-- /wp:heading -->

<!-- wp:paragraph {"textColor":"text-secondary"} -->
<p class="has-text-secondary-color has-text-color">' . $resource_blurb . '</p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column {"verticalAlignment":"center"} -->
<div class="wp-block-column is-vertically-aligned-center"><!-- wp:buttons {"layout":{"type":"flex","justifyContent":"right"}} -->
<div class="wp-block-buttons"><!-- wp:button {"backgroundColor":"accent"} -->
<div class="wp-block-button"><a class="wp-block-button__link has-accent-background-color has-background wp-element-button" href="' . esc_url( $resources_url ) . '">Browse resources</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div>
<!-- /wp:group -->',
		)
	);

	register_block_pattern(
		'msrsandbox/hub-hero',
		array(
			'title'       => __( 'Hub hero', 'msrsandbox' ),
			'description' => __( 'Editorial hero for Atlas Briefing hub pages.', 'msrsandbox' ),
			'categories'  => array( 'msr-publishing', 'banner' ),
			'content'     => '<!-- wp:group {"align":"full","style":{"color":{"background":"#f8f5f0"},"spacing":{"padding":{"top":"3rem","bottom":"3rem","left":"1.5rem","right":"1.5rem"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull has-background" style="background-color:#f8f5f0;padding-top:3rem;padding-right:1.5rem;padding-bottom:3rem;padding-left:1.5rem"><!-- wp:heading {"textAlign":"center","level":1,"fontFamily":"display","textColor":"ink"} -->
<h1 class="wp-block-heading has-text-align-center has-ink-color has-text-color has-display-font-family">Atlas Briefing</h1>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center","textColor":"text-secondary"} -->
<p class="has-text-align-center has-text-secondary-color has-text-color">' . $hub_blurb . '</p>
<!-- /wp:paragraph -->

<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"}} -->
<div class="wp-block-buttons"><!-- wp:button {"backgroundColor":"accent"} -->
<div class="wp-block-button"><a class="wp-block-button__link has-accent-background-color has-background wp-element-button" href="' . esc_url( $resources_url ) . '">Resource library</a></div>
<!-- /wp:button -->

<!-- wp:button {"className":"is-style-outline","textColor":"accent"} -->
<div class="wp-block-button is-style-outline"><a class="wp-block-button__link has-accent-color has-text-color wp-element-button" href="' . esc_url( $subscribe_url ) . '">Subscribe</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:group -->',
		)
	);
}
add_action( 'init', 'msr_publishing_register_block_patterns', 10 );
