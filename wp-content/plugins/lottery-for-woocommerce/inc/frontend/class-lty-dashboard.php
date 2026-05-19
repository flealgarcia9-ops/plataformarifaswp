<?php

/**
 * Dashboard
 */
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}
if (!class_exists('LTY_Dashboard')) {

	/**
	 *  Class.
	 */
	class LTY_Dashboard {

		/**
		 * Init
		 */
		public static function init() {
			//Render Title.
			add_action('lty_before_dashboard_contents', array( __CLASS__, 'render_dashboard_title' ));
			//Render Dashboard Navigation.
			add_action('lty_before_dashboard_contents', array( __CLASS__, 'render_dashboard_navigation' ), 20);
			//Render Dashboard Menu Contents .
			add_action('lty_dashboard_contents', array( __CLASS__, 'render_dashboard_menu_contents' ), 10);
		}

		/**
		 * Output the dashboard.
		 */
		public static function output() {
			global $current_lottery_menu;

			$participated_lotteries_url_param = lty_get_dashboard_participated_lotteries_endpoint_url();

			$current_lottery_menu = isset($_REQUEST['lty_dashboard_menu']) ? wc_clean(wp_unslash($_REQUEST['lty_dashboard_menu'])) : $participated_lotteries_url_param;

			lty_get_template('dashboard/dashboard.php');
		}

		/**
		 * Render Dashboard Title.
		 */
		public static function render_dashboard_title() {
			echo '<h2>' . wp_kses_post( lty_get_dashboard_my_lottery_label() ) . '</h2>';
		}

		/**
		 * Render Dashboard Navigation.
		 */
		public static function render_dashboard_navigation() {
			lty_get_template( 'dashboard/navigation.php' );
		}

		/**
		 * Render Dashboard Menu Contents.
		 */
		public static function render_dashboard_menu_contents() {
			global $current_lottery_menu;

			$participated_lotteries_url_param = lty_get_dashboard_participated_lotteries_endpoint_url();
			$won_lotteries_url_param          = lty_get_dashboard_won_lotteries_endpoint_url();
			$not_won_lotteries_url_param      = lty_get_dashboard_not_won_lotteries_endpoint_url();
			$instant_win_url_param            = lty_get_dashboard_instant_win_endpoint_url();

			if (empty($current_lottery_menu)) {
				$current_lottery_menu = isset($_REQUEST['lty_dashboard_menu']) ? wc_clean(wp_unslash($_REQUEST['lty_dashboard_menu'])) : $participated_lotteries_url_param;
			}

			$table_args = self::populate_data();

			switch ($current_lottery_menu) {
				case $won_lotteries_url_param:
					lty_get_template('dashboard/won-lottery-products-layout.php', $table_args);
					break;

				case $not_won_lotteries_url_param:
					lty_get_template('dashboard/not-won-lottery-products-layout.php', $table_args);
					break;

				case $instant_win_url_param:
					lty_get_template( 'dashboard/instant-win-layout.php', $table_args );
					break;

				default:
					lty_get_template('dashboard/my-lottery-products-layout.php', $table_args);
					break;
			}
		}

		/**
		 * Populate Data.
		 */
		public static function populate_data( $current_page = false, $lottery_menu = false ) {
			global $current_lottery_menu, $sitepress;
			if (!$lottery_menu) {
				$lottery_menu = &$current_lottery_menu;
			}

			$post_ids=array();
			$args = self::get_posts_args($lottery_menu);
			if (lty_check_is_array($args)) {
				$args = array_merge(self::get_default_args(), $args);
				$post_ids = get_posts($args);
			}

			/**
			 * This hook is used to alter the lottery product IDs in the dashboard.
			 * 
			 * @since 1.0
			 */
			$post_ids                    = apply_filters( 'lty_lottery_product_ids_in_dashboard', $post_ids );
			$not_won_lotteries_url_param = lty_get_dashboard_not_won_lotteries_endpoint_url();
			if ( $not_won_lotteries_url_param == $lottery_menu ) {
				$post_ids = lty_get_my_lost_lottery_ticket_from_product_id( $post_ids );
			}

			$post_per_page = get_option('lty_settings_lottery_dashboard_per_page', 10);
			if (!$current_page) {
				$current_page = isset($_REQUEST['page_no']) ? wc_clean(wp_unslash(absint($_REQUEST['page_no']))) : '1';
			}

			$offset = ( $post_per_page * $current_page ) - $post_per_page;
			$page_count = ceil(count($post_ids) / $post_per_page);

			return array(
				'post_ids' => array_slice($post_ids, $offset, $post_per_page),
				'pagination' => lty_prepare_pagination_arguments($current_page, $page_count),
				'current_lottery_menu' => $lottery_menu,
			);
		}

		/**
		 * Get Default Arguments.
		 */
		public static function get_default_args() {
			return array(
				'post_type' => 'product',
				'post_status' => 'publish',
				'posts_per_page' => -1,
				'fields' => 'ids',
			);
		}

		/**
		 * Get Posts Arguments.
		 */
		public static function get_posts_args( $menu_name ) {
			$args = array();
			$user_id = get_current_user_id();
			$won_lotteries_url_param     = lty_get_dashboard_won_lotteries_endpoint_url();
			$not_won_lotteries_url_param = lty_get_dashboard_not_won_lotteries_endpoint_url();
			$instant_win_url_param       = lty_get_dashboard_instant_win_endpoint_url();
		
			switch ($menu_name) {
				case $won_lotteries_url_param:
					$product_ids = lty_get_my_winner_lotteries($user_id);
					if (!lty_check_is_array($product_ids)) {
						return array();
					}

					$args = array(
						'post_type' => 'lty_lottery_winner',
						'post_status' => 'lty_publish',
						'post_parent__in' => $product_ids,
						'meta_key' => 'lty_user_id',
						'meta_value' => $user_id,
					);

					break;

				case $not_won_lotteries_url_param:
					$product_ids = lty_get_my_lotteries($user_id);

					if (!lty_check_is_array($product_ids)) {
						return array();
					}

					$args = array(
						'meta_query' => array(
							array(
								'key' => '_lty_closed',
								'compare' => 'EXISTS',
							),
						),
						'post__in' => $product_ids,
						'tax_query' => array(
							array(
								'taxonomy' => 'product_type',
								'field' => 'name',
								'terms' => 'lottery',
							),
						),
					);
					break;

				case $instant_win_url_param:
					$args = array(
						'post_type'   => LTY_Register_Post_Types::LOTTERY_INSTANT_WINNER_LOG_POSTTYPE,
						'post_status' => 'lty_won',
						'meta_key'    => 'lty_user_id',
						'meta_value'  => $user_id,
					);
					break;

				default:
					$product_ids = lty_get_my_lotteries($user_id, array( 'lty_ticket_buyer', 'lty_ticket_winner' ));
					if (!lty_check_is_array($product_ids)) {
						return array();
					}

					$args = array(
					'post__in' => $product_ids,
						'tax_query' => array(
							array(
								'taxonomy' => 'product_type',
								'field' => 'name',
								'terms' => 'lottery',
							),
						),
					);
					break;
			}

			return $args;
		}
	}

	LTY_Dashboard::init();
}
