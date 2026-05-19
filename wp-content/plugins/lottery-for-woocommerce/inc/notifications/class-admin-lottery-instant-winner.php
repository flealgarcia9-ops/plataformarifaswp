<?php
/**
 * Admin - Lottery Instant Winner
 *
 * @since 8.0.0
 * */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'LTY_Admin_Lottery_Instant_Winner_Notification' ) ) {

	/**
	 * Class.
	 *
	 * @sinc 8.0.0
	 * */
	class LTY_Admin_Lottery_Instant_Winner_Notification extends LTY_Notifications {

		/**
		 * Class Constructor.
		 * */
		public function __construct() {

			$this->id          = 'admin_instant_winner';
			$this->type        = 'admin';
			$this->title       = __( 'Admin - Giveaway Instant Winner', 'lottery-for-woocommerce' );
			$this->description = __( 'Send email to admin when instant winner has assigned in the giveaway.', 'lottery-for-woocommerce' );

			// Triggers for this email.
			add_action( sanitize_key( $this->plugin_slug . '_lottery_ticket_confirmed' ), array( $this, 'trigger' ), 10, 4 );
			// Render email shortcode information.
			add_action( 'woocommerce_admin_field_lty_display_email_shortcode_' . sanitize_title( $this->id ), array( $this, 'render_email_shortcode_information' ) );

			parent::__construct();
		}

		/**
		 * Default Subject.
		 *
		 * @since 8.0.0
		 * @return string.
		 * */
		public function get_default_subject() {
			return '{site_name} - Lottery Instant Winner';
		}

		/**
		 * Default Message.
		 *
		 * @since 8.0.0
		 * @return string.
		 * */
		public function get_default_message() {
			return 'Hi {user_name},

			Instant Winner has been assigned for the recent lottery purchase(Order number:#{order_number}).<br/><br/>
			<b>Instant Winner details:-</b><br/>
			<b>Winner Name :</b> {winner_name}<br/>
			{instant_winner_details}<br/><br/>
			Thanks.';
		}

		/**
		 * Trigger the sending of this email.
		 *
		 * @since 8.0.0
		 * @return void.
		 * */
		public function trigger( $ticket_ids, $ticket_data, $order_id, $instant_winner_ticket_ids ) {
			if ( ! $this->is_enabled() ) {
				return;
			}

			if ( ! lty_check_is_array( $instant_winner_ticket_ids ) || ! $order_id ) {
				return;
			}

			$order = wc_get_order( $order_id );
			if ( ! is_object( $order ) ) {
				return;
			}

			$user_id                              = $order->get_user_id();
			$user                                 = get_user_by( 'ID', $user_id );
			$this->recipient                      = $this->get_admin_emails();
			$this->placeholders['{user_name}']    = $this->get_from_name();
			$this->placeholders['{order_number}'] = sprintf( '<a href="%s">%s</a>', get_edit_post_link( $order_id ), esc_attr( $order_id ) );
			$this->placeholders['{order_id}']     = esc_attr( $order_id );
			$this->placeholders['{winner_name}']  = is_object( $user ) ? $user->user_login : __( 'Guest', 'lottery-for-woocommerce' );
			$this->placeholders['{instant_winner_details}'] = $this->display_winner_details( $instant_winner_ticket_ids );
			$this->placeholders['{user_billing_details}']   = $this->render_user_billing_details( $order );

			if ( $this->get_recipient() ) {
				$this->send_email( $this->get_recipient(), $this->get_subject(), $this->get_formatted_message(), $this->get_headers(), $this->get_attachments() );
			}
		}

		/**
		 * Get the settings array.
		 *
		 * @since 8.0.0
		 * @return array.
		 * */
		public function get_settings_array() {
			$section_fields = array();

			// Admin Instant Winner Email Section Start.
			$section_fields[] = array(
				'type'  => 'title',
				'title' => __( 'Admin - Giveaway Instant Winner', 'lottery-for-woocommerce' ),
				'id'    => 'lty_lottery_instant_winner_email_options',
			);
			$section_fields[] = array(
				'title'   => __( 'Enable', 'lottery-for-woocommerce' ),
				'type'    => 'checkbox',
				'default' => 'no',
				'id'      => $this->get_option_key( 'enabled' ),
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
				'id'   => 'lty_lottery_instant_winner_email_options',
			);
			// Admin Instant Winner Email Section End.
			return $section_fields;
		}

		/**
		 * Display Instant Winner Details.
		 *
		 * @since 8.0.0
		 * @param array $ticket_ids Ticket IDs.
		 * @return string|HTML
		 * */
		public function display_winner_details( $ticket_ids ) {
			$_columns = array(
				'product_name'  => __( 'Product Name', 'lottery-for-woocommerce' ),
				'ticket_number' => __( 'Ticket Number', 'lottery-for-woocommerce' ),
				'prize_details' => __( 'Prize', 'lottery-for-woocommerce' ),
			);

			$args = array(
				'winner_ids' => $ticket_ids,
				'_columns'   => $_columns,
			);

			return lty_get_template_html( 'email-shortcodes/admin-lottery-instant-winner-details.php', $args );
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
		 * Render email shortcode information.
		 *
		 * @since 8.0.0
		 * @return void.
		 * */
		public function render_email_shortcode_information() {
			$shortcodes_info = array(
				'{site_name}'              => array(
					'description' => __( 'Displays the site name', 'lottery-for-woocommerce' ),
				),
				'{user_name}'              => array(
					'description' => __( 'Displays the giveaway user name', 'lottery-for-woocommerce' ),
				),
				'{order_id}'               => array(
					'description' => __( 'Displays the order id without link', 'lottery-for-woocommerce' ),
				),
				'{order_number}'           => array(
					'description' => __( 'Displays the order number with link', 'lottery-for-woocommerce' ),
				),
				'{product_name}'           => array(
					'description' => __( 'Displays the giveaway product name', 'lottery-for-woocommerce' ),
				),
				'{lottery_end_date}'       => array(
					'description' => __( 'Displays the giveaway end date', 'lottery-for-woocommerce' ),
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
	}
}
