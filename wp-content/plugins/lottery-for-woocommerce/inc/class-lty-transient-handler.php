<?php
/**
 * Transient Handler.
 *
 * @since 11.9.0
 */
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'LTY_Transient_Handler' ) ) {

	/**
	 * Class.
	 *
	 * @since 11.9.0
	 */
	class LTY_Transient_Handler {
		/**
		 * Class initialization.
		 *
		 * @since 11.9.0
		 */
		public static function init() {
			add_action( 'lty_instant_winner_rule_created', array( __CLASS__, 'delete_product_transients' ), 10, 1 );
			add_action( 'lty_instant_winner_rules_saved', array( __CLASS__, 'delete_product_transients' ), 10, 1 );
			add_action( 'lty_instant_winner_rules_deleted', array( __CLASS__, 'delete_product_transients' ), 10, 1 );
			add_action( 'lty_lottery_product_saved', array( __CLASS__, 'delete_product_transients' ), 10, 1 );
			add_action( 'lty_lottery_after_relisted', array( __CLASS__, 'delete_product_transients' ), 10, 1 );
			add_action( 'lty_lottery_instant-winner-rule_imported', array( __CLASS__, 'delete_instant_winner_group_transient_after_import' ), 10, 1 );
		}

		/**
		 * Delete all transients.
		 *
		 * @since 12.3.0
		 * @param int $product_id Product ID.
		 * @param int $user_id User ID.
		 * @return void
		 */
		public static function delete_all_transients( $product_id, $user_id = false ) {
			self::delete_product_transients( $product_id );
			if ( $user_id ) {
				delete_transient( "lty_user_placed_ticket_count_{$product_id}_{$user_id}" );
				delete_transient( "lty_user_purchased_ticket_count_{$product_id}_{$user_id}" );
			}
		}

		/**
		 * Delete product transients.
		 *
		 * @since 12.3.0
		 * @param int $product_id Product ID.
		 * @return void
		 */
		public static function delete_product_transients( $product_id ) {
			delete_transient( "lty_instant_winner_rules_count_{$product_id}" );
			delete_transient( "lty_placed_ticket_count_{$product_id}" );
			delete_transient( "lty_purchased_ticket_count_{$product_id}" );

			self::delete_instant_winner_group_transient( $product_id );
		}

		/**
		 * Delete instant winner group transient.
		 *
		 * @since 11.9.0
		 * @param int $product_id Product ID.
		 * @return void
		 */
		public static function delete_instant_winner_group_transient( $product_id ) {
			$product = wc_get_product( $product_id );
			if ( ! lty_is_lottery_product( $product ) ) {
				return;
			}

			$group_ids = $product->get_instant_winner_prize_group_ids();
			if ( ! lty_check_is_array( $group_ids ) ) {
				return;
			}

			foreach ( $group_ids as $group_id ) {
				delete_transient( 'lty_group_current_instant_winner_log_ids_' . $group_id );
			}
		}

		/**
		 * Delete import instant winner group transient.
		 *
		 * @since 11.9.0
		 * @param object $importer Importer object.
		 * @return void
		 */
		public static function delete_instant_winner_group_transient_after_import( $importer ) {
			if ( ! is_object( $importer ) ) {
				return;
			}

			self::delete_instant_winner_group_transient( $importer->get_product_id() );
		}
	}

	LTY_Transient_Handler::init();
}
