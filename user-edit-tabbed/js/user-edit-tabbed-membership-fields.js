/**
* Name : user-edit-tabbed-membership-fields.js
* Author : Andrew Schultz
* Purpose : Setup various membership fields
*/

var $j = jQuery; //Wordress set to noConflict() by default
//http://digwp.com/2009/06/including-jquery-in-wordpress-the-right-way/


$j(document).ready(function(){

	var myPluginPath = "/wp-content/plugins/user-edit-tabbed";
	var myProtocol = window.location.protocol;
	var myHostURL = window.location.host;
	//Break up URL into components
	var pathArray = window.location.pathname.split( '/' );
	//For some reason zero element is empty
	var myShopPath = pathArray[1];
	var myBaseSiteURL = myProtocol + "//" + myHostURL + "/" + myShopPath;

	//Complimentary Memberships
	$j( '#life_memb_start_dtm' ).datepicker({onSelect: function() {
         $j("#life_memb").prop('checked', true);},
		 changeMonth: true,changeYear: true,dateFormat: 'yy-mm-dd', showOn: 'both', buttonImage: myBaseSiteURL + myPluginPath + '/images/calendar.gif'});
	$j( '#comp_memb_end_dtm' ).datepicker({onSelect: function() {
         $j("#comp_memb").prop('checked', true);},changeMonth: true,changeYear: true,dateFormat: 'yy-mm-dd', showOn: 'both', buttonImage: myBaseSiteURL + myPluginPath + '/images/calendar.gif'});
	$j( '#comp_memb_AJEC_end_dtm' ).datepicker({onSelect: function() {
         $j("#comp_memb_AJEC").prop('checked', true);},changeMonth: true,changeYear: true,dateFormat: 'yy-mm-dd', showOn: 'both', buttonImage: myBaseSiteURL + myPluginPath + '/images/calendar.gif'});
	$j( '#comp_memb_EC_end_dtm' ).datepicker({onSelect: function() {
         $j("#comp_memb_EC").prop('checked', true);},changeMonth: true,changeYear: true,dateFormat: 'yy-mm-dd', showOn: 'both', buttonImage: myBaseSiteURL + myPluginPath + '/images/calendar.gif'});
	$j( '#comp_memb_EDL_end_dtm' ).datepicker({onSelect: function() {
         $j("#comp_memb_EDL").prop('checked', true);},changeMonth: true,changeYear: true,dateFormat: 'yy-mm-dd', showOn: 'both', buttonImage: myBaseSiteURL + myPluginPath + '/images/calendar.gif'});
	$j( '#comp_memb_RIP_end_dtm' ).datepicker({onSelect: function() {
         $j("#comp_memb_RIP").prop('checked', true);},changeMonth: true,changeYear: true,dateFormat: 'yy-mm-dd', showOn: 'both', buttonImage: myBaseSiteURL + myPluginPath + '/images/calendar.gif'});
	
	//Not renewing datetime fields
	$j( '#not_renew_memb_end_dtm' ).datepicker({onSelect: function() {
         $j("#not_renew_memb").prop('checked', true);},changeMonth: true,changeYear: true,dateFormat: 'yy-mm-dd', showOn: 'both', buttonImage: myBaseSiteURL + myPluginPath + '/images/calendar.gif'});
	$j( '#not_renew_AJEC_end_dtm' ).datepicker({onSelect: function() {
         $j("#not_renew_AJEC").prop('checked', true);},changeMonth: true,changeYear: true,dateFormat: 'yy-mm-dd', showOn: 'both', buttonImage: myBaseSiteURL + myPluginPath + '/images/calendar.gif'});
	$j( '#not_renew_EC_end_dtm' ).datepicker({onSelect: function() {
         $j("#not_renew_EC").prop('checked', true);},changeMonth: true,changeYear: true,dateFormat: 'yy-mm-dd', showOn: 'both', buttonImage: myBaseSiteURL + myPluginPath + '/images/calendar.gif'});
	$j( '#not_renew_EDL_end_dtm' ).datepicker({onSelect: function() {
         $j("#not_renew_EDL").prop('checked', true);},changeMonth: true,changeYear: true,dateFormat: 'yy-mm-dd', showOn: 'both', buttonImage: myBaseSiteURL + myPluginPath + '/images/calendar.gif'});
	$j( '#not_renew_RIP_end_dtm' ).datepicker({onSelect: function() {
         $j("#not_renew_RIP").prop('checked', true);},changeMonth: true,changeYear: true,dateFormat: 'yy-mm-dd', showOn: 'both', buttonImage: myBaseSiteURL + myPluginPath + '/images/calendar.gif'});
	
	//Life time member checked default to today date if blank
	$j("#life_memb").click(function() {
		var isChecked = $j(this).prop('checked');
		if (isChecked)
		{
			$j("#life_memb_start_dtm").datepicker({dateFormat: "yy-mm-dd"}).datepicker("setDate", "0");
		}
	});
	
	/*Need to add event for calendar button click
	$j('.ui-datepicker-trigger').click(function() {
		var prevID = $j(this).prev().attr('id');
		if (prevID == 'life_memb_start_dtm')
			$j("#life_memb").prop('checked', true);
	});*/
	
	//Reset forms elements for membership fields
	$j("#reset_comp_memb").click(function() {
		$j( '#comp_memb_end_dtm' ).val('');
		$j( '#comp_memb_AJEC_end_dtm' ).val('');
		$j( '#comp_memb_EC_end_dtm' ).val('');
		$j( '#comp_memb_EDL_end_dtm' ).val('');
		$j( '#comp_memb_RIP_end_dtm' ).val('');
		$j( '#comp_memb' ).prop('checked', false);
		$j( '#comp_memb_AJEC' ).prop('checked', false);
		$j( '#comp_memb_EC' ).prop('checked', false);
		$j( '#comp_memb_EDL' ).prop('checked', false);
		$j( '#comp_memb_RIP' ).prop('checked', false);
	});
	
	//Reset non renewing fields
	$j("#reset_not_renew").click(function() {
		$j( '#not_renew_memb_end_dtm' ).val('');
		$j( '#not_renew_AJEC_end_dtm' ).val('');
		$j( '#not_renew_EC_end_dtm' ).val('');
		$j( '#not_renew_EDL_end_dtm' ).val('');
		$j( '#not_renew_RIP_end_dtm' ).val('');
		$j( '#not_renew_memb' ).prop('checked', false);
		$j( '#not_renew_AJEC' ).prop('checked', false);
		$j( '#not_renew_EC' ).prop('checked', false);
		$j( '#not_renew_EDL' ).prop('checked', false);
		$j( '#not_renew_RIP' ).prop('checked', false);
	});
});