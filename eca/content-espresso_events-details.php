<?php
//echo '<br/><h6 style="color:#2EA2CC;">'. __FILE__ . ' &nbsp; <span style="font-weight:normal;color:#E76700"> Line #: ' . __LINE__ . '</span></h6>';
global $post;
?>
<div class="event-content">
<h3 class="ee-event-h3">Details</h3>
	<div class="event-details">
	<?php
		do_action( 'AHEE_event_details_before_the_content', $post );
		espresso_event_content_or_excerpt();
		do_action( 'AHEE_event_details_after_the_content', $post );
	 ?>
	</div>
</div>
<!-- .event-content -->
