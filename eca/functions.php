<?php
/**
 * ECA functions and definitions
 *
 * Set up the theme and provides some helper functions, which are used in the
 * theme as custom template tags. Others are attached to action and filter
 * hooks in WordPress to change core functionality.
 *
 * When using a child theme you can override certain functions (those wrapped
 * in a function_exists() call) by defining them first in your child theme's
 * functions.php file. The child theme's functions.php file is included before
 * the parent theme's file, so the child theme functions would be used.
 *
 * @link http://codex.wordpress.org/Theme_Development
 * @link http://codex.wordpress.org/Child_Themes
 *
 * Functions that are not pluggable (not wrapped in function_exists()) are
 * instead attached to a filter or action hook.
 *
 * For more information on hooks, actions, and filters,
 * @link http://codex.wordpress.org/Plugin_API
 *
 * @package ECA
 * @subpackage ECA Theme
 * @since ECA 1.0
 */


/**
 * ECA theme setup.
 *
 * Sets up theme defaults and registers the various WordPress features that
 * ECA supports.
 *
 * @uses add_theme_support() To add support for woocommerce
 *
 * @since ECA 1.0
 *
 * @return void
 */
function eca_setup() {
	// WooCommerce theme support.
	add_theme_support( 'woocommerce' );
	
	// This theme uses wp_nav_menu() in one location.
	register_nav_menus( array(
		'primary'   => __( 'Top primary menu', 'eca' ),
	) );
}
add_action( 'after_setup_theme', 'eca_setup' );



/**
 * Enqueue scripts and styles for the front end.
 *
 * @since ECA 1.0
 *
 * @return void
 */
function eca_scripts() {
	// Load our main stylesheet.
	wp_enqueue_style( 'eca-jquery-ui', get_template_directory_uri() . '/jquery-ui.css', array() );
	wp_enqueue_style( 'eca-style', get_stylesheet_uri(), array() );
	
	// Load our main scripts
	wp_enqueue_script( 'jquery' );
	wp_enqueue_script( 'eca-jquery-ui', 'http://code.jquery.com/ui/1.10.3/jquery-ui.js', array() );
}
add_action( 'wp_enqueue_scripts', 'eca_scripts' );


/**
 * Extend the default WordPress body classes.
 *
 * Adds body classes to denote:
 * 1. Template layouts.
 * 2. Presence of footer widgets.
 * 3. Single views.
 *
 * @since ECA 1.0
 *
 * @param array $classes A list of existing body class values.
 * @return array The filtered body class list.
 */
function eca_body_classes( $classes ) {
	return $classes;
}
add_filter( 'body_class', 'eca_body_classes' );


/**
 * Create a nicely formatted and more specific title element text for output
 * in head of document, based on current view.
 *
 * @since ECA 1.0
 *
 * @param string $title Default title text for current view.
 * @param string $sep Optional separator.
 * @return string The filtered title.
 */
function eca_wp_title( $title, $sep ) {
	global $paged, $page;

	if ( is_feed() ) {
		return $title;
	}

	// Add the site name.
	$title .= get_bloginfo( 'name' );

	// Add the site description for the home/front page.
	$site_description = get_bloginfo( 'description', 'display' );
	if ( $site_description && ( is_home() || is_front_page() ) ) {
		$title = "$title $sep $site_description";
	}

	// Add a page number if necessary.
	if ( $paged >= 2 || $page >= 2 ) {
		$title = "$title $sep " . sprintf( __( 'Page %s', 'eca' ), max( $paged, $page ) );
	}

	return $title;
}
add_filter( 'wp_title', 'eca_wp_title', 10, 2 );


/**
 * Add the page slug for the menu into the classes array.
 * 
 * @since ECA 1.0
 * 
 * @param array $classes
 * @param object $item
 * @return array The update array.
 */
function eca_add_slug_nav_class( $classes, $item ) {
	if ( 'page' == $item->object ) {
		$page = get_post( $item->object_id );
		$classes[] = $page->post_name;
	}
	return $classes;
}
add_filter( 'nav_menu_css_class', 'eca_add_slug_nav_class', 10, 2 );

/**
 * Add 4 footer widget areas
 */
function eca_widgets_init() {
 
    // First footer widget area, located in the footer. Empty by default.
    register_sidebar( array(
        'name' => __( 'First Footer Widget Area', 'eca' ),
        'id' => 'first-footer-widget-area',
        'description' => __( 'The first footer widget area', 'eca' ),
        'before_widget' => '<div id="%1$s" class="widget-container %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h3 class="widget-title">',
        'after_title' => '</h3>',
    ) );
 
    // Second Footer Widget Area, located in the footer. Empty by default.
    register_sidebar( array(
        'name' => __( 'Second Footer Widget Area', 'eca' ),
        'id' => 'second-footer-widget-area',
        'description' => __( 'The second footer widget area', 'eca' ),
        'before_widget' => '<div id="%1$s" class="widget-container %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h3 class="widget-title">',
        'after_title' => '</h3>',
    ) );
 
    // Third Footer Widget Area, located in the footer. Empty by default.
    register_sidebar( array(
        'name' => __( 'Third Footer Widget Area', 'eca' ),
        'id' => 'third-footer-widget-area',
        'description' => __( 'The third footer widget area', 'eca' ),
        'before_widget' => '<div id="%1$s" class="widget-container %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h3 class="widget-title">',
        'after_title' => '</h3>',
    ) );
 
    // Fourth Footer Widget Area, located in the footer. Empty by default.
    register_sidebar( array(
        'name' => __( 'Fourth Footer Widget Area', 'eca' ),
        'id' => 'fourth-footer-widget-area',
        'description' => __( 'The fourth footer widget area', 'eca' ),
        'before_widget' => '<div id="%1$s" class="widget-container %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h3 class="widget-title">',
        'after_title' => '</h3>',
    ) );
         
}
 
// Register sidebars by running eca_widgets_init() on the widgets_init hook.
add_action( 'widgets_init', 'eca_widgets_init' );

/**
 * Disable default footer area
 */
function eca_remove_old_widget_area(){

	// Unregister some of the TwentyTen sidebars
	unregister_sidebar( 'sidebar-3' );
}

add_action( 'widgets_init', 'eca_remove_old_widget_area', 11 );

/**
 * Ensure cart contents update when products are added to the cart via AJAX
 * https://docs.woocommerce.com/document/show-cart-contents-total/
 */
add_filter( 'woocommerce_add_to_cart_fragments', 'woocommerce_header_add_to_cart_fragment' );

function woocommerce_header_add_to_cart_fragment( $fragments ) {
	ob_start();

	?>
	<a class="cart-contents" id="button-cart" href="<?php echo WC()->cart->get_cart_url(); ?>" title="<?php _e( 'View your shopping cart' ); ?>"><?php echo sprintf (_n( '%d item', '%d items', WC()->cart->get_cart_contents_count() ), WC()->cart->get_cart_contents_count() ); ?> - <?php echo WC()->cart->get_cart_total(); ?></a> 
	<?php
	
	$fragments['a.cart-contents'] = ob_get_clean();
	
	return $fragments;
}