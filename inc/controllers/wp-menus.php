<?php
/**
 * Register navigation menus — single registration (WP admin: Appearance → Menus).
 *
 * @package msrsandbox
 */

/**
 * @return void
 */
function msrsandbox_register_nav_menus() {
	register_nav_menus(
		array(
			'menu-1' => __( 'Primary', 'msrsandbox' ),
			'footer' => __( 'Footer', 'msrsandbox' ),
			'social' => __( 'Social', 'msrsandbox' ),
		)
	);
}
add_action( 'after_setup_theme', 'msrsandbox_register_nav_menus' );

/**
 * Custom walker for primary menu markup (#cssmenu / span wrappers).
 */
class CSS_Menu_Walker extends Walker {

	/** @var array<string, string> */
	public $db_fields = array(
		'parent' => 'menu_item_parent',
		'id'     => 'db_id',
	);

	/**
	 * @param string $output Output.
	 * @param int    $depth  Depth.
	 * @param array  $args   Args.
	 */
	public function start_lvl( &$output, $depth = 0, $args = array() ) {
		$indent  = str_repeat( "\t", $depth );
		$output .= "\n$indent<ul>\n";
	}

	/**
	 * @param string $output Output.
	 * @param int    $depth  Depth.
	 * @param array  $args   Args.
	 */
	public function end_lvl( &$output, $depth = 0, $args = array() ) {
		$indent  = str_repeat( "\t", $depth );
		$output .= "$indent</ul>\n";
	}

	/**
	 * @param string   $output Output.
	 * @param WP_Post  $item   Item.
	 * @param int      $depth  Depth.
	 * @param stdClass $args   Args.
	 * @param int      $id     ID.
	 */
	public function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
		$indent = $depth ? str_repeat( "\t", $depth ) : '';
		$classes = empty( $item->classes ) ? array() : (array) $item->classes;

		if ( in_array( 'current-menu-item', $classes, true ) ) {
			$classes[] = 'active';
		}

		$children = get_posts(
			array(
				'post_type'      => 'nav_menu_item',
				'nopaging'       => true,
				'numberposts'    => 1,
				'meta_key'       => '_menu_item_menu_item_parent',
				'meta_value'     => $item->ID,
			)
		);
		if ( ! empty( $children ) ) {
			$classes[] = 'has-sub';
		}

		$class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args ) );
		$class_names = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';

		$item_id = apply_filters( 'nav_menu_item_id', 'menu-item-' . $item->ID, $item, $args );
		$item_id = $item_id ? ' id="' . esc_attr( $item_id ) . '"' : '';

		$output .= $indent . '<li' . $item_id . $class_names . '>';

		$attributes  = ! empty( $item->attr_title ) ? ' title="' . esc_attr( $item->attr_title ) . '"' : '';
		$attributes .= ! empty( $item->target ) ? ' target="' . esc_attr( $item->target ) . '"' : '';
		$attributes .= ! empty( $item->xfn ) ? ' rel="' . esc_attr( $item->xfn ) . '"' : '';
		$attributes .= ! empty( $item->url ) ? ' href="' . esc_attr( $item->url ) . '"' : '';

		$item_output  = $args->before ?? '';
		$item_output .= '<a' . $attributes . '><span>';
		$item_output .= ( $args->link_before ?? '' ) . apply_filters( 'the_title', $item->title, $item->ID ) . ( $args->link_after ?? '' );
		$item_output .= '</span></a>';
		$item_output .= $args->after ?? '';

		$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
	}

	/**
	 * @param string  $output Output.
	 * @param WP_Post $item   Item.
	 * @param int     $depth  Depth.
	 * @param array   $args   Args.
	 */
	public function end_el( &$output, $item, $depth = 0, $args = array() ) {
		$output .= "</li>\n";
	}
}
