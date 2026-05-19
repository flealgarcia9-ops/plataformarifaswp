<?php

/**
 * Admin - Giveaway Winner
 * */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'LTY_Admin_Lottery_Winner_Notification' ) ) {

	/**
	 * Class LTY_Admin_Lottery_Winner_Notification.
	 * */
	class LTY_Admin_Lottery_Winner_Notification extends LTY_Notifications {

		/**
		 * Class Constructor.
		 * */
		public function __construct() {
			$this->id          = 'admin_winner';
			$this->type        = 'admin';
			$this->title       = __( 'Admin - Giveaway Winner', 'lottery-for-woocommerce' );
			$this->description = __( 'Send email to admin when the winners for the giveaway has been decided.', 'lottery-for-woocommerce' );

			// Triggers for this email.
			add_action( sanitize_key( $this->plugin_slug . '_lottery_product_after_finished' ), array( $this, 'trigger' ), 10, 1 );
			// Render email shortcode information.
			add_action( 'woocommerce_admin_field_lty_display_email_shortcode_' . sanitize_title( $this->id ), array( $this, 'render_email_shortcode_information' ) );

			parent::__construct();
		}

		/**
		 * Default Subject.
		 * */
		public function get_default_subject() {
			return '{site_name} - Lottery Winners';
		}

		/**
		 * Default Message.
		 * */
		public function get_default_message() {
			return 'Hi {user_name},

			The winner(s) for the {product_name} Lottery which ended on {lottery_end_date} are as follows,
			{winner_details}
			Thanks.';
		}

		/**
		 * Trigger the sending of this email.
		 * */
		public function trigger( $product_id ) {
			$product = wc_get_product( $product_id );
			if ( ! $this->is_valid( $product ) ) {
				return;
			}

			$this->recipient                          = $this->get_admin_emails();
			$this->placeholders['{user_name}']        = $this->get_from_name();
			$this->placeholders['{product_name}']     = sprintf( '<a href="%s">%s</a>', esc_url( $product->get_permalink() ), esc_html( $product->get_title() ) );
			$this->placeholders['{lottery_end_date}'] = esc_html( LTY_Date_Time::get_wp_format_datetime_from_gmt( $product->get_lty_end_date_gmt() ) );
			$this->placeholders['{winner_details}']   = $this->display_winner_details( $product_id, $product );

			if ( $this->get_recipient() ) {
				$this->send_email( $this->get_recipient(), $this->get_subject(), $this->get_formatted_message(), $this->get_headers(), $this->get_attachments() );
			}
		}

		/**
		 * Is valid to sent email?.
		 *
		 * @return bool
		 * */
		public function is_valid( $product ) {
			if ( ! $this->is_enabled() ) {
				return false;
			}

			if ( ! is_object( $product ) ) {
				return false;
			}

			if ( 'lty_lottery_finished' !== $product->get_lty_lottery_status() ) {
				return false;
			}

			return true;
		}

		/**
		 * Get the settings array.
		 * */
		public function get_settings_array() {
			$section_fields = array();

			// Admin Winner Email Section Start.
			$section_fields[] = array(
				'type'  => 'title',
				'title' => __( 'Admin - Giveaway Winner', 'lottery-for-woocommerce' ),
				'id'    => 'lty_lottery_winner_email_options',
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
				'id'   => 'lty_lottery_winner_email_options',
			);
			// Admin Winner Email Section End.

			return $section_fields;
		}

		/**
		 * Display Winner Details.
		 * */
		public function display_winner_details( $product_id, $product ) {
			$_columns = array(
				'user_name'     => __( 'User Name', 'lottery-for-woocommerce' ),
				'ticket_number' => __( 'Ticket Number', 'lottery-for-woocommerce' ),
				'answer'        => __( 'Chosen Answer', 'lottery-for-woocommerce' ),
				'order_id'      => __( 'Order ID', 'lottery-for-woocommerce' ),
				'gift_products' => __( 'Gift Products', 'lottery-for-woocommerce' ),
				'date'          => __( 'Date', 'lottery-for-woocommerce' ),
			);

			if ( ! $product->is_valid_question_answer() ) {
				unset( $_columns['answer'] );
			}

			$winner_args = array( 'product_id' => $product_id );
			if ( $product->is_unlimited_scheduled_lottery() ) {
				$winner_args['list_count'] = $product->get_current_relist_count();
			} else {
				$winner_args['start_date'] = $product->get_current_start_date_gmt();
			}

			$args = array(
				'winner_ids' => lty_get_lottery_winner_ids( $winner_args ),
				'_columns'   => $_columns,
				'product'    => $product,
			);

			return lty_get_template_html( 'email-shortcodes/admin-lottery-winner-details.php', $args );
		}

		/**
		 * Render email shortcode information.
		 * */
		public function render_email_shortcode_information() {
			$shortcodes_info = array(
				'{user_name}'        => array(
					'description' => __( 'Displays the giveaway user name', 'lottery-for-woocommerce' ),
				),
				'{product_name}'     => array(
					'description' => __( 'Displays the giveaway product name', 'lottery-for-woocommerce' ),
				),
				'{lottery_end_date}' => array(
					'description' => __( 'Displays the giveaway end date', 'lottery-for-woocommerce' ),
				),
				'{winner_details}'   => array(
					'description' => __( 'Displays the giveaway winner details', 'lottery-for-woocommerce' ),
				),
				'{site_name}'        => array(
					'description' => __( 'Displays the site name', 'lottery-for-woocommerce' ),
				),
				'{logo}'             => array(
					'description' => __( 'Displays the logo', 'lottery-for-woocommerce' ),
				),
			);

			include_once LTY_ABSPATH . 'inc/admin/menu/views/html-email-shortcodes-info.php';
		}
	}

}
