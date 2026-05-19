<?php

/**
 * Pages.
 * */
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}

if (!class_exists('LTY_Pages')) {

	/**
	 * Class.
	 */
	class LTY_Pages {
		/*
		 * Plugin Slug
		 */

		protected static $plugin_slug = 'lty';

		/**
		 * Create pages
		 * */
		public static function create_pages() {
			/**
			 * This hook is used to alter the create pages.
			 *
			 * @since 1.0
			 */
			$pages = apply_filters(
					self::$plugin_slug . '_create_pages', array(
				'lottery' => array(
					'name' => esc_html_x('Giveaway', 'Page slug', 'lottery-for-woocommerce'),
					'title' => esc_html_x('Giveaway', 'Page title', 'lottery-for-woocommerce'),
					'content' => '',
					'option' => 'woocommerce_lty_lottery_page_id',
					),
					'lottery_entry_list' => array(
					'name' => esc_html_x('giveaway entry list', 'Page slug', 'lottery-for-woocommerce'),
					'title' => esc_html_x('Giveaway Entry List', 'Page title', 'lottery-for-woocommerce'),
					'content' => '',
					'option' => 'woocommerce_lty_lottery_entry_list_page_id',
					),
					'lty_dashboard' => array(
					'name' => esc_html_x('dashboard', 'Page slug', 'lottery-for-woocommerce'),
					'title' => esc_html_x('Giveaway Dashboard', 'Page title', 'lottery-for-woocommerce'),
					'content' => '[lty_dashboard]',
					'option' => 'woocommerce_lty_dashboard_page_id',
					),
					)
			);

			foreach ($pages as $page_args) {
				self::create($page_args);
			}
		}

		/**
		 * Create page.
		 * */
		public static function create( $page_args = array() ) {

			$defalut_page_args = array(
				'name' => '',
				'title' => '',
				'content' => '',
				'option' => '',
			);

			$page_args = wp_parse_args($page_args, $defalut_page_args);

			$option_value = get_option($page_args['option']);
			$page_object = get_post($option_value);

			if (!empty($page_args['option']) && $page_object) {
				if ('page' == $page_object->post_type) {
					if (!in_array($page_object->post_status, array( 'pending', 'trash', 'future', 'auto-draft' ))) {
						return $page_object->ID;
					}
				}
			}

			$page_data = array(
				'post_status' => 'publish',
				'post_type' => 'page',
				'post_author' => 1,
				'post_name' => esc_sql($page_args['name']),
				'post_title' => $page_args['title'],
				'post_content' => $page_args['content'],
				'comment_status' => 'closed',
			);

			$page_id = wp_insert_post($page_data);

			if ($page_args['option']) {
				update_option($page_args['option'], $page_id);
			}

			return $page_id;
		}

		/**
		 * Class Initialization.
		 * */
		public static function init() {
			add_filter('display_post_states', array( __CLASS__, 'post_states' ), 10, 2);
		}

		/**
		 * Denotes the post states as such in the pages list table.
		 * */
		public static function post_states( $post_states, $post ) {

			if (wc_get_page_id('lty_lottery') == $post->ID) {
				$post_states[self::$plugin_slug . '_lottery_page'] = __('Giveaway Products Page', 'lottery-for-woocommerce');
			} elseif (wc_get_page_id('lty_lottery_entry_list') == $post->ID) {
				$post_states[self::$plugin_slug . '_lottery_entry_list_page'] = __('Giveaway Entry List Page', 'lottery-for-woocommerce');
			} elseif (wc_get_page_id('lty_dashboard') == $post->ID) {
				$post_states[self::$plugin_slug . '_lty_dashboard_page'] = __('Giveaway Dashboard', 'lottery-for-woocommerce');
			}

			return $post_states;
		}
	}

	LTY_Pages::init();
}
