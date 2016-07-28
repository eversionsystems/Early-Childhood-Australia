<?php
/*
* Custom shortcodes for Wordpress
* Author : Andrew Schultz
* Summary : Contains shortcodes we can use in wordpress pages
* Currently not used just leave in case we want to add some shortcodes
*/
	
//Function to launch contact form
function show_contacts_table () {

	//Get current user ID
	$user_id = get_current_user_id();

    // creating an instance of phpDataTable
    //$tbl1 = new PHPDataTable();
	//$tbl1->enableTableTools();
    // building table by query with parameters
    //$tbl1->buildByQuery( 'SELECT * FROM contacts WHERE user_id=?', array($user_id) );
    // printing the table
    //$tbl1->printTable();
	
	//phpinfo();
	echo wdt_output_table( 1 ) ;

}

function eca_show_all_cats() {

$args = array(
	'show_option_all'    => '',
	'orderby'            => 'name',
	'order'              => 'ASC',
	'style'              => 'list',
	'show_count'         => 0,
	'hide_empty'         => 1,
	'use_desc_for_title' => 1,
	'child_of'           => 0,
	'feed'               => '',
	'feed_type'          => '',
	'feed_image'         => '',
	'exclude'            => '',
	'exclude_tree'       => '',
	'include'            => '',
	'hierarchical'       => 1,
	'title_li'           => __( 'Categories' ),
	'show_option_none'   => __( 'No categories' ),
	'number'             => null,
	'echo'               => 1,
	'depth'              => 0,
	'current_category'   => 0,
	'pad_counts'         => 0,
	'taxonomy'           => 'category',
	'walker'             => null
	);
	
	//echo wp_list_categories( $args );
	echo wp_list_categories('orderby=name');
}

//Add a shortcode to display all categories in a wordpress page
//add_shortcode('show_all_cats', 'eca_show_all_cats');

?>