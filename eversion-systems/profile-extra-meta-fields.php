<?php

/*
* Custom Fields For Early Childhood Users
* Author : 	Andrew Schultz
* Summary : Stores additional meta data in the user meta table of wordpress for customers
*			This file contains methods to populate and create fields in the admin and my-account pages
*			Data is only allowed access for those members with Subscriptions
*			Additional configuration for display User Meta Data forms needs to be added to the my-account page
* Uses : 		Groups Plugin
* Style Enqueue : 	style-profile.css
* Script Enqueue :	profile-user-meta.js, my-account-user-meta.js
* History:	2014-08-21	Added configuration to display customer's number (userID) based on whether they have an old ECA number or not
*/

//Add customer number at the top of the my account page
add_action( 'woocommerce_before_my_account', 'es_add_customer_number_my_account');

function es_add_customer_number_my_account() {
	$user_id = get_current_user_id();
	$member_number = get_user_meta($user_id, 'old_customer_number', true);

	if ($member_number) {
		$customer_number = $member_number;
	}
	else {
		$customer_number = 'A'.$user_id;
	}

	?>
	<h2>Customer Number</h2>
	<div style="width: 150px;color:#ED1C24;border-width: 0px 1px 1px 0px;padding: 4px 8px;vertical-align: middle;border: 1px solid rgba(0, 0, 0, 0.1);font-size: 1.4em !important;">
		<?php echo $customer_number;?>
	</div>
	<?php
}

//Display customer number for other users - Hooks near the bottom of the profile page (if not current user) 
add_action('edit_user_profile', 'eca_profile_show_old_customer_number');
//if current user then use this hook to show their profile elements
add_action('show_user_profile', 'eca_profile_show_old_customer_number');

function eca_profile_show_old_customer_number() {
	$user_id = get_user_id_for_viewed_profile();
	$old_customer_number = get_user_meta($user_id, 'old_customer_number', true);
	$my_child_id = get_user_meta($user_id, 'my_child_id', true);
	$discount_tickets_number = get_user_meta($user_id, 'discount_tickets_number', true);
	$free_tickets_number = get_user_meta($user_id, 'free_tickets_number', true);
	
	?>
	<h3>Miscellaneous</h3>
	<table class="form-table">
	<tbody>
	<tr>
		<th><label for="old_customer_number">Old Customer Number</label></th>
		<td><input style="width:120px;" name="old_customer_number" id="old_customer_number" value="<?php echo $old_customer_number;?>" class="regular-text" type="text"></td>
	</tr>
	<tr>
		<th><label for="my_child_id">My Child ID</label></th>
		<td><input style="width:120px;" name="my_child_id" id="my_child_id" value="<?php echo $my_child_id;?>" class="regular-text" type="text"></td>
	</tr>
	<?php if (is_plugin_active('event-espresso/espresso.php')) { ?>
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

//Update customer number to dashboard for administration staff to update user's profiles
add_action('edit_user_profile_update', 'eca_profile_update_old_customer_number');
//Update user data for current user if they are viewing their own profile
add_action('personal_options_update', 'eca_profile_update_old_customer_number' );

//Update the old customer number and child ID
function eca_profile_update_old_customer_number() {
	$user_id = get_user_id_for_viewed_profile();
	
    //if ( current_user_can('edit_user', $user_id) )
    update_user_meta($user_id, 'old_customer_number', $_POST['old_customer_number']);
	update_user_meta($user_id, 'my_child_id', $_POST['my_child_id']);
	update_user_meta($user_id, 'discount_tickets_number', $_POST['discount_tickets_number']);
	update_user_meta($user_id, 'free_tickets_number', $_POST['free_tickets_number']);
}

//Add user meta-data
add_action( 'show_user_profile', 'show_extra_user_meta_data' );	//view your own profile
add_action( 'edit_user_profile', 'show_extra_user_meta_data' ); //edit another user profile hook
add_action( 'edit_user_profile_update', 'update_user_extra_meta_data' );
add_action( 'personal_options_update',  'update_user_extra_meta_data' );

function get_user_id_for_viewed_profile() {
	global $userMeta, $pagenow;

	if( $pagenow == 'profile.php' )
		$userID = $userMeta->userID();
	elseif( $pagenow == 'user-edit.php' )
		$userID = esc_attr( @$_REQUEST[ 'user_id' ] );
		
	return $userID;
}

/**
* Create additional user demographic data fields
*/
function show_extra_user_meta_data() {
	global $userMeta, $pagenow;

	//Enqueue Scripts for dashboard profile, this needs to be placed here as this is the hook required to activate the code
	if( $pagenow == 'profile.php' ) {
		wp_enqueue_script('jquery');
		wp_enqueue_script( 'profile-user-meta', constant( 'EVERSION_PLUGIN_URL' ). '/js/profile-user-meta.js', array( 'jquery' ), '1.0', true );
		wp_enqueue_style( 'admin-profile', constant( 'EVERSION_PLUGIN_URL' ) . '/style-profile.css' );
	}
	
	$userID = get_user_id_for_viewed_profile();
	
	//Check what groups user belongs to
	$user_array = array(
			'user_id' => $userID,
			'user_login' => null,
			'user_email' => null,
			'format' => '',
			'list_class' => 'groups',
			'item_class' => 'name',
			'order_by' => 'name',
			'order' => 'ASC'
			);
	
	//Get groups of user profile being editted
	$user_groups_string = '';
	$user_groups_string .= do_shortcode_func( 'groups_user_groups', $user_array );
	
	//Check what group user has and display the appropriate user fields
	if (strpos($user_groups_string, 'Individual')) {
		$form   = $userMeta->getFormData( 'Individual Member' );
		echo '<h3>Individual Australian</h3>';
	}
	else if (strpos($user_groups_string, 'Service')) {
		echo '<h3>Service Australian</h3>';
		$form   = $userMeta->getFormData( 'Service Member' );
	}
	else if (strpos($user_groups_string, 'Organisation')) {
		echo '<h3>Organisation Australian</h3>';
		$form   = $userMeta->getFormData( 'Organisation Member' );
	}
	else if (strpos($user_groups_string, 'Concession')) {
		echo '<h3>Concession Australian</h3>';
		$form   = $userMeta->getFormData( 'Concession Member' );
	}
	
	if(isset($form)) {

		$fields = $form["fields"];  
		
		$user = new WP_User( $userID );
		
		$formKey = 'um_backend_profile';

		$i = 0;
		
		foreach( $fields as $fieldID ){
			if( empty($fieldID) )
			continue;
			
			$i++;
			
			// if first rows is not section heading then initiate html table
			if( ( $i == 1 ) || ( @$fields[ $fieldID ]['field_type'] <> 'section_heading' ) ){
				echo "<table class=\"form-table\"><tbody>"; 
				$inTable = true;
			}
			
			if( $fieldID['field_type'] == 'section_heading' ){
				if( @$inTable ){
					echo "</tbody></table>";
					$inTable = false;
				}                                           
				echo "<h3>" . $fieldID['field_title'] . "</h3> <table class='form-table'><tbody>";
				$inTable = true;
				continue;
			}
			if(isset($fieldID['meta_key'])) {
				$fieldName = $fieldID['meta_key'];  
				
				if( !$fieldName )
					$fieldName = $fieldID['field_type'];
				
				$fieldID['field_name']  = $fieldName;
				$fieldID['field_value'] = @$user->$fieldName;
				$fieldID['title_position'] = 'hidden';
				
				//$field = $fields[ $fieldID ];
				
				//user_meta_field_config (since 1.1.3rc2)
				//Can be modify fields data by calling this filter hook.
				//Function arguments: Form Data (array), Field ID (int), Form Name (string)
				
				$fields = apply_filters( 'user_meta_field_config', $fields, $fieldID, $formKey );

				$fieldDisplay = $userMeta->renderPro( 'generateField', array( 
					'field'         => $fieldID,
					'form'          => null,
					'actionType'    => null,
					'userID'        => $userID,
					'inPage'        => null,
					'inSection'     => null,
					'isNext'        => null,
					'isPrevious'    => null,
					'currentPage'   => null,
					'uniqueID'      => 'profile',
				) );
		 
				//Filter Hook: user_meta_field_display (since 1.1.3rc2)
				//Applied to field html before browser output.
				//Function arguments: HTML (string), Field ID (int), Form Name (string), Field Data (array)
				$html = apply_filters( 'user_meta_field_display', $fieldDisplay, $fieldID, $formKey, $fields );
				
				$field_id = $fieldID['field_id'];
				
				if( $fieldID ['field_type'] == 'hidden' )
					echo $html;
				else
					echo "<tr><th><label for=\"um_field_$field_id\">{$fieldID['field_title']}</label></th><td>$html</td></tr>";
			}
		}
		
		if( @$inTable )
			echo "</tbody></table>";

		 ?>          
		<script type="text/javascript">
			jQuery(document).ready(function(){
				jQuery("#your-profile").validationEngine();
				jQuery(".um_rich_text").wysiwyg({initialContent:" "});  

				umFileUploader( '<?php  echo $userMeta->pluginUrl . '/framework/helper/uploader.php' ?>' );    

				var form = document.getElementById( 'your-profile' );
				form.encoding = 'multipart/form-data';
				form.setAttribute('enctype', 'multipart/form-data');  
			});
		</script>
		<?php   

	}
}

/**
* Update the custom demographic fields
*/
function update_user_extra_meta_data() {
	global $userMeta;
	
	$errors = new WP_Error;
	
	$userID = get_user_id_for_viewed_profile();
	
	//Check what groups user belongs to
	$user_array = array(
			'user_id' => $userID,
			'user_login' => null,
			'user_email' => null,
			'format' => '',
			'list_class' => 'groups',
			'item_class' => 'name',
			'order_by' => 'name',
			'order' => 'ASC'
			);
	
	//Get groups of user profile being editted
	$user_groups_string .= do_shortcode_func( 'groups_user_groups', $user_array );
	
	//Check what group user has and display the appropriate user fields
	if (strpos($user_groups_string, 'Individual')) {
		$form   = $userMeta->getFormData( 'Individual Member' );
	}
	else if (strpos($user_groups_string, 'Service')) {
		$form   = $userMeta->getFormData( 'Service Member' );
	}
	else if (strpos($user_groups_string, 'Organisation')) {
		$form   = $userMeta->getFormData( 'Organisation Member' );
	}
	else if (strpos($user_groups_string, 'Concession')) {
		$form   = $userMeta->getFormData( 'Concession Member' );
	}
	
	//$form   = $userMeta->getFormData( 'Individual Member' );

	
	$backendFields = $form["fields"];  
	
	//print "<pre>";
	//print_r($backendFields);
	//print "</pre>";
	
	
	if( !is_array( $backendFields ) ) return;
	
	$userData = array();
	foreach( $backendFields as $fieldID ){
			  
		$fieldData = $fieldID ;
		
		if( !empty( $fieldData[ 'meta_key' ] ) )
			$fieldName  = $fieldData[ 'meta_key' ];
		else{
			if( in_array( @$fieldData[ 'field_type' ], array('user_registered','user_avatar') ) )
				$fieldName = $fieldData[ 'field_type' ];
		}
		
		if( empty( $fieldName ) ) continue;
		
		$userData[ $fieldName ] = @$_POST[ $fieldName ];  

		// Handle non-ajax file upload
		if( in_array( $fieldData[ 'field_type' ], array( 'user_avatar', 'file' ) ) ){
			if( isset( $_FILES[ $fieldName ] ) ){
				$extensions = @$fieldData[ 'allowed_extension' ] ? $fieldData[ 'allowed_extension' ] : "jpg, png, gif";
				$maxSize    = @$fieldData[ 'max_file_size' ] ? $fieldData[ 'max_file_size' ] * 1024 : 1024 * 1024;
				$file = $userMeta->fileUpload( $fieldName, $extensions, $maxSize );
				if( is_wp_error( $file ) ){
					if( $file->get_error_code() <> 'no_file' )                       
						$errors->add( $file->get_error_code(), $file->get_error_message() );
				}else{
					if( is_string( $file ) )
						$userData[ $fieldName ] = $file;
				}                       
			}
		}       		
	}   
	
	$userMeta->insertUser( $userData, $userID );
}  

/**
* Display duplicate billing to shipping address
* Attach low priority so it shows last in the user edit area
*/
add_action('edit_user_profile', 'eca_profile_duplicate_billing_address', 99);
add_action('show_user_profile', 'eca_profile_duplicate_billing_address', 99);

function eca_profile_duplicate_billing_address() {
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

?>