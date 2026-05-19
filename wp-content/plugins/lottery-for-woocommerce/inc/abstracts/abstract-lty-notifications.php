<?php

/**
 * Abstract Notifications Class.
 * */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'LTY_Notifications' ) ) {

	/**
	 * LTY_Notifications Class.
	 * */
	abstract class LTY_Notifications {

		/**
		 * ID.
		 *
		 * @var string
		 * */
		protected $id;

		/**
		 * Enabled.
		 *
		 * @since  8.7.0
		 * @var boolean
		 * */
		protected $enabled;

		/**
		 * Subject.
		 *
		 * @var string
		 * */
		protected $subject = '';

		/**
		 * Show in table.
		 *
		 * @var bool
		 * */
		protected $show_in_table = true;

		/**
		 * Message.
		 *
		 * @var string
		 * */
		protected $message = '';

		/**
		 * Title.
		 *
		 * @var string
		 * */
		protected $title;

		/**
		 * Description.
		 *
		 * @var string
		 * */
		protected $description;

		/**
		 * Type.
		 *
		 * @var string
		 * */
		protected $type;

		/**
		 * Place holders.
		 *
		 * @var array
		 * */
		protected $placeholders = array();

		/**
		 * Plugin slug.
		 *
		 * @var string
		 * */
		protected $plugin_slug = 'lty';

		/**
		 * Recipient.
		 *
		 * @since 9.4.0
		 * @var string
		 */
		protected $recipient;

		/**
		 * Can display pdf attachment?
		 *
		 * @since 9.5.0
		 * @var bool
		 */
		public $pdf_attachment = false;

		/**
		 * Class Constructor.
		 * */
		public function __construct() {
			$this->enabled = $this->get_enabled();

			if ( empty( $this->placeholders ) ) {
				$this->placeholders = array(
					'{site_name}' => $this->get_blogname(),
					'{logo}'      => $this->get_logo(),
				);
			}
		}

		/**
		 * Get id.
		 * */
		public function get_id() {
			return $this->id;
		}

		/**
		 * Get title.
		 * */
		public function get_title() {
			return $this->title;
		}

		/**
		 * Get show or hide in table.
		 * */
		public function get_in_table() {
			return $this->show_in_table;
		}

		/**
		 * Get description.
		 * */
		public function get_description() {
			return $this->description;
		}

		/**
		 * Get type.
		 * */
		public function get_type() {
			return $this->type;
		}

		/**
		 * Get Enabled.
		 * */
		public function get_enabled() {

			return $this->get_option( 'enabled', 'no' );
		}

		/**
		 * Is enabled?.
		 * */
		public function is_enabled() {

			return 'yes' === $this->enabled;
		}

		/**
		 * Default Subject.
		 * */
		public function get_default_subject() {

			return '';
		}

		/**
		 * Default Message.
		 * */
		public function get_default_message() {

			return '';
		}

		/**
		 * Get subject.
		 * */
		public function get_subject() {

			return $this->format_string( $this->get_option( 'subject', $this->get_default_subject() ) );
		}

		/**
		 * Get Message.
		 * */
		public function get_message() {
			$message = $this->format_string( $this->get_option( 'message', $this->get_default_message() ) );
			$message = wpautop( $message );
			$message = $this->rtl_support( $message );
			$message = $this->email_inline_style( $message );

			return $message;
		}

		/**
		 * Support RTL.
		 *
		 * @return string
		 */
		public function rtl_support( $message ) {
			$direction = ( is_rtl() ) ? 'rtl' : 'ltr';

			$formatted_msg  = '<div class="lty-notifications-wrapper" dir="' . $direction . '">';
			$formatted_msg .= $message;
			$formatted_msg .= '</div>';

			return $formatted_msg;
		}

		/**
		 * Email Inline Style.
		 * */
		public function email_inline_style( $content ) {
			if ( ! $this->custom_css() || ! $content ) {
				return $content;
			}

			$emogrifier_class = '\\Pelago\\Emogrifier';
			if ( ! class_exists( $emogrifier_class ) ) {
				include_once dirname( WC_PLUGIN_FILE ) . '/includes/libraries/class-emogrifier.php';
			}

			$emogrifier = new $emogrifier_class( $content, $this->custom_css() );

			return $emogrifier->emogrify();
		}

		/**
		 * Get formatted Message.
		 * */
		public function get_formatted_message() {

			if ( get_option( 'lty_settings_email_template_type', '2' ) == '2' ) {
				ob_start();
				wc_get_template( 'emails/email-header.php', array( 'email_heading' => $this->get_subject() ) );
				echo esc_textarea( $this->get_message() );
				wc_get_template( 'emails/email-footer.php' );
				$message = ob_get_clean();
			} else {
				$message = $this->get_message();
			}

			return htmlspecialchars_decode( $message, ENT_COMPAT );
		}

		/**
		 * Get email headers.
		 * */
		public function get_headers() {
			$header = 'Content-Type: ' . $this->get_content_type() . "\r\n";

			return $header;
		}

		/**
		 * Is support pdf attachment?
		 *
		 * @since 9.5.0
		 * @return bool
		 * */
		public function is_support_pdf_attachment() {
			return $this->pdf_attachment;
		}

		/**
		 * Get attachments.
		 * */
		public function get_attachments() {

			return array();
		}

		/**
		 * Is enabled pdf attachment?
		 *
		 * @since 9.5.0
		 * @return bool
		 * */
		public function is_enabled_pdf_attachment() {
			return 'yes' === $this->get_option( 'pdf_attachment', 'no' );
		}

		/**
		 * Get content type.
		 * */
		public function get_content_type() {

			return 'text/html';
		}

		/**
		 * Get Option.
		 * */
		public function get_option( $key, $value = false ) {

			return get_option( $this->get_option_key( $key ), $value );
		}

		/**
		 * Get field key.
		 * */
		public function get_option_key( $key ) {
			return sanitize_key( $this->plugin_slug . '_' . $this->id . '_' . $key );
		}

		/**
		 * Get WordPress blog name.
		 * */
		public function get_blogname() {
			return wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );
		}

		/**
		 * Get logo.
		 *
		 * @since 8.5.0
		 * @return string
		 * */
		protected function get_logo() {
			if ( ! get_option( 'lty_settings_email_logo' ) ) {
				return '';
			}

			$image_sizes = get_option( 'lty_settings_email_logo_size', array() );
			$sizes       = lty_parse_relative_image_size_option( $image_sizes );

			/* translators: %1$s: Image url %2$s: Image height %3$s: Image width */
			return sprintf(
				'<img src="%1$s" style="height: %2$spx; width: %3$spx;">',
				esc_url( wp_get_attachment_url( get_option( 'lty_settings_email_logo' ) ) ),
				esc_attr( $sizes['height'] ),
				esc_attr( $sizes['width'] )
			);
		}

		/**
		 * Get valid recipients.
		 * */
		public function get_recipient() {
			$recipients = array_map( 'trim', explode( ',', $this->recipient ) );
			$recipients = array_filter( $recipients, 'is_email' );

			return implode( ', ', $recipients );
		}

		/**
		 * Get admin emails.
		 * */
		public function get_admin_emails() {
			return '' == get_option( $this->get_option_key( 'recipients' ) ) ? $this->get_from_address() : get_option( $this->get_option_key( 'recipients' ) );
		}

		/**
		 * Format String.
		 * */
		public function format_string( $string ) {
			$find    = array_keys( $this->placeholders );
			$replace = array_values( $this->placeholders );

			$string = str_replace( $find, $replace, $string );

			return $string;
		}

		/**
		 * Custom CSS.
		 * */
		public function custom_css() {
			return '';
		}

		/**
		 * Send an email.
		 * */
		public function send_email( $to, $subject, $message, $headers = false, $attachments = array() ) {
			if ( ! $headers ) {
				$headers = $this->get_headers();
			}

			add_filter( 'wp_mail_from', array( $this, 'get_from_address' ), 12 );
			add_filter( 'wp_mail_from_name', array( $this, 'get_from_name' ), 12 );
			add_filter( 'wp_mail_content_type', array( $this, 'get_content_type' ), 12 );

			if ( get_option( 'lty_settings_email_template_type', '2' ) == '2' ) {
				$mailer = WC()->mailer();
				$return = $mailer->send( $to, $subject, $message, $headers, $attachments );
			} else {
				$return = wp_mail( $to, $subject, $message, $headers, $attachments );
			}

			remove_filter( 'wp_mail_from', array( $this, 'get_from_address' ) );
			remove_filter( 'wp_mail_from_name', array( $this, 'get_from_name' ) );
			remove_filter( 'wp_mail_content_type', array( $this, 'get_content_type' ) );

			return $return;
		}

		/**
		 * Get the from name.
		 * */
		public function get_from_name() {

			$from_name = get_option( 'lty_settings_email_from_name' ) != '' ? get_option( 'lty_settings_email_from_name' ) : get_option( 'blogname' );

			return wp_specialchars_decode( esc_html( $from_name ), ENT_QUOTES );
		}

		/**
		 * Get the from address.
		 * */
		public function get_from_address() {

			$from_address = get_option( 'lty_settings_email_from_address' ) != '' ? get_option( 'lty_settings_email_from_address' ) : get_option( 'new_admin_email' );

			return sanitize_email( $from_address );
		}

		/**
		 * Get the settings array.
		 * */
		public function get_settings_array() {
			return array();
		}

		/**
		 * Output the settings.
		 * */
		public function output() {

			WC_Admin_Settings::output_fields( $this->get_settings_array() );
		}

		/**
		 * Save the settings.
		 * */
		public function save() {

			WC_Admin_Settings::save_fields( $this->get_settings_array() );
		}

		/**
		 * Reset the settings.
		 * */
		public function reset() {

			LTY_Settings::reset_fields( $this->get_settings_array() );
		}
	}

}
