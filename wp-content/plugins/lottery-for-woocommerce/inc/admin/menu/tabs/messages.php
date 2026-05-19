<?php

/**
 * Message Tab.
 * */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( class_exists( 'LTY_Message_Tab' ) ) {
	return new LTY_Message_Tab();
}

/**
 * LTY_Message_Tab.
 * */
class LTY_Message_Tab extends LTY_Settings_Page {

	/**
	 * Constructor.
	 * */
	public function __construct() {
		$this->id    = 'messages';
		$this->label = __( 'Messages', 'lottery-for-woocommerce' );

		parent::__construct();
	}

	/**
	 * Get settings for Messages section array.
	 * */
	public function messages_section_array() {
		$section_fields = array();

		// Lottery Messages on Single Product Page Section Start.
		$section_fields[] = array(
			'type'  => 'title',
			'title' => __( 'Giveaway Messages on Single Product Page', 'lottery-for-woocommerce' ),
			'id'    => $this->get_option_key( 'single_product_messages_section' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Minimum Ticket Message', 'lottery-for-woocommerce' ),
			'type'    => 'textarea',
			'default' => 'This giveaway has a minimum of {lottery_minimum_ticket} tickets',
			'id'      => $this->get_option_key( 'single_product_min_ticket_message' ),
			'desc'    => __( '<b>Supported Shortcodes:<br/>{lottery_minimum_ticket}</b> - Minimum Giveaway Ticket(s)', 'lottery-for-woocommerce' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Minimum Tickets Per User Message', 'lottery-for-woocommerce' ),
			'type'    => 'textarea',
			'default' => 'This giveaway to be purchased with a minimum of {lottery_minimum_ticket_per_user} tickets',
			'id'      => $this->get_option_key( 'single_product_min_tickets_per_user_message' ),
			'desc'    => __( '<b>Supported Shortcodes:<br/>{lottery_minimum_ticket_per_user}</b> - Minimum Giveaway Ticket(s) Per user', 'lottery-for-woocommerce' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Maximum Tickets Message', 'lottery-for-woocommerce' ),
			'type'    => 'textarea',
			'default' => 'This giveaway is limited to {lottery_maximum_ticket} tickets',
			'id'      => $this->get_option_key( 'single_product_max_ticket_message' ),
			'desc'    => __( '<b>Supported Shortcodes:<br/>{lottery_maximum_ticket}</b> - Maximum Giveaway Ticket(s)', 'lottery-for-woocommerce' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Maximum Tickets Per User Message', 'lottery-for-woocommerce' ),
			'type'    => 'textarea',
			'default' => 'This giveaway to be purchased with a maximum of {lottery_maximum_ticket_per_user} tickets',
			'id'      => $this->get_option_key( 'single_product_max_tickets_per_user_message' ),
			'desc'    => __( '<b>Supported Shortcodes:<br/>{lottery_maximum_ticket_per_user}</b> - Maximum Giveaway Ticket(s) Per user', 'lottery-for-woocommerce' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Current Tickets Sold Message', 'lottery-for-woocommerce' ),
			'type'    => 'textarea',
			'default' => 'Tickets Sold',
			'id'      => $this->get_option_key( 'single_product_current_ticket_sold_message' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Number of Winner in Giveaway Message', 'lottery-for-woocommerce' ),
			'type'    => 'textarea',
			'default' => 'This giveaway will have {lottery_winner_count} winners',
			'id'      => $this->get_option_key( 'single_product_lottery_winner_count_message' ),
			'desc'    => __( '<b>Supported Shortcodes:<br/>{lottery_winner_count}</b> - Giveaway Winner(s) Count', 'lottery-for-woocommerce' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Giveaway Gift Message - Product Inside the Site', 'lottery-for-woocommerce' ),
			'type'    => 'textarea',
			'default' => '{gift_product} will be Given to Winner for Free',
			'id'      => $this->get_option_key( 'single_product_lottery_gift_message' ),
			'desc'    => __( '<b>Supported Shortcodes:<br/>{gift_product}</b> - Displays the Gift Product', 'lottery-for-woocommerce' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Giveaway Gift Message - Product Outside the Site', 'lottery-for-woocommerce' ),
			'type'    => 'textarea',
			'default' => 'Winner will get the Products mentioned in {gift_details_URL}',
			'id'      => $this->get_option_key( 'single_product_outside_lottery_gift_message' ),
			'desc'    => __( '<b>Supported Shortcodes:<br/>{gift_details_URL}</b> - Displays the Gift Product(s) URL', 'lottery-for-woocommerce' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Message for Non-Winners', 'lottery-for-woocommerce' ),
			'type'    => 'textarea',
			'default' => 'Sorry, better luck next time.',
			'id'      => $this->get_option_key( 'single_product_lottery_not_winners_message' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Message for Waiting for Giveaway Result', 'lottery-for-woocommerce' ),
			'type'    => 'textarea',
			'default' => 'Please wait for the result',
			'id'      => $this->get_option_key( 'single_product_lottery_wait_message' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Single Winner Message', 'lottery-for-woocommerce' ),
			'type'    => 'textarea',
			'default' => 'Congratulations! You have won this giveaway',
			'id'      => $this->get_option_key( 'single_product_lottery_winner_message' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Multiple Winner Message', 'lottery-for-woocommerce' ),
			'type'    => 'textarea',
			'default' => 'Congratulations! You are one of the winners for this giveaway',
			'id'      => $this->get_option_key( 'single_product_lottery_multi_winner_message' ),
		);

		if ( '2' == get_option( 'lty_settings_guest_user_participate_type' ) ) {
			$section_fields[] = array(
				'title'   => __( 'Guest User Error Message', 'lottery-for-woocommerce' ),
				'type'    => 'textarea',
				'default' => 'Sorry, you must be logged-in to participate in this giveaway.',
				'id'      => $this->get_option_key( 'single_product_guest_error_message' ),
			);
			$section_fields[] = array(
				'title'   => __( 'Guest User Error Message when Add to Cart Button is Clicked', 'lottery-for-woocommerce' ),
				'type'    => 'textarea',
				'default' => 'Please login to participate in the giveaway',
				'id'      => $this->get_option_key( 'single_product_validate_guest_error_message' ),
			);
		}
		$section_fields[] = array(
			'title'   => __( 'IP Address Restriction Error Message', 'lottery-for-woocommerce' ),
			'type'    => 'textarea',
			'default' => 'Sorry, you cannot participate in this Giveaway because, your IP Address is restricted.',
			'id'      => $this->get_option_key( 'ip_address_restriction_error_message' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Lucky Dip Button Hovering Message(Enabled Force User to Select the Answer)', 'lottery-for-woocommerce' ),
			'type'    => 'textarea',
			'default' => 'Please select an answer',
			'id'      => $this->get_option_key( 'lucky_dip_question_answer_hover_message' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Lucky Dip Tickets Added to Cart Message', 'lottery-for-woocommerce' ),
			'type'    => 'textarea',
			'default' => '<b>Ticket Number(s) has been added to your cart.</b><br/>{ticket_numbers}',
			'id'      => $this->get_option_key( 'lucky_dip_added_to_cart_message' ),
			'desc'    => __( '<b>Supported Shortcodes:<br/>{ticket_numbers}</b> - Ticket Numbers added to cart', 'lottery-for-woocommerce' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Question & Answer Error Message', 'lottery-for-woocommerce' ),
			'type'    => 'textarea',
			'default' => 'Please select an answer',
			'id'      => $this->get_option_key( 'question_answer_alert_error_message' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Verify Question & Answer Alert Message', 'lottery-for-woocommerce' ),
			'type'    => 'textarea',
			'default' => 'Are you sure you want to proceed with the Selected Answer?',
			'id'      => $this->get_option_key( 'verify_question_answer_alert_error_message' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Manual Ticket Selection Error Message', 'lottery-for-woocommerce' ),
			'type'    => 'textarea',
			'default' => 'Please select a ticket number',
			'id'      => $this->get_option_key( 'ticket_selection_alert_error_message' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Verify Answer Limited Type(Attempts More than 1) Error Message', 'lottery-for-woocommerce' ),
			'type'    => 'textarea',
			'default' => 'Incorrect answer. {attempts} attempt(s) left.',
			'id'      => $this->get_option_key( 'limited_type_multiple_attempts_error_message' ),
			'desc'    => __( '<b>Supported Shortcodes:<br/>{attempts}</b> - Remaining Attempts', 'lottery-for-woocommerce' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Verify Answer Limited Type(1 Attempt) Error Message', 'lottery-for-woocommerce' ),
			'type'    => 'textarea',
			'default' => 'Incorrect answer. You cannot participate in this giveaway.',
			'id'      => $this->get_option_key( 'limited_type_single_attempt_error_message' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Verify Answer Unlimited Type Error Message', 'lottery-for-woocommerce' ),
			'type'    => 'textarea',
			'default' => 'Incorrect answer. Please select the correct answer to participate in the giveaway',
			'id'      => $this->get_option_key( 'unlimited_type_error_message' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Question Answer Time Limit Exceeded Message', 'lottery-for-woocommerce' ),
			'type'    => 'textarea',
			'default' => 'You have exceeded the time limit to answer the question, hence you cannot participate in this giveaway.',
			'id'      => $this->get_option_key( 'answer_time_limit_exists_error_message' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Maximum Ticket(s) Purchase Per User Limit Error Message', 'lottery-for-woocommerce' ),
			'type'    => 'textarea',
			'default' => 'You have reached the Maximum ticket count {maximum_tickets_count} for this giveaway. Hence you cannot purchase new giveaway tickets.',
			'id'      => $this->get_option_key( 'maximum_tickets_purchase_limit_error_message' ),
			'desc'    => __( '<b>Supported Shortcodes:<br/>{maximum_tickets_count}</b> - Maximum Ticket(s) Per User', 'lottery-for-woocommerce' ),
		);
		$section_fields[] = array(
			'title'   => __( 'User Chooses the Ticket Search Error Message', 'lottery-for-woocommerce' ),
			'type'    => 'textarea',
			'default' => 'Ticket(s) not found',
			'id'      => $this->get_option_key( 'user_chooses_ticket_search_error' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Tickets Sold Out Message on each tab on User Chooses the Ticket Mode', 'lottery-for-woocommerce' ),
			'type'    => 'textarea',
			'default' => 'Tickets Sold out in this tab',
			'id'      => $this->get_option_key( 'user_chooses_ticket_all_tickets_sold_label' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Purchased Tickets Message', 'lottery-for-woocommerce' ),
			'type'    => 'textarea',
			'default' => 'You have bought %d ticket(s) for this giveaway!',
			'id'      => $this->get_option_key( 'purchased_tickets_message' ),
			/* translators: %d: Purchased Ticket(s) Count */
			'desc'    => __( '<b>Supported Shortcodes:<br/>%d</b> - Purchased Ticket(s) Count', 'lottery-for-woocommerce' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Minimum Tickets not Met Error Message', 'lottery-for-woocommerce' ),
			'type'    => 'textarea',
			'default' => 'Minimum Ticket Count not met',
			'id'      => $this->get_option_key( 'minimum_tickets_not_met_error' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Unique Winner(s) Count not Met Error Message', 'lottery-for-woocommerce' ),
			'type'    => 'textarea',
			'default' => 'Unique Winners Count not met',
			'id'      => $this->get_option_key( 'unique_winners_count_not_met_error' ),
		);
		$section_fields[] = array(
			'type' => 'sectionend',
			'id'   => $this->get_option_key( 'single_product_messages_section' ),
		);
		// Lottery Messages on Single Product Page Section End.

		// Lottery messages on shop and category page section start.
		$section_fields[] = array(
			'type'  => 'title',
			'title' => __( 'Giveaway Messages on Shop and Category Page', 'lottery-for-woocommerce' ),
			'id'    => $this->get_option_key( 'shop_and_category_messages_section' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Remaining Tickets Message', 'lottery-for-woocommerce' ),
			'type'    => 'textarea',
			'default' => 'Remaining Tickets: {remaining_tickets}',
			'id'      => $this->get_option_key( 'shop_remaining_tickets_message' ),
			'desc'    => __( '<b>Supported Shortcodes:<br/>{maximum_tickets}</b> - Displays the Total number of tickets<br><b>{remaining_tickets}</b> - Displays the Remaining Tickets', 'lottery-for-woocommerce' ),
		);
		$section_fields[] = array(
			'type' => 'sectionend',
			'id'   => $this->get_option_key( 'shop_and_category_messages_section' ),
		);
		// Lottery messages on shop and category page section end.

		// Lottery order message section start.
		$section_fields[] = array(
			'type'  => 'title',
			'title' => __( 'Giveaway Order Messages', 'lottery-for-woocommerce' ),
			'id'    => $this->get_option_key( 'order_messages_section' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Ticket Number Display Message in Thank you Page and Order Details Page', 'lottery-for-woocommerce' ),
			'type'    => 'textarea',
			'default' => 'You will receive the ticket number only when the payment is completed.',
			'id'      => $this->get_option_key( 'ticket_pending_payment_message' ),
		);
		$section_fields[] = array(
			'type' => 'sectionend',
			'id'   => $this->get_option_key( 'order_messages_section' ),
		);
		// Lottery order messages section end.
		// Lottery Guest Messages Section Start.
		$section_fields[] = array(
			'type'  => 'title',
			'title' => __( 'Giveaway Guest Messages', 'lottery-for-woocommerce' ),
			'id'    => $this->get_option_key( 'guest_messages_section' ),
		);
		$section_fields[] = array(
			'title'   => __( 'Guest Message', 'lottery-for-woocommerce' ),
			'type'    => 'textarea',
			'default' => 'Please login to view/participate in the giveaway',
			'id'      => $this->get_option_key( 'guest_message' ),
		);
		$section_fields[] = array(
			'type' => 'sectionend',
			'id'   => $this->get_option_key( 'guest_messages_section' ),
		);
		// Lottery Guest Messages Section End.

		return $section_fields;
	}
}

return new LTY_Message_Tab();
