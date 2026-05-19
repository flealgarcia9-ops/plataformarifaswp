<?php
/**
 * Plugin Name: Giveaway (formerly Lottery) for WooCommerce
 * Description: Giveaway (formerly Lottery) for WooCommerce can help you to create and manage Online Giveaways on your WooCommerce Shop.
 * Version: 12.5.0
 *
 * Author: Flintop
 * Author URI: https://woo.com/vendor/flintop/
 *
 * Text Domain: lottery-for-woocommerce
 * Domain Path: /languages
 *
 * Requires at least: 6.2
 * WC requires at least: 8.2.0
 * Tested up to: 6.9
 * WC tested up to: 10.4.3
 * Requires PHP: 7.4.0
 *
 * Requires Plugins: woocommerce
 *
 * Copyright: © 2026 Flintop
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 * Woo: 5860289:211fe3453a9e8918bd090de1c7984310

 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Include once will help to avoid fatal error by load the files when you call init hook.
 * */
require_once ABSPATH . 'wp-admin/includes/plugin.php';

/**
 * Include main class file.
 * */
if ( ! class_exists( 'FP_Lottery' ) ) {
	include_once 'inc/class-lottery.php';
}


if ( ! function_exists( 'lty_is_plugin_active' ) ) {

	/**
	 * Is plugin active?
	 *
	 * @return bool
	 */
	function lty_is_plugin_active() {
		if ( lty_is_valid_wordpress_version() && lty_is_woocommerce_active() && lty_is_valid_woocommerce_version() ) {
			return true;
		}

		add_action( 'admin_notices', 'lty_display_warning_message' );

		return false;
	}
}

if ( ! function_exists( 'lty_is_woocommerce_active' ) ) {

	/**
	 * Function to check whether WooCommerce is active or not.
	 *
	 * @return bool
	 */
	function lty_is_woocommerce_active() {
		$return = true;
		// This condition is for multi site installation.
		if ( is_multisite() && ! is_plugin_active_for_network( 'woocommerce/woocommerce.php' ) && ! is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
			$return = false;
			// This condition is for single site installation.
		} elseif ( ! is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
			$return = false;
		}

		return $return;
	}
}

if ( ! function_exists( 'lty_is_valid_wordpress_version' ) ) {

	/**
	 * Is valid WordPress version?
	 *
	 * @return bool
	 */
	function lty_is_valid_wordpress_version() {
		if ( version_compare( get_bloginfo( 'version' ), FP_Lottery::$wp_minimum_version, '<' ) ) {
			return false;
		}

		return true;
	}
}

if ( ! function_exists( 'lty_is_valid_woocommerce_version' ) ) {

	/**
	 * Is valid WooCommerce version?
	 *
	 * @return bool
	 */
	function lty_is_valid_woocommerce_version() {
		if ( version_compare( get_option( 'woocommerce_db_version' ), FP_Lottery::$wc_minimum_version, '<' ) ) {
			return false;
		}

		return true;
	}
}

if ( ! function_exists( 'lty_display_warning_message' ) ) {

	/**
	 * Display the WooCommere is not active warning message.
	 */
	function lty_display_warning_message() {
		$notice = '';

		if ( ! lty_is_valid_wordpress_version() ) {
			$notice = sprintf( 'This version of Giveaway (formerly Lottery) for WooCommerce requires WordPress %1s or newer.', FP_Lottery::$wp_minimum_version );
		} elseif ( ! lty_is_woocommerce_active() ) {
			$notice = 'Giveaway (formerly Lottery) for WooCommerce Plugin will not work until WooCommerce Plugin is Activated. Please Activate the WooCommerce Plugin.';
		} elseif ( ! lty_is_valid_woocommerce_version() ) {
			$notice = sprintf( 'This version of Giveaway (formerly Lottery) for WooCommerce requires WooCommerce %1s or newer.', FP_Lottery::$wc_minimum_version );
		}

		if ( $notice ) {
			echo '<div class="error">';
			echo '<p>' . wp_kses_post( $notice ) . '</p>';
			echo '</div>';
		}
	}
}

// Return if the plugin is not active.
if ( ! lty_is_plugin_active() ) {
	return;
}

/**
 * Define constant.
 * */
if ( ! defined( 'LTY_PLUGIN_FILE' ) ) {
	define( 'LTY_PLUGIN_FILE', __FILE__ );
}

if ( ! function_exists( 'LTY' ) ) {

	/**
	 * Lottery class object.
	 *
	 * @return Object
	 * */
	function LTY() {
		return FP_Lottery::instance();
	}
}

/**
 * Initialize the plugin.
 * */
LTY();
