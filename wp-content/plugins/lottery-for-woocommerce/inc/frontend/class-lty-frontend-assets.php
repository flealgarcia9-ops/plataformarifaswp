<?php
/**
 * Frontend Assets.
 * */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'LTY_Frontend_Assets' ) ) {

	/**
	 * Class.
	 * */
	class LTY_Frontend_Assets {

		/**
		 * Suffix.
		 *
		 * @since 10.1.0
		 * @var string
		 */
		private static $suffix;

		/**
		 * Scripts.
		 *
		 * @since 10.1.0
		 * @var array
		 */
		private static $scripts = array();

		/**
		 * Styles.
		 *
		 * @since 10.1.0
		 * @var array
		 */
		private static $styles = array();

		/**
		 * Localized scripts.
		 *
		 * @since 10.1.0
		 * @var array
		 */
		private static $wp_localized_scripts = array();

		/**
		 * In Footer.
		 *
		 * @since 10.1.0
		 * @var bool
		 */
		private static $in_footer = false;

		/**
		 * Class initialization.
		 *
		 * @since 10.1.0
		 * */
		public static function init() {
			self::$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

			add_action( 'wp_enqueue_scripts', array( __CLASS__, 'load_scripts' ) );
			add_action( 'wp_print_scripts', array( __CLASS__, 'localize_printed_scripts' ), 5 );
			add_action( 'wp_print_footer_scripts', array( __CLASS__, 'localize_printed_scripts' ), 5 );
		}

		/**
		 * Register and enqueue frontend scripts.
		 *
		 * @since 10.1.0
		 */
		public static function load_scripts() {
			self::register_scripts();
			self::register_styles();

			self::enqueue_registered_scripts();
			self::enqueue_registered_styles();

			self::add_inline_style();
		}

		/**
		 * Register all scripts.
		 *
		 * @since 10.1.0
		 */
		private static function register_scripts() {
			$default_scripts = self::get_default_scripts();
			// Returns if there is no scripts to register.
			if ( ! lty_check_is_array( $default_scripts ) ) {
				return;
			}

			foreach ( $default_scripts as $handle => $script ) {
				if ( ! isset( $script['src'] ) ) {
					continue;
				}

				$deps      = isset( $script['deps'] ) ? array_merge( array( 'jquery' ), $script['deps'] ) : array( 'jquery' );
				$version   = isset( $script['version'] ) ? $script['version'] : LTY_VERSION;
				$in_footer = isset( $script['in_footer'] ) ? $script['in_footer'] : self::$in_footer;
				if ( ! wp_register_script( $handle, $script['src'], $deps, $version, $in_footer ) ) {
					continue;
				}

				self::$scripts[] = $handle;
			}
		}

		/**
		 * Register all styles.
		 *
		 * @since 10.1.0
		 */
		private static function register_styles() {
			$default_styles = self::get_default_styles();
			// Returns if there is no styles to register.
			if ( ! lty_check_is_array( $default_styles ) ) {
				return;
			}

			foreach ( $default_styles as $handle => $style ) {
				if ( ! isset( $style['src'] ) ) {
					continue;
				}

				$deps    = isset( $style['deps'] ) ? $style['deps'] : array();
				$version = isset( $style['version'] ) ? $style['version'] : LTY_VERSION;
				$media   = isset( $style['media'] ) ? $style['media'] : 'all';
				$has_rtl = isset( $style['has_rtl'] ) ? $style['has_rtl'] : false;
				if ( ! wp_register_style( $handle, $style['src'], $deps, $version, $media ) ) {
					continue;
				}

				self::$styles[] = $handle;

				if ( $has_rtl ) {
					wp_style_add_data( $handle, 'rtl', 'replace' );
				}
			}
		}

		/**
		 * Get the default scripts to register.
		 *
		 * @since 10.1.0
		 * @return array
		 */
		private static function get_default_scripts() {
			/**
			 * This hook is used to alter the default register scripts.
			 *
			 * @since 10.1.0
			 */
			return apply_filters(
				'lty_default_register_scripts',
				array(
					'lty-frontend'        => array(
						'src'  => self::get_asset_url( 'assets/js/frontend/frontend.js' ),
						'deps' => array( lty_get_wc_script_handle_name( 'blockui' ), 'jquery-alertable', lty_get_wc_script_handle_name( 'accounting' ), 'jquery-ui-slider', lty_get_wc_script_handle_name( 'touch-punch' ) ),
					),
					'jquery-alertable'    => array( 'src' => self::get_asset_url( 'assets/js/frontend/jquery.alertable' . self::$suffix . '.js' ) ),
					'lty-countdown-timer' => array( 'src' => self::get_asset_url( 'assets/js/frontend/countdown-timer.js' ) ),
					'jquery-modal'        => array( 'src' => self::get_asset_url( 'assets/lib/jquery-modal/jquery.modal.js' ) ),
				)
			);
		}

		/**
		 * Get the default styles to register.
		 *
		 * @since 10.1.0
		 * @return array
		 */
		private static function get_default_styles() {
			/**
			 * This hook is used to alter the default register styles.
			 *
			 * @since 10.1.0
			 */
			return apply_filters(
				'lty_default_register_styles',
				array(
					'lty-frontend'     => array(
						'src'  => self::get_asset_url( 'assets/css/frontend.css' ),
						'deps' => array( 'dashicons' ),
					),
					'jquery-alertable' => array(
						'src' => self::get_asset_url( 'assets/css/jquery.alertable.css' ),
					),
					'jquery-ui'        => array(
						'src' => WC()->plugin_url() . '/assets/css/jquery-ui/jquery-ui.min.css',
					),
					'jquery-modal'     => array(
						'src' => self::get_asset_url( 'assets/lib/jquery-modal/jquery.modal.css' ),
					),
				)
			);
		}

		/**
		 * Get script data.
		 *
		 * @since 10.1.0
		 * @param string $handle
		 * @global object $post Post object.
		 * @return array|false
		 */
		public static function get_script_data( $handle ) {
			global $post;

			switch ( $handle ) {
				case 'lty-frontend':
					$product_id = is_object( $post ) ? $post->ID : '';
					$product    = ! empty( $product_id ) ? wc_get_product( $product_id ) : false;

					$params = array(
						'lottery_tickets_nonce'            => wp_create_nonce( 'lty-lottery-tickets' ),
						'ajaxurl'                          => LTY_ADMIN_AJAX_URL,
						'guest_user'                       => ( ! is_user_logged_in() && '2' == get_option( 'lty_settings_guest_user_participate_type' ) ) ? 'yes' : 'no',
						'guest_error_msg'                  => get_option( 'lty_settings_single_product_validate_guest_error_message' ),
						'question_answer_alert_message'    => get_option( 'lty_settings_question_answer_alert_error_message', 'Please select an answer' ),
						'verify_question_answer_alert_message' => get_option( 'lty_settings_verify_question_answer_alert_error_message', 'Are you sure you want to proceed with the Selected Answer' ),
						'ticket_selection_alert_message'   => get_option( 'lty_settings_ticket_selection_alert_error_message', 'Please select a ticket number' ),
						'validate_correct_answer'          => lty_is_lottery_product( $product ) ? $product->is_verify_answer_enabled() : 'no',
						'incorrectly_selected_answer_restriction' => lty_is_lottery_product( $product ) ? $product->incorrectly_selected_answer_restriction_is_enabled() : 'no',
						'disable_answer_verification_alert' => get_option( 'lty_settings_hide_lottery_answer_verification_alert', 'no' ),
						'predefined_buttons_alert_message' => get_option( 'lty_settings_predefined_buttons_alert_error_message', 'Please select an option' ),
						'is_predefined_buttons_enabled'    => lty_is_lottery_product( $product ) ? $product->is_predefined_button_enabled() : false,
						'decimals'                         => wc_get_price_decimals(),
						'decimal_separator'                => wc_get_price_decimal_separator(),
						'thousand_separator'               => wc_get_price_thousand_separator(),
						'currency'                         => get_woocommerce_currency_symbol(),
						'disable_participate_now_button'   => get_option( 'lty_settings_disable_participate_now_button', 'no' ),
						'enable_cart_redirection'          => get_option( 'woocommerce_cart_redirect_after_add' ),
						'cart_url'                         => wc_get_cart_url(),
						'currency_pos'                     => get_option( 'woocommerce_currency_pos' ),
						'lottery_manual_ticket_search_action_nonce' => wp_create_nonce( 'lty-lottery-manual-ticket-search-action-nonce' ),
						'manual_ticket_search_empty_error' => __( 'Please enter any ticket', 'lottery-for-woocommerce' ),
						'pagination_action_nonce'          => wp_create_nonce( 'lty-pagination-action-nonce' ),
						'instant_win_prize_group_tickets_nonce'          => wp_create_nonce( 'lty-instant-win-prize-group-tickets-nonce' ),
						'search_nonce'                     => wp_create_nonce( 'lty-search-nonce' ),
						'view_more_ticket_label'           => wp_kses_post( lty_get_single_product_view_more_tickets_button_label() ),
						'view_less_ticket_label'           => wp_kses_post( lty_get_single_product_view_less_tickets_button_label() ),
						'can_display_predefined_with_quantity_selector' => lty_is_lottery_product( $product ) ? $product->can_display_predefined_with_quantity_selector() : false,
						'product_page_loading_mode'    => get_option( 'lty_settings_product_page_loading_mode', '1' ),
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
		 * @since 10.1.0
		 */
		private static function enqueue_registered_scripts() {
			foreach ( self::$scripts as $handle ) {
				self::enqueue_script( $handle );
			}
		}

		/**
		 * Enqueue script.
		 *
		 * @param string $handle
		 * @since 10.1.0
		 */
		private static function enqueue_script( $handle ) {
			if ( ! wp_script_is( $handle, 'registered' ) ) {
				return;
			}

			wp_enqueue_script( $handle );
		}

		/**
		 * Enqueue all registered styles.
		 *
		 * @since 10.1.0
		 */
		private static function enqueue_registered_styles() {
			foreach ( self::$styles as $handle ) {
				self::enqueue_style( $handle );
			}
		}

		/**
		 * Enqueue style.
		 *
		 * @param string $handle
		 * @since 10.1.0
		 */
		private static function enqueue_style( $handle ) {
			if ( ! wp_style_is( $handle, 'registered' ) ) {
				return;
			}

			wp_enqueue_style( $handle );
		}

		/**
		 * Localize scripts only when enqueued.
		 *
		 * @since 10.1.0
		 */
		public static function localize_printed_scripts() {
			foreach ( self::$scripts as $handle ) {
				self::localize_script( $handle );
			}
		}

		/**
		 * Localize the enqueued script.
		 *
		 * @since 10.1.0
		 * @param string $handle
		 * @return null
		 */
		private static function localize_script( $handle ) {
			// Return if already localized script or not enqueued script.
			if ( in_array( $handle, self::$wp_localized_scripts, true ) || ! wp_script_is( $handle ) ) {
				return;
			}

			// Get the data for current script.
			$data = self::get_script_data( $handle );
			if ( ! $data ) {
				return;
			}

			$name = str_replace( '-', '_', $handle ) . '_params';

			/**
			 * This hook is used to alter the script data.
			 *
			 * @since 10.1.0
			 */
			if ( wp_localize_script( $handle, $name, apply_filters( $name, $data ) ) ) {
				self::$wp_localized_scripts[] = $handle;
			}
		}

		/**
		 * Add Inline Style.
		 * */
		public static function add_inline_style() {
			$contents = '.lty-progress-bar {
                            background: ' . get_option( 'lty_settings_single_product_progress_bar_bg_color', '#f3efe6' ) . '; 
                 }
                  
                .lty-progress-bar span.lty-progress-fill {
                background: ' . get_option( 'lty_settings_single_product_progress_bar_fill_color', '#00cc00' ) . ';
                }

                .lty-badge {
                background: ' . get_option( 'lty_settings_lottery_batch_bg_color', '#3366ff' ) . ';
                }
                
                .lty-dashboard-navigation nav {
                 background: ' . get_option( 'lty_settings_dashboard_menu_bg_color', '#f7f7f7' ) . ';
                 border: 1px solid ' . get_option( 'lty_settings_dashboard_menu_bg_color', '#f7f7f7' ) . ';
                }
                
                .lty-dashboard-navigation nav a {
                 color: ' . get_option( 'lty_settings_dashboard_menu_color', '#000000' ) . '!important;
                }

                .lty-dashboard-navigation nav .lty-current { 
                color: ' . get_option( 'lty_settings_dashboard_current_menu_color', '#000000' ) . '!important;
                }
                
                .lty-dashboard-navigation nav a:hover {
                 color: ' . get_option( 'lty_settings_dashboard_current_menu_color', '#000000' ) . '!important;
                }
                
                .lty-dashboard-contents {
                    border-left: 2px solid ' . get_option( 'lty_settings_dashboard_menu_bg_color', '#f7f7f7' ) . ';
                    border-bottom: 2px solid ' . get_option( 'lty_settings_dashboard_menu_bg_color', '#f7f7f7' ) . ';
                    border-right: 2px solid ' . get_option( 'lty_settings_dashboard_menu_bg_color', '#f7f7f7' ) . ';
                }
                
                .lty-shop-timer-wrapper {
                 background: ' . get_option( 'lty_settings_timer_bg_color', '#f1f1f1' ) . ';
                }
                
                .lty-shop-timer-wrapper #lty_lottery_days, 
                .lty-shop-timer-wrapper #lty_lottery_hours,
                .lty-shop-timer-wrapper #lty_lottery_minutes,
                .lty-shop-timer-wrapper #lty_lottery_seconds {
                 color: ' . get_option( 'lty_settings_timer_time_color', '#000000' ) . '!important;
                }
                
                .lty-lottery-countdown-timer .lty-lottery-timer {
                color: ' . get_option( 'lty_settings_single_product_timer_label_color', '#000000' ) . ';
                }                       
                
                .lty-lottery-countdown-timer #lty_lottery_days, 
                .lty-lottery-countdown-timer #lty_lottery_hours,
                .lty-lottery-countdown-timer #lty_lottery_minutes,
                .lty-lottery-countdown-timer #lty_lottery_seconds {
                 color: ' . get_option( 'lty_settings_single_product_timer_time_color', '#000000' ) . ';
                }                        
                .lty-lottery-ticket-panel .lty-ticket-number-wrapper ul li {
                 border: 1px solid ' . get_option( 'lty_settings_single_product_ticket_border_color', '#e0e0e0' ) . ';
                 color: ' . get_option( 'lty_settings_single_product_ticket_number_color', '#444444' ) . ';
                }
                .lty-lottery-ticket-panel .lty-ticket-number-wrapper ul li.lty-selected-ticket {
                 background: ' . get_option( 'lty_settings_single_product_active_ticket_bg_color', '#00cc00' ) . ';
                 color: ' . get_option( 'lty_settings_single_product_active_ticket_number_color', '#ffffff' ) . ';
                 }
                 .lty-lottery-ticket-panel .lty-ticket-number-wrapper ul li:hover {
                 background: ' . get_option( 'lty_settings_single_product_active_ticket_bg_color', '#00cc00' ) . ';
                 color: ' . get_option( 'lty_settings_single_product_active_ticket_number_color', '#ffffff' ) . ';
                }

                 .lty-lottery-ticket-panel .lty-ticket-number-wrapper ul li.lty-booked-ticket {
                  background: ' . get_option( 'lty_settings_single_product_booked_ticket_bg_color', '#ff1111' ) . ';
                  color: ' . get_option( 'lty_settings_single_product_booked_ticket_number_color', '#ffffff' ) . ';                      
                  }
                  
                  .lty-lottery-ticket-panel .lty-ticket-number-wrapper ul li.lty-reserved-ticket {
                  background: ' . get_option( 'lty_settings_single_product_reserved_ticket_bg_color', '#00a1c9' ) . ';
                  color: ' . get_option( 'lty_settings_single_product_reserved_ticket_number_color', '#ffffff' ) . ';                      
                  }
                  
                .lty-lottery-ticket-panel .lty-lottery-ticket-tab-wrapper .lty-lottery-ticket-tab {
                background: ' . get_option( 'lty_settings_single_product_tab_bg_color', '#eeeeee' ) . ';
                border: 1px solid ' . get_option( 'lty_settings_single_product_tab_bg_color', '#eeeeee' ) . ';
                color: ' . get_option( 'lty_settings_single_product_tab_text_color', '#333333' ) . ';
                
                }

                .lty-lottery-ticket-panel .lty-lottery-ticket-tab-wrapper .lty-active-tab {
                 background: ' . get_option( 'lty_settings_single_product_active_tab_bg_color', '#cccccc' ) . ';
                 border: 1px solid ' . get_option( 'lty_settings_single_product_active_tab_bg_color', '#cccccc' ) . ';
                 color: ' . get_option( 'lty_settings_single_product_active_tab_text_color', '#333333' ) . ';
                 }

                .lty-lottery-ticket-panel .lty-lottery-ticket-tab-wrapper .lty-lottery-ticket-tab:hover {
                 background: ' . get_option( 'lty_settings_single_product_active_tab_bg_color', '#cccccc' ) . ';
                 border: 1px solid ' . get_option( 'lty_settings_single_product_active_tab_bg_color', '#cccccc' ) . ';
                 color: ' . get_option( 'lty_settings_single_product_active_tab_text_color', '#333333' ) . ';
                }
                
                ul.lty-lottery-answers li {
                border: 1px solid ' . get_option( 'lty_settings_single_product_answer_border_color', '#cccccc' ) . ';
                color: ' . get_option( 'lty_settings_single_product_answer_text_color', '#000000' ) . ';
                }

                ul.lty-lottery-answers li.lty-selected {
                color: ' . get_option( 'lty_settings_single_product_active_answer_text_color', '#000000' ) . ';
                background: ' . get_option( 'lty_settings_single_product_active_answer_bg_color', '#00cc00' ) . ';
                }

                ul.lty-lottery-answers li:hover {
                color: ' . get_option( 'lty_settings_single_product_active_answer_text_color', '#ffffff' ) . ';
                background: ' . get_option( 'lty_settings_single_product_active_answer_bg_color', '#00cc00' ) . ';
                }
                
                ul.lty-predefined-buttons li {
                border: 1px solid ' . get_option( 'lty_settings_single_product_predefined_buttons_border_color', '#cccccc' ) . ';
                color: ' . get_option( 'lty_settings_single_product_predefined_buttons_text_color', '#000000' ) . ';
                background: ' . get_option( 'lty_settings_single_product_predefined_buttons_bg_color', '#cccccc' ) . ';   
                }

                ul.lty-predefined-buttons li.lty-selected-button {
                color: ' . get_option( 'lty_settings_single_product_active_predefined_buttons_text_color', '#000000' ) . ';
                background: ' . get_option( 'lty_settings_single_product_active_predefined_buttons_bg_color', '#00cc00' ) . ';
                }

                ul.lty-predefined-buttons li:hover {
                color: ' . get_option( 'lty_settings_single_product_active_predefined_buttons_text_color', '#ffffff' ) . ';
                background: ' . get_option( 'lty_settings_single_product_active_predefined_buttons_bg_color', '#00cc00' ) . ';
                }
                
				.lty-order-instant-winners-heading {
                	color: ' . get_option( 'lty_settings_order_instant_winners_heading_color', '#358014' ) . ';
                }

				.lty-instant-win-better-luck-message {
                	color: ' . get_option( 'lty_settings_order_instant_win_better_luck_msg_color', '#ff0000' ) . ';
                }

				.lty-instant-winner-prize-group-item-content .lty_available_status,
				.lty-instant-winner-prize-group-item-content .lty_pending_status,
				.lty-instant-winner-available-dot:before {
					background-color: ' . get_option( 'lty_settings_instant_win_group_prize_available_bg_color', '#0bcc4c' ) . ';
				}

				.lty-instant-winner-prize-group-item-content .lty_won_status,
				.lty-instant-winner-won-dot:before {
					background-color: ' . get_option( 'lty_settings_instant_win_group_prize_won_bg_color', '#f44c2e' ) . ';
				}

				
				.lty-instant-winner-prize-group-item .lty-ticket-number {
					color: ' . get_option( 'lty_settings_instant_win_group_ticket_number_label_color', '#ffffff' ) . ';
				}

				.lty-instant-winners-table .lty-prize-available {
					color: ' . get_option( 'lty_settings_instant_win_group_available_prize_label_color', '#009933' ) . ';
				}

				.lty-instant-winners-table .lty-instant-winner {
					color: ' . get_option( 'lty_settings_instant_win_group_won_prize_label_color', '#e64c31' ) . ';
				}

                @media screen and (max-width: 768px) {
                    .lty-lottery-ticket-panel .lty-ticket-number-wrapper ul li.lty-unselected-ticket{
                       background:#fff !important;
                       color:' . get_option( 'lty_settings_single_product_ticket_number_color', '#444444' ) . ' !important;
                    }
                }
                  
                .lty-shop-timer-section {
                color: ' . get_option( 'lty_settings_timer_label_color', '#3c763d' ) . '; '
					. '}' . get_option( 'lty_settings_custom_css' );

			wp_register_style('lty-inline-style', false, array(), LTY_VERSION); // phpcs:ignore
			wp_enqueue_style( 'lty-inline-style' );

			// Add custom css as inline style.
			wp_add_inline_style( 'lty-inline-style', $contents );
		}

		/**
		 * Get asset URL.
		 *
		 * @since 10.1.0
		 * @param string $path Assets path.
		 * @return string
		 */
		private static function get_asset_url( $path ) {
			/**
			 * This hook is used to alter the asset URL.
			 *
			 * @since 10.1.0
			 */
			return apply_filters( 'lty_get_asset_url', LTY_PLUGIN_URL . '/' . $path, $path );
		}
	}

	LTY_Frontend_Assets::init();
}
