<?php
/**
* Name : 	add-comment-packing-slip.php
* Author : 	Andrew Schultz
* Company : Eversion Systems
* Purpose : Add a comment to the Woocommerce order regarding the packing slip when a packing slip is printed
*			Add two custom columns to show the last comment and an green image if the packing slip has been printed
*			Add custom comment type order_note to Comment menu
* Requires : WooCommerce Print Invoice/Packing list v2.4.3
*/

define('PACK_SLIP_TEXT','Packing slip printed');

//Print out the latest comment
add_action('manage_shop_order_posts_custom_column', 'eca_add_custom_order_columns');

function eca_add_custom_order_columns($column) {
	global $post, $woocommerce, $wpdb;
    $data = get_post_meta( $post->ID );
	
	// check the status of the post
	( $post->post_status !== 'trash' ) ? $status = '' : $status = 'post-trashed';

	$latest_notes = get_comments( array(
		'post_id'	=> $post->ID,
		'number'	=> 1,
		'status'	=> $status
	) );

	$latest_note = current( $latest_notes );

	if ($column == "order_last_note") {
		echo $latest_note->comment_content;
	}
}

//Define custom order view columns
add_filter('manage_edit-shop_order_columns', 'es_edit_order_columns', 20);

function es_edit_order_columns($columns){
	global $woocommerce;

	unset($columns["order_notes"]);
	$columns['order_last_note'] = __( 'Last Order Note', 'woocommerce' );

	return $columns;
}

add_filter( "manage_edit-shop_order_sortable_columns", 'eca_custom_columns_sortable' );

function eca_custom_columns_sortable( $columns ) {
    $custom = array(
        'ps_printed'    => 'ps_printed',
    );
    return wp_parse_args( $custom, $columns );
}

?>