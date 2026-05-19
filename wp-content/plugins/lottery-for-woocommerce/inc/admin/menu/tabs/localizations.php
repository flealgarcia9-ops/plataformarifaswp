<?php
/**
 * Localization Tab.
 * 
 * @since 1.0.0
 * */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( class_exists( 'LTY_Localization_Tab' ) ) {
	return new LTY_Localization_Tab();
}

/**
 * LTY_Localization_Tab.
 * */
class LTY_Localization_Tab extends LTY_Settings_Page {

	/**
	 * Constructor.
	 * */
	public function __construct() {
		$this->id    = 'localizations';
		$this->label = __( 'Localization', 'lottery-for-woocommerce' );

		parent::__construct();
	}

	/**
	 * Get sections.
	 *
	 * @return array
	 */
	public function get_sections() {

		$sections = array(
			'shop_category_page'  => __( 'Shop and Category', 'lottery-for-woocommerce' ),
			'single_product_page' => __( 'Single Product', 'lottery-for-woocommerce' ),
			'dashboard'           => __( 'Giveaway Dashboard', 'lottery-for-woocommerce' ),
			'status'              => __( 'Status', 'lottery-for-woocommerce' ),
			'predefined_buttons'  => __( 'Predefined Buttons', 'lottery-for-woocommerce' ),
			'winners_list'        => __( 'Winners List', 'lottery-for-woocommerce' ),
			'entry_list'          => __( 'Entry List', 'lottery-for-woocommerce' ),
			'other_pages'         => __( 'Other Pages', 'lottery-for-woocommerce' ),
		);

		/**
		 * This hook is used to alter the current tab sections.
		 *
		 * @since 1.0
		 */
		return apply_filters( $this->plugin_slug . '_get_sections_' . $this->id, $sections );
	}

	/**
	 * Get settings for localizations section array.
	 * */
	public function shop_category_page_section_array() {
		$section_fields = array();

		// Lottery, Shop and Category Pages message Section Start.
		$section_fields[] = array(
			'type'  => 'title',
			'title' => __( 'Shop and Category Pages', 'lottery-for-woocommerce' ),
			'id'    => $this->get_option_key( 'shop_messages_section' ),
		);
		$section_fields[] = array(
			'title'   => __( 'On-going Giveaway Button Label', 'lottery-for-woocommerce' ),
			'type'    => 'textarea',
			'default' => get_option( 'lty_settings_shop_read_more_btn_label', 'Participate Now' ),
			'id'      => $this->get_option_key( 'shop_lottery_started_btn_label' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Finished Giveaway Button Label', 'lottery-for-woocommerce' ),
			'type'    => 'textarea',
			'default' => get_option( 'lty_settings_shop_closed_lottery_read_more_btn_label', 'View Winner(s)' ),
			'id'      => $this->get_option_key( 'shop_lottery_finished_btn_label' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Closed Giveaway Button Label', 'lottery-for-woocommerce' ),
			'type'    => 'textarea',
			'default' => get_option( 'lty_settings_shop_closed_lottery_read_more_btn_label', 'Read More' ),
			'id'      => $this->get_option_key( 'shop_lottery_closed_btn_label' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Failed Giveaway Button Label', 'lottery-for-woocommerce' ),
			'type'    => 'textarea',
			'default' => get_option( 'lty_settings_shop_closed_lottery_read_more_btn_label', 'Read More' ),
			'id'      => $this->get_option_key( 'shop_lottery_failed_btn_label' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Not Started Giveaway Button Label', 'lottery-for-woocommerce' ),
			'type'    => 'textarea',
			'default' => get_option( 'lty_settings_shop_closed_lottery_read_more_btn_label', 'Read More' ),
			'id'      => $this->get_option_key( 'shop_lottery_not_started_btn_label' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Giveaway Starts on Label', 'lottery-for-woocommerce' ),
			'type'    => 'textarea',
			'default' => 'Start On',
			'id'      => $this->get_option_key( 'shop_lottery_start_label' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Giveaway Ends on Label', 'lottery-for-woocommerce' ),
			'type'    => 'textarea',
			'default' => 'Ends On',
			'id'      => $this->get_option_key( 'shop_lottery_ends_label' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Countdown Timer Days Label', 'lottery-for-woocommerce' ),
			'type'    => 'textarea',
			'default' => 'Days',
			'id'      => $this->get_option_key( 'shop_lottery_days_label' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Countdown Timer Hours Label', 'lottery-for-woocommerce' ),
			'type'    => 'textarea',
			'default' => 'Hours',
			'id'      => $this->get_option_key( 'shop_lottery_hours_label' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Countdown Timer Minutes Label', 'lottery-for-woocommerce' ),
			'type'    => 'textarea',
			'default' => 'Minutes',
			'id'      => $this->get_option_key( 'shop_lottery_minutes_label' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Countdown Timer Seconds Label', 'lottery-for-woocommerce' ),
			'type'    => 'textarea',
			'default' => 'Seconds',
			'id'      => $this->get_option_key( 'shop_lottery_seconds_label' ),
		);
		$section_fields[] = array(
			'type' => 'sectionend',
			'id'   => $this->get_option_key( 'shop_messages_section' ),
		);
		// Lottery, Shop and Category Pages message Section End.

		return $section_fields;
	}

	/**
	 * Get settings for single product page section array.
	 *
	 * @return array
	 * */
	public function single_product_page_section_array() {
		$section_fields = array();
		// Single Product Page message Section Start.
		$section_fields[] = array(
			'type'  => 'title',
			'title' => __( 'Single Product Page Labels', 'lottery-for-woocommerce' ),
			'id'    => $this->get_option_key( 'single_product_page_messages_section' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Time Left to Start Label', 'lottery-for-woocommerce' ),
			'type'    => 'textarea',
			'default' => 'Time Left to Start:',
			'id'      => $this->get_option_key( 'single_product_time_left_start_label' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Time Left to End Label', 'lottery-for-woocommerce' ),
			'type'    => 'textarea',
			'default' => 'Time Left to End:',
			'id'      => $this->get_option_key( 'single_product_time_left_end_label' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Giveaway Starts on Label', 'lottery-for-woocommerce' ),
			'type'    => 'textarea',
			'default' => 'Giveaway starts on',
			'id'      => $this->get_option_key( 'single_product_start_label' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Giveaway Ends on Label', 'lottery-for-woocommerce' ),
			'type'    => 'textarea',
			'default' => 'Giveaway ends on',
			'id'      => $this->get_option_key( 'single_product_end_label' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Giveaway Ended on Label', 'lottery-for-woocommerce' ),
			'type'    => 'textarea',
			'default' => 'Giveaway ended on',
			'id'      => $this->get_option_key( 'single_product_ended_label' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Giveaway Add to Cart Button Label for Automatic Ticket Type', 'lottery-for-woocommerce' ),
			'type'    => 'textarea',
			'default' => 'Participate now for {lottery_price}',
			'id'      => $this->get_option_key( 'single_product_price_label' ),
			'desc'    => __( '<b>Supported Shortcodes:<br/>{lottery_price}</b> - Ticket(s) Price<br/><b>{ticket_quantity}</b> - Ticket(s) Quantity', 'lottery-for-woocommerce' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Giveaway Add to Cart Button Label for User Chooses Ticket Type', 'lottery-for-woocommerce' ),
			'type'    => 'textarea',
			'default' => lty_get_single_product_price_label(),
			'id'      => $this->get_option_key( 'single_product_user_chooses_ticket_add_to_cart_label' ),
			'desc'    => __( '<b>Supported Shortcodes:<br/>{lottery_price}</b> - Ticket(s) Price<br/><b>{ticket_quantity}</b> - Ticket(s) Quantity', 'lottery-for-woocommerce' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Countdown Timer Days Label', 'lottery-for-woocommerce' ),
			'type'    => 'textarea',
			'default' => 'Days',
			'id'      => $this->get_option_key( 'single_product_days_label' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Countdown Timer Hours Label', 'lottery-for-woocommerce' ),
			'type'    => 'textarea',
			'default' => 'Hours',
			'id'      => $this->get_option_key( 'single_product_hours_label' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Countdown Timer Minutes Label', 'lottery-for-woocommerce' ),
			'type'    => 'textarea',
			'default' => 'Minutes',
			'id'      => $this->get_option_key( 'single_product_minutes_label' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Countdown Timer Seconds Label', 'lottery-for-woocommerce' ),
			'type'    => 'textarea',
			'default' => 'Seconds',
			'id'      => $this->get_option_key( 'single_product_seconds_label' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Progress Bar Remaining Tickets Label', 'lottery-for-woocommerce' ),
			'type'    => 'textarea',
			'default' => '%s Tickets remaining',
			'id'      => $this->get_option_key( 'single_product_progress_bar_remaining_ticket_label' ),
			/* translators: %s: Remaining Ticket count */
			'desc'    => __( '<b>Supported Shortcodes:<br/>%s</b> - Remaining Tickets Count', 'lottery-for-woocommerce' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Progress Bar Tickets Sold Label', 'lottery-for-woocommerce' ),
			'type'    => 'textarea',
			'default' => 'Tickets Sold: {ticket_count}',
			'id'      => $this->get_option_key( 'single_product_progress_bar_ticket_sold_notice_label' ),
			'desc'    => __( '<b>Supported Shortcodes:<br/>{ticket_count}</b> - Ticket(s) sold count', 'lottery-for-woocommerce' ),
		);
		$section_fields[] = array(
			'title'   => __( 'User Chooses the Ticket Search Button Label', 'lottery-for-woocommerce' ),
			'type'    => 'textarea',
			'default' => 'Search Ticket',
			'id'      => $this->get_option_key( 'single_product_ticket_search_button_label' ),
		);
		$section_fields[] = array(
			'title'   => __( 'User Chooses the Ticket Go Back Button Label', 'lottery-for-woocommerce' ),
			'type'    => 'textarea',
			'default' => 'Go Back',
			'id'      => $this->get_option_key( 'single_product_click_to_back_button_label' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Lucky Dip Button Label', 'lottery-for-woocommerce' ),
			'type'    => 'textarea',
			'default' => __( 'Lucky Dip', 'lottery-for-woocommerce' ),
			'id'      => $this->get_option_key( 'single_product_lucky_dip_button_label' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Lucky Dip Fixed Quantity Shortcode Label', 'lottery-for-woocommerce' ),
			'type'    => 'textarea',
			'default' => __( 'Lucky Dip <br> {ticket_quantity} Qty </br>', 'lottery-for-woocommerce' ),
			'id'      => $this->get_option_key( 'lucky_dip_fixed_quantity_shortcode_label' ),
			'desc'    => __( '<b>Supported Shortcodes:<br/>{ticket_quantity}</b> - Display the quantity value', 'lottery-for-woocommerce' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Lucky Dip Title Label on Popup', 'lottery-for-woocommerce' ),
			'type'    => 'textarea',
			'default' => __( 'Lucky Dip', 'lottery-for-woocommerce' ),
			'id'      => $this->get_option_key( 'single_product_lucky_dip_title_label' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Lucky Dip Quantity Label', 'lottery-for-woocommerce' ),
			'type'    => 'textarea',
			'default' => __( 'Quantity', 'lottery-for-woocommerce' ),
			'id'      => $this->get_option_key( 'single_product_lucky_dip_quantity_label' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Generate Lucky Dip Button Label', 'lottery-for-woocommerce' ),
			'type'    => 'textarea',
			'default' => __( 'Generate Lucky Dip', 'lottery-for-woocommerce' ),
			'id'      => $this->get_option_key( 'single_product_generate_lucky_dip_button_label' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Re-generate Lucky Dip Button Label', 'lottery-for-woocommerce' ),
			'type'    => 'textarea',
			'default' => __( 'Re-generate', 'lottery-for-woocommerce' ),
			'id'      => $this->get_option_key( 'single_product_regenerate_lucky_dip_button_label' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Lucky Dip Add to Cart Button Label', 'lottery-for-woocommerce' ),
			'type'    => 'textarea',
			'default' => __( 'Add to Cart', 'lottery-for-woocommerce' ),
			'id'      => $this->get_option_key( 'single_product_lucky_dip_add_to_cart_button_label' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Add More Lucky Dip Button Label', 'lottery-for-woocommerce' ),
			'type'    => 'textarea',
			'default' => __( 'Add More Lucky Dip', 'lottery-for-woocommerce' ),
			'id'      => $this->get_option_key( 'single_product_add_more_lucky_dip_button_label' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Lucky Dip - View Cart Button Label', 'lottery-for-woocommerce' ),
			'type'    => 'textarea',
			'default' => __( 'View Cart', 'lottery-for-woocommerce' ),
			'id'      => $this->get_option_key( 'single_product_lucky_dip_view_cart_button_label' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Generated Lucky Dip Ticket(s) Label', 'lottery-for-woocommerce' ),
			'type'    => 'textarea',
			'default' => __( 'Generated Lucky Dip Ticket Number(s)', 'lottery-for-woocommerce' ),
			'id'      => $this->get_option_key( 'single_product_generated_lucky_dip_tickets_label' ),
		);
		$section_fields[] = array(
			'title'   => __( 'View More Tickets Label for User Chooses the Ticket', 'lottery-for-woocommerce' ),
			'type'    => 'textarea',
			'default' => __( 'View More Tickets', 'lottery-for-woocommerce' ),
			'id'      => $this->get_option_key( 'single_product_view_more_tickets_button_label' ),
		);
		$section_fields[] = array(
			'title'   => __( 'View Less Tickets Label for User Chooses the Ticket', 'lottery-for-woocommerce' ),
			'type'    => 'textarea',
			'default' => __( 'View Less Tickets', 'lottery-for-woocommerce' ),
			'id'      => $this->get_option_key( 'single_product_view_less_tickets_button_label' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Question Answer Label', 'lottery-for-woocommerce' ),
			'type'    => 'textarea',
			'default' => 'Question Answers',
			'id'      => $this->get_option_key( 'single_product_question_answer_heading_label' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Q/A Options for Dropdown Label', 'lottery-for-woocommerce' ),
			'type'    => 'textarea',
			'default' => 'Choose Answer',
			'id'      => $this->get_option_key( 'question_answer_dropdown_default_label' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Question Answer time limit Label', 'lottery-for-woocommerce' ),
			'type'    => 'textarea',
			'default' => 'Time Left to Answer the Question. Hurry Up!!',
			'id'      => $this->get_option_key( 'single_product_question_answer_time_limit_label' ),
		);
		$section_fields[] = array(
			'type' => 'sectionend',
			'id'   => $this->get_option_key( 'single_product_page_messages_section' ),
		);
		// Single Product Page message section End.
		// Single Product tab message Section Start.
		$section_fields[] = array(
			'type'  => 'title',
			'title' => __( 'Single Product Page Tab & Entry List Tickets Log Table Message', 'lottery-for-woocommerce' ),
			'id'    => $this->get_option_key( 'single_product_tab_messages_section' ),
		);

		$section_fields[] = array(
			'title'   => __( 'Giveaway Details Label', 'lottery-for-woocommerce' ),
			'type'    => 'textarea',
			'default' => 'Giveaway Details',
			'id'      => $this->get_option_key( 'single_product_tab_lottery_details_label' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Ticket Search Button Label', 'lottery-for-woocommerce' ),
			'type'    => 'textarea',
			'default' => 'Search Tickets',
			'id'      => $this->get_option_key( 'tickets_search_button_label' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Username Label', 'lottery-for-woocommerce' ),
			'type'    => 'textarea',
			'default' => 'Username',
			'id'      => $this->get_option_key( 'single_product_tab_username_label' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Ticket Number Label', 'lottery-for-woocommerce' ),
			'type'    => 'textarea',
			'default' => 'Ticket Number',
			'id'      => $this->get_option_key( 'single_product_tab_ticket_number_label' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Date Label', 'lottery-for-woocommerce' ),
			'type'    => 'textarea',
			'default' => 'Date',
			'id'      => $this->get_option_key( 'single_product_tab_date_label' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Chosen Answer Label', 'lottery-for-woocommerce' ),
			'type'    => 'textarea',
			'default' => 'Chosen Answer',
			'id'      => $this->get_option_key( 'single_product_tab_answer_label' ),
		);
		$section_fields[] = array(
			'type' => 'sectionend',
			'id'   => $this->get_option_key( 'single_product_tab_messages_section' ),
		);
		// Single Product tab message Section End.
		// Winner labels section start.
		$section_fields[] = array(
			'type'  => 'title',
			'title' => __( 'Winner Labels', 'lottery-for-woocommerce' ),
			'id'    => 'lty_single_product_winner_labels',
		);
		$section_fields[] = array(
			'title'   => __( 'Winners Label', 'lottery-for-woocommerce' ),
			'type'    => 'textarea',
			'default' => 'Winners are',
			'id'      => $this->get_option_key( 'single_product_lottery_winner_label' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Username Label', 'lottery-for-woocommerce' ),
			'type'    => 'textarea',
			'default' => 'Username',
			'id'      => $this->get_option_key( 'single_product_lottery_username_label' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Ticket Number Label', 'lottery-for-woocommerce' ),
			'type'    => 'textarea',
			'default' => 'Ticket Number',
			'id'      => $this->get_option_key( 'single_product_lottery_ticket_number_label' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Gift Products Label', 'lottery-for-woocommerce' ),
			'type'    => 'textarea',
			'default' => 'Gift Products',
			'id'      => $this->get_option_key( 'single_product_lottery_gift_product_label' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Chosen Answer Label', 'lottery-for-woocommerce' ),
			'type'    => 'textarea',
			'default' => 'Chosen Answer',
			'id'      => $this->get_option_key( 'single_product_lottery_answer_label' ),
		);
		$section_fields[] = array(
			'type' => 'sectionend',
			'id'   => 'lty_single_product_winner_labels',
		);
		// Winner labels section end.
		// Instant Win Prizes Section Start.
		$section_fields[] = array(
			'type'  => 'title',
			'title' => __( 'Instant Win Prizes', 'lottery-instant-winners-prizes' ),
			'id'    => 'lty_instant_win_prizes_options',
		);
		$section_fields[] = array(
			'title'   => __( 'Instant Win Prizes Tab Label', 'lottery-instant-winners-prizes' ),
			'type'    => 'textarea',
			'default' => 'Instant Win Prizes',
			'id'      => $this->get_option_key( 'instant_winners_tab_label' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Available Prizes Count Label', 'lottery-instant-winners-prizes' ),
			'type'    => 'textarea',
			'default' => 'Available Prize(s)',
			'id'      => $this->get_option_key( 'instant_winners_available_prizes_count_label' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Won Prizes Count Label', 'lottery-instant-winners-prizes' ),
			'type'    => 'textarea',
			'default' => 'Won Prize(s)',
			'id'      => $this->get_option_key( 'instant_winners_won_prizes_count_label' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Image Column Label', 'lottery-instant-winners-prizes' ),
			'type'    => 'textarea',
			'default' => 'Image',
			'id'      => $this->get_option_key( 'instant_winners_image_column_label' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Ticket Number Column Label', 'lottery-instant-winners-prizes' ),
			'type'    => 'textarea',
			'default' => 'Ticket Number',
			'id'      => $this->get_option_key( 'instant_winners_ticket_column_label' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Prize Column Label', 'lottery-instant-winners-prizes' ),
			'type'    => 'textarea',
			'default' => 'Prize',
			'id'      => $this->get_option_key( 'instant_winners_prize_column_label' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Winner Column Label', 'lottery-instant-winners-prizes' ),
			'type'    => 'textarea',
			'default' => 'Winner',
			'id'      => $this->get_option_key( 'instant_winners_column_label' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Prize Available Label', 'lottery-instant-winners-prizes' ),
			'type'    => 'textarea',
			'default' => 'Prize Available',
			'id'      => $this->get_option_key( 'instant_winners_prize_available_label' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Won Prize(Group Prize) Label', 'lottery-instant-winners-prizes' ),
			'type'    => 'textarea',
			'default' => 'Won',
			'id'      => $this->get_option_key( 'instant_winner_won_prize_label' ),
			'desc'    => __( '<b>Supported Shortcodes:<br/>{instant_winner_name}</b> - Displays the winner username', 'lottery-for-woocommerce' ),
		);
		$section_fields[] = array(
			'type' => 'sectionend',
			'id'   => 'lty_instant_win_prizes_options',
		);
		// Instant Win Prizes Section End.

		return $section_fields;
	}

	/**
	 * Get settings for dashboard section array.
	 *
	 * @return array
	 * */
	public function dashboard_section_array() {

		// Lottery Dashboard message Section Start.
		$section_fields[] = array(
			'type'  => 'title',
			'title' => __( 'Giveaway Dashboard', 'lottery-for-woocommerce' ),
			'id'    => $this->get_option_key( 'dashboard_messages_section' ),
		);
		$section_fields[] = array(
			'title'   => __( 'My Giveaway Label', 'lottery-for-woocommerce' ),
			'type'    => 'textarea',
			'default' => 'My Giveaways',
			'id'      => $this->get_option_key( 'dashboard_my_lottery_label' ),
		);
		$section_fields[] = array(
			'title'   => __( 'User Purchased Ticket count Label', 'lottery-for-woocommerce' ),
			'type'    => 'textarea',
			'default' => 'You have bought {user_ticket_count} ticket(s) for this giveaway!',
			'id'      => $this->get_option_key( 'dashboard_purchased_ticket_count_label' ),
			'desc'    => __( '<b>Supported Shortcodes:<br/>{user_ticket_count}</b> - Displays the user purchased ticket count', 'lottery-for-woocommerce' ),
		);
		$section_fields[] = array(
			'type' => 'sectionend',
			'id'   => $this->get_option_key( 'dashboard_messages_section' ),
		);
		// Lottery Dashboard message Section End.
		// Participated Lotteries message Section Start.
		$section_fields[] = array(
			'type'  => 'title',
			'title' => __( 'Giveaway Dashboard - Participated Giveaways', 'lottery-for-woocommerce' ),
			'id'    => $this->get_option_key( 'dashboard_participated_lottery_messages_section' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Participated Giveaway Label', 'lottery-for-woocommerce' ),
			'type'    => 'textarea',
			'default' => 'Participated Giveaways',
			'id'      => $this->get_option_key( 'dashboard_participated_lottery_label' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Product Name Label ', 'lottery-for-woocommerce' ),
			'type'    => 'textarea',
			'default' => 'Product Name',
			'id'      => $this->get_option_key( 'dashboard_participated_lottery_product_name_label' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Giveaway Duration Label', 'lottery-for-woocommerce' ),
			'type'    => 'textarea',
			'default' => 'Giveaway Duration',
			'id'      => $this->get_option_key( 'dashboard_participated_lottery_duration_label' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Status Label ', 'lottery-for-woocommerce' ),
			'type'    => 'textarea',
			'default' => 'Status',
			'id'      => $this->get_option_key( 'dashboard_participated_lottery_status_label' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Ticket Number Label ', 'lottery-for-woocommerce' ),
			'type'    => 'textarea',
			'default' => 'Ticket Number',
			'id'      => $this->get_option_key( 'dashboard_participated_lottery_ticket_number_label' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Order ID Label', 'lottery-for-woocommerce' ),
			'type'    => 'text',
			'default' => 'Order ID',
			'id'      => $this->get_option_key( 'dashboard_participated_lottery_order_id_label' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Chosen Answer Label', 'lottery-for-woocommerce' ),
			'type'    => 'text',
			'default' => 'Chosen Answer',
			'id'      => $this->get_option_key( 'dashboard_participated_lottery_answer_label' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Giveaway Dashboard - Participated Giveaway URL Parameter', 'lottery-for-woocommerce' ),
			'type'    => 'text',
			'default' => 'lty_participated_giveaway_products',
			'value'   => lty_get_dashboard_participated_lotteries_endpoint_url(),
			'id'      => $this->get_option_key( 'dashboard_participated_lotteries_url_param' ),
		);
		$section_fields[] = array(
			'type' => 'sectionend',
			'id'   => $this->get_option_key( 'dashboard_participated_lottery_messages_section' ),
		);
		// Participated Lotteries message Section End.
		// Won Lotteries message Section Start.
		$section_fields[] = array(
			'type'  => 'title',
			'title' => __( 'Giveaway Dashboard - Won Giveaways', 'lottery-for-woocommerce' ),
			'id'    => $this->get_option_key( 'dashboard_won_lottery_messages_section' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Won Giveaway Label', 'lottery-for-woocommerce' ),
			'type'    => 'textarea',
			'default' => 'Won Giveaways',
			'id'      => $this->get_option_key( 'dashboard_won_lottery_label' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Product Name Label ', 'lottery-for-woocommerce' ),
			'type'    => 'textarea',
			'default' => 'Giveaway Name',
			'id'      => $this->get_option_key( 'dashboard_won_lottery_product_name_label' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Giveaway Duration Label', 'lottery-for-woocommerce' ),
			'type'    => 'textarea',
			'default' => 'Giveaway Duration',
			'id'      => $this->get_option_key( 'dashboard_won_lottery_duration_label' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Status Label', 'lottery-for-woocommerce' ),
			'type'    => 'text',
			'default' => 'Status',
			'id'      => $this->get_option_key( 'dashboard_won_lottery_status_label' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Ticket Number Label', 'lottery-for-woocommerce' ),
			'type'    => 'textarea',
			'default' => 'Ticket Number',
			'id'      => $this->get_option_key( 'dashboard_won_lottery_ticket_number_label' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Gift Product Label', 'lottery-for-woocommerce' ),
			'type'    => 'textarea',
			'default' => 'Gift Product(s)',
			'id'      => $this->get_option_key( 'dashboard_won_lottery_gift_product_label' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Order ID Label', 'lottery-for-woocommerce' ),
			'type'    => 'textarea',
			'default' => 'Order ID',
			'id'      => $this->get_option_key( 'dashboard_won_lottery_order_id_label' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Chosen Answer Label', 'lottery-for-woocommerce' ),
			'type'    => 'text',
			'default' => 'Chosen Answer',
			'id'      => $this->get_option_key( 'dashboard_won_lottery_answer_label' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Giveaway Dashboard - Won Giveaways URL Parameter', 'lottery-for-woocommerce' ),
			'type'    => 'text',
			'default' => 'lty_won_giveaway_products',
			'value'   => lty_get_dashboard_won_lotteries_endpoint_url(),
			'id'      => $this->get_option_key( 'dashboard_won_lotteries_url_param' ),
		);
		$section_fields[] = array(
			'type' => 'sectionend',
			'id'   => $this->get_option_key( 'dashboard_won_lottery_messages_section' ),
		);
		// Won Lottery message Section End.
		// Not Won Lotteries message Section Start.
		$section_fields[] = array(
			'type'  => 'title',
			'title' => __( 'Giveaway Dashboard - Lost Giveaways', 'lottery-for-woocommerce' ),
			'id'    => $this->get_option_key( 'dashboard_not_won_lottery_messages_section' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Lost Giveaways Label', 'lottery-for-woocommerce' ),
			'type'    => 'textarea',
			'default' => 'Lost Giveaways',
			'id'      => $this->get_option_key( 'dashboard_not_won_lottery_label' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Giveaway Name Label', 'lottery-for-woocommerce' ),
			'type'    => 'textarea',
			'default' => 'Giveaway Name',
			'id'      => $this->get_option_key( 'dashboard_not_won_lottery_product_name_label' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Giveaway Duration Label', 'lottery-for-woocommerce' ),
			'type'    => 'textarea',
			'default' => 'Giveaway Duration',
			'id'      => $this->get_option_key( 'dashboard_not_won_lottery_duration_label' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Status Label', 'lottery-for-woocommerce' ),
			'type'    => 'text',
			'default' => 'Status',
			'id'      => $this->get_option_key( 'dashboard_not_won_lottery_status_label' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Ticket Number Label', 'lottery-for-woocommerce' ),
			'type'    => 'textarea',
			'default' => 'Ticket Number',
			'id'      => $this->get_option_key( 'dashboard_not_won_lottery_ticket_number_label' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Chosen Answer Label', 'lottery-for-woocommerce' ),
			'type'    => 'text',
			'default' => 'Chosen Answer',
			'id'      => $this->get_option_key( 'dashboard_not_won_lottery_answer_label' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Giveaway Dashboard - Lost Giveaways URL Parameter', 'lottery-for-woocommerce' ),
			'type'    => 'text',
			'default' => 'lty_not_won_giveaway_products',
			'value'   => lty_get_dashboard_not_won_lotteries_endpoint_url(),
			'id'      => $this->get_option_key( 'dashboard_not_won_lotteries_url_param' ),
		);
		$section_fields[] = array(
			'type' => 'sectionend',
			'id'   => $this->get_option_key( 'dashboard_not_won_lottery_messages_section' ),
		);
		// Not Won Lottery message Section End.

		// Instant Win Section Start.
		$section_fields[] = array(
			'type'  => 'title',
			'title' => __( 'Giveaway Dashboard - Instant Win', 'lottery-for-woocommerce' ),
			'id'    => $this->get_option_key( 'dashboard_instant_win_section' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Instant Win Label', 'lottery-for-woocommerce' ),
			'type'    => 'textarea',
			'default' => 'Instant Win',
			'id'      => $this->get_option_key( 'dashboard_instant_win_label' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Giveaway Name Label', 'lottery-for-woocommerce' ),
			'type'    => 'textarea',
			'default' => 'Giveaway Name',
			'id'      => $this->get_option_key( 'dashboard_instant_win_product_name_label' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Giveaway Duration Label', 'lottery-for-woocommerce' ),
			'type'    => 'textarea',
			'default' => 'Giveaway Duration',
			'id'      => $this->get_option_key( 'dashboard_instant_win_lottery_duration_label' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Order Number Label', 'lottery-for-woocommerce' ),
			'type'    => 'textarea',
			'default' => 'Order Number',
			'id'      => $this->get_option_key( 'dashboard_instant_win_order_id_label' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Ticket Number Label', 'lottery-for-woocommerce' ),
			'type'    => 'textarea',
			'default' => 'Ticket Number',
			'id'      => $this->get_option_key( 'dashboard_instant_win_ticket_number_label' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Prize Details Label', 'lottery-for-woocommerce' ),
			'type'    => 'textarea',
			'default' => 'Prize Details',
			'id'      => $this->get_option_key( 'dashboard_instant_win_prize_details_label' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Giveaway Dashboard - Instant Win URL Parameter', 'lottery-for-woocommerce' ),
			'type'    => 'text',
			'default' => 'lty_instant_win',
			'value'   => lty_get_dashboard_instant_win_endpoint_url(),
			'id'      => $this->get_option_key( 'dashboard_instant_win_url_param' ),
		);
		$section_fields[] = array(
			'type' => 'sectionend',
			'id'   => $this->get_option_key( 'dashboard_instant_win_section' ),
		);
		// Instant Win Section End.

		// My Account page section Start.
		$section_fields[] = array(
			'type'  => 'title',
			'title' => __( 'Giveaway Dashboard - My Account Page', 'lottery-for-woocommerce' ),
			'id'    => $this->get_option_key( 'myaccount_dashboard_section' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Giveaway Dashboard Menu Label', 'lottery-for-woocommerce' ),
			'type'    => 'textarea',
			'default' => 'Giveaway Dashboard',
			'id'      => $this->get_option_key( 'myaccount_lottery_menu_label' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Giveaway Dashboard Menu End Point URL', 'lottery-for-woocommerce' ),
			'type'    => 'text',
			'default' => 'giveaway',
			'id'      => $this->get_option_key( 'myaccount_lottery_menu_endpoint_url' ),
		);
		$section_fields[] = array(
			'type' => 'sectionend',
			'id'   => $this->get_option_key( 'myaccount_dashboard_section' ),
		);
		return $section_fields;
	}

	/**
	 * Get settings for status section array.
	 *
	 * @return array
	 * */
	public function status_section_array() {

		// Lottery status label Section Start.
		$section_fields[] = array(
			'type'  => 'title',
			'title' => __( 'Giveaway Status Label Customization', 'lottery-for-woocommerce' ),
			'id'    => $this->get_option_key( 'lottery_status_labels_section' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Giveaway Not Started Label', 'lottery-for-woocommerce' ),
			'type'    => 'textarea',
			'default' => 'Giveaway Not Started',
			'id'      => $this->get_option_key( 'lottery_not_started_label' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Giveaway Started Label', 'lottery-for-woocommerce' ),
			'type'    => 'textarea',
			'default' => 'Giveaway Started',
			'id'      => $this->get_option_key( 'lottery_started_label' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Giveaway Closed Label', 'lottery-for-woocommerce' ),
			'type'    => 'textarea',
			'default' => 'Giveaway Closed',
			'id'      => $this->get_option_key( 'lottery_closed_label' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Giveaway Finished Label', 'lottery-for-woocommerce' ),
			'type'    => 'textarea',
			'default' => 'Giveaway Finished',
			'id'      => $this->get_option_key( 'lottery_finished_label' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Giveaway Failed Label', 'lottery-for-woocommerce' ),
			'type'    => 'textarea',
			'default' => 'Giveaway Failed',
			'id'      => $this->get_option_key( 'lottery_failed_label' ),
		);
		$section_fields[] = array(
			'type' => 'sectionend',
			'id'   => $this->get_option_key( 'lottery_status_labels_section' ),
		);
		// Lottery status label Section End.

		return $section_fields;
	}

	/**
	 * Get settings for predefined buttons section array.
	 *
	 * @return array
	 * */
	public function predefined_buttons_section_array() {

		// Lottery Predefined Buttons Settings Section Start.
		$section_fields[] = array(
			'type'  => 'title',
			'title' => __( 'Predefined Button Settings(Only applicable for Automatic ticket type)', 'lottery-for-woocommerce' ),
			'id'    => $this->get_option_key( 'predefined_buttons_section' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Predefined Buttons Heading', 'lottery-for-woocommerce' ),
			'type'    => 'textarea',
			'default' => 'Choose an option',
			'id'      => $this->get_option_key( 'predefined_buttons_heading' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Participate Now Button Label', 'lottery-for-woocommerce' ),
			'type'    => 'textarea',
			'default' => 'Participate Now',
			'id'      => $this->get_option_key( 'predefined_buttons_participate_now_label' ),
			'desc'    => __( '<b>Supported Shortcodes:<br/>{lottery_price}</b> - Ticket(s) Price<br/><b>{ticket_quantity}</b> - Ticket(s) Quantity', 'lottery-for-woocommerce' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Predefined Buttons Alert Message', 'lottery-for-woocommerce' ),
			'type'    => 'textarea',
			'default' => 'Please select an option',
			'id'      => $this->get_option_key( 'predefined_buttons_alert_error_message' ),
		);
		$section_fields[] = array(
			'type' => 'sectionend',
			'id'   => $this->get_option_key( 'predefined_buttons_section' ),
		);
		// Lottery Predefined Buttons Settings Section End.

		return $section_fields;
	}

	/**
	 * Get settings for winners list section array.
	 *
	 * @return array
	 * */
	public function winners_list_section_array() {

		// Lottery Winners List Shortcode Settings Section Start.
		$section_fields[] = array(
			'type'  => 'title',
			'title' => __( 'Giveaway Winners List Shortcode Customization', 'lottery-for-woocommerce' ),
			'id'    => $this->get_option_key( 'winners_list_shortcode' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Giveaway Winners List Title', 'lottery-for-woocommerce' ),
			'type'    => 'textarea',
			'default' => 'Giveaway Products Winners',
			'id'      => $this->get_option_key( 'winners_list_shortcode_title' ),
		);
		$section_fields[] = array(
			'title'   => __( 'S.No Label', 'lottery-for-woocommerce' ),
			'type'    => 'textarea',
			'default' => 'S.No',
			'id'      => $this->get_option_key( 'winners_list_shortcode_sno_label' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Winners Name Label', 'lottery-for-woocommerce' ),
			'type'    => 'textarea',
			'default' => 'Winners Name',
			'id'      => $this->get_option_key( 'winners_list_shortcode_winners_name_label' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Ticket Number Label', 'lottery-for-woocommerce' ),
			'type'    => 'textarea',
			'default' => 'Ticket Number',
			'id'      => $this->get_option_key( 'winners_list_shortcode_ticket_number_label' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Product Name Label', 'lottery-for-woocommerce' ),
			'type'    => 'textarea',
			'default' => 'Product Name',
			'id'      => $this->get_option_key( 'winners_list_shortcode_product_name_label' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Start Date Label', 'lottery-for-woocommerce' ),
			'type'    => 'textarea',
			'default' => 'Start Date',
			'id'      => $this->get_option_key( 'winners_list_shortcode_start_date_label' ),
		);
		$section_fields[] = array(
			'title'   => __( 'End Date Label', 'lottery-for-woocommerce' ),
			'type'    => 'textarea',
			'default' => 'End Date',
			'id'      => $this->get_option_key( 'winners_list_shortcode_end_date_label' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Gift Products Label', 'lottery-for-woocommerce' ),
			'type'    => 'textarea',
			'default' => 'Gift Products',
			'id'      => $this->get_option_key( 'winners_list_shortcode_gift_products_label' ),
		);
		$section_fields[] = array(
			'type' => 'sectionend',
			'id'   => $this->get_option_key( 'winners_list_shortcode' ),
		);
		// Lottery Winners List Shortcode Settings Section End.
		return $section_fields;
	}

	/**
	 * Get the settings for entry list section array.
	 *
	 * @since 9.0.0
	 * @return array
	 * */
	public function entry_list_section_array() {
		// Entry List Settings Section Start.
		$section_fields[] = array(
			'type'  => 'title',
			'title' => __( 'Entry List Page Customization', 'lottery-for-woocommerce' ),
			'id'    => $this->get_option_key( 'entry_list_localizations' ),
		);
		$section_fields[] = array(
			'title'   => __( 'View Participants Label', 'lottery-for-woocommerce' ),
			'type'    => 'textarea',
			'default' => 'View Participants',
			'id'      => $this->get_option_key( 'entry_list_view_participants_label' ),
		);
		$section_fields[] = array(
			'type' => 'sectionend',
			'id'   => $this->get_option_key( 'entry_list_localizations' ),
		);
		// Entry List Settings Section End.
		// Single Entry List Settings Section Start.
		$section_fields[] = array(
			'type'  => 'title',
			'title' => __( 'Single Giveaway Entry List Page Customization', 'lottery-for-woocommerce' ),
			'id'    => $this->get_option_key( 'single_entry_list_localizations' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Status Label', 'lottery-for-woocommerce' ),
			'type'    => 'textarea',
			'default' => 'Status:',
			'id'      => $this->get_option_key( 'entry_list_status_label' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Start Date Label', 'lottery-for-woocommerce' ),
			'type'    => 'textarea',
			'default' => 'Start Date:',
			'id'      => $this->get_option_key( 'entry_list_start_date_label' ),
		);
		$section_fields[] = array(
			'title'   => __( 'End Date Label', 'lottery-for-woocommerce' ),
			'type'    => 'textarea',
			'default' => 'End Date:',
			'id'      => $this->get_option_key( 'entry_list_end_date_label' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Winner(s) Count Label', 'lottery-for-woocommerce' ),
			'type'    => 'textarea',
			'default' => 'Winner(s) Count:',
			'id'      => $this->get_option_key( 'entry_list_winner_count_label' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Maximum Tickets Count Label', 'lottery-for-woocommerce' ),
			'type'    => 'textarea',
			'default' => 'Maximum Tickets Count:',
			'id'      => $this->get_option_key( 'entry_list_maximum_tickets_count_label' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Purchased Tickets Count Label', 'lottery-for-woocommerce' ),
			'type'    => 'textarea',
			'default' => 'Purchased Tickets Count:',
			'id'      => $this->get_option_key( 'entry_list_purchased_tickets_count_label' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Remaining Tickets Count Label', 'lottery-for-woocommerce' ),
			'type'    => 'textarea',
			'default' => 'Remaining Tickets Count:',
			'id'      => $this->get_option_key( 'entry_list_remaining_tickets_count_label' ),
		);
		$section_fields[] = array(
			'type' => 'sectionend',
			'id'   => $this->get_option_key( 'single_entry_list_localizations' ),
		);
		// Single Entry List Settings Section End.

		// Entry list pdf settings start.
		$section_fields[] = array(
			'type'  => 'title',
			'title' => __( 'Entry List PDF Labels Customization', 'lottery-for-woocommerce' ),
			'id'    => $this->get_option_key( 'entry_list_pdf_localizations' ),
		);
		$section_fields[] = array(
			'title'   => __( 'PDF Header Details', 'lottery-for-woocommerce' ),
			'type'    => 'textarea',
			'default' => '{site_name} {product_name}',
			'id'      => $this->get_option_key( 'entry_list_pdf_header' ),
			'desc'    => __( '<b>Supported Shortcodes:<br/>{logo}</b> - Displays the logo<br><b>{site_name}</b> - Displays the site name<br><b>{product_name}</b> - Displays the giveaway product name<br>', 'lottery-for-woocommerce' ),
		);
		$section_fields[] = array(
			'title'   => __( 'PDF Footer Details', 'lottery-for-woocommerce' ),
			'type'    => 'textarea',
			'default' => '<b>Ended on:</b> {end_date} &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;  &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;  &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;  &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; <b>Page {PAGENO} </b> of {nb}',
			'id'      => $this->get_option_key( 'entry_list_pdf_footer' ),
			'desc'    => __( '<b>Supported Shortcodes:<br/>{maximum_tickets}</b> - Displays the Total number of tickets<br><b>{end_date}</b> - Displays the giveaway Ended Date & Time<br><b>{PAGENO}</b> - Displays the Current page number<br><b>{nb}</b> - Displays the Total number of pages', 'lottery-for-woocommerce' ),
		);
		$section_fields[] = array(
			'type' => 'sectionend',
			'id'   => $this->get_option_key( 'entry_list_pdf_localizations' ),
		);
		// Entry list pdf footer settings end.

		return $section_fields;
	}

	/**
	 * Get the localizations for other pages section array.
	 *
	 * @since 10.4.0
	 * @return array
	 * */
	public function other_pages_section_array() {
		// Other pages localizations section start.
		$section_fields[] = array(
			'type'  => 'title',
			'title' => __( 'Giveaway Ticket(s) PDF Labels Customization', 'lottery-for-woocommerce' ),
			'id'    => $this->get_option_key( 'other_pages_localizations' ),
		);
		$section_fields[] = array(
			'title'   => __( 'PDF Header Details', 'lottery-for-woocommerce' ),
			'type'    => 'textarea',
			'default' => '',
			'id'      => $this->get_option_key( 'ticket_pdf_header' ),
			'desc'    => __( '<b>Supported Shortcodes:<br/>{site_name}</b> - Displays the site name', 'lottery-for-woocommerce' ),
		);
		$section_fields[] = array(
			'title'   => __( 'PDF Footer Details', 'lottery-for-woocommerce' ),
			'type'    => 'textarea',
			'default' => '',
			'id'      => $this->get_option_key( 'ticket_pdf_footer' ),
			'desc'    => __( '<b>Supported Shortcodes:<br/>{PAGENO}</b> - Displays the Current page number<br><b>{nb}</b> - Displays the Total number of pages', 'lottery-for-woocommerce' ),
		);
		$section_fields[] = array(
			'type' => 'sectionend',
			'id'   => $this->get_option_key( 'other_pages_localizations' ),
		);
		// Other pages localizations section end.

		// Thank you page settings start.
		$section_fields[] = array(
			'type'  => 'title',
			'title' => __( 'Thank you Page Labels Customization', 'lottery-for-woocommerce' ),
			'id'    => $this->get_option_key( 'thankyou_page_localizations' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Instant Win Prize Details Heading', 'lottery-for-woocommerce' ),
			'type'    => 'textarea',
			'default' => 'Congratulations! You have won the Instant Win Prize for the following ticket number(s)',
			'id'      => $this->get_option_key( 'order_instant_winners_heading' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Product Name Label ', 'lottery-for-woocommerce' ),
			'type'    => 'textarea',
			'default' => 'Product Name',
			'id'      => $this->get_option_key( 'order_instant_winners_product_name_label' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Ticket Number Label ', 'lottery-for-woocommerce' ),
			'type'    => 'textarea',
			'default' => 'Ticket Number',
			'id'      => $this->get_option_key( 'order_instant_winners_ticket_number_label' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Instant Win Image Label ', 'lottery-for-woocommerce' ),
			'type'    => 'textarea',
			'default' => 'Instant Win Image',
			'id'      => $this->get_option_key( 'order_instant_winners_image_label' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Instant Win Prize Label ', 'lottery-for-woocommerce' ),
			'type'    => 'textarea',
			'default' => ' Instant Win Prize',
			'id'      => $this->get_option_key( 'order_instant_winners_prize_label' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Instant Win Prize Better Luck Message(Thank you Page)', 'lottery-for-woocommerce' ),
			'type'    => 'textarea',
			'default' => 'No Instant Win Prize at this time, better luck next time',
			'id'      => $this->get_option_key( 'thankyou_page_instant_win_better_luck_msg' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Instant Win Prize Better Luck Message(Order Details Page)', 'lottery-for-woocommerce' ),
			'type'    => 'textarea',
			'default' => 'You have not won the Instant Win Prize for this order.',
			'id'      => $this->get_option_key( 'order_details_page_instant_win_better_luck_msg' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Free Product or Tickets Message for Instant Win Prize(Thank you Page)', 'lottery-for-woocommerce' ),
			'type'    => 'textarea',
			'default' => 'Congratulations! You have won {gift_product_name} as instant win prize for purchasing ticket number <b>{ticket_number}</b> in the <b>{lottery_product_name}</b>',
			'id'      => $this->get_option_key( 'thankyou_page_instant_win_gift_product_msg' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Free Product or Tickets Message for Instant Win Prize(Order Details Page)', 'lottery-for-woocommerce' ),
			'type'    => 'textarea',
			'default' => 'You have won {gift_product_name} as instant win prize for purchasing ticket number <b>{ticket_number}</b> in the <b>{lottery_product_name}</b>',
			'id'      => $this->get_option_key( 'order_details_page_instant_win_gift_product_msg' ),
		);
		$section_fields[] = array(
			'type' => 'sectionend',
			'id'   => $this->get_option_key( 'thankyou_page_localizations' ),
		);
		// Thank you page settings start.

		return $section_fields;
	}
}

return new LTY_Localization_Tab();
