<?php
/**
* Name : 	add-comment-packing-slip.php
* Author : 	Andrew Schultz
* Company : Eversion Systems
* Purpose : Add a comment to the Woocommerce order regarding the packing slip when a packing slip is printed
*			Add two custom columns to show the last comment and an green image if the packing slip has been printed
* Requires : WooCommerce Print Invoice/Packing list v2.4.3
*/

define('PACK_SLIP_TEXT','Packing slip printed');

//Print out the latest comment
add_action('manage_shop_order_posts_custom_column', 'eca_add_custom_order_columns');

function eca_add_custom_order_columns($column) {
	global $post, $woocommerce, $wpdb;
    $data = get_post_meta( $post->ID );
	
	$packing_slip_exists = $wpdb->get_var("SELECT count(*) FROM $wpdb->comments WHERE comment_post_ID=$post->ID AND comment_content='".PACK_SLIP_TEXT."'");
	
	// check the status of the post
	( $post->post_status !== 'trash' ) ? $status = '' : $status = 'post-trashed';

	$latest_notes = get_comments( array(
		'post_id'	=> $post->ID,
		'number'	=> 1,
		'status'	=> $status
	) );

	$latest_note = current( $latest_notes );
	
	if($column == "order_notes_packing_slip") {
		//$comment = $wpdb->get_var("SELECT comment_content FROM $wpdb->comments WHERE comment_post_ID=$post->ID ORDER BY comment_date DESC LIMIT 0,1");

		if ( $post->comment_count == 1 ) {
			echo '<span class="note-on tips" data-tip="' . esc_attr( $latest_note->comment_content ) . '">' . __( 'Yes', 'woocommerce' ) . '</span>';
		} 
		else {
			$note_tip = isset( $latest_note->comment_content ) ? esc_attr( $latest_note->comment_content . '<small style="display:block">' . sprintf( _n( 'plus %d other note', 'plus %d other notes', ( $post->comment_count - 1 ), 'woocommerce' ), ( $post->comment_count - 1 ) ) . '</small>' ) : sprintf( _n( '%d note', '%d notes', $post->comment_count, 'woocommerce' ), $post->comment_count );

			if ($packing_slip_exists > 0)
				echo '<span class="note-on tips packing-slip" data-tip="' . $note_tip . '">' . __( 'Yes', 'woocommerce' ) . '</span>';
			else
				echo '<span class="note-on tips" data-tip="' . $note_tip . '">' . __( 'Yes', 'woocommerce' ) . '</span>';
		}
	}
	else if($column == "packing_slip") {
		$comment_count = $wpdb->get_var("SELECT count(*) FROM $wpdb->comments WHERE comment_post_ID=$post->ID AND comment_content='".PACK_SLIP_TEXT."'");
		if($comment_count > 0)
			echo 'Yes';
	}
	else if ($column == "order_last_note") {
		echo $latest_note->comment_content;
	}
	else if ($column == "ps_printed") {
		$ps_printed = $data['ps_printed'][0];
		
		if($ps_printed == 1)
			echo 'Yes';
	}
	/*
	else if($column == "order_notes") {
		$comment_count = $wpdb->get_var("SELECT count(*) FROM $wpdb->comments WHERE comment_post_ID=$post->ID AND comment_content='".PACK_SLIP_TEXT."'");
		if($comment_count > 0)
			echo 'Here';
	}
	*/
}

//Define custom order view columns
add_filter('manage_edit-shop_order_columns', 'es_edit_order_columns', 20);

function es_edit_order_columns($columns){
	global $woocommerce;
	//unset($columns["order_comments"]);
	unset($columns["order_notes"]);
	$columns['order_last_note'] = __( 'Last Order Note', 'woocommerce' );
	$columns['order_notes_packing_slip'] = '<span class="order-notes_head tips" data-tip="' . esc_attr__( 'Order Notes Packing Slips', 'woocommerce' ) . '">' . esc_attr__( 'Order Notes Packing Slips', 'woocommerce' ) . '</span>';
	$columns['ps_printed'] = __('PS Printed', 'woocommerce');

	return $columns;
}

add_filter( "manage_edit-shop_order_sortable_columns", 'eca_custom_columns_sortable' );

function eca_custom_columns_sortable( $columns ) {
    $custom = array(
        'ps_printed'    => 'ps_printed',
    );
    return wp_parse_args( $custom, $columns );
}

//Hook to add a comment onto a order 
add_action( 'admin_init', 'es_woocommerce_pip_add_comment', 1 );
 
function es_woocommerce_pip_add_comment() {

	if ( isset($_GET['print_pip'] ) ) {
		$nonce = $_REQUEST['_wpnonce'];
		//Only add a comment for print packing slips not invoices
		$print_type = $_REQUEST['type'];

		$client = false;
		// Check that current user has needed access rights.
		if ( ! wp_verify_nonce( $nonce, 'print-pip' ) || ! is_user_logged_in() || woocommerce_pip_user_access() ) die( 'You are not allowed to view this page.' );
			
		//WC_Order::add_order_note('Packing slip printed', 1);
		
		if($print_type === 'print_packing') {
			//Create comment to insert	
			$order_id     = $_GET['post'];
			$order        = new WC_Order( $order_id );
			$user                 = wp_get_current_user();
			$comment_author       = $user->display_name;
			$comment_author_email = $user->user_email;
			$time = current_time('mysql');

			$data = array(
				'comment_post_ID' => $order_id,
				'comment_author' => $comment_author,
				'comment_author_email' => $comment_author_email,
				'comment_content' => PACK_SLIP_TEXT,
				'comment_type' => 'order_note',
				'comment_parent' => 0,
				'user_id' => $user->ID,
				'comment_agent' => 'WooCommerce',
				'comment_date' => $time,
				'comment_approved' => 1,
			);

			wp_insert_comment($data);
			
			//AS - Insert custom meta data to flag packing slip is printed
            update_post_meta( $order_id , 'ps_printed', true );
		}
	}
}

?>