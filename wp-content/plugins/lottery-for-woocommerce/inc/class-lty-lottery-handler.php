<?php

/**
 * Handles the Lottery.
 * */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'LTY_Lottery_Handler' ) ) {

	/**
	 * Class.
	 * */
	class LTY_Lottery_Handler {

		/**
		 * Init.
		 * */
		public static function init() {
			// Delete Lottery data's.
			add_action( 'before_delete_post', array( __CLASS__, 'delete_lottery_data' ) );
			add_action( 'woocommerce_before_delete_order', array( __CLASS__, 'maybe_delete_hpos_order_ticket_data' ), 10, 1 );
			// Delete user lottery data.
			add_action( 'deleted_user', array( __CLASS__, 'delete_user_lottery_data' ) );
			// Update instant winner log.
			add_action( 'lty_lottery_after_relisted', array( __CLASS__, 'maybe_update_instant_winner_log' ) );
			add_action( 'transition_post_status', array( __CLASS__, 'handle_scheduled_lottery_product_status' ), 10, 3 );
		}

		/**
		 * Handle scheduled product status transition
		 *
		 * @since 8.3.0
		 * @param string $new_status
		 * @param string $old_status
		 * @param object $post
		 * @return void
		 */
		public static function handle_scheduled_lottery_product_status( $new_status, $old_status, $post ) {
			if ( ! is_object( $post ) || 'product' !== $post->post_type || 'publish' !== $new_status ) {
				return;
			}

			$product = wc_get_product( $post->ID );
			if ( ! lty_is_lottery_product( $product ) || ! $product->exists() ) {
				return;
			}

			// Return if the lottery is not extended or relisted.
			if ( $product->is_closed() && ( isset( $_REQUEST['lty_lottery_extend'] ) || isset( $_REQUEST['lty_lottery_manual_relist'] ) ) ) {
				return;
			}

			$end_date = isset( $_REQUEST['_lty_end_date'] ) ? wc_clean( wp_unslash( $_REQUEST['_lty_end_date'] ) ) : '';
			// Return if the lottery end date is less than the current date.
			if ( empty( $end_date ) || ( $product->is_closed() && ( strtotime( 'now' ) > strtotime( LTY_Date_Time::get_mysql_date_time_format( $end_date, false, 'UTC' ) ) ) ) ) {
				return;
			}

			$status = $product->is_started() ? 'lty_lottery_started' : 'lty_lottery_not_started';
			$product->update_post_meta( 'lty_lottery_status', $status );
		}

		/**
		 * Delete lottery data.
		 *
		 * @since 1.0.0
		 * @param int $post_id Post ID.
		 * @return void
		 */
		public static function delete_lottery_data( $post_id ) {
			switch ( get_post_type( $post_id ) ) {
				case 'product':
					$product = wc_get_product( $post_id );

					// Return if given id is not a product or lottery product type.
					if ( ! lty_is_lottery_product( $product ) || ! $product->exists() ) {
						break;
					}

					// Delete ticket logs.
					self::delete_all_ticket_log( $post_id );

					// Delete winner logs.
					self::delete_all_winner_log( $post_id );
					break;

				case 'shop_order':
					// Delete order ticket data.
					self::delete_order_ticket_data( $post_id );
					break;
			}
		}

		/**
		 * Delete all ticket logs.
		 *
		 * @since 1.0.0
		 * @param int $product_id Product ID.
		 * @return void
		 */
		public static function delete_all_ticket_log( $product_id ) {
			// Delete ticket logs.
			$ticket_ids = lty_get_ticket_ids( array( 'product_id' => $product_id ) );
			if ( lty_check_is_array( $ticket_ids ) ) {
				$chunked_ticket_ids = array_filter( array_chunk( $ticket_ids, 100 ) );
				foreach ( $chunked_ticket_ids as $key => $ticket_ids ) {
					as_schedule_single_action(
						time(),
						'lty_delete_ticket_logs',
						array( 'ticket_log_ids' => $ticket_ids )
					);
				}
			}

			// Delete instant winner rules.
			$instant_winner_rules = lty_get_instant_winner_rule_ids( $product_id );
			if ( lty_check_is_array( $instant_winner_rules ) ) {
				$chunked_instant_winner_rule_ids = array_filter( array_chunk( $instant_winner_rules, 100 ) );
				foreach ( $chunked_instant_winner_rule_ids as $key => $instant_winner_rule_ids ) {
					as_schedule_single_action(
						time(),
						'lty_delete_instant_winner_rules',
						array( 'instant_winner_rule_ids' => $instant_winner_rule_ids )
					);
				}
			}

			// Delete instant winner logs.
			$instant_winner_logs = lty_get_instant_winner_log_ids( $product_id );
			if ( lty_check_is_array( $instant_winner_logs ) ) {
				$chunked_instant_winner_log_ids = array_filter( array_chunk( $instant_winner_logs, 100 ) );
				foreach ( $chunked_instant_winner_log_ids as $instant_winner_log_ids ) {
					as_schedule_single_action(
						time(),
						'lty_delete_instant_winner_logs',
						array( 'instant_winner_log_ids' => $instant_winner_log_ids )
					);
				}
			}

			// Delete instant winner groups.
			$instant_winner_prize_groups = lty_get_instant_winner_prize_group_ids( $product_id );
			if ( lty_check_is_array( $instant_winner_prize_groups ) ) {
				$chunked_instant_winner_prize_group_ids = array_filter( array_chunk( $instant_winner_prize_groups, 100 ) );
				foreach ( $chunked_instant_winner_prize_group_ids as $instant_winner_prize_group_ids ) {
					as_schedule_single_action(
						time(),
						'lty_delete_instant_winner_prize_groups',
						array( 'instant_winner_prize_group_ids' => $instant_winner_prize_group_ids )
					);
				}
			}

			LTY_Transient_Handler::delete_product_transients( $product_id );
		}

		/**
		 * Delete all winner log.
		 *
		 * @since 1.0.0
		 * @param int $product_id Product ID.
		 * @return void
		 */
		public static function delete_all_winner_log( $product_id ) {
			$winner_logs = lty_get_lottery_winners_by_product_id( $product_id );
			if ( ! lty_check_is_array( $winner_logs ) ) {
				return;
			}

			$chunked_winner_log_ids = array_filter( array_chunk( $winner_logs, 100 ) );
			foreach ( $chunked_winner_log_ids as $winner_log_ids ) {
				as_schedule_single_action(
					time(),
					'lty_delete_lottery_winner_logs',
					array( 'winner_log_ids' => $winner_log_ids )
				);
			}
		}

		/**
		 * Delete user lottery data.
		 *
		 * @since 1.0.0
		 * @param string|int $user_id User ID.
		 * @return void
		 */
		public static function delete_user_lottery_data( $user_id ) {
			// Delete lottery ticket ids.
			$ticket_logs = lty_get_ticket_ids( array( 'user_id' => $user_id ) );
			if ( lty_check_is_array( $ticket_logs ) ) {
				$chunked_ticket_log_ids = array_filter( array_chunk( $ticket_logs, 100 ) );
				foreach ( $chunked_ticket_log_ids as $key => $ticket_log_ids ) {
					as_schedule_single_action(
						time(),
						'lty_delete_ticket_logs',
						array( 'ticket_log_ids' => $ticket_log_ids )
					);
				}
			}

			// Delete lottery winner ID's.
			$winner_logs = lty_get_lottery_winner_ids( array( 'user_id' => $user_id ) );
			if ( lty_check_is_array( $winner_logs ) ) {
				$chunked_winner_log_ids = array_filter( array_chunk( $winner_logs, 100 ) );
				foreach ( $chunked_winner_log_ids as $winner_log_ids ) {
					as_schedule_single_action(
						time(),
						'lty_delete_lottery_winner_logs',
						array( 'winner_log_ids' => $winner_log_ids )
					);
				}
			}

			// Delete instant winner logs.
			$instant_winner_logs = lty_get_instant_winner_log_ids_by_user_id( $user_id );
			if ( lty_check_is_array( $instant_winner_logs ) ) {
				$chunked_instant_winner_log_ids = array_filter( array_chunk( $instant_winner_logs, 100 ) );
				foreach ( $chunked_instant_winner_log_ids as $instant_winner_log_ids ) {
					as_schedule_single_action(
						time(),
						'lty_delete_instant_winner_logs',
						array( 'instant_winner_log_ids' => $instant_winner_log_ids )
					);
				}
			}
		}

		/**
		 * Maybe delete custom orders table ticket logs on order.
		 *
		 * @since 12.1.0
		 * @param int $order_id Order ID.
		 * */
		public static function maybe_delete_hpos_order_ticket_data( $order_id ) {
			if ( ! \Automattic\WooCommerce\Utilities\OrderUtil::custom_orders_table_usage_is_enabled() ) {
				return;
			}

			self::delete_order_ticket_data( $order_id );
		}


		/**
		 * Delete all ticket logs on order.
		 *
		 * @since 12.1.0
		 * @param int $order_id Order ID.
		 * */
		public static function delete_order_ticket_data( $order_id ) {
			if ( 'yes' !== get_option( 'lty_settings_delete_lottery_ticket_data_on_order_delete', 'no' ) ) {
				return;
			}

			$order = wc_get_order( $order_id );
			if ( ! is_object( $order ) ) {
				return;
			}

			// Delete ticket logs.
			$ticket_ids = $order->get_meta( 'lty_ticket_ids_in_order' );
			if ( lty_check_is_array( $ticket_ids ) ) {
				foreach ( $ticket_ids as $ticket_id ) {
					lty_delete_lottery_ticket( $ticket_id );
				}
			}

			// Delete instant winner logs.
			$instant_winner_log_ids = lty_get_instant_winner_log_ids_by_order_id( $order_id );
			if ( lty_check_is_array( $instant_winner_log_ids ) ) {
				foreach ( $instant_winner_log_ids as $instant_winner_log_id ) {
					lty_delete_instant_winner_log( $instant_winner_log_id );
				}
			}
		}

		/**
		 * Handles the lottery product.
		 * */
		public static function handle_lottery_product( $product_id, $product, $action = false ) {

			// Return if given object is not a product or lottery product type.
			if ( ! is_object( $product ) || ! $product->exists() || 'lottery' !== $product->get_type() ) {
				return $action;
			}

			// Return if lottery is not created.
			if ( empty( $product->get_lty_lottery_status() ) || ( ! $product->is_unlimited_scheduled_lottery() && ( empty( $product->get_lty_end_date() ) || empty( $product->get_lty_start_date() ) ) ) ) {
				return $action;
			}

			// Lottery start and end.
			if ( ! $product->get_lty_closed() ) {
				// Update status in lottery.
				if ( $product->has_lottery_status( 'lty_lottery_not_started' ) && $product->is_started() && ! $product->is_closed() ) {
					// Start lottery.
					self::start_lottery( $product_id, $product );
					$action = true;
				} elseif ( $product->is_started() && $product->is_closed() && ! $product->has_lottery_status( 'lty_lottery_finished' ) ) {
					// Close lottery.
					self::end_lottery( $product_id, $product );
					$action = true;
				} elseif ( 'yes' === get_option( 'lty_settings_close_lottery_reach_max' ) ) {

					if ( $product->is_started() && 'lty_lottery_closed' !== $product->get_lty_lottery_status() && ! $product->is_closed() ) {
						$maximum_tickets = $product->get_lty_maximum_tickets();
						$ticket_count    = $product->get_purchased_ticket_count();
						if ( $ticket_count >= $maximum_tickets ) {
							// Close lottery when maximum ticket count reached.
							self::end_lottery( $product_id, $product );
							$action = true;
						}
					}
				}
			}

			return $action;
		}

		/**
		 * Delete Lottery ticket.
		 *
		 * @return void
		 * */
		public static function delete_lottery_ticket( $lottery_ticket_id, $product_id = false, $product = false ) {
			$lottery_ticket = lty_get_lottery_ticket( $lottery_ticket_id );

			if ( ! $lottery_ticket || ! $lottery_ticket->exists() ) {
				return;
			}

			if ( ! $product_id ) {
				$product_id = $lottery_ticket->get_product_id();
			}

			if ( ! $product ) {
				$product = wc_get_product( $product_id );
			}

			// Delete bid log.
			lty_delete_lottery_ticket( $lottery_ticket_id );
			LTY_Transient_Handler::delete_all_transients( $product_id, $lottery_ticket->get_user_id() );

			$hold_tickets = array_filter( (array) get_post_meta( $lottery_ticket->get_product_id(), '_lty_hold_tickets', true ) );
			if ( lty_check_is_array( $hold_tickets ) ) {
				$index = array_search( $lottery_ticket->get_lottery_ticket_number(), $hold_tickets );
				if ( isset( $hold_tickets[ $index ] ) ) {
					unset( $hold_tickets[ $index ] );
					update_post_meta( $lottery_ticket->get_product_id(), '_lty_hold_tickets', $hold_tickets );
				}
			}

			// Return.
			if ( ! is_object( $product ) ) {
				return;
			}

			$lottery_ticket_count = intval( $product->get_lty_ticket_count() );
			$product->update_post_meta( 'lty_ticket_count', $lottery_ticket_count - 1 );
		}

		/**
		 * Start lottery product.
		 * */
		public static function start_lottery( $product_id, $product, $update_dates = false ) {

			// Return if given id is not a product or lottery product type.
			if ( ! is_object( $product ) || ! $product->exists() || 'lottery' !== $product->get_type() ) {
				return;
			}
			// Update lottery status.
			$product->update_lottery_status( 'lty_lottery_started' );

			if ( $update_dates ) {
				$start_date_object = LTY_Date_Time::get_date_time_object( 'now' );
				$start_date        = $start_date_object->format( 'Y-m-d H:i:s' );
				$start_date_gmt    = $start_date_object->setTimeZone( new DateTimeZone( 'UTC' ) )->format( 'Y-m-d H:i:s' );

				$product->update_post_meta( 'lty_start_date', $start_date );
				$product->update_post_meta( 'lty_start_date_gmt', $start_date_gmt );
			}

			$updated_product = wc_get_product( $product_id );

			/**
			 * This hook is used to do extra action after lottery started.
			 *
			 * @since 1.0
			 */
			do_action( 'lty_lottery_after_started', $product_id );
		}

		/**
		 * End Lottery product.
		 * */
		public static function end_lottery( $product_id, $product, $update_dates = false ) {
			// Return if given id is not a product or lottery product type.
			if ( ! is_object( $product ) || ! $product->exists() || 'lottery' !== $product->get_type() ) {
				return;
			}

			// Return if the lottery has been closed.
			if ( get_post_meta( $product_id, '_lty_closed', true ) && ! $product->has_lottery_status( 'lty_lottery_started' ) ) {
				return;
			}

			// Return if the lottery is already in processing of end action.
			if ( get_transient( 'lty_processing_end_lottery_' . $product->get_id() ) ) {
				return;
			}

			// Set the lottery is in processing of end action.
			set_transient( 'lty_processing_end_lottery_' . $product->get_id(), true, 60 );

			$current_time     = current_time( 'mysql' );
			$current_time_gmt = current_time( 'mysql', true );

			// Update lottery closed meta's.
			$product->update_post_meta( 'lty_closed_date', $current_time );
			$product->update_post_meta( 'lty_closed_date_gmt', $current_time_gmt );

			if ( $update_dates ) {
				$end_date_object = LTY_Date_Time::get_date_time_object( 'now' );
				$end_date        = $end_date_object->format( 'Y-m-d H:i:s' );
				$end_date_gmt    = $end_date_object->setTimeZone( new DateTimeZone( 'UTC' ) )->format( 'Y-m-d H:i:s' );

				$product->update_post_meta( 'lty_end_date', $end_date );
				$product->update_post_meta( 'lty_end_date_gmt', $end_date_gmt );
			}

			if ( 'yes' === $product->get_lty_lottery_unique_winners() && $product->get_lottery_user_ids_count() < $product->get_lty_winners_count() ) {
				// Lottery failed status.
				$product->update_post_meta( 'lty_lottery_status', 'lty_lottery_failed' );
				$product->update_post_meta( 'lty_closed', '2' );
				$product->update_post_meta( 'lty_failed_reason', 2 );
				$product->update_post_meta( 'lty_failed_date', $current_time );
				$product->update_post_meta( 'lty_failed_date_gmt', $current_time_gmt );
			} elseif ( $product->is_ticket_count_reached() ) {
				// Lottery closed status.
				$product->update_post_meta( 'lty_lottery_status', 'lty_lottery_closed' );
				$product->update_post_meta( 'lty_closed', '1' );

				self::declare_winners( $product );
			} else {
				// Lottery failed status.
				$product->update_post_meta( 'lty_lottery_status', 'lty_lottery_failed' );
				$product->update_post_meta( 'lty_closed', '2' );
				$product->update_post_meta( 'lty_failed_reason', 1 );
				$product->update_post_meta( 'lty_failed_date', $current_time );
				$product->update_post_meta( 'lty_failed_date_gmt', $current_time_gmt );
			}

			// Delete processing end lottery.
			delete_transient( 'lty_processing_end_lottery_' . $product->get_id() );

			/**
			 * This hook is used to do extra action after lottery ended.
			 *
			 * @since 1.0
			 */
			do_action( 'lty_lottery_after_ended', $product_id );
		}

		/**
		 * Declare the lottery winners.
		 *
		 * @since 6.7
		 */
		public static function declare_winners( $product ) {
			// Return if the winner selection method is manual.
			if ( '1' != $product->get_lty_winner_selection_method() ) {
				return;
			}

			// Return if the already winners exists for the lottery.
			if ( lty_check_is_array( $product->get_current_winner_ids() ) ) {
				return;
			}

			// Get the valid winners automatic winner.
			// Return if the valid winners not exists for the lottery.
			$ticket_ids = ( 'yes' === $product->get_lty_lottery_unique_winners() ) ? lty_get_valid_unique_winner_lottery_tickets( $product ) : lty_get_valid_winner_lottery_tickets( $product );
			if ( ! lty_check_is_array( $ticket_ids ) || count( $ticket_ids ) < $product->get_lty_winners_count() ) {
				return;
			}

			$random_ticket_ids = lty_get_random_ticket_ids( $ticket_ids, $product );

			LTY_Lottery_Winner::handle_lottery_winner( $random_ticket_ids, $product, '3' );
		}

		/**
		 * Relist lottery product.
		 * */
		public static function relist_lottery( $product_id, $product = false, $automatic = false ) {
			if ( ! $product ) {
				$product = wc_get_product( $product_id );
			}

			// Return if given id is not a product or lottery product type.
			if ( ! is_object( $product ) || ! $product->exists() || 'lottery' != $product->get_type() ) {
				return;
			}

			$relist_duration = $product->get_relist_duration();
			if ( ! $product->is_unlimited_scheduled_lottery() && $automatic && ! lty_is_valid_duration( $relist_duration ) ) {
				return;
			}

			$start_date_object = LTY_Date_Time::get_date_time_object( 'now' );
			$end_date_object   = LTY_Date_Time::get_date_time_object( 'now' );
			$start_date        = $start_date_object->format( 'Y-m-d H:i:s' );
			$start_date_gmt    = $start_date_object->setTimeZone( new DateTimeZone( 'UTC' ) )->format( 'Y-m-d H:i:s' );

			$relist_details = array(
				'ticket_count'                => $product->get_purchased_ticket_count(),
				'start_date'                  => $product->get_lty_relisted_date() ? $product->get_lty_relisted_date() : $product->get_lty_start_date(),
				'end_date'                    => $product->get_lty_closed_date(),
				'start_date_gmt'              => $product->get_current_start_date_gmt(),
				'end_date_gmt'                => $product->get_lty_closed_date_gmt(),
				'failed_date'                 => $product->get_lty_failed_date(),
				'failed_date_gmt'             => $product->get_lty_failed_date_gmt(),
				'failed_reason'               => $product->get_lty_failed_reason(),
				'lottery_configuration'       => lty_get_lottery_product_configuration( $product_id, $product ),
				'lottery_status'              => $product->get_lty_lottery_status(),
				'finished_date'               => $product->get_lty_finished_date(),
				'finished_date_gmt'           => $product->get_lty_finished_date_gmt(),
				'instant_winner'              => $product->is_instant_winner() ? 'yes' : 'no',
				'list_count'                  => $product->get_current_relist_count(),
				'unlimited_scheduled_lottery' => '2' === $product->get_lty_lottery_schedule_type() ? 'yes' : 'no',
			);

			// Relist data.
			$last_relists   = lty_check_is_array( $product->get_lty_relists() ) ? $product->get_lty_relists() : array();
			$last_relists[] = $relist_details;

			$product->update_post_meta( 'lty_relisted_date', $start_date );
			$product->update_post_meta( 'lty_relisted_date_gmt', $start_date_gmt );

			$end_date     = '';
			$end_date_gmt = '';
			if ( ! $automatic ) {
				$start_date     = '';
				$start_date_gmt = '';
			} elseif ( ! $product->is_unlimited_scheduled_lottery() ) {
				/* translators: %1$s: number, %2$s: unit */
				$end_date_object->modify( sprintf( __( '+%1$s %2$s', 'lottery-for-woocommerce' ), $relist_duration['number'], $relist_duration['unit'] ) );
				$end_date     = $end_date_object->format( 'Y-m-d H:i:s' );
				$end_date_gmt = $end_date_object->setTimeZone( new DateTimeZone( 'UTC' ) )->format( 'Y-m-d H:i:s' );
			}

			if ( $automatic ) {
				// Update status.
				$product->update_lottery_status( 'lty_lottery_started' );
			}

			// Update meta's.
			$product->update_post_meta( 'lty_relisted', 'yes' );
			$product->update_post_meta( 'lty_start_date', $start_date );
			$product->update_post_meta( 'lty_start_date_gmt', $start_date_gmt );
			$product->update_post_meta( 'lty_end_date', $end_date );
			$product->update_post_meta( 'lty_end_date_gmt', $end_date_gmt );
			$product->update_post_meta( 'lty_relists', $last_relists );
			$product->update_post_meta( 'lty_list_count', count( $last_relists ) );

			// Set the lottery stock.
			wc_update_product_stock( $product_id, $product->get_lty_maximum_tickets(), 'set' );

			// Delete the lottery meta's.
			$relist_metas = lty_lottery_reset_meta_keys();

			foreach ( $relist_metas as $meta_key ) {
				delete_post_meta( $product->get_id(), $meta_key );
			}

			/**
			 * This hook is used to do extra action after lottery relisted.
			 *
			 * @since 1.0
			 */
			do_action( 'lty_lottery_after_relisted', $product_id, $last_relists );
		}

		/**
		 * Extend lottery product.
		 * */
		public static function extend_lottery( $product_id, $product = false ) {

			if ( ! $product ) {
				$product = wc_get_product( $product_id );
			}

			// Return if given id is not a product or lottery product type.
			if ( ! is_object( $product ) || ! $product->exists() || 'lottery' != $product->get_type() ) {
				return;
			}

			$end_date     = '';
			$end_date_gmt = '';
			$product->update_post_meta( 'lty_end_date', $end_date );
			$product->update_post_meta( 'lty_end_date_gmt', $end_date_gmt );
			// Update status.
			$product->update_lottery_status( 'lty_lottery_started' );
			$reset_meta_keys = lty_lottery_extend_meta_keys();

			// Delete the lottery metas.
			foreach ( $reset_meta_keys as $meta_key ) {
				delete_post_meta( $product->get_id(), $meta_key );
			}

			// Set the lottery is extended.
			set_transient( 'lty_lottery_extended_' . $product->get_id(), true, 86400 );
		}

		/**
		 * Update instant winner log.
		 *
		 * @since 8.0.0
		 * @param string $product_id, array $last_relists.
		 * @return void.
		 * */
		public static function maybe_update_instant_winner_log( $product_id, $last_relists = false ) {
			$product = wc_get_product( $product_id );
			// Return if given id is not a product or lottery product type.
			if ( ! is_object( $product ) || ! $product->exists() || 'lottery' !== $product->get_type() ) {
				return;
			}

			$relist_count           = count( $product->get_lty_relists() ) - 1;
			$instant_winner_log_ids = lty_get_instant_winner_log_ids( $product_id, false, $relist_count );

			foreach ( $instant_winner_log_ids as $instant_winner_log_id ) {
				$instant_winner_log = lty_get_instant_winner_log( $instant_winner_log_id );

				if ( ! $instant_winner_log->exists() ) {
					continue;
				}

				$args = array(
					'lty_ticket_number'        => $instant_winner_log->get_ticket_number(),
					'lty_prize_type'           => $instant_winner_log->get_prize_type(),
					'lty_instant_winner_prize' => $instant_winner_log->get_prize_message(),
					'lty_prize_amount'         => $instant_winner_log->get_prize_amount(),
					'lty_prize_group_id'       => $instant_winner_log->get_prize_group_id(),
					'lty_start_date'           => $product->get_lty_start_date(),
					'lty_end_date'             => $product->get_lty_end_date(),
					'lty_start_date_gmt'       => $product->get_lty_start_date_gmt(),
					'lty_end_date_gmt'         => $product->get_lty_end_date_gmt(),
					'lty_lottery_id'           => $product_id,
					'lty_current_relist_count' => count( $product->get_lty_relists() ),
				);

				$log_id = lty_get_instant_winner_log_id_by_rule_id( $instant_winner_log->rule_id, count( $product->get_lty_relists() ) );
				// Check if log exists.
				if ( ! $log_id ) {
					lty_create_new_instant_winner_log( $args, array( 'post_parent' => $instant_winner_log->rule_id ) );
				} else {
					lty_update_instant_winner_log( $log_id, $args );
				}
			}
		}
	}

	LTY_Lottery_Handler::init();
}
