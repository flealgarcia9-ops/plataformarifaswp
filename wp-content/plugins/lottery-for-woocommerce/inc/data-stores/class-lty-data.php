<?php
/**
 * Handles the data.
 *
 * @since 8.7.0
 * */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

if ( ! class_exists( 'LTY_WC_Data' ) ) {

	/**
	 * Class.
	 *
	 * @since 8.7.0
	 * */
	class LTY_WC_Data {

		/**
		 * Class initialization.
		 *
		 * @since 8.7.0
		 * */
		public static function init() {
			// Include the data stores.
			self::include_data_stores();

			// Register the Product Lottery data store.
			add_filter( 'woocommerce_data_stores', array( __CLASS__, 'register_data_store' ), 10, 1 );
		}

		/**
		 * Include the data stores.
		 *
		 * @since 8.7.0
		 * @return void
		 * */
		public static function include_data_stores() {
			include_once LTY_PLUGIN_PATH . '/inc/data-stores/class-wc-product-lottery-data-store-cpt.php';
		}

		/**
		 * Register the data stores.
		 *
		 * @since 8.7.0
		 * @param array $data_stores
		 * @return array
		 * */
		public static function register_data_store( $data_stores ) {
			$data_stores['product-lottery'] = 'WC_Product_Lottery_Data_Store_CPT';

			return $data_stores;
		}
	}

	LTY_WC_Data::init();
}
