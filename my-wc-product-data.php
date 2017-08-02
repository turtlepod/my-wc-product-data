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
} );













