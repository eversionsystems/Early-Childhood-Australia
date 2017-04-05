<?php
/**
* Name : woocommerce-functions.php
* Author : Andrew Schultz
* Purpose : Contains all the custom functions/fields used for Woocommerce
*
* Source Code Modifications:
	Modify woocommerce-subscriptions/classes/class-wc-subscriptions-manager.php
	Line 1070 modify this for changing cancelled subscriptions to active
	when an order is completed again.
*/

/**
* Load scripts (and/or styles) for specific pages
*/
function eca_manage_woocommerce_styles(){

	//global $post;
	//$slug = get_post( $post )->post_name;

	//if($slug == 'my-account'){
	wp_enqueue_style('eca-woocommerce-custom', constant( 'EVERSION_PLUGIN_URL' ).'/woocommerce-custom.css', '', '1.01');
	//}
}

//Set to 999 precedence so it loads css last
add_action( 'wp_enqueue_scripts', 'eca_manage_woocommerce_styles', 999 );

/**
* Remove WooCommerce Generator tag, styles, and scripts from the homepage.
* Tested and works with WooCommerce 2.0+
*
* @author Greg Rickaby
* @since 2.0.0
*/
function child_manage_woocommerce_styles() {

	remove_action( 'wp_head', array( $GLOBALS['woocommerce'], 'generator' ) );
	
	if ( is_front_page() || is_home() ) {
		wp_dequeue_style( 'woocommerce_frontend_styles' );
		wp_dequeue_style( 'woocommerce_fancybox_styles' );
		wp_dequeue_style( 'woocommerce_chosen_styles' );
		wp_dequeue_style( 'woocommerce_prettyPhoto_css' );
		wp_dequeue_script( 'wc_price_slider' );
		wp_dequeue_script( 'wc-single-product' );
		wp_dequeue_script( 'wc-add-to-cart' );
		wp_dequeue_script( 'wc-cart-fragments' );
		wp_dequeue_script( 'wc-checkout' );
		wp_dequeue_script( 'wc-add-to-cart-variation' );
		wp_dequeue_script( 'wc-single-product' );
		wp_dequeue_script( 'wc-cart' );
		wp_dequeue_script( 'wc-chosen' );
		wp_dequeue_script( 'woocommerce' );
		wp_dequeue_script( 'prettyPhoto' );
		wp_dequeue_script( 'prettyPhoto-init' );
		wp_dequeue_script( 'jquery-blockui' );
		wp_dequeue_script( 'jquery-placeholder' );
		wp_dequeue_script( 'fancybox' );
		wp_dequeue_script( 'jqueryui' );
	}
}

//Default woocommerce fields to wordpress fields
add_filter( 'woocommerce_checkout_fields' , 'custom_override_checkout_fields' );

function custom_override_checkout_fields( $fields ) {
	$current_user = wp_get_current_user();
    $fields['billing']['billing_first_name']['default'] = $current_user->user_firstname;
	$fields['billing']['billing_last_name']['default'] = $current_user->user_lastname;
    return $fields;
}
 
/**
* Remove the "Change Payment Method" button from the My Subscriptions table.
*
* This isn't actually necessary because @see eg_subscription_payment_method_cannot_be_changed()
* will prevent the button being displayed, however, it is included here as an example of how to
* remove just the button but allow the change payment method process.
*/
function eg_remove_my_subscriptions_button( $actions, $subscriptions ) {
 
	foreach ( $actions as $subscription_key => $action_buttons ) {
		foreach ( $action_buttons as $action => $button ) {
			switch ( $action ) {
				//case 'change_payment_method':	// Hide "Change Payment Method" button?
				case 'change_address': // Hide "Change Address" button?
				// case 'switch': // Hide "Switch Subscription" button?
				// case 'renew': // Hide "Renew" button on a cancelled subscription?
				// case 'pay': // Hide "Pay" button on subscriptions that are "on-hold" as they require payment?
				// case 'reactivate': // Hide "Reactive" button on subscriptions that are "on-hold"?
				// case 'cancel': // Hide "Cancel" button on subscriptions that are "active" or "on-hold"?
				//unset( $actions[ $subscription_key ][ $action ] );
				break;
				default:
				error_log( '-- $action = ' . print_r( $action, true ) );
				break;
			}
		}
	}
	 
	return $actions;
}
add_filter( 'woocommerce_my_account_my_subscriptions_actions', 'eg_remove_my_subscriptions_button', 100, 2 );

/**
* Change sort-code to BSB
*/

function wpse_77783_woo_bacs_ibn($translation, $text, $domain) {
    if ($domain == 'woocommerce') {
        switch ($text) {
            case 'Sort Code':
                $translation = 'BSB';
                break;

        }
    }

    return $translation;
}

add_filter('gettext', 'wpse_77783_woo_bacs_ibn', 10, 3);

/**
* Add a 1% surcharge to your cart / checkout
* change the $percentage to set the surcharge to a value to suit
* Uses the WooCommerce fees API
*
* Add to theme functions.php
* http://docs.woothemes.com/document/add-a-surcharge-to-cart-and-checkout-uses-fees-api/
* http://www.remicorson.com/add-custom-fee-to-woocommerce-cart-dynamically/
* https://wordpress.org/plugins/add-tip-woocommerce/
*/
//add_action( 'woocommerce_cart_calculate_fees','ec_woocommerce_custom_surcharge' );

function ec_woocommerce_custom_surcharge() {
global $woocommerce;
 
	if ( is_admin() && ! defined( 'DOING_AJAX' ) )
	return;
	 
	$percentage = 0.01;
	$surcharge = ( $woocommerce->cart->cart_contents_total + $woocommerce->cart->shipping_total ) * $percentage;
	$woocommerce->cart->add_fee( 'Surcharge', $surcharge, true, 'standard' );
 
}

function ec_woo_add_cart_fee() {
	global $woocommerce;
	$woocommerce->cart->add_fee( __('Custom', 'woocommerce'), 5 );
}

//add_action( 'woocommerce_before_calculate_totals', 'ec_woo_add_cart_fee' );

/**
* Show sale products
*/
function pjl_sale_products( $atts ){
global $woocommerce_loop, $woocommerce;

// Get page number from query (if set)
$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
$offset = ( 12* $paged ) - 12;

extract( shortcode_atts( array(
    'per_page'      => '12',
    'columns'       => '4',
    'orderby'       => 'title',
    'order'         => 'asc'
    ), $atts ) );

// Get products on sale
$product_ids_on_sale = woocommerce_get_product_ids_on_sale();

$meta_query = array();
$meta_query[] = $woocommerce->query->visibility_meta_query();
$meta_query[] = $woocommerce->query->stock_status_meta_query();
$meta_query   = array_filter( $meta_query );

$args = array(
    'paged'         => $paged, // Pass in page number from query
    'offset'        => $offset, // Pass in starting number
    'posts_per_page'=> $per_page,
    'orderby'         => $orderby,
    'order'         => $order,
    'no_found_rows' => 1,
    'post_status'     => 'publish',
    'post_type'     => 'product',
    'meta_query'     => $meta_query,
    'post__in'        => array_merge( array( 0 ), $product_ids_on_sale )
);

  ob_start();

$products = new WP_Query( $args );

$woocommerce_loop['columns'] = $columns;

if ( $products->have_posts() ) : ?>

    <?php woocommerce_product_loop_start(); ?>

        <?php while ( $products->have_posts() ) : $products->the_post(); ?>

            <?php woocommerce_get_template_part( 'content', 'product' ); ?>

        <?php endwhile; // end of the loop. ?>

    <?php woocommerce_product_loop_end(); ?>

<?php endif;

do_action('woocommerce_pagination');

wp_reset_postdata();

return '<div class="woocommerce">' . ob_get_clean() . '</div>';
}

add_shortcode('pjl_sale_products', 'pjl_sale_products');

/**
* Remove Woocommerce breadcrumbs
*/
add_action( 'init', 'eca_remove_wc_breadcrumbs' );

function eca_remove_wc_breadcrumbs() {
	remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20, 0 );
}

/**
* Remove Sale tag from products
*/
//Remove Sales Flash
add_filter('woocommerce_sale_flash', 'woo_custom_hide_sales_flash');

function woo_custom_hide_sales_flash()
{
	$cat_title = single_cat_title('', false);
	
	if ($cat_title === 'Sale' || $cat_title === 'Clearance') {
		return false;
	}
	else {
		return '<span class="onsale">Sale!</span>';
	}
}

/**
* Change number of related products on product page
* Set your own value for 'posts_per_page'
*
*/

//function woocommerce_output_related_products() {
	//woocommerce_related_products(4,1); // Display 4 products in rows of 2
//}

/**
* Add a $30/$23 surcharge to your cart / checkout based on delivery country for memberships/subscriptions
*
* Add countries to array('AU'); to exclude countries from surcharge
* http://en.wikipedia.org/wiki/ISO_3166-1#Current_codes for available alpha-2 country codes 
*
* Uses the WooCommerce fees API
*/
add_action( 'woocommerce_cart_calculate_fees','eca_woocommerce_international_membership_surcharge' );

function eca_woocommerce_international_membership_surcharge() {
	global $woocommerce;
 
	if ( is_admin() && ! defined( 'DOING_AJAX' ) )
		return;

	$subscription_fee = 0;
 	$country = array('AU');
	$membership_exists = false;
	$subscription_exists = false;

	//Virtual products don't have a shipping country so we need to check the customer object country
	//Memberships and subscriptions are setup as virtual products
	
	if ( !in_array( $woocommerce->customer->get_country(), $country ) ) :

		foreach($woocommerce->cart->get_cart() as $cart_item_key => $values ) {
			$SKU = $values['data']->get_sku();
			
			$membershipSKU = array('Individual', 'Organisation','Service', 'Concession');
			$subscriptionSKU = array('RIP', 'EDL', 'AJEC','EC','RIP-ORG', 'EDL-ORG', 'AJEC-ORG', 'EC-ORG');
			
			if (in_array($SKU, $membershipSKU)) {
				$membership_exists = true;
			}
			elseif (in_array($SKU, $subscriptionSKU)) {
				$subscription_exists = true;
			}
		}
		
		//At the moment we can only have one subscription in the cart at any one time but I have coded it so it will handle
		//this scenario in the future
		if ($membership_exists) {
			$subscription_fee = $subscription_fee + 30;
			//( $woocommerce->cart->cart_contents_total + $woocommerce->cart->shipping_total ) * $percentage;
			$woocommerce->cart->add_fee( 'International Membership Fee', $subscription_fee, true, 'standard' );
		}
		elseif ($subscription_exists) {
			$subscription_fee = $subscription_fee + 23;
			$woocommerce->cart->add_fee( 'International Subscription Fee', $subscription_fee, true, 'standard' );
		}
	endif;
	
	//Set the shipping fields for memberships and subscription orders
	//This only sets the data in the session variable, we need a direct DB update to update shipping data
	//$woocommerce->customer->set_shipping_country( $woocommerce->customer->get_country() );
}

/**
* Add hook to add additional user meta data on the cart page when a user is registered
* Use this to copy billing address to shipping for virtual products like memberships that have shipping address fields at checkout
*/
add_action( 'woocommerce_created_customer', 'eca_insert_member_shipping_meta' );

function eca_insert_member_shipping_meta( $user_id )
{
	global $woocommerce;
	
	 // Default virtual products number
	$virtual_products = 0;
	
	//Check for virtual cart products
	$products = $woocommerce->cart->get_cart();

	// Loop through cart products
	foreach( $products as $product ) {
		// Get product ID and '_virtual' post meta
		$product_id = $product['product_id'];
		$is_virtual = get_post_meta( $product_id, '_virtual', true );

		// Update $has_virtual_product if product is virtual
		if( $is_virtual == 'yes' )
			$virtual_products += 1;
	}

	if($virtual_products > 0) {
		$user_info = get_userdata($user_id);
		$billing_company = $user_info->billing_company;
		$address_line_2 = $woocommerce->customer->get_address_2();
		
		//Update all the shipping values, Address line 2 and Company Name is the only field not mandatory
		update_user_meta( $user_id, 'shipping_country', esc_attr($woocommerce->customer->get_country()));
		update_user_meta( $user_id, 'shipping_first_name', get_user_meta($user_id, 'first_name', true));
		update_user_meta( $user_id, 'shipping_last_name', get_user_meta($user_id, 'last_name', true));
		if ($billing_company)
			update_user_meta( $user_id, 'shipping_company', esc_attr());
		update_user_meta( $user_id, 'shipping_address_1', esc_attr($woocommerce->customer->get_address()));
		if($address_line_2)
			update_user_meta( $user_id, 'shipping_address_2', esc_attr($woocommerce->customer->get_address_2()));
		update_user_meta( $user_id, 'shipping_city', esc_attr($woocommerce->customer->get_city()));
		update_user_meta( $user_id, 'shipping_state', esc_attr($woocommerce->customer->get_state()));
		update_user_meta( $user_id, 'shipping_postcode', esc_attr($woocommerce->customer->get_postcode()));
	}
}

/** 
* Add WooCommerce customer username to edit/view order admin page
*/
add_action( 'woocommerce_admin_order_data_after_billing_address', 'woo_display_order_username', 10, 1 );
 
function woo_display_order_username( $order ){
 
	global $post;
	$customer_user = get_post_meta( $post->ID, '_customer_user', true );
	
	if(!empty($customer_user))
		echo '<p><strong style="display: block;">'.__('Customer Username').':</strong> <a href="user-edit.php?user_id=' . $customer_user . '">' . get_user_meta( $customer_user, 'nickname', true ) . '</a></p>';
}

/**
* Add discount to cart for bulk purchased items
* At present only the product Your child’s first year at school: Getting off to a good start
* To apply discounts we need coupons codes to be created in Woocommerce
* SKU PUB41
*
*/

//Decpreciated as of WooCommerce 2.3
//add_action( 'woocommerce_before_cart', 'es_add_discount_price_breaks', 1 );

function es_add_discount_price_breaks() {
    global $woocommerce;

	$products = $woocommerce->cart->get_cart(); //Returns the contents of the cart in an array with the 'data' element.
	$countYCFYS = 0;
	
	//print_readable_array($products);

	foreach( $products as $product ) {
		//All extended meta data is stored in a 'data' array, stores the WC_Product_Simple Object which contains
		//information about the product
		$_product = $product['data'];
		$SKU = $_product->get_sku();

		if ($SKU == 'PUB41') {
			$countYCFYS = $product['quantity'];
		}
	}

	//Remove all coupons before adding them again
	$woocommerce->cart->remove_coupons('ycfys-20-49');
	$woocommerce->cart->remove_coupons('ycfys-50');
    //if ( $woocommerce->cart->has_discount( $coupon_code ) ) return;

    if ( $countYCFYS >= 20 && $countYCFYS <= 49 ) {
        $woocommerce->cart->add_discount( 'ycfys-20-49' );
    }
	else if ($countYCFYS >= 50) {
		$woocommerce->cart->add_discount('ycfys-50');
	}
	
	//Display coupon code applied successfully
	//$woocommerce->show_messages();
	wc_print_notices();
}

/**
* Don't show the coupon code on cart or checkout to prevent users for using it other times
*/
function es_cart_change_coupon_label( $html , $coupon){

$html='Discount:';

return $html;

}

//Not required as we are using another solution as of WooCommerce v2.3
//add_filter('woocommerce_cart_totals_coupon_label', 'es_cart_change_coupon_label',11,2 );

/**
* Function to make Woocommerce check plugin directory for template overrides
* Need this for showing membership fields on thankyou page
* http://www.skyverge.com/blog/override-woocommerce-template-file-within-a-plugin/
*/

function es_plugin_path() {
  // gets the absolute path to this plugin directory
  return untrailingslashit( plugin_dir_path( __FILE__ ) );
}
 
add_filter( 'woocommerce_locate_template', 'es_woocommerce_locate_template', 10, 3 );

function es_woocommerce_locate_template( $template, $template_name, $template_path ) {
 
	global $woocommerce;

	$_template = $template;

	if ( ! $template_path ) $template_path = $woocommerce->template_url;
	$plugin_path  = es_plugin_path() . '/woocommerce/';

	// Look within passed path within the theme - this is priority
	$template = locate_template(
		array(
		  $template_path . $template_name,
		  $template_name
		)
	);

	// Modification: Get the template from this plugin, if it exists

	if ( ! $template && file_exists( $plugin_path . $template_name ) )
	$template = $plugin_path . $template_name;

	// Use default template
	if ( ! $template )
	$template = $_template;

	// Return what we found
	return $template;
}

/**
* BCC Email for orders completed
*/

//add_filter( 'woocommerce_email_headers', 'es_headers_filter_function', 10, 3);

function es_headers_filter_function( $headers, $id, $object ) {
    if ($id == 'customer_completed_order') {
        $headers .= 'BCC: Sales <sales@earlychildhood.org.au>' . "\r\n";
    }

    return $headers;
}

/** 
* Add WooCommerce do not report checkbox to edit/view order admin page
*/
add_action( 'woocommerce_admin_order_data_after_shipping_address', 'es_woocommerce_admin_do_not_report_checkbox', 10, 1 );
 
function es_woocommerce_admin_do_not_report_checkbox( $order ){
 
	global $post;
	
	$include_reporting = get_post_meta( $post->ID, 'dont_incl_reporting', true );
	$dont_send_woo_emails = get_post_meta( $post->ID, 'dont_send_woo_emails', true );

	echo '<p><strong style="display: block;">'.__('Miscellaneous').':</strong>';
	
	if ($include_reporting == 'on')
		echo '<input type="checkbox" name="dont_incl_reporting" id="dont_incl_reporting" checked="">Do not include in reporting';
	else
		echo '<input type="checkbox" name="dont_incl_reporting" id="dont_incl_reporting" >Do not include in reporting';
	
	if($dont_send_woo_emails == 'on')
		echo '<br><input type="checkbox" name="dont_send_woo_emails" id="dont_send_woo_emails" checked="">Do not send Woo emails';
	else
		echo '<br><input type="checkbox" name="dont_send_woo_emails" id="dont_send_woo_emails" >Do not send Woo emails';
	
	echo '</p>';
}

/**
* Save custom fields in administration order page, POST variables available in this hook
*/

add_action( 'woocommerce_process_shop_order_meta', 'es_woocommerce_admin_save_custom_fields', 10, 2 );

function es_woocommerce_admin_save_custom_fields ( $post_id, $post ) {
    
	if (isset($_POST["dont_incl_reporting"])) {
		$include_reporting = $_POST["dont_incl_reporting"];
		update_post_meta($post_id, 'dont_incl_reporting', $include_reporting);
	}
	else {
		update_post_meta($post_id, 'dont_incl_reporting', '');
	}
	
	if (isset($_POST['dont_send_woo_emails'])) {
		update_post_meta($post_id, 'dont_send_woo_emails', $_POST['dont_send_woo_emails']);
	}
	else {
		update_post_meta($post_id, 'dont_send_woo_emails', '');
	}
}

/**
* Functionality to add free shipping to cart when specific criteria is met
* At present just used for Your Child’s First Year At School for quantities over 50
* For future products this should be it's own plugin so users can customise through front end
*/

//Depreciated function as of WooCommerce v2.3
//add_filter( 'woocommerce_available_shipping_methods', 'es_remove_standard_shippings_when_free' , 10, 1 );
 
function es_remove_standard_shippings_when_free( $available_methods ) {
	global $woocommerce;

	//Use this to show all the shipping methods for debugging
	//var_dump( $available_methods );
	
	$products = $woocommerce->cart->get_cart(); //Returns the contents of the cart in an array with the 'data' element.
	$countYCFYS = 0;
	
	//print_readable_array($products);

	foreach( $products as $product ) {
		//All extended meta data is stored in a 'data' array, stores the WC_Product_Simple Object which contains
		//information about the product
		$_product = $product['data'];
		$SKU = $_product->get_sku();

		if ($SKU == 'PUB41') {
			$countYCFYS = $product['quantity'];
		}
	}
 
	//Add free shipping if over 50 copies
	if ($countYCFYS >= 50 OR es_has_coupon_free_shipping())
		$available_methods['free_shipping'] = new WC_Shipping_Rate('free_shipping', 'Free Shipping', 0, array(), '');
	else
		unset( $available_methods['free_shipping'] );
 
    if( isset( $available_methods['free_shipping'] ) ) {
        // remove all australia post shipping options
        unset( $available_methods['australia_post:AUS_PARCEL_EXPRESS'] );
        unset( $available_methods['australia_post:AUS_PARCEL_REGULAR'] );
    }
	
	return $available_methods;
}

/**
* check for coupon with free shipping
* @return bool
*/
function es_has_coupon_free_shipping() {
    global $woocommerce;

    foreach ($woocommerce->cart->applied_coupons as $code) {
        $coupon = new WC_Coupon($code);
        if ($coupon->is_valid() === true) {
            if ($coupon->enable_free_shipping()) {
                return true;
            }
        }
    }

    return false;
}

//add_action('cancelled_subscription', 'es_set_cancelled_subscription_to_on_hold',10,2);

function es_set_cancelled_subscription_to_on_hold($user_id, $subscription_key) {
	//Update status to on-hold instead of cancelled due to an issue
	//where completing an order does not reactivate the subscription
	//To reactivate subscription the order status must be on-hold
	//Modify woocommerce-subscriptions/classes/class-wc-subscriptions-manager.php
	//Line 1070 modify this
	//if ( ( $order_uses_manual_payments || $payment_gateway->supports( 'subscription_reactivation' ) ) && ($subscription['status'] == 'on-hold' || $subscription['status'] == 'cancelled')) {
		//$subscription_can_be_changed = true;
}

/**
* Remove email notification when checkbox is ticked on orders
*/
add_action( 'woocommerce_email', 'es_unhook_those_pesky_emails' );
 
function es_unhook_those_pesky_emails( $email_class ) {
	global $post;
	/**
	* Hooks for sending emails during store events
	**/
	//remove_action( 'woocommerce_low_stock_notification', array( $email_class, 'low_stock' ) );
	//remove_action( 'woocommerce_no_stock_notification', array( $email_class, 'no_stock' ) );
	//remove_action( 'woocommerce_product_on_backorder_notification', array( $email_class, 'backorder' ) );
	
	$dont_send_woo_emails = get_post_meta($post->ID, 'dont_send_woo_emails', true);

	if($dont_send_woo_emails == 'on') {
		// New order emails
		remove_action( 'woocommerce_order_status_pending_to_processing_notification', array( $email_class->emails['WC_Email_New_Order'], 'trigger' ) );
		remove_action( 'woocommerce_order_status_pending_to_completed_notification', array( $email_class->emails['WC_Email_New_Order'], 'trigger' ) );
		remove_action( 'woocommerce_order_status_pending_to_on-hold_notification', array( $email_class->emails['WC_Email_New_Order'], 'trigger' ) );
		remove_action( 'woocommerce_order_status_failed_to_processing_notification', array( $email_class->emails['WC_Email_New_Order'], 'trigger' ) );
		remove_action( 'woocommerce_order_status_failed_to_completed_notification', array( $email_class->emails['WC_Email_New_Order'], 'trigger' ) );
		remove_action( 'woocommerce_order_status_failed_to_on-hold_notification', array( $email_class->emails['WC_Email_New_Order'], 'trigger' ) );
		// Processing order emails
		remove_action( 'woocommerce_order_status_pending_to_processing_notification', array( $email_class->emails['WC_Email_Customer_Processing_Order'], 'trigger' ) );
		remove_action( 'woocommerce_order_status_pending_to_on-hold_notification', array( $email_class->emails['WC_Email_Customer_Processing_Order'], 'trigger' ) );
		// Completed order emails
		remove_action( 'woocommerce_order_status_completed_notification', array( $email_class->emails['WC_Email_Customer_Completed_Order'], 'trigger' ) );
		// Note emails
		remove_action( 'woocommerce_new_customer_note_notification', array( $email_class->emails['WC_Email_Customer_Note'], 'trigger' ) );
	}
}


/**
* Add Member pricing option to products metabox
*/
add_action('woocommerce_product_options_pricing', 'es_show_member_price' );

function es_show_member_price($thepostid) {
	global $thepostid;
	// Price
	woocommerce_wp_text_input( array( 'id' => 'member_price', 
	'label' => __( 'Member Price', 'woocommerce' ) . ' (' . get_woocommerce_currency_symbol() . ')', 
	'data_type' => 'price',
	'value'       => get_post_meta( $thepostid, 'member_price', true )));
}

add_action( 'woocommerce_process_product_meta_simple', 'es_woocommerce_admin_save_custom_product_fields');

function es_woocommerce_admin_save_custom_product_fields($post_id) {
	if ( ! empty( $_POST['member_price'] ) ) {
		update_post_meta( $post_id, 'member_price', $_POST['member_price'] );
	}
	else {
		delete_post_meta( $post_id, 'member_price' );
	}
}

/**
* This actions is required to update custom meta data for products
* such as subscriptions
*/
add_action( 'woocommerce_process_product_meta', 'es_woocommerce_admin_save_non_simple_custom_product_fields');

function es_woocommerce_admin_save_non_simple_custom_product_fields($post_id) {
	
	if (!empty($_POST['member_price'])) {
		update_post_meta($post_id, 'member_price', $_POST['member_price']);
	}
}

add_filter( 'woocommerce_get_sale_price', 'es_set_sale_price', 9, 2 );
add_filter( 'woocommerce_get_price', 'es_set_sale_price', 9, 2 );
add_action( 'wp_footer', 'es_sale_notice' );

/**
* Display sale notice at top of site.
*/
function es_sale_notice() {
	$date_now = new DateTime();
	$start_dtm = new DateTime( '2016-10-03' );
	$end_dtm = new DateTime( '2016-10-11' );
	
	if ( $date_now >= $start_dtm AND $date_now <= $end_dtm )
		echo apply_filters( 'woocommerce_demo_store', '<p class="demo_store">The ECA National Conference Sale! Offer ends 11 October. Terms and conditions apply.</p>'  );
}

/**
* Override prices with 10% off store wide.
*/
function es_set_sale_price( $price, $product ) {
	
	$exclude_skus = array( 'COEBRCE', 'COEPOSTER', 'PUB41' );
	$exclude_cats = array( 'specials', 'clearance' );
	$date_now = new DateTime();
	$start_dtm = new DateTime( '2016-10-03' );
	$end_dtm = new DateTime( '2016-10-11' );
	
	// exclude SKUs
	if ( in_array( $product->sku, $exclude_skus ) )
		return $price;
	
	// exclude specials category
	$product_cat = wp_get_post_terms( $product->id, 'product_cat' );
	
	foreach ( $product_cat as $term ){
		if( in_array( $term->slug, $exclude_cats ) ){
			return $price;
		}
	}	
	
	if ( ! $product->is_virtual() AND ( $date_now >= $start_dtm AND $date_now <= $end_dtm ) ) {
		$member_exists = es_check_membership_held();
		$member_price = get_post_meta($product->id, 'member_price', true);
		$price = $product->get_regular_price() * ( 0.9 );
		
		if ( ! empty( $member_price ) AND $member_exists ) {
			$product->set_price( $member_price );
			$product->sale_price = $price;
			$price = $member_price;		
		}
		else {
			$product->set_price( $price );
			$product->sale_price = $price;	
		}
		
	}
	
	return $price;
}

/**
 * Display custom pricing in admin screens and frontend.
 *
 * @param string $price
 * @param array $product
 */
add_filter( 'woocommerce_get_price_html', 'es_override_product_price', 11, 2 );

function es_override_product_price($price, $product) {

	if ( ! empty( $product ) AND ! is_admin() ) {
		$display_price         = $product->get_display_price();
		$display_regular_price = $product->get_display_price( $product->get_regular_price() );
		$sale_price = $product->sale_price;
		
		//Check if logged in user is a member
		$member_exists = es_check_membership_held();
		//Check for membership price
		$member_price = get_post_meta($product->id, 'member_price', true);
		
		if($product->is_on_sale() && $member_exists && $member_price > 0)
			$price = '<ins style="display: block;color:#000">'.wc_price($product->get_regular_price()).$product->get_price_suffix().'</ins>';
		elseif($product->is_on_sale() && !$member_exists)
			$price = '<ins style="display: block;color:#000">'.wc_price($product->get_regular_price()).$product->get_price_suffix().'</ins>';
		elseif($member_exists && $product->is_on_sale() && empty($member_price))
			$price = '<ins style="display: block;color:#000">'.wc_price($product->get_regular_price()).$product->get_price_suffix().'</ins>';
		elseif(!$product->is_on_sale() && !$member_exists)
			$price = '<ins style="display: block;color:#000">'.wc_price($product->get_regular_price()).$product->get_price_suffix().' <i class="fa fa-check"></i></ins>';
		elseif($member_exists && !$product->is_on_sale() && empty($member_price))
			$price = '<ins style="display: block;color:#000">'.wc_price($product->get_regular_price()).$product->get_price_suffix().' <i class="fa fa-check"></i></ins>';		
		else
			$price = '<ins style="display: block;color:#000">'.wc_price($product->get_regular_price()).$product->get_price_suffix().'</ins>';
		
		if($product->get_price() > 0) {

			if($product->is_on_sale() && $member_exists && $member_price > 0)
				$price .= '<ins style="display: block;">'.wc_price( $sale_price ).$product->get_price_suffix().' Sale Price</ins>';
			elseif($product->is_on_sale() && !$member_exists)
				$price .= '<ins style="display: block;">'.wc_price( $sale_price ).$product->get_price_suffix().' Sale Price <i class="fa fa-check"></i></ins>';
			elseif($product->is_on_sale() && $member_exists && empty($member_price))
				$price .= '<ins style="display: block;">'.wc_price( $sale_price ).$product->get_price_suffix().' Sale Price <i class="fa fa-check"></i></ins>';
			
			if($member_price > 0 && $member_exists)
				$price .= '<ins style="display: block;color:#77A464">'.wc_price( $member_price ).$product->get_price_suffix().' Member Price <i class="fa fa-check"></i></ins>';
			elseif ($member_price > 0)
				$price .= '<ins style="display: block;color:#77A464">'.wc_price( $member_price ).$product->get_price_suffix().' Member Price</ins>';
			
		} elseif ( $product->get_price() == 0 ) {

			if ( $product->is_on_sale() && $product->get_regular_price() ) {

				$price .= $product->get_price_html_from_to( $display_regular_price, __( 'Free!', 'woocommerce' ) );

			} else {
				$price = __( 'Free!', 'woocommerce' );
			}
		}
	}
	else if ( ! empty( $product ) AND is_admin() ) {
		$display_price = $product->get_display_price();
		$display_regular_price = $product->get_display_price( $product->get_regular_price() );
		$sale_price = $product->sale_price;
		$member_price = get_post_meta( $product->id, 'member_price', true );
		
		if( $product->is_on_sale() && $member_exists && $member_price > 0 )
			$price = '<ins style="display: block;color:#000">'.wc_price($product->get_regular_price()).$product->get_price_suffix().'</ins>';
		
		if( $product->get_price() > 0 ) {

			if ( $member_price > 0 )
				$price .= '<span style="display: block;color:#77A464">'.wc_price( $member_price ).$product->get_price_suffix().' Member Price</span>';

		} 
		elseif ( $product->get_price() == 0 ) {
			if ( $product->is_on_sale() && $product->get_regular_price() ) {
				$price .= $product->get_price_html_from_to( $display_regular_price, __( 'Free!', 'woocommerce' ) );
			} 
			else {
				$price = __( 'Free!', 'woocommerce' );
			}
		}
	}
	
	return  $price;
}

/*
* Issue here remove this code - AS 19/07/2016
*/
//add_filter('woocommerce_cart_item_subtotal', 'es_set_cart_product_subtotal', 20, 3);

function es_set_cart_product_subtotal($sub_total , $cart_item, $cart_item_key) {
	
	$quantity = intval( $cart_item['quantity'] );
	$price = es_get_product_price($cart_item);
	$cart_item['data']->price = ( $price * $quantity );	
	
	return wc_price($cart_item['data']->price);
}

/**
* Cart subtotal
*/

function es_set_cart_subtotal( $cart_object ) {

	foreach ( $cart_object->cart_contents as $key => $value ) {	
		$price = es_get_product_price($value);
		$value['data']->price = $price;
	}
}

add_action( 'woocommerce_before_calculate_totals', 'es_set_cart_subtotal', 20, 1 );

/**
* Price Override Functions
*/

function es_get_product_price($cart_item) {
	$product_id = intval( $cart_item['data']->id );
	$quantity = intval( $cart_item['quantity'] );
	
	$meta = get_post_meta($product_id);
	if ( isset( $meta['_sale_price'][0] ) )
		$sale_price = $meta['_sale_price'][0];
	$normal_price = $meta['_regular_price'][0];
	if ( isset( $meta['member_price'][0] ) )
		$member_price = $meta['member_price'][0];
	else 
		$member_price = '';
	$sku = $meta['_sku'][0];
	
	//Check if logged in user is a member
	$member_exists = es_check_membership_held();
	
	//write_log('Normal '.$normal_price.' Sale '.$sale_price.' Member '.$member_price);
	
	if ( $normal_price > 0 ) {
		
		if($member_price > 0 && $member_exists) {
			if ($sale_price > $member_price) {
				$price = $member_price;
			}
			elseif($sale_price < $member_price && $sale_price > 0) {
				$price = $sale_price;
			}
			else {
				$price = $member_price;
			}
		}
		elseif ( $sale_price > 0) {
			$price = $sale_price;
		}
		else {
			$price = $normal_price;
		}
	} elseif ( $normal_price == 0 ) {
		if ( $sale_price > 0 ) {
			$price = __( 'Free!', 'woocommerce' );

		} else {
			$price = __( 'Free!', 'woocommerce' );
		}
	}
	
	//Check if product has a bulk discount
	if(isset($sku))
		$price = es_product_bulk_price($price, $quantity, $sku);
	
	return $price;
}

/**
* Modify prices based on bulk discounts
*/
function es_product_bulk_price($price, $quantity, $sku) {
	/*
	* PUB41 - Your child’s first year at school Bulk discount
	* For orders of x >= 20 $12.95, x >= 50 $9.90
	*/
	if ($sku == 'PUB41') {
		if ($quantity >= 20 AND $quantity < 50) {
			$price = 12.95;
		}
		elseif ($quantity >= 50) {
			$price = 9.95;
		}
	}
	elseif($sku == 'SUND507') {
		if ($quantity >= 3)
			$price = 10.95;
	}
	elseif($sku == 'SUND621') {
		if ($quantity >= 3)
			$price = 16.95;
	}
	elseif($sku == 'SUND620') {
		if ($quantity >= 5)
			$price = 14.95;
	}
	
	return $price;
}

/**
 * woocommerce_package_rates is a 2.1+ hook
 */
add_filter( 'woocommerce_package_rates', 'hide_shipping_when_free_is_available', 10, 2 );
 
/**
 * Hide shipping rates when free shipping is available
 *
 * @param array $rates Array of rates found for the package
 * @param array $package The package array/object being shipped
 * @return array of modified rates
 */
function hide_shipping_when_free_is_available( $rates, $package ) {
 	
 	global $woocommerce;
	
	//Stop promotion after 1st June
	//if( '20160601' > date("Ymd") ) {
	
	$products = $woocommerce->cart->get_cart(); //Returns the contents of the cart in an array with the 'data' element.
	$count_pub41 = 0;
	$only_pub41 = true;
	
	foreach( $products as $product ) {
		//All extended meta data is stored in a 'data' array, stores the WC_Product_Simple Object which contains
		//information about the product
		
		$_product = $product['data'];
		$SKU = $_product->get_sku();

		if ($SKU == 'PUB41') {
			$count_pub41 = $product['quantity'];
		}
		else if (!$_product->is_virtual()) {
			$only_pub41 = false;
		}
	}
 
	if ( $count_pub41 >= 50 AND $only_pub41 ) {
		//$rates['free_shipping'] = new WC_Shipping_Rate('free_shipping', 'Free Shipping', 0, array(), '');
		unset( $rates['flat_rate'] );
		unset( $rates['eca_shipping_express'] );
		//unset( $rates['eca_shipping']);
		$rates['eca_shipping']->cost = 0;
	}
	//}
	
	return $rates;
}

/**
* Add free shipping for specific SKU's
*
* This NEEDS to be addded back in along with Free Shipping enabled in WooCommerce
* settings ie checkbox ticked.
* Only required if we move back to using Australia Post plugin
*/
//add_filter( 'woocommerce_cart_shipping_packages', 'es_free_woocommerce_cart_shipping_packages' );

function es_free_woocommerce_cart_shipping_packages( $packages ) {
    // Reset the packages
    $packages = array();
  
    // Cart items
    $free_items   = array();
    $regular_items = array();
	
	//Check for free shipping coupon
	$free_shipping = es_has_coupon_free_shipping();
    
    // Sort bulky from regular
    foreach ( WC()->cart->get_cart() as $item ) {
		if ($free_shipping) {
			$free_items[] = $item;
		}
        elseif ( $item['data']->needs_shipping() ) {
            if ( $item['data']->get_sku() == 'PUB41' AND $item['quantity'] >= 50 ) {
                $free_items[] = $item;
            } else {
                $regular_items[] = $item;
            }
        }
    }
	
	//write_log(print_r($free_items, true));
    
    // Put inside packages
    if ( $free_items ) {
        $packages[] = array(
            'ship_via'        => array( 'free_shipping' ),
            'contents'        => $free_items,
            'contents_cost'   => array_sum( wp_list_pluck( $free_items, 'line_total' ) ),
            'applied_coupons' => WC()->cart->applied_coupons,
            'destination'     => array(
                'country'   => WC()->customer->get_shipping_country(),
                'state'     => WC()->customer->get_shipping_state(),
                'postcode'  => WC()->customer->get_shipping_postcode(),
                'city'      => WC()->customer->get_shipping_city(),
                'address'   => WC()->customer->get_shipping_address(),
                'address_2' => WC()->customer->get_shipping_address_2()
            )
        );
    }
    if ( $regular_items ) {
        $packages[] = array(
			'ship_via'        => array( 'australia_post' ),
            'contents'        => $regular_items,
            'contents_cost'   => array_sum( wp_list_pluck( $regular_items, 'line_total' ) ),
            'applied_coupons' => WC()->cart->applied_coupons,
            'destination'     => array(
                'country'   => WC()->customer->get_shipping_country(),
                'state'     => WC()->customer->get_shipping_state(),
                'postcode'  => WC()->customer->get_shipping_postcode(),
                'city'      => WC()->customer->get_shipping_city(),
                'address'   => WC()->customer->get_shipping_address(),
                'address_2' => WC()->customer->get_shipping_address_2()
            )
        );
    }    
    
    return $packages;
}

/**
* Add custom countries
*/
function woo_add_my_country( $country ) {
  $country["AE-AS"] = 'Samoa';  
	return $country; 
}

//add_filter( 'woocommerce_countries', 'woo_add_my_country', 10, 1 );

// Rename 'Republic of Ireland' to 'Ireland'

add_filter( 'woocommerce_countries', 'rename_samoa' );

function rename_samoa( $countries ) {
	$countries['AS'] = 'Samoa';
	return $countries;
}

/**
* Display 16 products per page
*/
add_filter( 'loop_shop_per_page', create_function( '$cols', 'return 16;' ), 20 );

/**
* Filters the $order->get_items() method results to order all line items by product name
*/
add_filter( 'woocommerce_order_get_items', function( $items, $order ) {
 
	uasort( $items, 
		function( $a, $b ) { 
			return strnatcmp( $a['name'], $b['name'] ); 
		}
	);

	return $items;
 
}, 10, 2 );

/*
 * Hide prdoucts in the category "Front End Hidden" from shop users 
 *
 */
add_action( 'pre_get_posts', 'es_custom_pre_get_posts_query' );

function es_custom_pre_get_posts_query( $q )
{

	if (!$q->is_main_query() || !is_shop()) 
		return;
	
	if ( ! is_admin() ) {
		$q->set( 'tax_query', array(array(
			'taxonomy' => 'product_cat',
			'field' => 'id',
			'terms' => array( 195 ),
			'operator' => 'NOT IN')));

	}
}

/*
* Hide categories that have slugs entered in this custom shortcode 
* that replaces the WooCommerce version
*/

add_shortcode('es_product_categories', 'es_product_categories');

function es_product_categories( $atts ) {
	global $woocommerce_loop;

	$atts = shortcode_atts( array(
		'number'     => null,
		'orderby'    => 'name',
		'order'      => 'ASC',
		'columns'    => '4',
		'hide_empty' => 1,
		'parent'     => '',
		'ids'        => ''
	), $atts );

	if ( isset( $atts['ids'] ) ) {
		$ids = explode( ',', $atts['ids'] );
		$ids = array_map( 'trim', $ids );
	} else {
		$ids = array();
	}

	$hide_empty = ( $atts['hide_empty'] == true || $atts['hide_empty'] == 1 ) ? 1 : 0;

	// get terms and workaround WP bug with parents/pad counts
	$args = array(
		'orderby'    => $atts['orderby'],
		'order'      => $atts['order'],
		'hide_empty' => $hide_empty,
		'include'    => $ids,
		'pad_counts' => true,
		'child_of'   => $atts['parent']
	);

	$product_categories = get_terms( 'product_cat', $args );

	if ( '' !== $atts['parent'] ) {
		$product_categories = wp_list_filter( $product_categories, array( 'parent' => $atts['parent'] ) );
	}

	if ( $hide_empty ) {
		foreach ( $product_categories as $key => $category ) {
			if ( $category->count == 0 ) {
				unset( $product_categories[ $key ] );
			}
			else if ($category->slug == 'hidden-front-end-user' ) {
				//Remove your category slugs here from displaying
				unset( $product_categories[ $key ] );
			}
		}
	}

	if ( $atts['number'] ) {
		$product_categories = array_slice( $product_categories, 0, $atts['number'] );
	}

	$columns = absint( $atts['columns'] );
	$woocommerce_loop['columns'] = $columns;

	ob_start();

	// Reset loop/columns globals when starting a new loop
	$woocommerce_loop['loop'] = $woocommerce_loop['column'] = '';

	if ( $product_categories ) {
		woocommerce_product_loop_start();

		foreach ( $product_categories as $category ) {
			wc_get_template( 'content-product_cat.php', array(
				'category' => $category
			) );
		}

		woocommerce_product_loop_end();
	}

	woocommerce_reset_loop();

	return '<div class="woocommerce columns-' . $columns . '">' . ob_get_clean() . '</div>';
}

/*
* Hide category on single product page for hidden from front end users
*/

// Remove actions on init after they have hooked
function es_add_product_meta_action() {
	
	remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40 );
	
	if(is_product()) {
		global $product;
	
		$hide_category = false;
		$product_cats = wp_get_post_terms( $product->id, 'product_cat' );
	
		foreach($product_cats as $category) {
			if ($category->slug != 'hidden-front-end-user'){
				add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40 );
				break;
			}
		}	
	}
}

add_action( 'woocommerce_single_product_summary', 'es_add_product_meta_action' );

/**
* Add a new ordering drop down item for items with member prices
*/
add_filter('woocommerce_catalog_orderby', 'es_add_catalog_order_by_member_prices');

function es_add_catalog_order_by_member_prices($catalog_orderby_options) {
	
	//Remove specific sorting options
	unset($catalog_orderby_options["rating"]);

	$catalog_orderby_options['member_price'] = __( 'Sort by member price', 'woocommerce' );
	
	return $catalog_orderby_options;
}

add_filter('woocommerce_get_catalog_ordering_args', 'es_add_catalog_order_args_member_price');

function es_add_catalog_order_args_member_price($args) {
	
	if(isset( $_GET['orderby'] )) {
		$orderby_value = woocommerce_clean( $_GET['orderby'] );
		
		if($orderby_value == 'member_price') {
			$args['meta_key'] = 'member_price';
			$args['order'] = 'ASC';
			$args['orderby'] = 'meta_value_num';
		}
	}
	
	return $args;
}

/**
 * My Account functionality
 * Tabbed my account page introduced in WC 2.6
 * Removed php file my-account-extra-meta.php
 * Function woocommerce_before_my_account is now depreciated
 */

add_action ( 'woocommerce_account_dashboard', 'es_display_my_dashboard_customer_number');

function es_display_my_dashboard_customer_number() {
	$user_id = get_current_user_id();
	$member_number = get_user_meta($user_id, 'old_customer_number', true);

	if ( !empty( $member_number) ) {
		$customer_number = $member_number;
	}
	else {
		$customer_number = 'A' . $user_id;
	}
	
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

	ob_start();
	
	?>
	<h2>Customer Number</h2>
	<div style="width: 150px;color:#ED1C24;border-width: 0px 1px 1px 0px;padding: 4px 8px;vertical-align: middle;border: 1px solid rgba(0, 0, 0, 0.1);font-size: 1.4em !important;">
		<?php echo $customer_number;?>
	</div>
	<?php
	// Check Pay Per Post plugin is active
	if ( class_exists( 'Woocommerce_PayPerPost' ) ) {
		$pay_per_posts = do_shortcode( '[woocommerce-payperpost template="purchased"]' );
		
		if( ! empty( $pay_per_posts ) ) {
			echo '<h2>My Online Purchases</h2>' . $pay_per_posts;
		}
	}
	
	// Check for plugin Groups
	if ( class_exists( 'Groups_WordPress' ) ) {
		$user_groups_string = do_shortcode_func ( 'groups_user_groups', $user_array );
		
		// Check for User Meta Plugin
		if ( class_exists( 'userMeta' ) ) {
			if ( strpos( $user_groups_string, 'Individual' ) ) {
				echo '<h2>Individual Questions</h2>';
				echo do_shortcode( '[user-meta type="profile" form="Individual Member"]' );
			}
			else if ( strpos( $user_groups_string, 'Service' ) ) {
				echo '<h2>Service Questions</h2>';
				echo do_shortcode( '[user-meta type="profile" form="Service Member"]' );
				if ( class_exists( 'wpDataTableConstructor' ) ) {
					echo '<h2>Contacts</h2>';
					echo do_shortcode( '[wpdatatable id=1]' );
				}
			}
			else if ( strpos( $user_groups_string, 'Organisation' ) ) {
				echo '<h2>Organisation Questions</h2>';
				echo do_shortcode( '[user-meta type="profile" form="Organisation Member"]' );
				if ( class_exists( 'wpDataTableConstructor' ) ) {
					echo '<h2>Contacts</h2>';
					echo do_shortcode( '[wpdatatable id=1]' );
				}
			}
			else if ( strpos( $user_groups_string, 'Concession' ) ) {
				echo '<h2>Concession Questions</h2>';
				echo do_shortcode( '[user-meta type="profile" form="Concession Member"]' );
			}
		}
	}
	
	ob_end_flush();
}

add_filter( 'woocommerce_product_filters', 'es_custom_product_filters', 10, 1 );

/**
 * Add a custom filters for WooCommerce products.
 *
 * @param string $output
 */
function es_custom_product_filters( $output ) {
	global $wp_query;
	
	foreach ( $wp_query->query_vars['meta_query'] as $key => $value ) {
		if ( $value['key'] == '_backorders' ) {
			$current_backorders_value = isset( $value['value'] ) ? $value['value'] : '';
		}
		elseif ( $value['key'] == 'member_price' ) {
			$curent_price_value = isset( $value['value'] ) ? $value['value'] : '';
			$current_price_key = $value['key'];
		}
		elseif ( $value['key'] == '_sale_price' ) {
			$curent_price_value = isset( $value['value'] ) ? $value['value'] : '';
			$current_price_key = $value['key'];
		}
	}
	
	//$current_backorders_value = isset( $wp_query->query_vars['meta_value'] ) ? $wp_query->query_vars['meta_value'] : '';
	
	$output .= '<select id="allow-backorders-filter" class="backorders" name="allow_backorders_filter">';
	$output .= '<option value="" ' .  selected( $current_backorders_value, '', false ) . '>' . __( 'Allow backorders?', 'woocommerce' ) . '</option>';
	//<option selected="selected" value="">Allow backorders?</option>';

	$options = array(
		'no'     => __( 'Do not allow', 'woocommerce' ),
		'notify' => __( 'Allow, but notify customer', 'woocommerce' ),
		'yes'    => __( 'Allow', 'woocommerce' )
	);
	
	foreach ( $options as $key => $value ) {
		$output .= '<option value="' . esc_attr( $key ) . '"' . selected( $current_backorders_value, $key, false ) . '>'. $value .'</option>';
	}
	
	$output .= '</select>';
	
	$output .='<select id="price-filter" class="" name="price_filter">
				<option value="" ' . selected( $curent_price_value, '', false ) . '>Show All Prices</option>
				<option value="member-price" ' . selected( $current_price_key, 'member_price', false ) . '>Member Prices</option>
				<option value="sale-price" ' . selected( $current_price_key, '_sale_price', false ) . '>Sale Prices</option>
				</select>';
	
	return $output;
}

add_filter ( 'parse_query', 'es_filter_products_query' );

/**
 * Filter the products in admin based on options.
 *
 * @param mixed $query object passed by reference
 */
function es_filter_products_query( &$query ) {
	global $typenow, $wp_query;

	if ( 'product' == $typenow ) {
		if ( isset( $_GET['allow_backorders_filter'] ) && ! empty ( $_GET['allow_backorders_filter'] ) ) {
			$backorders = array(
                'key' => '_backorders',
                'value' => $_GET['allow_backorders_filter'],
                'compare' => '='
            );
			
			$meta_query_array[] = $backorders;
			//$query->query_vars['meta_value']    = $_GET['allow_backorders_filter'];
			//$query->query_vars['meta_key']      = '_backorders';
		}
		
		if ( isset( $_GET['price_filter'] ) && ! empty ( $_GET['price_filter'] ) ) {
			
			if ( $_GET['price_filter'] == 'member-price' )
				$prices = array(
					'key' => 'member_price',
					'value' => 0,
					'compare' => '>'
				);
			elseif ( $_GET['price_filter'] == 'sale-price' )
				$prices = array(
					'key' => '_sale_price',
					'value' => 0,
					'compare' => '>'
				);
			
			$meta_query_array[] = $prices;
			//$query->query_vars['meta_value']    = $_GET['allow_backorders_filter'];
			//$query->query_vars['meta_key']      = '_backorders';
		}
		
		if (!empty($meta_query_array))
			$query->query_vars['meta_query'] = $meta_query_array;
	}
}

/**
 * Change the order items sort order in documents in v3.0.2+
 *
 * @param string $sort_order_items_key The column key (such as 'sku', 'price', 'weight', etc.) to sort order items by
 * @param int $order_id The WC_Order id
 * @param string $document_type The type of document being viewed
 * @return string The filtered sort column key
 */
function sv_wc_pip_document_sort_order_items_key( $sort_by, $order_id, $type ) {
	// sort order items in all document types by SKU.
	$sort_by = 'sku';

	return $sort_by;
}

add_filter( 'wc_pip_document_sort_order_items_key', 'sv_wc_pip_document_sort_order_items_key', 10, 3 );

/**
 * Removes packing list / pick list sorting by category and
 *   outputs line items in alphabetical order
 */
add_filter( 'wc_pip_packing_list_group_items_by_category', '__return_false' );

/**
 * Example: Remove product grouping if an order is not yet paid. (WC 2.5+)
 * Only removes grouping for pick lists
 * Requires PIP 3.1.1+
 *
 * @param bool $group_items true if items should be grouped by category
 * @param int $order_id the ID for the document's order
 * @param string $document_type the type for the current document
 * @return bool
 */
function sv_wc_pip_packing_list_grouping( $group_items, $order_id, $document_type ) {
	// bail unless we're looking at a pick list
	if ( 'pick-list' !== $document_type ) {
		return $group_items;
	}
	$order = wc_get_order( $order_id );
	if ( ! $order->is_paid() ) {
		return false;
	}
	return $group_items;
}
add_filter( 'wc_pip_packing_list_group_items_by_category', 'sv_wc_pip_packing_list_grouping', 10, 3 );

/**
 * Modify the header for packing slips.
 * Use the format order number - order date
 *
 */
add_filter( 'wc_pip_document_heading', 'es_woocommerce_adjust_packing_slip_heading', 20, 4 );

function es_woocommerce_adjust_packing_slip_heading( $heading, $type, $action, $order ) {
	if ( class_exists( 'WC_PIP_Document_Packing_List' ) ) {
		$order_date = new DateTime( $order->order_date );
		$heading = sprintf( '<h3 class="order-info">' . esc_html__( 'Packing List for invoice %1$s (order %2$s)', 'woocommerce-pip') . '</h3>', $order_date->format('Y-m-d'), $order->id );
	}
	
	return $heading;
}

/**
 *Reduce the strength requirement on the woocommerce password.
 *
 * Strength Settings
 * 3 = Strong (default)
 * 2 = Medium
 * 1 = Weak
 * 0 = Very Weak / Anything
 *
 * @link	https://wordpress.org/support/topic/woocommerce-password-strength-meter-too-high/page/2/
 */
function reduce_woocommerce_min_strength_requirement( $strength ) {
    return 2;
}

add_filter( 'woocommerce_min_password_strength', 'reduce_woocommerce_min_strength_requirement' );

/**
 * Change display text of password meter
 *
 * @link	https://nicola.blog/2016/02/16/change-the-password-strength-meter-labels/
 */
function my_strength_meter_custom_strings( $data ) {
    $data_new = array(
        'i18n_password_error'   => esc_attr__( 'Please enter a stronger password.', 'eversion-systems' ),
        'i18n_password_hint'    => esc_attr__( 'Your password must be at least <strong>MEDIUM</strong> strength. To achieve this, it must contain a mixture of <strong>UPPER</strong> and <strong>lowercase</strong> letters, <strong>numbers</strong>, and <strong>symbols</strong> (e.g., <strong> ! " ? $ % ^ & </strong>). Keep adding additional characters and/or variations until a medium strength is achieved.', 'eversion-systems' )
    );

    return array_merge( $data, $data_new );
}

add_filter( 'wc_password_strength_meter_params', 'my_strength_meter_custom_strings' );

/**
 * Add a custom column to WooCommerce products quick edit.
 *
 */
function es_display_custom_quickedit_product() {
    ?>
	<br class="clear" />
	<h4>Custom Fields</h4>
	<label>
		<span class="title"><?php _e( 'Member Price', 'woocommerce' ); ?></span>
		<span class="input-text-wrap">
			<input type="text" name="member_price" class="text wc_input_price" value="">
		</span>
	</label>
	<br class="clear" />
    <?php
}

add_action( 'woocommerce_product_quick_edit_end', 'es_display_custom_quickedit_product' );

/**
 * Save the quick edit custom WooCommerce fields
 *
 * @link	https://wpdreamer.com/2012/03/manage-wordpress-posts-using-bulk-edit-and-quick-edit/#populate_quick_edit
 */
function es_save_custom_quickedit_product( $product ) {
	if ( isset( $_REQUEST['member_price'] ) ) {
		update_post_meta( $product->id, 'member_price', wc_clean( $_REQUEST['member_price'] ) );
	}
}

add_action( 'woocommerce_product_quick_edit_save', 'es_save_custom_quickedit_product' );

/**
 * Add a member price column for WooCommerce products
 *
 */
function es_managing_custom_product_columns( $columns, $post_type ) {
   if ( $post_type == 'product' )
      $columns[ 'member_price' ] = 'Member Price';
   return $columns;
}

add_filter( 'manage_posts_columns', 'es_managing_custom_product_columns', 10, 2 );

/**
 * Populate the custom WooCommerce columns with data.
 *
 */
function es_populating_custom_product_columns( $column_name, $post_id ) {
   switch( $column_name ) {
      case 'member_price':
		$member_price = get_post_meta( $post_id, 'member_price', true );
		if( $member_price > 0 )
			echo '<div id="member_price-' . $post_id . '" data-price="' . $member_price . '">'  . wc_price( $member_price ) . '</div>';
        
		break;
   }
}

add_action( 'manage_product_posts_custom_column', 'es_populating_custom_product_columns', 10, 2 );

?>