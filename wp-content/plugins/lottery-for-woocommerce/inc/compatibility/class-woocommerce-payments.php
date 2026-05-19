<?php
/**
 * WooCommerce Payments Compatibility.
 *
 * @since 9.7.0
 * */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

if ( ! class_exists( 'LTY_WooCommerce_Payments_Compatibility' ) ) {

	/**
	 * Class.
	 *
	 * @since 9.7.0
	 * */
	class LTY_WooCommerce_Payments_Compatibility extends LTY_Compatibility {

		/**
		 * Class constructor.
		 *
		 * @since 9.7.0
		 */
		public function __construct() {
			$this->id = 'woocommerce_payments';

			parent::__construct();
		}

		/**
		 * Is plugin enabled?.
		 *
		 * @since 9.7.0
		 * @return bool
		 * */
		public function is_plugin_enabled() {
			return class_exists( 'WC_Payments' );
		}

		/**
		 * Actions.
		 *
		 * @since 9.7.0
		 */
		public function actions() {
			// Allow lottery product type.
			add_filter( 'wcpay_payment_request_supported_types', array( $this, 'allow_lottery_product_type' ), 10, 1 );
		}

		/**
		 * Allow lottery product type.
		 *
		 * @since 9.7.0
		 * @param array $product_types Product types.
		 * @return array
		 */
		public function allow_lottery_product_type( $product_types ) {
			if ( ! lty_check_is_array( $product_types ) ) {
				return array( 'lottery' );
			}

			$product_types[] = 'lottery';

			return $product_types;
		}
	}

}
