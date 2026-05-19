<?php

/**
 * Initialize the plugin.
 * */
if ( ! defined( 'ABSPATH' ) ) {
	exit ; // Exit if accessed directly.
}

if ( ! class_exists( 'LTY_Install' ) ) {

	/**
	 * Class.
	 * */
	class LTY_Install {

		/**
		 *  Class initialization.
		 * */
		public static function init() {
			add_action( 'woocommerce_init', array( __CLASS__, 'check_version' ) ) ;
			add_filter( 'plugin_action_links_' . LTY_PLUGIN_SLUG, array( __CLASS__, 'settings_link' ) ) ;
		}

		/**
		 * Check current version of the plugin is updated when activating plugin, if not run updater.
		 */
		public static function check_version() {
			if ( version_compare( get_option( 'lty_current_version' ), LTY_VERSION, '>=' ) ) {
				return ;
			}

			self::install() ;
		}

		/**
		 * Install.
		 * */
		public static function install() {
			self::set_default_values(); // Default values.
			LTY_Pages::create_pages(); // Create pages.
			self::update_version(); // Update current version.

			// Flush permalinks and rewrite rules.
			lty_set_lottery_queue_flush_rewrite_rules();
		}

		/**
		 * Update current version.
		 * */
		private static function update_version() {
			update_option( 'lty_current_version', LTY_VERSION ) ;
		}

		/**
		 *  Settings link.
		 * */
		public static function settings_link( $links ) {
			$setting_page_link = '<a href="' . lty_get_settings_page_url() . '">' . __( 'Settings', 'lottery-for-woocommerce' ) . '</a>' ;

			array_unshift( $links, $setting_page_link ) ;

			return $links ;
		}

		/**
		 *  Set settings default values.
		 * */
		public static function set_default_values() {
			if ( ! class_exists( 'LTY_Settings' ) ) {
				include_once LTY_PLUGIN_PATH . '/inc/admin/menu/class-lty-settings-page.php'  ;
			}

			// Set default values for settings.
			$settings = LTY_Settings::get_settings_pages() ;
			foreach ( $settings as $setting ) {
				$sections = $setting->get_sections() ;
				// Update the section settings.
				if ( lty_check_is_array( $sections ) ) {
					foreach ( $sections as $section_key => $section ) {
						$settings_array = $setting->get_settings( $section_key ) ;
						foreach ( $settings_array as $value ) {
							//Check if the default and id key is exists.
							if ( isset( $value[ 'default' ] ) && isset( $value[ 'id' ] ) ) {
								//Check if option are saved or not.
								if ( get_option( $value[ 'id' ] ) === false ) {
									add_option( $value[ 'id' ], $value[ 'default' ] ) ;
								}
							}
						}
					}
				} else {
					$settings_fields = $setting->get_settings( $setting->get_id() ) ;
					foreach ( $settings_fields as $value ) {
						//Check if default and id key is exists.
						if ( isset( $value[ 'default' ] ) && isset( $value[ 'id' ] ) ) {
							//Check if option are saved or not.
							if ( get_option( $value[ 'id' ] ) === false ) {
								add_option( $value[ 'id' ], $value[ 'default' ] ) ;
							}
						}
					}
				}
			}

			//default for notification
			$notifications = LTY_Notification_Instances::get_notifications() ;

			foreach ( $notifications as $object ) {
				$settings = $object->get_settings_array() ;

				if ( ! lty_check_is_array( $settings ) ) {
					continue ;
				}

				foreach ( $settings as $setting ) {
					if ( isset( $setting[ 'default' ] ) && isset( $setting[ 'id' ] ) ) {
						if ( get_option( $setting[ 'id' ] ) === false ) {
							add_option( $setting[ 'id' ], $setting[ 'default' ] ) ;
						}
					}
				}
			}
		}
	}

	LTY_Install::init() ;
}
