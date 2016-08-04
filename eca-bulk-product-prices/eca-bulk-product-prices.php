<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://example.com
 * @since             1.0.0
 * @package           ECA_Bulk_Product_Prices
 *
 * @wordpress-plugin
 * Plugin Name:       ECA Bulk Product Prices
 * Plugin URI:        http://eversionsystems.com/eca-bulk-product-prices-uri/
 * Description:       Sell product SKU COEBRCE with custom bulk prices.
 * Version:           1.0.1
 * Author:            Eversion Systems
 * Author URI:        http://eversionsystems.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       eca-bulk-product-prices
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-eca-bulk-product-prices-activator.php
 */
function activate_ECA_Bulk_Product_Prices() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-eca-bulk-product-prices-activator.php';
	ECA_Bulk_Product_Prices_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-eca-bulk-product-prices-deactivator.php
 */
function deactivate_ECA_Bulk_Product_Prices() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-eca-bulk-product-prices-deactivator.php';
	ECA_Bulk_Product_Prices_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_ECA_Bulk_Product_Prices' );
register_deactivation_hook( __FILE__, 'deactivate_ECA_Bulk_Product_Prices' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-eca-bulk-product-prices.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_ECA_Bulk_Product_Prices() {

	$plugin = new ECA_Bulk_Product_Prices();
	$plugin->run();

}

run_ECA_Bulk_Product_Prices();

if (!function_exists('write_log')) {
    function write_log ( $log )  {
        if ( true === WP_DEBUG ) {
            if ( is_array( $log ) || is_object( $log ) ) {
                error_log( print_r( $log, true ) );
            } else {
                error_log( $log );
            }
        }
    }
}
