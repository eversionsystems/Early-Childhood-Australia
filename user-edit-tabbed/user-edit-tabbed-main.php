<?php
/*
File Name : user-edit-tabbed-main.php
Description: Functions for updating/displaying main fields in a TAB
Version: 1.0
Author: Andrew Schultz
Author URI: http://eversionsystems.com
License: GPL2
*/

/**
* Display
*/

add_action('edit_user_profile_main', 'uet_show_main_fields');

function uet_show_main_fields($user_id) {
	$all_meta_for_user = get_user_meta( $user_id );
	$agency_exists = $all_meta_for_user['agency_account'][0];
	$agency_name = $all_meta_for_user['agency_name'][0];
	$agency_type = $all_meta_for_user['agency_type'][0];
	$agency_user_id = $all_meta_for_user['agency_user_id'][0];
	$email_opt_in = $all_meta_for_user['email_opt_in'][0];;

	?>
	<h3><?php _e('Agency') ?></h3>

	<table class="form-table">
		<tr>
			<th scope="row"><label for="agency_account"><?php _e( 'Is Parent', 'eversion' ) ?></label></th>
			<?php if ($agency_exists == 1)
					echo '<td><label for="agency_account"><input name="agency_account" id="agency_account" value="1" type="checkbox" checked=""></label></td>';
				else
					echo '<td><label for="agency_account"><input name="agency_account" id="agency_account" value="1" type="checkbox"></label></td>';
			?>
		</tr>
		<tr id="agency_name_row">
			<th><label for="agency_name"><?php _e('Name') ?></label></th>
			<td><input type="text" name="agency_name" id="agency_name" value="<?php echo esc_attr($agency_name) ?>" class="regular-text" /></td>
		</tr>
		<tr id="agency_type_row">
			<th scope="row"><label for="agency_type"><?php _e( 'Type', 'eversion' ) ?></label></th>
			<td><label for="agency_type"><select name="agency_type" id="agency_type" >
				<option  <?php if($agency_type == 'Bulk'){echo 'selected="selected"'; }?>  value="Bulk">Bulk</option>
				<option  <?php if($agency_type == 'Agency'){echo 'selected="selected"'; }?>  value="Agency">Agency</option>
				</select></label>
			</td>
		</tr>
		<tr id="parent_agency_row">
		<style type="text/css">
			.select2-container { width:500px}
		</style>
		<th scope="row"><label for="agency_user_id"><?php _e( 'Parent', 'eversion' ) ?></label></th>
			<td>
			<?php
				$user_string = '';
				if ( ! empty( $agency_user_id ) ) {
					$user        = get_user_by( 'id', $agency_user_id );
					$user_string = esc_html( $user->display_name ) . ' (#' . absint( $user->ID ) . ' &ndash; ' . esc_html( $user->user_email );
				}
			?>
			<input type="hidden" class="wc-customer-search" id="agency_user_id" name="agency_user_id" data-placeholder="<?php _e( 'Search User', 'woocommerce' ); ?>" data-selected="<?php echo esc_attr( $user_string ); ?>" value="<?php echo $agency_user_id; ?>" data-allow_clear="true" />
			</td>
		</tr>
	</table>
	
	<h3><?php _e('Email') ?></h3>

	<table class="form-table">
		<tr>
			<th scope="row"><label for="email_opt_in"><?php _e( 'Email Opt-In', 'eversion' ) ?></label></th>
			<?php if ($email_opt_in == 'on')
					echo '<td><label for="email_opt_in"><input name="email_opt_in" id="email_opt_in" type="checkbox" checked=""></label></td>';
				else
					echo '<td><label for="email_opt_in"><input name="email_opt_in" id="email_opt_in" type="checkbox"></label></td>';
			?>
		</tr>
	</table>
	
	<?php
}


/**
* Update
*/

add_action('edit_user_profile_update_main', 'uet_update_main_fields');

function uet_update_main_fields($user_id) {
	if(isset($_POST['agency_account']))
		update_user_meta($user_id, 'agency_account', $_POST['agency_account']);
	else
		update_user_meta($user_id, 'agency_account', '');
	if(isset($_POST['agency_name']))
		update_user_meta($user_id, 'agency_name', $_POST['agency_name']);
	if(isset($_POST['agency_type']))
		update_user_meta($user_id, 'agency_type', $_POST['agency_type']);
	if(isset($_POST['agency_user_id']))
		update_user_meta($user_id, 'agency_user_id', $_POST['agency_user_id']);
	if(isset($_POST['email_opt_in']))
		update_user_meta($user_id, 'email_opt_in', $_POST['email_opt_in']);
	else
		update_user_meta($user_id, 'email_opt_in', 'off');
}

?>