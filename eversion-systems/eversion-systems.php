<?php
/*
Plugin Name: Eversion Systems Theme Customisations
Plugin URI: http://eversionsystems.com
Description: Customisations for Earlychildhood Australia Website
Version: 1.0
Author: Andrew Schultz
Author URI: http://eversionsystems.com
License: GPL2
*/

/* Enable function is_plugin_active */
include_once( ABSPATH . 'wp-admin/includes/plugin.php' ); 

include 'profile-extra-meta-fields.php';	//Fields to display in admin panel
include 'shortcodes.php';	//Various shortcodes
include 'woocommerce-functions.php';	//Woocommerce additional functions
include 'lib/meta-box.php';		//Xero custom fields
// Packing slips plugin now handles these extra columns
include 'add-comment-packing-slip.php';	//Packing slip comment adding and custom order view columns
include 'event-espresso-functions.php';	//Event Espresso
include 'woocommerce-subscriptions.php';	//Custom WooCommerce Subscription functions

//Debug Functions
//include 'debug-functions.php';

//Constants
define('BASE_URL', get_bloginfo('wpurl'));

//Base Directory
//define('child_template_directory', dirname( get_bloginfo('stylesheet_url')) );

//Plugin directory
define('EVERSION_PLUGIN_URL', plugin_dir_url( __FILE__ ));

add_action( 'after_setup_theme', 'childhood_theme_setup' );

/**
* Fires from wp-settings.php, place functions in here you want to load first
*/
function childhood_theme_setup() {

	// Remove WooCommerce Updater
	remove_action('admin_notices', 'woothemes_updater_notice');
	
	//Register hook to load other scripts
	add_action('wp_enqueue_scripts', 'custom_user_meta_load_scripts');
}

/**
* Admin dashboard scripts
*/
add_action( 'admin_enqueue_scripts', 'es_admin_enqueue_scripts' );
	
function es_admin_enqueue_scripts($hook) {
	wp_enqueue_style('dashboard-custom-css', constant( 'EVERSION_PLUGIN_URL' ).'css/admin-style.css');
	
	if ($hook == 'user-edit.php') {
		//Duplicate Billing to Shipping Address
		wp_enqueue_script( 'dashboard-duplicate-bill-address', constant( 'EVERSION_PLUGIN_URL' ). 'js/admin-duplicate-billing-address.js', array( 'jquery' ), '1.0', false );
	}
	
	/**
	 * We need this script in order to populate quick edit fields.
	 */
	if ( 'edit.php' === $hook && isset( $_GET['post_type'] ) && 'product' === $_GET['post_type'] ) {
		wp_enqueue_script('es-woo-quick-edit-script', EVERSION_PLUGIN_URL . '/js/eversion-woocommerce-quick-edit.js', array('jquery','inline-edit-post' ), '', true );
	}
}

/**
* Load scripts (and/or styles) for specific pages
*/
function custom_user_meta_load_scripts(){
	global $post;
	global $wpdb;
	global $woocommerce;

	wp_enqueue_style('eca-custom-css', constant( 'EVERSION_PLUGIN_URL' ).'/style-custom.css', '', '1.1');

	if(is_page()){ //Check if we are viewing a page
		$slug = get_post( $post )->post_name;
		
		if($slug == 'registration-checkout') {
			wp_register_script('es_espresso_registration_page', EVERSION_PLUGIN_URL.'/js/espresso-registration-page.js', array('jquery') );
			wp_enqueue_script('es_espresso_registration_page');
		}

		if($slug == 'my-account' OR $slug == 'checkout'){
			//wp_deregister_script('jquery');
			//wp_register_script('jquery', 'http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js', false, '1.10.2', true); //place in footer
			wp_enqueue_script('jquery');
			//Javascript for custom user demographic fields on front end. Script adds functionality to the fields in myaccount page
			wp_enqueue_script( 'my-account-user-meta', constant( 'EVERSION_PLUGIN_URL' ). '/js/my-account-user-meta.js', array( 'jquery' ), '1.0', true );
		}
		if( $slug === 'checkout'){
			//For paid for posts ensure that they create a membership account and not guest checkout
			wp_register_script('es_paid_post_force_account', EVERSION_PLUGIN_URL.'/js/paid-post-force-account-creation.js', array('jquery') );
			wp_enqueue_script('es_paid_post_force_account');
			
			$premium_post_content = false;
			
			foreach($woocommerce->cart->get_cart() as $cart_item_key => $values ) {
				$_product = $values['data'];
				
				$product_id = $_product->id;
				$content_url = $wpdb->get_var("SELECT GUID FROM wp_posts WHERE ID = (SELECT post_id FROM wp_postmeta WHERE meta_key = 'woocommerce_ppp_product_id' and meta_value=$product_id)");

				if (isset($content_url)) {
					$premium_post_content = true;
				}
			}
			
			$es_paid_post_data = array( 
				'paid_post_exists' => $premium_post_content
			);
        
			//Allow passing of data to Javascript, script name must match enqueued one
			wp_localize_script( 'es_paid_post_force_account', 'es_paid_post_obj', $es_paid_post_data );
		}
	}
	elseif (is_product()) {
		wp_enqueue_script( 'woocommerce-reviews', constant( 'EVERSION_PLUGIN_URL' ). '/js/woocommerce-reviews.js', array( 'jquery' ), '1.0', true );
	}
	elseif (is_single()) {
		//posts, use this for events
		$permalink = get_permalink();
		$slug = get_post( $post )->post_name;
		
		if($slug == "eca-reconciliation-symposium") {
			//wp_enqueue_style('es_event_espresso_custom', constant( 'EVERSION_PLUGIN_URL' ).'css/event-espresso-custom.css');
		}
		
		if (strpos($permalink,'/events/') !== false) {
			//We have an events post page, check membership exists
			$memberExists = es_check_membership_held();
			$user_ID = get_current_user_id();
			$discountEntitled = false;
			$freeEntitled = false;
			
			//Check if user has discount ticket number allocated to them
			$discount_tickets_number = get_user_meta($user_ID, 'discount_tickets_number', true);
			//Check for free tickets
			$free_tickets_number = get_user_meta($user_ID, 'free_tickets_number', true);
			
			if ($discount_tickets_number != 0)
				$discountEntitled = true;
			if ($free_tickets_number != 0)
				$freeEntitled = true;
			
			//write_log($user_groups_string);
			
			wp_register_script('es_event_hide_tickets', EVERSION_PLUGIN_URL.'/js/event-hide-tickets.js', array('jquery') );
			wp_enqueue_script('es_event_hide_tickets');
			
			$es_hide_tickets_data = array( 
				'member_exists' => $memberExists,
				'discount_entitled' => $discountEntitled,
				'free_entitled' => $freeEntitled
			);
        
			//Allow passing of data to Javascript, script name must match enqueued one
			wp_localize_script( 'es_event_hide_tickets', 'es_hide_tickets_obj', $es_hide_tickets_data );
		}
	}
}

function es_check_membership_held() {
	$user_ID = get_current_user_id();
	
	//Check what groups user belongs to
	$user_array = array(
			'user_id' => $user_ID,
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
	$memberExists = false;
	$discountEntitled = false;
	
	if (strpos($user_groups_string, 'Individual')) {
		$memberExists = true;
	}
	else if (strpos($user_groups_string, 'Service')) {
		$memberExists = true;
	}
	else if (strpos($user_groups_string, 'Organisation')) {
		$memberExists = true;
	}
	else if (strpos($user_groups_string, 'Concession')) {
		$memberExists = true;
	}
	
	return $memberExists;
}

/**
 * Call a shortcode function by tag name.
 *
 * @author J.D. Grimes
 * @link http://codesymphony.co/dont-do_shortcode/
 *
 * @param string $tag     The shortcode whose function to call.
 * @param array  $atts    The attributes to pass to the shortcode function. Optional.
 * @param array  $content The shortcode's content. Default is null (none).
 *
 * @return string|bool False on failure, the result of the shortcode on success.
 */
function do_shortcode_func( $tag, array $atts = array(), $content = null ) {
 
    global $shortcode_tags;
 
    if ( ! isset( $shortcode_tags[ $tag ] ) )
        return false;
 
    return call_user_func( $shortcode_tags[ $tag ], $atts, $content, $tag );
}

/*
* AJAX function to auto-populate the Contacts datatable for members
*/

function get_current_user_id_datatable(){
	global $current_user;
	$user_ID = get_current_user_id();
	echo $user_ID;
	//$result['user_id'] = $current_user->ID;
	//echo json_encode($result);
    die();
}

add_action('wp_ajax_get_current_user_id_datatable', 'get_current_user_id_datatable');
//add_action("wp_ajax_nopriv_get_current_user_id_datatable", "get_current_user_id_datatable");

/**
* Add custom login/logout link on all menus
* Reference : 	http://www.butlerblog.com/2011/11/21/show-menu-based-on-wordpress-login-status/
*				http://premium.wpmudev.org/blog/how-to-add-a-loginlogout-link-to-your-wordpress-menu/
*/

add_filter('wp_nav_menu_items', 'add_login_logout_link', 10, 2);

function add_login_logout_link($items, $args) {

	$menu_object = $args->menu;

	if($menu_object->slug == 'my-account') {
		if( is_user_logged_in() ) {
			$loginoutlink = '<a href="'.wp_logout_url(home_url()).'">Log out</a>';
		}
		else {
			$loginoutlink = '<a href="'.wp_login_url(home_url()).'">Log in</a>';
		}

		$items .= '<li>'. $loginoutlink .'</li>';
	}

	
    return $items;
}

/**
* Custom login logo for Early Childhood login page
*/
function eca_login_head() {
	echo "
	<style>
	body.login #login h1 a {
		background: url('".constant( 'EVERSION_PLUGIN_URL' )."/images/eca-logo-300px.png') no-repeat scroll center top transparent;
		height: 117px;
		width: 300px;
	}
	</style>
	";
}

add_action("login_head", "eca_login_head");

/**
* Show USER ID In Dashboard
*/

/*
 * Adding the column
 */
function rd_user_id_column( $columns ) {
	$columns['user_id'] = 'ID';
	return $columns;
}
add_filter('manage_users_columns', 'rd_user_id_column');
 
/*
 * Column content
 */
function rd_user_id_column_content($value, $column_name, $user_id) {
	if ( 'user_id' == $column_name )
		return $user_id;
	return $value;
}
add_action('manage_users_custom_column',  'rd_user_id_column_content', 10, 3);
 
/*
 * Column style (you can skip this if you want)
 */
function rd_user_id_column_style(){
	echo '<style>.column-user_id{width: 5%}</style>';
}
add_action('admin_head-users.php',  'rd_user_id_column_style');

?>