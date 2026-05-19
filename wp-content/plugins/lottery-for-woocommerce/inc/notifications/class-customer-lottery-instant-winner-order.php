<?php
/**
 * Customer - Lottery Instant Winner Order
 *
 * @since 10.4.0
 * */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'LTY_Customer_Lottery_Instant_Winner_Order_Notification' ) ) {

	/**
	 * Class.
	 *
	 * @since 10.4.0
	 * */
	class LTY_Customer_Lottery_Instant_Winner_Order_Notification extends LTY_Notifications {

		/**
		 * Class Constructor.
		 * */
		public function __construct() {
			$this->id          = 'customer_instant_winner_order';
			$this->type        = 'customer';
			$this->title       = __( 'Customer - Giveaway Instant Winner for recent order', 'lottery-for-woocommerce' );
			$this->description = __( 'Send email to the user who won instant win prize in participated giveaway for recent order.', 'lottery-for-woocommerce' );

			// Triggers for this email.
			add_action( sanitize_key( $this->plugin_slug . '_lottery_ticket_confirmed' ), array( $this, 'trigger' ), 10, 4 );
			// Render email shortcode information.
			add_action( 'woocommerce_admin_field_lty_display_email_shortcode_' . sanitize_title( $this->id ), array( $this, 'render_email_shortcode_information' ) );

			parent::__construct();
		}

		/**
		 * Default Subject.
		 *
		 * @since 10.4.0
		 * @return string
		 * */
		public function get_default_subject() {
			return '{site_name} - Congratulations! You have won Instant Win Prize in a Lottery';
		}

		/**
		 * Default Message.
		 *
		 * @since 10.4.0
		 * @return string
		 * */
		public function get_default_message() {
			return 'Hi {user_name},

			Congratulations! You have won Instant Win Prize in the recent lottery purchase(Order number: #{order_number}).<br/><br/>
			<b>Instant Win Details:-<b/><br/>
			{instant_winner_details}
			Thanks.';
		}

		/**
		 * Trigger the sending of this email.
		 *
		 * @since 10.4.0
		 * @param array $ticket_ids Ticket IDs.
		 * @param array $ticket_data Ticket data.
		 * @param int   $order_id Order ID.
		 * @param array $instant_winner_ticket_ids Instant winner ticket IDs.
		 * @return void
		 */
		public function trigger( $ticket_ids, $ticket_data, $order_id, $instant_winner_ticket_ids ) {
			if ( ! $this->is_enabled() ) {
				return;
			}

			if ( ! lty_check_is_array( $instant_winner_ticket_ids ) || ! $order_id ) {
				return;
			}

			$lottery_ticket = lty_get_lottery_ticket( $instant_winner_ticket_ids[0] );
			if ( ! is_object( $lottery_ticket ) ) {
				return;
			}

				$this->recipient                                = $lottery_ticket->get_user_email();
				$this->placeholders['{user_name}']              = $lottery_ticket->get_user_name();
				$this->placeholders['{first_name}']             = is_object( $lottery_ticket->get_user() ) ? $lottery_ticket->get_user()->first_name : '';
				$this->placeholders['{last_name}']              = is_object( $lottery_ticket->get_user() ) ? $lottery_ticket->get_user()->last_name : '';
				$this->placeholders['{site_name}']              = $this->get_blogname();
				$this->placeholders['{order_number}']           = sprintf( '<a href="%s">%s</a>', wc_get_endpoint_url( 'view-order', $order_id, get_permalink( wc_get_page_id( 'myaccount' ) ) ), esc_attr( $order_id ) );
				$this->placeholders['{order_id}']               = esc_attr( $order_id );
				$this->placeholders['{ticket_number}']          = $lottery_ticket->get_lottery_ticket_number();
				$this->placeholders['{lottery_start_date}']     = esc_html( LTY_Date_Time::get_wp_format_datetime_from_gmt( $lottery_ticket->get_product()->get_lty_start_date_gmt() ) );
				$this->placeholders['{lottery_end_date}']       = esc_html( LTY_Date_Time::get_wp_format_datetime_from_gmt( $lottery_ticket->get_product()->get_lty_end_date_gmt() ) );
				$this->placeholders['{instant_winner_details}'] = $this->get_instant_winner_ticket_details( $instant_winner_ticket_ids, $order_id );
				$this->placeholders['{user_billing_details}']   = $this->get_user_billing_details( $lottery_ticket->get_order() );

			if ( $this->get_recipient() ) {
				$this->send_email( $this->get_recipient(), $this->get_subject(), $this->get_formatted_message(), $this->get_headers(), $this->get_attachments() );
			}
		}

		/**
		 * Get the settings array.
		 *
		 * @since 10.4.0
		 * @return array
		 * */
		public function get_settings_array() {
			$section_fields = array();

			// Customer Instant Winner Email Section Start.
			$section_fields[] = array(
				'type'  => 'title',
				'title' => __( 'Customer - Giveaway Instant Winner for recent order', 'lottery-for-woocommerce' ),
				'id'    => 'lty_lottery_instant_winner_order_email_options',
			);
			$section_fields[] = array(
				'title'   => __( 'Enable', 'lottery-for-woocommerce' ),
				'type'    => 'checkbox',
				'default' => 'no',
				'id'      => $this->get_option_key( 'enabled' ),
			);
			$section_fields[] = array(
				'title'   => __( 'Subject', 'lottery-for-woocommerce' ),
				'type'    => 'text',
				'default' => $this->get_default_subject(),
				'id'      => $this->get_option_key( 'subject' ),
			);
			$section_fields[] = array(
				'title'     => __( 'Message', 'lottery-for-woocommerce' ),
				'type'      => 'lty_custom_fields',
				'lty_field' => 'wpeditor',
				'default'   => $this->get_default_message(),
				'id'        => $this->get_option_key( 'message' ),
			);
			$section_fields[] = array(
				'type' => 'lty_display_email_shortcode_' . $this->id,
			);
			$section_fields[] = array(
				'type' => 'sectionend',
				'id'   => 'lty_lottery_instant_winner_order_email_options',
			);
			// Customer Instant Winner Email Section End.
			return $section_fields;
		}

		/**
		 * Render email shortcode information.
		 *
		 * @since 10.4.0
		 * @return void
		 * */
		public function render_email_shortcode_information() {
			$shortcodes_info = array(
				'{site_name}'              => array(
					'description' => __( 'Displays the site name', 'lottery-for-woocommerce' ),
				),
				'{user_name}'              => array(
					'description' => __( 'Displays the giveaway user name', 'lottery-for-woocommerce' ),
				),
				'{first_name}'             => array(
					'description' => __( 'Displays the giveaway first name', 'lottery-for-woocommerce' ),
				),
				'{last_name}'              => array(
					'description' => __( 'Displays the giveaway last name', 'lottery-for-woocommerce' ),
				),
				'{order_id}'               => array(
					'description' => __( 'Displays the order id without link', 'lottery-for-woocommerce' ),
				),
				'{order_number}'           => array(
					'description' => __( 'Displays the order number with link', 'lottery-for-woocommerce' ),
				),
				'{lottery_start_date}'     => array(
					'description' => __( 'Displays the giveaway start date', 'lottery-for-woocommerce' ),
				),
				'{lottery_end_date}'       => array(
					'description' => __( 'Displays the giveaway end date', 'lottery-for-woocommerce' ),
				),
				'{Ticket_number}'          => array(
					'description' => __( 'Displays the giveaway ticket number', 'lottery-for-woocommerce' ),
				),
				'{instant_winner_details}' => array(
					'description' => __( 'Displays the giveaway instant winner details', 'lottery-for-woocommerce' ),
				),
				'{user_billing_details}'   => array(
					'description' => __( 'Displays the user billing details', 'lottery-for-woocommerce' ),
				),
				'{logo}'                   => array(
					'description' => __( 'Displays the logo', 'lottery-for-woocommerce' ),
				),
			);

			include_once LTY_ABSPATH . 'inc/admin/menu/views/html-email-shortcodes-info.php';
		}

		/**
		 * Get instant winner ticket details.
		 *
		 * @since 10.4.0
		 * @param int    $ticket_ids Ticket IDs.
		 * @param int    $order_id Order ID.
		 * @return string|HTML
		 */
		public function get_instant_winner_ticket_details( $ticket_ids, $order_id ) {
			$args = array(
				'ticket_ids' => $ticket_ids,
				'order_id'      => $order_id,
			);

			return lty_get_template_html( 'email-shortcodes/customer-instant-winner-details-order.php', $args );
		}

		/**
		 * Get user billing details.
		 *
		 * @since 10.4.0
		 * @param object $order Order object.
		 * @return string|HTML
		 * */
		public function get_user_billing_details( $order ) {
			return lty_get_template_html( 'email-shortcodes/user-billing-details.php', array( 'order' => $order ) );
		}
	}

}
