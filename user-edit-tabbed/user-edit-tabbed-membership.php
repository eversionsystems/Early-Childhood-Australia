<?php
/*
File Name : user-edit-tabbed-membership.php
Description: Functions for updating/displaying membership fields in a TAB
Version: 1.0
Author: Andrew Schultz
Author URI: http://eversionsystems.com
License: GPL2
*/

/**
* Display
*/

add_action('uet_edit_user_profile_membership', 'uet_show_membership_fields');

function uet_show_membership_fields($user_id) {
	
	//Get complimentary field values
	$life_memb = get_user_meta($user_id, 'life_memb', true);
	$life_memb_start_dtm = get_user_meta($user_id, 'life_memb_start_dtm', true);
	$comp_memb = get_user_meta($user_id, 'comp_memb', true);
	$comp_memb_end_dtm = get_user_meta($user_id, 'comp_memb_end_dtm', true);
	$comp_memb_AJEC = get_user_meta($user_id, 'comp_memb_AJEC', true);
	$comp_memb_AJEC_end_dtm = get_user_meta($user_id, 'comp_memb_AJEC_end_dtm', true);
	$comp_memb_EC = get_user_meta($user_id, 'comp_memb_EC', true);
	$comp_memb_EC_end_dtm = get_user_meta($user_id, 'comp_memb_EC_end_dtm', true);
	$comp_memb_EDL = get_user_meta($user_id, 'comp_memb_EDL', true);
	$comp_memb_EDL_end_dtm = get_user_meta($user_id, 'comp_memb_EDL_end_dtm', true);
	$comp_memb_RIP = get_user_meta($user_id, 'comp_memb_RIP', true);
	$comp_memb_RIP_end_dtm = get_user_meta($user_id, 'comp_memb_RIP_end_dtm', true);
	
	//Not renewing values
	$not_renew_memb = get_user_meta($user_id, 'not_renew_memb', true);
	$not_renew_AJEC = get_user_meta($user_id, 'not_renew_AJEC', true);
	$not_renew_EC = get_user_meta($user_id, 'not_renew_EC', true);
	$not_renew_EDL = get_user_meta($user_id, 'not_renew_EDL', true);
	$not_renew_RIP = get_user_meta($user_id, 'not_renew_RIP', true);
	$not_renew_memb_end_dtm = get_user_meta($user_id, 'not_renew_memb_end_dtm', true);
	$not_renew_AJEC_end_dtm = get_user_meta($user_id, 'not_renew_AJEC_end_dtm', true);
	$not_renew_EC_end_dtm = get_user_meta($user_id, 'not_renew_EC_end_dtm', true);
	$not_renew_EDL_end_dtm = get_user_meta($user_id, 'not_renew_EDL_end_dtm', true);
	$not_renew_RIP_end_dtm = get_user_meta($user_id, 'not_renew_RIP_end_dtm', true);
	
	//WooCommerce Groups Plugin
	if(is_plugin_active(GROUPS_WOOCOMM_PP)) {
		$user_info = get_userdata($user_id);
		$groups_ws_user = new Groups_WS_User();
		//Function below is causing display issues
		//Tried jquery below to remove DIV's but hasn't helped
		//Try a modified version of function groups_woocommerce_subscriptions_table and 
		//remove the DIV elements it outputs
		//$groups_ws_user->show_user_profile($user_info);

		uet_show_subscriptions($user_info);
		
		//$subscriptions = WC_Subscriptions_Manager::get_users_subscriptions( $user_id );
		//Show list of subscription names
		//echo WC_Subscriptions_Admin::do_subscriptions_shortcode(array('user_id'=>$user_id, 'status'  => 'active'));
	}
	
	//Groups plugin
	if (is_plugin_active(GROUPS_PP)) {
		 $user_info = get_userdata($user_id);
		//Create a new Groups object for displaying user data
		//$groups_user_profile = new Groups_Admin_User_Profile();
		//$groups_user_profile->edit_user_profile($user_info);
		uet_edit_user_profile($user_info);
	}
	
	$member_exists = uet_is_user_member($user_id);
	$user_has_subscription = uet_is_user_a_subscriber($user_id);
	
	if ( $member_exists ) : ?>
	<h3>Membership Options</h3>
	<table class="form-table">
	<tbody>
	<tr>
	<th><label for="life_memb">Life Member</label></th>
	<td width="22%">
	<?php
		if ($life_memb == 'on' OR isset($_POST['life_memb']))
			echo '<input type="checkbox" name="life_memb" id="life_memb" checked="">';
		else
			echo '<input type="checkbox" name="life_memb" id="life_memb" >';
	?>
	</td>
	<th><label for="life_memb_start_dtm">Start Date</label></th>
	<td>
		<script type="text/javascript">
		$j(document).ready(function(){
			$j('#life_memb_start_dtm').datepicker('setDate', '<?php if (isset($_POST['life_memb_start_dtm'])) { echo $_POST['life_memb_start_dtm']; } else { echo $life_memb_start_dtm;} ?>');
		});
		</script>
		<input type="text" name="life_memb_start_dtm" id="life_memb_start_dtm">
	</td>
	</tr>
	<tr>
	<th><label for="not_renew_memb">Not Renewing Member</label></th>
	<td>
	<?php
		if ($not_renew_memb == 'on' OR isset($_POST['not_renew_memb']))
			echo '<input type="checkbox" name="not_renew_memb" id="not_renew_memb" checked="">';
		else
			echo '<input type="checkbox" name="not_renew_memb" id="not_renew_memb" >';
	?>
	</td>
	<th><label for="not_renew_memb_end_dtm">End Date</label></th>
	<td>
		<script type="text/javascript">
		$j(document).ready(function(){
			$j('#not_renew_memb_end_dtm').datepicker('setDate', '<?php if(isset($_POST['not_renew_memb_end_dtm'])) { echo $_POST['not_renew_memb_end_dtm']; } else {  echo $not_renew_memb_end_dtm; } ?>');
		});
		</script>
		<input type="text" name="not_renew_memb_end_dtm" id="not_renew_memb_end_dtm">
	</td>
	</tr>
	</tbody>
	</table>
	
	<?php endif; ?>
	
	<h3>Complimentary</h3>
	<table class="form-table">
	<tbody>
	<tr>
	<th><label for="comp_memb">Complimentary Membership</label></th>
	<td>
	<?php
		if ($comp_memb == 'on' OR isset($_POST['comp_memb']))
			echo '<input type="checkbox" name="comp_memb" id="comp_memb" checked="">';
		else
			echo '<input type="checkbox" name="comp_memb" id="comp_memb" >';
	?>
	</td>
	<th><label for="comp_memb_end_dtm">End Date</label></th>
	<td>
		<script type="text/javascript">
		$j(document).ready(function(){
			$j('#comp_memb_end_dtm').datepicker('setDate', '<?php if(isset($_POST['comp_memb_end_dtm'])) { echo $_POST['comp_memb_end_dtm']; } else {  echo $comp_memb_end_dtm; }?>');
		});
		</script>
		<input type="text" name="comp_memb_end_dtm" id="comp_memb_end_dtm">
	</td>
	</tr>
	
	<tr>
	<th><label for="comp_memb_AJEC">Complimentary AJEC</label></th>
	<td>
	<?php
		if ($comp_memb_AJEC == 'on' OR isset($_POST['comp_memb_AJEC']))
			echo '<input type="checkbox" name="comp_memb_AJEC" id="comp_memb_AJEC" checked="">';
		else
			echo '<input type="checkbox" name="comp_memb_AJEC" id="comp_memb_AJEC" >';
	?>
	</td>
	<th><label for="comp_memb_AJEC_end_dtm">End Date</label></th>
	<td>
		<script type="text/javascript">
		$j(document).ready(function(){
			$j('#comp_memb_AJEC_end_dtm').datepicker('setDate', '<?php if(isset($_POST['comp_memb_AJEC_end_dtm'])) { echo $_POST['comp_memb_AJEC_end_dtm']; } else {  echo $comp_memb_AJEC_end_dtm; } ?>');
		});
		</script>
		<input type="text" name="comp_memb_AJEC_end_dtm" id="comp_memb_AJEC_end_dtm">
	</td>
	</tr>
	
	<tr>
	<th><label for="comp_memb_EC">Complimentary EC</label></th>
	<td>
	<?php
		if ($comp_memb_EC == 'on' OR isset($_POST['comp_memb_EC']))
			echo '<input type="checkbox" name="comp_memb_EC" id="comp_memb_EC" checked="">';
		else
			echo '<input type="checkbox" name="comp_memb_EC" id="comp_memb_EC" >';
	?>
	</td>
	<th><label for="comp_memb_EC_end_dtm">End Date</label></th>
	<td>
		<script type="text/javascript">
		$j(document).ready(function(){
			$j('#comp_memb_EC_end_dtm').datepicker('setDate', '<?php if(isset($_POST['comp_memb_EC_end_dtm'])) { echo $_POST['comp_memb_EC_end_dtm']; } else {  echo $comp_memb_EC_end_dtm; } ?>');
		});
		</script>
		<input type="text" name="comp_memb_EC_end_dtm" id="comp_memb_EC_end_dtm">
	</td>
	</tr>
	
	<tr>
	<th><label for="comp_memb_EDL">Complimentary EDL</label></th>
	<td>
	<?php
		if ($comp_memb_EDL == 'on' OR isset($_POST['comp_memb_EDL']))
			echo '<input type="checkbox" name="comp_memb_EDL" id="comp_memb_EDL" checked="">';
		else
			echo '<input type="checkbox" name="comp_memb_EDL" id="comp_memb_EDL" >';
	?>
	</td>
	<th><label for="comp_memb_EDL_end_dtm">End Date</label></th>
	<td>
		<script type="text/javascript">
		$j(document).ready(function(){
			$j('#comp_memb_EDL_end_dtm').datepicker('setDate', '<?php if(isset($_POST['comp_memb_EDL_end_dtm'])) { echo $_POST['comp_memb_EDL_end_dtm']; } else {  echo $comp_memb_EDL_end_dtm; }  ?>');
		});
		</script>
		<input type="text" name="comp_memb_EDL_end_dtm" id="comp_memb_EDL_end_dtm">
	</td>
	</tr>
	
	<tr>
	<th><label for="comp_memb_RIP">Complimentary RIP</label></th>
	<td>
	<?php
		if ($comp_memb_RIP == 'on' OR isset($_POST['comp_memb_RIP']))
			echo '<input type="checkbox" name="comp_memb_RIP" id="comp_memb_RIP" checked="">';
		else
			echo '<input type="checkbox" name="comp_memb_RIP" id="comp_memb_RIP" >';
	?>
	</td>
	<th><label for="comp_memb_RIP_end_dtm">End Date</label></th>
	<td>
		<script type="text/javascript">
		$j(document).ready(function(){
			$j('#comp_memb_RIP_end_dtm').datepicker('setDate', '<?php if(isset($_POST['comp_memb_RIP_end_dtm'])) { echo $_POST['comp_memb_RIP_end_dtm']; } else {  echo $comp_memb_RIP_end_dtm; } ?>');
		});
		</script>
		<input type="text" name="comp_memb_RIP_end_dtm" id="comp_memb_RIP_end_dtm">
	</td>
	</tr>
	<tr>
	<th><label for="reset_comp_memb">Reset Complimentary</label></th>
	</th>
	<td><button type="button" name="reset_comp_memb" id="reset_comp_memb" class="button button-primary">Reset</button>
	</td>
	</tr>
	</tbody>
	</table>
	
	<?php if($user_has_subscription) : ?>
	
	<h3>Not Renewing</h3>
	<table class="form-table">
	<tbody>
	<tr>
	<th><label for="not_renew_AJEC">Not Renewing AJEC</label></th>
	<td>
	<?php
		if ($not_renew_AJEC == 'on' OR isset($_POST['not_renew_AJEC']))
			echo '<input type="checkbox" name="not_renew_AJEC" id="not_renew_AJEC" checked="">';
		else
			echo '<input type="checkbox" name="not_renew_AJEC" id="not_renew_AJEC" >';
	?>
	</td>
	<th><label for="not_renew_AJEC_end_dtm">End Date</label></th>
	<td>
		<script type="text/javascript">
		$j(document).ready(function(){
			$j('#not_renew_AJEC_end_dtm').datepicker('setDate', '<?php if(isset($_POST['not_renew_AJEC_end_dtm'])) { echo $_POST['not_renew_AJEC_end_dtm']; } else {  echo $not_renew_AJEC_end_dtm; } ?>');
		});
		</script>
		<input type="text" name="not_renew_AJEC_end_dtm" id="not_renew_AJEC_end_dtm">
	</td>
	</tr>
	
	<tr>
	<th><label for="not_renew_EC">Not Renewing EC</label></th>
	<td>
	<?php
		if ($not_renew_EC == 'on' OR isset($_POST['not_renew_EC']))
			echo '<input type="checkbox" name="not_renew_EC" id="not_renew_EC" checked="">';
		else
			echo '<input type="checkbox" name="not_renew_EC" id="not_renew_EC" >';
	?>
	</td>
	<th><label for="not_renew_EC_end_dtm">End Date</label></th>
	<td>
		<script type="text/javascript">
		$j(document).ready(function(){
			$j('#not_renew_EC_end_dtm').datepicker('setDate', '<?php if(isset($_POST['not_renew_EC_end_dtm'])) { echo $_POST['not_renew_EC_end_dtm']; } else {  echo $not_renew_EC_end_dtm; } ?>');
		});
		</script>
		<input type="text" name="not_renew_EC_end_dtm" id="not_renew_EC_end_dtm">
	</td>
	</tr>
	
	<tr>
	<th><label for="not_renew_EDL">Not Renewing EDL</label></th>
	<td>
	<?php
		if ($not_renew_EDL == 'on' OR isset($_POST['not_renew_EDL']))
			echo '<input type="checkbox" name="not_renew_EDL" id="not_renew_EDL" checked="">';
		else
			echo '<input type="checkbox" name="not_renew_EDL" id="not_renew_EDL" >';
	?>
	</td>
	<th><label for="not_renew_EDL_end_dtm">End Date</label></th>
	<td>
		<script type="text/javascript">
		$j(document).ready(function(){
			$j('#not_renew_EDL_end_dtm').datepicker('setDate', '<?php if(isset($_POST['not_renew_EDL_end_dtm'])) { echo $_POST['not_renew_EDL_end_dtm']; } else {  echo $not_renew_EDL_end_dtm; } ?>');
		});
		</script>
		<input type="text" name="not_renew_EDL_end_dtm" id="not_renew_EDL_end_dtm">
	</td>
	</tr>
	
	<tr>
	<th><label for="not_renew_RIP">Not Renewing RIP</label></th>
	<td>
	<?php
		if ($not_renew_RIP == 'on' OR isset($_POST['not_renew_RIP']))
			echo '<input type="checkbox" name="not_renew_RIP" id="not_renew_RIP" checked="">';
		else
			echo '<input type="checkbox" name="not_renew_RIP" id="not_renew_RIP" >';
	?>
	</td>
	<th><label for="not_renew_RIP_end_dtm">End Date</label></th>
	<td>
		<script type="text/javascript">
		$j(document).ready(function(){
			$j('#not_renew_RIP_end_dtm').datepicker('setDate', '<?php if(isset($_POST['not_renew_RIP_end_dtm'])) { echo $_POST['not_renew_RIP_end_dtm']; } else {  echo $not_renew_RIP_end_dtm; } ?>');
		});
		</script>
		<input type="text" name="not_renew_RIP_end_dtm" id="not_renew_RIP_end_dtm">
	</td>
	</tr>
	
	<tr>
	<th><label for="reset_not_renew">Reset Not Renewing</label></th>
	</th>
	<td><button type="button" name="reset_not_renew" id="reset_not_renew" class="button button-primary">Reset</button>
	</td>
	</tr>
	</tbody>
	</table>
	<?php endif; 
	
}

/**
* Updates
*/

//add_action( 'uet_edit_user_profile_update_membership','uet_update_membership_fields');

function uet_update_membership_fields($user_id) {
	
	//Groups
	$groups_User_Profile = new Groups_Admin_User_Profile();
	$groups_User_Profile->edit_user_profile_update($user_id);
	
	//Complimentary
	if (isset($_POST['life_memb']))
		update_user_meta($user_id, 'life_memb', $_POST['life_memb']);
	if (isset($_POST['life_memb_start_dtm']))
		update_user_meta($user_id, 'life_memb_start_dtm', $_POST['life_memb_start_dtm']);
	if (isset($_POST['comp_memb']))
		update_user_meta($user_id, 'comp_memb', $_POST['comp_memb']);
	else
		update_user_meta($user_id, 'comp_memb', '');
	if (isset($_POST['comp_memb_end_dtm']))
		update_user_meta($user_id, 'comp_memb_end_dtm', $_POST['comp_memb_end_dtm']);
	if (isset($_POST['comp_memb_AJEC']))
		update_user_meta($user_id, 'comp_memb_AJEC', $_POST['comp_memb_AJEC']);
	else
		update_user_meta($user_id, 'comp_memb_AJEC', '');
	if (isset($_POST['comp_memb_AJEC_end_dtm']))
		update_user_meta($user_id, 'comp_memb_AJEC_end_dtm', $_POST['comp_memb_AJEC_end_dtm']);
	if (isset($_POST['comp_memb_EC']))
		update_user_meta($user_id, 'comp_memb_EC', $_POST['comp_memb_EC']);
	else
		update_user_meta($user_id, 'comp_memb_EC', '');
	if (isset($_POST['comp_memb_EC_end_dtm']))
		update_user_meta($user_id, 'comp_memb_EC_end_dtm', $_POST['comp_memb_EC_end_dtm']);	
	if (isset($_POST['comp_memb_EDL']))
		update_user_meta($user_id, 'comp_memb_EDL', $_POST['comp_memb_EDL']);
	else
		update_user_meta($user_id, 'comp_memb_EDL', '');
	if (isset($_POST['comp_memb_EDL_end_dtm']))
		update_user_meta($user_id, 'comp_memb_EDL_end_dtm', $_POST['comp_memb_EDL_end_dtm']);
	if (isset($_POST['comp_memb_RIP']))
		update_user_meta($user_id, 'comp_memb_RIP', $_POST['comp_memb_RIP']);
	else
		update_user_meta($user_id, 'comp_memb_RIP', '');
	if (isset($_POST['comp_memb_RIP_end_dtm']))
		update_user_meta($user_id, 'comp_memb_RIP_end_dtm', $_POST['comp_memb_RIP_end_dtm']);	
	
	//Not renewing
	if (isset($_POST['not_renew_memb']))
		update_user_meta($user_id, 'not_renew_memb', $_POST['not_renew_memb']);
	if (isset($_POST['not_renew_AJEC']))
		update_user_meta($user_id, 'not_renew_AJEC', $_POST['not_renew_AJEC']);
	if (isset($_POST['not_renew_EC']))
		update_user_meta($user_id, 'not_renew_EC', $_POST['not_renew_EC']);
	if (isset($_POST['not_renew_EDL']))
		update_user_meta($user_id, 'not_renew_EDL', $_POST['not_renew_EDL']);
	if (isset($_POST['not_renew_RIP']))
		update_user_meta($user_id, 'not_renew_RIP', $_POST['not_renew_RIP']);
	if (isset($_POST['not_renew_memb_end_dtm']))
		update_user_meta($user_id, 'not_renew_memb_end_dtm', $_POST['not_renew_memb_end_dtm']);
	if (isset($_POST['not_renew_AJEC_end_dtm']))
		update_user_meta($user_id, 'not_renew_AJEC_end_dtm', $_POST['not_renew_AJEC_end_dtm']);
	if (isset($_POST['not_renew_EC_end_dtm']))
		update_user_meta($user_id, 'not_renew_EC_end_dtm', $_POST['not_renew_EC_end_dtm']);
	if (isset($_POST['not_renew_EDL_end_dtm']))
		update_user_meta($user_id, 'not_renew_EDL_end_dtm', $_POST['not_renew_EDL_end_dtm']);
	if (isset($_POST['not_renew_RIP_end_dtm']))
		update_user_meta($user_id, 'not_renew_RIP_end_dtm', $_POST['not_renew_RIP_end_dtm']);
	
	if(empty($_POST['life_memb_start_dtm']) AND isset($_POST['life_memb'])) {
		$errors = new WP_Error();
		$errors->add( 'life_memb_dtm', 'Update FAILED: Life member start date is required' );
		return $errors;
	}
}

/**
* Other functions
*/

/**
* Groups WooCommerce Override function
* This removes DIV elements from the $output

* Renders group subscription info for the user.
* 
* @param object $user
*/
function uet_show_subscriptions( $user ) {

	echo '<h3>';
	echo __( 'Group Subscriptions', GROUPS_WS_PLUGIN_DOMAIN );
	echo '</h3>';

	/**
	* List of columns to pass - listed in class-groups-ws-subscriptions-table-renderer.php
	'status' 
	'title' 
	'start_date' 
	'expiry_date' 
	'end_date'  
	'trial_expiry_date' 
	'groups'     
	'order_id'
	*/
	
	require_once( GROUPS_WS_VIEWS_LIB . '/class-groups-ws-subscriptions-table-renderer.php' );
		$table = Groups_WS_Subscriptions_Table_Renderer::render( array(
			'status' => '*',
			'exclude_cancelled_after_end_of_prepaid_term' => true,
			'user_id' => $user->ID,
			'columns' => array( 'title', 'start_date', 'end_date', 'expiry_date', 'status' )
		),
		$n
	);
	//echo apply_filters( 'groups_woocommerce_show_subscriptions_style', '<style type="text/css">div.subscriptions-count { padding: 0px 0px 1em 2px; } div.group-subscriptions table th { text-align:left; padding-right: 1em; }</style>' );
	//echo '<div class="subscriptions-count">';
	if ( $n > 0 ) {
		echo sprintf( _n( 'One subscription.', '%d subscriptions.', $n, GROUPS_WS_PLUGIN_DOMAIN ), $n );
	} else {
		echo __( 'No subscriptions.', GROUPS_WS_PLUGIN_DOMAIN );
	}
	//echo '</div>';
	//echo '<div class="group-subscriptions">';
	echo $table;
	//echo '</div>';
} 

/**
 * Editing a user profile.
 * @param WP_User $user
 */
function uet_edit_user_profile( $user ) {
	global $wpdb;
	if ( current_user_can( GROUPS_ADMINISTER_GROUPS ) ) {
		$output = '<h3>' . __( 'User Groups', GROUPS_PLUGIN_DOMAIN ) . '</h3>';
		$user = new Groups_User( $user->ID );
		$user_groups = $user->groups;
		$groups_table = _groups_get_tablename( 'group' );
		if ( $groups = $wpdb->get_results( "SELECT * FROM $groups_table ORDER BY name" ) ) {
			$output .= '<style type="text/css">';
			$output .= '.groups .selectize-input { font-size: inherit; }';
			$output .= '</style>';
			$output .= sprintf(
				'<select id="user-groups" class="groups" name="group_ids[]" multiple="multiple" placeholder="%s" data-placeholder="%s">',
				esc_attr( __( 'Choose groups &hellip;', GROUPS_PLUGIN_DOMAIN ) ) ,
				esc_attr( __( 'Choose groups &hellip;', GROUPS_PLUGIN_DOMAIN ) )
			);
			foreach( $groups as $group ) {
				$is_member = Groups_User_Group::read( $user->ID, $group->group_id ) ? true : false;
				$output .= sprintf( '<option value="%d" %s>%s</option>', Groups_Utility::id( $group->group_id ), $is_member ? ' selected="selected" ' : '', wp_filter_nohtml_kses( $group->name ) );
			}
			$output .= '</select>';
			$output .= Groups_UIE::render_select( '#user-groups' );
			$output .= '<p class="description">' . __( 'The user is a member of the chosen groups.', GROUPS_PLUGIN_DOMAIN ) . '</p>';
		}
		echo $output;
	}
}


?>