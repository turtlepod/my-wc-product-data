<?php
/**
 * Plugin Name: My WC Product (TYPE)
 * Plugin URI: https://shellcreeper.com/
 * Description: Just testing WC 3.
 * Version: 1.0.0
 * Author: turtlepod
 * Author URI: https://shellcreeper.com
 * Requires at least: 4.8.0
 * Tested up to: 4.8
 **/

define( 'MWP_PATH', trailingslashit( plugin_dir_path( __FILE__ ) ) );
define( 'MWP_URL', trailingslashit( plugin_dir_url( __FILE__ ) ) );

add_action( 'plugins_loaded', function() {
	if ( ! class_exists( 'WooCommerce' ) ) {
		return;
	}

	/**
	 * TYPE SELECTOR IN PRODUCT META BOX
	 */
	add_filter( 'product_type_selector', function( $types ) {
		$types['ticket'] = 'Ticket';
		return $types;
	} );

	/**
	 * GENERAL TAB ~ PRODUCT DATA META BOX
	 */
	add_action( 'woocommerce_product_options_general_product_data', function() {
		$product = wc_get_product();
		?>
		<div class="options_group show_if_ticket">
			<?php woocommerce_wp_text_input( array(
				'id'     => '_address',
				'label'  => 'Address',
				'value'  => method_exists( $product, 'get_address' ) ? strip_tags( $product->get_address() ) : '',
				'type'   => 'text',
			) ); ?>
		</div>
		<?php
	} );

	/**
	 * SAVE ADDRESS FIELD
	 */
	add_action( 'woocommerce_admin_process_product_object', function( $product ) {
		$product->set_props( array(
			'address' => strip_tags( $_POST['_address'] ),
		) );
	} );

	/**
	 * WHICH CLASS TO LOAD FOR A PRODUCT TYPE
	 */
	add_filter( 'woocommerce_product_class', function( $classname, $product_type, $post_type, $product_id ) {
		if ( 'ticket' === $product_type ) {
			return 'My_Ticket';
		}
		return $classname;
	}, 10, 4 );

	/**
	 * PRODUCT CLASS: address getter and setter.
	 */
	class My_Ticket extends WC_Product_Simple {
		protected $extra_data = array(
			'address' => '',
		);
		public function __construct( $product = 0 ) {
			parent::__construct( $product );
		}
		// Set type. required.
		public function get_type() {
			return 'ticket';
		}
		// Setter.
		public function set_address( $address ) {
			$a = $this->set_prop( 'address', strip_tags( $address ) );
		}
		// Getter.
		public function get_address() {
			$a = $this->get_prop( 'address' );
			return $a;
		}
	}

	/**
	 * WHICH DATA STORES CLASS LOAD FOR TICKET PRODUCT TYPE ?
	 */
	add_filter( 'woocommerce_data_stores', function( $stores ) {
		$stores['product-ticket'] = 'My_Ticket_Data_Store';
		return $stores;
	} );

	/**
	 * DATA STORES CLASS : insert and update to custom DB.
	 */
	class My_Ticket_Data_Store extends WC_Product_Data_Store_CPT {
		/**
		 * Read.
		 */
		protected function read_extra_data( &$product ) {
			parent::read_extra_data( $product );

			global $wpdb;
			$id = $product->get_id();
			$row = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}my_tickets WHERE product_id = %d LIMIT 1", $id ), 'ARRAY_A' );

			$product->set_address( isset( $row['address'] ) ? $row['address'] : '' );
		}

		/**
		 * Update.
		 */
		public function update( &$product ) {
			parent::update( $product );

			// Get db.
			global $wpdb;
			$id = $product->get_id();
			$row = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}my_tickets WHERE product_id = %d LIMIT 1", $id ), 'ARRAY_A' );

			// Already exists?
			if ( $row ) {
				$wpdb->update( "{$wpdb->prefix}my_tickets", array( 'address' => strip_tags( $product->get_address() ), ), array( 'product_id' => $id ) );
			} else { // Not yet exists for this product.
				$wpdb->insert( "{$wpdb->prefix}my_tickets", array( 'address' => strip_tags( $product->get_address() ), 'product_id' => $id, ) );
			}

			// Clear cache.
			$this->clear_caches( $product );
		}

		/**
		 * Create.
		 */
		public function create( &$product ) {
			parent::create( $product );

			// Get db.
			global $wpdb;
			$id = $product->get_id();
			$row = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}my_tickets WHERE product_id = %d LIMIT 1", $id ), 'ARRAY_A' );

			// Already exists, update (maybe the update is not needed here)
			if ( $row ) {
				$wpdb->update( "{$wpdb->prefix}my_tickets", array( 'address' => strip_tags( $product->get_address() ), ), array( 'product_id' => $id ) );
			} else { // not yet exists.
				$wpdb->insert( "{$wpdb->prefix}my_tickets", array( 'address' => strip_tags( $product->get_address() ), 'product_id' => $id, ) );
			}

			// Clear cache.
			$this->clear_caches( $product );
		}

		/**
		 * Delete
		 */
		public function delete( &$product, $args = array() ) {
			parent::delete( $product, $args );

			$args = wp_parse_args( $args, array(
				'force_delete' => false,
			) );
			if ( $args['force_delete'] ) {
				global $wpdb;
				$id = $product->get_id();
				$wpdb->delete( "{$wpdb->prefix}my_tickets", array( 'product_id' => $id ) );
			}
		}

	} // End data store class.

	// Delete product on delete post.
	add_action( 'deleted_post', function( $id ) {
		if ( 'product' === get_post_type( $id ) ) {
			global $wpdb;
			$row = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}my_tickets WHERE product_id = %d LIMIT 1", $id ), 'ARRAY_A' );
			if ( $row ) {
				$wpdb->delete( "{$wpdb->prefix}my_tickets", array( 'product_id' => $id ) );
			}
		}
	} );

} );


/**
 * Install. Create Custom DB.
 */
register_activation_hook( __FILE__, function() {
	global $wpdb;
	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	$charset_collate = $wpdb->get_charset_collate();
	$sql = "
		CREATE TABLE {$wpdb->prefix}my_tickets (
			id int(11) unsigned NOT NULL AUTO_INCREMENT,
			product_id int(11) NOT NULL,
			address varchar(255) NOT NULL DEFAULT '',
			PRIMARY KEY (id)
		) $charset_collate;
	";
	dbDelta( $sql );
} );

