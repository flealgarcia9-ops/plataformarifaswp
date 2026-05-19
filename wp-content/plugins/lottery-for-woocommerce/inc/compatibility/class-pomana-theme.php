<?php
/**
 * Compatibility - Pomana theme.
 * Tested upto: 1.10.2
 *
 * @since 11.8.0
 * @link https://pomana.modeltheme.com/
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

if ( ! class_exists( 'LTY_Pomana_Theme_Compatibility' ) ) {

	/**
	 * Class.
	 *
	 * @since 10.8.0
	 * */
	class LTY_Pomana_Theme_Compatibility extends LTY_Compatibility {

		/**
		 * Class constructor.
		 *
		 * @since 10.8.0
		 */
		public function __construct() {
			$this->id = 'pomana_theme';

			add_action('after_setup_theme', array( $this, 'setup_theme_support' ));
			parent::__construct();
		}

		/**
		 * Is plugin enabled?.
		 *
		 * @since 10.8.0
		 * @return bool
		 * */
		public function is_plugin_enabled() {
			return class_exists('Redux_Framework_pomana_config');
		}

		/**
		 * Setup theme support.
		 *
		 * @since 10.8.0
		 */
		public function setup_theme_support() {
			if ($this->is_plugin_enabled()) {
				add_action('wp_enqueue_scripts', array( $this, 'conditionally_dequeue_bootstrap' ), 20);
			}
		}

		/**
		 * Conditionally dequeue Bootstrap if viewing Lottery dashboard or My Account Lottery endpoint.
		 *
		 * @since 10.8.0
		 */
		public static function conditionally_dequeue_bootstrap() {
			global $wp;

			if (is_page(wc_get_page_id('lty_dashboard')) || ( is_account_page() && is_object($wp) && isset($wp->query_vars['lottery']) )) {
				wp_dequeue_script('bootstrap');
				wp_dequeue_script('bootstrap-js', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js', array(), null, true);
				// Add custom css as inline style.
				$custom_css = '#lty_customer_lottery_tickets_modal { position: relative !important; }';
				wp_add_inline_style( 'lty-inline-style', $custom_css );
			}
		}
	}
}
