<?php

/**
 * Settings Page/Tab.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit ; // Exit if accessed directly
}

if ( ! class_exists( 'LTY_Settings_Page' ) ) {

	/**
	 * LTY_Settings_Page.
	 */
	abstract class LTY_Settings_Page {

		/**
		 * Setting page id.
		 * 
		 * @var string
		 * */
		protected $id = '' ;

		/**
		 * Setting page label.
		 * 
		 * @var string
		 * */
		protected $label = '' ;

		/**
		 * Setting page code.
		 * 
		 * @var string
		 * */
		protected $code = '' ;

		/**
		 * Show buttons.
		 * 
		 * @var bool
		 * */
		protected $show_button = true ;

		/**
		 * Plugin slug.
		 * 
		 * @var string
		 * */
		protected $plugin_slug = 'lty' ;

		/**
		 * Constructor.
		 */
		public function __construct() {
			add_filter( sanitize_key( $this->plugin_slug . '_settings_tabs_array' ), array( $this, 'add_settings_page' ), 20 ) ;
			add_action( sanitize_key( $this->plugin_slug . '_sections_' . $this->id ), array( $this, 'output_sections' ) ) ;
			add_action( sanitize_key( $this->plugin_slug . '_settings_' . $this->id ), array( $this, 'output' ), 10 ) ;
			add_action( sanitize_key( $this->plugin_slug . '_settings_' . $this->id ), array( $this, 'output_buttons' ), 20 ) ;
			add_action( sanitize_key( $this->plugin_slug . '_settings_' . $this->id ), array( $this, 'output_extra_fields' ), 30 ) ;
			add_action( sanitize_key( $this->plugin_slug . '_load_settings_' . $this->id ), array( $this, 'save' ), 10 ) ;
			add_action( sanitize_key( $this->plugin_slug . '_load_settings_' . $this->id ), array( $this, 'reset' ), 20 ) ;
		}

		/**
		 * Get settings page ID.
		 */
		public function get_id() {
			return $this->id ;
		}

		/**
		 * Get settings page label.
		 */
		public function get_label() {
			return $this->label ;
		}

		/**
		 * Get plugin slug.
		 */
		public function get_plugin_slug() {
			return $this->plugin_slug ;
		}

		/**
		 * Add this page to settings.
		 */
		public function add_settings_page( $pages ) {
			$pages[ $this->id ] = $this->label ;

			return $pages ;
		}

		/**
		 * Get settings array.
		 */
		public function get_settings( $current_section = '' ) {
			$settings = array() ;
			$function = $current_section . '_section_array' ;

			if ( method_exists( $this, $function ) ) {
				$settings = $this->$function() ;
			}

			/**
			 * This hook is used to alter the settings fields.
			 * 
			 * @since 1.0
			 */
			return apply_filters( sanitize_key( $this->plugin_slug . '_get_settings_' . $this->id ), $settings, $current_section ) ;
		}

		/**
		 * Get sections.
		 */
		public function get_sections() {
			/**
			 * This hook is used to alter the settings sections.
			 * 
			 * @since 1.0
			 */
			return apply_filters( sanitize_key( $this->plugin_slug . '_get_sections_' . $this->id ), array() ) ;
		}

		/**
		 * Output sections.
		 */
		public function output_sections() {
			global $current_section ;

			$sections = $this->get_sections() ;

			if ( ! lty_check_is_array( $sections ) || 1 === count( $sections ) ) {
				return ;
			}

			$section = '<ul class="subsubsub ' . $this->plugin_slug . '_sections ' . $this->plugin_slug . '_subtab">' ;

			foreach ( $sections as $id => $label ) {
				$section .= '<li>'
						. '<a href="' . esc_url(
								lty_get_settings_page_url(
										array(
											'tab'     => $this->id,
											'section' => sanitize_title( $id ),
										)
								)
						) . '" '
						. 'class="' . ( $current_section == $id ? 'current' : '' ) . '">' . esc_html( $label ) . '</a></li> | ' ;
			}

			$section = rtrim( $section, '| ' ) ;

			$section .= '</ul><br class="clear">' ;

			echo wp_kses_post( $section ) ;
		}

		/**
		 * Output the settings.
		 */
		public function output() {
			global $current_section, $current_sub_section ;

			$section = ( $current_sub_section ) ? $current_sub_section : $current_section ;

			$settings = $this->get_settings( $section ) ;

			WC_Admin_Settings::output_fields( $settings ) ;

			/**
			 * This hook is used to display the extra contents.
			 * 
			 * @since 1.0
			 */
			do_action( sanitize_key( $this->plugin_slug . '_after_' . $this->id . '_settings_fields' ) ) ;
		}

		/**
		 * Output the settings buttons.
		 * */
		public function output_buttons() {
			if ( ! $this->show_button ) {
				return ;
			}

			LTY_Settings::output_buttons() ;
		}

		/**
		 * Save settings.
		 * */
		public function save() {
			global $current_section, $current_sub_section ;

			$section = ( $current_sub_section ) ? $current_sub_section : $current_section ;

			if ( ! isset( $_POST[ 'save' ] ) || empty( $_POST[ 'save' ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.NoNonceVerification
				return ;
			}

			check_admin_referer( 'lty_save_settings', '_lty_nonce' ) ;

			$settings = $this->get_settings( $section ) ;

			WC_Admin_Settings::save_fields( $settings ) ;
			LTY_Settings::add_message( __( 'Your settings have been saved', 'lottery-for-woocommerce' ) ) ;

			// Flush permalinks and rewrite rules after save the settings.
			lty_set_lottery_queue_flush_rewrite_rules();

			/**
			 * This hook is used to do extra action after settings saved.
			 * 
			 * @since 1.0
			 */
			do_action( sanitize_key( $this->plugin_slug . '_after_' . $this->id . '_settings_saved' ) ) ;
		}

		/**
		 * Reset settings.
		 */
		public function reset() {
			global $current_section, $current_sub_section ;

			$section = ( $current_sub_section ) ? $current_sub_section : $current_section ;

			if ( ! isset( $_POST[ 'reset' ] ) || empty( $_POST[ 'reset' ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.NoNonceVerification
				return ;
			}

			check_admin_referer( 'lty_reset_settings', '_lty_nonce' ) ;

			$settings = $this->get_settings( $section ) ;
			LTY_Settings::reset_fields( $settings ) ;
			LTY_Settings::add_message( __( 'Your settings have been reset', 'lottery-for-woocommerce' ) ) ;

			// Flush permalinks and rewrite rules after reset the settings.
			lty_set_lottery_queue_flush_rewrite_rules();

			/**
			 * This hook is used to do extra action after settings reset.
			 * 
			 * @since 1.0
			 */
			do_action( sanitize_key( $this->plugin_slug . '_after_' . $this->id . '_settings_reset' ) ) ;
		}

		/**
		 * Output the extra fields
		 */
		public function output_extra_fields() {
		}

		/**
		 * Get option key
		 */
		public function get_option_key( $key ) {
			return sanitize_key( $this->plugin_slug . '_settings_' . $key ) ;
		}
	}

}
