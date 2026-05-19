<?php
/**
 * Compatibility instances class.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'LTY_Compatibility_Instances' ) ) {

	/**
	 * Class.
	 */
	class LTY_Compatibility_Instances {

		/**
		 * Compatibilities.
		 *
		 * @var array
		 * */
		private static $compatibilities;

		/**
		 * Get Compatibilities.
		 *
		 * @return array
		 */
		public static function instance() {
			if ( is_null( self::$compatibilities ) ) {
				self::$compatibilities = self::load_compatibilities();
			}

			return self::$compatibilities;
		}

		/**
		 * Load all compatibilities.
		 */
		public static function load_compatibilities() {
			if ( ! class_exists( 'LTY_Compatibility' ) ) {
				include LTY_PLUGIN_PATH . '/inc/abstracts/abstract-lty-compatibility.php';
			}

			$default_compatibility_classes = array(
				'wpml'                       => 'LTY_WPML_Compatibility',
				'woocommerce-stripe-gateway' => 'LTY_WooCommerce_Stripe_Gateway_Compatibility',
				'woocommerce-square-gateway' => 'LTY_WooCommerce_Square_Gateway_Compatibility',
				'woocommerce-payments'       => 'LTY_WooCommerce_Payments_Compatibility',
				'wallet'                     => 'LTY_Wallet_Compatibility',
				'woo-wallet'                 => 'LTY_Woo_Wallet_Compatibility',
				'woocommerce-store-credit'   => 'LTY_WooCommerce_Store_Credit_Compatibility',
				'tlg-framework'              => 'LTY_TLG_Framework_Compatibility',
				'woocommerce-smart-coupons'  => 'LTY_WooCommerce_Smart_Coupons_Compatibility',
				'pomana-theme'               => 'LTY_Pomana_Theme_Compatibility',
				'woocommerce-multi-currency' => 'LTY_WooCommerce_Multi_Currency_Compatibility',
			);

			foreach ( $default_compatibility_classes as $file_name => $compatibility_class ) {
				// Include file.
				include 'class-' . $file_name . '.php';

				// Add compatibility.
				self::add_compatibility( new $compatibility_class() );
			}
		}

		/**
		 * Add a Compatibility.
		 *
		 * @param object $compatibility Compatibility object.
		 * @return object
		 */
		public static function add_compatibility( $compatibility ) {
			self::$compatibilities[ $compatibility->get_id() ] = $compatibility;

			return new self();
		}

		/**
		 * Get compatibility by id.
		 *
		 * @param object $compatibility_id compatibility ID.
		 * @return object
		 */
		public static function get_compatibility_by_id( $compatibility_id ) {
			$compatibilities = self::instance();

			return isset( $compatibilities[ $compatibility_id ] ) ? $compatibilities[ $compatibility_id ] : false;
		}
	}

}
