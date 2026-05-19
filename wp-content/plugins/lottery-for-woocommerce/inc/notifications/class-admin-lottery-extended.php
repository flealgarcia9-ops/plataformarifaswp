<?php

/**
 * Admin - Giveaway Extend
 * 
 * @since 8.2.0
 * */
if (! defined('ABSPATH')) {
	exit ; // Exit if accessed directly.
}

if (! class_exists('LTY_Admin_Lottery_Extended_Notification')) {

	/**
	 * Class.
	 * 
	 * @since 8.2.0
	 * */
	class LTY_Admin_Lottery_Extended_Notification extends LTY_Notifications {

		/**
		 * Class constructor.
		 * 
		 * @since 8.2.0
		 * */
		public function __construct() {

			$this->id          = 'admin_lottery_exteded';
			$this->type        = 'admin';
			$this->title       = __('Admin - Giveaway Extend', 'lottery-for-woocommerce');
			$this->description = __('Send email to admin when the giveaway is extended.', 'lottery-for-woocommerce');

			//Triggers for this email.
			add_action( sanitize_key($this->plugin_slug . '_lottery_after_extended'), array( $this, 'trigger' ), 10, 1);
			// Render email shortcode information.
			add_action('woocommerce_admin_field_lty_display_email_shortcode_' . sanitize_title($this->id), array( $this, 'render_email_shortcode_information' ));
			parent::__construct();
		}

		/**
		 * Default subject.
		 * 
		 * @since 8.2.0
		 * @return string
		 * */
		public function get_default_subject() {
			return '{site_name} - Lottery Extended';
		}

		/**
		 * Default message.
		 * 
		 * @since 8.2.0
		 * @return string
		 * */
		public function get_default_message() {
			return 'Hi {user_name},

			The lottery "{product_name}" has been extended till on {end_date}.
			
			Thanks.';
		}

		/**
		 * Trigger the sending of this email.
		 * 
		 * @since 8.2.0
		 * @param object $product
		 * @return void
		 * */
		public function trigger( $product ) {
			if (! $this->is_valid($product)) {
				return;
			}

			$this->recipient = $this->get_admin_emails();
			$this->placeholders['{user_name}'] = $this->get_from_name();
			$this->placeholders['{product_name}'] = sprintf( '<a href="%s">%s</a>', esc_url($product->get_permalink()), esc_html($product->get_title()));
			$this->placeholders['{end_date}'] = LTY_Date_Time::get_wp_format_datetime_from_gmt($product->get_lty_end_date());
			$this->placeholders['{extended_date}'] = LTY_Date_Time::get_wp_format_datetime_from_gmt($product->get_lty_start_date_gmt());

			if ($this->get_recipient()) {
				$this->send_email($this->get_recipient(), $this->get_subject(), $this->get_formatted_message(), $this->get_headers(), $this->get_attachments());
			}
		}

		/**
		 * Is valid to sent email?.
		 * 
		 * @since 8.2.0
		 * @param object $product
		 * @return bool
		 * */
		public function is_valid( $product ) {
			if ( ! $this->is_enabled() ) {
				return false;
			}

			if ( ! lty_is_lottery_product( $product ) || $product->is_unlimited_scheduled_lottery() ) {
				return false;
			}

			return true;
		}

		/**
		 * Get the settings array.
		 * 
		 * @since 8.2.0
		 * @return array
		 * */
		public function get_settings_array() {
			$section_fields = array();

			// Admin lottery extended email section start.
			$section_fields[] = array(
				'type'  => 'title',
				'title' => __('Giveaway Extended', 'lottery-for-woocommerce'),
				'id'    => 'lty_lottery_extended_email_options',
			);
			$section_fields[] = array(
				'title'   => __('Enable', 'lottery-for-woocommerce'),
				'type'    => 'checkbox',
				'default' => 'no',
				'id'      => $this->get_option_key('enabled'),
			);
			$section_fields[] = array(
				'title'    => __('Recipient(s)', 'lottery-for-woocommerce'),
				'type'     => 'textarea',
				'default'  => $this->get_from_address(),
				'id'       => $this->get_option_key('recipients'),
				/* translators: %s: From address */
				'desc'     => sprintf( __('Enter recipients (comma separated) for this email. Defaults to %s.', 'lottery-for-woocommerce'), esc_attr($this->get_from_address())),
				'desc_tip' => true,
				'value'    => $this->get_admin_emails(),
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
				'id'   => 'lty_lottery_extended_email_options',
			) ;
			// Admin lottery extended email section end.

			return $section_fields;
		}
				
		/**
		 * Render email shortcode information.
		 * 
		 * @since 8.2.0
		 * @return void
		 * */
		public function render_email_shortcode_information() {
			$shortcodes_info = array(
				'{user_name}' => array(
					'description' => __('Displays the giveaway user name', 'lottery-for-woocommerce'),
				),
				'{product_name}' => array(
					'description' => __('Displays the giveaway product name', 'lottery-for-woocommerce'),
				),
				'{lottery_relisted_date}' => array(
					'description' => __('Displays the giveaway relisted date', 'lottery-for-woocommerce'),
				),
				'{site_name}' => array(
					'description' => __('Displays the site name', 'lottery-for-woocommerce'),
				),
				'{extended_date}' => array(
					'description' => __('Displays the giveaway start date', 'lottery-for-woocommerce'),
				),
				'{logo}' => array(
					'description' => __('Displays the logo', 'lottery-for-woocommerce' ),
				),
			);
					
			include_once LTY_ABSPATH . 'inc/admin/menu/views/html-email-shortcodes-info.php'  ;
		}
	}
}
