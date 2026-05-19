<?php
/**
 * Customer - Lottery Winner
 *
 * @since 1.0.0
 * */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'LTY_Customer_Lottery_Winner_Notification' ) ) {

	/**
	 * Class.
	 * */
	class LTY_Customer_Lottery_Winner_Notification extends LTY_Notifications {

		/**
		 * Class Constructor.
		 * */
		public function __construct() {
			$this->id          = 'customer_winner';
			$this->type        = 'customer';
			$this->title       = __( 'Customer - Giveaway Winner', 'lottery-for-woocommerce' );
			$this->description = __( 'Send email to the giveaway winner.', 'lottery-for-woocommerce' );

			// Triggers for this email.
			add_action( sanitize_key( $this->plugin_slug . '_lottery_product_after_finished' ), array( $this, 'trigger' ), 10, 2 );
			// Render email shortcode information.
			add_action( 'woocommerce_admin_field_lty_display_email_shortcode_' . sanitize_title( $this->id ), array( $this, 'render_email_shortcode_information' ) );

			parent::__construct();
		}

		/**
		 * Default Subject.
		 * */
		public function get_default_subject() {
			return '{site_name} - Congratulations! You have won a Lottery';
		}

		/**
		 * Default Message.
		 * */
		public function get_default_message() {
			return 'Hi,
			
			Congratulations! You have won the {product_name} Lottery held between {lottery_start_date} - {lottery_end_date}.
			Thanks.';
		}

		/**
		 * Trigger the sending of this email.
		 *
		 * @since 1.0.0
		 * @param int   $product_id Product ID.
		 * @param array $winner_ids Winner IDs.
		 * @return void
		 */
		public function trigger( $product_id, $winner_ids = array(), $force = false ) {
			if ( ! $this->is_enabled() && ! $force ) {
				return false;
			}

			$product = wc_get_product( $product_id );
			if ( ! $this->is_valid( $product ) ) {
				return;
			}

			$winner_ids = lty_check_is_array( $winner_ids ) ? $winner_ids : $product->get_current_winner_ids();
			if ( ! lty_check_is_array( $winner_ids ) ) {
				return;
			}

			foreach ( $winner_ids as $winner_id ) {
				$winner = lty_get_lottery_winner( $winner_id );
				if ( ! $winner->exists() ) {
					continue;
				}

				$this->recipient                            = $winner->get_user_email();
				$this->placeholders['{user_name}']          = $winner->get_user_name();
				$this->placeholders['{first_name}']         = is_object( $winner->get_user() ) ? $winner->get_user()->first_name : '';
				$this->placeholders['{last_name}']          = is_object( $winner->get_user() ) ? $winner->get_user()->last_name : '';
				$this->placeholders['{product_name}']       = $winner->get_product_name( true );
				$this->placeholders['{site_name}']          = $this->get_blogname();
				$this->placeholders['{lottery_start_date}'] = esc_html( LTY_Date_Time::get_wp_format_datetime_from_gmt( $product->get_lty_start_date_gmt() ) );
				$this->placeholders['{lottery_end_date}']   = esc_html( LTY_Date_Time::get_wp_format_datetime_from_gmt( $product->get_lty_end_date_gmt() ) );
				$this->placeholders['{ticket_number}']      = $winner->get_lottery_ticket_number();
				$this->placeholders['{winning_item}']       = lty_get_winner_gift_products_title( array_unique( $winner->get_gift_products() ), $product );

				if ( $this->get_recipient() ) {
					$this->send_email( $this->get_recipient(), $this->get_subject(), $this->get_formatted_message(), $this->get_headers(), $this->get_attachments() );
				}
			}
		}

		/**
		 * Is valid to sent email?.
		 *
		 * @since 1.0.0
		 * @param object $product Product object.
		 * @return bool
		 * */
		public function is_valid( $product ) {
			return is_object( $product ) && 'lty_lottery_finished' === $product->get_lty_lottery_status();
		}

		/**
		 * Get the settings array.
		 * */
		public function get_settings_array() {
			$section_fields = array();

			// Customer Winner Email Section Start.
			$section_fields[] = array(
				'type'  => 'title',
				'title' => __( 'Customer - Giveaway Winner', 'lottery-for-woocommerce' ),
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
			// Customer Winner Email Section End.

			return $section_fields;
		}

		/**
		 * Render email shortcode information.
		 * */
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
				'{Ticket_number}'      => array(
					'description' => __( 'Displays the giveaway ticket number', 'lottery-for-woocommerce' ),
				),
				'{winning_item}'       => array(
					'description' => __( 'Displays the giveaway winning prize details', 'lottery-for-woocommerce' ),
				),
				'{logo}'               => array(
					'description' => __( 'Displays the logo', 'lottery-for-woocommerce' ),
				),
			);

			include_once LTY_ABSPATH . 'inc/admin/menu/views/html-email-shortcodes-info.php';
		}
	}

}
