<?php

/**
 * Export Handler.
 *
 * @since 10.3.0
 */
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'LTY_Export_Handler' ) ) {

	/**
	 * Class.
	 *
	 * @since 10.3.0
	 */
	class LTY_Export_Handler {

		/**
		 * Exporters.
		 *
		 * @since 10.3.0
		 * @var array
		 */
		private static $exporters;

		/**
		 * Class initialization.
		 *
		 * @since 10.3.0
		 */
		public static function init() {
			add_action( 'admin_footer', array( __CLASS__, 'footer_content' ) );
		}

		/**
		 * Get the exporter by type.
		 *
		 * @since 10.3.0
		 * @param string $type Export type.
		 * @return object|boolean
		 */
		public static function get_exporter( $type ) {
			$exporters = self::get_exporters();

			return isset( $exporters[ $type ] ) ? $exporters[ $type ] : false;
		}

		/**
		 * Get the exporters.
		 *
		 * @since 10.3.0
		 * @return array
		 */
		public static function get_exporters() {
			if ( isset( self::$exporters ) ) {
				return self::$exporters;
			}

			/**
			 * Include dependencies.
			 */
			if ( ! class_exists( 'WC_CSV_Batch_Exporter', false ) ) {
				include_once WC_ABSPATH . 'includes/export/abstract-wc-csv-batch-exporter.php';
			}

			include_once LTY_ABSPATH . 'inc/export/class-lty-exporter.php';
			include_once LTY_ABSPATH . 'inc/export/class-lty-lottery-tickets-exporter.php';
			include_once LTY_ABSPATH . 'inc/export/class-lty-instant-winner-rules-exporter.php';
			include_once LTY_ABSPATH . 'inc/export/class-lty-instant-winner-prize-groups-exporter.php';

			$default_exporters = array(
				'lottery_tickets'             => 'LTY_Lottery_Tickets_Exporter',
				'instant_winner_rules'        => 'LTY_Instant_Winner_Rules_Exporter',
				'instant_winner_prize_groups' => 'LTY_Instant_Winner_Prize_Groups_Exporter',
			);

			self::$exporters = array();
			foreach ( $default_exporters as $exporter_key => $exporter_class ) {
				self::$exporters[ $exporter_key ] = new $exporter_class();
			}

			return self::$exporters;
		}

		/**
		 * Footer content of export popup modal.
		 *
		 * @since 10.3.0
		 */
		public static function footer_content() {
			echo '<script type="text/template" id="tmpl-lty-export-popup-modal">{{{data}}}</script>';
		}
	}

	LTY_Export_Handler::init();
}
