<?php

/**
 * WooCommerce Square Gateway Compatibility.
 * */
if ( ! defined( 'ABSPATH' ) ) {
	exit ; // Exit if accessed directly.
}

if ( ! class_exists( 'LTY_WooCommerce_Square_Gateway_Compatibility' ) ) {

	/**
	 * Class.
	 * */
	class LTY_WooCommerce_Square_Gateway_Compatibility extends LTY_Compatibility {
			
			
		/**
		 * Class Constructor.
		 */
		public function __construct() {
					
			$this->id = 'woocommerce_square_gateway' ;

			parent::__construct() ;
		}

		/**
		 * Action
		 */
		public function actions() {
			// Square supported product types for digital wallets.
			add_filter( 'wc_square_digital_wallets_supported_product_types' , array( $this, 'supported_product_types' ) , 10 ) ;
		}

		/**
		 * Supported product types.
		 */
		public function supported_product_types( $product_types ) {
			return array_merge($product_types, array( 'lottery' ));
		}
	}

}
