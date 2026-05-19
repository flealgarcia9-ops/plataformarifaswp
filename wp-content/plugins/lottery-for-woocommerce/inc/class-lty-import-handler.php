<?php

/**
 * Import Handler.
 *
 * @since 9.9.0
 */
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'LTY_Import_Handler' ) ) {

	/**
	 * Class.
	 *
	 * @since 9.9.0
	 */
	class LTY_Import_Handler {

		/**
		 * Class initialization.
		 *
		 * @since 9.9.0
		 */
		public static function init() {
			add_filter( 'admin_footer', array( __CLASS__, 'footer_content' ) );
		}

		/**
		 * Get the importer by type.
		 *
		 * @since 9.9.0
		 * @param string $type
		 * @return object/boolean
		 */
		public static function get_importer( $type ) {
			$importer          = false;
			$default_importers = array(
				'instant-winner-rule'         => 'LTY_Instant_Winner_Rule_Importer',
				'instant_winner_prize_groups' => 'LTY_Instant_Winner_Prize_Group_Importer',
			);
			if ( ! isset( $default_importers[ $type ] ) ) {
				return $importer;
			}

			$importer_class = $default_importers[ $type ];
			$importer       = new $importer_class();

			return $importer;
		}

		/**
		 * Footer content of import popup modal.
		 *
		 * @since 9.9.0
		 */
		public static function footer_content() {
			echo '<script type="text/template" id="tmpl-lty-import-popup-modal">{{{data}}}</script>';
		}
	}

	LTY_Import_Handler::init();
}
