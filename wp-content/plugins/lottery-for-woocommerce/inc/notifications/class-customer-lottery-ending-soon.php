<?php

/**
 * Customer - Giveaway ending soon.
 * 
 * @since 12.4.0
 * */
if (! defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}

if (! class_exists('LTY_Customer_Lottery_Ending_Soon_Notification' ) ) {

	/**
	 * Class.
	 * 
	 * @since 12.4.0
	 * */
	class LTY_Customer_Lottery_Ending_Soon_Notification extends LTY_Notifications {

		/**
		 * Sent emails.
		 * 
		 * @since 12.4.0
		 * @var array
		 * */
		private $sent_emails = array();

		/**
		 * Class constructor.
		 * 
		 * @since 12.4.0
		 * */
		public function __construct() {
			$this->id          = 'customer_lottery_ending_soon';
			$this->type        = 'customer';
			$this->title       = __('Customer - Giveaway Ending Soon', 'lottery-for-woocommerce');
			$this->description = __('Send an email notification to customers when the giveaway is ending soon.', 'lottery-for-woocommerce');

			//Triggers for this email.
			add_action( sanitize_key($this->plugin_slug . '_lottery_ending_soon_product'), array( $this, 'trigger' ), 10, 2) ;
			// Render email shortcode information.
			add_action('woocommerce_admin_field_lty_display_email_shortcode_' . sanitize_title($this->id), array( $this, 'render_email_shortcode_information' ));
						
			parent::__construct();
		}

		/**
		 * Default subject.
		 * 
		 * @since 12.4.0
		 * @return string
		 * */
		public function get_default_subject() {
			return '{site_name} - Lottery Ending Soon';
		}

		/**
		 * Default message.
		 * 
		 * @since 12.4.0
		 * @return string
		 * */
		public function get_default_message() {
			return 'Hi {user_name},

			The giveaway "{product_name}" is ending soon and will close on {lottery_end_date}.
			
			Hurry—grab your ticket now for a chance to win!
			
			{product_link}
			
			Thanks.' ;
		}

		/**
		 * Trigger the sending of this email.
		 * 
		 * @since 12.4.0
		 * @param object $product
		 * */
		public function trigger( $product_id, $product, $force = false ) {
			if (! $this->is_valid($product)) {
				return;
			}

			if ( 'yes' === $product->get_lty_ending_soon_user_email_sent() && ! $force ) {
				return;
			}
		
			$recipients = $this->get_recipient_user_ids( $product );
			if ( ! lty_check_is_array( $recipients ) ) {
				return;
			}

			foreach ( $recipients as $user_value ) {
				if ( is_numeric( $user_value ) ) {
					$user = get_userdata( $user_value );
					if ( ! is_object( $user ) ) {
						continue;
					}

					$this->recipient = $user->user_email;
					$this->placeholders['{user_name}'] = $user->display_name;

				} else {
					$this->recipient = $user_value;
					$this->placeholders['{user_name}'] =  __('Guest', 'lottery-for-woocommerce');
				}

				if ( isset( $this->sent_emails[ $this->recipient ] ) ) {
					continue;
				}

				$this->sent_emails[ $this->recipient ] = true;

				$this->placeholders['{product_name}'] = sprintf('<a href="%s">%s</a>', esc_url($product->get_permalink()), esc_html($product->get_title(), 'lottery-for-woocommerce'));
				$this->placeholders['{lottery_end_date}'] = esc_html( LTY_Date_Time::get_wp_format_datetime_from_gmt( $product->get_lty_end_date_gmt() ) );
				$this->placeholders['{product_link}'] = sprintf('<a href="%s">%s</a>', esc_url($product->get_permalink()), esc_html('Click here', 'lottery-for-woocommerce'));

				if ($this->get_recipient()) {
					$this->send_email($this->get_recipient(), $this->get_subject(), $this->get_formatted_message(), $this->get_headers(), $this->get_attachments());
				}
			}

			$product->update_post_meta( 'lty_ending_soon_user_email_sent', 'yes' );
		}

		/**
		 * Get recipient user IDs based on recipient type.
		 *
		 * @since 12.4.0
		 * @param object $product
		 * @return array
		 */
		protected function get_recipient_user_ids( $product ) {

			$user_ids = array();

			switch ( $this->get_option( 'recipients_type', '1' ) ) {

				// All registered users.
				case '1':
					$user_ids = get_users( array( 'fields' => 'ids' ) );
					break;

				// Participant users only.
				case '2':
					$user_ids = lty_get_lottery_participant_user_ids( $product->get_id() );
					break;

				// Non-participant users only.
				case '3':
					$exclude_ids  = array();
					foreach ( lty_get_lottery_participant_user_ids( $product->get_id() ) as $participant ) {
						if ( is_numeric( $participant ) ) {
							$exclude_ids[] = (int) $participant;
						}
					}

					$user_ids = get_users( array(
						'fields'  => 'ids',
						'exclude' => $exclude_ids,
					) );
					break;
			}

			return lty_check_is_array( $user_ids ) ? $user_ids : array();
		}

		/**
		 * Is valid to sent email?.
		 * 
		 * @since 12.4.0
		 * @param object $product
		 * @return bool
		 * */
		public function is_valid( $product ) {
			if (! $this->is_enabled()) {
				return false;
			}

			if (! is_object($product)) {
				return false;
			}

			if ('lty_lottery_started' !== $product->get_lty_lottery_status()) {
				return false;
			}

			return true;
		}

		/**
		 * Get the settings array.
		 * 
		 * @since 12.4.0
		 * @return array
		 * */
		public function get_settings_array() {
			$section_fields = array() ;

			// Customer lottery ending soon email section start.
			$section_fields[] = array(
				'type'  => 'title',
				'title' => __('Giveaway Ending Soon', 'lottery-for-woocommerce'),
				'id'    => 'lty_lottery_ending_soon_email_options',
			);
			$section_fields[] = array(
				'title'   => __('Enable', 'lottery-for-woocommerce'),
				'type'    => 'checkbox',
				'default' => 'no',
				'id'      => $this->get_option_key('enabled'),
			);
			$section_fields[] = array(
			'title'   => __( 'Recipients', 'lottery-for-woocommerce' ),
			'type'    => 'select',
			'default' => '1',
			'options' => array(
				'1' => __( 'All Users', 'lottery-for-woocommerce' ),
				'2' => __( 'Only Participant Users', 'lottery-for-woocommerce' ),
				'3' => __( 'Only Non Participant Users', 'lottery-for-woocommerce' ),
			),
			'id'      => $this->get_option_key( 'recipients_type' ),
				) ;
			$section_fields[] = array(
				'title'       => __( 'Set Last remaining Time to send ending soon email', 'lottery-for-woocommerce' ),
				'type'        => 'lty_custom_fields',
				'lty_field'   => 'relative_date_selector',
				'option_type' => '1',
				'default'     => array(
					'unit'   => 'hours',
					'number' => '1',
				),
				'id'          => $this->get_option_key( 'type' ),
			);
			$section_fields[] = array(
				'title'   => __('Subject', 'lottery-for-woocommerce'),
				'type'    => 'text',
				'default' => $this->get_default_subject(),
				'id'      => $this->get_option_key('subject'),
			);
			$section_fields[] = array(
				'title'     => __('Message', 'lottery-for-woocommerce'),
				'type'      => 'lty_custom_fields',
				'lty_field' => 'wpeditor',
				'default'   => $this->get_default_message(),
				'id'        => $this->get_option_key('message'),
			);
			$section_fields[] = array(
				'type'      => 'lty_display_email_shortcode_' . $this->id,
			);
			$section_fields[] = array(
				'type' => 'sectionend',
				'id'   => 'lty_lottery_ending_soon_email_options',
			);
			// Customer lottery ending soon email section end.

			return $section_fields;
		}
				
		/**
		 * Render email shortcode information.
		 * 
		 * @since 12.4.0
		 * */
		public function render_email_shortcode_information() {
			$shortcodes_info = array(
				'{user_name}' => array(
					'description' => __('Displays the giveaway user name', 'lottery-for-woocommerce'),
				),
				'{product_name}' => array(
					'description' => __('Displays the giveaway product name', 'lottery-for-woocommerce'),
				),
				'{first_name}' => array(
					'description' => __('Displays the giveaway first name', 'lottery-for-woocommerce'),
				),
				'{last_name}' => array(
					'description' => __('Displays the giveaway last name', 'lottery-for-woocommerce'),
				),
				'{site_name}' => array(
					'description' => __('Displays the site name', 'lottery-for-woocommerce'),
				),
				'{lottery_end_date}' => array(
					'description' => __( 'Displays the giveaway end date', 'lottery-for-woocommerce' ),
				),
				'{product_link}' => array(
					'description' => __('Displays Click here text for product link.', 'lottery-for-woocommerce'),
				),
				'{logo}' => array(
					'description' => __( 'Displays the logo' , 'lottery-for-woocommerce' ),
				),
			);
					
			include_once LTY_ABSPATH . 'inc/admin/menu/views/html-email-shortcodes-info.php';
		}
	}
}
