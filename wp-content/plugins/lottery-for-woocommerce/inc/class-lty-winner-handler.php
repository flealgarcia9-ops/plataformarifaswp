<?php
/**
 * Handles the Winner.
 *
 * @since 1.0.0
 * */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'LTY_Lottery_Winner' ) ) {

	/**
	 * Class.
	 * */
	class LTY_Lottery_Winner {

		/**
		 * Handle lottery winner.
		 *
		 * @param array  $ticket_ids ticket ids.
		 * @param object $lottery_product lottery product object.
		 * @param string $closed_type closed type.
		 * */
		public static function handle_lottery_winner( $ticket_ids, $lottery_product, $closed_type ) {
			if ( ! lty_check_is_array( $ticket_ids ) || ! lty_is_lottery_product( $lottery_product ) ) {
				return;
			}

			$lottery_product_id = $lottery_product->get_id();
			if ( 'lty_lottery_finished' === $lottery_product->get_lty_lottery_status() ) {
				return;
			}

			$winner_ids = array();
			foreach ( $ticket_ids as $ticket_id ) {
				$ticket = lty_get_lottery_ticket( $ticket_id );
				if ( 'yes' === $lottery_product->get_lty_lottery_unique_winners() && $lottery_product->has_user_already_winner( $ticket ) ) {
					continue;
				}

				// Create manual order for winners.
				$order_id = self::create_order_for_winners( $ticket->get_user_id(), $lottery_product, $ticket );
				// Create winner logs.
				$winner_ids[] = self::create_winner_logs( $order_id, $lottery_product, $ticket );
			}

			if ( $lottery_product->get_lty_winners_count() <= count( $lottery_product->get_current_winner_ids() ) ) {
				// Update lottery closed meta's.
				$lottery_product->update_post_meta( 'lty_closed', $closed_type );
				$lottery_product->update_post_meta( 'lty_lottery_status', 'lty_lottery_finished' );

				// Update lottery finished date.
				$lottery_product->update_post_meta( 'lty_finished_date', current_time( 'mysql' ) );
				$lottery_product->update_post_meta( 'lty_finished_date_gmt', current_time( 'mysql', true ) );
			}

			/**
			 * This hook is used to do extra action after lottery finished.
			 *
			 * @since 1.0
			 */
			do_action( 'lty_lottery_product_after_finished', $lottery_product_id );
		}

		/**
		 * Create order for winners.
		 *
		 * @param string $user_id user id
		 * @param object $lottery_product lottery product object.
		 * @param object $ticket lottery ticket object.
		 * */
		public static function create_order_for_winners( $user_id, $lottery_product, $ticket ) {
			$winner_gift_product_ids = $lottery_product->get_selected_gift_products();
			if ( ! lty_check_is_array( $winner_gift_product_ids ) ) {
				return '';
			}

			// create order function.
			// set order status and user id in $args.
			$order = wc_create_order(
				array(
					'status'      => 'pending',
					'customer_id' => $user_id,
				)
			);

			// set billing address.
			self::set_address_details( $order, $user_id, 'billing', $ticket->get_order() );
			// set shipping address.
			self::set_address_details( $order, $user_id, 'shipping', $ticket->get_order() );

			// Add gift products to orders.
			foreach ( $winner_gift_product_ids as $gift_product_id ) {
				$gift_product = wc_get_product( $gift_product_id );
				// params product object,quantity,total,subtotal.
				$order->add_product(
					$gift_product,
					1,
					array(
						'total'    => 0,
						'subtotal' => 0,
					)
				);
			}

			// save order object.
			$order->save();
			// Update Default Order status.
			$order->update_status( 'wc-processing' );

			return $order->get_id();
		}

		/**
		 * Set address details.
		 *
		 * @param object $order order object.
		 * @param string $user_id user id.
		 * @param string $type address for billing/shipping.
		 * */
		public static function set_address_details( &$order, $user_id, $type, $ticket_order ) {
			$data = array(
				'first_name' => array( 'billing', 'shipping' ),
				'last_name'  => array( 'billing', 'shipping' ),
				'company'    => array( 'billing', 'shipping' ),
				'address_1'  => array( 'billing', 'shipping' ),
				'address_2'  => array( 'billing', 'shipping' ),
				'city'       => array( 'billing', 'shipping' ),
				'postcode'   => array( 'billing', 'shipping' ),
				'country'    => array( 'billing', 'shipping' ),
				'state'      => array( 'billing', 'shipping' ),
				'email'      => array( 'billing' ),
				'phone'      => array( 'billing', 'shipping' ),
			);

			// get billing and shipping details.
			$value = lty_get_address( $user_id, $type, $ticket_order );
			foreach ( $data as $key => $applicable_to ) {
				if ( is_callable( array( $order, "set_{$type}_{$key}" ) ) ) {
					$order->{"set_{$type}_{$key}"}( $value[ $key ] );
				}
			}
		}

		/**
		 * Create winner logs.
		 *
		 * @param string $order_id order id.
		 * @param object $lottery_product lottery product object.
		 * @param object $ticket ticket object.
		 * */
		public static function create_winner_logs( $order_id, $lottery_product, $ticket ) {
			$gift_products = $lottery_product->get_selected_gift_products();
			if ( $order_id ) {
				$order = wc_get_order( $order_id );
				if ( is_object( $order ) ) {
					foreach ( $order->get_items() as $key => $value ) {
						$product_id      = ( ! empty( $value['variation_id'] ) ) ? $value['variation_id'] : $value['product_id'];
						$gift_products[] = $product_id;
					}
				}
			}

			$user_id                 = $ticket->get_user_id();
			$user                    = get_user_by( 'ID', $user_id );
			$updated_lottery_product = wc_get_product( $lottery_product->get_id() );
			$meta_data               = array(
				'lty_user_id'        => $user_id,
				'lty_gift_products'  => (array) $gift_products,
				'lty_user_name'      => $ticket->get_user_name(),
				'lty_user_email'     => $ticket->get_user_email(),
				'lty_order_id'       => $order_id,
				'lty_ticket_number'  => $ticket->get_lottery_ticket_number(),
				'lty_answer'         => $ticket->get_answer(),
				'lty_answers'        => $ticket->get_answers(),
				'lty_valid_answer'   => $ticket->get_valid_answer(),
				'lty_start_date'     => $updated_lottery_product->get_lty_start_date(),
				'lty_start_date_gmt' => $updated_lottery_product->get_lty_start_date_gmt(),
				'lty_end_date'       => $updated_lottery_product->get_lty_end_date(),
				'lty_end_date_gmt'   => $updated_lottery_product->get_lty_end_date_gmt(),
				'lty_winning_method' => $updated_lottery_product->get_winner_product_selection_method(),
				'lty_list_count'     => $updated_lottery_product->get_lty_list_count(),
			);

			$ticket->update_status( 'lty_ticket_winner' );

			return lty_create_new_lottery_winner( $meta_data, array( 'post_parent' => $lottery_product->get_lottery_id() ) );
		}
	}

}
