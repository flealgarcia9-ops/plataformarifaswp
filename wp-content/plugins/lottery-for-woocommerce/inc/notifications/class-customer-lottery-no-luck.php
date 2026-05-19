<?php
/**
 * Customer - Lottery Luck
 *
 * @since 1.0.0
 * */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'LTY_Customer_Lottery_No_Luck_Notification' ) ) {

	/**
	 * Class.
	 *
	 * @since 1.0.0
	 * */
	class LTY_Customer_Lottery_No_Luck_Notification extends LTY_Notifications {

		/**
		 * Sent emails.
		 *
		 * @since 1.0.0
		 * */
		private $sent_emails = array();

		/**
		 * Class Constructor.
		 * */
		public function __construct() {
			$this->id          = 'customer_no_luck';
			$this->type        = 'customer';
			$this->title       = __( 'Customer - Better Luck Next Time', 'lottery-for-woocommerce' );
			$this->description = __( 'Send email to users who have lost the giveaway', 'lottery-for-woocommerce' );

			// Triggers for this email.
			add_action( sanitize_key( $this->plugin_slug . '_lottery_product_after_finished' ), array( $this, 'trigger' ), 10, 1 );
			// Render email shortcode information.
			add_action( 'woocommerce_admin_field_lty_display_email_shortcode_' . sanitize_title( $this->id ), array( $this, 'render_email_shortcode_information' ) );

			parent::__construct();
		}

		/**
		 * Get default subject.
		 *
		 * @since 1.0.0
		 * @return string
		 * */
		public function get_default_subject() {
			return '{site_name} - You have Lost a Lottery';
		}

		/**
		 * Get default message.
		 *
		 * @since 1.0.0
		 * @return string
		 */
		public function get_default_message() {
			return 'Hi {user_name},
			
			We are sorry to announce that you have lost the {product_name} Lottery held between {lottery_start_date} - {lottery_end_date}. Better Luck Next Time.
			
			Thanks.';
		}

		/**
		 * Trigger the sending of this email.
		 *
		 * @since 1.0.0
		 * @param int $product_id Product ID.
		 * @return void
		 */
		public function trigger( $product_id, $force = false ) {
			if ( ! $this->is_enabled() && ! $force ) {
				return false;
			}

			$product = wc_get_product( $product_id );
			if ( ! $this->is_valid( $product ) ) {
				return;
			}

			$ticket_ids = $product->get_looser_ticket_ids();
			foreach ( $ticket_ids as $ticket_id ) {
				$ticket = lty_get_lottery_ticket( $ticket_id );

				if ( ! $ticket->exists() || in_array( $ticket->get_user_email(), $this->sent_emails ) ) {
					continue;
				}

				// Continue if the user already a winner.
				if ( $product->has_user_already_winner( $ticket ) ) {
					continue;
				}

				$this->recipient                            = $ticket->get_user_email();
				$this->placeholders['{user_name}']          = $ticket->get_user_name();
				$this->placeholders['{first_name}']         = is_object( $ticket->get_user() ) ? $ticket->get_user()->first_name : '';
				$this->placeholders['{last_name}']          = is_object( $ticket->get_user() ) ? $ticket->get_user()->last_name : '';
				$this->placeholders['{product_name}']       = $product->get_product_name( true );
				$this->placeholders['{site_name}']          = $this->get_blogname();
				$this->placeholders['{lottery_start_date}'] = esc_html( LTY_Date_Time::get_wp_format_datetime_from_gmt( $product->get_lty_start_date_gmt() ) );
				$this->placeholders['{lottery_end_date}']   = esc_html( LTY_Date_Time::get_wp_format_datetime_from_gmt( $product->get_lty_end_date_gmt() ) );
				$this->placeholders['{winner_details}']     = $this->display_winner_details( $product );

				if ( $this->get_recipient() ) {
					$this->send_email( $this->get_recipient(), $this->get_subject(), $this->get_formatted_message(), $this->get_headers(), $this->get_attachments() );

					$this->sent_emails[] = $ticket->get_user_email();
				}
			}
		}

		/**
		 * Is valid to sent email?.
		 *
		 * @since 1.0.0
		 * @param object $product Product object.
		 * @return bool
		 */
		public function is_valid( $product ) {
			if ( ! is_object( $product ) ) {
				return false;
			}

			return ( 'lty_lottery_finished' === $product->get_lty_lottery_status() );
		}

		/**
		 * Display winner details.
		 *
		 * @since 9.1.0
		 * @param object $product Product object.
		 * @return string|HTML
		 */
		public function display_winner_details( $product ) {
			if ( ! lty_check_is_array( $product->get_current_winner_ids() ) ) {
				return;
			}

			$args = array(
				'columns'         => array(
					'user_name'     => __( 'User Name', 'lottery-for-woocommerce' ),
					'ticket_number' => __( 'Ticket Number', 'lottery-for-woocommerce' ),
				),
				'lottery_winners' => $product->get_current_winner_ids(),
			);

			return lty_get_template_html( 'email-shortcodes/winner-details.php', $args );
		}

		/**
		 * Get the settings array.
		 *
		 * @since 1.0.0
		 * @return array
		 */
		public function get_settings_array() {
			$section_fields = array();

			// Customer No Luck Email Section Start.
			$section_fields[] = array(
				'type'  => 'title',
				'title' => __( 'Customer - Giveaway No Luck', 'lottery-for-woocommerce' ),
				'id'    => 'lty_lottery_winner_email_options',
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
				'id'   => 'lty_lottery_winner_email_options',
			);
			// Customer No luck Email Section End.

			return $section_fields;
		}

		/**
		 * Render email shortcode information.
		 *
		 * @since 1.0.0
		 * @return void
		 */
		public function render_email_shortcode_information() {
			$shortcodes_info = array(
				'{user_name}'          => array(
					'description' => __( 'Displays the giveaway user name', 'lottery-for-woocommerce' ),
				),
				'{product_name}'       => array(
					'description' => __( 'Displays the giveaway product name', 'lottery-for-woocommerce' ),
				),
				'{first_name}'         => array(
					'description' => __( 'Displays the giveaway first name', 'lottery-for-woocommerce' ),
				),
				'{last_name}'          => array(
					'description' => __( 'Displays the giveaway last name', 'lottery-for-woocommerce' ),
				),
				'{lottery_start_date}' => array(
					'description' => __( 'Displays the giveaway start date', 'lottery-for-woocommerce' ),
				),
				'{lottery_end_date}'   => array(
					'description' => __( 'Displays the giveaway end date', 'lottery-for-woocommerce' ),
				),
				'{site_name}'          => array(
					'description' => __( 'Displays the site name', 'lottery-for-woocommerce' ),
				),
				'{logo}'               => array(
					'description' => __( 'Displays the logo', 'lottery-for-woocommerce' ),
				),
				'{winner_details}'     => array(
					'description' => __( 'Displays the giveaway winner details', 'lottery-for-woocommerce' ),
				),
			);

			include_once LTY_ABSPATH . 'inc/admin/menu/views/html-email-shortcodes-info.php';
		}
	}

}
