<?php
/**
* Name : debug-functions.php
* Author : Andrew Schultz
* Purpose : Used for debugging things in wordpress
*/

//Print out content after a page
add_filter('the_content', 'add_test');

function add_test($content) {
	global $post;
	global $current_user;
	
	$slug = get_post( $post )->post_name;
	if ($slug == 'my-account') {
		$user_array = array(
			'user_id' => null,
			'user_login' => null,
			'user_email' => null,
			'format' => 'list',
			'list_class' => 'groups',
			'item_class' => 'name',
			'order_by' => 'name',
			'order' => 'ASC'
			);
		//$content .= 'BLAH';
		$content .= do_shortcode_func( 'groups_user_groups', $user_array ); 
		
		get_currentuserinfo();
		$content .= 'User ID: ' . $current_user->ID . "\n";
		//$content .= wdt_output_table( 1);
		$upload_dir = wp_upload_dir();
		$content .= $upload_dir['url'];
	}
	
	return $content;
}

/* Debug Functions Print Array */
function print_readable_array($array) {
	echo '<pre>';
	print_r($array);
	echo '</pre>';
}

?>