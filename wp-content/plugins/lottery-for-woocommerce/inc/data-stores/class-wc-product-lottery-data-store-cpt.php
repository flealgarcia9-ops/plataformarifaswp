<?php
/**
 * WC_Product_Lottery_Data_Store_CPT class file.
 *
 * @since 8.7.0
 * @package WooCommerce/Classes
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WC_Product_Lottery_Data_Store_CPT' ) ) {

	/**
	 * WC Product Data Store: Stored in CPT.
	 *
	 * @since 3.0.0
	 */
	class WC_Product_Lottery_Data_Store_CPT extends WC_Product_Data_Store_CPT {

		/**
		 * Data stored in meta keys, but not considered "meta" for the lottery type.
		 *
		 * @var array
		 */
		protected $extended_internal_meta_keys = array(
			'_lty_start_date',
			'_lty_end_date',
			'_lty_start_date_gmt',
			'_lty_end_date_gmt',
			'_lty_minimum_tickets',
			'_lty_maximum_tickets',
			'_lty_ticket_range_slider_type',
			'_lty_preset_tickets',
			'_lty_order_maximum_tickets',
			'_lty_user_minimum_tickets',
			'_lty_user_maximum_tickets',
			'_lty_ticket_price_type',
			'_lty_regular_price',
			'_lty_sale_price',
			'_lty_ticket_generation_type',
			'_lty_ticket_number_type',
			'_lty_choose_ticket_numbers',
			'_lty_ticket_sequential_start_number',
			'_lty_ticket_shuffled_start_number',
			'_lty_ticket_prefix',
			'_lty_ticket_suffix',
			'_lty_alphabet_with_sequence_nos_enabled',
			'_lty_alphabet_with_sequence_nos_type',
			'_lty_tickets_per_tab',
			'_lty_tickets_per_tab_display_type',
			'_lty_view_more_tickets_per_tab',
			'_lty_tickets_per_tab_view_more_count',
			'_lty_lucky_dip',
			'_lty_lucky_dip_method_type',
			'_lty_lottery_unique_winners',
			'_lty_winners_count',
			'_lty_winner_selection_method',
			'_lty_winning_product_selection',
			'_lty_selected_gift_products',
			'_lty_winner_outside_gift_items',
			'_lty_manage_question',
			'_lty_questions',
			'_lty_force_answer',
			'_lty_restrict_incorrectly_selected_answer',
			'_lty_validate_correct_answer',
			'_lty_question_answer_display_type',
			'_lty_question_answer_first_option_as_default_option',
			'_lty_question_answer_time_limit_type',
			'_lty_question_answer_time_limit',
			'_lty_verify_answer_type',
			'_lty_question_answer_selection_type',
			'_lty_question_answer_attempts',
			'_lty_ticket_start_number',
			'_lty_failed_reason',
			'_lty_hide_countdown_timer_selection_type',
			'_lty_hide_countdown_timer_in_shop',
			'_lty_hide_countdown_timer_in_single_product',
			'_lty_hide_progress_bar_selection_type',
			'_lty_hide_progress_bar_in_shop',
			'_lty_hide_progress_bar_in_single_product',
			'_lty_enable_predefined_buttons',
			'_lty_predefined_buttons_discount_tag',
			'_lty_predefined_buttons_label',
			'_lty_predefined_buttons_badge_label',
			'_lty_predefined_buttons_rule',
			'_lty_predefined_buttons_selection_type',
			'_lty_predefined_with_quantity_selector',
			'_lty_range_slider_predefined_discount_tag',
			'_lty_range_slider_predefined_discount_label',
			'_lty_relist_finished_lottery',
			'_lty_finished_lottery_relist_duration',
			'_lty_finished_lottery_relist_pause',
			'_lty_finished_lottery_relist_pause_duration',
			'_lty_finished_lottery_relist_count_type',
			'_lty_finished_lottery_relist_count',
			'_lty_relist_failed_lottery',
			'_lty_failed_lottery_relist_duration',
			'_lty_failed_lottery_relist_pause',
			'_lty_failed_lottery_relist_pause_duration',
			'_lty_failed_lottery_relist_count_type',
			'_lty_failed_lottery_relist_count',
			'_lty_instant_winners',
			'_lty_display_instant_winner_image',
			'_lty_instant_winner_display_mode',
			'_lty_list_count',
			'_lty_ending_soon_user_email_sent',
		);

		/**
		 * Callback to exclude bundle-specific meta data.
		 *
		 * @param object $meta
		 * @return bool
		 */
		protected function exclude_internal_meta_keys( $meta ) {
			return parent::exclude_internal_meta_keys( $meta ) || in_array( $meta->meta_key, $this->extended_internal_meta_keys );
		}
	}
}
