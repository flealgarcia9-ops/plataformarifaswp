<?php

/**
 * Lottery Product Type.
 * */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
if ( ! class_exists( 'LTY_Lottery_Product_Type_Handler' ) ) {

	/**
	 * Class.
	 * */
	class LTY_Lottery_Product_Type_Handler {

		/**
		 * Class initialization.
		 * */
		public static function init() {
			// Add Lottery Custom Product Selector.
			add_filter( 'product_type_selector', array( __CLASS__, 'add_custom_product_selector' ) );
			// Add tabs for product Lottery.
			add_filter( 'woocommerce_product_data_tabs', array( __CLASS__, 'product_lottery_tab' ) );
			// Lottery Product data Panel.
			add_action( 'woocommerce_product_data_panels', array( __CLASS__, 'product_data_panel' ) );
			// Save Lottery Product data Panel.
			add_action( 'woocommerce_process_product_meta_lottery', array( __CLASS__, 'save_lottery_product_data_options' ), 10, 1 );
			// Add Custom Meta Box to lottery product.
			add_action( 'add_meta_boxes', array( __CLASS__, 'lottery_view_log' ), 10 );
			// Update instant winner when product duplication.
			add_action( 'woocommerce_product_duplicate', array( __CLASS__, 'handle_product_duplication' ), 10, 2 );
		}

		/**
		 * Add Lottery Custom Product Selector.
		 * */
		public static function add_custom_product_selector( $product_selectors ) {
			// If resource is not array , declare empty array.
			if ( ! lty_check_is_array( $product_selectors ) ) {
				$product_selectors = array();
			}

			$product_selectors['lottery'] = __( 'Giveaway', 'lottery-for-woocommerce' );

			return $product_selectors;
		}

		/**
		 * Add tabs for product lottery.
		 * */
		public static function product_lottery_tab( $tabs ) {

			// If resource is not array , declare empty array
			if ( ! lty_check_is_array( $tabs ) ) {
				$tabs = array();
			}

			$tabs['general']['class'][]   = 'hide_if_lottery';
			$tabs['inventory']['class'][] = 'show_if_lottery';

			/**
			 * This hook is used to alter the lottery product tabs.
			 *
			 * @since 6.7
			 */
			$new_tabs = apply_filters(
				'lty_product_lottery_tab',
				array(
					'lty_lottery'                     => array(
						'label'  => __( 'Giveaway', 'lottery-for-woocommerce' ),
						'target' => 'lty_lottery_tab',
						'class'  => array( 'show_if_lottery active' ),
					),
					'lty_question'                    => array(
						'label'  => __( 'Q & A', 'lottery-for-woocommerce' ),
						'target' => 'lty_question_tab',
						'class'  => array( 'show_if_lottery active' ),
					),
					'lty_predefined_buttons'          => array(
						'label'  => __( 'Predefined Buttons', 'lottery-for-woocommerce' ),
						'target' => 'lty_predefined_buttons_tab',
						'class'  => array( 'show_if_lottery active' ),
					),
					'lty_lottery_relist'              => array(
						'label'  => __( 'Automatic Relisting', 'lottery-for-woocommerce' ),
						'target' => 'lty_lottery_relist_tab',
						'class'  => array( 'show_if_lottery active' ),
					),
					'lty_instant_winner'              => array(
						'label'  => __( 'Instant Win Prizes', 'lottery-for-woocommerce' ),
						'target' => 'lty_instant_winner_tab',
						'class'  => array( 'show_if_lottery active' ),
					),
					'lty_instant_winner_prize_groups' => array(
						'label'  => __( 'Instant Win Prize Groups', 'lottery-for-woocommerce' ),
						'target' => 'lty_instant_winner_prize_groups_tab',
						'class'  => array( 'show_if_lottery active' ),
					),
				)
			);

			$tabs = array_merge( $new_tabs, $tabs );

			return $tabs;
		}

		/**
		 * Lottery Product data Panel.
		 * */
		public static function product_data_panel() {
			global $post, $thepostid, $product_object;

			$tabs = array(
				'lottery',
				'question',
				'predefined-buttons',
				'lottery-relist',
				'instant-winner',
				'instant-winner-prize-groups',
			);

			foreach ( $tabs as $tab ) {
				$wrapper_class_name = array( 'lty_lottery_product_tab' );
				if ( is_callable( array( $product_object, 'has_lottery_status' ) ) ) {
					if ( $product_object->get_lty_lottery_status() && ! $product_object->has_lottery_status( array( 'lty_lottery_not_started', 'lty_lottery_started' ) ) ) {
						$wrapper_class_name[] = 'lty_lottery_closed_product_tab';
					}
				}

				$wrapper_class_name = implode( ' ', $wrapper_class_name );

				include 'views/html-product-data-' . $tab . '.php';
			}
		}

		/**
		 * Lottery Product data options as meta data.
		 *
		 * @since 1.0.0
		 * @param int $post_id
		 * @return void
		 */
		public static function save_lottery_product_data_options( $post_id ) {
			$product = wc_get_product( $post_id );
			if ( ! is_object( $product ) ) {
				return;
			}

			// Return if the lottery is not extended or relisted.
			if ( $product->is_closed() && ( isset( $_REQUEST['lty_lottery_extend'] ) || isset( $_REQUEST['lty_lottery_manual_relist'] ) ) ) {
				return;
			}

			$schedule_type = isset( $_REQUEST['lty_lottery_schedule_type'] )? wc_clean( wp_unslash( $_REQUEST['lty_lottery_schedule_type'] ) ) : '1';
			$start_date    = isset( $_REQUEST['_lty_start_date'] ) ? wc_clean( wp_unslash( $_REQUEST['_lty_start_date'] ) ) : '';
			$end_date      = '2' !== $schedule_type && isset( $_REQUEST['_lty_end_date'] ) ? wc_clean( wp_unslash( $_REQUEST['_lty_end_date'] ) ) : '';
			// Return if the lottery end date is less than the current date.
			if ( '2' !== $schedule_type && $product->is_closed() && ( strtotime( 'now' ) > strtotime( LTY_Date_Time::get_mysql_date_time_format( $end_date, false, 'UTC' ) ) ) ) {
				return;
			}

			$minimum_tickets                = isset( $_REQUEST['_lty_minimum_tickets'] ) ? wc_clean( wp_unslash( $_REQUEST['_lty_minimum_tickets'] ) ) : '';
			$maximum_tickets                = isset( $_REQUEST['_lty_maximum_tickets'] ) ? wc_clean( wp_unslash( $_REQUEST['_lty_maximum_tickets'] ) ) : '';
			$preset_tickets                 = isset( $_REQUEST['_lty_preset_tickets'] ) ? wc_clean( wp_unslash( $_REQUEST['_lty_preset_tickets'] ) ) : '';
			$minimum_tickets_per_user       = isset( $_REQUEST['_lty_user_minimum_tickets'] ) ? wc_clean( wp_unslash( $_REQUEST['_lty_user_minimum_tickets'] ) ) : '';
			$maximum_tickets_per_user       = isset( $_REQUEST['_lty_user_maximum_tickets'] ) ? wc_clean( wp_unslash( $_REQUEST['_lty_user_maximum_tickets'] ) ) : '';
			$ticket_price_type              = isset( $_REQUEST['_lty_ticket_price_type'] ) ? wc_clean( wp_unslash( $_REQUEST['_lty_ticket_price_type'] ) ) : '';
			$ticket_regular_price           = isset( $_REQUEST['_lty_regular_price'] ) ? wc_clean( wp_unslash( $_REQUEST['_lty_regular_price'] ) ) : '';
			$ticket_sale_price              = isset( $_REQUEST['_lty_sale_price'] ) ? wc_clean( wp_unslash( $_REQUEST['_lty_sale_price'] ) ) : '';
			$regular_price                  = ( '1' === $ticket_price_type ) ? wc_format_decimal( $ticket_regular_price ) : '';
			$sale_price                     = ( '1' === $ticket_price_type ) ? wc_format_decimal( $ticket_sale_price ) : '';
			$ticket_shuffled_start_number   = isset( $_REQUEST['_lty_ticket_shuffled_start_number'] ) ? ( wc_clean( wp_unslash( $_REQUEST['_lty_ticket_shuffled_start_number'] ) ) ) : 1;
			$ticket_sequential_start_number = isset( $_REQUEST['_lty_ticket_sequential_start_number'] ) ? ( wc_clean( wp_unslash( $_REQUEST['_lty_ticket_sequential_start_number'] ) ) ) : 1;
			$tickets_per_tab                = isset( $_REQUEST['_lty_tickets_per_tab'] ) ? wc_clean( wp_unslash( $_REQUEST['_lty_tickets_per_tab'] ) ) : '';
			$ticket_start_number            = isset( $_REQUEST['_lty_ticket_start_number'] ) ? ( wc_clean( wp_unslash( $_REQUEST['_lty_ticket_start_number'] ) ) ) : 1;
			$selected_gift_products         = isset( $_REQUEST['_lty_selected_gift_products'] ) ? wc_clean( wp_unslash( $_REQUEST['_lty_selected_gift_products'] ) ) : array();
			$question_answer_attempts       = isset( $_REQUEST['_lty_question_answer_attempts'] ) ? wc_clean( wp_unslash( $_REQUEST['_lty_question_answer_attempts'] ) ) : '1';
			if ( '3' === get_option( 'lty_settings_guest_user_participate_type' ) ) {
				$minimum_tickets_per_user = $minimum_tickets;
				$maximum_tickets_per_user = $maximum_tickets;
			}

			$_request                  = $_REQUEST;
			$winner_outside_gift_items = isset( $_request['_lty_winner_outside_gift_items'] ) ? $_request['_lty_winner_outside_gift_items'] : '';
			$meta_data                 = array(
				'_lty_lottery_schedule_type'               => $schedule_type,
				'_lty_start_date'                          => $start_date,
				'_lty_start_date_gmt'                      => $start_date ? LTY_Date_Time::get_mysql_date_time_format( $start_date, false, 'UTC' ) : '',
				'_lty_minimum_tickets'                     => wc_format_decimal( $minimum_tickets ),
				'_lty_maximum_tickets'                     => wc_format_decimal( $maximum_tickets ),
				'_lty_ticket_range_slider_type'            => isset( $_REQUEST['_lty_ticket_range_slider_type'] ) ? wc_clean( wp_unslash( $_REQUEST['_lty_ticket_range_slider_type'] ) ) : '',
				'_lty_preset_tickets'                      => wc_format_decimal( $preset_tickets ),
				'_lty_order_maximum_tickets'               => isset( $_REQUEST['_lty_order_maximum_tickets'] ) ? wc_clean( wp_unslash( $_REQUEST['_lty_order_maximum_tickets'] ) ) : '',
				'_lty_user_minimum_tickets'                => wc_format_decimal( $minimum_tickets_per_user ),
				'_lty_user_maximum_tickets'                => wc_format_decimal( $maximum_tickets_per_user ),
				'_lty_ticket_price_type'                   => $ticket_price_type,
				'_lty_regular_price'                       => $regular_price,
				'_regular_price'                           => $regular_price,
				'_lty_sale_price'                          => $sale_price,
				'_sale_price'                              => $sale_price,
				'_lty_ticket_generation_type'              => isset( $_REQUEST['_lty_ticket_generation_type'] ) ? wc_clean( wp_unslash( $_REQUEST['_lty_ticket_generation_type'] ) ) : '',
				'_lty_ticket_number_type'                  => isset( $_REQUEST['_lty_ticket_number_type'] ) ? wc_clean( wp_unslash( $_REQUEST['_lty_ticket_number_type'] ) ) : '',
				'_lty_ticket_shuffled_start_number'        => '' !== $ticket_shuffled_start_number ? $ticket_shuffled_start_number : 1,
				'_lty_ticket_sequential_start_number'      => '' !== $ticket_sequential_start_number ? $ticket_sequential_start_number : 1,
				'_lty_lucky_dip'                           => isset( $_REQUEST['_lty_lucky_dip'] ) ? wc_clean( wp_unslash( $_REQUEST['_lty_lucky_dip'] ) ) : 'no',
				'_lty_lucky_dip_method_type'               => isset( $_REQUEST['_lty_lucky_dip_method_type'] ) ? wc_clean( wp_unslash( $_REQUEST['_lty_lucky_dip_method_type'] ) ) : '1',
				'_lty_hide_sold_tickets'                   => isset( $_REQUEST['_lty_hide_sold_tickets'] ) ? wc_clean( wp_unslash( $_REQUEST['_lty_hide_sold_tickets'] ) ) : '',
				'_lty_ticket_length'                       => isset( $_REQUEST['_lty_ticket_length'] ) ? wc_clean( wp_unslash( $_REQUEST['_lty_ticket_length'] ) ) : '',
				'_lty_ticket_prefix'                       => isset( $_REQUEST['_lty_ticket_prefix'] ) ? wc_clean( wp_unslash( $_REQUEST['_lty_ticket_prefix'] ) ) : '',
				'_lty_ticket_suffix'                       => isset( $_REQUEST['_lty_ticket_suffix'] ) ? wc_clean( wp_unslash( $_REQUEST['_lty_ticket_suffix'] ) ) : '',
				'_lty_alphabet_with_sequence_nos_enabled'  => isset( $_REQUEST['_lty_alphabet_with_sequence_nos_enabled'] ) ? wc_clean( wp_unslash( $_REQUEST['_lty_alphabet_with_sequence_nos_enabled'] ) ) : '',
				'_lty_alphabet_with_sequence_nos_type'     => isset( $_REQUEST['_lty_alphabet_with_sequence_nos_type'] ) ? wc_clean( wp_unslash( $_REQUEST['_lty_alphabet_with_sequence_nos_type'] ) ) : '',
				'_lty_tickets_per_tab'                     => ! empty( $tickets_per_tab ) ? $tickets_per_tab : 10,
				'_lty_tickets_per_tab_display_type'        => isset( $_REQUEST['_lty_tickets_per_tab_display_type'] ) ? wc_clean( wp_unslash( $_REQUEST['_lty_tickets_per_tab_display_type'] ) ) : '1',
				'_lty_view_more_tickets_per_tab'           => isset( $_REQUEST['_lty_view_more_tickets_per_tab'] ) ? wc_clean( wp_unslash( $_REQUEST['_lty_view_more_tickets_per_tab'] ) ) : '',
				'_lty_tickets_per_tab_view_more_count'     => isset( $_REQUEST['_lty_tickets_per_tab_view_more_count'] ) ? wc_clean( wp_unslash( $_REQUEST['_lty_tickets_per_tab_view_more_count'] ) ) : '',
				'_lty_ticket_start_number'                 => '' !== $ticket_start_number ? $ticket_start_number : 1,
				'_lty_winners_count'                       => isset( $_REQUEST['_lty_winners_count'] ) ? wc_clean( wp_unslash( $_REQUEST['_lty_winners_count'] ) ) : '',
				'_lty_lottery_unique_winners'              => isset( $_REQUEST['_lty_lottery_unique_winners'] ) ? wc_clean( wp_unslash( $_REQUEST['_lty_lottery_unique_winners'] ) ) : 'no',
				'_lty_winner_selection_method'             => isset( $_REQUEST['_lty_winner_selection_method'] ) ? wc_clean( wp_unslash( $_REQUEST['_lty_winner_selection_method'] ) ) : '',
				'_lty_winning_product_selection'           => isset( $_REQUEST['_lty_winning_product_selection'] ) ? wc_clean( wp_unslash( $_REQUEST['_lty_winning_product_selection'] ) ) : '',
				'_lty_selected_gift_products'              => array_filter( (array) $selected_gift_products, 'lty_array_filter' ),
				'_lty_winner_outside_gift_items'           => $winner_outside_gift_items,
				'_lty_manage_question'                     => isset( $_REQUEST['_lty_manage_question'] ) ? wc_clean( wp_unslash( $_REQUEST['_lty_manage_question'] ) ) : 'no',
				'_lty_force_answer'                        => isset( $_REQUEST['_lty_force_answer'] ) ? wc_clean( wp_unslash( $_REQUEST['_lty_force_answer'] ) ) : 'no',
				'_lty_restrict_incorrectly_selected_answer' => isset( $_REQUEST['_lty_restrict_incorrectly_selected_answer'] ) ? wc_clean( wp_unslash( $_REQUEST['_lty_restrict_incorrectly_selected_answer'] ) ) : 'no',
				'_lty_questions'                           => self::prepare_questions(),
				'_lty_validate_correct_answer'             => isset( $_REQUEST['_lty_validate_correct_answer'] ) ? wc_clean( wp_unslash( $_REQUEST['_lty_validate_correct_answer'] ) ) : 'no',
				'_lty_question_answer_display_type'        => isset( $_REQUEST['_lty_question_answer_display_type'] ) ? wc_clean( wp_unslash( $_REQUEST['_lty_question_answer_display_type'] ) ) : '1',
				'_lty_question_answer_first_option_as_default_option' => isset( $_REQUEST['_lty_question_answer_first_option_as_default_option'] ) ? wc_clean( $_REQUEST['_lty_question_answer_first_option_as_default_option'] ) : 'no',
				'_lty_question_answer_time_limit_type'     => isset( $_REQUEST['_lty_question_answer_time_limit_type'] ) ? wc_clean( wp_unslash( $_REQUEST['_lty_question_answer_time_limit_type'] ) ) : '1',
				'_lty_question_answer_time_limit'          => isset( $_REQUEST['_lty_question_answer_time_limit'] ) ? wc_clean( wp_unslash( $_REQUEST['_lty_question_answer_time_limit'] ) ) : array(),
				'_lty_verify_answer_type'                  => isset( $_REQUEST['_lty_verify_answer_type'] ) ? wc_clean( wp_unslash( $_REQUEST['_lty_verify_answer_type'] ) ) : '1',
				'_lty_question_answer_attempts'            => '' !== $question_answer_attempts ? $question_answer_attempts : 1,
				'_lty_question_answer_selection_type'      => isset( $_REQUEST['_lty_question_answer_selection_type'] ) ? wc_clean( wp_unslash( $_REQUEST['_lty_question_answer_selection_type'] ) ) : '1',
				'_lty_hide_countdown_timer_selection_type' => isset( $_REQUEST['_lty_hide_countdown_timer_selection_type'] ) ? wc_clean( wp_unslash( $_REQUEST['_lty_hide_countdown_timer_selection_type'] ) ) : '',
				'_lty_hide_countdown_timer_in_shop'        => isset( $_REQUEST['_lty_hide_countdown_timer_in_shop'] ) ? wc_clean( wp_unslash( $_REQUEST['_lty_hide_countdown_timer_in_shop'] ) ) : 'no',
				'_lty_hide_countdown_timer_in_single_product' => isset( $_REQUEST['_lty_hide_countdown_timer_in_single_product'] ) ? wc_clean( wp_unslash( $_REQUEST['_lty_hide_countdown_timer_in_single_product'] ) ) : 'no',
				'_lty_hide_progress_bar_selection_type'    => isset( $_REQUEST['_lty_hide_progress_bar_selection_type'] ) ? wc_clean( wp_unslash( $_REQUEST['_lty_hide_progress_bar_selection_type'] ) ) : '',
				'_lty_hide_progress_bar_in_shop'           => isset( $_REQUEST['_lty_hide_progress_bar_in_shop'] ) ? wc_clean( wp_unslash( $_REQUEST['_lty_hide_progress_bar_in_shop'] ) ) : 'no',
				'_lty_hide_progress_bar_in_single_product' => isset( $_REQUEST['_lty_hide_progress_bar_in_single_product'] ) ? wc_clean( wp_unslash( $_REQUEST['_lty_hide_progress_bar_in_single_product'] ) ) : 'no',
				'_lty_enable_predefined_buttons'           => isset( $_REQUEST['_lty_enable_predefined_buttons'] ) ? wc_clean( wp_unslash( $_REQUEST['_lty_enable_predefined_buttons'] ) ) : 'no',
				'_lty_predefined_buttons_discount_tag'     => isset( $_REQUEST['lty_predefined_buttons_discount_tag'] ) ? wc_clean( wp_unslash( $_REQUEST['lty_predefined_buttons_discount_tag'] ) ) : 'no',
				'_lty_predefined_buttons_label'            => isset( $_REQUEST['_lty_predefined_buttons_label'] ) ? wc_clean( wp_unslash( $_REQUEST['_lty_predefined_buttons_label'] ) ) : '',
				'_lty_predefined_buttons_badge_label'      => isset( $_REQUEST['_lty_predefined_buttons_badge_label'] ) ? wc_clean( wp_unslash( $_REQUEST['_lty_predefined_buttons_badge_label'] ) ) : '',
				'_lty_predefined_buttons_rule'             => isset( $_REQUEST['_lty_predefined_buttons_rule'] ) ? wc_clean( wp_unslash( $_REQUEST['_lty_predefined_buttons_rule'] ) ) : array(),
				'_lty_predefined_buttons_selection_type'   => isset( $_REQUEST['_lty_predefined_buttons_selection_type'] ) ? wc_clean( wp_unslash( $_REQUEST['_lty_predefined_buttons_selection_type'] ) ) : 1,
				'_lty_predefined_with_quantity_selector'   => isset( $_REQUEST['_lty_predefined_with_quantity_selector'] ) ? wc_clean( wp_unslash( $_REQUEST['_lty_predefined_with_quantity_selector'] ) ) : 'no',
				'_lty_range_slider_predefined_discount_tag' => isset( $_REQUEST['lty_range_slider_predefined_discount_tag'] ) ? wc_clean( wp_unslash( $_REQUEST['lty_range_slider_predefined_discount_tag'] ) ) : 'no',
				'_lty_range_slider_predefined_discount_label' => isset( $_REQUEST['lty_range_slider_predefined_discount_label'] ) ? wc_clean( wp_unslash( $_REQUEST['lty_range_slider_predefined_discount_label'] ) ) : '',
				'_manage_stock'                            => 'yes',
				'_lty_relist_finished_lottery'             => isset( $_REQUEST['_lty_relist_finished_lottery'] ) ? wc_clean( wp_unslash( $_REQUEST['_lty_relist_finished_lottery'] ) ) : 'no',
				'_lty_finished_lottery_relist_duration'    => isset( $_REQUEST['_lty_finished_lottery_relist_duration'] ) ? wc_clean( wp_unslash( $_REQUEST['_lty_finished_lottery_relist_duration'] ) ) : '',
				'_lty_finished_lottery_relist_pause'       => isset( $_REQUEST['_lty_finished_lottery_relist_pause'] ) ? wc_clean( wp_unslash( $_REQUEST['_lty_finished_lottery_relist_pause'] ) ) : 'no',
				'_lty_finished_lottery_relist_pause_duration' => isset( $_REQUEST['_lty_finished_lottery_relist_pause_duration'] ) ? wc_clean( wp_unslash( $_REQUEST['_lty_finished_lottery_relist_pause_duration'] ) ) : '',
				'_lty_finished_lottery_relist_count_type'  => isset( $_REQUEST['_lty_finished_lottery_relist_count_type'] ) ? wc_clean( wp_unslash( $_REQUEST['_lty_finished_lottery_relist_count_type'] ) ) : '1',
				'_lty_finished_lottery_relist_count'       => isset( $_REQUEST['_lty_finished_lottery_relist_count'] ) ? wc_clean( wp_unslash( $_REQUEST['_lty_finished_lottery_relist_count'] ) ) : '',
				'_lty_relist_failed_lottery'               => isset( $_REQUEST['_lty_relist_failed_lottery'] ) ? wc_clean( wp_unslash( $_REQUEST['_lty_relist_failed_lottery'] ) ) : 'no',
				'_lty_failed_lottery_relist_duration'      => isset( $_REQUEST['_lty_failed_lottery_relist_duration'] ) ? wc_clean( wp_unslash( $_REQUEST['_lty_failed_lottery_relist_duration'] ) ) : '',
				'_lty_failed_lottery_relist_pause'         => isset( $_REQUEST['_lty_failed_lottery_relist_pause'] ) ? wc_clean( wp_unslash( $_REQUEST['_lty_failed_lottery_relist_pause'] ) ) : 'no',
				'_lty_failed_lottery_relist_pause_duration' => isset( $_REQUEST['_lty_failed_lottery_relist_pause_duration'] ) ? wc_clean( wp_unslash( $_REQUEST['_lty_failed_lottery_relist_pause_duration'] ) ) : '',
				'_lty_failed_lottery_relist_count_type'    => isset( $_REQUEST['_lty_failed_lottery_relist_count_type'] ) ? wc_clean( wp_unslash( $_REQUEST['_lty_failed_lottery_relist_count_type'] ) ) : '1',
				'_lty_failed_lottery_relist_count'         => isset( $_REQUEST['_lty_failed_lottery_relist_count'] ) ? wc_clean( wp_unslash( $_REQUEST['_lty_failed_lottery_relist_count'] ) ) : '',
				'_lty_instant_winners'                     => isset( $_REQUEST['lty_instant_winners'] ) ? 'yes' : 'no',
				'_lty_display_instant_winner_image'        => isset( $_REQUEST['lty_display_instant_winner_image'] ) ? wc_clean( wp_unslash( $_REQUEST['lty_display_instant_winner_image'] ) ) : '2',
				'_lty_instant_winner_display_mode'         => isset( $_REQUEST['lty_instant_winner_display_mode'] ) ? wc_clean( wp_unslash( $_REQUEST['lty_instant_winner_display_mode'] ) ) : '1',
			);

			if ( '2' !== $schedule_type ) {
				$meta_data = array_merge(
					$meta_data,
					array(
						'_lty_end_date'     => $end_date,
						'_lty_end_date_gmt' => $end_date ? LTY_Date_Time::get_mysql_date_time_format( $end_date, false, 'UTC' ) : '',
					)
				);
			}

			// Update Lottery meta values in product postmeta.
			foreach ( $meta_data as $key => $value ) {
				update_post_meta( $post_id, $key, $value );
			}

			$product = wc_get_product( $post_id );

			$product_price = 0;
			if ( '1' == $ticket_price_type ) {
				$product_price = $product->is_on_sale() ? $product->get_lty_sale_price() : $product->get_lty_regular_price();
			}

			// Update product price.
			update_post_meta( $post_id, '_price', $product_price );
			if ( ! is_object( $product ) || ! lty_is_lottery_product( $product ) || $product->is_closed() ) {
				return;
			}

			$stock_quantity = absint( $maximum_tickets ) - $product->get_placed_ticket_count();
			// Set lottery stock.
			wc_update_product_stock( $post_id, $stock_quantity, 'set', true );

			if ( in_array( $product->get_status(), array( 'publish', 'private' ) ) ) {
				$status = $product->is_started() ? 'lty_lottery_started' : 'lty_lottery_not_started';
				$product->update_post_meta( 'lty_lottery_status', $status );
			}

			// Format and update ticket numbers for sequential and shuffle automatic numbers.
			// Which is used to optimize the loop for every time call of ticket numbers.
			if ('1'===$meta_data['_lty_ticket_generation_type'] && '1'!==$meta_data['_lty_ticket_number_type']) {
				$product->format_and_update_automatic_ticket_numbers();
			} else {
				$product->delete_post_meta('lty_formatted_automatic_ticket_numbers');
			}

			// Update instant winner log.
			self::maybe_update_instant_winner_log( $product );
			// Maybe update current list count for lottery tickets.
			self::maybe_update_lottery_ticket_current_list_count( $product );

			// Check the lottery is extended.
			if ( get_transient( 'lty_lottery_extended_' . $product->get_id() ) ) {

				/**
				 * This hook is used to do extra action after lottery extended.
				 *
				 * @since 8.2.0
				 */
				do_action( 'lty_lottery_after_extended', $product );

				delete_transient( 'lty_lottery_extended_' . $product->get_id() );
			}

			/**
			 * This hook is used to do extra action after lottery settings saved.
			 *
			 * @since 1.0
			 */
			do_action( 'lty_lottery_product_saved', $post_id );
		}

		/**
		 * Maybe update instant winner log.
		 *
		 * @since 9.5.0
		 * @param object $product Product object.
		 * @return void
		 * */
		public static function maybe_update_instant_winner_log( $product ) {
			if ( ! lty_is_lottery_product( $product ) ) {
				return;
			}

			$instant_winner_rule_ids = lty_get_instant_winner_rule_ids( $product->get_id() );
			if ( ! lty_check_is_array( $instant_winner_rule_ids ) ) {
				return;
			}

			$relist_count            = is_callable( array( $product, 'get_current_relist_count' ) ) ? $product->get_current_relist_count() : 0;
			$instant_winner_log_args = array(
				'lty_start_date'           => $product->get_lty_start_date(),
				'lty_end_date'             => $product->get_lty_end_date(),
				'lty_start_date_gmt'       => $product->get_lty_start_date_gmt(),
				'lty_end_date_gmt'         => $product->get_lty_end_date_gmt(),
				'lty_lottery_id'           => $product->get_id(),
				'lty_current_relist_count' => $relist_count,
			);

			foreach ( $instant_winner_rule_ids as $instant_winner_rule_id ) {
				$instant_winner_log_id = lty_get_instant_winner_log_id_by_rule_id( $instant_winner_rule_id, $relist_count );
				if ( ! $instant_winner_log_id ) {
					continue;
				}

				lty_update_instant_winner_log( $instant_winner_log_id, $instant_winner_log_args, array( 'post_parent' => $instant_winner_rule_id ) );
			}
		}

		/**
		 * Maybe update current list count for lottery tickets.
		 *
		 * @since 11.7.0
		 * @param object $product Product object.
		 * @return void
		 * */
		public static function maybe_update_lottery_ticket_current_list_count( $product ) {
			if ( '' !== $product->get_lty_list_count() ) {
				return;
			}

			$list_count = is_callable( array( $product, 'get_current_relist_count' ) ) ? intval( $product->get_current_relist_count() ) : 0;
			$start_date = ! empty( $product->get_lty_start_date() ) ? $product->get_lty_start_date() : LTY_Date_Time::get_date_time_object( 'now' )->format( 'Y-m-d H:i:s' );
			update_post_meta( $product->get_id(), '_lty_list_count', $list_count );
			update_post_meta( $product->get_id(), '_lty_start_date', $start_date );
			update_post_meta( $product->get_id(), '_lty_start_date_gmt', LTY_Date_Time::get_mysql_date_time_format( $start_date, false, 'UTC' ) );

			$ticket_ids = lty_get_ticket_ids(
				array(
					'product_id' => $product->get_id(),
					'start_date' => $product->get_current_start_date_gmt(),
					'end_date'   => $product->get_lty_end_date_gmt(),
				)
			);
			if ( ! lty_check_is_array( $ticket_ids ) ) {
				return;
			}

			foreach ( $ticket_ids as $ticket_id ) {
				$ticket = lty_get_lottery_ticket( $ticket_id );
				if ( ! $ticket->exists() ) {
					continue;
				}

				update_post_meta( $ticket_id, 'lty_list_count', $list_count );
			}
		}

		/**
		 * Prepare the Questions.
		 *
		 * @return array
		 * */
		public static function prepare_questions() {
			$formatted_questions = array();
			$questions           = isset( $_REQUEST['_lty_questions'] ) ? wc_clean( wp_unslash( $_REQUEST['_lty_questions'] ) ) : array();

			if ( ! lty_check_is_array( $questions ) ) {
				return $formatted_questions;
			}

			$questions = array_filter( array_merge( $questions ), 'lty_array_filter' );

			foreach ( $questions as $key => $question ) {
				$formatted_questions[ $key ]['question'] = isset( $question['question'] ) ? $question['question'] : '';

				if ( ! isset( $question['answers'] ) || ! lty_check_is_array( $question['answers'] ) ) {
					continue;
				}

				$answers = array_filter( array_merge( $question['answers'] ), 'lty_array_filter' );

				foreach ( $answers as $answer_key => $answer ) {
					$formatted_answer = array(
						'label' => isset( $answer['label'] ) ? $answer['label'] : '',
						'valid' => isset( $answer['valid'] ) ? 'yes' : 'no',
						'key'   => $answer_key + 1,
					);

					$formatted_questions[ $key ]['answers'][ $answer_key + 1 ] = $formatted_answer;
				}
			}

			return $formatted_questions;
		}

		/**
		 * Manually relist the Lottery product.
		 * */
		public static function manual_relist( $product_id ) {
			$product = wc_get_product( $product_id );

			if ( ! is_object( $product ) ) {
				throw new exception( esc_html__( 'Invalid Product', 'lottery-for-woocommerce' ) );
			}

			if ( 'lottery' !== $product->get_type() ) {
				throw new exception( esc_html__( 'Invalid Product Type', 'lottery-for-woocommerce' ) );
			}

			if ( ! $product->is_closed() || ! $product->has_lottery_status( array( 'lty_lottery_failed', 'lty_lottery_finished' ) ) ) {
				throw new exception( esc_html__( 'Not eligible to relist', 'lottery-for-woocommerce' ) );
			}

			// Manual relist the lottery.
			LTY_Lottery_Handler::relist_lottery( $product_id, $product );
		}

		/**
		 * Custom Meta box for lottery product view log.
		 * */
		public static function lottery_view_log() {
			global $post;

			if ( isset( $post->post_type ) && 'product' === $post->post_type ) {
				$product = wc_get_product( $post->ID );

				if ( is_object( $product ) && 'lottery' === $product->get_type() ) {
					add_meta_box( 'lty_lottery_view_log', __( 'Giveaway Status', 'lottery-for-woocommerce' ), array( __CLASS__, 'render_ticket_logs_link_content' ), array( 'page', 'post', 'product' ), 'side', 'low' );
				}
			}
		}

		/**
		 * Render ticket logs link content.
		 * */
		public static function render_ticket_logs_link_content() {
			include_once LTY_ABSPATH . 'inc/admin/menu/views/html-view-lottery-status.php';
		}

		/**
		 * Handle product duplication.
		 *
		 * @since 8.6.0
		 * @param object $duplicate_product duplicated product.
		 * @param object $product current product.
		 * @return void
		 */
		public static function handle_product_duplication( $duplicate_product, $product ) {
			// Return if duplicate product/current product is invalid.
			if ( ! is_object( $duplicate_product ) || ! is_object( $product ) || ! lty_is_lottery_product( $duplicate_product ) || ! lty_is_lottery_product( $product ) ) {
				return;
			}

			// Update instant winner rules to the duplicated product.
			$product->update_instant_winner_rules( $duplicate_product->get_id() );

			// Update instant winner prize groups to the duplicated product.
			$product->update_instant_winner_prize_groups( $duplicate_product->get_id() );
		}
	}

	LTY_Lottery_Product_Type_Handler::init();
}
