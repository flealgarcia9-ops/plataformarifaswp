<?php

/**
 * Customer - Giveaway Extended
 * 
 * @since 8.2.0
 * */
if (! defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}

if (! class_exists('LTY_Customer_Lottery_Extended_Notification' ) ) {

	/**
	 * Class.
	 * 
	 * @since 8.2.0
	 * */
	class LTY_Customer_Lottery_Extended_Notification extends LTY_Notifications {

		/**
		 * Sent emails.
		 * 
		 * @since 8.2.0
		 * @var array
		 * */
		private $sent_emails = array();

		/**
		 * Class constructor.
		 * 
		 * @since 8.2.0
		 * */
		public function __construct() {
			$this->id          = 'customer_lottery_extended';
			$this->type        = 'customer';
			$this->title       = __('Customer - Giveaway Extend', 'lottery-for-woocommerce');
			$this->description = __('Send email to customer when the giveaway is extended.', 'lottery-for-woocommerce');

			// Triggers for this email.
			add_action( sanitize_key($this->plugin_slug . '_lottery_after_extended'), array( $this, 'trigger' ), 10, 1) ;
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
			
			Hurry up to grab your ticket number now.
			
			{product_link}
			
			Thanks.' ;
		}

		/**
		 * Trigger the sending of this email.
		 * 
		 * @since 8.2.0
		 * @param object $product
		 * @return void
		 * */
		public function trigger( $product, $force = false ) {
			if ( ! $this->is_enabled() && ! $force ) {
				return false;
			}

			if ( ! is_object( $product ) ) {
				return false;
			}
		
			$ticket_ids = $this->get_placed_ticket_ids($product->get_id());
		
			foreach ($ticket_ids as $ticket_id) {
				$ticket = lty_get_lottery_ticket($ticket_id);
		
				if (! $ticket->exists()) {
					continue;
				}
		
				if (in_array($ticket->get_user_email(), $this->sent_emails)) {
					continue;
				}
		
				$this->recipient = $ticket->get_user_email();
				$this->placeholders['{user_name}'] = $ticket->get_user_name();
				$this->placeholders['{first_name}'] = is_object($ticket->get_user()) ? $ticket->get_user()->first_name : '';
				$this->placeholders['{last_name}'] = is_object($ticket->get_user()) ? $ticket->get_user()->last_name : '';
				$this->placeholders['{product_name}'] = sprintf('<a href="%s">%s</a>', esc_url($product->get_permalink()), esc_html($product->get_title(), 'lottery-for-woocommerce'));
				$this->placeholders['{end_date}'] = LTY_Date_Time::get_wp_format_datetime_from_gmt($product->get_lty_end_date());
				$this->placeholders['{extended_date}'] = LTY_Date_Time::get_wp_format_datetime_from_gmt($product->get_lty_start_date_gmt());
				$this->placeholders['{product_link}'] = sprintf('<a href="%s">%s</a>', esc_url($product->get_permalink()), esc_html('Click here', 'lottery-for-woocommerce'));

				if ($this->get_recipient()) {
					$this->send_email($this->get_recipient(), $this->get_subject(), $this->get_formatted_message($this->placeholders), $this->get_headers(), $this->get_attachments());
		
					$this->sent_emails[] = $ticket->get_user_email();
				}
			}
		}

		/**
		 * Get the placed lottery ticket ID's.
		 *
		 * @since 8.2.0
		 * @param int $product_id Product ID.
		 * @return array
		 * */
		private function get_placed_ticket_ids( $product_id ) {
			return lty_get_ticket_ids( array( 'product_id' => $product_id ) );
		}

		/**
		 * Get the settings array.
		 * 
		 * @since 8.2.0
		 * @return array
		 * */
		public function get_settings_array() {
			$section_fields = array() ;

			// Customer lottery extended email section start.
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
				'id'   => 'lty_lottery_relisted_email_options',
			);
			// Customer lottery extended email section end.

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
				'{first_name}' => array(
					'description' => __('Displays the giveaway first name', 'lottery-for-woocommerce'),
				),
				'{last_name}' => array(
					'description' => __('Displays the giveaway last name', 'lottery-for-woocommerce'),
				),
				'{site_name}' => array(
					'description' => __('Displays the site name', 'lottery-for-woocommerce'),
				),
				'{extended_date}' => array(
					'description' => __('Displays the giveaway start date', 'lottery-for-woocommerce'),
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
