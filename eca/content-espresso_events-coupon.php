<?php
global $post;
?>
<div class="espresso-organiser-dv">
	<h3 class="ee-event-h3">Enter Coupon</h3>
	<div class="event-coupon">
		<?php
		$tickets = espresso_event_tickets_available( $post->ID, FALSE, FALSE );
		$ticket_price = 0;
		
		foreach ( $tickets as $TKT_ID => $ticket ) {
			if ( $ticket instanceof EE_Ticket ) {
				if($ticket->get_pretty('TKT_name') == 'Member Ticket') {
					$ticket_price = $ticket->price();
				}
			}
		}
		?>
		<p>Family Day Care Australia members please enter your coupon number below to purchase tickets at the ECA member rate of $<?php echo $ticket_price;?></p>
		<form action="<?php the_permalink() ?>" method="POST">
			<input type="text" id="coupon_code" name="coupon_code" style="padding:2px; line-height:18px;font-size:14px;margin-right:2px;">
			<input id="coupon-enter" class="event-register-btn" value="Add Coupon" type="submit">
		</form>
		<?php
		
		if(isset($_POST['coupon_code']) && $_SESSION['ee_coupon_valid'] == true) {
			echo '<span style="color:#32ABA4;font-size:1.2em"><strong>Coupon successfully added!</strong></span>';
			$_SESSION['ee_coupon_valid'] = null;
		}
		elseif (isset($_POST['coupon_code']) && $_SESSION['ee_coupon_valid'] == false) {
			echo '<span style="color:#ff0000;font-size:1.2em"><strong>Coupon invalid</strong></span>';
			$_SESSION['ee_coupon_valid'] = null;
		}
		?>
	</div>
</div>