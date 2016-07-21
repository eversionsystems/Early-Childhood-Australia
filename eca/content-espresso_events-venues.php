<?php
//echo '<br/><h6 style="color:#2EA2CC;">'. __FILE__ . ' &nbsp; <span style="font-weight:normal;color:#E76700"> Line #: ' . __LINE__ . '</span></h6>';

if (( is_single() && espresso_display_venue_in_event_details() ) || is_archive() && espresso_display_venue_in_event_list() ) :
	global $post;
	do_action( 'AHEE_event_details_before_venue_details', $post );?>

<div class="espresso-venue-dv">
	<h3 class="ee-event-h3">Where</h3>
	<div class="event-venue">
		<strong><?php espresso_venue_name(0,'none'); ?></strong>
		<?php  if ( espresso_venue_has_address( $post->ID )) : ?>
		<?php echo '<span>'.espresso_venue_address( 'multiline' ).'</span>'; ?>
		<?php  if ( $venue_phone = espresso_venue_phone( $post->ID, FALSE )) : ?>
		<?php echo $venue_phone; ?>
		<?php endif;  ?>
		<?php espresso_venue_gmap( $post->ID ); ?>
		<div class="clear"><br/></div>
		<?php endif;  ?>

		<?php $VNU_ID = espresso_venue_id( $post->ID ); ?>
		<?php if ( is_single() ) : ?>
			<?php $venue_description = espresso_venue_description( $VNU_ID, FALSE ); ?>
			<?php if ( $venue_description ) : ?>
		<p>
			<strong><?php _e( 'Description:', 'event_espresso' ); ?></strong><br/>
			<?php echo $venue_description; ?>
		</p>
			<?php endif;  ?>
		<?php else : ?>
			<?php $venue_excerpt = espresso_venue_excerpt( $VNU_ID, FALSE ); ?>
			<?php if ( $venue_excerpt ) : ?>
		<p>
			<strong><?php _e( 'Description:', 'event_espresso' ); ?></strong><br/>
			<?php echo $venue_excerpt; ?>
		</p>
			<?php endif;  ?>
		<?php endif;  ?>
	</div>
</div>
<!-- .espresso-venue-dv -->
<?php
do_action( 'AHEE_event_details_after_venue_details', $post );
endif;
?>
