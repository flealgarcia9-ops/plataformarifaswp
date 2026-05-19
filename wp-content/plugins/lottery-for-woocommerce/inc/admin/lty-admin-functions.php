<?php

/**
 * Admin functions
 * */
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}

if (!function_exists('lty_get_allowed_setting_tabs')) {

	/**
	 * Get the setting tabs.
	 *
	 * @return array
	 * */
	function lty_get_allowed_setting_tabs() {

		/**
		 * This hook is used to alter the settings tabs array.
		 *
		 * @since 1.0
		 */
		return apply_filters('lty_settings_tabs_array', array());
	}

}

if (!function_exists('lty_page_screen_ids')) {

	/**
	 * Page screen IDs.
	 *
	 * @return array
	 * */
	function lty_page_screen_ids() {
		/**
		 * This hook is used to alter the lottery page screen IDs.
		 *
		 * @since 1.0
		 */
		return apply_filters(
			'lty_page_screen_ids',
			array(
				sanitize_title( LTY()->menu_name() ) . '_page_lty_settings',
				'toplevel_page_lty_lottery',
				'product',
				'shop_order',
				'woocommerce_page_wc-orders',
			)
		);
	}

}

if (!function_exists('lty_current_page_screen_id')) {

	/**
	 * Get the current page screen ID.
	 *
	 * @since 9.2.0
	 * @static string $lty_current_screen_id
	 * @return string
	 */
	function lty_current_page_screen_id() {
		static $lty_current_screen_id;
		if ($lty_current_screen_id) {
			return $lty_current_screen_id;
		}

		$lty_current_screen_id = false;
		if (!empty($_REQUEST['screen'])) {
			$lty_current_screen_id = wc_clean(wp_unslash($_REQUEST['screen']));
		} elseif (function_exists('get_current_screen')) {
			$screen = get_current_screen();
			$lty_current_screen_id = isset($screen, $screen->id) ? $screen->id : '';
		}

		$lty_current_screen_id = str_replace('edit-', '', $lty_current_screen_id);

		return $lty_current_screen_id;
	}

}

if (!function_exists('lty_get_settings_page_url')) {

	/**
	 * Get the settings page URL.
	 *
	 * @return URL
	 * */
	function lty_get_settings_page_url( $args = array() ) {

		$url = add_query_arg(array( 'page' => 'lty_settings' ), admin_url('admin.php'));

		if (lty_check_is_array($args)) {
			$url = add_query_arg($args, $url);
		}

		return $url;
	}

}

if (!function_exists('lty_get_lottery_page_url')) {

	/**
	 * Get the lottery page URL.
	 *
	 * @return URL
	 * */
	function lty_get_lottery_page_url( $args = array() ) {

		$url = add_query_arg(array( 'page' => 'lty_lottery' ), admin_url('admin.php'));

		if (lty_check_is_array($args)) {
			$url = add_query_arg($args, $url);
		}

		return $url;
	}

}

if (!function_exists('lty_get_winner_selection_method_name')) {

	/**
	 * Get the winner selection method name.
	 *
	 * @return string
	 * */
	function lty_get_winner_selection_method_name( $method = 1 ) {
		return ( 1 == $method ) ? __('Automatic', 'lottery-for-woocommerce') : __('Manual', 'lottery-for-woocommerce');
	}

}

if ( ! function_exists( 'lty_get_product_status_datas' ) ) {
	/**
	 * Get product status Overview Data.
	 */
	function lty_get_product_status_datas( $product, $type ) {
		if ( ! lty_is_lottery_product( $product ) ) {
			return array();
		}

		if ( $product->get_lty_relisted() && isset( $_GET['section'] ) && ! empty( absint( $_GET['section'] ) ) ) {
			$index                          = isset( $_GET['section'] ) ? absint( $_GET['section'] - 1 ) : 0;
			$relist_data                    = array_reverse( $product->get_lty_relists() );
			$start_date                     = LTY_Date_Time::get_wp_format_datetime_from_gmt( $relist_data[ $index ]['start_date_gmt'] );
			$end_date                       = LTY_Date_Time::get_wp_format_datetime_from_gmt( $relist_data[ $index ]['end_date_gmt'] );
			$lottery_status                 = isset( $relist_data[ $index ]['lottery_status'] ) ? $relist_data[ $index ]['lottery_status'] : 'lty_lottery_failed';
			$failed_date                    = LTY_Date_Time::get_wp_format_datetime_from_gmt( $relist_data[ $index ]['failed_date_gmt'] );
			$failed_reason                  = lty_display_failed_reason( $relist_data[ $index ]['failed_reason'], false );
			$ticket_count                   = absint( $relist_data[ $index ]['ticket_count'] );
			$is_unlimited_scheduled_lottery = isset( $relist_data[ $index ]['unlimited_scheduled_lottery'] ) && 'yes' === $relist_data[ $index ]['unlimited_scheduled_lottery'];
		} else {
			$start_date                     = LTY_Date_Time::get_wp_format_datetime_from_gmt( $product->get_lty_start_date_gmt() );
			$end_date                       = LTY_Date_Time::get_wp_format_datetime_from_gmt( $product->get_lty_end_date_gmt() );
			$lottery_status                 = $product->get_lty_lottery_status();
			$failed_date                    = LTY_Date_Time::get_wp_format_datetime_from_gmt( $product->get_lty_failed_date_gmt() );
			$failed_reason                  = lty_display_failed_reason( $product->get_lty_failed_reason(), false );
			$ticket_count                   = $product->get_purchased_ticket_count();
			$is_unlimited_scheduled_lottery = $product->is_unlimited_scheduled_lottery();
		}

		$product_status_datas = array(
			'lty_start_date'   => array(
				'label' => __( 'Start Date', 'lottery-for-woocommerce' ),
				'value' => $start_date,
			),
			'lty_end_date'     => array(
				'label' => __( 'End Date', 'lottery-for-woocommerce' ),
				'value' => $is_unlimited_scheduled_lottery && ! $product->is_closed() ? __( 'Unlimited', 'lottery-for-woocommerce' ) : $end_date,
			),
			'lty_ticket_count' => array(
				'label' => __( 'Ticket Count', 'lottery-for-woocommerce' ),
				'value' => $ticket_count,
			),
			'lty_status'       => array(
				'label' => __( 'Status', 'lottery-for-woocommerce' ),
				'value' => lty_display_status( esc_html( $lottery_status ) ),
			),
		);

		// Failed Date.
		if ( ( $product->get_lty_relisted() && isset( $_GET['section'] ) ) && 'lty_lottery_failed' === $lottery_status ) {
			$product_status_datas['lty_failed_date']   = array(
				'label' => __( 'Failed Date', 'lottery-for-woocommerce' ),
				'value' => $failed_date,
			);
			$product_status_datas['lty_failed_reason'] = array(
				'label' => __( 'Failed Reason', 'lottery-for-woocommerce' ),
				'value' => $failed_reason,
			);
		}

		if ( '1' == $type ) {
			$product_status_datas['lty_listing_type'] = array(
				'label' => __( 'List Type', 'lottery-for-woocommerce' ),
				'value' => lty_check_is_array( $product->get_lty_relists() ) ? __( 'Relist', 'lottery-for-woocommerce' ) : __( 'Original', 'lottery-for-woocommerce' ),
			);

			if ( lty_check_is_array( $product->get_lty_relists() ) ) {
				$product_status_datas['lty_relist_count'] = array(
					'label' => __( 'Relist Count', 'lottery-for-woocommerce' ),
					'value' => $product->get_current_relist_count(),
				);
			}
		}

		global $current_section;
		if (!$current_section && $product->has_lottery_status('lty_lottery_started') && !empty(lty_get_order_ids_without_tickets($product))) {
			$html = sprintf('<a href="#" class="lty-orders-without-tickets-popup-action" data-product_id="%d">%s</a>', $product->get_id(), __('Click to View', 'lottery-for-woocommerce'));
			$product_status_datas['lty_orders_without_tickets'] = array(
				'label' => __('Order(s) Without Ticket Numbers (<span class="lty-error">Due to Technical Issue</span>)', 'lottery-for-woocommerce'),
				'value' => $html,
			);
			$template_args = array(
				'title' => __('Order(s) Without Ticket Numbers', 'lottery-for-woocommerce'),
				'template_name' => 'lty-orders-without-tickets-backbone-modal',
				'wrapper_class_name' => 'lty-orders-without-tickets-contents-wrapper',
				'contents_class_name' => 'lty-orders-without-tickets-contents',
			);

			lty_preview_template($template_args);
		}

		return $product_status_datas;
	}

}

if (!function_exists('lty_get_product_config_datas')) {

	/**
	 * Get Product Configuration Data.
	 *
	 * @since 1.0.0
	 * @param object $product
	 * @param string $current_section
	 * @return array
	 */
	function lty_get_product_config_datas( $product, $current_section = false ) {
		if (!is_object($product)) {
			return array();
		}

		if ($current_section) {
			$index = isset($_GET['section']) ? absint($_GET['section'] - 1) : 0;
			$relist_data = array_reverse($product->get_lty_relists());
			$product_config_datas = $relist_data[$index]['lottery_configuration'];
		} else {
			$product_config_datas = lty_get_lottery_product_configuration(false, $product);
		}

		return $product_config_datas;
	}

}

if (!function_exists('lty_get_lottery_product_configuration')) {

	/**
	 * Get endpoint URL .
	 */
	function lty_get_lottery_product_configuration( $product_id, $product = false ) {
		if (!$product_id && !is_object($product)) {
			return;
		}

		if (!is_object($product)) {
			$product = wc_get_product($product_id);
		}

		$product_config = array(
			'lottery_schedule_type' => array(
				'label' => __( 'Schedule Type', 'lottery-for-woocommerce' ),
				'value' => '2' === $product->get_lty_lottery_schedule_type() ? __( 'Unlimited', 'lottery-for-woocommerce' ) : __( 'Limited', 'lottery-for-woocommerce' ),
			),
			'start_date' => array(
				'label' => __('Start Date', 'lottery-for-woocommerce'),
				'value' => $product->get_lty_start_date_gmt(),
			),
			'end_date' => array(
				'label' => __('End Date', 'lottery-for-woocommerce'),
				'value' => $product->get_lty_end_date_gmt(),
			),
			'minimum_ticket' => array(
				'label' => __('Minimum Tickets', 'lottery-for-woocommerce'),
				'value' => $product->get_lty_minimum_tickets(),
			),
			'maximum_ticket' => array(
				'label' => __('Maximum Tickets', 'lottery-for-woocommerce'),
				'value' => $product->get_lty_maximum_tickets(),
			),
			'ticket_range_slider_type' => array(
				'label' => __('Display Range Slider Based On', 'lottery-for-woocommerce'),
				'value' => lty_get_lottery_ticket_range_slider_type_name($product->get_lty_ticket_range_slider_type()),
			),
			'preset_ticket' => array(
				'label' => __('Preset Tickets', 'lottery-for-woocommerce'),
				'value' => $product->get_lty_preset_tickets(),
			),
			'max_ticket_per_order' => array(
				'label' => __('Maximum Tickets Per Order', 'lottery-for-woocommerce'),
				'value' => $product->get_lty_order_maximum_tickets(),
			),
			'mix_ticket_per_user' => array(
				'label' => __('Minimum Tickets Per User', 'lottery-for-woocommerce'),
				'value' => $product->get_min_purchase_quantity(),
			),
			'max_ticket_per_user' => array(
				'label' => __('Maximum Tickets Per User', 'lottery-for-woocommerce'),
				'value' => $product->get_lty_user_maximum_tickets(),
			),
			'no_of_winner' => array(
				'label' => __('Number of Winner', 'lottery-for-woocommerce'),
				'value' => $product->get_lty_winners_count(),
			),
			'unique_winner' => array(
				'label' => __('Unique Winner', 'lottery-for-woocommerce'),
				'value' => $product->get_lty_lottery_unique_winners(),
			),
			'price_type' => array(
				'label' => __('Price Type', 'lottery-for-woocommerce'),
				'value' => lty_get_lottery_price_type_name($product->get_lty_ticket_price_type()),
			),
		);

		if ( $product->is_unlimited_scheduled_lottery() ) {
			unset( $product_config['end_date'] );
		}

		if ('3' == get_option('lty_settings_guest_user_participate_type')) {
			unset($product_config['mix_ticket_per_user']);
			unset($product_config['max_ticket_per_user']);
		}

		if ('2' !== get_option('lty_settings_quantity_selector_type')) {
			unset($product_config['preset_ticket']);
			unset($product_config['ticket_range_slider_type']);
		}

		if ('1' == $product->get_lty_ticket_price_type()) {
			$product_config ['regular_price'] = array(
				'label' => __('Regular Price', 'lottery-for-woocommerce'),
				'value' => lty_price($product->get_lty_regular_price()),
			);

			if (!empty($product->get_lty_sale_price())) {
				$product_config ['sale_price'] = array(
					'label' => __('Sale Price', 'lottery-for-woocommerce'),
					'value' => lty_price($product->get_lty_sale_price()),
				);
			}
		}

		if ($product->is_manual_ticket()) {
			$product_config ['ticket_generation_type'] = array(
				'label' => __('Ticket generation type', 'lottery-for-woocommerce'),
				'value' => __('User Chooses the Tickets', 'lottery-for-woocommerce'),
			);
			$product_config ['ticket_starting_number'] = array(
				'label' => __('Ticket Starting Number', 'lottery-for-woocommerce'),
				'value' => $product->get_lty_ticket_start_number(),
			);
			$product_config ['ticket_prefix'] = array(
				'label' => __('Ticket Prefix', 'lottery-for-woocommerce'),
				'value' => !empty($product->get_lty_ticket_prefix()) ? $product->get_lty_ticket_prefix() : '-',
			);
			$product_config ['ticket_suffix'] = array(
				'label' => __('Ticket Suffix', 'lottery-for-woocommerce'),
				'value' => !empty($product->get_lty_ticket_suffix()) ? $product->get_lty_ticket_suffix() : '-',
			);

			if ('yes' == $product->get_lty_alphabet_with_sequence_nos_enabled()) {
				$product_config ['alphabet_with_sequence_nos_enabled'] = array(
					'label' => __('Use Alphabet with Sequence Numbers', 'lottery-for-woocommerce'),
					'value' => 'on' === $product->get_lty_alphabet_with_sequence_nos_enabled() ? __('Yes', 'lottery-for-woocommerce') : __('No', 'lottery-for-woocommerce'),
				);
				$product_config ['alphabet_with_sequence_nos_type'] = array(
					'label' => __('Alphabet ticket Numbers Type', 'lottery-for-woocommerce'),
					'value' => '1' == $product->get_lty_alphabet_with_sequence_nos_type() ? __('Alphabet with Numbers', 'lottery-for-woocommerce') : __('Alphabet with Sequence Numbers', 'lottery-for-woocommerce'),
				);
			}

			$product_config ['no_ticket_per_tab'] = array(
				'label' => __('Number of Tickets Per Tab', 'lottery-for-woocommerce'),
				'value' => $product->get_lty_tickets_per_tab(),
			);

			$product_config['lucky_dip'] = array(
				'label' => __( 'Lucky Dip', 'lottery-for-woocommerce' ),
				'value' => $product->get_lty_lucky_dip(),
			);

			$product_config['lucky_dip_method_type'] = array(
				'label' => __( 'Lucky Dip Method Type', 'lottery-for-woocommerce' ),
				'value' => lty_get_lucky_dip_method_type_name( $product ),
			);
		} else {
			$product_config ['ticket_generation_type'] = array(
				'label' => __('Ticket generation type', 'lottery-for-woocommerce'),
				'value' => __('Automatic', 'lottery-for-woocommerce'),
			);
			if ($product->is_automatic_sequential_ticket()) {
				$product_config ['automatic_sequential_ticket'] = array(
					'label' => __('Ticket Number Pattern', 'lottery-for-woocommerce'),
					'value' => __('Sequential', 'lottery-for-woocommerce'),
				);
				$product_config ['ticket_starting_number'] = array(
					'label' => __('Ticket Starting Number', 'lottery-for-woocommerce'),
					'value' => $product->get_automatic_ticket_start_number(),
				);
				$product_config ['ticket_prefix'] = array(
					'label' => __('Ticket Prefix', 'lottery-for-woocommerce'),
					'value' => !empty($product->get_lty_ticket_prefix()) ? $product->get_lty_ticket_prefix() : '-',
				);
				$product_config ['ticket_suffix'] = array(
					'label' => __('Ticket Suffix', 'lottery-for-woocommerce'),
					'value' => !empty($product->get_lty_ticket_suffix()) ? $product->get_lty_ticket_suffix() : '-',
				);
			} elseif ($product->is_automatic_shuffled_ticket()) {
				$product_config ['automatic_shuffled_ticket'] = array(
					'label' => __('Ticket Number Pattern', 'lottery-for-woocommerce'),
					'value' => __('Shuffled', 'lottery-for-woocommerce'),
				);
				$product_config ['ticket_starting_number'] = array(
					'label' => __('Ticket Starting Number', 'lottery-for-woocommerce'),
					'value' => $product->get_automatic_ticket_start_number(),
				);
				$product_config ['ticket_prefix'] = array(
					'label' => __('Ticket Prefix', 'lottery-for-woocommerce'),
					'value' => !empty($product->get_lty_ticket_prefix()) ? $product->get_lty_ticket_prefix() : '-',
				);
				$product_config ['ticket_suffix'] = array(
					'label' => __('Ticket Suffix', 'lottery-for-woocommerce'),
					'value' => !empty($product->get_lty_ticket_suffix()) ? $product->get_lty_ticket_suffix() : '-',
				);
			} else {
				$lottery_ticket_settings = lty_get_lottery_ticket_args();
				$product_config ['automatic_random_ticket'] = array(
					'label' => __('Ticket Number Pattern', 'lottery-for-woocommerce'),
					'value' => __('Random', 'lottery-for-woocommerce'),
				);
				$char_type = ( '1' == $lottery_ticket_settings['character_type'] ) ? __('Only Numbers', 'lottery-for-woocommerce') : __('Alpha Numeric', 'lottery-for-woocommerce');
				$product_config ['ticket_num_type'] = array(
					'label' => __('Ticket Number Type', 'lottery-for-woocommerce'),
					'value' => $char_type,
				);
				$product_config ['ticket_length'] = array(
					'label' => __('Ticket Length', 'lottery-for-woocommerce'),
					'value' => !empty($lottery_ticket_settings['length']) ? $lottery_ticket_settings['length'] : '-',
				);
				$product_config ['ticket_prefix'] = array(
					'label' => __('Ticket Prefix', 'lottery-for-woocommerce'),
					'value' => !empty($lottery_ticket_settings['prefix']) ? $lottery_ticket_settings['prefix'] : '-',
				);
				$product_config ['ticket_suffix'] = array(
					'label' => __('Ticket Suffix', 'lottery-for-woocommerce'),
					'value' => !empty($lottery_ticket_settings['suffix']) ? $lottery_ticket_settings['suffix'] : '-',
				);
			}
		}

		$winner_selection_method = ( '1' == $product->get_lty_winner_selection_method() ) ? __('Automatic', 'lottery-for-woocommerce') : __('Manual', 'lottery-for-woocommerce');
		$gift_product_from = ( '1' == $product->get_winner_product_selection_method() ) ? 'Product from Inside the Site' : 'Product from Outside the Site';
		$gift_products = lty_get_lottery_gift_products($product_id, $product);
		$question_answer_selection_type = '1' == $product->get_question_answer_selection_type() ? __('Product Level', 'lottery-for-woocommerce') : __('Global Level', 'lottery-for-woocommerce');
		$guest_user_participation_type = lty_get_guest_user_participation_label(get_option('lty_settings_guest_user_participate_type'));
		$product_config ['winner_selection_method'] = array(
			'label' => __('Winner Selection Method', 'lottery-for-woocommerce'),
			'value' => $winner_selection_method,
		);
		$product_config ['winner_item_selection_method'] = array(
			'label' => __('Winner Product Selection from', 'lottery-for-woocommerce'),
			'value' => $gift_product_from,
		);
		$product_config ['winner_gift_product'] = array(
			'label' => __('Gift Product', 'lottery-for-woocommerce'),
			'value' => $gift_products,
		);
		$product_config ['question_answer_selection'] = array(
			'label' => __('Question Answer Selection Type', 'lottery-for-woocommerce'),
			'value' => $question_answer_selection_type,
		);
		$product_config ['guest_user_participation_type'] = array(
			'label' => __('Guest User Participation Type', 'lottery-for-woocommerce'),
			'value' => $guest_user_participation_type,
		);

		if ($product->is_instant_winner()) {
			$product_config['instant_winners_prizes'] = array(
				'label' => __('Instant Win Prizes', 'lottery-for-woocommerce'),
				'value' => $product->is_instant_winner() ? __('Yes', 'lottery-for-woocommerce') : __('No', 'lottery-for-woocommerce'),
			);
		}

		if ($product->is_valid_question_answer()) {
			foreach ($product->get_question_answers() as $key => $question) {

				if (!isset($question['answers']) || !lty_check_is_array($question['answers'])) {
					continue;
				}

				foreach ($question['answers'] as $answer) {
					if ('yes' == $answer['valid']) {
						$product_config ['question_answers'][$key] = array(
							'label' => $question['question'],
							'value' => $answer['label'],
						);

						break;
					}
				}
			}

			$product_config ['force_answer'] = array(
				'label' => __('Force Users to Answer the Question', 'lottery-for-woocommerce'),
				'value' => 'yes' === $product->is_force_answer_enabled() ? 'Yes' : 'No',
			);

			if ($product->is_force_answer_enabled() && 'yes' === $product->incorrectly_selected_answer_restriction_is_enabled()) {
				$product_config ['wrong_answer'] = array(
					'label' => __("Don't Generate Ticket Numbers for Incorrectly Answered Question", 'lottery-for-woocommerce'),
					'value' => 'yes' === $product->incorrectly_selected_answer_restriction_is_enabled() ? 'Yes' : 'No',
				);
			}

			if ($product->is_force_answer_enabled() && 'yes' === $product->is_verify_answer_enabled()) {

				$product_config ['validate_answer'] = array(
					'label' => __('Verify Answer Before Purchasing Giveaway', 'lottery-for-woocommerce'),
					'value' => 'yes' === $product->is_verify_answer_enabled() ? 'Yes' : 'No',
				);

				$product_config ['verify_answer_type'] = array(
					'label' => __('Select Verify Answer Type', 'lottery-for-woocommerce'),
					'value' => '1' == $product->verify_question_answer_type() ? __('Limited Attempts', 'lottery-for-woocommerce') : __('Unlimited Attempts', 'lottery-for-woocommerce'),
				);
				if ('1' == $product->verify_question_answer_type()) {
					$product_config ['verify_answer_attempts'] = array(
						'label' => __('Number of Attempts', 'lottery-for-woocommerce'),
						'value' => '' != $product->get_question_answer_attempts() ? $product->get_question_answer_attempts() : '1',
					);
				}
			}
		}

		if (!$product->is_manual_ticket() && $product->is_predefined_button_enabled()) {
			$product_config['predefined_buttons'] = array(
				'label' => __('Buttons Rule', 'lottery-for-woocommerce'),
				'value' => lty_display_predefined_buttons_html($product),
			);
			$product_config['predefined_buttons_label'] = array(
				'label' => __('Predefined Buttons Label', 'lottery-for-woocommerce'),
				'value' => $product->get_predefined_buttons_label(),
			);
			$product_config['predefined_buttons_badge_label'] = array(
				'label' => __('Predefined Buttons Badge Label', 'lottery-for-woocommerce'),
				'value' => $product->get_lty_predefined_buttons_badge_label(),
			);
			$product_config['predefined_with_quantity_selector'] = array(
				'label' => __('Display Quantity Selector(allow user to update quantity)', 'lottery-for-woocommerce'),
				'value' => $product->get_lty_predefined_with_quantity_selector(),
			);
			$product_config['range_slider_predefined_discount_label'] = array(
				'label' => __('Discount info on Range Slider Label', 'lottery-for-woocommerce'),
				'value' => $product->get_lty_range_slider_predefined_discount_label(),
			);
		}

		/**
		 * This hook is used to alter the product configuration data.
		 *
		 * @since 6.7
		 */
		return apply_filters('lty_product_configuration_data', $product_config, $product);
	}

}

if (!function_exists('lty_display_predefined_buttons_html')) {

	/**
	 * Display the predefined buttons HTML.
	 *
	 * @since 1.0.0
	 * @param object $product Product object.
	 * @return string
	 */
	function lty_display_predefined_buttons_html( $product ) {
		ob_start();
		include LTY_ABSPATH . 'inc/admin/menu/views/html-predefined-buttons-configuration-info.php';
		$contents = ob_get_contents();
		ob_end_clean();

		return $contents;
	}

}

if (!function_exists('lty_get_guest_user_participation_options')) {

	/**
	 * Get guest user participation options.
	 *
	 * @return array
	 */
	function lty_get_guest_user_participation_options() {
		/**
		 * This hook is used to alter the guest user participation options.
		 *
		 * @since 1.0
		 */
		return apply_filters(
				'lty_guest_user_participation_options',
				array(
					'1' => __('Force Login on Checkout Page', 'lottery-for-woocommerce'),
					'2' => __('Prevent Guest Participation', 'lottery-for-woocommerce'),
					'3' => __('Allow Guest Participation', 'lottery-for-woocommerce'),
				)
		);
	}

}

if (!function_exists('lty_get_guest_user_participation_label')) {

	/**
	 * Get guest user participation label.
	 *
	 * @return string
	 */
	function lty_get_guest_user_participation_label( $key ) {

		$options = lty_get_guest_user_participation_options();
		return isset($options[$key]) ? $options[$key] : '';
	}

}

if (!function_exists('lty_preview_template')) {

	/**
	 * Preview orders without tickets popup template.
	 *
	 * @return void
	 */
	function lty_preview_template( $template_args ) {

		extract(wp_parse_args($template_args));

		include LTY_PLUGIN_PATH . '/inc/admin/menu/views/backbone-modal/html-backbone-modal.php';
	}

}

if (!function_exists('lty_relative_date_picker_options')) {

	/**
	 * Relative date picker options.
	 *
	 * @return array
	 */
	function lty_relative_date_picker_options( $type = 'full' ) {

		$options = array(
			'seconds' => __('Seconds', 'lottery-for-woocommerce'),
			'minutes' => __('Minutes', 'lottery-for-woocommerce'),
			'hours' => __('Hours', 'lottery-for-woocommerce'),
			'days' => __('Days', 'lottery-for-woocommerce'),
		);

		switch ($type) {
			case '1':
				unset($options['seconds']);
				break;

			case '2':
				unset($options['hours']);
				unset($options['days']);
				break;

			case '3':
				unset($options['seconds']);
				unset($options['days']);
				break;
		}

		return $options;
	}

}

if (!function_exists('lty_get_lottery_action_button_url')) {

	/**
	 * Get the lottery action button URL.
	 *
	 * @return URL
	 */
	function lty_get_lottery_action_button_url( $status, $id, $current_url, $action = false ) {

		$url = '';
		$statuses = lty_get_lottery_statuses();

		if (!array_key_exists($status, $statuses)) {
			return '';
		}

		switch ($status) {
			case 'lty_lottery_started':
				$url = add_query_arg(
						array(
							'action' => 'end_now',
							'id' => $id,
						),
						$current_url
				);
				$button_name = __('End Now', 'lottery-for-woocommerce');
				break;
			case 'lty_lottery_not_started':
				$url = add_query_arg(
						array(
							'action' => 'start_now',
							'id' => $id,
						),
						$current_url
				);
				$button_name = __('Start Now', 'lottery-for-woocommerce');
				break;
		}

		if (empty($url)) {
			return '';
		}

		return sprintf('<a class="lty_lottery_button page-title-action" href="%s">%s</a>', esc_url($url), $button_name);
	}

}

if (!function_exists('lty_parse_relative_date_option')) {

	/**
	 * Parse relative date option.
	 *
	 * @return array.
	 */
	function lty_parse_relative_date_option( $raw_value, $option_type ) {

		$options = lty_relative_date_picker_options($option_type);

		return wp_parse_args(
				(array) $raw_value,
				array(
					'number' => '',
					'unit' => reset($options),
				)
		);
	}

}

if (!function_exists('lty_display_action_status')) {

	/**
	 * Display action status.
	 *
	 * @since 1.0.0
	 * @param string $status Status.
	 * @param int    $id Item ID.
	 * @param string $current_url Current page URL.
	 * @param bool   $action Action.
	 * @return string|URL
	 */
	function lty_display_action_status( $status, $id, $current_url, $action = false ) {
		switch ($status) {
			case 'view':
				$status_name = __('View Details', 'lottery-for-woocommerce');
				break;

			case 'edit_product':
				$status_name = __('Edit', 'lottery-for-woocommerce');
				break;

			case 'view_product':
				$status_name = __('View', 'lottery-for-woocommerce');
				break;

			case 'manual_winner':
				$status_name = __('Select as a Winner', 'lottery-for-woocommerce');
				break;
			
			case 'manual_lottery_notification':
				$status_name = '<span class="dashicons dashicons-email"></span>';
				$title       = __( 'Send Email Manually', 'lottery-for-woocommerce' );
				break;

			default:
				$status_name = __('Delete', 'lottery-for-woocommerce');
				break;
		}

		$section_name = $action ? 'action' : 'section';

		$class = '';
		switch ($status) {
			case 'delete':
				$class = 'lty_delete_data';
				break;

			case 'manual_winner':
				$class = 'lty_manual_winner_data';
				break;
			
			case 'manual_lottery_notification':
				return '<a class="lty-manual-lottery-notification" data-product_id="' . $id . '" href="javascript:;" title="' . $title . '" >' . $status_name . '</a>';
		}

		if ($id) {
			$current_url = add_query_arg(
					array(
						$section_name => $status,
						'id' => $id,
					),
					$current_url
			);
		}
		/* translators: %1s: url, %2s: class, %3s: status name */
		return sprintf('<a href="%1s" class="%2s">%3s</a>', esc_url($current_url), $class, $status_name);
	}

}

if (!function_exists('lty_display_instant_winner_action_status')) {

	/**
	 * Display instant winner action status.
	 *
	 * @since 10.6.0
	 * @param string $status Status.
	 * @param int    $id Item ID.
	 * @param string $current_url Current page URL.
	 * @param bool   $action Action.
	 * @return string|URL
	 */
	function lty_display_instant_winner_action_status( $status, $id, $current_url, $action = false ) {
		switch ($status) {
			case 'remove':
				$status_name = __('Remove Only Winner', 'lottery-for-woocommerce');
				break;

			default:
				$status_name = __('Delete', 'lottery-for-woocommerce');
				break;
		}

		$section_name = 'section';
		if ($action) {
			$section_name = 'instant_winner_action';
		}

		$class = '';
		switch ($status) {
			case 'delete':
				$class = 'lty_delete_instant_winner_data';
				break;

			case 'remove':
				$class = 'lty_remove_instant_winner_data';
				break;
		}

		if ($id) {
			$current_url = add_query_arg(
					array(
						$section_name => $status,
						'instant_winner_id' => $id,
					),
					$current_url
			);
		}
		
		/* translators: %1s: url, %2s: class, %3s: status name */
		return '<a href="' . esc_url($current_url) . '" class="' . $class . '" >' . $status_name . '</a>' ;
	}

}

if (!function_exists('lty_render_lottery_list_table')) {

	/**
	 * Render the lottery list table.
	 *
	 * @return void.
	 */
	function lty_render_lottery_list_table() {

		if (!class_exists('LTY_Lottery_List_Table')) {
			require_once LTY_PLUGIN_PATH . '/inc/admin/menu/wp-list-table/class-lty-lottery-list-table.php';
		}

		$post_table = new LTY_Lottery_List_Table();
		$post_table->render();
	}

}

if (!function_exists('lty_render_ticket_list_table')) {

	/**
	 * Render the ticket list table.
	 *
	 * @return void.
	 */
	function lty_render_ticket_list_table() {

		if (!class_exists('LTY_Lottery_Ticket_List_Table')) {
			require_once LTY_PLUGIN_PATH . '/inc/admin/menu/wp-list-table/class-lty-lottery-ticket-list-table.php';
		}

		$post_table = new LTY_Lottery_Ticket_List_Table();
		$post_table->render();
	}

}

if (!function_exists('lty_products_array_filter_readable')) {

	/**
	 * Products array filter readable.
	 */
	function lty_products_array_filter_readable( $product ) {
		return $product && is_a($product, 'WC_Product') && current_user_can('read_product', $product->get_id());
	}

}

if (!function_exists('lty_get_items_per_page')) {

	/**
	 * Get items per page.
	 *
	 * @since 3.2.0
	 * @param string $key
	 * @return int
	 */
	function lty_get_items_per_page( $key ) {
		if (!$key) {
			return;
		}

		$value = (int) get_user_option('lty_' . $key . '_per_page');

		return ( empty($value) || $value < 1 ) ? 10 : $value;
	}

}

if (!function_exists('lty_parse_relative_image_size_option')) {

	/**
	 * Parse relative image size option.
	 *
	 * @since 8.5.0
	 * @param array $raw_value Image size.
	 * @return array
	 */
	function lty_parse_relative_image_size_option( $raw_value ) {
		return wp_parse_args(
				(array) $raw_value,
				array(
					'width' => '',
					'height' => '',
				)
		);
	}

}

if ( ! function_exists( 'lty_render_lottery_instant_winners_rules' ) ) {
	/**
	 * Render lottery instant winners rules.
	 *
	 * @since 9.6.0
	 * @param object $product Product object.
	 * @param int    $current_page Current page.
	 * @return void
	 */
	function lty_render_lottery_instant_winners_rules( $product, $current_page = 1 ) {
		/**
		 * This hook is used to alter the lottery instant winners rules per page.
		 *
		 * @since 9.6.0
		 */
		$items_per_page = apply_filters( 'lty_lottery_instant_winners_rules_per_page', 20 );

		$instant_winner_rules_count = is_callable( array( $product, 'get_instant_winners_rules_count' ) ) ? $product->get_instant_winners_rules_count() : 0;
		$page_count                 = ceil( $instant_winner_rules_count / $items_per_page ) ? intval( ceil( $instant_winner_rules_count / $items_per_page ) ) : 1;
		$offset                     = $current_page > 1 ? ( $current_page - 1 ) * $items_per_page : 0;
		$total_instant_winner_ids   = lty_get_instant_winner_rule_ids( $product->get_id() );
		$total_instant_winner_count = lty_check_is_array( $total_instant_winner_ids ) ? count( $total_instant_winner_ids ) : 0;
		$instant_winner_ids         = array_slice( $total_instant_winner_ids, $offset, $items_per_page );
		$prize_group_options        = lty_get_instant_winner_prize_group_options( $product->get_id() );
		$prize_group_options_count  = lty_check_is_array( $prize_group_options ) ? count( $prize_group_options ) : 0;

		require LTY_ABSPATH . 'inc/admin/menu/views/html-product-instant-winner-rules.php';
	}

}

if ( ! function_exists( 'lty_render_instant_winner_prize_groups' ) ) {
	/**
	 * Render lottery instant winner prize groups.
	 *
	 * @since 11.1.0
	 * @param object $product Product object.
	 * @param int    $current_page Current page.
	 * @return void
	 */
	function lty_render_instant_winner_prize_groups( $product, $current_page = 1 ) {
		/**
		 * This hook is used to alter the lottery instant winner prize groups per page.
		 *
		 * @since 11.1.0
		 */
		$items_per_page = apply_filters( 'lty_instant_winner_prize_groups_per_page', 20 );

		$all_prize_group_ids         = lty_get_instant_winner_prize_group_ids( $product->get_id() );
		$total_prize_group_ids_count = lty_check_is_array( $all_prize_group_ids ) ? count( $all_prize_group_ids ) : 0;
		$offset                      = ( $items_per_page * $current_page ) - $items_per_page;
		$page_count                  = intval( ceil( $total_prize_group_ids_count / $items_per_page ) );
		$prize_group_ids             = array_slice( $all_prize_group_ids, $offset, $items_per_page );

		require LTY_ABSPATH . 'inc/admin/menu/views/html-instant-winner-prize-groups.php';
	}
}

if (!function_exists('lty_get_lottery_ticket_search_columns')) {

	/**
	 * Get the lottery ticket search columns.
	 *
	 * @since 9.8.0
	 * @static array $search_columns
	 * @return array
	 */
	function lty_get_lottery_ticket_search_columns() {
		static $search_columns;
		if ($search_columns) {
			return $search_columns;
		}

		$search_columns = array(
			'lty_user_name' => __('User Name', 'lottery-for-woocommerce'),
			'lty_user_email' => __('User Email', 'lottery-for-woocommerce'),
			'lty_ticket_number' => __('Ticket Number', 'lottery-for-woocommerce'),
			'lty_order_id' => __('Order ID', 'lottery-for-woocommerce'),
		);

		/**
		 * This hook is used to alter the lottery ticket search columns.
		 *
		 * @since 9.8.0
		 */
		return apply_filters('lty_lottery_ticket_search_columns', $search_columns);
	}
}

if (!function_exists('lty_get_instant_winners_search_columns')) {

	/**
	 * Get the lottery instant winners search columns.
	 *
	 * @since 9.8.0
	 * @static array $search_columns
	 * @return array
	 */
	function lty_get_instant_winners_search_columns() {
		static $search_columns;
		if ($search_columns) {
			return $search_columns;
		}

		$search_columns = array(
			'lty_ticket_number' => __('Ticket Number', 'lottery-for-woocommerce'),
			'lty_instant_winner_prize' => __('Winning Prize', 'lottery-for-woocommerce'),
			'lty_user_name' => __('Winner Name', 'lottery-for-woocommerce'),
			'lty_user_email' => __('User Email', 'lottery-for-woocommerce'),
			'lty_order_id' => __('Order ID', 'lottery-for-woocommerce'),
		);

		/**
		 * This hook is used to alter the lottery search columns.
		 *
		 * @since 9.8.0
		 */
		return apply_filters('lty_instant_winners_search_columns', $search_columns);
	}
}

if (!function_exists('lty_get_lottery_ticket_logs_search_options')) {

	/**
	 * Get the lottery ticket logs search by options.
	 *
	 * @since 10.2.0
	 * @static array $search_options
	 * @return array
	 */
	function lty_get_lottery_ticket_logs_search_options() {
		static $search_options;
		if ($search_options) {
			return $search_options;
		}

		$search_options = array(
			'lty_user_name' => __('User Name', 'lottery-for-woocommerce'),
			'lty_ticket_number' => __('Ticket Number', 'lottery-for-woocommerce'),
		);

		/**
		 * This hook is used to alter the lottery ticket logs search by options.
		 *
		 * @since 10.2.0
		 */
		return apply_filters('lty_lottery_ticket_logs_search_options', $search_options);
	}
}

if ( ! function_exists( 'lty_get_formatted_lottery_filters' ) ) {

	/**
	 * Get formatted lottery filter values.
	 *
	 * @since 10.2.0
	 * @param array $selected_filter_values Selected lottery filter values.
	 * @return array
	 */
	function lty_get_formatted_lottery_filters( $selected_filter_values ) {
		$selected_filter_values = wp_parse_args(
			$selected_filter_values,
			array(
				'ticket_generation_type' => false,
				'winner_selection_type'  => false,
			)
		);

		/**
		 * This hook is used to alter the formatted lottery filter values.
		 *
		 * @since 10.2.0
		 */
		return apply_filters( 'lty_formatted_lottery_filters', $selected_filter_values );
	}
}

if ( ! function_exists( 'lty_get_lottery_ticket_generation_type_filter_options' ) ) {

	/**
	 * Get the lottery ticket generation type filter options.
	 *
	 * @since 10.2.0
	 * @static array $filter_options Ticket generation type filter options.
	 * @return array
	 */
	function lty_get_lottery_ticket_generation_type_filter_options() {
		static $filter_options;
		if ( $filter_options ) {
			return $filter_options;
		}

		$filter_options = array(
			''  => __( 'All', 'lottery-for-woocommerce' ),
			'1' => __( 'Automatic', 'lottery-for-woocommerce' ),
			'2' => __( 'User Chooses the Ticket', 'lottery-for-woocommerce' ),
		);

		/**
		 * This hook is used to alter the lottery ticket generation type filter options.
		 *
		 * @since 10.2.0
		 */
		return apply_filters( 'lty_lottery_ticket_generation_type_filter_options', $filter_options );
	}
}

if ( ! function_exists( 'lty_get_lottery_winner_selection_type_filter_options' ) ) {

	/**
	 * Get the lottery winner selection type filter options.
	 *
	 * @since 10.2.0
	 * @static array $filter_options Winner selection type filter options.
	 * @return array
	 */
	function lty_get_lottery_winner_selection_type_filter_options() {
		static $filter_options;
		if ( $filter_options ) {
			return $filter_options;
		}

		$filter_options = array(
			''  => __( 'All', 'lottery-for-woocommerce' ),
			'1' => __( 'Automatic', 'lottery-for-woocommerce' ),
			'2' => __( 'Manual', 'lottery-for-woocommerce' ),
		);

		/**
		 * This hook is used to alter the lottery winner selection type filter options.
		 *
		 * @since 10.2.0
		 */
		return apply_filters( 'lty_lottery_winner_selection_type_filter_options', $filter_options );
	}
}

if ( ! function_exists( 'lty_get_formatted_lottery_ticket_filters' ) ) {

	/**
	 * Get formatted lottery ticket filter values.
	 *
	 * @since 10.2.0
	 * @param array $selected_filter_values Selected lottery ticket filter values.
	 * @return array
	 */
	function lty_get_formatted_lottery_ticket_filters( $selected_filter_values ) {
		$selected_filter_values = wp_parse_args(
			$selected_filter_values,
			array(
				'search_columns'      => array_keys( lty_get_lottery_ticket_search_columns() ),
				'search_columns_type' => '1',
				'user_type'           => false,
				'purchased_date_filter_type' => false,
				'purchased_from_date' => false,
				'purchased_to_date'   => false,
			)
		);

		/**
		 * This hook is used to alter the formatted lottery ticket filter values.
		 *
		 * @since 10.2.0
		 */
		return apply_filters( 'lty_formatted_lottery_ticket_filters', $selected_filter_values );
	}
}

if ( ! function_exists( 'lty_get_lottery_tickets_purchased_date_filter_options' ) ) {

	/**
	 * Get the lottery tickets purchased date filter options.
	 *
	 * @since 10.2.0
	 * @return array
	 */
	function lty_get_lottery_tickets_purchased_date_filter_options() {
		/**
		 * This hook is used to alter the lottery tickets purchased date filter options.
		 *
		 * @since 10.2.0
		 */
		return apply_filters(
			'lty_lottery_tickets_purchased_date_filter_options',
			array(
				''  => __( 'All Days', 'lottery-for-woocommerce' ),
				'1' => __( 'Today', 'lottery-for-woocommerce' ),
				'6' => __( 'Yesterday', 'lottery-for-woocommerce' ),
				'7' => __( 'Last One Week', 'lottery-for-woocommerce' ),
				'8' => __( 'Last One Month', 'lottery-for-woocommerce' ),
				'5' => __( 'Specific Date Range', 'lottery-for-woocommerce' ),
			)
		);
	}
}

if ( ! function_exists( 'lty_get_date_filter_values' ) ) {

	/**
	 * Get the date filter values.
	 *
	 * @since 10.2.0
	 * @param string $day_filter Day filter.
	 * @param string $selected_from_date Selected from date.
	 * @param string $selected_to_date Selected to date.
	 * @return array
	 */
	function lty_get_date_filter_values( $day_filter, $selected_from_date, $selected_to_date ) {
		$values = array(
			'from_date' => false,
			'to_date'   => false,
			'from_time' => false,
			'to_time'   => false,
		);

		switch ( $day_filter ) {
			case '8': // Last one month.
				$date_object         = LTY_Date_Time::get_date_time_object( 'now' );
				$values['to_date']   = $date_object->format( 'Y-m-d 23:59:59' );
				$values['from_date'] = $date_object->modify( '-1months' )->format( 'Y-m-d 00:00:00' );
				break;

			case '7': // Last one week.
				$date_object         = LTY_Date_Time::get_date_time_object( 'now' );
				$values['to_date']   = $date_object->format( 'Y-m-d 23:59:59' );
				$values['from_date'] = $date_object->modify( '-1weeks' )->format( 'Y-m-d 00:00:00' );
				break;

			case '6': // Yesterday.
				$date_object         = LTY_Date_Time::get_date_time_object( 'now' )->modify( '-1days' );
				$values['from_date'] = $date_object->format( 'Y-m-d 00:00:00' );
				$values['to_date']   = $date_object->format( 'Y-m-d 23:59:59' );
				break;

			case '5': // Specific date range.
				if ( $selected_from_date ) {
					$from_date_object    = LTY_Date_Time::get_date_time_object( $selected_from_date );
					$values['from_date'] = $from_date_object->format( 'Y-m-d 00:00:00' );
					$values['from_time'] = ( $from_date_object->format( 'G' ) ) ? $from_date_object->format( 'H:i' ) : false;
				}

				if ( $selected_to_date ) {
					$to_date_object    = LTY_Date_Time::get_date_time_object( $selected_to_date );
					$values['to_date'] = $to_date_object->format( 'Y-m-d 23:59:59' );
					$values['to_time'] = ( $to_date_object->format( 'G' ) ) ? $to_date_object->format( 'H:i' ) : false;
				}

				break;

			case '1': // Today.
				$date_object         = LTY_Date_Time::get_date_time_object( 'now' );
				$values['from_date'] = $date_object->format( 'Y-m-d 00:00:00' );
				$values['to_date']   = $date_object->format( 'Y-m-d 23:59:59' );
				break;
		}

		return $values;
	}
}

if ( ! function_exists( 'lty_get_formatted_instant_winners_filters' ) ) {

	/**
	 * Get formatted lottery instant winners filter values.
	 *
	 * @since 10.2.0
	 * @param array $selected_filter_values Selected lottery instant winners filter values.
	 * @return array
	 */
	function lty_get_formatted_instant_winners_filters( $selected_filter_values ) {
		$selected_filter_values = wp_parse_args(
			$selected_filter_values,
			array(
				'search_columns'      => array_keys( lty_get_instant_winners_search_columns() ),
				'search_columns_type' => '1',
				'user_type'           => false,
				'purchased_date_filter_type' => false,
				'purchased_from_date' => false,
				'purchased_to_date'   => false,
			)
		);

		/**
		 * This hook is used to alter the formatted lottery instant winners filter values.
		 *
		 * @since 10.2.0
		 */
		return apply_filters( 'lty_formatted_instant_winners_filters', $selected_filter_values );
	}
}

if ( ! function_exists( 'lty_remove_lottery_instant_winner' ) ) {

	/**
	 * Remove lottery instant winner.
	 *
	 * @since 10.6.0
	 * @param string $instant_winner_log_id
	 * @return array
	 */
	function lty_remove_lottery_instant_winner( $instant_winner_log_id ) {
		if (!$instant_winner_log_id) {
			return;
		}

		$instant_winner_log = lty_get_instant_winner_log($instant_winner_log_id);
		if (!$instant_winner_log->exists()) {
			return;
		}
		
		$instant_winner_log->remove_instant_winner();           
	}
}

if ( ! function_exists( 'lty_get_lucky_dip_method_type_options' ) ) {

	/**
	 * Get the lucky dip method type options.
	 *
	 * @since 10.4.0
	 * @static array $options Lucky dip method options.
	 * @return array
	 */
	function lty_get_lucky_dip_method_type_options() {
		static $options;
		if ( $options ) {
			return $options;
		}

		/**
		 * This hook is used to alter the lucky dip method options.
		 *
		 * @since 10.4.0
		 */
		$options = apply_filters(
			'lty_lucky_dip_method_options',
			array(
				'1' => __( 'Display & Add the Tickets to the Cart Directly', 'lottery-for-woocommerce' ),
				'2' => __( 'Only Display the Tickets', 'lottery-for-woocommerce' ),
			)
		);

		return $options;
	}
}

if ( ! function_exists( 'lty_get_lucky_dip_method_type_name' ) ) {

	/**
	 * Get the lucky dip method type name.
	 *
	 * @since 10.4.0
	 * @param object $product Product object.
	 * @return array
	 */
	function lty_get_lucky_dip_method_type_name( $product ) {
		if ( ! lty_is_lottery_product( $product ) ) {
			return '';
		}

		$lucky_dip_method_options = lty_get_lucky_dip_method_type_options();

		return isset( $lucky_dip_method_options[ $product->get_lty_lucky_dip_method_type() ] ) ? $lucky_dip_method_options[ $product->get_lty_lucky_dip_method_type() ] : '';
	}
}

if ( ! function_exists( 'lty_get_instant_winner_prize_type_options' ) ) {

	/**
	 * Get the instant winner prize type options.
	 *
	 * @since 10.6.0
	 * @return array
	 */
	function lty_get_instant_winner_prize_type_options() {
		/**
		 * This hook is used to alter the instant winner prize type options.
		 *
		 * @since 10.6.0
		 */
		return apply_filters(
			'lty_instant_winner_prize_type_options',
			array(
				'physical' => __( 'Physical(Manual)', 'lottery-for-woocommerce' ),
				'coupon'   => __( 'Coupon', 'lottery-for-woocommerce' ),
				'product'  => __( 'Product', 'lottery-for-woocommerce' ),
			)
		);
	}
}

if ( ! function_exists( 'lty_get_instant_winner_coupon_generation_type_options' ) ) {

	/**
	 * Get the instant winner coupon generation type options.
	 *
	 * @since 10.6.0
	 * @return array
	 */
	function lty_get_instant_winner_coupon_generation_type_options() {
		/**
		 * This hook is used to alter the instant winner prize type options.
		 *
		 * @since 10.6.0
		 */
		return apply_filters(
			'lty_instant_winner_coupon_generation_type_options',
			array(
				'1' => __( 'New Coupon', 'lottery-for-woocommerce' ),
				'2' => __( 'Existing Coupon', 'lottery-for-woocommerce' ),
			)
		);
	}
}

if ( ! function_exists( 'lty_get_instant_winner_coupon_discount_type_options' ) ) {

	/**
	 * Get the instant winner coupon discount type options.
	 *
	 * @since 10.6.0
	 * @return array
	 */
	function lty_get_instant_winner_coupon_discount_type_options() {
		/**
		 * This hook is used to alter the instant winner coupon discount type options.
		 *
		 * @since 10.6.0
		 */
		return apply_filters(
			'lty_instant_winner_coupon_discount_type_options',
			array(
				'percent'    => __( 'Percentage discount', 'lottery-for-woocommerce' ),
				'fixed_cart' => __( 'Fixed cart discount', 'lottery-for-woocommerce' ),
			)
		);
	}
}

if ( ! function_exists( 'lty_get_wc_categories' ) ) {

	/**
	 * Get the WC categories.
	 *
	 * @since 10.6.0
	 * @static array $lty_categories WC categories.
	 * @return array
	 */
	function lty_get_wc_categories() {
		static $lty_categories;
		if ( isset( $lty_categories ) ) {
			return $lty_categories;
		}

		$lty_categories = array();
		$wc_categories  = get_terms( 'product_cat' );
		if ( ! lty_check_is_array( $wc_categories ) ) {
			return $lty_categories;
		}

		foreach ( $wc_categories as $category ) {
			$lty_categories[ $category->term_id ] = $category->name;
		}

		return $lty_categories;
	}
}

if ( ! function_exists( 'lty_get_wc_available_gateways' ) ) {

	/**
	 * WC available gateways.
	 *
	 * @since 10.7.0
	 * @static array $available_gateways Available gateways.
	 * @return array
	 * */
	function lty_get_wc_available_gateways() {
		static $available_gateways;
		if ( isset( $available_gateways ) ) {
			return $available_gateways;
		}

		$wc_gateways = WC()->payment_gateways->payment_gateways();
		if ( ! lty_check_is_array( $wc_gateways ) ) {
			return array();
		}

		$available_gateways = array();
		foreach ( $wc_gateways as $gateway ) {
			$available_gateways[ $gateway->id ] = $gateway->title;
		}

		return $available_gateways;
	}
}

if ( ! function_exists( 'lty_get_manual_lottery_notification_options' ) ) {
	/**
	 * Get the manual lottery notification options.
	 *
	 * @since 12.4.0
	 * @param WC_Product_Lottery $product Product object.
	 * @return array
	 */
	function lty_get_manual_lottery_notification_options( $product ) {
		$notification_ids = array();
		switch ( $product->get_lty_lottery_status() ) {
			case 'lty_lottery_started':
				$notification_ids = array(
					'customer_lottery_started',
					'customer_lottery_extended',
					'customer_unlimited_scheduled_lottery_extended',
				);
				break;
			case 'lty_lottery_closed':
				$notification_ids = array(
					'customer_lottery_ended',
				);
				break;
			case 'lty_lottery_finished':
				$notification_ids = array(
					'customer_winner',
					'customer_no_luck',
					'customer_lottery_ended',
				);
				break;
			case 'lty_lottery_failed':
				$notification_ids = array(
					'customer_lottery_failed',
					'customer_lottery_ended',
				);
				break;
		}

		$notifications = LTY_Notification_Instances::get_notifications();

		$notification_options = array();
		foreach ( $notification_ids as $notification_id ) {
			if ( ! isset( $notifications[ $notification_id ] ) ) {
				continue;
			}

			$notification_options[ $notification_id ] = $notifications[ $notification_id ]->get_title();
		}

		/**
		 * This hook is used to alter the manual lottery notification options.
		 *
		 * @since 12.4.0
		 */
		return apply_filters( 'lty_manual_lottery_notification_options', $notification_options, $product );
	}
}
