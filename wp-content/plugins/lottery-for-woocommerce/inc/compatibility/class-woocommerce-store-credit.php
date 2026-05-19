<?php
/**
 * Compatibility - WooCommerce Store Credit Plugin.
 * Tested upto: 4.5.4
 *
 * @since 10.6.0
 * @link https://woocommerce.com/products/store-credit/
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

if ( ! class_exists( 'LTY_WooCommerce_Store_Credit_Compatibility' ) ) {

	/**
	 * Class.
	 *
	 * @since 10.6.0
	 * */
	class LTY_WooCommerce_Store_Credit_Compatibility extends LTY_Compatibility {

		/**
		 * Class constructor.
		 *
		 * @since 10.6.0
		 */
		public function __construct() {
			$this->id = 'store_credit';

			parent::__construct();
		}

		/**
		 * Is plugin enabled?.
		 *
		 * @since 10.6.0
		 * @return bool
		 * */
		public function is_plugin_enabled() {
			return class_exists( 'WC_Store_Credit' );
		}

		/**
		 * Admin actions.
		 *
		 * @since 10.6.0
		 */
		public function admin_action() {
			// Add store credit option in instant winner prize types.
			add_filter( 'lty_instant_winner_prize_type_options', array( $this, 'alter_instant_winner_prize_types' ), 10, 1 );
			// Alter instant winner prize type label.
			add_filter( 'lty_instant_winner_prize_type_label', array( $this, 'alter_instant_winner_prize_type_label' ), 10, 2 );
		}

		/**
		 * Actions.
		 *
		 * @since 10.6.0
		 */
		public function actions() {
			// Assign instant winner store credit prize.
			add_filter( 'lty_instant_winner_assign_credit_prize', array( $this, 'assign_store_credit_prize' ), 10, 2 );
			// Remove instant winner store credit prize.
			add_filter( 'lty_instant_winner_remove_won_credit_prize', array( $this, 'remove_store_credit_prize' ), 10, 2 );
		}

		/**
		 * Alter the instant winner prize types.
		 *
		 * @since 10.6.0
		 * @param array $prize_type_options Instant winner prize types.
		 * @return array
		 */
		public function alter_instant_winner_prize_types( $prize_type_options ) {
			if ( ! lty_check_is_array( $prize_type_options ) ) {
				$prize_type_options = array();
			}

			$prize_type_options['credit'] = __( 'Store Credit', 'lottery-for-woocommerce' );

			return $prize_type_options;
		}

		/**
		 * Alter instant winner prize type label.
		 *
		 * @since 10.6.0
		 * @param string $prize_type_label Prize type label.
		 * @param string $prize_type Prize type.
		 * @return string
		 */
		public function alter_instant_winner_prize_type_label( $prize_type_label, $prize_type ) {
			if ( 'credit' !== $prize_type ) {
				return $prize_type_label;
			}

			return __( 'Store Credit', 'lottery-for-woocommerce' );
		}

		/**
		 * Assign instant winner store credit prize.
		 *
		 * @since 10.6.0
		 * @param bool   $bool Whether prize assigned or not.
		 * @param object $instant_winner_log Instance of LTY_Instant_Winner_Log.
		 * @return bool
		 */
		public function assign_store_credit_prize( $bool, $instant_winner_log ) {
			if ( ! is_object( $instant_winner_log ) ) {
				return $bool;
			}

			$args = array(
				'usage_limit' => 1,
				'expiration'  => 'never',
				'description' => str_replace(
					array( '{prize_amount}', '{product_name}', '{order_number}' ),
					array( lty_price( $instant_winner_log->get_prize_amount() ), $instant_winner_log->get_product_name(), $instant_winner_log->get_order_number( false ) ),
					__( 'You have won giveaway Instant Win Prize({prize_amount}) on the {product_name}, Order #{order_number}', 'lottery-for-woocommerce' )
				),
			);

			$coupon = wc_store_credit_send_credit_to_customer( $instant_winner_log->get_user_email(), floatval( $instant_winner_log->get_prize_amount() ), $args );
			if ( is_object( $coupon ) ) {
				$instant_winner_log->update_meta( 'lty_coupon_code', $coupon->get_code() );
				$meta_data = array(
					'lty_coupon'            => 'yes',
					'lty_user_email'        => $instant_winner_log->get_user_email(),
					'lty_user_id'           => $instant_winner_log->get_user_id(),
					'lty_instant_winner_id' => $instant_winner_log->get_id(),
				);

				foreach ( $meta_data as $meta_key => $meta_value ) {
					update_post_meta( $coupon->get_id(), $meta_key, $meta_value );
				}

				return true;
			}

			return false;
		}

		/**
		 * Remove instant winner store credit prize.
		 *
		 * @since 10.6.0
		 * @param bool   $bool Whether prize Removed or not.
		 * @param object $instant_winner_log Instance of LTY_Instant_Winner_Log.
		 * @return bool
		 */
		public function remove_store_credit_prize( $bool, $instant_winner_log ) {
			if ( ! is_object( $instant_winner_log ) ) {
				return $bool;
			}

			return LTY_Instant_Winner_Coupon_Handler::delete_coupon( $instant_winner_log->get_id() );
		}
	}
}
