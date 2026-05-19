<?php
/**
 * Admin - Giveaway Ended
 *
 * @since 1.0.0
 * */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'LTY_Admin_Lottery_Ended_Notification' ) ) {

	/**
	 * Class LTY_Admin_Lottery_Ended_Notification.
	 * */
	class LTY_Admin_Lottery_Ended_Notification extends LTY_Notifications {

		/**
		 * Class Constructor.
		 * */
		public function __construct() {

			$this->id          = 'admin_lottery_ended';
			$this->type        = 'admin';
			$this->title       = __( 'Admin - Giveaway Ended', 'lottery-for-woocommerce' );
			$this->description = __( 'Send email to admin when the giveaway has ended', 'lottery-for-woocommerce' );

			// Triggers for this email.
			add_action( sanitize_key( $this->plugin_slug . '_lottery_after_ended' ), array( $this, 'trigger' ), 10, 1 );
			// Render email shortcode information.
			add_action( 'woocommerce_admin_field_lty_display_email_shortcode_' . sanitize_title( $this->id ), array( $this, 'render_email_shortcode_information' ) );

			parent::__construct();
		}

		/**
		 * Default Subject.
		 * */
		public function get_default_subject() {

			return '{site_name} - Lottery Ended';
		}

		/**
		 * Default Message.
		 * */
		public function get_default_message() {

			return 'Hi {user_name},

The {product_name} Lottery has ended on {lottery_end_date}.
Ended Reason: {end_reason}
Winner Selection Type: {winner_selection}
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
			$this->placeholders['{end_reason}']       = esc_html( $product->get_lottery_end_reason() );
			$this->placeholders['{winner_selection}'] = ( '1' == $product->get_lty_winner_selection_method() ) ? __( 'Automatic', 'lottery-for-woocommerce' ) : __( 'Manual', 'lottery-for-woocommerce' );

			if ( $this->get_recipient() ) {
				$this->send_email( $this->get_recipient(), $this->get_subject(), $this->get_formatted_message(), $this->get_headers(), $this->get_attachments() );
			}
		}

		/**
		 * Is valid to sent email?
		 *
		 * @since 1.0.0
		 * @param object $product .
		 * @return boolean
		 */
		public function is_valid( $product ) {
			if ( ! $this->is_enabled() ) {
				return false;
			}

			if ( ! is_object( $product ) ) {
				return false;
			}

			if ( ! $product->is_closed() ) {
				return false;
			}

			return true;
		}

		/**
		 * Get the settings array.
		 * */
		public function get_settings_array() {
			$section_fields = array();

			// Admin Lottery ended Email Section Start.
			$section_fields[] = array(
				'type'  => 'title',
				'title' => __( 'Giveaway Ended', 'lottery-for-woocommerce' ),
				'id'    => 'lty_lottery_ended_email_options',
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
				'id'   => 'lty_lottery_ended_email_options',
			);
			// Admin Lottery ended Email Section End.

			return $section_fields;
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
				'{end_reason}'       => array(
					'description' => __( 'Displays the giveaway end reason', 'lottery-for-woocommerce' ),
				),
				'{winner_selection}' => array(
					'description' => __( 'Displays the giveaway winner selection', 'lottery-for-woocommerce' ),
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
