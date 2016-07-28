/**
* Name : 	admin-duplicate-billing-address.js
* Author : 	Andrew Schultz
* Purpose : Copy Woocommerce billing address to customer address in the wordpress dashboard
*/

var $j = jQuery;

$j(document).ready(function(){
	//set initial state of checkbox to unchecked
	$j('#duplicate-billing-address').removeAttr('checked');
	
	$j( "#duplicate-billing-address" ).change(function() {
		if($j(this).is(":checked")) {
			//if checked then copy all values
            $j("#shipping_first_name").val($j('#billing_first_name').val());
			$j("#shipping_last_name").val($j('#billing_last_name').val());
			$j("#shipping_company").val($j('#billing_company').val());
			$j("#shipping_address_1").val($j('#billing_address_1').val());
			$j("#shipping_address_2").val($j('#billing_address_2').val());
			$j("#shipping_city").val($j('#billing_city').val());
			$j("#shipping_postcode").val($j('#billing_postcode').val());
			$j("#shipping_state").val($j('#billing_state').val());
			$j("#shipping_country").val($j('#billing_country').val());
        }
		else {
			//Clear values when unchecked
			$j("#shipping_first_name").val('');
			$j("#shipping_last_name").val('');
			$j("#shipping_company").val('');
			$j("#shipping_address_1").val('');
			$j("#shipping_address_2").val('');
			$j("#shipping_city").val('');
			$j("#shipping_postcode").val('');
			$j("#shipping_state").val('');
			$j("#shipping_country").val('');
		}
	});
});