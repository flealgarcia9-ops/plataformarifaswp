<?php

/**
 * Handles the Order.
 *
 * @since 1.0.0
 * */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'LTY_Order_Handler' ) ) {

	/**
	 * Class.
	 * */
	class LTY_Order_Handler {

		public static $order_object_saved = false;

		/**
		 * Tickets created orders.
		 *
		 * @since 12.0.0
		 * @var array
		 */
		public static $tickets_created_orders = array();

		/**
		 * Class Initialization.
		 * */
		public static function init() {
			// Update order meta.
			add_action( 'woocommerce_checkout_create_order_line_item', array( __CLASS__, 'adjust_order_item' ), 10, 4 );
			// Remove Order Item Meta key.
			add_action( 'woocommerce_hidden_order_itemmeta', array( __CLASS__, 'hide_order_item_meta_key' ), 10, 2 );
			// Create ticket on placing order.
			add_action( 'woocommerce_checkout_update_order_meta', array( __CLASS__, 'create_ticket_on_placing_order' ) );
			// Create ticket on placing order for checkout blocks.
			add_action( 'woocommerce_store_api_checkout_order_processed', array( __CLASS__, 'create_ticket_on_placing_order' ) );
			// Update lottery ticket for order.
			$lottery_order_statuses = get_option( 'lty_settings_lottery_complete_order_statuses' );

			if ( 'yes' !== get_option( 'lty_settings_prevent_create_ticket_via_rest_api', 'no' ) ) {
				// Rest API.
				add_action( 'woocommerce_payment_complete', array( __CLASS__, 'update_lottery_ticket_via_rest_api' ), 10, 1 );
				add_action( 'woocommerce_after_order_object_save', array( __CLASS__, 'create_ticket_on_placing_order_via_rest_api' ) );
			}

			if ( lty_check_is_array( $lottery_order_statuses ) ) {
				foreach ( $lottery_order_statuses as $order_status ) {
					add_action( "woocommerce_order_status_{$order_status}", array( __CLASS__, 'update_lottery_ticket_in_order' ), 10, 2 );
				}
			}

			// Remove lottery ticket for Cancelled orders.
			$cancel_order_statuses = array( 'cancelled', 'refunded', 'failed' );

			if ( lty_check_is_array( $cancel_order_statuses ) ) {
				foreach ( $cancel_order_statuses as $order_status ) {
					add_action( "woocommerce_order_status_{$order_status}", array( __CLASS__, 'remove_lottery_ticket_for_order_cancel' ), 10 );
				}
			}

			add_action( 'woocommerce_order_item_meta_start', array( __CLASS__, 'order_item_meta' ), 10, 3 );

			// Alter order item quantity to manage stock.
			add_filter( 'woocommerce_order_item_quantity', array( __CLASS__, 'alter_order_item_quantity' ), 10, 3 );
			// Prevent adjust line item product stock.
			add_filter( 'woocommerce_prevent_adjust_line_item_product_stock', array( __CLASS__, 'prevent_adjust_line_item_product_stock' ), 10, 3 );
			// Render lottery order item meta.
			add_action( 'woocommerce_order_item_meta_start', array( __CLASS__, 'render_lottery_order_item_meta' ), 20, 3 );
			// Render the order instant win prize details.
			add_action( 'woocommerce_after_order_details', array( __CLASS__, 'render_order_instant_winners' ), 10, 1 );
		}

		/**
		 * Create ticket on placing order via rest API.
		 * */
		public static function create_ticket_on_placing_order_via_rest_api( $order ) {
			// Return if Current request is not Rest API.
			if ( ! WC()->is_rest_api_request() || WC()->is_store_api_request() || self::$order_object_saved ) {
				return;
			}

			self::$order_object_saved = true;

			self::create_ticket_for_order_item( $order, false, true, false );
		}

		/**
		 * Create ticket on placing order.
		 * */
		public static function create_ticket_on_placing_order( $order_id, $product_id = false, $update_question_ans_meta = true, $create_once = true, $manual_order = false ) {
			$order_id = is_object( $order_id ) ? $order_id->get_id() : $order_id;
			$order    = wc_get_order( $order_id );
			if ( ! is_object( $order ) ) {
				return;
			}

			self::create_ticket_for_order_item( $order, $product_id, $update_question_ans_meta, $create_once, $manual_order );
		}

		/**
		 * Create ticket for the order lottery item.
		 *
		 * @since 10.4.0
		 * @param object  $order
		 * @param int     $product_id
		 * @param boolean $update_question_ans_meta
		 * @param boolean $create_once
		 * */
		public static function create_ticket_for_order_item( $order, $product_id = false, $update_question_ans_meta = true, $create_once = true, $manual_order = false ) {
			if ( ! is_object( $order ) ) {
				return;
			}

			// Read meta data from the database to bypass the object cache.
			if ( lty_check_is_array( self::$tickets_created_orders ) && ! $order->get_meta( 'lty_lottery_ticket_created_once' ) ) {
				$order->read_meta_data( true );
			}

			if ( $create_once && $order->get_meta( 'lty_lottery_ticket_created_once' ) ) {
				return;
			}

			$order_id = $order->get_id();
			$user_id  = $order->get_user_id();
			$user     = get_user_by( 'ID', $user_id );
			if ( ! $manual_order && '3' != get_option( 'lty_settings_guest_user_participate_type' ) && ( ! is_object( $user ) || ! $user->exists() ) ) {
				return;
			}

			$ip_address = ! empty( $order->get_customer_ip_address() ) ? $order->get_customer_ip_address() : lty_get_ip_address();
			$ticket_ids = lty_check_is_array( $order->get_meta( 'lty_ticket_ids_in_order' ) ) ? $order->get_meta( 'lty_ticket_ids_in_order' ) : array();

			foreach ( $order->get_items() as $item ) {
				$product = $item->get_product();
				if ( ! lty_is_lottery_product( $product ) || ! $product->has_lottery_status( 'lty_lottery_started' ) ) {
					continue;
				}

				// Validate product id.
				if ( $product_id && $product->get_id() != $product_id ) {
					continue;
				}

				$answer                     = '';
				$answers                    = array();
				$current_product_ticket_ids = array();
				$is_valid_answer            = 'no';
				if ( isset( $item['lty_lottery_answers'][0] ) ) {
					$question_answers = $product->get_answers();
					if ( array_key_exists( $item['lty_lottery_answers'][0], $question_answers ) ) {
						$answer  = $question_answers[ $item['lty_lottery_answers'][0] ]['label'];
						$answers = array(
							'label' => $question_answers[ $item['lty_lottery_answers'][0] ]['label'],
							'key'   => isset( $item['lty_lottery']['answers'] ) ? $item['lty_lottery']['answers'] : '',
							'valid' => $question_answers[ $item['lty_lottery_answers'][0] ]['valid'],
						);

						$is_valid_answer = $question_answers[ $item['lty_lottery_answers'][0] ]['valid'];
					}
				}

				$args = array(
					'lty_user_id'      => is_object( $user ) ? $user->ID : 0,
					'lty_product_id'   => $product->get_id(),
					'lty_amount'       => $product->get_price(),
					'lty_user_name'    => is_object( $user ) ? $user->user_login : __( 'Guest', 'lottery-for-woocommerce' ),
					'lty_user_email'   => is_object( $user ) ? $user->user_email : $order->get_billing_email(),
					'lty_currency'     => $order->get_currency(),
					'lty_order_id'     => $order_id,
					'lty_answer'       => $answer,
					'lty_answers'      => $answers,
					'lty_valid_answer' => $is_valid_answer,
					'lty_ip_address'   => $ip_address,
					'lty_list_count'   => $product->get_lty_list_count(),
				);

				if ( ! $product_id && 'yes' !== $is_valid_answer && $product->is_valid_question_answer() && $product->restrict_incorrectly_selected_answer() ) {
					$args['lty_ticket_number']    = '';
					$current_product_ticket_ids[] = lty_create_new_lottery_ticket(
						$args,
						array(
							'post_parent' => $product->get_lottery_id(),
							'post_status' => 'lty_ticket_canceled',
						)
					);
					$ticket_numbers               = isset( $item['lty_lottery_tickets'] ) ? $item['lty_lottery_tickets'] : array();

					if ( $product->is_manual_ticket() ) {
						// Delete order item meta.
						$item->delete_meta_data( '_lty_lottery_tickets' );
						$item->delete_meta_data( lty_get_order_item_ticket_number_name() );
						$item->save();
					}
				} else {
					$ticket_numbers = ( isset( $item['lty_lottery_tickets'] ) && $item['lty_lottery_tickets'] ) ? $item['lty_lottery_tickets'] : $product->get_ticket_numbers( $item );
					if ( ! lty_check_is_array( $ticket_numbers ) ) {
						continue;
					}

					$placed_ticket_numbers      = $product->get_placed_tickets();
					$unavailable_ticket_numbers = array();
					$hold_tickets               = array_filter( (array) get_post_meta( $product->get_id(), '_lty_hold_tickets', true ) );
					foreach ( $ticket_numbers as $ticket_number ) {
						if ( $product->is_manual_ticket() && in_array( $ticket_number, $placed_ticket_numbers, true ) ) {
							$unavailable_ticket_numbers[] = $ticket_number;
							continue;
						}

						$args['lty_ticket_number']    = $ticket_number;
						$current_product_ticket_ids[] = lty_create_new_lottery_ticket(
							$args,
							array(
								'post_parent' => $product->get_lottery_id(),
								'post_status' => 'lty_ticket_pending',
							)
						);

						if ( lty_check_is_array( $hold_tickets ) ) {
							$index = array_search( $ticket_number, $hold_tickets );
							if ( isset( $hold_tickets[ $index ] ) ) {
								unset( $hold_tickets[ $index ] );
							}
						}
					}

					update_post_meta( $product->get_id(), 'lty_hold_tickets', $hold_tickets );
					if ( lty_check_is_array( $unavailable_ticket_numbers ) ) {
						$order_note = str_replace(
							array( '{ticket_numbers}', '{lottery_product_name}' ),
							array( implode( ', ', $unavailable_ticket_numbers ), $product->get_product_name() ),
							__( 'Giveaway product {lottery_product_name} ticket number(s) {ticket_numbers} could not be assigned for this purchase as they are already sold out.', 'lottery-for-woocommerce' )
						);

						$order->add_order_note( $order_note );
						$order->save();
					}

					if ( ! $product->is_manual_ticket() ) {
						// Update order item meta for automatic type.
						$item->add_meta_data( '_lty_lottery_tickets', $ticket_numbers );
						$ticket_label = lty_get_order_item_ticket_number_name();
						/**
						 * This hook is used to validate the adding the ticket numbers in the order item.
						 *
						 * @since 6.8
						 */
						if ( ! $item->meta_exists( $ticket_label ) && apply_filters( 'lty_validate_order_item_ticket_numbers', lty_can_show_tickets_non_paid_order( $product, $order ), $product ) ) {
							$item->add_meta_data( $ticket_label, '<span class="notranslate">' . implode( ', ', $ticket_numbers ) . '</span>' );
						}

						$item->save();
					}
				}

				$ticket_ids = array_merge( $current_product_ticket_ids, $ticket_ids );
				// Unset meta's on placing order.
				self::unset_metas_on_placing_order( $product, $order, $ticket_numbers, $update_question_ans_meta );
				// Update is instant win lottery or not on order item meta.
				$item->add_meta_data( '_lty_is_instant_win_lottery', $product->is_instant_winner() ? 'yes' : 'no' );
				$item->save();

				// Declare instant winner.
				self::declare_instant_winner( $current_product_ticket_ids, $product, $order_id );

				LTY_Transient_Handler::delete_all_transients( $product->get_id(), $args['lty_user_id'] );
			}

			if ( lty_check_is_array( $ticket_ids ) ) {
				// Update custom order meta for ticket ids.
				$order->update_meta_data( 'lty_ticket_ids_in_order', $ticket_ids );
				$order->update_meta_data( 'lty_lottery_ticket_created_once', '1' );
				$order->save();
				self::$tickets_created_orders[] = $order_id;

				/**
				 * This hook is used to do extra action after lottery ticket created.
				 *
				 * @since 6.7
				 */
				do_action( 'lty_lottery_ticket_after_created', $ticket_ids, $order_id );
			}
		}

		/**
		 * Declare instant winner.
		 *
		 * @since 8.0.0
		 * @param array  $ticket_ids Ticket IDs.
		 * @param object $product Product object.
		 * @param int    $order_id Order ID.
		 * @return void
		 */
		public static function declare_instant_winner( $ticket_ids, $product, $order_id ) {
			if ( ! lty_check_is_array( $ticket_ids ) || ! is_object( $product ) ) {
				return;
			}

			if ( ! lty_is_lottery_product( $product ) || ! $product->is_instant_winner() ) {
				return;
			}

			foreach ( $ticket_ids as $ticket_id ) {
				$lottery_ticket = lty_get_lottery_ticket( $ticket_id );
				if ( ! $lottery_ticket->exists() || ! $lottery_ticket->has_status( 'lty_ticket_pending' ) ) {
					continue;
				}

				$rule_id = lty_get_rule_id_by_ticket_number( $product->get_id(), $lottery_ticket->get_lottery_ticket_number() );
				if ( ! $rule_id ) {
					continue;
				}

				$instant_winner_log_id = lty_get_instant_winner_log_id_by_rule_id( $rule_id, $product->get_current_relist_count() );
				if ( ! $instant_winner_log_id ) {
					continue;
				}

				$args = array(
					'lty_ticket_id'  => $ticket_id,
					'lty_order_id'   => $order_id,
					'lty_user_id'    => $lottery_ticket->get_user_id(),
					'lty_user_name'  => $lottery_ticket->get_user_name(),
					'lty_user_email' => $lottery_ticket->get_user_email(),
				);

				lty_update_instant_winner_log( $instant_winner_log_id, $args, array( 'post_status' => 'lty_pending' ) );
			}
		}

		/**
		 * Unset metas on placing order.
		 *
		 * @return void
		 * */
		public static function unset_metas_on_placing_order( $product, $order, $ticket_numbers, $update_question_ans_meta ) {
			// Unset reserved lottery tickets on placing order.
			self::unset_reserved_lottery_tickets_on_placing_order( $product, $ticket_numbers );

			if ( $update_question_ans_meta && ! is_admin() ) {
				$customer_id = lty_get_current_user_cart_session_value();
				// Unset question answer attempts on placing order.
				lty_unset_question_answer_metas( $product, $customer_id );
				// Delete the customer question viewed data.
				self::delete_customer_question_viewed_data( $product, $customer_id );
			}
		}

		/**
		 * Delete the customer question viewed data for the lottery.
		 *
		 * @since 6.7
		 *
		 * @param object $product
		 */
		public static function delete_customer_question_viewed_data( $product, $customer_id ) {
			$viewed_data = $product->get_lty_question_answer_viewed_data();
			if ( ! isset( $viewed_data[ $customer_id ] ) ) {
				return;
			}

			// Unset question answer viewed data.
			unset( $viewed_data[ $customer_id ] );

			$product->update_post_meta( 'lty_question_answer_viewed_data', $viewed_data );
		}

		/**
		 * Unset reserved lottery tickets on placing order.
		 *
		 * @return void
		 * */
		public static function unset_reserved_lottery_tickets_on_placing_order( $product, $ticket_numbers ) {
			// Return if not an reserved ticket.
			if ( ! lty_is_reserved_ticket( $product ) ) {
				return;
			}

			$reserved_tickets_data = $product->get_reserved_tickets_data();
			foreach ( $ticket_numbers as $ticket_number ) {

				if ( isset( $reserved_tickets_data[ $ticket_number ] ) ) {
					unset( $reserved_tickets_data[ $ticket_number ] );
				}
			}

			$product->update_post_meta( 'lty_manual_reserved_tickets', $reserved_tickets_data );
		}

		/**
		 * Update the lottery ticket data via Rest API.
		 *
		 * @since 10.4.0
		 * @param int $order_id
		 * */
		public static function update_lottery_ticket_via_rest_api( $order_id ) {
			// Return if Current request is not Rest API.
			if ( ! WC()->is_rest_api_request() ) {
				return;
			}

			$order = wc_get_order( $order_id );
			if ( ! is_object( $order ) ) {
				return;
			}

			self::update_lottery_ticket_in_order( $order_id, $order );
		}

		/**
		 * Update lottery ticket in order.
		 * */
		public static function update_lottery_ticket_in_order( $order_id, $order, $product_id = false, $manual_order = false ) {
			if ( ! is_object( $order ) ) {
				return;
			}

			if ( $order->get_meta( 'lty_lottery_ticket_updated_once' ) ) {
				return;
			}

			if ( $product_id ) {
				// Create the ticket before update ticket count.
				self::create_ticket_on_placing_order( $order_id, $product_id, false, true, $manual_order );
				$order = wc_get_order( $order_id );
			} else {
				// Create lottery ticket for order item.
				self::create_ticket_for_order_item( $order );
			}

			$ticket_ids = $order->get_meta( 'lty_ticket_ids_in_order' );
			if ( ! lty_check_is_array( $ticket_ids ) ) {
				return;
			}

			$ticket_data               = array();
			$instant_winner_ticket_ids = array();

			foreach ( $ticket_ids as $ticket_id ) {
				$ticket = lty_get_lottery_ticket( $ticket_id );
				if ( ! is_object( $ticket ) ) {
					continue;
				}

				// Continue if the ticket status already reached "ticket buyer".
				if ( $ticket->has_status( 'lty_ticket_buyer' ) ) {
					continue;
				}

				$product = $ticket->get_product();
				if ( ! lty_is_lottery_product( $product ) ) {
					continue;
				}

				// Validate product id.
				if ( $product_id && $product->get_id() != $product_id ) {
					continue;
				}

				if ( 'yes' !== $ticket->get_valid_answer() && 'lty_ticket_canceled' === $ticket->get_status() ) {
					/**
					 * This hook is used to do extra action when lottery incorrect answer in the order.
					 *
					 * @since 1.0.0
					 */
					do_action( 'lty_lottery_incorrect_answer_in_order', $product, $ticket );
					continue;
				}

				// Update ticket status.
				$ticket->update_status( 'lty_ticket_buyer' );

				// Update ticket count.
				$ticket_count = intval( $product->get_lty_ticket_count() );
				$product->update_post_meta( 'lty_ticket_count', $ticket_count + 1 );
				$ticket_data[ $product->get_id() ][ $ticket_id ] = $ticket->get_lottery_ticket_number();
				$instant_winner_log_id                           = lty_get_instant_winner_log_id_by_ticket_id( $ticket_id );

				if ( $instant_winner_log_id ) {
					$instant_winner_log = lty_get_instant_winner_log( $instant_winner_log_id );
					// Update log status.
					$instant_winner_log->update_status( 'lty_won' );
					$instant_winner_log->assign_winning_prize( $order );
					$instant_winner_ticket_ids[] = $ticket_id;
				}

				LTY_Transient_Handler::delete_all_transients( $product->get_id(), $ticket->get_user_id() );
			}

			// Update the ticket numbers in the order item.
			foreach ( $order->get_items() as $item ) {
				if ( ! lty_is_lottery_product( $item->get_product() ) ) {
					continue;
				}

				$ticket_numbers = self::maybe_update_lottery_tickets_order_item( $item, $order );
				if ( ! $ticket_numbers ) {
					continue;
				}

				$ticket_label = lty_get_order_item_ticket_number_name();
				// Read meta data from the database to bypass the object cache.
				if ( lty_check_is_array( $ticket_numbers ) && ! $item->meta_exists( $ticket_label ) ) {
					$item->read_meta_data( true );
				}

				if ( $item->meta_exists( $ticket_label ) ) {
					continue;
				}

				$item->add_meta_data( $ticket_label, '<span class="notranslate">' . implode( ', ', $ticket_numbers ) . '</span>' );
				$item->save();
			}
			/**
			 * This hook is used to do extra action after lottery ticket confirmed.
			 *
			 * @since 1.0
			 */
			do_action( 'lty_lottery_ticket_confirmed', $ticket_ids[0], $ticket_data, $order_id, $instant_winner_ticket_ids );

			$order->update_meta_data( 'lty_lottery_ticket_updated_once', '1' );
			$order->save();
		}

		/**
		 * May be update the lottery tickets in the order item meta.
		 *
		 * This function is used to overcome the issue of ticket numbers not being added to the ordered item when the user omits the order
		 * after proceeding to checkout using a third-party gateway then continue with the same order and place the order.
		 *
		 * @since 8.4.0
		 * @param object $item
		 * @param object $order
		 * @return array
		 */
		public static function maybe_update_lottery_tickets_order_item( $item, $order ) {
			$ticket_numbers = $item->get_meta( '_lty_lottery_tickets' );
			if ( $ticket_numbers ) {
				return $ticket_numbers;
			}

			$ticket_numbers = lty_get_ticket_numbers( $item->get_product(), false, 'all', $order->get_id() );

			$item->add_meta_data( '_lty_lottery_tickets', $ticket_numbers );
			$item->save();

			return $ticket_numbers;
		}

		/**
		 * Adjust the order item meta.
		 *
		 * @return void
		 * */
		public static function adjust_order_item( $item, $cart_item_key, $values, $order ) {
			if ( ! isset( $values['lty_lottery'] ) ) {
				return;
			}

			$product = wc_get_product( $values['product_id'] );
			if ( ! is_object( $product ) ) {
				return;
			}

			if ( 'lottery' !== $product->get_type() ) {
				return;
			}

			if ( isset( $values['lty_lottery']['tickets'] ) ) {
				// Update order item meta.
				$item->add_meta_data( '_lty_lottery_tickets', $values['lty_lottery']['tickets'] );
				$ticket_label = lty_get_order_item_ticket_number_name();
				/**
				 * This hook is used to validate the adding the ticket numbers in the order item.
				 *
				 * @since 6.8.0
				 */
				if ( ! $item->meta_exists( $ticket_label ) && apply_filters( 'lty_validate_order_item_ticket_numbers', lty_can_show_tickets_non_paid_order( $product, $order ), $product ) ) {
					$item->add_meta_data( $ticket_label, '<span class="notranslate">' . implode( ', ', $values['lty_lottery']['tickets'] ) . '</span>' );
				}
			}

			if ( isset( $values['lty_lottery']['answers'] ) ) {
				$answers = $product->get_answers();
				if ( array_key_exists( $values['lty_lottery']['answers'], $answers ) ) {
					// Update the order item meta.
					$item->add_meta_data( '_lty_lottery_answers', array( $values['lty_lottery']['answers'] ) );
					$item->add_meta_data( __( 'Chosen Answer', 'lottery-for-woocommerce' ), $answers[ $values['lty_lottery']['answers'] ]['label'] );
				}
			}
		}

		/**
		 * Hidden Custom Order item meta.
		 * */
		public static function hide_order_item_meta_key( $hidden_order_itemmeta ) {
			$custom_order_itemmeta = array( '_lty_lottery_tickets', '_lty_is_instant_win_lottery', '_lty_is_instant_win_gift_product', '_lty_gift_product_instant_win_log_id' );

			return array_merge( $hidden_order_itemmeta, $custom_order_itemmeta );
		}

		/**
		 * Remove lottery ticket for the order after order failed.
		 *
		 * @since 1.1.0
		 * @param int $order_id
		 * */
		public static function remove_lottery_ticket_for_order_cancel( $order_id ) {
			$order = wc_get_order( $order_id );
			if ( ! is_object( $order ) ) {
				return;
			}

			if ( ! $order->get_meta( 'lty_lottery_ticket_created_once' ) ) {
				return;
			}

			$ticket_ids = $order->get_meta( 'lty_ticket_ids_in_order' );
			if ( ! lty_check_is_array( $ticket_ids ) ) {
				return;
			}

			foreach ( $ticket_ids as $ticket_id ) {
				$ticket = lty_get_lottery_ticket( $ticket_id );
				if ( ! is_object( $ticket ) || ! is_object( $ticket->get_product() ) ) {
					continue;
				}

				$ticket_count = intval( $ticket->get_product()->get_lty_ticket_count() );
				// Update ticket count.
				$ticket->get_product()->update_post_meta( 'lty_ticket_count', $ticket_count - 1 );

				$hold_tickets = array_filter( (array) get_post_meta( $ticket->get_product_id(), '_lty_hold_tickets', true ) );
				$index        = array_search( $ticket->get_lottery_ticket_number(), $hold_tickets );
				unset( $hold_tickets[ $index ] );
				// Update Hold Tickets meta.
				update_post_meta( $ticket->get_product_id(), '_lty_hold_tickets', $hold_tickets );

				// Change the status to available when the order is rejected.
				$instant_winner_log_id = lty_get_instant_winner_log_id_by_ticket_id( $ticket_id );
				if ( $instant_winner_log_id ) {
					$instant_winner_log = lty_get_instant_winner_log( $instant_winner_log_id );
					if ( $instant_winner_log->exists() ) {
						// Remove won prize if already assigned.
						if ( $instant_winner_log->has_status( 'lty_won' ) ) {
							$instant_winner_log->remove_won_prize();
						}

						$instant_winner_log->remove_instant_winner();
						$instant_winner_log->update_status( 'lty_available' );
					}
				}

				lty_delete_lottery_ticket( $ticket_id );
				LTY_Transient_Handler::delete_all_transients( $ticket->get_product_id(), $ticket->get_user_id() );
			}

			// Delete tickets meta in order item.
			self::delete_tickets_meta_in_order_item( $order_id );

			// Delete the post meta.
			$order->delete_meta_data( 'lty_ticket_ids_in_order' );
			$order->delete_meta_data( 'lty_lottery_ticket_created_once' );
			$order->delete_meta_data( 'lty_lottery_ticket_updated_once' );
			$order->save();
		}

		/**
		 * Delete Tickets meta in Order item.
		 *
		 * @return void
		 * */
		public static function delete_tickets_meta_in_order_item( $order_id ) {
			$order = wc_get_order( $order_id );
			if ( ! is_object( $order ) ) {
				return;
			}

			foreach ( $order->get_items() as $item ) {
				if ( ! lty_is_lottery_product( $item->get_product() ) || $item->get_product()->is_manual_ticket() ) {
					continue;
				}

				// Delete order item meta for automatic type.
				$item->delete_meta_data( '_lty_lottery_tickets' );
				$item->delete_meta_data( lty_get_order_item_ticket_number_name() );
				$item->save();
			}
		}

		/**
		 * Alter order item quantity to manage stock.
		 *
		 * @return int
		 * */
		public static function alter_order_item_quantity( $qty, $order, $order_item ) {
			if ( ! self::validate_question_answer_in_order( $order_item ) ) {
				return 0;
			}

			return $qty;
		}

		/**
		 * Prevent adjust line item product stock.
		 *
		 * @return bool
		 * */
		public static function prevent_adjust_line_item_product_stock( $bool, $order_item ) {
			if ( ! self::validate_question_answer_in_order( $order_item ) ) {
				return true;
			}

			return false;
		}

		/**
		 * Validate question answer in order.
		 *
		 * @return bool
		 * */
		public static function validate_question_answer_in_order( $order_item ) {
			if ( ! is_object( $order_item ) ) {
				return true;
			}

			$product = $order_item->get_product();
			if ( ! lty_is_lottery_product( $product ) || $product->is_closed() ) {
				return true;
			}

			if ( ! isset( $order_item['lty_lottery_answers'][0] ) ) {
				return true;
			}

			$question_answers = $product->get_answers();
			if ( ! array_key_exists( $order_item['lty_lottery_answers'][0], $question_answers ) ) {
				return true;
			}

			$is_valid_answer = $question_answers[ $order_item['lty_lottery_answers'][0] ]['valid'];
			if ( 'yes' !== $is_valid_answer && $product->is_valid_question_answer() && $product->restrict_incorrectly_selected_answer() ) {
				return false;
			}

			return true;
		}

		/**
		 * Display the lottery message in the order item.
		 *
		 * @since 6.9.0
		 * @param int    $item_id
		 * @param object $item
		 * @param object $object
		 * @return void
		 */
		public static function order_item_meta( $item_id, $item, $object ) {
			$ticket_label = lty_get_order_item_ticket_number_name();
			if ( $item->meta_exists( $ticket_label ) ) {
				return;
			}

			$product = $item->get_product();
			if ( ! lty_is_lottery_product( $product ) ) {
				return;
			}

			if ( lty_can_show_tickets_non_paid_order( $product, $object ) ) {
				return;
			}

			$html = '<p class="lty_lottery_info">' . lty_get_ticket_pending_payment_message() . '</p>';
			echo wp_kses_post( $html );
		}

		/**
		 * Render lottery order item meta.
		 *
		 * @since 11.5.0
		 * @param int|string $item_id Item ID.
		 * @param object     $item Order Item object.
		 * @param object     $order Order object.
		 * @return void
		 */
		public static function render_lottery_order_item_meta( $item_id, $item, $order ) {
			// Render the instant win gift product message.
			self::render_instant_win_gift_product_message( $item_id, $item, $order );
			// Render the lottery ticket pdf download button.
			self::render_lottery_ticket_pdf_download_button( $item_id, $item, $order );
		}

		/**
		 * Render lottery ticket pdf download button.
		 *
		 * @since 9.5.0
		 * @param int|string $item_id Item ID.
		 * @param object     $item Order Item object.
		 * @param object     $order Order object.
		 * @return void
		 */
		public static function render_lottery_ticket_pdf_download_button( $item_id, $item, $order ) {
			if ( 'yes' !== get_option( 'lty_settings_download_lottery_ticket_pdf', 'no' ) ) {
				return;
			}

			if ( ! lty_check_is_array( $item->get_meta( '_lty_lottery_tickets' ) ) ) {
				return;
			}

			if ( ! $item->meta_exists( lty_get_order_item_ticket_number_name() ) ) {
				return;
			}

			$url = esc_url(
				add_query_arg(
					array(
						'action'  => 'lty-download',
						'lty_key' => lty_encode(
							array(
								'lty_lottery_id' => $item->get_product()->get_id(),
								'lty_order_id'   => $item->get_order_id(),
							),
							true
						),
					),
					get_site_url()
				)
			);

			lty_get_template( 'pdf/lottery-ticket-download-button.php', array( 'url' => $url ) );
		}

		/**
		 * Render the instant win gift product message.
		 *
		 * @since 1.5.0
		 * @param int|string $item_id Item ID.
		 * @param object     $item Order Item object.
		 * @param object     $order Order object.
		 * @return void
		 */
		public static function render_instant_win_gift_product_message( $item_id, $item, $order ) {
			if ( 'yes' !== $item->get_meta( '_lty_is_instant_win_gift_product' ) ) {
				return;
			}

			$instant_winner_log_id = $item->get_meta( '_lty_gift_product_instant_win_log_id' );
			if ( ! $instant_winner_log_id ) {
				return;
			}

			$instant_winner_log = lty_get_instant_winner_log( $instant_winner_log_id );
			if ( ! $instant_winner_log->exists() ) {
				return;
			}

			if ( ( is_checkout() || lty_is_block_checkout() ) && is_wc_endpoint_url( 'order-received' ) ) { // Thankyou page.
				lty_get_template( 'thankyou/instant-win-gift-product-message.php', array( 'message' => lty_get_thankyou_page_instant_win_gift_product_message( $instant_winner_log ) ) );
			} else { // Order details page.
				lty_get_template( 'order/instant-win-gift-product-message.php', array( 'message' => lty_get_order_details_page_instant_win_gift_product_message( $instant_winner_log ) ) );
			}
		}

		/**
		 * Render the order instant winners.
		 *
		 * @since 10.9.0
		 * @param object $order Order object.
		 * @return void
		 */
		public static function render_order_instant_winners( $order ) {
			if ( 'no' === get_option( 'lty_settings_display_instant_winners_on_order', 'no' ) ) {
				return;
			}

			// Return if the order don't have the lottery ticket IDs.
			if ( ! is_object( $order ) || ! lty_check_is_array( $order->get_meta( 'lty_ticket_ids_in_order' ) ) ) {
				return;
			}

			if ( ! self::is_order_ticket_payment_processed( $order ) ) {
				return;
			}

			if ( ( is_checkout() || lty_is_block_checkout() ) && is_wc_endpoint_url( 'order-received' ) ) { // Thankyou page.
				self::render_thankyou_page_instant_winners( $order );
			} else { // Order details page.
				self::render_order_details_page_instant_winners( $order );
			}
		}

		/**
		 * Render the order instant winners on thank you page.
		 *
		 * @since 11.4.0
		 * @param object $order Order object.
		 * @return void
		 */
		public static function render_thankyou_page_instant_winners( $order ) {
			$instant_winner_log_ids = lty_get_instant_winner_log_ids_by_order_id( $order->get_id(), false, 'lty_won' );
			if ( lty_check_is_array( $instant_winner_log_ids ) ) {
				$post_per_page = 20;
				$current_page  = 1;
				$offset        = ( $post_per_page * $current_page ) - $post_per_page;
				$page_count    = ceil( count( $instant_winner_log_ids ) / $post_per_page );

				lty_get_template(
					'thankyou/instant-winners-layout.php',
					array(
						'columns'                => lty_get_order_instant_winners_columns(),
						'instant_winner_log_ids' => array_slice( $instant_winner_log_ids, $offset, $post_per_page ),
						'order_id'               => $order->get_id(),
						'pagination'             => lty_prepare_pagination_arguments( $current_page, $page_count ),
					)
				);
			} elseif ( lty_is_order_contains_instant_win_lottery( $order ) ) {
				lty_get_template( 'thankyou/instant-win-better-luck-message.php', array( 'message' => lty_get_thankyou_page_instant_win_better_luck_message() ) );
			}
		}

		/**
		 * Render the order instant winners on order details page.
		 *
		 * @since 11.4.0
		 * @param object $order Order object.
		 * @return void
		 */
		public static function render_order_details_page_instant_winners( $order ) {
			$instant_winner_log_ids = lty_get_instant_winner_log_ids_by_order_id( $order->get_id(), false, 'lty_won' );
			if ( lty_check_is_array( $instant_winner_log_ids ) ) {
				$post_per_page = 20;
				$current_page  = 1;
				$offset        = ( $post_per_page * $current_page ) - $post_per_page;
				$page_count    = ceil( count( $instant_winner_log_ids ) / $post_per_page );

				lty_get_template(
					'order/instant-winners-layout.php',
					array(
						'columns'                => lty_get_order_instant_winners_columns(),
						'instant_winner_log_ids' => array_slice( $instant_winner_log_ids, $offset, $post_per_page ),
						'order_id'               => $order->get_id(),
						'pagination'             => lty_prepare_pagination_arguments( $current_page, $page_count ),
					)
				);
			} elseif ( lty_is_order_contains_instant_win_lottery( $order ) ) {
				lty_get_template( 'order/instant-win-better-luck-message.php', array( 'message' => lty_get_order_details_page_instant_win_better_luck_message() ) );
			}
		}

		/**
		 * Is order ticket payment processed?
		 *
		 * @since 11.4.0
		 * @param object $order Order object.
		 * @return bool
		 */
		public static function is_order_ticket_payment_processed( $order ) {
			if ( ! is_object( $order ) ) {
				return false;
			}

			foreach ( $order->get_items() as $item ) {
				if ( ! lty_is_lottery_product( $item->get_product() ) ) {
					continue;
				}

				if ( $item->meta_exists( __( 'Ticket Number( s )', 'lottery-for-woocommerce' ) ) ) {
					return true;
				}
			}

			return false;
		}
	}

	LTY_Order_Handler::init();
}
