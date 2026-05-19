<?php

/**
 * Front end functions
 * */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


if ( ! function_exists( 'lty_get_cart_lottery_ticket_count' ) ) {

	/**
	 * Get a lottery ticket count from the cart.
	 *
	 * @return int.
	 */
	function lty_get_cart_lottery_ticket_count( $product_id ) {

		$ticket_ids = lty_get_cart_lottery_tickets( $product_id );
		if ( ! lty_check_is_array( $ticket_ids ) ) {
			return 0;
		}

		return count( $ticket_ids );
	}
}

if ( ! function_exists( 'lty_get_cart_lottery_tickets' ) ) {

	/**
	 * Get a lottery tickets from the cart.
	 *
	 * @return array.
	 */
	function lty_get_cart_lottery_tickets( $product_id ) {
		if ( ! $product_id ) {
			return array();
		}

		$cart_items = lty_get_cart_contents();
		if ( ! lty_check_is_array( $cart_items ) ) {
			return array();
		}

		$ticket_ids = array();
		$i          = 0;
		foreach ( $cart_items as $cart_item_key => $cart_item_data ) {
			if ( $cart_item_data['product_id'] != $product_id ) {
				continue;
			}

			if ( is_array( $cart_item_data ) && isset( $cart_item_data['lty_lottery']['tickets'] ) ) {
				$cart_tickets = $cart_item_data['lty_lottery']['tickets'];
				$ticket_ids   = array_merge( $ticket_ids, $cart_tickets );
			}
		}

		return $ticket_ids;
	}
}

if ( ! function_exists( 'lty_lottery_product_exists_in_cart' ) ) {

	/**
	 * Lottery product exists in the cart.
	 *
	 * @since 6.7
	 *
	 * @return bool.
	 */
	function lty_lottery_product_exists_in_cart( $product_id ) {
		$bool = false;
		if ( ! is_object( WC()->cart ) ) {
			return $bool;
		}

		$cart_items = WC()->cart->get_cart();
		if ( ! lty_check_is_array( $cart_items ) ) {
			return $bool;
		}

		foreach ( $cart_items as $cart_item_data ) {
			if ( $cart_item_data['product_id'] != $product_id ) {
				continue;
			}

			$bool = true;
			break;
		}

		return $bool;
	}
}

if ( ! function_exists( 'lty_get_cart_lottery_product_count' ) ) {

	/**
	 * Get the cart lottery product count
	 *
	 * @return bool.
	 */
	function lty_get_cart_lottery_product_count( $product_id ) {
		$count = 0;

		if ( ! $product_id ) {
			return $count;
		}

		$cart_items = WC()->cart->cart_contents;
		if ( ! lty_check_is_array( $cart_items ) ) {
			return $count;
		}

		foreach ( $cart_items as $cart_item_key => $cart_item_data ) {
			if ( $cart_item_data['product_id'] != $product_id ) {
				continue;
			}

			$count += $cart_item_data['quantity'];
		}

		return $count;
	}
}

if ( ! function_exists( 'lty_get_endpoint_url' ) ) {

	/**
	 * Get endpoint URL .
	 */
	function lty_get_endpoint_url( $query_args, $page = false, $permalink = '' ) {

		if ( ! $permalink ) {
			$permalink = get_permalink();
		}

		/**
		 * This hook is used to alter the permalink.
		 *
		 * @since 1.0
		 */
		$url = apply_filters( 'lty_get_permalink', trailingslashit( $permalink ) );

		if ( $page ) {
			$query_args = array_merge( $query_args, array( 'page_no' => $page ) );
		}

		/**
		 * This hook is used to alter the endpoint URL.
		 *
		 * @since 1.0
		 */
		return apply_filters( 'lty_get_endpoint_url', add_query_arg( $query_args, $url ) );
	}
}

if ( ! function_exists( 'lty_validate_ticket_in_cart_items' ) ) {
	/**
	 * Validate ticket in cart items.
	 *
	 * @since 1.0.0
	 * @param object $product Product object.
	 * @return void
	 */
	function lty_validate_ticket_in_cart_items( $product ) {
		if ( ! lty_is_lottery_product( $product ) || ! is_object( WC()->cart ) ) {
			return;
		}

		$args = array( 'product_id' => $product->get_id() );
		if ( $product->is_unlimited_scheduled_lottery() ) {
			$args['list_count'] = $product->get_current_relist_count();
		} else {
			$args['start_date'] = $product->get_current_start_date_gmt();
		}

		$sold_ticket_ids = lty_get_ticket_ids( $args );
		if ( ! lty_check_is_array( $sold_ticket_ids ) ) {
			return;
		}

		$sold_ticket_numbers = array();
		foreach ( $sold_ticket_ids as $sold_ticket_id ) {
			$ticket_obj            = lty_get_lottery_ticket( $sold_ticket_id );
			$sold_ticket_numbers[] = $ticket_obj->get_lottery_ticket_number();
		}

		$cart_contents = WC()->cart->cart_contents;
		if ( ! lty_check_is_array( $cart_contents ) || ! lty_check_is_array( $sold_ticket_numbers ) ) {
			return;
		}

		$removed_tickets = array();
		foreach ( $cart_contents as $cart_item_key => $values ) {
			$product_id = isset( $values['product_id'] ) ? $values['product_id'] : '';
			if ( $product->get_id() == $product_id ) {
				$product = wc_get_product( $product_id );
				if ( ! lty_is_lottery_product( $product ) ) {
					continue;
				}

				if ( isset( $values['lty_lottery']['tickets'] ) && lty_check_is_array( $values['lty_lottery']['tickets'] ) ) {
					$current_ticket_numbers = $values['lty_lottery']['tickets'];
					$duplicate_tickets      = array_intersect( $current_ticket_numbers, $sold_ticket_numbers );
					$remaining_tickets      = array_diff( $current_ticket_numbers, $sold_ticket_numbers );

					if ( ! lty_check_is_array( $duplicate_tickets ) ) {
						continue;
					}

					foreach ( $duplicate_tickets as $duplicate_ticket ) {
						$removed_tickets[] = $duplicate_ticket;
					}

					if ( lty_check_is_array( $remaining_tickets ) ) {
						$qty = count( $remaining_tickets );
						// Update cart.
						WC()->cart->set_quantity( $cart_item_key, $qty );
						WC()->cart->cart_contents[ $cart_item_key ]['lty_lottery']['tickets'] = $remaining_tickets;
					} else {
						// Remove duplicate tickets in manual tickets form.
						WC()->cart->remove_cart_item( $cart_item_key );
					}

					WC()->cart->set_session();
				}
			}
		}

		if ( lty_check_is_array( $removed_tickets ) ) {
			/* translators: %s: Ticket Numbers */
			$message = sprintf( __( 'Ticket Number(s) are you selected %1$s are purchased by another user', 'lottery-for-woocommerce' ), implode( ',', $removed_tickets ) );
			wc_add_notice( $message, 'error' );
		}
	}
}


if ( ! function_exists( 'lty_is_cart_contains_lottery_items' ) ) {

	/**
	 * Check if the cart contains lottery product.
	 *
	 * @return bool
	 */
	function lty_is_cart_contains_lottery_items() {
		$bool = false;
		if ( ! is_object( WC()->cart ) ) {
			return $bool;
		}

		$cart_contents = WC()->cart->get_cart();
		if ( ! lty_check_is_array( $cart_contents ) ) {
			return $bool;
		}

		foreach ( $cart_contents as $cart_item_key => $values ) {
			$product_id = isset( $values['product_id'] ) ? $values['product_id'] : $values['variation_id'];
			$product    = wc_get_product( $product_id );
			if ( 'lottery' != $product->get_type() ) {
				continue;
			}

			$bool = true;
			break;
		}

		return $bool;
	}
}

if ( ! function_exists( 'lty_get_session_reserved_tickets' ) ) {

	/**
	 * Get the session reserved tickets.
	 *
	 * @since 8.3.0
	 * @return array
	 */
	function lty_get_session_reserved_tickets() {
		return array_filter( (array) WC()->session->get( 'lty_reserved_tickets' ) );
	}
}

if ( ! function_exists( 'lty_get_myaccount_lottery_menu_position' ) ) {

	/**
	 * Get the position for My Acoount page lottery menu.
	 *
	 * @since 9.1.0
	 * @return string
	 * */
	function lty_get_myaccount_lottery_menu_position() {
		/**
		 * This hook is used to alter the My Account page lottery menu position.
		 *
		 * @since 9.1.0
		 */
		return apply_filters( 'lty_myaccount_lottery_menu_position', get_option( 'lty_settings_myaccount_lottery_menu_position', 'dashboard' ) );
	}
}

if ( ! function_exists( 'lty_get_ticket_logs_search_columns' ) ) {

	/**
	 * Get the ticket logs search by columns.
	 * Search results will be retrieved if the search term matches with the columns data.
	 *
	 * @since 10.2.0
	 * @return array
	 * */
	function lty_get_ticket_logs_search_columns() {
		/**
		 * This hook is used to alter the ticket logs search by columns.
		 *
		 * @since 10.2.0
		 */
		return apply_filters( 'lty_ticket_logs_search_columns', get_option( 'lty_settings_ticket_logs_search_columns', array( 'lty_ticket_number' ) ) );
	}
}

if ( ! function_exists( 'lty_get_formatted_ticket_logs_search_term' ) ) {

	/**
	 * Get the formatted ticket logs search term.
	 *
	 * @since 10.2.0
	 * @param string $term Search term.
	 * @return string
	 * */
	function lty_get_formatted_ticket_logs_search_term( $term ) {
		// Whether to retrieve related search term results or exact match results.
		return '1' === get_option( 'lty_settings_ticket_logs_search_type', '1' ) ? '%' . $term . '%' : $term;
	}
}

if ( ! function_exists( 'lty_prepare_participated_lottery_tickets_details_arguments' ) ) {

	/**
	 * Get the participated lottery tickets details arguments.
	 *
	 * @since 10.2.0
	 * @param object $product Product object.
	 * @param int    $current_page Current page.
	 * @return array
	 */
	function lty_prepare_participated_lottery_tickets_details_arguments( $product, $current_page = 1 ) {
		$args = array(
			'user_id'     => get_current_user_id(),
			'product_id'  => $product->get_id(),
			'post_status' => lty_get_lottery_ticket_statuses(),
		);

		if ( $product->is_unlimited_scheduled_lottery() ) {
			$args['list_count'] = $product->get_current_relist_count();
		} else {
			$args['start_date'] = $product->get_current_start_date_gmt();
			$args['end_date']   = $product->get_lty_end_date_gmt();
		}

		$ticket_ids    = lty_get_ticket_ids( $args );
		$post_per_page = lty_get_popup_lottery_dashboard_tickets_per_page();
		$pagination    = false;
		if ( ! empty( $post_per_page ) ) {
			$offset     = ( $post_per_page * $current_page ) - $post_per_page;
			$page_count = ceil( count( $ticket_ids ) / $post_per_page );
			$ticket_ids = array_slice( $ticket_ids, $offset, $post_per_page );
			$pagination = lty_prepare_pagination_arguments( $current_page, $page_count );
		}

		/**
		 * This hook is used to alter the participated lottery tickets details arguments.
		 *
		 * @since 10.3.0
		 */
		return apply_filters(
			'lty_participated_lottery_tickets_details_arguments',
			array(
				'product'    => $product,
				'ticket_ids' => $ticket_ids,
				'columns'    => lty_get_participated_lottery_tickets_details_columns( $product ),
				'pagination' => $pagination,
			)
		);
	}
}

if ( ! function_exists( 'lty_get_popup_lottery_dashboard_tickets_per_page' ) ) {

	/**
	 * Get the popup lottery dashboard tickets per page.
	 *
	 * @since 10.3.0
	 * @return int
	 */
	function lty_get_popup_lottery_dashboard_tickets_per_page() {
		/**
		 * This hook is used to alter the popup lottery dashboard tickets per page.
		 *
		 * @since 10.3.0
		 */
		return apply_filters( 'lty_popup_lottery_dashboard_tickets_per_page', intval( get_option( 'lty_settings_popup_lottery_dashboard_tickets_per_page', 10 ) ) );
	}
}

if ( ! function_exists( 'lty_get_instant_winner_coupon_include_products' ) ) {

	/**
	 * Get the instant winner coupon include products.
	 *
	 * @since 10.6.0
	 * @return array
	 */
	function lty_get_instant_winner_coupon_include_products() {
		/**
		 * This hook is used to alter the instant winner coupon include products.
		 *
		 * @since 10.6.0
		 */
		return apply_filters( 'lty_instant_winner_coupon_include_products', array_filter( (array) get_option( 'lty_settings_instant_win_coupon_include_products' ) ) );
	}
}

if ( ! function_exists( 'lty_get_instant_winner_coupon_exclude_products' ) ) {

	/**
	 * Get the instant winner coupon exclude products.
	 *
	 * @since 10.6.0
	 * @return array
	 */
	function lty_get_instant_winner_coupon_exclude_products() {
		/**
		 * This hook is used to alter the instant winner coupon exclude products.
		 *
		 * @since 10.6.0
		 */
		return apply_filters( 'lty_instant_winner_coupon_exclude_products', array_filter( (array) get_option( 'lty_settings_instant_win_coupon_exclude_products' ) ) );
	}
}

if ( ! function_exists( 'lty_get_instant_winner_coupon_include_categories' ) ) {

	/**
	 * Get the instant winner coupon include categories.
	 *
	 * @since 10.6.0
	 * @return array
	 */
	function lty_get_instant_winner_coupon_include_categories() {
		/**
		 * This hook is used to alter the instant winner coupon include categories.
		 *
		 * @since 10.6.0
		 */
		return apply_filters( 'lty_instant_winner_coupon_include_categories', array_filter( (array) get_option( 'lty_settings_instant_win_coupon_include_categories' ) ) );
	}
}

if ( ! function_exists( 'lty_get_instant_winner_coupon_exclude_categories' ) ) {

	/**
	 * Get the instant winner coupon exclude categories.
	 *
	 * @since 10.6.0
	 * @return array
	 */
	function lty_get_instant_winner_coupon_exclude_categories() {
		/**
		 * This hook is used to alter the instant winner coupon exclude categories.
		 *
		 * @since 10.6.0
		 */
		return apply_filters( 'lty_instant_winner_coupon_exclude_categories', array_filter( (array) get_option( 'lty_settings_instant_win_coupon_exclude_categories' ) ) );
	}
}
