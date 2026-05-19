<?php
/**
 * Compatibility - WooCommerce Smart Coupons Plugin.
 * Tested upto: 9.23.0
 *
 * @since 11.0.0
 * @link https://woocommerce.com/products/smart-coupons/
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

if ( ! class_exists( 'LTY_WooCommerce_Smart_Coupons_Compatibility' ) ) {

	/**
	 * Class.
	 *
	 * @since 11.0.0
	 * */
	class LTY_WooCommerce_Smart_Coupons_Compatibility extends LTY_Compatibility {

		/**
		 * Class constructor.
		 *
		 * @since 11.0.0
		 */
		public function __construct() {
			$this->id = 'wc_smart_coupons';

			parent::__construct();
		}

		/**
		 * Is plugin enabled?.
		 *
		 * @since 11.0.0
		 * @return bool
		 * */
		public function is_plugin_enabled() {
			return class_exists( 'WC_Smart_Coupons' );
		}

		/**
		 * Admin actions.
		 *
		 * @since 11.0.0
		 */
		public function admin_action() {
			// Add smart coupon option in instant winner prize types.
			add_filter( 'lty_instant_winner_prize_type_options', array( $this, 'alter_instant_winner_prize_types' ), 10, 1 );
			// Alter instant winner prize type label.
			add_filter( 'lty_instant_winner_prize_type_label', array( $this, 'alter_instant_winner_prize_type_label' ), 10, 2 );
		}

		/**
		 * Actions.
		 *
		 * @since 11.0.0
		 */
		public function actions() {
			// Assign instant winner smart coupon prize.
			add_filter( 'lty_instant_winner_assign_smart_coupon_prize', array( $this, 'assign_smart_coupon_prize' ), 10, 2 );
			// Remove instant winner smart coupon prize.
			add_filter( 'lty_instant_winner_remove_won_smart_coupon_prize', array( $this, 'remove_smart_coupon_prize' ), 10, 2 );
		}

		/**
		 * Alter the instant winner prize types.
		 *
		 * @since 11.0.0
		 * @param array $prize_type_options Instant winner prize types.
		 * @return array
		 */
		public function alter_instant_winner_prize_types( $prize_type_options ) {
			if ( ! lty_check_is_array( $prize_type_options ) ) {
				$prize_type_options = array();
			}

			$prize_type_options['smart_coupon'] = __( 'Store Credit(Smart Coupon)', 'lottery-for-woocommerce' );

			return $prize_type_options;
		}

		/**
		 * Alter instant winner prize type label.
		 *
		 * @since 11.0.0
		 * @param string $prize_type_label Prize type label.
		 * @param string $prize_type Prize type.
		 * @return string
		 */
		public function alter_instant_winner_prize_type_label( $prize_type_label, $prize_type ) {
			if ( 'smart_coupon' !== $prize_type ) {
				return $prize_type_label;
			}

			return __( 'Store Credit(Smart Coupon)', 'lottery-for-woocommerce' );
		}

		/**
		 * Assign instant winner smart coupon prize.
		 *
		 * @since 11.0.0
		 * @param bool   $bool Whether prize assigned or not.
		 * @param object $instant_winner_log Instance of LTY_Instant_Winner_Log.
		 * @return bool
		 */
		public function assign_smart_coupon_prize( $bool, $instant_winner_log ) {
			if ( ! is_object( $instant_winner_log ) ) {
				return $bool;
			}

			global $smart_coupon_codes;

			if ( lty_check_is_array( $smart_coupon_codes ) && isset( $smart_coupon_codes[ $instant_winner_log->get_user_email() ] ) ) {
				unset( $smart_coupon_codes[ $instant_winner_log->get_user_email() ] );
			}

			$coupons_data = WC_Smart_Coupons::get_instance()->generate_smart_coupon( $instant_winner_log->get_user_email(), floatval( $instant_winner_log->get_prize_amount() ), '', '', 'smart_coupon', $instant_winner_log->get_user_email(), $this->get_prize_message( $instant_winner_log ) );
			$coupon_code  = $this->get_generated_coupon_code( $coupons_data );
			if ( ! $coupon_code ) {
				return $bool;
			}

			$coupon = new WC_Coupon( $coupon_code );
			if ( ! is_object( $coupon ) ) {
				return $bool;
			}

			// Update the smart coupon metadata.
			WC_SC_Coupon_Fields::get_instance()->woocommerce_process_smart_coupon_meta( $coupon->get_id(), $coupon );
			// Update the lottery meta meta data.
			$instant_winner_log->update_meta( 'lty_coupon_code', $coupon->get_code() );
			$meta_data = array(
				'lty_coupon'            => 'yes',
				'lty_user_email'        => $instant_winner_log->get_user_email(),
				'lty_instant_winner_id' => $instant_winner_log->get_id(),
			);

			foreach ( $meta_data as $meta_key => $meta_value ) {
				update_post_meta( $coupon->get_id(), $meta_key, $meta_value );
			}

			return true;
		}

		/**
		 * Remove instant winner smart coupon prize.
		 *
		 * @since 11.0.0
		 * @param bool   $bool Whether prize Removed or not.
		 * @param object $instant_winner_log Instance of LTY_Instant_Winner_Log.
		 * @return bool
		 */
		public function remove_smart_coupon_prize( $bool, $instant_winner_log ) {
			if ( ! is_object( $instant_winner_log ) ) {
				return $bool;
			}

			$coupon = new WC_Coupon( $instant_winner_log->get_coupon_code() );
			if ( ! is_object( $coupon ) ) {
				return $bool;
			}

			if ( class_exists( 'WC_SC_Coupon_Data_Store' ) ) {
				// Remove coupon data from the smart coupon custom table.
				WC_SC_Coupon_Data_Store::get_instance()->remove_coupon_from_custom_table( $coupon->get_id() );
			}

			return LTY_Instant_Winner_Coupon_Handler::delete_coupon( $instant_winner_log->get_id() );
		}

		/**
		 * Get the instant winner prize message.
		 *
		 * @since 11.0.0
		 * @param object $instant_winner_log Instance of LTY_Instant_Winner_Log.
		 * @return string
		 */
		private function get_prize_message( $instant_winner_log ) {
			return str_replace(
				array( '{prize_amount}', '{product_name}', '{order_number}' ),
				array( lty_price( $instant_winner_log->get_prize_amount() ), $instant_winner_log->get_product_name(), $instant_winner_log->get_order_number( false ) ),
				__( 'You have won giveaway Instant Win Prize({prize_amount}) on the {product_name}, Order #{order_number}', 'lottery-for-woocommerce' )
			);
		}

		/**
		 * Get the generated coupon code.
		 *
		 * @since 11.0.0
		 * @param array $coupons_data Generated coupon data.
		 * @return string|bool
		 */
		private function get_generated_coupon_code( $coupons_data ) {
			if ( ! lty_check_is_array( $coupons_data ) ) {
				return false;
			}

			foreach ( $coupons_data as $email_id => $user_coupons_data ) {
				if ( ! lty_check_is_array( $user_coupons_data ) ) {
					continue;
				}

				// Get the first available coupon data.
				$coupon_data = reset( $user_coupons_data );

				return lty_check_is_array( $coupon_data ) && isset( $coupon_data['code'] ) ? $coupon_data['code'] : false;
			}

			return false;
		}
	}
}
