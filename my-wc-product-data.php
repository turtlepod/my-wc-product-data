<?php
/**
 * Plugin Name: My WC Product Data
 * Plugin URI: https://shellcreeper.com/
 * Description: Just testing CRUD in WC 3.
 * Version: 1.0.0
 * Author: turtlepod
 * Author URI: https://shellcreeper.com
 * Requires at least: 4.8.0
 * Tested up to: 4.8
 **/

define( 'MWPD_PATH', trailingslashit( plugin_dir_path( __FILE__ ) ) );
define( 'MWPD_URL', trailingslashit( plugin_dir_url( __FILE__ ) ) );

add_action( 'plugins_loaded', function() {

	if ( ! class_exists( 'WooCommerce' ) ) {
		return;
	}

	require_once( MWPD_PATH . 'inc/test-data.php' );
	require_once( MWPD_PATH . 'inc/product.php' );
} );

/**
 * Override the product type lookup.
 *
 * If we can find a row in our custom table with the same product then we can adjust the type.
 *
 * @since 1.0.0
 *
 * @param bool
 * @param int $product_id
 * @return string $product_type
 */
function custom_woocommerce_product_type_query( $override, $product_id ) {
	// Do a proper check to see if we are really a custom type.
	return 1 === 1 ? 'custom' : $override;
}
add_filter( 'woocommerce_product_type_query', 'custom_woocommerce_product_type_query', 10, 2 );

/**
 * When using the WC_Product_Factory ensure our custom product class
 * is used if applicable. 
 *
 * @since 1.0.0
 *
 * @param string $classname
 * @param string $product_type
 * @param string $post_type
 * @param int $product_id
 * @return string $classname
 */
function custom_woocommerce_product_class( $classname, $product_type, $post_type, $product_id ) {
	if ( 'custom' == $product_type ) {
		return 'My_Product_Custom';
	}

	return $classname;
}
add_filter( 'woocommerce_product_class', 'custom_woocommerce_product_class', 10, 4 );
