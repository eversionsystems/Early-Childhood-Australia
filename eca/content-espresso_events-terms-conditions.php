<?php
global $post;
$terms_conditions = get_post_meta( $post->ID, 'ee_terms_conditions', true );

if(!empty($terms_conditions)) {
?>
<div class="espresso-terms-dv">
	<h3 class="ee-event-h3">Terms and Conditions</h3>
	<div class="event-terms">
	<?php echo $terms_conditions; ?>
	</div>
</div>
<?php
}