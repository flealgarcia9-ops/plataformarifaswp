<?php

/**
 * Handles the lottery pages.
 *
 * @since 9.0.0
 * */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
if ( ! class_exists( 'LTY_Lottery_Page_Handler' ) ) {

	/**
	 * Class.
	 *
	 * @since 9.0.0
	 * */
	class LTY_Lottery_Page_Handler {

		/**
		 * Class Initialization.
		 *
		 * @since 9.0.0
		 * */
		public static function init() {
			// Add the custom endpoint.
			add_action( 'init', array( __CLASS__, 'rewrite_custom_endpoint' ), 9 );
			// Add the custom query vars.
			add_filter( 'query_vars', array( __CLASS__, 'custom_query_vars' ), 0 );
			// Custom template include.
			add_filter( 'template_include', array( __CLASS__, 'custom_template_include' ), 100 );
			// Alter the entry list add to cart permalink.
			add_filter( 'post_type_link', array( __CLASS__, 'alter_entry_list_add_to_cart_permalink' ), 10, 2 );
			// Alter the entry list add to cart text.
			add_filter( 'woocommerce_product_add_to_cart_text', array( __CLASS__, 'alter_entry_list_add_to_cart_text' ), 10, 2 );
			// Render the lottery entry list content.
			add_action( 'lty_lottery_entry_list_content', array( __CLASS__, 'render_lottery_entry_list_content' ), 1 );
		}

		/**
		 * Alter the entry list add to cart permalink.
		 *
		 * @since 9.0.0
		 * @param string $add_to_cart_url
		 * @param object $post
		 * @return string
		 */
		public static function alter_entry_list_add_to_cart_permalink( $add_to_cart_url, $post ) {
			if ( ! get_query_var( 'is_lottery_entry_list_archive', false ) ) {
				return $add_to_cart_url;
			}

			if ( ! is_object( $post ) ) {
				return $add_to_cart_url;
			}

			$product = wc_get_product( $post->ID );
			if ( ! lty_is_lottery_product( $product ) ) {
				return $add_to_cart_url;
			}

			return wc_get_endpoint_url( get_post( $product->get_id() )->post_name, '', get_page_link( wc_get_page_id( 'lty_lottery_entry_list' ) ) );
		}

		/**
		 * Alter the entry list add to cart text.
		 *
		 * @since 9.0.0
		 * @param string $add_to_cart_text
		 * @param object $product
		 * @return string
		 */
		public static function alter_entry_list_add_to_cart_text( $add_to_cart_text, $product ) {
			if ( ! get_query_var( 'is_lottery_entry_list_archive', false ) ) {
				return $add_to_cart_text;
			}

			return lty_get_entry_list_view_participants_label();
		}

		/**
		 * Rewrite the custom endpoints.
		 *
		 * @since 9.0.0
		 */
		public static function rewrite_custom_endpoint() {
			$page_id = wc_get_page_id( 'lty_lottery_entry_list' );
			$slug    = get_post_field( 'post_name', $page_id );
			add_rewrite_rule(
				'^' . get_page_uri( $page_id ) . '/([^/]*)/?$',
				'index.php?pagename=' . $slug . '&lottery_entry_list=true&lottery_single_entry_list=$matches[1]',
				'top'
			);
			add_rewrite_rule(
				'^' . get_page_uri( $page_id ) . '/([^/]*)/page/([0-9]{1,})/?$',
				'index.php?pagename=' . $slug . '&lottery_entry_list=true&lottery_single_entry_list=$matches[1]&paged=$matches[2]',
				'top'
			);
		}

		/**
		 * Add the custom query variable.
		 *
		 * @since 9.0.0
		 * @return array.
		 * */
		public static function custom_query_vars( $query_vars ) {
			$query_vars        = lty_check_is_array( $query_vars ) ? $query_vars : array();
			$custom_query_vars = array( 'lottery_entry_list', 'lottery_single_entry_list' );

			return array_merge( $query_vars, $custom_query_vars );
		}

		/**
		 * Custom template include.
		 *
		 * @since 9.0.0
		 * @param string $template_path
		 * @retrun string
		 */
		public static function custom_template_include( $template_path ) {
			if ( ! get_query_var( 'lottery_single_entry_list' ) ) {
				return $template_path;
			}

			$post = get_page_by_path( get_query_var( 'lottery_single_entry_list', false ), OBJECT, 'product' );
			if ( ! is_object( $post ) ) {
				return get_404_template();
			}

			global $product;

			$product = wc_get_product( $post->ID );
			if ( ! lty_is_lottery_product( $product ) ) {
				return get_404_template();
			}

			return wc_locate_template( 'single-entry-list-page.php', 'lottery-for-woocommerce/', lty()->templates() );
		}

		/**
		 * Render the lottery entry list content.
		 *
		 * @since 9.2.0
		 * @param object $product Product object.
		 * @return void
		 */
		public static function render_lottery_entry_list_content( $product ) {
			if ( ! lty_is_lottery_product( $product ) ) {
				return;
			}

			// Render the lottery entry list overview content.
			self::render_entry_list_overview_content( $product );

			// Render the lottery entry list winner logs content.
			self::render_entry_list_winner_logs_content( $product );

			// Render the lottery entry list ticket logs content.
			self::render_entry_list_ticket_logs_content( $product );
		}

		/**
		 * Render the lottery entry list overview content.
		 *
		 * @since 9.0.0
		 * @param object $product Product object.
		 */
		public static function render_entry_list_overview_content( $product ) {
			if ( ! lty_is_lottery_product( $product ) ) {
				return;
			}

			$entry_list_args = array( 'product' => $product );
			// Lottery entry list pdf download button url.
			if ( lty_can_display_lottery_entry_list_pdf_download_button() ) {
				$entry_list_args['pdf_download_button_url'] = esc_url(
					add_query_arg(
						array(
							'action'        => 'lty-download',
							'lty_pdf_nonce' => wp_create_nonce( 'lty-lottery-entry-list-pdf' ),
							'lty_key'       => lty_encode(
								array(
									'lty_lottery_id' => $product->get_id(),
								),
								true
							),
						),
						get_site_url()
					)
				);
			}

			lty_get_template( 'single-entry-list/summary.php', $entry_list_args );
		}

		/**
		 * Render the lottery entry list ticket logs content.
		 *
		 * @since 9.0.0
		 * @param object $product Product object.
		 */
		public static function render_entry_list_ticket_logs_content( $product ) {
			if ( ! lty_is_lottery_product( $product ) ) {
				return;
			}

			lty_get_template( 'single-entry-list/ticket-logs.php', array( 'product' => $product ) );
		}

		/**
		 * Render the lottery entry list winner logs content.
		 *
		 * @since 9.2.0
		 * @param object $product Product object.
		 * @return void
		 */
		public static function render_entry_list_winner_logs_content( $product ) {
			if ( 'yes' === get_option( 'lty_settings_hide_entry_list_winners_details', 'no' ) || ! lty_is_lottery_product( $product ) || ! $product->has_lottery_status( 'lty_lottery_finished' ) ) {
				return;
			}

			$winner_log_args = array(
				'columns'         => lty_get_lottery_winner_table_header( $product ),
				'lottery_winners' => $product->get_current_winner_ids(),
				'product'         => $product,
			);

			if ( ! lty_check_is_array( $winner_log_args['columns'] ) || ! lty_check_is_array( $winner_log_args['lottery_winners'] ) ) {
				return;
			}

			lty_get_template( 'single-entry-list/winner-logs.php', $winner_log_args );
		}
	}

	LTY_Lottery_Page_Handler::init();
}
