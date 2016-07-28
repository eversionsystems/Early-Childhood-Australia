var $j = jQuery; //Wordress set to noConflict() by default

$j(document).ready( function() {

	//Hide membership and non membership tickets if free/discount tickets are allocated
	if (es_hide_tickets_obj.discount_entitled == true) {
		$j('.ee-ticket-non-member-ticket').hide();
		$j('.ee-ticket-member-ticket').hide();
	}
	
	if (es_hide_tickets_obj.free_entitled == true) {
		$j('.ee-ticket-non-member-ticket').hide();
		$j('.ee-ticket-member-ticket').hide();
	}
	
	//Change button text for buying tickets
	$j("#ticket-selector-submit-5398-btn").prop('value', 'Buy Tickets');
	
	//Scroll to bottom of page after enter attendees
	//$j('#spco-go-to-step-payment_options-btn').click(function () {
		//$j('html, body').animate({scrollTop:$(document).height()}, 'slow');
		//return false;
	//});
});