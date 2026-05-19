<?php

/**
 * Common functions
 *
 * @since 1.0.0
 * */
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}

require_once 'lty-layout-functions.php';
require_once 'lty-post-functions.php';
require_once 'lty-template-functions.php';
require_once 'admin/lty-admin-functions.php';

if (!function_exists('lty_check_is_array')) {

	/**
	 * Check if resource is array.
	 *
	 * @return bool
	 * */
	function lty_check_is_array( $data ) {
		return ( is_array($data) && !empty($data) );
	}

}

if (!function_exists('lty_get_lottery_ticket_args')) {

	function lty_get_lottery_ticket_args() {

		return array(
			'character_type' => get_option('lty_settings_generate_ticket_type'),
			'length' => !empty(get_option('lty_settings_ticket_length')) ? get_option('lty_settings_ticket_length') : '',
			'prefix' => get_option('lty_settings_ticket_prefix'),
			'suffix' => get_option('lty_settings_ticket_suffix'),
		);
	}

}

if (!function_exists('lty_generate_random_ticket_number')) {

	function lty_generate_random_ticket_number( $args = array() ) {
		$args = wp_parse_args(
				$args,
				array(
					'number_type' => 'random',
					'character_type' => '3',
					'length' => 10,
					'prefix' => '',
					'suffix' => '',
					'series_alphanumeric' => '',
					'sequence_number' => '',
				)
		);

		if ('series' == $args['number_type']) {
			$random_code = sanitize_title($args['series_alphanumeric']);
		} else {
			if ('2' == $args['character_type']) {
				$characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
			} elseif ('3' == $args['character_type']) {
				$exploded_val = explode(',', $args['exclude_alphbates']);
				$characters = str_replace($exploded_val, '', 'abcdefghijklmnopqrstuvwxyz1234567890');
			} else {
				$characters = '1234567890';
			}

			$random_codes = array();
			$character_length = strlen($characters) - 1; // put the length -1 in cache

			for ($i = 0; $i < $args['length']; $i++) {
				$n = mt_rand(0, $character_length);
				$random_codes[] = $characters[$n];
			}

			$random_code = implode($random_codes);
		}

		$generated_random_code = $args['prefix'] . $random_code . $args['suffix'];

		if ($args['sequence_number']) {
			$generated_random_code = $generated_random_code . '_' . $args['sequence_number'];
		}

		return $generated_random_code;
	}

}

if (!function_exists('lty_get_badge_image')) {

	/**
	 * Get Batch Image Url.
	 *
	 * @return url
	 * */
	function lty_get_badge_image( $default = false ) {
		$image_url = '';

		if (!$default) {
			$image_url = get_option('lty_settings_upload_badge_image_url');
		}

		return !empty($image_url) ? $image_url : LTY_PLUGIN_URL . '/assets/images/batch.png';
	}

}

if (!function_exists('lty_get_lottery_price_type_name')) {

	/**
	 * Get the lottery price type name.
	 *
	 * @return string
	 * */
	function lty_get_lottery_price_type_name( $method = 1 ) {
		return ( 1 == $method ) ? __('Price', 'lottery-for-woocommerce') : __('Free', 'lottery-for-woocommerce');
	}

}

if (!function_exists('lty_get_lottery_ticket_range_slider_type_name')) {

	/**
	 * Get the lottery ticket range slider type name.
	 *
	 * @since 7.5.0
	 * @param int $method
	 * @return string
	 * */
	function lty_get_lottery_ticket_range_slider_type_name( $method = 1 ) {
		return ( 1 == $method ) ? __('Maximum Tickets per User', 'lottery-for-woocommerce') : __('Preset Tickets', 'lottery-for-woocommerce');
	}

}

if ( ! function_exists( 'lty_get_valid_unique_winner_lottery_tickets' ) ) {

	/**
	 * Get the valid unique winner lottery tickets.
	 *
	 * @since 9.6.0
	 * @param object $product Product object.
	 * @return array
	 */
	function lty_get_valid_unique_winner_lottery_tickets( $product ) {
		$user_email_ids = (array) lty_get_lottery_user_email_ids( $product, 'lty_ticket_buyer' );
		if ( ! lty_check_is_array( $user_email_ids ) ) {
			return array();
		}

		$random_keys = count( $user_email_ids ) >= $product->get_lty_winners_count() ? (array) array_rand( $user_email_ids, $product->get_lty_winners_count() ) : array();
		if ( ! lty_check_is_array( $random_keys ) ) {
			return array();
		}

		$winning_user_email_ids = array_intersect_key( $user_email_ids, array_flip( $random_keys ) );

		$ticket_ids = array();
		foreach ( $winning_user_email_ids as $user_email_id ) {
			$user_ticket_ids = (array) lty_get_lottery_ticket_ids_by_user_email_id( $product, $user_email_id, 'lty_ticket_buyer' );
			$ticket_ids[]    = $user_ticket_ids[ array_rand( $user_ticket_ids, 1 ) ];
		}

		return $ticket_ids;
	}
}

if (!function_exists('lty_get_random_ticket_ids')) {

	/**
	 * Get the random ticket ids.
	 *
	 * @return array.
	 */
	function lty_get_random_ticket_ids( $ticket_ids, $lottery_product ) {

		$number_of_winners = $lottery_product->get_lty_winners_count();
		$random_ticket_ids = array();
		$random_keys = array_rand((array) $ticket_ids, $number_of_winners);
		$ticket_keys = array_keys($ticket_ids);

		if (lty_check_is_array($random_keys)) {
			foreach ($random_keys as $key) {
				if (in_array($key, $ticket_keys)) {
					$random_ticket_ids[] = isset($ticket_ids[$key]) ? $ticket_ids[$key] : '';
				}
			}
		} elseif ('1' == $number_of_winners) {
			$random_ticket_ids[] = isset($ticket_ids[array_rand($ticket_ids, 1)]) ? $ticket_ids[array_rand($ticket_ids, 1)] : $ticket_ids[0];
		}

		return $random_ticket_ids;
	}

}

if ( ! function_exists( 'lty_get_address_metas' ) ) {
	/**
	 * Get User Address meta(s).
	 *
	 * @return array.
	 */
	function lty_get_address_metas( $flag ) {
		$address_metas = array(
			'first_name',
			'last_name',
			'company',
			'address_1',
			'address_2',
			'city',
			'country',
			'postcode',
			'state',
			'phone',
		);

		return 'billing' === $flag ? array_merge( $address_metas, array( 'email' ) ) : $address_metas;
	}
}

if (!function_exists('lty_get_address')) {

	/**
	 * Get User Address
	 *
	 * @return array.
	 */
	function lty_get_address( $user_id, $flag, $ticket_order ) {
		$billing_metas = lty_get_address_metas($flag);

		foreach ($billing_metas as $each_meta) {
			if ($user_id) {
				$billing_address[$each_meta] = get_user_meta($user_id, $flag . '_' . $each_meta, true);
			} else {
				$billing_address[$each_meta] = is_object($ticket_order) ? $ticket_order->{"get_{$flag}_{$each_meta}"}() : false;
			}
		}

		return $billing_address;
	}

}

if (!function_exists('lty_is_lottery_product')) {

	/**
	 * Is a lottery product?
	 *
	 * @since 1.0.0
	 * @param object $product Product object.
	 * @return bool
	 */
	function lty_is_lottery_product( $product ) {
		if (!$product) {
			return false;
		}

		$product = is_numeric($product) ? wc_get_product($product) : $product;
		if (!is_object($product)) {
			return false;
		}

		return ( 'lottery' === $product->get_type() );
	}

}

if (!function_exists('lty_get_lottery_gift_products')) {

	/**
	 * Get user id's from lottery products.
	 */
	function lty_get_lottery_gift_products( $product_id, $product = false, $outside = false ) {
		if (!$product_id && !is_object($product)) {
			return;
		}

		if (!is_object($product)) {
			$product = wc_get_product($product_id);
		}

		$gift_products = '';
		if ('1' == $product->get_winner_product_selection_method() && lty_check_is_array($product->get_selected_gift_products())) {
			$gift_product_ids = $product->get_selected_gift_products();
			end($gift_product_ids);
			$last_key = key($gift_product_ids);

			foreach ($gift_product_ids as $key => $gift_product_id) {
				$gift_product = wc_get_product($gift_product_id);

				if (!is_object($gift_product)) {
					continue;
				}

				if ($outside) {
					$gift_products .= '<a href="' . esc_url(get_permalink($gift_product_id)) . '">' . esc_html($gift_product->get_name()) . '</a>';
				} else {
					$gift_products .= '<a href="' . esc_url(get_edit_post_link($gift_product_id)) . '">' . esc_html($gift_product->get_name()) . '</a>';
				}

				if ($key == $last_key) {
					break;
				}

				$gift_products .= ', ';
			}
		} elseif ('2' == $product->get_winner_product_selection_method()) {
			/* translators: %1$s: URL, %2$s: Display text */
			$gift_products = wp_http_validate_url($product->get_lty_winner_outside_gift_items()) ? sprintf('<a href="%1$s">%1$s</a>', esc_url($product->get_lty_winner_outside_gift_items()), esc_url($product->get_lty_winner_outside_gift_items())) : $product->get_lty_winner_outside_gift_items();
		}

		return $gift_products;
	}

}

if (!function_exists('lty_get_ip_address')) {

	/**
	 * Get IP address.
	 *
	 * @return string.
	 */
	function lty_get_ip_address() {

		$ipaddress = '';

		if (isset($_SERVER['HTTP_X_REAL_IP'])) {
			$ipaddress = sanitize_text_field(wp_unslash($_SERVER['HTTP_X_REAL_IP']));
		} elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
			$ipaddress = sanitize_text_field(wp_unslash($_SERVER['HTTP_CLIENT_IP']));
		} elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			$ipaddress = sanitize_text_field(wp_unslash($_SERVER['HTTP_X_FORWARDED_FOR']));
		} elseif (isset($_SERVER['HTTP_X_FORWARDED'])) {
			$ipaddress = sanitize_text_field(wp_unslash($_SERVER['HTTP_X_FORWARDED']));
		} elseif (isset($_SERVER['HTTP_FORWARDED_FOR'])) {
			$ipaddress = sanitize_text_field(wp_unslash($_SERVER['HTTP_FORWARDED_FOR']));
		} elseif (isset($_SERVER['HTTP_FORWARDED'])) {
			$ipaddress = sanitize_text_field(wp_unslash($_SERVER['HTTP_FORWARDED']));
		} elseif (isset($_SERVER['REMOTE_ADDR'])) {
			$ipaddress = sanitize_text_field(wp_unslash($_SERVER['REMOTE_ADDR']));
		}

		return $ipaddress;
	}

}

if (!function_exists('lty_get_winner_gift_products_title')) {

	/**
	 * Get winner gift products title.
	 *
	 * @return array.
	 */
	function lty_get_winner_gift_products_title( $gift_products, $product ) {

		if (!lty_check_is_array($gift_products)) {
			return '-';
		}

		$product_titles = array();
		foreach ($gift_products as $gift_product) {
			$selection_method = $product->get_winner_product_selection_method();
			if ('1' == $selection_method) {
				$gift_product_object = wc_get_product($gift_product);
				if (is_object($gift_product_object)) {
					$product_titles[] = sprintf('<a href="%s">%s</a>', esc_url($gift_product_object->get_permalink()), esc_html($gift_product_object->get_title()));
				}
			} else {
				$product_titles[] = $gift_product;
			}
		}

		if (!lty_check_is_array($product_titles)) {
			return '-';
		}

		return implode(' , ', $product_titles);
	}

}

if (!function_exists('lty_get_lottery_ticket_numbers')) {

	/**
	 * Get lottery ticket numbers.
	 *
	 * @return array.
	 */
	function lty_get_lottery_ticket_numbers( $product, $order_item ) {

		if (!is_object($product) || 'lottery' != $product->get_type()) {
			return false;
		}

		$quantity = isset($order_item['quantity']) ? $order_item['quantity'] : 1;
		$ticket_numbers = array();

		if ($product->is_manual_ticket()) {
			// Manual ticket numbers.
			$ticket_numbers = isset($order_item['lty_lottery_tickets']) ? $order_item['lty_lottery_tickets'] : array();
		} elseif ($product->is_automatic_sequential_ticket()) {
			// Sequential ticket numbers.
			$ticket_numbers = lty_get_remaining_sequential_ticket_numbers($product, $quantity);
		} elseif ($product->is_automatic_shuffled_ticket()) {
			// Shuffle ticket numbers.
			$ticket_numbers = lty_get_remaining_shuffle_ticket_numbers($product, $quantity);
		} else {
			// Random ticket numbers.
			$ticket_numbers = lty_get_random_ticket_numbers($product, $quantity);
		}

		return $ticket_numbers;
	}

}

if (!function_exists('lty_get_random_ticket_numbers')) {

	/**
	 * Get random ticket numbers.
	 *
	 * @return array.
	 */
	function lty_get_random_ticket_numbers( $product, $quantity ) {

		// Random ticket numbers.
		$ticket_numbers = array();
		$i = 0;
		while ($i < $quantity) {
			$ticket_args = lty_get_lottery_ticket_args();
			$per_ticket_number = lty_generate_random_ticket_number($ticket_args);
			if (in_array($per_ticket_number, $ticket_numbers)) {
				continue;
			}

			$ticket_numbers[] = $per_ticket_number;
			++$i;
		}

		return $ticket_numbers;
	}

}

if (!function_exists('lty_get_remaining_sequential_ticket_numbers')) {

	/**
	 * Get remaining sequential ticket numbers.
	 *
	 * @return array.
	 */
	function lty_get_remaining_sequential_ticket_numbers( $product, $quantity ) {
		$ticket_numbers = array_values(array_diff((array) $product->get_formatted_sequential_ticket_numbers(), (array) $product->get_placed_tickets()));
		if (!lty_check_is_array($ticket_numbers)) {
			return array();
		}

		return array_slice($ticket_numbers, 0, $quantity);
	}

}

if (!function_exists('lty_get_remaining_shuffle_ticket_numbers')) {

	/**
	 * Get remaining shuffle ticket numbers.
	 *
	 * @return array.
	 */
	function lty_get_remaining_shuffle_ticket_numbers( $product, $quantity ) {
		$ticket_numbers = array_values(array_diff((array) $product->get_formatted_shuffle_ticket_numbers(), (array) $product->get_placed_tickets()));
		if (!lty_check_is_array($ticket_numbers)) {
			return array();
		}

		$shuffled_ticket_ids = array();
		$random_keys = array_rand((array) $ticket_numbers, $quantity);
		$ticket_keys = array_keys($ticket_numbers);

		if (lty_check_is_array($random_keys)) {
			foreach ($random_keys as $key) {
				if (in_array($key, $ticket_keys)) {
					$shuffled_ticket_ids[] = isset($ticket_numbers[$key]) ? $ticket_numbers[$key] : '';
				}
			}
		} elseif ('1' == $quantity) {
			$shuffled_ticket_ids[] = isset($ticket_numbers[$random_keys]) ? $ticket_numbers[$random_keys] : $ticket_numbers[0];
		}

		return $shuffled_ticket_ids;
	}

}

if (!function_exists('lty_get_wc_order_statuses')) {

	/**
	 * Get the WC Order statuses.
	 *
	 * @return array
	 * */
	function lty_get_wc_order_statuses() {

		$order_statuses_keys = array_keys(wc_get_order_statuses());
		$order_statuses_keys = str_replace('wc-', '', $order_statuses_keys);
		$order_statuses_values = array_values(wc_get_order_statuses());

		return array_combine($order_statuses_keys, $order_statuses_values);
	}

}

if (!function_exists('lty_is_reserved_ticket')) {

	/**
	 * Is reserved ticket.
	 *
	 * @return bool
	 * */
	function lty_is_reserved_ticket( $product, $validate_reserved_tickets_data = true ) {

		if (!is_object($product) || !$product->exists() || 'lottery' != $product->get_type() || !$product->is_manual_ticket()) {
			return false;
		}

		if ('yes' != get_option('lty_settings_enable_reserve_ticket_manual_selection_type') || !get_option('lty_settings_reserve_ticket_time_in_min')) {
			return false;
		}

		if ($validate_reserved_tickets_data) {
			$reserved_tickets_data = $product->get_reserved_tickets_data();
			if (!lty_check_is_array($reserved_tickets_data)) {
				return false;
			}
		}

		return true;
	}

}
if (!function_exists('lty_unset_question_answer_metas')) {

	/**
	 * Unset question answer metas.
	 *
	 * @return void
	 * */
	function lty_unset_question_answer_metas( $product, $customer_id ) {

		// Return if product not an lottery product or product is not closed.
		if (!lty_is_lottery_product($product) || $product->is_closed()) {
			return;
		}

		// Return if valid question answer or force answer option is not enabled or validate correct answer option is not enabled.
		if (!$product->is_valid_question_answer() || 'yes' != $product->is_force_answer_enabled() || 'yes' == $product->incorrectly_selected_answer_restriction_is_enabled() || 'yes' != $product->is_verify_answer_enabled()) {
			return;
		}

		// Unset attempts.
		$attempts_data = $product->get_lty_question_answer_attempts_data();
		if (isset($attempts_data[$customer_id]) && $attempts_data[$customer_id]) {
			$attempts_data[$customer_id] = 0;
			$product->update_post_meta('lty_question_answer_attempts_data', $attempts_data);
		}

		// Unset userid.
		$incorrect_answer_user_ids = $product->get_lty_incorrect_answer_user_ids();
		if (isset($incorrect_answer_user_ids[$customer_id])) {
			unset($incorrect_answer_user_ids[$customer_id]);
			$product->update_post_meta('lty_incorrect_answer_user_ids', $incorrect_answer_user_ids);
		}
	}

}

if (!function_exists('lty_get_lottery_id')) {

	/**
	 * Get lottery id.
	 *
	 * @return int
	 * */
	function lty_get_lottery_id( $product_id ) {
		/**
		 * This hook is used to alter the lottery product ID.
		 *
		 * @since 1.0
		 */
		return apply_filters('lty_lottery_product_id', $product_id);
	}

}

if (!function_exists('lty_update_lottery_post_meta')) {

	/**
	 * Update lottery post meta.
	 *
	 * @return void
	 * */
	function lty_update_lottery_post_meta( $product_id, $key, $value ) {
		$meta = update_post_meta($product_id, sanitize_key('_' . $key), $value);
		/**
		 * This hook is used to do extra action after lottery post meta updated.
		 *
		 * @since 1.0
		 */
		do_action('lty_update_post_meta', $product_id, $key, $value);

		return $meta;
	}

}

if (!function_exists('lty_delete_lottery_post_meta')) {

	/**
	 * Delete lottery post meta.
	 *
	 * @since 11.2.0
	 * @param int $product_id
	 * @param string $key
	 * */
	function lty_delete_lottery_post_meta( $product_id, $key ) {
		 delete_post_meta($product_id, sanitize_key('_' . $key));
		/**
		 * This hook is used to do extra action after lottery post meta deleted.
		 *
		 * @since 11.2.0
		 */
		do_action('lty_delete_post_meta', $product_id, $key);
	}

}

if (!function_exists('lty_get_max_ticket_based_on_ticket_length')) {

	/**
	 * Get max ticket based on ticket length
	 *
	 * @return int
	 * */
	function lty_get_max_ticket_based_on_ticket_length() {

		$ticket_length = get_option('lty_settings_ticket_length');
		if (!$ticket_length) {
			return 0;
		}

		$max_tickets = '';
		for ($i = 0; $i < $ticket_length; $i++) {
			$max_tickets .= 9;
		}

		return absint($max_tickets);
	}

}

if (!function_exists('lty_get_current_user_cart_session_value')) {

	/**
	 * Get the current user cart session value.
	 *
	 * @since 6.8
	 *
	 * @return mixed
	 */
	function lty_get_current_user_cart_session_value() {

		if (!isset(WC()->session) || !is_object(WC()->session)) {
			return false;
		}

		return WC()->session->get_customer_id();
	}

}

if (!function_exists('lty_mask_name')) {

	/**
	 * Mask the name.
	 *
	 * @since 7.4.0
	 * @param string $name
	 *
	 * @return string.
	 */
	function lty_mask_name( $name ) {
		if (!$name) {
			return '';
		}

		$len = strlen($name);
		if ($len <= 2) {
			return str_repeat('*', $len);
		}

		return substr($name, 0, 1) . str_repeat('*', $len - 2) . substr($name, $len - 1, 1);
	}

}

if (!function_exists('lty_supported_automatic_relist_statuses')) {

	/**
	 * Supported automatic relist status.
	 *
	 * @since 7.5.0
	 *
	 * @return array.
	 */
	function lty_supported_automatic_relist_statuses() {
		/**
		 * This hook is used to alter the supported automatic relist statuses.
		 *
		 * @since 7.5.0
		 */
		return apply_filters('lty_supported_automatic_relist_statuses', array( 'lty_lottery_failed', 'lty_lottery_finished' ));
	}

}

if (!function_exists('lty_is_valid_duration')) {

	/**
	 * Is valid duration.
	 *
	 * @since 7.5.0
	 * @param array $duration
	 *
	 * @return bool.
	 */
	function lty_is_valid_duration( $duration ) {
		if (!isset($duration['number']) || empty($duration['number'])) {
			return false;
		}

		return true;
	}

}

if (!function_exists('lty_prepare_winning_dates_start_date')) {

	/**
	 * Prepare the winning dates start date.
	 *
	 * @since 8.1.0
	 * @param string $number
	 * @param string $unit
	 * @return string/boolean
	 */
	function lty_prepare_winning_dates_start_date( $number, $unit ) {
		if (!$number || !$unit) {
			return '';
		}

		$start_date = '';
		$current_date_object = LTY_Date_Time::get_gmt_date_time_object('NOW');
		switch ($unit) {
			case 'days':
				$start_date = $current_date_object->modify('-' . $number . ' days')->format('Y-m-d H:i:s');
				break;

			case 'weeks':
				$start_date = $current_date_object->modify('-' . $number . ' weeks')->format('Y-m-d H:i:s');
				break;

			case 'months':
				$start_date = $current_date_object->modify('-' . $number . ' months')->format('Y-m-d H:i:s');
				break;

			case 'years':
				$start_date = $current_date_object->modify('-' . $number . ' years')->format('Y-m-d H:i:s');
				break;
		}

		return $start_date;
	}

}

if (!function_exists('lty_prepare_pagination_arguments')) {

	/**
	 * Prepare the pagination arguments.
	 *
	 * @since 8.1.0
	 * @param int $current_page
	 * @param int $page_count
	 * @return array
	 */
	function lty_prepare_pagination_arguments( $current_page, $page_count ) {
		/**
		 * This hook is used to alter the pagination arguments.
		 *
		 * @since 8.1.0
		 */
		return apply_filters(
				'lty_pagination_arguments',
				array(
					'page_count' => $page_count,
					'current_page' => $current_page,
					'start_page' => ( $current_page - 1 ) < 2 ? 2 : $current_page - 1,
					'end_page' => ( $page_count - 1 > $current_page ) ? $current_page + 1 : $page_count,
					'prev_page_count' => ( ( $current_page - 1 ) == 0 ) ? ( $current_page ) : ( $current_page - 1 ),
					'next_page_count' => ( ( $current_page + 1 ) <= ( $page_count ) ) ? ( $current_page + 1 ) : ( $current_page ),
					'prev_dot' => ( 3 <= $current_page ),
					'next_dot' => ( ( $page_count - 1 ) != $current_page ) && ( $page_count != $current_page ),
					'prev_arrows' => 1 != $current_page,
					'next_arrows' => $page_count != $current_page,
				)
		);
	}

}

if (!function_exists('lty_can_show_tickets_non_paid_order')) {

	/**
	 * Can show the tickets non paid orders?
	 *
	 * @since 8.7.0
	 * @param object $product
	 * @param object $order
	 * @return bool
	 */
	function lty_can_show_tickets_non_paid_order( $product, $order ) {
		if (!is_object($product) || !is_object($order)) {
			return false;
		}

		// Don't show the tickets for the lottery product is instant winner.
		if ($product->is_instant_winner()) {
			return false;
		}

		// Return if order status not matched.
		if ('2' === get_option('lty_settings_show_order_ticket_number') && !in_array($order->get_status(), (array) get_option('lty_settings_lottery_complete_order_statuses'))) {
			return false;
		}

		return true;
	}

}

if (!function_exists('lty_get_lottery_sorting_options')) {

	/**
	 * Get the lottery sorting options.
	 *
	 * @since 9.0.0
	 * @staticvar array $lty_sorting_options
	 * @return type
	 */
	function lty_get_lottery_sorting_options() {
		static $lty_sorting_options;
		if ($lty_sorting_options) {
			return $lty_sorting_options;
		}

		$lty_sorting_options = array(
			'ticket_count' => __('Sort by most ticket sale on-going giveaways', 'lottery-for-woocommerce'),
			'remaining_ticket_count' => __('Sort by remaining tickets: low to high', 'lottery-for-woocommerce'),
			'remaining_ticket_count-desc' => __('Sort by remaining tickets: high to low', 'lottery-for-woocommerce'),
			'recently_started' => __('Sort by recently started giveaways', 'lottery-for-woocommerce'),
			'ending_soon' => __('Sort by ending soon giveaways', 'lottery-for-woocommerce'),
			'closed' => __('Sort by closed giveaways', 'lottery-for-woocommerce'),
			'on_going' => __('Sort by on-going giveaways', 'lottery-for-woocommerce'),
			'future' => __('Sort by future giveaways', 'lottery-for-woocommerce'),
			'failed' => __('Sort by failed giveaways', 'lottery-for-woocommerce'),
			'finished' => __('Sort by finished giveaways', 'lottery-for-woocommerce'),
		);

		/**
		 * This hook is used to alter the lottery sorting options.
		 *
		 * @since 9.0.0
		 */
		return apply_filters('lty_lottery_sorting_options', $lty_sorting_options);
	}

}

if (!function_exists('lty_get_entry_list_sorting_options')) {

	/**
	 * Get the entry list sorting options.
	 *
	 * @since 9.0.0
	 * @staticvar array $lty_entry_list_sorting_options
	 * @return array
	 */
	function lty_get_entry_list_sorting_options() {
		static $lty_entry_list_sorting_options;
		if ($lty_entry_list_sorting_options) {
			return $lty_entry_list_sorting_options;
		}

		$lty_entry_list_sorting_options = array(
			'on_going' => __('Sort by on-going giveaways', 'lottery-for-woocommerce'),
			'ticket_count' => __('Sort by most ticket sale on-going giveaways', 'lottery-for-woocommerce'),
			'remaining_ticket_count' => __('Sort by remaining tickets: low to high', 'lottery-for-woocommerce'),
			'remaining_ticket_count-desc' => __('Sort by remaining tickets: high to low', 'lottery-for-woocommerce'),
			'recently_started' => __('Sort by recently started giveaways', 'lottery-for-woocommerce'),
			'ending_soon' => __('Sort by ending soon giveaways', 'lottery-for-woocommerce'),
			'closed' => __('Sort by closed giveaways', 'lottery-for-woocommerce'),
			'failed' => __('Sort by finished giveaways', 'lottery-for-woocommerce'),
			'finished' => __('Sort by finished giveaways', 'lottery-for-woocommerce'),
		);

		/**
		 * This hook is used to alter the lottery entry list sorting options.
		 *
		 * @since 9.0.0
		 */
		return apply_filters('lty_lottery_entry_list_sorting_options', $lty_entry_list_sorting_options);
	}

}

if (!function_exists('lty_get_entry_list_product_permalink')) {

	/**
	 * Get the entry list product permalink.
	 *
	 * @since 9.0.0
	 * @return bool
	 * */
	function lty_get_entry_list_product_permalink( $product ) {

		remove_filter('post_type_link', array( 'LTY_Lottery_Page_Handler', 'alter_entry_list_add_to_cart_permalink' ), 10, 2);
		$permalink = $product->get_permalink();
		add_filter('post_type_link', array( 'LTY_Lottery_Page_Handler', 'alter_entry_list_add_to_cart_permalink' ), 10, 2);

		return $permalink;
	}

}

if (!function_exists('lty_customize_array_position')) {

	/**
	 * Customize array position in my account page.
	 *
	 * @since 9.1.0
	 * @param array  $existing_menus Existing menus.
	 * @param string $position Position to display after.
	 * @param array  $new_menu New menu.
	 * @return array
	 */
	function lty_customize_array_position( $existing_menus, $position, $new_menu ) {
		$index = array_search($position, array_keys($existing_menus));
		$position = false === $index ? count($existing_menus) : $index + 1;
		$new_menu = is_array($new_menu) ? $new_menu : array( $new_menu );

		return array_merge(array_slice($existing_menus, 0, $position), $new_menu, array_slice($existing_menus, $position));
	}

}

if (!function_exists('lty_can_display_closed_lottery_details_in_product_page')) {

	/**
	 * Can display closed lottery details in product page?
	 *
	 * @since 9.2.0
	 * @return bool
	 * */
	function lty_can_display_closed_lottery_details_in_product_page() {
		return 'yes' === get_option('lty_settings_display_closed_lottery_details_product_page');
	}

}

if (!function_exists('lty_can_display_finished_lottery_details_in_product_page')) {

	/**
	 * Can display finished lottery details in product page?
	 *
	 * @since 9.2.0
	 * @return bool
	 * */
	function lty_can_display_finished_lottery_details_in_product_page() {
		return 'yes' === get_option('lty_settings_display_finished_lottery_details_product_page');
	}

}

if (!function_exists('lty_can_display_failed_lottery_details_in_product_page')) {

	/**
	 * Can display failed lottery details in product page?
	 *
	 * @since 9.2.0
	 * @return bool
	 * */
	function lty_can_display_failed_lottery_details_in_product_page() {
		return 'yes' === get_option('lty_settings_display_failed_lottery_details_product_page');
	}

}

if (!function_exists('lty_add_html_inline_style')) {

	/**
	 * Add the custom CSS to HTML elements.
	 *
	 * @since 9.5.0
	 * @param string|HTML $content Contents.
	 * @param string      $css CSS.
	 * @param bool        $full_content Full content or not.
	 * @return mixed
	 */
	function lty_add_html_inline_style( $content, $css, $full_content = false ) {
		if (!$css || !$content) {
			return $content;
		}

		// Return the content with style css when DOMDocument class not exists.
		if (!class_exists('DOMDocument')) {
			return '<style type="text/css">' . $css . '</style>' . $content;
		}

		if (class_exists('\Pelago\Emogrifier\CssInliner')) {
			// To create a instance with original HTML.
			$css_inliner_class = 'Pelago\Emogrifier\CssInliner';
			$domDocument = $css_inliner_class::fromHtml($content)->inlineCss($css)->getDomDocument();
			// Removing the elements with display:none style declaration from the content.
			$html_pruner_class = 'Pelago\Emogrifier\HtmlProcessor\HtmlPruner';
			$html_pruner_class::fromDomDocument($domDocument)->removeElementsWithDisplayNone();
			// Converts a few style attributes values to visual HTML attributes.
			$attribute_converter_class = 'Pelago\Emogrifier\HtmlProcessor\CssToAttributeConverter';
			$visual_html = $attribute_converter_class::fromDomDocument($domDocument)->convertCssToVisualAttributes();

			$content = ( $full_content ) ? $visual_html->render() : $visual_html->renderBodyContent();
		} elseif (class_exists('\Pelago\Emogrifier')) {
			$emogrifier_class = 'Pelago\Emogrifier';
			$emogrifier = new Emogrifier($content, $css);
			$content = ( $full_content ) ? $emogrifier->emogrify() : $emogrifier->emogrifyBodyContent();
		} elseif (version_compare(WC_VERSION, '4.0', '<')) {
			$emogrifier_class = 'Emogrifier';
			if (!class_exists($emogrifier_class)) {
				include_once dirname(WC_PLUGIN_FILE) . '/includes/libraries/class-emogrifier.php';
			}

			$emogrifier = new Emogrifier($content, $css);
			$content = ( $full_content ) ? $emogrifier->emogrify() : $emogrifier->emogrifyBodyContent();
		}

		return $content;
	}

}

if (!function_exists('lty_get_lottery_ticket_pdf_file_path')) {

	/**
	 * Get lottery ticket PDF file path.
	 *
	 * @since 9.5.0
	 * @param array      $ticket_ids Ticket IDs.
	 * @param int|string $order_id Order ID.
	 * @return string
	 * */
	function lty_get_lottery_ticket_pdf_file_path( $ticket_ids, $order_id ) {
		$file_name = str_replace(
				array( '{order_id}', '{tickets_count}', '{date}' ),
				array( $order_id, count($ticket_ids), gmdate('Ymd') ),
				get_option('lty_settings_lottery_ticket_pdf_file_name', __('Giveaway Ticket for {order_id}{tickets_count}', 'lottery-for-woocommerce'))
		);

		$file_path = LTY_File_Uploader::prepare_pdf_file_name($file_name, 'pdf');
		if (!file_exists($file_path)) {
			LTY_Generate_PDF_Handler::generate_lottery_ticket($ticket_ids, $order_id);
			return $file_path;
		}

		return $file_path;
	}

}

if (!function_exists('lty_can_display_lottery_entry_list_summary')) {

	/**
	 * Can display lottery details in entry list page?
	 *
	 * @since 9.5.0
	 * @return bool
	 * */
	function lty_can_display_lottery_entry_list_summary() {
		return 'yes' !== get_option('lty_settings_hide_entry_list_lottery_details', 'no');
	}

}

if (!function_exists('lty_prepare_ticket_logs_template_arguments')) {

	/**
	 * Get the ticket logs template arguments.
	 *
	 * @since 1.0.0
	 * @param object $product Product object.
	 * @param int    $current_page Current page.
	 * @param string $search Search term.
	 * @return array
	 */
	function lty_prepare_ticket_logs_template_arguments( $product, $current_page = 1, $search = '' ) {
		$post_ids = lty_get_purchased_tickets_ids_on_ticket_logs_tab($product, $search);
		$post_per_page = get_option('lty_settings_single_product_tab_lottery_details_per_page', 10);
		$offset = ( $post_per_page * $current_page ) - $post_per_page;
		$page_count = ceil(count($post_ids) / $post_per_page);

		return array(
			'product' => $product,
			'ticket_ids' => array_slice($post_ids, $offset, $post_per_page),
			'columns' => lty_get_ticket_logs_table_header($product),
			'search' => $search,
			'pagination' => lty_prepare_pagination_arguments($current_page, $page_count),
			'page' => 'product',
		);
	}

}

if (!function_exists('lty_prepare_lottery_entry_list_ticket_log_arguments')) {

	/**
	 * Get the lottery entry list ticket log arguments.
	 *
	 * @since 9.5.0
	 * @param object $product Product object.
	 * @param int    $current_page Current page.
	 * @param string $search Search term.
	 * @return array
	 */
	function lty_prepare_lottery_entry_list_ticket_log_arguments( $product, $current_page = 1, $search = '' ) {
		$post_ids = lty_get_purchased_tickets_ids_on_ticket_logs_tab($product, $search);
		$post_per_page = get_option('lty_settings_single_product_tab_lottery_details_per_page', 10);
		$offset = ( $post_per_page * $current_page ) - $post_per_page;
		$page_count = ceil(count($post_ids) / $post_per_page);

		return array(
			'product' => $product,
			'ticket_ids' => array_slice($post_ids, $offset, $post_per_page),
			'columns' => lty_get_lottery_entry_list_ticket_logs_table_columns($product),
			'search' => $search,
			'pagination' => lty_prepare_pagination_arguments($current_page, $page_count),
			'page' => 'entry-list',
		);
	}

}

if (!function_exists('lty_can_display_lottery_entry_list_pdf_download_button')) {

	/**
	 * Can display lottery entry list pdf download button?
	 *
	 * @since 9.5.0
	 * @return bool
	 * */
	function lty_can_display_lottery_entry_list_pdf_download_button() {
		return 'yes' === get_option('lty_settings_allow_entry_list_pdf_download', 'no');
	}

}

if (!function_exists('lty_encode')) {

	/**
	 * Get encoded code.
	 *
	 * @since 9.5.0
	 * @param mixed $code The code to be encoded.
	 * @param bool  $json_encode Whether to json encode or not.
	 * @return string
	 * */
	function lty_encode( $code, $json_encode = false ) {
		if ($json_encode) {
			$code = wp_json_encode($code);
		}

		return base64_encode($code);
	}

}

if (!function_exists('lty_decode')) {

	/**
	 * Get encoded string.
	 *
	 * @since 9.5.0
	 * @param string $code The code to be encoded.
	 * @param bool   $json_decode Whether to json decode or not.
	 * @return string
	 * */
	function lty_decode( $code, $json_decode = false ) {
		$decoded_code = base64_decode($code);
		if (!$json_decode) {
			return $decoded_code;
		}

		return json_decode($decoded_code);
	}

}

if (!function_exists('lty_get_cart_contents')) {

	/**
	 * Get cart contents.
	 *
	 * @since 9.6.0
	 * @return array
	 * */
	function lty_get_cart_contents() {
		return is_object(WC()->cart) ? WC()->cart->get_cart() : array();
	}

}

if (!function_exists('lty_prepare_instant_winner_logs_arguments')) {

	/**
	 * Get the instant winner logs template arguments.
	 *
	 * @since 1.0.0
	 * @param object $product Product object.
	 * @param int    $current_page Current page.
	 * @return array
	 */
	function lty_prepare_instant_winner_logs_arguments( $product, $current_page = 1 ) {
		$post_ids = array();
		$args     = array( 'product' => $product );
		if ( lty_is_lottery_product( $product ) ) {
			// Check is group prize display mode.
			if ( '2' === $product->get_lty_instant_winner_display_mode() ) {
				$post_ids = $product->get_instant_winner_prize_group_ids();
			} else { // Default mode.
				$post_ids        = $product->get_current_instant_winner_log_ids();
				$args['columns'] = lty_get_instant_winners_prize_columns( $product );
			}
		}

		$post_per_page = intval( get_option( 'lty_settings_instant_winner_prizes_per_page', 10 ) );
		$page_count    = 1;
		if ( 0 < $post_per_page ) {
			$offset     = ( $post_per_page * $current_page ) - $post_per_page;
			$page_count = ceil( count( $post_ids ) / $post_per_page );
			$post_ids   = array_slice( $post_ids, $offset, $post_per_page );
		}

		return array_merge(
			$args,
			array(
				'post_ids'   => $post_ids,
				'pagination' => lty_prepare_pagination_arguments($current_page, $page_count),
			)
		);
	}
}

if (!function_exists('lty_get_instant_winner_prize_group_ticket_logs_arguments')) {

	/**
	 * Get the instant winner prize group ticket logs template arguments.
	 *
	 * @since 12.0.0
	 * @param object $product Product
	 * @param int $prize_group_id Prize group id.
	 * @param int $current_page Current page.
	 * @return array
	 */
	function lty_get_instant_winner_prize_group_ticket_logs_arguments( $product, $prize_group_id, $current_page = 1 ) {
		$args     = array( 'post_ids' => array(), 'product' => $product, 'prize_group_id' => $prize_group_id, 'pagination' => array() );

		if (! $prize_group_id ) {
			return $args;
		}

		$prize_group = lty_get_instant_winner_prize_group( $prize_group_id );
		if ( ! $prize_group->exists() || ! lty_check_is_array( $prize_group->get_instant_winner_log_ids() ) ) {
			return $args;
		}

		$post_ids    = $prize_group->get_instant_winner_log_ids();
		$post_per_page = intval( get_option( 'lty_settings_instant_win_group_tickets_per_page', 10 ) );
		$page_count    = 1;
		if ( 0 < $post_per_page ) {
			$offset     = ( $post_per_page * $current_page ) - $post_per_page;
			$page_count = ceil( count( $post_ids ) / $post_per_page );
			$post_ids   = array_slice( $post_ids, $offset, $post_per_page );
		}

		$args['post_ids']   = $post_ids;
		$args['pagination'] = lty_prepare_pagination_arguments( $current_page, $page_count );

		return $args;
	}
}

if (!function_exists('lty_get_dashboard_participated_lotteries_endpoint_url')) {

	/**
	 * Get the dashboard participated lotteries URL parameter.
	 *
	 * @since 10.2.0
	 * @return string
	 * */
	function lty_get_dashboard_participated_lotteries_endpoint_url() {
		$url_parameter = !empty(get_option('lty_settings_dashboard_participated_lotteries_url_param')) ? get_option('lty_settings_dashboard_participated_lotteries_url_param') : 'lty_participated_lottery_products';

		/**
		 * This hook is used to alter the dashboard participated lotteries URL parameter.
		 *
		 * @since 10.2.0
		 */
		return apply_filters('lty_dashboard_participated_lotteries_endpoint_url', $url_parameter);
	}

}

if (!function_exists('lty_get_dashboard_won_lotteries_endpoint_url')) {

	/**
	 * Get the dashboard won lotteries URL parameter.
	 *
	 * @since 10.2.0
	 * @return string
	 * */
	function lty_get_dashboard_won_lotteries_endpoint_url() {
		$url_parameter = !empty(get_option('lty_settings_dashboard_won_lotteries_url_param')) ? get_option('lty_settings_dashboard_won_lotteries_url_param') : 'lty_won_lottery_products';

		/**
		 * This hook is used to alter the dashboard won lotteries URL parameter.
		 *
		 * @since 10.2.0
		 */
		return apply_filters('lty_dashboard_won_lotteries_endpoint_url', $url_parameter);
	}

}

if (!function_exists('lty_get_dashboard_not_won_lotteries_endpoint_url')) {

	/**
	 * Get the dashboard not won/lost lotteries URL parameter.
	 *
	 * @since 10.2.0
	 * @return string
	 * */
	function lty_get_dashboard_not_won_lotteries_endpoint_url() {
		$url_parameter = !empty(get_option('lty_settings_dashboard_not_won_lotteries_url_param')) ? get_option('lty_settings_dashboard_not_won_lotteries_url_param') : 'lty_not_won_lottery_products';

		/**
		 * This hook is used to alter the dashboard not won/lost lotteries URL parameter.
		 *
		 * @since 10.2.0
		 */
		return apply_filters('lty_dashboard_not_won_lotteries_endpoint_url', $url_parameter);
	}

}

if ( ! function_exists('lty_get_dashboard_instant_win_endpoint_url' ) ) {

	/**
	 * Get the dashboard instant win URL parameter.
	 *
	 * @since 10.6.0
	 * @return string
	 * */
	function lty_get_dashboard_instant_win_endpoint_url() {
		$url_parameter = get_option('lty_settings_dashboard_instant_win_url_param' );
		if ( empty( $url_parameter ) ) {
			$url_parameter = 'lty_instant_win';
		}

		/**
		 * This hook is used to alter the dashboard instant win URL parameter.
		 *
		 * @since 10.6.0
		 */
		return apply_filters( 'lty_dashboard_instant_win_endpoint_url', $url_parameter );
	}
}

if ( ! function_exists( 'lty_generate_instant_winner_random_coupon_code' ) ) {

	/**
	 * Generate instant winner random coupon code.
	 * 
	 * @since 10.6.0
	 * @return string
	 */
	function lty_generate_instant_winner_random_coupon_code() {
		$prefix = get_option( 'lty_settings_instant_win_coupon_prefix' );
		$suffix = get_option( 'lty_settings_instant_win_coupon_suffix' );

		$coupon_length = intval( get_option( 'lty_settings_instant_win_coupon_length', 8 ) ) - ( strlen( $prefix ) - strlen( $suffix ) );
		$coupon_length = $coupon_length ? $coupon_length : 1;
		$characters    = array_merge( range( 'A', 'Z' ), range( '0', '9' ) );
		
		$code = '';
		// Pick the random characters.
		for ( $i = 0; $i < $coupon_length; $i++ ) {
			$code .= $characters[ array_rand( $characters ) ];
		}

		// Prepare the random coupon code.
		$random_code = $prefix . $code . $suffix;

		/**
		 * This hook is used to alter the generated random coupon code.
		 * 
		 * @since 10.6.0
		 */
		return apply_filters( 'lty_generate_instant_winner_random_coupon_code', $random_code, $code );
	}
}

if ( ! function_exists( 'lty_is_order_contains_instant_win_lottery' ) ) {
	/**
	 * Is order contains instant win lottery?
	 * 
	 * @since 11.4.0
	 * @param object $order Order object.
	 * @return bool
	 */
	function lty_is_order_contains_instant_win_lottery( $order ) {
		if ( ! is_object( $order ) ) {
			return false;
		}

		foreach ( $order->get_items() as $item ) {
			if ( ! lty_is_lottery_product( $item->get_product() ) ) {
				continue;
			}

			if ( 'yes' === $item->get_meta( 'lty_is_instant_win_lottery' ) || $item->get_product()->is_instant_winner() ) {
				return true;
			}
		}

		return false;
	}
}

if ( ! function_exists( 'lty_is_block_checkout' ) ) {
	/**
	 * Is a block checkout page?
	 *
	 * @since 11.4.0
	 * @return bool
	 */
	function lty_is_block_checkout() {
		static $is_block_checkout;
		if ( isset( $is_block_checkout ) ) {
			return $is_block_checkout;
		}

		global $post;
		$is_singular = true;
		if ( ! is_a( $post, 'WP_Post' ) ) {
			$is_singular = false;
		}

		// Consider as block checkout while the request call via Store API.
		if ( isset( $GLOBALS['wp']->query_vars['rest_route'] ) && false !== strpos( $GLOBALS['wp']->query_vars['rest_route'], '/wc/store/v1' ) ) {
			return true;
		}

		$is_block_checkout = $is_singular && has_block( 'woocommerce/checkout', $post );

		return $is_block_checkout;
	}
}

if ( ! function_exists('lty_get_order_item_ticket_number_name' ) ) {
	/**
	 * Get the order item ticket number(s) name.
	 *
	 * @since 11.5.0
	 * @return string
	 * */
	function lty_get_order_item_ticket_number_name() {
		return __( 'Ticket Number( s )', 'lottery-for-woocommerce' );
	}
}

if ( ! function_exists( 'lty_get_random_user_chooses_ticket_numbers_by_quantity' ) ) {
	/**
	 * Get the random user chooses ticket numbers by quantity.
	 *
	 * @since 11.5.0
	 * @param object $product Product object.
	 * @param int    $quantity Quantity.
	 * @return array
	 * */
	function lty_get_random_user_chooses_ticket_numbers_by_quantity( $product, $quantity ) {
		if ( ! lty_is_lottery_product( $product ) ) {
			return array();
		}

		$remaining_ticket_numbers = $product->get_remaining_tickets();
		if ( ! lty_check_is_array( $remaining_ticket_numbers ) ) {
			return array();
		}

		$tickets_keys = (array) array_rand( $remaining_ticket_numbers, $quantity );
		foreach ( $tickets_keys as $ticket_key ) {
			$ticket_numbers[] = isset( $remaining_ticket_numbers[ $ticket_key ] ) ? $remaining_ticket_numbers[ $ticket_key ] : '';
		}

		return array_filter( $ticket_numbers );
	}
}

if ( ! function_exists( 'lty_set_lottery_queue_flush_rewrite_rules' ) ) {
	/**
	 * Set lottery queue flush rewrite rules.
	 *
	 * @since 11.6.0
	 * @return void
	 */
	function lty_set_lottery_queue_flush_rewrite_rules() {
		update_option( 'lty_lottery_queue_flush_rewrite_rules', 'yes' );
	}
}

if ( ! function_exists( 'lty_unset_lottery_queue_flush_rewrite_rules' ) ) {
	/**
	 * Unset lottery queue flush rewrite rules.
	 *
	 * @since 11.6.0
	 * @return void
	 */
	function lty_unset_lottery_queue_flush_rewrite_rules() {
		update_option( 'lty_lottery_queue_flush_rewrite_rules', 'no' );
	}
}

if ( ! function_exists( 'lty_array_filter' ) ) {
	/**
	 * Array filter.
	 *
	 * @since 11.8.0
	 * @param mixed $data Filter data.
	 * @return bool
	 * */
	function lty_array_filter( $data ) {
		return ! empty( $data );
	}
}

if ( ! function_exists( 'lty_get_wc_script_handle_name' ) ) {
	/**
	 * Get the WooCommerce script handle name.
	 *
	 * @since 12.1.0
	 * @param string $handle_key Handle key.
	 * @return string
	 * */
	function lty_get_wc_script_handle_name( $handle_key ) {
		if ( version_compare( WC_VERSION, '10.3.0', '<' ) ) {
			$handle_names = array( 'blockui' => 'jquery-blockui', 'accounting' => 'accounting', 'touch-punch' => 'jquery-touch-punch', 'select2' => 'select2' );
		} else {
			$handle_names = array( 'blockui' => 'wc-jquery-blockui', 'accounting' => 'wc-accounting', 'touch-punch' => 'wc-jquery-ui-touchpunch', 'select2' => 'wc-select2' );
		}

		return isset( $handle_names[ $handle_key ] ) ? $handle_names[ $handle_key ] : '';
	}
}

if ( ! function_exists( 'lty_get_remainder_email_scheduler_time' ) ) {
	/**
	 * Get the remainder email scheduler time.
	 *
	 * @since 12.4.0
	 * @param string $type
	 * @return int
	 * */
	function lty_get_remainder_email_scheduler_time( $type = 'ending_soon' ) {
		$interval = 0;
		$cron_type = '';
		$cron_time = '';

		switch ($type) {
			case 'ending_soon':
				$remainder_email = get_option('lty_customer_lottery_ending_soon_type');
				$cron_type = isset($remainder_email['unit']) ? $remainder_email['unit'] : 'hours';
				$cron_time = isset($remainder_email['number']) ? $remainder_email['number'] : 1;
				break;
		}

		switch ($cron_type) {
			case 'days':
				$interval = $cron_time * 86400;
				break;
			case 'minutes':
				$interval = $cron_time * 60;
				break;
			default:
				$interval = $cron_time * 3600;
				break;
		}

		return $interval;
	}
}
