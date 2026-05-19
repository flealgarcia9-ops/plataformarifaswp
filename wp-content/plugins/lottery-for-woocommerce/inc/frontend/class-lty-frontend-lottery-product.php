<?php
/**
 * Frontend Lottery Product.
 *
 * @since 1.0.0
 * */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'LTY_Frontend_Lottery_Product' ) ) {

	/**
	 * Class.
	 * */
	class LTY_Frontend_Lottery_Product {

		/**
		 * Class Initialization.
		 * */
		public static function init() {
			// Product Page hooks.
			// Set a guest cookie.
			add_action( 'template_redirect', array( __CLASS__, 'set_guest_cookie' ), 20 );
			// Track Product View.
			add_action( 'template_redirect', array( __CLASS__, 'track_product_view' ), 30 );
			// May be add the lottery single_product notice.
			add_action( 'woocommerce_before_single_product', array( __CLASS__, 'maybe_lottery_single_product_notice' ) );
			// Add the extra product tabs.
			add_filter( 'woocommerce_product_tabs', array( __CLASS__, 'add_extra_product_tabs' ) );

			// Shop Loop Item hooks.
			// Render badge in shop page.
			add_filter( 'woocommerce_before_shop_loop_item', array( __CLASS__, 'render_badge' ), 5 );
			// Display ticket status in shop page.
			add_action( 'woocommerce_after_shop_loop_item_title', array( __CLASS__, 'render_ticket_status' ), 20 );
			// Display countdown timer in shop page.
			add_action( 'woocommerce_after_shop_loop_item_title', array( __CLASS__, 'render_countdown_timer' ), 20 );
			// Display Progress Bar in shop & category pages.
			add_action( 'woocommerce_after_shop_loop_item_title', array( __CLASS__, 'render_progress_bar_template' ), 20 );
			// Render the remaining tickets message.
			add_action( 'woocommerce_after_shop_loop_item_title', array( __CLASS__, 'render_remaining_tickets_template' ), 20 );
			// Display the short description in shop & category pages.
			add_action( 'woocommerce_after_shop_loop_item_title', array( __CLASS__, 'render_short_description_template' ), 20 );
			// Process generate add to cart url.
			add_action( 'wp', array( __CLASS__, 'generate_add_to_cart_url' ) );

			// Checkout hooks.
			// Force Signup if Guests placing the lottery product order.
			add_filter( 'woocommerce_checkout_process', array( __CLASS__, 'force_create_account_for_guest' ), 10 );
			add_filter( 'woocommerce_checkout_registration_enabled', array( __CLASS__, 'force_enable_guest_signup_on_checkout' ), 10, 1 );
			add_filter( 'woocommerce_checkout_registration_required', array( __CLASS__, 'force_enable_guest_signup_on_checkout' ), 10, 1 );
		}

		/**
		 * May be set a guest cookie.
		 *
		 * @since 6.7
		 * */
		public static function set_guest_cookie() {
			// is_singular($post_types).
			if ( ! is_singular( 'product' ) ) {
				return;
			}

			global $post;
			if ( ! is_object( $post ) ) {
				return;
			}

			if ( is_user_logged_in() ) {
				return;
			}

			$product = wc_get_product( $post->ID );
			if ( 'lottery' !== $product->get_type() ) {
				return;
			}

			// Sets the session cookie.
			if ( ! headers_sent() && did_action( 'wp_loaded' ) ) {
				WC()->session->set_customer_session_cookie( true );
			}
		}

		/**
		 * Track the Lottery product view.
		 *
		 * @return void
		 * */
		public static function track_product_view() {
			// is_singular($post_types).
			// is_active_widget( $callback,$widget_id,$id_base,$skip_inactive).
			if ( ! is_singular( 'product' ) || ! is_active_widget( false, false, 'lty_recent_viewed_lottery', true ) ) {
				return;
			}

			global $post;

			if ( empty( $_COOKIE['lty_recent_viewed_lottery'] ) ) {
				$viewed_products = array();
			} else {
				$viewed_products = wp_parse_id_list((array) explode('|', wc_clean(wp_unslash($_COOKIE['lty_recent_viewed_lottery'])))); // @codingStandardsIgnoreLine. ;
			}

			$product = wc_get_product( $post->ID );
			if ( 'lottery' !== $product->get_type() ) {
				return;
			}
			// Unset if already in viewed products list.
			// array_flip — Exchanges all keys with their associated values in an array.
			$keys = array_flip( $viewed_products );

			if ( isset( $keys[ $post->ID ] ) ) {
				unset( $viewed_products[ $keys[ $post->ID ] ] );
			}

			$viewed_products[] = $post->ID;

			if ( count( $viewed_products ) > 15 ) {
				array_shift( $viewed_products );
			}

			// Set the cookie for recent viewed products.
			setcookie( 'lty_recent_viewed_lottery', implode( '|', $viewed_products ), 0, COOKIEPATH ? COOKIEPATH : '/', COOKIE_DOMAIN, is_ssl(), true );
		}

		/**
		 * May be add the lottery single_product notice.
		 *
		 * @return void
		 * */
		public static function maybe_lottery_single_product_notice() {
			// Check the current page is singular page.
			if ( ! is_product() ) {
				return;
			}

			global $product;

			if ( ! lty_is_lottery_product( $product ) || $product->is_closed() ) {
				return;
			}

			self::guest_user_notice();

			self::user_lottery_purchased_notice();

			self::maximum_tickets_purchase_limit_reached_notice();
		}

		/**
		 * The guest user notice.
		 *
		 * @return void
		 * */
		public static function guest_user_notice() {
			global $product;

			if ( wp_get_current_user()->exists() ) {
				return;
			}

			if ( '2' != get_option( 'lty_settings_guest_user_participate_type' ) ) {
				return;
			}

			$error_message = get_option( 'lty_settings_single_product_guest_error_message' );
			$login_url     = '<a href="' . esc_url( wc_get_page_permalink( 'myaccount' ) ) . '" class="button">' . __( 'Login', 'lottery-for-woocommerce' ) . '</a>';

			// Error Notice.
			wc_add_notice( $error_message . $login_url, 'error' );
		}

		/**
		 *  Display the user current lottery purchased ticket notice.
		 *
		 * @return void
		 */
		public static function user_lottery_purchased_notice() {

			global $product;

			if ( ! wp_get_current_user()->exists() ) {
				return;
			}

			// return if the user is not placed the lottery.
			if ( empty( $product->get_user_purchased_ticket_count() ) ) {
				return;
			}

			/* translators: %d: User Limit */
			$message = sprintf( get_option( 'lty_settings_purchased_tickets_message'), $product->get_user_purchased_ticket_count() );

			wc_add_notice( $message, 'success' );
		}

		/**
		 *  Display the maximum tickets purchase limit reached notice.
		 *
		 * @return void
		 */
		public static function maximum_tickets_purchase_limit_reached_notice() {
			global $product;

			if ( ! wp_get_current_user()->exists() ) {
				return;
			}

			// return if the user purchase limit is not reached.
			if ( ! lty_is_lottery_product( $product ) || ! $product->user_purchase_limit_exists() || $product->is_closed() ) {
				return;
			}

			wc_add_notice( lty_get_lottery_maximum_tickets_purchase_limit_error_message( $product ), 'error' );
		}

		/**
		 * Add the extra product tabs.
		 *
		 * @since 1.0.0
		 * @param array $tabs Product tabs.
		 * @global object $product Product object.
		 * @return array
		 * */
		public static function add_extra_product_tabs( $tabs ) {
			global $product;

			if ( ! lty_is_lottery_product( $product ) ) {
				return $tabs;
			}

			if ( '2' !== get_option( 'lty_settings_single_product_tab_details_toggle', '1' ) ) {
				$tabs['lty_ticket_logs'] = array(
					'title'    => get_option( 'lty_settings_single_product_tab_lottery_details_label' ),
					'priority' => 13,
					'callback' => array( __CLASS__, 'render_ticket_logs_tab_content' ),
				);
			}

			if ( 'yes' !== get_option( 'lty_settings_instant_winners_tab_enabled', 'yes' ) && $product->is_instant_winner() ) {
				$tabs['lty_instant_winners'] = array(
					'title'    => get_option( 'lty_settings_instant_winners_tab_label', 'Lottery Instant Winners' ),
					'priority' => 13,
					'callback' => array( __CLASS__, 'render_instant_winners_prizes_tab_content' ),
				);
			}

			/**
			 * This hook is used to alter the lottery product tabs.
			 *
			 * @since 6.7.0
			 * @param array $tabs Product tabs.
			 */
			return apply_filters( 'lty_lottery_product_tabs', $tabs );
		}

		/**
		 * Render tickets logs tab content.
		 * */
		public static function render_ticket_logs_tab_content() {
			global $product;
			if ( ! lty_is_lottery_product( $product ) ) {
				return;
			}

			lty_get_template( 'single-product/tabs/ticket-logs-layout.php', lty_prepare_ticket_logs_template_arguments( $product ) );
		}

		/**
		 * Render Instant Winners Prizes Tab Content.
		 *
		 * @since 8.0.0
		 * */
		public static function render_instant_winners_prizes_tab_content() {
			global $product;
			if ( ! lty_is_lottery_product( $product ) || $product->has_lottery_status( 'lty_lottery_failed' ) ) {
				return;
			}

			if ( ! lty_check_is_array( $product->get_current_instant_winner_log_ids() ) ) {
				return;
			}

			if ( '2' === $product->get_lty_instant_winner_display_mode() ) {
				$template = 'single-product/tabs/instant-winner-prize-groups-layout.php';
			} else {
				$template = 'single-product/tabs/instant-winners-logs-layout.php';
			}

			lty_get_template( $template, lty_prepare_instant_winner_logs_arguments( $product ) );
		}

		/**
		 * Render badge in shop page.
		 * */
		public static function render_badge() {
			global $product;

			if ( ! lty_is_lottery_product( $product ) ) {
				return;
			}

			if ( 'no' == get_option( 'lty_settings_enable_lottery_badge' ) ) {
				return;
			}

			lty_get_template( 'loop/badge.php' );
		}

		/**
		 * Render ticket status.
		 * */
		public static function render_ticket_status() {
			global $product;
			if ( ! lty_is_lottery_product( $product ) ) {
				return;
			}

			if ( 'yes' === get_option( 'lty_settings_hide_lottery_status_in_shop' ) ) {
				return;
			}

			lty_get_template( 'loop/ticket-status.php', array( 'product' => $product ) );
		}

		/**
		 * Render count down timer in shop.
		 * */
		public static function render_countdown_timer() {

			global $product;

			if ( ! lty_is_lottery_product( $product ) ) {
				return;
			}

			if ( ! $product->display_countdown_timer_in_shop() ) {
				return;
			}

			if ( '2' === $product->get_lty_lottery_schedule_type() || empty( $product->get_lty_end_date() ) || empty( $product->get_lty_start_date() ) ) {
				return;
			}

			lty_get_template( 'loop/countdown-timer.php' );
		}

		/**
		 * Render progress bar in shop and category pages.
		 *
		 * @since 1.0.0
		 * @return void
		 */
		public static function render_progress_bar_template() {
			global $product;

			if ( ! lty_is_lottery_product( $product ) || $product->is_closed() ) {
				return;
			}

			if ( ! $product->display_progress_bar_in_shop() ) {
				return;
			}

			$args = array(
				'product'                 => $product,
				'progress_bar_percentage' => lty_get_shop_page_progress_bar_percentage( $product ),
			);

			lty_get_template( 'loop/progress-bar.php', $args );
		}

		/**
		 * Render remaining tickets on shop and category pages.
		 *
		 * @since 10.3.0
		 * @global object $product Product object.
		 * @return void
		 */
		public static function render_remaining_tickets_template() {
			if ( ! lty_display_remaining_tickets_message_on_shop() ) {
				return;
			}

			global $product;
			if ( ! lty_is_lottery_product( $product ) || ! $product->is_started() ) {
				return;
			}

			lty_get_template( 'loop/remaining-tickets.php', array( 'product' => $product ) );
		}

		/**
		 * Render the short description in the shop and category pages.
		 *
		 * @since 7.0
		 * */
		public static function render_short_description_template() {
			global $product, $woocommerce_loop;

			if ( ! lty_is_lottery_product( $product ) || $product->is_closed() ) {
				return;
			}

			if ( ! isset( $woocommerce_loop['short_description'] ) ) {
				return;
			}

			lty_get_template( 'loop/short-description.php', array( 'product' => $product ) );
		}

		/**
		 * Force Display Signup on Checkout for Guest when purchasing the lottery product.
		 *
		 * @since 1.0.0
		 * @param bool $bool
		 * @return bool
		 */
		public static function force_enable_guest_signup_on_checkout( $bool ) {
			if ( '1' != get_option( 'lty_settings_guest_user_participate_type' ) ) {
				return $bool;
			}

			if ( is_user_logged_in() || ! lty_is_cart_contains_lottery_items() ) {
				return $bool;
			}

			return true;
		}

		/**
		 * To Create account for Guest when purchasing the lottery product.
		 *
		 * @return void
		 */
		public static function force_create_account_for_guest() {
			if ( ! is_user_logged_in() && '1' == get_option( 'lty_settings_guest_user_participate_type' ) && lty_is_cart_contains_lottery_items() ) {
				$_POST['createaccount'] = 1;
			}
		}

		/**
		 * Generate add to cart URL.
		 *
		 * @since 7.3
		 * @return void
		 */
		public static function generate_add_to_cart_url() {
			$product_id = isset( $_REQUEST['lty_product_id'] ) ? intval( $_REQUEST['lty_product_id'] ) : '';
			$quantity   = isset( $_REQUEST['quantity'] ) ? intval( $_REQUEST['quantity'] ) : '';
			$button_key = isset( $_REQUEST['button_key'] ) ? intval( $_REQUEST['button_key'] ) : 0;

			if ( empty( $product_id ) || empty( $quantity ) || ! isset( $button_key ) ) {
				return;
			}

			try {
				$product = wc_get_product( $product_id );
				// Check if product id is valid.
				if ( ! lty_is_lottery_product( $product ) ) {
					return;
				}

				// Return if manual tickets or predefined button not enabled.
				if ( $product->is_manual_ticket() || ! $product->is_predefined_button_enabled() ) {
					return;
				}

				// Return if not match predefined button rule key.
				if ( ! array_key_exists( $button_key, $product->get_predefined_buttons_rule() ) ) {
					wc_add_notice( 'Invalid Request', 'error' );
					return;
				}

				$cart_item_data = array(
					'lty_lottery' => array(
						'lty_predefined_button_id' => $button_key,
						'lty_product_id'           => $product_id,
						'lty_per_ticket_amount'    => $product->get_predefined_buttons_per_ticket_amount( $button_key ),
						'qty'                      => $quantity,
					),
				);

				// Add Lottery Product in cart.
				WC()->cart->add_to_cart( $product_id, $quantity, 0, array(), $cart_item_data );
				// Redirect to checkout page.
				wp_safe_redirect( wc_get_cart_url() );
				exit();
			} catch ( Exception $ex ) {
				wc_add_notice( $ex->getMessage(), 'error' );
			}

			// Redirect to current single product page.
			wp_safe_redirect( remove_query_arg( array( 'lty_product_id' ), get_permalink( $product_id ) ) );
			exit();
		}
	}

	LTY_Frontend_Lottery_Product::init();
}
