<?php

/**
 * Customer - Lottery Relist
 * */
if ( ! defined( 'ABSPATH' ) ) {
	exit ; // Exit if accessed directly.
}

if ( ! class_exists( 'LTY_Customer_Lottery_Relisted_Notification' ) ) {

	/**
	 * Class LTY_Customer_Lottery_Relisted_Notification.
	 * */
	class LTY_Customer_Lottery_Relisted_Notification extends LTY_Notifications {

		/**
		 * Sent emails.
		 * 
		 * */
		private $sent_emails = array() ;

		/**
		 * Class Constructor.
		 * */
		public function __construct() {

			$this->id          = 'customer_lottery_relisted' ;
			$this->type        = 'customer' ;
			$this->title       = __( 'Customer - Giveaway Re-listed' , 'lottery-for-woocommerce' ) ;
			$this->description = __( 'Send email to customer when the giveaway has been re-listed.' , 'lottery-for-woocommerce' ) ;

			//Triggers for this email.
			add_action( sanitize_key( $this->plugin_slug . '_lottery_after_relisted' ) , array( $this, 'trigger' ) , 10 , 2 ) ;
			// Render email shortcode information.
			add_action('woocommerce_admin_field_lty_display_email_shortcode_' . sanitize_title($this->id), array( $this, 'render_email_shortcode_information' ));
						
			parent::__construct() ;
		}

		/**
		 * Default Subject.
		 * */
		public function get_default_subject() {

			return '{site_name} - Lottery Re-listed' ;
		}

		/**
		 * Default Message.
		 * */
		public function get_default_message() {

			return 'Hi {user_name},

The {product_name} Lottery has been re-listed on {lottery_relisted_date}. If you want to participate again please visit {product_name}.

Thanks.' ;
		}

		/**
		 * Trigger the sending of this email.
		 * */
		public function trigger( $product_id, $relists ) {
			$product = wc_get_product( $product_id ) ;

			if ( ! $this->is_valid( $product ) ) {
				return ;
			}

			$ticket_ids = $this->get_placed_ticket_ids( $relists , $product_id ) ;

			foreach ( $ticket_ids as $ticket_id ) {
				$ticket = lty_get_lottery_ticket( $ticket_id ) ;

				if ( ! $ticket->exists() ) {
					continue ;
				}

				if ( in_array( $ticket->get_user_email() , $this->sent_emails ) ) {
					continue ;
				}

				$this->recipient                                 = $ticket->get_user_email() ;
				$this->placeholders[ '{user_name}' ]             = $ticket->get_user_name() ;
				$this->placeholders[ '{first_name}' ]            = is_object($ticket->get_user()) ? $ticket->get_user()->first_name:'';
				$this->placeholders[ '{last_name}' ]             = is_object($ticket->get_user()) ? $ticket->get_user()->last_name:'';
				$this->placeholders[ '{product_name}' ]          = sprintf( '<a href="%s">%s</a>' , esc_url( $product->get_permalink() ) , esc_html( $product->get_title() ) ) ;
				$this->placeholders[ '{lottery_relisted_date}' ] = esc_html( LTY_Date_Time::get_wp_format_datetime_from_gmt( $product->get_lty_relisted_date_gmt() ) ) ;
				$this->placeholders[ '{site_name}' ]             = $this->get_blogname() ;

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
			if ( ! $this->is_enabled() ) {
				return false ;
			}

			if ( ! is_object( $product ) ) {
				return false ;
			}

			if ( 'yes' != $product->get_lty_relisted() ) {
				return false ;
			}

			return true ;
		}

		/**
		 * Get the current relist placed ticket ID's.
		 *
		 * @since 1.0.0
		 * @param array $relists Relist data.
		 * @param int   $product_id Product ID.
		 * @return array
		 * */
		private function get_placed_ticket_ids( $relists, $product_id ) {
			if ( ! lty_check_is_array( $relists ) ) {
				return array();
			}

			$current_list = end( $relists );
			$args         = array( 'product_id' => $product_id );
			if ( isset( $current_list['unlimited_scheduled_lottery'] ) && 'yes' === $current_list['unlimited_scheduled_lottery'] ) {
				$args['list_count'] = $current_list['list_count'];
			} else {
				$args['start_date'] = $current_list['start_date'];
				$args['end_date']   = $current_list['end_date'];
			}

			return lty_get_ticket_ids( $args );
		}

		/**
		 * Get the settings array.
		 * */
		public function get_settings_array() {
			$section_fields = array() ;

			// Customer Lottery relisted Email Section Start.
			$section_fields[] = array(
				'type'  => 'title',
				'title' => __( 'Giveaway Relisted' , 'lottery-for-woocommerce' ),
				'id'    => 'lty_lottery_relisted_email_options',
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
				'id'   => 'lty_lottery_relisted_email_options',
					) ;
			//  Customer Lottery relisted Email Section End.

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
				'{lottery_relisted_date}' => array(
					'description' => __( 'Displays the giveaway relisted date' , 'lottery-for-woocommerce' ),
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
