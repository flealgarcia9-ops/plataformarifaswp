<?php

/**
 * Giveaway(formerly Lottery) For WooCommerce Main class.
 *
 * @since 1.0.0
 */
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}

if (!class_exists('FP_Lottery')) {

	/**
	 * Main class.
	 * */
	final class FP_Lottery {

		/**
		 * Version.
		 *
		 * @var string
		 * */
		private $version = '12.5.0';

		/**
		 * Locale.
		 *
		 * @var string
		 * */
		private $locale = 'lottery-for-woocommerce';

		/**
		 * Folder Name.
		 *
		 * @var string
		 * */
		private $folder_name = 'lottery-for-woocommerce';

		/**
		 * WC minimum version.
		 *
		 * @var string
		 */
		public static $wc_minimum_version = '8.2.0';

		/**
		 * WP minimum version.
		 *
		 * @var string
		 */
		public static $wp_minimum_version = '6.2';

		/**
		 * Widgets.
		 *
		 * @var array
		 * */
		protected $widgets;

		/**
		 * Notifications.
		 *
		 * @var array
		 * */
		protected $notifications;

		/**
		 * The single instance of the class.
		 * */
		protected static $_instance = null;

		/**
		 * Load Lottery Class in Single Instance.
		 * */
		public static function instance() {
			if (is_null(self::$_instance)) {
				self::$_instance = new self();
			}

			return self::$_instance;
		}

		/**
		 *  Cloning has been forbidden
		 * */
		public function __clone() {
			_doing_it_wrong(__FUNCTION__, 'You are not allowed to perform this action!!!', '1.0');
		}

		/**
		 * UnSerialize the class data has been forbidden.
		 * */
		public function __wakeup() {
			_doing_it_wrong(__FUNCTION__, 'You are not allowed to perform this action!!!', '1.0');
		}

		/**
		 * Constructor.
		 * */
		public function __construct() {
			$this->define_constants();
			$this->include_files();
			$this->init_hooks();
		}

		/**
		 * Load plugin the translate files.
		 * */
		private function load_plugin_textdomain() {
			$locale = determine_locale();
			/**
			 * This hook is used to alter the plugin locale.
			 *
			 * @since 1.0.0
			 */
			$locale = apply_filters('plugin_locale', $locale, LTY_LOCALE);

			// Unload the text domain if other plugins/themes loaded the same text domain by mistake.
			unload_textdomain(LTY_LOCALE, true);

			// Load the text domain from the "wp-content" languages folder. we have handles the plugin folder in languages folder for easily handle it.
			load_textdomain(LTY_LOCALE, WP_LANG_DIR . '/' . LTY_FOLDER_NAME . '/' . LTY_LOCALE . '-' . $locale . '.mo');

			// Load the text domain from the current plugin languages folder.
			load_plugin_textdomain(LTY_LOCALE, false, dirname(plugin_basename(LTY_PLUGIN_FILE)) . '/languages');
		}

		/**
		 * Prepare the constants value array.
		 * */
		private function define_constants() {

			$constant_array = array(
				'LTY_VERSION' => $this->version,
				'LTY_LOCALE' => $this->locale,
				'LTY_FOLDER_NAME' => $this->folder_name,
				'LTY_ABSPATH' => dirname(LTY_PLUGIN_FILE) . '/',
				'LTY_ADMIN_URL' => admin_url('admin.php'),
				'LTY_ADMIN_AJAX_URL' => admin_url('admin-ajax.php'),
				'LTY_PLUGIN_SLUG' => plugin_basename(LTY_PLUGIN_FILE),
				'LTY_PLUGIN_PATH' => untrailingslashit(plugin_dir_path(LTY_PLUGIN_FILE)),
				'LTY_PLUGIN_URL' => untrailingslashit(plugins_url('/', LTY_PLUGIN_FILE)),
			);

			/**
			 * This hook is used to alter the define constants.
			 *
			 * @since 1.0
			 */
			$constant_array = apply_filters('lty_define_constants', $constant_array);

			if (is_array($constant_array) && !empty($constant_array)) {
				foreach ($constant_array as $name => $value) {
					$this->define_constant($name, $value);
				}
			}
		}

		/**
		 * Define the Constants value.
		 * */
		private function define_constant( $name, $value ) {
			if (!defined($name)) {
				define($name, $value);
			}
		}

		/**
		 * Include required files.
		 * */
		private function include_files() {

			// Function.
			include_once LTY_ABSPATH . 'inc/lty-common-functions.php';

			// Abstract classes.
			include_once LTY_ABSPATH . 'inc/abstracts/abstract-lty-post.php';

			// Pages.
			include_once LTY_ABSPATH . 'inc/class-lty-pages.php';

			include_once LTY_ABSPATH . 'inc/class-lty-install.php';
			include_once LTY_ABSPATH . 'inc/class-lty-date-time.php';
			include_once LTY_ABSPATH . 'inc/privacy/class-lty-privacy.php';

			// Instances.
			include_once LTY_ABSPATH . 'inc/notifications/class-lty-notification-instances.php';
			include_once LTY_ABSPATH . 'inc/widgets/class-lty-widget-instances.php';

			// Compatibility.
			include_once LTY_ABSPATH . 'inc/compatibility/class-lty-compatibility-instances.php';

			// Post Type and Status.
			include_once LTY_ABSPATH . 'inc/class-lty-register-post-types.php';
			include_once LTY_ABSPATH . 'inc/class-lty-register-post-status.php';

			// Query.
			include_once LTY_ABSPATH . 'inc/class-lty-query.php';

			// Handler.
			include_once LTY_ABSPATH . 'inc/class-lty-cron-handler.php';
			include_once LTY_ABSPATH . 'inc/class-lty-order-handler.php';
			include_once LTY_ABSPATH . 'inc/class-lty-lottery-handler.php';
			include_once LTY_ABSPATH . 'inc/class-lty-winner-handler.php';
			include_once LTY_ABSPATH . 'inc/class-lty-generate-pdf-handler.php';
			include_once LTY_ABSPATH . 'inc/class-lty-file-uploader.php';
			include_once LTY_ABSPATH . 'inc/class-lty-action-scheduler-handler.php';
			include_once LTY_ABSPATH . 'inc/class-lty-instant-winner-coupon-handler.php';
			include_once LTY_ABSPATH . 'inc/class-lty-transient-handler.php';

			// Entity.
			include_once LTY_ABSPATH . 'inc/entity/class-lty-lottery-ticket.php';
			include_once LTY_ABSPATH . 'inc/entity/class-lty-lottery-product-winner.php';
			include_once LTY_ABSPATH . 'inc/entity/class-lty-instant-winner-rule.php';
			include_once LTY_ABSPATH . 'inc/entity/class-lty-instant-winner-prize-group.php';
			include_once LTY_ABSPATH . 'inc/entity/class-lty-instant-winner-log.php';

			if (is_admin()) {
				$this->include_admin_files();
			}

			if (!is_admin() || defined('DOING_AJAX')) {
				$this->include_frontend_files();
			}
		}

		/**
		 * Include admin files.
		 * */
		private function include_admin_files() {
			include_once LTY_ABSPATH . 'inc/admin/class-lty-admin-assets.php';
			include_once LTY_ABSPATH . 'inc/admin/class-lty-admin-ajax.php';
			include_once LTY_ABSPATH . 'inc/admin/menu/class-lty-menu-management.php';
			include_once LTY_ABSPATH . 'inc/admin/menu/class-lty-lottery-product-type-handler.php';
			include_once LTY_ABSPATH . 'inc/admin/class-lty-order-item-generate-ticket.php';

			// Import.
			include_once LTY_ABSPATH . 'inc/import/class-lty-importer.php';
			include_once LTY_ABSPATH . 'inc/class-lty-import-handler.php';
			include_once LTY_ABSPATH . 'inc/import/class-lty-instant-winner-rule-importer.php';
			include_once LTY_ABSPATH . 'inc/import/class-lty-instant-winner-prize-group-importer.php';

			// Export.
			include_once LTY_ABSPATH . 'inc/class-lty-export-handler.php';
		}

		/**
		 * Include frontend files.
		 * */
		private function include_frontend_files() {
			// Function.
			include_once LTY_ABSPATH . 'inc/frontend/lty-frontend-functions.php';

			include_once LTY_ABSPATH . 'inc/frontend/class-lty-frontend-assets.php';
			include_once LTY_ABSPATH . 'inc/frontend/class-lty-frontend-lottery-product.php';
			include_once LTY_ABSPATH . 'inc/frontend/class-lty-lottery-single-product-templates.php';
			include_once LTY_ABSPATH . 'inc/frontend/class-lty-lottery-cart.php';
			include_once LTY_ABSPATH . 'inc/frontend/class-lty-lottery-frontend.php';
			include_once LTY_ABSPATH . 'inc/frontend/class-lty-dashboard.php';
			include_once LTY_ABSPATH . 'inc/frontend/class-lty-shortcodes.php';
			include_once LTY_ABSPATH . 'inc/frontend/class-lty-product-page-shortcodes.php';
			include_once LTY_ABSPATH . 'inc/frontend/class-lty-shortcode-products.php';
			include_once LTY_ABSPATH . 'inc/frontend/class-lty-lottery-page-handler.php';
			include_once LTY_ABSPATH . 'inc/frontend/class-lty-myaccount-handler.php';
		}

		/**
		 * Define the hooks.
		 * */
		private function init_hooks() {
			// WC compatibility to the plugin.
			add_action('before_woocommerce_init', array( $this, 'declare_WC_compatibility' ));
			// Init the plugin.
			add_action('init', array( $this, 'init' ));

			add_action('plugins_loaded', array( $this, 'plugins_loaded' ), 99);

			// Register the plugin.
			register_activation_hook(LTY_PLUGIN_FILE, array( 'LTY_Install', 'install' ));
		}

		/**
		 * Declare the plugin is compatibility with WC features.
		 *
		 * @since 9.9.0
		 * @return void
		 */
		public function declare_WC_compatibility() {
			// HPOS compatibility.
			$this->declare_WC_HPOS_compatibility();

			// Block compatibility.
			$this->declare_WC_Block_compatibility();
		}

		/**
		 * Declare the plugin is compatibility with WC HPOS.
		 *
		 * @since 8.3.1
		 * @return void
		 */
		public function declare_WC_HPOS_compatibility() {
			if (class_exists(\Automattic\WooCommerce\Utilities\FeaturesUtil::class)) {
				\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility('custom_order_tables', LTY_PLUGIN_FILE, true);
			}
		}

		/**
		 * Declare the plugin is compatibility with WC block.
		 *
		 * @since 9.9.0
		 * @return void
		 */
		public function declare_WC_Block_compatibility() {
			if (class_exists('\Automattic\WooCommerce\Utilities\FeaturesUtil')) {
				\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility('cart_checkout_blocks', LTY_PLUGIN_FILE, true);
			}
		}

		/**
		 * Init.
		 * */
		public function init() {
			$this->load_plugin_textdomain();

			$this->widgets       = LTY_Widget_Instances::get_widgets();
			$this->notifications = LTY_Notification_Instances::get_notifications();
		}

		/**
		 * Plugins Loaded.
		 * */
		public function plugins_loaded() {
			/**
			 * This hook is used to do extra action before plugin loaded.
			 *
			 * @since 1.0
			 */
			do_action('lty_before_plugin_loaded');

			include_once LTY_ABSPATH . 'inc/abstracts/class-lty-lottery-product-data.php';
			include_once LTY_ABSPATH . 'inc/entity/class-lty-lottery-product.php';

			LTY_Compatibility_Instances::instance();

			/**
			 * This hook is used to do extra action after plugin loaded.
			 *
			 * @since 1.0
			 */
			do_action('lty_after_plugin_loaded');
		}

		/**
		 * Templates.
		 * */
		public function templates() {
			return LTY_PLUGIN_PATH . '/templates/';
		}

		/**
		 * Widgets instances.
		 * */
		public function widgets() {
			return $this->widgets;
		}

		/**
		 * Notifications instances.
		 * */
		public function notifications() {
			return $this->notifications;
		}

		/**
		 * Get the menu name.
		 *
		 * @since 10.7.0
		 * @return string
		 * */
		public function menu_name() {
			return __( 'Giveaway', 'lottery-for-woocommerce' );
		}
	}

}
