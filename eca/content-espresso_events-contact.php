<?php
global $post;

$contact_name = get_post_meta( $post->ID, 'ee_contact_name', true );
$contact_position = get_post_meta( $post->ID, 'ee_contact_position', true );
$contact_phone = get_post_meta( $post->ID, 'ee_contact_phone_number', true );
$contact_email = get_post_meta( $post->ID, 'ee_contact_email', true );
?>
<div class="espresso-organiser-dv">
	<h3 class="ee-event-h3">Contact Us</h3>
	<div class="event-contact">
		<?php
		if ( !empty($contact_name)) :
			echo '<span>'.$contact_name.'</span><br>';
		endif;
		if ( !empty($contact_position)) :
			echo '<span>'.$contact_position.'</span><br>';
		endif;
		if ( !empty($contact_phone)) :
			echo '<span>P: '.$contact_phone.'</span><br>';
		endif;
		if(!empty($contact_email)) :
			echo '<span>E: <a href="mailto:'.$contact_email.'">'.$contact_email.'</a></span><br>';
		endif; 
		?>
	</div>
</div>