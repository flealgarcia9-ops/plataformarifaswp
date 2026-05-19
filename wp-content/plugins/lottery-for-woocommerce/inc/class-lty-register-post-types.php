<?php

/**
 * Custom Post Type.
 * */
if ( ! defined( 'ABSPATH' ) ) {
	exit ; // Exit if accessed directly.
}

if ( ! class_exists( 'LTY_Register_Post_Types' ) ) {

	/**
	 * LTY_Register_Post_Types Class.
	 * */
	class LTY_Register_Post_Types {

		/**
		 * Lottery Ticket Post Type.
		 * */
		const LOTTERY_TICKET_POSTTYPE = 'lty_lottery_ticket' ;

		/**
		 * Lottery Winner Post Type.
		 * */
		const LOTTERY_WINNER_POSTTYPE = 'lty_lottery_winner' ;

		/**
		 * Lottery Instant Winner Rules Post Type.
		 * 
		 * @since 8.0.0
		 * */
		const LOTTERY_INSTANT_WINNER_RULE_POSTTYPE = 'lty_instant_winners';

		/**
		 * Instant winner prize group post type.
		 *
		 * @since 11.1.0
		 * */
		const LOTTERY_INSTANT_WINNER_PRIZE_GROUP_POST_TYPE = 'lty_ins_win_group';

		/**
		 * Lottery instant winners log post type.
		 * 
		 * @since 8.0.0
		 * */
		const LOTTERY_INSTANT_WINNER_LOG_POSTTYPE = 'lty_ins_winner_log';

		/**
		 * LTY_Register_Post_Types Class initialization.
		 * */
		public static function init() {
			add_action( 'init', array( __CLASS__, 'register_custom_post_types' ) ) ;
			add_action( 'lty_lottery_after_register_post_type', array( __CLASS__, 'maybe_flush_rewrite_rules' ) );
		}

		/**
		 * Register Custom Post types.
		 * */
		public static function register_custom_post_types() {
			if ( ! is_blog_installed() ) {
				return ;
			}

			$custom_post_types = array(
				self::LOTTERY_TICKET_POSTTYPE              => array( 'LTY_Register_Post_Types', 'lottery_ticket_post_type_args' ),
				self::LOTTERY_WINNER_POSTTYPE              => array( 'LTY_Register_Post_Types', 'lottery_winner_post_type_args' ),
				self::LOTTERY_INSTANT_WINNER_RULE_POSTTYPE => array( 'LTY_Register_Post_Types', 'lottery_instant_winner_rule_post_type_args' ),
				self::LOTTERY_INSTANT_WINNER_PRIZE_GROUP_POST_TYPE => array( 'LTY_Register_Post_Types', 'lottery_instant_winner_prize_group_post_type_args' ),
				self::LOTTERY_INSTANT_WINNER_LOG_POSTTYPE  => array( 'LTY_Register_Post_Types', 'lottery_instant_winner_log_post_type_args' ),
			);

			/**
			 * This hook is used to alter the custom post types.
			 * 
			 * @since 1.0
			 */
			$custom_post_types = apply_filters( 'lty_add_custom_post_types', $custom_post_types ) ;

			// Return if no post type to register.
			if ( ! lty_check_is_array( $custom_post_types ) ) {
				return ;
			}

			// Flush permalinks and rewrite rules after register post type.
			$theme_support = wc_current_theme_supports_woocommerce_or_fse() ? 'yes' : 'no';
			if ( get_option( 'current_theme_supports_woocommerce' ) !== $theme_support && update_option( 'current_theme_supports_woocommerce', $theme_support ) ) {
				lty_set_lottery_queue_flush_rewrite_rules();
			}

			foreach ( $custom_post_types as $post_type => $args_function ) {

				$args = array() ;
				if ( $args_function ) {
					$args = call_user_func_array( $args_function, $args ) ;
				}

				// Register custom post type.
				register_post_type( $post_type, $args ) ;
			}

			/**
			 * Triggers after register lottery custom post types.
			 *
			 * @since 11.6.0
			 */
			do_action( 'lty_lottery_after_register_post_type' );
		}

		/**
		 * Prepare Lottery Ticket Post type arguments.
		 * */
		public static function lottery_ticket_post_type_args() {
			/**
			 * This hook is used to alter the lottery ticket post type arguments.
			 * 
			 * @since 1.0
			 */
			return apply_filters(
					'lty_lottery_ticket_post_type_args', array(
				'label'           => __( 'Giveaway Ticket', 'lottery-for-woocommerce' ),
				'public'          => false,
				'hierarchical'    => false,
				'supports'        => false,
				'capability_type' => 'post',
				'rewrite'         => false,
					)
					) ;
		}

		/**
		 * Prepare Lottery Winner Post type arguments.
		 * */
		public static function lottery_winner_post_type_args() {
			/**
			 * This hook is used to alter the lottery winner post type arguments.
			 * 
			 * @since 1.0
			 */
			return apply_filters(
					'lty_lottery_winner_post_type_args', array(
				'label'           => __( 'Giveaway Winner', 'lottery-for-woocommerce' ),
				'public'          => false,
				'hierarchical'    => false,
				'supports'        => false,
				'capability_type' => 'post',
				'rewrite'         => false,
					)
					) ;
		}

		/**
		 * Prepare Lottery Instant Winner rule Post Type arguments.
		 * 
		 * @since 8.0.0
		 * */
		public static function lottery_instant_winner_rule_post_type_args() {
			/**
			 * This hook is used to alter the instant winner rule post type args.
			 * 
			 * @since 8.0.0
			 */
			return apply_filters(
					'lty_lottery_instant_winners_prizes_post_type_args', array(
				'label'         => __('Giveaway Instant Winners Prizes', 'lottery-for-woocommerce'),
				'public'            => false,
				'hierarchical'      => false,
				'supports'      => false,
				'capability_type'   => 'post',
				'rewrite'           => false,
					)
			);
		}

		/**
		 * Prepare lottery instant winner prize group post type arguments.
		 *
		 * @since 11.1.0
		 * */
		public static function lottery_instant_winner_prize_group_post_type_args() {
			/**
			 * This hook is used to alter the instant winner prize group post type args.
			 *
			 * @since 11.1.0
			 */
			return apply_filters(
				'lty_lottery_instant_winners_prizes_post_type_args',
				array(
					'label'           => __( 'Giveaway Instant Win Prize Groups', 'lottery-for-woocommerce' ),
					'public'          => false,
					'hierarchical'    => false,
					'supports'        => false,
					'capability_type' => 'post',
					'rewrite'         => false,
				)
			);
		}

		/**
		 * Prepare lottery instant winner log post type arguments.
		 * 
		 * @since 8.0.0
		 * */
		public static function lottery_instant_winner_log_post_type_args() {
			/**
			 * This hook is used to alter the instant winner log post type args.
			 * 
			 * @since 8.0.0
			 */
			return apply_filters(
					'lty_lottery_instant_winner_log_post_type_args', array(
				'label'         => __('Giveaway Instant Winner Log', 'lottery-for-woocommerce'),
				'public'        => false,
				'hierarchical'  => false,
				'supports'      => false,
				'capability_type'=> 'post',
				'rewrite'       => false,
					)
			);
		}

		/**
		 * Flush rules if the event is queued.
		 *
		 * @since 11.6.0
		 */
		public static function maybe_flush_rewrite_rules() {
			if ( 'yes' === get_option( 'lty_lottery_queue_flush_rewrite_rules' ) ) {
				lty_unset_lottery_queue_flush_rewrite_rules();
				self::flush_rewrite_rules();
			}
		}

		/**
		 * Flush rewrite rules.
		 *
		 * @since 11.6.0
		 * */
		public static function flush_rewrite_rules() {
			flush_rewrite_rules();
		}
	}

	LTY_Register_Post_Types::init() ;
}
