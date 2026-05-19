<?php

/**
 * Lottery Page Class.
 * */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'LTY_Lottery_Page' ) ) {

	/**
	 * LTY_Lottery_Page Class.
	 * */
	class LTY_Lottery_Page {

		/**
		 * Plugin slug.
		 *
		 * @var string
		 * */
		private static $plugin_slug = 'lty';

		/**
		 * Class initialization.
		 * */
		public static function init() {
			// Render lottery tickets sections.
			add_action( 'lty_lottery_tickets_sections', array( __CLASS__, 'render_lottery_tickets_sections' ) );
			// Render lottery tickets content.
			add_action( 'lty_lottery_tickets_content', array( __CLASS__, 'render_lottery_tickets_content' ) );
		}

		/**
		 * Output Lottery Page.
		 * */
		public static function output() {
			global $current_lottery;

			switch ( $current_lottery ) {
				case 'view':
					self::render_view_lottery();
					break;
				default:
					self::render_lottery();
					break;
			}
		}

		/**
		 * Output the Lottery WP List Table.
		 * */
		public static function render_lottery() {

			// Html for view lottery page.
			include_once LTY_PLUGIN_PATH . '/inc/admin/menu/views/html-lottery.php';
		}

		/**
		 * View Lottery.
		 * */
		public static function render_view_lottery() {
			global $lty_product, $current_tab, $lottery_id;

			if ( ! $lty_product->exists() ) {
				return;
			}

			$tabs = self::lottery_tabs();

			// Html for view lottery page.
			include_once LTY_PLUGIN_PATH . '/inc/admin/menu/views/html-view-lottery.php';
		}

		/**
		 * Get Lottery tabs.
		 * */
		public static function lottery_tabs() {
			global $lty_product;

			$tabs = array();
			if ( ! $lty_product->exists() ) {
				return $tabs;
			}

			$tabs = array(
				'tickets' => __( 'Tickets log', 'lottery-for-woocommerce' ),
			);

			return $tabs;
		}

		/**
		 * View lottery tickets sections.
		 * */
		public static function render_lottery_tickets_sections() {
			global $lty_product, $lottery_id, $current_tab, $current_section;

			if ( ! $lty_product->exists() ) {
				return;
			}

			$url             = lty_get_lottery_page_url(
				array(
					'lty_action' => 'view',
					'product_id' => $lottery_id,
					'tab'        => 'tickets',
				)
			);
			$product_relists = array();
			$product_relists = $lty_product->get_lty_relists();

			// Return if relist not exists.
			if ( ! lty_check_is_array( $product_relists ) ) {
				return;
			}

			end( $product_relists );

			$last_key       = key( $product_relists );
			$i              = 1;
			$old_list_count = count( $product_relists );
			$class_name     = array( 'lty_listing_views' );

			if ( '' == $current_section ) {
				$class_name[] = 'current';
			}

			$views  = '<div class="subsubsub">';
			$views .= sprintf( '<li><a href="%s" class="%s">%s</a></li>', esc_url( $url ), implode( ' ', $class_name ), __( 'Current Listing', 'lottery-for-woocommerce' ) );

			foreach ( $product_relists as $relist_key => $relist_data ) {
				$class_name = array( 'lty_listing_views' );

				if ( $i == $current_section ) {
					$class_name[] = 'current';
				}

				if ( 1 == $old_list_count ) {
					$label = esc_html( 'Initial Listing', 'lottery-for-woocommerce' );
				} else {
					$label = __( 'Relist', 'lottery-for-woocommerce' ) . ' #' . ( $old_list_count - 1 );
				}

				$url    = lty_get_lottery_page_url(
					array(
						'lty_action' => 'view',
						'product_id' => $lottery_id,
						'tab'        => 'tickets',
						'section'    => $i,
					)
				);
				$views .= ' | ' . sprintf( '<li><a href="%s" class="%s">%s</a></li>', esc_url( $url ), implode( ' ', $class_name ), $label );

				$i++;
				$old_list_count--;
			}

			$views .= '</ul>';

			echo wp_kses_post( $views );
		}

		/**
		 * Render lottery tickets content.
		 *
		 * @since 1.0.0
		 * @global var $lty_product
		 * @global var $current_section
		 * @return void
		 * */
		public static function render_lottery_tickets_content() {
			global $lty_product, $current_section;

			if ( ! $lty_product->exists() ) {
				return;
			}

			$type                          = ! isset( $_GET['section'] ) ? '2' : '3';
			$lottery_ticket_overview_datas = lty_get_product_status_datas( $lty_product, 1 );
			$product_ticket_config_datas   = lty_get_product_config_datas( $lty_product, $current_section );

			// Html for view lottery page.
			include_once LTY_PLUGIN_PATH . '/inc/admin/menu/views/html-view-lottery-tickets.php';
		}
	}

}
