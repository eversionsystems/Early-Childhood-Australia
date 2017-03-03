<?php
/*
File Name : user-edit-tabbed-notes.php
Description: Functions for updating/displaying notes fields in a TAB
Version: 1.0
Author: Andrew Schultz
Author URI: http://eversionsystems.com
License: GPL2
*/

/**
* Display
*/

add_action('uet_edit_user_profile_notes', 'uet_show_notes_fields');

function uet_show_notes_fields($user_id) {
	$account_notes = get_user_meta($user_id, 'account_notes', true);
	
	?>
	<table class="form-table">
	<tbody>
	<tr>
		<th><label for="account_notes">Account Notes</label></th>
	<td>
		<textarea name="account_notes" id="account_notes" cols="80" rows="10"><?php echo $account_notes; ?></textarea>
	</td>
	</tr>
	</tbody>
	</table>
	<?php
}

/**
* Updates
*/

add_action('uet_edit_user_profile_update_notes', 'uet_update_notes_fields');

function uet_update_notes_fields($user_id) {
	update_user_meta($user_id, 'account_notes', $_POST['account_notes']);
}


?>