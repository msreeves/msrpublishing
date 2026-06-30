<?php
/**
 * Legacy demo CPTs (admin-only / non-public): event, publication, advert.
 * Grouped under WP Admin → Legacy demos. See README_LEGACY_CPTS.md.
 * Public publishing IA uses `resource` CPT (inc/msr-publishing-resources.php).
 */
class New_Post_Types {

    public function __construct() {
        add_action( 'init', array( $this, 'all_post_types' ) );
    }
    

    public function all_post_types() {
        $post_types = [
            [
                'post_type' => 'event',
                'label'     => 'Events',
                'singular'  => 'Event',
                'slug'      => 'event',
                'menu_icon' => 'dashicons-calendar',
                'public'    => false,   
                
            ],
             [
                'post_type' => 'publication',
                'label'     => 'Publications',
                'singular'  => 'Publication',
                'menu_icon' => 'dashicons-book',
                'public'    => false, 
                
            ],
               [
                'post_type' => 'advert',
                'label'     => 'Advertising',
                'singular'  => 'Advert',
                'menu_icon' => 'dashicons-format-image',
                'public'    => false, 
            ],

               [
                'post_type' => 'partner',
                'label'     => 'Partnerships',
                'singular'  => 'Partner',
                'slug'      => 'partnership',
                'menu_icon' => 'dashicons-groups',
                'public'    => false,                           
                
            ],

             [
                 'post_type' => 'member',
                 'label'     => 'Members',
                 'singular'  => 'member',
                 'slug'      => 'member',
                 'menu_icon' => 'dashicons-businessman',
                 'orderby'   => 'meta_value',
	             'order'     => 'ASC',
	             'meta_key'  => 'name',
                 'public'    => false,
             ],
        ];

        foreach ($post_types as $key => $post_type) {
            $this -> register_post_type( $post_type );
        }
    }

    private function register_post_type( $data ) {

        $singular  = $data['singular'];
        $label   = $data['label'];
        $plural      = isset( $data['plural'] ) ? $data['plural'] : $label;
        $post_type = $data['post_type'];
        $dashicon  = $data['menu_icon'];
        $trueorfalse  = $data['public'];
        $slug      = isset( $data['slug'] ) ? $data['slug'] : $post_type;

        $labels = array(
            'name'               => _x( $label, 'post type general name', 'sandbox-theme' ),
            'singular_name'      => _x( $singular, 'post type singular name', 'sandbox-theme' ),
            'plural_name'        => _x( $plural, 'post type plural name', 'sandbox-theme' ),
            'menu_name'          => _x( $label, 'admin menu', 'sandbox-theme' ),
            'name_admin_bar'     => _x( $singular, 'add new on admin bar', 'sandbox-theme' ),
            'add_new'            => _x( 'Add New', $singular, 'sandbox-theme' ),
            'add_new_item'       => __( 'Add New ' . $singular, 'sandbox-theme' ),
            'new_item'           => __( 'New ' . $singular, 'sandbox-theme' ),
            'edit_item'          => __( 'Edit ' . $singular, 'sandbox-theme' ),
            'view_item'          => __( 'View ' . $singular, 'sandbox-theme' ),
            'all_items'          => __( 'All ' . $plural, 'sandbox-theme' ),
            'search_items'       => __( 'Search ' . $plural, 'sandbox-theme' ),
            'parent_item_colon'  => __( 'Parent ' . $plural . ':', 'sandbox-theme' ),
            'not_found'          => __( 'No ' . $plural . ' found.', 'sandbox-theme' ),
            'not_found_in_trash' => __( 'No ' . $plural . ' found in Trash.', 'sandbox-theme' )
        );

        $legacy_types = array( 'event', 'publication', 'advert' );
        $show_in_menu = in_array( $post_type, $legacy_types, true ) ? 'msr-legacy-demos' : true;

        $args = array(
            'labels'             => $labels,
            'description'        => __( $singular .'.', 'sandbox-theme' ),
            'public'             => $trueorfalse,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => $show_in_menu,
            'rewrite'            => array( 'slug' => $slug ),
            'capability_type'    => 'post',
            'has_archive'        => false,
            'hierarchical'       => false,
            'menu_position'      => null,
            'supports'           => array( 'title', 'thumbnail'),
            'menu_icon'          => $dashicon,
        );

    register_post_type( $post_type, $args );

            // Register the columns.
    add_filter( "manage_{$post_type}_posts_columns", function ( $defaults ) {
    unset($defaults['date']);
	unset($defaults['author']);
	unset($defaults['comments']);

	return $defaults;
} );
    }

} // End class

new New_Post_Types;

function be_register_taxonomies() {

	$taxonomies = array(
          	array(
			'slug'         => 'location',
			'single_name'  => 'Location',
			'plural_name'  => 'Location',
			'post_type'    => 'advert',
		),
	);

	foreach( $taxonomies as $taxonomy ) {
		$labels = array(
			'name' => $taxonomy['plural_name'],
			'singular_name' => $taxonomy['single_name'],
			'search_items' =>  'Search ' . $taxonomy['plural_name'],
			'all_items' => 'All ' . $taxonomy['plural_name'],
			'parent_item' => 'Parent ' . $taxonomy['single_name'],
			'parent_item_colon' => 'Parent ' . $taxonomy['single_name'] . ':',
			'edit_item' => 'Edit ' . $taxonomy['single_name'],
			'update_item' => 'Update ' . $taxonomy['single_name'],
			'add_new_item' => 'Add New ' . $taxonomy['single_name'],
			'new_item_name' => 'New ' . $taxonomy['single_name'] . ' Name',
			'menu_name' => $taxonomy['plural_name']
		);
		
		$rewrite = isset( $taxonomy['rewrite'] ) ? $taxonomy['rewrite'] : array( 'slug' => $taxonomy['slug'] );
		$hierarchical = isset( $taxonomy['hierarchical'] ) ? $taxonomy['hierarchical'] : true;
	
		register_taxonomy( $taxonomy['slug'], $taxonomy['post_type'], array(
			'hierarchical' => $hierarchical,
			'labels' => $labels,
			'show_ui' => true,
			'query_var' => true,
			'rewrite' => $rewrite,
            'show_admin_column' => true,
		));
	}
	
}
add_action( 'init', 'be_register_taxonomies' );
