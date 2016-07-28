/**
* Name : 	espresso-registration-page.js
* Author : 	Andrew Schultz
* Purpose : Various functions for the registration page of event espresso
*/

var $j = jQuery; //Wordress set to noConflict() by default

$j(document).ready( function() {

	//Add paypal payment information after div
	$j( "<p>You can pay by credit card using PayPal</p>" ).insertAfter( "#Paypal_Standard-payment-option-dv" );
	
	//Auto click the paypal button link so it picks that payment option as default
	$j('#spco-go-to-step-payment_options-btn').click(function() {
		$j('#Paypal_Standard-payment-option-dv').find('a').trigger('click');
    });
});