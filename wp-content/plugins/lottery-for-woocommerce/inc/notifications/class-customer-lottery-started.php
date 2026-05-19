<?php

/**
 * Customer - Giveaway Started
 * 
 * @since 7.0
 * */
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}

if (!class_exists('LTY_Customer_Lottery_Started_Notification')) {

	/**
	 * Class.
	 * */
	class LTY_Customer_Lottery_Started_Notification extends LTY_Notifications {

		/**
		 * Class Constructor.
		 * */
		public function __construct() {

			$this->id = 'customer_lottery_started';
			$this->type = 'customer';
			$this->title = __('Customer - Giveaway Started', 'lottery-for-woocommerce');
			$this->description = __('Send email to all registered-user(s) in the site when the giveaway is started', 'lottery-for-woocommerce');

			// Triggers for this email.
			add_action(sanitize_key($this->plugin_slug . '_lottery_started_emails'), array( $this, 'trigger' ), 10, 3);
			// Render the email shortcode information.
			add_action('woocommerce_admin_field_lty_display_email_shortcode_' . sanitize_title($this->id), array( $this, 'render_email_shortcode_information' ));

			parent::__construct();
		}

		/**
		 * Default Subject.
		 * */
		public function get_default_subject() {
			return '{site_name} - Lottery Started';
		}

		/**
		 * Default Message.
		 * */
		public function get_default_message() {
			return 'Hi Username,

The Lottery {product_name} is started. Hurry up! to grab your tickets.
{lottery_details}
Thanks.';
		}

		/**
		 * Trigger the sending of this email.
		 * */
		public function trigger( $user_ids, $product_id, $force = false ) {
			if ( ! $this->is_enabled() && ! $force ) {
				return false;
			}

			$product = wc_get_product($product_id);
			if (!$this->is_valid($product) || !lty_check_is_array($user_ids)) {
				return;
			}

			foreach ($user_ids as $user_id) {
				$user = get_userdata($user_id);
				if (!is_object($user)) {
					continue;
				}

				$this->recipient = $user->user_email;
				$this->placeholders['{user_name}'] = $user->display_name;
				$this->placeholders['{product_name}'] = sprintf('<a href="%s">%s</a>', esc_url($product->get_permalink()), esc_html($product->get_title()));
				$this->placeholders['{lottery_details}'] = lty_get_template_html('email-shortcodes/admin-lottery-details.php', array( 'product' => $product ));

				if ($this->get_recipient()) {
					$this->send_email($this->get_recipient(), $this->get_subject(), $this->get_formatted_message(), $this->get_headers(), $this->get_attachments());
				}
			}
		}

		/**
		 * Is valid to sent email?.
		 * 
		 * @return bool
		 * */
		public function is_valid( $product ) {
			if (!is_object($product)) {
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
		 * @return array
		 * */
		public function get_settings_array() {
			$section_fields = array();

			// Customer lottery started email section start.
			$section_fields[] = array(
				'type' => 'title',
				'title' => __('Customer - Giveaway Started', 'lottery-for-woocommerce'),
				'id' => 'lty_lottery_started_email_options',
			);
			$section_fields[] = array(
				'title' => __('Enable', 'lottery-for-woocommerce'),
				'type' => 'checkbox',
				'default' => 'no',
				'id' => $this->get_option_key('enabled'),
			);
			$section_fields[] = array(
				'title' => __('Subject', 'lottery-for-woocommerce'),
				'type' => 'text',
				'default' => $this->get_default_subject(),
				'id' => $this->get_option_key('subject'),
			);
			$section_fields[] = array(
				'title' => __('Message', 'lottery-for-woocommerce'),
				'type' => 'lty_custom_fields',
				'lty_field' => 'wpeditor',
				'default' => $this->get_default_message(),
				'id' => $this->get_option_key('message'),
			);
			$section_fields[] = array(
				'type' => 'lty_display_email_shortcode_' . $this->id,
			);
			$section_fields[] = array(
				'type' => 'sectionend',
				'id' => 'lty_lottery_started_email_options',
			);
			// Customer lottery started email section end.

			return $section_fields;
		}

		/**
		 * Render the email short code information.
		 * */
		public function render_email_shortcode_information() {
			$shortcodes_info = array(
				'{user_name}' => array(
					'description' => __('Displays the giveaway user name', 'lottery-for-woocommerce'),
				),
				'{product_name}' => array(
					'description' => __('Displays the giveaway product name', 'lottery-for-woocommerce'),
				),
				'{lottery_details}' => array(
					'description' => __('Displays the giveaway details', 'lottery-for-woocommerce'),
				),
				'{site_name}' => array(
					'description' => __('Displays the site name', 'lottery-for-woocommerce'),
				),
				'{logo}' => array(
					'description' => __( 'Displays the logo' , 'lottery-for-woocommerce' ),
				),
			);

			include_once LTY_ABSPATH . 'inc/admin/menu/views/html-email-shortcodes-info.php' ;
		}
	}

}
