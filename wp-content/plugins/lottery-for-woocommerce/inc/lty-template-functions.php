<?php

/**
 * Template functions
 * */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! function_exists( 'lty_get_template' ) ) {

	/**
	 *  Get other templates from themes.
	 * */
	function lty_get_template( $template_name, $args = array() ) {

		wc_get_template( $template_name, $args, 'lottery-for-woocommerce/', LTY()->templates() );
	}
}

if ( ! function_exists( 'lty_get_template_html' ) ) {

	/**
	 *  Like lty_get_template, but returns the HTML instead of outputting.
	 *
	 *  @return string
	 * */
	function lty_get_template_html( $template_name, $args = array() ) {

		ob_start();
		lty_get_template( $template_name, $args );
		return ob_get_clean();
	}
}

if ( ! function_exists( 'lty_dashboard_menus' ) ) {

	/**
	 * Dashboard Menus.
	 *
	 * @return array
	 */
	function lty_dashboard_menus() {
		$participated_lotteries_url_param = lty_get_dashboard_participated_lotteries_endpoint_url();
		$won_lotteries_url_param          = lty_get_dashboard_won_lotteries_endpoint_url();
		$not_won_lotteries_url_param      = lty_get_dashboard_not_won_lotteries_endpoint_url();

		$dashboard_menus = array(
			$participated_lotteries_url_param => array(
				'label' => get_option( 'lty_settings_dashboard_participated_lottery_label' ),
				'code'  => 'clock',
			),
			$won_lotteries_url_param          => array(
				'label' => get_option( 'lty_settings_dashboard_won_lottery_label' ),
				'code'  => 'awards',
			),
			$not_won_lotteries_url_param      => array(
				'label' => get_option( 'lty_settings_dashboard_not_won_lottery_label' ),
				'code'  => 'no',
			),
		);

		if ( 'yes' === get_option( 'lty_settings_display_lottery_dashboard_instant_win', 'no' ) ) {
			$dashboard_menus[ lty_get_dashboard_instant_win_endpoint_url() ] = array(
				'label' => get_option( 'lty_settings_dashboard_instant_win_label', __( 'Instant Win', 'lottery-for-woocommerce' ) ),
				'code'  => 'awards',
			);
		}

		/**
		 * This hook is used to alter the frontend dashboard menus.
		 *
		 * @since 1.0.0
		 */
		return apply_filters( 'lty_frontend_dashboard_menus', $dashboard_menus );
	}
}

if ( ! function_exists( 'lty_dashboard_menu_columns' ) ) {

	/**
	 * Dashboard Menu Columns.
	 *
	 * @return array
	 */
	function lty_dashboard_menu_columns( $menu_name ) {
		$won_lotteries_url_param     = lty_get_dashboard_won_lotteries_endpoint_url();
		$not_won_lotteries_url_param = lty_get_dashboard_not_won_lotteries_endpoint_url();
		$instant_win_url_param       = lty_get_dashboard_instant_win_endpoint_url();

		switch ( $menu_name ) {
			case $won_lotteries_url_param:
				$columns['ticket_number']    = get_option( 'lty_settings_dashboard_won_lottery_ticket_name_label' );
				$columns['product_name']     = get_option( 'lty_settings_dashboard_won_lottery_product_name_label' );
				$columns['lottery_duration'] = get_option( 'lty_settings_dashboard_won_lottery_duration_label' );
				$columns['ticket_number']    = get_option( 'lty_settings_dashboard_won_lottery_ticket_number_label' );
				$columns['gift_product']     = get_option( 'lty_settings_dashboard_won_lottery_gift_product_label' );
				$columns['order_id']         = get_option( 'lty_settings_dashboard_won_lottery_order_id_label' );
				$columns['answer']           = get_option( 'lty_settings_dashboard_won_lottery_answer_label' );
				break;

			case $not_won_lotteries_url_param:
				$columns['ticket_number']    = get_option( 'lty_settings_dashboard_not_won_lottery_ticket_name_label' );
				$columns['product_name']     = get_option( 'lty_settings_dashboard_not_won_lottery_product_name_label' );
				$columns['lottery_duration'] = get_option( 'lty_settings_dashboard_not_won_lottery_duration_label' );
				$columns['ticket_number']    = get_option( 'lty_settings_dashboard_not_won_lottery_ticket_number_label' );
				$columns['answer']           = get_option( 'lty_settings_dashboard_not_won_lottery_answer_label' );
				break;

			case $instant_win_url_param:
				$columns['product_name']     = get_option( 'lty_settings_dashboard_instant_win_product_name_label', __( 'Product Name', 'lottery-for-woocommerce' ) );
				$columns['lottery_duration'] = get_option( 'lty_settings_dashboard_instant_win_lottery_duration_label', __( 'Duration', 'lottery-for-woocommerce' ) );
				$columns['order_id']         = get_option( 'lty_settings_dashboard_instant_win_order_id_label', __( 'Order Number', 'lottery-for-woocommerce' ) );
				$columns['ticket_number']    = get_option( 'lty_settings_dashboard_instant_win_ticket_number_label', __( 'Ticket Number', 'lottery-for-woocommerce' ) );
				$columns['prize_details']    = get_option( 'lty_settings_dashboard_instant_win_prize_details_label', __( 'Prize Details', 'lottery-for-woocommerce' ) );
				break;

			default:
				$columns['product_name']     = get_option( 'lty_settings_dashboard_participated_lottery_product_name_label' );
				$columns['lottery_duration'] = get_option( 'lty_settings_dashboard_participated_lottery_duration_label' );
				$columns['status']           = get_option( 'lty_settings_dashboard_participated_lottery_status_label' );
				$columns['ticket_number']    = get_option( 'lty_settings_dashboard_participated_lottery_ticket_number_label' );
				break;
		}

		/**
		 * This hook is used to alter the dashboard extra columns.
		 *
		 * @since 1.0
		 */
		return apply_filters( 'lty_dashboard_extra_columns', $columns, $menu_name );
	}
}

if ( ! function_exists( 'lty_get_ticket_logs_table_header' ) ) {

	/**
	 * Ticket Logs Table Header.
	 *
	 * @return array
	 * */
	function lty_get_ticket_logs_table_header( $product ) {

		// Check if the product is a object.
		if ( ! is_object( $product ) ) {
			return array();
		}

		$_columns = array(
			'date'          => lty_get_single_product_tab_date_label(),
			'user_name'     => get_option( 'lty_settings_single_product_tab_username_label' ),
			'ticket_number' => get_option( 'lty_settings_single_product_tab_ticket_number_label' ),
		);

		if ( '1' == get_option( 'lty_settings_single_product_tab_show_chosen_answer_column', 1 ) && $product->is_valid_question_answer() && $product->has_lottery_status( 'lty_lottery_finished' ) ) {
			$_columns['answer'] = lty_get_single_product_tab_answer_label();
		}

		/**
		 * This hook is used to alter the ticket logs extra columns.
		 *
		 * @since 1.0
		 */
		return apply_filters( 'lty_ticket_logs_extra_columns', $_columns, $product );
	}
}

if ( ! function_exists( 'lty_get_question_answer_dropdown_default_label' ) ) {

	/**
	 * Get the question answer dropdown default label.
	 *
	 * @since 8.2.0
	 * @return string
	 * */
	function lty_get_question_answer_dropdown_default_label() {

		/**
		 * This hook is used to alter the question answer dropdown default label.
		 *
		 * @since 8.2.0
		 */
		return apply_filters( 'lty_question_answer_dropdown_default_label', get_option( 'lty_settings_question_answer_dropdown_default_label', __( 'Choose Answer', 'lottery-for-woocommerce' ) ) );
	}
}

if ( ! function_exists( 'lty_get_lottery_winner_table_header' ) ) {

	/**
	 * Ticket Logs Table Header.
	 *
	 * @return array
	 * */
	function lty_get_lottery_winner_table_header( $product ) {

		// Check if the product is a object.
		if ( ! is_object( $product ) ) {
			return array();
		}

		$column_val = array();

		if ( '2' != get_option( 'lty_settings_single_product_lottery_username_toggle' ) ) {
			$column_val['username'] = get_option( 'lty_settings_single_product_lottery_username_label' );
		}

		if ( '2' != get_option( 'lty_settings_single_product_lottery_ticket_number_toggle' ) ) {
			$column_val['ticket_number'] = get_option( 'lty_settings_single_product_lottery_ticket_number_label' );
		}

		if ( '2' != get_option( 'lty_settings_single_product_lottery_gift_product_toggle' ) ) {
			$column_val['gift_product'] = get_option( 'lty_settings_single_product_lottery_gift_product_label' );
		}

		if ( '2' != get_option( 'lty_settings_single_product_lottery_answer_toggle' ) && $product->is_valid_question_answer() ) {
			$column_val['answer'] = get_option( 'lty_settings_single_product_lottery_answer_label' );
		}

		/**
		 * This hook is used to alter the ticket logs extra columns.
		 *
		 * @since 1.0
		 */
		return apply_filters( 'lty_ticket_logs_extra_columns', $column_val );
	}
}

if ( ! function_exists( 'lty_get_lottery_shortcode_winner_table_header' ) ) {

	/**
	 * Shortcode Winner table header
	 *
	 * @retrun array
	 * */
	function lty_get_lottery_shortcode_winner_table_header() {
		/**
		 * This hook is used to alter the product winner lists.
		 *
		 * @since 1.0
		 */
		$winner_data = apply_filters(
			'lty_lottery_product_winners_lists',
			array(
				'sno'                  => get_option( 'lty_settings_winners_list_shortcode_sno_label', __( 'S.No', 'lottery-for-woocommerce' ) ),
				'winners_name'         => get_option( 'lty_settings_winners_list_shortcode_winners_name_label', __( 'Winners Name', 'lottery-for-woocommerce' ) ),
				'ticket_number'        => get_option( 'lty_settings_winners_list_shortcode_ticket_number_label', __( 'Ticket Number', 'lottery-for-woocommerce' ) ),
				'lottery_product_name' => get_option( 'lty_settings_winners_list_shortcode_product_name_label', __( 'Product Name', 'lottery-for-woocommerce' ) ),
				'lottery_start_date'   => get_option( 'lty_settings_winners_list_shortcode_start_date_label', __( 'Start Date', 'lottery-for-woocommerce' ) ),
				'lottery_end_date'     => get_option( 'lty_settings_winners_list_shortcode_end_date_label', __( 'End Date', 'lottery-for-woocommerce' ) ),
				'gift_products'        => get_option( 'lty_settings_winners_list_shortcode_gift_products_label', __( 'Gift Products', 'lottery-for-woocommerce' ) ),
			)
		);

		if ( '2' === get_option( 'lty_settings_single_product_lottery_username_toggle', 1 ) ) {
			unset( $winner_data['winners_name'] );
		}

		if ( '2' === get_option( 'lty_settings_single_product_lottery_ticket_number_toggle' ) ) {
			unset( $winner_data['ticket_number'] );
		}

		if ( '2' === get_option( 'lty_settings_single_product_lottery_gift_product_toggle' ) ) {
			unset( $winner_data['gift_products'] );
		}

		return $winner_data;
	}
}

if ( ! function_exists( 'lty_get_shop_page_start_label' ) ) {

	/**
	 * Get the label for shop page start label.
	 *
	 * @return string.
	 * */
	function lty_get_shop_page_start_label() {
		/**
		 * This hook is used to alter the lottery start label in shop page.
		 *
		 * @since 1.0
		 */
		return apply_filters( 'lty_lottery_shop_page_start_label', get_option( 'lty_settings_shop_lottery_start_label', __( 'Start On', 'lottery-for-woocommerce' ) ) );
	}
}

if ( ! function_exists( 'lty_get_shop_page_end_label' ) ) {

	/**
	 * Get the label for shop page end label.
	 *
	 * @return string.
	 * */
	function lty_get_shop_page_end_label() {
		/**
		 * This hook is used to alter the lottery end label in shop page.
		 *
		 * @since 1.0
		 */
		return apply_filters( 'lty_lottery_shop_page_end_label', get_option( 'lty_settings_shop_lottery_ends_label', __( 'Ends On', 'lottery-for-woocommerce' ) ) );
	}
}

if ( ! function_exists( 'lty_get_shop_page_timer_days_label' ) ) {

	/**
	 * Get the label for shop page countdown timer days.
	 *
	 * @return string.
	 * */
	function lty_get_shop_page_timer_days_label() {
		/**
		 * This hook is used to alter the lottery timer days label in shop page.
		 *
		 * @since 1.0
		 */
		return apply_filters( 'lty_shop_page_timer_days_label', get_option( 'lty_settings_shop_lottery_days_label', __( 'Days', 'lottery-for-woocommerce' ) ) );
	}
}

if ( ! function_exists( 'lty_get_shop_page_timer_hours_label' ) ) {

	/**
	 * Get the label for shop page countdown timer hours.
	 *
	 * @return string.
	 * */
	function lty_get_shop_page_timer_hours_label() {
		/**
		 * This hook is used to alter the lottery timer hours label in shop page.
		 *
		 * @since 1.0
		 */
		return apply_filters( 'lty_shop_page_timer_hours_label', get_option( 'lty_settings_shop_lottery_hours_label', __( 'Hours', 'lottery-for-woocommerce' ) ) );
	}
}

if ( ! function_exists( 'lty_get_shop_page_timer_minutes_label' ) ) {

	/**
	 * Get the label for shop page countdown timer minutes.
	 *
	 * @return string.
	 * */
	function lty_get_shop_page_timer_minutes_label() {
		/**
		 * This hook is used to alter the lottery timer minutes label in shop page.
		 *
		 * @since 1.0
		 */
		return apply_filters( 'lty_shop_page_timer_minutes_label', get_option( 'lty_settings_shop_lottery_minutes_label', __( 'Minutes', 'lottery-for-woocommerce' ) ) );
	}
}

if ( ! function_exists( 'lty_get_shop_page_timer_seconds_label' ) ) {

	/**
	 * Get the label for shop page countdown timer seconds.
	 *
	 * @return string.
	 * */
	function lty_get_shop_page_timer_seconds_label() {
		/**
		 * This hook is used to alter the lottery timer seconds label in shop page.
		 *
		 * @since 1.0
		 */
		return apply_filters( 'lty_shop_page_timer_seconds_label', get_option( 'lty_settings_shop_lottery_seconds_label', __( 'Seconds', 'lottery-for-woocommerce' ) ) );
	}
}

if ( ! function_exists( 'lty_get_single_product_page_start_label' ) ) {

	/**
	 * Get the label for single product page start label.
	 *
	 * @return string.
	 * */
	function lty_get_single_product_page_start_label( $product_id, $product ) {
		// Check if the product is a object.
		if ( ! is_object( $product ) ) {
			return '';
		}

		$display_tz  = 'yes' === get_option( 'lty_settings_hide_tz_display_in_single_product_page' ) ? false : true;
		$start_label = sprintf( '<b>' . get_option( 'lty_settings_single_product_start_label' ) . ':</b> %s', LTY_Date_Time::get_wp_format_datetime( $product->get_lty_start_date(), false, false, false, ' ', $display_tz ) );

		/**
		 * This hook is used to alter the lottery start label in single product page.
		 *
		 * @since 1.0
		 */
		return apply_filters( 'lty_lottery_single_product_start_label', $start_label );
	}
}

if ( ! function_exists( 'lty_get_single_product_page_end_label' ) ) {

	/**
	 * Get the label for single product page end label.
	 *
	 * @since 1.0.0
	 * @param object $product Product object.
	 * @return string
	 */
	function lty_get_single_product_page_end_label( $product ) {
		// Check is lottery product.
		if ( ! lty_is_lottery_product( $product ) ) {
			return '';
		}

		$display_tz = 'yes' !== get_option( 'lty_settings_hide_tz_display_in_single_product_page' );
		if ( $product->is_closed() ) {
			$end_label = sprintf( '<b>' . get_option( 'lty_settings_single_product_ended_label') . ':</b> %s', LTY_Date_Time::get_wp_format_datetime( $product->get_lty_closed_date(), false, false, false, ' ', $display_tz ) );
		} else {
			$end_label = sprintf( '<b>' . get_option( 'lty_settings_single_product_end_label') . ':</b> %s', LTY_Date_Time::get_wp_format_datetime( $product->get_lty_end_date(), false, false, false, ' ', $display_tz ) );
		}

		/**
		 * This hook is used to alter the lottery end label in single product page.
		 *
		 * @since 1.0.0
		 */
		return apply_filters( 'lty_lottery_single_product_end_label', $end_label );
	}
}

if ( ! function_exists( 'lty_get_single_product_timer_days_label' ) ) {

	/**
	 * Get the label for single product page countdown timer days.
	 *
	 * @return string.
	 * */
	function lty_get_single_product_timer_days_label() {
		/**
		 * This hook is used to alter the lottery timer days label in single product page.
		 *
		 * @since 1.0
		 */
		return apply_filters( 'lty_single_product_timer_days_label', get_option( 'lty_settings_single_product_days_label', __( 'Days', 'lottery-for-woocommerce' ) ) );
	}
}

if ( ! function_exists( 'lty_get_single_product_timer_hours_label' ) ) {

	/**
	 * Get the label for single product page countdown timer hours.
	 *
	 * @return string.
	 * */
	function lty_get_single_product_timer_hours_label() {
		/**
		 * This hook is used to alter the lottery timer hours label in single product page.
		 *
		 * @since 1.0
		 */
		return apply_filters( 'lty_single_product_timer_hours_label', get_option( 'lty_settings_single_product_hours_label', __( 'Hours', 'lottery-for-woocommerce' ) ) );
	}
}

if ( ! function_exists( 'lty_get_single_product_timer_minutes_label' ) ) {

	/**
	 * Get the label for single product page countdown timer minutes.
	 *
	 * @return string.
	 * */
	function lty_get_single_product_timer_minutes_label() {
		/**
		 * This hook is used to alter the lottery timer minutes label in single product page.
		 *
		 * @since 1.0
		 */
		return apply_filters( 'lty_single_product_timer_minutes_label', get_option( 'lty_settings_single_product_minutes_label', __( 'Minutes', 'lottery-for-woocommerce' ) ) );
	}
}

if ( ! function_exists( 'lty_get_single_product_timer_seconds_label' ) ) {

	/**
	 * Get the label for single product page countdown timer seconds.
	 *
	 * @return string.
	 * */
	function lty_get_single_product_timer_seconds_label() {
		/**
		 * This hook is used to alter the lottery timer seconds label in single product page.
		 *
		 * @since 1.0
		 */
		return apply_filters( 'lty_single_product_timer_seconds_label', get_option( 'lty_settings_single_product_seconds_label', __( 'Seconds', 'lottery-for-woocommerce' ) ) );
	}
}

if ( ! function_exists( 'lty_get_add_to_cart_button_classes' ) ) {

	/**
	 * Get the classes for single product page add to cart button.
	 *
	 * @return array.
	 * */
	function lty_get_add_to_cart_button_classes( $product ) {

		// Check if the product is a object.
		if ( ! is_object( $product ) ) {
			return array();
		}

		$classes = array(
			'single_add_to_cart_button',
			'button',
			'alt',
			'lty-participate-now-button',
		);

		if ( $product->is_manual_ticket() || ( $product->is_valid_question_answer() && 'yes' == $product->is_force_answer_enabled() ) ) {
			$classes[] = 'lty_manual_add_to_cart';
		}

		if ( '2' === get_option( 'lty_settings_quantity_selector_type' ) ) {
			$classes[] = 'lty-range-slider';
		}
		/**
		 * This hook is used to alter the lottery add to cart button classes in single product page.
		 *
		 * @since 1.0
		 */
		return apply_filters( 'lty_single_product_add_to_cart_button_classes', $classes, $product );
	}
}

if ( ! function_exists( 'lty_get_quantity_input_arguments' ) ) {

	/**
	 * Get the arguments for single product page quantity input.
	 *
	 * @return array.
	 * */
	function lty_get_quantity_input_arguments( $product ) {

		// Check if the product is a object.
		if ( ! is_object( $product ) ) {
			return array();
		}

		$qty_value = isset( $_REQUEST['quantity'] ) ? wc_stock_amount( wc_clean( wp_unslash( $_REQUEST['quantity'] ) ) ) : $product->get_min_purchase_quantity(); // WPCS: CSRF ok, input var ok.

		$quantity_array = array(
			/**
			 * This hook is used to alter the add to cart input quantity minimum.
			 *
			 * @since 1.0
			 */
			'min_value'   => apply_filters( 'lty_quantity_input_min', $product->get_min_purchase_quantity(), $product ),
			/**
			 * This hook is used to alter the add to cart input quantity maximum.
			 *
			 * @since 1.0
			 */
			'max_value'   => apply_filters( 'lty_quantity_input_max', $product->get_max_purchase_quantity(), $product ),
			/**
			 * This hook is used to alter the add to cart input quantity value.
			 *
			 * @since 1.0
			 */
			'input_value' => apply_filters( 'lty_quantity_input_value', $qty_value, $product ),
		);

		/**
		 * This hook is used to alter the lottery product quantity input arguments in single product page.
		 *
		 * @since 1.0
		 */
		return apply_filters( 'lty_single_product_quantity_input_arguments', $quantity_array, $product );
	}
}

if ( ! function_exists( 'lty_get_ticket_tabs' ) ) {

	/**
	 * Get the tabs for single product page ticket.
	 *
	 * @return string.
	 * */
	function lty_get_ticket_tabs( $product ) {
		$ticket_tabs = array();
		// Check if the product is a object.
		if ( ! is_object( $product ) ) {
			return $ticket_tabs;
		}

		$start_range = $product->get_ticket_start_number();
		$tab_count   = (int) ceil( $product->get_lty_maximum_tickets() / $product->get_lty_tickets_per_tab() );
		$end_range   = ( $product->get_lty_tickets_per_tab() > $product->get_lty_maximum_tickets() ) ? $product->get_lty_maximum_tickets() : $product->get_lty_tickets_per_tab();
		$end_range   = $end_range + $start_range - 1;
		$index       = 0;
		for ( $start_tab = 1; $start_tab <= $tab_count; $start_tab++ ) {

			$ticket_tabs[ $start_range ] = $product->format_ticket_tab_name( $start_range, $end_range, $index );

			$start_range = $start_range + $product->get_lty_tickets_per_tab();

			$end_range = $start_range + $product->get_lty_tickets_per_tab() - 1;

			$end_range = $end_range > ( $product->get_lty_maximum_tickets() + $product->get_ticket_start_number() - 1 ) ? $product->get_lty_maximum_tickets() + $product->get_ticket_start_number() - 1 : $end_range;

			++$index;
		}

		/**
		 * This hook is used to alter the lottery tickets tabs.
		 *
		 * @since 1.0
		 */
		return apply_filters( 'lty_get_ticket_tabs', $ticket_tabs, $product );
	}
}

if ( ! function_exists( 'lty_get_lucky_dip_button_classes' ) ) {

	/**
	 * Get the classes for single product page lucky dip button.
	 *
	 * @return array.
	 * */
	function lty_get_lucky_dip_button_classes( $product ) {

		// Check if the product is a object.
		if ( ! is_object( $product ) ) {
			return array();
		}

		$classes = array(
			'button',
			'alt',
			'lty-lucky-dip-button',
		);

		$classes[] = '2' === $product->get_lty_lucky_dip_method_type() ? 'lty-regenerate-lucky-dip-button' : 'lty-add-to-cart-lucky-dip-button';
		if ( $product->is_valid_question_answer() && 'yes' == $product->is_force_answer_enabled() ) {
			$classes[] = 'lty_manual_add_to_cart';
		}
		/**
		 * This hook is used to alter the lottery lucky dip button classes in single product page.
		 *
		 * @since 1.0
		 */
		return apply_filters( 'lty_single_product_lucky_dip_button_classes', $classes, $product );
	}
}

if ( ! function_exists( 'lty_get_lucky_dip_quantity_input_arguments' ) ) {

	/**
	 * Get the arguments for single product page lucky dip quantity input.
	 *
	 * @return array.
	 * */
	function lty_get_lucky_dip_quantity_input_arguments( $product ) {

		// Check if the product is a object.
		if ( ! is_object( $product ) ) {
			return array();
		}

		$quantity_array = array(
			/**
			 * This hook is used to alter the lucky dip input quantity classes.
			 *
			 * @since 1.0
			 */
			'classes'     => apply_filters( 'lty_lucky_dip_quantity_input_classes', array( 'input-text', 'qty', 'text', 'lty-lucky-dip-quantity' ), $product ),
			/**
			 * This hook is used to alter the lucky dip input quantity minimum.
			 *
			 * @since 1.0
			 */
			'min_value'   => apply_filters( 'lty_lucky_dip_quantity_input_min', $product->get_min_purchase_quantity(), $product ),
			/**
			 * This hook is used to alter the lucky dip input quantity maximum.
			 *
			 * @since 1.0
			 */
			'max_value'   => apply_filters( 'lty_lucky_dip_quantity_input_max', $product->get_max_purchase_quantity(), $product ),
			/**
			 * This hook is used to alter the lucky dip input quantity value.
			 *
			 * @since 1.0
			 */
			'input_value' => apply_filters( 'lty_lucky_dip_quantity_input_value', $product->get_min_purchase_quantity(), $product ),
		);
		/**
		 * This hook is used to alter the lottery lucky dip quantity input arguments in single product page.
		 *
		 * @since 1.0
		 */
		return apply_filters( 'lty_single_product_lucky_dip_quantity_input_arguments', $quantity_array, $product );
	}
}

if ( ! function_exists( 'lty_get_progress_bar_maximum_tickets' ) ) {

	/**
	 * Get the progress bar maximum tickets.
	 *
	 * @return int.
	 * */
	function lty_get_progress_bar_maximum_tickets( $product ) {
		// Check if the product is a object.
		if ( ! is_object( $product ) ) {
			return 0;
		}
		/**
		 * This hook is used to alter the lottery progress bar maximum tickets.
		 *
		 * @since 1.0
		 */
		return apply_filters( 'lty_progress_bar_maximum_tickets', intval( $product->get_lty_maximum_tickets() ), $product );
	}
}

if ( ! function_exists( 'lty_get_progress_bar_percentage' ) ) {

	/**
	 * Get the progress bar percentage.
	 *
	 * @since 1.0.0
	 * @param object $product Product object.
	 * @return int|float
	 * */
	function lty_get_progress_bar_percentage( $product ) {
		// Check if the product is a object.
		if ( ! is_object( $product ) ) {
			return 0;
		}

		$percentage = $product->get_lty_maximum_tickets() ? ( $product->get_purchased_ticket_count() / intval( $product->get_lty_maximum_tickets() ) ) * 100 : 0;
		/**
		 * This hook is used to alter the lottery progress bar percentage.
		 *
		 * @since 1.0
		 */
		return apply_filters( 'lty_progress_bar_percentage', $percentage, $product );
	}
}

if ( ! function_exists( 'lty_get_product_page_progress_bar_percentage' ) ) {
	/**
	 * Get the product page progress bar percentage.
	 *
	 * @since 9.1.0
	 * @param object $product Product object.
	 * @return int|float
	 * */
	function lty_get_product_page_progress_bar_percentage( $product ) {
		$percentage = round( lty_get_progress_bar_percentage( $product ), 2 );

		return ( '2' === get_option( 'lty_settings_progress_bar_percentage_type_product_page', '1' ) ) ? round( $percentage ) : $percentage;
	}
}

if ( ! function_exists( 'lty_get_shop_page_progress_bar_percentage' ) ) {
	/**
	 * Get the shop page progress bar percentage.
	 *
	 * @since 9.1.0
	 * @param object $product Product object.
	 * @return int|float
	 * */
	function lty_get_shop_page_progress_bar_percentage( $product ) {
		$percentage = round( lty_get_progress_bar_percentage( $product ), 2 );

		return ( '2' === get_option( 'lty_settings_progress_bar_percentage_type_shop_page', '1' ) ) ? round( $percentage ) : $percentage;
	}
}

if ( ! function_exists( 'lty_get_progress_bar_remaining_ticket_label' ) ) {

	/**
	 * Get the progress bar remaining ticket message.
	 *
	 * @since 1.0.0
	 * @param object $product
	 * @return string.
	 * */
	function lty_get_progress_bar_remaining_ticket_label( $product ) {
		// Check if the product is a object.
		if ( ! is_object( $product ) ) {
			return '';
		}

		/* translators : %s: Remaining tickets */
		$remaining_tickets = sprintf( get_option( 'lty_settings_single_product_progress_bar_remaining_ticket_label', '%s Tickets remaining' ), $product->get_remaining_ticket_count() );
		/**
		 * This hook is used to alter the lottery progress bar remaining ticket label.
		 *
		 * @since 1.0
		 */
		return apply_filters( 'lty_single_product_progress_bar_remaining_ticket_label', $remaining_tickets, $product );
	}
}

if ( ! function_exists( 'lty_get_shop_remaining_tickets_message' ) ) {

	/**
	 * Get the shop page remaining tickets message.
	 *
	 * @since 10.3.0
	 * @param object $product Product object.
	 * @return string
	 * */
	function lty_get_shop_remaining_tickets_message( $product ) {
		// Return if it not an lottery product.
		if ( ! lty_is_lottery_product( $product ) ) {
			return '';
		}

		$remaining_tickets_message = str_replace(
			array( '{remaining_tickets}', '{maximum_tickets}' ),
			array( $product->get_remaining_ticket_count(), intval( $product->get_lty_maximum_tickets() ) ),
			get_option( 'lty_settings_shop_remaining_tickets_message', 'Remaining Tickets: {remaining_tickets}' )
		);

		/**
		 * This hook is used to alter the shop page remaining tickets label.
		 *
		 * @since 10.3.0
		 * @param string $remaining_tickets_message
		 * @param object $product
		 */
		return apply_filters( 'lty_shop_remaining_tickets_message', $remaining_tickets_message, $product );
	}
}

if ( ! function_exists( 'lty_get_progress_bar_notice' ) ) {
	/**
	 * Get the progress bar notice.
	 *
	 * @since 1.0.0
	 * @param object $product Product object.
	 * @return string
	 * */
	function lty_get_progress_bar_notice( $product ) {
		$message = str_replace( '{ticket_count}', $product->get_purchased_ticket_count(), get_option( 'lty_settings_single_product_progress_bar_ticket_sold_notice_label' ) );
		/**
		 * This hook is used to alter the lottery progress bar ticket sold notice.
		 *
		 * @since 1.0.0
		 */
		return apply_filters( 'lty_single_product_progress_bar_ticket_sold_notice_label', $message );
	}
}

if ( ! function_exists( 'lty_hide_progress_bar_ticket_remaining_message' ) ) {

	/**
	 * Hide the progress bar ticket remaining message.
	 *
	 * @return bool.
	 * */
	function lty_hide_progress_bar_ticket_remaining_message() {
		/**
		 * This hook is used to validate to show the lottery progress bar ticket remaining message.
		 *
		 * @since 1.0
		 */
		return apply_filters( 'lty_hide_progress_bar_ticket_remaining_message', ( 'yes' == get_option( 'lty_settings_hide_progress_bar_ticket_remaining_message' ) ) );
	}
}

if ( ! function_exists( 'lty_display_remaining_tickets_message_on_shop' ) ) {

	/**
	 * Display the remaining tickets message on shop page.
	 *
	 * @since 10.3.0
	 * @return bool
	 * */
	function lty_display_remaining_tickets_message_on_shop() {
		/**
		 * This hook is used to validate to show the remaining tickets message on shop page.
		 *
		 * @since 10.3.0
		 */
		return apply_filters( 'lty_display_remaining_tickets_message_on_shop', ( 'yes' === get_option( 'lty_settings_display_remaining_tickets_message_on_shop', 'no' ) ) );
	}
}

if ( ! function_exists( 'lty_hide_minimum_lottery_ticket_message' ) ) {

	/**
	 * Hide the minimum lottery ticket message in single product page.
	 *
	 * @return bool.
	 * */
	function lty_hide_minimum_lottery_ticket_message() {
		/**
		 * This hook is used to validate to show the lottery minimum ticket message.
		 *
		 * @since 1.0
		 */
		return apply_filters( 'lty_hide_minimum_ticket_message_single_product_page', 'yes' == get_option( 'lty_settings_hide_minimum_ticket_message_in_single_product_page' ) );
	}
}

if ( ! function_exists( 'lty_get_lottery_ticket_minimum_message' ) ) {

	/**
	 * Get the message for lottery ticket minimum.
	 *
	 * @since 1.0.0
	 * @param object $product Product object.
	 * @return string
	 * */
	function lty_get_lottery_ticket_minimum_message( $product ) {
		// Check if the product is a object.
		if ( ! is_object( $product ) ) {
			return '';
		}

		if ( $product->get_purchased_ticket_count() >= $product->get_lty_minimum_tickets() ) {
			return '';
		}

		$minimum_ticket_message = str_replace( '{lottery_minimum_ticket}', '<b>' . $product->get_lty_minimum_tickets() . '</b>', get_option( 'lty_settings_single_product_min_ticket_message' ) );
		/**
		 * This hook is used to alter the lottery minimum ticket message.
		 *
		 * @since 1.0.0
		 */
		return apply_filters( 'lty_lottery_ticket_minimum_message', $minimum_ticket_message, $product );
	}
}

if ( ! function_exists( 'lty_get_lottery_ticket_maximum_message' ) ) {

	/**
	 * Get the message for lottery ticket maximum.
	 *
	 * @return string.
	 * */
	function lty_get_lottery_ticket_maximum_message( $product ) {
		// Check if the product is a object.
		if ( ! is_object( $product ) ) {
			return '';
		}

		$maximum_ticket_message = sprintf( str_replace( '{lottery_maximum_ticket}', '<b>' . $product->get_lty_maximum_tickets() . '</b>', get_option( 'lty_settings_single_product_max_ticket_message' ) ) );
		/**
		 * This hook is used to alter the lottery maximum ticket message.
		 *
		 * @since 1.0
		 */
		return apply_filters( 'lty_lottery_ticket_maximum_message', $maximum_ticket_message, $product );
	}
}

if ( ! function_exists( 'lty_get_lottery_minimum_tickets_per_user_message' ) ) {

	/**
	 * Get lottery minimum tickets per user message.
	 *
	 * @return string.
	 * */
	function lty_get_lottery_minimum_tickets_per_user_message( $product ) {
		$message = str_replace( '{lottery_minimum_ticket_per_user}', '<b>' . absint( $product->get_min_purchase_quantity() ) . '</b>', get_option( 'lty_settings_single_product_min_tickets_per_user_message', 'This lottery to be purchased with a minimum of {lottery_minimum_ticket_per_user} tickets' ) );
		/**
		 * This hook is used to alter the lottery minimum ticket per user message.
		 *
		 * @since 1.0
		 */
		return apply_filters( 'lty_lottery_minimum_tickets_per_user_message', $message );
	}
}

if ( ! function_exists( 'lty_get_lottery_maximum_tickets_per_user_message' ) ) {

	/**
	 * Get lottery maximum tickets per user message.
	 *
	 * @return string.
	 * */
	function lty_get_lottery_maximum_tickets_per_user_message( $product ) {
		$message = str_replace( '{lottery_maximum_ticket_per_user}', '<b>' . absint( $product->get_max_purchase_quantity() ) . '</b>', get_option( 'lty_settings_single_product_max_tickets_per_user_message', 'This lottery to be purchased with a maximum of {lottery_maximum_ticket_per_user} tickets' ) );
		/**
		 * This hook is used to alter the lottery maximum ticket per user message.
		 *
		 * @since 1.0
		 */
		return apply_filters( 'lty_lottery_maximum_tickets_per_user_message', $message );
	}
}

if ( ! function_exists( 'lty_hide_maximum_lottery_ticket_message' ) ) {

	/**
	 * Hide the maximum lottery ticket message in single product page.
	 *
	 * @return bool.
	 * */
	function lty_hide_maximum_lottery_ticket_message() {
		/**
		 * This hook is used to validate to show the lottery maximum ticket message.
		 *
		 * @since 1.0
		 */
		return apply_filters( 'lty_hide_maximum_ticket_message_single_product_page', 'yes' == get_option( 'lty_settings_hide_maximum_ticket_message_in_single_product_page' ) );
	}
}

if ( ! function_exists( 'lty_lucky_dip_question_answer_hover_message' ) ) {

	/**
	 * Display lucky dip question answer hover message.
	 *
	 * @return string.
	 * */
	function lty_lucky_dip_question_answer_hover_message( $product ) {

		if ( ! $product->is_valid_question_answer() || 'yes' != $product->is_force_answer_enabled() ) {
			return '';
		}
		/**
		 * This hook is used to alter the lottery lucky dip question answer hover message.
		 *
		 * @since 1.0
		 */
		return apply_filters( 'lty_lucky_dip_question_answer_hover_message', get_option( 'lty_settings_lucky_dip_question_answer_hover_message', 'Please select an answer' ) );
	}
}

if ( ! function_exists( 'lty_get_lucky_dip_added_to_cart_message' ) ) {

	/**
	 * Get the lucky dip tickets added to cart message.
	 *
	 * @since 10.4.0
	 * @param array $ticket_numbers Ticket numbers.
	 * @return string
	 * */
	function lty_get_lucky_dip_added_to_cart_message( $ticket_numbers ) {
		$tickets_added_to_cart_message = str_replace(
			'{ticket_numbers}',
			esc_html( implode( ', ', $ticket_numbers ) ),
			get_option( 'lty_settings_lucky_dip_added_to_cart_message', __( '<b>Ticket Number(s) has been added to your cart.</b><br/>{ticket_numbers}', 'lottery-for-woocommerce' ) )
		);

		/**
		 * This hook is used to alter the lucky dip tickets added to cart message.
		 *
		 * @since 10.4.0
		 */
		return apply_filters( 'lty_lucky_dip_added_to_cart_message', $tickets_added_to_cart_message );
	}
}

if ( ! function_exists( 'lty_get_single_product_page_winners_count_label' ) ) {

	/**
	 * Get the label for single product page winners count label.
	 *
	 * @return string.
	 * */
	function lty_get_single_product_page_winners_count_label( $product ) {

		// Check if the product is a object.
		if ( ! is_object( $product ) ) {
			return '';
		}

		$winners_count_notice = sprintf( str_replace( '{lottery_winner_count}', '<b>' . intval( $product->get_lty_winners_count() ) . '</b>', get_option( 'lty_settings_single_product_lottery_winner_count_message' ) ) );

		/**
		 * This hook is used to alter the lottery winners count label.
		 *
		 * @since 1.0
		 */
		return apply_filters( 'lty_lottery_single_product_winners_count_label', $winners_count_notice );
	}
}

if ( ! function_exists( 'lty_display_date_starts_on_label_in_single_product' ) ) {

	/**
	 * Display date starts on label message.
	 *
	 * @return bool.
	 * */
	function lty_display_date_starts_on_label_in_single_product() {
		/**
		 * This hook is used to validate to show the lottery start date label in the single product page.
		 *
		 * @since 1.0
		 */
		return apply_filters( 'lty_settings_display_date_starts_on_label_in_single_product', 'yes' != get_option( 'lty_settings_hide_lottery_starts_on_message_in_single_product_page', 'no' ) );
	}
}

if ( ! function_exists( 'lty_display_date_ends_on_label_in_single_product' ) ) {

	/**
	 * Display date ends on label message.
	 *
	 * @return bool.
	 * */
	function lty_display_date_ends_on_label_in_single_product() {
		/**
		 * This hook is used to validate to show the lottery end date label in the single product page.
		 *
		 * @since 1.0
		 */
		return apply_filters( 'lty_settings_display_date_ends_on_label_in_single_product', 'yes' != get_option( 'lty_settings_hide_lottery_ends_on_message_in_single_product_page', 'no' ) );
	}
}

if ( ! function_exists( 'lty_get_single_product_lucky_dip_title_label' ) ) {

	/**
	 * Get the lucky dip title label in the single product page.
	 *
	 * @since 10.4.0
	 * @return string
	 * */
	function lty_get_single_product_lucky_dip_title_label() {
		/**
		 * This hook is used to alter lucky dip title label in the single product page.
		 *
		 * @since 10.4.0
		 */
		return apply_filters( 'lty_single_product_lucky_dip_title_label', get_option( 'lty_settings_single_product_lucky_dip_title_label', __( 'Lucky Dip', 'lottery-for-woocommerce' ) ) );
	}
}

if ( ! function_exists( 'lty_get_single_product_lucky_dip_quantity_label' ) ) {

	/**
	 * Get the lucky dip quantity label in the single product page.
	 *
	 * @since 10.4.0
	 * @return string
	 * */
	function lty_get_single_product_lucky_dip_quantity_label() {
		/**
		 * This hook is used to alter lucky dip quantity field label in the single product page.
		 *
		 * @since 10.4.0
		 */
		return apply_filters( 'lty_single_product_lucky_dip_quantity_label', get_option( 'lty_settings_single_product_lucky_dip_quantity_label', __( 'Quantity', 'lottery-for-woocommerce' ) ) );
	}
}

if ( ! function_exists( 'lty_get_single_product_generate_lucky_dip_button_label' ) ) {

	/**
	 * Get the generate lucky dip button label in the single product page.
	 *
	 * @since 10.4.0
	 * @return string
	 * */
	function lty_get_single_product_generate_lucky_dip_button_label() {
		/**
		 * This hook is used to alter generate lucky dip button label in the single product page.
		 *
		 * @since 10.4.0
		 */
		return apply_filters( 'lty_single_product_generate_lucky_dip_button_label', get_option( 'lty_settings_single_product_generate_lucky_dip_button_label', __( 'Generate Lucky Dip', 'lottery-for-woocommerce' ) ) );
	}
}

if ( ! function_exists( 'lty_get_single_product_regenerate_lucky_dip_button_label' ) ) {

	/**
	 * Get the regenerate lucky dip button label in the single product page.
	 *
	 * @since 10.4.0
	 * @return string
	 * */
	function lty_get_single_product_regenerate_lucky_dip_button_label() {
		/**
		 * This hook is used to alter regenerate lucky dip button label in the single product page.
		 *
		 * @since 10.4.0
		 */
		return apply_filters( 'lty_single_product_regenerate_lucky_dip_button_label', get_option( 'lty_settings_single_product_regenerate_lucky_dip_button_label', __( 'Re-generate Lucky Dip', 'lottery-for-woocommerce' ) ) );
	}
}

if ( ! function_exists( 'lty_get_single_product_lucky_dip_add_to_cart_button_label' ) ) {

	/**
	 * Get the lucky dip add to cart button label in the single product page.
	 *
	 * @since 10.4.0
	 * @return string
	 * */
	function lty_get_single_product_lucky_dip_add_to_cart_button_label() {
		/**
		 * This hook is used to alter lucky dip add to cart button label in the single product page.
		 *
		 * @since 10.4.0
		 */
		return apply_filters( 'lty_single_product_lucky_dip_add_to_cart_button_label', get_option( 'lty_settings_single_product_lucky_dip_add_to_cart_button_label', __( 'Add to Cart', 'lottery-for-woocommerce' ) ) );
	}
}

if ( ! function_exists( 'lty_get_single_product_add_more_lucky_dip_button_label' ) ) {

	/**
	 * Get the lucky dip add more button label in the single product page.
	 *
	 * @since 6.7
	 *
	 * @return string.
	 * */
	function lty_get_single_product_add_more_lucky_dip_button_label() {
		/**
		 * This hook is used to alter lucky dip add more button label in the single product page.
		 *
		 * @since 6.7
		 */
		return apply_filters( 'lty_single_product_add_more_lucky_dip_button_label', get_option( 'lty_settings_single_product_add_more_lucky_dip_button_label' ) );
	}
}

if ( ! function_exists( 'lty_get_single_product_lucky_dip_view_cart_button_label' ) ) {

	/**
	 * Get the lucky dip view cart button label in the single product page.
	 *
	 * @since 6.7
	 *
	 * @return string.
	 * */
	function lty_get_single_product_lucky_dip_view_cart_button_label() {
		/**
		 * This hook is used to alter lucky dip view cart label in the single product page.
		 *
		 * @since 6.7
		 */
		return apply_filters( 'lty_single_product_lucky_dip_view_cart_button_label', get_option( 'lty_settings_single_product_lucky_dip_view_cart_button_label' ) );
	}
}

if ( ! function_exists( 'lty_get_single_product_generated_lucky_dip_tickets_label' ) ) {

	/**
	 * Get the generated lucky dip ticket numbers label in the single product page.
	 *
	 * @since 10.4.0
	 * @return string
	 * */
	function lty_get_single_product_generated_lucky_dip_tickets_label() {
		/**
		 * This hook is used to alter generated lucky dip tickets label in the single product page.
		 *
		 * @since 10.4.0
		 */
		return apply_filters( 'lty_single_product_generated_lucky_dip_tickets_label', get_option( 'lty_settings_single_product_generated_lucky_dip_tickets_label', __( 'Generated Lucky Dip Ticket Number(s):', 'lottery-for-woocommerce' ) ) );
	}
}

if ( ! function_exists( 'lty_get_single_product_view_more_tickets_button_label' ) ) {

	/**
	 * Get the view more tickets button label in the single product page.
	 *
	 * @since 8.6.0
	 * @return string
	 * */
	function lty_get_single_product_view_more_tickets_button_label() {
		/**
		 * This hook is used to alter the view more tickets button label in the single product page.
		 *
		 * @since 8.6.0
		 */
		return apply_filters( 'lty_single_product_view_more_tickets_button_label', get_option( 'lty_settings_single_product_view_more_tickets_button_label', 'View More Tickets' ) );
	}
}

if ( ! function_exists( 'lty_get_single_product_view_less_tickets_button_label' ) ) {

	/**
	 * Get the view less tickets button label in the single product page.
	 *
	 * @since 8.6.0
	 * @return string
	 * */
	function lty_get_single_product_view_less_tickets_button_label() {
		/**
		 * This hook is used to alter the view less tickets button label in the single product page.
		 *
		 * @since 8.6.0
		 */
		return apply_filters( 'lty_single_product_view_less_tickets_button_label', get_option( 'lty_settings_single_product_view_less_tickets_button_label', 'View Less Tickets' ) );
	}
}

if ( ! function_exists( 'lty_get_single_product_question_answer_time_limit_label' ) ) {

	/**
	 * Get the question answer time limit label in the single product page.
	 *
	 * @since 6.7
	 *
	 * @return string.
	 * */
	function lty_get_single_product_question_answer_time_limit_label() {
		/**
		 * This hook is used to alter question answer time limit label label in the single product page.
		 *
		 * @since 6.7
		 */
		return apply_filters( 'lty_single_product_question_answer_time_limit_label', get_option( 'lty_settings_single_product_question_answer_time_limit_label' ) );
	}
}

if ( ! function_exists( 'lty_get_user_chooses_ticket_all_tickets_sold_label' ) ) {
	/**
	 * Get the all ticket are sold label in the single product page.
	 *
	 * @since 8.9.0
	 * @return string
	 * */
	function lty_get_user_chooses_ticket_all_tickets_sold_label() {
		/**
		 * This hook is used to alter all ticket are sold label label in the single product page.
		 *
		 * @since 8.9.0
		 */
		return apply_filters( 'lty_user_chooses_ticket_all_tickets_sold_label', get_option( 'lty_settings_user_chooses_ticket_all_tickets_sold_label', 'Tickets are Sold out in this tab' ) );
	}
}

if ( ! function_exists( 'lty_display_starts_on_label_in_shop' ) ) {

	/**
	 * Display starts on label message in shop.
	 *
	 * @return bool.
	 * */
	function lty_display_starts_on_label_in_shop() {
		/**
		 * This hook is used to validate to show the lottery start date label in the shop page.
		 *
		 * @since 1.0
		 */
		return apply_filters( 'lty_settings_display_starts_on_label_in_shop', 'yes' != get_option( 'lty_settings_hide_lottery_starts_on_message_in_shop_page', 'no' ) );
	}
}

if ( ! function_exists( 'lty_display_ends_on_label_in_shop' ) ) {

	/**
	 * Display ends on label message in shop.
	 *
	 * @return bool.
	 * */
	function lty_display_ends_on_label_in_shop() {
		/**
		 * This hook is used to validate to show the lottery end date label in the shop page.
		 *
		 * @since 1.0
		 */
		return apply_filters( 'lty_settings_display_ends_on_label_in_shop', 'yes' != get_option( 'lty_settings_hide_lottery_ends_on_message_in_shop_page', 'no' ) );
	}
}

if ( ! function_exists( 'lty_get_ticket_pending_payment_message' ) ) {

	/**
	 * Get the ticket pending payment message.
	 *
	 * @since 6.9
	 *
	 * @return string.
	 * */
	function lty_get_ticket_pending_payment_message() {
		/**
		 * This hook is used to alter ticket pending payment message.
		 *
		 * @since 6.9
		 */
		return apply_filters( 'lty_ticket_pending_payment_message', get_option( 'lty_settings_ticket_pending_payment_message' ) );
	}
}

if ( ! function_exists( 'lty_get_pagination_classes' ) ) {

	/**
	 * Get the pagination classes.
	 *
	 *  @return array
	 */
	function lty_get_pagination_classes( $page_no, $current_page ) {
		$classes = array( 'lty-pagination', 'lty-pagination-' . $page_no );
		if ( $current_page == $page_no ) {
			$classes[] = 'current';
		}
		/**
		 * This hook is used to alter the pagination classes.
		 *
		 * @since 1.0
		 */
		return apply_filters( 'lty_pagination_classes', $classes, $page_no, $current_page );
	}
}

if ( ! function_exists( 'lty_get_pagination_number' ) ) {

	/**
	 * Get the pagination number.
	 *
	 *  @return int
	 */
	function lty_get_pagination_number( $start, $page_count, $current_page ) {
		$page_no = false;
		if ( $current_page <= $page_count && $start <= $page_count ) {
			$page_no = $start;
		} elseif ( $current_page > $page_count ) {
			$overall_count = $current_page - $page_count + $start;
			if ( $overall_count <= $current_page ) {
				$page_no = $overall_count;
			}
		}
		/**
		 * This hook is used to alter the pagination number.
		 *
		 * @since 1.0
		 */
		return apply_filters( 'lty_pagination_number', $page_no, $start, $page_count, $current_page );
	}
}

if ( ! function_exists( 'lty_show_ticket_logs_search' ) ) {

	/**
	 * Can display the ticket logs search?
	 *
	 * @since 7.0.0
	 * @param string $page Current page.
	 * @return bool
	 */
	function lty_show_ticket_logs_search( $page = 'product' ) {
		$bool = 'entry-list' === $page ? 'yes' !== get_option( 'lty_settings_hide_entry_list_tickets_search', 'no' ) : '1' === get_option( 'lty_settings_display_ticket_logs_search' );

		/**
		 * This hook is used to alter the ticket logs search display.
		 *
		 * @since 7.0.0
		 */
		return apply_filters( 'lty_show_ticket_logs_search', $bool );
	}
}

if ( ! function_exists( 'lty_get_ticket_search_button_label' ) ) {

	/**
	 * Get the label for tickets search button.
	 *
	 * @since 7.0
	 *
	 * @return string.
	 * */
	function lty_get_ticket_search_button_label() {
		/**
		 * This hook is used to alter the ticket search button label.
		 *
		 * @since 7.0
		 */
		return apply_filters( 'lty_ticket_search_button_label', get_option( 'lty_settings_tickets_search_button_label' ) );
	}
}

if ( ! function_exists( 'lty_render_quantity_field' ) ) {

	/**
	 * Render the quantity field.
	 *
	 * @since 7.5.0
	 * @param object $product Product object.
	 */
	function lty_render_quantity_field( $product ) {
		if ( $product->can_display_predefined_buttons() && ! $product->can_display_predefined_with_quantity_selector() ) {
			return;
		}

		if ( '2' === get_option( 'lty_settings_quantity_selector_type' ) ) {
			if ( '2' === $product->get_lty_ticket_range_slider_type() ) {
				$args = array(
					'min_value'    => 1,
					'max_value'    => $product->get_lty_maximum_tickets(),
					'preset_value' => $product->get_preset_tickets(),
				);
			} else {
				$args = array(
					'min_value'    => $product->get_min_purchase_quantity(),
					'max_value'    => $product->get_max_purchase_quantity(),
					'preset_value' => $product->get_preset_tickets(),
				);
			}

			lty_get_template( 'single-product/add-to-cart/range-slider.php', array_merge( $args, array( 'product' => $product ) ) );
		} else {
			woocommerce_quantity_input( lty_get_quantity_input_arguments( $product ) );
		}
	}
}

if ( ! function_exists( 'lty_get_lottery_range_slider_message' ) ) {

	/**
	 * Get the lottery range slider message.
	 *
	 * @since 7.5.0
	 * @param int $value
	 * @return string
	 */
	function lty_get_lottery_range_slider_message( $value ) {
		$message = str_replace( '{quantity}', '<span class="lty-range-slider-current-value">' . $value . '</span>', __( '{quantity} Ticket(s)', 'lottery-for-woocommerce' ) );
		/**
		 * This hook is used to alter the lottery range slider message.
		 *
		 * @since 7.5.0
		 */
		return apply_filters( 'lty_lottery_range_slider_message', $message );
	}
}

if ( ! function_exists( 'lty_get_instant_winners_prize_columns' ) ) {

	/**
	 * Instant winners prize column.
	 *
	 * @since 8.0.0
	 * @param object $product Product object.
	 * @return array
	 * */
	function lty_get_instant_winners_prize_columns( $product ) {
		$columns = array( 'ticket_number' => get_option( 'lty_settings_instant_winners_ticket_column_label', __( 'Ticket Number', 'lottery-for-woocommerce' ) ) );

		if ( lty_is_lottery_product( $product ) && '1' === $product->get_lty_display_instant_winner_image() ) {
			$columns['image'] = get_option( 'lty_settings_instant_winners_image_column_label', __( 'Prize Image', 'lottery-for-woocommerce' ) );
		}

		$columns['prize']  = get_option( 'lty_settings_instant_winners_prize_column_label', __( 'Prize', 'lottery-for-woocommerce' ) );
		$columns['winner'] = get_option( 'lty_settings_instant_winners_column_label', __( 'Winner', 'lottery-for-woocommerce' ) );

		/**
		 * This hook is used to alter the instant winners prize columns.
		 *
		 * @since 8.0.0
		 */
		return apply_filters( 'lty_instant_winners_prize_columns', $columns );
	}
}

if ( ! function_exists( 'lty_get_instant_winners_prize_available_label' ) ) {

	/**
	 * Get the label for instant winner prize available.
	 *
	 * @return string.
	 * */
	function lty_get_instant_winners_prize_available_label() {
		/**
		 * This hook is used to alter the lottery instant winner prize available label.
		 *
		 * @since 8.0.0
		 */
		return apply_filters( 'lty_instant_winners_prize_available_label', get_option( 'lty_settings_instant_winners_prize_available_label', __( 'Prize Available', 'lottery-for-woocommerce' ) ) );
	}
}

if ( ! function_exists( 'lty_get_instant_winner_won_prize_label' ) ) {
	/**
	 * Get the label for instant winner won prize.
	 *
	 * @since 11.1.0
	 * @return string
	 * */
	function lty_get_instant_winner_won_prize_label() {
		/**
		 * This hook is used to alter the lottery instant winner won prize label.
		 *
		 * @since 11.1.0
		 */
		return apply_filters( 'lty_instant_winner_won_prize_label', get_option( 'lty_settings_instant_winner_won_prize_label', __( 'Won', 'lottery-for-woocommerce' ) ) );
	}
}

if ( ! function_exists( 'lty_get_instant_winner_available_prices_count_label' ) ) {

	/**
	 * Get the label for instant winner available prizes count.
	 *
	 * @since 8.1.0
	 * @return string.
	 * */
	function lty_get_instant_winner_available_prices_count_label() {
		/**
		 * This hook is used to alter the lottery instant winner available prizes count label.
		 *
		 * @since 8.1.0
		 */
		return apply_filters( 'lty_instant_winner_available_prices_count_label', get_option( 'lty_settings_instant_winners_available_prizes_count_label' ) );
	}
}

if ( ! function_exists( 'lty_get_instant_winner_won_prices_count_label' ) ) {

	/**
	 * Get the label for instant winner won prizes count.
	 *
	 * @since 8.1.0
	 * @return string.
	 * */
	function lty_get_instant_winner_won_prices_count_label() {
		/**
		 * This hook is used to alter the lottery instant winner won prizes count label.
		 *
		 * @since 8.1.0
		 */
		return apply_filters( 'lty_instant_winner_won_prices_count_label', get_option( 'lty_settings_instant_winners_won_prizes_count_label' ) );
	}
}

if ( ! function_exists( 'lty_get_entry_list_view_participants_label' ) ) {

	/**
	 * Get the label for entry list view participants.
	 *
	 * @since 9.0.0
	 * @return string.
	 * */
	function lty_get_entry_list_view_participants_label() {
		/**
		 * This hook is used to alter the lottery entry list view participants label.
		 *
		 * @since 9.0.0
		 */
		return apply_filters( 'lty_entry_list_view_participants_label', get_option( 'lty_settings_entry_list_view_participants_label' ) );
	}
}

if ( ! function_exists( 'lty_get_entry_list_status_label' ) ) {

	/**
	 * Get the label for entry list status.
	 *
	 * @since 9.0.0
	 * @return string.
	 * */
	function lty_get_entry_list_status_label() {
		/**
		 * This hook is used to alter the lottery entry list status label.
		 *
		 * @since 9.0.0
		 */
		return apply_filters( 'lty_entry_list_status_label', get_option( 'lty_settings_entry_list_status_label' ) );
	}
}

if ( ! function_exists( 'lty_get_entry_list_start_date_label' ) ) {

	/**
	 * Get the label for entry list start date.
	 *
	 * @since 9.0.0
	 * @return string.
	 * */
	function lty_get_entry_list_start_date_label() {
		/**
		 * This hook is used to alter the lottery entry list start date label.
		 *
		 * @since 9.0.0
		 */
		return apply_filters( 'lty_entry_list_start_date_label', get_option( 'lty_settings_entry_list_start_date_label' ) );
	}
}
if ( ! function_exists( 'lty_get_entry_list_end_date_label' ) ) {

	/**
	 * Get the label for entry list end date.
	 *
	 * @since 9.0.0
	 * @return string.
	 * */
	function lty_get_entry_list_end_date_label() {
		/**
		 * This hook is used to alter the lottery entry list end date label.
		 *
		 * @since 9.0.0
		 */
		return apply_filters( 'lty_entry_list_end_date_label', get_option( 'lty_settings_entry_list_end_date_label' ) );
	}
}
if ( ! function_exists( 'lty_get_entry_list_winner_count_label' ) ) {

	/**
	 * Get the label for entry list winner count.
	 *
	 * @since 9.0.0
	 * @return string.
	 * */
	function lty_get_entry_list_winner_count_label() {
		/**
		 * This hook is used to alter the lottery entry list winner count label.
		 *
		 * @since 9.0.0
		 */
		return apply_filters( 'lty_entry_list_winner_count_label', get_option( 'lty_settings_entry_list_winner_count_label' ) );
	}
}
if ( ! function_exists( 'lty_get_entry_list_maximum_tickets_count_label' ) ) {

	/**
	 * Get the label for entry list maximum tickets count.
	 *
	 * @since 9.0.0
	 * @return string.
	 * */
	function lty_get_entry_list_maximum_tickets_count_label() {
		/**
		 * This hook is used to alter the lottery entry list maximum tickets count label.
		 *
		 * @since 9.0.0
		 */
		return apply_filters( 'lty_entry_list_maximum_tickets_count_label', get_option( 'lty_settings_entry_list_maximum_tickets_count_label' ) );
	}
}
if ( ! function_exists( 'lty_get_entry_list_purchased_tickets_count_label' ) ) {

	/**
	 * Get the label for entry list purchased tickets count.
	 *
	 * @since 9.0.0
	 * @return string.
	 * */
	function lty_get_entry_list_purchased_tickets_count_label() {
		/**
		 * This hook is used to alter the lottery entry list purchased tickets count label.
		 *
		 * @since 9.0.0
		 */
		return apply_filters( 'lty_entry_list_purchased_tickets_count_label', get_option( 'lty_settings_entry_list_purchased_tickets_count_label' ) );
	}
}

if ( ! function_exists( 'lty_get_entry_list_remaining_tickets_count_label' ) ) {

	/**
	 * Get the label for entry list remaining tickets count.
	 *
	 * @since 9.0.0
	 * @return string.
	 * */
	function lty_get_entry_list_remaining_tickets_count_label() {
		/**
		 * This hook is used to alter the lottery entry list remaining tickets count label.
		 *
		 * @since 9.0.0
		 */
		return apply_filters( 'lty_entry_list_remaining_tickets_count_label', get_option( 'lty_settings_entry_list_remaining_tickets_count_label' ) );
	}
}

if ( ! function_exists( 'lty_prepare_entry_list_pdf_arguments' ) ) {

	/**
	 * Prepare entry list pdf arguments.
	 *
	 * @since 9.5.0
	 * @param int|object $product Product object.
	 * @return array
	 * */
	function lty_prepare_entry_list_pdf_arguments( $product ) {
		$product = is_object( $product ) ? $product : wc_get_product( $product );
		$args    = array(
			'product'            => $product,
			'ticket_ids'         => lty_get_purchased_tickets_ids_on_ticket_logs_tab( $product ),
			'ticket_log_columns' => array(
				'ticket_number' => get_option( 'lty_settings_single_product_tab_ticket_number_label' ),
				'user_name'     => get_option( 'lty_settings_single_product_tab_username_label' ),
				'date'          => lty_get_single_product_tab_date_label(),
			),
		);

		// Finished lottery.
		if ( $product->has_lottery_status( 'lty_lottery_finished' ) ) {
			$args['winner_log_columns'] = array(
				'ticket_number' => get_option( 'lty_settings_single_product_lottery_ticket_number_label' ),
				'username'      => get_option( 'lty_settings_single_product_lottery_username_label' ),
				'gift_product'  => get_option( 'lty_settings_single_product_lottery_gift_product_label' ),
			);
			$args['winner_ids']         = $product->get_current_winner_ids();
		}

		return $args;
	}
}

if ( ! function_exists( 'lty_get_lottery_entry_list_ticket_logs_table_columns' ) ) {

	/**
	 * Get lottery entry list table columns.
	 *
	 * @since 9.5.0
	 * @param object $product Product object.
	 * @return array
	 */
	function lty_get_lottery_entry_list_ticket_logs_table_columns( $product ) {
		// Check if the product is a object.
		if ( ! is_object( $product ) ) {
			return array();
		}

		$columns = array(
			'ticket_number' => get_option( 'lty_settings_single_product_tab_ticket_number_label' ),
			'user_name'     => get_option( 'lty_settings_single_product_tab_username_label' ),
		);

		if ( 'yes' !== get_option( 'lty_settings_hide_entry_list_ticket_purchased_date', 'no' ) ) {
			$columns['date'] = lty_get_single_product_tab_date_label();
		}

		if ( 'yes' !== get_option( 'lty_settings_hide_entry_list_chosen_answer', 'no' ) && $product->is_valid_question_answer() && $product->has_lottery_status( 'lty_lottery_finished' ) ) {
			$columns['answer'] = lty_get_single_product_tab_answer_label();
		}

		/**
		 * This hook is used to alter the lottery entry list ticket logs columns.
		 *
		 * @since 9.5.0
		 */
		return apply_filters( 'lty_lottery_entry_list_ticket_logs_columns', $columns, $product );
	}
}

if ( ! function_exists( 'lty_get_lottery_entry_list_pdf_logo' ) ) {

	/**
	 * Get lottery entry list pdf logo.
	 *
	 * @since 9.5.0
	 * @return string|HTML
	 * */
	function lty_get_lottery_entry_list_pdf_logo() {
		if ( ! get_option( 'lty_settings_entry_list_pdf_logo' ) ) {
			return '';
		}

		$image_sizes = get_option( 'lty_settings_entry_list_pdf_logo_size', array() );
		$sizes       = lty_parse_relative_image_size_option( $image_sizes );

		/* translators: %1$s: Image url %2$s: Image height %3$s: Image width */
		return sprintf(
			'<img src="%1$s" style="height: %2$spx; width: %3$spx;">',
			esc_url( wp_get_attachment_url( get_option( 'lty_settings_entry_list_pdf_logo' ) ) ),
			esc_attr( $sizes['height'] ),
			esc_attr( $sizes['width'] )
		);
	}
}

if ( ! function_exists( 'lty_get_lottery_entry_list_pdf_header_details' ) ) {

	/**
	 * Get the lottery entry list PDF header details.
	 *
	 * @since 9.5.0
	 * @param object $product Product object.
	 * @return string|HTML
	 */
	function lty_get_lottery_entry_list_pdf_header_details( $product ) {
		if ( ! is_object( $product ) ) {
			return '';
		}

		$header_html = str_replace(
			array( '{logo}', '{site_name}', '{product_name}' ),
			array(
				lty_get_lottery_entry_list_pdf_logo(),
				wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES ),
				$product->get_product_name(),
			),
			get_option( 'lty_settings_entry_list_pdf_header' )
		);

		/**
		 * This hook is used to alter the lottery entry list pdf header.
		 *
		 * @since 9.5.0
		 */
		return apply_filters( 'lty_lottery_entry_list_pdf_header', $header_html, $product );
	}
}

if ( ! function_exists( 'lty_get_lottery_entry_list_pdf_footer_details' ) ) {

	/**
	 * Get the lottery entry list PDF footer details.
	 *
	 * @since 9.5.0
	 * @param object $product Product object.
	 * @return string|HTML
	 */
	function lty_get_lottery_entry_list_pdf_footer_details( $product ) {
		if ( ! is_object( $product ) ) {
			return '';
		}

		$footer_html = str_replace(
			array( '{maximum_tickets}', '{end_date}' ),
			array(
				$product->get_lty_maximum_tickets(),
				$product->get_fomatted_end_date_text(),
			),
			get_option( 'lty_settings_entry_list_pdf_footer' )
		);

		/**
		 * This hook is used to alter the lottery entry list pdf footer.
		 *
		 * @since 9.5.0
		 */
		return apply_filters( 'lty_lottery_entry_list_pdf_footer', $footer_html, $product );
	}
}

if ( ! function_exists( 'lty_get_lottery_ticket_pdf_header_details' ) ) {

	/**
	 * Get the lottery ticket PDF header details.
	 *
	 * @since 10.4.0
	 * @return string|HTML
	 */
	function lty_get_lottery_ticket_pdf_header_details() {
		$header_html = str_replace( '{site_name}', wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES ), get_option( 'lty_settings_ticket_pdf_header' ) );

		/**
		 * This hook is used to alter the lottery ticket pdf header.
		 *
		 * @since 10.4.0
		 * @param string Header HTML.
		 */
		return apply_filters( 'lty_lottery_ticket_pdf_header', $header_html );
	}
}

if ( ! function_exists( 'lty_get_lottery_ticket_pdf_footer_details' ) ) {

	/**
	 * Get the lottery ticket PDF footer details.
	 *
	 * @since 10.4.0
	 * @return string|HTML
	 */
	function lty_get_lottery_ticket_pdf_footer_details() {
		/**
		 * This hook is used to alter the lottery ticket pdf footer.
		 *
		 * @since 10.4.0
		 * @param string Footer HTML.
		 */
		return apply_filters( 'lty_lottery_ticket_pdf_footer', get_option( 'lty_settings_ticket_pdf_footer' ) );
	}
}

if ( ! function_exists( 'lty_get_ticket_pdf_bg_color_left' ) ) {

	/**
	 * Get the lottery ticket PDF left background color.
	 *
	 * @since 9.8.0
	 * @return string
	 */
	function lty_get_ticket_pdf_bg_color_left() {
		return get_option( 'lty_settings_ticket_pdf_bg_color_left', '#ffffff' );
	}
}

if ( ! function_exists( 'lty_get_ticket_pdf_bg_color_right' ) ) {

	/**
	 * Get the lottery ticket PDF right background color.
	 *
	 * @since 9.8.0
	 * @return string
	 */
	function lty_get_ticket_pdf_bg_color_right() {
		return get_option( 'lty_settings_ticket_pdf_bg_color_right', '#f57436' );
	}
}

if ( ! function_exists( 'lty_get_ticket_pdf_bg_color_ratio' ) ) {

	/**
	 * Get the lottery ticket PDF background color ratio.
	 *
	 * @since 9.8.0
	 * @return float
	 */
	function lty_get_ticket_pdf_bg_color_ratio() {
		return floatval( get_option( 'lty_settings_ticket_pdf_bg_color_ratio', '-20' ) );
	}
}

if ( ! function_exists( 'lty_get_participated_lottery_tickets_details_columns' ) ) {

	/**
	 * Get the participated lottery tickets details columns.
	 *
	 * @since 10.2.0
	 * @param object $product Product object.
	 * @return array
	 * */
	function lty_get_participated_lottery_tickets_details_columns( $product ) {
		$columns = array(
			'ticket_number' => get_option( 'lty_settings_dashboard_participated_lottery_ticket_number_label', __( 'Ticket Number', 'lottery-for-woocommerce' ) ),
			'order_id'      => get_option( 'lty_settings_dashboard_participated_lottery_order_id_label', __( 'Order ID', 'lottery-for-woocommerce' ) ),
		);

		if ( lty_is_lottery_product( $product ) && $product->is_valid_question_answer() ) {
			$columns['answer'] = get_option( 'lty_settings_dashboard_participated_lottery_answer_label', __( 'Chosen Answer', 'lottery-for-woocommerce' ) );
		}

		/**
		 * This hook is used to alter the participated lottery tickets details columns.
		 *
		 * @since 10.2.0
		 */
		return apply_filters( 'lty_participated_lottery_tickets_details_columns', $columns );
	}
}

if ( ! function_exists( 'lty_get_lottery_maximum_tickets_purchase_limit_error_message' ) ) {

	/**
	 * Get the message for lottery maximum tickets purchase limit error message.
	 *
	 * @since 10.2.0
	 * @param object $product Product object.
	 * @return string
	 * */
	function lty_get_lottery_maximum_tickets_purchase_limit_error_message( $product ) {
		// Return if the product is not a object.
		if ( ! is_object( $product ) ) {
			return '';
		}

		$message = get_option( 'lty_settings_maximum_tickets_purchase_limit_error_message', 'You have reached the Maximum ticket(s) count {maximum_tickets_count} for this lottery. Hence you cannot purchase new lottery tickets.' );
		$message = str_replace( '{maximum_tickets_count}', '<b>' . $product->get_lty_user_maximum_tickets() . '</b>', $message );

		/**
		 * This hook is used to alter the lottery maximum tickets purchase limit error message.
		 *
		 * @since 10.2.0
		 */
		return apply_filters( 'lty_lottery_maximum_tickets_purchase_limit_error_message', $message, $product );
	}
}

if ( ! function_exists( 'lty_get_guest_message' ) ) {

	/**
	 * Get the guest message.
	 *
	 * @since 10.2.0
	 * @return string
	 * */
	function lty_get_guest_message() {
		/**
		 * This hook is used to alter guest message.
		 *
		 * @since 10.2.0
		 */
		return apply_filters( 'lty_guest_message', get_option( 'lty_settings_guest_message', __( 'Please Logged in to view the contents', 'lottery-for-woocommerce' ) ) );
	}
}

if ( ! function_exists( 'lty_get_single_product_price_label' ) ) {

	/**
	 * Get the single product page price label.
	 *
	 * @since 10.2.0
	 * @return string
	 * */
	function lty_get_single_product_price_label() {
		/**
		 * This hook is used to alter the single product page price label.
		 *
		 * @since 10.2.0
		 */
		return apply_filters( 'lty_single_product_price_label', get_option( 'lty_settings_single_product_price_label', 'Participate now for {lottery_price}' ) );
	}
}

if ( ! function_exists( 'lty_get_single_product_tab_date_label' ) ) {

	/**
	 * Get the single product tab date column label.
	 *
	 * @since 10.2.0
	 * @return string
	 * */
	function lty_get_single_product_tab_date_label() {
		/**
		 * This hook is used to alter the single product tab date column label.
		 *
		 * @since 10.2.0
		 */
		return apply_filters( 'lty_single_product_tab_date_label', get_option( 'lty_settings_single_product_tab_date_label' ) );
	}
}

if ( ! function_exists( 'lty_get_single_product_tab_answer_label' ) ) {

	/**
	 * Get the single product tab answer label.
	 *
	 * @since 10.2.0
	 * @return string
	 * */
	function lty_get_single_product_tab_answer_label() {
		/**
		 * This hook is used to alter the single product tab answer label.
		 *
		 * @since 10.2.0
		 */
		return apply_filters( 'lty_single_product_tab_answer_label', get_option( 'lty_settings_single_product_tab_answer_label' ) );
	}
}

if ( ! function_exists( 'lty_get_single_product_lottery_winner_label' ) ) {

	/**
	 * Get the single product lottery winner label.
	 *
	 * @since 10.2.0
	 * @return string
	 * */
	function lty_get_single_product_lottery_winner_label() {
		/**
		 * This hook is used to alter the single product lottery winner label.
		 *
		 * @since 10.2.0
		 */
		return apply_filters( 'lty_single_product_lottery_winner_label', get_option( 'lty_settings_single_product_lottery_winner_label' ) );
	}
}

if ( ! function_exists( 'lty_get_dashboard_my_lottery_label' ) ) {

	/**
	 * Get the dashboard my lottery label.
	 *
	 * @since 10.2.0
	 * @return string
	 * */
	function lty_get_dashboard_my_lottery_label() {
		/**
		 * This hook is used to alter the dashboard my lottery label.
		 *
		 * @since 10.2.0
		 */
		return apply_filters( 'lty_dashboard_my_lottery_label', get_option( 'lty_settings_dashboard_my_lottery_label' ) );
	}
}

if ( ! function_exists( 'lty_get_myaccount_lottery_dashboard_menu_label' ) ) {

	/**
	 * Get the label for myaccount page lottery dashboard menu.
	 *
	 * @since 10.2.0
	 * @return string
	 * */
	function lty_get_myaccount_lottery_dashboard_menu_label() {
		/**
		 * This hook is used to alter the myaccount page dashboard menu label.
		 *
		 * @since 10.2.0
		 */
		return apply_filters( 'lty_myaccount_lottery_dashboard_menu_label', get_option( 'lty_settings_myaccount_lottery_menu_label' ) );
	}
}

if ( ! function_exists( 'lty_get_predefined_buttons_heading' ) ) {

	/**
	 * Get the predefined buttons heading.
	 *
	 * @since 10.2.0
	 * @return string
	 * */
	function lty_get_predefined_buttons_heading() {
		/**
		 * This hook is used to alter the predefined buttons heading.
		 *
		 * @since 10.2.0
		 */
		return apply_filters( 'lty_predefined_buttons_heading', get_option( 'lty_settings_predefined_buttons_heading', __( 'Choose an option', 'lottery-for-woocommerce' ) ) );
	}
}

if ( ! function_exists( 'lty_get_winners_list_shortcode_title' ) ) {

	/**
	 * Get the winners list shortcode title.
	 *
	 * @since 10.2.0
	 * @return string
	 * */
	function lty_get_winners_list_shortcode_title() {
		/**
		 * This hook is used to alter the winners list shortcode title.
		 *
		 * @since 10.2.0
		 */
		return apply_filters( 'lty_winners_list_shortcode_title', get_option( 'lty_settings_winners_list_shortcode_title' ) );
	}
}

if ( ! function_exists( 'lty_get_lottery_dashboard_user_purchased_ticket_count_label' ) ) {

	/**
	 * Get the lottery dashboard user purchased ticket count label.
	 *
	 * @since 10.3.0
	 * @param object $product Product object.
	 * @return string
	 * */
	function lty_get_lottery_dashboard_user_purchased_ticket_count_label( $product ) {
		if ( ! lty_is_lottery_product( $product ) ) {
			return '';
		}

		$label = str_replace( '{user_ticket_count}', $product->get_user_purchased_ticket_count(), get_option( 'lty_settings_dashboard_purchased_ticket_count_label' ) );

		/**
		 * This hook is used to alter the lottery dashboard user purchased ticket count label.
		 *
		 * @since 10.3.0
		 */
		return apply_filters( 'lty_lottery_dashboard_user_purchased_ticket_count_label', $label );
	}
}

if ( ! function_exists( 'lty_get_email_lottery_ticket_pdf_download_button_label' ) ) {

	/**
	 * Get email lottery ticket(s) PDF download button label.
	 *
	 * @since 10.5.0
	 * @return string
	 * */
	function lty_get_email_lottery_ticket_pdf_download_button_label() {
		/**
		 * This hook is used to alter the email lottery ticket(s) PDF download button label.
		 *
		 * @since 10.5.0
		 */
		return apply_filters( 'lty_email_lottery_ticket_pdf_download_button_label', get_option( 'lty_settings_email_tickets_pdf_download_button_label' ) );
	}
}

if ( ! function_exists( 'lty_get_customer_instant_winner_details_columns' ) ) {

	/**
	 * Get the customer instant winner details columns.
	 *
	 * @since 10.6.0
	 * @param object $instant_winner_log Instant winner log object.
	 * @return array
	 * */
	function lty_get_customer_instant_winner_details_columns( $instant_winner_log = false ) {
		$columns = array(
			'order_id'      => __( 'Order Number', 'lottery-for-woocommerce' ),
			'ticket_number' => __( 'Ticket Number', 'lottery-for-woocommerce' ),
			'prize'         => __( 'Prize', 'lottery-for-woocommerce' ),
		);

		if ( is_object( $instant_winner_log ) ) {
			switch ( $instant_winner_log->get_prize_type() ) {
				case 'coupon':
				case 'smart_coupon':
					$columns['coupon_code']   = __( 'Coupon Code', 'lottery-for-woocommerce' );
					$columns['coupon_value']  = __( 'Coupon Value', 'lottery-for-woocommerce' );
					$columns['coupon_expiry'] = __( 'Coupon Expiry Date', 'lottery-for-woocommerce' );
					break;

				case 'wallet':
				case 'woo_wallet':
					$columns['credit_value'] = __( 'Wallet Credited Value', 'lottery-for-woocommerce' );
					break;

				case 'credit':
					$columns['credit_value'] = __( 'Credited Value', 'lottery-for-woocommerce' );
					break;
			}
		}

		/**
		 * This hook is used to alter the customer instant winner details columns.
		 *
		 * @since 10.6.0
		 * @param array $columns Column labels.
		 * @param object $instant_winner_log The instant winner log object.
		 */
		return apply_filters( 'lty_customer_instant_winner_details_columns', $columns, $instant_winner_log );
	}
}

if ( ! function_exists( 'lty_get_order_instant_winners_columns' ) ) {

	/**
	 * Get order instant winner details columns.
	 *
	 * @since 10.9.0
	 * @static array $columns Columns.
	 * @return array
	 * */
	function lty_get_order_instant_winners_columns() {
		static $columns;
		if ( isset( $columns ) ) {
			return $columns;
		}

		/**
		 * This hook is used to alter the order instant winner details columns.
		 *
		 * @since 10.9.0
		 * @param array Instant winner details columns.
		 */
		$columns = apply_filters(
			'lty_order_instant_winners_columns',
			array(
				'product_name'  => get_option( 'lty_settings_order_instant_winners_product_name_label', __( 'Product Name', 'lottery-for-woocommerce' ) ),
				'ticket_number' => get_option( 'lty_settings_order_instant_winners_ticket_number_label', __( 'Ticket Number', 'lottery-for-woocommerce' ) ),
				'image'         => get_option( 'lty_settings_order_instant_winners_image_label', __( 'Instant Win Image', 'lottery-for-woocommerce' ) ),
				'prize'         => get_option( 'lty_settings_order_instant_winners_prize_label', __( 'Instant Win Prize', 'lottery-for-woocommerce' ) ),
			)
		);

		return $columns;
	}
}

if ( ! function_exists( 'lty_get_order_instant_winners_heading' ) ) {

	/**
	 * Get the instant winner prize details heading for the order.
	 *
	 * @since 10.9.0
	 * @return string
	 * */
	function lty_get_order_instant_winners_heading() {
		/**
		 * This hook is used to alter the instant winner prize details heading.
		 *
		 * @since 10.9.0
		 */
		return apply_filters( 'lty_order_instant_winners_heading', get_option( 'lty_settings_order_instant_winners_heading', __( 'Congratulations! You have won the Instant Win Prize for the following ticket number(s)', 'lottery-for-woocommerce' ) ) );
	}
}

if ( ! function_exists( 'lty_get_thankyou_page_instant_win_better_luck_message' ) ) {
	/**
	 * Get the instant winner prize better luck message for thank you page.
	 *
	 * @since 11.4.0
	 * @return string
	 * */
	function lty_get_thankyou_page_instant_win_better_luck_message() {
		/**
		 * This hook is used to alter the instant winner prize better luck message for thank you page.
		 *
		 * @since 11.4.0
		 */
		return apply_filters( 'lty_thankyou_page_instant_win_better_luck_message', get_option( 'lty_settings_thankyou_page_instant_win_better_luck_msg', __( 'No Instant Win Prize at this time, better luck next time', 'lottery-for-woocommerce' ) ) );
	}
}

if ( ! function_exists( 'lty_get_order_details_page_instant_win_better_luck_message' ) ) {
	/**
	 * Get the instant winner prize better luck message for order details page.
	 *
	 * @since 11.4.0
	 * @return string
	 * */
	function lty_get_order_details_page_instant_win_better_luck_message() {
		/**
		 * This hook is used to alter the instant winner prize better luck message for order details page.
		 *
		 * @since 11.4.0
		 */
		return apply_filters( 'lty_order_details_page_instant_win_better_luck_message', get_option( 'lty_settings_order_details_page_instant_win_better_luck_msg', __( 'You have not won the Instant Win Prize for this order.', 'lottery-for-woocommerce' ) ) );
	}
}

if ( ! function_exists( 'lty_get_instant_winner_prize_group_colors_labels' ) ) {
	/**
	 * Get the instant winner prize group colors labels.
	 *
	 * @since 11.1.0
	 * @static array $colors_labels
	 * @return array
	 * */
	function lty_get_instant_winner_prize_group_colors_labels() {
		static $colors_labels;
		if ( isset( $colors_labels ) ) {
			return $colors_labels;
		}

		$colors_labels = array(
			'available' => __( 'Available Prize', 'lottery-for-woocommerce' ),
			'won'       => __( 'Won Prize', 'lottery-for-woocommerce' ),
		);

		/**
		 * This hook is used to alter the instant winner prize group colors labels.
		 *
		 * @since 11.1.0
		 * @param array $colors_labels
		 */
		return apply_filters( 'lty_instant_winner_prize_group_colors_labels', $colors_labels );
	}
}

if (!function_exists('lty_get_lottery_remaining_ticket_count_message')) {

	/**
	 *  Get the lottery remaining ticket count message.
	 *
	 *  @since 11.1.0
	 *  @param string $ticket_count
	 *  @return string
	 */
	function lty_get_lottery_remaining_ticket_count_message( $ticket_count ) {
		$find_array = array( '{remaining_tickets}', '{max_tickets}' );
		$replace_array = array( '<span class="lty-remaining-ticket-count">' . $ticket_count . '</span>', $ticket_count );

		return str_replace($find_array, $replace_array, __( 'Remaining/Max Ticket Qty to Enter: {remaining_tickets}/{max_tickets}', 'lottery-for-woocommerce' ));
	}

}

if ( ! function_exists( 'lty_get_thankyou_page_instant_win_gift_product_message' ) ) {
	/**
	 * Get the instant winner gift product prize message for thank you page.
	 *
	 * @since 11.5.0
	 * @param object $instant_winner_log Instant winner log object.
	 * @return string
	 * */
	function lty_get_thankyou_page_instant_win_gift_product_message( $instant_winner_log ) {
		if ( ! is_object( $instant_winner_log ) ) {
			return '';
		}

		$message = str_replace(
			array( '{gift_product_name}', '{lottery_product_name}', '{ticket_number}' ),
			array( $instant_winner_log->get_gift_product_name(), $instant_winner_log->get_product_name(), $instant_winner_log->get_ticket_number() ),
			get_option( 'lty_settings_thankyou_page_instant_win_gift_product_msg', __( 'Congratulations! You have won {gift_product_name} as instant win prize for purchasing ticket number <b>{ticket_number}</b> in the <b>{lottery_product_name}</b>', 'lottery-for-woocommerce' ) )
		);

		/**
		 * This hook is used to alter the instant winner gift product prize message for thank you page.
		 *
		 * @since 11.5.0
		 * @param string $message Message.
		 * @param object $instant_winner_log Instant winner log object.
		 */
		return apply_filters( 'lty_thankyou_page_instant_win_gift_product_message', $message, $instant_winner_log );
	}
}

if ( ! function_exists( 'lty_get_order_details_page_instant_win_gift_product_message' ) ) {
	/**
	 * Get the instant winner gift product prize message for order details page.
	 *
	 * @since 11.5.0
	 * @param object $instant_winner_log Instant winner log object.
	 * @return string
	 * */
	function lty_get_order_details_page_instant_win_gift_product_message( $instant_winner_log ) {
		if ( ! is_object( $instant_winner_log ) ) {
			return '';
		}

		$message = str_replace(
			array( '{gift_product_name}', '{lottery_product_name}', '{ticket_number}' ),
			array( $instant_winner_log->get_gift_product_name(), $instant_winner_log->get_product_name(), $instant_winner_log->get_ticket_number() ),
			get_option( 'lty_settings_order_details_page_instant_win_gift_product_msg', __( 'You have won {gift_product_name} as instant win prize for purchasing ticket number <b>{ticket_number}</b> in the <b>{lottery_product_name}</b>', 'lottery-for-woocommerce' ) )
		);

		/**
		 * This hook is used to alter the instant winner gift product prize message for order details page.
		 *
		 * @since 11.5.0
		 * @param string $message Message.
		 * @param object $instant_winner_log Instant winner log object.
		 */
		return apply_filters( 'lty_order_details_page_instant_win_gift_product_message', $message, $instant_winner_log );
	}
}

if ( ! function_exists( 'lty_get_lottery_product_page_display_mode' ) ) {

	/**
	 * Get the lottery product page display mode.
	 *
	 * @since 12.0.0
	 * @return string
	 * */
	function lty_get_lottery_product_page_display_mode() {
		/**
		 * This hook is used to alter lottery product page display mode.
		 *
		 * @since 12.0.0
		 */
		return apply_filters( 'lty_lottery_product_page_display_mode', get_option( 'lty_settings_product_page_loading_mode', '1' ) );
	}
}
