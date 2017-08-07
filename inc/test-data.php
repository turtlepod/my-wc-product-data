<?php
// Load Class.
MyWC_PData::get_instance();

/**
 * Test.
 *
 * @since 1.0.0
 */
class MyWC_PData {

	/**
	 * Instance.
	 */
	public static function get_instance() {
		static $instance = null;
		if ( is_null( $instance ) ) $instance = new self;
		return $instance;
	}

	/**
	 * Construct
	 */
	public function __construct() {

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
		add_filter( 'woocommerce_product_type_query', function( $override, $product_id ) {
			// Do a proper check to see if we are really a custom type.
			return self::is_custom_product() ? 'custom' : $override;
		}, 10, 2 );

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
		add_filter( 'woocommerce_product_class', function( $classname, $product_type, $post_type, $product_id ) {
			if ( 'custom' == $product_type || self::is_custom_product() ) {
				return 'My_Product_Custom';
			}
			//wp_die( print_r( $product_type ) );
			return $classname;
		}, 10, 4 );

		// Add tabs.
		add_filter( 'woocommerce_product_data_tabs', function( $tabs ) {
			$tabs['test'] = array(
				'label'    => 'TEST',
				'target'   => 'my_test_data',
				'class'    => array(),
				'priority' => 1,
			);
			return $tabs;
		} );

		// Add panels.
		add_action( 'woocommerce_product_data_panels', function() {
			global $product_object;
			?>
			<div id="my_test_data" class="panel woocommerce_options_panel">
				<?php //print_r( $product_object->get_type() ); ?>
				<?php woocommerce_wp_text_input( array(
					'id'                => '_my_test_data',
					'label'             => 'Test Input',
					'description'       => 'Lorem Ipsum',
					'value'             => $product_object->get_my_test_data( 'edit' ),
					'placeholder'       => '',
					'type'              => 'text',
				) ); ?>

			</div>
			<?php
		} );

		// Save.
		add_action( 'woocommerce_admin_process_product_object', function( $product ) {
			if ( isset( $_POST['_my_test_data'] ) ) {
				$product->set_props( array(
					'my_test_data' => $_POST['_my_test_data'],
				) );
			}
		}, 10, 2 );
	}

	public static function is_custom_product() {
		// We would query against our custom table for data.
		return 1 === 1;
	}

}
