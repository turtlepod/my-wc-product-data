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
				<?php print_r( $product_object->get_type() ); ?>
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

}
