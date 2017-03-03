<?php
/*
Plugin Name: User Edit Tabbed
Plugin URI: http://eversionsystems.com
Description: Create a tabbed user edit screen in WordPress. Redirects user-edit.php to a custom user edit page.
Version: 1.01
Author: Andrew Schultz
Author URI: http://eversionsystems.com
License: GPL2

Version		Date			Changes
1.01		2015-08-19		Added support for WordPress 4.3 user edit strong password

*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/* Enable function is_plugin_active */
include_once( ABSPATH . 'wp-admin/includes/plugin.php' ); 

//Constants
define('UET_BASE_URL', get_bloginfo('wpurl'));
define('UET_VERSION','1.0' );
define('USER_EDIT_MENU_SLUG','custom-user-edit');
define('AJEC_SUB_NAME', 'Australasian Journal of Early Childhood');
define('EC_SUB_NAME', 'Every Child');
define('EDL_SUB_NAME', 'Everyday Learning Series');
define('RIP_SUB_NAME', 'Research in Practice Series');

//Valid subscriptions
$subs_array = array(AJEC_SUB_NAME, EC_SUB_NAME, EDL_SUB_NAME, RIP_SUB_NAME);

//TAB names
define('MEMBER_TAB_NAME','membership');
define('MEMBER_DISPLAY_NAME', 'Membership');
define('ADDRESS_TAB_NAME','address');
define('ADDRESS_DISPLAY_NAME','Address');
define('QUESTIONS_TAB_NAME','questions');
define('QUESTIONS_DISPLAY_NAME','Details');
define('MAIN_TAB_NAME', 'main');
define('MAIN_DISPLAY_NAME', 'User');
define('GENERAL_TAB_NAME', 'general');
define('GENERAL_DISPLAY_NAME', 'General');
define('MISC_TAB_NAME', 'misc');
define('MISC_DISPLAY_NAME', 'Miscellaneous');
define('NOTES_TAB_NAME', 'notes');
define('NOTES_DISPLAY_NAME', 'Notes');
define('UET_PLUGIN_URL', plugin_dir_url( __FILE__ ));

//TAB Array
$tabs = array( MAIN_TAB_NAME => MAIN_DISPLAY_NAME, 
		ADDRESS_TAB_NAME => ADDRESS_DISPLAY_NAME, 
		MEMBER_TAB_NAME => MEMBER_DISPLAY_NAME,
		GENERAL_TAB_NAME => GENERAL_DISPLAY_NAME,		
		QUESTIONS_TAB_NAME => QUESTIONS_DISPLAY_NAME ,
		NOTES_TAB_NAME => NOTES_DISPLAY_NAME,
		MISC_TAB_NAME =>MISC_DISPLAY_NAME
		);
		
//Plugin files
define('USER_ROLE_EDITOR_PP', 'user-role-editor/user-role-editor.php');
define('YOAST_SEO_PP', 'wordpress-seo/wp-seo.php');
define('WOOCOMMERCE_PP', 'woocommerce/woocommerce.php');
define('USER_META_PP', 'user-meta/user-meta.php');
define('GROUPS_PP', 'groups/groups.php');
define('GROUPS_WOOCOMM_PP', 'groups-woocommerce/groups-woocommerce.php');
define('EVERSION_SYS_PP', 'eversion-systems/eversion-systems.php');
define('SUBSCRIPTION_WOO_PP', 'woocommerce-subscriptions/woocommerce-subscriptions.php');	

/**
* Fires from wp-settings.php, place functions in here you want to load first
*/
add_action( 'admin_enqueue_scripts', 'uet_admin_enqueue_scripts' );

/**
* Add scripts to dashboard
*/
function uet_admin_enqueue_scripts($hook) {

	if($hook == "users_page_custom-user-edit") {
		
		//Load admin css for styling all TABS
		wp_enqueue_style('user-edit-tabbed', UET_PLUGIN_URL.'css/user-edit-tabbed-admin.css');
		
		//Are you sure plugin for prompting unsaved changes
		wp_enqueue_script( 'are-you-sure-include', UET_PLUGIN_URL. 'js/are-you-sure.js', array( 'jquery' ), '1.0', true );
		
		//Wordpress 4.3 password change
		//wp_enqueue_script( 'user-profile', site_url(). '\wp-admin\js\user-profile.js', array( 'jquery' ), '', true );
		
		switch ($_GET['tab']) {
			case QUESTIONS_TAB_NAME:
				if(is_plugin_active(USER_META_PP)) {
					//Load all scripts for User-Meta plugin
					//$umSupportModel = new umSupportModel();
					//$umSupportModel->loadAllScripts();
					uet_loadAllScripts();
				}
				if(!is_plugin_active(EVERSION_SYS_PP)) {
					//These files are currently being loaded by the eversion sys plugin for when we are editting
					//the user meta forms from the front end
					wp_enqueue_script( 'profile-user-meta', UET_PLUGIN_URL. '/js/profile-user-meta.js', array( 'jquery' ), '1.0', true );
				}
				
				wp_enqueue_script( 'user-edit-tabbed-questions', UET_PLUGIN_URL . 'js/user-edit-tabbed-questions-fields.js', array( 'jquery' ), '1.0', false );
				
				break;
			case MEMBER_TAB_NAME:
				if(is_plugin_active(GROUPS_PP)) {
					wp_enqueue_script( 'selectize-load', site_url('/wp-content/plugins/groups/js/selectize/selectize.min.js'), array( 'jquery' ), '1.0', false );
					wp_enqueue_style('selectize', site_url('/wp-content/plugins/groups/css/selectize/selectize.css'));
				}
				wp_enqueue_script( 'user-edit-tabbed-member', UET_PLUGIN_URL . 'js/user-edit-tabbed-membership-fields.js', array( 'jquery' ), '1.0', false );
				break;
			case ADDRESS_TAB_NAME:
				wp_enqueue_script( 'user-edit-tabbed-address', UET_PLUGIN_URL . 'js/user-edit-tabbed-duplicate-billing-address.js', array( 'jquery' ), '1.0', false );
				break;
			case MAIN_TAB_NAME:
				wp_enqueue_style('user-edit-select2', UET_PLUGIN_URL . 'css/select2.css');
				wp_enqueue_script( 'user-edit-tabbed-main', UET_PLUGIN_URL . 'js/user-edit-tabbed-main-fields.js', array( 'jquery' ), '1.0', true );
				wp_enqueue_script( 'user-edit-select2', UET_PLUGIN_URL . 'js/select2/select2.min.js', array( 'jquery' ), '1.0', true );
				wp_enqueue_script( 'user-edit-search-users', UET_PLUGIN_URL . 'js/user-edit-tabbed-search-users.js', array( 'jquery' ), '1.0', true );
				
				wp_localize_script( 'user-edit-search-users', 'wc_enhanced_select_params', array(
			'i18n_matches_1'            => _x( 'One result is available, press enter to select it.', 'enhanced select', 'woocommerce' ),
			'i18n_matches_n'            => _x( '%qty% results are available, use up and down arrow keys to navigate.', 'enhanced select', 'woocommerce' ),
			'i18n_no_matches'           => _x( 'No matches found', 'enhanced select', 'woocommerce' ),
			'i18n_ajax_error'           => _x( 'Loading failed', 'enhanced select', 'woocommerce' ),
			'i18n_input_too_short_1'    => _x( 'Please enter 1 or more characters', 'enhanced select', 'woocommerce' ),
			'i18n_input_too_short_n'    => _x( 'Please enter %qty% or more characters', 'enhanced select', 'woocommerce' ),
			'i18n_input_too_long_1'     => _x( 'Please delete 1 character', 'enhanced select', 'woocommerce' ),
			'i18n_input_too_long_n'     => _x( 'Please delete %qty% characters', 'enhanced select', 'woocommerce' ),
			'i18n_selection_too_long_1' => _x( 'You can only select 1 item', 'enhanced select', 'woocommerce' ),
			'i18n_selection_too_long_n' => _x( 'You can only select %qty% items', 'enhanced select', 'woocommerce' ),
			'i18n_load_more'            => _x( 'Loading more results&hellip;', 'enhanced select', 'woocommerce' ),
			'i18n_searching'            => _x( 'Searching&hellip;', 'enhanced select', 'woocommerce' ),
			'ajax_url'                  => admin_url( 'admin-ajax.php' ),
			'search_products_nonce'     => wp_create_nonce( 'search-products' ),
			'search_customers_nonce'    => wp_create_nonce( 'search-customers' )
		) );
				
				break;
		}
		//Duplicate Billing to Shipping Address
		//wp_enqueue_script( 'dashboard-duplicate-bill-address', constant( 'EVERSION_PLUGIN_URL' ). 'js/admin-duplicate-billing-address.js', array( 'jquery' ), '1.0', false );
	}
}

/**
* Create a custom menu but don't show it in the user menu
*/
add_action('admin_menu', 'uet_register_menu' );

function uet_register_menu() {
	//$parent_slug = null;
	//Issue here with using slug users.php
	//If we have a null slug it adds the menu to the last parent menu
	//which is this case is Event Espresso
	$parent_slug = 'users';
	$capability = 'manage_options';
	$page_title = 'Custom User Edit';
	$menu_title = '';
	$menu_slug = USER_EDIT_MENU_SLUG;
	$function = 'uet_create_custom_user_edit_page';
	$hook = add_users_page( $page_title, $menu_title, $capability, $menu_slug, $function);
	//add_submenu_page( $parent_slug, $page_title, $menu_title, $capability, $menu_slug, $function );
}

/**
* Redirect the user-edit.php file to our custom version
*/
add_action('load-user-edit.php', 'uet_redirect_user_edit' );

function uet_redirect_user_edit() {

	if( is_admin() ) {
		$screen = get_current_screen();

		if( $screen->base == 'user-edit' ) {
			//Redirect clears PHP POST	
			$redirect_url = 'users.php?user_id='.$_GET['user_id'].'&page='.USER_EDIT_MENU_SLUG;
			if (isset($_GET['tab']))
				$redirect_url .= '&tab='.$_GET['tab'];
			else
				$redirect_url .= '&tab='.MAIN_TAB_NAME;

			//Redirect user to custom user edit page
			wp_redirect( admin_url( $redirect_url));
		}
	}
}

function uet_create_custom_user_edit_page() {
	include_once( 'user-edit-tabbed-page.php' );
}

/**
* Output TAB's
*/
function uet_user_edit_admin_tabs( $current = MAIN_TAB_NAME ) {
    //$tabs = array( MAIN_TAB_NAME => MAIN_DISPLAY_NAME, GENERAL_TAB_NAME => GENERAL_DISPLAY_NAME, ADDRESS_TAB_NAME => ADDRESS_DISPLAY_NAME, MEMBER_TAB_NAME => MEMBER_DISPLAY_NAME, QUESTIONS_TAB_NAME => QUESTIONS_DISPLAY_NAME );
    global $tabs;
	echo '<div id="icon-themes" class="icon32"><br></div>';
    echo '<h2 class="nav-tab-wrapper">';
    foreach( $tabs as $tab => $name ){
        $class = ( $tab == $current ) ? ' nav-tab-active' : '';
		$user_id = $_GET['user_id'];
		$user_edit_menu_slug = USER_EDIT_MENU_SLUG;
        echo "<a class='nav-tab$class' href='?user_id=$user_id&page=$user_edit_menu_slug&tab=$tab'>$name</a>";

    }
    echo '</h2>';
}

//Function to enqueue scripts for User-Meta plugin
//timepicker causes jquery error so leave it out
function uet_loadAllScripts() {
	global $userMeta;

	$userMeta->enqueueScripts( array( 
		'user-meta',           
		'jquery-ui-all',
		'fileuploader',
		'wysiwyg',
		'jquery-ui-datepicker',
		'jquery-ui-slider',
		//'timepicker',
		'validationEngine',
		'password_strength',
		'placeholder',
		'multiple-select'
	) );                      
	$userMeta->runLocalization();
}

/*
* Check if user is a member
*/

function uet_is_user_member($user_id) {
	
	$member_exists = false;
	
	if(is_plugin_active(GROUPS_PP)) {
		//Check if the current user is a member, if not then hide the complimentary section
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
			$member_exists = true;
		}
		else if (strpos($user_groups_string, 'Service')) {
			$member_exists = true;
		}
		else if (strpos($user_groups_string, 'Organisation')) {
			$member_exists = true;
		}
		else if (strpos($user_groups_string, 'Concession')) {
			$member_exists = true;
		}
	}
	
	return $member_exists;
}

/**
* Check user has a subscription
*/

function uet_is_user_a_subscriber($user_id) {
	
	$user_has_subscription = false;
	global $subs_array;

	if(is_plugin_active(SUBSCRIPTION_WOO_PP)) {
		$subscription_list = WC_Subscriptions_Admin::do_subscriptions_shortcode(array('user_id'=>$user_id, 'status'  => 'active'));

		foreach ($subs_array as &$value) {
			if (strpos($subscription_list, $value)) {
				$user_has_subscription = true;
			}
		}
		//$user_has_subscription = WC_Subscriptions_Manager::user_has_subscription($user_id);
	}
	
	return $user_has_subscription;
}

/**
* Display visually whether an update is successful or not
* Function edit_user returns the userID if successful so check for this condition for main page update
*/

function uet_show_user_update_result($action, $errors, $user_id) {

	//is_wp_error check a variable of unknown type if it's an error object
	if(isset($errors) && is_wp_error( $errors )) {
		//Add exclusion for user-meta no file error when deleting an image
		if ( $errors->get_error_code() <> 'no_file' ) { ?>
		<div class="error"><p><?php echo implode( "</p>\n<p>", $errors->get_error_messages() ); ?></p></div><?php
		}
	}
	elseif ( $action == 'update') { ?>
		<div id="message" class="updated"> <?php
		if ( IS_PROFILE_PAGE ) { ?>
			<p><strong><?php _e('Profile updated.') ?></strong></p><?php
		}
		else { ?>
			<p><strong><?php _e('User updated.') ?></strong></p><?php 
		}
		if ( !IS_PROFILE_PAGE ) { ?>
			<p><a href="<?php echo esc_url( site_url('/wp-admin/users.php') ); ?>"><?php _e('&larr; Back to Users'); ?></a></p><?php
		} ?>
		</div><?php 
	}
}

add_action('wp_ajax_uet_json_search_customers', 'uet_json_search_customers');

/**
 * Search for customers and return json
 */
function uet_json_search_customers() {
	ob_start();

	check_ajax_referer( 'search-customers', 'security' );

	$term = wc_clean( stripslashes( $_GET['term'] ) );

	if ( empty( $term ) ) {
		die();
	}

	$found_customers = array();

	add_action( 'pre_user_query', array( __CLASS__, 'uet_json_search_customer_name' ) );

	$customers_query = new WP_User_Query( apply_filters( 'uet_json_search_customers_query', array(
		'fields'         => 'all',
		'orderby'        => 'display_name',
		'search'         => '*' . $term . '*',
		'search_columns' => array( 'ID', 'user_login', 'user_email', 'user_nicename' )
	) ) );

	remove_action( 'pre_user_query', array( __CLASS__, 'uet_json_search_customer_name' ) );

	$customers = $customers_query->get_results();

	if ( $customers ) {
		foreach ( $customers as $customer ) {
			$found_customers[ $customer->ID ] = $customer->display_name . ' (#' . $customer->ID . ' &ndash; ' . sanitize_email( $customer->user_email ) . ')';
		}
	}

	wp_send_json( $found_customers );

}

?>