<?php
/**
 * Compatibility - CURCY - WooCommerce Multi Currency - Currency Switcher.
 *
 * Tested upto: 2.3.11
 *
 * @since 12.0.0
 * @link https://codecanyon.net/item/woocommerce-multi-currency/20948446
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

if ( ! class_exists( 'LTY_WooCommerce_Multi_Currency_Compatibility' ) ) {

	/**
	 * Class.
	 *
	 * @since 12.0.0
	 */
	class LTY_WooCommerce_Multi_Currency_Compatibility extends LTY_Compatibility {

		/**
		 * Class Constructor.
		 *
		 * @since 12.0.0
		 */
		public function __construct() {
			$this->id = 'woocommerce_multi_currency';

			parent::__construct();
		}

		/**
		 * Is plugin enabled?.
		 *
		 * @since 12.0.0
		 * @return bool
		 * */
		public function is_plugin_enabled() {
			return function_exists( 'wmc_get_price' );
		}

		/**
		 * Frontend actions.
		 *
		 * @since 12.0.0
		 */
		public function frontend_action() {
			add_filter( 'lty_predefined_button_product_price', array( $this, 'convert_price_to_default_currency' ), 10, 1 );
		}

		/**
		 * Convert the price based on default currency.
		 *
		 * @since 12.0.0
		 * @param int|float $price Price.
		 * @return int|float
		 */
		public static function convert_price_to_default_currency( $price ) {
			return self::convert_price( $price, true );
		}

		/**
		 * Convert the price based on current currency.
		 *
		 * @since 12.0.0
		 * @param float $price Price.
		 * @param bool  $convert_to_default_currency Whether convert to default currency or not.
		 * @return float
		 */
		public static function convert_price( $price, $convert_to_default_currency = false ) {
			if ( ! $price ) {
				return $price;
			}

			if ( $convert_to_default_currency ) {
				return wmc_revert_price( $price );
			}

			return wmc_get_price( $price );
		}
	}

}
