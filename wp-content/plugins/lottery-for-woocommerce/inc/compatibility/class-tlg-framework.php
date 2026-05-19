<?php
/**
 * Compatibility - TLG Framework plugin.
 *
 * @since 10.8.0
 * @tested upto 1.4.9
 * @link http://www.themelogi.com
 * */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

if ( ! class_exists( 'LTY_TLG_Framework_Compatibility' ) ) {

	/**
	 * Class.
	 *
	 * @since 10.8.0
	 * */
	class LTY_TLG_Framework_Compatibility extends LTY_Compatibility {

		/**
		 * Class constructor.
		 *
		 * @since 10.8.0
		 */
		public function __construct() {
			$this->id = 'tlg_framework';

			parent::__construct();
		}

		/**
		 * Is plugin enabled?.
		 *
		 * @since 10.8.0
		 * @return bool
		 * */
		public function is_plugin_enabled() {
			return function_exists( 'tlg_framework_textdomain' );
		}

		/**
		 * Actions.
		 *
		 * @since 10.8.0
		 */
		public function actions() {
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		}

		/**
		 * Add enqueue scripts.
		 *
		 * @since 10.8.0
		 * @return void
		 */
		public static function enqueue_scripts() {
			wp_dequeue_script( 'tlg_framework-script' );
		}
	}

}
