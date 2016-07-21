<?php
//echo '<br/><h6 style="color:#2EA2CC;">'. __FILE__ . ' &nbsp; <span style="font-weight:normal;color:#E76700"> Line #: ' . __LINE__ . '</span></h6>';
global $post;
if ( espresso_display_ticket_selector( $post->ID ) && ( is_single() || ( is_archive() && espresso_display_ticket_selector_in_event_list() ))) :
?>
<div class="espresso-tickets-dv">
	<h3 class="ee-event-h3">Tickets</h3>
	<div class="event-tickets" style="clear: both;">
		<?php
		if (!is_user_logged_in()) {
			//This will return an array of available for sale tickets.
			//It will be empty if tickets are scheduled to be sold.
			$tickets = espresso_event_tickets_available( $EVT_ID, FALSE, FALSE );
			$ticket_price = 0;
			
			//$tkt_status = $ticket->ticket_status();
			if(is_array($tickets)) {
				foreach ( $tickets as $TKT_ID => $ticket ) {
					if ( $ticket instanceof EE_Ticket ) {
						if($ticket->get_pretty('TKT_name') == 'Member Ticket') {
							$ticket_price = $ticket->price();
							break;
						}
					}
				}
			}
			
			if($ticket_price > 0) {
				echo '<span style="font-size:1.5em"><strong>Are you an ECA member?</strong></span><br>'; 
				echo '<span><strong><a href='. wp_login_url( get_permalink() ).' title="Login">Login</a></strong> to purchase tickets at the member rate of $'.$ticket_price.'</span><br><br>';
			}

		}
		espresso_ticket_selector( $post ); ?>
	</div>
	<!-- .event-tickets -->
	<?php elseif ( ! is_single() ) : ?>
	<?php espresso_view_details_btn( $post ); ?>
	<?php endif; ?>
</div>