<?php

/**
 * Post functions.
 * */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! function_exists( 'lty_create_new_lottery_ticket' ) ) {

	/**
	 * Create New Lottery Ticket.
	 *
	 * @return integer/string
	 * */
	function lty_create_new_lottery_ticket( $meta_args, $post_args = array() ) {

		$object = new LTY_Lottery_Ticket();
		$id     = $object->create( $meta_args, $post_args );

		return $id;
	}
}

if ( ! function_exists( 'lty_get_lottery_ticket' ) ) {

	/**
	 * Get lottery ticket object.
	 *
	 * @return object
	 * */
	function lty_get_lottery_ticket( $id ) {
		$object = new LTY_Lottery_Ticket( $id );

		return $object;
	}
}

if ( ! function_exists( 'lty_update_lottery_ticket' ) ) {

	/**
	 * Update lottery ticket.
	 *
	 * @return object
	 * */
	function lty_update_lottery_ticket( $id, $meta_args, $post_args = array() ) {

		$object = new LTY_Lottery_Ticket( $id );
		$object->update( $meta_args, $post_args );

		return $object;
	}
}

if ( ! function_exists( 'lty_delete_lottery_ticket' ) ) {

	/**
	 * Delete lottery ticket.
	 *
	 * @return bool
	 * */
	function lty_delete_lottery_ticket( $id, $force = true ) {

		wp_delete_post( $id, $force );

		return true;
	}
}

if ( ! function_exists( 'lty_get_lottery_ticket_statuses' ) ) {

	/**
	 * Get Lottery Ticket statuses.
	 *
	 * @return array
	 * */
	function lty_get_lottery_ticket_statuses() {
		/**
		 * This hook is used to alter the lottery ticket statuses.
		 *
		 * @since 1.0
		 */
		return apply_filters( 'lty_lottery_ticket_statuses', array( 'lty_ticket_buyer', 'lty_ticket_winner' ) );
	}
}

if ( ! function_exists( 'lty_get_ticket_statuses' ) ) {

	/**
	 * Get the ticket statuses.
	 *
	 * @return array
	 * */
	function lty_get_ticket_statuses() {
		/**
		 * This hook is used to alter the ticket statuses.
		 *
		 * @since 1.0
		 */
		return apply_filters( 'lty_ticket_statuses', array( 'lty_ticket_pending', 'lty_ticket_buyer', 'lty_ticket_winner' ) );
	}
}

if ( ! function_exists( 'lty_get_ticket_status_labels' ) ) {
	/**
	 * Get the ticket status labels.
	 *
	 * @since 11.9.0
	 * @return array
	 * */
	function lty_get_ticket_status_labels() {
		/**
		 * This hook is used to alter the ticket status labels.
		 *
		 * @since 11.9.0
		 */
		return apply_filters(
			'lty_ticket_statuses',
			array(
				'lty_ticket_pending'  => __( 'Ticket Pending', 'lottery-for-woocommerce' ),
				'lty_ticket_buyer'    => __( 'Ticket Buyer', 'lottery-for-woocommerce' ),
				'lty_ticket_winner'   => __( 'Ticket Winner', 'lottery-for-woocommerce' ),
				'lty_ticket_canceled' => __( 'Ticket Canceled', 'lottery-for-woocommerce' ),
			)
		);
	}
}

if ( ! function_exists( 'lty_create_new_lottery_winner' ) ) {

	/**
	 * Create New Lottery Winner.
	 *
	 * @return integer/string
	 * */
	function lty_create_new_lottery_winner( $meta_args, $post_args = array() ) {
		$object = new LTY_Lottery_Product_Winner();
		$id     = $object->create( $meta_args, $post_args );

		return $id;
	}
}

if ( ! function_exists( 'lty_get_lottery_winner' ) ) {

	/**
	 * Get lottery winner object.
	 *
	 * @return object
	 * */
	function lty_get_lottery_winner( $id ) {
		$object = new LTY_Lottery_Product_Winner( $id );

		return $object;
	}
}

if ( ! function_exists( 'lty_update_lottery_winner' ) ) {

	/**
	 * Update lottery winner.
	 *
	 * @return object
	 * */
	function lty_update_lottery_winner( $id, $meta_args, $post_args = array() ) {
		$object = new LTY_Lottery_Product_Winner( $id );
		$object->update( $meta_args, $post_args );

		return $object;
	}
}

if ( ! function_exists( 'lty_delete_lottery_winner' ) ) {

	/**
	 * Delete lottery winner.
	 *
	 * @return bool
	 * */
	function lty_delete_lottery_winner( $id, $force = true ) {

		wp_delete_post( $id, $force );

		return true;
	}
}

if ( ! function_exists( 'lty_create_new_instant_winner_prize_group' ) ) {
	/**
	 * Create new instant winner prize group.
	 *
	 * @since 11.1.0
	 * @param array $meta_args Meta arguments.
	 * @param array $post_args Post arguments.
	 * @return int
	 */
	function lty_create_new_instant_winner_prize_group( $meta_args, $post_args = array() ) {
		$object = new LTY_Instant_Winner_Prize_Group();
		$id     = $object->create( $meta_args, $post_args );

		return $id;
	}
}

if ( ! function_exists( 'lty_update_instant_winner_prize_group' ) ) {
	/**
	 * Update the instant winner prize group.
	 *
	 * @since 11.1.0
	 * @param int   $id Instant winner prize group ID.
	 * @param array $meta_args Meta arguments.
	 * @param array $post_args Post arguments.
	 * @return object
	 */
	function lty_update_instant_winner_prize_group( $id, $meta_args, $post_args = array() ) {
		$object = new LTY_Instant_Winner_Prize_Group( $id );
		$object->update( $meta_args, $post_args );

		return $object;
	}
}

if ( ! function_exists( 'lty_delete_instant_winner_prize_group' ) ) {
	/**
	 * Delete the instant winner prize group.
	 *
	 * @since 11.1.0
	 * @param int  $id Instant winner prize group ID.
	 * @param bool $force Whether to bypass Trash and force deletion.
	 * @return bool
	 */
	function lty_delete_instant_winner_prize_group( $id, $force = true ) {
		wp_delete_post( $id, $force );

		return true;
	}
}

if ( ! function_exists( 'lty_get_instant_winner_prize_group' ) ) {
	/**
	 * Get the instant winner prize group object.
	 *
	 * @since 11.1.0
	 * @param int $id Instant winner prize group ID.
	 * @return object
	 */
	function lty_get_instant_winner_prize_group( $id ) {
		$object = new LTY_Instant_Winner_Prize_Group( $id );

		return $object;
	}
}

if ( ! function_exists( 'lty_get_instant_winner_prize_group_statuses' ) ) {
	/**
	 * Get lottery instant winner prize group statuses.
	 *
	 * @since 11.1.0
	 * @return array
	 * */
	function lty_get_instant_winner_prize_group_statuses() {
		/**
		 * This hook is used to alter the instant winner prize group statuses.
		 *
		 * @since 11.1.0
		 */
		return apply_filters( 'lty_instant_winner_prize_group_statuses', array( 'publish' ) );
	}
}

if ( ! function_exists( 'lty_get_lottery_tickets' ) ) {
	/**
	 * Get the lottery tickets.
	 *
	 * @deprecated since 11.7.0, use lty_get_lottery_ticket_ids instead.
	 * @return array
	 * */
	function lty_get_lottery_tickets( $user_id = false, $product_id = false, $start_date = false, $end_date = false, $post_status = false, $order_by = 'ID', $order = 'DESC', $page = false, $limit = false ) {
		wc_deprecated_function( 'lty_get_lottery_tickets', '11.7.0', 'lty_get_lottery_ticket_ids' );

		return lty_get_ticket_ids(
			array(
				'user_id'     => $user_id,
				'product_id'  => $product_id,
				'start_date'  => $start_date,
				'end_date'    => $end_date,
				'post_status' => $post_status,
				'order_by'    => $order_by,
				'order'       => $order,
				'page'        => $page,
				'limit'       => $limit,
			)
		);
	}
}

if ( ! function_exists( 'lty_get_ticket_ids' ) ) {
	/**
	 * Get the lottery ticket ID's.
	 *
	 * @since 11.7.0
	 * @param array $args Ticket arguments.
	 * @return array
	 * */
	function lty_get_ticket_ids( $args ) {
		$args = wp_parse_args(
			$args,
			array(
				'user_id'       => false,
				'user_email_id' => false,
				'product_id'    => false,
				'start_date'    => false,
				'end_date'      => false,
				'post_status'   => lty_get_ticket_statuses(),
				'order_by'      => 'ID',
				'order'         => 'DESC',
				'page'          => false,
				'limit'         => '-1',
				'list_count'    => false,
			)
		);

		$query_args = array(
			'post_type'       => LTY_Register_Post_Types::LOTTERY_TICKET_POSTTYPE,
			'fields'          => 'ids',
			'post_status'     => $args['post_status'],
			'posts_per_page'  => $args['limit'],
			'order'           => $args['order'],
			'orderby'         => $args['order_by'],
			'post_parent__in' => false !== $args['product_id'] ? ( lty_check_is_array( $args['product_id'] ) ? $args['product_id'] : array( lty_get_lottery_id( $args['product_id'] ) ) ) : lty_get_lottery_ids(),
		);

		if ( false !== $args['start_date'] || false !== $args['end_date'] ) {
			$query_args['date_query'] = array();
			if ( false !== $args['start_date'] ) {
				$query_args['date_query'][] = array(
					'column' => 'post_date_gmt',
					'after'  => $args['start_date'],
				);
			}

			if ( false !== $args['end_date'] ) {
				$query_args['date_query'][] = array(
					'column' => 'post_date_gmt',
					'before' => $args['end_date'],
				);
			}
		}

		$meta_query = array();
		if ( false !== $args['user_id'] ) {
			$meta_query[] = array(
				'key'   => 'lty_user_id',
				'value' => $args['user_id'],
			);
		}

		if ( false !== $args['user_email_id'] ) {
			$meta_query[] = array(
				'key'   => 'lty_user_email',
				'value' => $args['user_email_id'],
			);
		}

		if ( false !== $args['list_count'] ) {
			$meta_query[] = array(
				'key'   => 'lty_list_count',
				'value' => $args['list_count'],
			);
		}

		if ( lty_check_is_array( $meta_query ) ) {
			$query_args['meta_query'] = array_merge( array( 'relation' => 'AND' ), $meta_query );
		}

		if ( $args['page'] ) {
			$query_args['paged'] = $args['page'];
		}

		if ( 'ID' !== $query_args['orderby'] ) {
			$query_args['meta_key'] = 'lty_ticket_number';
		}

		return get_posts( $query_args );
	}
}

if ( ! function_exists( 'lty_get_valid_winner_lottery_tickets' ) ) {

	/**
	 * Get the valid winner lottery tickets.
	 *
	 * @return array
	 * */
	function lty_get_valid_winner_lottery_tickets( $product ) {
		if ( ! lty_is_lottery_product( $product ) ) {
			return array();
		}

		$args = array(
			'post_type'      => LTY_Register_Post_Types::LOTTERY_TICKET_POSTTYPE,
			'post_status'    => 'lty_ticket_buyer',
			'posts_per_page' => '-1',
			'fields'         => 'ids',
			'orderby'        => 'ID',
			'post_parent'    => $product->get_lottery_id(),
		);

		if ( false !== $product->get_lty_relisted_date_gmt() ) {
			$args['date_query'][] = array(
				'column' => 'post_date_gmt',
				'after'  => $product->get_lty_relisted_date_gmt(),
			);
		}

		return get_posts( $args );
	}
}

if ( ! function_exists( 'lty_get_lottery_winners' ) ) {
	/**
	 * Get lottery winners.
	 *
	 * @deprecated since 11.7.0, use lty_get_lottery_winner_ids instead.
	 * @return array
	 * */
	function lty_get_lottery_winners( $user_id = false, $product_id = false, $start_date = false, $end_date = false ) {
		wc_deprecated_function( 'lty_get_lottery_winners', '11.7.0', 'lty_get_lottery_winner_ids' );

		return lty_get_lottery_winner_ids(
			array(
				'user_id'    => $user_id,
				'product_id' => $product_id,
				'start_date' => $start_date,
				'end_date'   => $end_date,
			)
		);
	}
}

if ( ! function_exists( 'lty_get_lottery_winner_ids' ) ) {
	/**
	 * Get lottery winner ID's.
	 *
	 * @since 11.7.0
	 * @param array $args Lottery winner arguments.
	 * @return array
	 * */
	function lty_get_lottery_winner_ids( $args = array() ) {
		$args = wp_parse_args(
			$args,
			array(
				'user_id'    => false,
				'product_id' => false,
				'start_date' => false,
				'end_date'   => false,
				'list_count' => false,
			)
		);

		$query_args = array(
			'post_type'      => LTY_Register_Post_Types::LOTTERY_WINNER_POSTTYPE,
			'post_status'    => array( 'lty_publish' ),
			'posts_per_page' => '-1',
			'fields'         => 'ids',
			'orderby'        => 'ID',
		);

		$meta_query = array();
		if ( false !== $args['user_id'] ) {
			$meta_query[] = array(
				'key'   => 'lty_user_id',
				'value' => $args['user_id'],
			);
		}

		if ( false !== $args['list_count'] ) {
			$meta_query[] = array(
				'key'   => 'lty_list_count',
				'value' => $args['list_count'],
			);
		}

		if ( lty_check_is_array( $meta_query ) ) {
			$query_args['meta_query'] = array_merge( array( 'relation' => 'AND' ), $meta_query );
		}

		if ( false !== $args['product_id'] ) {
			$query_args['post_parent'] = lty_get_lottery_id( $args['product_id'] );
		}

		if ( false !== $args['start_date'] || false !== $args['end_date'] ) {
			$query_args['date_query'] = array();
			if ( false !== $args['start_date'] ) {
				$query_args['date_query'][] = array(
					'column'    => 'post_date_gmt',
					'after'     => $args['start_date'],
					'inclusive' => true,
				);
			}

			if ( false !== $args['end_date'] ) {
				$query_args['date_query'][] = array(
					'column'    => 'post_date_gmt',
					'before'    => $args['end_date'],
					'inclusive' => true,
				);
			}
		}

		return get_posts( $query_args );
	}
}

if ( ! function_exists( 'lty_get_lottery_statuses' ) ) {

	/**
	 * Get Lottery statuses.
	 *
	 * @return array
	 * */
	function lty_get_lottery_statuses() {
		static $lottery_statuses;

		if ( $lottery_statuses ) {
			return $lottery_statuses;
		}
		/**
		 * This hook is used to alter the lottery statuses.
		 *
		 * @since 1.0
		 */
		return apply_filters(
			'lty_lottery_statuses',
			array(
				'lty_lottery_not_started' => get_option( 'lty_settings_lottery_not_started_label' ),
				'lty_lottery_started'     => get_option( 'lty_settings_lottery_started_label' ),
				'lty_lottery_closed'      => get_option( 'lty_settings_lottery_closed_label' ),
				'lty_lottery_finished'    => get_option( 'lty_settings_lottery_finished_label' ),
				'lty_lottery_failed'      => get_option( 'lty_settings_lottery_failed_label' ),
			)
		);
	}
}

if ( ! function_exists( 'lty_lottery_reset_meta_keys' ) ) {

	/**
	 * Get lottery reset meta keys
	 *
	 * @return array
	 */
	function lty_lottery_reset_meta_keys() {
		/**
		 * This hook is used to alter the lottery reset meta keys.
		 *
		 * @since 1.0
		 */
		return apply_filters(
			'lty_lottery_reset_meta_keys',
			array(
				'_lty_closed',
				'_lty_failed_reason',
				'_lty_closed_date',
				'_lty_closed_date_gmt',
				'_lty_failed_date',
				'_lty_failed_date_gmt',
				'_lty_last_activity',
				'_lty_ticket_count',
				'_lty_manual_reserved_tickets',
				'_lty_incorrect_answer_user_ids',
				'_lty_question_answer_viewed_data',
				'_lty_question_answer_attempts_data',
				'_lty_hold_tickets',
				'_lty_ending_soon_user_email_sent',
			)
		);
	}
}

if ( ! function_exists( 'lty_lottery_extend_meta_keys' ) ) {

	/**
	 * Get the lottery extend meta keys.
	 *
	 * @return array
	 */
	function lty_lottery_extend_meta_keys() {
		/**
		 * This hook is used to alter the lottery extend meta keys.
		 *
		 * @since 1.0
		 */
		return apply_filters(
			'lty_lottery_extend_meta_keys',
			array(
				'_lty_closed',
				'_lty_failed_reason',
				'_lty_closed_date',
				'_lty_closed_date_gmt',
				'_lty_failed_date',
				'_lty_failed_date_gmt',
			)
		);
	}
}

if ( ! function_exists( 'lty_get_failed_reasons' ) ) {

	/**
	 * Get lottery failed reasons.
	 *
	 * @return array
	 */
	function lty_get_failed_reasons() {
		static $reasons;

		if ( ! $reasons ) {
			/**
			 * This hook is used to alter the lottery failed reasons.
			 *
			 * @since 1.0
			 */
			$reasons = apply_filters(
				'lty_lottery_failed_reason',
				array(
					'1' => get_option( 'lty_settings_minimum_tickets_not_met_error', __( 'Minimum Giveaway Ticket not met', 'lottery-for-woocommerce' ) ),
					'2' => get_option( 'lty_settings_unique_winners_count_not_met_error', __( 'Unique Winners Count not met', 'lottery-for-woocommerce' ) ),
				)
			);
		}

		return $reasons;
	}
}

if ( ! function_exists( 'lty_get_lottery_ticket_ids_by_product_id' ) ) {
	/**
	 * Get Lottery ticket ID's by product ID.
	 *
	 * @since 1.0.0
	 * @param int          $product_id Product ID.
	 * @param bool         $count Whether to return count or IDs.
	 * @param string|array $post_status Post status.
	 * @return array
	 */
	function lty_get_lottery_ticket_ids_by_product_id( $product_id, $count = false, $post_status = false ) {
		if ( empty( $product_id ) ) {
			return;
		}

		$ticket_ids = lty_get_ticket_ids(
			array(
				'product_id'  => $product_id,
				'post_status' => $post_status,
			)
		);

		return $count ? ( lty_check_is_array( $ticket_ids ) ? count( $ticket_ids ) : 0 ) : $ticket_ids;
	}
}

if ( ! function_exists( 'lty_get_lottery_ticket_ids_by_user_id' ) ) {
	/**
	 * Get lottery ticket ID's by user ID.
	 *
	 * @since 1.0.0
	 * @param int  $user_id User ID.
	 * @param bool $count Whether to return count or ID's.
	 * @return array
	 */
	function lty_get_lottery_ticket_ids_by_user_id( $user_id, $count = false ) {
		if ( empty( $user_id ) ) {
			$user_id = get_current_user_id();
		}

		$ticket_ids = lty_get_ticket_ids( array( 'user_id' => $user_id ) );

		return $count ? ( lty_check_is_array( $ticket_ids ) ? count( $ticket_ids ) : 0 ) : $ticket_ids;
	}
}

if ( ! function_exists( 'lty_product_ticket_number_exists' ) ) {
	/**
	 * Is ticket number exists in the product?
	 *
	 * @since 1.0.0
	 * @param  int    $product_id Product ID.
	 * @param  string $ticket_number Ticket number.
	 * @global object $wpdb WordPress database object.
	 * @return bool
	 */
	function lty_product_ticket_number_exists( $product_id, $ticket_number ) {
		if ( empty( $product_id ) || empty( $ticket_number ) ) {
			return false;
		}

		$product = wc_get_product( $product_id );
		if ( ! lty_is_lottery_product( $product ) ) {
			return false;
		}

		global $wpdb;
		$post_query = new LTY_Query( $wpdb->prefix . 'posts', 'p' );
		$post_query->leftJoin( $wpdb->prefix . 'postmeta', 'pm1', '`p`.`ID` = `pm1`.`post_id`' )
				->where( '`p`.post_type', LTY_Register_Post_Types::LOTTERY_TICKET_POSTTYPE )
				->whereIn( '`p`.post_status', lty_get_ticket_statuses() )
				->where( '`p`.post_parent', $product->get_lottery_id() )
				->where( '`pm1`.meta_key', 'lty_ticket_number' )
				->where( '`pm1`.meta_value', $ticket_number );

		if ( $product->is_unlimited_scheduled_lottery() ) {
			$post_query->leftJoin( $wpdb->prefix . 'postmeta', 'pm2', '`p`.`ID` = `pm2`.`post_id`' )
				->where( '`pm2`.meta_key', 'lty_list_count' )
				->where( '`pm2`.meta_value', $product->get_current_relist_count() );
		} else {
			$post_query->whereBetween( '`p`.post_date_gmt', $product->get_current_start_date_gmt(), $product->get_lty_end_date_gmt() );
		}

		$ticket_ids = $post_query->fetchCol( '`p`.`ID`' );

		return lty_check_is_array( $ticket_ids );
	}
}

if ( ! function_exists( 'lty_product_ticket_numbers_exists' ) ) {
	/**
	 * Check the product ticket numbers exists.
	 *
	 * @deprecated since 11.7.0, use lty_product_ticket_number_exists instead.
	 * @return bool
	 */
	function lty_product_ticket_numbers_exists( $product_id, $ticket_number ) {
		wc_deprecated_function( 'lty_product_ticket_numbers_exists', '11.7.0', 'lty_product_ticket_number_exists' );

		return lty_product_ticket_number_exists( $product_id, $ticket_number );
	}
}

if ( ! function_exists( 'lty_get_user_placed_ticket_numbers_by_product_id' ) ) {

	/**
	 * Get the lottery ticket numbers by user placed using product id.
	 *
	 * @return array.
	 */
	function lty_get_user_placed_ticket_numbers_by_product_id( $product = false, $user_id = false ) {
		if ( ! $user_id ) {
			$user_id = get_current_user_id();
			if ( ! $user_id ) {
				return array();
			}
		}

		return lty_get_ticket_numbers( $product, $user_id );
	}
}

if ( ! function_exists( 'lty_get_user_purchased_ticket_numbers_by_product_id' ) ) {

	/**
	 * Get the lottery ticket numbers by user purchased using product id.
	 *
	 * @return array.
	 */
	function lty_get_user_purchased_ticket_numbers_by_product_id( $product = false, $user_id = false ) {
		if ( ! $user_id ) {
			$user_id = get_current_user_id();
			if ( ! $user_id ) {
				return array();
			}
		}

		return lty_get_ticket_numbers( $product, $user_id, false );
	}
}

if ( ! function_exists( 'lty_get_placed_ticket_numbers_by_product_id' ) ) {

	/**
	 * Get the lottery ticket numbers placed using product id.
	 *
	 * @return array.
	 */
	function lty_get_placed_ticket_numbers_by_product_id( $product = false ) {
		return lty_get_ticket_numbers( $product );
	}
}

if ( ! function_exists( 'lty_get_purchased_ticket_numbers_by_product_id' ) ) {

	/**
	 * Get the lottery ticket numbers purchased using product id.
	 *
	 * @return array.
	 */
	function lty_get_purchased_ticket_numbers_by_product_id( $product = false ) {
		return lty_get_ticket_numbers( $product, false, false );
	}
}

if ( ! function_exists( 'lty_get_purchased_ticket_numbers_by_order' ) ) {

	/**
	 * Get the lottery ticket numbers purchased by order.
	 *
	 * @return array.
	 */
	function lty_get_purchased_ticket_numbers_by_order( $product, $order_id, $user_id ) {
		return lty_get_ticket_numbers( $product, $user_id, 'all', $order_id );
	}
}

if ( ! function_exists( 'lty_get_ticket_numbers' ) ) {
	/**
	 * Get the lottery ticket numbers.
	 *
	 * @since 1.0.0
	 * @param object|int $product Product object or ID.
	 * @param bool       $user_id User ID.
	 * @param string     $post_status Post status.
	 * @param bool       $order_id Order ID.
	 * @return array
	 */
	function lty_get_ticket_numbers( $product, $user_id = false, $post_status = 'all', $order_id = false ) {
		$product = ! is_object( $product ) ? wc_get_product( $product ) : $product;
		if ( ! lty_is_lottery_product( $product ) ) {
			return array();
		}

		// Ticket statuses.
		$post_statuses = 'all' === $post_status ? lty_get_ticket_statuses() : lty_get_lottery_ticket_statuses();

		global $wpdb;
		$post_query = new LTY_Query( $wpdb->prefix . 'posts', 'p' );
		$post_query->leftJoin( $wpdb->prefix . 'postmeta', 'pm', '`p`.`ID` = `pm`.`post_id`' )
				->where( '`p`.post_type', LTY_Register_Post_Types::LOTTERY_TICKET_POSTTYPE )
				->whereIn( '`p`.post_status', $post_statuses )
				->where( '`p`.post_parent', $product->get_lottery_id() )
				->where( '`pm`.meta_key', 'lty_ticket_number' );

		if ( $user_id && $order_id ) {
			$post_query->leftJoin( $wpdb->prefix . 'postmeta', 'pm1', '`p`.`ID` = `pm1`.`post_id`' )
					->where( '`pm1`.meta_key', 'lty_user_id' )
					->where( '`pm1`.meta_value', $user_id );
			$post_query->leftJoin( $wpdb->prefix . 'postmeta', 'pm2', '`p`.`ID` = `pm2`.`post_id`' )
					->where( '`pm2`.meta_key', 'lty_order_id' )
					->where( '`pm2`.meta_value', $order_id );
		} elseif ( $user_id ) {
			$post_query->leftJoin( $wpdb->prefix . 'postmeta', 'pm1', '`p`.`ID` = `pm1`.`post_id`' )
					->where( '`pm1`.meta_key', 'lty_user_id' )
					->where( '`pm1`.meta_value', $user_id );
		} elseif ( $order_id ) {
			$post_query->leftJoin( $wpdb->prefix . 'postmeta', 'pm1', '`p`.`ID` = `pm1`.`post_id`' )
					->where( '`pm1`.meta_key', 'lty_order_id' )
					->where( '`pm1`.meta_value', $order_id );
		}

		if ( $product->is_unlimited_scheduled_lottery() ) {
			$post_query->leftJoin( $wpdb->prefix . 'postmeta', 'pm3', '`p`.`ID` = `pm3`.`post_id`' )
				->where( '`pm3`.meta_key', 'lty_list_count' )
				->where( '`pm3`.meta_value', $product->get_current_relist_count() );
		} else {
			$post_query->whereBetween( '`p`.post_date_gmt', $product->get_current_start_date_gmt(), $product->get_lty_end_date_gmt() );
		}

		return $post_query->fetchCol( '`pm`.meta_value' );
	}
}

if ( ! function_exists( 'lty_get_unique_lottery_ticket_ids' ) ) {

	/**
	 * Get unique lottery ticket ids by user email ids.
	 *
	 * @since 10.1.0
	 * @param object $product Product object.
	 * @param string $status Ticket status.
	 * @global object $wpdb WordPress database.
	 * @return array
	 */
	function lty_get_unique_lottery_ticket_ids( $product, $status = 'all' ) {
		if ( ! lty_is_lottery_product( $product ) ) {
			return array();
		}

		global $wpdb;
		$status = 'all' === $status ? lty_get_lottery_ticket_statuses() : array( $status );

		$post_query = new LTY_Query( $wpdb->prefix . 'posts', 'p' );
		$post_query->innerJoin( $wpdb->prefix . 'postmeta', 'pm', '`p`.`ID` = `pm`.`post_id`' )
				->where( '`p`.post_type', LTY_Register_Post_Types::LOTTERY_TICKET_POSTTYPE )
				->whereIn( '`p`.post_status', $status )
				->where( '`p`.post_parent', $product->get_lottery_id() )
				->where( '`pm`.meta_key', 'lty_user_email' )
				->groupBy( '`pm`.meta_value' );

		if ( $product->is_unlimited_scheduled_lottery() ) {
			$post_query->leftJoin( $wpdb->prefix . 'postmeta', 'pm1', '`p`.`ID` = `pm1`.`post_id`' )
				->where( '`pm1`.meta_key', 'lty_list_count' )
				->where( '`pm1`.meta_value', $product->get_current_relist_count() );
		} else {
			$post_query->whereBetween( '`p`.post_date_gmt', $product->get_current_start_date_gmt(), $product->get_lty_end_date_gmt() );
		}

		return $post_query->fetchCol( '`p`.ID' );
	}
}

if ( ! function_exists( 'lty_get_lottery_user_email_ids' ) ) {

	/**
	 * Get lottery ticket purchased user email ids.
	 *
	 * @since 12.4.0
	 * @param object $product Product object.
	 * @param string $status Ticket status.
	 * @global object $wpdb WordPress database.
	 * @return array
	 */
	function lty_get_lottery_user_email_ids( $product, $status = 'all' ) {
		if ( ! lty_is_lottery_product( $product ) ) {
			return array();
		}

		global $wpdb;
		$status = 'all' === $status ? lty_get_lottery_ticket_statuses() : array( $status );

		$post_query = new LTY_Query( $wpdb->prefix . 'posts', 'p' );
		$post_query->innerJoin( $wpdb->prefix . 'postmeta', 'pm', '`p`.`ID` = `pm`.`post_id`' )
				->where( '`p`.post_type', LTY_Register_Post_Types::LOTTERY_TICKET_POSTTYPE )
				->whereIn( '`p`.post_status', $status )
				->where( '`p`.post_parent', $product->get_lottery_id() )
				->where( '`pm`.meta_key', 'lty_user_email' );

		if ( $product->is_unlimited_scheduled_lottery() ) {
			$post_query->leftJoin( $wpdb->prefix . 'postmeta', 'pm1', '`p`.`ID` = `pm1`.`post_id`' )
				->where( '`pm1`.meta_key', 'lty_list_count' )
				->where( '`pm1`.meta_value', $product->get_current_relist_count() );
		} else {
			$post_query->whereBetween( '`p`.post_date_gmt', $product->get_current_start_date_gmt(), $product->get_lty_end_date_gmt() );
		}

		return $post_query->fetchCol( 'DISTINCT `pm`.meta_value' );
	}
}

if ( ! function_exists( 'lty_get_lottery_ticket_ids_by_user_email_id' ) ) {
	/**
	 * Get ticket ids by user email id.
	 *
	 * @since 12.4.0
	 * @param object $product Product object.
	 * @param string $user_email_id User email ID.
	 * @param string $status Ticket status.
	 * @return array
	 * */
	function lty_get_lottery_ticket_ids_by_user_email_id( $product, $user_email_id, $status = 'all' ) {
		if ( ! lty_is_lottery_product( $product ) ) {
			return array();
		}

		$args = array(
			'product_id'    => $product->get_id(),
			'user_email_id' => $user_email_id,
			'post_status'   => $status,
		);

		if ( $product->is_unlimited_scheduled_lottery() ) {
			$args['list_count'] = $product->get_current_relist_count();
		} else {
			$args['start_date'] = $product->get_current_start_date_gmt();
			$args['end_date']   = $product->get_lty_end_date_gmt();
		}

		return lty_get_ticket_ids( $args );
	}
}

if ( ! function_exists( 'lty_get_my_lotteries' ) ) {
	/**
	 * Get user participated lottery ID's.
	 *
	 * @since 1.0.0
	 * @param int   $user_id User ID.
	 * @param array $status Post status.
	 * @global object $wpdb WordPress database object.
	 * @return array
	 */
	function lty_get_my_lotteries( $user_id = false, $status = array( 'lty_ticket_buyer' ) ) {
		global $wpdb;
		if ( ! $user_id ) {
			$user_id = get_current_user_id();
		}

		$post_query = new LTY_Query( $wpdb->prefix . 'posts', 'p' );
		$post_query->innerJoin( $wpdb->prefix . 'postmeta', 'pm', '`p`.`ID` = `pm`.`post_id`' )
				->where( '`p`.post_type', LTY_Register_Post_Types::LOTTERY_TICKET_POSTTYPE )
				->whereIn( '`p`.post_status', $status )
				->where( '`pm`.meta_key', 'lty_user_id' )
				->where( '`pm`.meta_value', $user_id )
				->groupBy( '`p`.post_parent' );

		return $post_query->fetchCol( '`p`.`post_parent`' );
	}
}

if ( ! function_exists( 'lty_get_my_winner_lotteries' ) ) {
	/**
	 * Get user won lottery ID's.
	 *
	 * @since 1.0.0
	 * @param int $user_id User ID.
	 * @global object $wpdb WordPress database object.
	 * @return array
	 */
	function lty_get_my_winner_lotteries( $user_id = false ) {
		global $wpdb;
		if ( ! $user_id ) {
			$user_id = get_current_user_id();
		}

		$post_query = new LTY_Query( $wpdb->prefix . 'posts', 'p' );
		$post_query->innerJoin( $wpdb->prefix . 'postmeta', 'pm', '`p`.`ID` = `pm`.`post_id`' )
				->where( '`p`.post_type', LTY_Register_Post_Types::LOTTERY_WINNER_POSTTYPE )
				->whereIn( '`p`.post_status', array( 'lty_publish' ) )
				->where( '`pm`.meta_key', 'lty_user_id' )
				->where( '`pm`.meta_value', $user_id )
				->groupBy( '`p`.post_parent' );

		return $post_query->fetchCol( '`p`.`post_parent`' );
	}
}

if ( ! function_exists( 'lty_get_my_lost_lottery_ticket_from_product_id' ) ) {
	/**
	 * Get My lotteries.
	 *
	 * @return ID (or) bool
	 */
	function lty_get_my_lost_lottery_ticket_from_product_id( $product_ids, $user_id = false ) {
		global $wpdb;
		if ( ! $user_id ) {
			$user_id = get_current_user_id();
		}

		$post_query = new LTY_Query( $wpdb->prefix . 'posts', 'p' );
		$post_query->innerJoin( $wpdb->prefix . 'postmeta', 'pm', '`p`.`ID` = `pm`.`post_id`' )
				->where( '`p`.post_type', LTY_Register_Post_Types::LOTTERY_TICKET_POSTTYPE )
				->whereIn( '`p`.post_status', array( 'lty_ticket_buyer' ) )
				->whereIn( '`p`.post_parent', $product_ids )
				->where( '`pm`.meta_key', 'lty_user_id' )
				->where( '`pm`.meta_value', $user_id );

		return $post_query->fetchCol( '`p`.`ID`' );
	}
}

if ( ! function_exists( 'lty_get_lottery_winners_by_product_id' ) ) {
	/**
	 * Get the lottery winner ID's by product ID.
	 *
	 * @since 1.0.0
	 * @param int  $product_id Product ID.
	 * @param bool $start_date Start date.
	 * @param bool $end_date End date.
	 * @param int  $list_count List count.
	 * @return array
	 */
	function lty_get_lottery_winners_by_product_id( $product_id, $start_date = false, $end_date = false, $list_count = false ) {
		if ( ! lty_is_lottery_product( $product_id ) ) {
			return array();
		}

		return lty_get_lottery_winner_ids(
			array(
				'product_id' => $product_id,
				'start_date' => $start_date,
				'end_date'   => $end_date,
				'list_count' => $list_count,
			)
		);
	}
}

if ( ! function_exists( 'lty_get_user_winner_ids_by_product_id' ) ) {
	/**
	 * Get the user lottery winner ID's by product ID.
	 *
	 * @since 1.0.0
	 * @param int    $user_id User ID.
	 * @param int    $product_id Product ID.
	 * @param string $start_date Start date.
	 * @param string $end_date End date.
	 * @param int    $list_count List count.
	 * @return array
	 */
	function lty_get_user_winner_ids_by_product_id( $user_id, $product_id, $start_date = false, $end_date = false, $list_count = false ) {
		return lty_get_lottery_winner_ids(
			array(
				'user_id'    => $user_id,
				'product_id' => $product_id,
				'start_date' => $start_date,
				'end_date'   => $end_date,
				'list_count' => $list_count,
			)
		);
	}
}

if ( ! function_exists( 'lty_get_lottery_participant_count' ) ) {
	/**
	 * Get lottery participant count.
	 *
	 * @since 1.0.0
	 * @param int    $product_id Product ID.
	 * @param bool   $count Whether to return participant count or user IDs.
	 * @param string $post_status Post status.
	 * @return int|array
	 */
	function lty_get_lottery_participant_count( $product_id, $count = false, $post_status = false ) {
		if ( empty( $product_id ) ) {
			return;
		}

		$product = wc_get_product( $product_id );
		if ( ! lty_is_lottery_product( $product ) ) {
			return;
		}

		global $wpdb;
		$post_statuses = 'all' === $post_status ? lty_get_ticket_statuses() : lty_get_lottery_ticket_statuses();
		$post_query    = new LTY_Query( $wpdb->prefix . 'posts', 'p' );
		$post_query->leftJoin( $wpdb->prefix . 'postmeta', 'pm', '`p`.`ID` = `pm`.`post_id`' )
				->where( '`p`.post_type', LTY_Register_Post_Types::LOTTERY_TICKET_POSTTYPE )
				->whereIn( '`p`.post_status', $post_statuses )
				->where( '`p`.post_parent', $product->get_lottery_id() )
				->where( '`pm`.meta_key', 'lty_user_email' );

		if ( $product->is_unlimited_scheduled_lottery() ) {
			$post_query->leftJoin( $wpdb->prefix . 'postmeta', 'pm1', '`p`.`ID` = `pm1`.`post_id`' )
				->where( '`pm1`.meta_key', 'lty_list_count' )
				->where( '`pm1`.meta_value', $product->get_current_relist_count() );
		} else {
			$post_query->whereBetween( '`p`.post_date_gmt', $product->get_current_start_date_gmt(), $product->get_lty_end_date_gmt() );
		}

		$user_ids = $post_query->fetchCol( 'DISTINCT `pm`.meta_value' );

		return $count ? ( lty_check_is_array( $user_ids ) ? count( $user_ids ) : 0 ) : $user_ids;
	}
}

if ( ! function_exists( 'lty_get_lottery_participant_user_ids' ) ) {
	/**
	 * Get lottery participant user ids.
	 *
	 * @since 12.4.0
	 * @param int $product_id Product ID.
	 * @param string $post_status Post status.
	 * @return array
	 */
	function lty_get_lottery_participant_user_ids( $product_id, $post_status = false ) {
		if ( empty( $product_id ) ) {
			return array();
		}

		$product = wc_get_product( $product_id );
		if ( ! lty_is_lottery_product( $product ) ) {
			return array();
		}

		global $wpdb;
		$post_statuses = 'all' === $post_status ? lty_get_ticket_statuses() : lty_get_lottery_ticket_statuses();
		$user_id_query = new LTY_Query( $wpdb->prefix . 'posts', 'p' );
		$user_id_query->leftJoin($wpdb->prefix . 'postmeta', 'pm', '`p`.`ID` = `pm`.`post_id` AND `pm`.meta_key = "lty_user_id"')
				->where( 'p.post_type', LTY_Register_Post_Types::LOTTERY_TICKET_POSTTYPE )
				->whereIn( 'p.post_status', $post_statuses )
				->where( 'p.post_parent', $product->get_lottery_id() )
				->groupBy( 'pm.meta_value' );

		if ( $product->is_unlimited_scheduled_lottery() ) {
			$user_id_query->leftJoin( $wpdb->prefix . 'postmeta', 'pm1', '`p`.`ID` = `pm1`.`post_id`' )
				->where( '`pm1`.meta_key', 'lty_list_count' )
				->where( '`pm1`.meta_value', $product->get_current_relist_count() );
		} else {
			$user_id_query->whereBetween( '`p`.post_date_gmt', $product->get_current_start_date_gmt(), $product->get_lty_end_date_gmt() );
		}

		$user_ids = (array) $user_id_query->fetchCol( 'DISTINCT `pm`.meta_value' );

		// Guest user emails.
		$user_email_query = new LTY_Query( $wpdb->prefix . 'posts', 'p' );
		$user_email_query->leftJoin($wpdb->prefix . 'postmeta', 'pm', '`p`.`ID` = `pm`.`post_id` AND `pm`.meta_key = "lty_user_id"')
			->leftJoin($wpdb->prefix . 'postmeta', 'pm1', '`p`.`ID` = `pm1`.`post_id` AND `pm1`.meta_key = "lty_user_email"')
			->where( 'p.post_type', LTY_Register_Post_Types::LOTTERY_TICKET_POSTTYPE )
			->whereIn( 'p.post_status', $post_statuses )
			->where( 'p.post_parent', $product->get_lottery_id() )
			->where( 'pm.meta_value', '0' )
			->groupBy( 'pm1.meta_value' );

		if ( $product->is_unlimited_scheduled_lottery() ) {
			$user_email_query->leftJoin( $wpdb->prefix . 'postmeta', 'pm2', '`p`.`ID` = `pm2`.`post_id`' )
				->where( '`pm2`.meta_key', 'lty_list_count' )
				->where( '`pm2`.meta_value', $product->get_current_relist_count() );
		} else {
			$user_email_query->whereBetween( '`p`.post_date_gmt', $product->get_current_start_date_gmt(), $product->get_lty_end_date_gmt() );
		}

		$user_emails = (array) $user_email_query->fetchCol( 'DISTINCT `pm1`.meta_value' );

		return array_values( array_unique( array_merge( array_filter( $user_ids ), array_filter( $user_emails ) ) ) );
	}
}

if ( ! function_exists( 'lty_get_placed_lottery_product_ticket_ids' ) ) {
	/**
	 * Get the placed lottery product ticket ID's.
	 *
	 * @since 1.0.0
	 * @param object $product Product object.
	 * @return array
	 * */
	function lty_get_placed_lottery_product_ticket_ids( $product ) {
		if ( ! lty_is_lottery_product( $product ) ) {
			return array();
		}

		$args = array( 'product_id' => $product->get_id() );
		if ( $product->is_unlimited_scheduled_lottery() ) {
			$args['list_count'] = $product->get_current_relist_count();
		} else {
			$args['start_date'] = $product->get_current_start_date_gmt();
			$args['end_date']   = $product->get_lty_end_date_gmt();
		}

		return lty_get_ticket_ids( $args );
	}
}

if ( ! function_exists( 'lty_get_purchased_lottery_product_ticket_ids' ) ) {
	/**
	 * Get the purchased lottery product ticket ID's.
	 *
	 * @since 1.0.0
	 * @param object $product Product object.
	 * @param string $order_by Order by field.
	 * @param string $order Order direction (ASC or DESC).
	 * @return array
	 * */
	function lty_get_purchased_lottery_product_ticket_ids( $product, $order_by, $order ) {
		if ( ! lty_is_lottery_product( $product ) ) {
			return array();
		}

		$args = array(
			'product_id'  => $product->get_id(),
			'post_status' => lty_get_lottery_ticket_statuses(),
			'order_by'    => $order_by,
			'order'       => $order,
		);

		if ( $product->is_unlimited_scheduled_lottery() ) {
			$args['list_count'] = $product->get_current_relist_count();
		} else {
			$args['start_date'] = $product->get_current_start_date_gmt();
			$args['end_date']   = $product->get_lty_end_date_gmt();
		}

		return lty_get_ticket_ids( $args );
	}
}

if ( ! function_exists( 'lty_get_lottery_looser_ids' ) ) {
	/**
	 * Get the lottery looser ticket ids.
	 *
	 * @since 1.0.0
	 * @param object $product Product object.
	 * @return array
	 * */
	function lty_get_lottery_looser_ticket_ids( $product ) {
		$product = ! is_object( $product ) ? wc_get_product( $product ) : $product;
		if ( ! lty_is_lottery_product( $product ) ) {
			return array();
		}

		$args = array(
			'post_status' => 'lty_ticket_buyer',
			'product_id'  => $product->get_id(),
		);

		if ( $product->is_unlimited_scheduled_lottery() ) {
			$args['list_count'] = $product->get_current_relist_count();
		} else {
			$args['start_date'] = $product->get_current_start_date_gmt();
			$args['end_date']   = $product->get_lty_end_date_gmt();
		}

		return lty_get_ticket_ids( $args );
	}
}


if ( ! function_exists( 'lty_get_lottery_looser_tickets' ) ) {
	/**
	 * Get the lottery looser tickets.
	 *
	 * @deprecated since 11.7.0, use lty_get_lottery_looser_ticket_ids instead.
	 * @return array
	 * */
	function lty_get_lottery_looser_tickets( $user_id = false, $product_id = false, $from_date = false, $end_date = false, $post_status = false ) {
		wc_deprecated_function( 'lty_get_lottery_looser_tickets', '11.7.0', 'lty_get_lottery_looser_ticket_ids' );

		return lty_get_lottery_looser_ticket_ids( $product_id );
	}
}

if ( ! function_exists( 'lty_check_is_ticket_number_exists' ) ) {

	/**
	 * Check lottery ticket was already exist or not
	 *
	 * @return bool
	 * */
	function lty_check_is_ticket_number_exists( $ticket_numbers, $product_id, $start_date = false, $end_date = false, $list_count = false ) {
		if ( ! lty_check_is_array( $ticket_numbers ) || empty( $product_id ) ) {
			return array();
		}

		$product = wc_get_product( $product_id );
		if ( ! lty_is_lottery_product( $product ) ) {
			return array();
		}

		global $wpdb;

		$post_query = new LTY_Query( $wpdb->prefix . 'posts', 'p' );
		$post_query->innerJoin( $wpdb->prefix . 'postmeta', 'pm', '`p`.`ID` = `pm`.`post_id`' )
				->where( '`p`.post_type', LTY_Register_Post_Types::LOTTERY_TICKET_POSTTYPE )
				->whereIn( '`p`.post_status', lty_get_ticket_statuses() )
				->where( '`p`.post_parent', $product->get_lottery_id() )
				->where( '`pm`.meta_key', 'lty_ticket_number' )
				->whereIn( '`pm`.meta_value', $ticket_numbers );

		if ( false !== $list_count ) {
			$post_query->innerJoin( $wpdb->prefix . 'postmeta', 'pm1', '`p`.`ID` = `pm1`.`post_id`' )
				->where( 'pm1.meta_key', 'lty_list_count' )
				->where( 'pm1.meta_value', $list_count );
		} else {
			$start_date = $start_date ? $start_date : $product->get_current_start_date_gmt();
			$end_date   = $end_date ? $end_date : $product->get_lty_end_date_gmt();
			$post_query->whereBetween( '`p`.post_date_gmt', $start_date, $end_date );
		}

		$lost_ticket_ids = $post_query->fetchCol( '`p`.`ID`' );

		return lty_check_is_array( $lost_ticket_ids ) ? $lost_ticket_ids : array();
	}
}

if ( ! function_exists( 'lty_get_current_winner_user_ids' ) ) {
	/**
	 * Get current winner user ids.
	 *
	 * @since 1.0.0
	 * @param int $product_id Product ID.
	 * @global object $wpdb WordPress database object.
	 * @return array
	 * */
	function lty_get_current_winner_user_ids( $product_id ) {
		$product = wc_get_product( $product_id );
		if ( ! lty_is_lottery_product( $product ) ) {
			return array();
		}

		global $wpdb;
		$post_query = new LTY_Query( $wpdb->prefix . 'posts', 'p' );
		$post_query->innerJoin( $wpdb->prefix . 'postmeta', 'pm', '`p`.`ID` = `pm`.`post_id`' )
				->where( '`p`.post_type', LTY_Register_Post_Types::LOTTERY_WINNER_POSTTYPE )
				->whereIn( '`p`.post_status', array( 'lty_publish' ) )
				->where( '`p`.post_parent', $product->get_lottery_id() )
				->where( '`pm`.meta_key', 'lty_user_id' );

		if ( $product->is_unlimited_scheduled_lottery() ) {
			$post_query->leftJoin( $wpdb->prefix . 'postmeta', 'pm1', '`p`.`ID` = `pm1`.`post_id`' )
				->where( '`pm1`.meta_key', 'lty_list_count' )
				->where( '`pm1`.meta_value', $product->get_current_relist_count() );
		} else {
			$post_query->whereGt( '`p`.post_date_gmt', $product->get_current_start_date_gmt() );
		}

		return $post_query->fetchCol( 'DISTINCT `pm`.`meta_value`' );
	}
}

if ( ! function_exists( 'lty_get_current_winner_user_emails' ) ) {
	/**
	 * Get current winner user emails.
	 *
	 * @since 1.0.0
	 * @param int $product_id Product ID.
	 * @global object $wpdb WordPress database object.
	 * @return array
	 * */
	function lty_get_current_winner_user_emails( $product_id ) {
		$product = wc_get_product( $product_id );
		if ( ! lty_is_lottery_product( $product ) ) {
			return array();
		}

		global $wpdb;
		$post_query = new LTY_Query( $wpdb->prefix . 'posts', 'p' );
		$post_query->innerJoin( $wpdb->prefix . 'postmeta', 'pm', '`p`.`ID` = `pm`.`post_id`' )
				->where( '`p`.post_type', LTY_Register_Post_Types::LOTTERY_WINNER_POSTTYPE )
				->whereIn( '`p`.post_status', array( 'lty_publish' ) )
				->where( '`p`.post_parent', $product->get_lottery_id() )
				->where( '`pm`.meta_key', 'lty_user_email' );

		if ( $product->is_unlimited_scheduled_lottery() ) {
			$post_query->leftJoin( $wpdb->prefix . 'postmeta', 'pm1', '`p`.`ID` = `pm1`.`post_id`' )
				->where( '`pm1`.meta_key', 'lty_list_count' )
				->where( '`pm1`.meta_value', $product->get_current_relist_count() );
		} else {
			$post_query->whereGt( '`p`.post_date_gmt', $product->get_current_start_date_gmt() );
		}

		return $post_query->fetchCol( 'DISTINCT `pm`.`meta_value`' );
	}
}

if ( ! function_exists( 'lty_get_order_ids_without_tickets' ) ) {

	/**
	 * Get order ids without tickets.
	 *
	 * @return array
	 * */
	function lty_get_order_ids_without_tickets( $product, $order_status = false ) {
		if ( ! lty_is_lottery_product( $product ) ) {
			return array();
		}

		$selected_order_statuses = ! empty( get_option( 'lty_settings_lottery_complete_order_statuses' ) ) ? get_option( 'lty_settings_lottery_complete_order_statuses' ) : array( 'processing', 'completed' );
		$order_statuses          = array();
		foreach ( $selected_order_statuses as $status_key ) {
			$order_statuses[] = 'wc-' . $status_key;
		}

		if ( $order_status ) {
			$order_statuses = array( $order_status );
		}

		global $wpdb;
		$db               = &$wpdb;
		$meta_value_query = '3' != get_option( 'lty_settings_guest_user_participate_type' ) ? 'AND meta.meta_value > 0' : '';
		$order_ids        = $db->get_col(
			$db->prepare(
				"SELECT DISTINCT posts.ID
                                    FROM $db->posts as posts
                                    LEFT JOIN {$db->postmeta} AS meta ON posts.ID = meta.post_id
                                    LEFT JOIN {$db->postmeta} AS meta1 ON posts.ID = meta1.post_id AND meta1.meta_key='lty_ticket_ids_in_order'
                                    LEFT JOIN {$db->prefix}woocommerce_order_items AS order_items ON posts.ID = order_items.order_id
                                    LEFT JOIN {$db->prefix}woocommerce_order_itemmeta AS order_itemmeta ON order_items.order_item_id = order_itemmeta.order_item_id    
                                    WHERE posts.post_type           = 'shop_order'
                                    AND   posts.post_status IN ('" . implode( "','", $order_statuses ) . "')
                                    AND   meta.meta_key             = '_customer_user'    
                                    $meta_value_query
                                    AND   order_itemmeta.meta_key   = '_product_id'
                                    AND   order_itemmeta.meta_value = %d
                                    AND   posts.post_date_gmt BETWEEN %s AND %s
                                    AND   meta1.meta_key is null ORDER BY posts.ID DESC",
				$product->get_id(),
				$product->get_current_start_date_gmt(),
				$product->get_lty_end_date_gmt()
			)
		);

		return $order_ids;
	}
}

if ( ! function_exists( 'lty_get_ticket_id_by_ticket_number' ) ) {

	/**
	 * Get the ticket ID by ticket number.
	 *
	 * @param type $ticket_number
	 */
	function lty_get_ticket_id_by_ticket_number( $ticket_number, $product_id = false ) {
		$args = array(
			'post_type'      => LTY_Register_Post_Types::LOTTERY_TICKET_POSTTYPE,
			'post_status'    => lty_get_lottery_ticket_statuses(),
			'posts_per_page' => '1',
			'fields'         => 'ids',
			'meta_key'       => 'lty_ticket_number',
			'meta_value'     => $ticket_number,
		);

		if ( $product_id ) {
			$args['post_parent'] = $product_id;
		}

		$ticket_id = get_posts( $args );
		if ( ! lty_check_is_array( $ticket_id ) ) {
			return $ticket_id;
		}

		return reset( $ticket_id );
	}
}

if ( ! function_exists( 'lty_get_lottery_winning_dates' ) ) {

	/**
	 * Get the lottery winning dates.
	 *
	 * @since 8.0.0
	 * @global object $wpdb
	 * @param string $order
	 * @param string $start_date
	 * @return array
	 */
	function lty_get_lottery_winning_dates( $order, $start_date = false ) {
		global $wpdb;

		$post_query = new LTY_Query( $wpdb->prefix . 'posts', 'p' );
		$post_query->where( '`p`.post_type', LTY_Register_Post_Types::LOTTERY_WINNER_POSTTYPE )
				->whereIn( '`p`.post_status', array( 'lty_publish' ) )
				->orderBy( 'DATE(`p`.post_date_gmt)', $order )
				->groupBy( 'DATE(`p`.post_date_gmt)' );

		if ( $start_date ) {
			$post_query->whereGte( 'DATE(`p`.post_date_gmt)', $start_date );
		}

		return $post_query->fetchCol( 'DATE(`p`.post_date_gmt) as date' );
	}
}

if ( ! function_exists( 'lty_get_lottery_instant_winning_dates' ) ) {

	/**
	 * Get the lottery instant winning dates.
	 *
	 * @since 9.2.0
	 * @global object $wpdb WordPress database.
	 * @param string $order Whether to order ASC|DESC.
	 * @param string $start_date Start date.
	 * @return array
	 */
	function lty_get_lottery_instant_winning_dates( $order, $start_date = false ) {
		global $wpdb;

		$post_query = new LTY_Query( $wpdb->prefix . 'posts', 'p' );
		$post_query->where( '`p`.post_type', LTY_Register_Post_Types::LOTTERY_INSTANT_WINNER_LOG_POSTTYPE )
				->whereIn( '`p`.post_status', array( 'lty_won' ) )
				->orderBy( 'DATE(`p`.post_date_gmt)', $order )
				->groupBy( 'DATE(`p`.post_date_gmt)' );

		if ( $start_date ) {
			$post_query->whereGte( 'DATE(`p`.post_date_gmt)', $start_date );
		}

		return $post_query->fetchCol( 'DATE(`p`.post_date_gmt) as date' );
	}
}

if ( ! function_exists( 'lty_get_instant_winner_rule_statuses' ) ) {

	/**
	 * Get lottery instant winner rule statuses.
	 *
	 * @since 8.0.0
	 * @return array
	 * */
	function lty_get_instant_winner_rule_statuses() {
		/**
		 * This hook is used to alter the instant winner rule statuses.
		 *
		 * @since 8.0.0
		 */
		return apply_filters( 'lty_get_instant_winner_rule_statuses', array( 'publish' ) );
	}
}

if ( ! function_exists( 'lty_get_instant_winner_log_statuses' ) ) {

	/**
	 * Get lottery instant winner log statuses.
	 *
	 * @since 8.0.0
	 * @return array
	 * */
	function lty_get_instant_winner_log_statuses() {
		/**
		 * This hook is used to alter the instant winner log statuses.
		 *
		 * @since 8.0.0
		 */
		return apply_filters( 'lty_get_instant_winner_log_statuses', array( 'lty_available', 'lty_pending', 'lty_won' ) );
	}
}

if ( ! function_exists( 'lty_create_new_instant_winner_rule' ) ) {

	/**
	 * Create New Instant Winner Rule.
	 *
	 * @since 8.0.0
	 * @return integer/string
	 * */
	function lty_create_new_instant_winner_rule( $meta_args, $post_args = array() ) {
		$object = new LTY_Instant_Winner_Rule();
		$id     = $object->create( $meta_args, $post_args );
		return $id;
	}
}

if ( ! function_exists( 'lty_update_instant_winner_rule' ) ) {

	/**
	 * Update Instant Winner rule.
	 *
	 * @since 8.0.0
	 * @return object
	 * */
	function lty_update_instant_winner_rule( $id, $meta_args, $post_args = array() ) {
		$object = new LTY_Instant_Winner_Rule( $id );
		$object->update( $meta_args, $post_args );

		return $object;
	}
}

if ( ! function_exists( 'lty_delete_instant_winner_rule' ) ) {

	/**
	 * Delete Instant Winner rule.
	 *
	 * @since 8.0.0
	 * @return bool
	 * */
	function lty_delete_instant_winner_rule( $id, $force = true ) {
		wp_delete_post( $id, $force );
		return true;
	}
}

if ( ! function_exists( 'lty_get_instant_winner_rule' ) ) {

	/**
	 * Get Instant Winner rule object.
	 *
	 * @since 8.0.0
	 * @return object
	 * */
	function lty_get_instant_winner_rule( $id ) {
		$object = new LTY_Instant_Winner_Rule( $id );
		return $object;
	}
}

if ( ! function_exists( 'lty_get_rule_id_by_ticket_number' ) ) {

	/**
	 * Get rule id by ticket number.
	 *
	 * @since 8.0.0
	 * @return string/bool
	 * */
	function lty_get_rule_id_by_ticket_number( $lottery_id, $ticket_number ) {
		$rule_ids = get_posts(
			array(
				'post_type'      => LTY_Register_Post_Types::LOTTERY_INSTANT_WINNER_RULE_POSTTYPE,
				'post_status'    => lty_get_instant_winner_rule_statuses(),
				'posts_per_page' => -1,
				'fields'         => 'ids',
				'meta_key'       => 'lty_ticket_number',
				'meta_value'     => $ticket_number,
				'post_parent'    => $lottery_id,
			)
		);

		return lty_check_is_array( $rule_ids ) ? reset( $rule_ids ) : false;
	}
}

if ( ! function_exists( 'lty_get_instant_winner_rule_ids' ) ) {

	/**
	 * Get instant winner rule ids.
	 *
	 * @since 8.0.0
	 * @return array
	 * */
	function lty_get_instant_winner_rule_ids( $lottery_id, $offset = 0, $limit = -1 ) {
		if ( ! $lottery_id ) {
			return array();
		}

		return get_posts(
			array(
				'post_type'      => LTY_Register_Post_Types::LOTTERY_INSTANT_WINNER_RULE_POSTTYPE,
				'post_status'    => lty_get_instant_winner_rule_statuses(),
				'posts_per_page' => $limit,
				'fields'         => 'ids',
				'orderby'        => 'ID',
				'order'          => 'ASC',
				'post_parent'    => $lottery_id,
				'offset'         => $offset,
			)
		);
	}
}

if ( ! function_exists( 'lty_get_ticket_id_by_rule_id' ) ) {

	/**
	 * Get ticket id by rule id.
	 *
	 * @since 8.0.0
	 * @return string/bool
	 * */
	function lty_get_ticket_id_by_rule_id( $rule_id ) {
		$rule_ids = get_posts(
			array(
				'post_type'      => LTY_Register_Post_Types::LOTTERY_TICKET_POSTTYPE,
				'post_status'    => array_merge( array( 'lty_ticket_pending' ), (array) lty_get_lottery_ticket_statuses() ),
				'posts_per_page' => -1,
				'fields'         => 'ids',
				'meta_key'       => 'lty_rule_id',
				'meta_value'     => $rule_id,
			)
		);

		return lty_check_is_array( $rule_ids ) ? reset( $rule_ids ) : false;
	}
}

if ( ! function_exists( 'lty_create_new_instant_winner_log' ) ) {

	/**
	 * Create new instant winner log.
	 *
	 * @since 8.0.0
	 * @return integer/string
	 * */
	function lty_create_new_instant_winner_log( $meta_args, $post_args = array() ) {
		$object = new LTY_Instant_Winner_Log();
		$id     = $object->create( $meta_args, $post_args );
		return $id;
	}
}

if ( ! function_exists( 'lty_update_instant_winner_log' ) ) {

	/**
	 * Update instant winner log.
	 *
	 * @since 8.0.0
	 * @return object
	 * */
	function lty_update_instant_winner_log( $id, $meta_args, $post_args = array() ) {
		$object = new LTY_Instant_Winner_Log( $id );
		$object->update( $meta_args, $post_args );

		return $object;
	}
}

if ( ! function_exists( 'lty_delete_instant_winner_log' ) ) {

	/**
	 * Delete instant winner log.
	 *
	 * @since 8.0.0
	 * @return bool
	 * */
	function lty_delete_instant_winner_log( $id, $force = true ) {
		wp_delete_post( $id, $force );
		return true;
	}
}

if ( ! function_exists( 'lty_get_instant_winner_log' ) ) {

	/**
	 * Get instant winner log.
	 *
	 * @since 8.0.0
	 * @return array
	 * */
	function lty_get_instant_winner_log( $log_id ) {
		$object = new LTY_Instant_Winner_Log( $log_id );
		return $object;
	}
}

if ( ! function_exists( 'lty_get_instant_winner_log_ids' ) ) {

	/**
	 * Get the instant winner log ids.
	 *
	 * @since 8.0.0
	 * @param int          $lottery_id
	 * @param int          $rule_id
	 * @param int          $list_count
	 * @param array/string $statuses
	 * @return array
	 */
	function lty_get_instant_winner_log_ids( $lottery_id, $rule_id = false, $list_count = false, $statuses = 'all' ) {
		if ( ! $lottery_id ) {
			return array();
		}

		if ( 'all' === $statuses ) {
			$statuses = lty_get_instant_winner_log_statuses();
		}

		$args = array(
			'post_type'      => LTY_Register_Post_Types::LOTTERY_INSTANT_WINNER_LOG_POSTTYPE,
			'post_status'    => $statuses,
			'posts_per_page' => -1,
			'fields'         => 'ids',
			'orderby'        => 'ID',
			'order'          => 'ASC',
			'meta_query'     => array(
				'relation' => 'AND',
				array(
					'key'   => 'lty_lottery_id',
					'value' => $lottery_id,
				),
			),
		);

		if ( false !== $list_count ) {
			$args['meta_query'][] = array(
				'key'   => 'lty_current_relist_count',
				'value' => $list_count,
			);
		}

		if ( $rule_id ) {
			$args['post_parent'] = $rule_id;
		}

		$log_ids = get_posts( $args );

		return lty_check_is_array( $log_ids ) ? $log_ids : array();
	}
}

if ( ! function_exists( 'lty_get_instant_winner_statuses_count' ) ) {

	/**
	 * Get the instant winner statuses count.
	 *
	 * @since 11.0.0
	 * @param int $lottery_id
	 * @return array
	 */
	function lty_get_instant_winner_statuses_count( $lottery_id ) {
		if ( ! $lottery_id ) {
			return array();
		}

		global $wpdb;

		$post_query = new LTY_Query( $wpdb->prefix . 'posts', 'p' );
		$post_query->select( 'DISTINCT `p`.post_status, COUNT(`p`.ID) AS status_count' )
			->leftJoin( $wpdb->prefix . 'postmeta', 'pm', '`p`.`ID` = `pm`.`post_id`' )
			->where( '`p`.post_type', LTY_Register_Post_Types::LOTTERY_INSTANT_WINNER_LOG_POSTTYPE )
			->whereIn( '`p`.post_status', lty_get_instant_winner_log_statuses() )
			->where( '`pm`.meta_key', 'lty_lottery_id' )
			->where( '`pm`.meta_value', $lottery_id )
			->groupBy( '`p`.post_status' );

		$results = $post_query->fetchArray();

		return lty_check_is_array( $results ) ? array_column( $results, 'status_count', 'post_status' ) : array();
	}
}

if ( ! function_exists( 'lty_get_instant_winner_log_ids_by_user_id' ) ) {

	/**
	 * Get the instant winner log ids by user ID.
	 *
	 * @since 9.8.0
	 * @param int $user_id User ID.
	 * @return array
	 */
	function lty_get_instant_winner_log_ids_by_user_id( $user_id ) {
		if ( ! $user_id ) {
			return array();
		}

		return get_posts(
			array(
				'post_type'      => LTY_Register_Post_Types::LOTTERY_INSTANT_WINNER_LOG_POSTTYPE,
				'post_status'    => lty_get_instant_winner_log_statuses(),
				'posts_per_page' => -1,
				'fields'         => 'ids',
				'meta_key'       => 'lty_user_id',
				'meta_value'     => $user_id,
			)
		);
	}
}

if ( ! function_exists( 'lty_get_group_instant_winner_logs_data' ) ) {
	/**
	 * Get the group instant winner logs data.
	 *
	 * @since 11.1.0
	 * @param int          $product_id Product ID.
	 * @param bool         $list_count List count.
	 * @param array|string $statuses Statuses.
	 * @return array
	 */
	function lty_get_group_instant_winner_logs_data( $product_id, $list_count = false, $statuses = 'all' ) {
		global $wpdb;
		if ( 'all' === $statuses ) {
			$statuses = lty_get_instant_winner_log_statuses();
		}

		$post_query = new LTY_Query( $wpdb->prefix . 'posts', 'p' );
		$post_query->select( 'pm2.meta_value AS group_id, GROUP_CONCAT(p.ID) AS log_ids' )
			->leftJoin( $wpdb->prefix . 'postmeta', 'pm1', 'p.ID = pm1.post_id' )
			->innerJoin( $wpdb->prefix . 'postmeta', 'pm2', 'p.ID = pm2.post_id AND pm2.meta_key = "lty_prize_group_id"' )
			->where( 'p.post_type', LTY_Register_Post_Types::LOTTERY_INSTANT_WINNER_LOG_POSTTYPE )
			->whereIn( 'p.post_status', $statuses )
			->where( 'pm1.meta_key', 'lty_lottery_id' )
			->where( 'pm1.meta_value', $product_id )
			->groupBy( 'pm2.meta_value' );

		if ( ! empty( $list_count ) ) {
			$post_query->leftJoin( $wpdb->prefix . 'postmeta', 'pm3', 'p.ID = pm3.post_id' )
				->where( 'pm3.meta_key', 'lty_current_relist_count' )
				->where( 'pm3.meta_value', $list_count );
		}

		return $post_query->fetchArray();
	}
}

if ( ! function_exists( 'lty_get_group_instant_winner_logs_status_count' ) ) {
	/**
	 * Get the group instant winner logs data.
	 *
	 * @since 11.1.0
	 * @param int  $prize_group_id Prize Group ID.
	 * @param bool $list_count List count.
	 * @return array
	 */
	function lty_get_group_instant_winner_logs_status_count( $prize_group_id, $list_count = false ) {
		global $wpdb;

		$statuses = lty_get_instant_winner_log_statuses();

		$post_query = new LTY_Query( $wpdb->prefix . 'posts', 'p' );
		$post_query->select( 'p.post_status, COUNT(p.post_status) AS count' )
			->leftJoin( $wpdb->prefix . 'postmeta', 'pm1', 'p.ID = pm1.post_id' )
			->where( 'p.post_type', LTY_Register_Post_Types::LOTTERY_INSTANT_WINNER_LOG_POSTTYPE )
			->whereIn( 'p.post_status', $statuses )
			->where( 'pm1.meta_key', 'lty_prize_group_id' )
			->where( 'pm1.meta_value', $prize_group_id )
			->groupBy( 'p.post_status' );

		if ( ! empty( $list_count ) ) {
			$post_query->leftJoin( $wpdb->prefix . 'postmeta', 'pm2', 'p.ID = pm2.post_id' )
				->where( 'pm2.meta_key', 'lty_current_relist_count' )
				->where( 'pm2.meta_value', $list_count );
		}

		$status_counts = $post_query->fetchArray();

		return lty_check_is_array( $status_counts ) ? array_column( $status_counts, 'count', 'post_status' ) : array();
	}
}

if ( ! function_exists( 'lty_get_lottery_winner_ids_by_date' ) ) {

	/**
	 * Get the lottery winner IDs by date.
	 *
	 * @since 8.0.0
	 * @global object $wpdb
	 * @param string $date
	 * @return array
	 */
	function lty_get_lottery_winner_ids_by_date( $date ) {
		global $wpdb;

		$post_query = new LTY_Query( $wpdb->prefix . 'posts', 'p' );
		$post_query->where( '`p`.post_type', LTY_Register_Post_Types::LOTTERY_WINNER_POSTTYPE )
				->whereIn( '`p`.post_status', array( 'lty_publish' ) )
				->where( 'DATE(`p`.post_date_gmt)', $date );

		return $post_query->fetchCol( '`p`.ID' );
	}
}

if ( ! function_exists( 'lty_get_lottery_instant_winner_log_ids_by_date' ) ) {

	/**
	 * Get the lottery instant winner log IDs by date.
	 *
	 * @since 8.0.0
	 * @global object $wpdb WordPress database.
	 * @param string $date Date of the lottery instant winner.
	 * @return array
	 */
	function lty_get_lottery_instant_winner_log_ids_by_date( $date ) {
		global $wpdb;

		$post_query = new LTY_Query( $wpdb->prefix . 'posts', 'p' );
		$post_query->where( '`p`.post_type', LTY_Register_Post_Types::LOTTERY_INSTANT_WINNER_LOG_POSTTYPE )
				->whereIn( '`p`.post_status', array( 'lty_won' ) )
				->where( 'DATE(`p`.post_date_gmt)', $date );

		return $post_query->fetchCol( '`p`.ID' );
	}
}

if ( ! function_exists( 'lty_get_instant_winner_log_id_by_rule_id' ) ) {

	/**
	 * Get instant winner log id by rule id.
	 *
	 * @since 8.0.0
	 * @param string $rule_id
	 * @param string $list_count
	 * @return string
	 * */
	function lty_get_instant_winner_log_id_by_rule_id( $rule_id, $list_count ) {
		if ( ! $rule_id ) {
			return false;
		}

		$log_ids = get_posts(
			array(
				'post_type'      => LTY_Register_Post_Types::LOTTERY_INSTANT_WINNER_LOG_POSTTYPE,
				'post_status'    => lty_get_instant_winner_log_statuses(),
				'posts_per_page' => -1,
				'fields'         => 'ids',
				'orderby'        => 'ID',
				'post_parent'    => $rule_id,
				'meta_key'       => 'lty_current_relist_count',
				'meta_value'     => $list_count,
			)
		);

		return lty_check_is_array( $log_ids ) ? reset( $log_ids ) : false;
	}
}

if ( ! function_exists( 'lty_get_instant_winner_log_id_by_ticket_id' ) ) {

	/**
	 * Get instant winner log id by ticket id.
	 *
	 * @since 8.0.0
	 * @param string $ticket_id
	 * @return string
	 * */
	function lty_get_instant_winner_log_id_by_ticket_id( $ticket_id ) {
		if ( ! $ticket_id ) {
			return false;
		}

		$log_ids = get_posts(
			array(
				'post_type'      => LTY_Register_Post_Types::LOTTERY_INSTANT_WINNER_LOG_POSTTYPE,
				'post_status'    => lty_get_instant_winner_log_statuses(),
				'posts_per_page' => -1,
				'fields'         => 'ids',
				'orderby'        => 'ID',
				'meta_key'       => 'lty_ticket_id',
				'meta_value'     => $ticket_id,
			)
		);

		return lty_check_is_array( $log_ids ) ? reset( $log_ids ) : false;
	}
}

if ( ! function_exists( 'lty_get_lottery_ids' ) ) {

	/**
	 * Get the Lottery IDs.
	 *
	 * @since 8.5.0
	 * @return array
	 * */
	function lty_get_lottery_ids() {
		static $lottery_ids;
		if ( isset( $lottery_ids ) ) {
			return $lottery_ids;
		}

		$lottery_ids = get_posts(
			array(
				'post_type'      => 'product',
				'post_status'    => array( 'publish', 'private' ),
				'posts_per_page' => '-1',
				'fields'         => 'ids',
				'tax_query'      => array(
					array(
						'taxonomy' => 'product_type',
						'field'    => 'name',
						'terms'    => 'lottery',
					),
				),
			)
		);

		return lty_check_is_array( $lottery_ids ) ? $lottery_ids : array();
	}

}

if ( ! function_exists( 'lty_get_purchased_tickets_ids_on_ticket_logs_tab' ) ) {

	/**
	 * Get the purchased tickets ids on ticket logs tab
	 *
	 * @since 1.0.0
	 * @param object $product Product object.
	 * @param string $search Search term.
	 * @return array
	 */
	function lty_get_purchased_tickets_ids_on_ticket_logs_tab( $product, $search = false ) {
		if ( ! lty_is_lottery_product( $product ) ) {
			return array();
		}

		$is_alphanumeric = false;
		if ( '1' == $product->get_lty_ticket_generation_type() && $product->is_automatic_random_ticket() ) {
			$is_alphanumeric = ! empty( get_option( 'lty_settings_ticket_prefix' ) ) || ! empty( get_option( 'lty_settings_ticket_suffix' ) );
			$is_alphanumeric = ( '2' == get_option( 'lty_settings_generate_ticket_type' ) ) ? true : $is_alphanumeric;
		} else {
			$is_alphanumeric = ! empty( $product->get_lty_ticket_prefix() ) || ! empty( $product->get_lty_ticket_suffix() );
		}

		$key      = $is_alphanumeric ? 'meta_value meta_value_num' : 'meta_value_num';
		$order_by = '1' == get_option( 'lty_settings_single_product_tab_details_order_by', 1 ) ? 'ID' : $key;
		$order    = '1' == get_option( 'lty_settings_single_product_tab_details_order', 1 ) ? 'DESC' : 'ASC';

		$ticket_ids = $product->get_purchased_ticket_ids( $order_by, $order );

		// Search.
		if ( $search ) {
			global $wpdb;
			$searched_ids = array();
			$terms        = explode( ' , ', $search );
			$database     = &$wpdb;

			foreach ( $terms as $term ) {
				$term       = $database->esc_like( $term );
				$post_query = new LTY_Query( $database->prefix . 'posts', 'p' );
				$post_query->select( 'DISTINCT `p`.ID' )
						->leftJoin( $database->prefix . 'postmeta', 'pm', '`p`.`ID` = `pm`.`post_id`' )
						->where( '`p`.post_type', LTY_Register_Post_Types::LOTTERY_TICKET_POSTTYPE )
						->whereIn( '`p`.post_status', lty_get_ticket_statuses() )
						->where( '`p`.post_parent', $product->get_id() )
						->whereIn( '`pm`.meta_key', lty_get_ticket_logs_search_columns() )
						->whereLike( '`pm`.meta_value', lty_get_formatted_ticket_logs_search_term( $term ) );

				$ids = $post_query->fetchCol( 'ID' );

				$searched_ids = array_merge( $searched_ids, $ids );
			}

			$ticket_ids = array_intersect( $ticket_ids, array_filter( array_unique( $searched_ids ) ) );
		}

		return $ticket_ids;
	}
}

if ( ! function_exists( 'lty_get_lottery_ticket_ids_by_order_id' ) ) {

	/**
	 * Get the lottery ticket IDs by order ID.
	 *
	 * @since 9.5.0
	 * @param int $order_id Order ID.
	 * @param int $product_id Product ID.
	 * @return array
	 * */
	function lty_get_lottery_ticket_ids_by_order_id( $order_id, $product_id ) {
		if ( ! $product_id || ! $order_id ) {
			return array();
		}

		return get_posts(
			array(
				'post_type'      => LTY_Register_Post_Types::LOTTERY_TICKET_POSTTYPE,
				'post_status'    => lty_get_ticket_statuses(),
				'posts_per_page' => '-1',
				'fields'         => 'ids',
				'orderby'        => 'ID',
				'post_parent'    => $product_id,
				'meta_key'       => 'lty_order_id',
				'meta_value'     => $order_id,
			)
		);
	}
}

if ( ! function_exists( 'lty_get_instant_winner_log_ids_by_order_id' ) ) {

	/**
	 * Get the instant winner log IDs by order ID.
	 *
	 * @since 10.9.0
	 * @param int          $order_id Order ID.
	 * @param int          $product_id Product ID.
	 * @param string|array $status Post status.
	 * @return array
	 * */
	function lty_get_instant_winner_log_ids_by_order_id( $order_id, $product_id = false, $status = 'all' ) {
		if ( 'all' === $status ) {
			$status = lty_get_instant_winner_log_statuses();
		}

		$args = array(
			'post_type'      => LTY_Register_Post_Types::LOTTERY_INSTANT_WINNER_LOG_POSTTYPE,
			'post_status'    => $status,
			'posts_per_page' => -1,
			'fields'         => 'ids',
			'orderby'        => 'ID',
			'order'          => 'ASC',
			'meta_query'     => array(
				'relation' => 'AND',
				array(
					'key'   => 'lty_order_id',
					'value' => $order_id,
				),
			),
		);

		if ( $product_id ) {
			$args['meta_query'][] = array(
				'key'   => 'lty_lottery_id',
				'value' => $product_id,
			);
		}

		return get_posts( $args );
	}
}

if ( ! function_exists( 'lty_get_instant_winner_rule_ticket_numbers' ) ) {
	/**
	 * Get the instant winner rule ticket numbers.
	 *
	 * @since 10.9.0
	 * @param  int $lottery_id Lottery product ID.
	 * @global object $wpdb WordPress database.
	 * @return array
	 * */
	function lty_get_instant_winner_rule_ticket_numbers( $lottery_id ) {
		global $wpdb;

		$post_query = new LTY_Query( $wpdb->prefix . 'posts', 'p' );
		$post_query->innerJoin( $wpdb->prefix . 'postmeta', 'pm', '`p`.`ID` = `pm`.`post_id`' )
				->where( '`p`.post_type', LTY_Register_Post_Types::LOTTERY_INSTANT_WINNER_RULE_POSTTYPE )
				->whereIn( '`p`.post_status', lty_get_instant_winner_rule_statuses() )
				->where( '`p`.post_parent', $lottery_id )
				->where( '`pm`.meta_key', 'lty_ticket_number' );

		return $post_query->fetchCol( '`pm`.`meta_value`' );
	}
}

if ( ! function_exists( 'lty_get_instant_winner_prize_group_ids' ) ) {
	/**
	 * Get instant winner prize group ids.
	 *
	 * @since 11.1.0
	 * @param int $product_id Product ID.
	 * @return array
	 */
	function lty_get_instant_winner_prize_group_ids( $product_id ) {
		return get_posts(
			array(
				'post_type'      => LTY_Register_Post_Types::LOTTERY_INSTANT_WINNER_PRIZE_GROUP_POST_TYPE,
				'post_status'    => lty_get_instant_winner_prize_group_statuses(),
				'posts_per_page' => -1,
				'fields'         => 'ids',
				'orderby'        => 'ID',
				'order'          => 'ASC',
				'post_parent'    => $product_id,
			)
		);
	}
}

if ( ! function_exists( 'lty_get_instant_winner_prize_group_options' ) ) {
	/**
	 * Get instant winner prize group options.
	 *
	 * @since 11.1.0
	 * @param int $product_id Product ID.
	 * @static array $prize_group_options Instant winner prize group options.
	 * @return array
	 */
	function lty_get_instant_winner_prize_group_options( $product_id ) {
		static $prize_group_options;
		if ( isset( $prize_group_options ) ) {
			return $prize_group_options;
		}

		global $wpdb;

		$post_query = new LTY_Query( $wpdb->prefix . 'posts', 'p' );
		$post_query->select( 'p.ID AS prize_group_id, p.post_title AS title' )
			->where( 'p.post_type', LTY_Register_Post_Types::LOTTERY_INSTANT_WINNER_PRIZE_GROUP_POST_TYPE )
			->whereIn( 'p.post_status', lty_get_instant_winner_prize_group_statuses() )
			->where( 'p.post_parent', $product_id );

		$prize_group_options = $post_query->fetchArray();

		return $prize_group_options;
	}
}

if ( ! function_exists( 'lty_get_instant_winner_prize_group_id_by_title' ) ) {
	/**
	 * Get instant winner prize group ID by prize group title.
	 *
	 * @since 11.1.0
	 * @param string $prize_group_title Prize group title.
	 * @param int    $product_id Product ID.
	 * @return array
	 */
	function lty_get_instant_winner_prize_group_id_by_title( $prize_group_title, $product_id ) {
		global $wpdb;

		$post_query = new LTY_Query( $wpdb->prefix . 'posts', 'p' );
		$post_query->select( 'DISTINCT p.ID' )
			->where( 'p.post_type', LTY_Register_Post_Types::LOTTERY_INSTANT_WINNER_PRIZE_GROUP_POST_TYPE )
			->whereIn( 'p.post_status', lty_get_instant_winner_prize_group_statuses() )
			->where( 'p.post_parent', $product_id )
			->where( 'p.post_title', $prize_group_title );

		$prize_group_id = $post_query->fetchCol( 'ID' );

		return lty_check_is_array( $prize_group_id ) ? reset( $prize_group_id ) : false;
	}
}

if ( ! function_exists( 'lty_get_purchased_tickets_count_by_product_id' ) ) {
	/**
	 * Get the lottery purchased tickets count by product ID.
	 *
	 * @since 11.2.0
	 * @param int|object $product_id Product ID or object.
	 * @global object $wpdb WordPress database.
	 * @return int
	 */
	function lty_get_purchased_tickets_count_by_product_id( $product_id ) {
		$product = ! is_object( $product_id ) ? wc_get_product( $product_id ) : $product_id;
		if ( ! lty_is_lottery_product( $product ) ) {
			return 0;
		}

		$ticket_count = get_transient( 'lty_purchased_ticket_count_' . $product->get_id() );
		if ( false !== $ticket_count ) {
			return $ticket_count;
		}

		global $wpdb;

		$post_query = new LTY_Query( $wpdb->prefix . 'posts', 'p' );
		$post_query->leftJoin( $wpdb->prefix . 'postmeta', 'pm', 'p.ID = pm.post_id' )
				->where( 'p.post_type', LTY_Register_Post_Types::LOTTERY_TICKET_POSTTYPE )
				->whereIn( 'p.post_status', lty_get_lottery_ticket_statuses() )
				->where( 'p.post_parent', $product->get_lottery_id() )
				->where( 'pm.meta_key', 'lty_ticket_number' );

		if ( $product->is_unlimited_scheduled_lottery() ) {
			$post_query->leftJoin( $wpdb->prefix . 'postmeta', 'pm1', 'p.ID = pm1.post_id' )
				->where( 'pm1.meta_key', 'lty_list_count' )
				->where( 'pm1.meta_value', $product->get_current_relist_count() );
		} else {
			$post_query->whereBetween( 'p.post_date_gmt', $product->get_current_start_date_gmt(), $product->get_lty_end_date_gmt() );
		}

		$ticket_count = $post_query->count();
		set_transient( 'lty_purchased_ticket_count_' . $product->get_id(), $ticket_count, HOUR_IN_SECONDS );

		return $ticket_count;
	}
}

if ( ! function_exists( 'lty_get_instant_winner_log_ids_by_group_id' ) ) {
	/**
	 * Get the instant winner log IDs by group ID.
	 *
	 * @since 11.9.0
	 * @param int          $group_id Group ID.
	 * @param int          $list_count List count.
	 * @param string|array $statuses Post statuses.
	 * @return array
	 */
	function lty_get_instant_winner_log_ids_by_group_id( $group_id, $list_count = false, $statuses = 'all' ) {
		$instant_winner_log_ids = get_transient( "lty_group_current_instant_winner_log_ids_{$group_id}" );
		if ( false !== $instant_winner_log_ids ) {
			return $instant_winner_log_ids;
		}

		global $wpdb;
		if ( 'all' === $statuses ) {
			$statuses = lty_get_instant_winner_log_statuses();
		}

		$post_query = new LTY_Query( $wpdb->prefix . 'posts', 'p' );
		$post_query->select( 'DISTINCT `p`.ID' )
			->leftJoin( $wpdb->prefix . 'postmeta', 'pm1', 'p.ID = pm1.post_id' )
			->where( 'p.post_type', LTY_Register_Post_Types::LOTTERY_INSTANT_WINNER_LOG_POSTTYPE )
			->whereIn( 'p.post_status', $statuses )
			->where( 'pm1.meta_key', 'lty_prize_group_id' )
			->where( 'pm1.meta_value', $group_id );

		if ( false !== $list_count ) {
			$post_query->leftJoin( $wpdb->prefix . 'postmeta', 'pm2', 'p.ID = pm2.post_id' )
				->where( 'pm2.meta_key', 'lty_current_relist_count' )
				->where( 'pm2.meta_value', $list_count );
		}

		$instant_winner_log_ids = $post_query->fetchCol( 'ID' );
		set_transient( "lty_group_current_instant_winner_log_ids_{$group_id}", $instant_winner_log_ids, HOUR_IN_SECONDS );

		return $instant_winner_log_ids;
	}
}

if ( ! function_exists( 'lty_get_instant_winner_rule_ids_by_group_id' ) ) {

	/**
	 * Get instant winner rule ID's by group ID.
	 *
	 * @since 12.2.0
	 * @param int $group_id Group ID.
	 * @param int $product_id Product ID.
	 * @return array
	 * */
	function lty_get_instant_winner_rule_ids_by_group_id( $group_id, $product_id ) {
		return get_posts(
			array(
				'post_type'      => LTY_Register_Post_Types::LOTTERY_INSTANT_WINNER_RULE_POSTTYPE,
				'post_status'    => lty_get_instant_winner_rule_statuses(),
				'posts_per_page' => -1,
				'fields'         => 'ids',
				'meta_key'       => 'lty_prize_group_id',
				'meta_value'     => $group_id,
				'post_parent'    => $product_id,
			)
		);
	}
}
