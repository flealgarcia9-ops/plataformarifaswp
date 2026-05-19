<?php

/**
 * Lottery product type data.
 *
 * @since 1.1.0
 * */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'WC_Product_Lottery_Data' ) ) {

	/**
	 * Class.
	 *
	 * @since 1.1.0
	 * */
	class WC_Product_Lottery_Data extends WC_Product {

		/**
		 * Product Type.
		 *
		 * @since 8.7.0
		 * @var string
		 */
		protected $product_type = 'lottery';

		/**
		 * Updated lottery IDs.
		 *
		 * @since 8.5.0
		 * @var array
		 */
		private static $updated_lottery_ids = array();

		/**
		 * Meta data keys.
		 *
		 * @var array.
		 * */
		protected $extra_data = array(
			'lty_lottery_status'                         => '',
			'lty_lottery_schedule_type'                  => '',
			'lty_start_date'                             => '',
			'lty_end_date'                               => '',
			'lty_start_date_gmt'                         => '',
			'lty_end_date_gmt'                           => '',
			'lty_minimum_tickets'                        => '',
			'lty_maximum_tickets'                        => '',
			'lty_ticket_range_slider_type'               => '1',
			'lty_preset_tickets'                         => '',
			'lty_order_maximum_tickets'                  => '',
			'lty_user_minimum_tickets'                   => '',
			'lty_user_maximum_tickets'                   => '',
			'lty_ticket_price_type'                      => '',
			'lty_regular_price'                          => '',
			'lty_sale_price'                             => '',
			'lty_ticket_generation_type'                 => '',
			'lty_formatted_automatic_ticket_numbers'     => array(),
			'lty_ticket_number_type'                     => '',
			'lty_choose_ticket_numbers'                  => '',
			'lty_ticket_sequential_start_number'         => '',
			'lty_ticket_shuffled_start_number'           => '',
			'lty_ticket_prefix'                          => '',
			'lty_ticket_suffix'                          => '',
			'lty_alphabet_with_sequence_nos_enabled'     => '',
			'lty_alphabet_with_sequence_nos_type'        => '',
			'lty_tickets_per_tab'                        => '',
			'lty_tickets_per_tab_display_type'           => '',
			'lty_view_more_tickets_per_tab'              => '',
			'lty_tickets_per_tab_view_more_count'        => '',
			'lty_lucky_dip'                              => '',
			'lty_lucky_dip_method_type'                  => '',
			'lty_hide_sold_tickets'                      => '',
			'lty_lottery_unique_winners'                 => '',
			'lty_winners_count'                          => '',
			'lty_winner_selection_method'                => '',
			'lty_winning_product_selection'              => '',
			'lty_selected_gift_products'                 => array(),
			'lty_winner_outside_gift_items'              => '',
			'lty_manage_question'                        => '',
			'lty_questions'                              => array(),
			'lty_force_answer'                           => '',
			'lty_restrict_incorrectly_selected_answer'   => '',
			'lty_validate_correct_answer'                => '',
			'lty_question_answer_display_type'           => '1',
			'lty_question_answer_first_option_as_default_option' => '',
			'lty_question_answer_time_limit_type'        => '1',
			'lty_question_answer_time_limit'             => array(
				'unit'   => 'minutes',
				'number' => 5,
			),
			'lty_verify_answer_type'                     => '',
			'lty_question_answer_viewed_data'            => array(),
			'lty_question_answer_attempts_data'          => array(),
			'lty_question_answer_selection_type'         => '',
			'lty_question_answer_attempts'               => '1',
			'lty_incorrect_answer_user_ids'              => array(),
			'lty_ticket_count'                           => '',
			'lty_ticket_start_number'                    => '',
			'lty_closed'                                 => '',
			'lty_closed_date'                            => '',
			'lty_closed_date_gmt'                        => '',
			'lty_failed_reason'                          => '',
			'lty_failed_date'                            => '',
			'lty_failed_date_gmt'                        => '',
			'lty_relisted'                               => '',
			'lty_relists'                                => '',
			'lty_relisted_date'                          => '',
			'lty_relisted_date_gmt'                      => '',
			'lty_list_count'                             => '',
			'lty_finished_date'                          => '',
			'lty_finished_date_gmt'                      => '',
			'lty_manual_reserved_tickets'                => array(),
			'lty_hide_countdown_timer_selection_type'    => '',
			'lty_hide_countdown_timer_in_shop'           => '',
			'lty_hide_countdown_timer_in_single_product' => '',
			'lty_hide_progress_bar_selection_type'       => '',
			'lty_hide_progress_bar_in_shop'              => '',
			'lty_hide_progress_bar_in_single_product'    => '',
			'lty_enable_predefined_buttons'              => '',
			'lty_predefined_buttons_discount_tag'        => '',
			'lty_predefined_buttons_label'               => '',
			'lty_predefined_buttons_badge_label'         => '',
			'lty_predefined_buttons_rule'                => array(),
			'lty_predefined_buttons_selection_type'      => '',
			'lty_predefined_with_quantity_selector'      => '',
			'lty_range_slider_predefined_discount_tag'   => '',
			'lty_range_slider_predefined_discount_label' => '',
			'lty_relist_finished_lottery'                => '',
			'lty_finished_lottery_relist_duration'       => '',
			'lty_finished_lottery_relist_pause'          => '',
			'lty_finished_lottery_relist_pause_duration' => '',
			'lty_finished_lottery_relist_count_type'     => '',
			'lty_finished_lottery_relist_count'          => '',
			'lty_finished_relisted_count'                => '',
			'lty_relist_failed_lottery'                  => '',
			'lty_failed_lottery_relist_duration'         => '',
			'lty_failed_lottery_relist_pause'            => '',
			'lty_failed_lottery_relist_pause_duration'   => '',
			'lty_failed_lottery_relist_count_type'       => '',
			'lty_failed_lottery_relist_count'            => '',
			'lty_failed_relisted_count'                  => '',
			'lty_instant_winners'                        => '',
			'lty_display_instant_winner_image'           => '',
			'lty_instant_winner_display_mode'            => '',
			'lty_ending_soon_user_email_sent'            => '',
		);

		/**
		 * Initialize Lottery product.
		 *
		 * @param WC_Product|int $product Product instance or ID.
		 * */
		public function __construct( $product = 0 ) {

			$this->product_type = 'lottery';

			parent::__construct( $product );

			$this->update_lottery( $product );
		}

		/**
		 * Update lottery.
		 *
		 * @return void
		 * */
		public function update_lottery( $product ) {
			// Omit the update of current lottery if already updated for the current page.
			if ( in_array( $this->get_id(), self::$updated_lottery_ids, true ) ) {
				return;
			}

			$action = LTY_Lottery_Handler::handle_lottery_product( $this->get_id(), $this );

			if ( $action ) {
				parent::__construct( $product );
			}

			self::$updated_lottery_ids[] = $this->get_id();
		}

		/**
		 * Get internal type.
		 *
		 * @return string
		 * */
		public function get_type() {
			return 'lottery';
		}

		/**
		 * Has Lottery Status?.
		 *
		 * @return bool
		 * */
		public function has_lottery_status( $status ) {
			$current_status = $this->get_lty_lottery_status();

			if ( is_array( $status ) && in_array( $current_status, $status ) ) {
				return true;
			}

			if ( $current_status == $status ) {
				return true;
			}

			return false;
		}

		/**
		 * Update Lottery Status.
		 *
		 * @return string
		 * */
		public function update_lottery_status( $status_name ) {
			return $this->update_post_meta( 'lty_lottery_status', $status_name );
		}

		/**
		 * Get post meta.
		 *
		 * @return string
		 * */
		public function get_post_meta( $key ) {
			return get_post_meta( $this->get_id(), sanitize_key( '_' . $key ), true );
		}

		/**
		 * Update post meta.
		 *
		 * @return string
		 * */
		public function update_post_meta( $key, $value ) {
			return lty_update_lottery_post_meta( $this->get_id(), $key, $value );
		}

		/**
		 * Delete post meta.
		 *
		 * @since 11.2.0
		 * @param string $key 
		 * */
		public function delete_post_meta( $key ) {
			return lty_delete_lottery_post_meta( $this->get_id(), $key );
		}

		/**
		 * Get lottery id.
		 *
		 * @return int
		 */
		public function get_lottery_id() {
			return lty_get_lottery_id( $this->get_id() );
		}

		/**
		 * Get lottery data.
		 *
		 * @return array
		 */
		public function get_lottery_data() {
			$lottery_product_data = $this->get_data();
			foreach ( $lottery_product_data as $key => $value ) {

				if ( in_array( $key, $this->get_lottery_keys() ) ) {
					continue;
				}

				unset( $lottery_product_data[ $key ] );
			}

			/**
			 * This hook is used to alter the lottery product data.
			 *
			 * @since 1.0
			 */
			return apply_filters( 'lty_lottery_product_data', $lottery_product_data );
		}

		/**
		 * Get relist duration.
		 *
		 * @since 7.5.0
		 *
		 * @return array
		 */
		public function get_relist_duration() {
			switch ( $this->get_lty_lottery_status() ) {
				case 'lty_lottery_finished':
					$relist_duration = $this->get_lty_finished_lottery_relist_duration();
					break;
				case 'lty_lottery_failed':
					$relist_duration = $this->get_lty_failed_lottery_relist_duration();
					break;
			}

			/**
			 * This hook is used to alter the lottery relist duration.
			 *
			 * @since 7.5.0
			 */
			return apply_filters( 'lty_lottery_relist_duration', $relist_duration );
		}

		/**
		 * Update automatic relist count
		 *
		 * @since 7.5.0
		 *
		 * @return void
		 */
		public function update_automatic_relisted_count() {
			switch ( $this->get_lty_lottery_status() ) {
				case 'lty_lottery_finished':
					$finished_relist_count = intval( $this->get_lty_finished_relisted_count() );
					$this->update_post_meta( 'lty_finished_relisted_count', $finished_relist_count + 1 );
					break;
				case 'lty_lottery_failed':
					$failed_relist_count = intval( $this->get_lty_failed_relisted_count() );
					$this->update_post_meta( 'lty_failed_relisted_count', $failed_relist_count + 1 );
					break;
			}
		}

		/**
		 * Get lottery keys.
		 *
		 * @return array
		 */
		public function get_lottery_keys() {
			$meta_keys = array( 'regular_price', 'sale_price', 'manage_stock', 'price' );

			/**
			 * This hook is used to alter the lottery product keys.
			 *
			 * @since 1.0
			 */
			return apply_filters( 'lty_lottery_product_keys', array_merge( $this->get_extra_data_keys(), $meta_keys ) );
		}

		/*
			|--------------------------------------------------------------------------
			| Setters
			|--------------------------------------------------------------------------
			|
			| Functions for setting product data. These should not update anything in the
			| database itself and should only change what is stored in the class
			| object.
		 */

		/**
		 * Set schedule type.
		 *
		 * @since 11.7.0
		 * @param string $schedule_type Schedule type.
		 * */
		public function set_lty_lottery_schedule_type( $schedule_type ) {
			$this->set_prop( 'lty_lottery_schedule_type', $schedule_type );
		}

		/**
		 * Set start date.
		 *
		 * @param string $start_date start date.
		 * */
		public function set_lty_start_date( $start_date ) {
			$this->set_prop( 'lty_start_date', $start_date );
		}

		/**
		 * Set end date.
		 *
		 * @param string $end_date end date.
		 * */
		public function set_lty_end_date( $end_date ) {
			$this->set_prop( 'lty_end_date', $end_date );
		}

		/**
		 * Set start date gmt.
		 *
		 * @param string $start_date_gmt start date gmt.
		 * */
		public function set_lty_start_date_gmt( $start_date_gmt ) {
			$this->set_prop( 'lty_start_date_gmt', $start_date_gmt );
		}

		/**
		 * Set end date gmt.
		 *
		 * @param string $end_date_gmt end date gmt.
		 * */
		public function set_lty_end_date_gmt( $end_date_gmt ) {
			$this->set_prop( 'lty_end_date_gmt', $end_date_gmt );
		}

		/**
		 * Set minimum tickets.
		 *
		 * @param string $minimum_tickets minimum tickets.
		 * */
		public function set_lty_minimum_tickets( $minimum_tickets ) {
			$this->set_prop( 'lty_minimum_tickets', $minimum_tickets );
		}

		/**
		 * Set maximum tickets.
		 *
		 * @param string $maximum_tickets maximum tickets.
		 * */
		public function set_lty_maximum_tickets( $maximum_tickets ) {
			$this->set_prop( 'lty_maximum_tickets', $maximum_tickets );
		}

		/**
		 * Set ticket range slider type.
		 *
		 * @since 7.5.0
		 * @param string $ticket_range_slider_type.
		 * */
		public function set_lty_ticket_range_slider_type( $ticket_range_slider_type ) {
			$this->set_prop( 'lty_ticket_range_slider_type', $ticket_range_slider_type );
		}

		/**
		 * Set preset tickets.
		 *
		 * @since 7.5.0
		 * @param string $preset_tickets preset tickets.
		 * */
		public function set_lty_preset_tickets( $preset_tickets ) {
			$this->set_prop( 'lty_preset_tickets', $preset_tickets );
		}

		/**
		 * Set user minimum tickets.
		 *
		 * @param string $minimum_tickets_per_user minimum tickets per user.
		 * */
		public function set_lty_user_minimum_tickets( $minimum_tickets_per_user ) {
			$this->set_prop( 'lty_user_minimum_tickets', $minimum_tickets_per_user );
		}

		/**
		 * Set user maximum tickets.
		 *
		 * @param string $maximum_tickets_per_user maximum tickets per user.
		 * */
		public function set_lty_user_maximum_tickets( $maximum_tickets_per_user ) {
			$this->set_prop( 'lty_user_maximum_tickets', $maximum_tickets_per_user );
		}

		/**
		 * Set order maximum tickets.
		 *
		 * @param string $maximum_tickets_per_order maximum tickets per order.
		 * */
		public function set_lty_order_maximum_tickets( $maximum_tickets_per_order ) {
			$this->set_prop( 'lty_order_maximum_tickets', $maximum_tickets_per_order );
		}

		/**
		 * Set ticket price type.
		 *
		 * @param string $price_type lottery product price type.
		 * */
		public function set_lty_ticket_price_type( $price_type ) {
			$this->set_prop( 'lty_ticket_price_type', $price_type );
		}

		/**
		 * Set regular price.
		 *
		 * @param string $regular_price lottery product regular price.
		 * */
		public function set_lty_regular_price( $regular_price ) {
			$this->set_prop( 'lty_regular_price', $regular_price );
		}

		/**
		 * Set sale price.
		 *
		 * @param string $sale_price lottery product sale price.
		 * */
		public function set_lty_sale_price( $sale_price ) {
			$this->set_prop( 'lty_sale_price', $sale_price );
		}

		/**
		 * Set choose ticket generation type.
		 *
		 * @param string $ticket_generation_type ticket generation type.
		 * */
		public function set_lty_ticket_generation_type( $ticket_generation_type ) {
			$this->set_prop( 'lty_ticket_generation_type', $ticket_generation_type );
		}

		/**
		 * Set formatted automatic ticket numbers.
		 *
		 * @since 11.2.0
		 * @param array $formatted_automatic_ticket_numbers
		 * */
		public function set_lty_formatted_automatic_ticket_numbers( $formatted_automatic_ticket_numbers ) {
			$this->set_prop( 'lty_formatted_automatic_ticket_numbers', $formatted_automatic_ticket_numbers );
		}

		/**
		 * Set choose ticket number type.
		 *
		 * @param string $ticket_number_type ticket number type.
		 * */
		public function set_lty_ticket_number_type( $ticket_number_type ) {
			$this->set_prop( 'lty_ticket_number_type', $ticket_number_type );
		}

		/**
		 * Set choose ticket numbers.
		 *
		 * @param string $choose_ticket_numbers user to choose ticket numbers.
		 * */
		public function set_lty_choose_ticket_numbers( $choose_ticket_numbers ) {
			$this->set_prop( 'lty_choose_ticket_numbers', $choose_ticket_numbers );
		}

		/**
		 * Set sequential start ticket number.
		 *
		 * @param string $allow_sequential_ticket_numbers allow sequential ticket numbers.
		 * */
		public function set_lty_ticket_sequential_start_number( $sequential_ticket_number ) {
			$this->set_prop( 'lty_ticket_sequential_start_number', $sequential_ticket_number );
		}

		/**
		 * Set shuffled start ticket number.
		 *
		 * @param string $shuffled_ticket_number allow shuffled start ticket numbers.
		 * */
		public function set_lty_ticket_shuffled_start_number( $shuffled_ticket_number ) {
			$this->set_prop( 'lty_ticket_shuffled_start_number', $shuffled_ticket_number );
		}

		/**
		 * Set ticket prefix.
		 *
		 * @param string.
		 * */
		public function set_lty_ticket_prefix( $ticket_prefix ) {
			$this->set_prop( 'lty_ticket_prefix', $ticket_prefix );
		}

		/**
		 * Set ticket suffix.
		 *
		 * @param string.
		 * */
		public function set_lty_ticket_suffix( $ticket_suffix ) {
			$this->set_prop( 'lty_ticket_suffix', $ticket_suffix );
		}

		/**
		 * Set alphabet with sequence numbers enabled.
		 *
		 * @param string $alphabet_with_sequence_nos_enabled.
		 * */
		public function set_lty_alphabet_with_sequence_nos_enabled( $alphabet_with_sequence_nos_enabled ) {
			$this->set_prop( 'lty_alphabet_with_sequence_nos_enabled', $alphabet_with_sequence_nos_enabled );
		}

		/**
		 * Set alphabet with sequence numbers type.
		 *
		 * @param string $alphabet_with_sequence_nos_type.
		 * */
		public function set_lty_alphabet_with_sequence_nos_type( $alphabet_with_sequence_nos_type ) {
			$this->set_prop( 'lty_alphabet_with_sequence_nos_type', $alphabet_with_sequence_nos_type );
		}

		/**
		 * Set tickets per tab.
		 *
		 * @param string $tickets_per_tab lottery tickets per tab.
		 * */
		public function set_lty_tickets_per_tab( $tickets_per_tab ) {
			$this->set_prop( 'lty_tickets_per_tab', $tickets_per_tab );
		}

		/**
		 * Set lucky dip.
		 *
		 * @param string $lucky_dip enable lucky dip.
		 * */
		public function set_lty_lucky_dip( $lucky_dip ) {
			$this->set_prop( 'lty_lucky_dip', $lucky_dip );
		}

		/**
		 * Set lucky dip method type.
		 *
		 * @since 10.4.0
		 * @param string $lucky_dip_method_type Lucky dip method type.
		 * */
		public function set_lty_lucky_dip_method_type( $lucky_dip_method_type ) {
			$this->set_prop( 'lty_lucky_dip_method_type', $lucky_dip_method_type );
		}

		/**
		 * Set hide sold tickets in per tab.
		 *
		 * @since 8.9.0
		 * @param string $value
		 * */
		public function set_lty_hide_sold_tickets( $value ) {
			$this->set_prop( 'lty_hide_sold_tickets', $value );
		}

				/**
				 * Set lottery unique winners.
				 *
				 * @since 9.6.0
				 * @param string $value Whether to charge or not.
				 * @return void
				 * */
		public function set_lty_lottery_unique_winners( $value ) {
			$this->set_prop( 'lty_lottery_unique_winners', $value );
		}

		/**
		 * Set winners count.
		 *
		 * @param string $number_of_winners number of lottery winners.
		 * */
		public function set_lty_winners_count( $number_of_winners ) {
			$this->set_prop( 'lty_winners_count', $number_of_winners );
		}

		/**
		 * Set winner selection method.
		 *
		 * @param string $selection_method manual/automatic selection method for winners.
		 * */
		public function set_lty_winner_selection_method( $selection_method ) {
			$this->set_prop( 'lty_winner_selection_method', $selection_method );
		}

		/**
		 * Set winning product selection.
		 *
		 * @param string $product_selection inside/outside the site item selection for lottery products.
		 * */
		public function set_lty_winning_product_selection( $product_selection ) {
			$this->set_prop( 'lty_winning_product_selection', $product_selection );
		}

		/**
		 * Set item selection.
		 *
		 * @param array $gift_products select gift products for lottery winners.
		 * */
		public function set_lty_selected_gift_products( $gift_products ) {
			$this->set_prop( 'lty_selected_gift_products', $gift_products );
		}

		/**
		 * Set winner outside gift items.
		 *
		 * @param string $outside_items outside gift items.
		 * */
		public function set_lty_winner_outside_gift_items( $outside_items ) {
			$this->set_prop( 'lty_winner_outside_gift_items', $outside_items );
		}

		/**
		 * Set manage question.
		 *
		 * @param string $manage_question manage the question.
		 * */
		public function set_lty_manage_question( $manage_question ) {
			$this->set_prop( 'lty_manage_question', $manage_question );
		}

		/**
		 * Set force answer.
		 *
		 * @param string $force_answer force the answer.
		 * */
		public function set_lty_force_answer( $force_answer ) {
			$this->set_prop( 'lty_force_answer', $force_answer );
		}

		/**
		 * Set Incorrectly selected answer restriction.
		 *
		 * @param string $incorrectly_selected_answer incorrectly selected answer restriction.
		 * */
		public function set_lty_restrict_incorrectly_selected_answer( $incorrectly_selected_answer ) {
			$this->set_prop( 'lty_restrict_incorrectly_selected_answer', $incorrectly_selected_answer );
		}

		/**
		 * Set correct answer.
		 *
		 * @param string $validate_correct_answer validate the correct answer.
		 * */
		public function set_lty_validate_correct_answer( $validate_correct_answer ) {
			$this->set_prop( 'lty_validate_correct_answer', $validate_correct_answer );
		}

		/**
		 * Set question answer display type.
		 *
		 * @since 8.2.0
		 * @param string $value
		 * */
		public function set_lty_question_answer_display_type( $value ) {
			$this->set_prop( 'lty_question_answer_display_type', $value );
		}

		/**
		 * Set question answer first dropdown option as default option.
		 *
		 * @since 10.2.0
		 * @param string $value Whether to select first dropdown option or not.
		 * */
		public function set_lty_question_answer_first_option_as_default_option( $value ) {
			$this->set_prop( 'lty_question_answer_first_option_as_default_option', $value );
		}

		/**
		 * Set question answer time limit type.
		 *
		 * @param string $question_answer_time_limit_type question answer time limit type.
		 * */
		public function set_lty_question_answer_time_limit_type( $question_answer_time_limit_type ) {
			$this->set_prop( 'lty_question_answer_time_limit_type', $question_answer_time_limit_type );
		}

		/**
		 * Set question answer time limit.
		 *
		 * @param array $question_answer_time_limit question answer time limit.
		 * */
		public function set_lty_question_answer_time_limit( $question_answer_time_limit ) {
			$this->set_prop( 'lty_question_answer_time_limit', $question_answer_time_limit );
		}

		/**
		 * Set verify answer type.
		 *
		 * @param string $verify_answer_type verify answer type.
		 * */
		public function set_lty_verify_answer_type( $verify_answer_type ) {
			$this->set_prop( 'lty_verify_answer_type', $verify_answer_type );
		}

		/**
		 * Set question answer viewed data.
		 *
		 * @param array $question_answer_viewed_data question answer viewed data.
		 * */
		public function set_lty_question_answer_viewed_data( $question_answer_viewed_data ) {
			$this->set_prop( 'lty_question_answer_viewed_data', $question_answer_viewed_data );
		}

		/**
		 * Set question answer attempts data.
		 *
		 * @param array $question_answer_attempts_data question answer attempts data.
		 * */
		public function set_lty_question_answer_attempts_data( $question_answer_attempts_data ) {
			$this->set_prop( 'lty_question_answer_attempts_data', $question_answer_attempts_data );
		}

		/**
		 * Set question answer selection type.
		 *
		 * @param $selection_type selection type for QA.
		 * */
		public function set_lty_question_answer_selection_type( $selection_type ) {
			$this->set_prop( 'lty_question_answer_selection_type', $selection_type );
		}

		/**
		 * Set question answer attempts.
		 *
		 * @param $attempts question answer attempts.
		 * */
		public function set_lty_question_answer_attempts( $attempts ) {
			$this->set_prop( 'lty_question_answer_attempts', $attempts );
		}

		/**
		 * Set incorrect answer userids.
		 *
		 * @param array $incorrect_answer_user_id incorrect answer userids.
		 * */
		public function set_lty_incorrect_answer_user_ids( $incorrect_answer_user_ids ) {
			$this->set_prop( 'lty_incorrect_answer_user_ids', $incorrect_answer_user_ids );
		}

		/**
		 * Set questions.
		 *
		 * @param array $questions.
		 * */
		public function set_lty_questions( $questions ) {
			$this->set_prop( 'lty_questions', $questions );
		}

		/**
		 * Set Lottery Ticket count.
		 *
		 * @param string $number_of_winners number of lottery winners.
		 * */
		public function set_lty_ticket_count( $number_of_tickets ) {
			$this->set_prop( 'lty_ticket_count', $number_of_tickets );
		}

		/**
		 * Set Lottery Ticket start number.
		 *
		 * @param int $start_number ticket start number.
		 * */
		public function set_lty_ticket_start_number( $start_number ) {
			$this->set_prop( 'lty_ticket_start_number', $start_number );
		}

		/**
		 * Set Lottery status.
		 *
		 * @param string $status lottery status.
		 * */
		public function set_lty_lottery_status( $status ) {
			$this->set_prop( 'lty_lottery_status', $status );
		}

		/**
		 * Set Lottery Closed.
		 *
		 * @param string $closed_type lottery closed type.
		 * */
		public function set_lty_closed( $closed_type ) {
			$this->set_prop( 'lty_closed', $closed_type );
		}

		/**
		 * Set Lottery closed date.
		 *
		 * @param string $closed_date lottery closed date.
		 * */
		public function set_lty_closed_date( $closed_date ) {
			$this->set_prop( 'lty_closed_date', $closed_date );
		}

		/**
		 * Set Lottery closed date gmt.
		 *
		 * @param string $closed_date_gmt lottery closed date gmt.
		 * */
		public function set_lty_closed_date_gmt( $closed_date_gmt ) {
			$this->set_prop( 'lty_closed_date_gmt', $closed_date_gmt );
		}

		/**
		 * Set Lottery failed reason.
		 *
		 * @param string $reason lottery failed reason.
		 * */
		public function set_lty_failed_reason( $reason ) {
			$this->set_prop( 'lty_failed_reason', $reason );
		}

		/**
		 * Set Lottery failed date.
		 *
		 * @param string $failed_date lottery failed date.
		 * */
		public function set_lty_failed_date( $failed_date ) {
			$this->set_prop( 'lty_failed_date', $failed_date );
		}

		/**
		 * Set Lottery failed date gmt.
		 *
		 * @param string $failed_date_gmt lottery failed date gmt.
		 * */
		public function set_lty_failed_date_gmt( $failed_date_gmt ) {
			$this->set_prop( 'lty_failed_date_gmt', $failed_date_gmt );
		}

		/**
		 * Set lottery relisted.
		 *
		 * @param $relisted.
		 * */
		public function set_lty_relisted( $relisted ) {
			$this->set_prop( 'lty_relisted', $relisted );
		}

		/**
		 * Set lottery relists.
		 *
		 * @param $relists.
		 * */
		public function set_lty_relists( $relists ) {
			$this->set_prop( 'lty_relists', $relists );
		}

		/**
		 * Set lottery relisted date.
		 *
		 * @param $relisted_date relisted date.
		 * */
		public function set_lty_relisted_date( $relisted_date ) {
			$this->set_prop( 'lty_relisted_date', $relisted_date );
		}

		/**
		 * Set lottery relisted date gmt.
		 *
		 * @param $relisted_date_gmt relisted date gmt.
		 * */
		public function set_lty_relisted_date_gmt( $relisted_date_gmt ) {
			$this->set_prop( 'lty_relisted_date_gmt', $relisted_date_gmt );
		}

		/**
		 * Set lottery current list count.
		 *
		 * @since 11.7.0
		 * @param int $value Current list count.
		 * */
		public function set_lty_list_count( $value ) {
			$this->set_prop( 'lty_list_count', $value );
		}

		/**
		 * Set lottery finished date.
		 *
		 * @param $finished_date finished date.
		 * */
		public function set_lty_finished_date( $finished_date ) {
			$this->set_prop( 'lty_finished_date', $finished_date );
		}

		/**
		 * Set lottery finished date gmt.
		 *
		 * @param $finished_date_gmt finished date gmt.
		 * */
		public function set_lty_finished_date_gmt( $finished_date_gmt ) {
			$this->set_prop( 'lty_finished_date_gmt', $finished_date_gmt );
		}

		/**
		 * Set manual reserved tickets.
		 *
		 * @param $reserved_tickets reserved tickets.
		 * */
		public function set_lty_manual_reserved_tickets( $reserved_tickets ) {
			$this->set_prop( 'lty_manual_reserved_tickets', $reserved_tickets );
		}

		/**
		 * Set hide countdown timer selection type.
		 *
		 * @param $level_selection level selection.
		 * */
		public function set_lty_hide_countdown_timer_selection_type( $level_selection ) {
			$this->set_prop( 'lty_hide_countdown_timer_selection_type', $level_selection );
		}

		/**
		 * Set hide countdown timer in shop.
		 *
		 * @param $hide_countdown_timer_in_shop hide countdown timer in shop.
		 * */
		public function set_lty_hide_countdown_timer_in_shop( $hide_countdown_timer_in_shop ) {
			$this->set_prop( 'lty_hide_countdown_timer_in_shop', $hide_countdown_timer_in_shop );
		}

		/**
		 * Set hide countdown timer in single product.
		 *
		 * @param $hide_countdown_timer_in_single_product hide countdown timer in single product.
		 * */
		public function set_lty_hide_countdown_timer_in_single_product( $hide_countdown_timer_in_single_product ) {
			$this->set_prop( 'lty_hide_countdown_timer_in_single_product', $hide_countdown_timer_in_single_product );
		}

		/**
		 * Set hide progress bar selection type.
		 *
		 * @param $level_selection level selection.
		 * */
		public function set_lty_hide_progress_bar_selection_type( $level_selection ) {
			$this->set_prop( 'lty_hide_progress_bar_selection_type', $level_selection );
		}

		/**
		 * Set hide progress bar in shop.
		 *
		 * @param $hide_progress_bar_in_shop hide progress bar in shop.
		 * */
		public function set_lty_hide_progress_bar_in_shop( $hide_progress_bar_in_shop ) {
			$this->set_prop( 'lty_hide_progress_bar_in_shop', $hide_progress_bar_in_shop );
		}

		/**
		 * Set hide progress bar in single product.
		 *
		 * @param $hide_progress_bar_in_single_product hide progress bar in single product.
		 * */
		public function set_lty_hide_progress_bar_in_single_product( $hide_progress_bar_in_single_product ) {
			$this->set_prop( 'lty_hide_progress_bar_in_single_product', $hide_progress_bar_in_single_product );
		}

		/**
		 * Set enable predefined buttons.
		 *
		 * @param $enable_predefined_buttons enable predefined buttons.
		 * */
		public function set_lty_enable_predefined_buttons( $enable_predefined_buttons ) {
			$this->set_prop( 'lty_enable_predefined_buttons', $enable_predefined_buttons );
		}

		/**
		 * Set predefined buttons discount tag is enabled or not.
		 *
		 * @since 10.6.0
		 * @param string $value Whether predefined buttons discount tag is enabled or not.
		 * */
		public function set_lty_predefined_buttons_discount_tag( $value ) {
			$this->set_prop( 'lty_predefined_buttons_discount_tag', $value );
		}

		/**
		 * Set predefined buttons label.
		 *
		 * @param $predefined_buttons_label predefined buttons label.
		 * */
		public function set_lty_predefined_buttons_label( $predefined_buttons_label ) {
			$this->set_prop( 'lty_predefined_buttons_label', $predefined_buttons_label );
		}

		/**
		 * Set predefined buttons badge label.
		 *
		 * @since 10.5.0
		 * @param string $badge_label Predefined buttons badge label.
		 */
		public function set_lty_predefined_buttons_badge_label( $badge_label ) {
			$this->set_prop( 'lty_predefined_buttons_badge_label', $badge_label );
		}

		/**
		 * Set predefined buttons rule.
		 *
		 * @param $predefined_buttons_rule predefined buttons rule.
		 * */
		public function set_lty_predefined_buttons_rule( $predefined_buttons_rule ) {
			$this->set_prop( 'lty_predefined_buttons_rule', $predefined_buttons_rule );
		}

		/**
		 * Set predefined buttons selection type.
		 *
		 * @param $predefined_buttons_selection_type predefined buttons selection type.
		 * */
		public function set_lty_predefined_buttons_selection_type( $predefined_buttons_selection_type ) {
			$this->set_prop( 'lty_predefined_buttons_selection_type', $predefined_buttons_selection_type );
		}

		/**
		 * Set predefined with quantity selector.
		 *
		 * @since 9.8.0
		 * @param string $value Whether to display quantity selector or not.
		 * */
		public function set_lty_predefined_with_quantity_selector( $value ) {
			$this->set_prop( 'lty_predefined_with_quantity_selector', $value );
		}

		/**
		 * Set predefined buttons discount on range slider.
		 *
		 * @since 10.6.0
		 * @param string $value Whether to display predefined buttons discount on range slider or not.
		 * */
		public function set_lty_range_slider_predefined_discount_tag( $value ) {
			$this->set_prop( 'lty_range_slider_predefined_discount_tag', $value );
		}

		/**
		 * Set predefined buttons discount information in range slider label.
		 *
		 * @since 10.6.0
		 * @param string $value Label.
		 * */
		public function set_lty_range_slider_predefined_discount_label( $value ) {
			$this->set_prop( 'lty_range_slider_predefined_discount_label', $value );
		}

		/**
		 * Set relist finished lottery.
		 *
		 * @since 7.5.0
		 * @param string $relist_finished_lottery
		 *
		 * @return void.
		 * */
		public function set_lty_relist_finished_lottery( $relist_finished_lottery ) {
			$this->set_prop( 'lty_relist_finished_lottery', $relist_finished_lottery );
		}

		/**
		 * Set finished lottery relist duration.
		 *
		 * @since 7.5.0
		 * @param array $finished_lottery_relist_duration
		 *
		 * @return void.
		 * */
		public function set_lty_finished_lottery_relist_duration( $finished_lottery_relist_duration ) {
			$this->set_prop( 'lty_finished_lottery_relist_duration', $finished_lottery_relist_duration );
		}

		/**
		 * Set finished lottery relist pause.
		 *
		 * @since 7.5.0
		 * @param string $finished_lottery_relist_pause
		 *
		 * @return void.
		 * */
		public function set_lty_finished_lottery_relist_pause( $finished_lottery_relist_pause ) {
			$this->set_prop( 'lty_finished_lottery_relist_pause', $finished_lottery_relist_pause );
		}

		/**
		 * Set finished lottery relist pause duration.
		 *
		 * @since 7.5.0
		 * @param array $finished_lottery_relist_pause_duration
		 *
		 * @return void.
		 * */
		public function set_lty_finished_lottery_relist_pause_duration( $finished_lottery_relist_pause_duration ) {
			$this->set_prop( 'lty_finished_lottery_relist_pause_duration', $finished_lottery_relist_pause_duration );
		}

		/**
		 * Set finished lottery relist count type.
		 *
		 * @since 7.5.0
		 * @param string $finished_lottery_relist_count_type
		 *
		 * @return void.
		 * */
		public function set_lty_finished_lottery_relist_count_type( $finished_lottery_relist_count_type ) {
			$this->set_prop( 'lty_finished_lottery_relist_count_type', $finished_lottery_relist_count_type );
		}

		/**
		 * Set finished lottery relist count.
		 *
		 * @since 7.5.0
		 * @param int $finished_lottery_relist_count
		 *
		 * @return void.
		 * */
		public function set_lty_finished_lottery_relist_count( $finished_lottery_relist_count ) {
			$this->set_prop( 'lty_finished_lottery_relist_count', $finished_lottery_relist_count );
		}

		/**
		 * Set finished relist count.
		 *
		 * @since 7.5.0
		 * @param int $finished_relist_count
		 *
		 * @return void
		 * */
		public function set_lty_finished_relisted_count( $finished_relist_count ) {
			$this->set_prop( 'lty_finished_relisted_count', $finished_relist_count );
		}

		/**
		 * Set relist failed lottery.
		 *
		 * @since 7.5.0
		 * @param string $relist_failed_lottery
		 *
		 * @return void.
		 * */
		public function set_lty_relist_failed_lottery( $relist_failed_lottery ) {
			$this->set_prop( 'lty_relist_failed_lottery', $relist_failed_lottery );
		}

		/**
		 * Set failed lottery relist duration.
		 *
		 * @since 7.5.0
		 * @param array $failed_lottery_relist_duration
		 *
		 * @return void.
		 * */
		public function set_lty_failed_lottery_relist_duration( $failed_lottery_relist_duration ) {
			$this->set_prop( 'lty_failed_lottery_relist_duration', $failed_lottery_relist_duration );
		}

		/**
		 * Set failed lottery relist pause.
		 *
		 * @since 7.5.0
		 * @param string $failed_lottery_relist_pause
		 *
		 * @return void.
		 * */
		public function set_lty_failed_lottery_relist_pause( $failed_lottery_relist_pause ) {
			$this->set_prop( 'lty_failed_lottery_relist_pause', $failed_lottery_relist_pause );
		}

		/**
		 * Set failed lottery parse duration.
		 *
		 * @since 7.5.0
		 * @param array $failed_lottery_relist_pause_duration
		 *
		 * @return void.
		 * */
		public function set_lty_failed_lottery_relist_pause_duration( $failed_lottery_relist_pause_duration ) {
			$this->set_prop( 'lty_failed_lottery_relist_pause_duration', $failed_lottery_relist_pause_duration );
		}

		/**
		 * Set failed lottery relist count type.
		 *
		 * @since 7.5.0
		 * @param string $failed_lottery_relist_count_type
		 *
		 * @return void.
		 * */
		public function set_lty_failed_lottery_relist_count_type( $failed_lottery_relist_count_type ) {
			$this->set_prop( 'lty_failed_lottery_relist_count_type', $failed_lottery_relist_count_type );
		}

		/**
		 * Set failed lottery relist count.
		 *
		 * @since 7.5.0
		 * @param int $failed_lottery_relist_count
		 *
		 * @return void.
		 * */
		public function set_lty_failed_lottery_relist_count( $failed_lottery_relist_count ) {
			$this->set_prop( 'lty_failed_lottery_relist_count', $failed_lottery_relist_count );
		}

		/**
		 * Set lottery instant winner.
		 *
		 * @since 8.0.0
		 * */
		public function set_lty_instant_winners( $value ) {
			$this->set_prop( 'lty_instant_winners', $value );
		}

		/**
		 * Set to display lottery instant winner image.
		 *
		 * @since 10.6.0
		 * @param string $value Whether to display or not.
		 * */
		public function set_lty_display_instant_winner_image( $value ) {
			$this->set_prop( 'lty_display_instant_winner_image', $value );
		}

		/**
		 * Set to lottery instant winner display mode.
		 *
		 * @since 11.1.0
		 * @param string $value Display mode.
		 * */
		public function set_lty_instant_winner_display_mode( $value ) {
			$this->set_prop( 'lty_instant_winner_display_mode', $value );
		}

		/**
		 * Set lottery ending soon user email sent.
		 *
		 * @since 12.4.0
		 * @param string $value
		 * */
		public function set_lty_ending_soon_user_email_sent( $value ) {
			$this->set_prop( 'lty_ending_soon_user_email_sent', $value );
		}

		/**
		 * Set failed relist count.
		 *
		 * @since 7.5.0
		 * @param int $failed_relist_count
		 *
		 * @return void
		 * */
		public function set_lty_failed_relist_count( $failed_relist_count ) {
			$this->set_prop( 'lty_failed_relist_count', $failed_relist_count );
		}

		/**
		 * Set lottery ticket numbers display pattern.
		 *
		 * @since 8.6.0
		 * @param string $value
		 * @return void
		 */
		public function set_lty_tickets_per_tab_display_type( $value ) {
			$this->set_prop( 'lty_tickets_per_tab_display_type', $value );
		}

		/**
		 * Set view more option enabled or not in per tab.
		 *
		 * @since 8.6.0
		 * @param string $value
		 * @return void
		 */
		public function set_lty_view_more_tickets_per_tab( $value ) {
			$this->set_prop( 'lty_view_more_tickets_per_tab', $value );
		}

		/**
		 * Set number of tickets to be displayed on view more option.
		 *
		 * @since 8.6.0
		 * @param string $value
		 * @return void
		 */
		public function set_lty_tickets_per_tab_view_more_count( $value ) {
			$this->set_prop( 'lty_tickets_per_tab_view_more_count', $value );
		}

		/**
		 * ---------------------------------------------
		 * Getters
		 * ---------------------------------------------
		 *
		 * Functions for getting product data.
		 */

		/**
		 * Get schedule type.
		 *
		 * @since 11.7.0
		 * @return string
		 * */
		public function get_lty_lottery_schedule_type( $context = 'view' ) {
			return $this->get_prop( 'lty_lottery_schedule_type', $context );
		}

		/**
		 * Get start date.
		 *
		 * @return string.
		 * */
		public function get_lty_start_date( $context = 'view' ) {
			return $this->get_prop( 'lty_start_date', $context );
		}

		/**
		 * Get end date.
		 *
		 * @return string.
		 * */
		public function get_lty_end_date( $context = 'view' ) {
			return $this->get_prop( 'lty_end_date', $context );
		}

		/**
		 * Get start date gmt.
		 *
		 * @return string.
		 * */
		public function get_lty_start_date_gmt( $context = 'view' ) {
			return $this->get_prop( 'lty_start_date_gmt', $context );
		}

		/**
		 * Get end date gmt.
		 *
		 * @return string.
		 * */
		public function get_lty_end_date_gmt( $context = 'view' ) {
			return $this->get_prop( 'lty_end_date_gmt', $context );
		}

		/**
		 * Get minimum tickets.
		 *
		 * @return string.
		 * */
		public function get_lty_minimum_tickets( $context = 'view' ) {
			return $this->get_prop( 'lty_minimum_tickets', $context );
		}

		/**
		 * Get maximum tickets.
		 *
		 * @return string.
		 * */
		public function get_lty_maximum_tickets( $context = 'view' ) {
			return $this->get_prop( 'lty_maximum_tickets', $context );
		}

		/**
		 * Get the ticket range slider type.
		 *
		 * @since 7.5.0
		 * @param string $context
		 * @return string
		 */
		public function get_lty_ticket_range_slider_type( $context = 'view' ) {
			return $this->get_prop( 'lty_ticket_range_slider_type', $context );
		}

		/**
		 * Get the preset tickets.
		 *
		 * @since 7.5.0
		 * @param string $context
		 * @return string
		 */
		public function get_lty_preset_tickets( $context = 'view' ) {
			return $this->get_prop( 'lty_preset_tickets', $context );
		}

		/**
		 * Get user minimum tickets.
		 *
		 * @return string.
		 * */
		public function get_lty_user_minimum_tickets( $context = 'view' ) {
			return $this->get_prop( 'lty_user_minimum_tickets', $context );
		}

		/**
		 * Get user maximum tickets.
		 *
		 * @return string.
		 * */
		public function get_lty_user_maximum_tickets( $context = 'view' ) {
			return $this->get_prop( 'lty_user_maximum_tickets', $context );
		}

		/**
		 * Get order maximum tickets.
		 *
		 * @return string.
		 * */
		public function get_lty_order_maximum_tickets( $context = 'view' ) {
			return $this->get_prop( 'lty_order_maximum_tickets', $context );
		}

		/**
		 * Get ticket price type.
		 *
		 * @return string.
		 * */
		public function get_lty_ticket_price_type( $context = 'view' ) {
			return $this->get_prop( 'lty_ticket_price_type', $context );
		}

		/**
		 * Get regular price.
		 *
		 * @return string.
		 * */
		public function get_lty_regular_price( $context = 'view' ) {
			return $this->get_prop( 'lty_regular_price', $context );
		}

		/**
		 * Get sale price.
		 *
		 * @return string.
		 * */
		public function get_lty_sale_price( $context = 'view' ) {
			return $this->get_prop( 'lty_sale_price', $context );
		}

		/**
		 * Get ticket generation type.
		 *
		 * @return string.
		 * */
		public function get_lty_ticket_generation_type( $context = 'view' ) {
			return $this->get_prop( 'lty_ticket_generation_type', $context );
		}

		/**
		 * Get formatted automatic ticket numbers.
		 *
		 * @since 11.2.0
		 * @return array.
		 * */
		public function get_lty_formatted_automatic_ticket_numbers( $context = 'view' ) {
			return $this->get_prop( 'lty_formatted_automatic_ticket_numbers', $context );
		}

		/**
		 * Get ticket number type.
		 *
		 * @return string.
		 * */
		public function get_lty_ticket_number_type( $context = 'view' ) {
			return $this->get_prop( 'lty_ticket_number_type', $context );
		}

		/**
		 * Get choose ticket numbers.
		 *
		 * @return string.
		 * */
		public function get_lty_choose_ticket_numbers( $context = 'view' ) {
			return $this->get_prop( 'lty_choose_ticket_numbers', $context );
		}

		/**
		 * Get tickets per tab.
		 *
		 * @return string.
		 * */
		public function get_lty_tickets_per_tab( $context = 'view' ) {
			return $this->get_prop( 'lty_tickets_per_tab', $context );
		}

		/**
		 * Get sequential ticket number.
		 *
		 * @return int.
		 * */
		public function get_lty_ticket_sequential_start_number( $context = 'view' ) {
			return $this->get_prop( 'lty_ticket_sequential_start_number', $context );
		}

		/**
		 * Get shuffled ticket number.
		 *
		 * @return int.
		 * */
		public function get_lty_ticket_shuffled_start_number( $context = 'view' ) {
			return $this->get_prop( 'lty_ticket_shuffled_start_number', $context );
		}

		/**
		 * Get ticket prefix.
		 *
		 * @return string.
		 * */
		public function get_lty_ticket_prefix( $context = 'view' ) {
			return $this->get_prop( 'lty_ticket_prefix', $context );
		}

		/**
		 * Get ticket suffix.
		 *
		 * @return string.
		 * */
		public function get_lty_ticket_suffix( $context = 'view' ) {
			return $this->get_prop( 'lty_ticket_suffix', $context );
		}

		/**
		 * Get alphabet with sequence numbers enabled.
		 *
		 * @return string.
		 * */
		public function get_lty_alphabet_with_sequence_nos_enabled( $context = 'view' ) {
			return $this->get_prop( 'lty_alphabet_with_sequence_nos_enabled', $context );
		}

		/**
		 * Get alphabet with sequence numbers type.
		 *
		 * @return string.
		 * */
		public function get_lty_alphabet_with_sequence_nos_type( $context = 'view' ) {
			return $this->get_prop( 'lty_alphabet_with_sequence_nos_type', $context );
		}

		/**
		 * Get lucky dip.
		 *
		 * @return string.
		 * */
		public function get_lty_lucky_dip( $context = 'view' ) {
			return $this->get_prop( 'lty_lucky_dip', $context );
		}

		/**
		 * Get lucky dip method type.
		 *
		 * @since 10.4.0
		 * @return string
		 * */
		public function get_lty_lucky_dip_method_type( $context = 'view' ) {
			return $this->get_prop( 'lty_lucky_dip_method_type' );
		}

		/**
		 * Get hide sold tickets in per tab.
		 *
		 * @since 8.9.0
		 * @param string $value
		 * */
		public function get_lty_hide_sold_tickets( $context = 'view' ) {
			return $this->get_prop( 'lty_hide_sold_tickets', $context );
		}

				/**
				 * Get lottery unique winners.
				 *
				 * @since 9.6.0
				 * @param string $context The context of the request.
				 * @return string.
				 * */
		public function get_lty_lottery_unique_winners( $context = 'view' ) {
			return $this->get_prop( 'lty_lottery_unique_winners', $context );
		}

		/**
		 * Get winners count.
		 *
		 * @return string.
		 * */
		public function get_lty_winners_count( $context = 'view' ) {
			return $this->get_prop( 'lty_winners_count', $context );
		}

		/**
		 * Get winner selection method.
		 *
		 * @return string.
		 * */
		public function get_lty_winner_selection_method( $context = 'view' ) {
			return $this->get_prop( 'lty_winner_selection_method', $context );
		}

		/**
		 * Get winning product selection.
		 *
		 * @return string.
		 * */
		public function get_lty_winning_product_selection( $context = 'view' ) {
			return $this->get_prop( 'lty_winning_product_selection', $context );
		}

		/**
		 * Get item selection.
		 *
		 * @return array.
		 * */
		public function get_lty_selected_gift_products( $context = 'view' ) {
			return $this->get_prop( 'lty_selected_gift_products', $context );
		}

		/**
		 * Get winner outside gift items.
		 *
		 * @return string.
		 * */
		public function get_lty_winner_outside_gift_items( $context = 'view' ) {
			return $this->get_prop( 'lty_winner_outside_gift_items', $context );
		}

		/**
		 * Get manage question.
		 *
		 * @return string.
		 * */
		public function get_lty_manage_question( $context = 'view' ) {
			return $this->get_prop( 'lty_manage_question', $context );
		}

		/**
		 * Get force answer.
		 *
		 * @return string.
		 * */
		public function get_lty_force_answer( $context = 'view' ) {
			return $this->get_prop( 'lty_force_answer', $context );
		}

		/**
		 * Get incorrectly selected answer restriction.
		 *
		 * @return string.
		 * */
		public function get_lty_restrict_incorrectly_selected_answer( $context = 'view' ) {
			return $this->get_prop( 'lty_restrict_incorrectly_selected_answer', $context );
		}

		/**
		 * Get correct answer.
		 *
		 * @return string.
		 * */
		public function get_lty_validate_correct_answer( $context = 'view' ) {
			return $this->get_prop( 'lty_validate_correct_answer', $context );
		}

		/**
		 * Get question answer display type.
		 *
		 * @since 8.2.0
		 * @return string.
		 * */
		public function get_lty_question_answer_display_type() {
			return $this->get_prop( 'lty_question_answer_display_type' );
		}

		/**
		 * Get question answer first dropdown option as default option.
		 *
		 * @since 10.2.0
		 * @return string
		 * */
		public function get_lty_question_answer_first_option_as_default_option() {
			return $this->get_prop( 'lty_question_answer_first_option_as_default_option' );
		}

		/**
		 * Get question answer time limit type.
		 *
		 * @return string.
		 * */
		public function get_lty_question_answer_time_limit_type( $context = 'view' ) {
			return $this->get_prop( 'lty_question_answer_time_limit_type', $context );
		}

		/**
		 * Get question answer time limit.
		 *
		 * @return array.
		 * */
		public function get_lty_question_answer_time_limit( $context = 'view' ) {
			return $this->get_prop( 'lty_question_answer_time_limit', $context );
		}

		/**
		 * Get verify answer type.
		 *
		 * @return string.
		 * */
		public function get_lty_verify_answer_type( $context = 'view' ) {
			return $this->get_prop( 'lty_verify_answer_type', $context );
		}

		/**
		 * Get question answer viewed data.
		 *
		 * @return array.
		 * */
		public function get_lty_question_answer_viewed_data( $context = 'view' ) {
			return $this->get_prop( 'lty_question_answer_viewed_data', $context );
		}

		/**
		 * Get question answer attempts data.
		 *
		 * @return array.
		 * */
		public function get_lty_question_answer_attempts_data( $context = 'view' ) {
			return $this->get_prop( 'lty_question_answer_attempts_data', $context );
		}

		/**
		 * Get question answer selection type.
		 *
		 * @return string
		 * */
		public function get_lty_question_answer_selection_type( $context = 'view' ) {
			return $this->get_prop( 'lty_question_answer_selection_type', $context );
		}

		/**
		 * Get question answer attempts.
		 *
		 * @return int
		 * */
		public function get_lty_question_answer_attempts( $context = 'view' ) {
			return $this->get_prop( 'lty_question_answer_attempts', $context );
		}

		/**
		 * Get incorrect answer user ids.
		 *
		 * @return array.
		 * */
		public function get_lty_incorrect_answer_user_ids( $context = 'view' ) {
			return $this->get_prop( 'lty_incorrect_answer_user_ids', $context );
		}

		/**
		 * Get questions.
		 *
		 * @return array.
		 * */
		public function get_lty_questions( $context = 'view' ) {
			return $this->get_prop( 'lty_questions', $context );
		}

		/**
		 * Get Lottery Ticket count.
		 *
		 * @return string.
		 * */
		public function get_lty_ticket_count( $context = 'view' ) {
			return $this->get_prop( 'lty_ticket_count', $context );
		}

		/**
		 * Get Lottery Ticket start number.
		 *
		 * @return int.
		 * */
		public function get_lty_ticket_start_number( $context = 'view' ) {
			return $this->get_prop( 'lty_ticket_start_number', $context );
		}

		/**
		 * Get Lottery status.
		 *
		 * @return string.
		 * */
		public function get_lty_lottery_status( $context = 'view' ) {
			return $this->get_prop( 'lty_lottery_status', $context );
		}

		/**
		 * Get Lottery Closed.
		 *
		 * @return string.
		 * */
		public function get_lty_closed( $context = 'view' ) {
			return $this->get_prop( 'lty_closed', $context );
		}

		/**
		 * Get Lottery closed date.
		 *
		 * @return string.
		 * */
		public function get_lty_closed_date( $context = 'view' ) {
			return $this->get_prop( 'lty_closed_date', $context );
		}

		/**
		 * Get Lottery closed date gmt.
		 *
		 * $return string.
		 * */
		public function get_lty_closed_date_gmt( $context = 'view' ) {
			return $this->get_prop( 'lty_closed_date_gmt', $context );
		}

		/**
		 * Get Lottery failed reason.
		 *
		 * @return string.
		 * */
		public function get_lty_failed_reason( $context = 'view' ) {
			return $this->get_prop( 'lty_failed_reason', $context );
		}

		/**
		 * Get Lottery failed date.
		 *
		 * @return string.
		 * */
		public function get_lty_failed_date( $context = 'view' ) {
			return $this->get_prop( 'lty_failed_date', $context );
		}

		/**
		 * Get Lottery failed date gmt.
		 *
		 * @return string.
		 * */
		public function get_lty_failed_date_gmt( $context = 'view' ) {
			return $this->get_prop( 'lty_failed_date_gmt', $context );
		}

		/**
		 * Get lottery relisted.
		 *
		 * @return string
		 * */
		public function get_lty_relisted( $context = 'view' ) {
			return $this->get_prop( 'lty_relisted', $context );
		}

		/**
		 * Get lottery relists.
		 *
		 * @return string
		 * */
		public function get_lty_relists( $context = 'view' ) {
			return $this->get_prop( 'lty_relists', $context );
		}

		/**
		 * Get lottery relisted date.
		 *
		 * @return string
		 * */
		public function get_lty_relisted_date( $context = 'view' ) {
			return $this->get_prop( 'lty_relisted_date', $context );
		}

		/**
		 * Get lottery relisted date gmt.
		 *
		 * @return string
		 * */
		public function get_lty_relisted_date_gmt( $context = 'view' ) {
			return $this->get_prop( 'lty_relisted_date_gmt', $context );
		}

		/**
		 * Get lottery current list count.
		 *
		 * @since 11.7.0
		 * @return int
		 * */
		public function get_lty_list_count( $context = 'view' ) {
			return $this->get_prop( 'lty_list_count', $context );
		}

		/**
		 * Get lottery finished date.
		 *
		 * @return string
		 * */
		public function get_lty_finished_date( $context = 'view' ) {
			return $this->get_prop( 'lty_finished_date', $context );
		}

		/**
		 * Get lottery finished date gmt.
		 *
		 * @return string
		 * */
		public function get_lty_finished_date_gmt( $context = 'view' ) {
			return $this->get_prop( 'lty_finished_date_gmt', $context );
		}

		/**
		 * Get manual reserved tickets.
		 *
		 * @return array
		 * */
		public function get_lty_manual_reserved_tickets( $context = 'view' ) {
			return $this->get_prop( 'lty_manual_reserved_tickets', $context );
		}

		/**
		 * Get Hide countdown timer selection type.
		 *
		 * @return string
		 * */
		public function get_lty_hide_countdown_timer_selection_type( $context = 'view' ) {
			return $this->get_prop( 'lty_hide_countdown_timer_selection_type', $context );
		}

		/**
		 * Get Hide countdown timer in shop.
		 *
		 * @return string
		 * */
		public function get_lty_hide_countdown_timer_in_shop( $context = 'view' ) {
			return $this->get_prop( 'lty_hide_countdown_timer_in_shop', $context );
		}

		/**
		 * Get Hide countdown timer in single product.
		 *
		 * @return string
		 * */
		public function get_lty_hide_countdown_timer_in_single_product( $context = 'view' ) {
			return $this->get_prop( 'lty_hide_countdown_timer_in_single_product', $context );
		}

		/**
		 * Get hide progress bar selection type.
		 *
		 * @return string
		 * */
		public function get_lty_hide_progress_bar_selection_type( $context = 'view' ) {
			return $this->get_prop( 'lty_hide_progress_bar_selection_type', $context );
		}

		/**
		 * Get hide progress bar in shop.
		 *
		 * @return string
		 * */
		public function get_lty_hide_progress_bar_in_shop( $context = 'view' ) {
			return $this->get_prop( 'lty_hide_progress_bar_in_shop', $context );
		}

		/**
		 * Get Hide progress bar in single product.
		 *
		 * @return string
		 * */
		public function get_lty_hide_progress_bar_in_single_product( $context = 'view' ) {
			return $this->get_prop( 'lty_hide_progress_bar_in_single_product', $context );
		}

		/**
		 * Get enable predefined buttons.
		 *
		 * @param string
		 * */
		public function get_lty_enable_predefined_buttons( $context = 'view' ) {
			return $this->get_prop( 'lty_enable_predefined_buttons', $context );
		}

		/**
		 * Get predefined buttons discount tag is enabled or not.
		 *
		 * @since 10.6.0
		 * @return string
		 * */
		public function get_lty_predefined_buttons_discount_tag( $context = 'view' ) {
			return $this->get_prop( 'lty_predefined_buttons_discount_tag', $context );
		}

		/**
		 * Get predefined buttons label.
		 *
		 * @param string
		 * */
		public function get_lty_predefined_buttons_label( $context = 'view' ) {
			return $this->get_prop( 'lty_predefined_buttons_label', $context );
		}

		/**
		 * Get predefined buttons badge label.
		 *
		 * @since 10.6.0
		 * @param string $context
		 * @return string
		 */
		public function get_lty_predefined_buttons_badge_label( $context = 'view' ) {
			return $this->get_prop( 'lty_predefined_buttons_badge_label', $context );
		}

		/**
		 * Get predefined buttons rule.
		 *
		 * @return array.
		 * */
		public function get_lty_predefined_buttons_rule( $context = 'view' ) {
			return $this->get_prop( 'lty_predefined_buttons_rule', $context );
		}

		/**
		 * Get predefined buttons selection type.
		 *
		 * @return string.
		 * */
		public function get_lty_predefined_buttons_selection_type( $context = 'view' ) {
			return $this->get_prop( 'lty_predefined_buttons_selection_type', $context );
		}

		/**
		 * Get predefined with quantity selector.
		 *
		 * @since 9.8.0
		 * @return string
		 * */
		public function get_lty_predefined_with_quantity_selector() {
			return $this->get_prop( 'lty_predefined_with_quantity_selector' );
		}

		/**
		 * Get predefined buttons discount on range slider.
		 *
		 * @since 10.6.0
		 * @return string
		 * */
		public function get_lty_range_slider_predefined_discount_tag( $context = 'view' ) {
			return $this->get_prop( 'lty_range_slider_predefined_discount_tag', $context );
		}

		/**
		 * Get predefined buttons discount information in range slider label.
		 *
		 * @since 10.6.0
		 * @return string
		 * */
		public function get_lty_range_slider_predefined_discount_label( $context = 'view' ) {
			return $this->get_prop( 'lty_range_slider_predefined_discount_label', $context );
		}

		/**
		 * Get relist finished lottery.
		 *
		 * @since 7.5.0
		 *
		 * @return string.
		 * */
		public function get_lty_relist_finished_lottery( $context = 'view' ) {
			return $this->get_prop( 'lty_relist_finished_lottery', $context );
		}

		/**
		 * Get finished lottery relist duration.
		 *
		 * @since 7.5.0
		 *
		 * @return array.
		 * */
		public function get_lty_finished_lottery_relist_duration( $context = 'view' ) {
			return $this->get_prop( 'lty_finished_lottery_relist_duration', $context );
		}

		/**
		 * Get finished lottery relist pause.
		 *
		 * @since 7.5.0
		 *
		 * @return string.
		 * */
		public function get_lty_finished_lottery_relist_pause( $context = 'view' ) {
			return $this->get_prop( 'lty_finished_lottery_relist_pause', $context );
		}

		/**
		 * Get lottery finished relist extend time.
		 *
		 * @since 7.5.0
		 *
		 * @return array.
		 * */
		public function get_lty_finished_lottery_relist_pause_duration( $context = 'view' ) {
			return $this->get_prop( 'lty_finished_lottery_relist_pause_duration', $context );
		}

		/**
		 * Get lottery finished relist count type.
		 *
		 * @since 7.5.0
		 *
		 * @return string.
		 * */
		public function get_lty_finished_lottery_relist_count_type( $context = 'view' ) {
			return $this->get_prop( 'lty_finished_lottery_relist_count_type', $context );
		}

		/**
		 * Get lottery finished relist duration.
		 *
		 * @since 7.5.0
		 *
		 * @return int.
		 * */
		public function get_lty_finished_lottery_relist_count( $context = 'view' ) {
			return $this->get_prop( 'lty_finished_lottery_relist_count', $context );
		}

		/**
		 * Get finished relist count.
		 *
		 * @since 7.5.0
		 *
		 * @return int.
		 * */
		public function get_lty_finished_relisted_count( $context = 'view' ) {
			return $this->get_prop( 'lty_finished_relisted_count', $context );
		}

		/**
		 * Get relist failed lottery.
		 *
		 * @since 7.5.0
		 *
		 * @return string.
		 * */
		public function get_lty_relist_failed_lottery( $context = 'view' ) {
			return $this->get_prop( 'lty_relist_failed_lottery', $context );
		}

		/**
		 * Get failed lottery relist duration.
		 *
		 * @since 7.5.0
		 *
		 * @return array.
		 * */
		public function get_lty_failed_lottery_relist_duration( $context = 'view' ) {
			return $this->get_prop( 'lty_failed_lottery_relist_duration', $context );
		}

		/**
		 * Get failed lottery relist pause.
		 *
		 * @since 7.5.0
		 *
		 * @return string.
		 * */
		public function get_lty_failed_lottery_relist_pause( $context = 'view' ) {
			return $this->get_prop( 'lty_failed_lottery_relist_pause', $context );
		}

		/**
		 * Get failed lottery relist pause duration
		 *
		 * @since 7.5.0
		 *
		 * @return array.
		 * */
		public function get_lty_failed_lottery_relist_pause_duration( $context = 'view' ) {
			return $this->get_prop( 'lty_failed_lottery_relist_pause_duration', $context );
		}

		/**
		 * Get failed lottery relist count type.
		 *
		 * @since 7.5.0
		 *
		 * @return string.
		 * */
		public function get_lty_failed_lottery_relist_count_type( $context = 'view' ) {
			return $this->get_prop( 'lty_failed_lottery_relist_count_type', $context );
		}

		/**
		 * Get failed lottery relist count.
		 *
		 * @since 7.5.0
		 *
		 * @return int.
		 * */
		public function get_lty_failed_lottery_relist_count( $context = 'view' ) {
			return $this->get_prop( 'lty_failed_lottery_relist_count', $context );
		}

		/**
		 * Get failed relist count.
		 *
		 * @since 7.5.0
		 *
		 * @return int.
		 * */
		public function get_lty_failed_relisted_count( $context = 'view' ) {
			return $this->get_prop( 'lty_failed_relisted_count', $context );
		}

		/**
		 * Get lottery instant winner.
		 *
		 * @since 8.0.0
		 * @return string
		 * */
		public function get_lty_instant_winners( $context = 'view' ) {
			return $this->get_prop( 'lty_instant_winners', $context );
		}

		/**
		 * Get display lottery instant winner image.
		 *
		 * @since 10.6.0
		 * @return string
		 * */
		public function get_lty_display_instant_winner_image( $context = 'view' ) {
			return $this->get_prop( 'lty_display_instant_winner_image', $context );
		}

		/**
		 * Get to lottery instant winner display mode.
		 *
		 * @since 11.1.0
		 * @return string
		 * */
		public function get_lty_instant_winner_display_mode( $context = 'view' ) {
			return $this->get_prop( 'lty_instant_winner_display_mode', $context );
		}

		/**
		 * Get lottery ending soon user email sent.
		 *
		 * @since 12.4.0
		 * @return string
		 * */
		public function get_lty_ending_soon_user_email_sent( $context = 'view' ) {
			return $this->get_prop( 'lty_ending_soon_user_email_sent', $context );
		}

		/**
		 * Get lottery ticket numbers display pattern.
		 *
		 * @since 8.6.0
		 * @param string $context
		 * @return string
		 */
		public function get_lty_tickets_per_tab_display_type( $context = 'view' ) {
			return $this->get_prop( 'lty_tickets_per_tab_display_type', $context );
		}

		/**
		 * Set view more option enabled or not in per tab.
		 *
		 * @since 8.6.0
		 * @param string $context
		 * @return string
		 */
		public function get_lty_view_more_tickets_per_tab( $context = 'view' ) {
			return $this->get_prop( 'lty_view_more_tickets_per_tab', $context );
		}

		/**
		 * Set number of tickets to be displayed on view more option.
		 *
		 * @since 8.6.0
		 * @param string $context
		 * @return string
		 */
		public function get_lty_tickets_per_tab_view_more_count( $context = 'view' ) {
			return $this->get_prop( 'lty_tickets_per_tab_view_more_count', $context );
		}
	}

}
