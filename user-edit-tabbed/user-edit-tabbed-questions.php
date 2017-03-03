<?php
/*
File Name : user-edit-tabbed-questions.php
Description: Functions for updating/displaying question fields in a TAB
Version: 1.0
Author: Andrew Schultz
Author URI: http://eversionsystems.com
License: GPL2
*/

/**
* Display
*/

add_action('uet_edit_user_profile_questions', 'uet_show_questions');

function uet_show_questions($user_id) {
	
	if (is_plugin_active(USER_META_PP)) {
		global $userMeta, $pagenow;

		//Check what groups user belongs to
		$user_array = array(
				'user_id' => $user_id,
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
		//Output customer number regardless 

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
		else
			echo 'User has no groups with questions associated with it';

		if(isset($form)) {
		
			$fields = $form["fields"];  
			$user = new WP_User( $user_id );
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
					
					//user_meta_field_config (since 1.1.3rc2)
					//Can be modify fields data by calling this filter hook.
					//Function arguments: Form Data (array), Field ID (int), Form Name (string)
					
					$fields = apply_filters( 'user_meta_field_config', $fields, $fieldID, $formKey );

					$fieldDisplay = $userMeta->renderPro( 'generateField', array( 
						'field'         => $fieldID,
						'form'          => null,
						'actionType'    => null,
						'userID'        => $user_id,
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
	else
		echo 'Activate User Meta plugin to display details for customer';
}

/**
* Updates
*/

//add_action('uet_edit_user_profile_update_questions', 'uet_update_questions_fields');

function uet_update_questions_fields($user_id) {
	
	global $userMeta, $errors;
	
	//Check what groups user belongs to
	$user_array = array(
			'user_id' => $user_id,
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
	
	$backendFields = $form["fields"];  
	
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
		//AJAX method of uploading files is not working so use standard POST
		if( in_array( $fieldData[ 'field_type' ], array( 'user_avatar', 'file' ) ) ){
			if( isset( $_FILES[ $fieldName ] ) ){
				$extensions = @$fieldData[ 'allowed_extension' ] ? $fieldData[ 'allowed_extension' ] : "jpg, png, gif";
				$maxSize    = @$fieldData[ 'max_file_size' ] ? $fieldData[ 'max_file_size' ] * 1024 : 1024 * 1024;
				$file = $userMeta->fileUpload( $fieldName, $extensions, $maxSize );
				if( is_wp_error( $file ) ){
					if( $file->get_error_code() <> 'no_file' )
						$errors = new WP_Error();
						if( is_object( $errors )) {
							$errors->add( $file->get_error_code(), $file->get_error_message() );
							return $errors;
						}
				}else{
					if( is_string( $file ) )
						$userData[ $fieldName ] = $file;
				}                       
			}
		}       		
	}  
	
	$userMeta->insertUser( $userData, $user_id );
}

?>