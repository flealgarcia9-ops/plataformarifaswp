<?php
/**
 * Handles the action scheduler.
 *
 * @since 9.8.0
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

if ( ! class_exists( 'LTY_Action_Scheduler_Handler' ) ) {

	/**
	 * Class.
	 *
	 * @since 9.8.0
	 */
	class LTY_Action_Scheduler_Handler {

		/**
		 * Events.
		 *
		 * @since 9.8.0
		 * @var array
		 */
		private static $events = array(
			'lty_delete_ticket_logs'                 => 1,
			'lty_delete_instant_winner_rules'        => 1,
			'lty_delete_instant_winner_logs'         => 1,
			'lty_delete_instant_winner_prize_groups' => 1,
			'lty_delete_lottery_winner_logs'         => 1,
		);

		/**
		 * Class initialization.
		 *
		 * @since 9.8.0
		 */
		public static function init() {
			foreach ( self::$events as $event => $argument_count ) {
				$method = str_replace( 'lty_', '', $event );
				if ( ! method_exists( __CLASS__, $method ) ) {
					continue;
				}

				add_action( $event, array( __CLASS__, $method ), 10, $argument_count );
			}
		}

		/**
		 * Delete the ticket logs.
		 *
		 * @since 9.8.0
		 * @param array $ticket_log_ids Ticket log IDs.
		 * @return void
		 */
		public static function delete_ticket_logs( $ticket_log_ids ) {
			// Return if the ticket log IDs is empty.
			if ( ! lty_check_is_array( $ticket_log_ids ) ) {
				return;
			}

			foreach ( $ticket_log_ids as $key => $ticket_log_id ) {
				lty_delete_lottery_ticket( $ticket_log_id );
			}
		}

		/**
		 * Delete the instant winner rules.
		 *
		 * @since 9.8.0
		 * @param array $instant_winner_rule_ids Instant winner rule IDs.
		 * @return void
		 */
		public static function delete_instant_winner_rules( $instant_winner_rule_ids ) {
			// Return if the instant winner rule IDs is empty.
			if ( ! lty_check_is_array( $instant_winner_rule_ids ) ) {
				return;
			}

			foreach ( $instant_winner_rule_ids as $key => $instant_winner_rule_id ) {
				lty_delete_instant_winner_rule( $instant_winner_rule_id );
			}
		}

		/**
		 * Delete the instant winner logs.
		 *
		 * @since 9.8.0
		 * @param array $instant_winner_log_ids Instant winner log IDs.
		 * @return void
		 */
		public static function delete_instant_winner_logs( $instant_winner_log_ids ) {
			// Return if the instant winner log IDs is empty.
			if ( ! lty_check_is_array( $instant_winner_log_ids ) ) {
				return;
			}

			foreach ( $instant_winner_log_ids as $key => $instant_winner_log_id ) {
				lty_delete_instant_winner_log( $instant_winner_log_id );
			}
		}

		/**
		 * Delete the instant winner prize groups.
		 *
		 * @since 11.6.0
		 * @param array $instant_winner_prize_group_ids Instant winner prize group IDs.
		 * @return void
		 */
		public static function delete_instant_winner_prize_groups( $instant_winner_prize_group_ids ) {
			// Return if the instant winner prize group ID's is empty.
			if ( ! lty_check_is_array( $instant_winner_prize_group_ids ) ) {
				return;
			}

			foreach ( $instant_winner_prize_group_ids as $key => $group_id ) {
				lty_delete_instant_winner_prize_group( $group_id );
			}
		}

		/**
		 * Delete the lottery winner logs.
		 *
		 * @since 9.8.0
		 * @param array $winner_log_ids Lottery winner log IDs.
		 * @return void
		 */
		public static function delete_lottery_winner_logs( $winner_log_ids ) {
			// Return if the lottery winner log IDs is empty.
			if ( ! lty_check_is_array( $winner_log_ids ) ) {
				return;
			}

			foreach ( $winner_log_ids as $key => $winner_log_id ) {
				lty_delete_lottery_winner( $winner_log_id );
			}
		}
	}

	LTY_Action_Scheduler_Handler::init();
}
