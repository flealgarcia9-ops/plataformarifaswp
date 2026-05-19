<?php

/**
 * Notification Tab.
 * */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( class_exists( 'LTY_Notification_Tab' ) ) {
	return new LTY_Notification_Tab();
}

/**
 * LTY_Notification_Tab.
 * */
class LTY_Notification_Tab extends LTY_Settings_Page {

	/**
	 * Constructor.
	 * */
	public function __construct() {
		$this->id    = 'notifications';
		$this->label = __( 'Notifications', 'lottery-for-woocommerce' );

		// Render Email Table.
		add_action( 'woocommerce_admin_field_lty_email_notifications', array( $this, 'email_notification_setting' ) );
		// Output the Notification Settings.
		add_action( sanitize_key( $this->plugin_slug . '_after_' . $this->id . '_settings_fields' ), array( $this, 'output_notification_settings' ) );
		// Save the Notification Settings.
		add_action( sanitize_key( $this->plugin_slug . '_after_' . $this->id . '_settings_saved' ), array( $this, 'save_notification_settings' ) );
		// Reset the Notification Settings.
		add_action( sanitize_key( $this->plugin_slug . '_after_' . $this->id . '_settings_reset' ), array( $this, 'reset_notification_settings' ) );

		parent::__construct();
	}

	/**
	 * Get the sections.
	 *
	 * @since 10.5.0
	 * @return array
	 */
	public function get_sections() {
		$sections = array(
			'general'      => __( 'General', 'lottery-for-woocommerce' ),
			'settings'     => __( 'Settings', 'lottery-for-woocommerce' ),
			'localization' => __( 'Localization', 'lottery-for-woocommerce' ),
		);

		/**
		 * This hook is used to alter the current tab sections.
		 *
		 * @since 10.5.0
		 */
		return apply_filters( $this->plugin_slug . '_get_sections_' . $this->id, $sections );
	}

	/**
	 * Get general section array.
	 *
	 * @since 10.5.0
	 * @return array
	 * */
	public function general_section_array() {
		$section_fields = array();

		// General section start.
		$section_fields[] = array(
			'type'  => 'title',
			'title' => __( 'Email Customization Settings', 'lottery-for-woocommerce' ),
			'id'    => 'lty_notifications_options',
		);
		$section_fields[] = array(
			'type' => 'lty_email_notifications',
		);
		$section_fields[] = array(
			'type' => 'sectionend',
			'id'   => 'lty_notifications_options',
		);
		// General section end.

		return $section_fields;
	}

	/**
	 * Get settings for notifications section array.
	 * */
	public function settings_section_array() {
		$section_fields = array();

		// Email settings section start.
		$section_fields[] = array(
			'type'  => 'title',
			'title' => __( 'Email Settings', 'lottery-for-woocommerce' ),
			'id'    => 'lty_email_options',
		);
		$section_fields[] = array(
			'title'   => __( 'Email Type', 'lottery-for-woocommerce' ),
			'id'      => $this->get_option_key( 'email_template_type' ),
			'type'    => 'select',
			'default' => '1',
			'options' => array(
				'1' => __( 'HTML', 'lottery-for-woocommerce' ),
				'2' => __( 'WooCommerce Template', 'lottery-for-woocommerce' ),
			),
		);
		$section_fields[] = array(
			'title'   => __( 'From Name', 'lottery-for-woocommerce' ),
			'id'      => $this->get_option_key( 'email_from_name' ),
			'type'    => 'text',
			'default' => get_option( 'woocommerce_email_from_name' ),
		);
		$section_fields[] = array(
			'title'   => __( 'From Address', 'lottery-for-woocommerce' ),
			'id'      => $this->get_option_key( 'email_from_address' ),
			'type'    => 'text',
			'default' => get_option( 'woocommerce_email_from_address' ),
		);
		$section_fields[] = array(
			'title'                     => __( 'Logo', 'lottery-for-woocommerce' ),
			'type'                      => 'lty_custom_fields',
			'lty_field'                 => 'image_upload',
			'id'                        => $this->get_option_key( 'email_logo' ),
			'default'                   => '',
			'add-image-button-label'    => __( 'Choose Image', 'lottery-for-woocommerce' ),
			'remove-image-button-label' => __( 'Remove', 'lottery-for-woocommerce' ),
		);
		$section_fields[] = array(
			'title'             => __( 'Width x Height (pixels)', 'lottery-for-woocommerce' ),
			'type'              => 'lty_custom_fields',
			'lty_field'         => 'image_size',
			'id'                => $this->get_option_key( 'email_logo_size' ),
			'default'           => array(
				'height' => 90,
				'width'  => 90,
			),
			'custom_attributes' => array(
				'size' => '5',
				'min'  => 1,
			),
		);
		$section_fields[] = array(
			'type' => 'sectionend',
			'id'   => 'lty_email_options',
		);
		// Email settings section end.

		return $section_fields;
	}

	/**
	 * Get localization section array.
	 *
	 * @since 10.5.0
	 * @return array
	 * */
	public function localization_section_array() {
		$section_fields = array();

		// Localization section start.
		$section_fields[] = array(
			'type'  => 'title',
			'title' => __( 'Email Localization', 'lottery-for-woocommerce' ),
			'id'    => 'lty_localization_options',
		);
		$section_fields[] = array(
			'title'    => __( 'Ticket(s) PDF Download Button(Shortcode) Label', 'lottery-for-woocommerce' ),
			'id'       => $this->get_option_key( 'email_tickets_pdf_download_button_label' ),
			'type'     => 'textarea',
			'default'  => __( 'Download Giveaway Ticket(s) PDF', 'lottery-for-woocommerce' ),
			'desc'     => __( 'The button label will support only for <b>Customer - Giveaway Participation Confirmation</b> and <b>Customer - Giveaway Participation Confirmation for recent order</b> email notifications.', 'lottery-for-woocommerce' ),
			'desc_tip' => true,
		);
		$section_fields[] = array(
			'type' => 'sectionend',
			'id'   => 'lty_localization_options',
		);
		// Localization section end.

		return $section_fields;
	}

	/**
	 * Output the notifications settings.
	 * */
	public function output_notification_settings() {
		global $current_section;

		if ( ! $current_section ) {
			return;
		}

		$notification = LTY_Notification_Instances::get_notification_by_id( $current_section );
		if ( ! $notification ) {
			return;
		}

		$notification->output();
	}

	/**
	 * Save the notification settings.
	 */
	public function save_notification_settings() {
		global $current_section;

		switch ( $current_section ) {
			case 'general':
				$notifications = LTY()->notifications();
				foreach ( $notifications as $notification ) {
					// Enable/ Disable the Notifications.
					$value = ( isset( $_REQUEST[ $notification->get_option_key( 'enabled' ) ] ) ) ? 'yes' : 'no';
					update_option( $notification->get_option_key( 'enabled' ), $value );

					// Enable/disable the pdf attachment.
					if ( $notification->is_support_pdf_attachment() ) {
						$pdf_attachment = ( isset( $_REQUEST[ $notification->get_option_key( 'pdf_attachment' ) ] ) ) ? 'yes' : 'no';
						update_option( $notification->get_option_key( 'pdf_attachment' ), $pdf_attachment );
					}
				}

				LTY_Notification_Instances::reset();
				break;

			default:
				$notification = LTY_Notification_Instances::get_notification_by_id( $current_section );
				if ( ! $notification ) {
					return;
				}

				$notification->save();
				break;
		}
	}

	/**
	 * Reset the notifications settings.
	 */
	public function reset_notification_settings() {
		global $current_section;

		if ( 'notifications' !== $current_section ) {
			$notification = LTY_Notification_Instances::get_notification_by_id( $current_section );

			if ( ! $notification ) {
				return;
			}
			$notification->reset();
		} else {
			$notifications = LTY()->notifications();
			foreach ( $notifications as $notification ) {
				// Disable Emails if notification is enabled.
				if ( $notification->is_enabled() ) {
					update_option( $notification->get_option_key( 'enabled' ), '' );
				}

				// Disable pdf attachments.
				if ( $notification->is_support_pdf_attachment() ) {
					update_option( $notification->get_option_key( 'pdf_attachment' ), '' );
				}
			}

			LTY_Notification_Instances::reset();
		}
	}

	/**
	 * Email notification settings.
	 * */
	public static function email_notification_setting() {

		include_once LTY_ABSPATH . 'inc/admin/menu/views/html-email-notifications.php';
	}
}

return new LTY_Notification_Tab();
