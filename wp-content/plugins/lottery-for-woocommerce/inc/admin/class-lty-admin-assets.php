<?php

/**
 * Admin Assets.
 *
 * @since 1.0.0
 * */
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}

if (!class_exists('LTY_Admin_Assets')) {

	/**
	 * Class.
	 *
	 * @since 1.0.0
	 * */
	class LTY_Admin_Assets {

		/**
		 * Suffix.
		 *
		 * @since 1.0.0
		 * @var string
		 */
		private static $suffix;

		/**
		 * In Footer.
		 *
		 * @since 10.2.0
		 * @var bool
		 */
		private static $in_footer = false;

		/**
		 * Localize the scripts.
		 *
		 * @since 10.2.0
		 * @var array
		 */
		private static $wp_localized_scripts = array();

		/**
		 * Scripts.
		 *
		 * @since 10.2.0
		 * @var array
		 */
		private static $scripts = array();

		/**
		 * Styles.
		 *
		 * @since 10.2.0
		 * @var array
		 */
		private static $styles = array();

		/**
		 * Class initialization.
		 *
		 * @since 1.0.0
		 */
		public static function init() {
			self::$suffix = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min';

			add_action('admin_enqueue_scripts', array( __CLASS__, 'load_scripts' ));
			add_action('admin_print_scripts', array( __CLASS__, 'localize_printed_scripts' ), 5);
			add_action('admin_print_footer_scripts', array( __CLASS__, 'localize_printed_scripts' ), 5);
		}

		/**
		 * Register and enqueue admin scripts.
		 *
		 * @since 10.2.0
		 */
		public static function load_scripts() {
			// Register admin scripts and styles.
			self::register_scripts();
			self::register_styles();

			// Enqueue registered scripts and styles.
			self::enqueue_registered_scripts();
			self::enqueue_registered_styles();
		}

		/**
		 * Register all scripts.
		 *
		 * @since 10.2.0
		 */
		private static function register_scripts() {
			$default_scripts = self::get_default_scripts();
			// Returns if there is no scripts to register.
			if (!lty_check_is_array($default_scripts)) {
				return;
			}

			foreach ($default_scripts as $handle => $script) {
				if (!isset($script['src'])) {
					continue;
				}

				$deps = isset($script['deps']) ? array_merge(array( 'jquery' ), $script['deps']) : array( 'jquery' );
				$version = isset($script['version']) ? $script['version'] : LTY_VERSION;
				$in_footer = isset($script['in_footer']) ? $script['in_footer'] : self::$in_footer;
				if (!wp_register_script($handle, $script['src'], $deps, $version, $in_footer)) {
					continue;
				}

				self::$scripts[] = $handle;
			}
		}

		/**
		 * Register all styles.
		 *
		 * @since 10.2.0
		 */
		private static function register_styles() {
			$default_styles = self::get_default_styles();
			// Returns if there is no styles to register.
			if (!lty_check_is_array($default_styles)) {
				return;
			}

			foreach ($default_styles as $handle => $style) {
				if (!isset($style['src'])) {
					continue;
				}

				$deps = isset($style['deps']) ? $style['deps'] : array();
				$version = isset($style['version']) ? $style['version'] : LTY_VERSION;
				$media = isset($style['media']) ? $style['media'] : 'all';
				$has_rtl = isset($style['has_rtl']) ? $style['has_rtl'] : false;
				if (!wp_register_style($handle, $style['src'], $deps, $version, $media)) {
					continue;
				}

				self::$styles[] = $handle;

				if ($has_rtl) {
					wp_style_add_data($handle, 'rtl', 'replace');
				}
			}
		}

		/**
		 * Get the default scripts to register.
		 *
		 * @since 10.2.0
		 * @return array
		 */
		private static function get_default_scripts() {
			if (!in_array(lty_current_page_screen_id(), lty_page_screen_ids())) {
				return array();
			}

			/**
			 * This hook is used to alter the admin default register scripts.
			 *
			 * @since 10.2.0
			 */
			return apply_filters(
					'lty_admin_default_register_scripts',
					array(
						'lty-admin' => array(
							'src' => self::get_asset_url('assets/js/admin/admin.js'),
							'deps' => array( lty_get_wc_script_handle_name( 'blockui' ), 'wc-backbone-modal' ),
						),
						'lty-admin-settings' => array(
							'src' => self::get_asset_url('assets/js/admin/settings.js'),
							'deps' => array( lty_get_wc_script_handle_name( 'blockui' ) ),
						),
						'jquery-modal' => array(
							'src' => self::get_asset_url('assets/lib/jquery-modal/jquery.modal.js'),
						),
						'lty-export-import' => array(
							'src' => self::get_asset_url('assets/js/admin/export-import.js'),
							'deps' => array( lty_get_wc_script_handle_name( 'blockui' ), 'backbone', 'wc-backbone-modal' ),
						),
						'jquery-ui-timpicker-addon' => array(
							'src' => self::get_asset_url('assets/js/jquery-ui-timepicker-addon' . self::$suffix . '.js'),
							'deps' => array( 'jquery-ui-datepicker' ),
						),
						'lty-enhanced' => array(
							'src' => self::get_asset_url('assets/js/lty-enhanced.js'),
							'deps' => array( lty_get_wc_script_handle_name( 'select2' ), 'jquery-ui-datepicker' ),
						),
						'lty-shop-order' => array(
							'src' => self::get_asset_url('assets/js/admin/shop-order.js'),
							'deps' => array( lty_get_wc_script_handle_name( 'blockui' ), 'wc-backbone-modal' ),
						),
					)
			);
		}

		/**
		 * Get the default styles to register.
		 *
		 * @since 10.2.0
		 * @return array
		 */
		private static function get_default_styles() {
			if (!in_array(lty_current_page_screen_id(), lty_page_screen_ids())) {
				return array();
			}

			/**
			 * This hook is used to alter the admin default register styles.
			 *
			 * @since 10.2.0
			 */
			return apply_filters(
					'lty_admin_default_register_styles',
					array(
						'lty-admin' => array(
							'src' => self::get_asset_url('assets/css/admin.css'),
							'deps' => array( 'wc-admin-layout' ),
						),
						'lty-order-item' => array(
							'src' => self::get_asset_url('assets/css/order-item.css'),
						),
						'jquery-ui-datepicker-addon' => array(
							'src' => self::get_asset_url('assets/css/jquery-ui-timepicker-addon' . self::$suffix . '.css'),
						),
						'jquery-modal' => array(
							'src' => self::get_asset_url('assets/lib/jquery-modal/jquery.modal.css'),
						),
					)
			);
		}

		/**
		 * Get script data.
		 *
		 * @since 10.2.0
		 * @param string $handle
		 * @return array|false
		 */
		public static function get_script_data( $handle ) {
			switch ($handle) {
				case 'lty-admin':
					$params = array(
						'lty_manual_winner_select'         => wp_create_nonce( 'lty-manual-winner-select' ),
						'lty_manual_relist_nonce'          => wp_create_nonce( 'lty-manual-relist-nonce' ),
						'lty_extend_nonce'                 => wp_create_nonce( 'lty-extend-nonce' ),
						'manual_winner_empty_msg'          => __( 'Please select the ticket number', 'lottery-for-woocommerce' ),
						'lty_confirm_message'              => __( 'Are you sure you want to continue?', 'lottery-for-woocommerce' ),
						'lty_delete_instant_winner_message' => __( 'Are you sure you want to delete this instant prize entry?', 'lottery-for-woocommerce' ),
						'lty_remove_instant_winner_message' => __( 'Note: If you removed the winner, please cancel the order to back the ticket number again for purchase. Are you sure you want to remove the winner?', 'lottery-for-woocommerce' ),
						'lty_date_error_message'           => __( 'End date must be greater than start date', 'lottery-for-woocommerce' ),
						'end_date_error_message'           => __( 'End date must be a future date', 'lottery-for-woocommerce' ),
						'lty_max_ticket_error_message'     => __( 'Maximum tickets must be greater than minimum ticket', 'lottery-for-woocommerce' ),
						'lty_max_ticket_per_order_error_message' => __( 'Maximum tickets per order must be lesser than or equal to maximum tickets', 'lottery-for-woocommerce' ),
						'lty_min_ticket_per_user_error_message' => __( 'Minimum tickets per user must be lesser than maximum tickets per user', 'lottery-for-woocommerce' ),
						'lty_winner_count_error_message'   => __( 'Maximum tickets must be greater than winner count', 'lottery-for-woocommerce' ),
						'lty_pick_answer_error_message'    => __( 'Please select an answer', 'lottery-for-woocommerce' ),
						'lty_min_ticket_error_message'     => __( 'Minimum tickets must be greater or equal to winner count', 'lottery-for-woocommerce' ),
						'lty_winner_outside_gift_error_message' => __( 'Winning Items Information URL field cannot be empty.', 'lottery-for-woocommerce' ),
						'lty_ticket_start_number_error_message' => __( 'Ticket Starting Number should be a number', 'lottery-for-woocommerce' ),
						'export_lottery_csv_nonce'         => wp_create_nonce( 'lty-export-lottery-csv' ),
						'product_id'                       => isset( $_GET['lty_action'], $_GET['product_id'] ) ? absint( $_GET['product_id'] ) : false,
						'section'                          => isset( $_GET['lty_action'], $_GET['section'] ) ? wc_clean( wp_unslash( ( $_GET['section'] ) ) ) : '',
						'paged'                            => isset( $_GET['lty_action'], $_GET['paged'] ) ? wc_clean( wp_unslash( ( $_GET['paged'] ) ) ) : '',
						'woo_stock_management_enabled'     => get_option( 'woocommerce_manage_stock' ),
						'woo_stock_management_error_message' => __( 'Please enable stock management in WooCommerce Product settings to run the giveaway products. WooCommerce->Settings->Products->Inventory->Manage stock', 'lottery-for-woocommerce' ),
						'random_ticket_length_error'       => __( 'Ticket Length is not matched based on the Maximum Tickets value in Random Ticket type. Please make sure the Ticket Length value(Giveaway -> Settings -> General -> Ticket Generation -> Automatic Random Ticket Generation Settings -> Ticket Length)', 'lottery-for-woocommerce' ),
						'random_max_ticket'                => lty_get_max_ticket_based_on_ticket_length(),
						'lty_orders_without_tickets_nonce' => wp_create_nonce( 'lty-orders-without-tickets-nonce' ),
						'lty_orders_status_action_nonce'   => wp_create_nonce( 'lty-orders-status-action-nonce' ),
						'guest_participation_type'         => get_option( 'lty_settings_guest_user_participate_type' ),
						'allow_guest_alert_message'        => __( 'If you allow guest user to purchase the giveaway tickets then "Maximum tickets per user" and "Minimum tickets per user" restriction options will be removed from product level settings.', 'lottery-for-woocommerce' ),
						'is_new_lottery_product'           => isset( $_GET['lty_lottery_product'] ) && 'new' === wc_clean( wp_unslash( $_GET['lty_lottery_product'] ) ) ? 'yes' : 'no',
						'no_of_tickets_per_tab_empty_error' => __( 'Number of Tickets per Tab should not be left empty', 'lottery-for-woocommerce' ),
						'preset_min_qty_per_user_error_message' => __( 'Preset Quantity Tickets must be greater than or equal to minimum tickets per user', 'lottery-for-woocommerce' ),
						'preset_max_qty_per_user_error_message' => __( 'Preset Quantity Tickets must be lesser than or equal to maximum tickets per user', 'lottery-for-woocommerce' ),
						'preset_max_qty_error_message'     => __( 'Preset Quantity Tickets must be lesser than or equal to maximum tickets', 'lottery-for-woocommerce' ),
						'preset_max_qty_per_order_error_message' => __( 'Preset Quantity Tickets must be lesser than or equal to maximum tickets per order', 'lottery-for-woocommerce' ),
						'view_more_label'                  => __( 'View More', 'lottery-for-woocommerce' ),
						'view_less_label'                  => __( 'View Less', 'lottery-for-woocommerce' ),
						'search_filter_empty_error_message' => __( 'Please select the filter to search', 'lottery-for-woocommerce' ),
						'duplicate_predefined_button_quantity_message' => __( '%quantity% already exists. Please enter different quantity value', 'lottery-for-woocommerce' ),
						'predefined_button_quantity_error_message' => __( 'Please enter different values in Predefined Quantity fields', 'lottery-for-woocommerce' ),
						'placeholder_image_url'            => wc_placeholder_img_src(),
						'instant_winner_nonce'             => wp_create_nonce( 'lty-instant-winner' ),
						'instant_winner_rules'             => array(
							'save_alert_msg'              => __( 'Please save the Instant Win Prize Settings', 'lottery-for-woocommerce' ),
							'remove_rule_alert_msg'       => __( 'If you are removing this rule then the data will be lost. Are you sure you want to proceed?', 'lottery-for-woocommerce' ),
							'ticket_number_empty_error_msg' => __( 'Ticket Number field cannot be empty', 'lottery-for-woocommerce' ),
							'amount_empty_error_msg'      => __( 'Prize Value field cannot be empty', 'lottery-for-woocommerce' ),
							'coupon_id_empty_error_msg'   => __( 'Coupon cannot be empty', 'lottery-for-woocommerce' ),
							'gift_product_id_empty_error_msg' => __( 'Product ID cannot be empty', 'lottery-for-woocommerce' ),
							'gift_product_quantity_empty_error_msg' => __( 'Product Quantity cannot be empty', 'lottery-for-woocommerce' ),
							'prize_message_empty_error_msg' => __( 'Ticket Prize field cannot be empty', 'lottery-for-woocommerce' ),
							'prize_group_empty_error_msg' => __( 'Group Prize cannot be empty.', 'lottery-for-woocommerce' ), 
						),
						'instant_winner_prize_groups'      => array(
							'title_empty_error_msg'     => __( 'Group Title cannot be empty', 'lottery-for-woocommerce' ),
							'coupon_id_empty_error_msg' => __( 'Coupon cannot be empty', 'lottery-for-woocommerce' ),
							'gift_product_id_empty_error_msg' => __( 'Product ID cannot be empty', 'lottery-for-woocommerce' ),
							'gift_product_quantity_empty_error_msg' => __( 'Product Quantity cannot be empty', 'lottery-for-woocommerce' ),
							'amount_empty_error_msg'    => __( 'Prize Value field cannot be empty', 'lottery-for-woocommerce' ),
							'prize_message_empty_error_msg' => __( 'Prize Message field cannot be empty', 'lottery-for-woocommerce' ),
							'remove_group_alert_msg'    => __( 'If you are removing this prize group, the data will be permanently lost. Are you sure you want to proceed?', 'lottery-for-woocommerce' ),
							'save_alert_msg'            => __( 'Please save the Instant Win Prize Group Settings', 'lottery-for-woocommerce' ),
						),
						'manual_lottery_notification_nonce' => wp_create_nonce( 'lty-manual-lottery-notification' ),
					);
					break;

				case 'lty-admin-settings':
					$params = array(
						'cron_validate_error_message' => __('Please enter the cron value', 'lottery-for-woocommerce'),
						'ticket_confirmation_mail_enabled' => get_option('lty_customer_lottery_ticket_confirmation_enabled'),
						'winner_mail_enabled' => get_option('lty_customer_winner_enabled'),
					);
					break;

				case 'lty-export-import':
					$params = array(
						'import_nonce' => wp_create_nonce('lty-import'),
						'export_nonce' => wp_create_nonce('lty-export'),
						'upload_file_empty_error' => __('Please upload a file.', 'lottery-for-woocommerce'),
						'file_name_empty_error' => __('Please enter file name.', 'lottery-for-woocommerce'),
						/* translators: %s - WP Max upload size */
						'upload_file_max_size_error' => sprintf(__('You cannot upload maximum size of %s.', 'lottery-for-woocommerce'), size_format(wp_max_upload_size())),
					);
					break;

				case 'lty-enhanced':
					$params = array(
						'i18n_no_matches' => esc_html_x('No matches found', 'enhanced select', 'lottery-for-woocommerce'),
						'i18n_input_too_short_1' => esc_html_x('Please enter 1 or more characters', 'enhanced select', 'lottery-for-woocommerce'),
						'i18n_input_too_short_n' => esc_html_x('Please enter %qty% or more characters', 'enhanced select', 'lottery-for-woocommerce'),
						'i18n_input_too_long_1' => esc_html_x('Please delete 1 character', 'enhanced select', 'lottery-for-woocommerce'),
						'i18n_input_too_long_n' => esc_html_x('Please delete %qty% characters', 'enhanced select', 'lottery-for-woocommerce'),
						'i18n_selection_too_long_1' => esc_html_x('You can only select 1 item', 'enhanced select', 'lottery-for-woocommerce'),
						'i18n_selection_too_long_n' => esc_html_x('You can only select %qty% items', 'enhanced select', 'lottery-for-woocommerce'),
						'i18n_load_more' => esc_html_x('Loading more results&hellip;', 'enhanced select', 'lottery-for-woocommerce'),
						'i18n_searching' => esc_html_x('Searching&hellip;', 'enhanced select', 'lottery-for-woocommerce'),
						'search_nonce' => wp_create_nonce('lty-search-nonce'),
						'calendar_image' => WC()->plugin_url() . '/assets/images/calendar.png',
						'ajaxurl' => LTY_ADMIN_AJAX_URL,
						'wc_version' => WC()->version,
					);
					break;

				case 'lty-shop-order':
					$params = array(
						'lty_automatic_ticket_nonce' => wp_create_nonce('lty-automatic-ticket-nonce'),
						'lty_manual_ticket_nonce' => wp_create_nonce('lty-manual-ticket-nonce'),
						'lty_confirm_message' => __('Are you sure you want to continue?', 'lottery-for-woocommerce'),
						'lty_success_message' => __('Ticket created successfully', 'lottery-for-woocommerce'),
						'lty_ticket_number_empty_error_message' => __('Please select a ticket number', 'lottery-for-woocommerce'),
						'lty_answer_require_error_message' => __('Please select a answer', 'lottery-for-woocommerce'),
						'lty_reserved_ticket_error_message' => __('This Ticket is already reserved', 'lottery-for-woocommerce'),
					);
					break;

				default:
					$params = false;
					break;
			}

			return $params;
		}

		/**
		 * Enqueue all registered scripts.
		 *
		 * @since 10.2.0
		 */
		private static function enqueue_registered_scripts() {
			foreach (self::$scripts as $handle) {
				self::enqueue_script($handle);
			}

			// Media.
			wp_enqueue_media();
		}

		/**
		 * Enqueue script.
		 *
		 * @param string $handle
		 * @since 10.2.0
		 */
		public static function enqueue_script( $handle ) {
			if (!wp_script_is($handle, 'registered')) {
				return;
			}

			wp_enqueue_script($handle);
		}

		/**
		 * Enqueue all registered styles.
		 *
		 * @since 10.2.0
		 */
		private static function enqueue_registered_styles() {
			foreach (self::$styles as $handle) {
				self::enqueue_style($handle);
			}
		}

		/**
		 * Enqueue style.
		 *
		 * @param string $handle
		 * @since 10.2.0
		 */
		public static function enqueue_style( $handle ) {
			if (!wp_style_is($handle, 'registered')) {
				return;
			}

			wp_enqueue_style($handle);
		}

		/**
		 * Print the localized scripts when registered handle.
		 *
		 * @since 10.2.0
		 * @return void
		 */
		public static function localize_printed_scripts() {
			foreach (self::$scripts as $handle) {
				self::localize_script($handle);
			}
		}

		/**
		 * Localize the enqueued script.
		 *
		 * @since 10.2.0
		 * @param string $handle
		 * @return null
		 */
		public static function localize_script( $handle ) {
			// Return if already localized script or not enqueued script.
			if (in_array($handle, self::$wp_localized_scripts, true) || !wp_script_is($handle)) {
				return;
			}

			// Get the data for current script.
			$data = self::get_script_data($handle);
			if (!$data) {
				return;
			}

			$name = str_replace('-', '_', $handle) . '_params';

			/**
			 * This hook is used to alter the script data.
			 *
			 * @since 10.2.0
			 */
			if (wp_localize_script($handle, $name, apply_filters($name, $data))) {
				self::$wp_localized_scripts[] = $handle;
			}
		}

		/**
		 * Get asset URL.
		 *
		 * @since 10.2.0
		 * @param string $path Assets path.
		 * @return string
		 */
		private static function get_asset_url( $path ) {
			/**
			 * This hook is used to alter the admin asset URL.
			 *
			 * @since 10.2.0
			 */
			return apply_filters('lty_admin_get_asset_url', LTY_PLUGIN_URL . '/' . $path, $path);
		}
	}

	LTY_Admin_Assets::init();
}
