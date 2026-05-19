<?php

/**
 * Customer - Lottery Ticket Confirmation
 *
 * @since 8.3.0
 * */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'LTY_Customer_Lottery_Ticket_Confirmation_Order_Notification' ) ) {

	/**
	 * Class.
	 *
	 * @since 8.3.0
	 * */
	class LTY_Customer_Lottery_Ticket_Confirmation_Order_Notification extends LTY_Notifications {

		/**
		 * Ticket IDs.
		 *
		 * @since 9.5.0
		 * @var array
		 * */
		private $ticket_ids = array();

		/**
		 * Order ID.
		 *
		 * @since 9.5.0
		 * @var string|int
		 * */
		private $order_id;

		/**
		 * Can display pdf attachment fields?
		 *
		 * @since 9.5.0
		 * @var bool
		 */
		public $pdf_attachment = true;

		/**
		 * Class constructor.
		 *
		 * @since 8.3.0
		 * */
		public function __construct() {
			$this->id          = 'customer_lottery_ticket_confirmation_order';
			$this->type        = 'customer';
			$this->title       = __( 'Customer - Giveaway Participation Confirmation for recent order', 'lottery-for-woocommerce' );
			$this->description = __( 'Send email to the giveaway participants for recent order', 'lottery-for-woocommerce' );

			// Triggers for this email.
			add_action( sanitize_key( $this->plugin_slug . '_lottery_ticket_confirmed' ), array( $this, 'trigger' ), 10, 3 );
			// Render email shortcode information.
			add_action( 'woocommerce_admin_field_lty_display_email_shortcode_' . sanitize_title( $this->id ), array( $this, 'render_email_shortcode_information' ) );

			parent::__construct();
		}

		/**
		 * Default subject.
		 *
		 * @since 8.3.0
		 * @return string
		 * */
		public function get_default_subject() {
			return '{site_name} - Lottery Participation Confirmation for recent order #{order_id}';
		}

		/**
		 * Default message.
		 *
		 * @since 8.3.0
		 * @return string
		 * */
		public function get_default_message() {
			return 'Hi {user_name},

			Thanks for participating in the Lottery.

			<b>Details</b>

			Order number: #{order_number}

			{lottery_details}

			Thanks.';
		}

		/**
		 * Trigger the sending of this email.
		 *
		 * @since 8.3.0
		 * @param int/string $ticket_id
		 * @param array      $tickets_data
		 * @param int/string $order_id
		 * @return void
		 * */
		public function trigger( $ticket_id, $tickets_data, $order_id ) {
			if ( ! $this->is_enabled() ) {
				return;
			}

			if ( ! lty_check_is_array( $tickets_data ) ) {
				return;
			}

			$ticket = lty_get_lottery_ticket( $ticket_id );
			if ( ! is_object( $ticket ) ) {
				return;
			}

			$this->ticket_ids                                    = $tickets_data;
			$this->order_id                                      = $order_id;
			$this->recipient                                     = $ticket->get_user_email();
			$this->placeholders['{user_name}']                   = $ticket->get_user_name();
			$this->placeholders['{first_name}']                  = is_object( $ticket->get_user() ) ? $ticket->get_user()->first_name : '';
			$this->placeholders['{last_name}']                   = is_object( $ticket->get_user() ) ? $ticket->get_user()->last_name : '';
			$this->placeholders['{order_number}']                = sprintf( '<a href="%s">%s</a>', wc_get_endpoint_url( 'view-order', $order_id, get_permalink( wc_get_page_id( 'myaccount' ) ) ), esc_attr( $order_id ) );
			$this->placeholders['{order_id}']                    = esc_attr( $order_id );
			$this->placeholders['{lottery_details}']             = $this->display_lottery_details( $tickets_data );
			$this->placeholders['{user_billing_details}']        = $this->render_user_billing_details( $ticket->get_order() );
			$this->placeholders['{tickets_pdf_download_button}'] = $this->render_ticket_pdf_download_button();

			if ( $this->get_recipient() ) {
				$this->send_email( $this->get_recipient(), $this->get_subject(), $this->get_formatted_message(), $this->get_headers(), $this->get_attachments() );
			}
		}

		/**
		 * Display lottery details.
		 *
		 * @since 8.3.0
		 * @param array $tickets_data
		 * @return string
		 * */
		public function display_lottery_details( $tickets_data ) {
			$args = array(
				'tickets_data' => $tickets_data,
			);

			return lty_get_template_html( 'email-shortcodes/customer-lottery-winner-details-order.php', $args );
		}

		/**
		 * Render user billing details.
		 *
		 * @since 8.5.0
		 * @param object $order
		 * @return string
		 * */
		public function render_user_billing_details( $order ) {
			return lty_get_template_html( 'email-shortcodes/user-billing-details.php', array( 'order' => $order ) );
		}

		/**
		 * Render ticket(s) pdf download button details.
		 *
		 * @since 10.5.0
		 * @return string
		 * */
		public function render_ticket_pdf_download_button() {
			$button_url = esc_url(
				add_query_arg(
					array(
						'action'  => 'lty-download',
						'lty_key' => lty_encode(
							array( 'lty_order_id' => $this->order_id ),
							true
						),
					),
					get_site_url()
				)
			);

			return lty_get_template_html( 'email-shortcodes/lottery-ticket-download-button.php', array( 'button_url' => $button_url ) );
		}

		/**
		 * Get the attachments.
		 *
		 * @since 9.5.0
		 * @return array
		 * */
		public function get_attachments() {
			$email_attachments = array();
			if ( ! $this->is_enabled_pdf_attachment() ) {
				return $email_attachments;
			}

			$ticket_ids = array();
			foreach ( $this->ticket_ids as $product_id => $ticket_numbers ) {
				if ( ! lty_check_is_array( $ticket_numbers ) ) {
					continue;
				}

				foreach ( $ticket_numbers as $ticket_id => $ticket_number ) {
					$ticket_ids[] = $ticket_id;
				}
			}

			$email_attachments[] = lty_get_lottery_ticket_pdf_file_path( $ticket_ids, $this->order_id );

			/**
			 * This hook is used to alter the customer lottery ticket confirmation for recent order email attachments.
			 *
			 * @since 9.5.0
			 * @param array $email_attachments
			 * @param object $this
			 */
			return apply_filters( 'lty_customer_lottery_ticket_confirmation_order_email_attachments', $email_attachments, $this );
		}

		/**
		 * Get the settings array.
		 *
		 * @since 8.3.0
		 * @return array
		 * */
		public function get_settings_array() {
			$section_fields = array();

			// Customer lottery ticket confirmation email section start.
			$section_fields[] = array(
				'type'  => 'title',
				'title' => __( 'Giveaway Ticket Confirmation for recent order', 'lottery-for-woocommerce' ),
				'id'    => 'lty_lottery_ticket_confirmation_email_options',
			);
			$section_fields[] = array(
				'title'   => __( 'Enable', 'lottery-for-woocommerce' ),
				'type'    => 'checkbox',
				'default' => 'no',
				'id'      => $this->get_option_key( 'enabled' ),
			);
			$section_fields[] = array(
				'title'   => __( 'Enable PDF', 'lottery-for-woocommerce' ),
				'type'    => 'checkbox',
				'default' => 'no',
				'id'      => $this->get_option_key( 'pdf_attachment' ),
				'desc'    => __( 'Enable this checkbox to send an email to PDF attached on giveaway ticket confirmation.', 'lottery-for-woocommerce' ),
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
				'id'   => 'lty_lottery_ticket_confirmation_email_options',
			);
			// Customer lottery ticket confirmation email section end.

			return $section_fields;
		}

		/**
		 * Render email shortcode information.
		 *
		 * @since 8.3.0
		 * @return void
		 * */
		public function render_email_shortcode_information() {
			$shortcodes_info = array(
				'{user_name}'            => array(
					'description' => __( 'Displays the giveaway user name', 'lottery-for-woocommerce' ),
				),
				'{first_name}'           => array(
					'description' => __( 'Displays the giveaway first name', 'lottery-for-woocommerce' ),
				),
				'{last_name}'            => array(
					'description' => __( 'Displays the giveaway last name', 'lottery-for-woocommerce' ),
				),
				'{order_id}'             => array(
					'description' => __( 'Displays the order id without link', 'lottery-for-woocommerce' ),
				),
				'{order_number}'         => array(
					'description' => __( 'Displays the order number with link', 'lottery-for-woocommerce' ),
				),
				'{lottery_details}'      => array(
					'description' => __( 'Displays the giveaway ticket details', 'lottery-for-woocommerce' ),
				),
				'{site_name}'            => array(
					'description' => __( 'Displays the site name', 'lottery-for-woocommerce' ),
				),
				'{user_billing_details}' => array(
					'description' => __( 'Displays the user billing details', 'lottery-for-woocommerce' ),
				),
				'{logo}'                 => array(
					'description' => __( 'Displays the logo', 'lottery-for-woocommerce' ),
				),
				'{tickets_pdf_download_button}' => array(
					'description' => __( 'Displays the giveaway ticket(s) PDF download button', 'lotttery-for-woocommerce' ),
				),
			);

			include_once LTY_ABSPATH . 'inc/admin/menu/views/html-email-shortcodes-info.php';
		}
	}
}
