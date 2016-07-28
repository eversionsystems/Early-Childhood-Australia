/**
* Name : profile-user-meta.js
* Author : Andrew Schultz
* Purpose : Hide/Display various fields in the dashboard relating to custom demographic fields
*/

var $j = jQuery; //Wordress set to noConflict() by default
//http://digwp.com/2009/06/including-jquery-in-wordpress-the-right-way/


$j(document).ready(function(){

	var myPluginPath = "/wp-content/plugins/eversion-systems";
	var myProtocol = window.location.protocol;
	var myHostURL = window.location.host;
	//Break up URL into components
	var pathArray = window.location.pathname.split( '/' );
	//For some reason zero element is empty
	var myShopPath = pathArray[1];
	var myBaseSiteURL = myProtocol + "//" + myHostURL + "/" + myShopPath;

	//Hide duplicated fields
	$j( 'input:text[name="first_name"]' ).parents( "tr" ).hide();
	$j( 'input:text[name="last_name"]' ).parents( "tr" ).hide();
	$j( 'input:text[name="user_email"]' ).parents( "tr" ).hide();
	
	//Enable editing member number, do this in User Meta Plugin
	//$j( '[name="member_number"]' ).prop('disabled', false);
	
	//Change default date range for birth date
	$j( '[name="birth_date"]' ).datepicker( "option", "yearRange", "1920:2010" );
	$j( '[name="birth_date"]' ).datepicker( "option", "showOn", "both" );
	$j( '[name="birth_date"]' ).datepicker( "option", "buttonImage", myBaseSiteURL + myPluginPath + "/images/datepicker.png" );
	
	//Same for student card expirty
	$j( '[name="student_card_expiry"]' ).datepicker( "option", "yearRange", "2014:2024" );
	$j( '[name="student_card_expiry"]' ).datepicker( "option", "showOn", "both" );
	$j( '[name="student_card_expiry"]' ).datepicker( "option", "buttonImage", myBaseSiteURL + myPluginPath + "/images/datepicker.png" );
	
	//Service established
	$j( '[name="service_established"]' ).datepicker( "option", "showOn", "both" );
	$j( '[name="service_established"]' ).datepicker( "option", "buttonImage", myBaseSiteURL + myPluginPath + "/images/datepicker.png" );
	
	//Hide other entity until other is selected in combobox
	showEntityOtherTextBox();
	
	//Hide selections if no option selected for student
	showStudentOptions();
	
	//Hide Other textbox for professional_development_services checkboxes
	showOtherProfessionalServicesTextBox();
	
	$j('[name="student"]').click(function(){
		showStudentOptions();
	});
	
	$j('[name="entity"]').change(function(){
		showEntityOtherTextBox();
	});
	
	$j('[name="ownership"]').change(function(){
		$j('[name="entity"]').empty();

		addEntityOptions();
	});
	
	$j('input:checkbox[name="professional_development_services[]"]').change(function(){
		showOtherProfessionalServicesTextBox();
	});
	
	function showStudentOptions() {
		if ($j('input:radio[name=student]:checked').val() == "no") {
			$j('input:text[name="university"]').parents("tr" ).hide("slow");
			$j('input:text[name="student_number"]').parents("tr" ).hide("slow");
			$j('input:text[name="student_card_expiry"]').parents("tr" ).hide("slow");
		}
		else if ($j('input:radio[name=student]:checked').val() == "yes") {
			$j('input:text[name="university"]').parents("tr" ).show("slow");
			$j('input:text[name="student_number"]').parents("tr" ).show("slow");
			$j('input:text[name="student_card_expiry"]').parents("tr" ).show("slow");
		}
	}
	
	function showEntityOtherTextBox() {
		if ($j('[name="entity"]').val() == "other") {
			$j('[name="other_entity"]').parents( "tr" ).show("slow");
		}
		else {
			$j('[name="other_entity"]').parents( "tr" ).hide("slow");
		}
	}
	
	function addEntityOptions() {
		switch ($j('[name="ownership"]').find('option:selected').val()) {
		case 'be': 
			$j('[name="entity"]').append($j('<option />').val('pc').text('Private company (Pty Ltd or Partnership)'));
			$j('[name="entity"]').append($j('<option />').val('plc').text('Publicly listed company'));
			$j('[name="entity"]').append($j('<option />').val('other').text('Other please describe below'));
			break;
		case 'ngo': 
			$j('[name="entity"]').append($j('<option />').val('ia').text('Incorporated Association'));
			$j('[name="entity"]').append($j('<option />').val('clg').text('Company Limited by Guarantee'));
			$j('[name="entity"]').append($j('<option />').val('tf').text('Trust or Foundation'));
			$j('[name="entity"]').append($j('<option />').val('other').text('Other please describe below'));
			break;
		case 'gad': 
			$j('[name="entity"]').append($j('<option />').val('lgc').text('Local Government or Council '));
			$j('[name="entity"]').append($j('<option />').val('sgd').text('State Government Department'));
			$j('[name="entity"]').append($j('<option />').val('fgd').text('Federal Government Department'));
			$j('[name="entity"]').append($j('<option />').val('sa').text('Statutory Authority'));
			$j('[name="entity"]').append($j('<option />').val('other').text('Other please describe below'));
			break;
		}
	}
	
	function showOtherProfessionalServicesTextBox() {
		var selectedValue = '';
		
		//Default hide if none selected
		var numSelected = $j( 'input[name="professional_development_services[]"]:checked' ).length;
		
		if (numSelected === 0) {
			$j('[name="other_professional_services"]').parents( "tr" ).hide("slow");
		}
		
		$j.each($j("input[name='professional_development_services[]']:checked"), function() {
		  selectedValue =$j(this).val();
		  if (selectedValue == "other") {
			$j('[name="other_professional_services"]').parents( "tr" ).show("slow");
		  }
		  else {
			$j('[name="other_professional_services"]').parents( "tr" ).hide("slow");
		  }
		});
	}

});