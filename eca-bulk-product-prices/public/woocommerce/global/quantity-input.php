<?php
/**
 * Product quantity inputs
 *
 * @author  WooThemes
 * @package WooCommerce/Templates
 * @version 2.1.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ($sku == 'COEBRCE') {
	
	global $woocommerce;
	$items = $woocommerce->cart->get_cart();

	foreach($items as $item => $values) { 
		
		$product_id = $values['product_id'];
		$product_object = new WC_Product( $product_id );
		
		if( $sku == $product_object->get_sku() )
			$input_value = $values['quantity'];
		
	}
?>
	<div class="quantity_select">
		<select name="<?php echo esc_attr( $input_name ); ?>" title="<?php _ex( 'Qty', 'Product quantity input tooltip', 'woocommerce' ) ?>" class="qty">
		<?php
		
		$product_quantities = array('10', '20', '30', '40', '50', '60', '70', '80', '90', '100', '200', '300', '400', '500', '600', '700', '800');
		
		foreach ( $product_quantities as $value) {
			if ( $value == $input_value )
				$selected = ' selected';
			else 
				$selected = '';
			echo '<option value="' . $value . '"' . $selected . '>' . $value . '</option>';
		}
		?>
		</select>
	</div>
<?php
}
else {
?>
<div class="quantity">
	<input type="number" step="<?php echo esc_attr( $step ); ?>" min="<?php echo esc_attr( $min_value ); ?>" max="<?php echo esc_attr( $max_value ); ?>" name="<?php echo esc_attr( $input_name ); ?>" value="<?php echo esc_attr( $input_value ); ?>" title="<?php echo esc_attr_x( 'Qty', 'Product quantity input tooltip', 'woocommerce' ) ?>" class="input-text qty text" size="4" pattern="<?php echo esc_attr( $pattern ); ?>" inputmode="<?php echo esc_attr( $inputmode ); ?>" />
</div>
<?php
}