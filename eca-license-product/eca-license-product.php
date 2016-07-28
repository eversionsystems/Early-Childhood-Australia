<?php
/*
Plugin Name: ECA License Product
Plugin URI: http://eversionsystems.com
Description: Create a license product
Version: 1.0
Author: Andrew Schultz
Author URI: http://eversionsystems.com
License: GPL2
*/

require_once( dirname(__FILE__) . '/fpdf/fpdf.php' );
require_once( dirname(__FILE__) . '/fpdi/fpdi.php' );

/**
* Add Checkbox for licensed product
*/
add_action('woocommerce_product_options_general_product_data', 'es_display_license_product_checkbox' );

function es_display_license_product_checkbox($thepostid) {
	global $woocommerce, $thepostid;
    echo '<div class="options_group show_if_subscription">';
	
	woocommerce_wp_checkbox(
		array(
		'id'            => 'license_product',
		'label'         => __('Licensed Product', 'woocommerce' ),
		'desc_tip'    	=> 'true',
		'description'   => __( 'Is the subscription a license type?', 'woocommerce' ),
	));
		
	echo '</div>';
}

/*
*Save the custom fields for the product
*/
add_action( 'woocommerce_process_product_meta', 'es_save_license_product_settings' );

function es_save_license_product_settings( $post_id ){

	// save purchasable option
	$is_licensed_product = isset( $_POST['license_product'] ) ? 'yes' : 'no';
	update_post_meta( $post_id, 'license_product', $is_licensed_product );
}

/**
* Add a ABN field on checkout for licensed product
*/

// Hook in
add_filter( 'woocommerce_checkout_fields' , 'eca_add_custom_license_checkout_fields' );

// Our hooked in function - $fields is passed via the filter!
function eca_add_custom_license_checkout_fields( $fields ) {
	
	$show_abn = es_hide_terms_and_conditions();
	
	if($show_abn) {
		
		$fields2['billing']['billing_first_name'] = $fields['billing']['billing_first_name'];
		$fields2['billing']['billing_last_name'] = $fields['billing']['billing_last_name'];
		$fields2['billing']['billing_company'] = $fields['billing']['billing_company'];
		
		$fields2['billing']['billing_abn'] = array(
			'label'     => __('ABN', 'woocommerce'),
			'placeholder'   => _x('ABN', 'placeholder', 'woocommerce'),
			'required'  => true,
			'class'     => array('form-row-wide'),
			'clear'     => true
		);
		
		$fields2['billing']['billing_address_google'] = $fields['billing']['billing_address_google'];
		$fields2['billing']['billing_address_1'] = $fields['billing']['billing_address_1'];
		$fields2['billing']['billing_address_2'] = $fields['billing']['billing_address_2'];
		$fields2['billing']['billing_city'] = $fields['billing']['billing_city'];
		$fields2['billing']['billing_state'] = $fields['billing']['billing_state'];
		$fields2['billing']['billing_postcode'] = $fields['billing']['billing_postcode'];
		$fields2['billing']['billing_country'] = $fields['billing']['billing_country'];
		$fields2['billing']['billing_phone'] = $fields['billing']['billing_phone'];
		$fields2['billing']['billing_email'] = $fields['billing']['billing_email'];

		//just copying these (keeps the standard order)
		$fields2['shipping'] = $fields['shipping'];
		$fields2['account'] = $fields['account'];
		$fields2['order'] = $fields['order'];

		return $fields2;
	}


    return $fields;
}


/*
* Enable the terms and conditions for licensed products
*/

add_filter('woocommerce_checkout_show_terms','es_hide_terms_and_conditions');

function es_hide_terms_and_conditions(){
	global $woocommerce;
	
	if ( sizeof( $woocommerce->cart->get_cart() ) > 0 ) {
		foreach ( $woocommerce->cart->get_cart() as $cart_item_key => $values ) {
			$_product = $values['data'];
			$_custom_values = $_product->product_custom_fields;
			
			if(isset($_custom_values['license_product']))
				$licence_product = $_custom_values['license_product'];
			
			if (isset($licence_product) && $licence_product[0] == 'yes' ){
				return true;
			}
		}
	}
	
	return false;
}

add_filter( 'woocommerce_email_attachments', 'es_attach_terms_conditions_pdf_to_email', 10, 3);

function es_attach_terms_conditions_pdf_to_email ( $attachments, $status , $order ) {
	
	//$allowed_statuses = array( 'customer_processing_order', 'customer_completed_order' );
	
	$allowed_statuses = array( 'customer_completed_order' );
	
	$order_items = $order->get_items();
	
	$order_id = $order->id;
	$order_date = date_i18n( __( 'd/m/Y', 'woocommerce' ), strtotime( $order->order_date ));
	
	//Add a year to the order date
	$tmp_date = date_create($order->order_date);
	date_add($tmp_date, date_interval_create_from_date_string("365 days"));
	$end_date = date_format($tmp_date,"d/m/Y");
	
	//$end_date = date('d/m/Y', strtotime("+365 days"));
	
	$billing_address = $order->get_address('billing');
	//write_log($billing_address);
	//write_log('Billing address line 1 = '.$order->billing_address_1);
	$company_abn = $order->billing_abn;
	$company_name = $billing_address['company'];
	$full_address = $billing_address['address_1'].', '.$billing_address['city'].', '.$billing_address['state'].', '.$billing_address['postcode'];

	//write_log($order_date);
	//write_log($company_name);
	//write_log($full_address);
	
    foreach ($order_items as $order_item_id => $order_item) { 
		//write_log($order_item);
		$is_licensed_product = get_post_meta($order_item['product_id'], 'license_product', true );
		
		if($is_licensed_product)
			break;
		//$is_licensed_product = $order->get_item_meta($order_item_id, 'license_product');
		//write_log('Is licensed = '.$is_licensed_product);
    }
	
	if( isset( $status ) && in_array ( $status, $allowed_statuses ) && $is_licensed_product ) {
		//$pdf_path = get_template_directory() . '/Service_Provider_PDF_Agreement_Template.pdf';
		$dir = plugin_dir_path( __FILE__ );
		$pdf_path = $dir . '/Service_Provider_PDF_Agreement_Template.pdf';
		//write_log($pdf_path);
		
		if (!file_exists($dir.'/temp')) {
			mkdir($dir.'/temp', 0755, true);
		}
		
		//$dest_file = get_home_path().'wp-content/temp/terms_and_conditions-'.$order_id.'.pdf';
		$dest_file = $dir.'/temp/terms_and_conditions-'.$order_id.'.pdf';
		
		//$dest_file = '/webfiles/shoptest/tmp/terms_and_conditions-'.$order_id.'.pdf';
		
		//$pdf = new FPDI();
		$pdf= new ESPDF();
		
		/*$pdf->AddPage();
		$pdf->setSourceFile($pdf_path); 
		// import page 1 for modifying
		$tplIdx = $pdf->importPage(1); 
		//use the imported page and place it at point 0,0; calculate width and height
		//automaticallay and ajust the page size to the size of the imported page 
		$pdf->useTemplate($tplIdx, 0, 0, 0, 0, true); 
		
		$pdf->SetFont('Arial'); 
		$pdf->SetTextColor(255,0,0); 
		$pdf->SetXY(25, 25); 
		$pdf->Write(0, "This is just a simple text"); 
		
		$pdf->Output( $dest_file, 'F' );*/
		
		$pageCount = $pdf->setSourceFile($pdf_path);
		
		$pdf->SetRightMargin(25);
		$pdf->SetFont('Arial','B',10);
		
		// iterate through all pages
		for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
			
			$templateId = $pdf->importPage($pageNo);
			// get the size of the imported page
			$size = $pdf->getTemplateSize($templateId);

			// create a page (landscape or portrait depending on the imported page size)
			if ($size['w'] > $size['h']) {
				$pdf->AddPage('L', array($size['w'], $size['h']));
			} else {
				$pdf->AddPage('P', array($size['w'], $size['h']));
			}
			
			//If page 1 then we create the whole page
			if($pageNo == 1) {
				$pdf->useTemplate($templateId);
				// Removes bold
				//$pdf->SetFont('');
				//This works to write html tags -> $pdf->WriteHTML('<b>Testing</b>');
				$pdf->SetXY(24, 25);
				//$pdf->Write(5, 'THIS AGREEMENT IS MADE ON '.$order_date.' BETWEEN '.$company_name.' (ABN '.$company_abn.') OF '.$full_address.' (You)');
				$pdf->MultiCell(0, 5, 'THIS AGREEMENT IS MADE ON '.$order_date.' BETWEEN '.$company_name.' (ABN '.$company_abn.') OF '.$full_address.' (You)');
				//$pdf->Ln();
				$pdf->SetXY(24, 140); //130-150
				$pdf->MultiCell(0, 5, 'THE TERM OF THIS AGREEMENT IS FROM '.$order_date.' TO '.$end_date.'.');
			}
			elseif($pageNo == 3) {
				$pdf->useTemplate($templateId);
				$pdf->SetFont('Arial','',10);
				$pdf->SetXY(24, 195); //200 //190 //25 //20
				//$pdf->MultiCell(0, 5,'Licence Year means any consecutive period of 12 months commencing on '.$order_date.'.');
				$pdf->WriteHTML('<b>Licence Year</b> means any consecutive period of 12 months commencing on '.$order_date.'.');
			}
			else {
				// use the imported page
				$pdf->useTemplate($templateId);
			}
		}
		
		$pdf->Output( $dest_file, 'F' );
		
		$attachments[] = $dest_file;
	}
	
	//Path need to be in this format (absolute server path)
	///home/eversion/public_html/demo/wp-content/themes/twentythirteen/Service_Provider_PDF_Agreement_2015.pdf
	
	return $attachments;
}

//require('fpdi/fpdi.php');

class ESPDF extends FPDI
{
	protected $B = 0;
	protected $I = 0;
	protected $U = 0;
	protected $HREF = '';

	function WriteHTML($html)
	{
		// HTML parser
		$html = str_replace("\n",' ',$html);
		$a = preg_split('/<(.*)>/U',$html,-1,PREG_SPLIT_DELIM_CAPTURE);
		foreach($a as $i=>$e)
		{
			if($i%2==0)
			{
				// Text
				if($this->HREF)
					$this->PutLink($this->HREF,$e);
				else
					$this->Write(5,$e);
			}
			else
			{
				// Tag
				if($e[0]=='/')
					$this->CloseTag(strtoupper(substr($e,1)));
				else
				{
					// Extract attributes
					$a2 = explode(' ',$e);
					$tag = strtoupper(array_shift($a2));
					$attr = array();
					foreach($a2 as $v)
					{
						if(preg_match('/([^=]*)=["\']?([^"\']*)/',$v,$a3))
							$attr[strtoupper($a3[1])] = $a3[2];
					}
					$this->OpenTag($tag,$attr);
				}
			}
		}
	}

	function OpenTag($tag, $attr)
	{
		// Opening tag
		if($tag=='B' || $tag=='I' || $tag=='U')
			$this->SetStyle($tag,true);
		if($tag=='A')
			$this->HREF = $attr['HREF'];
		if($tag=='BR')
			$this->Ln(5);
	}

	function CloseTag($tag)
	{
		// Closing tag
		if($tag=='B' || $tag=='I' || $tag=='U')
			$this->SetStyle($tag,false);
		if($tag=='A')
			$this->HREF = '';
	}

	function SetStyle($tag, $enable)
	{
		// Modify style and select corresponding font
		$this->$tag += ($enable ? 1 : -1);
		$style = '';
		foreach(array('B', 'I', 'U') as $s)
		{
			if($this->$s>0)
				$style .= $s;
		}
		$this->SetFont('',$style);
	}

	function PutLink($URL, $txt)
	{
		// Put a hyperlink
		$this->SetTextColor(0,0,255);
		$this->SetStyle('U',true);
		$this->Write(5,$txt,$URL);
		$this->SetStyle('U',false);
		$this->SetTextColor(0);
	}
}

?>