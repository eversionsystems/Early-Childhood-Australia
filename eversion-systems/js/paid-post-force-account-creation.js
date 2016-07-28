var $j = jQuery; //Wordress set to noConflict() by default

$j(document).ready( function() {

	if (es_paid_post_obj.paid_post_exists == true) {
		$j('#createaccount').prop('checked', true);
		//$j('#createaccount').prop('disabled', true);
	}
	
	$j('#createaccount').click(function(){
		if (es_paid_post_obj.paid_post_exists == true) {
			$j('#createaccount').prop('checked', true);
			$j('.create-account').show();
		}
	});
});