<?php

/**
 * Admin - Giveaway Ticket Confirmation
 * */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'LTY_Admin_Lottery_Ticket_Confirmation_Notification' ) ) {

	/**
	 * Class LTY_Admin_Lottery_Ticket_Confirmation_Notification.
	 * */
	class LTY_Admin_Lottery_Ticket_Confirmation_Notification extends LTY_Notifications {

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
		 * Class Constructor.
		 * */
		public function __construct() {
			$this->id          = 'admin_lottery_ticket_confirmation';
			$this->type        = 'admin';
			$this->title       = __( 'Admin - Giveaway Ticket Confirmation', 'lottery-for-woocommerce' );
			$this->description = __( 'Send email to admin when the giveaway ticket has been Confirmation', 'lottery-for-woocommerce' );

			// Triggers for this email.
			add_action( sanitize_key( $this->plugin_slug . '_lottery_ticket_confirmed' ), array( $this, 'trigger' ), 10, 3 );
			// Render email shortcode information.
			add_action( 'woocommerce_admin_field_lty_display_email_shortcode_' . sanitize_title( $this->id ), array( $this, 'render_email_shortcode_information' ) );
			parent::__construct();
		}

		/**
		 * Default Subject.
		 * */
		public function get_default_subject() {
			return '{site_name} - Lottery Ticket Confirmation';
		}

		/**
		 * Default Message.
		 * */
		public function get_default_message() {
			return 'Hi {user_name},

			The Lottery "{product_name}"  No. Ticket: {quantity}, 
			Ticket Number: {Ticket_number}, 
			Order number: #{order_id} has been purchased on {site_name}.
			Thanks';
		}

		/**
		 * Trigger the sending of this email.
		 * */
		public function trigger( $ticket_id, $tickets_data, $order_id ) {
			if ( ! $this->is_enabled() ) {
				return;
			}

			if ( ! lty_check_is_array( $tickets_data ) ) {
				return;
			}

			$this->ticket_ids = $tickets_data;
			$this->order_id   = $order_id;
			foreach ( $tickets_data as $product_id => $ticket_numbers ) {
				$product = wc_get_product( $product_id );
				if ( ! lty_is_lottery_product( $product ) ) {
					continue;
				}

				$this->recipient                              = $this->get_admin_emails();
				$this->placeholders['{user_name}']            = $this->get_from_name();
				$this->placeholders['{quantity}']             = count( array_values( $ticket_numbers ) );
				$this->placeholders['{product_name}']         = $product->get_product_name( true );
				$this->placeholders['{Ticket_number}']        = implode( ' , ', array_values( $ticket_numbers ) );
				$this->placeholders['{order_number}']         = sprintf( '<a href="%s">%s</a>', get_edit_post_link( $order_id ), esc_attr( $order_id ) );
				$this->placeholders['{order_id}']             = esc_attr( $order_id );
				$this->placeholders['{site_name}']            = $this->get_blogname();
				$this->placeholders['{lottery_winning_item}'] = lty_get_lottery_gift_products( false, $product, true );
				$this->placeholders['{selected_answer}']      = $this->get_selected_answer( array_keys( $ticket_numbers ) );
				$this->placeholders['{correct_answer}']       = $this->get_correct_answer( $product );
				$this->placeholders['{user_billing_details}'] = $this->render_user_billing_details( wc_get_order( $order_id ) );

				if ( $this->get_recipient() ) {
					$this->send_email( $this->get_recipient(), $this->get_subject(), $this->get_formatted_message(), $this->get_headers(), $this->get_attachments() );
				}
			}
		}

		/**
		 * Get selected answer.
		 * */
		public function get_selected_answer( $ticket_ids ) {
			$ticket_id = isset( $ticket_ids[0] ) ? $ticket_ids[0] : '';
			if ( '' == $ticket_id ) {
				return '-';
			}

			$ticket = lty_get_lottery_ticket( $ticket_id );
			if ( ! is_object( $ticket ) ) {
				return '-';
			}

			return '' !== $ticket->get_answer() ? $ticket->get_answer() : '-';
		}

		/**
		 * Get correct answer.
		 * */
		public function get_correct_answer( $product ) {
			$answers = $product->get_answers();
			if ( ! lty_check_is_array( $answers ) ) {
				return '-';
			}

			foreach ( $answers as $answer ) {
				$valid = isset( $answer['valid'] ) ? $answer['valid'] : '';
				if ( 'yes' == $valid ) {
					return isset( $answer['label'] ) ? $answer['label'] : '-';
				}
			}

			return '-';
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
			 * This hook is used to alter the lottery ticket attachment.
			 *
			 * @since 9.5.0
			 * @param array $email_attachments
			 * @param object $this
			 */
			return apply_filters( 'lty_lottery_ticket_confirmation_email_attachments', $email_attachments, $this );
		}

		/**
		 * Get the settings array.
		 * */
		public function get_settings_array() {
			$section_fields = array();

			// Admin Lottery ticket confirmed Email Section Start.
			$section_fields[] = array(
				'type'  => 'title',
				'title' => __( 'Giveaway Ticket Confirmed', 'lottery-for-woocommerce' ),
				'id'    => 'lty_lottery_ticket_confirmed_email_options',
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
				'title'    => __( 'Recipient(s)', 'lottery-for-woocommerce' ),
				'type'     => 'textarea',
				'default'  => $this->get_from_address(),
				'id'       => $this->get_option_key( 'recipients' ),
				/* translators: %s: From address */
				'desc'     => sprintf( __( 'Enter recipients (comma separated) for this email. Defaults to %s.', 'lottery-for-woocommerce' ), esc_attr( $this->get_from_address() ) ),
				'desc_tip' => true,
				'value'    => $this->get_admin_emails(),
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
				'id'   => 'lty_lottery_ticket_confirmed_email_options',
			);
			// Admin Lottery ticket confirmed Email Section End.

			return $section_fields;
		}

		/**
		 * Render email shortcode information.
		 * */
		public function render_email_shortcode_information() {
			$shortcodes_info = array(
				'{user_name}'            => array(
					'description' => __( 'Displays the giveaway user name', 'lottery-for-woocommerce' ),
				),
				'{product_name}'         => array(
					'description' => __( 'Displays the giveaway product name', 'lottery-for-woocommerce' ),
				),
				'[quantity]'             => array(
					'description' => __( 'Displays the giveaway product quantity', 'lottery-for-woocommerce' ),
				),
				'{Ticket_number}'        => array(
					'description' => __( 'Displays the giveaway ticket number', 'lottery-for-woocommerce' ),
				),
				'{order_id}'             => array(
					'description' => __( 'Displays the order id without link', 'lottery-for-woocommerce' ),
				),
				'{order_number}'         => array(
					'description' => __( 'Displays the order number with link', 'lottery-for-woocommerce' ),
				),
				'{lottery_winning_item}' => array(
					'description' => __( 'Displays the giveaway winning item', 'lottery-for-woocommerce' ),
				),
				'{selected_answer}'      => array(
					'description' => __( 'Displays the giveaway selected answer', 'lottery-for-woocommerce' ),
				),
				'{correct_answer}'       => array(
					'description' => __( 'Displays the giveaway correct answer', 'lottery-for-woocommerce' ),
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
			);

			include_once LTY_ABSPATH . 'inc/admin/menu/views/html-email-shortcodes-info.php';
		}
	}

}
