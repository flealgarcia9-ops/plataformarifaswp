<?php
/**
 * Menu Management
 *
 * @since 1.0.0
 * */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'LTY_Menu_Management' ) ) {

	include_once 'class-lty-settings-page.php';
	include_once 'class-lty-lottery-page.php';

	/**
	 * LTY_Menu_Management Class.
	 * */
	class LTY_Menu_Management {

		/**
		 * Plugin slug.
		 *
		 * @var string
		 */
		protected static $plugin_slug = 'lty';

		/**
		 * Menu slug.
		 *
		 * @var string
		 */
		protected static $menu_slug = 'lty_lottery';

		/**
		 * Settings slug.
		 *
		 * @var string
		 */
		protected static $settings_slug = 'lty_settings';

		/**
		 * Class initialization.
		 * */
		public static function init() {
			// Add custom menus.
			add_action( 'admin_menu', array( __CLASS__, 'add_menu_pages' ) );
			// Add custom screen ids in woocomerce screen ids.
			add_filter( 'woocommerce_screen_ids', array( __CLASS__, 'add_custom_wc_screen_ids' ), 9, 1 );
			// Sanitize settings value.
			add_filter( 'woocommerce_admin_settings_sanitize_option', array( __CLASS__, 'save_custom_fields' ), 10, 3 );
			// Display the error messages.
			add_action( 'lty_before_settings_current_tab_content', array( __CLASS__, 'display_error_messages' ) );
			// Handle exports.
			add_action( 'admin_init', array( __CLASS__, 'handle_exports' ) );
			// Set screen option values.
			add_filter( 'admin_init', array( __CLASS__, 'set_screen_option_values' ) );
		}

		/**
		 * Add Custom Screen IDs in WooCommerce.
		 *
		 * @since 1.0.0
		 * @param array $wc_screen_ids Screen IDs.
		 * @return array
		 */
		public static function add_custom_wc_screen_ids( $wc_screen_ids ) {
			$screen_ids = lty_page_screen_ids();
			$screenid   = lty_current_page_screen_id();

			// Return if current page is not lottery system page.
			if ( ! in_array( $screenid, $screen_ids ) ) {
				return $wc_screen_ids;
			}

			$wc_screen_ids[] = $screenid;

			return $wc_screen_ids;
		}

		/**
		 * Display the error messages.
		 *
		 * @return void.
		 * */
		public static function display_error_messages() {

			self::display_cron_error_messages();

			self::display_woo_stock_management_error_message();

			self::display_guest_notification_error_message();
		}

		/**
		 * Display the cron error messages.
		 *
		 * @return void.
		 * */
		public static function display_cron_error_messages() {
			$error_message = '';
			$cron_type     = get_option( 'lty_settings_cron_type_selection' );

			// Display the WP Cron configuration error message.
			if ( '2' == $cron_type && ! wp_next_scheduled( 'lty_lottery_cronjob' ) ) {
				// Error for the Wp cron is not set/configured.
				$error_message = __( 'WP Cron not configured. WP Cron is required for the plugin to work properly.', 'lottery-for-woocommerce' );

				// Display the Server Cron configuration error message.
			} elseif ( '2' != $cron_type ) {

				$last_updated_date = get_option( 'lty_update_server_cron_last_updated_date' );
				if ( ! $last_updated_date ) {
					// Error for the server cron is not configured.
					$error_message = __( 'Server Cron Not Configured. Server Cron is required for the plugin to work properly.', 'lottery-for-woocommerce' );
				} else {

					$current_date_object = LTY_Date_Time::get_gmt_date_time_object( 'now' );
					$updated_date_object = LTY_Date_Time::get_gmt_date_time_object( $last_updated_date )->modify( '+1 hours' );

					if ( $current_date_object > $updated_date_object ) {
						// Error for the server cron is not trigger above one hour.
						$formatted_date = LTY_Date_Time::get_wp_format_datetime_from_gmt( $last_updated_date, false, ' ', true );
						/* translators: %s: Date */
						$error_message = sprintf( __( 'Please cross-check your Server Cron Configuration for Giveaway Plugin. Last Cron updated on(%s).', 'lottery-for-woocommerce' ), $formatted_date );
					}
				}
			}

			// May be display the error in settings.
			if ( $error_message ) {
				LTY_Settings::error_message( $error_message );
			}
		}

		/**
		 * Display WooCommerce stock management error message.
		 * */
		public static function display_woo_stock_management_error_message() {

			if ( 'yes' == get_option( 'woocommerce_manage_stock' ) ) {
				return;
			}
			/* translators: %s: url %s: url name */
			$error_message = __( sprintf( 'Please enable stock management in WooCommerce Product settings to run the lottery products, to see the settings please <a href="%s" target="_blank">%s</a>.', esc_url( admin_url( 'admin.php?page=wc-settings&tab=products&section=inventory' ) ), 'click here' ), 'lottery-for-woocommerce' );

			LTY_Settings::error_message( $error_message );
		}

		/**
		 * Display Guest notification error message.
		 * */
		public static function display_guest_notification_error_message() {
			if ( '3' != get_option( 'lty_settings_guest_user_participate_type' ) ) {
				return;
			}

			// Return if any of the customer email notifications are enabled.
			if ( 'yes' === get_option( 'lty_customer_winner_enabled' ) ||
				'yes' === get_option( 'lty_customer_multiple_winner_enabled' ) ||
				'yes' === get_option( 'lty_customer_lottery_ticket_confirmation_enabled' ) ||
				'yes' === get_option( 'lty_customer_lottery_ticket_confirmation_order_enabled' ) ) {
					return;
			}

			$error_message = __( 'If you want to send Giveaway Participation & Giveaway Winner emails to the guest users, please enable "Customer – Giveaway Participation Confirmation" email and "Customer - Giveaway Winner" email in Giveaway -> Settings-> Notifications.', 'lottery-for-woocommerce' );
			?>
			<div class='notice notice-info inline'>
				<p><strong><?php echo esc_html( $error_message ); ?></strong></p>
			</div>
			<?php
		}

		/**
		* Handle the export.
		*
		* @since 10.3.0
		* */
		public static function handle_exports() {
			if (!isset($_GET['lty_action'], $_GET['lty_nonce'], $_GET['export_type'])) {
				return;
			}

			if (!wp_verify_nonce(wc_clean(wp_unslash($_GET['lty_nonce'])), 'lty-export')) {
				return;
			}

			$exporter = LTY_Export_Handler::get_exporter(wc_clean(wp_unslash($_GET['export_type'])));
			// Throw error, when the action type does not have exporter.
			if (!is_object($exporter)) {
				return;
			}

			if (!empty($_GET['filename'])) { // WPCS: input var ok.
				$exporter->set_filename(sanitize_file_name(wp_unslash($_GET['filename']))); // WPCS: input var ok, sanitization ok.
			}

			$exporter->export();
		}

		/**
		 * Add menu pages.
		 * */
		public static function add_menu_pages() {
			$url = LTY_PLUGIN_URL . '/assets/images/dash-icon.png';

			// Add Lottery Menu.
			$lottery_page = add_menu_page( __( 'Giveaway', 'lottery-for-woocommerce' ), __( 'Giveaway', 'lottery-for-woocommerce' ), 'manage_woocommerce', self::$menu_slug, array( __CLASS__, 'lottery_page' ), $url );

			// Add Lottery System Submenu.
			$settings_page = add_submenu_page( self::$menu_slug, __( 'Settings', 'lottery-for-woocommerce' ), __( 'Settings', 'lottery-for-woocommerce' ), 'manage_woocommerce', self::$settings_slug, array( __CLASS__, 'settings_page' ) );

			// Lottery page init.
			add_action( sanitize_key( 'load-' . $lottery_page ), array( __CLASS__, 'lottery_page_init' ) );
			// Settings page init.
			add_action( sanitize_key( 'load-' . $settings_page ), array( __CLASS__, 'settings_page_init' ) );
		}

		/**
		 * Lottery page init.
		 * */
		public static function lottery_page_init() {
			global $current_lottery, $lottery_id, $lty_product, $current_tab, $current_section;

			$lottery_id      = isset( $_REQUEST[ 'product_id' ] ) ? intval( $_REQUEST[ 'product_id' ] ) : '' ; // @codingStandardsIgnoreLine.
			$lty_product     = wc_get_product( $lottery_id );
			$current_lottery = empty( $_REQUEST[ 'lty_action' ] ) ? '' : sanitize_title( wp_unslash( $_REQUEST[ 'lty_action' ] ) ) ; // @codingStandardsIgnoreLine.
			$current_section = empty( $_REQUEST[ 'section' ] ) ? '' : sanitize_title( wp_unslash( $_REQUEST[ 'section' ] ) ) ; // @codingStandardsIgnoreLine.

			if ( 'view' == $current_lottery ) {
				$tabs = LTY_Lottery_Page::lottery_tabs();

				// Get current tab.
				$current_tab = key( $tabs );
				if ( ! empty( $_GET['tab'] ) ) {
					$sanitize_current_tab = sanitize_title( wp_unslash( $_GET[ 'tab' ] ) ) ; // @codingStandardsIgnoreLine.
					if ( array_key_exists( $sanitize_current_tab, $tabs ) ) {
						$current_tab = $sanitize_current_tab;
					}
				}
			}

			// Render lottery screen options.
			add_filter( 'screen_settings', array( __CLASS__, 'render_lottery_screen_option' ), 10, 2 );
			// This needs a submit button.
			add_filter( 'screen_options_show_submit', '__return_true' );

			LTY_Lottery_Page::init();
		}

		/**
		 * Render the lottery screen option.
		 *
		 * @since 8.2.0
		 * @param string $screen_settings
		 * @param object $screen_object
		 * @return string
		 */
		public static function render_lottery_screen_option( $screen_settings, $screen_object ) {
			if ( ! is_object( $screen_object ) ) {
				return $screen_settings;
			}

			$current_page            = empty( $_REQUEST['lty_action'] ) ? '' : sanitize_title( wp_unslash( $_REQUEST['lty_action'] ) );
			$display_instant_winners = true;

			if ( 'view' === $current_page ) {
				global $lty_product, $current_section;

				$relist_data = array_reverse( (array) $lty_product->get_lty_relists() );
				if ( $current_section ) {
					$relist_count = count( $relist_data );
					$relist_count = $relist_count - $current_section;

					if ( isset( $relist_data[ $relist_count ]['instant_winner'] ) ) {
						$display_instant_winners = 'yes' !== $relist_data[ $relist_count ]['instant_winner'] ? false : true;
					}
				} elseif ( ! $lty_product->is_instant_winner() ) {
					$display_instant_winners = false;
				}
			}

			ob_start();
				include_once LTY_PLUGIN_PATH . '/inc/admin/menu/views/html-lottery-screen-options.php';
				$screen_settings = ob_get_contents();
			ob_end_clean();

			return $screen_settings;
		}

		/**
		 * Set the screen option values.
		 *
		 * @since 8.2.0
		 * @return void
		 */
		public static function set_screen_option_values() {
			$user = wp_get_current_user();
			if ( ! $user ) {
				return;
			}

			$screen_options = array(
				'lottery',
				'lottery_ticket',
				'lottery_instant_winners',
			);

			foreach ( $screen_options as $screen_option ) {
				$option = 'lty_' . $screen_option . '_per_page';
				$value  = isset( $_REQUEST[ $option ] ) ? wc_clean( wp_unslash( $_REQUEST[ $option ] ) ) : false;
				if ( ! $value ) {
					continue;
				}

				update_user_meta( $user->ID, $option, $value );
			}
		}

		/**
		 * Settings page init.
		 * */
		public static function settings_page_init() {
			global $current_tab, $current_section, $current_sub_section;

			// Include settings pages.
			$settings = LTY_Settings::get_settings_pages();

			$tabs = lty_get_allowed_setting_tabs();

			// Get current tab/section.
			$current_tab = key( $tabs );
			if ( ! empty( $_GET['tab'] ) ) {
				$sanitize_current_tab = sanitize_title( wp_unslash( $_GET[ 'tab' ] ) ) ; // @codingStandardsIgnoreLine.
				if ( array_key_exists( $sanitize_current_tab, $tabs ) ) {
					$current_tab = $sanitize_current_tab;
				}
			}

			$section = isset( $settings[ $current_tab ] ) ? $settings[ $current_tab ]->get_sections() : array();

			$current_section     = empty( $_REQUEST[ 'section' ] ) ? key( $section ) : sanitize_title( wp_unslash( $_REQUEST[ 'section' ] ) ) ; // @codingStandardsIgnoreLine.
			$current_section     = empty( $current_section ) ? $current_tab : $current_section;
			$current_sub_section = empty( $_REQUEST[ 'subsection' ] ) ? '' : sanitize_title( wp_unslash( $_REQUEST[ 'subsection' ] ) ) ; // @codingStandardsIgnoreLine.

			/**
			 * This hook is used to do extra action for current tab when load the settings.
			 *
			 * @hooked LTY_Settings_Page->save - 10 (save the settings).
			 * @hooked LTY_Settings_Page->reset - 20 (reset the settings)
			 * @since 1.0
			 */
			do_action( sanitize_key( self::$plugin_slug . '_load_settings_' . $current_tab ), $current_section );

			add_action( 'woocommerce_admin_field_lty_custom_fields', array( __CLASS__, 'custom_fields_output' ) );
		}

		/**
		 * Settings page output.
		 * */
		public static function settings_page() {
			LTY_Settings::output();
		}

		/**
		 * Output the custom field settings.
		 * */
		public static function custom_fields_output( $options ) {

			LTY_Settings::output_fields( $options );
		}

		/**
		 * Save Custom Field settings.
		 * */
		public static function save_custom_fields( $value, $option, $raw_value ) {

			return LTY_Settings::save_fields( $value, $option, $raw_value );
		}

		/**
		 * Lottery page output.
		 * */
		public static function lottery_page() {
			LTY_Lottery_Page::output();
		}
	}

	LTY_Menu_Management::init();
}
