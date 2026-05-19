<?php

/**
 * Customer - Giveaway Failed
 * */
if ( ! defined( 'ABSPATH' ) ) {
	exit ; // Exit if accessed directly.
}

if ( ! class_exists( 'LTY_Customer_Lottery_Failed_Notification' ) ) {

	/**
	 * Class LTY_Customer_Lottery_Failed_Notification.
	 * */
	class LTY_Customer_Lottery_Failed_Notification extends LTY_Notifications {

		/**
		 * Sent emails.
		 * 
		 * */
		private $sent_emails = array() ;

		/**
		 * Class Constructor.
		 * */
		public function __construct() {

			$this->id          = 'customer_lottery_failed' ;
			$this->type        = 'customer' ;
			$this->title       = __( 'Customer - Giveaway Failed' , 'lottery-for-woocommerce' ) ;
			$this->description = __( 'Send email to the users who participated in the giveaway.' , 'lottery-for-woocommerce' ) ;

			// Triggers for this email.
			add_action( sanitize_key( $this->plugin_slug . '_lottery_after_ended' ) , array( $this, 'trigger' ) , 11 , 1 ) ;
			// Render email shortcode information.
			add_action('woocommerce_admin_field_lty_display_email_shortcode_' . sanitize_title($this->id), array( $this, 'render_email_shortcode_information' ));

			parent::__construct() ;
		}

		/**
		 * Default Subject.
		 * */
		public function get_default_subject() {

			return '{site_name} - Lottery Failed' ;
		}

		/**
		 * Default Message.
		 * */
		public function get_default_message() {

			return 'Hi {user_name},
The {product_name} Lottery has failed due to {failure_reason}.
Thanks.' ;
		}

		/**
		 * Trigger the sending of this email.
		 * */
		public function trigger( $product_id, $force = false ) {
			if ( ! $this->is_enabled() && ! $force ) {
				return false;
			}

			$product = wc_get_product( $product_id ) ;
			if ( ! $this->is_valid( $product ) ) {
				return ;
			}

			$ticket_ids = $product->get_placed_ticket_ids() ;

			foreach ( $ticket_ids as $ticket_id ) {
				$ticket = lty_get_lottery_ticket( $ticket_id ) ;

				if ( ! $ticket->exists() ) {
					continue ;
				}

				if ( in_array( $ticket->get_user_email() , $this->sent_emails ) ) {
					continue ;
				}

				$this->recipient                          = $ticket->get_user_email() ;
				$this->placeholders[ '{user_name}' ]      = $ticket->get_user_name() ;
				$this->placeholders[ '{first_name}' ]     = is_object($ticket->get_user()) ? $ticket->get_user()->first_name:'';
				$this->placeholders[ '{last_name}' ]      = is_object($ticket->get_user()) ? $ticket->get_user()->last_name:'';
				$this->placeholders[ '{product_name}' ]   = sprintf( '<a href="%s">%s</a>' , esc_url( $product->get_permalink() ) , esc_html( $product->get_title() ) ) ;
				$this->placeholders[ '{failure_reason}' ] = lty_display_failed_reason( $product->get_lty_failed_reason() , false ) ;

				if ( $this->get_recipient() ) {
					$this->send_email( $this->get_recipient() , $this->get_subject() , $this->get_formatted_message() , $this->get_headers() , $this->get_attachments() ) ;

					$this->sent_emails[] = $ticket->get_user_email() ;
				}
			}
		}

		/**
		 * Is valid to sent email?.
		 * 
		 * @return bool
		 * */
		public function is_valid( $product ) {
			if ( ! is_object( $product ) ) {
				return false ;
			}

			if ( ! $product->is_closed() || ! $product->has_lottery_status( 'lty_lottery_failed' ) ) {
				return false ;
			}

			return true ;
		}

		/**
		 * Get the settings array.
		 * */
		public function get_settings_array() {
			$section_fields = array() ;

			// Customer Lottery failed Email Section Start.
			$section_fields[] = array(
				'type'  => 'title',
				'title' => __( 'Giveaway Failed' , 'lottery-for-woocommerce' ),
				'id'    => 'lty_lottery_failed_email_options',
					) ;
			$section_fields[] = array(
				'title'   => __( 'Enable' , 'lottery-for-woocommerce' ),
				'type'    => 'checkbox',
				'default' => 'no',
				'id'      => $this->get_option_key( 'enabled' ),
					) ;
			$section_fields[] = array(
				'title'   => __( 'Subject' , 'lottery-for-woocommerce' ),
				'type'    => 'text',
				'default' => $this->get_default_subject(),
				'id'      => $this->get_option_key( 'subject' ),
					) ;
			$section_fields[] = array(
				'title'     => __( 'Message' , 'lottery-for-woocommerce' ),
				'type'      => 'lty_custom_fields',
				'lty_field' => 'wpeditor',
				'default'   => $this->get_default_message(),
				'id'        => $this->get_option_key( 'message' ),
					) ;
			$section_fields[] = array(
				'type'      => 'lty_display_email_shortcode_' . $this->id,
					) ;
			$section_fields[] = array(
				'type' => 'sectionend',
				'id'   => 'lty_lottery_failed_email_options',
					) ;
			//  Customer Lottery failed Email Section End.

			return $section_fields ;
		}
				
		/**
		 * Render email shortcode information.
		 * */
		public function render_email_shortcode_information() {
			$shortcodes_info = array(
				'{user_name}' => array(
					'description' => __( 'Displays the giveaway user name' , 'lottery-for-woocommerce' ),
				),
				'{product_name}' => array(
					'description' => __( 'Displays the giveaway product name' , 'lottery-for-woocommerce' ),
				),
				'{first_name}' => array(
					'description' => __( 'Displays the giveaway first name' , 'lottery-for-woocommerce' ),
				),
				'{last_name}' => array(
					'description' => __( 'Displays the giveaway last name' , 'lottery-for-woocommerce' ),
				),
				'{failure_reason}' => array(
					'description' => __( 'Displays the giveaway failure reason' , 'lottery-for-woocommerce' ),
				),
				'{site_name}' => array(
					'description' => __( 'Displays the site name' , 'lottery-for-woocommerce' ),
				),
				'{logo}' => array(
					'description' => __( 'Displays the logo' , 'lottery-for-woocommerce' ),
				),
			);
					
			include_once LTY_ABSPATH . 'inc/admin/menu/views/html-email-shortcodes-info.php'  ;
		}
	}

}
