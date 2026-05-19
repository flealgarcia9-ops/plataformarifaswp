<?php
/**
 * Compatibility - Wallet for WooCommerce Plugin.
 * Tested upto: 4.3.0
 *
 * @since 10.6.0
 * @link https://woocommerce.com/products/wallet-for-woocommerce/
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

if ( ! class_exists( 'LTY_Wallet_Compatibility' ) ) {

	/**
	 * Class.
	 *
	 * @since 10.6.0
	 * */
	class LTY_Wallet_Compatibility extends LTY_Compatibility {

		/**
		 * Class constructor.
		 *
		 * @since 10.6.0
		 */
		public function __construct() {
			$this->id = 'wallet';

			parent::__construct();
		}

		/**
		 * Is plugin enabled?.
		 *
		 * @since 10.6.0
		 * @return bool
		 * */
		public function is_plugin_enabled() {
			return class_exists( 'FP_Wallet' );
		}

		/**
		 * Admin actions.
		 *
		 * @since 10.6.0
		 */
		public function admin_action() {
			// Add wallet option in instant winner prize types.
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
			// Assign instant winner wallet prize.
			add_filter( 'lty_instant_winner_assign_wallet_prize', array( $this, 'assign_wallet_prize' ), 10, 2 );
			// Remove instant winner wallet prize.
			add_filter( 'lty_instant_winner_remove_won_wallet_prize', array( $this, 'remove_wallet_prize' ), 10, 2 );
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

			$prize_type_options['wallet'] = __( 'Wallet', 'lottery-for-woocommerce' );

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
			if ( 'wallet' !== $prize_type ) {
				return $prize_type_label;
			}

			return __( 'Wallet', 'lottery-for-woocommerce' );
		}

		/**
		 * Assign instant winner wallet prize.
		 *
		 * @since 10.6.0
		 * @param bool   $bool Whether prize assigned or not.
		 * @param object $instant_winner_log Instance of LTY_Instant_Winner_Log.
		 * @return bool
		 */
		public function assign_wallet_prize( $bool, $instant_winner_log ) {
			if ( ! is_object( $instant_winner_log ) ) {
				return $bool;
			}

			// Credit wallet amount.
			return wal_credit_wallet_fund(
				array(
					'user_id'       => $instant_winner_log->get_user_id(),
					'order_id'      => $instant_winner_log->get_order_id(),
					'amount'        => floatval( $instant_winner_log->get_prize_amount() ),
					'event_id'      => 'lottery',
					'event_message' => str_replace(
						array( '{prize_amount}', '{product_name}', '{order_number}' ),
						array( lty_price( $instant_winner_log->get_prize_amount() ), $instant_winner_log->get_product_name(), $instant_winner_log->get_order_number( false ) ),
						__( 'You have won giveaway Instant Win Prize({prize_amount}) on the {product_name}, Order #{order_number}', 'lottery-for-woocommerce' )
					),
				)
			);
		}

		/**
		 * Remove instant winner wallet prize.
		 *
		 * @since 10.6.0
		 * @param bool   $bool Whether prize Removed or not.
		 * @param object $instant_winner_log Instance of LTY_Instant_Winner_Log.
		 * @return bool
		 */
		public function remove_wallet_prize( $bool, $instant_winner_log ) {
			if ( ! is_object( $instant_winner_log ) ) {
				return $bool;
			}

			// Debit wallet amount.
			return wal_debit_wallet_fund(
				array(
					'user_id'       => $instant_winner_log->get_user_id(),
					'order_id'      => $instant_winner_log->get_order_id(),
					'amount'        => floatval( $instant_winner_log->get_prize_amount() ),
					'event_id'      => 'lottery',
					'event_message' => str_replace(
						array( '{prize_amount}', '{product_name}' ),
						array( lty_price( $instant_winner_log->get_prize_amount() ), $instant_winner_log->get_product_name() ),
						__( 'Instant Win Prize({prize_amount}) on the {product_name} giveaway has been failed', 'lottery-for-woocommerce' )
					),
				)
			);
		}
	}

}
