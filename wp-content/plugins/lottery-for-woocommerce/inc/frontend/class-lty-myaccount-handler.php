<?php
/**
 * My Account - Lottery Handler
 *
 * @since 9.1.0
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

if ( ! class_exists( 'LTY_Myaccount_Handler' ) ) {

	/**
	 * Class.
	 *
	 * @since 9.1.0
	 */
	class LTY_Myaccount_Handler {

		/**
		 * Lottery Dashboard menu endpoint.
		 *
		 * @since 9.1.0
		 * @var string
		 */
		public static $lottery_menu_endpoint;

		/**
		 * Class initialization.
		 *
		 * @since 9.1.0
		 */
		public static function init() {
			if ( 'yes' !== get_option( 'lty_settings_enable_myaccount_lottery_menu' ) ) {
				return;
			}

			self::$lottery_menu_endpoint = get_option( 'lty_settings_myaccount_lottery_menu_endpoint_url', 'lottery' );

			// Add custom rewrite endpoint.
			add_action( 'init', array( __CLASS__, 'custom_rewrite_endpoint' ) );
			// Add custom query vars.
			add_filter( 'query_vars', array( __CLASS__, 'custom_query_vars' ), 0 );
			// Add custom myaccount Menu.
			add_filter( 'woocommerce_account_menu_items', array( __CLASS__, 'custom_myaccount_menu' ) );
			// Customize the myaccount menu title.
			add_filter( 'the_title', array( __CLASS__, 'customize_menu_title' ) );
			// Render the lottery dashboard menu content.
			add_action( 'woocommerce_account_' . self::$lottery_menu_endpoint . '_endpoint', array( __CLASS__, 'render' ), 10 );
			// Render myaccount lottery menu title.
			add_action( 'lty_before_myaccount_lottery_contents', array( __CLASS__, 'render_myaccount_lottery_title' ), 10, 1 );
			// Render myaccount lottery menu navigation.
			add_action( 'lty_before_myaccount_lottery_contents', array( __CLASS__, 'render_myaccount_lottery_navigation' ), 20, 1 );
			// Render myaccount lottery menu contents .
			add_action( 'lty_myaccount_lottery_contents', array( __CLASS__, 'render_myaccount_lottery_menu_contents' ), 10, 1 );
		}

		/**
		 * Custom rewrite endpoint.
		 *
		 * @since 9.1.0
		 * @return void
		 */
		public static function custom_rewrite_endpoint() {
			add_rewrite_endpoint( self::$lottery_menu_endpoint, EP_ROOT | EP_PAGES );
		}

		/**
		 * Add custom Query variable.
		 *
		 * @since 9.1.0
		 * @param array $vars Query variables.
		 * @return array
		 */
		public static function custom_query_vars( $vars ) {
			$vars[] = self::$lottery_menu_endpoint;

			return $vars;
		}

		/**
		 * Custom My account menus.
		 *
		 * @since 9.1.0
		 * @param array $menus Existing menus.
		 * @return array
		 */
		public static function custom_myaccount_menu( $menus ) {
			return lty_customize_array_position( $menus, lty_get_myaccount_lottery_menu_position(), array( self::$lottery_menu_endpoint => lty_get_myaccount_lottery_dashboard_menu_label() ) );
		}

		/**
		 * Customize the My account menu title.
		 *
		 * @since 9.1.0
		 * @param string $title My Account Title.
		 * @global object $wp_query WP Query object.
		 * @return string
		 */
		public static function customize_menu_title( $title ) {
			global $wp_query;

			if ( ! is_main_query() || ! in_the_loop() || ! is_account_page() ) {
				return $title;
			}

			if ( ! isset( $wp_query->query_vars[ self::$lottery_menu_endpoint ] ) ) {
				return $title;
			}

			remove_filter( 'the_title', array( __CLASS__, 'customize_menu_title' ) );

			return lty_get_myaccount_lottery_dashboard_menu_label();
		}

		/**
		 * Render the myaccount lottery.
		 *
		 * @since 9.1.0
		 */
		public static function render() {
			$participated_lotteries_url_param = lty_get_dashboard_participated_lotteries_endpoint_url();
			$current_lottery_menu             = isset( $_REQUEST['lty_dashboard_menu'] ) ? wc_clean( wp_unslash( $_REQUEST['lty_dashboard_menu'] ) ) : $participated_lotteries_url_param;

			lty_get_template( 'myaccount/lottery.php', array( 'current_lottery_menu' => $current_lottery_menu ) );
		}

		/**
		 * Render myaccount lottery title.
		 *
		 * @since 1.0.0
		 * @param string $current_lottery_menu Current lottery menu.
		 * @return void
		 */
		public static function render_myaccount_lottery_title( $current_lottery_menu ) {
			echo '<h2>' . wp_kses_post( lty_get_dashboard_my_lottery_label() ) . '</h2>';
		}

		/**
		 * Render myaccount lottery navigation.
		 *
		 * @since 1.0.0
		 * @param string $current_lottery_menu Current lottery menu.
		 * @return void
		 */
		public static function render_myaccount_lottery_navigation( $current_lottery_menu ) {
			lty_get_template( 'myaccount/navigation.php', array( 'current_lottery_menu' => $current_lottery_menu ) );
		}

		/**
		 * Render myaccount lottery menu contents.
		 *
		 * @since 9.1.0
		 * @param string $current_lottery_menu Current lottery menu.
		 * @return void
		 */
		public static function render_myaccount_lottery_menu_contents( $current_lottery_menu ) {
			$participated_lotteries_url_param = lty_get_dashboard_participated_lotteries_endpoint_url();
			$won_lotteries_url_param          = lty_get_dashboard_won_lotteries_endpoint_url();
			$not_won_lotteries_url_param      = lty_get_dashboard_not_won_lotteries_endpoint_url();
			$instant_win_url_param            = lty_get_dashboard_instant_win_endpoint_url();

			if ( ! $current_lottery_menu ) {
				$current_lottery_menu = isset( $_REQUEST['lty_dashboard_menu'] ) ? wc_clean( wp_unslash( $_REQUEST['lty_dashboard_menu'] ) ) : $participated_lotteries_url_param;
			}

			$table_args = self::prepare_myaccount_lottery_template_arguments( false, $current_lottery_menu );
			if ( ! lty_check_is_array( $table_args ) ) {
				esc_html_e( 'No data found', 'lottery-for-woocommerce' );
				return;
			}

			switch ( $current_lottery_menu ) {
				case $won_lotteries_url_param:
					lty_get_template( 'myaccount/won-lottery-products-layout.php', $table_args );
					break;

				case $not_won_lotteries_url_param:
					lty_get_template( 'myaccount/not-won-lottery-products-layout.php', $table_args );
					break;

				case $instant_win_url_param:
					lty_get_template( 'myaccount/instant-win-layout.php', $table_args );
					break;

				default:
					lty_get_template( 'myaccount/participated-lottery-products-layout.php', $table_args );
					break;
			}
		}

		/**
		 * Prepare the myaccount lottery template arguments.
		 *
		 * @since 9.1.0
		 * @param bool $current_page Current page.
		 * @param bool $lottery_menu Lottery menu.
		 * @return array
		 */
		public static function prepare_myaccount_lottery_template_arguments( $current_page = false, $lottery_menu = false ) {
			$args = self::prepare_posts_args( $lottery_menu );
			if ( ! lty_check_is_array( $args ) ) {
				return array();
			}

			$args     = array_merge( self::get_default_args(), $args );
			$post_ids = get_posts( $args );

			/**
			 * This hook is used to alter the lottery product IDs in the myaccount lottery.
			 *
			 * @since 9.1.0
			 */
			$post_ids                    = apply_filters( 'lty_lottery_product_ids_in_myaccount_lottery', $post_ids );
			$not_won_lotteries_url_param = lty_get_dashboard_not_won_lotteries_endpoint_url();
			if ( $not_won_lotteries_url_param === $lottery_menu ) {
				$post_ids = lty_get_my_lost_lottery_ticket_from_product_id( $post_ids );
			}

			$post_per_page = get_option( 'lty_settings_lottery_dashboard_per_page', 10 );
			if ( ! $current_page ) {
				$current_page = isset( $_REQUEST['page_no'] ) ? wc_clean( wp_unslash( absint( $_REQUEST['page_no'] ) ) ) : '1';
			}

			$offset     = ( $post_per_page * $current_page ) - $post_per_page;
			$page_count = ceil( count( $post_ids ) / $post_per_page );

			return array(
				'post_ids'             => array_slice( $post_ids, $offset, $post_per_page ),
				'pagination'           => lty_prepare_pagination_arguments( $current_page, $page_count ),
				'current_lottery_menu' => $lottery_menu,
			);
		}

		/**
		 * Get default arguments.
		 *
		 * @since 9.1.0
		 * @return array
		 */
		public static function get_default_args() {
			return array(
				'post_type'      => 'product',
				'post_status'    => 'publish',
				'posts_per_page' => -1,
				'fields'         => 'ids',
			);
		}

		/**
		 * Prepare posts arguments.
		 *
		 * @since 9.1.0
		 * @param string $menu_name Menu name.
		 * @return array
		 */
		public static function prepare_posts_args( $menu_name ) {
			$args                        = array();
			$user_id                     = get_current_user_id();
			$won_lotteries_url_param     = lty_get_dashboard_won_lotteries_endpoint_url();
			$not_won_lotteries_url_param = lty_get_dashboard_not_won_lotteries_endpoint_url();
			$instant_win_url_param       = lty_get_dashboard_instant_win_endpoint_url();

			switch ( $menu_name ) {
				case $won_lotteries_url_param:
					$product_ids = lty_get_my_winner_lotteries( $user_id );
					if ( ! lty_check_is_array( $product_ids ) ) {
						return array();
					}

					$args = array(
						'post_type'       => 'lty_lottery_winner',
						'post_status'     => 'lty_publish',
						'post_parent__in' => $product_ids,
						'meta_key'        => 'lty_user_id',
						'meta_value'      => $user_id,
					);

					break;

				case $not_won_lotteries_url_param:
					$product_ids = lty_get_my_lotteries( $user_id );
					if ( ! lty_check_is_array( $product_ids ) ) {
						return array();
					}

					$args = array(
						'meta_query' => array(
							array(
								'key'     => '_lty_closed',
								'compare' => 'EXISTS',
							),
						),
						'post__in'   => $product_ids,
						'tax_query'  => array(
							array(
								'taxonomy' => 'product_type',
								'field'    => 'name',
								'terms'    => 'lottery',
							),
						),
					);
					break;

				case $instant_win_url_param:
					$args = array(
						'post_type'   => LTY_Register_Post_Types::LOTTERY_INSTANT_WINNER_LOG_POSTTYPE,
						'post_status' => lty_get_instant_winner_log_statuses(),
						'meta_key'    => 'lty_user_id',
						'meta_value'  => $user_id,
					);
					break;

				default:
					$product_ids = lty_get_my_lotteries( $user_id, array( 'lty_ticket_buyer', 'lty_ticket_winner' ) );
					if ( ! lty_check_is_array( $product_ids ) ) {
						return array();
					}

					$args = array(
						'post__in'  => $product_ids,
						'tax_query' => array(
							array(
								'taxonomy' => 'product_type',
								'field'    => 'name',
								'terms'    => 'lottery',
							),
						),
					);
					break;
			}

			return $args;
		}
	}

	LTY_Myaccount_Handler::init();
}
