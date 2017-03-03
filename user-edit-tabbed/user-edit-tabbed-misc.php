<?php
/*
File Name : user-edit-tabbed-misc.php
Description: Functions for updating/displaying misc fields in a TAB
Version: 1.0
Author: Andrew Schultz
Author URI: http://eversionsystems.com
License: GPL2
*/

/**
* Display
*/

add_action('uet_edit_user_profile_misc', 'uet_show_misc_fields');

function uet_show_misc_fields($user_id) {
	
	//User Role Editor
	//Function edit_user_profile has been deprecated in 4.17 of URE
	if (is_plugin_active(USER_ROLE_EDITOR_PP)) {
		$user_info = get_userdata($user_id);
		//$GLOBALS['user_role_editor']->edit_user_profile_html($user_info);

		if (isset($GLOBALS['user_role_editor'])) {
			//$GLOBALS['user_role_editor']->edit_user_profile($user_info);
			//$edit_user_profile->edit_user_profile($user_info);
		}
	}
	
	//YOAST Wordpress SEO
	if (is_plugin_active(YOAST_SEO_PP)) {
		$user_info = get_userdata($user_id);
		
		if(class_exists('WPSEO_Admin_User_Profile')) {
			$WPSEO = new WPSEO_Admin_User_Profile();
			$WPSEO->user_profile($user_info);
		}
	}
}

/**
* Updates
*/

add_action('uet_edit_user_profile_update_misc', 'uet_update_misc_fields');

function uet_update_misc_fields($user_id) {
	if(class_exists('WPSEO_Admin_User_Profile')) {
		$WPSEO = new WPSEO_Admin_User_Profile();
		$WPSEO->process_user_option_update($user_id);
	}
}

?>