<?php

/**
 *  Handles the cart lottery.
 * */
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}
if (!class_exists('LTY_Lottery_Cart')) {

	/**
	 * Class.
	 * */
	class LTY_Lottery_Cart {

		/**
		 * Assigned lottery cart item prices.
		 * 
		 * @since 10.3.0
		 * @var array
		 */
		private static $assigned_lottery_cart_item_prices = array();

		/**
		 * Class Initialization.
		 * */
		public static function init() {
			// May be restrict the lottery product add to cart.
			add_filter('woocommerce_add_to_cart_validation', array( __CLASS__, 'restrict_woocommerce_add_to_cart' ), 12, 3);
			// May be get the custom item data in the cart.
			add_action('woocommerce_get_item_data', array( __CLASS__, 'maybe_get_custom_item_data' ), 10, 2);
			// May be add the custom item data in the cart.
			add_filter('woocommerce_add_cart_item_data', array( __CLASS__, 'maybe_add_custom_item_data' ), 8, 3);
			// May be add the custom item data in the cart when order again is made.
			add_filter('woocommerce_order_again_cart_item_data', array( __CLASS__, 'maybe_add_order_again_custom_item_data' ), 8, 3);
			// Get the cart item quantity.
			add_filter('woocommerce_cart_item_quantity', array( __CLASS__, 'get_cart_item_quantity' ), 10, 3);
			// Quantity input arguments.
			add_filter('woocommerce_quantity_input_args', array( __CLASS__, 'quantity_input_args' ), 10, 2);
			// Set the lottery product as virtual when purchasing the product.
			add_filter('woocommerce_is_virtual', array( __CLASS__, 'set_lottery_as_virtual' ), 12, 2);
			// Check the cart item validity.
			add_action('woocommerce_check_cart_items', array( __CLASS__, 'check_cart_items' ), 1);
			// May be add reserve tickets in cart.
			add_action('woocommerce_add_to_cart', array( __CLASS__, 'maybe_add_reserve_tickets_in_cart' ), 10, 6);
			// Remove reserved tickets in cart.
			add_action('woocommerce_remove_cart_item', array( __CLASS__, 'remove_reserved_tickets_in_cart' ), 10, 2);
			// Display notices before single product.
			add_action('woocommerce_before_single_product', array( __CLASS__, 'display_messages_before_single_product' ));
			// Display notices before cart.
			add_action('woocommerce_before_cart', array( __CLASS__, 'display_reserved_time_notice_in_cart_and_checkout' ), 8);
			// Display notices before checkout form.
			add_action('woocommerce_before_checkout_form', array( __CLASS__, 'display_reserved_time_notice_in_cart_and_checkout' ), 8);
			// May be handle manual reserved tickets.
			add_action('wp_head', array( __CLASS__, 'maybe_handle_manual_reserved_tickets' ));
			// Set predefined buttons data.
			add_action('woocommerce_before_calculate_totals', array( __CLASS__, 'set_predefined_buttons_data' ));
			// Add to cart redirect.
			add_filter('woocommerce_add_to_cart_redirect', array( __CLASS__, 'add_to_cart_redirect' ), 20, 2);
			// Get the cart item quantity for cart blocks.
			add_filter('woocommerce_store_api_product_quantity_minimum', array( __CLASS__, 'get_cart_item_quantity' ), 10, 3);
			add_filter('woocommerce_store_api_product_quantity_maximum', array( __CLASS__, 'get_cart_item_quantity' ), 10, 3);
			// Handle payment gateways.
			add_filter( 'woocommerce_available_payment_gateways', array( __CLASS__, 'handle_payment_gateways' ), 10, 1 );
		}

		/**
		 * Restrict the lottery product add to cart.
		 *
		 * @return bool
		 * */
		public static function restrict_woocommerce_add_to_cart( $bool, $product_id, $qty ) {
			$product = wc_get_product($product_id);
			if (!lty_is_lottery_product($product)) {
				return $bool;
			}

			if (!self::maybe_validate_ip_address($product_id)) {
				wc_add_notice(get_option('lty_settings_ip_address_restriction_error_message', 'Sorry, you cannot participate in this Lottery because, your IP Address is restricted.'), 'error');
				return false;
			}

			if (!self::may_be_validate_guest_user_add_to_cart()) {
				return false;
			}

			if (!self::may_be_validate_lottery_add_to_cart($product, $qty)) {
				return false;
			}

			if (!self::may_be_validate_reserved_ticket_add_to_cart($product, $qty)) {
				return false;
			}

			if (!self::may_be_add_and_validate_answer_add_to_cart($product)) {
				return false;
			}

			if (!self::may_be_validate_predefined_buttons_add_to_cart($product, $qty)) {
				return false;
			}

			return $bool;
		}

		/**
		 * May be validate the guest user lottery product add to cart.
		 *
		 * @return bool
		 * */
		public static function may_be_validate_guest_user_add_to_cart() {
			if (is_user_logged_in()) {
				return true;
			}

			if ('2' != get_option('lty_settings_guest_user_participate_type')) {
				return true;
			}

			return false;
		}

		/**
		 * May be validate the lottery product add to cart.
		 *
		 * @return bool
		 * */
		public static function may_be_validate_lottery_add_to_cart( $product, $qty ) {
			$cart_count = lty_get_cart_lottery_product_count($product->get_id());
			$user_limit = intval($product->get_lty_user_maximum_tickets()) - $product->get_user_placed_ticket_count();
			$user_limit = ( $product->get_lty_order_maximum_tickets() && $product->get_lty_order_maximum_tickets() <= $user_limit ) ? $product->get_lty_order_maximum_tickets() : $user_limit;
			$cart_quantity = $cart_count + $qty;

			$minimum_purchase_quantity = $product->get_min_purchase_quantity();
			if ($minimum_purchase_quantity && $qty < $minimum_purchase_quantity) {
				/* translators: %1$s: Product Title %2$s: User Limit %3$s: Quantity */
				wc_add_notice(sprintf(__('The minimum allowed quantity for %1$s is %2$d . So you cannot add %3$d to your cart.', 'lottery-for-woocommerce'), $product->get_title(), absint($minimum_purchase_quantity), $qty), 'error');
				return false;
			}

			if ($user_limit >= $cart_quantity) {
				return true;
			}

			if (!$user_limit) {
				wc_add_notice(__('You have reached the maximum quantity limit for this giveaway.', 'lottery-for-woocommerce'), 'error');
			} elseif ($minimum_purchase_quantity && $minimum_purchase_quantity > $user_limit) {
				/* translators: %1$d: Placed tickets %2$s: Product Title %3$d: Minimum tickets per user %4$d: Remaining tickets */
				wc_add_notice(sprintf(__('You have already bought %1$d tickets so you cannot purchase the %2$s because minimum ticket quantity for add to cart is %3$d but you have remaining ticket quantity is %4$d.', 'lottery-for-woocommerce'), $product->get_user_placed_ticket_count(), $product->get_title(), $minimum_purchase_quantity, $user_limit), 'error');
			} else {
				/* translators: %1$s: Product Title %2$s: User Limit %3$s: Quantity */
				wc_add_notice(sprintf(__('The maximum allowed quantity for %1$s is %2$d . So you cannot add %3$d to your cart.', 'lottery-for-woocommerce'), $product->get_title(), $user_limit, $cart_quantity), 'error');
			}

			return false;
		}

		/**
		 * May be validate reserved ticket add to cart.
		 *
		 * @return bool
		 * */
		public static function may_be_validate_reserved_ticket_add_to_cart( $product, $qty ) {

			if (!$product->is_manual_ticket()) {
				return true;
			}

			$user_selected_tickets = isset($_REQUEST['lty_lottery_ticket_numbers']) ? explode(',', wc_clean(wp_unslash(( $_REQUEST['lty_lottery_ticket_numbers'] )))) : array();
			$tickets = array_intersect((array) $user_selected_tickets, $product->get_reserved_tickets());

			if (lty_check_is_array($tickets)) {
				if (count($tickets) == 1) {
					/* translators: %s: Reserved Tickets */
					wc_add_notice(sprintf(__(' The Ticket %s is already reserved.', 'lottery-for-woocommerce'), implode(',', $tickets)), 'error');
					return false;
				} else {
					/* translators: %s: Reserved Tickets */
					wc_add_notice(sprintf(__(' The Tickets %s are already reserved.', 'lottery-for-woocommerce'), implode(',', $tickets)), 'error');
					return false;
				}
			}

			return true;
		}

		/**
		 * May be add and validate lottery answer add to cart.
		 *
		 * @return bool
		 * */
		public static function may_be_add_and_validate_answer_add_to_cart( $product, $answer_key = false ) {

			// Return if valid question answer or force answer option is not enabled or validate correct answer option is not enabled.
			if (!$product->is_valid_question_answer() || 'yes' != $product->is_force_answer_enabled() || 'yes' == $product->incorrectly_selected_answer_restriction_is_enabled() || 'yes' != $product->is_verify_answer_enabled()) {
				return true;
			}

			$answer_key = isset($_REQUEST['lty_question_answer_id']) ? wc_clean(wp_unslash($_REQUEST['lty_question_answer_id'])) : $answer_key;
			// Return if the answer key is empty.
			if (!$answer_key) {
				return true;
			}

			$answers = $product->get_answers();
			$answer_data = isset($answers[$answer_key]) ? $answers[$answer_key] : array();
			// Return if the answer data is empty.
			if (!lty_check_is_array($answer_data)) {
				return true;
			}

			$customer_id = lty_get_current_user_cart_session_value();
			// Return if the customer ID is empty.
			if (!$customer_id) {
				return true;
			}

			if (isset($answer_data['valid']) && 'yes' != $answer_data['valid']) {

				// Sets the session cookie on-demand (usually after adding an item to the cart).
				if (!headers_sent() && did_action('wp_loaded')) {
					WC()->session->set_customer_session_cookie(true);
				}

				// Return if unlimited attempts type is selected.
				if ('2' == $product->verify_question_answer_type()) {
					lty_unset_question_answer_metas($product, $customer_id);
					return false;
				}

				if ($product->get_question_answer_attempts() && !$product->is_limited_answer_attempts_reached($customer_id)) {
					// Update question answer attempts data.
					self::update_question_answer_attempts_data($product, $customer_id);

					$attempts_count = absint(wc_get_product($product->get_id())->get_question_answer_remaining_attempts($customer_id));
					if ($attempts_count) {
						if (isset($_REQUEST['lty_question_answer_id'])) {
							// Display question answer remaining attempts notice.
							wc_add_notice(str_replace('{attempts}', $attempts_count, get_option('lty_settings_limited_type_multiple_attempts_error_message', 'Incorrect answer. {attempts} attempt(s) left.')), 'error');
						}
						return false;
					}
				}

				// Return if the user selected incorrect answer.
				if ($product->validate_user_incorrect_answer($customer_id) || $product->is_customer_question_answer_time_limit_exists($customer_id)) {
					return false;
				}

				// Update user ids when incorrect answer is selected.
				$product->update_post_meta('lty_incorrect_answer_user_ids', array_merge((array) $product->get_lty_incorrect_answer_user_ids(), array( $customer_id )));
				return false;
			}
			return true;
		}

		/**
		 * Update question answer attempts data.
		 *
		 * @return void
		 * */
		public static function update_question_answer_attempts_data( $product, $user_id ) {
			$attempts_data = lty_check_is_array($product->get_lty_question_answer_attempts_data()) ? $product->get_lty_question_answer_attempts_data() : array();
			if (!isset($attempts_data[$user_id])) {

				$old_attempts_count = in_array($user_id, (array) $product->get_lty_incorrect_answer_user_ids()) ? 1 : 0;
				$attempts_data[$user_id] = absint($old_attempts_count + 1);

				// Update question answer attempts data meta.
				$product->update_post_meta('lty_question_answer_attempts_data', $attempts_data);
			} else {

				$attempts_count = isset($attempts_data[$user_id]) ? $attempts_data[$user_id] : '';
				$attempts_data[$user_id] = absint($attempts_count + 1);
				// Update question answer attempts data meta.
				$product->update_post_meta('lty_question_answer_attempts_data', $attempts_data);
			}
		}

		/**
		 * May be validate predefined buttons add to cart.
		 *
		 * @return void
		 * */
		public static function may_be_validate_predefined_buttons_add_to_cart( $product, $ticket_quantity_in_cart ) {
			if ($product->is_manual_ticket() || !$product->is_predefined_button_enabled() || $product->can_display_predefined_with_quantity_selector() || !isset($_REQUEST['lty_predefined_button_id'], $_REQUEST['lty_per_ticket_amount'])) {
				return true;
			}

			$predefined_button_id = '' !== $_REQUEST['lty_predefined_button_id'] ? absint($_REQUEST['lty_predefined_button_id']) : '';
			if ('' === $predefined_button_id) {
				return true;
			}

			$ticket_quantity = $product->get_predefined_buttons_ticket_quantity($predefined_button_id);
			$per_ticket_amount = $product->get_predefined_buttons_per_ticket_amount($predefined_button_id);
			$per_ticket_amount_in_cart = !empty($_REQUEST['lty_per_ticket_amount']) ? floatval($_REQUEST['lty_per_ticket_amount']) : 0;

			$per_ticket_amount = "$per_ticket_amount";
			$per_ticket_amount_in_cart = "$per_ticket_amount_in_cart";
			$ticket_quantity_in_cart = "$ticket_quantity_in_cart";
			$ticket_quantity = "$ticket_quantity";

			if ($per_ticket_amount_in_cart != $per_ticket_amount || $ticket_quantity_in_cart != $ticket_quantity) {
				wc_add_notice(__('Invalid Data', 'lottery-for-woocommerce'), 'error');
				return false;
			}

			return true;
		}

		/**
		 * May be add the custom item data in the cart.
		 *
		 * @return array
		 * */
		public static function maybe_add_custom_item_data( $cart_item_data, $product_id, $variation_id ) {
			$product = wc_get_product($product_id);

			if (!is_object($product)) {
				return $cart_item_data;
			}

			if ('lottery' != $product->get_type()) {
				return $cart_item_data;
			}

			$custom_cart_item_data = array();

			// Prepare the ticket cart item data.
			if (isset($_REQUEST['lty_lottery_ticket_numbers']) && $product->is_manual_ticket()) {
				$tickets = wc_clean(wp_unslash($_REQUEST['lty_lottery_ticket_numbers']));

				if ('' !== $tickets) {
					$custom_cart_item_data['tickets'] = explode(',', $tickets);
				}
			}

			// Prepare the answers cart item data.
			if (isset($_REQUEST['lty_question_answer_id']) && $product->is_valid_question_answer()) {
				$answer_key = wc_clean(wp_unslash($_REQUEST['lty_question_answer_id']));

				if (!empty($answer_key)) {
					$answers = $product->get_answers();
					if (array_key_exists($answer_key, $answers)) {
						$custom_cart_item_data['answers'] = $answer_key;
					}
				}
			}

			// Prepare the predefined buttons cart item data.
			if (!$product->is_manual_ticket() && $product->is_predefined_button_enabled() && isset($_REQUEST['lty_predefined_button_id'], $_REQUEST['lty_per_ticket_amount'])) {

				$predefined_button_id = '' !== $_REQUEST['lty_predefined_button_id'] ? absint($_REQUEST['lty_predefined_button_id']) : '';
				$per_ticket_amount = !empty($_REQUEST['lty_per_ticket_amount']) ? floatval($_REQUEST['lty_per_ticket_amount']) : 0;

				$predefined_button_rule = $product->get_predefined_buttons_rule();
				if (isset($predefined_button_rule[$predefined_button_id])) {
					$custom_cart_item_data['lty_predefined_button_id'] = $predefined_button_id;
					$custom_cart_item_data['lty_per_ticket_amount'] = $per_ticket_amount;
				}
			}

			if (!lty_check_is_array($custom_cart_item_data)) {
				return $cart_item_data;
			}

			$cart_item_data['lty_lottery'] = $custom_cart_item_data;

			return $cart_item_data;
		}

		/**
		 * May be add the order again custom item data in the cart.
		 *
		 * @return array
		 * */
		public static function maybe_add_order_again_custom_item_data( $cart_item_data, $order_item, $order ) {

			if (!is_object($order) || !is_object($order_item)) {
				return $cart_item_data;
			}

			$product = $order_item->get_product();
			if (!is_object($product) || 'lottery' != $product->get_type()) {
				return $cart_item_data;
			}

			$custom_cart_item_data = array();

			// Prepare the answers cart item data.
			$answer_keys = $order_item->get_meta('_lty_lottery_answers', true);
			if (lty_check_is_array($answer_keys) && $product->is_valid_question_answer()) {
				$answers = $product->get_answers();
				foreach ($answer_keys as $answer_key) {
					if (array_key_exists($answer_key, $answers)) {
						$custom_cart_item_data['answers'] = $answer_key;
					}
				}
			}

			if (!lty_check_is_array($custom_cart_item_data)) {
				return $cart_item_data;
			}

			$cart_item_data['lty_lottery'] = $custom_cart_item_data;

			return $cart_item_data;
		}

		/**
		 * May be get the custom item data in the cart.
		 *
		 * @return array
		 * */
		public static function maybe_get_custom_item_data( $item_data, $cart_item ) {
			if (!isset($cart_item['product_id']) || empty($cart_item['product_id'])) {
				return $item_data;
			}

			if (!isset($cart_item['lty_lottery'])) {
				return $item_data;
			}

			$product = wc_get_product($cart_item['product_id']);

			if (!is_object($product)) {
				return $item_data;
			}

			if ('lottery' != $product->get_type()) {
				return $item_data;
			}

			// Prepare the ticket item data.
			if (isset($cart_item['lty_lottery']['tickets']) && $product->is_manual_ticket()) {
				if (lty_check_is_array($cart_item['lty_lottery']['tickets'])) {

					$item_data[] = array(
						'name' => lty_get_order_item_ticket_number_name(),
						'value' => implode(',', $cart_item['lty_lottery']['tickets']),
						'display' => '',
					);
				}
			}

			// Prepare the question answer item data.
			if (isset($cart_item['lty_lottery']['answers']) && $product->is_valid_question_answer()) {
				if (!empty($cart_item['lty_lottery']['answers'])) {
					$answers = $product->get_answers();

					if (array_key_exists($cart_item['lty_lottery']['answers'], $answers)) {
						$item_data[] = array(
							'name' => __('Chosen Answer', 'lottery-for-woocommerce'),
							'value' => $answers[$cart_item['lty_lottery']['answers']]['label'],
							'display' => '',
						);
					}
				}
			}

			return $item_data;
		}

		/**
		 * Set the lottery product as virtual when purchasing the product.
		 *
		 * @return bool
		 * */
		public static function set_lottery_as_virtual( $virtual, $product ) {
			if (!is_object($product)) {
				return $virtual;
			}

			if ('lottery' === $product->get_type()) {
				$virtual = true;
			}

			return $virtual;
		}

		/**
		 * Get the cart item quantity.
		 *
		 * @return string
		 * */
		public static function get_cart_item_quantity( $quantity, $cart_item_key, $cart_item_data ) {
			if ( ! isset( $cart_item_data['product_id'] ) || empty( $cart_item_data['product_id'] ) ) {
				return $quantity;
			}

			$product = wc_get_product( $cart_item_data['product_id'] );
			if ( ! lty_is_lottery_product( $product ) ) {
				return $quantity;
			}

			if ( ! $product->is_manual_ticket() ) {
				if ( $product->is_predefined_button_enabled() && ! $product->can_display_predefined_with_quantity_selector() && isset( $cart_item_data['lty_lottery']['lty_per_ticket_amount'] ) ) {
					return $cart_item_data['quantity'];
				}

				return absint( $product->get_min_purchase_quantity() ) === absint( $product->get_max_purchase_quantity() ) ? absint( $product->get_min_purchase_quantity() ) : $quantity;
			}

			if ( ! isset( $cart_item_data['lty_lottery']['tickets'] ) || ! lty_check_is_array( $cart_item_data['lty_lottery']['tickets'] ) ) {
				return $quantity;
			}

			return $cart_item_data['quantity'];
		}

		/**
		 * Modify the quantity input arguments for the product.
		 *
		 * @since 1.0.0
		 * @param array  $quantity_args
		 * @param object $product
		 * @return array
		 */
		public static function quantity_input_args( $quantity_args, $product ) {
			if (!lty_is_lottery_product($product)) {
				return $quantity_args;
			}

			if ($product->get_min_purchase_quantity()) {
				$quantity_args['min_value'] = ( $product->get_min_purchase_quantity() > 1 ) ? $product->get_min_purchase_quantity() : 1;
			}

			if (is_singular('product') && '1' === get_option('lty_settings_quantity_selector_type')) {
				$quantity_args['input_value'] = $product->get_preset_tickets();
			}

			return $quantity_args;
		}

		/**
		 * Check the cart items if the lottery product is validate to purchase.
		 *
		 * @return bool
		 * */
		public static function check_cart_items() {
			$return = true;
			if (!is_object(WC()->cart)) {
				return $return;
			}

			$cart_items = WC()->cart->get_cart();
			if (!lty_check_is_array($cart_items)) {
				return $return;
			}

			foreach ($cart_items as $cart_item_key => $value) {

				$product_id = isset($value['product_id']) ? $value['product_id'] : '';
				$product = wc_get_product($product_id);
				if (!is_object($product) || 'lottery' != $product->get_type()) {
					continue;
				}

				$result = self::validate_lottery_cart_items($product, $value);
				if (!is_wp_error($result)) {
					continue;
				}

				// Remove the product from cart.
				WC()->cart->set_quantity($cart_item_key, 0);

				wc_add_notice($result->get_error_message(), 'error');

				$return = false;
			}

			return $return;
		}

		/**
		 * Validate the lottery product is closed.
		 * check the ticket count exists.
		 *
		 * @return bool/error message.
		 * */
		public static function validate_lottery_cart_items( $product, $value ) {
			// Check if the lottery ticket is closed.
			if ($product->is_closed()) {
				return new WP_Error('invalid', __('Giveaway product is removed from the cart because giveaway has been closed.', 'lottery-for-woocommerce'));
			}

			// Check if the lottery ticket count exists.
			if (!$product->is_closed() && $product->get_placed_ticket_count() >= $product->get_lty_maximum_tickets()) {
				return new WP_Error('invalid', __('Giveaway product is removed from the cart because the maximum ticket count for the giveaway has been reached.', 'lottery-for-woocommerce'));
			}

			// Check if the user lottery ticket count exists.
			if (!self::maybe_validate_maximum_tickets_per_user_add_to_cart($product->get_id(), $value)) {
				return new WP_Error('invalid', __('You cannot purchase any more tickets for this giveaway.', 'lottery-for-woocommerce'));
			}

			// Check if the ticket numbers are already purchased.
			if (isset($value['lty_lottery']['tickets']) && lty_check_is_array($value['lty_lottery']['tickets'])) {

				if (array_diff($value['lty_lottery']['tickets'], $value['data']->get_overall_tickets())) {
					/* translators: %s: product name */
					return new WP_Error('invalid', sprintf(__('Sorry, Tickets are not matching for the product %s hence it is removed from the cart.', 'lottery-for-woocommerce'), $product->get_name()));
				}

				$already_purchased_tickets = array();
				foreach ($value['lty_lottery']['tickets'] as $ticket_number) {
					$ticket_numbers = lty_product_ticket_number_exists($product->get_id(), $ticket_number);

					if (!$ticket_numbers) {
						continue;
					}

					$already_purchased_tickets[] = $ticket_number;
				}

				if (lty_check_is_array($already_purchased_tickets)) {
					/* translators: %s: ticket numbers */
					return new WP_Error('invalid', sprintf(__('You cannot purchase Ticket Number(s) %s as it was already purchased by another user(s).', 'lottery-for-woocommerce'), implode(' , ', $already_purchased_tickets)));
				}
			}

			if (!self::maybe_validate_ip_address($product->get_id())) {
				return new WP_Error('invalid', get_option('lty_settings_ip_address_restriction_error_message', 'Sorry, you cannot participate in this Lottery because, your IP Address is restricted.'));
			}

			if (!self::maybe_validate_manual_reserve_tickets($product->get_id(), $value)) {
				/* translators: %s: product name */
				return new WP_Error('invalid', sprintf(__('Sorry, you have exceeded the time limit for completing the purchase for %s giveaway hence, the product has been removed from your cart.', 'lottery-for-woocommerce'), $product->get_name()));
			}

			if (!self::maybe_validate_manual_tickets_add_to_cart($product->get_id(), $value)) {
				/* translators: %s: product name */
				return new WP_Error('invalid', sprintf(__('Sorry, tickets are not added for the product %s hence it is removed from the cart.', 'lottery-for-woocommerce'), $product->get_name()));
			}

			if (!self::maybe_validate_minimum_tickets_per_user_add_to_cart($product->get_id(), $value)) {
				/* translators: %s: product name */
				return new WP_Error('invalid', sprintf(__('Sorry, minimum tickets per user not matching for the product %s hence it is removed from the cart.', 'lottery-for-woocommerce'), $product->get_name()));
			}

			if (!self::maybe_validate_predefined_buttons_add_to_cart($product->get_id(), $value)) {
				/* translators: %s: product name */
				return new WP_Error('invalid', sprintf(__('Sorry, Giveaway data changed for this product %s hence it is removed from the cart.', 'lottery-for-woocommerce'), $product->get_name()));
			}

			return true;
		}

		/**
		 * May be Validate IP Address.
		 *
		 * @return bool
		 * */
		public static function maybe_validate_ip_address( $lottery_product_id ) {

			if ('yes' != get_option('lty_settings_validate_user_ip_address')) {
				return true;
			}

			$args = array(
				'post_type' => LTY_Register_Post_Types::LOTTERY_TICKET_POSTTYPE,
				'post_status' => array_merge(array( 'lty_ticket_pending' ), (array) lty_get_lottery_ticket_statuses()),
				'posts_per_page' => '-1',
				'fields' => 'ids',
				'post_parent' => $lottery_product_id,
				'meta_key' => 'lty_ip_address',
				'meta_value' => lty_get_ip_address(),
			);

			$ticket_ids = get_posts($args);

			if (lty_check_is_array($ticket_ids)) {

				$lottery_ticket = isset($ticket_ids[0]) ? lty_get_lottery_ticket($ticket_ids[0]) : false;
				return is_object($lottery_ticket) ? get_current_user_id() == $lottery_ticket->get_user_id() : false;
			}

			return true;
		}

		/**
		 * May be validate manual reserve tickets.
		 *
		 * @return bool
		 * */
		public static function maybe_validate_manual_reserve_tickets( $lottery_product_id, $value ) {
			$product = wc_get_product($lottery_product_id);
			// Return if not an reserved ticket.
			if (!lty_is_reserved_ticket($product, false)) {
				return true;
			}

			$reserved_ticket_time = get_option('lty_settings_reserve_ticket_time_in_min');
			$lottery_tickets = isset($value['lty_lottery']['tickets']) ? $value['lty_lottery']['tickets'] : array();
			if (!lty_check_is_array($lottery_tickets)) {
				return true;
			}

			$customer_id = lty_get_current_user_cart_session_value();
			if (!$customer_id) {
				return true;
			}

			$session_reserved_tickets = lty_get_session_reserved_tickets();
			$current_time_gmt = time();
			foreach ($lottery_tickets as $ticket) {

				$reserved_tickets_data = $product->get_reserved_ticket_values($ticket);
				// Check reserved ticket is an array.
				if (!lty_check_is_array($reserved_tickets_data)) {
					return false;
				}

				// Remove cart items when the reserved tickets customer is invalid.
				if (!in_array($customer_id, array_keys($reserved_tickets_data)) && !isset($session_reserved_tickets[$lottery_product_id][$ticket])) {
					return false;
				}

				foreach ($reserved_tickets_data as $customer_id => $reserved_time_gmt) {
					if ($current_time_gmt > absint($reserved_time_gmt + (int) ( 60 * $reserved_ticket_time ))) {
						return false;
					}
				}
			}

			return true;
		}

		/**
		 * May be validate manual tickets add to cart.
		 *
		 * @return bool
		 * */
		public static function maybe_validate_manual_tickets_add_to_cart( $lottery_product_id, $value ) {

			$lottery_product = wc_get_product($lottery_product_id);
			if (!lty_is_lottery_product($lottery_product) || !$lottery_product->is_manual_ticket()) {
				return true;
			}

			$manual_lottery_tickets = isset($value['lty_lottery']['tickets']) ? $value['lty_lottery']['tickets'] : array();
			// Remove cart items when the manual lottery tickets is empty.
			if (!lty_check_is_array($manual_lottery_tickets)) {
				return false;
			}

			return true;
		}

		/**
		 * May be validate maximum tickets per user add to cart.
		 *
		 * @return bool
		 * */
		public static function maybe_validate_maximum_tickets_per_user_add_to_cart( $lottery_product_id, $value ) {

			$lottery_product = wc_get_product($lottery_product_id);
			if (!lty_is_lottery_product($lottery_product)) {
				return true;
			}

			if ('3' == get_option('lty_settings_guest_user_participate_type')) {
				return true;
			}

			$cart_quantity = isset($value['quantity']) ? absint($value['quantity']) : 1;
			$cart_product_id = isset($value['product_id']) ? absint($value['product_id']) : 0;
			if ($cart_product_id == $lottery_product_id) {
				if (!$lottery_product->is_closed() && $lottery_product->get_user_placed_ticket_count() + $cart_quantity > $lottery_product->get_lty_user_maximum_tickets()) {
					return false;
				}
			}

			return true;
		}

		/**
		 * May be validate minimum tickets per user add to cart.
		 *
		 * @return bool
		 * */
		public static function maybe_validate_minimum_tickets_per_user_add_to_cart( $lottery_product_id, $value ) {

			$lottery_product = wc_get_product($lottery_product_id);
			if (!lty_is_lottery_product($lottery_product)) {
				return true;
			}

			if ('3' == get_option('lty_settings_guest_user_participate_type')) {
				return true;
			}

			$cart_quantity = isset($value['quantity']) ? $value['quantity'] : 0;
			if ($cart_quantity && $lottery_product->get_min_purchase_quantity() && $cart_quantity < $lottery_product->get_min_purchase_quantity()) {
				return false;
			}

			return true;
		}

		/**
		 * May be add reserve tickets in cart.
		 *
		 * @return void
		 * */
		public static function maybe_add_reserve_tickets_in_cart( $cart_item_key, $product_id, $quantity, $variation_id, $variation, $cart_item_data ) {

			$product = wc_get_product($product_id);
			// Return if not an reserved ticket.
			if (!lty_is_reserved_ticket($product, false)) {
				return;
			}

			$customer_id = lty_get_current_user_cart_session_value();
			if (!$customer_id) {
				return;
			}

			$lottery_tickets = isset($cart_item_data['lty_lottery']['tickets']) ? $cart_item_data['lty_lottery']['tickets'] : array();
			if (!lty_check_is_array($lottery_tickets)) {
				return;
			}

			$session_reserved_tickets = lty_get_session_reserved_tickets();

			$current_time_gmt = time();
			$cart_tickets = array();
			foreach ($lottery_tickets as $ticket) {
				$cart_tickets[$ticket] = array( $customer_id => $current_time_gmt );

				if (isset($session_reserved_tickets[$product_id])) {
					$session_reserved_tickets[$product_id][$ticket] = $customer_id;
				} else {
					$session_reserved_tickets[$product_id] = array( $ticket => $customer_id );
				}
			}

			// Update manual reserved tickets.
			$reserved_tickets_data = $product->get_reserved_tickets_data() + $cart_tickets;
			$product->update_post_meta('lty_manual_reserved_tickets', $reserved_tickets_data);

			// Set reserved tickets in the session.
			WC()->session->set('lty_reserved_tickets', $session_reserved_tickets);
		}

		/**
		 * Remove reserved tickets in cart.
		 *
		 * @since 1.0.0
		 * @param string $cart_item_key Cart item key.
		 * @param object $cart Cart object.
		 * @return void
		 * */
		public static function remove_reserved_tickets_in_cart( $cart_item_key, $cart ) {
			if (!$cart_item_key || !is_object($cart)) {
				return;
			}

			$cart_item = $cart->get_cart_item($cart_item_key);
			$customer_id = lty_get_current_user_cart_session_value();
			if (!$customer_id) {
				return;
			}

			$product_id = isset($cart_item['product_id']) ? $cart_item['product_id'] : '';
			$product = wc_get_product($product_id);
			if (!lty_is_lottery_product($product) || !$product->is_manual_ticket()) {
				return;
			}

			$session_reserved_tickets = lty_get_session_reserved_tickets();
			$lottery_tickets = isset($cart_item['lty_lottery']['tickets']) ? $cart_item['lty_lottery']['tickets'] : array();
			if (!lty_check_is_array($lottery_tickets)) {
				return;
			}

			if ('yes' !== get_option('lty_settings_enable_reserve_ticket_manual_selection_type')) {
				$reserved_tickets = array();
			} else {
				$reserved_tickets = $product->get_reserved_tickets_data();
				foreach ($lottery_tickets as $ticket_number) {
					$reserved_ticket_data = $product->get_reserved_ticket_values($ticket_number);
					if (!lty_check_is_array($reserved_ticket_data)) {
						continue;
					}

					// Remove cart items based on customer id.
					if (!in_array($customer_id, array_keys($reserved_ticket_data)) && !isset($session_reserved_tickets[$product_id][$ticket_number])) {
						continue;
					}

					unset($reserved_tickets[$ticket_number]);
					unset($session_reserved_tickets[$product_id][$ticket_number]);
				}
			}

			// Update reserved tickets when cart is removed.
			$product->update_post_meta('lty_manual_reserved_tickets', $reserved_tickets);

			WC()->session->set('lty_reserved_tickets', $session_reserved_tickets);
		}

		/**
		 * May be handle reserved tickets.
		 *
		 * @return void
		 * */
		public static function maybe_handle_manual_reserved_tickets() {
			global $post;
			if (!is_object($post)) {
				return;
			}

			$product = wc_get_product($post->ID);
			// Return if not an reserved ticket.
			if (!is_object($product) || !$product->exists() || 'lottery' != $product->get_type() || !$product->is_manual_ticket()) {
				return false;
			}

			$session_reserved_tickets = lty_get_session_reserved_tickets();
			if ('yes' !== get_option('lty_settings_enable_reserve_ticket_manual_selection_type')) {
				$remaining_tickets_data = array();
				$update_meta = true;
			} else {
				$reserved_ticket_time = get_option('lty_settings_reserve_ticket_time_in_min');
				$reserved_tickets_data = $product->get_reserved_tickets_data();
				$remaining_tickets_data = $reserved_tickets_data;
				$current_time_gmt = time();
				$update_meta = false;

				// Unset time exceeded tickets in reserved tickets meta.
				foreach ($reserved_tickets_data as $ticket_id => $reserved_time_data) {
					foreach ($reserved_time_data as $customer_id => $reserved_time_gmt) {
						if ($current_time_gmt > absint($reserved_time_gmt + (int) ( 60 * $reserved_ticket_time ))) {
							unset($remaining_tickets_data[$ticket_id]);
							unset($session_reserved_tickets[$product->get_id()][$ticket_id]);
							$update_meta = true;
						}
					}
				}
			}

			if ($update_meta) {
				// Update reserved tickets.
				$product->update_post_meta('lty_manual_reserved_tickets', $remaining_tickets_data);

				WC()->session->set('lty_reserved_tickets', $session_reserved_tickets);
			}
		}

		/**
		 * Display messages before single product.
		 *
		 * @return void
		 * */
		public static function display_messages_before_single_product() {

			global $product;
			// Return if the product is not an object or product is closed.
			if (!lty_is_lottery_product($product) || $product->is_closed()) {
				return;
			}

			if (lty_is_reserved_ticket($product)) {

				// Display reserved time notices in single product.
				self::display_reserved_time_notices_in_single_product();
			}

			if ($product->is_valid_question_answer()) {

				// Display incorrect answer notice in single product.
				self::display_incorrect_answer_notice_in_single_product();

				// Display incorrect answer notice in single product.
				self::display_correct_answer_notice_in_single_product();
			}
		}

		/**
		 * Display reserved time notices in single product.
		 *
		 * @return void
		 * */
		public static function display_reserved_time_notices_in_single_product() {

			global $product;

			$reserved_ticket_time = get_option('lty_settings_reserve_ticket_time_in_min');
			$reserved_tickets = $product->get_reserved_tickets_data();
			$customer_id = lty_get_current_user_cart_session_value();
			if (!$customer_id) {
				return;
			}

			$cart_contents = WC()->cart->get_cart();
			if (!lty_check_is_array($cart_contents)) {
				return;
			}

			$tickets = array();
			foreach ($reserved_tickets as $ticket => $reserved_ticket_data) {
				if (!in_array($customer_id, array_keys($reserved_ticket_data))) {
					return;
				}

				$tickets[] = $ticket;
			}

			/* translators: %1$s product name,%2$d ticket number, %3$d,%4$d reserved ticket time */
			wc_add_notice(sprintf(__(' The %1$s ticket number %2$s is reserved for %3$d minutes. Please complete your purchase within %3$d minutes.', 'lottery-for-woocommerce'), $product->get_name(), implode(' , ', $tickets), $reserved_ticket_time, $reserved_ticket_time), 'notice');
		}

		/**
		 * Display incorrect answer notice in single product.
		 *
		 * @return void
		 * */
		public static function display_incorrect_answer_notice_in_single_product() {
			global $product;

			$customer_id = lty_get_current_user_cart_session_value();
			// Display error notice if the user selected incorrect answer.
			if ($product->validate_user_incorrect_answer($customer_id)) {
				wc_add_notice(get_option('lty_settings_limited_type_single_attempt_error_message'), 'error');
			} elseif ($product->is_customer_question_answer_time_limit_exists($customer_id)) {
				wc_add_notice(get_option('lty_settings_answer_time_limit_exists_error_message'), 'error');
			}
		}

		/**
		 * Display correct answer notice in single product.
		 *
		 * @return void
		 * */
		public static function display_correct_answer_notice_in_single_product() {

			global $product;

			// Return if the force answer/validate correct answer is not enabled.
			if ('yes' != $product->is_force_answer_enabled() || 'yes' == $product->incorrectly_selected_answer_restriction_is_enabled() || 'yes' != $product->is_verify_answer_enabled()) {
				return;
			}

			// Return if limited question answer attempts is selected.
			if ('1' == $product->verify_question_answer_type()) {
				return;
			}

			$answers = $product->get_answers();
			if (!lty_check_is_array($answers)) {
				return;
			}

			$question_answer_id = isset($_REQUEST['lty_question_answer_id']) ? absint($_REQUEST['lty_question_answer_id']) : '';
			if (!$question_answer_id || !isset($answers[$question_answer_id])) {
				return;
			}

			$answer = $answers[$question_answer_id];
			if (isset($answer['valid']) && 'yes' == $answer['valid']) {
				return;
			}

			wc_add_notice(get_option('lty_settings_unlimited_type_error_message', 'Incorrect answer. Please select the correct answer to participate in the lottery'), 'error');
		}

		/**
		 * Display reserved time notice in cart and checkout.
		 *
		 * @return void
		 * */
		public static function display_reserved_time_notice_in_cart_and_checkout() {

			$cart_contents = WC()->cart->get_cart();
			if (!lty_check_is_array($cart_contents)) {
				return;
			}

			if ('yes' != get_option('lty_settings_enable_reserve_ticket_manual_selection_type')) {
				return;
			}

			$reserved_ticket_time = get_option('lty_settings_reserve_ticket_time_in_min');
			if (!$reserved_ticket_time) {
				return;
			}

			$customer_id = lty_get_current_user_cart_session_value();
			if (!$customer_id) {
				return;
			}

			foreach ($cart_contents as $key => $value) {

				$product_id = isset($value['product_id']) ? $value['product_id'] : '';
				$product = wc_get_product($product_id);
				if (!is_object($product) || !$product->exists() || 'lottery' != $product->get_type() || !$product->is_manual_ticket()) {
					continue;
				}

				$lottery_tickets = isset($value['lty_lottery']['tickets']) ? $value['lty_lottery']['tickets'] : array();
				if (!lty_check_is_array($lottery_tickets)) {
					continue;
				}

				foreach ($lottery_tickets as $ticket) {

					$reserved_ticket_data = $product->get_reserved_ticket_values($ticket);
					if (!lty_check_is_array($reserved_ticket_data)) {
						continue;
					}

					if (!in_array($customer_id, array_keys($reserved_ticket_data))) {
						continue;
					}
				}

				/* translators: %1$s product name,%2$d ticket number, %3$d,%4$d reserved ticket time */
				wc_add_notice(sprintf(__(' The %1$s ticket number %2$s is reserved for %3$d minutes. Please complete your purchase within %4$d minutes.', 'lottery-for-woocommerce'), $product->get_name(), implode(' , ', $lottery_tickets), $reserved_ticket_time, $reserved_ticket_time), 'notice');
			}
		}

		/**
		 * Set predefined buttons data.
		 *
		 * @return void
		 * */
		public static function set_predefined_buttons_data( $cart ) {
			foreach ( $cart->get_cart() as $key => $value ) {
				// If already assigned customized price for the lottery products.
				if ( in_array( $key, self::$assigned_lottery_cart_item_prices ) ) {
					continue;
				}

				if ( ! lty_is_lottery_product( $value['data'] ) || ( ! $value['data']->can_display_predefined_buttons() && ! $value['data']->can_display_predefined_with_quantity_selector() ) ) {
					continue;
				}

				if ( $value['data']->is_manual_ticket() || ! $value['data']->is_predefined_button_enabled() ) {
					continue;
				}

				/**
				 * This hook is used to alter the predefined button price in the cart.
				 *
				 * @since 8.0.0
				 */
				$price = apply_filters(' lty_predefined_button_cart_item_price', floatval( $value['data']->get_predefined_button_price_by_quantity( $value['quantity'] ) ), $value );

				$value['data']->set_price( $price );

				self::$assigned_lottery_cart_item_prices[] = $key;
			}
		}

		/**
		 * Add to cart redirection.
		 *
		 * @return string
		 * */
		public static function add_to_cart_redirect( $cart_url, $product ) {

			if (!lty_is_lottery_product($product) || 'yes' != get_option('lty_settings_participate_now_checkout_redirection_enabled')) {
				return $cart_url;
			}

			return wc_get_checkout_url();
		}

		/**
		 * May be validate predefined buttons add to cart.
		 *
		 * @return bool
		 * */
		public static function maybe_validate_predefined_buttons_add_to_cart( $lottery_product_id, $value ) {
			$lottery_product = wc_get_product($lottery_product_id);
			if (!lty_is_lottery_product($lottery_product) || ( !$lottery_product->can_display_predefined_buttons() || $lottery_product->can_display_predefined_with_quantity_selector() )) {
				return true;
			}

			if ($lottery_product->is_manual_ticket() && isset($value['lty_lottery']['lty_predefined_button_id'])) {
				return false;
			}

			if (!$lottery_product->is_predefined_button_enabled() && isset($value['lty_lottery']['lty_predefined_button_id'])) {
				return false;
			}

			if (!$lottery_product->is_manual_ticket() && $lottery_product->is_predefined_button_enabled() && !isset($value['lty_lottery']['lty_predefined_button_id'])) {
				return false;
			}

			return true;
		}

		/**
		 * Handles the payment gateways.
		 *
		 * @since 10.7.0
		 * @return array
		 */
		public static function handle_payment_gateways( $wc_gateways ) {
			if ( ! lty_is_cart_contains_lottery_items() ) {
				return $wc_gateways;
			}

			$restricted_gateways = get_option( 'lty_settings_hide_payments', array() );
			if ( ! lty_check_is_array( $restricted_gateways ) ) {
				return $wc_gateways;
			}

			foreach ( $wc_gateways as $wc_gateway_id => $wc_gateway ) {
				if ( in_array( $wc_gateway_id, $restricted_gateways ) ) {
					unset( $wc_gateways[ $wc_gateway_id ] );
				}
			}

			return $wc_gateways;
		}
	}

	LTY_Lottery_Cart::init();
}
