<?php

/**
 * Admin - Giveaway Started
 * */
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}

if (!class_exists('LTY_Admin_Lottery_Started_Notification')) {

	/**
	 * Class LTY_Admin_Lottery_Started_Notification.
	 * */
	class LTY_Admin_Lottery_Started_Notification extends LTY_Notifications {

		/**
		 * Class Constructor.
		 * */
		public function __construct() {

			$this->id = 'admin_lottery_started';
			$this->type = 'admin';
			$this->title = __('Admin - Giveaway Started', 'lottery-for-woocommerce');
			$this->description = __('Send email to admin when the giveaway has started', 'lottery-for-woocommerce');

			// Triggers for this email.
			add_action(sanitize_key($this->plugin_slug . '_lottery_after_started'), array( $this, 'trigger' ), 10, 1);
			// Render email shortcode information.
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

			return 'Hi {user_name},
The {product_name} Lottery has started. The details of the Lottery are as follows
{lottery_details}
Thanks.';
		}

		/**
		 * Trigger the sending of this email.
		 * */
		public function trigger( $product_id ) {
			if (!$this->is_enabled()) {
				return;
			}

			$product = wc_get_product($product_id);
			if (!is_object($product)) {
				return;
			}

			if ($product->is_closed()) {
				return;
			}

			$this->recipient = $this->get_admin_emails();
			$this->placeholders['{user_name}'] = $this->get_from_name();
			$this->placeholders['{product_name}'] = sprintf('<a href="%s">%s</a>', esc_url($product->get_permalink()), esc_html($product->get_title()));
			$this->placeholders['{lottery_details}'] = $this->display_lottery_details($product);

			if ($this->get_recipient()) {
				$this->send_email($this->get_recipient(), $this->get_subject(), $this->get_formatted_message(), $this->get_headers(), $this->get_attachments());
			}
		}

		/**
		 * Get the settings array.
		 * */
		public function get_settings_array() {
			$section_fields = array();

			// Admin Lottery started Email Section Start.
			$section_fields[] = array(
				'type' => 'title',
				'title' => __('Giveaway Started', 'lottery-for-woocommerce'),
				'id' => 'lty_lottery_started_email_options',
			);
			$section_fields[] = array(
				'title' => __('Enable', 'lottery-for-woocommerce'),
				'type' => 'checkbox',
				'default' => 'no',
				'id' => $this->get_option_key('enabled'),
			);
			$section_fields[] = array(
				'title' => __('Recipient(s)', 'lottery-for-woocommerce'),
				'type' => 'textarea',
				'default' => $this->get_from_address(),
				'id' => $this->get_option_key('recipients'),
				/* translators: %s: From address */
				'desc' => sprintf(__('Enter recipients (comma separated) for this email. Defaults to %s.', 'lottery-for-woocommerce'), esc_attr($this->get_from_address())),
				'desc_tip' => true,
				'value' => $this->get_admin_emails(),
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
			//  Admin Lottery Started Email Section End.

			return $section_fields;
		}

		/**
		 * Display Lottery Details.
		 * */
		public function display_lottery_details( $product ) {
			$args = array( 'product' => $product );

			return lty_get_template_html('email-shortcodes/admin-lottery-details.php', $args);
		}

		/**
		 * Render email shortcode information.
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
