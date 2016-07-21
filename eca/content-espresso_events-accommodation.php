<?php
global $post;
$accom_phone = get_post_meta( $post->ID, 'ee_accomodation_phone_number', true );
$accom_email = get_post_meta( $post->ID, 'ee_accomodation_email', true );
$accom_details = get_post_meta( $post->ID, 'ee_accomodation_details', true );

if(!empty($accom_details)) {
?>
<div class="espresso-organiser-dv">
	<h3 class="ee-event-h3">Accommodation</h3>
	<div class="event-details">
		<?php 
		if(!empty($accom_details)) {
			echo $accom_details;
		}
		else {
			echo '<p>No accommodation for this event</p>';
		}
		
		if ( !empty($accom_phone)) :
			echo '<span>P: '.$accom_phone.'</span><br>';
		endif;
		if(!empty($accom_email)) :
			echo '<span>E: <a href="mailto:'.$accom_email.'">'.$accom_email.'</a></span><br>';
		endif; 
		?>
	</div>
</div>
<?php
}
?>
<!-- .accommodation-content -->