/**
* Name : 	user-edit-tabbed-main-fields.js
* Author : 	Andrew Schultz
* Purpose : Disable/Enable fields for Agency
*/

var $j = jQuery; //Wordress set to noConflict() by default

$j(document).ready( function() {

	if(!$j('#agency_account').is(':checked')) {
		$j('#agency_type_row').hide();
		//$j('#agency_name_row').hide();
	}
	
	$j('#agency_account').change(function() {
		if($j(this).is(':checked')) {
			$j('#agency_type_row').show();
			//$j('#agency_name_row').show();
		}
		else {
			$j('#agency_type_row').hide();
			//$j('#agency_name_row').hide();
		}
	});
});