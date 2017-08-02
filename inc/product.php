<?php

class My_Product_Custom extends WC_Product_Simple {

	protected $extra_data = array(
		'my_test_data' => null,
	);

	public function get_type() {
		return 'custom';
	}

	public function __construct( $product = 0 ) {
		// Merge in our custom data so the getters and setters can be validated.
		$this->data = array_merge( $this->data, $this->extra_data );

		parent::__construct( $product );
	}

	public function get_my_test_data( $context = 'view' ) {
		return $this->get_prop( 'my_test_data', $context );
	}

	public function set_my_test_data( $value ) {
		$this->set_prop( 'my_test_data', $value );
	}

}
