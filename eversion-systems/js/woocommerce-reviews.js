/**
* Name : woocommerce-reviews.js
* Author : Andrew Schultz
* Purpose : Prepopulate the review text with standard text to allow star reviews to be saved
*			otherwise an error occurs saying the field is blank
*/

var $j = jQuery;

$j(document).ready(function(){

	//Default text value for review
	$j('#comment').val("Review Comment");
});