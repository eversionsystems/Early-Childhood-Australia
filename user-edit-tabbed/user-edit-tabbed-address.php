<?php
/*
File Name : user-edit-tabbed-address.php
Description: Functions for updating/displaying address fields in a TAB
Version: 1.0
Author: Andrew Schultz
Author URI: http://eversionsystems.com
License: GPL2
*/

/**
* Display
*/

//Add duplicate billing to shipping address functionality
add_action('uet_edit_user_profile_address', 'uet_show_address_fields');

function uet_show_address_fields($user_id) {
	
	if (is_plugin_active(WOOCOMMERCE_PP)) {
		//Output WooCommerce address fields
		$user_info = get_userdata($user_id);
		$wc_admin_profile = new WC_Admin_Profile();
		$wc_admin_profile->add_customer_meta_fields($user_info);
		
	?>
		<table class="form-table">
			<tr>
				<th><label for="duplicate-billing-address">Duplicate Address</label></th>
				<td>
					<input type="checkbox" id="duplicate-billing-address" name="duplicate-billing-address" value="yes">Copy billing to shipping address
				</td>
			</tr>
		</table>
	<?php
	}
	else {
		echo 'Activate Woocommerce to view address details';
	}
}

/**
* Updates
*/

add_action ('uet_edit_user_profile_update_address', 'uet_update_address_fields');

function uet_update_address_fields($user_id) {
	
	if (is_plugin_active(WOOCOMMERCE_PP)) {
	  //Create a new WooCommerce object for profile updating
	  $wc_admin_profile = new WC_Admin_Profile();	
	  $wc_admin_profile->save_customer_meta_fields($user_id);
	}
}