<?php

/**
 * Register Custom Post Status.
 * */
if ( ! defined( 'ABSPATH' ) ) {
	exit ; // Exit if accessed directly.
}

if ( ! class_exists( 'LTY_Register_Post_Status' ) ) {

	/**
	 * Class.
	 * */
	class LTY_Register_Post_Status {

		/**
		 * Class initialization.
		 * */
		public static function init() {
			add_action( 'init', array( __CLASS__, 'register_custom_post_status' ) ) ;
		}

		/**
		 * Register Custom Post Status.
		 * */
		public static function register_custom_post_status() {
			$custom_post_statuses = array(
				'lty_ticket_buyer'    => array( 'LTY_Register_Post_Status', 'buyer_post_status_args' ),
				'lty_ticket_winner'   => array( 'LTY_Register_Post_Status', 'winner_post_status_args' ),
				'lty_ticket_pending'  => array( 'LTY_Register_Post_Status', 'lottery_pending_post_status_args' ),
				'lty_ticket_canceled' => array( 'LTY_Register_Post_Status', 'canceled_post_status_args' ),
				'lty_available'       => array( 'LTY_Register_Post_Status', 'available_post_status_args' ),
				'lty_won'             => array( 'LTY_Register_Post_Status', 'won_post_status_args' ),
				'lty_pending'         => array( 'LTY_Register_Post_Status', 'pending_post_status_args' ),
				) ;

			/**
			 * This hook is used to alter the custom post statuses.
			 * 
			 * @since 1.0
			 */
			$custom_post_statuses = apply_filters( 'lty_add_custom_post_status', $custom_post_statuses ) ;

			// Return if no post status to register.
			if ( ! lty_check_is_array( $custom_post_statuses ) ) {
				return ;
			}

			foreach ( $custom_post_statuses as $post_status => $args_function ) {

				$args = array() ;
				if ( $args_function ) {
					$args = call_user_func_array( $args_function, array() ) ;
				}

				// Register post status.
				register_post_status( $post_status, $args ) ;
			}
		}

		/**
		 * Buyer Custom Post Status arguments.
		 * */
		public static function buyer_post_status_args() {
			/**
			 * This hook is used to alter the buyer post status arguments.
			 * 
			 * @since 1.0
			 */
			$args = apply_filters(
					'lty_buyer_post_status_args', array(
				'label'                     => esc_html_x( 'Ticket Buyer', 'lottery-for-woocommerce' ),
				'public'                    => true,
				'exclude_from_search'       => false,
				'show_in_admin_all_list'    => true,
				'show_in_admin_status_list' => true,
				/* translators: %s: count */
				'label_count'               => _n_noop( 'Ticket Buyer <span class="count">(%s)</span>', 'Ticket Buyer <span class="count">(%s)</span>', 'lottery-for-woocommerce' ),
					)
					) ;

			return $args ;
		}

		/**
		 * Winner Custom Post Status arguments.
		 * */
		public static function winner_post_status_args() {
			/**
			 * This hook is used to alter the winner post status arguments.
			 * 
			 * @since 1.0
			 */
			$args = apply_filters(
					'lty_winner_post_status_args', array(
				'label'                     => esc_html_x( 'Ticket Winner', 'lottery-for-woocommerce' ),
				'public'                    => true,
				'exclude_from_search'       => false,
				'show_in_admin_all_list'    => true,
				'show_in_admin_status_list' => true,
				/* translators: %s: count */
				'label_count'               => _n_noop( 'Ticket Winner <span class="count">(%s)</span>', 'Ticket Winner <span class="count">(%s)</span>', 'lottery-for-woocommerce' ),
					)
					) ;

			return $args ;
		}

		/**
		 * Pending Payment Custom Post Status arguments.
		 * */
		public static function lottery_pending_post_status_args() {
			/**
			 * This hook is used to alter the pending post status arguments.
			 * 
			 * @since 1.0
			 */
			$args = apply_filters(
					'lty_lottery_pending_post_status_args', array(
				'label'                     => esc_html_x( 'Ticket Pending', 'lottery-for-woocommerce' ),
				'public'                    => true,
				'exclude_from_search'       => false,
				'show_in_admin_all_list'    => true,
				'show_in_admin_status_list' => true,
				/* translators: %s: count */
				'label_count'               => _n_noop( 'Ticket Pending <span class="count">(%s)</span>', 'Ticket Pending <span class="count">(%s)</span>', 'lottery-for-woocommerce' ),
					)
					) ;

			return $args ;
		}

		/**
		 * Canceled Payment Custom Post Status arguments.
		 * */
		public static function canceled_post_status_args() {
			/**
			 * This hook is used to alter the canceled post status arguments.
			 * 
			 * @since 1.0
			 */
			$args = apply_filters(
					'lty_canceled_post_status_args', array(
				'label'                     => esc_html_x( 'Ticket Canceled', 'lottery-for-woocommerce' ),
				'public'                    => true,
				'exclude_from_search'       => false,
				'show_in_admin_all_list'    => true,
				'show_in_admin_status_list' => true,
				/* translators: %s: count */
				'label_count'               => _n_noop( 'Ticket Canceled <span class="count">(%s)</span>', 'Ticket Canceled <span class="count">(%s)</span>', 'lottery-for-woocommerce' ),
					)
					) ;

			return $args ;
		}
		
		/**
		 * Available Custom Post Status arguments.
		 * 
		 * @since 8.0.0
		 * */
		public static function available_post_status_args() {
			/**
			 * This hook is used to alter the available post status arguments.
			 * 
			 * @since 8.0.0
			 */
			$args = apply_filters(
					'lty_available_post_status_args', array(
				'label'                     => esc_html_x( 'Ticket Available', 'lottery-for-woocommerce' ),
				'public'                    => true,
				'exclude_from_search'       => false,
				'show_in_admin_all_list'    => true,
				'show_in_admin_status_list' => true,
				/* translators: %s: count */
				'label_count'               => _n_noop( 'Ticket Available <span class="count">(%s)</span>', 'Ticket Available <span class="count">(%s)</span>', 'lottery-for-woocommerce' ),
					)
					) ;

			return $args ;
		}

		/**
		 * Won Custom Post Status arguments.
		 * 
		 * @since 8.0.0
		 * */
		public static function won_post_status_args() {
			/**
			 * This hook is used to alter the Won post status arguments.
			 * 
			 * @since 8.0.0
			 */
			$args = apply_filters(
					'lty_won_post_status_args', array(
				'label'                     => esc_html_x( 'Won', 'lottery-for-woocommerce' ),
				'public'                    => true,
				'exclude_from_search'       => false,
				'show_in_admin_all_list'    => true,
				'show_in_admin_status_list' => true,
				/* translators: %s: count */
				'label_count'               => _n_noop( 'Won <span class="count">(%s)</span>', 'Won <span class="count">(%s)</span>', 'lottery-for-woocommerce' ),
					)
					) ;

			return $args ;
		}
		
		/**
		 * Pending Payment Custom Post Status arguments.
		 * 
		 * @since 8.0.0
		 * @return array.
		 * */
		public static function pending_post_status_args() {
			/**
			 * This hook is used to alter the pending post status arguments.
			 * 
			 * @since 8.0.0
			 */
			$args = apply_filters(
					'lty_pending_post_status_args', array(
				'label'                     => esc_html_x( 'Pending', 'lottery-for-woocommerce' ),
				'public'                    => true,
				'exclude_from_search'       => false,
				'show_in_admin_all_list'    => true,
				'show_in_admin_status_list' => true,
				/* translators: %s: count */
				'label_count'               => _n_noop( 'Pending <span class="count">(%s)</span>', 'Pending <span class="count">(%s)</span>', 'lottery-for-woocommerce' ),
					)
					) ;

			return $args ;
		}
	}

	LTY_Register_Post_Status::init() ;
}
