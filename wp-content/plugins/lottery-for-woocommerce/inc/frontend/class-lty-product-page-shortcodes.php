<?php

/**
 * Shortcodes - Product page.
 *
 * @since 10.2.0
 * */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'LTY_Product_Page_Shortcodes' ) ) {

	/**
	 * Class.
	 *
	 * @since 10.2.0
	 */
	class LTY_Product_Page_Shortcodes {

		/**
		 * Shortcodes.
		 *
		 * @since 11.8.0
		 * @var array
		 */
		protected static $shortcodes = array(
			'lty_lottery_status',
			'lty_lottery_start_date',
			'lty_lottery_end_date',
			'lty_lottery_date_notice',
			'lty_lottery_count_down_timer',
			'lty_lottery_minimum_tickets',
			'lty_lottery_maximum_tickets',
			'lty_lottery_maximum_tickets_per_user',
			'lty_lottery_minimum_tickets_per_user',
			'lty_lottery_maximum_tickets_per_user_notice',
			'lty_lottery_progress_bar',
			'lty_lottery_tickets_sold_percentage',
			'lty_lottery_tickets_sold_count',
			'lty_lottery_winning_item',
			'lty_lottery_question_answer',
			'lty_lottery_quantity_selector',
			'lty_lottery_participate_button',
			'lty_lottery_predefined_buttons',
			'lty_lottery_predefined_button_url',
			'lty_lottery_predefined_button_amount',
			'lty_lottery_predefined_button_total_amount',
			'lty_lottery_predefined_button_discount',
			'lty_lottery_predefined_button_tickets_quantity',
			'lty_instant_win_prizes',
			'lty_user_chooses_ticket',
			'lty_lucky_dip',
			'lty_lucky_dip_fixed_quantity',
			'lty_lottery_details_tab',
		);

		/**
		 * Class Initialization.
		 *
		 * @since 10.2.0
		 * */
		public static function init() {
			foreach ( self::$shortcodes as $shortcode_name ) {
				$callback_method = 'process_' . str_replace( 'lty_', '', $shortcode_name ) . '_shortcode';
				// Add a shortcode.
				add_shortcode( $shortcode_name, array( __CLASS__, $callback_method ) );
			}
		}

		/**
		 * Process the lottery details tab shortcode.
		 *
		 * @since 11.8.0
		 * @param array  $atts Shortcode attributes.
		 * @param string $content Shortcode content.
		 * @return string|HTML
		 */
		public static function process_lottery_details_tab_shortcode( $atts, $content ) {
			$lottery_product = self::get_product_based_on_shortcode_attributes( $atts );
			if ( ! lty_is_lottery_product( $lottery_product ) ) {
				return;
			}

			return lty_get_template_html( 'single-product/tabs/ticket-logs-layout.php', lty_prepare_ticket_logs_template_arguments( $lottery_product ) );
		}

		/**
		 * Process the lottery status shortcode.
		 *
		 * @since 11.8.0
		 * @param array  $atts Shortcode attributes.
		 * @param string $content Shortcode content.
		 * @return string|HTML
		 */
		public static function process_lottery_status_shortcode( $atts, $content ) {
			$lottery_product = self::get_product_based_on_shortcode_attributes( $atts );
			if ( ! lty_is_lottery_product( $lottery_product ) ) {
				return;
			}

			return lty_get_template_html( 'single-product/ticket-status.php', array( 'product' => $lottery_product ) );
		}

		/**
		 * Process the lottery start date shortcode.
		 *
		 * @since 11.8.0
		 * @param array  $atts Shortcode attributes.
		 * @param string $content Shortcode content.
		 * @return string|HTML
		 */
		public static function process_lottery_start_date_shortcode( $atts, $content ) {
			$lottery_product = self::get_product_based_on_shortcode_attributes( $atts );
			if ( ! lty_is_lottery_product( $lottery_product ) || ! $lottery_product->get_lty_start_date() ) {
				return;
			}

			$display_timezone = isset( $atts['display_timezone'] ) ? ( 'false' === $atts['display_timezone'] ? false : true ) : true;

			return LTY_Date_Time::get_wp_format_datetime( $lottery_product->get_lty_start_date(), false, false, false, ' ', $display_timezone );
		}

		/**
		 * Process the lottery end date shortcode.
		 *
		 * @since 11.8.0
		 * @param array  $atts Shortcode attributes.
		 * @param string $content Shortcode content.
		 * @return string|HTML
		 */
		public static function process_lottery_end_date_shortcode( $atts, $content ) {
			$product = self::get_product_based_on_shortcode_attributes( $atts );
			if ( ! lty_is_lottery_product( $product ) ) {
				return;
			}

			if ( $product->is_unlimited_scheduled_lottery() ) {
				return __( 'Unlimited', 'lottery-for-woocommerce' );
			}

			if ( ! $product->get_lty_end_date() ) {
				return;
			}

			$display_timezone = isset( $atts['display_timezone'] ) ? ( 'false' === $atts['display_timezone'] ? false : true ) : true;

			return LTY_Date_Time::get_wp_format_datetime( $product->get_lty_end_date(), false, false, false, ' ', $display_timezone );
		}

		/**
		 * Process the lottery date notice shortcode.
		 *
		 * @since 11.8.0
		 * @param array  $atts Shortcode attributes.
		 * @param string $content Shortcode content.
		 * @return string|HTML
		 */
		public static function process_lottery_date_notice_shortcode( $atts, $content ) {
			$product = self::get_product_based_on_shortcode_attributes( $atts );
			if ( ! lty_is_lottery_product( $product ) ) {
				return;
			}

			if ( ! $product->is_valid_to_display_countdown_timer_in_product_page( 'shortcode' ) ) {
				return;
			}

			return lty_get_template_html( 'single-product/date-notice.php', array( 'product' => $product ) );
		}

		/**
		 * Process the lottery countdown timer shortcode.
		 *
		 * @since 11.8.0
		 * @param array  $atts Shortcode attributes.
		 * @param string $content Shortcode content.
		 * @return string|HTML
		 */
		public static function process_lottery_count_down_timer_shortcode( $atts, $content ) {
			$product = self::get_product_based_on_shortcode_attributes( $atts );
			if ( ! lty_is_lottery_product( $product ) ) {
				return;
			}

			if ( ! $product->is_valid_to_display_countdown_timer_in_product_page( 'shortcode' ) ) {
				return;
			}

			return lty_get_template_html( 'single-product/date-ranges.php', array( 'product' => $product ) );
		}

		/**
		 * Process the lottery minimum tickets shortcode.
		 *
		 * @since 11.8.0
		 * @param array  $atts Shortcode attributes.
		 * @param string $content Shortcode content.
		 * @return int
		 */
		public static function process_lottery_minimum_tickets_shortcode( $atts, $content ) {
			$product = self::get_product_based_on_shortcode_attributes( $atts );
			if ( ! lty_is_lottery_product( $product ) ) {
				return;
			}

			return absint( $product->get_lty_minimum_tickets() );
		}

		/**
		 * Process the lottery maximum tickets shortcode.
		 *
		 * @since 11.8.0
		 * @param array  $atts Shortcode attributes.
		 * @param string $content Shortcode content.
		 * @return int
		 */
		public static function process_lottery_maximum_tickets_shortcode( $atts, $content ) {
			$lottery_product = self::get_product_based_on_shortcode_attributes( $atts );
			if ( ! lty_is_lottery_product( $lottery_product ) ) {
				return;
			}

			return absint( $lottery_product->get_lty_maximum_tickets() );
		}

		/**
		 * Process the lottery maximum tickets per user shortcode.
		 *
		 * @since 11.8.0
		 * @param array  $atts Shortcode attributes.
		 * @param string $content Shortcode content.
		 * @return int
		 */
		public static function process_lottery_maximum_tickets_per_user_shortcode( $atts, $content ) {
			$lottery_product = self::get_product_based_on_shortcode_attributes( $atts );
			if ( ! lty_is_lottery_product( $lottery_product ) ) {
				return;
			}

			if ( '3' == get_option( 'lty_settings_guest_user_participate_type' ) ) {
				return;
			}

			return absint( $lottery_product->get_lty_user_maximum_tickets() );
		}

		/**
		 * Process the lottery minimum tickets per user shortcode.
		 *
		 * @since 11.8.0
		 * @param array  $atts Shortcode attributes.
		 * @param string $content Shortcode content.
		 * @return int
		 * */
		public static function process_lottery_minimum_tickets_per_user_shortcode( $atts, $content ) {
			if ( '3' == get_option( 'lty_settings_guest_user_participate_type' ) ) {
				return;
			}

			$lottery_product = self::get_product_based_on_shortcode_attributes( $atts );
			if ( ! lty_is_lottery_product( $lottery_product ) ) {
				return;
			}

			return absint( $lottery_product->get_lty_user_minimum_tickets() );
		}

		/**
		 * Process the lottery maximum tickets per user notice shortcode.
		 *
		 * @since 11.8.0
		 * @param array  $atts Shortcode attributes.
		 * @param string $content Shortcode content.
		 * @return string|HTML
		 * */
		public static function process_lottery_maximum_tickets_per_user_notice_shortcode( $atts, $content ) {
			if ( ! wp_get_current_user()->exists() ) {
				return;
			}

			$lottery_product = self::get_product_based_on_shortcode_attributes( $atts );
			if ( ! lty_is_lottery_product( $lottery_product ) ) {
				return;
			}

			// return if the user purchase limit is not reached.
			if ( ! $lottery_product->user_purchase_limit_exists() || $lottery_product->is_closed() ) {
				return;
			}

			$message = get_option( 'lty_settings_maximum_tickets_purchase_limit_error_message', 'You have reached the Maximum ticket(s) count {maximum_tickets_count} for this lottery. Hence you cannot purchase new lottery tickets.' );
			$message = str_replace( '{maximum_tickets_count}', '<b>' . $lottery_product->get_lty_user_maximum_tickets() . '</b>', $message );

			return $message;
		}

		/**
		 * Process the lottery progress bar shortcode.
		 *
		 * @since 11.8.0
		 * @param array  $atts Shortcode attributes.
		 * @param string $content Shortcode content.
		 * @return string|HTML
		 */
		public static function process_lottery_progress_bar_shortcode( $atts, $content ) {
			$lottery_product = self::get_product_based_on_shortcode_attributes( $atts );
			if ( ! lty_is_lottery_product( $lottery_product ) || $lottery_product->is_closed() ) {
				return;
			}

			$args = array(
				'product'                 => $lottery_product,
				'progress_bar_percentage' => lty_get_product_page_progress_bar_percentage( $lottery_product ),
			);

			return lty_get_template_html( 'single-product/progress-bar.php', $args );
		}

		/**
		 * Process the lottery tickets sold percentage shortcode.
		 *
		 * @since 11.8.0
		 * @param array  $atts Shortcode attributes.
		 * @param string $content Shortcode content.
		 * @return string
		 * */
		public static function process_lottery_tickets_sold_percentage_shortcode( $atts, $content ) {
			$lottery_product = self::get_product_based_on_shortcode_attributes( $atts );
			if ( ! lty_is_lottery_product( $lottery_product ) || $lottery_product->is_closed() ) {
				return;
			}

			$sold_percentage = isset( $atts['decimal_count'] ) && is_numeric( $atts['decimal_count'] ) ? round( lty_get_progress_bar_percentage( $lottery_product ), (int) $atts['decimal_count'] ) : wc_format_decimal( lty_get_progress_bar_percentage( $lottery_product ), '' );

			return $sold_percentage . '%';
		}

		/**
		 * Process the lottery tickets sold count shortcode.
		 *
		 * @since 11.8.0
		 * @param array  $atts Shortcode attributes.
		 * @param string $content Shortcode content.
		 * @return int
		 */
		public static function process_lottery_tickets_sold_count_shortcode( $atts, $content ) {
			$lottery_product = self::get_product_based_on_shortcode_attributes( $atts );
			if ( ! lty_is_lottery_product( $lottery_product ) || $lottery_product->is_closed() ) {
				return;
			}

			return $lottery_product->get_purchased_ticket_count();
		}

		/**
		 * Process the lottery winning item shortcode.
		 *
		 * @since 11.8.0
		 * @param array  $atts Shortcode attributes.
		 * @param string $content Shortcode content.
		 * @return string|HTML
		 * */
		public static function process_lottery_winning_item_shortcode( $atts, $content ) {
			$lottery_product = self::get_product_based_on_shortcode_attributes( $atts );
			if ( ! lty_is_lottery_product( $lottery_product ) ) {
				return;
			}

			return lty_get_lottery_gift_products( false, $lottery_product, true );
		}

		/**
		 * Process the lottery question answer shortcode.
		 *
		 * @since 11.8.0
		 * @param array  $atts Shortcode attributes.
		 * @param string $content Shortcode content.
		 * @return string|HTML
		 * */
		public static function process_lottery_question_answer_shortcode( $atts, $content ) {
			$lottery_product = self::get_product_based_on_shortcode_attributes( $atts );
			if ( ! lty_is_lottery_product( $lottery_product ) ) {
				return;
			}

			ob_start();
			LTY_Lottery_Single_Product_Templates::render_question_answer_template( $lottery_product );
			$content = ob_get_contents();
			ob_end_clean();

			return $content;
		}

		/**
		 * Process the lottery quantity selector shortcode.
		 *
		 * @since 11.8.0
		 * @param array  $atts Shortcode attributes.
		 * @param string $content Shortcode content.
		 * @return string|HTML
		 * */
		public static function process_lottery_quantity_selector_shortcode( $atts, $content ) {
			$lottery_product = self::get_product_based_on_shortcode_attributes( $atts );
			if ( ! lty_is_lottery_product( $lottery_product ) ) {
				return;
			}

			if ( ! $lottery_product->is_purchasable() || ! $lottery_product->is_in_stock() || ! $lottery_product->is_started() || $lottery_product->is_closed() || $lottery_product->user_purchase_limit_exists() ) {
				return;
			}

			if ( ( $lottery_product->can_display_predefined_buttons() && ! $lottery_product->can_display_predefined_with_quantity_selector() ) || $lottery_product->is_manual_ticket() ) {
				return;
			}

			$customer_id = lty_get_current_user_cart_session_value();
			// Return if the user selected incorrect answer.
			if ( $lottery_product->validate_user_incorrect_answer( $customer_id ) || $lottery_product->is_customer_question_answer_time_limit_exists( $customer_id ) ) {
				return;
			}

			ob_start();
			lty_render_quantity_field( $lottery_product );
			$content = ob_get_contents();
			ob_end_clean();

			return $content;
		}

		/**
		 * Process the lottery participate button shortcode.
		 *
		 * @since 11.8.0
		 * @param array  $atts Shortcode attributes.
		 * @param string $content Shortcode content.
		 * @return string|HTML
		 * */
		public static function process_lottery_participate_button_shortcode( $atts, $content ) {
			$lottery_product = self::get_product_based_on_shortcode_attributes( $atts );
			if ( ! lty_is_lottery_product( $lottery_product ) ) {
				return;
			}

			if ( ! $lottery_product->is_purchasable() || ! $lottery_product->is_in_stock() || ! $lottery_product->is_started() || $lottery_product->is_closed() || $lottery_product->user_purchase_limit_exists() ) {
				return;
			}

			$customer_id = lty_get_current_user_cart_session_value();
			// Return if the user selected incorrect answer.
			if ( $lottery_product->validate_user_incorrect_answer( $customer_id ) || $lottery_product->is_customer_question_answer_time_limit_exists( $customer_id ) ) {
				return;
			}

			return lty_get_template_html( 'shortcodes/participate-button.php', array( 'product' => $lottery_product ) );
		}

		/**
		 * Process the lottery predefined buttons shortcode.
		 *
		 * @since 11.8.0
		 * @param array  $atts Shortcode attributes.
		 * @param string $content Shortcode content.
		 * @return string|HTML
		 */
		public static function process_lottery_predefined_buttons_shortcode( $atts, $content ) {
			$lottery_product = self::get_product_based_on_shortcode_attributes( $atts );
			if ( ! lty_is_lottery_product( $lottery_product ) ) {
				return;
			}

			if ( ! $lottery_product->has_lottery_status( 'lty_lottery_started' ) || $lottery_product->is_manual_ticket() || $lottery_product->user_purchase_limit_exists() ) {
				return;
			}

			if ( ! $lottery_product->is_started() || $lottery_product->is_closed() || ! $lottery_product->is_predefined_button_enabled() ) {
				return;
			}

			$args = array(
				'product'        => $lottery_product,
				'selection_type' => $lottery_product->get_predefined_buttons_selection_type(),
				'buttons_rule'   => $lottery_product->get_predefined_buttons_rule(),
			);

			return lty_get_template_html( 'shortcodes/predefined-buttons.php', $args );
		}

		/**
		 * Process the lottery predefined button URL shortcode.
		 *
		 * @since 11.8.0
		 * @param array  $atts Shortcode attributes.
		 * @param string $content Shortcode content.
		 * @return string|URL
		 */
		public static function process_lottery_predefined_button_url_shortcode( $atts, $content ) {
			$lottery_product = self::is_valid_lottery_predefined_button( $atts );
			if ( ! is_object( $lottery_product ) ) {
				return;
			}

			return add_query_arg(
				array(
					'lty_product_id' => $lottery_product->get_id(),
					'quantity'       => $lottery_product->get_predefined_buttons_ticket_quantity( $atts['button_key'] ),
					'button_key'     => $atts['button_key'],
				),
				$lottery_product->get_permalink()
			);
		}

		/**
		 * Process the lottery predefined button amount shortcode.
		 *
		 * @since 11.8.0
		 * @param array  $atts Shortcode attributes.
		 * @param string $content Shortcode content.
		 * @return float
		 */
		public static function process_lottery_predefined_button_amount_shortcode( $atts, $content ) {
			$lottery_product = self::is_valid_lottery_predefined_button( $atts );
			if ( ! is_object( $lottery_product ) ) {
				return;
			}

			return $lottery_product->get_predefined_buttons_per_ticket_amount( $atts['button_key'] );
		}

		/**
		 * Process the lottery predefined button total amount shortcode.
		 *
		 * @since 11.8.0
		 * @param array  $atts Shortcode attributes.
		 * @param string $content Shortcode content.
		 * @return float
		 */
		public static function process_lottery_predefined_button_total_amount_shortcode( $atts, $content ) {
			$lottery_product = self::is_valid_lottery_predefined_button( $atts );
			if ( ! is_object( $lottery_product ) ) {
				return;
			}

			return $lottery_product->get_predefined_buttons_total_ticket_amount( $atts['button_key'] );
		}

		/**
		 * Process the lottery predefined button discount shortcode.
		 *
		 * @since 11.8.0
		 * @param array  $atts Shortcode attributes.
		 * @param string $content Shortcode content.
		 * @return float
		 */
		public static function process_lottery_predefined_button_discount_shortcode( $atts, $content ) {
			$lottery_product = self::is_valid_lottery_predefined_button( $atts );
			if ( ! is_object( $lottery_product ) ) {
				return;
			}

			return $lottery_product->get_predefined_buttons_discount_amount( $atts['button_key'] );
		}

		/**
		 * Process the lottery predefined button tickets quantity shortcode.
		 *
		 * @since 11.8.0
		 * @param array  $atts Shortcode attributes.
		 * @param string $content Shortcode content.
		 * @return int
		 */
		public static function process_lottery_predefined_button_tickets_quantity_shortcode( $atts, $content ) {
			$lottery_product = self::is_valid_lottery_predefined_button( $atts );
			if ( ! is_object( $lottery_product ) ) {
				return;
			}

			return $lottery_product->get_predefined_buttons_ticket_quantity( $atts['button_key'] );
		}

		/**
		 * Process instant winner prizes shortcode.
		 *
		 * @since 11.8.0
		 * @param array  $atts Shortcode attributes.
		 * @param string $content Shortcode content.
		 * @return string|HTML
		 * */
		public static function process_instant_win_prizes_shortcode( $atts, $content ) {
			$product = self::get_product_based_on_shortcode_attributes( $atts );
			if ( ! lty_is_lottery_product( $product ) || ! $product->is_instant_winner() ) {
				return;
			}

			if ( ! lty_check_is_array( $product->get_current_instant_winner_log_ids() ) ) {
				return;
			}

			if ( '2' === $product->get_lty_instant_winner_display_mode() ) {
				$template = 'single-product/tabs/instant-winner-prize-groups-layout.php';
			} else {
				$template = 'single-product/tabs/instant-winners-logs-layout.php';
			}

			return lty_get_template_html( $template, lty_prepare_instant_winner_logs_arguments( $product ) );
		}

		/**
		 * Process user chooses ticket shortcode.
		 *
		 * @since 11.8.0
		 * @param array  $atts Shortcode attributes.
		 * @param string $content Shortcode content.
		 * @return string|HTML
		 * */
		public static function process_user_chooses_ticket_shortcode( $atts, $content ) {
			$product = self::get_product_based_on_shortcode_attributes( $atts );
			if ( ! lty_is_lottery_product( $product ) || ! $product->is_in_stock() ) {
				return;
			}

			if ( ! $product->is_started() || $product->is_closed() || ! $product->is_manual_ticket() || $product->user_purchase_limit_exists() ) {
				return;
			}

			return lty_get_template_html( 'single-product/ticket-summary.php', array( 'product' => $product ) );
		}

		/**
		 * Process lucky dip shortcode.
		 *
		 * @since 11.8.0
		 * @param array  $atts Shortcode attributes.
		 * @param string $content Shortcode content.
		 * @return string|HTML
		 * */
		public static function process_lucky_dip_shortcode( $atts, $content ) {
			$product = self::get_product_based_on_shortcode_attributes( $atts );
			if ( ! lty_is_lottery_product( $product ) || ! $product->is_in_stock() || ! $product->is_lucky_dip() ) {
				return;
			}

			if ( ! $product->is_started() || $product->is_closed() || ! $product->is_manual_ticket() || $product->user_purchase_limit_exists() ) {
				return;
			}

			return lty_get_template_html( 'single-product/ticket-lucky-dip.php', array( 'product' => $product ) );
		}

		/**
		 * Process lucky dip fixed quantity shortcode.
		 *
		 * @since 11.8.0
		 * @param array  $atts Shortcode attributes.
		 * @param string $content Shortcode content.
		 * @return string|HTML
		 * */
		public static function process_lucky_dip_fixed_quantity_shortcode( $atts, $content ) {
			$product = self::get_product_based_on_shortcode_attributes( $atts );
			if ( ! lty_is_lottery_product( $product ) || ! $product->is_in_stock() || ! $product->is_lucky_dip() ) {
				return;
			}

			if ( ! $product->is_started() || $product->is_closed() || ! $product->is_manual_ticket() || $product->user_purchase_limit_exists() ) {
				return;
			}

			return lty_get_template_html(
				'shortcodes/ticket-lucky-dip.php',
				array(
					'product'  => $product,
					'quantity' => isset( $atts['quantity'] ) ? $atts['quantity'] : '1',
				)
			);
		}

		/**
		 * Is valid lottery predefined button?
		 *
		 * @since 7.3.0
		 * @param array $atts Shortcode attributes.
		 * @return bool|object
		 */
		public static function is_valid_lottery_predefined_button( $atts ) {
			if ( '' == $atts['button_key'] ) {
				return false;
			}

			$lottery_product = self::get_product_based_on_shortcode_attributes( $atts );
			if ( ! lty_is_lottery_product( $lottery_product ) ) {
				return false;
			}

			if ( ! $lottery_product->is_purchasable() || ! $lottery_product->is_in_stock() || ! $lottery_product->is_started() || $lottery_product->is_closed() || $lottery_product->user_purchase_limit_exists() ) {
				return false;
			}

			$customer_id = lty_get_current_user_cart_session_value();
			// Return if the user selected incorrect answer.
			if ( $lottery_product->validate_user_incorrect_answer( $customer_id ) || $lottery_product->is_customer_question_answer_time_limit_exists( $customer_id ) ) {
				return false;
			}

			if ( $lottery_product->is_manual_ticket() || ! $lottery_product->is_predefined_button_enabled() ) {
				return false;
			}

			if ( ! array_key_exists( $atts['button_key'], $lottery_product->get_predefined_buttons_rule() ) ) {
				return false;
			}

			return $lottery_product;
		}

		/**
		 * Get product based on shortcode attributes.
		 *
		 * @since 10.2.0
		 * @param array $atts Shortcode attributes.
		 * @return object Product object.
		 */
		public static function get_product_based_on_shortcode_attributes( $atts ) {
			global $product;
			$product_id = isset( $atts['product_id'] ) ? absint( $atts['product_id'] ) : '';

			return ! empty( $product_id ) ? wc_get_product( $product_id ) : $product;
		}
	}

	LTY_Product_Page_Shortcodes::init();
}
