<?php
/**
 * Compatibility - Woo Wallet Plugin.
 * Tested upto: 1.5.7
 *
 * @since 10.6.0
 * @link https://standalonetech.com/
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

if ( ! class_exists( 'LTY_Woo_Wallet_Compatibility' ) ) {

	/**
	 * Class.
	 *
	 * @since 10.6.0
	 * */
	class LTY_Woo_Wallet_Compatibility extends LTY_Compatibility {

		/**
		 * Class constructor.
		 *
		 * @since 10.6.0
		 */
		public function __construct() {
			$this->id = 'woo_wallet';

			parent::__construct();
		}

		/**
		 * Is plugin enabled?.
		 *
		 * @since 10.6.0
		 * @return bool
		 * */
		public function is_plugin_enabled() {
			return class_exists( 'Woo_Wallet' ) || class_exists( 'WooWallet' );
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
			// Assign instant winner woo wallet prize.
			add_filter( 'lty_instant_winner_assign_woo_wallet_prize', array( $this, 'assign_wallet_prize' ), 10, 2 );
			// Remove instant winner woo wallet prize.
			add_filter( 'lty_instant_winner_remove_won_woo_wallet_prize', array( $this, 'remove_wallet_prize' ), 10, 2 );
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

			$prize_type_options['woo_wallet'] = __( 'Woo-Wallet', 'lottery-for-woocommerce' );

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
			if ( 'woo_wallet' !== $prize_type ) {
				return $prize_type_label;
			}

			return __( 'Terra Wallet', 'lottery-for-woocommerce' );
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

			if ( ! is_object( woo_wallet() ) || ! is_object( woo_wallet()->wallet ) ) {
				return $bool;
			}

			$description = str_replace(
				array( '{prize_amount}', '{product_name}', '{order_number}' ),
				array( lty_price( $instant_winner_log->get_prize_amount() ), $instant_winner_log->get_product_name(), $instant_winner_log->get_order_number( false ) ),
				__( 'You have won giveaway Instant Win Prize({prize_amount}) on the {product_name}, Order #{order_number}', 'lottery-for-woocommerce' )
			);

			return woo_wallet()->wallet->credit( $instant_winner_log->get_user_id(), floatval( $instant_winner_log->get_prize_amount() ), $description );
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

			if ( ! is_object( woo_wallet() ) || ! is_object( woo_wallet()->wallet ) ) {
				return $bool;
			}

			$description = str_replace(
				array( '{prize_amount}', '{product_name}' ),
				array( lty_price( $instant_winner_log->get_prize_amount() ), $instant_winner_log->get_product_name( true ) ),
				__( 'Instant Win Prize({prize_amount}) on the {product_name} giveaway has been failed', 'lottery-for-woocommerce' )
			);

			return woo_wallet()->wallet->debit( $instant_winner_log->get_user_id(), floatval( $instant_winner_log->get_prize_amount() ), $description );
		}
	}
}
