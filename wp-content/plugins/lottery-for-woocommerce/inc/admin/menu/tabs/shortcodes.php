<?php

/**
 * Short codes Tab.
 *
 * @since 1.0.0
 * */
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}

if (class_exists('LTY_Shortcode_Tab')) {
	return new LTY_Shortcode_Tab();
}

/**
 * Class.
 * 
 * @since 1.0.0
 * */
class LTY_Shortcode_Tab extends LTY_Settings_Page {

	/**
	 * Constructor.
	 * 
	 * @since 1.0.0
	 * */
	public function __construct() {
		$this->id = 'shortcodes';
		$this->label = __('Shortcodes', 'lottery-for-woocommerce');
		$this->show_button = false;

		parent::__construct();
	}

	/**
	 * Display the short codes details.
	 * 
	 * @since 10.1.0
	 */
	public function output_extra_fields() {
		include_once LTY_ABSPATH . 'inc/admin/menu/views/shortcode/html-shortcodes.php';
	}

	/**
	 * Get the short code tabs.
	 * 
	 * @since 10.1.0
	 * @return array
	 */
	public static function get_shortcode_tabs() {
		/**
		 * This hook is used to alter the short code tabs.
		 * 
		 * @since 10.1.0
		 */
		return apply_filters('lty_shortcode_tabs', array(
			'common' => __('Common Shortcodes', 'lottery-for-woocommerce'),
			'product-page' => __('Single Product Page Shortcodes', 'lottery-for-woocommerce'),
			'parameters' => __('Parameters Value', 'lottery-for-woocommerce'),
			'example' => __('Example', 'lottery-for-woocommerce'),
		));
	}

	/**
	 * Get the common short codes.
	 * 
	 * @since 10.1.0
	 * @return array
	 */
	public static function get_common_shortcodes() {
		/**
		 * This hook is used to alter the common short codes.
		 * 
		 * @since 10.1.0
		 */
		return apply_filters('lty_common_shortcodes', array(
			'[lty_dashboard]' => array(
				'supported_parameters' => 'No',
				'usage' => __('Displays giveaway dashboard', 'lottery-for-woocommerce'),
			),
			'[lty_lottery_products_winners_list]' => array(
				'supported_parameters' => 'No',
				'usage' => __('Displays the list of giveaway winners', 'lottery-for-woocommerce'),
			),
			'[lty_lottery_winners_by_date]' => array(
				'supported_parameters' => 'order, posts_per_page, paginate, date_filter_number, date_filter_unit',
				'usage' => __('Displays the list of giveaway winners by date', 'lottery-for-woocommerce'),
			),
			'[lty_lottery_instant_winners_by_date]' => array(
				'supported_parameters' => 'order, posts_per_page, paginate, date_filter_number, date_filter_unit',
				'usage' => __('Displays the list of giveaway instant winners by date', 'lottery-for-woocommerce'),
			),
			'[lty_my_lottery_products]' => array(
				'supported_parameters' => 'order, orderby, posts_per_page, paginate, category',
				'usage' => __('Displays the giveaways which the user has participated', 'lottery-for-woocommerce'),
			),
			'[lty_all_lottery_products]' => array(
				'supported_parameters' => 'order, orderby, posts_per_page, paginate, category',
				'usage' => __('Displays all the giveaways', 'lottery-for-woocommerce'),
			),
			'[lty_ongoing_lottery_products]' => array(
				'supported_parameters' => 'order, orderby, posts_per_page, paginate, category',
				'usage' => __('Displays the On-going giveaways', 'lottery-for-woocommerce'),
			),
			'[lty_ending_soon_lottery_products]' => array(
				'supported_parameters' => 'order, orderby, posts_per_page, paginate, category',
				'usage' => __('Displays the giveaways which will end soon', 'lottery-for-woocommerce'),
			),
			'[lty_future_lottery_products]' => array(
				'supported_parameters' => 'order, orderby, posts_per_page, paginate, category',
				'usage' => __("Displays the giveaways which haven't started", 'lottery-for-woocommerce'),
			),
			'[lty_featured_lottery_products]' => array(
				'supported_parameters' => 'order, orderby, posts_per_page, paginate, category',
				'usage' => __('Displays the featured giveaway', 'lottery-for-woocommerce'),
			),
			'[lty_closed_lottery_products]' => array(
				'supported_parameters' => 'order, orderby, posts_per_page, paginate, category',
				'usage' => __('Displays the closed giveaways', 'lottery-for-woocommerce'),
			),
			'[lty_finished_lottery_products]' => array(
				'supported_parameters' => 'order, orderby, posts_per_page, paginate, category',
				'usage' => __('Displays the finished giveaways', 'lottery-for-woocommerce'),
			),
			'[lty_random_lottery_products]' => array(
				'supported_parameters' => 'order, orderby, posts_per_page, paginate, category',
				'usage' => __('Displays random giveaway products', 'lottery-for-woocommerce'),
			),
		));
	}

	/**
	 * Get the product page short codes.
	 * 
	 * @since 10.1.0
	 * @return array
	 */
	public static function get_product_page_shortcodes() {
		/**
		 * This hook is used to alter the product page short codes.
		 * 
		 * @since 10.1.0
		 */
		return apply_filters('lty_product_page_shortcodes', array(
			'[lty_lottery_details_tab]' => array(
				'supported_parameters' => 'product_id',
				'required_form' => false,
				'usage' => __('Displays the giveaway details tab', 'lottery-for-woocommerce'),
			),
			'[lty_lottery_status]' => array(
				'supported_parameters' => 'product_id',
				'required_form' => false,
				'usage' => __('Displays the giveaway status', 'lottery-for-woocommerce'),
			),
			'[lty_lottery_start_date]' => array(
				'supported_parameters' => 'product_id, display_timezone',
				'required_form' => false,
				'usage' => __('Displays the giveaway start date', 'lottery-for-woocommerce'),
			),
			'[lty_lottery_end_date]' => array(
				'supported_parameters' => 'product_id, display_timezone',
				'required_form' => false,
				'usage' => __('Displays the giveaway end date', 'lottery-for-woocommerce'),
			),
			'[lty_lottery_date_notice]' => array(
				'supported_parameters' => 'product_id',
				'required_form' => false,
				'usage' => __('Displays the giveaway date notice ( Start date, End date, Ended date )', 'lottery-for-woocommerce'),
			),
			'[lty_lottery_count_down_timer]' => array(
				'supported_parameters' => 'product_id',
				'required_form' => false,
				'usage' => __('Displays the giveaway count down timer', 'lottery-for-woocommerce'),
			),
			'[lty_lottery_minimum_tickets]' => array(
				'supported_parameters' => 'product_id',
				'required_form' => false,
				'usage' => __('Displays the giveaway minimum tickets', 'lottery-for-woocommerce'),
			),
			'[lty_lottery_maximum_tickets]' => array(
				'supported_parameters' => 'product_id',
				'required_form' => false,
				'usage' => __('Displays the giveaway maximum tickets', 'lottery-for-woocommerce'),
			),
			'[lty_lottery_maximum_tickets_per_user]' => array(
				'supported_parameters' => 'product_id',
				'required_form' => false,
				'usage' => __('Displays the giveaway maximum tickets per user', 'lottery-for-woocommerce'),
			),
			'[lty_lottery_minimum_tickets_per_user]' => array(
				'supported_parameters' => 'product_id',
				'required_form' => false,
				'usage' => __('Displays the giveaway minimum tickets per user', 'lottery-for-woocommerce'),
			),
			'[lty_lottery_maximum_tickets_per_user_notice]' => array(
				'supported_parameters' => 'product_id',
				'required_form' => false,
				'usage' => __('Displays the giveaway maximum tickets per user notice', 'lottery-for-woocommerce'),
			),
			'[lty_lottery_progress_bar]' => array(
				'supported_parameters' => 'product_id',
				'required_form' => false,
				'usage' => __('Displays the giveaway progress bar', 'lottery-for-woocommerce'),
			),
			'[lty_lottery_tickets_sold_percentage]' => array(
				'supported_parameters' => 'product_id, decimal_count',
				'required_form' => false,
				'usage' => __('Displays the giveaway tickets sold percentage', 'lottery-for-woocommerce'),
			),
			'[lty_lottery_tickets_sold_count]' => array(
				'supported_parameters' => 'product_id, decimal_count',
				'required_form' => false,
				'usage' => __('Displays the sold tickets count', 'lottery-for-woocommerce'),
			),
			'[lty_lottery_winning_item]' => array(
				'supported_parameters' => 'product_id',
				'required_form' => false,
				'usage' => __('Displays the giveaway winning item', 'lottery-for-woocommerce'),
			),
			'[lty_lottery_question_answer]' => array(
				'supported_parameters' => 'product_id',
				'required_form' => true,
				'usage' => __('Displays the giveaway question answer', 'lottery-for-woocommerce'),
			),
			'[lty_lottery_quantity_selector]' => array(
				'supported_parameters' => 'product_id',
				'required_form' => true,
				'usage' => __('Displays the giveaway quantity selector', 'lottery-for-woocommerce'),
			),
			'[lty_lottery_participate_button]' => array(
				'supported_parameters' => 'product_id',
				'required_form' => true,
				'usage' => __('Displays the giveaway participate button', 'lottery-for-woocommerce'),
			),
			'[lty_lottery_predefined_buttons]' => array(
				'supported_parameters' => 'product_id',
				'required_form' => true,
				'usage' => __('Displays the giveaway predefined buttons', 'lottery-for-woocommerce'),
			),
			'[lty_lottery_predefined_button_url]' => array(
				'supported_parameters' => 'product_id',
				'required_form' => false,
				'usage' => __('Displays the giveaway predefined button url', 'lottery-for-woocommerce'),
			),
			'[lty_lottery_predefined_button_amount]' => array(
				'supported_parameters' => 'product_id, button_key',
				'required_form' => false,
				'usage' => __('Displays the giveaway predefined button amount', 'lottery-for-woocommerce'),
			),
			'[lty_lottery_predefined_button_total_amount]' => array(
				'supported_parameters' => 'product_id, button_key',
				'required_form' => false,
				'usage' => __('Displays the giveaway predefined button total amount', 'lottery-for-woocommerce'),
			),
			'[lty_lottery_predefined_button_discount]' => array(
				'supported_parameters' => 'product_id, button_key',
				'required_form' => false,
				'usage' => __('Displays the giveaway predefined button discount', 'lottery-for-woocommerce'),
			),
			'[lty_lottery_predefined_button_tickets_quantity]' => array(
				'supported_parameters' => 'product_id, button_key',
				'required_form' => false,
				'usage' => __('Displays the giveaway predefined button tickets quantity', 'lottery-for-woocommerce'),
			),
			'[lty_instant_win_prizes]' => array(
				'supported_parameters' => 'product_id',
				'required_form' => false,
				'usage' => __('Displays instant win prizes table', 'lottery-for-woocommerce'),
			),
			'[lty_user_chooses_ticket]' => array(
				'supported_parameters' => 'product_id',
				'required_form' => true,
				'usage' => __('Displays the giveaway ticket numbers without giveaway details', 'lottery-for-woocommerce'),
			),
			'[lty_lucky_dip]' => array(
				'supported_parameters' => 'product_id',
				'required_form' => false,
				'usage' => __('Displays the giveaway lucky dip', 'lottery-for-woocommerce'),
			),
			'[lty_lucky_dip_fixed_quantity]' => array(
				'supported_parameters' => 'product_id, quantity',
				'required_form' => false,
				'usage' => __('Displays the lucky dip button with fixed quantity value(Enter the quantity value in the shortcode parameter)', 'lottery-for-woocommerce'),
			),
		));
	}

	/**
	 * Get the short code parameter value.
	 * 
	 * @since 10.1.0
	 * @return array
	 */
	public static function get_shortcode_parameter_values() {
		/**
		 * This hook is used to alter the short code parameter values.
		 * 
		 * @since 10.1.0
		 */
		return apply_filters('lty_shortcode_parameter_values', array(
			'order' => 'ASC, DESC',
			'posts_per_page' => 'any number',
			'paginate' => 'true/false',
			'orderby' => 'rand, title, date, start_date, end_date, finished_date, closed_date, failed_date, remaining_ticket_count',
			'category' => 'category slug separated by comma',
			'short_description' => 'true/false',
			'date_filter_number' => 'any number',
			'date_filter_unit' => 'days, weeks, months, years',
			'product_id' => 'any number',
			'display_timezone' => 'true/false',
			'button_key' => 'any number',
			'decimal_count' => 'any number',
		));
	}
}

return new LTY_Shortcode_Tab();
