<?php

/**
 * WooCommerce Stripe Gateway Compatibility.
 * */
if ( ! defined( 'ABSPATH' ) ) {
	exit ; // Exit if accessed directly.
}

if ( ! class_exists( 'LTY_WooCommerce_Stripe_Gateway_Compatibility' ) ) {

	/**
	 * Class.
	 * */
	class LTY_WooCommerce_Stripe_Gateway_Compatibility extends LTY_Compatibility {
			
			
		/**
		 * Class Constructor.
		 */
		public function __construct() {
					
			$this->id = 'woocommerce_stripe_gateway' ;

			parent::__construct() ;
		}

		/**
		 * Action
		 */
		public function actions() {
			// Stripe supported product types.
			add_filter( 'wc_stripe_payment_request_supported_types' , array( $this, 'supported_product_types' ) , 10 ) ;
		}

		/**
		 * Supported product types.
		 */
		public function supported_product_types( $product_types ) {
			return array_merge($product_types, array( 'lottery' ));
		}
	}

}
