/**
* Name : my-account-user-meta.js
* Author : Andrew Schultz
* Purpose : Hide/Display various fields on my-account page relating to custom demographic fields
*/

var $j = jQuery; //Wordress set to noConflict() by default
//http://digwp.com/2009/06/including-jquery-in-wordpress-the-right-way/


$j(document).ready(function(){

	//$j( '<div style="clear: both;"></div>' ).insertAfter( "#um_field_19_individualmember_label" );
	var myPluginPath = "/wp-content/plugins/eversion-systems";
	var myProtocol = window.location.protocol;
	var myHostURL = window.location.host;
	//Break up URL into components
	var pathArray = window.location.pathname.split( '/' );
	//For some reason zero element is empty
	var myShopPath = pathArray[1];
	var myBaseSiteURL = myProtocol + "//" + myHostURL + "/" + myShopPath;
	
	//Disable editing of member number, do this in User Meta Plugin
	//$j( '[name="member_number"]' ).prop('disabled', true);
	
	//Change default date range for birth date
	$j( '[name="birth_date"]' ).datepicker( "option", "yearRange", "1920:2010" );
	$j( '[name="birth_date"]' ).datepicker( "option", "showOn", "both" );
	$j( '[name="birth_date"]' ).datepicker( "option", "buttonImage", myBaseSiteURL + myPluginPath + "/images/datepicker.png" );
	
	//Same for student card expirty
	$j( '[name="student_card_expiry"]' ).datepicker( "option", "yearRange", "2014:2024" );
	$j( '[name="student_card_expiry"]' ).datepicker( "option", "showOn", "both" );
	$j( '[name="student_card_expiry"]' ).datepicker( "option", "buttonImage", myBaseSiteURL + myPluginPath + "/images/datepicker.png" );
	
	//Service eastablish
	$j( '[name="service_established"]' ).datepicker( "option", "showOn", "both" );
	$j( '[name="service_established"]' ).datepicker( "option", "buttonImage", myBaseSiteURL + myPluginPath + "/images/datepicker.png" );

	//Hide other entity until other is selected in combobox
	showEntityOtherTextBox();
	
	//Hide selections if no option selected for student
	showStudentOptions();
	
	//Hide Other textbox for professional_development_services checkboxes
	showOtherProfessionalServicesTextBox();
	
	//Move description for checkboxes underneath label
	//$j("#um_field_27_organisationmember_description").appendTo("#um_field_27_organisationmember_label");

	$j('[name="ownership"]').change(function(){
		$j('[name="entity"]').empty();

		addEntityOptions();
	});
	
	$j('[name="entity"]').change(function(){
		showEntityOtherTextBox();
	});
	
	$j('[name="student"]').click(function(){
		showStudentOptions();
	});
	
	$j('input:checkbox[name="professional_development_services[]"]').change(function(){
		showOtherProfessionalServicesTextBox();
	});
	
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
	
	function showEntityOtherTextBox() {
		if ($j('[name="entity"]').val() == "other") {
			$j('[name="other_entity"]').parent( ".um_field_container" ).show("slow");
		}
		else {
			$j('[name="other_entity"]').parent( ".um_field_container" ).hide("slow");
		}
	}
	
	function showOtherProfessionalServicesTextBox() {
		var selectedValue = '';
		
		//Default hide if none selected
		var numSelected = $j( 'input[name="professional_development_services[]"]:checked' ).length;
		
		if (numSelected === 0) {
			$j('[name="other_professional_services"]').parent( ".um_field_container" ).hide("slow");
		}
		
		$j.each($j("input[name='professional_development_services[]']:checked"), function() {
		  selectedValue =$j(this).val();
		  if (selectedValue == "other") {
			$j('[name="other_professional_services"]').parent( ".um_field_container" ).show("slow");
		  }
		  else {
			$j('[name="other_professional_services"]').parent( ".um_field_container" ).hide("slow");
		  }
		});
	}
	
	function showStudentOptions() {
		if ($j('input:radio[name=student]:checked').val() == "no") {
			$j('input:text[name="university"]').parent(".um_field_container" ).hide("slow");
			$j('input:text[name="student_number"]').parent(".um_field_container" ).hide("slow");
			$j('input:text[name="student_card_expiry"]').parent(".um_field_container" ).hide("slow");
			//alert($j('input:radio[name=student]:checked').val());
			//$('#select-table > .roomNumber').attr('enabled',false);
		}
		else if ($j('input:radio[name=student]:checked').val() == "yes") {
			$j('input:text[name="university"]').parent(".um_field_container" ).show("slow");
			$j('input:text[name="student_number"]').parent(".um_field_container" ).show("slow");
			$j('input:text[name="student_card_expiry"]').parent(".um_field_container" ).show("slow");
		}
	}
	
	//Contact table default the value of user_id to the current user
	//Modify wpDataTables.js instead of here as this event fires before the wpDataTables file
	$j( "#new_table_0" ).click(function() {
		//alert( "Handler for .click() called." );
		
		//$j("#table_0_user_id").val("3");
		$j.ajax({
			url: myBaseSiteURL + "/wp-admin/admin-ajax.php",
			data: {action : "get_current_user_id_datatable"},
			datatype: "json",
		    success: function(data) {
				//alert("user id = " + data);
				$j("#table_0_user_id").val(data);
				$j("#table_0_create_dtm").parents("tr").hide();
				//Disable control from being editted
				$j("#table_0_user_id").parents("tr").hide();
		  }
		});
	});
});