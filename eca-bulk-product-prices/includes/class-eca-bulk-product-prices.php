<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    ECA_Bulk_Product_Prices
 * @subpackage ECA_Bulk_Product_Prices/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    ECA_Bulk_Product_Prices
 * @subpackage ECA_Bulk_Product_Prices/includes
 * @author     Your Name <email@example.com>
 */
class ECA_Bulk_Product_Prices {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      ECA_Bulk_Product_Prices_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $ECA_Bulk_Product_Prices    The string used to uniquely identify this plugin.
	 */
	protected $ECA_Bulk_Product_Prices;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		$this->ECA_Bulk_Product_Prices = 'eca-bulk-product-prices';
		$this->version = '1.0.1';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - ECA_Bulk_Product_Prices_Loader. Orchestrates the hooks of the plugin.
	 * - ECA_Bulk_Product_Prices_i18n. Defines internationalization functionality.
	 * - ECA_Bulk_Product_Prices_Admin. Defines all hooks for the admin area.
	 * - ECA_Bulk_Product_Prices_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-eca-bulk-product-prices-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-eca-bulk-product-prices-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-eca-bulk-product-prices-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-eca-bulk-product-prices-public.php';

		$this->loader = new ECA_Bulk_Product_Prices_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the ECA_Bulk_Product_Prices_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new ECA_Bulk_Product_Prices_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new ECA_Bulk_Product_Prices_Admin( $this->get_ECA_Bulk_Product_Prices(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new ECA_Bulk_Product_Prices_Public( $this->get_ECA_Bulk_Product_Prices(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
		$this->loader->add_action( 'woocommerce_locate_template', $plugin_public, 'woocommerce_locate_template', 10, 3 );
		$this->loader->add_action( 'woocommerce_single_product_summary', $plugin_public, 'remove_add_to_cart_button' );
		$this->loader->add_action( 'woocommerce_quantity_input_args', $plugin_public, 'product_attributes', 10, 2 );
		$this->loader->add_filter( 'woocommerce_get_price_html', $plugin_public, 'get_bulk_price', 40, 2 );
		$this->loader->add_action( 'woocommerce_before_calculate_totals', $plugin_public, 'set_cart_item_product_price', 40, 1 );
		$this->loader->add_action( 'woocommerce_single_product_summary', $plugin_public, 'add_remove_from_cart_button', 40 );
		$this->loader->add_filter( 'woocommerce_loop_add_to_cart_link', $plugin_public, 'remove_add_to_cart_button_from_loop', 11, 2 );
		$this->loader->add_action( 'woocommerce_single_product_summary', $plugin_public, 'show_table_quantity', 15 );
		$this->loader->add_action( 'wp', $plugin_public, 'change_woocommerce_single_product_summary_order' );
		//$this->loader->add_filter( 'woocommerce_cart_item_subtotal', $plugin_public, 'set_cart_product_subtotal', 40, 3 );
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_ECA_Bulk_Product_Prices() {
		return $this->ECA_Bulk_Product_Prices;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    ECA_Bulk_Product_Prices_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}
}
