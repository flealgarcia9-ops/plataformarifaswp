<?php

/**
 * Customer - Lottery Selected Incorrect Answer Notification
 * */
if ( ! defined( 'ABSPATH' ) ) {
	exit ; // Exit if accessed directly.
}

if ( ! class_exists( 'LTY_Customer_Lottery_Selected_Incorrect_Answer_Notification' ) ) {

	/**
	 * Class LTY_Customer_Lottery_Selected_Incorrect_Answer_Notification.
	 * */
	class LTY_Customer_Lottery_Selected_Incorrect_Answer_Notification extends LTY_Notifications {

		/**
		 * Class Constructor.
		 * */
		public function __construct() {

			$this->id          = 'customer_lottery_selected_incorrect_answer' ;
			$this->type        = 'customer' ;
			$this->title       = __( 'Customer - Selected Incorrect Answer' , 'lottery-for-woocommerce' ) ;
			$this->description = __( 'Send email to the giveaway participant when they choose an incorrect answer' , 'lottery-for-woocommerce' ) ;

			//Triggers for this email.
			add_action( sanitize_key( $this->plugin_slug . '_lottery_incorrect_answer_in_order' ) , array( $this, 'trigger' ) , 10 , 2 ) ;
			// Render email shortcode information.
			add_action( 'woocommerce_admin_field_lty_display_email_shortcode_' . sanitize_title( $this->id ) , array( $this, 'render_email_shortcode_information' ) ) ;

			parent::__construct() ;
		}

		/**
		 * Default Subject.
		 * */
		public function get_default_subject() {

			return '{site_name} - Selected Incorrect answer for lottery purchase' ;
		}

		/**
		 * Default Message.
		 * */
		public function get_default_message() {

			return 'Hi {user_name},

			You have selected an incorrect answer{incorrect_answer} during the {product_name} lottery purchase.Hence, you are not eligible to receive the ticket number for this purchase #{order_number}.

			Better luck next time.

			Thanks';
		}

		/**
		 * Trigger the sending of this email.
		 * */
		public function trigger( $product, $ticket ) {

			if ( ! $this->is_enabled() ) {
				return ;
			}

			if ( ! lty_is_lottery_product( $product ) || ! is_object( $ticket ) ) {
				return ;
			}

			$order = wc_get_order( $ticket->get_order_id() ) ;
			if ( ! is_object( $order ) ) {
				return ;
			}

			// Return if the order is created manually.
			if ( 'admin' === $order->get_created_via() ) {
				return;
			}

			$this->recipient                            = $ticket->get_user_email() ;
			$this->placeholders[ '{user_name}' ]        = $ticket->get_user_name() ;
			$this->placeholders[ '{first_name}' ]       = $ticket->get_first_name() ;
			$this->placeholders[ '{last_name}' ]        = $ticket->get_last_name() ;
			$this->placeholders[ '{site_name}' ]        = $this->get_blogname() ;
			$this->placeholders[ '{product_name}' ]     = sprintf( '<a href="%s">%s</a>' , esc_url( $product->get_permalink() ) , esc_html( $product->get_title() ) ) ;
			$this->placeholders[ '{incorrect_answer}' ] = $ticket->get_answer() ;
			$this->placeholders[ '{order_number}' ] = sprintf('<a href="%s">%s</a>', wc_get_endpoint_url('view-order', $ticket->get_order_id(), get_permalink(wc_get_page_id('myaccount'))), esc_attr($ticket->get_order_id()));
			$this->placeholders[ '{order_id}' ] = esc_attr($ticket->get_order_id());

			if ( $this->get_recipient() ) {
				$this->send_email( $this->get_recipient() , $this->get_subject() , $this->get_formatted_message() , $this->get_headers() , $this->get_attachments() ) ;
			}
		}

		/**
		 * Get the settings array.
		 * */
		public function get_settings_array() {
			$section_fields = array() ;

			// Customer Selected Incorrect Answer Email Section Start.
			$section_fields[] = array(
				'type'  => 'title',
				'title' => __( 'Customer Selected Incorrect Answer' , 'lottery-for-woocommerce' ),
				'id'    => 'lty_lottery_customer_selected_incorrect_answer_email_options',
					) ;
			$section_fields[] = array(
				'title'   => __( 'Enable' , 'lottery-for-woocommerce' ),
				'type'    => 'checkbox',
				'default' => 'yes',
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
				'type' => 'lty_display_email_shortcode_' . $this->id,
					) ;
			$section_fields[] = array(
				'type' => 'sectionend',
				'id'   => 'lty_lottery_customer_selected_incorrect_answer_email_options',
					) ;
			// Customer Selected Incorrect Answer Email Section End.

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
				'{order_id}' => array(
					'description' => __( 'Displays the giveaway order id without link' , 'lottery-for-woocommerce' ),
				),
				'{order_number}' => array(
					'description' => __( 'Displays the order number with link' , 'lottery-for-woocommerce' ),
				),
				'{incorrect_answer}' => array(
					'description' => __( 'Displays the user incorrect answer' , 'lottery-for-woocommerce' ),
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
