<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    ECA_Bulk_Product_Prices
 * @subpackage ECA_Bulk_Product_Prices/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    ECA_Bulk_Product_Prices
 * @subpackage ECA_Bulk_Product_Prices/public
 * @author     Your Name <email@example.com>
 */
class ECA_Bulk_Product_Prices_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $ECA_Bulk_Product_Prices    The ID of this plugin.
	 */
	private $ECA_Bulk_Product_Prices;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;
	
	/**
	 * Store all the bulk prices/quantities.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      array    $bulk_prices    Store all the bulk prices/quantities.
	 */
	protected $bulk_prices;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $ECA_Bulk_Product_Prices       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $ECA_Bulk_Product_Prices, $version ) {

		$this->ECA_Bulk_Product_Prices = $ECA_Bulk_Product_Prices;
		$this->version = $version;
		
		$this->bulk_prices = array (
				array(10,9,10),
				array(20, 10.10, 11.1),
				array(30, 13.85, 15.25),
				array(40, 17.10, 18.8),
				array(50, 19.65, 21.6),
				array(60, 21.95, 24.15),
				array(70, 22.65, 24.90),
				array(80, 24.05, 26.4),
				array(90, 26, 28.6),
				array(100, 28.7, 31.55),
				array(200, 44.5, 49),
				array(300, 60.75, 66.9),
				array(400, 77.92, 85.72),
				array(500, 93.35, 102.65),
				array(600, 108.78, 119.58),
				array(700, 124.6, 137.2),
				array(800, 152, 167.2)
			);
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in ECA_Bulk_Product_Prices_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The ECA_Bulk_Product_Prices_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->ECA_Bulk_Product_Prices, plugin_dir_url( __FILE__ ) . 'css/eca-bulk-product-prices-public.css', array(), self::script_version_id(), 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in ECA_Bulk_Product_Prices_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The ECA_Bulk_Product_Prices_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		if(is_product()) {
			global $woocommerce;
			global $product;

			$items = $woocommerce->cart->get_cart();
			$quantity = 0;
			 
			foreach ( $items as $key => $values ) {
				if($values['data']->get_sku() == 'COEBRCE') {
					$quantity = $values['quantity'];
					$member_exists = es_check_membership_held();
					break;
				}
			}
			 
			$object_array = array(
				'prices' => $this->bulk_prices,
				'is_member' => es_check_membership_held(),
				'selected_quantity' => $quantity,
				'product_slug' => $product
			);
			
			//write_log($product);
		
			//if($product->get_sku() == 'COEBRCE') {
		
				wp_register_script( $this->ECA_Bulk_Product_Prices, plugin_dir_url( __FILE__ ) . 'js/eca-bulk-product-prices-public.js', array( 'jquery' ), self::script_version_id(), false );
				wp_localize_script( $this->ECA_Bulk_Product_Prices, 'bulk_prices', $object_array ); 
				wp_enqueue_script( $this->ECA_Bulk_Product_Prices );
				//http://openexchangerates.github.io/accounting.js/
				wp_enqueue_script( 'accounting', plugin_dir_url( __FILE__ ) . 'js/accounting.min.js', array( 'jquery' ), self::script_version_id(), false );
			//}
		}
	}
	
	/**
	 * Locate and override WooCommerce templates in the plugin directory.
	 *
	 * @since    1.0.0
	 */
	public function woocommerce_locate_template( $template, $template_name, $template_path ) {
	 
		global $woocommerce;

		$_template = $template;

		if ( ! $template_path ) $template_path = $woocommerce->template_url;
		$plugin_path  = plugin_dir_path( __FILE__ ) . 'woocommerce/';

		// Look within passed path within the theme - this is priority
		$template = locate_template(
			array(
			  $template_path . $template_name,
			  $template_name
			)
		);

		// Modification: Get the template from this plugin, if it exists
		if ( ! $template && file_exists( $plugin_path . $template_name ) )
			$template = $plugin_path . $template_name;

		// Use default template
		if ( ! $template )
		$template = $_template;

		return $template;
		
	}
	
	/**
	 * Show the price of the bulk item on the shop page.
	 *
	 * @since    1.0.0
	 */
	public function get_bulk_price($price, $product) {
		if($product->get_sku() == 'COEBRCE') {
			
			return 'From $10.00 for 10';
		}
		
		return $price;
	}
	
	/**
	 * Set default attributes for bulk products.
	 *
	 * @since    1.0.0
	 */
	public function product_attributes( $defaults, $product) {

		if($product->get_sku() == 'COEBRCE') {
			//$defaults['input_value'] = 10;
			$defaults['min_value'] = 10;
		}
		
		//add args to pass to quantity-input.php
		$defaults['sku'] = $product->get_sku();

		return $defaults;
	}
	
	/**
	 * Set the individual cart price per product for bulk items.
	 *
	 * @since    1.0.0
	 */
	public function set_cart_item_product_price( $cart_object ) {
		
		foreach ( $cart_object->cart_contents as $key => $values ) {
			if($values['data']->get_sku() == 'COEBRCE') {
				$quantity = $values['quantity'];
				$member_exists = es_check_membership_held();
				
				foreach ($this->bulk_prices as $value) {
					if($value[0] == $quantity) {
						if($member_exists)
							$values['data']->price = $value[1] / $quantity;
						else 
							$values['data']->price = $value[2] / $quantity;
						
						break;
					}
				}
			}
		}
	}
	
	/**
	 * Remove the add to cart button for certain products on the single product page.
	 *
	 * @since    1.0.0
	 */
	public function remove_add_to_cart_button() {
		global $product;
		global $woocommerce;
		$items = $woocommerce->cart->get_cart();
		
		$product_sku = $product->get_sku();
		
		if($product_sku == 'COEBRCE') {
			
			foreach($items as $cart_item_key => $values) { 

				if(isset($values['product_id'])) {
					//$product_id = $values['product_id'];
					//$product_object = new WC_Product( $product_id );
					
					$quantity = 0;
					$quantity = $values['quantity'];
					//$product_sku = $product_object->get_sku();
					
					if( $quantity >= 10 ) {
						remove_action('woocommerce_simple_add_to_cart', 'woocommerce_simple_add_to_cart', 30);
						//add_filter( 'woocommerce_is_purchasable', false );
						/*echo apply_filters( 'woocommerce_cart_item_remove_link', sprintf(
											'<a href="%s" class="button alt" title="%s" data-product_id="%s" data-product_sku="%s">Remove from basket</a>',
											esc_url( WC()->cart->get_remove_url( $cart_item_key ) ),
											__( 'Remove this item', 'woocommerce' ),
											esc_attr( $product_id ),
											esc_attr( $product_sku )
										), $cart_item_key );*/
					}
				}
			}
		}
	}
	
	/**
	 * Add a remove from cart button when a user has already added the bulk item to the cart to prevent them adding an invalid quantity.
	 *
	 * @since    1.0.0
	 */
	public function add_remove_from_cart_button() {
		global $product;
		global $woocommerce;
		
		$items = $woocommerce->cart->get_cart();
		$product_sku = $product->get_sku();
		
		if($product_sku == 'COEBRCE') {
			foreach($items as $cart_item_key => $values) { 

				if(isset($values['product_id'])) {
					$product_id = $values['product_id'];
					$product_object = new WC_Product( $product_id );
					
					$quantity = 0;
					$quantity = $values['quantity'];
					//$product_sku = $product_object->get_sku();
					
					if( $quantity >= 10 ) {
						remove_action('woocommerce_simple_add_to_cart', 'woocommerce_simple_add_to_cart', 30);
						//add_filter( 'woocommerce_is_purchasable', false );
						echo apply_filters( 'woocommerce_cart_item_remove_link', sprintf(
											'<a href="%s" class="button alt" title="%s" data-product_id="%s" data-product_sku="%s">Remove from basket</a>',
											esc_url( WC()->cart->get_remove_url( $cart_item_key ) ),
											__( 'Remove this item', 'woocommerce' ),
											esc_attr( $product_id ),
											esc_attr( $product_sku )
										), $cart_item_key );
					}
				}
			}
		}
	}
	
	/**
	 * Show a read more button instead of a add to cart button for the bulk item on the shop loop page
	 *
	 * @since    1.0.0
	 */
	public function remove_add_to_cart_button_from_loop($link, $product) {

		$product_sku = $product->get_sku();

		if($product_sku == 'COEBRCE') {
			return sprintf( '<a rel="nofollow" href="%s" data-quantity="%s" data-product_id="%s" data-product_sku="%s" class="%s">%s</a>',
				esc_url( $product->get_permalink() ),
				esc_attr( isset( $quantity ) ? $quantity : 1 ),
				esc_attr( $product->id ),
				esc_attr( $product->get_sku() ),
				esc_attr( isset( $class ) ? $class : 'button' ),
				esc_html(  esc_html__( 'Read More', 'woocommerce' ) )
			);
		}
		
		return $link;
	}
	
	/**
	 * Change the order of the way the single product summary displays
	 *
	 * @since    1.0.0
	 */
	public function change_woocommerce_single_product_summary_order() {
		global $post;
		global $product;	//Returns slug of product
		
		$product = new WC_Product($post->ID);
		$sku = $product->get_sku();
		
		if($sku == 'COEBRCE') {
			
			remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_title', 5 );
			remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_rating', 10 );
			remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );
			remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20 );
			remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40 );
			remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_sharing', 50 );
			
			add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_title', 5 );
			add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_rating', 20 );
			add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 20 );
			add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 10 );
			add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40 );
			add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_sharing', 50 );
		}
	}
	
	/**
	 * Show the pricing table for COEBRCE
	 *
	 * @since    1.0.0
	 */
	public function show_table_quantity() {
		global $product;
		
		$product_sku = $product->get_sku();

		if($product_sku == 'COEBRCE') {
		?>
		<p><strong>Quantity Discounts</strong></p>
		<table id="eca-table-discounts">
			<tr>
				<th>Quantity</th>
				<th>Member Price</th> 
				<th>Non-Member Price</th>
			</tr>
			<?php foreach ($this->bulk_prices as $bulk_price) { ?>
			<tr>
				<td><?php echo $bulk_price[0]; ?></td>
				<td><?php echo wc_price($bulk_price[1]); ?></td>
				<td><?php echo wc_price($bulk_price[2]); ?></td>
			</tr>
			<?php } ?>
		</table>
		<?php		
		}
	}
	
	/**
	 * Used for debugging when we want javascript and stylesheets to reflect our changes
	 */
	public function script_version_id() {
		if ( WP_DEBUG )
			return time();
		return $this->version;
	}
}
