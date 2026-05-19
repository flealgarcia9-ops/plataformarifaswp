<?php

/**
 * Admin Ajax
 * */
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}
if (!class_exists('LTY_Admin_Ajax')) {

	/**
	 * Class.
	 * */
	class LTY_Admin_Ajax {

		/**
		 * Class initialization.
		 * */
		public static function init() {

			$actions = array(
				'json_search_products_and_variations'      => false,
				'json_search_products'                     => false,
				'json_search_customers'                    => false,
				'json_search_coupons'                      => false,
				'lottery_manual_relist'                    => false,
				'lottery_extend'                           => false,
				'ticket_tab_selection'                     => true,
				'process_lucky_dip'                        => true,
				'process_regenerate_lucky_dip'             => true,
				'regenerate_lucky_dip_add_to_cart'         => true,
				'generate_automatic_ticket_edit_order'     => false,
				'manual_ticket_popup'                      => false,
				'get_automatic_ticket_popup_html'          => false,
				'question_answer_popup_content'            => false,
				'ticket_tab_selection_edit_order'          => false,
				'generate_manual_ticket_edit_order'        => false,
				'generate_order_item_product_answer'       => false,
				'manual_ticket_search_action'              => true,
				'orders_without_tickets_popup'             => false,
				'order_status_action'                      => false,
				'pagination_action'                        => true,
				'search_ticket_logs'                       => true,
				'add_instant_winner_rule'                  => false,
				'save_instant_winners_rules'               => false,
				'remove_instant_winner_rule'               => false,
				'instant_winners_rules_pagination_content' => false,
				'create_instant_winner_prize_group'        => false,
				'save_instant_winner_prize_groups'         => false,
				'remove_instant_winner_prize_group'        => false,
				'instant_winner_prize_groups_pagination_action' => false,
				'fetch_export_popup_content'               => false,
				'run_export'                               => false,
				'fetch_import_popup_content'               => false,
				'upload_import_form'                       => false,
				'run_import'                               => false,
				'get_instant_winner_prize_group_tickets_html' => false,
				'manual_lottery_notification_popup_content' => false,
				'manual_lottery_notification'              => false,
			);

			foreach ($actions as $action => $nopriv) {
				// For user support.
				add_action('wp_ajax_lty_' . $action, array( __CLASS__, $action ));

				if ($nopriv) {
					// For guest support.
					add_action('wp_ajax_nopriv_lty_' . $action, array( __CLASS__, $action ));
				}
			}
		}

		/**
		 * Search for products.
		 * */
		public static function json_search_products( $term = '', $include_variations = false ) {
			check_ajax_referer('search-products', 'lty_security');

			try {

				if (empty($term) && isset($_GET['term'])) {
					$term = isset($_GET['term']) ? wc_clean(wp_unslash($_GET['term'])) : '';
				}

				if (empty($term)) {
					throw new exception(__('No Products found', 'lottery-for-woocommerce'));
				}

				if (!empty($_GET['limit'])) {
					$limit = absint($_GET['limit']);
				} else {
					/**
					 * This hook is used to alter the WooCommerce JSON search limit.
					 *
					 * @since 1.0
					 */
					$limit = absint(apply_filters('woocommerce_json_search_limit', 30));
				}

				$data_store = WC_Data_Store::load('product');
				$ids = $data_store->search_products($term, '', (bool) $include_variations, false, $limit);

				$product = wc_get_product($ids);
				$product_objects = array_filter(array_map('wc_get_product', $ids), 'lty_products_array_filter_readable');
				$products = array();

				$exclude_global_variable = isset($_GET['exclude_global_variable']) ? wc_clean(wp_unslash($_GET['exclude_global_variable'])) : 'no'; // @codingStandardsIgnoreLine.

				$exclude_product_type = isset($_GET['exclude_product_type']) ? wc_clean(wp_unslash($_GET['exclude_product_type'])) : 'no'; // @codingStandardsIgnoreLine.

				$exclude_out_of_stock = isset($_GET['exclude_out_of_stock']) ? wc_clean(wp_unslash($_GET['exclude_out_of_stock'])) : 'no';
				$include_lottery_statuses = isset( $_GET['include_lottery_statuses'] ) ? wc_clean( wp_unslash( $_GET['include_lottery_statuses'] ) ) : '';
				$include_lottery_statuses = ! empty( $include_lottery_statuses ) ? explode( ',', $include_lottery_statuses ) : array();
				foreach ($product_objects as $product_object) {
					if ('yes' === $exclude_out_of_stock && !$product_object->is_in_stock()) {
						continue;
					}

					if ( lty_is_lottery_product( $product_object ) && lty_check_is_array( $include_lottery_statuses ) && ! in_array( $product_object->get_lty_lottery_status(), $include_lottery_statuses, true ) ) {
						continue;
					}

					if ('yes' === $exclude_global_variable && $product_object->is_type('variable')) {
						continue;
					}

					if ('no' !== $exclude_product_type && $product_object->get_type() == $exclude_product_type) {
						continue;
					}

					$products[$product_object->get_id()] = rawurldecode($product_object->get_formatted_name());
				}

				/**
				 * This hook is used to alter the WooCommerce JSON search found products.
				 *
				 * @since 1.0
				 */
				wp_send_json(apply_filters('woocommerce_json_search_found_products', $products));
			} catch (Exception $ex) {
				wp_die();
			}
		}

		/**
		 * Search for product variations.
		 * */
		public static function json_search_products_and_variations( $term = '', $include_variations = false ) {
			self::json_search_products('', true);
		}

		/**
		 * Customers search.
		 * */
		public static function json_search_customers() {
			check_ajax_referer('lty-search-nonce', 'lty_security');

			try {
				$term = isset($_GET['term']) ? wc_clean(wp_unslash($_GET['term'])) : ''; // @codingStandardsIgnoreLine.

				if (empty($term)) {
					throw new exception(esc_html__('No Customer found', 'lottery-for-woocommerce'));
				}

				$exclude = isset($_GET['exclude']) ? wc_clean(wp_unslash($_GET['exclude'])) : ''; // @codingStandardsIgnoreLine.
				$exclude = !empty($exclude) ? array_map('intval', explode(',', $exclude)) : array();

				$found_customers = array();
				$customers_query = new WP_User_Query(
						array(
					'fields' => 'all',
					'orderby' => 'display_name',
					'search' => '*' . $term . '*',
					'search_columns' => array( 'ID', 'user_login', 'user_email', 'user_nicename' ),
						)
				);
				$customers = $customers_query->get_results();

				if (lty_check_is_array($customers)) {
					foreach ($customers as $customer) {
						if (!in_array($customer->ID, $exclude)) {
							$found_customers[$customer->ID] = $customer->display_name . ' (#' . $customer->ID . ' &ndash; ' . sanitize_email($customer->user_email) . ')';
						}
					}
				}

				wp_send_json($found_customers);
			} catch (Exception $ex) {
				wp_die();
			}
		}

		/**
		 * Search coupons.
		 *
		 * @since 10.6.0
		 * @throws exception
		 */
		public static function json_search_coupons() {
			check_ajax_referer( 'lty-search-nonce', 'lty_security' );

			try {
				$term = isset( $_GET['term'] ) ? wc_clean( wp_unslash( $_GET['term'] ) ) : '';
				if ( empty( $term ) ) {
					throw new exception( __( 'No coupon found', 'lottery-for-woocommerce' ) );
				}

				global $wpdb;
				$like = '%' . $wpdb->esc_like( $term ) . '%';

				$search_results = array_filter(
					$wpdb->get_results(
						$wpdb->prepare(
							"SELECT DISTINCT ID as id, post_title as name FROM {$wpdb->posts}
							WHERE post_type='shop_coupon' AND post_status IN('publish')
                       		AND (post_title LIKE %s) ORDER BY post_title ASC",
							$like
						),
						ARRAY_A
					),
					'lty_array_filter'
				);

				$found_coupons = array();
				if ( lty_check_is_array( $search_results ) ) {
					foreach ( $search_results as $search_result ) {
						/* translators: %1$s - Coupon code , %2$s - Coupon ID */
						$found_coupons[ $search_result['id'] ] = sprintf( __( '%1$s (ID: %2$s)', 'lottery-for-woocommerce' ), $search_result['name'], $search_result['id'] );
					}
				}

				wp_send_json( $found_coupons );
			} catch ( Exception $ex ) {
				wp_die();
			}
		}

		/**
		 * Manually relist the lottery product.
		 * */
		public static function lottery_manual_relist() {
			check_ajax_referer('lty-manual-relist-nonce', 'lty_security');

			try {
				if (!isset($_POST)) {
					throw new exception(esc_html__('Invalid Request', 'lottery-for-woocommerce'));
				}

				if (!isset($_POST['product_id']) || empty(absint($_POST['product_id']))) { // @codingStandardsIgnoreLine.
					throw new exception(esc_html__('Invalid Product ID', 'lottery-for-woocommerce'));
				}

				// Return if current user not have permission.
				if (!current_user_can('edit_posts')) {
					throw new exception(esc_html__("You don't have permission to do this action", 'lottery-for-woocommerce'));
				}

				// Sanitize post values.
				$product_id = !empty($_POST['product_id']) ? absint($_POST['product_id']) : 0; // @codingStandardsIgnoreLine.
				// Manually relist the product.
				LTY_Lottery_Product_Type_Handler::manual_relist($product_id);

				wp_send_json_success();
			} catch (Exception $ex) {
				wp_send_json_error(array( 'error' => $ex->getMessage() ));
			}
		}

		/**
		 * Manually extend the lottery product.
		 * */
		public static function lottery_extend() {

			check_ajax_referer('lty-extend-nonce', 'lty_security');

			try {
				if (!isset($_POST)) {
					throw new exception(esc_html__('Invalid Request', 'lottery-for-woocommerce'));
				}

				if (!isset($_POST['product_id']) || empty(absint($_POST['product_id']))) { // @codingStandardsIgnoreLine.
					throw new exception(__('Invalid Product ID', 'lottery-for-woocommerce'));
				}

				// Return if current user not have permission.
				if (!current_user_can('edit_posts')) {
					throw new exception(esc_html__("You don't have permission to do this action", 'lottery-for-woocommerce'));
				}

				// Sanitize post values.
				$product_id = !empty($_POST['product_id']) ? absint($_POST['product_id']) : 0; // @codingStandardsIgnoreLine.

				$product = wc_get_product($product_id);
				if (!is_object($product)) {
					throw new exception(esc_html__('Invalid Product', 'lottery-for-woocommerce'));
				}

				if ('lottery' !== $product->get_type()) {
					throw new exception(esc_html__('Invalid Product Type', 'lottery-for-woocommerce'));
				}

				if (!$product->is_closed() || ( $product->has_lottery_status('lty_lottery_finished') )) {
					throw new exception(esc_html__('Invalid Request', 'lottery-for-woocommerce'));
				}

				// Extend the lottery product.
				LTY_Lottery_Handler::extend_lottery($product_id, $product);

				wp_send_json_success();
			} catch (Exception $ex) {
				wp_send_json_error(array( 'error' => $ex->getMessage() ));
			}
		}

		/**
		 * Ticket tab selection.
		 * */
		public static function ticket_tab_selection() {
			check_ajax_referer('lty-lottery-tickets', 'lty_security');

			try {
				if (!isset($_POST['product_id']) || empty(absint($_POST['product_id']))) {
					throw new exception(esc_html__('Invalid Credentials', 'lottery-for-woocommerce'));
				}

				if (!isset($_POST['tab']) || '' === $_POST['tab']) {
					throw new exception(esc_html__('Invalid Credentials', 'lottery-for-woocommerce'));
				}

				$product = wc_get_product(absint($_POST['product_id']));

				if (!is_object($product)) {
					throw new exception(esc_html__('Invalid Credentials', 'lottery-for-woocommerce'));
				}

				$start_range = absint($_POST['tab']);
				$end_range = '1' == $product->get_alphabet_sequence_type() ? $product->get_ticket_start_number() + $product->get_lty_tickets_per_tab() - 1 : $start_range + $product->get_lty_tickets_per_tab() - 1;
				$maximum_tickets = $product->get_ticket_start_number() + $product->get_lty_maximum_tickets() - 1;
				$end_range = $end_range > $maximum_tickets ? $maximum_tickets : $end_range;
				$start_range = '1' == $product->get_alphabet_sequence_type() ? $product->get_ticket_start_number() : $start_range;
				// Prepare ticket numbers based on start range and end range.
				$ticket_numbers = range($start_range, $end_range);
				// Shuffle ticket numbers.
				if ('2' === $product->get_lty_tickets_per_tab_display_type()) {
					shuffle($ticket_numbers);
				}

				if ($product->is_sold_all_tickets($ticket_numbers)) {
					$html = sprintf('<span class="lty-all-tickets-sold">%s</span>', wp_kses_post(lty_get_user_chooses_ticket_all_tickets_sold_label()));
				} else {
					$ticket_args = array(
						'product' => $product,
						'sold_tickets' => $product->get_placed_tickets(),
						'cart_tickets' => $product->get_cart_tickets(),
						'reserved_tickets' => $product->get_reserved_tickets(),
						'index' => isset($_POST['index']) ? absint($_POST['index']) : '',
						'ticket_numbers' => $ticket_numbers,
						'view_more' => ( 'yes' === $product->get_lty_view_more_tickets_per_tab() ) ? $product->get_lty_tickets_per_tab_view_more_count() : false,
					);

					$html = lty_get_template_html('single-product/ticket-tab-content.php', $ticket_args);
				}

				$ticket_numbers = isset($_POST['ticket_numbers']) ? explode(',', wc_clean(wp_unslash($_POST['ticket_numbers']))) : array();

				wp_send_json_success(
						array(
							'html' => $html,
							'ticket_numbers' => $ticket_numbers,
						)
				);
			} catch (Exception $ex) {
				wp_send_json_error(array( 'error' => $ex->getMessage() ));
			}
		}

		/**
		 * Processes the lucky dip.
		 *
		 * @return void
		 * */
		public static function process_lucky_dip() {
			check_ajax_referer('lty-lottery-tickets', 'lty_security');

			try {
				if (!isset($_POST['product_id']) || empty(absint($_POST['product_id']))) {
					throw new exception(esc_html__('Invalid Product', 'lottery-for-woocommerce'));
				}

				if (!isset($_POST['qty']) || empty(absint($_POST['qty']))) {
					throw new exception(esc_html__('Invalid Quantity', 'lottery-for-woocommerce'));
				}

				$product_id = absint($_POST['product_id']);
				$product = wc_get_product($product_id);

				if (!is_object($product)) {
					throw new exception(esc_html__('Invalid Product', 'lottery-for-woocommerce'));
				}

				if ('3' != get_option('lty_settings_guest_user_participate_type') && empty($product->get_remaining_purchase_limit_per_user())) {
					throw new exception(esc_html__('You have reached your maximum ticket count for this giveaway.', 'lottery-for-woocommerce'));
				}

				$qty = absint($_POST['qty']);

				if ('3' != get_option('lty_settings_guest_user_participate_type') && $qty < $product->get_min_purchase_quantity()) {
					/* translators: %1$s: Product Title %2$s: User Limit %3$s: Quantity */
					throw new exception(sprintf(__('The minimum allowed quantity for %1$s is %2$d . So you cannot add %3$d to your cart.', 'lottery-for-woocommerce'), $product->get_title(), $product->get_min_purchase_quantity(), $qty));
				}

				if ('3' != get_option('lty_settings_guest_user_participate_type') && $qty > $product->get_remaining_purchase_limit_per_user()) {
					/* translators: %s: remaining tickets */
					throw new exception(sprintf(__('You can add only %s more ticket(s) to the cart.', 'lottery-for-woocommerce'), abs($product->get_remaining_purchase_limit_per_user())));
				}

				$tickets = lty_get_random_user_chooses_ticket_numbers_by_quantity( $product, $qty );
				if (empty($tickets)) {
					throw new exception(esc_html__('You cannot purchase tickets for this giveaway anymore.', 'lottery-for-woocommerce'));
				}

				// Error message displayed when incorrect answer is selected.
				if (!empty($_POST['answer']) && !LTY_Lottery_Cart::may_be_add_and_validate_answer_add_to_cart($product, absint($_POST['answer']))) {
					self::throw_question_answer_message($product);
				}

				$cart_item_data = array(
					'lty_lottery' => array(
						'tickets' => $tickets,
					),
				);

				// Prepare the answer.
				$cart_item_data = self::prepare_answer($product, $cart_item_data);

				// Add the lottery product in the cart.
				WC()->cart->add_to_cart($product_id, $qty, 0, array(), $cart_item_data);

				// Add to cart message.
				wc_add_to_cart_message(array( $product_id => $qty ), true);

				wp_send_json_success( array( 'html' => lty_get_template_html( 'single-product/ticket-lucky-dip-popup.php', array( 'ticket_numbers' => $tickets, 'quantity' => $qty ) ) ) );
			} catch (Exception $ex) {
				wp_send_json_error(array( 'error' => $ex->getMessage() ));
			}
		}

		/**
		 * Processes the regenerate lucky dip.
		 *
		 * @since 10.4.0
		 * @throws exception
		 * */
		public static function process_regenerate_lucky_dip() {
			check_ajax_referer( 'lty-lottery-tickets', 'lty_security' );

			try {
				$product_id = isset( $_POST['product_id'] ) ?  absint( $_POST['product_id'] ) : '';
				if ( ! $product_id ) {
					throw new exception( esc_html__( 'Invalid Product', 'lottery-for-woocommerce' ) );
				}

				$quantity = isset( $_POST['quantity'] ) ? absint( $_POST['quantity'] ) : '';
				if ( ! $quantity ) {
					throw new exception( esc_html__( 'Invalid Quantity', 'lottery-for-woocommerce' ) );
				}

				$product = wc_get_product( $product_id );
				if ( ! lty_is_lottery_product( $product ) ) {
					throw new exception( esc_html__( 'Invalid Product', 'lottery-for-woocommerce' ) );
				}

				if ( '3' != get_option( 'lty_settings_guest_user_participate_type' ) && empty( $product->get_remaining_purchase_limit_per_user() ) ) {
					throw new exception( esc_html__( 'You have reached your maximum ticket count for this giveaway.', 'lottery-for-woocommerce' ) );
				}

				if ( '3' != get_option( 'lty_settings_guest_user_participate_type' ) && $quantity < $product->get_min_purchase_quantity() ) {
					/* translators: %1$s: Product Title %2$s: User Limit %3$s: Quantity */
					throw new exception( sprintf( __( 'The minimum allowed quantity for %1$s is %2$d . So you cannot add %3$d to your cart.', 'lottery-for-woocommerce' ), $product->get_title(), $product->get_min_purchase_quantity(), $quantity ) );
				}

				if ( '3' != get_option( 'lty_settings_guest_user_participate_type' ) && $quantity > $product->get_remaining_purchase_limit_per_user() ) {
					/* translators: %s: remaining tickets */
					throw new exception( sprintf( __( 'You can add only %s more ticket(s) to the cart.', 'lottery-for-woocommerce' ), abs( $product->get_remaining_purchase_limit_per_user() ) ) );
				}

				$tickets = lty_get_random_user_chooses_ticket_numbers_by_quantity( $product, $quantity );
				if ( empty( $tickets ) ) {
					throw new exception( esc_html__( 'You cannot purchase tickets for this giveaway anymore.', 'lottery-for-woocommerce' ) );
				}

				$quantity_args                = lty_get_lucky_dip_quantity_input_arguments( $product );
				$quantity_args['input_value'] = $quantity;
				$quantity_args['readonly'] = ( isset( $_POST['fixed_quantity'] ) && 'yes' === wc_clean( wp_unslash( $_POST['fixed_quantity'] ) ) ) ? true : false;

				wp_send_json_success(
					array(
						'html' => lty_get_template_html(
							'single-product/regenerate-lucky-dip-popup.php',
							array(
								'product'        => $product,
								'ticket_numbers' => $tickets,
								'quantity_args'  => $quantity_args,
								'action'         => 'regenerate',
							)
						),
					)
				);
			} catch ( Exception $ex ) {
				wp_send_json_error( array( 'error' => $ex->getMessage() ) );
			}
		}

		/**
		 * Processes the regenerate lucky dip add to cart.
		 *
		 * @since 10.4.0
		 * @throws exception
		 * */
		public static function regenerate_lucky_dip_add_to_cart() {
			check_ajax_referer( 'lty-lottery-tickets', 'lty_security' );

			try {
				$product_id = isset( $_POST['product_id'] ) ? absint( $_POST['product_id'] ) : '';
				if ( ! $product_id ) {
					throw new exception( esc_html__( 'Invalid Product', 'lottery-for-woocommerce' ) );
				}

				$quantity = isset( $_POST['quantity'] ) ? absint( $_POST['quantity'] ) : '';
				if ( ! $quantity ) {
					throw new exception( esc_html__( 'Invalid Quantity', 'lottery-for-woocommerce' ) );
				}

				$tickets = isset( $_POST['tickets'] ) ? explode( ',', wc_clean( wp_unslash( $_POST['tickets'] ) ) ) : '';
				if ( ! lty_check_is_array( $tickets ) ) {
					throw new exception( esc_html__( 'Invalid Tickets', 'lottery-for-woocommerce' ) );
				}

				$product = wc_get_product( $product_id );
				if ( ! lty_is_lottery_product( $product ) ) {
					throw new exception( esc_html__( 'Invalid Product', 'lottery-for-woocommerce' ) );
				}

				if ('3' != get_option( 'lty_settings_guest_user_participate_type' ) && empty( $product->get_remaining_purchase_limit_per_user() ) ) {
					throw new exception( esc_html__( 'You have reached your maximum ticket count for this giveaway.', 'lottery-for-woocommerce' ) );
				}

				if ('3' != get_option( 'lty_settings_guest_user_participate_type' ) && $quantity < $product->get_min_purchase_quantity() ) {
					/* translators: %1$s: Product Title %2$s: User Limit %3$s: Quantity */
					throw new exception( sprintf( __( 'The minimum allowed quantity for %1$s is %2$d . So you cannot add %3$d to your cart.', 'lottery-for-woocommerce' ), $product->get_title(), $product->get_min_purchase_quantity(), $quantity ) );
				}

				if ('3' != get_option( 'lty_settings_guest_user_participate_type' ) && $quantity > $product->get_remaining_purchase_limit_per_user() ) {
					/* translators: %s: remaining tickets */
					throw new exception( sprintf( __('You can add only %s more ticket(s) to the cart.', 'lottery-for-woocommerce' ), abs( $product->get_remaining_purchase_limit_per_user() ) ) );
				}

				// Error message displayed when incorrect answer is selected.
				if ( ! empty( $_POST['answer'] ) && ! LTY_Lottery_Cart::may_be_add_and_validate_answer_add_to_cart( $product, absint( $_POST['answer'] ) ) ) {
					self::throw_question_answer_message( $product );
				}

				// Add the generated tickets to the cart.
				$cart_item_data = array(
					'lty_lottery' => array(
						'tickets' => $tickets,
					),
				);

				// Prepare the answer.
				$cart_item_data = self::prepare_answer( $product, $cart_item_data );

				// Add the lottery product in the cart.
				WC()->cart->add_to_cart( $product_id, $quantity, 0, array(), $cart_item_data );

				wp_send_json_success(
					array(
						'html' => lty_get_template_html(
							'single-product/regenerate-lucky-dip-popup.php',
							array(
								'product'        => $product,
								'ticket_numbers' => $tickets,
								'quantity_args'  => lty_get_lucky_dip_quantity_input_arguments( $product ),
								'action'         => 'add_to_cart',
							)
						),
					)
				);
			} catch ( Exception $ex ) {
				wp_send_json_error( array( 'error' => $ex->getMessage() ));
			}
		}

		/**
		 * Throw question answer message.
		 *
		 * @return void
		 * */
		public static function throw_question_answer_message( $product ) {

			if ('1' == $product->verify_question_answer_type()) {

				$customer_id = lty_get_current_user_cart_session_value();
				$attempts_count = absint(wc_get_product($product->get_id())->get_question_answer_remaining_attempts($customer_id));
				if ($attempts_count) {
					// Display question answer remaining attempts alert message.
					throw new exception(wp_kses_post(str_replace('{attempts}', $attempts_count, get_option('lty_settings_limited_type_multiple_attempts_error_message', 'Incorrect answer. {attempts} attempt(s) left.'))));
				} else {
					// Display incorrect answer alert message.
					throw new exception(esc_html__('You have selected an incorrect answer, hence you cannot participate in the giveaway.', 'lottery-for-woocommerce'));
				}
			} else {
				// Display correct answer alert message.
				throw new exception(wp_kses_post(get_option('lty_settings_unlimited_type_error_message', 'Incorrect answer. Please select the correct answer to participate in the lottery')));
			}
		}

		/**
		 * Prepare the answer.
		 *
		 * @return array
		 * */
		public static function prepare_answer( $product, $cart_item_data ) {
			$answer = isset($_REQUEST['answer']) ? wc_clean(wp_unslash($_REQUEST['answer'])) : ''; // @codingStandardsIgnoreLine.

			if (!empty($answer) && $product->is_valid_question_answer()) {
				$answers = $product->get_answers();

				if (array_key_exists($answer, $answers)) {
					$cart_item_data['lty_lottery']['answers'] = $answer;
				}
			}

			return $cart_item_data;
		}

		/**
		 * Generate automatic ticket in edit order page.
		 *
		 * @return void
		 * */
		public static function generate_automatic_ticket_edit_order() {     
			check_ajax_referer('lty-automatic-ticket-nonce', 'lty_security'); 
			try {  
				$mode = isset($_POST['mode']) ? absint($_POST['mode']) : 1;
				self::generate_edit_order_automatic_ticket($mode);
			} catch (Exception $ex) {
				wp_send_json_error(array( 'error' => $ex->getMessage() ));
			}
		}

		/**
		 * Generate ticket in edit order page.
		 *
		 * @since 11.1.0
		 * @return void
		 * */
		public static function generate_edit_order_automatic_ticket( $mode ) {
			// Validate lottery tickets on edit order.
			self::validate_lottery_tickets_edit_order();

			$order_id = isset($_REQUEST['order_id']) ? absint($_REQUEST['order_id']) : '';
			$order = wc_get_order($order_id);
			if ( ! is_object( $order ) ) {
				throw new exception( esc_html__( 'Invalid Order', 'lottery-for-woocommerce' ) );
			}
				
			$item = isset($_REQUEST['item_id']) ? $order->get_item(absint($_REQUEST['item_id'])) : '';
			if (!is_object($item)) {
				throw new exception(esc_html__('Invalid item', 'lottery-for-woocommerce'));
			}
				
			// Ticket number processing.
			if (2 === $mode) { 
				// Manual mode
				$ticket_numbers = isset($_REQUEST['ticket_numbers']) ? wc_clean(wp_unslash($_REQUEST['ticket_numbers'])) : '';
				if ('' === $ticket_numbers) {
					throw new Exception(esc_html__('Select any Ticket Number', 'lottery-for-woocommerce'));
				}
				$ticket_numbers = lty_check_is_array($ticket_numbers) ? $ticket_numbers : explode(',', $ticket_numbers);
				$purchased_ticket_numbers = $item->get_product()->get_purchased_tickets();
				$common_tickets = array_intersect($ticket_numbers, $purchased_ticket_numbers);

				if (!empty($common_tickets)) {
					$ticket_list = implode(', ', $common_tickets); 
					throw new Exception(esc_html__('The following ticket number(s) have already been purchased: ') . esc_html($ticket_list));
				}
			} else { 
				// Automatic mode
				$ticket_numbers = $item->get_meta( '_lty_lottery_tickets' );
				if (lty_check_is_array($ticket_numbers)) {
					throw new Exception(esc_html__('Generated ticket numbers are already assigned. Please assign the ticket number again.', 'lottery-for-woocommerce'));
				}
			}

			// Add ticket numbers to order item meta
			if ( $ticket_numbers ) {
				wc_add_order_item_meta($item->get_id(), '_lty_lottery_tickets', $ticket_numbers);
				wc_add_order_item_meta($item->get_id(), lty_get_order_item_ticket_number_name(), '<span class="notranslate">' . implode(', ', $ticket_numbers) . '</span>');
			}
				// Process answer (if available)
				$answer_id = isset($_REQUEST['answer_id']) ? wc_clean(wp_unslash($_REQUEST['answer_id'])) : '';
			if ($answer_id) {
				$answers = $item->get_product()->get_answers();
				if (array_key_exists($answer_id, $answers)) {
					wc_add_order_item_meta($item->get_id(), '_lty_lottery_answers', array( $answer_id ));
					wc_add_order_item_meta($item->get_id(), __('Chosen Answer', 'lottery-for-woocommerce'), $answers[$answer_id]['label']);
				}
			}
		
			// Delete order meta's.
			$order->delete_meta_data('lty_lottery_ticket_created_once');
			$order->delete_meta_data('lty_lottery_ticket_updated_once');
			// Add the note.
			$note = ( 2 === $mode ) ? sprintf(__('Giveaway ticket generated in manual', 'lottery-for-woocommerce')) : sprintf(__('Giveaway ticket generated in automatic', 'lottery-for-woocommerce'));
			$order->add_order_note($note);
			$order->save();
		
			// Update lottery ticket in order
			LTY_Order_Handler::update_lottery_ticket_in_order($order_id, $order, $item->get_product_id(), true);
	
			wp_send_json_success();
		}

		/**
		 * Get automatic ticket popup html.
		 *
		 * @since 11.1.0
		 * @return void
		 * */
		public static function get_automatic_ticket_popup_html() {

			check_ajax_referer('lty-automatic-ticket-nonce', 'lty_security');

			try {
				// Return if post is invalid.
				if (!isset($_POST)) {
					throw new exception(__('Invalid Request', 'lottery-for-woocommerce'));
				}

				// Return if current user not have permission.
				if (!current_user_can('edit_posts')) {
					throw new exception(__("You don't have permission to do this action", 'lottery-for-woocommerce'));
				}

				// Return if order id is invalid.
				if (!isset($_POST['order_id']) || empty(absint($_POST['order_id']))) { // @codingStandardsIgnoreLine.
					throw new exception(__('Invalid Order ID', 'lottery-for-woocommerce'));
				}

				$order_id = isset($_POST['order_id']) ? absint($_POST['order_id']) : '';
				$order = wc_get_order($order_id);
				// Return if invalid order.
				if (!is_object($order)) {
					throw new exception(__('Invalid Order', 'lottery-for-woocommerce'));
				}

				// Return if item id is invalid.
				if (!isset($_POST['item_id']) || empty(absint($_POST['item_id']))) { // @codingStandardsIgnoreLine.
					throw new exception(__('Invalid Product Item', 'lottery-for-woocommerce'));
				}

				// Return if invalid order item.
				$item_id = isset($_POST['item_id']) ? absint($_POST['item_id']) : '';
				$item = $order->get_item($item_id);
				if (!is_object($item)) {
					throw new exception(__('Invalid Order Item', 'lottery-for-woocommerce'));
				}

				$product = wc_get_product($item->get_product_id());
				if (!lty_is_lottery_product($product) || $product->is_closed()) {
					throw new exception(__('Invalid Product', 'lottery-for-woocommerce'));
				}

				ob_start();
				include LTY_ABSPATH . 'inc/admin/menu/views/order-item/automatic-ticket-summary.php';
				$html = ob_get_contents();
				ob_end_clean();

				wp_send_json_success(array( 'html' => $html ));
			} catch (Exception $ex) {
				wp_send_json_error(array( 'error' => $ex->getMessage() ));
			}
		}

		/**
		 * Manual ticket popup.
		 *
		 * @return void
		 * */
		public static function manual_ticket_popup() {

			check_ajax_referer('lty-manual-ticket-nonce', 'lty_security');

			try {
				// Return if post is invalid.
				if (!isset($_POST)) {
					throw new exception(__('Invalid Request', 'lottery-for-woocommerce'));
				}

				// Return if current user not have permission.
				if (!current_user_can('edit_posts')) {
					throw new exception(__("You don't have permission to do this action", 'lottery-for-woocommerce'));
				}

				// Return if order id is invalid.
				if (!isset($_POST['order_id']) || empty(absint($_POST['order_id']))) { // @codingStandardsIgnoreLine.
					throw new exception(__('Invalid Order ID', 'lottery-for-woocommerce'));
				}

				$order_id = isset($_POST['order_id']) ? absint($_POST['order_id']) : '';
				$order = wc_get_order($order_id);
				// Return if invalid order.
				if (!is_object($order)) {
					throw new exception(__('Invalid Order', 'lottery-for-woocommerce'));
				}

				// Return if item id is invalid.
				if (!isset($_POST['item_id']) || empty(absint($_POST['item_id']))) { // @codingStandardsIgnoreLine.
					throw new exception(__('Invalid Product Item', 'lottery-for-woocommerce'));
				}

				// Return if invalid order item.
				$item_id = isset($_POST['item_id']) ? absint($_POST['item_id']) : '';
				$item = $order->get_item($item_id);
				if (!is_object($item)) {
					throw new exception(__('Invalid Item', 'lottery-for-woocommerce'));
				}

				$product = wc_get_product($item->get_product_id());
				if (!lty_is_lottery_product($product) || $product->is_closed()) {
					throw new exception(__('Invalid Product', 'lottery-for-woocommerce'));
				}

				ob_start();
				include LTY_ABSPATH . 'inc/admin/menu/views/order-item/manual-ticket-summary.php';
				$html = ob_get_contents();
				ob_end_clean();

				wp_send_json_success(array( 'html' => $html ));
			} catch (Exception $ex) {
				wp_send_json_error(array( 'error' => $ex->getMessage() ));
			}
		}

		/**
		 * Prepare the question answer popup content
		 *
		 * @since 7.4
		 * */
		public static function question_answer_popup_content() {
			check_ajax_referer('lty-manual-ticket-nonce', 'lty_security');

			try {
				// Throw the error when the order ID is not valid.
				if (!isset($_POST['order_id']) || empty(absint($_POST['order_id']))) {
					throw new exception(__('Invalid Order ID', 'lottery-for-woocommerce'));
				}

				// Throw the error when the order is not valid.
				$order = wc_get_order(absint($_POST['order_id']));
				if (!is_object($order)) {
					throw new exception(__('Invalid order', 'lottery-for-woocommerce'));
				}

				// Throw the error when the item ID is not valid.
				if (!isset($_POST['item_id']) || empty(absint($_POST['item_id']))) {
					throw new exception(__('Invalid item ID', 'lottery-for-woocommerce'));
				}

				// Throw the error when the item is not valid.
				$item = $order->get_item(absint($_POST['item_id']));
				if (!is_object($item)) {
					throw new exception(__('Invalid item', 'lottery-for-woocommerce'));
				}

				// Throw the error when the product is not valid or lottery already closed.
				$product = wc_get_product($item->get_product_id());
				if (!lty_is_lottery_product($product) || $product->is_closed()) {
					throw new exception(__('Invalid product', 'lottery-for-woocommerce'));
				}

				// Throw the error when the question answer is not valid for the lottery product.
				if (!$product->is_valid_question_answer()) {
					throw new exception(__('Invalid request', 'lottery-for-woocommerce'));
				}

				// Throw the error when the current user don't have permission to edit posts.
				if (!current_user_can('edit_posts')) {
					throw new exception(__("You don't have permission to do this action", 'lottery-for-woocommerce'));
				}

				$question_answers = $product->get_question_answers();
				$question = reset($question_answers);

				ob_start();
				include LTY_ABSPATH . 'inc/admin/menu/views/order-item/question-answer.php';
				$html = ob_get_contents();
				ob_end_clean();

				wp_send_json_success(
						array(
							'html' => $html,
							'button_class_name' => 'lty-generate-answer',
						)
				);
			} catch (Exception $ex) {
				wp_send_json_error(array( 'error' => $ex->getMessage() ));
			}
		}

		/**
		 * Ticket tab selection edit order page.
		 *
		 * @return void
		 * */
		public static function ticket_tab_selection_edit_order() {

			check_ajax_referer('lty-manual-ticket-nonce', 'lty_security');

			try {
				// Return if post is invalid.
				if (!isset($_POST)) {
					throw new exception(__('Invalid Request', 'lottery-for-woocommerce'));
				}

				// Return if current user not have permission.
				if (!current_user_can('edit_posts')) {
					throw new exception(__("You don't have permission to do this action", 'lottery-for-woocommerce'));
				}

				if (!isset($_POST['product_id']) || empty(absint($_POST['product_id']))) {
					throw new exception(__('Invalid Product', 'lottery-for-woocommerce'));
				}

				if (!isset($_POST['tab']) || '' === $_POST['tab']) {
					throw new exception(__('Invalid Data', 'lottery-for-woocommerce'));
				}

				$product = wc_get_product(absint($_POST['product_id']));
				if (!is_object($product)) {
					throw new exception(__('Invalid Product', 'lottery-for-woocommerce'));
				}

				$start_range = absint($_POST['tab']);
				$end_range = '1' == $product->get_alphabet_sequence_type() ? $product->get_ticket_start_number() + $product->get_lty_tickets_per_tab() - 1 : $start_range + $product->get_lty_tickets_per_tab() - 1;
				$maximum_tickets = $product->get_ticket_start_number() + $product->get_lty_maximum_tickets() - 1;
				$end_range = $end_range > $maximum_tickets ? $maximum_tickets : $end_range;
				$start_range = '1' == $product->get_alphabet_sequence_type() ? $product->get_ticket_start_number() : $start_range;
				// Prepare ticket numbers based on start range and end range.
				$ticket_numbers = range($start_range, $end_range);
				// Shuffle ticket numbers.
				if ('2' === $product->get_lty_tickets_per_tab_display_type()) {
					shuffle($ticket_numbers);
				}

				if ($product->is_sold_all_tickets($ticket_numbers)) {
					$html = sprintf('<span class="lty-all-tickets-sold">%s</span>', wp_kses_post(lty_get_user_chooses_ticket_all_tickets_sold_label()));
				} else {
					$ticket_args = array(
						'product' => $product,
						'sold_tickets' => $product->get_placed_tickets(),
						'cart_tickets' => array(),
						'reserved_tickets' => $product->get_reserved_tickets(),
						'index' => isset($_POST['index']) ? absint($_POST['index']) : '',
						'ticket_numbers' => $ticket_numbers,
						'view_more' => ( 'yes' === $product->get_lty_view_more_tickets_per_tab() ) ? $product->get_lty_tickets_per_tab_view_more_count() : false,
					);

					$html = lty_get_template_html('single-product/ticket-tab-content.php', $ticket_args);
				}
				$ticket_numbers = isset($_POST['ticket_numbers']) ? explode(',', wc_clean(wp_unslash($_POST['ticket_numbers']))) : array();

				wp_send_json_success(
						array(
							'html' => $html,
							'ticket_numbers' => $ticket_numbers,
						)
				);
			} catch (Exception $ex) {
				wp_send_json_error(array( 'error' => $ex->getMessage() ));
			}
		}

		/**
		 * Generate manual ticket edit order.
		 *
		 * @return void
		 * */
		public static function generate_manual_ticket_edit_order() {
			check_ajax_referer('lty-manual-ticket-nonce', 'lty_security');

			try {

				// Validate lottery ticket on edit order.
				self::validate_lottery_tickets_edit_order();

				$ticket_numbers = isset($_POST['ticket_numbers']) ? wc_clean(wp_unslash($_POST['ticket_numbers'])) : '';
				$answer_id = isset($_POST['answer_id']) ? wc_clean(wp_unslash($_POST['answer_id'])) : '';
				// Return if ticket number is not selected.
				if ('' === $ticket_numbers) {
					throw new exception(__('Select any Ticket Number', 'lottery-for-woocommerce'));
				}

				$ticket_numbers = is_array($ticket_numbers) ? $ticket_numbers : explode(',', $ticket_numbers);
				$order_id = isset($_POST['order_id']) ? absint($_POST['order_id']) : '';
				$order = wc_get_order($order_id);
				$item = isset($_POST['item_id']) ? $order->get_item(absint($_POST['item_id'])) : '';

				// Update order item meta.
				wc_add_order_item_meta($item->get_id(), '_lty_lottery_tickets', $ticket_numbers);
				wc_add_order_item_meta($item->get_id(), lty_get_order_item_ticket_number_name(), '<span class="notranslate">' . implode(', ', $ticket_numbers) . '</span>');

				// Set the answer.
				if ($answer_id) {
					$product = wc_get_product($item->get_product_id());
					$answers = $product->get_answers();
					if (array_key_exists($answer_id, $answers)) {
						// Update the order item meta.
						wc_add_order_item_meta($item->get_id(), '_lty_lottery_answers', array( $answer_id ));
						wc_add_order_item_meta($item->get_id(), __('Chosen Answer', 'lottery-for-woocommerce'), $answers[$answer_id]['label']);
					}
				}

				// Delete metas.
				$order->delete_meta_data('lty_lottery_ticket_created_once');
				$order->delete_meta_data('lty_lottery_ticket_updated_once');
				$order->save();

				// Update lottery ticket in order.
				LTY_Order_Handler::update_lottery_ticket_in_order($order_id, $order, $item->get_product_id());

				wp_send_json_success();
			} catch (Exception $ex) {
				wp_send_json_error(array( 'error' => $ex->getMessage() ));
			}
		}

		/**
		 * Validate lottery tickets on edit order page.
		 *
		 * @return void
		 * */
		public static function validate_lottery_tickets_edit_order() {

			// Return if request is invalid.
			$request = $_REQUEST;
			if (!isset($request)) {
				throw new exception(esc_html__('Invalid Request', 'lottery-for-woocommerce'));
			}

			// Return if current user not have permission.
			if (!current_user_can('edit_posts')) {
				throw new exception(esc_html__("You don't have permission to do this action", 'lottery-for-woocommerce'));
			}

			// Return if order id is invalid.
			if (!isset($request['order_id']) || empty(absint($request['order_id']))) { // @codingStandardsIgnoreLine.
				throw new exception(esc_html__('Invalid Order ID', 'lottery-for-woocommerce'));
			}

			$order_id = absint($request['order_id']);
			$order = wc_get_order($order_id);
			// Return if invalid order.
			if (!is_object($order)) {
				throw new exception(esc_html__('Invalid Order', 'lottery-for-woocommerce'));
			}

			// Return if item id is invalid.
			if (!isset($request['item_id']) || empty(absint($request['item_id']))) { // @codingStandardsIgnoreLine.
				throw new exception(esc_html__('Invalid Product Item', 'lottery-for-woocommerce'));
			}

			// Return if invalid order item.
			$item = $order->get_item(absint($request['item_id']));
			if (!is_object($item)) {
				throw new exception(esc_html__('Invalid Item', 'lottery-for-woocommerce'));
			}

			$product = wc_get_product($item->get_product_id());
			// Return if product is not a lottery.
			if (!lty_is_lottery_product($product) || $product->is_closed()) {
				throw new exception(esc_html__('Invalid Product', 'lottery-for-woocommerce'));
			}

			$quantity = $item->get_quantity();
			$mode = isset($request['mode']) ? absint($request['mode']) : 1;
			// Return if quantity not matching for manual ticket selection.
			if ($product->is_manual_ticket() || 2 === $mode) {
				if (empty($request['quantity']) || absint($request['quantity']) != $quantity) {
					if (1 > $quantity) {
						/* translators: %d: quantity */
						throw new exception(sprintf(esc_html__('Please select %d tickets', 'lottery-for-woocommerce'), absint($item->get_quantity())));
					} else {
						/* translators: %d: quantity */
						throw new exception(sprintf(esc_html__('Please select %d ticket', 'lottery-for-woocommerce'), absint($item->get_quantity())));
					}
				}
			}
			// Throw an error if the answer is not selected.
			if ($product->is_valid_question_answer() && !$product->is_force_answer_enabled() && ( !isset($request['answer_id']) || empty($request['answer_id']) )) {
				throw new exception(esc_html__('Please select a answer', 'lottery-for-woocommerce'));
			}
		}

		/**
		 * Generate the order item answer for the lottery product.
		 *
		 * @since 7.3
		 * @throws exception
		 */
		public static function generate_order_item_product_answer() {
			check_ajax_referer('lty-manual-ticket-nonce', 'lty_security');

			try {

				$answer_id = isset($_POST['answer_id']) ? wc_clean(wp_unslash($_POST['answer_id'])) : '';
				// Return if ticket number is not selected.
				if (!$answer_id) {
					throw new exception(esc_html__('Select a answer', 'lottery-for-woocommerce'));
				}

				// Throw the error when the order ID is not valid.
				if (!isset($_POST['order_id']) || empty(absint($_POST['order_id']))) {
					throw new exception(esc_html__('Invalid Order ID', 'lottery-for-woocommerce'));
				}

				// Throw the error when the order is not valid.
				$order = wc_get_order(absint($_POST['order_id']));
				if (!is_object($order)) {
					throw new exception(esc_html__('Invalid order', 'lottery-for-woocommerce'));
				}

				// Throw the error when the item ID is not valid.
				if (!isset($_POST['item_id']) || empty(absint($_POST['item_id']))) {
					throw new exception(esc_html__('Invalid item ID', 'lottery-for-woocommerce'));
				}

				// Throw the error when the item is not valid.
				$item = $order->get_item(absint($_POST['item_id']));
				if (!is_object($item)) {
					throw new exception(esc_html__('Invalid item', 'lottery-for-woocommerce'));
				}

				// Throw the error when the product is not valid or lottery already closed.
				$product = wc_get_product($item->get_product_id());
				if (!lty_is_lottery_product($product) || $product->is_closed()) {
					throw new exception(esc_html__('Invalid product', 'lottery-for-woocommerce'));
				}

				// Throw the error when the question answer is not valid for the lottery product.
				if (!$product->is_valid_question_answer() || !$item['lty_lottery_tickets']) {
					throw new exception(esc_html__('Invalid request', 'lottery-for-woocommerce'));
				}

				// Throw the error when the selected answer is not valid for the product.
				$question_answers = $product->get_answers();
				if (!array_key_exists($answer_id, $question_answers)) {
					throw new exception(esc_html__('Invalid answer', 'lottery-for-woocommerce'));
				}

				// Throw the error when the current user don't have permission to edit posts.
				if (!current_user_can('edit_posts')) {
					throw new exception(esc_html__("You don't have permission to do this action", 'lottery-for-woocommerce'));
				}

				$answers = array(
					'label' => $question_answers[$answer_id]['label'],
					'key' => $answer_id,
					'valid' => $question_answers[$answer_id]['valid'],
				);

				foreach ($item['lty_lottery_tickets'] as $ticket_number) {
					$ticket_id = lty_get_ticket_id_by_ticket_number($ticket_number);
					if (!$ticket_id) {
						continue;
					}

					update_post_meta($ticket_id, 'lty_answer', $question_answers[$answer_id]['label']);
					update_post_meta($ticket_id, 'lty_answers', $answers);
					update_post_meta($ticket_id, 'lty_valid_answer', $question_answers[$answer_id]['valid']);
				}

				// Update the order item meta.
				wc_add_order_item_meta($item->get_id(), '_lty_lottery_answers', array( $answer_id ));
				wc_add_order_item_meta($item->get_id(), __('Chosen Answer', 'lottery-for-woocommerce'), $question_answers[$answer_id]['label']);

				wp_send_json_success(array( 'msg' => __('Answer generated successfully', 'lottery-for-woocommerce') ));
			} catch (Exception $ex) {
				wp_send_json_error(array( 'error' => $ex->getMessage() ));
			}
		}

		/**
		 * Manual ticket search action.
		 *
		 * @return void
		 * */
		public static function manual_ticket_search_action() {

			check_ajax_referer('lty-lottery-manual-ticket-search-action-nonce', 'lty_security');

			try {
				if (!isset($_POST['product_id']) || empty(absint($_POST['product_id']))) {
					throw new exception(__('Invalid Product', 'lottery-for-woocommerce'));
				}

				$product = wc_get_product(absint($_POST['product_id']));
				if (!lty_is_lottery_product($product)) {
					throw new exception(__('Invalid Product', 'lottery-for-woocommerce'));
				}

				if (!$product->has_lottery_status('lty_lottery_started') || !$product->is_manual_ticket() || $product->user_purchase_limit_exists()) {
					throw new exception(__('Invalid Data', 'lottery-for-woocommerce'));
				}

				if (!isset($_POST['searched_value']) || empty($_POST['searched_value'])) {
					throw new exception(__('Invalid Search', 'lottery-for-woocommerce'));
				}

				$searched_value = wc_clean(wp_unslash($_POST['searched_value']));

				$searched_value = str_replace(array( $product->get_lty_ticket_prefix(), $product->get_lty_ticket_suffix() ), array( '', '' ), $searched_value);
				if (!is_numeric($searched_value)) {
					throw new exception(wp_kses_post(get_option('lty_settings_user_chooses_ticket_search_error', 'Ticket(s) not found')));
				}

				$searched_value = absint($searched_value);
				if ($searched_value < $product->get_ticket_start_number()) {
					throw new exception(wp_kses_post(get_option('lty_settings_user_chooses_ticket_search_error', 'Ticket(s) not found')));
				}

				$maximum_tickets = $product->get_ticket_start_number() + $product->get_lty_maximum_tickets() - 1;
				if ($searched_value > $maximum_tickets) {
					throw new exception(__('Please enter less than maximum tickets', 'lottery-for-woocommerce'));
				}

				$searched_length = strlen($searched_value);
				$matched_ticket_numbers = array();
				if (1 == $searched_length) {
					$end_range = $searched_value + 1000;
					$end_range = $end_range > $maximum_tickets ? $maximum_tickets : $end_range;
					$lottery_tickets = range($searched_value, $end_range);
					if (!lty_check_is_array($lottery_tickets)) {
						throw new exception(wp_kses_post(get_option('lty_settings_user_chooses_ticket_search_error', 'Ticket(s) not found')));
					}

					$splitted_strings = str_split($searched_value);
					foreach ($splitted_strings as $splitted_string) {
						foreach ($lottery_tickets as $ticket) {
							if (false !== strpos($ticket, $splitted_string)) {
								$matched_ticket_numbers[] = $ticket;
							}
						}
					}
				} else {
					$matched_ticket_numbers[] = $searched_value;
				}

				if (!lty_check_is_array($matched_ticket_numbers)) {
					throw new exception(wp_kses_post(get_option('lty_settings_user_chooses_ticket_search_error', 'Ticket(s) not found')));
				}

				$ticket_args = array(
					'matched_ticket_numbers' => array_unique($matched_ticket_numbers),
					'product' => $product,
					'cart_tickets' => $product->get_cart_tickets(),
					'reserved_tickets' => $product->get_reserved_tickets(),
					'sold_tickets' => $product->get_placed_tickets(),
				);

				$html = lty_get_template_html('single-product/ticket-tab-content-search.php', $ticket_args);

				wp_send_json_success(array( 'html' => $html ));
			} catch (Exception $ex) {
				wp_send_json_error(array( 'error' => $ex->getMessage() ));
			}
		}

		/**
		 * Order without tickets popup.
		 *
		 * @return void
		 * */
		public static function orders_without_tickets_popup() {

			check_ajax_referer('lty-orders-without-tickets-nonce', 'lty_security');

			try {
				if (!isset($_POST['product_id']) || empty(absint($_POST['product_id']))) {
					throw new exception(__('Invalid Product', 'lottery-for-woocommerce'));
				}

				$product = wc_get_product(absint($_POST['product_id']));
				if (!lty_is_lottery_product($product)) {
					throw new exception(__('Invalid Product', 'lottery-for-woocommerce'));
				}

				$order_ids = lty_get_order_ids_without_tickets($product);
				if (!lty_check_is_array($order_ids)) {
					throw new exception(__('No Data Found', 'lottery-for-woocommerce'));
				}

				ob_start();
				include LTY_ABSPATH . 'inc/admin/menu/views/backbone-modal/html-orders-without-tickets-content.php';
				$contents = ob_get_contents();
				ob_end_clean();

				wp_send_json_success(array( 'html' => $contents ));
			} catch (Exception $ex) {
				wp_send_json_error(array( 'error' => $ex->getMessage() ));
			}
		}

		/**
		 * Order status action.
		 *
		 * @return void
		 * */
		public static function order_status_action() {

			check_ajax_referer('lty-orders-status-action-nonce', 'lty_security');

			try {
				if (!isset($_POST['product_id']) || empty(absint($_POST['product_id']))) {
					throw new exception(__('Invalid Product', 'lottery-for-woocommerce'));
				}

				$product = wc_get_product(absint($_POST['product_id']));
				if (!lty_is_lottery_product($product)) {
					throw new exception(__('Invalid Product', 'lottery-for-woocommerce'));
				}

				$status = isset($_POST['status']) ? wc_clean(wp_unslash($_POST['status'])) : false;
				$status = 'all' == $status || false == $status ? false : $status;
				$order_ids = lty_get_order_ids_without_tickets($product, $status);
				if (!lty_check_is_array($order_ids)) {
					throw new exception(__('No Data Found', 'lottery-for-woocommerce'));
				}

				ob_start();
				include LTY_ABSPATH . 'inc/admin/menu/views/backbone-modal/html-orders-without-tickets-table-content.php';
				$contents = ob_get_contents();
				ob_end_clean();

				wp_send_json_success(array( 'html' => $contents ));
			} catch (Exception $ex) {
				wp_send_json_error(array( 'error' => $ex->getMessage() ));
			}
		}

		/**
		 * Pagination action.
		 *
		 * @return void
		 * */
		public static function pagination_action() {
			check_ajax_referer('lty-pagination-action-nonce', 'lty_security');

			try {
				if (!isset($_POST) || !isset($_POST['selected_page'])) { // @codingStandardsIgnoreLine.
					throw new exception(__('Invalid Request', 'lottery-for-woocommerce'));
				}

				$table_action_name = isset($_POST['table_action_name']) ? wc_clean(wp_unslash($_POST['table_action_name'])) : '';
				if (!$table_action_name) {
					throw new exception(__('Invalid Data', 'lottery-for-woocommerce'));
				}

				$product_id = isset($_POST['product_id']) ? absint($_POST['product_id']) : 0;
				$product = wc_get_product($product_id);
				$lottery_table_actions = array( 'lty_ticket_logs', 'lty_instant_winner_logs', 'lty_participated_lotteries_popup' );
				if (in_array($table_action_name, $lottery_table_actions) && !lty_is_lottery_product($product)) {
					throw new exception(__('Invalid Data', 'lottery-for-woocommerce'));
				}

				$current_page = isset($_POST['selected_page']) && !empty($_POST['selected_page']) ? absint($_POST['selected_page']) : 1;
				$contents = '';
				switch ($table_action_name) :
					case 'lty_ticket_logs':
						$search = isset($_POST['s']) ? wc_clean(wp_unslash($_POST['s'])) : '';
						$contents = lty_get_template_html('single-product/tabs/ticket-logs-layout.php', lty_prepare_ticket_logs_template_arguments($product, $current_page, $search));
						break;

					case 'lty_instant_winner_logs':
						$contents = lty_get_template_html( 'single-product/tabs/instant-winners-logs.php', lty_prepare_instant_winner_logs_arguments( $product, $current_page ) );
						break;

					case 'lty_instant_winner_prize_group':
						$extra_data = isset( $_POST['extra_data'] ) ? wc_clean( wp_unslash( $_POST['extra_data'] ) ) : array();
						if ( ! lty_check_is_array( $extra_data ) ) {
							throw new exception( __( 'Invalid Data', 'lottery-for-woocommerce' ) );
						}

						if ( ! isset( $extra_data['product_id'] ) || empty( $extra_data['product_id'] ) ) {
							throw new exception( __( 'Invalid Data', 'lottery-for-woocommerce' ) );
						}

						$product = wc_get_product( $extra_data['product_id'] );
						if ( ! lty_is_lottery_product( $product ) ) {
							throw new exception( __( 'Invalid Data', 'lottery-for-woocommerce' ) );
						}

						$contents = lty_get_template_html( 'single-product/tabs/instant-winner-prize-groups-layout.php', lty_prepare_instant_winner_logs_arguments( $product, $current_page ) );
						break;

					case 'lty_instant_winner_prize_group_ticket':
						$extra_data = isset( $_POST['extra_data'] ) ? wc_clean( wp_unslash( $_POST['extra_data'] ) ) : array();
						if ( ! lty_check_is_array( $extra_data ) ) {
							throw new exception( __( 'Invalid Data', 'lottery-for-woocommerce' ) );
						}

						if ( ! isset( $extra_data['product_id'] ) || ! isset( $extra_data['prize_group_id'] ) ) {
							throw new exception( __( 'Invalid Data', 'lottery-for-woocommerce' ) );
						}

						$product_id     = absint( $extra_data['product_id'] );
						$prize_group_id = absint( $extra_data['prize_group_id'] );

						if ( ! $product_id || ! $prize_group_id ) {
							throw new Exception( __( 'Invalid Data', 'lottery-for-woocommerce' ) );
						}

						$product = wc_get_product( $product_id );
						if ( ! lty_is_lottery_product( $product ) ) {
							throw new exception( __( 'Invalid Data', 'lottery-for-woocommerce' ) );
						}

						$contents = lty_get_template_html( 'single-product/tabs/instant-winner-prize-group-tickets.php', lty_get_instant_winner_prize_group_ticket_logs_arguments( $product, $prize_group_id, $current_page ) );
						break;

					case 'lty_winners_list':
						$post_per_page = get_option('lty_settings_winners_list_per_page', 10);
						$offset = ( $post_per_page * $current_page ) - $post_per_page;
						$lottery_winner_ids = lty_get_lottery_winner_ids();
						$page_count = ceil(count($lottery_winner_ids) / $post_per_page);

						$contents = lty_get_template_html(
								'lottery-product-winners-list-layout.php',
								array(
									'winner_ids' => array_slice($lottery_winner_ids, $offset, $post_per_page),
									'offset' => $offset,
									'columns' => lty_get_lottery_shortcode_winner_table_header(),
									'pagination' => lty_prepare_pagination_arguments($current_page, $page_count),
								)
						);
						break;

					case 'winners_by_date':
						$per_page = isset($_POST['per_page']) ? absint($_POST['per_page']) : 99999;
						$order = isset($_POST['order']) ? wc_clean(wp_unslash($_POST['order'])) : 'DESC';
						$date_filter_number = isset($_POST['date_filter_number']) ? absint($_POST['date_filter_number']) : '';
						$date_filter_unit = isset($_POST['date_filter_unit']) ? wc_clean(wp_unslash($_POST['date_filter_unit'])) : '';

						$start_date = lty_prepare_winning_dates_start_date($date_filter_number, $date_filter_unit);
						$lottery_winning_dates = lty_get_lottery_winning_dates($order, $start_date);
						$page_count = ceil(count($lottery_winning_dates) / $per_page);
						$offset = ( $per_page * $current_page ) - $per_page;

						$contents = lty_get_template_html(
								'shortcodes/lottery-winners-by-date-layout.php',
								array(
									'lottery_winning_dates' => array_slice($lottery_winning_dates, $offset, $per_page),
									'paginate' => true,
									'per_page' => $per_page,
									'order' => $order,
									'date_filter_number' => $date_filter_number,
									'date_filter_unit' => $date_filter_unit,
									'pagination' => lty_prepare_pagination_arguments($current_page, $page_count),
								)
						);
						break;

					case 'instant_winners_by_date':
							$per_page = isset($_POST['per_page']) ? absint($_POST['per_page']) : 99999;
							$order = isset($_POST['order']) ? wc_clean(wp_unslash($_POST['order'])) : 'DESC';
							$date_filter_number = isset($_POST['date_filter_number']) ? absint($_POST['date_filter_number']) : '';
							$date_filter_unit = isset($_POST['date_filter_unit']) ? wc_clean(wp_unslash($_POST['date_filter_unit'])) : '';
	
							$start_date = lty_prepare_winning_dates_start_date($date_filter_number, $date_filter_unit);
							$lottery_instant_winning_dates = lty_get_lottery_instant_winning_dates($order, $start_date);
							$page_count = ceil(count($lottery_instant_winning_dates) / $per_page);
							$offset = ( $per_page * $current_page ) - $per_page;
	
							$contents = lty_get_template_html(
									'shortcodes/lottery-instant-winners-by-date-layout.php',
									array(
										'lottery_instant_winning_dates' => array_slice($lottery_instant_winning_dates, $offset, $per_page),
										'paginate' => true,
										'per_page' => $per_page,
										'order' => $order,
										'date_filter_number' => $date_filter_number,
										'date_filter_unit' => $date_filter_unit,
										'pagination' => lty_prepare_pagination_arguments($current_page, $page_count),
									)
							);
						break;
	
					case 'lty_won_lottery_products':
					case 'lty_my_lottery_products':
					case 'lty_not_won_lottery_products':
					case 'lty_instant_win':
						if (!class_exists('LTY_Dashboard')) {
							include_once LTY_ABSPATH . 'inc/frontend/class-lty-dashboard.php';
						}

						switch ( $table_action_name ) {
							case 'lty_won_lottery_products':
								$url_param = lty_get_dashboard_won_lotteries_endpoint_url();
								$template_name = 'dashboard/won-lottery-products-layout.php';
								break;
							
							case 'lty_not_won_lottery_products':
								$template_name = 'dashboard/not-won-lottery-products-layout.php';
								$url_param = lty_get_dashboard_lost_lotteries_url_parameter();
								break;

							case 'lty_instant_win':
								$template_name = 'dashboard/instant-win-layout.php';
								$url_param = lty_get_dashboard_instant_win_endpoint_url();
								break;

							default:
								$template_name = 'dashboard/my-lottery-products-layout.php';
								$url_param = lty_get_dashboard_participated_lotteries_endpoint_url();
						}

						$table_args = LTY_Dashboard::populate_data($current_page, $url_param);
						$contents = lty_get_template_html(
								$template_name,
								array(
									'columns' => lty_dashboard_menu_columns($url_param),
									'post_ids' => $table_args['post_ids'],
									'current_lottery_menu' => $url_param,
									'pagination' => $table_args['pagination'],
								)
						);

						break;

					case 'lty_myaccount_lottery_won_lottery_products':
					case 'lty_myaccount_lottery_participated_lottery_products':
					case 'lty_myaccount_lottery_not_won_lottery_products':
					case 'lty_myaccount_instant_win':
						if (!class_exists('LTY_Myaccount_Handler')) {
							include_once LTY_ABSPATH . 'inc/frontend/class-lty-myaccount-handler.php';
						}

						switch ( $table_action_name ) {
							case 'lty_myaccount_lottery_won_lottery_products':
								$url_param = lty_get_dashboard_won_lotteries_endpoint_url();
								$template_name = 'myaccount/won-lottery-products-layout.php';
								break;

							case 'lty_myaccount_lottery_not_won_lottery_products':
								$template_name = 'myaccount/not-won-lottery-products-layout.php';
								$url_param = lty_get_dashboard_not_won_lotteries_endpoint_url();
								break;

							case 'lty_myaccount_instant_win':
								$template_name = 'myaccount/instant-win-layout.php';
								$url_param = lty_get_dashboard_instant_win_endpoint_url();
								break;

							default:
								$template_name = 'myaccount/participated-lottery-products-layout.php';
								$url_param = lty_get_dashboard_participated_lotteries_endpoint_url();
						}

						$table_args = LTY_Myaccount_Handler::prepare_myaccount_lottery_template_arguments($current_page, $url_param);
						if (!lty_check_is_array($table_args)) {
							throw new exception(__('No Data Found', 'lottery-for-woocommerce'));
						}

						$contents = lty_get_template_html(
								$template_name,
								array(
									'columns' => lty_dashboard_menu_columns($url_param),
									'post_ids' => $table_args['post_ids'],
									'current_lottery_menu' => $url_param,
									'pagination' => $table_args['pagination'],
								)
						);

						break;

					case 'lty_participated_lotteries_popup':
						$contents = lty_get_template_html('popup/participated-lottery-tickets-details.php', lty_prepare_participated_lottery_tickets_details_arguments($product, $current_page));
						break;

					case 'lty_order_instant_winners':
						$extra_data = isset( $_POST['extra_data'] ) ? wc_clean( wp_unslash( $_POST['extra_data'] ) ) : array();
						if ( ! lty_check_is_array( $extra_data ) ) {
							throw new exception( __( 'Invalid Data', 'lottery-for-woocommerce' ) );
						}

						$order_id = isset( $extra_data['order_id'] ) ? absint( $extra_data['order_id'] ) : false;
						if ( ! $order_id ) {
							throw new exception( __( 'Invalid Data', 'lottery-for-woocommerce' ) );
						}

						$order = wc_get_order( $order_id );
						if ( ! is_object( $order_id ) ) {
							throw new exception( __( 'No Data Found', 'lottery-for-woocommerce' ) );
						}

						$instant_winner_log_ids = lty_get_instant_winner_log_ids_by_order_id( $order_id, false, 'lty_won' );
						if ( ! lty_check_is_array( $instant_winner_log_ids ) ) {
							throw new exception( __( 'No Data Found', 'lottery-for-woocommerce' ) );
						}

						$template = isset( $extra_data['template'] ) && 'thankyou' === $extra_data['template'] ? 'thankyou/instant-winners-layout.php' : 'order/instant-winners-layout.php';

						$post_per_page = 20;
						$offset        = ( $post_per_page * $current_page ) - $post_per_page;
						$page_count    = ceil( count( $instant_winner_log_ids ) / $post_per_page );

						$contents = lty_get_template_html(
							$template,
							array(
								'columns'                => lty_get_order_instant_winners_columns(),
								'instant_winner_log_ids' => array_slice( $instant_winner_log_ids, $offset, $post_per_page ),
								'order_id'               => $order_id,
								'pagination'             => lty_prepare_pagination_arguments( $current_page, $page_count ),
							)
						);
						break;

				endswitch;

				if (!$contents) {
					throw new exception(__('No Data Found', 'lottery-for-woocommerce'));
				}

				wp_send_json_success(array( 'html' => $contents ));
			} catch (Exception $ex) {
				wp_send_json_error(array( 'error' => $ex->getMessage() ));
			}
		}

		/**
		 * Get the instant winner prize group tickets html.
		 *
		 * @since 12.0.0
		 * */
		public static function get_instant_winner_prize_group_tickets_html() {
			check_ajax_referer('lty-instant-win-prize-group-tickets-nonce', 'lty_security');

			try {
				$extra_data = isset( $_POST['extra_data'] ) ? wc_clean( wp_unslash( $_POST['extra_data'] ) ) : array();
				if ( ! lty_check_is_array( $extra_data ) ) {
					throw new exception( __( 'Invalid Data', 'lottery-for-woocommerce' ) );
				}

				if ( ! isset( $extra_data['product_id'] ) || ! isset( $extra_data['prize_group_id'] ) ) {
					throw new exception( __( 'Invalid Data', 'lottery-for-woocommerce' ) );
				}

				$product_id     = absint( $extra_data['product_id'] );
				$prize_group_id = absint( $extra_data['prize_group_id'] );

				if ( ! $product_id || ! $prize_group_id ) {
					throw new Exception( __( 'Invalid Data', 'lottery-for-woocommerce' ) );
				}

				$product = wc_get_product( $product_id );
				if ( ! lty_is_lottery_product( $product ) ) {
					throw new exception( __( 'Invalid Data', 'lottery-for-woocommerce' ) );
				}

				$contents = lty_get_template_html( 'single-product/tabs/instant-winner-prize-group-tickets.php', lty_get_instant_winner_prize_group_ticket_logs_arguments( $product, $prize_group_id ) );

				wp_send_json_success(array( 'html' => $contents ));
			} catch (Exception $ex) {
				wp_send_json_error(array( 'error' => $ex->getMessage() ));
			}
		}

		/**
		 * Search ticket logs.
		 *
		 * @since 7.0
		 * */
		public static function search_ticket_logs() {
			check_ajax_referer('lty-search-nonce', 'lty_security');

			try {
				if (!isset($_POST) || !isset($_POST['s'])) { // @codingStandardsIgnoreLine.
					throw new exception(__('Invalid Request', 'lottery-for-woocommerce'));
				}

				$product_id = isset($_POST['product_id']) ? absint($_POST['product_id']) : 0;
				$search = isset($_POST['s']) ? wc_clean(wp_unslash($_POST['s'])) : 0;
				$product = wc_get_product($product_id);
				if (!lty_is_lottery_product($product)) {
					throw new exception(__('Invalid Request', 'lottery-for-woocommerce'));
				}

				$contents = lty_get_template_html('single-product/tabs/ticket-logs-layout.php', lty_prepare_ticket_logs_template_arguments($product, 1, $search));

				wp_send_json_success(array( 'html' => $contents ));
			} catch (Exception $ex) {
				wp_send_json_error(array( 'error' => $ex->getMessage() ));
			}
		}

		/**
		 * Add instant winner rule.
		 *
		 * @since 8.0.0
		 * @throws exception
		 */
		public static function add_instant_winner_rule() {
			check_ajax_referer( 'lty-instant-winner', 'lty_security' );

			try {
				// Return if the current user does not have permission.
				if ( ! current_user_can( 'edit_posts' ) ) {
					throw new exception( esc_html__( "You don't have permission to do this action", 'lottery-for-woocommerce' ) );
				}

				if ( ! isset( $_POST['product_id'], $_POST['instant_winner_rule'] ) ) {
					throw new exception( __( 'Invalid Request', 'lottery-for-woocommerce' ) );
				}

				$instant_winner_rule = ! empty( $_POST['instant_winner_rule'] ) ? wc_clean( wp_unslash( $_POST['instant_winner_rule'] ) ) : array();
				if ( ! lty_check_is_array( $instant_winner_rule ) ) {
					throw new exception( __( 'Invalid Request', 'lottery-for-woocommerce' ) );
				}

				$product_id = ! empty( $_POST['product_id'] ) ? wc_clean( wp_unslash( $_POST['product_id'] ) ) : '';
				if ( ! $product_id ) {
					throw new exception( __( 'Invalid Request', 'lottery-for-woocommerce' ) );
				}

				$product = wc_get_product( $product_id );
				if ( ! $product->exists() ) {
					throw new exception( __( 'Invalid Request', 'lottery-for-woocommerce' ) );
				}

				// Check is group diplay mode.
				$display_mode = ! empty( $_POST['display_mode'] ) ? wc_clean( wp_unslash( $_POST['display_mode'] ) ) : '1';
				if ( '2' === $display_mode ) {
					if ( empty( $instant_winner_rule['prize_group_id'] ) ) {
						throw new exception( __( 'Prize Group cannot be empty', 'lottery-for-woocommerce' ) );
					}

					$prize_group = lty_get_instant_winner_prize_group( $instant_winner_rule['prize_group_id'] );
					if ( ! $prize_group->exists() ) {
						throw new exception( __( 'Prize Group is not exists', 'lottery-for-woocommerce' ) );
					}

					$rule_data = array(
						'lty_image_id'               => $prize_group->get_image_id(),
						'lty_prize_type'             => $prize_group->get_prize_type(),
						'lty_coupon_generation_type' => $prize_group->get_coupon_generation_type(),
						'lty_coupon_discount_type'   => $prize_group->get_coupon_discount_type(),
						'lty_coupon_id'              => $prize_group->get_coupon_id(),
						'lty_gift_product_id'        => $prize_group->get_gift_product_id(),
						'lty_gift_product_quantity'  => $prize_group->get_gift_product_quantity(),
						'lty_prize_amount'           => $prize_group->get_prize_amount(),
						'lty_instant_winner_prize'   => $prize_group->get_prize_message(),
					);
				} else {
					$rule_data = array(
						'lty_image_id'               => $instant_winner_rule['image_id'],
						'lty_prize_type'             => $instant_winner_rule['prize_type'],
						'lty_coupon_generation_type' => $instant_winner_rule['coupon_generation_type'],
						'lty_coupon_discount_type'   => $instant_winner_rule['coupon_discount_type'],
						'lty_coupon_id'              => $instant_winner_rule['coupon_id'],
						'lty_gift_product_id'        => $instant_winner_rule['gift_product_id'],
						'lty_gift_product_quantity'  => $instant_winner_rule['gift_product_quantity'],
						'lty_prize_amount'           => $instant_winner_rule['prize_amount'],
						'lty_instant_winner_prize'   => $instant_winner_rule['prize_message'],
					);
				}

				$rule_data['lty_ticket_number']  = $instant_winner_rule['ticket_number'];
				$rule_data['lty_prize_group_id'] = $instant_winner_rule['prize_group_id'];

				/**
				 * This hook is used to alter the prepared instant winner rule data to save.
				 *
				 * @since 11.0.0
				 */
				$instant_winner_rule_data = apply_filters( 'lty_instant_winner_rule_data_before_save', $rule_data );

				// Validate the if the ticket number already exists.
				$rule_id = lty_get_rule_id_by_ticket_number( $product->get_id(), $instant_winner_rule_data['lty_ticket_number'] );
				if ( $rule_id ) {
					throw new exception( __( 'Ticket Number Already exists', 'lottery-for-woocommerce' ) );
				}

				$instant_winner_rule_id = lty_create_new_instant_winner_rule( $instant_winner_rule_data, array( 'post_parent' => $product_id ) );
				$instant_winner         = lty_get_instant_winner_rule( $instant_winner_rule_id );

				$relist_count = is_callable( array( $product, 'get_current_relist_count' ) ) ? $product->get_current_relist_count() : 0;
				lty_create_new_instant_winner_log( array_merge( $instant_winner_rule_data, array( 'lty_current_relist_count' => $relist_count ) ), array( 'post_parent' => $instant_winner_rule_id ) );

				/**
				 * This hook is used to do extra action after instant winner rule created.
				 *
				 * @since 11.9.0
				 * @param int Product ID.
				 */
				do_action( 'lty_instant_winner_rule_created', $product_id );

				wp_send_json_success();
			} catch ( Exception $ex ) {
				wp_send_json_error( array( 'error' => $ex->getMessage() ) );
			}
		}

		/**
		 * Save instant winners rules.
		 *
		 * @since 9.6.0
		 * @throws exception
		 */
		public static function save_instant_winners_rules() {
			check_ajax_referer( 'lty-instant-winner', 'lty_security' );

			try {
				// Return if the current user does not have permission.
				if ( ! current_user_can( 'edit_posts' ) ) {
					throw new exception( esc_html__( "You don't have permission to do this action", 'lottery-for-woocommerce' ) );
				}

				if ( ! isset( $_POST['product_id'], $_POST['instant_winners_rules'], $_POST['display_mode'] ) ) {
					throw new exception( __( 'Invalid Request', 'lottery-for-woocommerce' ) );
				}

				$product_id = ! empty( $_POST['product_id'] ) ? wc_clean( wp_unslash( $_POST['product_id'] ) ) : '';
				if ( ! $product_id ) {
					throw new exception( __( 'Invalid Request', 'lottery-for-woocommerce' ) );
				}

				$instant_winners_rules = ! empty( $_POST['instant_winners_rules'] ) ? wc_clean( wp_unslash( $_POST['instant_winners_rules'] ) ) : array();
				if ( ! lty_check_is_array( $instant_winners_rules ) ) {
					throw new exception( __( 'Invalid Request', 'lottery-for-woocommerce' ) );
				}

				$product = wc_get_product( $product_id );
				if ( ! $product->exists() ) {
					throw new exception( __( 'Invalid Request', 'lottery-for-woocommerce' ) );
				}

				$display_mode = ! empty( $_POST['display_mode'] ) ? wc_clean( wp_unslash( $_POST['display_mode'] ) ) : '1';
				$relist_count = is_callable( array( $product, 'get_current_relist_count' ) ) ? $product->get_current_relist_count() : 0;
				foreach ( $instant_winners_rules as $instant_winner_rule_id => $instant_winner_rule ) {
					$rule_id = lty_get_rule_id_by_ticket_number( $product_id, $instant_winner_rule['ticket_number'] );
					if ( $rule_id && $instant_winner_rule_id !== $rule_id ) {
						continue;
					}

					// Check is group diplay mode.
					if ( '2' === $display_mode ) {
						if ( empty( $instant_winner_rule['prize_group_id'] ) ) {
							continue;
						}

						$prize_group = lty_get_instant_winner_prize_group( $instant_winner_rule['prize_group_id'] );
						if ( ! $prize_group->exists() ) {
							continue;
						}

						$rule_data = array(
							'lty_image_id'               => $prize_group->get_image_id(),
							'lty_prize_type'             => $prize_group->get_prize_type(),
							'lty_coupon_generation_type' => $prize_group->get_coupon_generation_type(),
							'lty_coupon_discount_type'   => $prize_group->get_coupon_discount_type(),
							'lty_coupon_id'              => $prize_group->get_coupon_id(),
							'lty_gift_product_id'        => $prize_group->get_gift_product_id(),
							'lty_gift_product_quantity'  => $prize_group->get_gift_product_quantity(),
							'lty_prize_amount'           => $prize_group->get_prize_amount(),
							'lty_instant_winner_prize'   => $prize_group->get_prize_message(),
						);
					} else {
						$rule_data = array(
							'lty_image_id'               => $instant_winner_rule['image_id'],
							'lty_prize_type'             => $instant_winner_rule['prize_type'],
							'lty_coupon_generation_type' => $instant_winner_rule['coupon_generation_type'],
							'lty_coupon_discount_type'   => $instant_winner_rule['coupon_discount_type'],
							'lty_coupon_id'              => $instant_winner_rule['coupon_id'],
							'lty_gift_product_id'        => $instant_winner_rule['gift_product_id'],
							'lty_gift_product_quantity'  => $instant_winner_rule['gift_product_quantity'],
							'lty_prize_amount'           => $instant_winner_rule['prize_amount'],
							'lty_instant_winner_prize'   => $instant_winner_rule['prize_message'],
						);
					}

					$rule_data['lty_ticket_number']  = $instant_winner_rule['ticket_number'];
					$rule_data['lty_prize_group_id'] = $instant_winner_rule['prize_group_id'];

					/**
					 * This hook is used to alter the prepared instant winner rule data to save.
					 *
					 * @since 11.0.0
					 */
					$instant_winner_rule_args = apply_filters( 'lty_instant_winner_rule_data_before_save', $rule_data );

					lty_update_instant_winner_rule( $instant_winner_rule_id, $instant_winner_rule_args );
					$instant_winner_log_id = lty_get_instant_winner_log_id_by_rule_id( $instant_winner_rule_id, $relist_count );
					if ( ! $instant_winner_log_id ) {
						continue;
					}

					lty_update_instant_winner_log( $instant_winner_log_id, array_merge( $instant_winner_rule_args, array( 'lty_current_relist_count' => $relist_count ) ), array( 'post_parent' => $instant_winner_rule_id ) );
				}

				/**
				 * This hook is used to do extra action after instant winner rules saved.
				 *
				 * @since 11.9.0
				 * @param int Product ID.
				 */
				do_action( 'lty_instant_winner_rules_saved', $product_id );

				wp_send_json_success( array( 'success' => __( 'Settings saved successfully!', 'lottery-for-woocommerce' ) ) );
			} catch ( Exception $ex ) {
				wp_send_json_error( array( 'error' => $ex->getMessage() ) );
			}
		}

		/**
		 * Remove instant winner rule.
		 *
		 * @since 8.0.0
		 * @throws exception
		 */
		public static function remove_instant_winner_rule() {
			check_ajax_referer( 'lty-instant-winner', 'lty_security' );

			try {

				// Return if the current user does not have permission.
				if (!current_user_can('edit_posts')) {
					throw new exception(esc_html__("You don't have permission to do this action", 'lottery-for-woocommerce'));
				}

				if (!isset($_POST['product_id'], $_POST['instant_winner_rule_ids'])) {
					throw new exception(esc_html__('Invalid Data', 'lottery-for-woocommerce'));
				}

				$product_id = !empty($_POST['product_id']) ? wc_clean(wp_unslash($_POST['product_id'])) : '';
				if (empty($product_id)) {
					throw new exception(esc_html__('Invalid Data', 'lottery-for-woocommerce'));
				}

				$instant_winner_rule_ids = !empty($_POST['instant_winner_rule_ids']) ? wc_clean(wp_unslash($_POST['instant_winner_rule_ids'])) : array();
				if (!lty_check_is_array($instant_winner_rule_ids)) {
					throw new exception(esc_html__('Please select any rule.', 'lottery-for-woocommerce'));
				}

				$product = wc_get_product($product_id);
				if ( ! $product->exists() ) {
					throw new exception(esc_html__('Invalid Data', 'lottery-for-woocommerce'));
				}

				$relist_count = is_callable(array( $product, 'get_current_relist_count' )) ? $product->get_current_relist_count() : 0;
				foreach ($instant_winner_rule_ids as $instant_winner_rule_id) {
					$instant_winner_rule = lty_get_instant_winner_rule($instant_winner_rule_id);
					if (!$instant_winner_rule->exists()) {
						continue;
					}

					$instant_winner_log_id = lty_get_instant_winner_log_id_by_rule_id($instant_winner_rule_id, $relist_count);
					$instant_winner_log    = lty_get_instant_winner_log( $instant_winner_log_id );
					// Delete instant winner log.
					if ( $instant_winner_log->exists() ) {
						lty_delete_instant_winner_log($instant_winner_log_id);
					}

					// Delete instant winner rule.
					if ($instant_winner_rule->exists()) {
						lty_delete_instant_winner_rule($instant_winner_rule_id);
					}
				}

				/**
				 * This hook is used to do extra action after instant winner rules deleted.
				 *
				 * @since 11.9.0
				 * @param int Product ID.
				 */
				do_action( 'lty_instant_winner_rules_deleted', $product_id );

				wp_send_json_success();
			} catch (Exception $ex) {
				wp_send_json_error(array( 'error' => $ex->getMessage() ));
			}
		}

		/**
		 * Handles the lottery instant winners rules pagination content.
		 *
		 * @since 9.6.0
		 * @throws exception
		 */
		public static function instant_winners_rules_pagination_content() {
			check_ajax_referer( 'lty-instant-winner', 'lty_security' );

			try {
				// Return if the current user does not have permission.
				if (!current_user_can('edit_posts')) {
					throw new exception(esc_html__("You don't have permission to do this action", 'lottery-for-woocommerce'));
				}

				if (!isset($_POST['product_id'])) {
					throw new exception(esc_html__('Invalid Request', 'lottery-for-woocommerce'));
				}

				$product_id = isset($_POST['product_id']) && !empty($_POST['product_id']) ? wc_clean(wp_unslash($_POST['product_id'])) : '';
				if (!$product_id) {
					throw new exception(esc_html__('Invalid Request', 'lottery-for-woocommerce'));
				}

				$product = wc_get_product($product_id);
				if (!$product->exists()) {
					throw new exception(esc_html__('Invalid Request', 'lottery-for-woocommerce'));
				}

				$current_page = isset($_POST['current_page']) ? intval(wc_clean(wp_unslash($_POST['current_page']))) : 1;

				ob_start();
				lty_render_lottery_instant_winners_rules($product, $current_page);
				$contents = ob_get_contents();
				ob_end_clean();

				wp_send_json_success(array( 'html' => $contents ));
			} catch (Exception $ex) {
				wp_send_json_error(array( 'error' => $ex->getMessage() ));
			}
		}

		/**
		 * Create a new instant winner prize group.
		 *
		 * @since 11.1.0
		 * @throws exception
		 */
		public static function create_instant_winner_prize_group() {
			check_ajax_referer( 'lty-instant-winner', 'lty_security' );

			try {
				// Return if the current user does not have permission.
				if ( ! current_user_can( 'edit_posts' ) ) {
					throw new exception( esc_html__( "You don't have permission to do this action", 'lottery-for-woocommerce' ) );
				}

				if ( ! isset( $_POST['product_id'], $_POST['prize_group'] ) ) {
					throw new exception( __( 'Invalid Request', 'lottery-for-woocommerce' ) );
				}

				$product_id = ! empty( $_POST['product_id'] ) ? wc_clean( wp_unslash( $_POST['product_id'] ) ) : '';
				if ( ! $product_id ) {
					throw new exception( __( 'Invalid Request', 'lottery-for-woocommerce' ) );
				}

				$product = wc_get_product( $product_id );
				if ( ! $product->exists() ) {
					throw new exception( __( 'Invalid Request', 'lottery-for-woocommerce' ) );
				}

				$prize_group = wc_clean( wp_unslash( $_POST['prize_group'] ) );

				/**
				 * This hook is used to alter the prepared instant winner prize group data before save.
				 *
				 * @since 11.1.0
				 */
				$prize_group_data = apply_filters(
					'lty_instant_winner_prize_group_data_before_save',
					array(
						'lty_image_id'               => ! empty( $prize_group['image_id'] ) ? absint( $prize_group['image_id'] ) : '',
						'lty_prize_type'             => ! empty( $prize_group['prize_type'] ) ? $prize_group['prize_type'] : 'physical',
						'lty_coupon_generation_type' => ! empty( $prize_group['coupon_generation_type'] ) ? $prize_group['coupon_generation_type'] : '',
						'lty_coupon_discount_type'   => ! empty( $prize_group['coupon_discount_type'] ) ? $prize_group['coupon_discount_type'] : '',
						'lty_coupon_id'              => ! empty( $prize_group['coupon_id'] ) ? absint( $prize_group['coupon_id'] ) : '',
						'lty_gift_product_id'        => ! empty( $prize_group['gift_product_id'] ) ? absint( $prize_group['gift_product_id'] ) : '',
						'lty_gift_product_quantity'  => ! empty( $prize_group['gift_product_quantity'] ) ? absint( $prize_group['gift_product_quantity'] ) : '',
						'lty_prize_amount'           => ! empty( $prize_group['prize_amount'] ) ? wc_format_decimal( $prize_group['prize_amount'] ) : '',
						'lty_prize_message'          => ! empty( $prize_group['prize_message'] ) ? $prize_group['prize_message'] : '',
					)
				);

				lty_create_new_instant_winner_prize_group(
					$prize_group_data,
					array(
						'post_title'  => ! empty( $prize_group['title'] ) ? $prize_group['title'] : 'Untitled',
						'post_parent' => $product_id,
					)
				);

				wp_send_json_success();
			} catch ( Exception $ex ) {
				wp_send_json_error( array( 'error' => $ex->getMessage() ) );
			}
		}

		/**
		 * Save instant winner prize groups.
		 *
		 * @since 11.1.0
		 * @throws exception
		 */
		public static function save_instant_winner_prize_groups() {
			check_ajax_referer( 'lty-instant-winner', 'lty_security' );

			try {
				// Return if the current user does not have permission.
				if ( ! current_user_can( 'edit_posts' ) ) {
					throw new exception( esc_html__( "You don't have permission to do this action", 'lottery-for-woocommerce' ) );
				}

				if ( ! isset( $_POST['product_id'], $_POST['prize_groups'] ) ) {
					throw new exception( __( 'Invalid Request', 'lottery-for-woocommerce' ) );
				}

				$product_id = ! empty( $_POST['product_id'] ) ? wc_clean( wp_unslash( $_POST['product_id'] ) ) : '';
				if ( ! $product_id ) {
					throw new exception( __( 'Invalid Request', 'lottery-for-woocommerce' ) );
				}

				$product = wc_get_product( $product_id );
				if ( ! $product->exists() ) {
					throw new exception( __( 'Invalid Request', 'lottery-for-woocommerce' ) );
				}

				$prize_groups = wc_clean( wp_unslash( $_POST['prize_groups'] ) );
				if ( ! lty_check_is_array( $prize_groups ) ) {
					throw new exception( __( 'Invalid Request', 'lottery-for-woocommerce' ) );
				}

				$relist_count = is_callable( array( $product, 'get_current_relist_count' ) ) ? $product->get_current_relist_count() : 0;
				foreach ( $prize_groups as $prize_group_id => $prize_group ) {
					/**
					 * This hook is used to alter the prepared instant winner rule data to save.
					 *
					 * @since 11.1.0
					 */
					$prize_group_data = apply_filters(
						'lty_instant_winner_prize_group_data_before_save',
						array(
							'lty_image_id'               => ! empty( $prize_group['image_id'] ) ? absint( $prize_group['image_id'] ) : '',
							'lty_prize_type'             => ! empty( $prize_group['prize_type'] ) ? $prize_group['prize_type'] : 'physical',
							'lty_coupon_generation_type' => ! empty( $prize_group['coupon_generation_type'] ) ? $prize_group['coupon_generation_type'] : '',
							'lty_coupon_discount_type'   => ! empty( $prize_group['coupon_discount_type'] ) ? $prize_group['coupon_discount_type'] : '',
							'lty_coupon_id'              => ! empty( $prize_group['coupon_id'] ) ? absint( $prize_group['coupon_id'] ) : '',
							'lty_prize_amount'           => ! empty( $prize_group['prize_amount'] ) ? wc_format_decimal( $prize_group['prize_amount'] ) : '',
							'lty_gift_product_id'        => ! empty( $prize_group['gift_product_id'] ) ? absint( $prize_group['gift_product_id'] ) : '',
							'lty_gift_product_quantity'  => ! empty( $prize_group['gift_product_quantity'] ) ? absint( $prize_group['gift_product_quantity'] ) : '',
							'lty_prize_message'          => ! empty( $prize_group['prize_message'] ) ? $prize_group['prize_message'] : '',
						)
					);

					lty_update_instant_winner_prize_group(
						$prize_group_id,
						$prize_group_data,
						array(
							'post_title' => ! empty( $prize_group['title'] ) ? $prize_group['title'] : 'Untitled',
						)
					);

					$instant_winner_rule_ids = lty_get_instant_winner_rule_ids_by_group_id( $prize_group_id, $product->get_id() );
					if ( ! lty_check_is_array( $instant_winner_rule_ids ) ) {
						continue;
					}

					$instant_winner_rule_data = array(
						'lty_image_id'               => $prize_group_data['lty_image_id'],
						'lty_prize_type'             => $prize_group_data['lty_prize_type'],
						'lty_coupon_generation_type' => $prize_group_data['lty_coupon_generation_type'],
						'lty_coupon_discount_type'   => $prize_group_data['lty_coupon_discount_type'],
						'lty_coupon_id'              => $prize_group_data['lty_coupon_id'],
						'lty_gift_product_id'        => $prize_group_data['lty_gift_product_id'],
						'lty_gift_product_quantity'  => $prize_group_data['lty_gift_product_quantity'],
						'lty_prize_amount'           => $prize_group_data['lty_prize_amount'],
						'lty_instant_winner_prize'   => $prize_group_data['lty_prize_message'],
					);

					foreach ( $instant_winner_rule_ids as $instant_winner_rule_id ) {
						lty_update_instant_winner_rule( $instant_winner_rule_id, $instant_winner_rule_data );
						$instant_winner_log_id = lty_get_instant_winner_log_id_by_rule_id( $instant_winner_rule_id, $relist_count );
						if ( ! $instant_winner_log_id ) {
							continue;
						}

						lty_update_instant_winner_log( $instant_winner_log_id, $instant_winner_rule_data );
					}
				}

				wp_send_json_success( array( 'success' => __( 'Settings saved successfully!', 'lottery-for-woocommerce' ) ) );
			} catch ( Exception $ex ) {
				wp_send_json_error( array( 'error' => $ex->getMessage() ) );
			}
		}

		/**
		 * Remove instant winner prize groups.
		 *
		 * @since 11.1.0
		 * @throws exception
		 */
		public static function remove_instant_winner_prize_group() {
			check_ajax_referer( 'lty-instant-winner', 'lty_security' );

			try {

				// Return if the current user does not have permission.
				if ( ! current_user_can( 'edit_posts' ) ) {
					throw new exception( esc_html__( "You don't have permission to do this action", 'lottery-for-woocommerce' ) );
				}

				if ( ! isset( $_POST['product_id'], $_POST['prize_group_ids'] ) ) {
					throw new exception( esc_html__( 'Invalid Data', 'lottery-for-woocommerce' ) );
				}

				$product_id = ! empty( $_POST['product_id'] ) ? wc_clean( wp_unslash( $_POST['product_id'] ) ) : '';
				if ( empty( $product_id ) ) {
					throw new exception( esc_html__( 'Invalid Data', 'lottery-for-woocommerce' ) );
				}

				$prize_group_ids = ! empty( $_POST['prize_group_ids'] ) ? wc_clean( wp_unslash( $_POST['prize_group_ids'] ) ) : array();
				if ( ! lty_check_is_array( $prize_group_ids ) ) {
					throw new exception( esc_html__( 'Please select any prize group.', 'lottery-for-woocommerce' ) );
				}

				$product = wc_get_product( $product_id );
				if ( ! $product->exists() ) {
					throw new exception( esc_html__( 'Invalid Data', 'lottery-for-woocommerce' ) );
				}

				foreach ( $prize_group_ids as $prize_group_id ) {
					$prize_group = lty_get_instant_winner_prize_group( $prize_group_id );
					if ( ! $prize_group->exists() ) {
						continue;
					}

					lty_delete_instant_winner_prize_group( $prize_group_id );
				}

				wp_send_json_success();
			} catch ( Exception $ex ) {
				wp_send_json_error( array( 'error' => $ex->getMessage() ) );
			}
		}

		/**
		 * Handles the instant winner prize groups pagination action.
		 *
		 * @since 11.1.0
		 * @throws exception
		 */
		public static function instant_winner_prize_groups_pagination_action() {
			check_ajax_referer( 'lty-instant-winner', 'lty_security' );

			try {
				// Return if the current user does not have permission.
				if ( ! current_user_can( 'edit_posts' ) ) {
					throw new exception( esc_html__( "You don't have permission to do this action", 'lottery-for-woocommerce' ) );
				}

				if ( ! isset( $_POST['product_id'] ) ) {
					throw new exception( esc_html__( 'Invalid Request', 'lottery-for-woocommerce' ) );
				}

				$product_id = isset( $_POST['product_id'] ) ? wc_clean( wp_unslash( $_POST['product_id'] ) ) : '';
				if ( ! $product_id ) {
					throw new exception( esc_html__( 'Invalid Request', 'lottery-for-woocommerce' ) );
				}

				$product = wc_get_product( $product_id );
				if ( ! $product->exists() ) {
					throw new exception( esc_html__( 'Invalid Request', 'lottery-for-woocommerce' ) );
				}

				$current_page = isset( $_POST['current_page'] ) ? intval( wc_clean( wp_unslash( $_POST['current_page'] ) ) ) : 1;

				ob_start();
				lty_render_instant_winner_prize_groups( $product, $current_page );
				$contents = ob_get_contents();
				ob_end_clean();

				wp_send_json_success( array( 'html' => $contents ) );
			} catch ( Exception $ex ) {
				wp_send_json_error( array( 'error' => $ex->getMessage() ) );
			}
		}

		/**
		 * Fetch export popup content.
		 *
		 * @since 10.3.0
		 * @throws exception
		 */
		public static function fetch_export_popup_content() {
			check_ajax_referer('lty-export', 'lty_security');

			try {
				// Throw error if the current user does not have permission.
				if (!current_user_can('export')) {
					throw new exception(esc_html__("You don't have permission to do this action", 'lottery-for-woocommerce'));
				}

				// Throw error, when the export type does pass in the request.
				if (empty($_POST['export_type'])) {
					throw new exception(esc_html__('Invalid Request', 'lottery-for-woocommerce'));
				}

				$exporter = LTY_Export_Handler::get_exporter(wc_clean(wp_unslash($_POST['export_type'])));
				// Throw error, when the export type does not have exporter.
				if (!is_object($exporter)) {
					throw new exception(esc_html__('Invalid Request', 'lottery-for-woocommerce'));
				}

				ob_start();
				if (!empty($_POST['step'])) {
					$exporter->set_step(wc_clean(wp_unslash($_POST['step'])));
					$exporter->set_total_rows();
				}

				$exporter->output();
				$contents = ob_get_contents();
				ob_end_clean();

				wp_send_json_success(array( 'html' => $contents ));
			} catch (Exception $ex) {
				wp_send_json_error(array( 'error' => $ex->getMessage() ));
			}
		}

		/**
		 * Run export.
		 *
		 * @since 10.3.0
		 * @throws exception
		 */
		public static function run_export() {
			check_ajax_referer('lty-export', 'lty_security');

			try {
				// Throw error if the current user does not have permission.
				if (!current_user_can('export')) {
					throw new exception(esc_html__("You don't have permission to do export action", 'lottery-for-woocommerce'));
				}

				// Throw error, when the action type does pass in the request.
				if (empty($_POST['export_type'])) {
					throw new exception(esc_html__('Invalid Request', 'lottery-for-woocommerce'));
				}

				$exporter = LTY_Export_Handler::get_exporter(wc_clean(wp_unslash($_POST['export_type'])));
				// Throw error, when the action type does not have exporter.
				if (!is_object($exporter)) {
					throw new exception(esc_html__('Invalid Request', 'lottery-for-woocommerce'));
				}

				// Run export based on current position.
				$exporter->generate_file();
				$percent_complete = $exporter->get_percent_complete();

				if (100 == $percent_complete) {
					ob_start();
					$exporter->set_step('done');
					$exporter->output();
					$contents = ob_get_contents();
					ob_end_clean();

					$data = array( 'percentage' => 100, 'html' => $contents, 'download_url' => $exporter->download_link() );
				} else {
					$page = $exporter->get_page();
					$data = array(
						'percentage' => $percent_complete,
						'page' => ++$page,
						'exported_count' => $exporter->get_total_exported(),
					);
				}

				wp_send_json_success($data);
			} catch (Exception $ex) {
				wp_send_json_error(array( 'error' => $ex->getMessage() ));
			}
		}

		/**
		 * Fetch import popup content.
		 *
		 * @since 9.9.0
		 * @throws exception
		 */
		public static function fetch_import_popup_content() {
			check_ajax_referer('lty-import', 'lty_security');

			try {
				// Throw error if the current user does not have permission.
				if (!current_user_can('import')) {
					throw new exception(esc_html__("You don't have permission to do this action", 'lottery-for-woocommerce'));
				}

				// Throw error, when the action type does pass in the request.
				if (empty($_POST['action_type'])) {
					throw new exception(esc_html__('Invalid Request', 'lottery-for-woocommerce'));
				}

				$importer = LTY_Import_Handler::get_importer(wc_clean(wp_unslash($_POST['action_type'])));
				// Throw error, when the action type does not have importer.
				if (!is_object($importer)) {
					throw new exception(esc_html__('Invalid Request', 'lottery-for-woocommerce'));
				}

				ob_start();
				$importer->output();
				$contents = ob_get_contents();
				ob_end_clean();

				wp_send_json_success(array( 'html' => $contents ));
			} catch (Exception $ex) {
				wp_send_json_error(array( 'error' => $ex->getMessage() ));
			}
		}

		/**
		 * Upload import form data.
		 *
		 * @since 9.9.0
		 * @throws exception
		 */
		public static function upload_import_form() {
			check_ajax_referer('lty-import', 'lty_security');

			try {
				// Throw error if the current user does not have permission.
				if (!current_user_can('import')) {
					throw new exception(esc_html__("You don't have permission to do this action", 'lottery-for-woocommerce'));
				}

				// Throw error, when the action type does pass in the request.
				if (empty($_POST['action_type'])) {
					throw new exception(esc_html__('Invalid Request', 'lottery-for-woocommerce'));
				}

				$importer = LTY_Import_Handler::get_importer(wc_clean(wp_unslash($_POST['action_type'])));
				// Throw error, when the action type does not have importer.
				if (!is_object($importer)) {
					throw new exception(esc_html__('Invalid Request', 'lottery-for-woocommerce'));
				}

				// Process the upload form.
				$importer->process_upload($_FILES);

				ob_start();
				$importer->output();
				$contents = ob_get_contents();
				ob_end_clean();

				wp_send_json_success(array( 'html' => $contents ));
			} catch (Exception $ex) {
				wp_send_json_error(array( 'error' => $ex->getMessage() ));
			}
		}

		/**
		 * Run import.
		 *
		 * @since 9.9.0
		 * @throws exception
		 */
		public static function run_import() {
			check_ajax_referer('lty-import', 'lty_security');

			try {
				// Throw error if the current user does not have permission.
				if (!current_user_can('import')) {
					throw new exception(esc_html__("You don't have permission to do import action", 'lottery-for-woocommerce'));
				}

				// Throw error, when the action type does pass in the request.
				if (empty($_POST['action_type'])) {
					throw new exception(esc_html__('Invalid Request', 'lottery-for-woocommerce'));
				}

				$importer = LTY_Import_Handler::get_importer(wc_clean(wp_unslash($_POST['action_type'])));
				// Throw error, when the action type does not have importer.
				if (!is_object($importer)) {
					throw new exception(esc_html__('Invalid Request', 'lottery-for-woocommerce'));
				}

				// Run import based on current position.
				$results = $importer->run_import();
				$percent_complete = $importer->get_percent_complete();

				if (100 == $percent_complete) {
					ob_start();
					$importer->get_move_next_step('import');
					$importer->output();
					$contents = ob_get_contents();
					ob_end_clean();

					$data = array( 'html' => $contents );
				} else {
					$data = array(
						'position' => $importer->get_position_count(),
						'percentage' => $percent_complete,
						'imported' => $importer->get_imported_count(),
						'updated' => $importer->get_updated_count(),
						'failed' => $importer->get_failed_count(),
					);
				}

				wp_send_json_success($data);
			} catch (Exception $ex) {
				wp_send_json_error(array( 'error' => $ex->getMessage() ));
			}
		}

		/**
		 * Manual lottery notification popup content.
		 *
		 * @since 12.4.0
		 * @throws exception
		 * */
		public static function manual_lottery_notification_popup_content() {
			check_ajax_referer( 'lty-manual-lottery-notification', 'lty_security' );

			try {
				// Return if the current user does not have permission.
				if ( ! current_user_can( 'edit_posts' ) ) {
					throw new exception( esc_html__( "You don't have permission to do this action", 'lottery-for-woocommerce' ) );
				}

				if ( ! isset( $_POST['product_id'] ) || empty( absint( $_POST['product_id'] ) ) ) {
					throw new exception( __( 'Invalid Product', 'lottery-for-woocommerce' ) );
				}

				$product = wc_get_product( absint( $_POST['product_id'] ) );
				if ( ! lty_is_lottery_product( $product ) ) {
					throw new exception( __('Invalid Product', 'lottery-for-woocommerce' ) );
				}

				ob_start();
				include LTY_ABSPATH . 'inc/admin/menu/views/backbone-modal/html-manual-lottery-notification-content.php';
				$contents = ob_get_contents();
				ob_end_clean();

				wp_send_json_success( array( 'html' => $contents ) );
			} catch ( Exception $ex ) {
				wp_send_json_error( array( 'error' => $ex->getMessage() ) );
			}
		}


		/**
		 * Manual lottery notification.
		 *
		 * @since 12.4.0
		 * @throws exception
		 * */
		public static function manual_lottery_notification() {
			check_ajax_referer( 'lty-manual-lottery-notification', 'lty_security' );

			try {
				// Return if the current user does not have permission.
				if ( ! current_user_can( 'edit_posts' ) ) {
					throw new exception( esc_html__( "You don't have permission to do this action", 'lottery-for-woocommerce' ) );
				}

				if ( ! isset( $_POST['product_id'] ) || empty( absint( $_POST['product_id'] ) ) ) {
					throw new exception( __( 'Invalid Product', 'lottery-for-woocommerce' ) );
				}

				$product = wc_get_product( absint( $_POST['product_id'] ) );
				if ( ! lty_is_lottery_product( $product ) ) {
					throw new exception( __('Invalid Product', 'lottery-for-woocommerce' ) );
				}

				$notification_id = isset( $_POST['notification_id'] ) && ! empty( $_POST['notification_id'] ) ? wc_clean( wp_unslash( $_POST['notification_id'] ) ) : '';
				if ( ! $notification_id ) {
					throw new exception( __( 'Invalid Notification', 'lottery-for-woocommerce' ) );
				}

				$notifications = LTY_Notification_Instances::get_notifications();
				if ( ! isset( $notifications[ $notification_id ] ) ) {
					throw new exception( __( 'Invalid Notification', 'lottery-for-woocommerce' ) );
				}

				switch ( $notification_id ) {
					case 'customer_lottery_started':
						LTY_Cron_Handler::schedule_lottery_started_emails( $product->get_id(), true );
						break;

					case 'customer_lottery_extended':
					case 'customer_unlimited_scheduled_lottery_extended':
						$notifications[ $notification_id ]->trigger( $product, true );
						break;

					case 'customer_winner':
						$notifications[ $notification_id ]->trigger( $product->get_id(), array(), true );
						break;

					default:
						$notifications[ $notification_id ]->trigger( $product->get_id(), true );
						break;
				}

				wp_send_json_success( array( 'notice' => __( 'Notification Sent Successfully.', 'lottery-for-woocommerce' ) ) );
			} catch ( Exception $ex ) {
				wp_send_json_error( array( 'error' => $ex->getMessage() ) );
			}
		}
	}

	LTY_Admin_Ajax::init();
}
