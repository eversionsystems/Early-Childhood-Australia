<?php
/*
File Name : user-edit-tabbed-general.php
Description: Functions for updating/displaying general fields in a TAB
Version: 1.0
Author: Andrew Schultz
Author URI: http://eversionsystems.com
License: GPL2
*/

/**
* Display
*/

add_action('uet_edit_user_profile_general', 'uet_show_general_fields');

function uet_show_general_fields($user_id) {
	
	$old_customer_number = get_user_meta($user_id, 'old_customer_number', true);
	$my_child_id = get_user_meta($user_id, 'my_child_id', true);
	$discount_tickets_number = get_user_meta($user_id, 'discount_tickets_number', true);
	$free_tickets_number = get_user_meta($user_id, 'free_tickets_number', true);
	
	?>
	<h3>Links</h3>
	<table class="form-table">
	<tbody>
	<tr>
		<th><label for="old_customer_number">Old Customer Number</label></th>
		<td><input name="old_customer_number" id="old_customer_number" value="<?php echo $old_customer_number;?>" class="regular-text" type="text" style="width:120px;"></td>
	</tr>
	<tr>
		<th><label for="my_child_id">My Child ID</label></th>
		<td><input style="width:120px;" name="my_child_id" id="my_child_id" value="<?php echo $my_child_id;?>" class="regular-text" type="text"></td>
	</tr>
	</tbody>
	</table>
	<?php if (is_plugin_active('event-espresso-core-reg/espresso.php')) { ?>
	<h3>Events</h3>
	<table class="form-table">
	<tbody>
	<tr>
		<th><label for="discount_tickets_number">Number Discount Tickets</label></th>
		<td><input name="discount_tickets_number" id="discount_tickets_number" value="<?php echo $discount_tickets_number;?>" class="regular-text" type="text" style="width:40px;"></td>
	</tr>
	<tr>
		<th><label for="free_tickets_number">Number Complimentary Tickets</label></th>
		<td><input name="free_tickets_number" id="free_tickets_number" value="<?php echo $free_tickets_number;?>" class="regular-text" type="text" style="width:40px;"></td>
	</tr>
	<?php } ?>
	</tbody>
	</table>
	<?php
}

/**
* Updates
*/

add_action ('uet_edit_user_profile_update_general', 'uet_update_general_fields');

function uet_update_general_fields($user_id) {
	
	update_user_meta($user_id, 'my_child_id', $_POST['my_child_id']);
    update_user_meta($user_id, 'old_customer_number', $_POST['old_customer_number']);
	update_user_meta($user_id, 'discount_tickets_number', $_POST['discount_tickets_number']);
	update_user_meta($user_id, 'free_tickets_number', $_POST['free_tickets_number']);
}

?>