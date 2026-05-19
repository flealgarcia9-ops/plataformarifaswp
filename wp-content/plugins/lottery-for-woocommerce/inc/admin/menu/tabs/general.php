<?php
/**
 * General Tab.
 *
 * @since 1.0.0
 * */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( class_exists( 'LTY_General_Tab' ) ) {
	return new LTY_General_Tab();
}

/**
 * LTY_General_Tab.
 * */
class LTY_General_Tab extends LTY_Settings_Page {

	/**
	 * Constructor.
	 * */
	public function __construct() {
		$this->id    = 'general';
		$this->label = __( 'General', 'lottery-for-woocommerce' );

		// Display the question answers global.
		add_action( 'woocommerce_admin_field_lty_question_answers_global', array( $this, 'display_question_answers' ) );
		// Save Question answers field.
		add_action( 'woocommerce_admin_settings_sanitize_option_lty_question_answers_global', array( $this, 'save_question_answers' ), 10, 3 );

		parent::__construct();
	}

	/**
	 * Get the sections.
	 *
	 * @return array
	 */
	public function get_sections() {
		$sections = array(
			'general'             => __( 'General', 'lottery-for-woocommerce' ),
			'question_answer'     => __( 'Question Answer', 'lottery-for-woocommerce' ),
			'ticket_generation'   => __( 'Ticket Generation', 'lottery-for-woocommerce' ),
			'other_pages'         => __( 'Shop, Category and  Other Pages', 'lottery-for-woocommerce' ),
			'single_product_page' => __( 'Single Product Page', 'lottery-for-woocommerce' ),
			'lottery_details'     => __( 'Giveaway Tickets Details', 'lottery-for-woocommerce' ),
			'winner'              => __( 'Winner Details', 'lottery-for-woocommerce' ),
			'pdf'                 => __( 'PDF', 'lottery-for-woocommerce' ),
			'instant_winner'      => __( 'Instant Win', 'lottery-for-woocommerce' ),
		);

		/**
		 * This hook is used to alter the current tab sections.
		 *
		 * @since 1.0
		 */
		return apply_filters( $this->plugin_slug . '_get_sections_' . $this->id, $sections );
	}

	/**
	 * Get the settings for general section array.
	 *
	 * @return array
	 * */
	public function general_section_array() {
		$section_fields    = array();
		$wc_order_statuses = lty_get_wc_order_statuses();

		// General section start.
		$section_fields[] = array(
			'type'  => 'title',
			'title' => __( 'General Settings', 'lottery-for-woocommerce' ),
			'id'    => 'lty_general_options',
		);
		$section_fields[] = array(
			'title'    => __( 'Giveaway Page', 'lottery-for-woocommerce' ),
			'id'       => 'woocommerce_lty_lottery_page_id',
			'default'  => wc_get_page_id( 'lty_lottery' ),
			'type'     => 'single_select_page',
			'class'    => 'wc-enhanced-select-nostd',
			'args'     => array(
				'exclude' =>
				array(
					wc_get_page_id( 'cart' ),
					wc_get_page_id( 'checkout' ),
				),
			),
			'desc_tip' => true,
			'desc'     => __( 'All giveaway products will be displayed on the selected page. Note: If giveaway products are not displayed on the selected page, please change the permalink structure to Post name (WordPress Dashboard → Settings → Permalinks → Permalink structure → Post name).', 'lottery-for-woocommerce' ),
			'autoload' => false,
		);
		$section_fields[] = array(
			'title'   => __( 'Default Sorting on Giveaway Page', 'lottery-for-woocommerce' ),
			'type'    => 'select',
			'default' => get_option( 'woocommerce_default_catalog_orderby' ),
			/**
			 * This hook is used to alter the WooCommerce default catalog order by options.
			 *
			 * @since 1.0
			 */
			'options' => apply_filters(
				'woocommerce_default_catalog_orderby_options',
				array(
					'menu_order'                  => __( 'Default sorting (custom ordering + name)', 'lottery-for-woocommerce' ),
					'popularity'                  => __( 'Popularity (sales)', 'lottery-for-woocommerce' ),
					'rating'                      => __( 'Average rating', 'lottery-for-woocommerce' ),
					'date'                        => __( 'Sort by most recent', 'lottery-for-woocommerce' ),
					'price'                       => __( 'Sort by price: low to high', 'lottery-for-woocommerce' ),
					'price-desc'                  => __( 'Sort by price: high to low', 'lottery-for-woocommerce' ),
					'ticket_count'                => __( 'Sort by most ticket sale on-going giveaways', 'lottery-for-woocommerce' ),
					'remaining_ticket_count'      => __( 'Sort by remaining tickets: low to high', 'lottery-for-woocommerce' ),
					'remaining_ticket_count-desc' => __( 'Sort by remaining tickets: high to low', 'lottery-for-woocommerce' ),
					'recently_started'            => __( 'Sort by recently started giveaways', 'lottery-for-woocommerce' ),
					'ending_soon'                 => __( 'Sort by ending soon giveaways', 'lottery-for-woocommerce' ),
					'closed'                      => __( 'Sort by closed giveaways', 'lottery-for-woocommerce' ),
					'on_going'                    => __( 'Sort by on-going giveaways', 'lottery-for-woocommerce' ),
					'future'                      => __( 'Sort by future giveaways', 'lottery-for-woocommerce' ),
					'failed'                      => __( 'Sort by failed giveaways', 'lottery-for-woocommerce' ),
					'finished'                    => __( 'Sort by finished giveaways', 'lottery-for-woocommerce' ),
				)
			),
			'id'      => $this->get_option_key( 'default_lottery_orderby' ),
		);
		$section_fields[] = array(
			'title'    => __( 'Giveaway Entry List Page', 'lottery-for-woocommerce' ),
			'id'       => 'woocommerce_lty_lottery_entry_list_page_id',
			'default'  => wc_get_page_id( 'lty_lottery_entry_list' ),
			'type'     => 'single_select_page',
			'class'    => 'wc-enhanced-select-nostd',
			'args'     => array(
				'exclude' =>
				array(
					wc_get_page_id( 'cart' ),
					wc_get_page_id( 'checkout' ),
				),
			),
			'desc_tip' => true,
			'desc'     => __( 'Giveaway products entry list will be displayed on the selected page', 'lottery-for-woocommerce' ),
			'autoload' => false,
		);
		$section_fields[] = array(
			'title'   => __( 'Default Sorting in Entry List Page', 'lottery-for-woocommerce' ),
			'type'    => 'select',
			'default' => 'menu_order',
			/**
			 * This hook is used to alter the WooCommerce default catalog order by options.
			 *
			 * @since 1.0
			 */
			'options' => apply_filters(
				'woocommerce_default_catalog_orderby_options',
				array(
					'menu_order'                  => __( 'Default sorting (custom ordering + name)', 'lottery-for-woocommerce' ),
					'popularity'                  => __( 'Popularity (sales)', 'lottery-for-woocommerce' ),
					'rating'                      => __( 'Average rating', 'lottery-for-woocommerce' ),
					'date'                        => __( 'Sort by most recent', 'lottery-for-woocommerce' ),
					'price'                       => __( 'Sort by price: low to high', 'lottery-for-woocommerce' ),
					'price-desc'                  => __( 'Sort by price: high to low', 'lottery-for-woocommerce' ),
					'on_going'                    => __( 'Sort by on-going giveaways', 'lottery-for-woocommerce' ),
					'ticket_count'                => __( 'Sort by most ticket sale on-going giveaways', 'lottery-for-woocommerce' ),
					'remaining_ticket_count'      => __( 'Sort by remaining tickets: low to high', 'lottery-for-woocommerce' ),
					'remaining_ticket_count-desc' => __( 'Sort by remaining tickets: high to low', 'lottery-for-woocommerce' ),
					'recently_started'            => __( 'Sort by recently started giveaways', 'lottery-for-woocommerce' ),
					'ending_soon'                 => __( 'Sort by ending soon giveaways', 'lottery-for-woocommerce' ),
					'closed'                      => __( 'Sort by closed giveaways', 'lottery-for-woocommerce' ),
					'failed'                      => __( 'Sort by failed giveaways', 'lottery-for-woocommerce' ),
					'finished'                    => __( 'Sort by finished giveaways', 'lottery-for-woocommerce' ),
				)
			),
			'id'      => $this->get_option_key( 'default_entry_list_orderby' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Enable Giveaway Badge', 'lottery-for-woocommerce' ),
			'type'    => 'checkbox',
			'default' => 'no',
			'id'      => $this->get_option_key( 'enable_lottery_badge' ),
			'desc'    => __( 'When enabled, a badge will be displayed on all giveaway product images.', 'lottery-for-woocommerce' ),
		);
		$section_fields[] = array(
			'title'     => __( 'Badge Image', 'lottery-for-woocommerce' ),
			'type'      => 'lty_custom_fields',
			'lty_field' => 'file_upload',
			'id'        => $this->get_option_key( 'upload_badge_image_url' ),
			'default'   => lty_get_badge_image( true ),
		);
		$section_fields[] = array(
			'title'   => __( 'Guest User Participation Type', 'lottery-for-woocommerce' ),
			'type'    => 'select',
			'default' => '1',
			'options' => lty_get_guest_user_participation_options(),
			'id'      => $this->get_option_key( 'guest_user_participate_type' ),
			'desc'    => __( '<b>Force Login on Checkout Page:</b> Guest Users can participate in giveaway but, they will be forced to create an account on checkout page. <br/><b>Prevent Guest Participation:</b> Prevent Guest Participation: Guest users will not be allowed to participate in the giveaway.<br/><b>Allow Guest Participation:</b> Allow Guest Participation: Allow guest user(s) to participate in the giveaway.', 'lottery-for-woocommerce' ),
		);
		$section_fields[] = array(
			'title'    => __( 'Order Status', 'lottery-for-woocommerce' ),
			'type'     => 'multiselect',
			'class'    => 'lty_select2',
			'default'  => array( 'processing', 'completed' ),
			'options'  => $wc_order_statuses,
			'id'       => $this->get_option_key( 'lottery_complete_order_statuses' ),
			'desc_tip' => true,
			'desc'     => __( 'Giveaway tickets will be assigned to the user based on the selected order status.', 'lottery-for-woocommerce' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Display Ticket Number(s) in Thank you Page & Order Details Page', 'lottery-for-woocommerce' ),
			'type'    => 'select',
			'default' => '1',
			'options' => array(
				'1' => __( 'Immediately After Placing Order', 'lottery-for-woocommerce' ),
				'2' => __( 'After Receiving Payment', 'lottery-for-woocommerce' ),
			),
			'id'      => $this->get_option_key( 'show_order_ticket_number' ),
			'desc'    => __( 'If you select "Immediately After Placing Order" option, the ticket number will be displayed immediately on Thank you & Order Details pages once the order is Placed. If you enable instant win prize for the giveaway product, then ticket number will display only when the payment is completed even if you selected "Immediately After Placing Order" option.<br/>If you select "After Receiving Payment" option, the ticket number(s) will be displayed only when the order status reaches the selected order status in Giveaway Order Status settings(Giveaway -> Settings -> General -> Order Status).', 'lottery-for-woocommerce' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Close Giveaway when All the Tickets have been Sold', 'lottery-for-woocommerce' ),
			'type'    => 'checkbox',
			'default' => 'no',
			'id'      => $this->get_option_key( 'close_lottery_reach_max' ),
			'desc'    => __( 'When enabled, the giveaway will be closed once the maximum number of tickets have been sold.', 'lottery-for-woocommerce' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Display Giveaway Dashboard as a Menu on My Account Page', 'lottery-for-woocommerce' ),
			'type'    => 'checkbox',
			'default' => 'no',
			'id'      => $this->get_option_key( 'enable_myaccount_lottery_menu' ),
			'desc'    => __( 'When Enabled, the Giveaway dashboard menu will be displayed on My Account Page.', 'lottery-for-woocommerce' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Display Giveaway Dashboard menu position on the My Account Page After', 'lottery-for-woocommerce' ),
			'type'    => 'select',
			'default' => 'dashboard',
			'id'      => $this->get_option_key( 'myaccount_lottery_menu_position' ),
			'options' => wc_get_account_menu_items(),
		);
		$section_fields[] = array(
			'type' => 'sectionend',
			'id'   => 'lty_general_options',
		);
		// General section end.
		// Reserve ticket number section start.
		$section_fields[] = array(
			'type'  => 'title',
			'title' => __( 'Reserve Ticket Number Settings', 'lottery-for-woocommerce' ),
			'id'    => 'lty_reserve_ticket_number_options',
		);
		$section_fields[] = array(
			'title'   => __( 'Reserve Ticket Number Till Purchase Completion', 'lottery-for-woocommerce' ),
			'type'    => 'checkbox',
			'default' => 'no',
			'id'      => $this->get_option_key( 'enable_reserve_ticket_manual_selection_type' ),
			'desc'    => __( 'When enabled, the ticket added to cart will be reserved for the user for a fixed time limit within which the user can complete the purchase. Note: This works only when the user is allowed to choose the ticket number.', 'lottery-for-woocommerce' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Ticket Reserve Duration in Minutes', 'lottery-for-woocommerce' ),
			'type'    => 'number',
			'default' => '5',
			'id'      => $this->get_option_key( 'reserve_ticket_time_in_min' ),
		);
		$section_fields[] = array(
			'type' => 'sectionend',
			'id'   => 'lty_reserve_ticket_number_options',
		);
		// Reserve ticket number section end.

		// Payment gateways section start.
		$section_fields[] = array(
			'type'  => 'title',
			'title' => __( 'Payment Gateway Settings', 'lottery-for-woocommerce' ),
			'id'    => 'lty_payment_gateways_options',
		);
		$section_fields[] = array(
			'title'    => __( 'Hide Selected Payment Gateway(s) for Giveaway Ticket Purchase ', 'lottery-for-woocommerce' ),
			'type'     => 'multiselect',
			'default'  => array(),
			'class'    => 'lty_select2',
			'options'  => lty_get_wc_available_gateways(),
			'id'       => $this->get_option_key( 'hide_payments' ),
			'desc'     => __( 'By default, all payment gateways will be displayed when a user tries to make payment for giveaway ticket purchase. If you wish to hide certain payment gateways during this process, you can do so by using this option.', 'lottery-for-woocommerce' ),
			'desc_tip' => true,
		);
		$section_fields[] = array(
			'type' => 'sectionend',
			'id'   => 'lty_payment_gateways_options',
		);
		// Payment gateways section end.

		return $section_fields;
	}

	/**
	 * Get the settings for other pages.
	 *
	 * @since 7.0.0
	 * @return array
	 * */
	public function other_pages_section_array() {
		$section_fields = array();
		// Display settings start.
		$section_fields[] = array(
			'type'  => 'title',
			'title' => __( 'General Display settings', 'lottery-for-woocommerce' ),
			'id'    => 'lty_display_settings',
		);
		$section_fields[] = array(
			'title'   => __( 'Hide Giveaway Products on Shop Page', 'lottery-for-woocommerce' ),
			'type'    => 'checkbox',
			'default' => 'no',
			'id'      => $this->get_option_key( 'restrict_lottery_in_shop_page' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Hide Giveaway Products on Category Page', 'lottery-for-woocommerce' ),
			'type'    => 'checkbox',
			'default' => 'no',
			'id'      => $this->get_option_key( 'restrict_lottery_in_category_page' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Hide Giveaway Products on Tag Page', 'lottery-for-woocommerce' ),
			'type'    => 'checkbox',
			'default' => 'no',
			'id'      => $this->get_option_key( 'restrict_lottery_in_tag_page' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Hide Finished Giveaway Products on Shop & Category Pages', 'lottery-for-woocommerce' ),
			'type'    => 'checkbox',
			'default' => 'no',
			'id'      => $this->get_option_key( 'hide_lottery_finished_status_products' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Hide Failed Giveaway Products on Shop & Category Pages', 'lottery-for-woocommerce' ),
			'type'    => 'checkbox',
			'default' => 'no',
			'id'      => $this->get_option_key( 'hide_lottery_failed_status_products' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Hide Closed Giveaway Products on Shop & Category Pages', 'lottery-for-woocommerce' ),
			'type'    => 'checkbox',
			'default' => 'no',
			'id'      => $this->get_option_key( 'hide_lottery_closed_status_products' ),
		);
		$section_fields[] = array(
			'type' => 'sectionend',
			'id'   => 'lty_display_settings',
		);
		// Display settings end.

		// Display lottery product settings start.
		$section_fields[] = array(
			'type'  => 'title',
			'title' => __( 'Giveaway Products Display Settings', 'lottery-for-woocommerce' ),
			'id'    => 'lty_display_lottery_products_settings',
		);
		$section_fields[] = array(
			'title'   => __( 'Display Progress Bar on the Shop and Category Page(s)', 'lottery-for-woocommerce' ),
			'type'    => 'checkbox',
			'default' => 'no',
			'id'      => $this->get_option_key( 'restrict_progress_bar_shop_page' ),
			'desc'    => __( 'When enabled, a Progress Bar for presenting the ticket sales count will be displayed on the Shop & Category Page(s).', 'lottery-for-woocommerce' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Display Ticket Sold Percentage on Progress Bar', 'lottery-for-woocommerce' ),
			'type'    => 'checkbox',
			'class'   => 'lty-progress-bar-percentage-fields',
			'default' => 'no',
			'id'      => $this->get_option_key( 'display_progress_bar_percentage_shop_page' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Progress Bar Percentage Type', 'lottery-for-woocommerce' ),
			'type'    => 'select',
			'default' => '1',
			'class'   => 'lty-progress-bar-percentage-fields',
			'options' => array(
				'1' => __( 'Decimal', 'lottery-for-woocommerce' ),
				'2' => __( 'Rounded', 'lottery-for-woocommerce' ),
			),
			'id'      => $this->get_option_key( 'progress_bar_percentage_type_shop_page' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Progress Bar Percentage Display Style', 'lottery-for-woocommerce' ),
			'type'    => 'select',
			'default' => '1',
			'class'   => 'lty-progress-bar-percentage-fields',
			'options' => array(
				'1' => __( 'Outside of the Progress bar', 'lottery-for-woocommerce' ),
				'2' => __( 'Inside the Progress bar', 'lottery-for-woocommerce' ),
			),
			'id'      => $this->get_option_key( 'progress_bar_percentage_display_type_shop_page' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Hide Remaining Tickets Info Message on the Progress Bar', 'lottery-for-woocommerce' ),
			'type'    => 'checkbox',
			'default' => 'no',
			'id'      => $this->get_option_key( 'hide_progress_bar_ticket_remaining_message' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Display Remaining Tickets Message', 'lottery-for-woocommerce' ),
			'type'    => 'checkbox',
			'default' => 'no',
			'id'      => $this->get_option_key( 'display_remaining_tickets_message_on_shop' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Hide Giveaway Start Date on Shop Page', 'lottery-for-woocommerce' ),
			'type'    => 'checkbox',
			'default' => 'no',
			'id'      => $this->get_option_key( 'hide_lottery_starts_on_message_in_shop_page' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Hide Giveaway End Date on Shop Page', 'lottery-for-woocommerce' ),
			'type'    => 'checkbox',
			'default' => 'no',
			'id'      => $this->get_option_key( 'hide_lottery_ends_on_message_in_shop_page' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Hide Countdown Timer for Giveaway Products on Shop and Category Pages', 'lottery-for-woocommerce' ),
			'type'    => 'checkbox',
			'default' => 'no',
			'id'      => $this->get_option_key( 'restrict_countdown_timer_in_shop' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Hide Giveaway Status on Shop and Category Pages', 'lottery-for-woocommerce' ),
			'type'    => 'checkbox',
			'default' => 'no',
			'id'      => $this->get_option_key( 'hide_lottery_status_in_shop' ),
		);
		$section_fields[] = array(
			'type' => 'sectionend',
			'id'   => 'lty_display_lottery_products_settings',
		);
		// Display lottery product settings end.

		// Display entry list settings start.
		$section_fields[] = array(
			'type'  => 'title',
			'title' => __( 'Entry List Display Settings', 'lottery-for-woocommerce' ),
			'id'    => 'lty_display_entry_list_settings',
		);
		$section_fields[] = array(
			'title'   => __( 'Hide Giveaway Product Details', 'lottery-for-woocommerce' ),
			'type'    => 'checkbox',
			'default' => 'no',
			'id'      => $this->get_option_key( 'hide_entry_list_lottery_details' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Hide Ticket Purchased Date', 'lottery-for-woocommerce' ),
			'type'    => 'checkbox',
			'default' => 'no',
			'id'      => $this->get_option_key( 'hide_entry_list_ticket_purchased_date' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Hide Chosen Answer', 'lottery-for-woocommerce' ),
			'type'    => 'checkbox',
			'default' => 'no',
			'id'      => $this->get_option_key( 'hide_entry_list_chosen_answer' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Hide Search Tickets Option', 'lottery-for-woocommerce' ),
			'type'    => 'checkbox',
			'default' => 'no',
			'id'      => $this->get_option_key( 'hide_entry_list_tickets_search' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Hide Winners Details for Finished Giveaways', 'lottery-for-woocommerce' ),
			'type'    => 'checkbox',
			'default' => 'no',
			'id'      => $this->get_option_key( 'hide_entry_list_winners_details' ),
		);
		$section_fields[] = array(
			'type' => 'sectionend',
			'id'   => 'lty_display_entry_list_settings',
		);
		// Display entry list settings end.

		// Lottery dashboard display settings start.
		$section_fields[] = array(
			'type'  => 'title',
			'title' => __( 'Giveaway Dashboard Display Settings', 'lottery-for-woocommerce' ),
			'id'    => 'lty_lottery_dashboard_display_settings',
		);
		$section_fields[] = array(
			'title'   => __( 'Display Instant Win on Giveaway Dashboard', 'lottery-for-woocommerce' ),
			'type'    => 'checkbox',
			'default' => 'yes',
			'id'      => $this->get_option_key( 'display_lottery_dashboard_instant_win' ),
		);
		$section_fields[] = array(
			'type' => 'sectionend',
			'id'   => 'lty_lottery_dashboard_display_settings',
		);
		// Lottery dashboard display settings end.

		// Thank you page settings start.
		$section_fields[] = array(
			'type'  => 'title',
			'title' => __( 'Thank You Page & Order Details Page Display Settings', 'lottery-for-woocommerce' ),
			'id'    => 'lty_thankyou_page_display_settings',
		);
		$section_fields[] = array(
			'title'   => __( 'Display Instant Win Prize Details On Thank You Page & Order Details Page', 'lottery-for-woocommerce' ),
			'type'    => 'checkbox',
			'default' => 'no',
			'id'      => $this->get_option_key( 'display_instant_winners_on_order' ),
			'desc'    => __( 'When enabled, instant win prize details will display on thank you page(Instant win prize details will display only when the user won the instant win prize).', 'lottery-for-woocommerce' ),
		);
		$section_fields[] = array(
			'type' => 'sectionend',
			'id'   => 'lty_thankyou_page_display_settings',
		);
		// Thank you page settings end.

		return $section_fields;
	}

	/**
	 * Get the settings for single product page section.
	 *
	 * @since 7.0.0
	 * @return array
	 * */
	public function single_product_page_section_array() {
		$section_fields = array();
		// General section start.
		$section_fields[] = array(
			'type'  => 'title',
			'title' => __( 'General Settings', 'lottery-for-woocommerce' ),
			'id'    => 'lty_general_options',
		);
		$section_fields[] = array(
			'title'    => __( 'Quantity Selector Type', 'lottery-for-woocommerce' ),
			'type'     => 'select',
			'default'  => '1',
			'options'  => array(
				'1' => __( 'Default', 'lottery-for-woocommerce' ),
				'2' => __( 'Range Selector', 'lottery-for-woocommerce' ),
			),
			'id'       => $this->get_option_key( 'quantity_selector_type' ),
			'desc_tip' => true,
			'desc'     => __( '<b>Default:</b>Will display the default WooCommerce quantity selector.</br><b>Range Selector:</b>Ticket Selector Method for Automatic Ticket Generation Type.', 'lottery-for-woocommerce' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Disable Participate Now Button when the Question Answer and Ticket Number is not Selected', 'lottery-for-woocommerce' ),
			'type'    => 'checkbox',
			'default' => 'no',
			'id'      => $this->get_option_key( 'disable_participate_now_button' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Enable Participate Now Button Redirection to Checkout Page', 'lottery-for-woocommerce' ),
			'type'    => 'checkbox',
			'default' => 'no',
			'id'      => $this->get_option_key( 'participate_now_checkout_redirection_enabled' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Enable Ticket Search Bar(User Chooses Tickets)', 'lottery-for-woocommerce' ),
			'type'    => 'checkbox',
			'default' => 'no',
			'id'      => $this->get_option_key( 'enable_manual_ticket_selection_search_bar' ),
			'desc'    => __( 'Enable to display search option for ticket numbers in the user chooses ticket type.', 'lottery-for-woocommerce' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Hide Instant Win Prizes Tab', 'lottery-instant-winners-prizes' ),
			'type'    => 'checkbox',
			'default' => 'no',
			'id'      => $this->get_option_key( 'instant_winners_tab_enabled' ),
		);
		$section_fields[] = array(
			'type' => 'sectionend',
			'id'   => 'lty_general_options',
		);
		// General section end.
		// Display section start.
		$section_fields[] = array(
			'type'  => 'title',
			'title' => __( 'Display Settings', 'lottery-for-woocommerce' ),
			'id'    => 'lty_display_options',
		);
		$section_fields[] = array(
			'title'   => __( 'Hide Giveaway Status on Single Product Page', 'lottery-for-woocommerce' ),
			'type'    => 'checkbox',
			'default' => 'no',
			'id'      => $this->get_option_key( 'hide_lottery_status_in_single_product_page' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Hide Giveaway Start Date on Single Product Page', 'lottery-for-woocommerce' ),
			'type'    => 'checkbox',
			'default' => 'no',
			'id'      => $this->get_option_key( 'hide_lottery_starts_on_message_in_single_product_page' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Hide Giveaway End Date on Single Product Page', 'lottery-for-woocommerce' ),
			'type'    => 'checkbox',
			'default' => 'no',
			'id'      => $this->get_option_key( 'hide_lottery_ends_on_message_in_single_product_page' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Hide Countdown Timer for Single Product Page', 'lottery-for-woocommerce' ),
			'type'    => 'checkbox',
			'default' => 'no',
			'id'      => $this->get_option_key( 'restrict_countdown_timer_in_single_product_page' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Hide Timezone Info on the Single Product Page', 'lottery-for-woocommerce' ),
			'type'    => 'checkbox',
			'default' => 'no',
			'id'      => $this->get_option_key( 'hide_tz_display_in_single_product_page' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Hide Minimum Ticket Info on the Single Product Page', 'lottery-for-woocommerce' ),
			'type'    => 'checkbox',
			'default' => 'no',
			'id'      => $this->get_option_key( 'hide_minimum_ticket_message_in_single_product_page' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Hide Maximum Ticket Info on the Single Product Page', 'lottery-for-woocommerce' ),
			'type'    => 'checkbox',
			'default' => 'no',
			'id'      => $this->get_option_key( 'hide_maximum_ticket_message_in_single_product_page' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Hide Minimum Tickets per User Info on the Single Product Page', 'lottery-for-woocommerce' ),
			'type'    => 'checkbox',
			'default' => 'no',
			'id'      => $this->get_option_key( 'hide_minimum_tickets_per_user_info_in_single_product_page' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Hide Maximum Tickets per User Info on the Single Product Page', 'lottery-for-woocommerce' ),
			'type'    => 'checkbox',
			'default' => 'no',
			'id'      => $this->get_option_key( 'hide_maximum_tickets_per_user_single_product' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Display Progress Bar', 'lottery-for-woocommerce' ),
			'type'    => 'checkbox',
			'default' => 'no',
			'id'      => $this->get_option_key( 'restrict_progress_bar_single_product_page' ),
			'desc'    => __( 'When enabled, a Progress Bar for presenting the ticket sales count will be displayed on the Single Product Page.', 'lottery-for-woocommerce' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Display Ticket Sold Percentage in Progress Bar', 'lottery-for-woocommerce' ),
			'type'    => 'checkbox',
			'class'   => 'lty-progress-bar-percentage-fields',
			'default' => 'no',
			'id'      => $this->get_option_key( 'display_progress_bar_percentage_product_page' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Progress Bar Percentage Type', 'lottery-for-woocommerce' ),
			'type'    => 'select',
			'default' => '1',
			'class'   => 'lty-progress-bar-percentage-fields',
			'options' => array(
				'1' => __( 'Decimal', 'lottery-for-woocommerce' ),
				'2' => __( 'Rounded', 'lottery-for-woocommerce' ),
			),
			'id'      => $this->get_option_key( 'progress_bar_percentage_type_product_page' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Progress Bar Percentage Display Style', 'lottery-for-woocommerce' ),
			'type'    => 'select',
			'default' => '1',
			'class'   => 'lty-progress-bar-percentage-fields',
			'options' => array(
				'1' => __( 'Outside of the Progress bar', 'lottery-for-woocommerce' ),
				'2' => __( 'Inside the Progress bar', 'lottery-for-woocommerce' ),
			),
			'id'      => $this->get_option_key( 'progress_bar_percentage_display_type_product_page' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Hide Winner Message on Single Product Page', 'lottery-for-woocommerce' ),
			'type'    => 'checkbox',
			'default' => 'no',
			'id'      => $this->get_option_key( 'hide_lottery_winner_message_in_single_product_page' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Hide Giveaway Tickets Sold on Single Product Page', 'lottery-for-woocommerce' ),
			'type'    => 'checkbox',
			'default' => 'no',
			'id'      => $this->get_option_key( 'hide_lottery_tickets_sold_in_single_product_page' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Mask the Username in Tickets Log Tab on Single Product Page', 'lottery-for-woocommerce' ),
			'type'    => 'checkbox',
			'default' => 'no',
			'id'      => $this->get_option_key( 'hide_user_name_in_ticket_logs' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Hide Predefined button(s) when the Stock Quantity is Lesser than Predefined Button Quantity Value', 'lottery-for-woocommerce' ),
			'type'    => 'checkbox',
			'default' => 'no',
			'id'      => $this->get_option_key( 'hide_less_quantity_predefined_button' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Display Giveaway Details for Closed Giveaways', 'lottery-for-woocommerce' ),
			'type'    => 'checkbox',
			'default' => 'no',
			'id'      => $this->get_option_key( 'display_closed_lottery_details_product_page' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Display Giveway Details for Finished Giveaways', 'lottery-for-woocommerce' ),
			'type'    => 'checkbox',
			'default' => 'no',
			'id'      => $this->get_option_key( 'display_finished_lottery_details_product_page' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Display Giveaway Details for Failed Giveaways', 'lottery-for-woocommerce' ),
			'type'    => 'checkbox',
			'default' => 'no',
			'id'      => $this->get_option_key( 'display_failed_lottery_details_product_page' ),
		);
		$section_fields[] = array(
			'type' => 'sectionend',
			'id'   => 'lty_display_options',
		);
		// Display section end.
		// Instant winner prizes settings start.
		$section_fields[] = array(
			'type'  => 'title',
			'title' => __( 'Instant Win Prizes Settings', 'lottery-for-woocommerce' ),
			'id'    => 'lty_instant_winner_prizes_settings',
		);
		$section_fields[] = array(
			'title'             => __( 'Number of Instant Win Prizes Per Page', 'lottery-for-woocommerce' ),
			'type'              => 'number',
			'default'           => '10',
			'custom_attributes' => array(
				'min'  => 1,
				'step' => 1,
			),
			'id'                => $this->get_option_key( 'instant_winner_prizes_per_page' ),
		);
		$section_fields[] = array(
			'title'             => __( 'Number of Tickets Per Instant Win Prize Group (with Pagination)', 'lottery-for-woocommerce' ),
			'type'              => 'number',
			'default'           => '',
			'custom_attributes' => array(
				'min'  => 1,
				'step' => 1,
			),
			'id'                => $this->get_option_key( 'instant_win_group_tickets_per_page' ),
		);
		$section_fields[] = array(
			'type' => 'sectionend',
			'id'   => 'lty_instant_winner_prizes_settings',
		);
		// Instant winner prizes settings end.
		// Alert settings start.
		$section_fields[] = array(
			'type'  => 'title',
			'title' => __( 'Alert Settings', 'lottery-for-woocommerce' ),
			'id'    => 'lty_alert_settings',
		);
		$section_fields[] = array(
			'title'   => __( 'Hide Confirmation Alert from Users for Selecting the Answer', 'lottery-for-woocommerce' ),
			'type'    => 'checkbox',
			'default' => 'no',
			'id'      => $this->get_option_key( 'hide_lottery_answer_verification_alert' ),
		);
		$section_fields[] = array(
			'type' => 'sectionend',
			'id'   => 'lty_alert_settings',
		);
		// Alert settings end.

		return $section_fields;
	}

	/**
	 * Get the settings for question answer section.
	 *
	 * @since 7.0.0
	 * @return array
	 * */
	public function question_answer_section_array() {
		$section_fields = array();

		// Q/A settings start.
		$section_fields[] = array(
			'type'  => 'title',
			'title' => __( 'Q & A Settings', 'lottery-for-woocommerce' ),
			'id'    => 'lty_question_answer_settings',
		);
		$section_fields[] = array(
			'title'   => __( 'Ask a Question to Users before Allowing them to Participate in Giveaways', 'lottery-for-woocommerce' ),
			'type'    => 'checkbox',
			'default' => 'no',
			'desc'    => __( 'When enabled, question(s) will be displayed to users on the giveaway participation page.', 'lottery-for-woocommerce' ),
			'id'      => $this->get_option_key( 'manage_question_global_setting' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Force Users to Answer the Question', 'lottery-for-woocommerce' ),
			'type'    => 'checkbox',
			'default' => 'no',
			'desc'    => __( 'When enabled, the users will not be allowed to participate in the giveaway unless they select an answer.', 'lottery-for-woocommerce' ),
			'id'      => $this->get_option_key( 'force_answer_global_setting' ),
			'class'   => 'lty_hide_question_answer_setting',
		);
		$section_fields[] = array(
			'title'    => __( 'Q/A Time Limit Type', 'lottery-for-woocommerce' ),
			'type'     => 'select',
			'default'  => '1',
			'options'  => array(
				'1' => __( 'Unlimited', 'lottery-for-woocommerce' ),
				'2' => __( 'Limited', 'lottery-for-woocommerce' ),
			),
			'class'    => 'lty_hide_question_answer_setting lty-force-question-answer-field',
			'id'       => $this->get_option_key( 'question_answer_time_limit_type' ),
			'desc_tip' => true,
			'desc'     => __( '"Unlimited" option, user can select the answer to the question without having any time limit. "Limited" option, you can set a time limit for the user to answer the question(if the time limit is  exceeded then the user cannot participate in the giveaway).', 'lottery-for-woocommerce' ),
		);
		$section_fields[] = array(
			'title'       => __( 'Set Time Limit', 'lottery-for-woocommerce' ),
			'type'        => 'lty_custom_fields',
			'lty_field'   => 'relative_date_selector',
			'option_type' => '3',
			'default'     => array(
				'unit'   => 'minutes',
				'number' => '5',
			),
			'class'       => 'lty_hide_question_answer_setting lty-force-question-answer-field',
			'id'          => $this->get_option_key( 'question_answer_time_limit' ),
		);
		$section_fields[] = array(
			'title'   => __( "Don't Generate Ticket Numbers for Incorrectly Answered Questions", 'lottery-for-woocommerce' ),
			'type'    => 'checkbox',
			'default' => 'no',
			'desc'    => __( 'When enabled, the user will be allowed to complete the giveaway ticket purchase but, ticket will not be generated for the purchase.', 'lottery-for-woocommerce' ),
			'id'      => $this->get_option_key( 'restrict_incorrectly_selected_answer_global_setting' ),
			'class'   => 'lty_hide_question_answer_setting lty-force-question-answer-field',
		);
		$section_fields[] = array(
			'title'   => __( 'Verify Answer Before Purchasing Giveaway Tickets', 'lottery-for-woocommerce' ),
			'type'    => 'checkbox',
			'default' => 'no',
			'desc'    => __( 'When enabled, only the users who answer the questions correctly will be allowed to participate in the giveaway.', 'lottery-for-woocommerce' ),
			'id'      => $this->get_option_key( 'validate_correct_answer_global_setting' ),
			'class'   => 'lty_hide_question_answer_setting lty-force-question-answer-field',
		);
		$section_fields[] = array(
			'title'   => __( 'Verify Answer Type', 'lottery-for-woocommerce' ),
			'type'    => 'select',
			'default' => '1',
			'options' => array(
				'1' => __( 'Limited Attempts', 'lottery-for-woocommerce' ),
				'2' => __( 'Unlimited Attempts', 'lottery-for-woocommerce' ),
			),
			'class'   => 'lty_hide_verify_answer_setting_global',
			'id'      => $this->get_option_key( 'verify_answer_type_global' ),
		);
		$section_fields[] = array(
			'title'             => __( 'Number of Attempts', 'lottery-for-woocommerce' ),
			'type'              => 'number',
			'default'           => '1',
			'custom_attributes' => array( 'min' => 1 ),
			'class'             => 'lty_hide_verify_answer_setting_global',
			'id'                => $this->get_option_key( 'question_answer_attempts_global' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Your Question', 'lottery-for-woocommerce' ),
			'type'    => 'textarea',
			'default' => get_option( $this->get_option_key( 'lty_questions_global_settings' ), '' ),
			'id'      => $this->get_option_key( 'lty_questions_global_settings' ),
			'class'   => 'lty_hide_question_answer_setting',
		);
		$section_fields[] = array(
			'title'   => __( 'Options Display type', 'lottery-for-woocommerce' ),
			'type'    => 'select',
			'default' => '1',
			'options' => array(
				'1' => __( 'Display all the Options to Choose', 'lottery-for-woocommerce' ),
				'2' => __( 'Use Dropdown for Options to Choose', 'lottery-for-woocommerce' ),
			),
			'id'      => $this->get_option_key( 'question_answer_display_type' ),
			'class'   => 'lty_hide_question_answer_setting lty-global-question-answer-display-type',
		);
		$section_fields[] = array(
			'title'   => __( 'Display the first option as default in the Dropdown', 'lottery-for-woocommerce' ),
			'type'    => 'checkbox',
			'default' => 'no',
			'desc'    => __( 'When enabled, it will remove the "Choose Answer" label and display the first option as default in the dropdown', 'lottery-for-woocommerce' ),
			'id'      => $this->get_option_key( 'question_answer_first_option_as_default_option' ),
			'class'   => 'lty_hide_question_answer_setting lty-global-question-answer-first-option-as-default-option',
		);
		$section_fields[] = array(
			'id'   => 'lty_question_answers_global',
			'type' => 'lty_question_answers_global',
		);
		$section_fields[] = array(
			'type' => 'sectionend',
			'id'   => 'lty_question_answer_settings',
		);
		// Q/A settings end.

		return $section_fields;
	}

	/**
	 * Get the settings for ticket generation section.
	 *
	 * @since 7.0.0
	 * @return array
	 * */
	public function ticket_generation_section_array() {
		$section_fields = array();

		// Ticket generation settings start.
		$section_fields[] = array(
			'type'  => 'title',
			'title' => __( 'Automatic Random Ticket Generation Settings', 'lottery-for-woocommerce' ),
			'id'    => 'lty_ticket_generation_settings',
		);
		$section_fields[] = array(
			'title'   => __( 'Ticket Number Type', 'lottery-for-woocommerce' ),
			'type'    => 'select',
			'default' => '1',
			'options' => array(
				'1' => __( 'Only Numbers', 'lottery-for-woocommerce' ),
				'2' => __( 'Alphanumeric', 'lottery-for-woocommerce' ),
			),
			'id'      => $this->get_option_key( 'generate_ticket_type' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Ticket Length', 'lottery-for-woocommerce' ),
			'type'    => 'number',
			'default' => '8',
			'id'      => $this->get_option_key( 'ticket_length' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Ticket Prefix', 'lottery-for-woocommerce' ),
			'type'    => 'text',
			'default' => '',
			'id'      => $this->get_option_key( 'ticket_prefix' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Ticket Suffix', 'lottery-for-woocommerce' ),
			'type'    => 'text',
			'default' => '',
			'id'      => $this->get_option_key( 'ticket_suffix' ),
		);
		$section_fields[] = array(
			'type' => 'sectionend',
			'id'   => 'lty_ticket_generation_settings',
		);
		// Ticket generation settings end.

		return $section_fields;
	}

	/**
	 * Get the settings for lottery details section.
	 *
	 * @since 7.0.0
	 * @return array
	 * */
	public function lottery_details_section_array() {
		$section_fields = array();
		// Lottery details section start.
		$section_fields[] = array(
			'type'  => 'title',
			'title' => __( 'Giveaway Details in Single Product Page', 'lottery-for-woocommerce' ),
			'id'    => 'lty_lottery_details_options',
		);
		$section_fields[] = array(
			'title'   => __( 'Show/Hide Giveaway Tickets Details(Purchased Ticket Entries)', 'lottery-for-woocommerce' ),
			'type'    => 'select',
			'default' => '1',
			'options' => array(
				'1' => __( 'Show', 'lottery-for-woocommerce' ),
				'2' => __( 'Hide', 'lottery-for-woocommerce' ),
			),
			'id'      => $this->get_option_key( 'single_product_tab_details_toggle' ),
		);
		$section_fields[] = array(
			'title'             => __( 'Number of Entries Per Page in Giveaway Tickets Details Tab', 'lottery-for-woocommerce' ),
			'type'              => 'number',
			'default'           => '10',
			'custom_attributes' => array(
				'min'      => 1,
				'step'     => 1,
				'required' => true,
			),
			'id'                => $this->get_option_key( 'single_product_tab_lottery_details_per_page' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Order by Type in Giveaway Tickets Details Tab', 'lottery-for-woocommerce' ),
			'type'    => 'select',
			'default' => '1',
			'options' => array(
				'1' => __( 'Post ID', 'lottery-for-woocommerce' ),
				'2' => __( 'Ticket Number', 'lottery-for-woocommerce' ),
			),
			'id'      => $this->get_option_key( 'single_product_tab_details_order_by' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Order Type in Giveaway Tickets Details Tab', 'lottery-for-woocommerce' ),
			'type'    => 'select',
			'default' => '1',
			'options' => array(
				'1' => __( 'Descending', 'lottery-for-woocommerce' ),
				'2' => __( 'Ascending', 'lottery-for-woocommerce' ),
			),
			'id'      => $this->get_option_key( 'single_product_tab_details_order' ),
		);
		$section_fields[] = array(
			'title'   => __( 'User Details Display Type in Giveaway Tickets Details Tab', 'lottery-for-woocommerce' ),
			'type'    => 'select',
			'default' => '1',
			'options' => array(
				'1' => __( 'Username', 'lottery-for-woocommerce' ),
				'2' => __( 'First & Last Name', 'lottery-for-woocommerce' ),
				'3' => __( 'First Name Only', 'lottery-for-woocommerce' ),
			),
			'id'      => $this->get_option_key( 'single_product_tab_details_username_display_type' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Show/Hide Chosen Answer Column for Finished Giveaways in Giveaway Tickets Details Tab', 'lottery-for-woocommerce' ),
			'type'    => 'select',
			'default' => '1',
			'options' => array(
				'1' => __( 'Show', 'lottery-for-woocommerce' ),
				'2' => __( 'Hide', 'lottery-for-woocommerce' ),
			),
			'id'      => $this->get_option_key( 'single_product_tab_show_chosen_answer_column' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Show/Hide Search in Giveaway Tickets Details Tab(Purchased Ticket Entries)', 'lottery-for-woocommerce' ),
			'type'    => 'select',
			'default' => '2',
			'options' => array(
				'1' => __( 'Show', 'lottery-for-woocommerce' ),
				'2' => __( 'Hide', 'lottery-for-woocommerce' ),
			),
			'id'      => $this->get_option_key( 'display_ticket_logs_search' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Giveaway Tickets Search by', 'lottery-for-woocommerce' ),
			'type'    => 'multiselect',
			'class'   => 'lty_select2 lty-ticket-logs-search',
			'default' => array( 'lty_ticket_number' ),
			'options' => lty_get_lottery_ticket_logs_search_options(),
			'id'      => $this->get_option_key( 'ticket_logs_search_columns' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Search Ticket Number(s) Type', 'lottery-for-woocommerce' ),
			'type'    => 'select',
			'default' => '1',
			'options' => array(
				'1' => __( 'Display related results', 'lottery-for-woocommerce' ),
				'2' => __( 'Display only Exact results', 'lottery-for-woocommerce' ),
			),
			'id'      => $this->get_option_key( 'ticket_logs_search_type' ),
			'class'   => 'lty-ticket-logs-search',
		);
		$section_fields[] = array(
			'type' => 'sectionend',
			'id'   => 'lty_lottery_details_options',
		);
		// Lottery details section end.
		// Dashboard section start.
		$section_fields[] = array(
			'type'  => 'title',
			'title' => __( 'Dashboard Settings', 'lottery-for-woocommerce' ),
			'id'    => 'lty_dashboard_options',
		);
		$section_fields[] = array(
			'title'             => __( 'Number of Entries Per Page on Giveaway Dashboard', 'lottery-for-woocommerce' ),
			'type'              => 'number',
			'default'           => 10,
			'custom_attributes' => array(
				'min' => '1',
			),
			'id'                => $this->get_option_key( 'lottery_dashboard_per_page' ),
		);
		$section_fields[] = array(
			'title'             => __( 'Number of Entries Per Page on Popup Giveaway Dashboard', 'lottery-for-woocommerce' ),
			'type'              => 'number',
			'default'           => 10,
			'custom_attributes' => array(
				'min' => '1',
			),
			'id'                => $this->get_option_key( 'popup_lottery_dashboard_tickets_per_page' ),
		);
		$section_fields[] = array(
			'type' => 'sectionend',
			'id'   => 'lty_dashboard_options',
		);
		// Dashboard section end.

		return $section_fields;
	}

	/**
	 * Get the settings for winner section.
	 *
	 * @since 7.0.0
	 * @return array
	 * */
	public function winner_section_array() {
		$section_fields = array();
		// Winner details section start.
		$section_fields[] = array(
			'type'  => 'title',
			'title' => __( 'Winner Details Settings', 'lottery-for-woocommerce' ),
			'id'    => 'lty_winner_details_options',
		);
		$section_fields[] = array(
			'title'   => __( 'Show/Hide Winner Details', 'lottery-for-woocommerce' ),
			'type'    => 'select',
			'default' => '1',
			'options' => array(
				'1' => __( 'Show', 'lottery-for-woocommerce' ),
				'2' => __( 'Hide', 'lottery-for-woocommerce' ),
			),
			'id'      => $this->get_option_key( 'single_product_lottery_winner_toggle' ),
		);
		$section_fields[] = array(
			'title'             => __( 'Number of Entries Per Page in Winners List', 'lottery-for-woocommerce' ),
			'type'              => 'number',
			'default'           => 10,
			'custom_attributes' => array(
				'min' => '1',
			),
			'id'                => $this->get_option_key( 'winners_list_per_page' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Show/Hide Username', 'lottery-for-woocommerce' ),
			'type'    => 'select',
			'default' => '1',
			'options' => array(
				'1' => __( 'Show', 'lottery-for-woocommerce' ),
				'2' => __( 'Hide', 'lottery-for-woocommerce' ),
			),
			'id'      => $this->get_option_key( 'single_product_lottery_username_toggle' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Mask Winner(s) Username', 'lottery-for-woocommerce' ),
			'type'    => 'checkbox',
			'default' => 'no',
			'id'      => $this->get_option_key( 'single_product_lottery_mask_winner_username' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Show/Hide Ticket Number', 'lottery-for-woocommerce' ),
			'type'    => 'select',
			'default' => '1',
			'options' => array(
				'1' => __( 'Show', 'lottery-for-woocommerce' ),
				'2' => __( 'Hide', 'lottery-for-woocommerce' ),
			),
			'id'      => $this->get_option_key( 'single_product_lottery_ticket_number_toggle' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Show/Hide Gift Product', 'lottery-for-woocommerce' ),
			'type'    => 'select',
			'default' => '1',
			'options' => array(
				'1' => __( 'Show', 'lottery-for-woocommerce' ),
				'2' => __( 'Hide', 'lottery-for-woocommerce' ),
			),
			'id'      => $this->get_option_key( 'single_product_lottery_gift_product_toggle' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Show/Hide Chosen Answer', 'lottery-for-woocommerce' ),
			'type'    => 'select',
			'default' => '1',
			'options' => array(
				'1' => __( 'Show', 'lottery-for-woocommerce' ),
				'2' => __( 'Hide', 'lottery-for-woocommerce' ),
			),
			'id'      => $this->get_option_key( 'single_product_lottery_answer_toggle' ),
		);
		$section_fields[] = array(
			'type' => 'sectionend',
			'id'   => 'lty_winner_details_options',
		);
		// Winner details section end.

		return $section_fields;
	}

	/**
	 * Get the settings for pdf section.
	 *
	 * @since 9.5.0
	 * @return array
	 * */
	public function pdf_section_array() {
		$section_fields = array();

		// PDF general settings start.
		$section_fields[] = array(
			'type'  => 'title',
			'title' => __( 'General Settings', 'lottery-for-woocommerce' ),
			'id'    => 'lty_general_pdf_options',
		);
		$section_fields[] = array(
			'title'   => __( 'Enable PDF Download for Entry List', 'lottery-for-woocommerce' ),
			'type'    => 'checkbox',
			'default' => 'no',
			'id'      => $this->get_option_key( 'allow_entry_list_pdf_download' ),
			'desc'    => __( 'When enabled, you can allow the users to download the giveaway product entry list details as PDF.', 'lottery-for-woocommerce' ),
		);
		$section_fields[] = array(
			'title'     => __( 'Upload Site logo in Entry List PDF header', 'lottery-for-woocommerce' ),
			'type'      => 'lty_custom_fields',
			'lty_field' => 'image_upload',
			'id'        => $this->get_option_key( 'entry_list_pdf_logo' ),
			'default'   => '',
			'class'     => 'lty-entry-list-pdf-field',
		);
		$section_fields[] = array(
			'title'             => __( 'Width x Height(pixels) ', 'lottery-for-woocommerce' ),
			'type'              => 'lty_custom_fields',
			'lty_field'         => 'image_size',
			'id'                => $this->get_option_key( 'entry_list_pdf_logo_size' ),
			'class'             => 'lty-entry-list-pdf-field',
			'default'           => array(
				'height' => 50,
				'width'  => 50,
			),
			'custom_attributes' => array(
				'size' => '5',
				'min'  => 1,
			),
		);
		$section_fields[] = array(
			'title'   => __( 'Entry List PDF File Name', 'lottery-for-woocommerce' ),
			'type'    => 'text',
			'default' => 'Entry list for {product_name}',
			'id'      => $this->get_option_key( 'entry_list_pdf_file_name' ),
			'desc'    => __( '<b>Supported Shortcodes: <br/>{product_name}</b> - Product Name<br/><b>{date}</b> - Current Date(Ymd)', 'lottery-for-woocommerce' ),
			'class'   => 'lty-entry-list-pdf-field',
		);
		$section_fields[] = array(
			'title'   => __( 'Enable PDF Download for Ticket Numbers on Thank You Page & Order Details Page', 'lottery-for-woocommerce' ),
			'type'    => 'checkbox',
			'default' => 'no',
			'id'      => $this->get_option_key( 'download_lottery_ticket_pdf' ),
			'desc'    => __( 'When enabled, you can allow the users to download the giveaway ticket numbers as PDF on Thank you page & Order details page.', 'lottery-for-woocommerce' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Giveaway Ticket(s) PDF File Name', 'lottery-for-woocommerce' ),
			'type'    => 'text',
			'default' => 'Giveaway Ticket for {order_id}{tickets_count}',
			'id'      => $this->get_option_key( 'lottery_ticket_pdf_file_name' ),
			'desc'    => __( '<b>Supported Shortcodes: <br/>{order_id}</b> - Order ID<br/><b>{tickets_count}</b> - Tickets Count<br/><b>{date}</b> - Current Date(Ymd)', 'lottery-for-woocommerce' ),
		);
		$section_fields[] = array(
			'type' => 'sectionend',
			'id'   => 'lty_general_pdf_options',
		);
		// PDF general settings end.
		// Lottery ticket pdf color customization settings start.
		$section_fields[] = array(
			'type'  => 'title',
			'title' => __( 'Giveaway Ticket(s) PDF Color Customization Settings', 'lottery-for-woocommerce' ),
			'id'    => 'lty_ticket_pdf_color_customization_options',
		);
		$section_fields[] = array(
			'title'   => __( 'PDF Header Background Color', 'lottery-for-woocommerce' ),
			'type'    => 'color',
			'css'     => 'width: 6em;',
			'default' => '#f4f4f4',
			'id'      => $this->get_option_key( 'ticket_pdf_header_bg_color' ),
		);
		$section_fields[] = array(
			'title'   => __( 'PDF Header Font Color', 'lottery-for-woocommerce' ),
			'type'    => 'color',
			'css'     => 'width: 6em;',
			'default' => '#000000',
			'id'      => $this->get_option_key( 'ticket_pdf_header_font_color' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Background Color - 1', 'lottery-for-woocommerce' ),
			'type'    => 'color',
			'css'     => 'width: 6em;',
			'default' => '#ffffff',
			'id'      => $this->get_option_key( 'ticket_pdf_bg_color_left' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Background Color - 2', 'lottery-for-woocommerce' ),
			'type'    => 'color',
			'css'     => 'width: 6em;',
			'default' => '#f57436',
			'id'      => $this->get_option_key( 'ticket_pdf_bg_color_right' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Color Ratio in Degree', 'lottery-for-woocommerce' ),
			'type'    => 'number',
			'default' => '-20',
			'css'     => 'width: 6em;',
			'id'      => $this->get_option_key( 'ticket_pdf_bg_color_ratio' ),
		);
		$section_fields[] = array(
			'title'   => __( 'PDF Footer Background Color', 'lottery-for-woocommerce' ),
			'type'    => 'color',
			'css'     => 'width: 6em;',
			'default' => '#f4f4f4',
			'id'      => $this->get_option_key( 'ticket_pdf_footer_bg_color' ),
		);
		$section_fields[] = array(
			'title'   => __( 'PDF Footer Font Color', 'lottery-for-woocommerce' ),
			'type'    => 'color',
			'css'     => 'width: 6em;',
			'default' => '#000000',
			'id'      => $this->get_option_key( 'ticket_pdf_footer_font_color' ),
		);
		$section_fields[] = array(
			'type' => 'sectionend',
			'id'   => 'lty_ticket_pdf_color_customization_options',
		);
		// Lottery ticket pdf color customization settings end.

		// Entry list pdf color customization settings start.
		$section_fields[] = array(
			'type'  => 'title',
			'title' => __( 'Entry List PDF Color Customization Settings', 'lottery-for-woocommerce' ),
			'id'    => 'lty_entry_list_pdf_color_customization_options',
		);
		$section_fields[] = array(
			'title'   => __( 'PDF Header Background Color', 'lottery-for-woocommerce' ),
			'type'    => 'color',
			'css'     => 'width: 6em;',
			'default' => '#f4f4f4',
			'id'      => $this->get_option_key( 'entry_list_pdf_header_bg_color' ),
		);
		$section_fields[] = array(
			'title'   => __( 'PDF Header Font Color', 'lottery-for-woocommerce' ),
			'type'    => 'color',
			'css'     => 'width: 6em;',
			'default' => '#000000',
			'id'      => $this->get_option_key( 'entry_list_pdf_header_font_color' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Table Header Background Color', 'lottery-for-woocommerce' ),
			'type'    => 'color',
			'css'     => 'width: 6em;',
			'default' => '#cccccc',
			'id'      => $this->get_option_key( 'entry_list_pdf_table_header_bg_color' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Table Header Font Color', 'lottery-for-woocommerce' ),
			'type'    => 'color',
			'css'     => 'width: 6em;',
			'default' => '#000000',
			'id'      => $this->get_option_key( 'entry_list_pdf_table_header_font_color' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Table Border Color', 'lottery-for-woocommerce' ),
			'type'    => 'color',
			'css'     => 'width: 6em;',
			'default' => '#dddddd',
			'id'      => $this->get_option_key( 'single_product_progress_bar_bg_color' ),
		);
		$section_fields[] = array(
			'title'   => __( 'PDF Footer Background Color', 'lottery-for-woocommerce' ),
			'type'    => 'color',
			'css'     => 'width: 6em;',
			'default' => '#f4f4f4',
			'id'      => $this->get_option_key( 'entry_list_pdf_footer_bg_color' ),
		);
		$section_fields[] = array(
			'title'   => __( 'PDF Footer Font Color', 'lottery-for-woocommerce' ),
			'type'    => 'color',
			'css'     => 'width: 6em;',
			'default' => '#000000',
			'id'      => $this->get_option_key( 'entry_list_pdf_footer_font_color' ),
		);
		$section_fields[] = array(
			'type' => 'sectionend',
			'id'   => 'lty_entry_list_pdf_color_customization_options',
		);
		// Entry list pdf color customization settings end.

		return $section_fields;
	}

	/**
	 * Get the settings for instant winner section.
	 *
	 * @since 10.6.0
	 * @return array
	 * */
	public function instant_winner_section_array() {
		$section_fields = array();
		// Instant winner coupon section start.
		$section_fields[] = array(
			'type'  => 'title',
			'title' => __( 'Instant Winner Coupon Creation Settings', 'lottery-for-woocommerce' ),
			'id'    => 'lty_instant_winner_options',
		);
		$section_fields[] = array(
			'title'   => __( 'Coupon Prefix', 'lottery-for-woocommerce' ),
			'id'      => $this->get_option_key( 'instant_win_coupon_prefix' ),
			'type'    => 'text',
			'default' => 'LTY',
		);
		$section_fields[] = array(
			'title' => __( 'Coupon Suffix', 'lottery-for-woocommerce' ),
			'id'    => $this->get_option_key( 'instant_win_coupon_suffix' ),
			'type'  => 'text',
		);
		$section_fields[] = array(
			'title'             => __( 'Coupon Length', 'lottery-for-woocommerce' ),
			'id'                => $this->get_option_key( 'instant_win_coupon_length' ),
			'type'              => 'number',
			'default'           => 8,
			'custom_attributes' => array( 'min' => 1 ),
			'desc'              => __( 'Coupon length includes coupon prefix and coupon suffix', 'lottery-for-woocommerce' ),
		);
		$section_fields[] = array(
			'title'             => __( 'Validity in Days', 'lottery-for-woocommerce' ),
			'id'                => $this->get_option_key( 'instant_win_coupon_validity' ),
			'type'              => 'number',
			'custom_attributes' => array( 'min' => 1 ),
		);
		$section_fields[] = array(
			'title' => __( 'Minimum Amount for Coupon Usage', 'lottery-for-woocommerce' ),
			'id'    => $this->get_option_key( 'instant_win_coupon_minimum_amount' ),
			'type'  => 'text',
			'class' => 'wc_input_price',
		);
		$section_fields[] = array(
			'title' => __( 'Maximum Amount for Coupon Usage', 'lottery-for-woocommerce' ),
			'id'    => $this->get_option_key( 'instant_win_coupon_maximum_amount' ),
			'type'  => 'text',
			'class' => 'wc_input_price',
		);
		$section_fields[] = array(
			'title'   => __( 'Individual Use Only', 'lottery-for-woocommerce' ),
			'type'    => 'checkbox',
			'default' => 'no',
			'id'      => $this->get_option_key( 'instant_win_coupon_individual_use' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Exclude sale items', 'lottery-for-woocommerce' ),
			'type'    => 'checkbox',
			'default' => 'no',
			'id'      => $this->get_option_key( 'instant_win_coupon_exclude_sale_items' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Allow Free Shipping', 'lottery-for-woocommerce' ),
			'type'    => 'checkbox',
			'default' => 'no',
			'id'      => $this->get_option_key( 'instant_win_coupon_allow_free_shipping' ),
		);
		$section_fields[] = array(
			'title'                   => __( 'Include Products', 'lottery-for-woocommerce' ),
			'id'                      => $this->get_option_key( 'instant_win_coupon_include_products' ),
			'action'                  => 'lty_json_search_products_and_variations',
			'type'                    => 'lty_custom_fields',
			'exclude_global_variable' => 'yes',
			'list_type'               => 'products',
			'lty_field'               => 'ajaxmultiselect',
			'desc'                    => __( 'Product that the coupon will be applied to, or that need to be in the cart in order to be applied.', 'lottery-for-woocommerce' ),
			'desc_tip'                => true,
			'placeholder'             => __( 'Select a Product', 'lottery-for-woocommerce' ),
		);
		$section_fields[] = array(
			'title'                   => __( 'Exclude Products', 'lottery-for-woocommerce' ),
			'id'                      => $this->get_option_key( 'instant_win_coupon_exclude_products' ),
			'action'                  => 'lty_json_search_products_and_variations',
			'type'                    => 'lty_custom_fields',
			'exclude_global_variable' => 'yes',
			'list_type'               => 'products',
			'lty_field'               => 'ajaxmultiselect',
			'desc'                    => __( 'Product that the coupon will not be applied to, or that cannot be in the cart in order to be applied.', 'lottery-for-woocommerce' ),
			'desc_tip'                => true,
			'placeholder'             => __( 'Select a Product', 'lottery-for-woocommerce' ),
		);
		$section_fields[] = array(
			'title'    => __( 'Include Categories', 'lottery-for-woocommerce' ),
			'id'       => $this->get_option_key( 'instant_win_coupon_include_categories' ),
			'type'     => 'multiselect',
			'class'    => 'lty_select2',
			'default'  => array(),
			'options'  => lty_get_wc_categories(),
			'desc'     => __( 'Product categories that the coupon will be applied to, or that need to be in the cart in order to be applied.', 'lottery-for-woocommerce' ),
			'desc_tip' => true,
		);
		$section_fields[] = array(
			'title'    => __( 'Exclude Categories', 'lottery-for-woocommerce' ),
			'id'       => $this->get_option_key( 'coupon_exclude_categories' ),
			'type'     => 'multiselect',
			'class'    => 'lty_select2',
			'default'  => array(),
			'options'  => lty_get_wc_categories(),
			'desc'     => __( 'Product categories that the coupon will not be applied to, or that cannot be in the cart in order to be applied.', 'lottery-for-woocommerce' ),
			'desc_tip' => true,
		);
		$section_fields[] = array(
			'type' => 'sectionend',
			'id'   => 'lty_instant_winner_options',
		);
		// Instant winner section end.

		return $section_fields;
	}

	/**
	 * Display question answers.
	 * */
	public function display_question_answers() {
		include_once LTY_PLUGIN_PATH . '/inc/admin/menu/views/html-product-data-question-global.php';
	}

	/**
	 * Save question answers.
	 *
	 * @return array
	 * */
	public function save_question_answers( $question_answers, $option, $raw_value ) {

		if ( ! lty_check_is_array( $question_answers ) ) {
			return array();
		}

		$formatted_questions = array();
		foreach ( $question_answers as $key => $question ) {
			$formatted_questions[ $key ]['question'] = isset( $_REQUEST['lty_settings_lty_questions_global_settings'] ) ? wc_clean( wp_unslash( $_REQUEST['lty_settings_lty_questions_global_settings'] ) ) : '';

			if ( ! isset( $question['answers'] ) || ! lty_check_is_array( $question['answers'] ) ) {
				continue;
			}

			$answers = array_filter( array_merge( $question['answers'] ) );

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
}

return new LTY_General_Tab();
