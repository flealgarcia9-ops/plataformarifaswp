<?php

/**
 * Advanced Tab.
 * */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( class_exists( 'LTY_Advanced_Tab' ) ) {
	return new LTY_Advanced_Tab();
}

/**
 * LTY_Advanced_Tab.
 * */
class LTY_Advanced_Tab extends LTY_Settings_Page {

	/**
	 * Constructor.
	 * */
	public function __construct() {

		$this->id    = 'advanced';
		$this->label = __( 'Advanced', 'lottery-for-woocommerce' );

		// Display the cron information.
		add_action( 'woocommerce_admin_field_lty_display_cron_information', array( $this, 'display_cron_information' ) );

		parent::__construct();
	}

	/**
	 * Get sections.
	 *
	 * @return array
	 */
	public function get_sections() {
		$sections = array(
			'advanced'            => __( 'General', 'lottery-for-woocommerce' ),
			'cron'                => __( 'Cron', 'lottery-for-woocommerce' ),
			'color_customization' => __( 'Color Customization', 'lottery-for-woocommerce' ),
		);

		/**
		 * This hook is used to alter the current tab sections.
		 *
		 * @since 1.0
		 */
		return apply_filters( $this->plugin_slug . '_get_sections_' . $this->id, $sections );
	}

	/**
	 * Get settings for advanced section array.
	 *
	 * @return array
	 * */
	public function advanced_section_array() {

		$section_fields = array();
		// IP Address Restriction Settings Section Start.
		$section_fields[] = array(
			'type'  => 'title',
			'title' => __( 'IP Address Restriction Settings', 'lottery-for-woocommerce' ),
			'id'    => 'lty_ip_address_restriction_options',
		);
		$section_fields[] = array(
			'title'   => __( 'Restrict Users to Purchase Giveaway Ticket based on IP Address', 'lottery-for-woocommerce' ),
			'type'    => 'checkbox',
			'default' => 'no',
			'desc'    => __( 'Prevent Multiple Users from the Same IP Address to Participate in the Same Giveaway.', 'lottery-for-woocommerce' ),
			'id'      => $this->get_option_key( 'validate_user_ip_address' ),
		);
		$section_fields[] = array(
			'type' => 'sectionend',
			'id'   => 'lty_ip_address_restriction_options',
		);
		// IP Address Restriction Settings Section End.

		// Troubleshoot Settings Section Start.
		$section_fields[] = array(
			'type'  => 'title',
			'title' => __( 'Troubleshoot Settings', 'lottery-for-woocommerce' ),
			'id'    => 'lty_troubleshoot_options',
		);
		$section_fields[] = array(
			'title'   => __( 'Prevent Creating Giveaway Tickets through Rest API', 'lottery-for-woocommerce' ),
			'type'    => 'checkbox',
			'id'      => $this->get_option_key( 'prevent_create_ticket_via_rest_api' ),
			'default' => 'no',
		);
		$section_fields[] = array(
			'title'    => __( 'Giveaway Product Page Loading Mode', 'lottery-for-woocommerce' ),
			'type'     => 'select',
			'default'  => '1',
			'options'  => array(
				'1' => __( 'Default (Load with page)', 'lottery-for-woocommerce' ),
				'2' => __( 'AJAX Load (Load dynamically after page load)', 'lottery-for-woocommerce' ),
			),
			'id'       => $this->get_option_key( 'product_page_loading_mode' ),
			'desc_tip' => true,
		);
		$section_fields[] = array(
			'title'   => __( 'Auto Delete Ticket Entries in Giveaway Post table When Order is Deleted', 'lottery-for-woocommerce' ),
			'type'    => 'checkbox',
			'id'      => $this->get_option_key( 'delete_lottery_ticket_data_on_order_delete' ),
			'default' => 'no',
			'desc'    => __( 'Enable this option to automatically remove related giveaway ticket entries whenever the corresponding WooCommerce order is deleted.', 'lottery-for-woocommerce' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Hold Tickets in the Database to Prevent Duplicate Tickets During Simultaneous Purchases.', 'lottery-for-woocommerce' ),
			'type'    => 'checkbox',
			'id'      => $this->get_option_key( 'restrict_db_hold_tickets' ),
			'default' => 'no',
		);
		$section_fields[] = array(
			'type' => 'sectionend',
			'id'   => 'lty_troubleshoot_options',
		);
		// Troubleshoot Settings Section End.

		// Custom CSS Section Start.
		$section_fields[] = array(
			'type'  => 'title',
			'title' => __( 'Custom CSS Settings', 'lottery-for-woocommerce' ),
			'id'    => 'lty_custom_css_options',
		);
		$section_fields[] = array(
			'title'             => __( 'Custom CSS', 'lottery-for-woocommerce' ),
			'type'              => 'textarea',
			'default'           => '',
			'custom_attributes' => array( 'rows' => 10 ),
			'id'                => $this->get_option_key( 'custom_css' ),
		);
		$section_fields[] = array(
			'type' => 'sectionend',
			'id'   => 'lty_custom_css_options',
		);
		// Custom CSS Section End.

		return $section_fields;
	}

	/**
	 * Get the settings for color customization section array.
	 *
	 * @return array
	 * */
	public function color_customization_section_array() {
		$section_fields = array();

		// Progress bar on single product page section start.
		$section_fields[] = array(
			'type'  => 'title',
			'title' => __( 'Progress Bar on Single Product Page', 'lottery-for-woocommerce' ),
			'id'    => 'lty_progress_bar_single_product_options',
		);
		$section_fields[] = array(
			'title'   => __( 'Progress Bar Background Color', 'lottery-for-woocommerce' ),
			'type'    => 'color',
			'css'     => 'width:6em;',
			'default' => '#f3efe6',
			'id'      => $this->get_option_key( 'single_product_progress_bar_bg_color' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Progress Bar Fill Color', 'lottery-for-woocommerce' ),
			'type'    => 'color',
			'css'     => 'width:6em;',
			'default' => '#00cc00',
			'id'      => $this->get_option_key( 'single_product_progress_bar_fill_color' ),
		);
		$section_fields[] = array(
			'type' => 'sectionend',
			'id'   => 'lty_progress_bar_single_product_options',
		);
		// Progress bar on single product section end.
		// Countdown timer on single product page section start.
		$section_fields[] = array(
			'type'  => 'title',
			'title' => __( 'Countdown Timer on Single Product Page', 'lottery-for-woocommerce' ),
			'id'    => 'lty_count_down_timer_single_product_options',
		);
		$section_fields[] = array(
			'title'   => __( 'Countdown Timer Number Color', 'lottery-for-woocommerce' ),
			'type'    => 'color',
			'css'     => 'width:6em;',
			'default' => '#000000',
			'id'      => $this->get_option_key( 'single_product_timer_time_color' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Countdown Timer Text Color', 'lottery-for-woocommerce' ),
			'type'    => 'color',
			'css'     => 'width:6em;',
			'default' => '#000000',
			'id'      => $this->get_option_key( 'single_product_timer_label_color' ),
		);
		$section_fields[] = array(
			'type' => 'sectionend',
			'id'   => 'lty_count_down_timer_single_product_options',
		);
		// Count down timer in single product section end.
		// Ticket design in single product page section start.
		$section_fields[] = array(
			'type'  => 'title',
			'title' => __( 'Ticket Number on Single Product Page', 'lottery-for-woocommerce' ),
			'id'    => 'lty_ticket_design_single_product_options',
		);
		$section_fields[] = array(
			'title'   => __( 'Ticket Border Color', 'lottery-for-woocommerce' ),
			'type'    => 'color',
			'css'     => 'width:6em;',
			'default' => '#e0e0e0',
			'id'      => $this->get_option_key( 'single_product_ticket_border_color' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Ticket Number Color', 'lottery-for-woocommerce' ),
			'type'    => 'color',
			'css'     => 'width:6em;',
			'default' => '#444',
			'id'      => $this->get_option_key( 'single_product_ticket_number_color' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Active Ticket Background Color', 'lottery-for-woocommerce' ),
			'type'    => 'color',
			'css'     => 'width:6em;',
			'default' => '#00cc00',
			'id'      => $this->get_option_key( 'single_product_active_ticket_bg_color' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Active Ticket Number Color', 'lottery-for-woocommerce' ),
			'type'    => 'color',
			'css'     => 'width:6em;',
			'default' => '#fff',
			'id'      => $this->get_option_key( 'single_product_active_ticket_number_color' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Booked Ticket Background Color', 'lottery-for-woocommerce' ),
			'type'    => 'color',
			'css'     => 'width:6em;',
			'default' => '#ff1111',
			'id'      => $this->get_option_key( 'single_product_booked_ticket_bg_color' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Booked Ticket Number Color', 'lottery-for-woocommerce' ),
			'type'    => 'color',
			'css'     => 'width:6em;',
			'default' => '#fff',
			'id'      => $this->get_option_key( 'single_product_booked_ticket_number_color' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Reserved Ticket Background Color', 'lottery-for-woocommerce' ),
			'type'    => 'color',
			'css'     => 'width:6em;',
			'default' => '#00a1c9',
			'id'      => $this->get_option_key( 'single_product_reserved_ticket_bg_color' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Reserved Ticket Number Color', 'lottery-for-woocommerce' ),
			'type'    => 'color',
			'css'     => 'width:6em;',
			'default' => '#ffffff',
			'id'      => $this->get_option_key( 'single_product_reserved_ticket_number_color' ),
		);
		$section_fields[] = array(
			'type' => 'sectionend',
			'id'   => 'lty_ticket_design_single_product_options',
		);
		// Ticket design on single product page section end.
		// Tab design on single product page section start.
		$section_fields[] = array(
			'type'  => 'title',
			'title' => __( 'Ticket Number Navigation Tab on Single Product Page', 'lottery-for-woocommerce' ),
			'id'    => 'lty_tab_design_single_product_options',
		);
		$section_fields[] = array(
			'title'   => __( 'Background Color', 'lottery-for-woocommerce' ),
			'type'    => 'color',
			'css'     => 'width:6em;',
			'default' => '#eeeeee',
			'id'      => $this->get_option_key( 'single_product_tab_bg_color' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Text Color', 'lottery-for-woocommerce' ),
			'type'    => 'color',
			'css'     => 'width:6em;',
			'default' => '#333333',
			'id'      => $this->get_option_key( 'single_product_tab_text_color' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Active Tab Background Color', 'lottery-for-woocommerce' ),
			'type'    => 'color',
			'css'     => 'width:6em;',
			'default' => '#ccc',
			'id'      => $this->get_option_key( 'single_product_active_tab_bg_color' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Active Tab Text Color', 'lottery-for-woocommerce' ),
			'type'    => 'color',
			'css'     => 'width:6em;',
			'default' => '#333333',
			'id'      => $this->get_option_key( 'single_product_active_tab_text_color' ),
		);
		$section_fields[] = array(
			'type' => 'sectionend',
			'id'   => 'lty_tab_design_single_product_options',
		);
		// Tab design on single product page section end.
		// Q & A design on single product page section start.
		$section_fields[] = array(
			'type'  => 'title',
			'title' => __( 'Q & A on Single Product Page', 'lottery-for-woocommerce' ),
			'id'    => 'lty_qa_design_single_product_options',
		);
		$section_fields[] = array(
			'title'   => __( 'Answer Options Text Color', 'lottery-for-woocommerce' ),
			'type'    => 'color',
			'css'     => 'width:6em;',
			'default' => '#000',
			'id'      => $this->get_option_key( 'single_product_answer_text_color' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Border Color', 'lottery-for-woocommerce' ),
			'type'    => 'color',
			'css'     => 'width:6em;',
			'default' => '#ccc',
			'id'      => $this->get_option_key( 'single_product_answer_border_color' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Active Answer Background Color', 'lottery-for-woocommerce' ),
			'type'    => 'color',
			'css'     => 'width:6em;',
			'default' => '#00cc00',
			'id'      => $this->get_option_key( 'single_product_active_answer_bg_color' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Active Answer Text Color', 'lottery-for-woocommerce' ),
			'type'    => 'color',
			'css'     => 'width:6em;',
			'default' => '#fff',
			'id'      => $this->get_option_key( 'single_product_active_answer_text_color' ),
		);
		$section_fields[] = array(
			'type' => 'sectionend',
			'id'   => 'lty_qa_design_single_product_options',
		);
		// Q & A design in single product page section end.
		// Predefined buttons design on single product page section start.
		$section_fields[] = array(
			'type'  => 'title',
			'title' => __( 'Predefined Buttons on Single Product Page', 'lottery-for-woocommerce' ),
			'id'    => 'lty_predefined_buttons_design_single_product_options',
		);
		$section_fields[] = array(
			'title'   => __( 'Predefined Buttons Text Color', 'lottery-for-woocommerce' ),
			'type'    => 'color',
			'css'     => 'width:6em;',
			'default' => '#000',
			'id'      => $this->get_option_key( 'single_product_predefined_buttons_text_color' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Predefined Buttons Background Color', 'lottery-for-woocommerce' ),
			'type'    => 'color',
			'css'     => 'width:6em;',
			'default' => '#ccc',
			'id'      => $this->get_option_key( 'single_product_predefined_buttons_bg_color' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Border Color', 'lottery-for-woocommerce' ),
			'type'    => 'color',
			'css'     => 'width:6em;',
			'default' => '#ccc',
			'id'      => $this->get_option_key( 'single_product_predefined_buttons_border_color' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Active Predefined Buttons Background Color', 'lottery-for-woocommerce' ),
			'type'    => 'color',
			'css'     => 'width:6em;',
			'default' => '#00cc00',
			'id'      => $this->get_option_key( 'single_product_active_predefined_buttons_bg_color' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Active Predefined Buttons Text Color', 'lottery-for-woocommerce' ),
			'type'    => 'color',
			'css'     => 'width:6em;',
			'default' => '#fff',
			'id'      => $this->get_option_key( 'single_product_active_predefined_buttons_text_color' ),
		);
		$section_fields[] = array(
			'type' => 'sectionend',
			'id'   => 'lty_predefined_buttons_design_single_product_options',
		);
		// Predefined buttons design on single product page section end.
		// Batch color section start.
		$section_fields[] = array(
			'type'  => 'title',
			'title' => __( 'Badge Color Customization', 'lottery-for-woocommerce' ),
			'id'    => 'lty_batch_color_options',
		);
		$section_fields[] = array(
			'title'   => __( 'Badge Background Color', 'lottery-for-woocommerce' ),
			'type'    => 'color',
			'css'     => 'width:6em;',
			'default' => '#3366ff',
			'id'      => $this->get_option_key( 'lottery_batch_bg_color' ),
		);
		$section_fields[] = array(
			'type' => 'sectionend',
			'id'   => 'lty_batch_color_options',
		);
		// Batch color section end.
		// Count down timer in shop , category and tag page section start.
		$section_fields[] = array(
			'type'  => 'title',
			'title' => __( 'Countdown Timer Shop, Category and Tag Page Settings', 'lottery-for-woocommerce' ),
			'id'    => 'lty_count_down_timer_options',
		);
		$section_fields[] = array(
			'title'   => __( 'Countdown Timer Background Color', 'lottery-for-woocommerce' ),
			'type'    => 'color',
			'css'     => 'width:6em;',
			'default' => '#f1f1f1',
			'id'      => $this->get_option_key( 'timer_bg_color' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Countdown Timer Text Color', 'lottery-for-woocommerce' ),
			'type'    => 'color',
			'css'     => 'width:6em;',
			'default' => '#3c763d',
			'id'      => $this->get_option_key( 'timer_label_color' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Countdown Timer Number Color', 'lottery-for-woocommerce' ),
			'type'    => 'color',
			'css'     => 'width:6em;',
			'default' => '#000000',
			'id'      => $this->get_option_key( 'timer_time_color' ),
		);
		$section_fields[] = array(
			'type' => 'sectionend',
			'id'   => 'lty_count_down_timer_options',
		);
		// Count down timer in shop, category and tag page section end.

		// Instant winner prize on single product page section start.
		$section_fields[] = array(
			'type'  => 'title',
			'title' => __( 'Instant Win Prize Color Customization', 'lottery-for-woocommerce' ),
			'id'    => 'lty_instant_winner_prize_options',
		);
		$section_fields[] = array(
			'title'   => __( 'Available Ticket Background Color', 'lottery-for-woocommerce' ),
			'type'    => 'color',
			'css'     => 'width:6em;',
			'default' => '#0bcc4c',
			'id'      => $this->get_option_key( 'instant_win_group_prize_available_bg_color' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Won Ticket Background Color', 'lottery-for-woocommerce' ),
			'type'    => 'color',
			'css'     => 'width:6em;',
			'default' => '#f44c2e',
			'id'      => $this->get_option_key( 'instant_win_group_prize_won_bg_color' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Ticket Number Text Color', 'lottery-for-woocommerce' ),
			'type'    => 'color',
			'css'     => 'width:6em;',
			'default' => '#ffffff',
			'id'      => $this->get_option_key( 'instant_win_group_ticket_number_label_color' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Available Prize Text Color', 'lottery-for-woocommerce' ),
			'type'    => 'color',
			'css'     => 'width:6em;',
			'default' => '#009933',
			'id'      => $this->get_option_key( 'instant_win_group_available_prize_label_color' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Won Prize Text Color', 'lottery-for-woocommerce' ),
			'type'    => 'color',
			'css'     => 'width:6em;',
			'default' => '#e64c31',
			'id'      => $this->get_option_key( 'instant_win_group_won_prize_label_color' ),
		);
		$section_fields[] = array(
			'type' => 'sectionend',
			'id'   => 'lty_instant_winner_prize_options',
		);
		// Instant winner prize on single product page section end.

		// Dashboard color section start.
		$section_fields[] = array(
			'type'  => 'title',
			'title' => __( 'Dashboard Color Customization ', 'lottery-for-woocommerce' ),
			'id'    => 'lty_count_down_timer_single_product_options',
		);
		$section_fields[] = array(
			'title'   => __( 'Menu Background Color', 'lottery-for-woocommerce' ),
			'type'    => 'color',
			'css'     => 'width:6em;',
			'default' => '#f7f7f7',
			'id'      => $this->get_option_key( 'dashboard_menu_bg_color' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Menu Color', 'lottery-for-woocommerce' ),
			'type'    => 'color',
			'css'     => 'width:6em;',
			'default' => '#000000',
			'id'      => $this->get_option_key( 'dashboard_menu_color' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Current Menu Active Color', 'lottery-for-woocommerce' ),
			'type'    => 'color',
			'css'     => 'width:6em;',
			'default' => '#000000',
			'id'      => $this->get_option_key( 'dashboard_current_menu_color' ),
		);
		$section_fields[] = array(
			'type' => 'sectionend',
			'id'   => 'lty_count_down_timer_single_product_options',
		);
		// Dashboard color section end.

		// Thank you page color customization start.
		$section_fields[] = array(
			'type'  => 'title',
			'title' => __( 'Thank You Page & Order Details Page Color Customization ', 'lottery-for-woocommerce' ),
			'id'    => 'lty_thankyou_page_color_options',
		);
		$section_fields[] = array(
			'title'   => __( 'Instant Win Details Heading Color', 'lottery-for-woocommerce' ),
			'type'    => 'color',
			'css'     => 'width: 6em;',
			'default' => '#358014',
			'id'      => $this->get_option_key( 'order_instant_winners_heading_color' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Instant Win Better Luck Message Color', 'lottery-for-woocommerce' ),
			'type'    => 'color',
			'css'     => 'width: 6em;',
			'default' => '#ff0000',
			'id'      => $this->get_option_key( 'order_instant_win_better_luck_msg_color' ),
		);
		$section_fields[] = array(
			'type' => 'sectionend',
			'id'   => 'lty_thankyou_page_color_options',
		);
		// Thank you page color customization end.

		return $section_fields;
	}

	/**
	 * Get the settings for cron section.
	 *
	 * @retrun array
	 * */
	public function cron_section_array() {

		// Cron section start.
		$section_fields[] = array(
			'type'  => 'title',
			'title' => __( 'Cron Settings', 'lottery-for-woocommerce' ),
			'id'    => 'lty_cron_options',
		);
		$section_fields[] = array(
			'title'   => __( 'Cron Type', 'lottery-for-woocommerce' ),
			'type'    => 'select',
			'default' => '1',
			'options' => array(
				'1' => __( 'Server Cron', 'lottery-for-woocommerce' ),
				'2' => __( 'WP Cron', 'lottery-for-woocommerce' ),
			),
			'id'      => $this->get_option_key( 'cron_type_selection' ),
		);
		$section_fields[] = array(
			'title'       => __( 'Cron Trigger Frequency', 'lottery-for-woocommerce' ),
			'type'        => 'lty_custom_fields',
			'lty_field'   => 'relative_date_selector',
			'option_type' => '1',
			'default'     => array(
				'unit'   => 'minutes',
				'number' => '5',
			),
			'id'          => $this->get_option_key( 'wp_cron_time' ),
			'class'       => 'lty-wp-cron-field',
		);
		$section_fields[] = array(
			'title'       => __( 'Relist Cron Trigger Frequency', 'lottery-for-woocommerce' ),
			'type'        => 'lty_custom_fields',
			'lty_field'   => 'relative_date_selector',
			'class'       => 'lty_wp_cron_time lty-wp-cron-field',
			'option_type' => '1',
			'default'     => array(
				'unit'   => 'minutes',
				'number' => '5',
			),
			'id'          => $this->get_option_key( 'relist_wp_cron_time' ),
		);
		$section_fields[] = array(
			'title'       => __( 'Ending Soon Cron Trigger Frequency', 'lottery-for-woocommerce' ),
			'type'        => 'lty_custom_fields',
			'lty_field'   => 'relative_date_selector',
			'class'       => 'lty_wp_cron_time lty-wp-cron-field',
			'option_type' => '1',
			'default'     => array(
				'unit'   => 'minutes',
				'number' => '5',
			),
			'id'          => $this->get_option_key( 'ending_soon_wp_cron_time' ),
		);
		$section_fields[] = array(
			'type' => 'sectionend',
			'id'   => 'lty_cron_options',
		);
		// Cron section end.
		// Cron information section start.
		$section_fields[] = array(
			'type'  => 'title',
			'title' => __( 'Cron Information', 'lottery-for-woocommerce' ),
			'id'    => 'lty_cron_information_options',
		);
		$section_fields[] = array(
			'type' => 'lty_display_cron_information',
		);
		$section_fields[] = array(
			'type' => 'sectionend',
			'id'   => 'lty_cron_information_options',
		);
		// Cron information section end.

		return $section_fields;
	}

	/**
	 * Display the cron information
	 *
	 * @return void
	 * */
	public function display_cron_information() {

		$cron_info = self::get_cron_info();

		include_once LTY_ABSPATH . 'inc/admin/menu/views/html-cron-info.php';
	}

	/**
	 * Get the server cron information.
	 *
	 * @return array.
	 * */
	public static function get_cron_info() {

		if ( '2' != get_option( 'lty_settings_cron_type_selection' ) ) {
			$last_updated_date        = get_option( 'lty_update_server_cron_last_updated_date' );
			$relist_last_updated_date = get_option( 'lty_relist_server_cron_last_updated_date' );
			$ending_soon_last_updated_date = get_option( 'lty_ending_soon_server_cron_last_updated_date' ) ;
			$cron_name                = __( 'Server Cron', 'lottery-for-woocommerce' );
		} else {
			$last_updated_date        = get_option( 'lty_update_wp_cron_last_updated_date' );
			$relist_last_updated_date = get_option( 'lty_relist_wp_cron_last_updated_date' );
			$ending_soon_last_updated_date = get_option( 'lty_ending_soon_wp_cron_last_updated_date' ) ;
			$cron_name                = __( 'WP Cron', 'lottery-for-woocommerce' );
		}

		$cron_info = array(
			'update' => array(
				'cron'              => __( 'Update Cron', 'lottery-for-woocommerce' ),
				'last_updated_date' => LTY_Date_Time::get_wp_format_datetime_from_gmt( $last_updated_date, false, ' ', true ),
			),
			'relist' => array(
				'cron'              => __( 'Relist Cron', 'lottery-for-woocommerce' ),
				'last_updated_date' => LTY_Date_Time::get_wp_format_datetime_from_gmt( $relist_last_updated_date, false, ' ', true ),
			),
			'ending_soon' => array(
				'cron'              => __( 'Ending Soon Cron', 'lottery-for-woocommerce' ),
				'last_updated_date' => LTY_Date_Time::get_wp_format_datetime_from_gmt( $ending_soon_last_updated_date, false, ' ', true ),
			),
		);

		return $cron_info;
	}
}

return new LTY_Advanced_Tab();
