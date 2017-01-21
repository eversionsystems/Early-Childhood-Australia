<?php 
/*
 * Activate subscriptions that have expired and set a new scheduled expiration job
 * Uses functions from WooCommerce Subscriptions extension
 *
 * 1.0.0
 */
//require_once( dirname(__FILE__) . '/wp-load.php');
//require_once( dirname(__FILE__) . '/wp-content/plugins/woocommerce-subscriptions/woocommerce-subscriptions.php' );
//$root = realpath($_SERVER["DOCUMENT_ROOT"]);
//require_once( "$root/webfiles/shop/wp-load.php");
//require_once( "$root/webfiles/shop/wp-content/plugins/woocommerce-subscriptions/woocommerce-subscriptions.php" );
set_time_limit(300);
require_once( '../wp-load.php');
require_once( '../wp-content/plugins/woocommerce-subscriptions/woocommerce-subscriptions.php' );

// subscription key is the $order_id . '_' . $product_id;
$sub_keys = array (  );

foreach( $sub_keys as $subscription_key ) {
	// setup the active subscription parameters
	$new_subscription_details = array( 'status' => 'active', 'end_date' => '' );
	
	$subscription = WC_Subscriptions_Manager::update_subscription( $subscription_key, $new_subscription_details );
	//echo '<pre>';
	//print_r($subscription);
	//echo '</pre>';
	$user_id = WC_Subscriptions_Manager::get_user_id_from_subscription_key( $subscription_key );
	//print_r($user_id);
	
	// ensure a new cron job to expire the subscription exists
	$cron = WC_Subscriptions_Manager::set_expiration_date( $subscription_key, $user_id, $subscription['expiry_date'] );
	//print_r($cron);
	
	//$count++;
	//echo 'Processed Subscription ' . $subscription_key . '<br>';
}

echo 'Complete';

/*
$new_subscription_details = array( 'status' => 'active', 'end_date' => '' );
//$order_id = 56816;
//$product_id = 58;
// $subscription_key = $order_id . '_' . $product_id;
$subscription_key = '57453_80';
//$subscription_key = WC_Subscriptions_Manager::get_subscription_key( $order_id, $product_id );

// get the new subscription array
$subscription = WC_Subscriptions_Manager::update_subscription( $subscription_key, $new_subscription_details );

$user_id = WC_Subscriptions_Manager::get_user_id_from_subscription_key( $subscription_key );
// ensure a new cron job to expire the subscription exists
WC_Subscriptions_Manager::set_expiration_date( $subscription_key, $user_id, $subscription['expiry_date'] );

echo 'done';
*/

/*
activate_subscription($user_id, $subscription_key )
activate_subscriptions_for_order ($order_id)
update_users_subscriptions_for_order( $order, $status = 'active' )
*/

/*
@param array $subscriptions An array of arrays with a subscription key and corresponding 'detail' => 'value' pair. Can alter any of these details:
	 *        'start_date'          The date the subscription was activated
	 *        'expiry_date'         The date the subscription expires or expired, false if the subscription will never expire
	 *        'failed_payments'     The date the subscription's trial expires or expired, false if the subscription has no trial period
	 *        'end_date'            The date the subscription ended, false if the subscription has not yet ended
	 *        'status'              Subscription status can be: cancelled, active, expired or failed
	 *        'completed_payments'  An array of MySQL formatted dates for all payments that have been made on the subscription
	 *        'failed_payments'     An integer representing a count of failed payments
	 *        'suspension_count'    An integer representing a count of the number of times the subscription has been suspended for this billing period
	 * @since 1.0
*/
/*
update_users_subscriptions( $user_id, $subscriptions ) 
update_subscription( $subscription_key, $new_subscription_details ) 

get_subscription_key( $order_id, $product_id = '' )

calculate_subscription_expiration_date( $subscription_key, $user_id = '', $type = 'mysql' ) returns expiration_date we can use below
set_expiration_date( $subscription_key, $user_id = '', $expiration_date = '' )
get_user_id_from_subscription_key( $subscription_key )
*/

?> 