<?php
/**
* Name : woocommerce-subscriptions.php
* Author : Andrew Schultz
* Purpose : Contains all the custom functions/fields used for Woocommerce subscriptions
*/

/**
* Subscription end date selector and modify button
*/

add_action ('woocommerce_after_order_itemmeta', 'es_subscription_end_date_selector', 10, 3);

function es_subscription_end_date_selector($item_id, $item, $_product) {

	$_custom_values = $_product->product_custom_fields;
	
	if(isset($_custom_values['license_product']))
		$licence_product = $_custom_values['license_product'];
	else
		$licence_product[0] = 'no';
	
	if ( isset( $_product ) ) {
	
		if ($_product->is_type('subscription')) {
			if(isset($licence_product) && $licence_product[0] == 'no') {
			?>
			
			<table id="modify_subscription_length" class="display_meta" cellspacing="0">
				<tr>
				<th style="width:100px"><label for="subscription_end_dtm_<?php echo $item_id;?>">End Date</label></th>
					<td>
					<script type="text/javascript">
					jQuery(document).ready( function($) {
						$( "#subscription_end_dtm_<?php echo $item_id;?>" ).datepicker( {
							dateFormat: 'yy-mm-dd',
							showOn: "button",
							buttonImage: "<?php echo EVERSION_PLUGIN_URL;?>/images/calendar.gif",
							buttonImageOnly: false,
							buttonText: "Select date"
						});
						
						$( "#subscription_end_dtm_<?php echo $item_id;?>" ).datepicker( 'setDate', '<?php echo wc_get_order_item_meta($item_id,'_subscription_expiry_date');?>');
					});
					</script>
					<input type="text" name="subscription_end_dtm_<?php echo $item_id;?>" id="subscription_end_dtm_<?php echo $item_id;?>" style="width:100px;border: 1px solid #DDD;">
					</td>
				</tr>
				<tr>
					<td colspan="4">
					<input type="hidden" name="subscription_item_id_<?php echo $item_id;?>" id="subscription_item_id_<?php echo $item_id;?>" value="<?php echo $item_id;?>">
					<button id="modify_subscription_period_<?php echo $item_id;?>" class="button-primary">Update Subscription</button>
					<div id="expire-action" style="margin-top:10px">
					<?php
					global $post;
					$post_id = $post->ID;
					$post_name =  $_product->post->post_title;
					$subscription_status = wc_get_order_item_meta($item_id, '_subscription_status');
					?>
					<button id="expire_subscription_<?php echo $item_id;?>" name ="expire_subscription_<?php echo $item_id;?>" value="expire_subscription_<?php echo $item_id;?>" class="button-primary" <?php if ( $subscription_status == 'expired' ) echo 'disabled="disabled"';?> onclick="if ( confirm('<?php echo esc_js(sprintf(__("You are about to expire this subscription '%s'\n  'Cancel' to stop, 'OK' to expire subscription."), $post_name )); ?>') ) {return true;}return false;">Cancel Subscription</button>
					</div>
					</td>
				</tr>
			</table>
			<?php
			}
		}
	}
}

/**
* Add ability for users to change subscription period with datepickers
*/

//Fire after subscriptions plugin has done it's thing
add_action('woocommerce_process_shop_order_meta', 'es_modify_subscription_data', 9999, 2);

function es_modify_subscription_data($post_id, $post) {
	
	$order = wc_get_order( $post->ID );
	$items = $order->get_items(); 

	foreach ( $items as $item ) {
		$product_id = $item['product_id'];
		$subscription_key = WC_Subscriptions_Manager::get_subscription_key( $post_id, $product_id );
		$item_id = WC_Subscriptions_Order::get_item_id_by_subscription_key( $subscription_key );
		$item    = WC_Subscriptions_Order::get_item_by_id( $item_id );
		$sub_button_name = 'subscription_item_id_' . $item_id;
		$expire_button_name = 'expire_subscription_' . $item_id;
		
		if(isset($_POST[$sub_button_name])) {
			// subscription button pressed
			$end_dtm_input = 'subscription_end_dtm_'.$item_id;
			$end_dtm = $_POST[$end_dtm_input];
			$user_id = get_post_meta( $post_id, '_customer_user', true);
			$start_dtm = wc_get_order_item_meta( $item_id, '_subscription_start_date' );
			
			if(isset($start_dtm)) {
				$start_dtm_obj = new DateTime($start_dtm);
			}
	
			if(isset($end_dtm)) {
				$end_dtm_obj = new DateTime($end_dtm);
			}
		
			if(!empty($end_dtm)) {
				//Add the same start time as subscription start to end date
				$hours = $start_dtm_obj->format('H');
				$mins = $start_dtm_obj->format('i');
				$seconds = $start_dtm_obj->format('s');
				$timePeriod = 'PT'.$hours.'H'.$mins.'M'.$seconds.'S';
				$end_dtm_obj->add(new DateInterval($timePeriod));
				
				// Updates a subscription's expiration date as scheduled in WP-Cron and in the subscription details array.
				WC_Subscriptions_Manager::set_expiration_date( $subscription_key, $user_id, $end_dtm );
			
				//Calculate days difference
				$interval = $start_dtm_obj->diff($end_dtm_obj);

				if(!empty($interval)) {
					//Make interval and length the same number for subscriptions
					wc_update_order_item_meta( $item_id, '_subscription_interval', $interval->days);
					wc_update_order_item_meta( $item_id, '_subscription_length', $interval->days);
					wc_update_order_item_meta( $item_id, '_subscription_period', 'day');
					wc_update_order_item_meta( $item_id, '_subscription_expiry_date', $end_dtm_obj->format('Y-m-d H:i:s'));
				}
			}
		}
		
		if( isset($_POST[$expire_button_name]) ) {
			// expire subscription button pressed
			WC_Subscriptions_Manager::expire_subscription( $user_id, $subscription_key );

			$order = new WC_Order($post_id);
			$order->add_order_note('Subscription cancelled', 0, true);
		}
	}
}


?>