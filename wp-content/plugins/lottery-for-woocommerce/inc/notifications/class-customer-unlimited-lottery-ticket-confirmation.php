<?php
/**
 * Customer - Unlimited scheduled lottery ticket confirmation.
 *
 * @since 11.7.0
 * */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

if ( ! class_exists( 'LTY_Customer_Unlimited_Lottery_Ticket_Confirmation_Notification' ) ) {

	/**
	 * Class.
	 *
	 * @since 11.7.0
	 * */
	class LTY_Customer_Unlimited_Lottery_Ticket_Confirmation_Notification extends LTY_Notifications {

		/**
		 * Ticket IDs.
		 *
		 * @since 11.7.0
		 * @var array
		 * */
		private $ticket_ids = array();

		/**
		 * Order ID.
		 *
		 * @since 11.7.0
		 * @var string|int
		 * */
		private $order_id;

		/**
		 * Can display pdf attachment fields?
		 *
		 * @since 11.7.0
		 * @var bool
		 */
		public $pdf_attachment = true;

		/**
		 * Class constructor.
		 *
		 * @since 11.7.0
		 * */
		public function __construct() {
			$this->id          = 'customer_unlimited_scheduled_lottery_ticket_confirmation';
			$this->type        = 'customer';
			$this->title       = __( 'Customer - Unlimited Scheduled Giveaway Participation Confirmation', 'lottery-for-woocommerce' );
			$this->description = __( 'Send email to the giveaway participants about the giveaway tickets confirmation.', 'lottery-for-woocommerce' );

			// Triggers for this email.
			add_action( sanitize_key( $this->plugin_slug . '_lottery_ticket_confirmed' ), array( $this, 'trigger' ), 10, 3 );
			// Render email shortcode information.
			add_action( 'woocommerce_admin_field_lty_display_email_shortcode_' . sanitize_title( $this->id ), array( $this, 'render_email_shortcode_information' ) );

			parent::__construct();
		}

		/**
		 * Default subject.
		 *
		 * @since 11.7.0
		 * */
		public function get_default_subject() {
			return '{site_name} - Lottery Participation Confirmation';
		}

		/**
		 * Default message.
		 *
		 * @since 11.7.0
		 * */
		public function get_default_message() {
			return 'Hi {user_name},

			Thanks for participating in {product_name} Lottery.  Please find the details of your ticket below. The winners will be announced after the lottery is ended.
			{lottery_details}
			Thanks.';
		}

		/**
		 * Trigger the sending of this email.
		 *
		 * @since 11.7.0
		 * @param int   $ticket_id Ticket ID.
		 * @param array $tickets_data Tickets data.
		 * @param int   $order_id Order ID.
		 * @return void
		 * */
		public function trigger( $ticket_id, $tickets_data, $order_id ) {
			if ( ! $this->is_enabled() || ! lty_check_is_array( $tickets_data ) ) {
				return;
			}

			$ticket = lty_get_lottery_ticket( $ticket_id );
			if ( ! $ticket->exists() ) {
				return;
			}

			$this->ticket_ids = $tickets_data;
			$this->order_id   = $order_id;
			foreach ( $tickets_data as $product_id => $ticket_numbers ) {
				$product = wc_get_product( $product_id );
				if ( ! lty_is_lottery_product( $product ) || ! $product->is_unlimited_scheduled_lottery() ) {
					continue;
				}

				$this->recipient                                     = $ticket->get_user_email();
				$this->placeholders['{user_name}']                   = $ticket->get_user_name();
				$this->placeholders['{first_name}']                  = is_object( $ticket->get_user() ) ? $ticket->get_user()->first_name : '';
				$this->placeholders['{last_name}']                   = is_object( $ticket->get_user() ) ? $ticket->get_user()->last_name : '';
				$this->placeholders['{site_name}']                   = $this->get_blogname();
				$this->placeholders['{product_name}']                = sprintf( '<a href="%s">%s</a>', esc_url( $product->get_permalink() ), esc_html( $product->get_title() ) );
				$this->placeholders['{lottery_end_date}']            = esc_html( LTY_Date_Time::get_wp_format_datetime_from_gmt( $product->get_lty_end_date_gmt() ) );
				$this->placeholders['{lottery_details}']             = $this->display_lottery_details( array_values( $ticket_numbers ), $order_id );
				$this->placeholders['{lottery_winning_item}']        = lty_get_lottery_gift_products( false, $product, true );
				$this->placeholders['{selected_answer}']             = $this->get_selected_answer( array_keys( $ticket_numbers ) );
				$this->placeholders['{correct_answer}']              = $this->get_correct_answer( $product );
				$this->placeholders['{user_billing_details}']        = $this->render_user_billing_details( $ticket->get_order() );
				$this->placeholders['{tickets_pdf_download_button}'] = $this->render_ticket_pdf_download_button( $product_id );

				if ( $this->get_recipient() ) {
					$this->send_email( $this->get_recipient(), $this->get_subject(), $this->get_formatted_message(), $this->get_headers(), $this->get_attachments() );
				}
			}
		}

		/**
		 * Get selected answer.
		 *
		 * @since 11.7.0
		 * @param array $ticket_ids Ticket IDs.
		 * @return string
		 * */
		public function get_selected_answer( $ticket_ids ) {
			$ticket_id = isset( $ticket_ids[0] ) ? $ticket_ids[0] : '';
			if ( '' === $ticket_id ) {
				return '-';
			}

			$ticket = lty_get_lottery_ticket( $ticket_id );

			return $ticket->exists() && '' !== $ticket->get_answer() ? $ticket->get_answer() : '-';
		}

		/**
		 * Get correct answer.
		 *
		 * @since 11.7.0
		 * @param object $product Product object.
		 * @return string
		 * */
		public function get_correct_answer( $product ) {
			$answers = $product->get_answers();
			if ( ! lty_check_is_array( $answers ) ) {
				return '-';
			}

			foreach ( $answers as $answer ) {
				$valid = isset( $answer['valid'] ) ? $answer['valid'] : '';
				if ( 'yes' === $valid ) {
					return isset( $answer['label'] ) ? $answer['label'] : '-';
				}
			}

			return '-';
		}

		/**
		 * Display lottery details.
		 *
		 * @since 11.7.0
		 * @param array $ticket_numbers Ticket numbers.
		 * @param int   $order_id Order ID.
		 * @return string
		 * */
		public function display_lottery_details( $ticket_numbers, $order_id ) {
			return lty_get_template_html(
				'email-shortcodes/customer-lottery-details.php',
				array(
					'ticket_numbers' => $ticket_numbers,
					'order_id'       => $order_id,
				)
			);
		}

		/**
		 * Render user billing details.
		 *
		 * @since 11.7.0
		 * @param object $order Order object.
		 * @return string
		 * */
		public function render_user_billing_details( $order ) {
			return lty_get_template_html( 'email-shortcodes/user-billing-details.php', array( 'order' => $order ) );
		}

		/**
		 * Render ticket(s) pdf download button details.
		 *
		 * @since 11.7.0
		 * @param int $product_id Product ID.
		 * @return string
		 * */
		public function render_ticket_pdf_download_button( $product_id ) {
			$button_url = esc_url(
				add_query_arg(
					array(
						'action'  => 'lty-download',
						'lty_key' => lty_encode(
							array(
								'lty_lottery_id' => $product_id,
								'lty_order_id'   => $this->order_id,
							),
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
		 * @since 11.7.0
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
			 * This hook is used to alter the customer lottery ticket confirmation email attachments.
			 *
			 * @since 11.7.0
			 * @param array $email_attachments
			 * @param object $this
			 */
			return apply_filters( 'lty_customer_lottery_ticket_confirmation_email_attachments', $email_attachments, $this );
		}

		/**
		 * Get the settings array.
		 *
		 * @since 11.7.0
		 * @return array
		 * */
		public function get_settings_array() {
			$section_fields = array();

			// Customer Lottery ticket confirmed Email Section Start.
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
			// Customer Lottery ticket confirmed Email Section End.

			return $section_fields;
		}

		/**
		 * Render email shortcode information.
		 *
		 * @since 11.7.0
		 * @return void
		 * */
		public function render_email_shortcode_information() {
			$shortcodes_info = array(
				'{logo}'                        => array(
					'description' => __( 'Displays the logo', 'lottery-for-woocommerce' ),
				),
				'{site_name}'                   => array(
					'description' => __( 'Displays the site name', 'lottery-for-woocommerce' ),
				),
				'{user_name}'                   => array(
					'description' => __( 'Displays the giveaway user name', 'lottery-for-woocommerce' ),
				),
				'{product_name}'                => array(
					'description' => __( 'Displays the giveaway product name', 'lottery-for-woocommerce' ),
				),
				'{first_name}'                  => array(
					'description' => __( 'Displays the giveaway first name', 'lottery-for-woocommerce' ),
				),
				'{last_name}'                   => array(
					'description' => __( 'Displays the giveaway last name', 'lottery-for-woocommerce' ),
				),
				'{lottery_details}'             => array(
					'description' => __( 'Displays the giveaway details', 'lottery-for-woocommerce' ),
				),
				'{lottery_winning_item}'        => array(
					'description' => __( 'Displays the giveaway winning item', 'lottery-for-woocommerce' ),
				),
				'{selected_answer}'             => array(
					'description' => __( 'Displays the giveaway selected answer', 'lottery-for-woocommerce' ),
				),
				'{correct_answer}'              => array(
					'description' => __( 'Displays the giveaway correct answer', 'lottery-for-woocommerce' ),
				),
				'{user_billing_details}'        => array(
					'description' => __( 'Displays the user billing details', 'lottery-for-woocommerce' ),
				),
				'{tickets_pdf_download_button}' => array(
					'description' => __( 'Displays the giveaway ticket(s) PDF download button', 'lotttery-for-woocommerce' ),
				),
			);

			include_once LTY_ABSPATH . 'inc/admin/menu/views/html-email-shortcodes-info.php';
		}
	}

}
