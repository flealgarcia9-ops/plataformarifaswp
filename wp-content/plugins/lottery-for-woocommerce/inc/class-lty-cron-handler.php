<?php

/**
 * Handles the Cron.
 * */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'LTY_Cron_Handler' ) ) {

	/**
	 * Class.
	 * */
	class LTY_Cron_Handler {

		/**
		 *  Class initialization.
		 * */
		public static function init() {

			// Maybe set the WP schedule event.
			add_action( 'init', array( __CLASS__, 'maybe_set_wp_schedule_event' ), 10 );
			// Maybe set the lottery WP cron schedules.
			add_filter( 'cron_schedules', array( __CLASS__, 'maybe_set_wp_cron_schedules' ) );
			// Handle the WP Cron.
			add_action( 'lty_lottery_cronjob', array( __CLASS__, 'handle_wp_cron' ) );
			// Handle the Server Cron.
			add_action( 'init', array( __CLASS__, 'handle_server_cron' ), 10 );
			// Handle the relist lottery WP Cron.
			add_action( 'lty_lottery_relist_cronjob', array( __CLASS__, 'handle_wp_cron_relist_lottery' ) );
			// Handle the ending soon lottery WP Cron.
			add_action('lty_lottery_ending_soon_cronjob', array( __CLASS__, 'handle_wp_cron_ending_soon_lottery' ));
			// Schedule the lottery started emails for the users
			add_action( 'lty_lottery_after_started', array( __CLASS__, 'schedule_lottery_started_emails' ), 10, 1 );
			add_action( 'lty_lottery_after_relisted', array( __CLASS__, 'schedule_lottery_started_emails' ), 10, 1 );
		}

		/**
		 * Maybe set the WP schedule event.
		 *
		 * @return void.
		 * */
		public static function maybe_set_wp_schedule_event() {

			// Check if the WP cron is enabled. if not clear the lottery schedule event hook.
			if ( '2' != get_option( 'lty_settings_cron_type_selection' ) ) {
				wp_clear_scheduled_hook( 'lty_lottery_cronjob' );
				wp_clear_scheduled_hook( 'lty_lottery_relist_cronjob' );
				wp_clear_scheduled_hook( 'lty_lottery_ending_soon_cronjob' );
				return;
			}

			// Check if the lottey schedule event is already not exists.
			if ( ! wp_next_scheduled( 'lty_lottery_cronjob' ) ) {
				// Add the lottery schedule event.
				wp_schedule_event( time(), 'lty_lottery_interval', 'lty_lottery_cronjob' );
			}

			// Check if the lottey schedule event is already not exists.
			if ( ! wp_next_scheduled( 'lty_lottery_relist_cronjob' ) ) {
				// Add the lottery relist schedule event.
				wp_schedule_event( time(), 'lty_lottery_relist_interval', 'lty_lottery_relist_cronjob' );
			}

			// Check if the lottey ending soon schedule event is already not exists.
			if ( ! wp_next_scheduled( 'lty_lottery_ending_soon_cronjob' ) ) {
				// Add the lottery ending soon schedule event.
				wp_schedule_event( time(), 'lty_lottery_ending_soon_interval', 'lty_lottery_ending_soon_cronjob' );
			}
		}

		/**
		 * Maybe set the lottery WP cron schedules.
		 *
		 * @return array.
		 * */
		public static function maybe_set_wp_cron_schedules( $schedules ) {
			// Check if the WP cron is enabled.
			if ( '2' != get_option( 'lty_settings_cron_type_selection' ) ) {
				return $schedules;
			}

			// Add the lottery interval.
			$schedules['lty_lottery_interval']        = self::get_wp_cron_schedules();
			$schedules['lty_lottery_relist_interval'] = self::get_wp_cron_schedules( 'relist' );
			$schedules['lty_lottery_ending_soon_interval'] = self::get_wp_cron_schedules( 'ending_soon' );

			return $schedules;
		}

		/**
		 * Get the WP cron schedules.
		 *
		 * @return array.
		 * */
		public static function get_wp_cron_schedules( $type = 'update' ) {
			$cron_type = '';
			$cron_time = '';
			switch ( $type ) {
				case 'update':
					$cron_settings = get_option( 'lty_settings_wp_cron_time' );
					$cron_type     = isset( $cron_settings['unit'] ) ? $cron_settings['unit'] : 'minutes';
					$cron_time     = isset( $cron_settings['number'] ) ? intval( $cron_settings['number'] ) : 5;
					break;
				case 'relist':
					$cron_settings = get_option( 'lty_settings_relist_wp_cron_time' );
					$cron_type     = isset( $cron_settings['unit'] ) ? $cron_settings['unit'] : 'minutes';
					$cron_time     = isset( $cron_settings['number'] ) ? intval( $cron_settings['number'] ) : 5;
					break;
				case 'ending_soon':
					$cron_settings = get_option( 'lty_settings_ending_soon_wp_cron_time' );
					$cron_type     = isset( $cron_settings['unit'] ) ? $cron_settings['unit'] : 'minutes';
					$cron_time     = isset( $cron_settings['number'] ) ? intval( $cron_settings['number'] ) : 5;
					break;
			}

			switch ( $cron_type ) {
				case 'hours':
					$interval = $cron_time * 3600;
					$display  = __( 'Hours', 'lottery-for-woocommerce' );
					break;

				case 'days':
					$interval = $cron_time * 86400;
					$display  = __( 'Days', 'lottery-for-woocommerce' );
					break;

				default:
					$interval = $cron_time * 60;
					$display  = __( 'Minutes', 'lottery-for-woocommerce' );
					break;
			}

			return array(
				'interval' => $interval,
				'display'  => $display,
			);
		}

		/**
		 * Handles the WP cron.
		 *
		 * @return void.
		 * */
		public static function handle_wp_cron() {

			// Return if the WP cron is not enabled.
			if ( '2' != get_option( 'lty_settings_cron_type_selection' ) ) {
				return;
			}

			// Update the WP cron current date.
			update_option( 'lty_update_wp_cron_last_updated_date', LTY_Date_Time::get_mysql_date_time_format( 'now', true ) );

			// May be handle the lottery.
			self::maybe_handle_lottery();
		}

		/**
		 * Handles the server cron.
		 *
		 * @since 7.5.0
		 *
		 * @return void.
		 * */
		public static function handle_server_cron() {
			// Handle the Server Cron update lottery.
			self::handle_server_cron_update_lottery();
			// Handle the Server Cron relist lottery.
			self::handle_server_cron_relist_lottery();
			// Handle the Server Cron ending soon lottery.
			self::handle_server_cron_ending_soon_lottery();
		}

		/**
		 * Handle server cron update lottery
		 *
		 * @since 7.5.0
		 *
		 * @return void
		 */
		public static function handle_server_cron_update_lottery() {
			// Return if the cron is not triggered.
			if ( ! isset( $_REQUEST['lty_lottery_cron'] ) ) {
				return;
			}

			$cron_type = wc_clean( wp_unslash( $_REQUEST['lty_lottery_cron'] ) );
			// Return if the cron type is not matched.
			if ( ! $cron_type || 'update' != $cron_type ) {
				return;
			}

			// Return if the server cron is not enabled.
			if ( '2' == get_option( 'lty_settings_cron_type_selection' ) ) {
				return;
			}

			// Update the server cron current date.
			update_option( 'lty_update_server_cron_last_updated_date', LTY_Date_Time::get_mysql_date_time_format( 'now', true ) );

			// May be handle the lottery.
			self::maybe_handle_lottery();
		}

		/**
		 * Handle server cron relist lottery
		 *
		 * @since 7.5.0
		 * @return void
		 */
		public static function handle_server_cron_relist_lottery() {
			// Return if the cron is not triggered.
			if ( ! isset( $_REQUEST['lty_lottery_cron'] ) ) {
				return;
			}

			$cron_type = wc_clean( wp_unslash( $_REQUEST['lty_lottery_cron'] ) );
			// Return if the cron type is not matched.
			if ( ! $cron_type || 'relist' !== $cron_type ) {
				return;
			}

			// Return if the server cron is not enabled.
			if ( '2' === get_option( 'lty_settings_cron_type_selection' ) ) {
				return;
			}

			// Update the server cron current date.
			update_option( 'lty_relist_server_cron_last_updated_date', LTY_Date_Time::get_mysql_date_time_format( 'now', true ) );

			// May be handle automatic relist.
			self::handle_automatic_relist();
		}

		/**
		 * Handle server cron ending soon lottery.
		 *
		 * @since 12.4.0
		 */
		public static function handle_server_cron_ending_soon_lottery() {
			// Return if cron is not trigger.
			if (!isset($_REQUEST['lty_lottery_cron'])) {
				return;
			}

			$cron_type = wc_clean(wp_unslash($_REQUEST['lty_lottery_cron']));
			// Return if cron is not trigger.
			if (!$cron_type || 'ending_soon' != $cron_type) {
				return;
			}

			// Update current date. 
			update_option('lty_ending_soon_server_cron_last_updated_date', LTY_Date_Time::get_mysql_date_time_format('now', true));

			self::handle_ending_soon_lottery();

			/**
			 * This hook is used to do extra action server cron lottery ending soon.
			 * 
			 * @since 12.4.0
			 */
			do_action('lty_lottery_server_cron_ending_soon');
		}

		/**
		 * Handles the WP cron relist lottery.
		 *
		 * @since 7.5.0
		 *
		 * @return void.
		 * */
		public static function handle_wp_cron_relist_lottery() {
			// Return if the WP cron is not enabled.
			if ( '2' != get_option( 'lty_settings_cron_type_selection' ) ) {
				return;
			}

			// Update the WP cron current date.
			update_option( 'lty_relist_wp_cron_last_updated_date', LTY_Date_Time::get_mysql_date_time_format( 'now', true ) );

			// May be handle automatic relist.
			self::handle_automatic_relist();
		}

		/**
		 * Handles the WP cron ending soon lottery.
		 *
		 * @since 12.4.0
		 * */
		public static function handle_wp_cron_ending_soon_lottery() {
			// Return if the WP cron is not enabled.
			if ('2' != get_option('lty_settings_cron_type_selection')) {
				return;
			}

			// Update the WP cron current date. 
			update_option('lty_ending_soon_wp_cron_last_updated_date', LTY_Date_Time::get_mysql_date_time_format('now', true));

			self::handle_ending_soon_lottery();
		}

		/**
		 * Handles ending soon lottery. 
		 * 
		 * @since 12.4.0
		 * */
		public static function handle_ending_soon_lottery() {
			// Return if the ending soon email scheduler interval is not set.
			if (empty(lty_get_remainder_email_scheduler_time())) {
				return;
			}

			// Return if there are no lottery products are started.
			if (!lty_check_is_array(self::get_started_lottery_product_ids())) {
				return;
			}

			foreach (self::get_started_lottery_product_ids() as $lottery_product_id) {

				$product = wc_get_product($lottery_product_id);
				if (!is_object($product)) {
					continue;
				}

				// Skip if the product is unlimited scheduled.
				if ( $product->is_unlimited_scheduled_lottery()) {
					continue;
				}

				// Skip if the product is reached ending soon period.
				if ( !$product->is_product_reached_ending_soon_time()) {
					continue;
				}

				/**
				 * This hook is used to do extra action ending soon lottery product.
				 * 
				 * @hooked LTY_Customer_Lottery_Ending_Soon_Notification::trigger - 10 
				 * @since 12.4.0
				 */
				do_action('lty_lottery_ending_soon_product', $lottery_product_id, $product);
			}
		}

		/**
		 * Get started lottery product ids.
		 *
		 * @return array
		 * */
		public static function get_started_lottery_product_ids() {

			$lottery_ids = get_posts(
				array(
					'post_type'      => 'product',
					'post_status'    => array( 'publish', 'private' ),
					'posts_per_page' => '-1',
					'fields'         => 'ids',
					'tax_query'      => array(
						array(
							'taxonomy' => 'product_type',
							'field'    => 'name',
							'terms'    => 'lottery',
						),
					),
					'meta_query' => array(
						'relation' => 'AND',
						array(
							'key' => '_lty_lottery_status',
							'value' => 'lty_lottery_started',
							'compare' => '=',
						),
						array(
							'key' => '_lty_ending_soon_user_email_sent',
							'compare' => 'NOT EXISTS',
						),
					),
				)
			);

			return lty_check_is_array( $lottery_ids ) ? $lottery_ids : array();
		}

		/**
		 * May be Handle the lottery.
		 *
		 * @return void.
		 * */
		public static function maybe_handle_lottery() {

			$args = array(
				'post_type'      => 'product',
				'post_status'    => array( 'publish', 'private' ),
				'posts_per_page' => '-1',
				'fields'         => 'ids',
				'tax_query'      => array(
					array(
						'taxonomy' => 'product_type',
						'field'    => 'name',
						'terms'    => 'lottery',
					),
				),
				'meta_query'     => array(
					array(
						'key'     => '_lty_closed',
						'compare' => 'NOT EXISTS',
					),
				),
			);

			$post_ids = get_posts( $args );

			// Return if the post ids is not exists.
			if ( ! lty_check_is_array( $post_ids ) ) {
				return;
			}

			foreach ( $post_ids as $post_id ) {
				$product = wc_get_product( $post_id );

				// Return if the id is not a product or lottery product type.
				if ( ! is_object( $product ) || ! $product->exists() || 'lottery' != $product->get_type() ) {
					return;
				}

				// Update status in lottery.
				if ( $product->has_lottery_status( 'lty_lottery_not_started' ) && $product->is_started() && ! $product->is_closed() ) {
					// Start the Lottery.
					LTY_Lottery_Handler::start_lottery( $post_id, $product );
				} elseif ( $product->is_started() && $product->is_closed() && ! $product->has_lottery_status( 'lty_lottery_finished' ) ) {
					// Close the Lottery.
					LTY_Lottery_Handler::end_lottery( $post_id, $product );
				}
			}
		}

		/**
		 * Handle the automatic relist.
		 *
		 * @since 7.5.0
		 *
		 * @return void
		 * */
		public static function handle_automatic_relist() {
			$product_ids = self::get_automatic_relist_product_ids();

			// Return if no product ids.
			if ( ! lty_check_is_array( $product_ids ) ) {
				return;
			}

			$automatic = false;
			foreach ( $product_ids as $product_id ) {
				$product = wc_get_product( $product_id );

				// Return if given object is not a product or lottery product type.
				if ( ! is_object( $product ) || ! $product->exists() || 'lottery' != $product->get_type() ) {
					continue;
				}

				// Return if the lottery is not a automatic relist.
				if ( ! self::is_automatic_relist_lottery( $product ) ) {
					continue;
				}

				$automatic = true;
				$product->update_automatic_relisted_count();

				// Relist the lottery.
				LTY_Lottery_Handler::relist_lottery( $product_id, $product, $automatic );
			}
		}

		/**
		 * Is automatic relist lottery.
		 *
		 * @since 7.5.0
		 * @param object $product
		 *
		 * @return bool
		 * */
		public static function is_automatic_relist_lottery( $product ) {
			$bool = false;
			switch ( $product->get_lty_lottery_status() ) {
				case 'lty_lottery_finished':
					$bool = self::is_automatic_relist_finished_lottery( $product ) ? true : false;
					break;
				case 'lty_lottery_failed':
					$bool = self::is_automatic_relist_failed_lottery( $product ) ? true : false;
					break;
			}

			return $bool;
		}

		/**
		 * Is automatic relist finished lottery.
		 *
		 * @since 7.5.0
		 * @param object $product
		 *
		 * @return bool
		 * */
		public static function is_automatic_relist_finished_lottery( $product ) {
			// Return if finished lottery relist enable.
			if ( 'yes' !== $product->get_lty_relist_finished_lottery() ) {
				return false;
			}

			if ( '2' === $product->get_lty_finished_lottery_relist_count_type() && intval( $product->get_lty_finished_lottery_relist_count() ) < intval( $product->get_lty_finished_relisted_count() ) ) {
				return false;
			}

			$current_date_object            = LTY_Date_Time::get_gmt_date_time_object( 'now' );
			$finished_date_object           = LTY_Date_Time::get_gmt_date_time_object( $product->get_lty_finished_date_gmt() );
			$finished_relist_pause_duration = $product->get_lty_finished_lottery_relist_pause_duration();

			// Return true, if relist pause time is not enabled or invalid pause time.
			if ( 'yes' !== $product->get_lty_finished_lottery_relist_pause() || ! lty_is_valid_duration( $finished_relist_pause_duration ) ) {
				return true;
			}

			/* translators: %1$s:number, %2$s:unit */
			$finished_date_object->modify( sprintf( __( '+%1$s %2$s', 'lottery-for-woocommerce' ), $finished_relist_pause_duration['number'], $finished_relist_pause_duration['unit'] ) );
			// Return true, if current time is reached the lottery relist pause time.
			if ( $current_date_object >= $finished_date_object ) {
				return true;
			}
			return false;
		}

		/**
		 * Automatic relist failed lottery .
		 *
		 * @since 7.5.0
		 * @param object $product
		 *
		 * @return bool
		 * */
		public static function is_automatic_relist_failed_lottery( $product ) {
			// Return if failed lottery relist enable.
			if ( 'yes' !== $product->get_lty_relist_failed_lottery() ) {
				return false;
			}

			if ( '2' === $product->get_lty_failed_lottery_relist_count_type() && intval( $product->get_lty_failed_lottery_relist_count() ) < intval( $product->get_lty_failed_relisted_count() ) ) {
				return false;
			}

			$current_date_object          = LTY_Date_Time::get_gmt_date_time_object( 'now' );
			$failed_date_object           = LTY_Date_Time::get_gmt_date_time_object( $product->get_lty_failed_date_gmt() );
			$failed_relist_pause_duration = $product->get_lty_failed_lottery_relist_pause_duration();

			// Return true, if relist pause time is not enabled or invalid pause time.
			if ( 'yes' !== $product->get_lty_failed_lottery_relist_pause() || ! lty_is_valid_duration( $failed_relist_pause_duration ) ) {
				return true;
			}

			/* translators: %1$s: number, %2$s: unit */
			$failed_date_object->modify( sprintf( __( '+%1$s %2$s', 'lottery-for-woocommerce' ), $failed_relist_pause_duration['number'], $failed_relist_pause_duration['unit'] ) );
			// Return true, if current time is reached the lottery relist pause time.
			if ( $current_date_object >= $failed_date_object ) {
				return true;
			}
			return false;
		}

		/**
		 * Get automatic relist product ids.
		 *
		 * @since 7.5.0
		 *
		 * @return array.
		 * */
		public static function get_automatic_relist_product_ids() {
			$args = array(
				'post_type'      => 'product',
				'post_status'    => array( 'publish', 'private' ),
				'posts_per_page' => '-1',
				'fields'         => 'ids',
				'tax_query'      => array(
					array(
						'taxonomy' => 'product_type',
						'field'    => 'name',
						'terms'    => 'lottery',
					),
				),
				'meta_query'     => array(
					'relation' => 'AND',
					array(
						'relation' => 'OR',
						array(
							'key'   => '_lty_relist_finished_lottery',
							'value' => 'yes',
						),
						array(
							'key'   => '_lty_relist_failed_lottery',
							'value' => 'yes',
						),
					),
					array(
						'key'     => '_lty_closed',
						'compare' => 'EXISTS',
					),
					array(
						'key'   => '_lty_lottery_status',
						'value' => lty_supported_automatic_relist_statuses(),
					),
				),
			);

			return get_posts( $args );
		}

		/**
		 * Schedule the lottery started emails for users.
		 *
		 * @since 7.0
		 * */
		public static function schedule_lottery_started_emails( $product_id, $force = false ) {
			$product = wc_get_product( $product_id );
			if ( ! is_object( $product ) || 'publish' !== $product->get_status() ) {
				return;
			}

			// Return if the lottery status is started status.
			if ( 'lty_lottery_started' !== $product->get_lty_lottery_status() ) {
				return;
			}

			// Return if the emails is not enabled.
			if ( 'yes' !== get_option( 'lty_customer_lottery_started_enabled' ) && ! $force ) {
				return;
			}

			$user_ids = get_users( array( 'fields' => 'ids' ) );
			$user_ids = array_filter( array_chunk( $user_ids, 200 ) );

			foreach ( $user_ids as $count => $chunked_user_ids ) {
				as_schedule_single_action(
					time() + $count,
					'lty_lottery_started_emails',
					array(
						'user_ids'   => $chunked_user_ids,
						'product_id' => $product->get_id(),
						'force'      => $force,
					)
				);
			}
		}
	}

	LTY_Cron_Handler::init();
}
