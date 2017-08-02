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
  // $location = query_custom_table_for_location_data_row( $product_id );

  return 'custom';
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
function custom_woocommerce_product_class( $classname, $product_type ) {
  // Would need to do a few extra checks here for saving data in the admin while maintaining
  // the simple product type.
  //
  // if ( 'custom' == $product_type ) {
  //   return 'My_Product_Custom';
  // }

  return 'My_Product_Custom';

  return $classname;
}
add_filter( 'woocommerce_product_class', 'custom_woocommerce_product_class', 10, 2 );
