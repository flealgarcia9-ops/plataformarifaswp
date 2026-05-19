<?php

/**
 * Handles Order Items Generation of Ticket.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


if ( ! class_exists( 'LTY_Order_Item_Generate_Tickets' ) ) {

	/**
	 * LTY_Order_Item_Generate_Tickets.
	 */
	class LTY_Order_Item_Generate_Tickets {

		public static function init() {
			// Render the ticket generation button.
			add_action( 'woocommerce_order_item_line_item_html', array( __CLASS__, 'render_ticket_generation_button' ), 10, 3 );
			// Display ticket tab content.
			add_action( 'lty_lottery_ticket_tab_content_order_item', array( __CLASS__, 'display_ticket_tab_content' ), 10, 1 );
			// Render the question answer content.
			add_action( 'lty_after_lottery_ticket_order_item', array( __CLASS__, 'render_question_answer_content' ), 10, 1 );
		}

		/**
		 * Render the ticket generation button.
		 */
		public static function render_ticket_generation_button( $item_id, $item, $order ) {
			if ( ! is_object( $order ) ) {
				return;
			}

			$product = $item->get_product();
			// Return if not an object or not a lottery product.
			if ( ! lty_is_lottery_product( $product ) || $product->is_closed() ) {
				return;
			}

			// Return if order status not matched.
			if ( ! in_array( $order->get_status(), (array) get_option( 'lty_settings_lottery_complete_order_statuses' ) ) ) {
				return;
			}

			$show_button            = false;
			$manual_generation_args = array(
				'class_name'          => 'lty-generate-automatic-ticket',
				'button_name'         => __( 'Assign Ticket(s)', 'lottery-for-woocommerce' ),
				'show_question_modal' => false,
				'answer_modal_id'     => 'lty-question-answer-modal',
			);

			// Show button if the tickets are not assigned for the item.
			if ( ! isset( $item['lty_lottery_tickets'] ) || ! lty_check_is_array( $item['lty_lottery_tickets'] ) ) {
				$manual_generation_args['class_name']  = $product->is_manual_ticket() ? 'lty-manual-tickets-popup-btn' : 'lty-automatic-tickets-popup-btn';
				$manual_generation_args['button_name'] = $product->is_manual_ticket() ? __( 'Select Ticket(s)', 'lottery-for-woocommerce' ) : __( 'Assign Ticket(s)', 'lottery-for-woocommerce' );
				$show_button = true;
				// Show button if the tickets are assigned but the answer is not selected.
			} elseif ( $product->is_valid_question_answer() && ( ! isset( $item['lty_lottery_answers'] ) || empty( $item['lty_lottery_answers'] ) ) ) {
				$manual_generation_args['class_name']  = 'lty-question-answer-popup-btn';
				$manual_generation_args['button_name'] = __( 'Choose Answer', 'lottery-for-woocommerce' );
				$manual_generation_args['show_question_modal'] = true;
				$show_button                           = true;
			}

			if ( ! $show_button ) {
				return;
			}

			include LTY_ABSPATH . 'inc/admin/menu/views/order-item/html-lottery-button.php';
		}

		/**
		 * Display ticket tab content.
		 */
		public static function display_ticket_tab_content( $product ) {
			$start_range = $product->get_ticket_start_number();
			$end_range   = ( $product->get_lty_tickets_per_tab() > $product->get_lty_maximum_tickets() ) ? $product->get_lty_maximum_tickets() : $product->get_lty_tickets_per_tab();
			$end_range   = $end_range + $product->get_ticket_start_number() - 1;

			// Prepare ticket numbers based on start range and end range.
			$ticket_numbers = range( $start_range, $end_range );
			// Shuffle ticket numbers.
			if ( '2' === $product->get_lty_tickets_per_tab_display_type() ) {
				shuffle( $ticket_numbers );
			}

			if ( $product->is_sold_all_tickets( $ticket_numbers ) ) {
				printf( '<span class="lty-all-tickets-sold">%s</span>', wp_kses_post( lty_get_user_chooses_ticket_all_tickets_sold_label() ) );
				return;
			}

			$tickets_args = array(
				'product'          => $product,
				'sold_tickets'     => $product->get_placed_tickets(),
				'cart_tickets'     => array(),
				'reserved_tickets' => $product->get_reserved_tickets(),
				'ticket_numbers'   => $ticket_numbers,
				'index'            => 0,
				'view_more'        => ( 'yes' === $product->get_lty_view_more_tickets_per_tab() ) ? $product->get_lty_tickets_per_tab_view_more_count() : false,
			);

			lty_get_template( 'single-product/ticket-tab-content.php', $tickets_args );
		}

		/**
		 * Render the question answer content.
		 *
		 * @since 7.3
		 * @param object $product
		 */
		public static function render_question_answer_content( $product ) {
			// Return if the question answer is not valid.
			if ( ! $product->is_valid_question_answer() ) {
				return;
			}

			$question_answers = $product->get_question_answers();
			$question         = reset( $question_answers );

			include LTY_ABSPATH . 'inc/admin/menu/views/order-item/question-answer.php';
		}
	}

	LTY_Order_Item_Generate_Tickets::init();
}
