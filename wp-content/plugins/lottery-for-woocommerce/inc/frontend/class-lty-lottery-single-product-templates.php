<?php
/**
 * Lottery Single Product Templates.
 *
 * @since 1.0.0
 * */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'LTY_Lottery_Single_Product_Templates' ) ) {

	/**
	 * Class.
	 *
	 * @since 1.0.0
	 * */
	class LTY_Lottery_Single_Product_Templates {

		/**
		 * Class initialization.
		 *
		 * @since 1.0.0
		 * */
		public static function init() {
			// Render product summary Template.
			add_action( 'woocommerce_lottery_add_to_cart', array( __CLASS__, 'render_product_summary_template' ) );
			// Render add to cart button template.
			add_action( 'woocommerce_lottery_add_to_cart', array( __CLASS__, 'render_add_to_cart_template' ), 20 );
			// Render the question answer timer.
			add_action( 'lty_before_lottery_question_content', array( __CLASS__, 'render_question_answer_timer_template' ), 30 );
			// Render the question answer template.
			add_action( 'woocommerce_before_add_to_cart_button', array( __CLASS__, 'render_question_answer_template' ), 10 );
			// Render tickets summary template.
			add_action( 'woocommerce_before_add_to_cart_button', array( __CLASS__, 'render_ticket_summary_template' ), 20 );
			// Render predefined buttons template.
			add_action( 'woocommerce_before_add_to_cart_button', array( __CLASS__, 'render_predefined_buttons_template' ), 30 );
			// Render the ticket tab content template.
			add_action( 'lty_lottery_ticket_tab_content', array( __CLASS__, 'render_ticket_tab_content' ) );
			// Render the ticket lucky dip template.
			add_action( 'lty_before_lottery_ticket', array( __CLASS__, 'render_ticket_lucky_dip' ), 30 );
			// Render manual ticket search template.
			add_action( 'lty_before_lottery_ticket', array( __CLASS__, 'render_manual_ticket_search' ), 40 );

			// Add lottery summary hooks.
			self::add_lottery_summary_hooks();
		}

		/**
		 * Add lottery summary hooks.
		 *
		 * @since 1.0.0
		 * */
		public static function add_lottery_summary_hooks() {
			/**
			 * This hook is used to alter the lottery summary hooks.
			 *
			 * @since 1.0.0
			 */
			$hooks = apply_filters(
				'lty_lottery_summary_hooks',
				array(
					'tickets_status'                  => 5,
					'date_notice'                     => 10,
					'countdown_timer'                 => 15,
					'failed_reason_notice'            => 20,
					'minimum_tickets_notice'          => 25,
					'maximum_tickets_notice'          => 30,
					'minimum_tickets_per_user_notice' => 35,
					'maximum_tickets_per_user_notice' => 40,
					'gift_product_notice'             => 45,
					'waiting_for_result'              => 50,
					'tickets_sold_notice'             => 55,
					'progress_bar'                    => 60,
					'winner_message'                  => 65,
					'loser_message'                   => 70,
					'guest_error_notice'              => 75,
					'winners_count'                   => 80,
					'winner_log'                      => 85,
				)
			);

			if ( ! lty_check_is_array( $hooks ) ) {
				return $hooks;
			}

			foreach ( $hooks as $hook_name => $priority ) {
				/**
				 * This hook is used to alter the lottery summary display hook.
				 *
				 * @since 7.1.0
				 */
				$display_hook = apply_filters( 'lty_lottery_fields_display_hook', 'lty_lottery_single_product_content' );

				// Render template.
				add_action( $display_hook, array( __CLASS__, 'render_' . $hook_name . '_template' ), intval( $priority ), 1 );
			}
		}

		/**
		 * Render product summary template.
		 *
		 * @since 1.0.0
		 * @global object $product Product object.
		 * @return void
		 */
		public static function render_product_summary_template() {
			global $product;
			if ( ! lty_is_lottery_product( $product ) ) {
				return;
			}

			lty_get_template( 'single-product/product-summary.php', array( 'product' => $product ) );
		}

		/**
		 * Render add to cart button template.
		 *
		 * @since 1.0.0
		 * @global object $product Product object.
		 * @return void
		 */
		public static function render_add_to_cart_template() {
			global $product;

			if ( ! lty_is_lottery_product( $product ) ) {
				return;
			}

			lty_get_template( 'single-product/add-to-cart/lottery.php' );
		}

		/**
		 * Render ticket status template.
		 *
		 * @since 1.0.0
		 * @param object $product Product object.
		 * @return void
		 */
		public static function render_tickets_status_template( $product ) {
			if ( ! lty_is_lottery_product( $product ) ) {
				return;
			}

			if ( 'yes' === get_option( 'lty_settings_hide_lottery_status_in_single_product_page' ) ) {
				return;
			}

			lty_get_template( 'single-product/ticket-status.php', array( 'product' => $product ) );
		}

		/**
		 * Render date notice template.
		 *
		 * @since 9.2.0
		 * @param object $product Product object.
		 * @return void
		 */
		public static function render_date_notice_template( $product ) {
			if ( ! lty_is_lottery_product( $product ) ) {
				return;
			}

			if ( ! $product->display_countdown_timer_in_single_product() || ! $product->can_display_lottery_details() ) {
				return;
			}

			lty_get_template( 'single-product/date-notice.php', array( 'product' => $product ) );
		}

		/**
		 * Render countdown timer template.
		 *
		 * @since 9.2.0
		 * @param object $product Product object.
		 * @return void
		 */
		public static function render_countdown_timer_template( $product ) {
			if ( ! lty_is_lottery_product( $product ) ) {
				return;
			}

			if ( ! $product->is_valid_to_display_countdown_timer_in_product_page() ) {
				return;
			}

			lty_get_template( 'single-product/countdown-timer.php', array( 'product' => $product ) );
		}

		/**
		 * Render minimum tickets notice template.
		 *
		 * @since 1.0.0
		 * @param object $product Product object.
		 * @return void
		 */
		public static function render_minimum_tickets_notice_template( $product ) {
			if ( ! lty_is_lottery_product( $product ) || lty_hide_minimum_lottery_ticket_message() ) {
				return;
			}

			if ( ! $product->can_display_lottery_details() ) {
				return;
			}

			lty_get_template( 'single-product/minimum-ticket-notice.php', array( 'product' => $product ) );
		}

		/**
		 * Render maximum tickets notice template.
		 *
		 * @since 1.0.0
		 * @param object $product Product object.
		 * @return void
		 */
		public static function render_maximum_tickets_notice_template( $product ) {
			if ( ! lty_is_lottery_product( $product ) || lty_hide_maximum_lottery_ticket_message() ) {
				return;
			}

			if ( ! $product->can_display_lottery_details() ) {
				return;
			}

			lty_get_template( 'single-product/maximum-ticket-notice.php', array( 'product' => $product ) );
		}

		/**
		 * Render minimum tickets per user notice template.
		 *
		 * @since 1.0.0
		 * @param object $product Product object.
		 * @return void
		 */
		public static function render_minimum_tickets_per_user_notice_template( $product ) {
			if ( ! lty_is_lottery_product( $product ) || ! absint( $product->get_lty_user_minimum_tickets() ) ) {
				return;
			}

			if ( 'yes' === get_option( 'lty_settings_hide_minimum_tickets_per_user_info_in_single_product_page' ) ) {
				return;
			}

			if ( '3' == get_option( 'lty_settings_guest_user_participate_type' ) ) {
				return;
			}

			if ( ! $product->can_display_lottery_details() ) {
				return;
			}

			lty_get_template( 'single-product/minimum-tickets-per-user-notice.php', array( 'product' => $product ) );
		}

		/**
		 * Render maximum tickets per user notice template.
		 *
		 * @since 1.0.0
		 * @param object $product Product object.
		 * @return void
		 */
		public static function render_maximum_tickets_per_user_notice_template( $product ) {
			if ( ! lty_is_lottery_product( $product ) || ! wp_get_current_user()->exists() || $product->user_purchase_limit_exists() ) {
				return;
			}

			if ( 'yes' === get_option( 'lty_settings_hide_maximum_tickets_per_user_single_product' ) ) {
				return;
			}

			if ( '3' == get_option( 'lty_settings_guest_user_participate_type' ) ) {
				return;
			}

			if ( ! $product->can_display_lottery_details() ) {
				return;
			}

			lty_get_template( 'single-product/maximum-tickets-per-user-notice.php', array( 'product' => $product ) );
		}

		/**
		 * Render failed reason notice template.
		 *
		 * @since 1.0.0
		 * @param object $product Product object.
		 * @return void
		 */
		public static function render_failed_reason_notice_template( $product ) {
			if ( ! lty_is_lottery_product( $product ) || ! $product->has_lottery_status( 'lty_lottery_failed' ) ) {
				return;
			}

			$args = array(
				'failed_reason' => lty_display_failed_reason( $product->get_lty_failed_reason(), true ),
			);

			lty_get_template( 'single-product/failed-reason-notice.php', $args );
		}

		/**
		 * Render Gift Product notice template.
		 *
		 * @since 1.0.0
		 * @param object $product Product object.
		 * @return void
		 */
		public static function render_gift_product_notice_template( $product ) {
			if ( ! lty_is_lottery_product( $product ) || $product->is_closed() ) {
				return;
			}

			$product_name = lty_get_lottery_gift_products( false, $product, true );
			if ( '2' == $product->get_winner_product_selection_method() ) {
				$gift_product_message = str_replace( '{gift_details_URL}', $product_name, get_option( 'lty_settings_single_product_outside_lottery_gift_message' ) );
			} else {
				$gift_product_message = str_replace( '{gift_product}', $product_name, get_option( 'lty_settings_single_product_lottery_gift_message' ) );
			}

			lty_get_template( 'single-product/gift-product-notice.php', array( 'gift_product_message' => $gift_product_message ) );
		}

		/**
		 * Render tickets sold notice template.
		 *
		 * @since 1.0.0
		 * @param object $product Product object.
		 * @return void
		 */
		public static function render_tickets_sold_notice_template( $product ) {
			if ( ! lty_is_lottery_product( $product ) ) {
				return;
			}

			if ( 'yes' === get_option( 'lty_settings_hide_lottery_tickets_sold_in_single_product_page' ) ) {
				return;
			}

			if ( ! $product->can_display_lottery_details() ) {
				return;
			}

			$ticket_sold_notice = get_option( 'lty_settings_single_product_current_ticket_sold_message' ) . ' : <b>' . $product->get_purchased_ticket_count() . '</b>';

			lty_get_template( 'single-product/ticket-sold-notice.php', array( 'ticket_sold_notice' => $ticket_sold_notice ) );
		}

		/**
		 * Render the progress bar template.
		 *
		 * @since 1.0.0
		 * @param object $product Product object.
		 * @return void
		 */
		public static function render_progress_bar_template( $product ) {
			if ( ! lty_is_lottery_product( $product ) ) {
				return;
			}

			if ( ! $product->display_progress_bar_in_single_product() || ! $product->can_display_lottery_details() ) {
				return;
			}

			$args = array(
				'product'                 => $product,
				'progress_bar_percentage' => lty_get_product_page_progress_bar_percentage( $product ),
			);

			lty_get_template( 'single-product/progress-bar.php', $args );
		}

		/**
		 * Render Waiting for Result notice template.
		 *
		 * @since 1.0.0
		 * @param object $product Product object.
		 * @return void
		 */
		public static function render_waiting_for_result_template( $product ) {
			if ( ! lty_is_lottery_product( $product ) || ! $product->has_lottery_status( 'lty_lottery_closed' ) ) {
				return;
			}

			lty_get_template( 'single-product/waiting-for-result-notice.php', array( 'wait_message' => get_option( 'lty_settings_single_product_lottery_wait_message' ) ) );
		}

		/**
		 * Render the guest error notice template.
		 *
		 * @since 1.0.0
		 * @param object $product Product object.
		 * @return void
		 */
		public static function render_guest_error_notice_template( $product ) {
			if ( ! lty_is_lottery_product( $product ) || $product->is_closed() ) {
				return;
			}

			// Check if the current user is not a guest.
			if ( wp_get_current_user()->exists() ) {
				return;
			}

			// Check if the option is Force to login at Checkout/Prevent Guest Participation.
			if ( '2' != get_option( 'lty_settings_guest_user_participate_type' ) ) {
				return;
			}

			lty_get_template( 'single-product/guest-login-notice.php', array( 'guest_error_message' => get_option( 'lty_settings_single_product_guest_error_message' ) ) );
		}

		/**
		 * Render the winner message template.
		 *
		 * @since 1.0.0
		 * @param object $product Product object.
		 * @return void
		 */
		public static function render_winner_message_template( $product ) {
			if ( ! lty_is_lottery_product( $product ) || ! $product->has_lottery_status( 'lty_lottery_finished' ) ) {
				return;
			}

			if ( 'yes' === get_option( 'lty_settings_hide_lottery_winner_message_in_single_product_page', 'no' ) ) {
				return;
			}

			// Check if the current user is a guest.
			if ( ! wp_get_current_user()->exists() ) {
				return;
			}

			// Check If the user is not a winner.
			if ( empty( $product->get_current_user_winner_ids() ) ) {
				return;
			}

			$msg = ( $product->get_lty_winners_count() > 1 ) ? get_option( 'lty_settings_single_product_lottery_multi_winner_message' ) : get_option( 'lty_settings_single_product_lottery_winner_message' );

			lty_get_template( 'single-product/win-message-notice.php', array( 'win_message' => $msg ) );
		}

		/**
		 * Render loser message template.
		 *
		 * @since 1.0.0
		 * @param object $product Product object.
		 * @return void
		 */
		public static function render_loser_message_template( $product ) {
			if ( ! lty_is_lottery_product( $product ) || ! $product->has_lottery_status( 'lty_lottery_finished' ) ) {
				return;
			}

			// Check if the current user is a guest.
			if ( ! wp_get_current_user()->exists() ) {
				return;
			}

			// Check If the user is a winner.
			if ( ! empty( $product->get_current_user_winner_ids() ) ) {
				return;
			}

			lty_get_template( 'single-product/lose-message-notice.php', array( 'lose_message' => get_option( 'lty_settings_single_product_lottery_not_winners_message' ) ) );
		}

		/**
		 * Render the winner log template.
		 *
		 * @since 1.0.0
		 * @param object $product Product object.
		 * @return void
		 */
		public static function render_winner_log_template( $product ) {
			if ( '2' === get_option( 'lty_settings_single_product_lottery_winner_toggle' ) ) {
				return;
			}

			if ( ! lty_is_lottery_product( $product ) || ! $product->has_lottery_status( 'lty_lottery_finished' ) ) {
				return;
			}

			$args = array(
				'_columns'        => lty_get_lottery_winner_table_header( $product ),
				'lottery_winners' => $product->get_current_winner_ids(),
				'product'         => $product,
			);

			if ( ! lty_check_is_array( $args['_columns'] ) || ! lty_check_is_array( $args['lottery_winners'] ) ) {
				return;
			}

			lty_get_template( 'single-product/winner-log.php', $args );
		}

		/**
		 * Render the winners count template.
		 *
		 * @since 1.0.0
		 * @param object $product Product object.
		 * @return void
		 */
		public static function render_winners_count_template( $product ) {
			if ( ! lty_is_lottery_product( $product ) ) {
				return;
			}

			lty_get_template( 'single-product/winners-count-notice.php' );
		}

		/**
		 * Render the ticket summary template.
		 *
		 * @since 1.0.0
		 * @global object $product Product object.
		 * @return void
		 */
		public static function render_ticket_summary_template() {
			global $product;

			if ( ! lty_is_lottery_product( $product ) ) {
				return;
			}

			if ( ! $product->has_lottery_status( 'lty_lottery_started' ) || ! $product->is_manual_ticket() || $product->user_purchase_limit_exists() ) {
				return;
			}

			lty_validate_ticket_in_cart_items( $product );

			lty_get_template( 'single-product/ticket-summary.php', array( 'product' => $product ) );
		}

		/**
		 * Render predefined buttons template.
		 *
		 * @since 1.0.0
		 * @global object $product Product object.
		 * @return void
		 */
		public static function render_predefined_buttons_template() {
			global $product;

			if ( ! lty_is_lottery_product( $product ) ) {
				return;
			}

			if ( ! $product->has_lottery_status( 'lty_lottery_started' ) || $product->is_manual_ticket() || $product->user_purchase_limit_exists() ) {
				return;
			}

			if ( ! $product->is_started() || $product->is_closed() ) {
				return;
			}

			if ( ! $product->is_predefined_button_enabled() || ( ! $product->can_display_predefined_buttons() && ! $product->can_display_predefined_with_quantity_selector() ) ) {
				return;
			}

			$args = array(
				'product'        => $product,
				'selection_type' => $product->get_predefined_buttons_selection_type(),
				'buttons_rule'   => $product->get_predefined_buttons_rule(),
			);

			lty_get_template( 'single-product/predefined-buttons.php', $args );
		}

		/**
		 * Render the ticket tab content.
		 *
		 * @since 1.0.0
		 * @param object $product Product object.
		 * @return void
		 * */
		public static function render_ticket_tab_content( $product ) {
			if ( ! lty_is_lottery_product( $product ) ) {
				return;
			}

			if ( ! $product->has_lottery_status( 'lty_lottery_started' ) || ! $product->is_manual_ticket() || $product->user_purchase_limit_exists() ) {
				return;
			}

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
				'cart_tickets'     => $product->get_cart_tickets(),
				'reserved_tickets' => $product->get_reserved_tickets(),
				'index'            => 0,
				'ticket_numbers'   => $ticket_numbers,
				'view_more'        => ( 'yes' === $product->get_lty_view_more_tickets_per_tab() ) ? $product->get_lty_tickets_per_tab_view_more_count() : false,
			);

			lty_get_template( 'single-product/ticket-tab-content.php', $tickets_args );
		}

		/**
		 * Render the ticket lucky dip.
		 *
		 * @since 1.0.0
		 * @global object $product Product object.
		 * @return void
		 */
		public static function render_ticket_lucky_dip() {
			global $product;

			if ( ! lty_is_lottery_product( $product ) ) {
				return;
			}

			if ( ! $product->is_lucky_dip() || ! $product->is_in_stock() ) {
				return;
			}

			if ( ! $product->has_lottery_status( 'lty_lottery_started' ) || ! $product->is_manual_ticket() || $product->user_purchase_limit_exists() ) {
				return;
			}

			lty_get_template( 'single-product/ticket-lucky-dip.php', array( 'product' => $product ) );
		}

		/**
		 * Render manual ticket search.
		 *
		 * @since 1.0.0
		 * @global object $product Product object.
		 * @return void
		 */
		public static function render_manual_ticket_search() {
			global $product;

			if ( ! lty_is_lottery_product( $product ) || ! $product->is_in_stock() ) {
				return;
			}

			if ( 'yes' !== get_option( 'lty_settings_enable_manual_ticket_selection_search_bar' ) ) {
				return;
			}

			if ( ! $product->has_lottery_status( 'lty_lottery_started' ) || ! $product->is_manual_ticket() || $product->user_purchase_limit_exists() ) {
				return;
			}

			if ( 'yes' === $product->get_lty_alphabet_with_sequence_nos_enabled() ) {
				return;
			}

			lty_get_template( 'single-product/manual-ticket-search.php' );
		}

		/**
		 * Render the question answer template.
		 *
		 * @since 1.0.0
		 * @param object $product Product object.
		 * @return void
		 * */
		public static function render_question_answer_template( $product = false ) {
			if ( ! $product ) {
				global $product;
			}

			if ( ! lty_is_lottery_product( $product ) || ! $product->is_in_stock() ) {
				return;
			}

			// Return if the question answer is not valid.
			if ( ! $product->is_valid_question_answer() ) {
				return;
			}

			// Return if the user purchase limit exists.
			if ( $product->user_purchase_limit_exists() ) {
				return;
			}

			// Return if the current customer question answer time limit is exists.
			if ( $product->is_customer_question_answer_time_limit_exists() ) {
				return;
			}

			$questions       = $product->get_question_answers();
			$cart_contents   = lty_get_cart_contents();
			$cart_answer_ids = array();
			if ( lty_check_is_array( $cart_contents ) ) {
				foreach ( $cart_contents as $cart_item ) {
					$cart_product_id = isset( $cart_item['product_id'] ) ? $cart_item['product_id'] : '';
					$cart_product    = wc_get_product( $cart_product_id );
					if ( ! lty_is_lottery_product( $cart_product ) ) {
						continue;
					}

					$cart_answer_ids[ $cart_product->get_id() ] = isset( $cart_item['lty_lottery']['answers'] ) ? $cart_item['lty_lottery']['answers'] : '';
				}
			}

			$args = array(
				'product'        => $product,
				'question'       => $questions[0],
				'cart_answer_id' => isset( $cart_answer_ids[ $product->get_id() ] ) ? $cart_answer_ids[ $product->get_id() ] : '',
			);

			lty_get_template( 'single-product/question-answer.php', $args );
		}

		/**
		 * Render the question answer timer template.
		 *
		 * @since 6.7.0
		 * @param object $product Product object.
		 * @return void
		 * */
		public static function render_question_answer_timer_template( $product ) {
			if ( ! $product ) {
				global $product;
			}

			if ( ! lty_is_lottery_product( $product ) ) {
				return;
			}

			// Return if the question answer time limit is set as unlimited.
			if ( '2' != $product->get_question_answer_time_limit_type() ) {
				return;
			}

			$limit = $product->get_question_answer_time_limit();
			if ( ! isset( $limit['number'] ) || ! $limit['number'] ) {
				return;
			}

			// Return if the product exists in the cart.
			if ( lty_lottery_product_exists_in_cart( $product->get_id() ) ) {
				return;
			}

			$customer_id = lty_get_current_user_cart_session_value();
			// Update the view of the question answer for the current customer.
			$viewed_data = array_filter( (array) $product->get_lty_question_answer_viewed_data() );
			if ( ! isset( $viewed_data[ $customer_id ] ) ) {
				$viewed_data[ $customer_id ] = LTY_Date_Time::get_mysql_date_time_format( 'now', true );
				$product->update_post_meta( 'lty_question_answer_viewed_data', $viewed_data );
			}

			$args = array(
				'product'        => $product,
				'remaining_date' => $product->get_customer_question_answer_time_limit_date( $customer_id ),
			);

			lty_get_template( 'single-product/question-answer-timer.php', $args );
		}
	}

	LTY_Lottery_Single_Product_Templates::init();
}
