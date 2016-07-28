<?php

//event-espresso-functions.php
//Customisation for Event Espresso 4
//Remove iCal from event page

function es_ee_custom_ts_short_date($dformat) {
	
	$dformat = 'd/m/Y';
	return $dformat;
}

add_filter('FHEE__EED_Ticket_Selector__display_goes_on_sale__date_format', 'es_ee_custom_ts_short_date');

/*
* Check event espresso plugin is active
*/
if (is_plugin_active('event-espresso-core-reg/espresso.php')) {
	
	/**
	* Hardcode a footer image for specific events
	*/
	add_action('AHEE_event_details_footer_top','eca_add_ee_footer_image');
	
	function eca_add_ee_footer_image(){
		global $post;
		
		if($post->ID == 53299)
			echo '<img src="'.site_url().'/wp-content/uploads/2015/09/EYC-web-portal-footer.jpg">';
	}
	
	/**
	* DUPLICATE EVENT SAVE CUSTOM FIELDS
	---------------------------------------------------------------------------------------------*/
	
	add_action('AHEE__Extend_Events_Admin_Page___duplicate_event__after', 'es_ee_duplicate_event_custom_fields_save', 10, 2);
	
	function es_ee_duplicate_event_custom_fields_save($new_event, $orig_event ){
		$orig_event_id = $orig_event->ID();
		$new_event_id = $new_event->ID();
		
		//Get all post meta for original event
		$orig_post_meta = get_post_meta($orig_event_id);
		
		//Xero Event Espresso fields
		update_post_meta($new_event_id, 'ee_xero_cost_centre', $orig_post_meta['ee_xero_cost_centre'][0]);
		update_post_meta($new_event_id,'ee_xero_sub_cost_centre', $orig_post_meta['ee_xero_sub_cost_centre'][0]);
		
		//Ticket Notes
		update_post_meta($new_event_id,'ee_ticket_notes', $orig_post_meta['ee_ticket_notes'][0]);
		
		//Terms and Conditions
		update_post_meta($new_event_id,'ee_terms_conditions', $orig_post_meta['ee_terms_conditions'][0]);
		
		//Contact Details
		update_post_meta($new_event_id,'ee_contact_name', $orig_post_meta['ee_contact_name'][0]);
		update_post_meta($new_event_id,'ee_contact_position', $orig_post_meta['ee_contact_position'][0]);
		update_post_meta($new_event_id,'ee_contact_phone_number', $orig_post_meta['ee_contact_phone_number'][0]);
		update_post_meta($new_event_id,'ee_contact_email', $orig_post_meta['ee_contact_email'][0]);
		
		//Accomodation Details
		update_post_meta($new_event_id,'ee_accomodation_details', $orig_post_meta['ee_accomodation_details'][0]);
		update_post_meta($new_event_id,'ee_accomodation_phone_number', $orig_post_meta['ee_accomodation_phone_number'][0]);
		update_post_meta($new_event_id,'ee_accomodation_email', $orig_post_meta['ee_accomodation_email'][0]);
		
		//Thumbnail
		update_post_meta($new_event_id,'_thumbnail_id', $orig_post_meta['_thumbnail_id'][0]);
		
		//Ticket SKUs
		$orig_datetimes = $orig_event->get_many_related('Datetime');
		
		foreach ( $orig_datetimes as $orig_dtt ) {
			$orig_tkts = $orig_dtt->tickets();
			
			foreach ( (array) $orig_tkts as $orig_tkt ) {
				//it's possible a datetime will have no tickets so let's verify we HAVE a ticket first.
				if ( ! $orig_tkt instanceof EE_Ticket )
					continue;

				//is this ticket archived?  If it is then let's skip
				if ( $orig_tkt->get( 'TKT_deleted' ) ) {
					continue;
				}
				
				//Get original ticket SKU and update new event
				$ticket_SKU = $orig_tkt->get_extra_meta( 'SKU', true );
				$ticket_name = $orig_tkt->get_pretty('TKT_name');
				
				//Find new ticket by name in $new_event and update it with SKU
				$new_datetimes = $new_event->get_many_related('Datetime');
				
				foreach ( $new_datetimes as $new_dtt ) {
					$new_tkts = $new_dtt->tickets();
					foreach ( (array) $new_tkts as $new_tkt ) {
						if($new_tkt->get_pretty('TKT_name') == $ticket_name)
							$new_tkt->update_extra_meta( 'SKU', $ticket_SKU );
					}
				}
			}
		}
	}
	
	/**
	* Message before payment gateways on registration page
	*/
	add_filter('FHEE__registration_page_payment_options__method_of_payment_hdr', 'ee_es_payment_gateway_message');

	function ee_es_payment_gateway_message() {
		return 'We welcome payment by credit card. Please select your credit card type from the PayPal payment options.';
	}
	
	/**
	* Change thankyou message at end of transaction
	*/
	add_filter('FHEE__thank_you_page_overview_template__order_conf_desc', 'es_ee_modify_thankyou_message');
	
	function es_ee_modify_thankyou_message() {
		return '<h3 class="">Congratulations</h3><br>Your registration has been successfully processed.<br>Check your email for your registration confirmation.';
	}
	
	/**
	* CSS additions
	*/
	add_action( 'wp_enqueue_scripts', 'es_ee_add_scripts' );

	function es_ee_add_scripts() { 
	
		if(is_page('thank-you')) { ?>
			<style type="text/css">
			#espresso-thank-you-page-overview-dv .ee-button {
				display:none;
			}
			</style> 
		<?php 
		}
		
		if(is_page('registration-checkout')) { ?>
			<style type="text/css">
			#spco-payment-method-info-paypal_standard {font-size:1.2em}
			#spco-payment-info-table {font-size:1.2em}
			.spco-ticket-info-dv table {border-spacing: 2px;border-width: 1px;}
			.small-text {font-size: 1.2em !important;}
			.spco-attendee-panel-dv {font-size:1.2em}
			.ee-form-add-new-state-lnk.display-the-hidden.smaller-text {display:none;}
			</style>
		<?php		
		}
	}

	/**
	* Add class to metbox to enable fileds to be put into columns
	*/
	add_action( 'admin_enqueue_scripts', 'es_ee_admin_enqueue_scripts' );

	/**
	* Add scripts to admin dashboard
	*/
	function es_ee_admin_enqueue_scripts($hook) {

		if($hook == 'toplevel_page_espresso_events') {
			wp_enqueue_script( 'ee-admin-js', EVERSION_PLUGIN_URL. 'js/ee-new-event.js', array( 'jquery' ), '1.0', true );
			wp_enqueue_style('ee-admin', EVERSION_PLUGIN_URL.'/css/ee-admin-style.css');
		}
	}
	
	//Custom ticket selector template
	add_filter ('FHEE__EE_Ticket_Selector__display_ticket_selector__template_path', 'my_custom_ticket_selector_template_location');
	 
	function my_custom_ticket_selector_template_location(){
		return get_stylesheet_directory() . '/ticket_selector_chart.template.php';
	}

	//add_action( 'template_redirect', 'my_remove_ical_link' );
	 
	function my_remove_ical_link() {
		remove_filter( 'FHEE__espresso_list_of_event_dates__datetime_html', array( 'EED_Ical', 'generate_add_to_iCal_button' ), 10 );
	}

	/**
	* PayPal Message
	*/

	function ee_payment_options_paypal_optional( $translated, $original, $domain ) {
		$strings = array(
		'After finalizing your registration, you will be transferred to the PayPal.com website where your payment will be securely processed.' => 'Upon clicking on the Finalize Registration button, you will be transferred to PayPal.com where your payment will be securely processed. A <strong>PayPal account is not required</strong> and you can pay with a credit or debit card.'
		);
		
		if ( isset( $strings[$original] ) ) {
			$translations = &get_translations_for_domain( $domain );
			$translated = $translations->translate( $strings[$original] );
		}
		
		return $translated;
	}

	add_filter( 'gettext', 'ee_payment_options_paypal_optional', 10, 3 );
	
	/*
	* Change Register text to Buy Tickets, this function not working so use jQuery instead
	*/
	function event_button_override_page_116($phrase)
	{
		$post = get_the_id();
		//echo get_post( $post )->post_name;
		//eca-reconciliation-symposium

		if ($post == 5398 ) {
			$phrase = str_replace('Register Now', 'Buy Ticket', $phrase);
			return $phrase;
		}
		else {
			return $phrase;
		}
	}

	//add_action('AHEE__SPCO__before_registration_steps', 'es_decrement_discount_tickets');
	//A better hook to use which fires after finalising registration
	add_action('AHEE__EE_Single_Page_Checkout__process_finalize_registration__before_gateway', 'es_decrement_discount_tickets');

	function es_decrement_discount_tickets() {
		//Only for logged in users
		if (is_user_logged_in()) {
			//Get the cart object
			$cart = EE_Cart::instance();
			//$count_tickets = $cart->all_ticket_quantity_count();
			
			$tickets = $cart->get_tickets();
			
			//Find if any discounted tickets and remove from users allocated profile
			foreach ( $tickets as $ticket ) {
				if ($ticket->get('LIN_name') == "Discounted Ticket") {
					$purchased_quantity = $ticket->get('LIN_quantity');
					$user_ID = get_current_user_id();
					$discount_tickets_number = get_user_meta($user_ID, 'discount_tickets_number', true);
					$discount_tickets_number = $discount_tickets_number - $purchased_quantity;

					if ($discount_tickets_number >= 0)
						update_user_meta($user_ID, 'discount_tickets_number', $discount_tickets_number);
					
					//write_log('updated '.$discount_tickets_number);
				}
				else if($ticket->get('LIN_name') == "Complimentary Ticket") {
					$purchased_quantity = $ticket->get('LIN_quantity');
					$user_ID = get_current_user_id();
					$free_tickets_number = get_user_meta($user_ID, 'free_tickets_number', true);
					$free_tickets_number = $free_tickets_number - $purchased_quantity;

					if ($free_tickets_number >= 0)
						update_user_meta($user_ID, 'free_tickets_number', $free_tickets_number);
				}
			}
		}
		
		//write_log('Inside the hook AHEE__EE_Single_Page_Checkout__process_finalize_registration__before_gateway');
	}
	
	add_action('AHEE__ticket_selector_chart__template__after_ticket_selector', 'es_ee_display_info_after_ticket_select', 10, 2);
	
	function es_ee_display_info_after_ticket_select($EVT_ID, $max_atndz) {
		
		if ( $max_atndz > 0 ) { 
			$ticket_info = get_post_meta( $EVT_ID, 'ee_ticket_notes', true );
			?>
			<strong><span>Please Note</span></strong><br>
			<?php 
			echo sprintf( __( '<span>* A maximum of %d tickets can be purchased for this event per order.</span>', 'event_espresso' ), $max_atndz );
			if(!empty($ticket_info))
				echo $ticket_info;
			?>
			<?php 
		}
	}
	
	/**
	* Update/Add SKU for tickets
	*/
	
	add_action('AHEE__espresso_events_Pricing_Hooks___update_tkts_update_ticket', 'es_update_tkt_SKU', 10, 4);
	add_action('AHEE__espresso_events_Pricing_Hooks___update_tkts_new_ticket', 'es_update_tkt_SKU', 10, 4);
	
	function es_update_tkt_SKU($TKT, $row, $tkt, $data) {
		//Add SKU into the extra meta table for that ticket ID
		$TKT->update_extra_meta('SKU',$tkt['TKT_SKU']);
	}
	
	//This is used to display the value of the SKU
	add_filter( 'FHEE__espresso_events_Pricing_Hooks___get_ticket_row__template_args', 'es_display_tkt_SKU', 10, 7);
		
	function es_display_tkt_SKU($template_args, $tktrow, $ticket, $ticket_datetimes, $all_dtts, $default, $all_tickets ) {
		
		$TKT_ID = $template_args['TKT_ID'];
		
		//if($TKT_ID > 0) {
		if ( $ticket instanceof EE_Ticket ) {
			$template_args['TKT_SKU'] = $ticket->get_extra_meta( 'SKU', true );
		}
		
		return $template_args;
	}
	
	add_filter( 'rwmb_meta_boxes', 'es_ee_terms_conditions_meta_box' );
	
	/**
	 * Register meta boxes for terms and conditions
	 *
	 * @return void
	 */
	function es_ee_terms_conditions_meta_box( $meta_boxes )
	{
		/**
		 * Prefix of meta keys (optional)
		 * Use underscore (_) at the beginning to make keys hidden
		 * Alt.: You also can make prefix empty to disable it
		 */
		// Better has an underscore as last sign
		
		$prefix = 'ee_';

		// 1st meta box
		$meta_boxes[] = array(
			// Meta box id, UNIQUE per meta box. Optional since 4.1.5
			'id' => 'terms-conditions',

			// Meta box title - Will appear at the drag and drop handle bar. Required.
			'title' => __( 'Terms and Conditions', 'rwmb' ),

			// Post types, accept custom post types as well - DEFAULT is array('post'). Optional.
			'pages' => array( 'espresso_events' ),

			// Where the meta box appear: normal (default), advanced, side. Optional.
			'context' => 'normal',

			// Order of meta box: high (default), low. Optional.
			'priority' => 'high',

			// Auto save: true, false (default). Optional.
			'autosave' => true,

			// List of meta fields
			'fields' => array(
				array(
					// Field name - Will be used as label
					//'name'  => __( 'Notes', 'rwmb' ),
					// Field ID, i.e. the meta key
					'id'    => "{$prefix}terms_conditions",
					// Field description (optional)
					'desc'  => __( 'Terms and Conditions of the event', 'rwmb' ),
					'type'  => 'wysiwyg',
					'options' => array('textarea_rows' => 10, 'media_buttons' => false)
				)
			)
		);

		return $meta_boxes;
	}
	
	add_filter( 'rwmb_meta_boxes', 'es_ee_ticket_notes_meta_box' );

	/**
	 * Register meta boxes for ticket notes
	 *
	 * @return void
	 */
	function es_ee_ticket_notes_meta_box( $meta_boxes )
	{
		/**
		 * Prefix of meta keys (optional)
		 * Use underscore (_) at the beginning to make keys hidden
		 * Alt.: You also can make prefix empty to disable it
		 */
		// Better has an underscore as last sign
		
		$prefix = 'ee_';

		// 1st meta box
		$meta_boxes[] = array(
			// Meta box id, UNIQUE per meta box. Optional since 4.1.5
			'id' => 'ticket-notes',

			// Meta box title - Will appear at the drag and drop handle bar. Required.
			'title' => __( 'Ticket Notes', 'rwmb' ),

			// Post types, accept custom post types as well - DEFAULT is array('post'). Optional.
			'pages' => array( 'espresso_events' ),

			// Where the meta box appear: normal (default), advanced, side. Optional.
			'context' => 'normal',

			// Order of meta box: high (default), low. Optional.
			'priority' => 'high',

			// Auto save: true, false (default). Optional.
			'autosave' => true,

			// List of meta fields
			'fields' => array(
				array(
					// Field name - Will be used as label
					//'name'  => __( 'Notes', 'rwmb' ),
					// Field ID, i.e. the meta key
					'id'    => "{$prefix}ticket_notes",
					// Field description (optional)
					'desc'  => __( 'Notes that appear under the ticket selector', 'rwmb' ),
					'type'  => 'wysiwyg',
					'options' => array('textarea_rows' => 5, 'media_buttons' => false)
				)
			)
		);

		return $meta_boxes;
	}
	
	add_filter( 'rwmb_meta_boxes', 'es_ee_contact_details_meta_box' );

	/**
	 * Register meta boxes for contact details
	 *
	 * @return void
	 */
	function es_ee_contact_details_meta_box( $meta_boxes )
	{
		/**
		 * Prefix of meta keys (optional)
		 * Use underscore (_) at the beginning to make keys hidden
		 * Alt.: You also can make prefix empty to disable it
		 */
		// Better has an underscore as last sign
		
		$prefix = 'ee_';

		// 1st meta box
		$meta_boxes[] = array(
			// Meta box id, UNIQUE per meta box. Optional since 4.1.5
			'id' => 'contact-details',

			// Meta box title - Will appear at the drag and drop handle bar. Required.
			'title' => __( 'Contact Details', 'rwmb' ),

			// Post types, accept custom post types as well - DEFAULT is array('post'). Optional.
			'pages' => array( 'espresso_events' ),

			// Where the meta box appear: normal (default), advanced, side. Optional.
			'context' => 'normal',

			// Order of meta box: high (default), low. Optional.
			'priority' => 'high',

			// Auto save: true, false (default). Optional.
			'autosave' => true,

			// List of meta fields
			'fields' => array(
				array(
					// Field name - Will be used as label
					'name'  => __( 'Name', 'rwmb' ),
					// Field ID, i.e. the meta key
					'id'    => "{$prefix}contact_name",
					// Field description (optional)
					'desc'  => __( 'Name of contact person', 'rwmb' ),
					'type'  => 'text'
				),
				array(
					// Field name - Will be used as label
					'name'  => __( 'Position', 'rwmb' ),
					// Field ID, i.e. the meta key
					'id'    => "{$prefix}contact_position",
					// Field description (optional)
					'desc'  => __( 'Position of contact person', 'rwmb' ),
					'type'  => 'text'
				),
				array(
					// Field name - Will be used as label
					'name'  => __( 'Phone Number', 'rwmb' ),
					// Field ID, i.e. the meta key
					'id'    => "{$prefix}contact_phone_number",
					// Field description (optional)
					'desc'  => __( 'Phone number of contact person', 'rwmb' ),
					'type'  => 'text'
				),
				array(
					// Field name - Will be used as label
					'name'  => __( 'Email', 'rwmb' ),
					// Field ID, i.e. the meta key
					'id'    => "{$prefix}contact_email",
					// Field description (optional)
					'desc'  => __( 'Email of contact person', 'rwmb' ),
					'type'  => 'text'
				)
			)
		);

		return $meta_boxes;
	}
	
	add_filter( 'rwmb_meta_boxes', 'es_espresso_accomodation_details_meta_box' );

	/**
	 * Register meta boxes for accomodation details
	 *
	 * @return void
	 */
	function es_espresso_accomodation_details_meta_box( $meta_boxes )
	{
		/**
		 * Prefix of meta keys (optional)
		 * Use underscore (_) at the beginning to make keys hidden
		 * Alt.: You also can make prefix empty to disable it
		 */
		// Better has an underscore as last sign
		
		$prefix = 'ee_';

		// 1st meta box
		$meta_boxes[] = array(
			// Meta box id, UNIQUE per meta box. Optional since 4.1.5
			'id' => 'accomodation-details',

			// Meta box title - Will appear at the drag and drop handle bar. Required.
			'title' => __( 'Accomodation Details', 'rwmb' ),

			// Post types, accept custom post types as well - DEFAULT is array('post'). Optional.
			'pages' => array( 'espresso_events' ),

			// Where the meta box appear: normal (default), advanced, side. Optional.
			'context' => 'normal',

			// Order of meta box: high (default), low. Optional.
			'priority' => 'high',

			// Auto save: true, false (default). Optional.
			'autosave' => true,

			// List of meta fields
			'fields' => array(
				array(
					// Field name - Will be used as label
					//'name'  => __( 'Details', 'rwmb' ),
					// Field ID, i.e. the meta key
					'id'    => "{$prefix}accomodation_details",
					// Field description (optional)
					'desc'  => __( 'Details for venue accomodation', 'rwmb' ),
					'type'  => 'wysiwyg',
					'options' => array('textarea_rows' => 5, 'media_buttons' => false)
				),
				array(
					// Field name - Will be used as label
					'name'  => __( 'Phone Number', 'rwmb' ),
					// Field ID, i.e. the meta key
					'id'    => "{$prefix}accomodation_phone_number",
					// Field description (optional)
					'desc'  => __( 'Phone number of accomodation', 'rwmb' ),
					'type'  => 'text'
				),
				array(
					// Field name - Will be used as label
					'name'  => __( 'Email', 'rwmb' ),
					// Field ID, i.e. the meta key
					'id'    => "{$prefix}accomodation_email",
					// Field description (optional)
					'desc'  => __( 'Email of accomodation venue', 'rwmb' ),
					'type'  => 'text'
				)
			)
		);

		return $meta_boxes;
	}
	
}

?>