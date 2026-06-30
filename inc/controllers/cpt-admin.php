<?php 
$post_type = 'event';

// Register the columns.
add_filter( "manage_{$post_type}_posts_columns", function ( $defaults ) {
	
  $defaults['title'] = 'Name';
  $defaults['dates'] = 'Dates';
  $defaults['venue'] = 'Venue';

	return $defaults;
} );

// Handle the value for each of the new columns.
add_action( "manage_{$post_type}_posts_custom_column", function ( $column_name, $post_id ) {
	
	if ( $column_name == 'title' ) {
		echo get_title( $post_id, true);
	}

   if ( $column_name == 'dates' ) {
		 echo (new DateTime(get_post_meta($post_id,'date_start',true)))->format('F j, Y');
	  $finish = get_post_meta($post_id,'date_finish',true);
	 	if (empty($finish)) {
		echo '';
		} 
		else {
		echo '<br>';
		echo (new DateTime($finish))->format('F j, Y');
		}
	}

  if ( $column_name == 'venue' ) {
		echo get_post_meta( $post_id, 'venue_name', true);
	}
	
}, 10, 2 );

$post_type = 'member';

// Register the columns.
add_filter( "manage_{$post_type}_posts_columns", function ( $defaults ) {
	
	$defaults['job-title'] = 'Job Title';
    unset($defaults['date']);
	unset($defaults['comments']);

	return $defaults;
} );

// Handle the value for each of the new columns.
add_action( "manage_{$post_type}_posts_custom_column", function ( $column_name, $post_id ) {
	if ( $column_name == 'job-title' ) {
		echo get_field( 'job_title', $post_id );
	}
	
	if ( $column_name == 'company' ) {
		echo get_field( 'company', $post_id );
	}
	
}, 10, 2 );

function ig_sort_member_name( $query ){
    global $pagenow;
    
    if( is_admin() && 'edit.php' == $pagenow && !isset( $_GET['orderby'] ) ):
        if( $_GET['post_type'] == 'member' && !isset( $_GET['post_status'] ) ):

            $query->set( 'meta_key', 'name' );
            $query->set( 'orderby', 'meta_value' );
            $query->set( 'order', 'ASC' );
        endif;
            
    endif;
}
add_action( 'pre_get_posts', 'ig_sort_member_name' );

function ig_sort_partner_name( $query ){
    global $pagenow;
    
    if( is_admin() && 'edit.php' == $pagenow && !isset( $_GET['orderby'] ) ):
        if( $_GET['post_type'] == 'partner' && !isset( $_GET['post_status'] ) ):

            $query->set( 'orderby', 'title' );
            $query->set( 'order', 'ASC' );
        endif;
            
    endif;
}
add_action( 'pre_get_posts', 'ig_sort_partner_name' );