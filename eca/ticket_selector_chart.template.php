<div id="tkt-slctr-tbl-wrap-dv-<?php echo $EVT_ID; ?>" class="tkt-slctr-tbl-wrap-dv" >
	<table id="tkt-slctr-tbl-<?php echo $EVT_ID; ?>" class="tkt-slctr-tbl" border="0" cellspacing="0" cellpadding="0">
		<thead>
			<tr>
				<th scope="col" width=""></th>
				<?php if ( apply_filters( 'FHEE__ticket_selector_chart_template__display_ticket_price_details', TRUE )) { ?>
				<th scope="col" width="22.5%"></th>
				<?php } ?>
				<th scope="col" width="17.5%" class="cntr"><?php _e( 'Qty*', 'event_espresso' ); ?></th>
				<th scope="col" width="15%" class="cntr"></th>
			</tr>
		</thead>
		<tbody>
<?php

$row = 1;
$ticket_count = count( $tickets );
foreach ( $tickets as $TKT_ID => $ticket ) {
	if ( $ticket instanceof EE_Ticket ) {
	
		$max = $ticket->max();
		$min = 0;
		$remaining = $ticket->remaining();
		
		if ( $ticket->is_on_sale() && $ticket->is_remaining() ) {
			// offer the number of $tickets_remaining or $max_atndz, whichever is smaller
			$max = min( $remaining, $max_atndz );
			// but... we also want to restrict the number of tickets by the ticket max setting,
			// however, the max still can't be higher than what was just set above
			$max = $ticket->max() > 0 ? min( $ticket->max(), $max ) : $max;
			// and we also want to restrict the minimum number of tickets by the ticket min setting
			$min = $ticket->min() > 0 ? $ticket->min() : 0;
			// and if the ticket is required, then make sure that min qty is at least 1
			$min = $ticket->required() ? max( $min, 1 ) : $min;
		}
	
		//Check for membership
		$membership_exists = es_check_membership_held();
		
		//UserID 2691 memberservices@fdca.com.au for membership rates, 79532 is customer number
		/*$customer_number = get_user_meta(2691, 'old_customer_number', true);
			
		if(isset($_POST['coupon_code'])) {
			//Check for coupon code, set discount to one ticket
			if($_POST['coupon_code'] == $customer_number){
				$_SESSION['ee_coupon_valid'] = true;
				$membership_exists = true;
			}
			else {
				$_SESSION['ee_coupon_valid'] = false;
				write_log('ticket coupon invalid:'.$_SESSION['ee_coupon_valid']);
			}
		}*/
		
		//Check for discounted tickets
		if ($ticket->get_pretty('TKT_name') == 'Discounted Ticket') {
			$user_ID = get_current_user_id();
			//Check if user has discount ticket number allocated to them
			$discount_tickets_number = get_user_meta($user_ID, 'discount_tickets_number', true);
			
			//Set max to number of discounts tickets a user has on their profile
			$max = $discount_tickets_number;
		}
		else if ($ticket->get_pretty('TKT_name') == 'Complementary Ticket') {
			$user_ID = get_current_user_id();
			//Check if user has discount ticket number allocated to them
			$free_tickets_number = get_user_meta($user_ID, 'free_tickets_number', true);
			
			//Set max to number of discounts tickets a user has on their profile
			$max = $free_tickets_number;
		}
		
		$ticket_name = $ticket->get_pretty('TKT_name');
		
		if (($ticket_name == 'Member Ticket' AND $membership_exists) 
			OR ($ticket_name == 'Non-Member Ticket' AND !$membership_exists) 
		OR ($ticket_name == 'Discounted Ticket' AND $discount_tickets_number > 0) 
		OR ($ticket_name == 'Complementary Ticket' AND $free_tickets_number > 0)
		OR ($ticket_name != 'Complementary Ticket' AND $ticket_name  != 'Non-Member Ticket' AND $ticket_name  != 'Member Ticket' AND $ticket_name  != 'Discounted Ticket')) {

		$ticket_price = $ticket->get_ticket_total_with_taxes();
		$ticket_bundle = FALSE;
		// for ticket bundles, set min and max qty the same
		if ( $ticket->min() != 0 && $ticket->min() == $ticket->max() ) {
			$ticket_price = $ticket_price * $ticket->min();
			$ticket_bundle = TRUE;
		}
		$ticket_price = apply_filters( 'FHEE__ticket_selector_chart_template__ticket_price', $ticket_price, $ticket );

		$tkt_status = $ticket->ticket_status();
		// check ticket status
		switch ( $tkt_status ) {
			// sold_out
			case EE_Ticket::sold_out :
				$ticket_status = '<span class="ticket-sales-sold-out">' . $ticket->ticket_status( TRUE ) . '</span>';
				$status_class = 'ticket-sales-sold-out lt-grey-text';
			break;
			// expired
			case EE_Ticket::expired :
				$ticket_status = '<span class="ticket-sales-expired">' . $ticket->ticket_status( TRUE ) . '</span>';
				$status_class = 'ticket-sales-expired lt-grey-text';
			break;
			// archived
			case EE_Ticket::archived :
				$ticket_status = '<span class="archived-ticket">' . $ticket->ticket_status( TRUE ) . '</span>';
				$status_class = 'archived-ticket hidden';
			break;
			// pending
			case EE_Ticket::pending :
				$ticket_status = '<span class="ticket-pending">' . $ticket->ticket_status( TRUE ) . '</span>';
				$status_class = 'ticket-pending';
			break;
			// onsale
			case EE_Ticket::onsale :
				$ticket_status = '<span class="ticket-on-sale">' . $ticket->ticket_status( TRUE ) . '</span>';
				$status_class = 'ticket-on-sale';
			break;
		}

	?>
				<tr class="tckt-slctr-tbl-tr <?php echo $status_class . ' ' . espresso_get_object_css_class( $ticket ); ?>">
					<td class="tckt-slctr-tbl-td-name">
						<b><span><?php echo $ticket->get_pretty('TKT_name');?></span></b>
					<?php if ( $ticket->required() ) { ?>
						<p class="ticket-required-pg"><?php _e( 'This ticket is required and must be purchased.', 'event_espresso' ); ?></p>
					<?php } ?>

					</td>
					<?php if ( apply_filters( 'FHEE__ticket_selector_chart_template__display_ticket_price_details', TRUE )) { ?>
					<td class="tckt-slctr-tbl-td-price jst-rght"><span><?php echo EEH_Template::format_currency( $ticket_price ); ?>&nbsp;</span>
					</td>
					<?php } ?>
					<td class="tckt-slctr-tbl-td-qty cntr">
				<?php
					$hidden_input_qty = $max_atndz > 1 ? TRUE : FALSE;
					// sold out or other status ?
					if ( $tkt_status == EE_Ticket::sold_out || $remaining == 0 ) {
						echo '<span class="sold-out">' . __( 'Sold&nbsp;Out', 'event_espresso' ) . '</span>';
					} else if ( $tkt_status == EE_Ticket::expired || $tkt_status == EE_Ticket::archived ) {
						echo $ticket_status;
					} else if ( $tkt_status == EE_Ticket::pending ) {
					?>
					<div class="ticket-pending-pg">
						<span class="ticket-pending"><?php _e( 'Goes&nbsp;On&nbsp;Sale', 'event_espresso' ); ?></span><br/>
						<span class="small-text"><?php echo $ticket->get_i18n_datetime( 'TKT_start_date', apply_filters( 'FHEE__EED_Ticket_Selector__display_goes_on_sale__date_format', 'M d, Y' ) ); ?></span>
					</div>
					<?php
					// min qty purchasable is less than tickets available
					} else if ( $ticket->min() > $remaining ) {
					?>
					<div class="archived-ticket-pg">
						<span class="archived-ticket small-text"><?php _e( 'Not Available', 'event_espresso' ); ?></span><br/>
					</div>
					<?php
					// if only one attendee is allowed to register at a time
					} else if ( $max_atndz  == 1 ) {
						// display submit button since we have tickets availalbe
						add_filter( 'FHEE__EE_Ticket_Selector__display_ticket_selector_submit', '__return_true' );
				?>
					<input type="radio" name="tkt-slctr-qty-<?php echo $EVT_ID; ?>" id="ticket-selector-tbl-qty-slct-<?php echo $EVT_ID . '-' . $row; ?>" class="ticket-selector-tbl-qty-slct" value="<?php echo $row . '-'; ?>1" <?php echo $row == 1 ? ' checked="checked"' : ''; ?> />
			<?php
						$hidden_input_qty = FALSE;

					} else if ( $max_atndz  == 0 ) {
						echo '<span class="sold-out">' . __( 'Closed', 'event_espresso' ) . '</span>';
					} elseif ( $max > 0 ) {
						// display submit button since we have tickets availalbe
						add_filter( 'FHEE__EE_Ticket_Selector__display_ticket_selector_submit', '__return_true' );

				?>
					<span>
					<select name="tkt-slctr-qty-<?php echo $EVT_ID; ?>[]" id="ticket-selector-tbl-qty-slct-<?php echo $EVT_ID . '-' . $row; ?>" class="ticket-selector-tbl-qty-slct">
					<?php
						// this ensures that non-required tickets with non-zero MIN QTYs don't HAVE to be purchased
						if ( ! $ticket->required() && $min !== 0 ) {
					?>
						<option value="0">&nbsp;0&nbsp;</option>
					<?php }
						
						// offer ticket quantities from the min to the max
						for ( $i = $min; $i <= $max; $i++) {
					?>
						<option value="<?php echo $i; ?>">&nbsp;<?php echo $i; ?>&nbsp;</option>
					<?php } ?>
					</select>
					</span>
				<?php
						$hidden_input_qty = FALSE;

					}
					// depending on group reg we need to change the format for qty
					if ( $hidden_input_qty ) {
					?>
					<input type="hidden" name="tkt-slctr-qty-<?php echo $EVT_ID; ?>[]" value="0" />
					<?php
					}
				?>
						<input type="hidden" name="tkt-slctr-ticket-id-<?php echo $EVT_ID; ?>[]" value="<?php echo $TKT_ID; ?>" />
						<input type="hidden" name="tkt-slctr-ticket-obj-<?php echo $EVT_ID; ?>[]" value="<?php echo base64_encode( serialize( $ticket )); ?>" />
					</td class="cntr">
					<td>
					<?php 
					//Got this piece of code from EED_Ticket_Selector.module.php in modules\ticket_selector, I hide the default Register button and 
					//add this custom button into a table cell
					if ( ($tkt_status <> EE_Ticket::sold_out) AND ($tkt_status <> EE_Ticket::pending)) {
						echo '<input id="ticket-selector-submit-'. $EVT_ID .'-btn" class="event-register-btn" type="submit" value="Buy Tickets" />';
					}
					else {
						 echo '<button class="event-register-btn" type="button" disabled>Buy Tickets</button>';
					}
					?>
					</td>
				</tr>
				
	<?php
			$row++;
			}
		}
	}
?>

		</tbody>
	</table>

	<input type="hidden" name="noheader" value="true" />
	<input type="hidden" name="tkt-slctr-return-url-<?php echo $EVT_ID ?>" value="<?php echo $_SERVER['REQUEST_URI']?>" />
	<input type="hidden" name="tkt-slctr-rows-<?php echo $EVT_ID; ?>" value="<?php echo $row - 1; ?>" />
	<input type="hidden" name="tkt-slctr-max-atndz-<?php echo $EVT_ID; ?>" value="<?php echo $max_atndz; ?>" />
	<input type="hidden" name="tkt-slctr-event-id" value="<?php echo $EVT_ID; ?>" />
	<input type="hidden" name="tkt-slctr-event-<?php echo $EVT_ID; ?>" value="<?php echo base64_encode( serialize( $event )); ?>" />

	<?php do_action( 'AHEE__ticket_selector_chart__template__after_ticket_selector', $EVT_ID, $max_atndz ); ?>

</div>
