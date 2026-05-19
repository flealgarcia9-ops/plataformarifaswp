<?php
/**
 * Lottery Product Type.
 *
 * @since 1.0.0
 * */

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}

if (!class_exists('WC_Product_Lottery')) {

	/**
	 * Class.
	 *
	 * @since 1.0.0
	 * */
	class WC_Product_Lottery extends WC_Product_Lottery_Data {

		/**
		 * Alphabet with sequences.
		 *
		 * @since 1.0.0
		 * @var array
		 * */
		protected $alphabet_with_sequences;

		/**
		 * Instant winner ids.
		 *
		 * @since 8.0.0
		 * @var array
		 * */
		private $instant_winner_ids;

		/**
		 * Instant winner statuses count.
		 *
		 * @since 11.0.0
		 * @var array
		 * */
		private $instant_winner_statuses_count;

		/**
		 * Instant winner log ids.
		 *
		 * @since 8.0.0
		 * @var array
		 * */
		private $instant_winner_log_ids;

		/**
		 * Instant winner prize group ID's.
		 *
		 * @since 11.1.0
		 * @var array
		 */
		private $instant_winner_prize_group_ids;

		/**
		 * Instant winner prize groups data.
		 *
		 * @since 11.1.0
		 * @var array
		 */
		private $instant_winner_prize_groups_data;

		/**
		 * Placed tickets.
		 *
		 * @since 8.5.0
		 * @var array
		 */
		private $placed_tickets;

		/**
		 * Placed ticket IDs.
		 *
		 * @since 8.5.0
		 * @var array
		 */
		private $placed_ticket_ids;

		/**
		 * Purchased tickets.
		 *
		 * @since 8.5.0
		 * @var array
		 */
		private $purchased_tickets;

		/**
		 * Looser ticket IDs.
		 *
		 * @since 8.5.0
		 * @var array
		 */
		private $looser_ticket_ids;

		/**
		 * User placed tickets.
		 *
		 * @since 8.5.0
		 * @var array
		 */
		private $user_placed_tickets;

		/**
		 * User purchased tickets.
		 *
		 * @since 8.5.0
		 * @var array
		 */
		private $user_purchased_tickets;

		/**
		 * Automatic Ticket numbers.
		 *
		 * @since 8.5.0
		 * @var array
		 */
		private $automatic_ticket_numbers;

		/**
		 * Manual ticket numbers.
		 *
		 * @since 8.5.0
		 * @var array
		 */
		private $manual_ticket_numbers;

		/**
		 * Get the add to cart button text.
		 *
		 * @since 1.0.0
		 * @return string
		 * */
		public function add_to_cart_text() {
			switch ( $this->get_lty_lottery_status() ) {
				case 'lty_lottery_not_started':
					$add_to_cart_text = get_option( 'lty_settings_shop_lottery_not_started_btn_label', __( 'Read More', 'lottery-for-woocommerce' ) );
					break;

				case 'lty_lottery_started':
					$add_to_cart_text = get_option( 'lty_settings_shop_lottery_started_btn_label', __( 'Participate Now', 'lottery-for-woocommerce' ) );
					break;

				case 'lty_lottery_closed':
					$add_to_cart_text = get_option( 'lty_settings_shop_lottery_closed_btn_label', __( 'Read More', 'lottery-for-woocommerce' ) );
					break;

				case 'lty_lottery_finished':
					$add_to_cart_text = get_option( 'lty_settings_shop_lottery_finished_btn_label', __( 'View Winner(s)', 'lottery-for-woocommerce' ) );
					break;

				default:
					$add_to_cart_text = get_option( 'lty_settings_shop_lottery_failed_btn_label', __( 'Read More', 'lottery-for-woocommerce' ) );
					break;
			}

			/**
			 * This hook is used to alter the lottery product add to cart text.
			 *
			 * @since 1.0.0
			 */
			return apply_filters( 'woocommerce_product_add_to_cart_text', $add_to_cart_text, $this );
		}

		/**
		 * Check if the lottery is started.
		 *
		 * @since 1.0.0
		 * @return bool
		 * */
		public function is_started() {
			if ( $this->get_lty_start_date_gmt() ) {
				return strtotime( $this->get_lty_start_date_gmt() ) < strtotime( 'now' );
			}

			return false;
		}

		/**
		 * Check if the lottery is closed.
		 *
		 * @since 1.0.0
		 * @return bool
		 * */
		public function is_closed() {
			// If already lottery closed by any action.
			if ( $this->get_lty_closed() ) {
				return true;
			}

			if ( '2' !== $this->get_lty_lottery_schedule_type() && $this->get_lty_end_date_gmt() ) {
				return strtotime( $this->get_lty_end_date_gmt() ) < strtotime( 'now' );
			}

			return false;
		}

		/**
		 * Get the current start date GMT.
		 *
		 * @since 1.0.0
		 * @return string
		 * */
		public function get_current_start_date_gmt() {
			$start_date = $this->get_lty_start_date_gmt();
			if (!empty($this->get_lty_relisted_date_gmt())) {
				$start_date = $this->get_lty_relisted_date_gmt();
			}

			/**
			 * This hook is used to alter the lottery product current start date GMT.
			 *
			 * @since 1.0.0
			 */
			return apply_filters('lty_lottery_current_start_date_gmt', $start_date, $this);
		}

		/**
		 * Get the formatted start date text.
		 *
		 * @since 9.0.0
		 * @return string
		 * */
		public function get_fomatted_start_date_text() {
			$display_tz = ( 'yes' === get_option('lty_settings_hide_tz_display_in_single_product_page') ) ? false : true;

			return LTY_Date_Time::get_wp_format_datetime($this->get_lty_start_date(), false, false, false, ' ', $display_tz);
		}

		/**
		 * Get the formatted end date text.
		 *
		 * @since 9.0.0
		 * @return string
		 * */
		public function get_fomatted_end_date_text() {
			$display_tz = ( 'yes' === get_option('lty_settings_hide_tz_display_in_single_product_page') ) ? false : true;

			return LTY_Date_Time::get_wp_format_datetime($this->get_lty_end_date(), false, false, false, ' ', $display_tz);
		}

		/**
		 * Returns the date ranges text.
		 *
		 * @since 1.0.0
		 * @return string
		 * */
		public function get_date_ranges_text() {
			if (!$this->is_started()) {
				$text = get_option('lty_settings_single_product_time_left_start_label');
			} else {
				$text = get_option('lty_settings_single_product_time_left_end_label');
			}

			/**
			 * This hook is used to alter the lottery product date ranges text.
			 *
			 * @since 1.0.0
			 */
			return apply_filters('lty_lottery_product_date_ranges_text', $text, $this);
		}

		/**
		 * Get question answer selection type.
		 *
		 * @since 1.0.0
		 * @return string
		 * */
		public function get_question_answer_selection_type() {
			return !empty($this->get_lty_question_answer_selection_type()) ? $this->get_lty_question_answer_selection_type() : 1;
		}

		/**
		 * Is valid question answer?.
		 *
		 * @since 1.0.0
		 * @return bool
		 * */
		public function is_valid_question_answer() {
			// Product Level.
			if ('1' == $this->get_question_answer_selection_type()) {
				// Return if the question answer is not enabled.
				if ('yes' !== $this->get_lty_manage_question()) {
					return false;
				}
				// Global Level.
			} elseif ('yes' !== get_option('lty_settings_manage_question_global_setting')) {
				return false;
			}

			$questions = $this->get_question_answers();

			// Return if the question answer is not configured.
			if (!isset($questions[0]['answers']) || !lty_check_is_array($questions[0]['answers'])) {
				return false;
			}

			return true;
		}

		/**
		 * Is force answer enabled.
		 *
		 * @since 1.0.0
		 * @return string
		 * */
		public function is_force_answer_enabled() {
			if ('1' == $this->get_question_answer_selection_type()) {
				// Product Level.
				return $this->get_lty_force_answer();
			} else {
				// Global Level.
				return get_option('lty_settings_force_answer_global_setting', 'no');
			}
		}

		/**
		 * Is question answer time limit exists?.
		 *
		 * @since 6.7.0
		 * @param int $customer_id Customer ID.
		 * @return bool
		 * */
		public function is_customer_question_answer_time_limit_exists( $customer_id = false ) {
			// Return false if the question answer time limit is set as unlimited.
			if ('2' != $this->get_question_answer_time_limit_type()) {
				return false;
			}

			// Return if the product already exists in the cart.
			if (lty_lottery_product_exists_in_cart($this->get_id())) {
				return false;
			}

			if (!$customer_id) {
				$customer_id = lty_get_current_user_cart_session_value();
			}

			$viewed_data = $this->get_lty_question_answer_viewed_data();
			if (!isset($viewed_data[$customer_id])) {
				return false;
			}

			$viewed_date = $this->get_customer_question_answer_time_limit_date($customer_id, $viewed_data[$customer_id]);
			$date_now = LTY_Date_Time::get_date_time_object('now', true)->format('Y/m/d H:i:s');
			if ($viewed_date > $date_now) {
				return false;
			}

			return true;
		}

		/**
		 * Get the answer countdown timer date.
		 *
		 * @since 6.7.0
		 * @param int    $customer_id Customer ID.
		 * @param string $viewed_date Viewed date.
		 * @return string
		 * */
		public function get_customer_question_answer_time_limit_date( $customer_id = false, $viewed_date = false ) {
			if (!$customer_id) {
				$customer_id = lty_get_current_user_cart_session_value();
			}

			if (!$viewed_date) {
				$viewed_data = $this->get_lty_question_answer_viewed_data();
				$viewed_date = isset($viewed_data[$customer_id]) ? $viewed_data[$customer_id] : LTY_Date_Time::get_mysql_date_time_format('now', true);
			}

			$time_limit = $this->get_question_answer_time_limit();
			$date_object = LTY_Date_Time::get_date_time_object($viewed_date, true);
			$date_object->modify('+' . $time_limit['number'] . ' ' . $time_limit['unit']);

			return $date_object->format('Y/m/d H:i:s');
		}

		/**
		 * Get the formatted answer countdown time limit.
		 *
		 * @since 6.7.0
		 * @return array
		 * */
		public function get_formatted_question_answer_time_limit() {
			return wp_parse_args(
					$this->get_lty_question_answer_time_limit(),
					array(
						'unit' => 'minutes',
						'number' => '5',
					)
			);
		}

		/**
		 * Get the answer countdown time limit.
		 *
		 * @since 6.7.0
		 * @return array
		 * */
		public function get_question_answer_time_limit() {
			if ('1' == $this->get_question_answer_selection_type()) {
				return $this->get_formatted_question_answer_time_limit();
			}

			return get_option('lty_settings_question_answer_time_limit');
		}

		/**
		 * Get the answer countdown time limit type.
		 *
		 * @since 6.7.0
		 * @return string
		 * */
		public function get_question_answer_time_limit_type() {
			if ('1' == $this->get_question_answer_selection_type()) {
				return $this->get_lty_question_answer_time_limit_type();
			}

			return get_option('lty_settings_question_answer_time_limit_type');
		}

		/**
		 * Get question answers.
		 *
		 * @since 1.0.0
		 * @return array
		 * */
		public function get_question_answers() {
			// Product Level.
			if ('1' == $this->get_question_answer_selection_type()) {
				return $this->get_lty_questions();
			}

			// Global Level.
			return get_option('lty_question_answers_global', array());
		}

		/**
		 * Get the answers.
		 *
		 * @since 1.0.0
		 * @return array
		 * */
		public function get_answers() {
			$questions = $this->get_question_answers();

			return lty_check_is_array( $questions ) && isset( $questions[0] ) ? $questions[0]['answers'] : array();
		}

		/**
		 * Incorrectly selected answer restriction is enabled.
		 *
		 * @since 1.0.0
		 * @return string
		 * */
		public function incorrectly_selected_answer_restriction_is_enabled() {
			// Return if verify answer is enabled.
			if ('yes' === $this->is_verify_answer_enabled()) {
				return 'no';
			}

			if ('1' == $this->get_question_answer_selection_type()) {
				// Product Level.
				return $this->get_lty_restrict_incorrectly_selected_answer();
			} else {
				// Global Level.
				return get_option('lty_settings_restrict_incorrectly_selected_answer_global_setting', 'no');
			}
		}

		/**
		 * Restrict incorrectly selected answer.
		 *
		 * @since 1.0.0
		 * @return bool
		 * */
		public function restrict_incorrectly_selected_answer() {
			if ('yes' !== $this->is_force_answer_enabled() || 'yes' !== $this->incorrectly_selected_answer_restriction_is_enabled()) {
				return false;
			}

			return true;
		}

		/**
		 * Is verify answer enabled.
		 *
		 * @return string
		 */
		public function is_verify_answer_enabled() {
			if ('1' == $this->get_question_answer_selection_type()) {
				// Product Level.
				return $this->get_lty_validate_correct_answer();
			} else {
				// Global Level.
				return get_option('lty_settings_validate_correct_answer_global_setting', 'no');
			}
		}

		/**
		 * Validate user incorrect answer.
		 *
		 * @since 1.0.0
		 * @param int $user_id User ID.
		 * @return bool
		 */
		public function validate_user_incorrect_answer( $user_id ) {
			// Return if the force answer/validate correct answer is not enabled.
			if ('yes' !== $this->is_force_answer_enabled() || 'yes' === $this->incorrectly_selected_answer_restriction_is_enabled() || 'yes' !== $this->is_verify_answer_enabled()) {
				return false;
			}

			// Return if unlimited attempts type is selected.
			if ('2' == $this->verify_question_answer_type()) {
				return false;
			}

			return in_array($user_id, (array) $this->get_lty_incorrect_answer_user_ids());
		}

		/**
		 * Verify question answer type.
		 *
		 * @return int
		 * */
		public function verify_question_answer_type() {
			if ('1' == $this->get_question_answer_selection_type()) {
				// Product Level.
				return '' != $this->get_lty_verify_answer_type() ? $this->get_lty_verify_answer_type() : '1';
			} else {
				// Global Level.
				return get_option('lty_settings_verify_answer_type_global', 1);
			}
		}

		/**
		 * Get question answer attempts.
		 *
		 * @return int
		 * */
		public function get_question_answer_attempts() {
			if ('1' == $this->get_question_answer_selection_type()) {
				// Product Level.
				return '' != $this->get_lty_question_answer_attempts() ? absint($this->get_lty_question_answer_attempts()) : '1';
			} else {
				// Global Level.
				return '' != get_option('lty_settings_question_answer_attempts_global', 1) ? get_option('lty_settings_question_answer_attempts_global', 1) : 1;
			}
		}

		/**
		 * Is limited answer attempts reached.
		 *
		 * @since 1.0.0
		 * @param int $user_id User ID.
		 * @return bool
		 * */
		public function is_limited_answer_attempts_reached( $user_id ) {
			return !$this->get_question_answer_remaining_attempts($user_id);
		}

		/**
		 * Get question answer remaining attempts.
		 *
		 * @since 1.0.0
		 * @param int $user_id User ID.
		 * @return int
		 * */
		public function get_question_answer_remaining_attempts( $user_id ) {
			$remaining_attempts_data = $this->get_lty_question_answer_attempts_data();
			if (!lty_check_is_array($remaining_attempts_data)) {
				return absint($this->get_question_answer_attempts());
			}

			return isset($remaining_attempts_data[$user_id]) ? absint($this->get_question_answer_attempts() - $remaining_attempts_data[$user_id]) : absint($this->get_question_answer_attempts());
		}

		/**
		 * Get countdown timer end date.
		 *
		 * @return string
		 * */
		public function get_countdown_timer_enddate() {
			$date = ( !$this->is_started() ) ? $this->get_lty_start_date() : $this->get_lty_end_date();
			$date_object = LTY_Date_Time::get_date_time_object($date, false, 'UTC');

			return $date_object->format('Y/m/d H:i:s');
		}

		/**
		 * Get the participate now button text.
		 *
		 * @return string
		 */
		public function get_participate_now_text() {
			if ( $this->is_predefined_button_enabled() && $this->is_automatic_ticket() ) {
				$button_label = get_option( 'lty_settings_predefined_buttons_participate_now_label', __( 'Participate now', 'lottery-for-woocommerce' ) );
			} else {
				$automatic_button_label = lty_get_single_product_price_label();
				$button_label           = $this->is_manual_ticket() ? get_option( 'lty_settings_single_product_user_chooses_ticket_add_to_cart_label', $automatic_button_label ) : $automatic_button_label;
			}

			$lottery_price = lty_price( wc_get_price_to_display( $this, array( 'qty' => $this->get_preset_tickets() ) ) );
			if ( $this->get_price() ) {
				$lottery_price = sprintf( "<span class='lty-lottery-price' data-price-amount='%s'>%s</span>", $this->get_price(), $lottery_price );
			}

			$quantity    = ! $this->is_manual_ticket() ? $this->get_preset_tickets() : 1;
			$button_text = str_replace( array( '{lottery_price}', '{ticket_quantity}' ), array( $lottery_price, sprintf( "<span class='lty-ticket-quantity'>%s</span>", $quantity ) ), $button_label );

			/**
			 * This hook is used to alter the lottery product participate now text.
			 *
			 * @since 1.0.0
			 */
			return apply_filters( 'lty_lottery_product_participate_now_text', $button_text, $this );
		}

		/**
		 * Get the lucky dip fixed quantity button text.
		 *
		 * @since 11.4.0
		 * @param string $quantity
		 * @return string
		 */
		public function get_lucky_dip_fixed_quantity_text( $quantity ) {
			/**
			 * This hook is used to alter the lottery product lucky dip fixed quantity text.
			 *
			 * @since 11.4.0
			 */
			return apply_filters( 'lty_lottery_product_lucky_dip_fixed_quantity_text', str_replace( array( '{ticket_quantity}' ), array( $quantity ), get_option( 'lty_settings_lucky_dip_fixed_quantity_shortcode_label', __( 'Lucky Dip <br> {ticket_quantity} Qty </br>', 'lottery-for-woocommerce' ) ) ), $this );
		}

		/**
		 * Is lucky dip?.
		 *
		 * @since 11.4.0
		 * @return bool
		 * */
		public function is_lucky_dip() {
			return 'yes' === $this->get_lty_lucky_dip();
		}

		/**
		 * Get the lucky dip button text.
		 *
		 * @since 1.0.0
		 * @return string
		 */
		public function get_lucky_dip_text() {

			/**
			 * This hook is used to alter the lottery product lucky dip text.
			 *
			 * @since 1.0.0
			 */
			return apply_filters( 'lty_lottery_product_lucky_dip_text', get_option( 'lty_settings_single_product_lucky_dip_button_label', __( 'Lucky Dip', 'lottery-for-woocommerce' ) ), $this );
		}

		/**
		 * Get min quantity which can be purchased at once.
		 *
		 * @since 1.0.0
		 * @return int|string
		 */
		public function get_min_purchase_quantity() {
			if ('3' == get_option('lty_settings_guest_user_participate_type')) {
				return 1;
			}

			return ( '' != $this->get_lty_user_minimum_tickets() ) ? absint($this->get_lty_user_minimum_tickets()) : 1;
		}

		/**
		 * Get the maximum quantity which can be purchased at once.
		 *
		 * @since 1.0.0
		 * @return int|string
		 */
		public function get_max_purchase_quantity() {
			if ('3' == get_option('lty_settings_guest_user_participate_type')) {
				return ( $this->get_lty_order_maximum_tickets() ) ? $this->get_lty_order_maximum_tickets() : $this->get_stock_quantity();
			}

			$max_quantity = ( $this->get_stock_quantity() >= (int) $this->get_lty_user_maximum_tickets() ) ? ( (int) $this->get_lty_user_maximum_tickets() - $this->get_user_placed_ticket_count() ) : $this->get_stock_quantity();

			return ( $this->get_lty_order_maximum_tickets() && $max_quantity >= $this->get_lty_order_maximum_tickets() ) ? $this->get_lty_order_maximum_tickets() : $max_quantity;
		}

		/**
		 * Get the preset tickets quantity.
		 *
		 * @since 8.6.0
		 * @return int|string
		 */
		public function get_preset_tickets() {
			$max_quantity = ( '2' === get_option('lty_settings_quantity_selector_type') && '2' === $this->get_lty_ticket_range_slider_type() ) ? $this->get_lty_maximum_tickets() : $this->get_max_purchase_quantity();

			return ( intval($this->get_lty_preset_tickets()) >= intval($this->get_min_purchase_quantity()) ) && ( intval($this->get_lty_preset_tickets()) <= intval($max_quantity) ) ? $this->get_lty_preset_tickets() : $this->get_min_purchase_quantity();
		}

		/**
		 * Get the remaining purchase limit per user.
		 *
		 * @return string
		 */
		public function get_remaining_purchase_limit_per_user() {
			return intval($this->get_lty_user_maximum_tickets()) - ( $this->get_user_placed_ticket_count() + intval($this->get_cart_ticket_count()) );
		}

		/**
		 * Get the overall ticket numbers.
		 *
		 * @return array
		 */
		public function get_overall_tickets() {
			if (isset($this->manual_ticket_numbers)) {
				return $this->manual_ticket_numbers;
			}

			$tickets_per_tab = $this->get_lty_tickets_per_tab();
			$tickets = array();
			$tickets_count = 0;
			$index = 0;
			$ticket_number = $this->get_ticket_start_number();
			for ($start_range = $this->get_ticket_start_number(); $start_range <= intval($this->get_lty_maximum_tickets() + $this->get_ticket_start_number() - 1); $start_range++) {

				// Works for Alphabets with Numbers.
				if ('1' == $this->get_alphabet_sequence_type() && $ticket_number == $this->get_ticket_start_number() + $this->get_lty_tickets_per_tab()) {
					$ticket_number = $this->get_ticket_start_number();
				}

				$tickets[] = $this->format_ticket_number($ticket_number, $index);

				++$ticket_number;

				if ($tickets_count == $tickets_per_tab - 1) {
					++$index;
					$tickets_per_tab = $tickets_per_tab + $this->get_lty_tickets_per_tab();
				}

				++$tickets_count;
			}

			$this->manual_ticket_numbers = $tickets;

			return $this->manual_ticket_numbers;
		}

		/**
		 * Is ticket count reached?.
		 *
		 * @return bool
		 * */
		public function is_ticket_count_reached() {
			return $this->get_purchased_ticket_count() >= $this->get_lty_minimum_tickets();
		}

		/**
		 * Get winner gift products selection method.
		 *
		 * @return string
		 */
		public function get_winner_product_selection_method() {
			// inside the site.
			return $this->get_lty_winning_product_selection();
		}

		/**
		 * Get winner gift products.
		 *
		 * @return string
		 */
		public function get_selected_gift_products() {
			if ('1' == $this->get_lty_winning_product_selection()) {
				// Inside the site.
				return $this->get_lty_selected_gift_products();
			} else {
				// Outside the site.
				return $this->get_lty_winner_outside_gift_items();
			}
		}

		/**
		 * Check the user purchase limit exists.
		 *
		 * @since 1.0.0
		 * @param int $user_id User ID.
		 * @return string
		 */
		public function user_purchase_limit_exists( $user_id = false ) {
			if ( ! $user_id ) {
				$user_id = get_current_user_id();
			}

			if ( '3' === get_option( 'lty_settings_guest_user_participate_type' ) ) {
				return false;
			}

			$args = array(
				'user_id'    => $user_id,
				'product_id' => $this->get_id(),
			);
			if ( $this->is_unlimited_scheduled_lottery() ) {
				$args['list_count'] = $this->get_current_relist_count();
			} else {
				$args['start_date'] = $this->get_current_start_date_gmt();
				$args['end_date']   = $this->get_lty_end_date_gmt();
			}

			$ticket_ids              = lty_get_ticket_ids( $args );
			$purchased_tickets_count = lty_check_is_array( $ticket_ids ) ? count( $ticket_ids ) : 0;

			return ( $purchased_tickets_count >= intval( $this->get_lty_user_maximum_tickets() ) );
		}

		/**
		 * Format ticket number.
		 *
		 * @since 1.0.0
		 * @param string $ticket_number Ticket number.
		 * @param string $index Index.
		 * @return string
		 */
		public function format_ticket_number( $ticket_number, $index = '' ) {
			// str_pad() function pads a string to a new length.
			$format_ticket = str_pad($ticket_number, strlen($this->get_ticket_start_number()), $this->get_ticket_start_number(), STR_PAD_LEFT);

			return $this->get_lty_ticket_prefix() . $this->get_alphabet_sequence($index) . $format_ticket . $this->get_lty_ticket_suffix();
		}

		/**
		 * Format ticket tab name.
		 *
		 * @since 1.0.0
		 * @param int|string $start_range Start range.
		 * @param int|string $end_range End range.
		 * @param int        $index Index.
		 * @return string
		 */
		public function format_ticket_tab_name( $start_range, $end_range, $index ) {
			if ('1' == $this->get_alphabet_sequence_type()) {
				return $this->get_alphabet_sequence($index);
			} else {
				return $this->format_ticket_number($start_range, $index) . '-' . $this->format_ticket_number($end_range, $index);
			}
		}

		/**
		 * Get alphabet sequence.
		 *
		 * @since 1.0.0
		 * @param int $index Index.
		 * @return string
		 */
		public function get_alphabet_sequence( $index ) {
			if ('yes' !== $this->get_lty_alphabet_with_sequence_nos_enabled()) {
				return '';
			}

			if (isset($this->alphabet_with_sequences[$this->get_id()][$index])) {
				return $this->alphabet_with_sequences[$this->get_id()][$index];
			}

			$char = 'A';
			$alphabet_sequences = array();
			for ($i = $this->get_ticket_start_number(); $i < intval($this->get_lty_maximum_tickets() + $this->get_ticket_start_number() - 1); $i++) {
				$alphabet_sequences[] = $char;
				++$char;
			}

			$this->alphabet_with_sequences = array();
			$this->alphabet_with_sequences[$this->get_id()] = array_values(array_filter($alphabet_sequences));
			if (!isset($this->alphabet_with_sequences[$this->get_id()][$index])) {
				return '';
			}

			return $this->alphabet_with_sequences[$this->get_id()][$index];
		}

		/**
		 * Get alphabet sequence type.
		 *
		 * @return string
		 */
		public function get_alphabet_sequence_type() {
			if ('yes' !== $this->get_lty_alphabet_with_sequence_nos_enabled()) {
				return '';
			}

			return $this->get_lty_alphabet_with_sequence_nos_type();
		}

		/**
		 * Format the automatic ticket number.
		 *
		 * @since 1.0.0
		 * @param int|string $ticket_number Ticket number.
		 * @return string
		 */
		public function format_automatic_ticket_number( $ticket_number ) {
			// str_pad() function pads a string to a new length.
			$format_ticket = str_pad($ticket_number, strlen($this->get_automatic_ticket_start_number()), $this->get_automatic_ticket_start_number(), STR_PAD_LEFT);

			if (!empty($this->get_lty_ticket_prefix())) {
				$format_ticket = $this->get_lty_ticket_prefix() . $format_ticket;
			}

			if (!empty($this->get_lty_ticket_suffix())) {
				$format_ticket = $format_ticket . $this->get_lty_ticket_suffix();
			}

			return $format_ticket;
		}

		/**
		 * Get the ticket start number.
		 *
		 * @return int
		 */
		public function get_ticket_start_number() {
			if ('' === $this->get_lty_ticket_start_number()) {
				return 1;
			}

			return $this->get_lty_ticket_start_number();
		}

		/**
		 * Get the automatic ticket start number.
		 *
		 * @return int
		 */
		public function get_automatic_ticket_start_number() {
			$ticket_start_number = 1;
			if ($this->is_automatic_sequential_ticket()) {
				$ticket_start_number = $this->get_lty_ticket_sequential_start_number();
			} elseif ($this->is_automatic_shuffled_ticket()) {
				$ticket_start_number = $this->get_lty_ticket_shuffled_start_number();
			}

			return !empty($ticket_start_number) ? $ticket_start_number : 1;
		}

		/**
		 * Is manual lottery.
		 *
		 * @return bool
		 */
		public function is_manual_ticket() {
			return '2' == $this->get_ticket_generation_type();
		}

		/**
		 * Is automatic ticket.
		 *
		 * @return bool
		 */
		public function is_automatic_ticket() {
			return '1' == $this->get_ticket_generation_type();
		}

		/**
		 * Is automatic sequential ticket.
		 *
		 * @return bool
		 */
		public function is_automatic_sequential_ticket() {
			if (!$this->is_automatic_ticket() || '2' != $this->get_ticket_number_type()) {
				return false;
			}

			return true;
		}

		/**
		 * Is automatic shuffled ticket.
		 *
		 * @return bool
		 */
		public function is_automatic_shuffled_ticket() {
			if (!$this->is_automatic_ticket() || '3' != $this->get_ticket_number_type()) {
				return false;
			}

			return true;
		}

		/**
		 * Is automatic random lottery.
		 *
		 * @return bool
		 */
		public function is_automatic_random_ticket() {
			if (!$this->is_automatic_ticket() || '1' != $this->get_ticket_number_type()) {
				return false;
			}

			return true;
		}

		/**
		 * Get ticket numbers based on start number for sequential/shuffle type.
		 *
		 * @return array
		 */
		public function get_ticket_numbers_based_on_start_number() {
			if (isset($this->automatic_ticket_numbers)) {
				return $this->automatic_ticket_numbers;
			}

			$ticket_start_number = $this->get_automatic_ticket_start_number();
			$maximum_tickets = absint($this->get_lty_maximum_tickets() + $ticket_start_number - 1);

			$ticket_numbers = array();
			for ($i = $ticket_start_number; $i <= $maximum_tickets; $i++) {
				$ticket_numbers[] = $this->format_automatic_ticket_number($i);
			}

			$this->automatic_ticket_numbers = $ticket_numbers;

			return $this->automatic_ticket_numbers;
		}

		/**
		 * Format and Update automatic ticket numbers.
		 *
		 * @since 11.2.0
		 */
		public function format_and_update_automatic_ticket_numbers() {
			$ticket_numbers=$this->get_ticket_numbers_based_on_start_number();

			$this->update_post_meta('lty_formatted_automatic_ticket_numbers', $ticket_numbers);

			$this->set_prop('lty_formatted_automatic_ticket_numbers', $ticket_numbers);
		}

		/**
		 * Get the formatted sequential ticket numbers.
		 *
		 * @since 11.2.0
		 */
		public function get_formatted_sequential_ticket_numbers() {
			$ticket_numbers = $this->get_lty_formatted_automatic_ticket_numbers();
			if ( ! lty_check_is_array( $ticket_numbers ) ) {
				$this->format_and_update_automatic_ticket_numbers();
				$ticket_numbers = $this->get_lty_formatted_automatic_ticket_numbers();
			}

			/**
			 * This hook is used to alter the formatted sequential ticket numbers.
			 *
			 * @since 12.4.0
			 */
			return apply_filters( 'lty_formatted_sequential_ticket_numbers', $ticket_numbers, $this );
		}

		/**
		 * Get the formatted shuffle ticket numbers.
		 *
		 * @since 11.2.0
		 */
		public function get_formatted_shuffle_ticket_numbers() {
			$ticket_numbers = $this->get_lty_formatted_automatic_ticket_numbers();
			if ( ! lty_check_is_array( $ticket_numbers ) ) {
				$this->format_and_update_automatic_ticket_numbers();
				$ticket_numbers = $this->get_lty_formatted_automatic_ticket_numbers();
			}

			/**
			 * This hook is used to alter the formatted suffled ticket numbers.
			 *
			 * @since 12.4.0
			 */
			return apply_filters( 'lty_formatted_shuffled_ticket_numbers', $ticket_numbers, $this );
		}

		/**
		 * Get ticket generation type.
		 *
		 * @return string
		 */
		public function get_ticket_generation_type() {
			$ticket_generation_type = $this->get_lty_ticket_generation_type();

			return '' == $ticket_generation_type ? ( ( 'yes' === $this->get_lty_choose_ticket_numbers() ) ? '2' : '1' ) : $ticket_generation_type;
		}

		/**
		 * Get ticket number type.
		 *
		 * @return string
		 */
		public function get_ticket_number_type() {
			$ticket_number_type = $this->get_lty_ticket_number_type();
			if (!$ticket_number_type) {
				return '1';
			}

			return $ticket_number_type;
		}

		/**
		 * Get reserved tickets data.
		 *
		 * @return array
		 */
		public function get_reserved_tickets_data() {
			$reserved_tickets_data = $this->get_lty_manual_reserved_tickets();

			return lty_check_is_array($reserved_tickets_data) ? $reserved_tickets_data : array();
		}

		/**
		 * Get reserved ticket values.
		 *
		 * @return array
		 */
		public function get_reserved_ticket_values( $ticket ) {
			$reserved_tickets = $this->get_reserved_tickets_data();
			if (!lty_check_is_array($reserved_tickets)) {
				return array();
			}

			return isset($reserved_tickets[$ticket]) ? $reserved_tickets[$ticket] : array();
		}

		/**
		 * Get reserved tickets.
		 *
		 * @return array
		 */
		public function get_reserved_tickets() {
			$reserved_tickets = $this->get_reserved_tickets_data();

			return lty_check_is_array($reserved_tickets) ? array_keys($reserved_tickets) : array();
		}

		/**
		 * Get the user ids count.
		 *
		 * @since 9.6.0
		 * @return int
		 */
		public function get_lottery_user_ids_count() {
			$unique_ticket_ids = lty_get_unique_lottery_ticket_ids($this);

			return count($unique_ticket_ids);
		}

		/**
		 * Get the user placed ticket count.
		 *
		 * @return string
		 */
		public function get_user_placed_ticket_count() {
			$ticket_count = get_transient( 'lty_user_placed_ticket_count_' . $this->get_id() . '_' . get_current_user_id() );
			if ( false !== $ticket_count ) {
				return $ticket_count;
			}

			$ticket_count = lty_check_is_array( $this->get_user_placed_tickets() ) ? intval( count( $this->get_user_placed_tickets() ) ) : 0;
			set_transient( 'lty_user_placed_ticket_count_' . $this->get_id() . '_' . get_current_user_id(), $ticket_count, HOUR_IN_SECONDS );

			return $ticket_count;
		}

		/**
		 * Get the user placed tickets.
		 *
		 * @since 1.0.0
		 * @return array
		 */
		public function get_user_placed_tickets() {
			if (isset($this->user_placed_tickets)) {
				return $this->user_placed_tickets;
			}

			$this->user_placed_tickets = lty_get_user_placed_ticket_numbers_by_product_id($this);

			return $this->user_placed_tickets;
		}

		/**
		 * Get the user purchased ticket count.
		 *
		 * @return integer
		 */
		public function get_user_purchased_ticket_count() {
			$ticket_count = get_transient( 'lty_user_purchased_ticket_count_' . $this->get_id() . '_' . get_current_user_id() );
			if ( false !== $ticket_count ) {
				return $ticket_count;
			}

			$ticket_count = lty_check_is_array( $this->get_user_purchased_tickets() ) ? intval( count( $this->get_user_purchased_tickets() ) ) : 0;
			set_transient( 'lty_user_purchased_ticket_count_' . $this->get_id() . '_' . get_current_user_id(), $ticket_count, HOUR_IN_SECONDS );

			return $ticket_count;
		}

		/**
		 * Get the user purchased tickets.
		 *
		 * @return array
		 */
		public function get_user_purchased_tickets() {
			if (isset($this->user_purchased_tickets)) {
				return $this->user_purchased_tickets;
			}

			$this->user_purchased_tickets = lty_get_user_purchased_ticket_numbers_by_product_id($this);

			return $this->user_purchased_tickets;
		}

		/**
		 * Get the placed ticket count.
		 *
		 * @return integer
		 */
		public function get_placed_ticket_count() {
			$ticket_count = get_transient( 'lty_placed_ticket_count_' . $this->get_id() );
			if ( false !== $ticket_count ) {
				return $ticket_count;
			}

			$ticket_count = lty_check_is_array( $this->get_placed_tickets() ) ? intval( count( $this->get_placed_tickets() ) ) : 0;
			set_transient( 'lty_placed_ticket_count_' . $this->get_id(), $ticket_count, HOUR_IN_SECONDS );

			return $ticket_count;
		}

		/**
		 * Get the placed tickets.
		 *
		 * @param boolean $force
		 * @return array
		 */
		public function get_placed_tickets( $force = false ) {
			if (isset($this->placed_tickets)&& !$force) {
				return $this->placed_tickets;
			}

			$this->placed_tickets = lty_get_placed_ticket_numbers_by_product_id($this);

			return $this->placed_tickets;
		}

		/**
		 * Get the purchased tickets count.
		 *
		 * @since 1.0.0
		 * @return int
		 */
		public function get_purchased_ticket_count() {
			return lty_get_purchased_tickets_count_by_product_id( $this );
		}

		/**
		 * Get the remaining ticket count.
		 *
		 * @since 9.0.0
		 * @return int
		 */
		public function get_remaining_ticket_count() {
			$maximum_tickets = intval($this->get_lty_maximum_tickets());

			return $maximum_tickets < $this->get_purchased_ticket_count() ? 0 : $maximum_tickets - $this->get_purchased_ticket_count();
		}

		/**
		 * Get the purchased tickets.
		 *
		 * @since 1.0.0
		 * @return array
		 */
		public function get_purchased_tickets() {
			if (isset($this->purchased_tickets)) {
				return $this->purchased_tickets;
			}

			$this->purchased_tickets = lty_get_purchased_ticket_numbers_by_product_id($this);

			return $this->purchased_tickets;
		}

		/**
		 * Get purchased tickets by order.
		 *
		 * @return array
		 */
		public function get_purchased_tickets_by_order( $order_id, $user_id = false ) {
			return lty_get_purchased_ticket_numbers_by_order($this, $order_id, $user_id);
		}

		/**
		 * Get lottery Ticket count.
		 *
		 * @deprecated Use get_purchased_ticket_count() method instead.
		 * @return int
		 * */
		public function get_lottery_ticket_count() {
			wc_deprecated_function( 'get_lottery_ticket_count', '11.2.0', 'get_purchased_ticket_count' );
			return $this->get_purchased_ticket_count();
		}

		/**
		 * Get the looser ticket count.
		 *
		 * @return string
		 */
		public function get_looser_ticket_count() {
			return intval(count($this->get_looser_ticket_ids()));
		}

		/**
		 * Get the looser ticket ids.
		 *
		 * @return array
		 */
		public function get_looser_ticket_ids() {
			if (isset($this->looser_ticket_ids)) {
				return $this->looser_ticket_ids;
			}

			$this->looser_ticket_ids = lty_get_lottery_looser_ticket_ids($this);

			return $this->looser_ticket_ids;
		}

		/**
		 * Get the placed ticket IDs.
		 *
		 * @return array
		 */
		public function get_placed_ticket_ids() {
			if (isset($this->placed_ticket_ids)) {
				return $this->placed_ticket_ids;
			}

			$this->placed_ticket_ids = lty_get_placed_lottery_product_ticket_ids($this);

			return $this->placed_ticket_ids;
		}

		/**
		 * Get the purchased ticket IDs.
		 *
		 * @return array
		 */
		public function get_purchased_ticket_ids( $order_by = 'ID', $order = 'DESC' ) {
			return lty_get_purchased_lottery_product_ticket_ids($this, $order_by, $order);
		}

		/**
		 * Get the cart ticket count.
		 *
		 * @return int
		 */
		public function get_cart_ticket_count() {
			return lty_get_cart_lottery_ticket_count($this->get_id());
		}

		/**
		 * Get the cart tickets.
		 *
		 * @return array
		 */
		public function get_cart_tickets() {
			return lty_get_cart_lottery_tickets($this->get_id());
		}

		/**
		 * Get the remaining tickets.
		 *
		 * @return array
		 */
		public function get_remaining_tickets() {
			return array_diff($this->get_overall_tickets(), $this->get_placed_tickets(), $this->get_cart_tickets(), $this->get_reserved_tickets());
		}

		/**
		 * Get the current user winner IDs.
		 *
		 * @return array
		 */
		public function get_current_user_winner_ids() {
			// Return if user does not exist(Guest user).
			if ( ! wp_get_current_user()->exists() ) {
				return array();
			}

			$list_count = false;
			$start_date = false;
			if ( $this->is_unlimited_scheduled_lottery() ) {
				$list_count = $this->get_current_relist_count();
			} else {
				$start_date = $this->get_current_start_date_gmt();
			}

			return lty_get_user_winner_ids_by_product_id( wp_get_current_user()->ID, $this->get_id(), $start_date, $list_count );
		}

		/**
		 * Get the current lottery winner IDs.
		 *
		 * @return array
		 */
		public function get_current_winner_ids() {
			$start_date   = false;
			$relist_count = false;
			if ( $this->is_unlimited_scheduled_lottery() ) {
				$relist_count = $this->get_current_relist_count();
			} else {
				$start_date = $this->get_current_start_date_gmt();
			}

			return lty_get_lottery_winners_by_product_id( $this->get_id(), $start_date, false, $relist_count );
		}

		/**
		 * Get winner user IDs.
		 *
		 * @return array
		 */
		public function get_winner_user_ids() {
			return lty_get_current_winner_user_ids( $this->get_id() );
		}

		/**
		 * Get winner user emails.
		 *
		 * @return array
		 */
		public function get_winner_user_emails() {
			return lty_get_current_winner_user_emails( $this->get_id() );
		}

		/**
		 * Display countdown timer in shop.
		 *
		 * @return bool
		 */
		public function display_countdown_timer_in_shop() {
			$display = true;
			if ( '1' === $this->get_lty_hide_countdown_timer_selection_type() || ! $this->get_lty_hide_countdown_timer_selection_type()) {
				$display = ( 'yes' === get_option( 'lty_settings_restrict_countdown_timer_in_shop' ) ) ? false : true;
			} else {
				$display = ( 'yes' === $this->get_lty_hide_countdown_timer_in_shop() ) ? false : true;
			}

			if ( $display && $this->is_unlimited_scheduled_lottery() ) {
				$display = $this->has_lottery_status( 'lty_lottery_not_started' );
			}

			/**
			 * This hook is used to alter the display of lottery product countdown timer in the shop.
			 *
			 * @since 1.0
			 */
			return apply_filters( 'lty_display_countdown_timer_in_shop', $display, $this );
		}

		/**
		 * Display countdown timer in single product.
		 *
		 * @return bool
		 */
		public function display_countdown_timer_in_single_product() {
			$display = true;
			if ( '1' === $this->get_lty_hide_countdown_timer_selection_type() || ! $this->get_lty_hide_countdown_timer_selection_type() ) {
				$display = ( 'yes' === get_option( 'lty_settings_restrict_countdown_timer_in_single_product_page' ) ) ? false : true;
			} else {
				$display = ( 'yes' === $this->get_lty_hide_countdown_timer_in_single_product() ) ? false : true;
			}

			if ( $display && $this->is_unlimited_scheduled_lottery() ) {
				$display = $this->has_lottery_status( 'lty_lottery_not_started' );
			}

			/**
			 * This hook is used to alter the display of lottery product countdown timer in the single product page.
			 *
			 * @since 1.0
			 */
			return apply_filters( 'lty_display_countdown_timer_in_single_product', $display, $this );
		}

		/**
		 * Display progress bar in shop.
		 *
		 * @return bool
		 */
		public function display_progress_bar_in_shop() {
			$display = true;
			if ('1' == $this->get_lty_hide_progress_bar_selection_type() || !$this->get_lty_hide_progress_bar_selection_type()) {
				$display = ( 'yes' === get_option('lty_settings_restrict_progress_bar_shop_page') ) ? true : false;
			} else {
				$display = ( 'yes' === $this->get_lty_hide_progress_bar_in_shop() ) ? false : true;
			}

			/**
			 * This hook is used to alter the display of lottery product progress bar in the shop.
			 *
			 * @since 1.0
			 */
			return apply_filters('lty_display_progress_bar_in_shop', $display, $this);
		}

		/**
		 * Display progress bar in single product.
		 *
		 * @return bool
		 */
		public function display_progress_bar_in_single_product() {
			$display = true;
			if ('1' == $this->get_lty_hide_progress_bar_selection_type() || !$this->get_lty_hide_progress_bar_selection_type()) {
				$display = 'yes' === get_option('lty_settings_restrict_progress_bar_single_product_page');
			} else {
				$display = 'no' === $this->get_lty_hide_progress_bar_in_single_product();
			}

			/**
			 * This hook is used to alter the display of lottery product progress bar in the single product page.
			 *
			 * @since 1.0.0
			 */
			return apply_filters('lty_display_progress_bar_in_single_product', $display, $this);
		}

		/**
		 * Get lottery ticket numbers.
		 *
		 * @return array
		 */
		public function get_ticket_numbers( $order_item ) {
			$bool           = true;
			$ticket_numbers = array();
			$list_count     = $this->is_unlimited_scheduled_lottery() ? $this->get_lty_list_count() : false;
			while ( $bool ) {
				$placed_tickets = $this->get_placed_tickets(true);
				$ticket_numbers = lty_get_lottery_ticket_numbers($this, $order_item);
				if (!lty_check_is_array($ticket_numbers)) {
					break;
				}

				$check_ticket_exists = lty_check_is_ticket_number_exists( $ticket_numbers, $this->get_id(), false, false, $list_count );
				if (lty_check_is_array($check_ticket_exists)) {
					continue;
				}

				$hold_tickets = array_filter( (array) get_post_meta( $this->get_id(), '_lty_hold_tickets', true ) );
				$hold_tickets = array_intersect($hold_tickets, $placed_tickets);
				$hold_tickets_exists = array_intersect($ticket_numbers, $hold_tickets);
				if (lty_check_is_array($hold_tickets_exists)) {
					continue;
				}

				$result = $this->maybe_update_hold_tickets($ticket_numbers, $hold_tickets);
				if (!$result) {
					continue;
				}

				lty_update_lottery_post_meta($this->get_id(), 'lty_hold_tickets', $hold_tickets);

				$bool = false;
			}

			return $ticket_numbers;
		}

		/**
		 * May be update hold tickets in respective meta data if tickets are not exists.
		 * 
		 * @since 12.0.0
		 * @param array $ticket_numbers
		 * @param array $hold_tickets
		 * @return int|boolean
		 */
		public function maybe_update_hold_tickets( $ticket_numbers, $hold_tickets ) {
			if ( 'yes' !== get_option( 'lty_settings_restrict_db_hold_tickets', 'no' ) ) {
				return true;
			}

			/**
			 * This filter hook is used to restrict the update of hold tickets in the database.
			 *
			 * @since 11.7.0
			 * @param bool Whether to restrict or not.
			 * @param array $ticket_numbers Ticket numbers.
			 * @param array $hold_tickets Hold tickets.
			 * @param object $this Product object.
			 */
			if ( apply_filters( 'lty_restrict_db_hold_tickets_update', false, $ticket_numbers, $hold_tickets, $this ) ) {
				return true;
			}

			global $wpdb;
				
			// Add meta data of "_lty_hold_tickets" if not exists.
			if ( ! lty_check_is_array( $hold_tickets ) ) {
				update_post_meta( $this->get_id(), '_lty_hold_tickets', array() );
			}

			$regexp_format_ticket_numbers = '"' . implode( '"|"', $ticket_numbers ) . '"';
			$hold_tickets                 = array_merge( $hold_tickets, $ticket_numbers );

			return $wpdb->query( $wpdb->prepare( "UPDATE $wpdb->postmeta as meta SET meta.`meta_value`=%s WHERE meta.`post_id`=%d AND meta.`meta_key`='_lty_hold_tickets' AND meta.`meta_value` NOT REGEXP %s", maybe_serialize( wp_unslash( $hold_tickets ) ), $this->get_id(), $regexp_format_ticket_numbers ) );
		}

		/**
		 * Get predefined buttons rule.
		 *
		 * @return array
		 */
		public function get_predefined_buttons_rule() {
			$buttons_data = $this->get_lty_predefined_buttons_rule();

			return isset($buttons_data['predefined_buttons']) ? $buttons_data['predefined_buttons'] : array();
		}

		/**
		 * Get predefined buttons label.
		 *
		 * @return string
		 */
		public function get_predefined_buttons_label() {
			return '' != $this->get_lty_predefined_buttons_label() ? $this->get_lty_predefined_buttons_label() : 'Buy {ticket_quantity} ticket(s) for {price}';
		}

		/**
		 * Is predefined button enabled.
		 *
		 * @return bool
		 */
		public function is_predefined_button_enabled() {
			if ('yes' !== $this->get_lty_enable_predefined_buttons()) {
				return false;
			}

			if (!lty_check_is_array($this->get_predefined_buttons_rule())) {
				return false;
			}

			return true;
		}

		/**
		 * Get predefined buttons selection type.
		 *
		 * @return string
		 */
		public function get_predefined_buttons_selection_type() {
			$predefined_buttons_selection_type = $this->get_lty_predefined_buttons_selection_type();
			if (!$predefined_buttons_selection_type) {
				return 1;
			}

			return $this->get_lty_predefined_buttons_selection_type();
		}

		/**
		 * Is a valid predefined button?
		 *
		 * @since 8.0.0
		 * @param int $predefined_button_id
		 * @return boolean
		 */
		public function is_valid_predefined_button( $predefined_button_id ) {
			$predefined_button_rules = $this->get_predefined_buttons_rule();
			if (!isset($predefined_button_rules[$predefined_button_id])) {
				return false;
			}

			return true;
		}

		/**
		 * Get predefined buttons selection type.
		 *
		 * @return array
		 */
		public function get_predefined_buttons_data_based_on_rule_id( $predefined_button_id ) {
			$predefined_button_rule = $this->get_predefined_buttons_rule();
			$predefined_button_data = isset($predefined_button_rule[$predefined_button_id]) ? $predefined_button_rule[$predefined_button_id] : array();

			return lty_check_is_array($predefined_button_data) ? $predefined_button_data : array();
		}

		/**
		 * Get predefined buttons ticket quantity.
		 *
		 * @return int
		 */
		public function get_predefined_buttons_ticket_quantity( $predefined_button_id ) {
			$button_data = $this->get_predefined_buttons_data_based_on_rule_id($predefined_button_id);
			$ticket_quantity = !empty($button_data['ticket_quantity']) ? absint($button_data['ticket_quantity']) : 0;

			return $ticket_quantity;
		}

		/**
		 * Get predefined buttons discount amount.
		 *
		 * @return int|float
		 */
		public function get_predefined_buttons_discount_amount( $predefined_button_id ) {
			$button_data = $this->get_predefined_buttons_data_based_on_rule_id($predefined_button_id);
			if ('1' == $this->get_predefined_buttons_selection_type()) {
				return !empty($button_data['discount_percentage']) ? absint($button_data['discount_percentage']) : 0;
			}

			return !empty($button_data['fixed_price']) ? floatval($button_data['fixed_price']) : 0;
		}

		/**
		 * Get predefined buttons per ticket amount.
		 *
		 * @return int|float
		 */
		public function get_predefined_buttons_per_ticket_amount( $predefined_button_id, $quantity = false ) {
			$ticket_quantity = $quantity ? $quantity : $this->get_predefined_buttons_ticket_quantity($predefined_button_id);
			$discount_amount = $this->get_predefined_buttons_discount_amount($predefined_button_id);

			/**
			 * This hook is used to alter the predefined button product price.
			 *
			 * @since 12.0.0
			 */
			$product_price = apply_filters( 'lty_predefined_button_product_price', $this->get_price() );

			if ('1' == $this->get_predefined_buttons_selection_type()) {
				return 0 != $discount_amount ? floatval($product_price) - floatval($product_price) * $discount_amount / 100 : floatval($product_price);
			}

			return 0 != $discount_amount ? floatval($discount_amount) / $ticket_quantity : floatval($product_price);
		}

		/**
		 * Get predefined buttons total ticket amount.
		 *
		 * @since 7.3.0
		 *
		 * @return int|float
		 */
		public function get_predefined_buttons_total_ticket_amount( $predefined_button_id ) {
			$total_ticket_amount = $this->get_predefined_buttons_ticket_quantity($predefined_button_id) * $this->get_predefined_buttons_per_ticket_amount($predefined_button_id);

			/**
			 * This hook is used to alter the predefined button total ticket amount.
			 *
			 * @since 7.3.0
			 */
			return apply_filters('lty_predefined_buttons_total_ticket_amount', $total_ticket_amount);
		}

		/**
		 * Get predefined button label.
		 *
		 * @since 11.9.0
		 * @return string
		 */
		public function get_predefined_button_label( $predefined_button_id, $ticket_quantity ) {
			return str_replace(
				array(
					'{ticket_quantity}',
					'{price}',
					'{discount}',
				),
				array(
					$ticket_quantity,
					wc_price( $ticket_quantity * $this->get_predefined_buttons_per_ticket_amount( $predefined_button_id ) ),
					wc_format_decimal( $this->get_predefined_buttons_discount_amount( $predefined_button_id ), wc_get_price_decimals() ),
				),
				$this->get_predefined_buttons_label()
			);
		}

		/**
		 * Get predefined button badge label.
		 *
		 * @since 11.9.0
		 * @return string
		 */
		public function get_predefined_button_badge_label( $predefined_button_id, $ticket_quantity ) {
			return str_replace(
				array(
					'{ticket_quantity}',
					'{price}',
					'{discount}',
				),
				array(
					$ticket_quantity,
					wc_price( $ticket_quantity * $this->get_predefined_buttons_per_ticket_amount( $predefined_button_id ) ),
					wc_format_decimal( $this->get_predefined_buttons_discount_amount( $predefined_button_id ), wc_get_price_decimals() ),
				),
				$this->get_lty_predefined_buttons_badge_label()
			);
		}

		/**
		 * Get range slider predefined button discount label.
		 *
		 * @since 11.9.0
		 * @return string
		 */
		public function get_range_slider_predefined_discount_label( $predefined_button_id, $ticket_quantity ) {
			return str_replace(
				array(
					'{ticket_quantity}',
					'{price}',
					'{discount}',
				),
				array(
					$ticket_quantity,
					wc_price( $ticket_quantity * $this->get_predefined_buttons_per_ticket_amount( $predefined_button_id ) ),
					wc_format_decimal( $this->get_predefined_buttons_discount_amount( $predefined_button_id ), wc_get_price_decimals() ),
				),
				$this->get_lty_range_slider_predefined_discount_label()
			);
		}

		/**
		 * Get the instant winner statuses count.
		 *
		 * @since 11.0.0
		 * @return array.
		 */
		public function get_instant_winner_statuses_count() {
			if (isset($this->instant_winner_statuses_count)) {
				return $this->instant_winner_statuses_count;
			}

			$this->instant_winner_statuses_count = lty_get_instant_winner_statuses_count($this->get_id());

			return $this->instant_winner_statuses_count;
		}

		/**
		 * Get the instant winner available prizes count.
		 *
		 * @since 8.0.0
		 * @return int.
		 */
		public function get_instant_winner_available_prizes_count() {
			$instant_winner_statuses_count = $this->get_instant_winner_statuses_count();
			if (!lty_check_is_array($instant_winner_statuses_count)) {
				return 0;
			}

			$count = 0;
			$available_prizes_statuses = array( 'lty_available', 'lty_pending' );
			foreach ($available_prizes_statuses as $status) {
				$count += isset( $instant_winner_statuses_count[ $status ] ) ? $instant_winner_statuses_count[ $status ] : 0;
			}

			return $count;
		}

		/**
		 * Get the instant winner won prizes count.
		 *
		 * @since 8.0.0
		 * @return int.
		 */
		public function get_instant_winner_won_prizes_count() {
			$instant_winner_statuses_count = $this->get_instant_winner_statuses_count();

			return ( lty_check_is_array($instant_winner_statuses_count) && isset($instant_winner_statuses_count['lty_won']) ) ? $instant_winner_statuses_count['lty_won'] : 0;
		}

		/**
		 * Get the instant winner rule ids.
		 *
		 * @since 8.0.0
		 * @return array.
		 */
		public function get_instant_winner_rule_ids() {
			if (isset($this->instant_winner_ids)) {
				return $this->instant_winner_ids;
			}

			$this->instant_winner_ids = lty_get_instant_winner_rule_ids($this->get_id());

			return $this->instant_winner_ids;
		}

		/**
		 * Get the current instant winner log ids.
		 *
		 * @since 8.0.0
		 * @return array.
		 */
		public function get_current_instant_winner_log_ids() {
			if (isset($this->instant_winner_log_ids)) {
				return $this->instant_winner_log_ids;
			}

			$this->instant_winner_log_ids = lty_get_instant_winner_log_ids($this->get_id(), false, $this->get_current_relist_count());

			return $this->instant_winner_log_ids;
		}

		/**
		 * Get the current instant winner prize groups.
		 *
		 * @since 11.1.0
		 * @return array
		 */
		public function get_instant_winner_prize_group_ids() {
			if (isset($this->instant_winner_prize_group_ids)) {
				return $this->instant_winner_prize_group_ids;
			}

			$this->instant_winner_prize_group_ids  = lty_get_instant_winner_prize_group_ids( $this->get_id() );

			return $this->instant_winner_prize_group_ids;
		}

		/**
		 * Get the current instant winner prize groups data.
		 *
		 * @since 11.1.0
		 * @return array
		 */
		public function get_current_instant_winner_prize_groups_data() {
			if (isset($this->instant_winner_prize_groups_data)) {
				return $this->instant_winner_prize_groups_data;
			}

			$this->instant_winner_prize_groups_data  = lty_get_group_instant_winner_logs_data( $this->get_id(), $this->get_current_relist_count() );

			return $this->instant_winner_prize_groups_data;
		}

		/**
		 * Get the group instant winner available prizes count.
		 *
		 * @since 11.1.0
		 * @return int
		 */
		public function get_group_instant_winner_available_prizes_count( $prize_group_id ) {
			$status_counts = lty_get_group_instant_winner_logs_status_count( $prize_group_id, $this->get_current_relist_count() );

			$available_count = 0;
			if ( lty_check_is_array( $status_counts ) ) {
				$available_count = isset( $status_counts['lty_available'] ) ? intval( $status_counts['lty_available'] ) : 0;
				$available_count += isset( $status_counts['lty_pending'] ) ? intval( $status_counts['lty_pending'] ) : 0;
			}

			return $available_count;
		}

		/**
		 * Get the current relist count.
		 *
		 * @since 8.1.0
		 * @return int
		 */
		public function get_current_relist_count() {
			return lty_check_is_array( $this->get_lty_relists() ) ? count( $this->get_lty_relists() ) : 0;
		}

		/**
		 * Check is lottery instant winner?.
		 *
		 * @since 8.0.0
		 * @return bool
		 */
		public function is_instant_winner() {
			if ('yes' !== $this->get_lty_instant_winners()) {
				return false;
			}

			return lty_check_is_array($this->get_instant_winner_rule_ids());
		}

		/**
		 * Get the question answer display type.
		 *
		 * @since 8.2.0
		 * @return string
		 */
		public function get_question_answer_display_type() {
			return ( '1' === $this->get_question_answer_selection_type() ) ? $this->get_lty_question_answer_display_type() : get_option('lty_settings_question_answer_display_type', '1');
		}

		/**
		 * Is question answer first option as default option?
		 *
		 * @since 10.2.0
		 * @return bool
		 */
		public function is_question_answer_first_option_as_default_option() {
			return ( '1' === $this->get_question_answer_selection_type() ) ? 'yes' === $this->get_lty_question_answer_first_option_as_default_option() : 'yes' === get_option( 'lty_settings_question_answer_first_option_as_default_option', 'no' );
		}

		/**
		 * Get the end reason of the lottery.
		 *
		 * @since 8.6.0
		 * @return string
		 */
		public function get_lottery_end_reason() {
			if ('yes' === get_option('lty_settings_close_lottery_reach_max') && ( $this->get_lty_maximum_tickets() <= $this->get_purchased_ticket_count() )) {
				/* translators: %1s: Maximum tickets */
				return sprintf(__('Maximum Ticket Count reached %1s', 'lottery-for-woocommerce'), $this->get_lty_maximum_tickets());
			}

			return __('Time Over', 'lottery-for-woocommerce');
		}

		/**
		 * Update instant winner rules when a product is duplicated.
		 * This function is responsible for updating the instant winner rules of a duplicated product.
		 *
		 * @since 8.6.0
		 * @param int $product_id duplicated product ID.
		 * @return void
		 */
		public function update_instant_winner_rules( $product_id ) {
			// Return if product Id is invalid.
			if (!$product_id) {
				return;
			}

			$instant_winner_rule_ids = $this->get_instant_winner_rule_ids();
			if (!lty_check_is_array($instant_winner_rule_ids)) {
				return;
			}

			foreach ($instant_winner_rule_ids as $rule_id) {
				$instant_winner_rule = lty_get_instant_winner_rule($rule_id);
				if (!is_object($instant_winner_rule)) {
					continue;
				}

				$new_rule_id = $instant_winner_rule->duplicate();
				lty_create_new_instant_winner_log(
					array(
						'lty_image_id'               => $instant_winner_rule->get_image_id(),
						'lty_ticket_number'          => $instant_winner_rule->get_ticket_number(),
						'lty_prize_type'             => $instant_winner_rule->get_prize_type(),
						'lty_coupon_generation_type' => $instant_winner_rule->get_coupon_generation_type(),
						'lty_coupon_discount_type'   => $instant_winner_rule->get_coupon_discount_type(),
						'lty_coupon_id'              => $instant_winner_rule->get_coupon_id(),
						'lty_instant_winner_prize'   => $instant_winner_rule->get_prize_message(),
						'lty_prize_amount'           => $instant_winner_rule->get_prize_amount(),
						'lty_prize_group_id'         => $instant_winner_rule->get_prize_group_id(),
						'lty_current_relist_count'   => 0,
					),
					array(
						'post_parent' => $new_rule_id,
					)
				);
			}
		}

		/**
		 * Update instant winner prize groups when a product is duplicated.
		 *
		 * @since 11.1.0
		 * @param int $product_id duplicated product ID.
		 * @return void
		 */
		public function update_instant_winner_prize_groups( $product_id ) {
			// Return if product Id is invalid.
			if ( ! $product_id ) {
				return;
			}

			$prize_group_ids = $this->get_instant_winner_prize_group_ids();
			if ( ! lty_check_is_array( $prize_group_ids ) ) {
				return;
			}

			foreach ( $prize_group_ids as $prize_group_id ) {
				$prize_group = lty_get_instant_winner_prize_group( $prize_group_id );
				if ( ! $prize_group->exists() ) {
					continue;
				}

				$prize_group->duplicate();
			}
		}

		/**
		 * Is valid to display ticket number?
		 *
		 * @since 8.9.0
		 * @param string $ticket_number Ticket number.
		 * @return bool
		 */
		public function is_valid_to_display_ticket_number( $ticket_number ) {
			return ( 'yes' === $this->get_lty_hide_sold_tickets() && in_array($ticket_number, $this->get_placed_tickets()) );
		}

		/**
		 * Is sold all tickets?
		 *
		 * @since 8.9.0
		 * @param array $ticket_numbers Ticket numbers.
		 * @return bool
		 */
		public function is_sold_all_tickets( $ticket_numbers ) {
			return ( 'yes' === $this->get_lty_hide_sold_tickets() && ( count($ticket_numbers) === count(array_intersect($ticket_numbers, $this->get_placed_tickets())) ) );
		}

		/**
		 * Get the product name.
		 *
		 * @since 9.1.0
		 * @param bool $linkable Whether to return the product name as a link or not.
		 * @return string|html
		 * */
		public function get_product_name( $linkable = false ) {
			if (!$linkable) {
				return $this->get_title();
			}

			return sprintf('<a href="%s">%s</a>', esc_url($this->get_permalink()), esc_html($this->get_title()));
		}

		/**
		 * Is valid to display the predefined button?
		 *
		 * @since 9.2.0
		 * @param int|string $button_id Predefined button ID.
		 * @return bool
		 */
		public function is_valid_to_display_predefined_button( $button_id ) {
			if ('yes' !== get_option('lty_settings_hide_less_quantity_predefined_button', 'no')) {
				return true;
			}

			return $this->get_remaining_ticket_count() >= $this->get_predefined_buttons_ticket_quantity($button_id);
		}

		/**
		 * Can display lottery details?
		 *
		 * @since 9.2.0
		 * @return bool
		 */
		public function can_display_lottery_details() {
			if (empty($this->get_lty_lottery_status())) {
				return false;
			}

			switch ($this->get_lty_lottery_status()) {
				case 'lty_lottery_closed':
					return lty_can_display_closed_lottery_details_in_product_page();
				case 'lty_lottery_finished':
					return lty_can_display_finished_lottery_details_in_product_page();
				case 'lty_lottery_failed':
					return lty_can_display_failed_lottery_details_in_product_page();
			}

			return true;
		}

		/**
		 * Is valid to display the product page countdown timer?
		 *
		 * @since 9.2.0
		 * @param string $page Where to display the countdown timer.
		 * @return bool
		 */
		public function is_valid_to_display_countdown_timer_in_product_page( $page = 'product' ) {          
			if ( ( ! $this->is_unlimited_scheduled_lottery() && empty( $this->get_lty_end_date() ) ) || empty( $this->get_lty_start_date() ) || $this->is_closed() ) {
				return false;
			}

			if ( 'shortcode' !== $page && ! $this->display_countdown_timer_in_single_product() ) {
				return false;
			}
			
			if ( 'shortcode' === $page && $this->is_unlimited_scheduled_lottery() && ! $this->has_lottery_status( 'lty_lottery_not_started' ) ) {
				return false;
			}

			return true;
		}

		/**
		 * Get the instant winners rules count.
		 *
		 * @since 9.6.0
		 * @return int
		 */
		public function get_instant_winners_rules_count() {
			$rules_count = get_transient( 'lty_instant_winner_rules_count_' . $this->get_id() );
			if ( false !== $rules_count ) {
				return $rules_count;
			}

			$rules_count = lty_check_is_array( $this->get_instant_winner_rule_ids() ) ? count( $this->get_instant_winner_rule_ids() ) : 0;
			set_transient( 'lty_instant_winner_rules_count_' . $this->get_id(), $rules_count, HOUR_IN_SECONDS );

			return $rules_count;
		}

		/**
		 * Get ticket number orderby.
		 *
		 * @since 9.6.0
		 * @return string
		 */
		public function get_ticket_number_orderby() {
			return '1' === $this->get_lty_ticket_number_type() && ( '2' === get_option('lty_settings_generate_ticket_type') ) ? 'alpha_numeric' : 'numeric';
		}

		/**
		 * Can display predefined buttons with quantity selector?
		 *
		 * @since 9.8.0
		 * @return bool
		 */
		public function can_display_predefined_with_quantity_selector() {
			return 'yes' === $this->get_lty_predefined_with_quantity_selector();
		}

		/**
		 * Can display predefined buttons.
		 *
		 * @since 9.8.0
		 * @return bool
		 */
		public function can_display_predefined_buttons() {
			if (!$this->is_predefined_button_enabled()) {
				return false;
			}

			if (!lty_check_is_array($this->get_predefined_buttons_rule())) {
				return false;
			}

			foreach ($this->get_predefined_buttons_rule() as $button) {
				if (intval($button['ticket_quantity']) <= $this->get_remaining_ticket_count()) {
					return true;
				}
			}

			return false;
		}

		/**
		 * Is predefined with quantity selector?
		 *
		 * @since 10.0.0
		 * @return bool
		 */
		public function is_predefined_with_quantity_selector() {
			return 'yes' === $this->get_lty_predefined_with_quantity_selector();
		}

		/**
		 * Ticket purchased user already a winner?
		 *
		 * @since 10.1.0
		 * @param object $ticket Ticket object.
		 * @return bool
		 */
		public function has_user_already_winner( $ticket ) {
			if (!is_object($ticket)) {
				return false;
			}

			return empty($ticket->get_user_id()) ? in_array($ticket->get_user_email(), $this->get_winner_user_emails()) : in_array($ticket->get_user_id(), $this->get_winner_user_ids());
		}

		/**
		 * Can display predefined buttons discount tag?
		 *
		 * @since 10.6.0
		 * @return bool
		 */
		public function can_display_predefined_buttons_discount_tag() {
			return 'yes' === $this->get_lty_predefined_buttons_discount_tag();
		}

		/**
		 * Can display range slider predefined buttons discount tag?
		 *
		 * @since 10.6.0
		 * @return bool
		 */
		public function can_display_range_slider_predefined_buttons_discount_tag() {
			return 'yes' === $this->get_lty_range_slider_predefined_discount_tag();
		}

		/**
		 * Get predefined button price by quantity for per ticket.
		 *
		 * @since 10.8.0
		 * @param int $quantity Item quantity.
		 * @return float|bool
		 */
		public function get_predefined_button_price_by_quantity( $quantity ) {
			$predefined_button_rules = $this->get_predefined_buttons_rule();
			foreach ( $predefined_button_rules as $predefined_button_id => $predefined_button ) {
				$ticket_quantity = ! empty( $predefined_button['ticket_quantity'] ) ? absint( $predefined_button['ticket_quantity'] ) : 0;
				if ( absint( $quantity ) !== $ticket_quantity ) {
					continue;
				}

				return $this->get_predefined_buttons_per_ticket_amount( $predefined_button_id, $quantity );
			}

			return floatval( $this->get_price() );          
		}

		/**
		 * Is unlimited scheduled lottery?
		 *
		 * @since 11.7.0
		 * @param bool|int $list_count List count. false considers current schedule.
		 * @return bool
		 */
		public function is_unlimited_scheduled_lottery( $list_count = false ) {
			$is_unlimited_scheduled_lottery = '2' === $this->get_lty_lottery_schedule_type();
			if ( false === $list_count ) {
				return $is_unlimited_scheduled_lottery;
			}

			$relist_data = $this->get_lty_relists();
			if ( ! lty_check_is_array( $relist_data) ) {
				return $is_unlimited_scheduled_lottery;
			}

			if ( isset( $relist_data[ $list_count ] ) && isset( $relist_data[ $list_count ]['unlimited_scheduled_lottery'] ) ) {
				return 'yes' === $relist_data[ $list_count ]['unlimited_scheduled_lottery'];
			}

			return false;
		}

		/**
		 * Get the lottery scheduled duration details.
		 *
		 * @since 11.7.0
		 * @return string
		 */
		public function get_lottery_scheduled_duration_details() {
			$start_date = LTY_Date_Time::get_wp_format_datetime_from_gmt( $this->get_lty_start_date_gmt(), false, ' ', false );
			$end_date   = $this->is_unlimited_scheduled_lottery() && ! $this->is_closed() ? __( 'Unlimited', 'lottery-for-woocommerce' ) : LTY_Date_Time::get_wp_format_datetime_from_gmt( $this->get_lty_end_date_gmt(), false, ' ', false );

			return $start_date . ' - ' . $end_date;
		}

		/**
		 * Is the lottery product reached ending soon time.
		 *
		 * @since 12.4.0
		 * @return bool
		 */
		public function is_product_reached_ending_soon_time() {
			if (empty($this->get_lty_end_date_gmt()) || empty(lty_get_remainder_email_scheduler_time())) {
				return false;
			}

			return strtotime(LTY_Date_Time::get_mysql_date_time_format('now', true)) > strtotime($this->get_lty_end_date_gmt()) - absint(lty_get_remainder_email_scheduler_time());
		}
	}

}
