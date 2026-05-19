<?php
/**
 * Lottery Ticket Post Table.
 *
 * @since 1.0.0
 * */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

if ( ! class_exists( 'LTY_Lottery_Ticket_List_Table' ) ) {

	/**
	 * LTY_Lottery_Ticket_List_Table Class.
	 * */
	class LTY_Lottery_Ticket_List_Table extends WP_List_Table {

		/**
		 * Per page count.
		 *
		 * @var int
		 * */
		private $perpage = 10;

		/**
		 * Database.
		 *
		 * @var object
		 * */
		private $database;

		/**
		 * Offset.
		 *
		 * @var int
		 * */
		private $offset;

		/**
		 * Order BY.
		 *
		 * @var string
		 * */
		private $orderby = 'ORDER BY ID DESC';

		/**
		 * Post type.
		 *
		 * @var string
		 * */
		private $post_type = LTY_Register_Post_Types::LOTTERY_TICKET_POSTTYPE;

		/**
		 * List Slug.
		 *
		 * @var string
		 * */
		private $list_slug = 'lty_tickets';

		/**
		 * Base URL.
		 *
		 * @var string
		 * */
		private $base_url;

		/**
		 * Current URL.
		 *
		 * @var string
		 * */
		private $current_url;

		/**
		 * Product ID.
		 *
		 * @var string|int
		 * */
		private $product_id;

		/**
		 * Product.
		 *
		 * @var object
		 * */
		private $product;

		/**
		 * List count.
		 *
		 * @since 11.7.0
		 * @var int|bool
		 * */
		private $list_count = false;

		/**
		 * From date.
		 *
		 * @since 11.7.0
		 * @var string
		 * */
		private $from_date;

		/**
		 * To date.
		 *
		 * @since 11.7.0
		 * @var string
		 * */
		private $to_date;

		/**
		 * Constructor.
		 */
		public function __construct() {
			global $wpdb, $lottery_id, $lty_product, $current_tab, $current_section;

			$this->database   = &$wpdb;
			$this->product_id = $lottery_id;
			$this->product    = &$lty_product;

			if ( is_object( $this->product ) ) {
				if ( $current_section ) {
					$relist_data      = array_reverse( $this->product->get_lty_relists() );
					$this->from_date  = $relist_data[ $current_section - 1 ]['start_date_gmt'];
					$this->to_date    = $relist_data[ $current_section - 1 ]['end_date_gmt'];
					$this->list_count = count( $relist_data ) - intval( $current_section );
				} else {
					$this->from_date = $this->product->get_current_start_date_gmt();
					$this->to_date   = $this->product->get_lty_end_date_gmt();
				}
			}

			// Prepare the base url.
			$base_args = array(
				'lty_action' => 'view',
				'product_id' => $this->product_id,
				'tab'        => $current_tab,
				'section'    => $current_section,
			);

			$this->base_url = lty_get_lottery_page_url( $base_args );

			parent::__construct(
				array(
					'singular' => 'ticket',
					'plural'   => 'tickets',
					'ajax'     => false,
				)
			);
		}

		/**
		 * Prepares the list of items for displaying.
		 * */
		public function prepare_items() {
			// Prepare the current url.
			$this->current_url = add_query_arg( array( 'paged' => absint( $this->get_pagenum() ) ), $this->base_url );

			// Prepare the per page.
			$this->perpage = lty_get_items_per_page( 'lottery_ticket' );

			// Process the bulk actions.
			$this->perform_bulk_action();

			// Prepare the offset.
			$this->offset = $this->perpage * ( absint( $this->get_pagenum() ) - 1 );

			// Prepare the header columns.
			$this->_column_headers = array( $this->get_columns(), $this->get_hidden_columns(), $this->get_sortable_columns() );

			// Prepare the query clauses.
			$join    = $this->get_query_join();
			$where   = $this->get_query_where();
			$limit   = $this->get_query_limit();
			$offset  = $this->get_query_offset();
			$orderby = $this->get_query_orderby();

			// Prepare the all items.
			$count_items = $this->database->get_var( 'SELECT COUNT(DISTINCT ID) FROM ' . $this->database->posts . " AS p $join $where $orderby" );

			// Prepare the current page items.
			$prepare_query = $this->database->prepare( 'SELECT DISTINCT ID FROM ' . $this->database->posts . " AS p $join $where $orderby LIMIT %d,%d", $offset, $limit );
			$items         = $this->database->get_results( $prepare_query, ARRAY_A );

			// Prepare the item object.
			$this->prepare_item_object( $items );

			// Prepare the pagination arguments.
			$this->set_pagination_args(
				array(
					'total_items' => $count_items,
					'per_page'    => $this->perpage,
				)
			);
		}

		/**
		 * Render the table.
		 * */
		public function render() {
			if ( isset( $_REQUEST[ 's' ] ) && strlen( wc_clean( wp_unslash( $_REQUEST[ 's' ] ) ) ) ) { // @codingStandardsIgnoreLine.
				/* translators: %s: search keywords */
				$search_results = sprintf( __( 'Search results for &#8220;%s&#8221;', 'lottery-for-woocommerce' ), wc_clean( wp_unslash( $_REQUEST[ 's' ] ) ) ); // @codingStandardsIgnoreLine.
				echo '<span class="subtitle">' . esc_html( $search_results ) . '</span>';
			}

			// Output the table.
			$this->prepare_items();
			$this->views();
			$this->display();
		}

		/**
		 * Get a list of columns.
		 *
		 * @return array
		 * */
		public function get_columns() {
			$columns = array(
				'cb'            => '<input type="checkbox" />',
				'user_details'  => __( 'User Name', 'lottery-for-woocommerce' ),
				'ticket_number' => __( 'Ticket Number', 'lottery-for-woocommerce' ),
				'answer'        => __( 'Chosen Answer', 'lottery-for-woocommerce' ),
				'order_id'      => __( 'Order ID', 'lottery-for-woocommerce' ),
				'order_status'  => __( 'Order Status', 'lottery-for-woocommerce' ),
				'date'          => __( 'Ticket Purchase Date', 'lottery-for-woocommerce' ),
				'status'        => __( 'Status', 'lottery-for-woocommerce' ),
				'action'        => __( 'Action', 'lottery-for-woocommerce' ),
			);

			if ( ! $this->product->is_valid_question_answer() ) {
				unset( $columns['answer'] );
			}

			return $columns;
		}

		/**
		 * Get a list of hidden columns.
		 *
		 * @return array
		 * */
		public function get_hidden_columns() {
			return array();
		}

		/**
		 * Get a list of sortable columns.
		 *
		 * @return array
		 * */
		public function get_sortable_columns() {
			return array(
				'user_details'  => array( 'user_details', false ),
				'ticket_number' => array( 'ticket_number', false ),
				'order_id'      => array( 'order_id', false ),
				'answer'        => array( 'answer', false ),
				'status'        => array( 'post_status', false ),
				'date'          => array( 'date', false ),
			);
		}

		/**
		 * Message to be displayed when there are no items.
		 */
		public function no_items() {
			esc_html_e( 'No tickets to show.', 'lottery-for-woocommerce' );
		}

		/**
		 * Get a list of bulk actions.
		 *
		 * @return array
		 * */
		protected function get_bulk_actions() {
			/**
			 * This hook is used to alter the lottery ticket bulk actions.
			 *
			 * @since 1.0
			 */
			return apply_filters( $this->list_slug . '_lottery_ticket_bulk_actions', array( 'delete' => __( 'Delete', 'lottery-for-woocommerce' ) ) );
		}

		/**
		 * Processes the bulk action.
		 * */
		public function perform_bulk_action() {
			$ids = isset( $_REQUEST['id'] ) ? wc_clean( wp_unslash( $_REQUEST['id'] ) ) : array();
			$ids = ! is_array( $ids ) ? explode( ',', $ids ) : $ids;
			if ( ! lty_check_is_array( $ids ) ) {
				return;
			}

			// Return if current user not have permission.
			if ( ! current_user_can( 'edit_posts' ) ) {
				throw new exception( esc_html__( "You don't have permission to do this giveaway", 'lottery-for-woocommerce' ) );
			}

			$action = $this->current_action();
			foreach ( $ids as $id ) {
				if ( 'delete' === $action ) {
					LTY_Lottery_Handler::delete_lottery_ticket( $id );
				} elseif ( 'manual_winner' === $action ) {
					// Manual winner.
					LTY_Lottery_Winner::handle_lottery_winner( array( $id ), $this->product, '4' );
				}
			}

			wp_safe_redirect( $this->current_url );
			exit();
		}

		/**
		 * Display the list of views available on this table.
		 *
		 * @return array
		 * */
		protected function get_views() {
			$args         = array();
			$views        = array();
			$status_array = array(
				'all'                 => __( 'All', 'lottery-for-woocommerce' ),
				'lty_ticket_pending'  => __( 'Ticket Pending', 'lottery-for-woocommerce' ),
				'lty_ticket_buyer'    => __( 'Ticket Buyer', 'lottery-for-woocommerce' ),
				'lty_ticket_winner'   => __( 'Ticket Winner', 'lottery-for-woocommerce' ),
				'lty_ticket_canceled' => __( 'Ticket Canceled', 'lottery-for-woocommerce' ),
			);

			foreach ( $status_array as $status_name => $status_label ) {
				$status_count = $this->get_item_count_for_status( $status_name );
				if ( ! $status_count ) {
					continue;
				}

				$args['status'] = $status_name;
				$label          = $status_label . ' (' . $status_count . ')';
				$class          = array( strtolower( $status_name ) );
				if ( isset( $_GET[ 'status' ] ) && ( sanitize_title( $_GET[ 'status' ] ) == $status_name ) ) { // @codingStandardsIgnoreLine.
					$class[] = 'current';
				}

				if ( ! isset( $_GET[ 'status' ] ) && 'all' == $status_name ) { // @codingStandardsIgnoreLine.
					$class[] = 'current';
				}

				$views[ $status_name ] = $this->get_edit_link( $args, $label, implode( ' ', $class ) );
			}

			return $views;
		}

		/**
		 * Get the item count for the status.
		 *
		 * @since 1.0.0
		 * @param string $status Status.
		 * @global int $current_section Current section.
		 * @return int
		 * */
		private function get_item_count_for_status( $status ) {
			global $wpdb, $current_section;
			if ( ! is_object( $this->product ) ) {
				return;
			}

			$status     = ( 'all' === $status ) ? array_merge( lty_get_ticket_statuses(), array( 'lty_ticket_canceled' ) ) : array( $status );
			$post_query = new LTY_Query( $wpdb->prefix . 'posts', 'p' );
			$post_query->where( '`p`.post_type', LTY_Register_Post_Types::LOTTERY_TICKET_POSTTYPE )
					->whereIn( '`p`.post_status', $status )
					->where( '`p`.post_parent', $this->product->get_id() );

			if ( $this->product->is_unlimited_scheduled_lottery( $this->list_count ) ) {
				$post_query->leftJoin( $wpdb->prefix . 'postmeta', 'pm', 'p.ID = pm.post_id' )
					->where( 'pm.meta_key', 'lty_list_count' )
					->where( 'pm.meta_value', false === $this->list_count ? $this->product->get_current_relist_count() : $this->list_count );
			} else {
				$post_query->whereBetween( 'p.post_date_gmt', $this->from_date, $this->to_date );
			}

			return count( array_unique( $post_query->fetchCol( '`p`.ID' ) ) );
		}

		/**
		 * Get the edit link for status.
		 *
		 * @since 1.0.0
		 * @param array  $args Arguments.
		 * @param string $label Label.
		 * @param string $class Class.
		 * @return string
		 */
		private function get_edit_link( $args, $label, $class = '' ) {
			$url        = add_query_arg( $args, $this->base_url );
			$class_html = '';
			if ( ! empty( $class ) ) {
				$class_html = sprintf( ' class="%s"', esc_attr( $class ) );
			}

			return sprintf( '<a href="%s"%s>%s</a>', esc_url( $url ), $class_html, $label );
		}

		/**
		 * Extra controls to be displayed between bulk actions and pagination.
		 * */
		protected function extra_tablenav( $which ) {
			if ( 'top' !== $which || ! $this->has_items() ) {
				return;
			}

			global $current_section;
			$status = isset($_GET['status']) ? wc_clean(wp_unslash($_GET['status'])) : false;
			$extra_data = wp_json_encode(array( 'product_id' => $this->product->get_id(), 'status' => $status, 'export_lottery' => 'section', 'section' => $current_section ));
			// Export CSV button.
			printf("<button type='button' class='button button-primary lty-export-popup' data-export_type='lottery_tickets' data-extra_data=%s>%s</button>", esc_attr($extra_data), esc_html__('Export CSV', 'lottery-for-woocommerce'));

			$orderby        = isset( $_REQUEST['orderby'] ) && ! empty( $_REQUEST['orderby'] ) ? wc_clean( wp_unslash( $_REQUEST['orderby'] ) ) : '';
			$order          = isset( $_REQUEST['order'] ) && ! empty( $_REQUEST['order'] ) ? wc_clean( wp_unslash( $_REQUEST['order'] ) ) : '';
			$post_mime_type = isset( $_REQUEST['post_mime_type'] ) && ! empty( $_REQUEST['post_mime_type'] ) ? wc_clean( wp_unslash( $_REQUEST['post_mime_type'] ) ) : '';
			$detached       = isset( $_REQUEST['detached'] ) && ! empty( $_REQUEST['detached'] ) ? wc_clean( wp_unslash( $_REQUEST['detached'] ) ) : '';

			$selected_filters = isset( $_REQUEST['lty_lottery_ticket_filters'] ) ? wc_clean( wp_unslash( $_REQUEST['lty_lottery_ticket_filters'] ) ) : array();
			$filter_values    = lty_get_formatted_lottery_ticket_filters( $selected_filters );

			// Search giveaway.
			include LTY_PLUGIN_PATH . '/inc/admin/menu/views/html-lottery-tickets-search.php';
		}

		/**
		 * Format the status.
		 *
		 * @return string
		 * */
		private function format_status( $status ) {
			if ( 'all' === $status ) {
				$statuses = array_merge( lty_get_ticket_statuses(), array( 'lty_ticket_canceled' ) );
				$status   = implode( "', '", $statuses );
			}

			return $status;
		}

		/**
		 * Prepare cb column data
		 * */
		protected function column_cb( $item ) {
			/* translators: %1$s: Ticket ID, %2$s: Ticket ID label */
			return sprintf( '<input type="checkbox" name="id[]" class="tips" value="%1$s" data-tip="%2$s: %1$s" />', $item->get_id(), __( 'Ticket ID', 'lottery-for-woocommerce' ) );
		}

		/**
		 * Prepare each column data.
		 *
		 * @since 1.0.0
		 * @param object $item instanceof LTY_Lottery_Ticket.
		 * @param string $column_name Column name.
		 * @return string
		 */
		protected function column_default( $item, $column_name ) {
			switch ( $column_name ) {
				case 'user_details':
					$billing_name = is_object( $item->get_order() ) ? $item->get_order()->get_formatted_billing_full_name() : '';
					$tooltip_html = '<div class="tips" data-tip="' . wc_sanitize_tooltip( __( 'Billing name: ' . $billing_name, 'lottery-for-woocommerce' ) ) . '">';

					return $tooltip_html . $item->get_user_name() . ' (' . $item->get_user_email() . ') ';

				case 'ticket_number':
					return '' === $item->get_lottery_ticket_number() ? '-' : $item->get_lottery_ticket_number();

				case 'answer':
					if ( empty( $item->get_answer() ) ) {
						return '-';
					}

					$class = ( 'yes' === $item->get_valid_answer() ) ? 'lty-correct-answer' : 'lty-wrong-answer';

					return '<p class="' . $class . '">' . $item->get_answer() . '</p>';

				case 'order_id':
					if ( empty( $item->get_order_id() ) ) {
						return '-';
					}

					if ( ! is_object( $item->get_order() ) ) {
						return esc_html( '#' . $item->get_order_id() );
					}

					return sprintf( '<a href="%s">%s</a>', esc_url( get_edit_post_link( $item->get_order_id() ) ), esc_html( '#' . $item->get_order_id() ) );

				case 'order_status':
					if ( empty( $item->get_order_id() ) || ! is_object( $item->get_order() ) ) {
						return '-';
					}

					return ucfirst( $item->get_order()->get_status() );

				case 'date':
					return $item->get_formatted_created_date();

				case 'status':
					return lty_display_status( $item->get_status() );

				case 'action':
					$actions = array( 'delete' => lty_display_action_status( 'delete', $item->get_id(), $this->current_url, true ) );
					// Manual winner select.
					if ( is_object( $this->product ) && 'lty_ticket_winner' !== $item->get_status() && '2' == $this->product->get_lty_winner_selection_method() && $this->product->get_lty_closed() && $this->product->has_lottery_status( array( 'lty_lottery_closed' ) ) ) {
						$args = array( 'product_id' => $this->product_id );
						if ( $this->product->is_unlimited_scheduled_lottery( $this->list_count ) ) {
							$args['list_count'] = false === $this->list_count ? $this->product->get_current_relist_count() : $this->list_count;
						} else {
							$args['start_date'] = $this->product->get_current_start_date_gmt();
							$args['end_date']   = $this->product->get_lty_end_date_gmt();
						}

						$lottery_winner_ids = lty_get_lottery_winner_ids( $args );
						if ( $this->product->get_lty_winners_count() > count( $lottery_winner_ids ) && ( 'yes' !== $this->product->get_lty_lottery_unique_winners() || ! $this->product->has_user_already_winner( $item ) ) ) {
							$url                      = add_query_arg( array( 'product_id' => $this->product_id ), $this->current_url );
							$actions['manual_winner'] = lty_display_action_status( 'manual_winner', $item->get_id(), $url, true );
						}
					}

					$views = '';
					foreach ( $actions as $key => $action ) {
						$views .= $action . ' | ';
					}

					return before_last_bar( $views );
			}
		}

		/**
		 * Prepare the item Object.
		 *
		 * @return void
		 * */
		private function prepare_item_object( $items ) {
			$prepare_items = array();
			if ( lty_check_is_array( $items ) ) {
				foreach ( $items as $item ) {
					$prepare_items[] = lty_get_lottery_ticket( $item['ID'] );
				}
			}

			$this->items = $prepare_items;
		}

		/**
		 * Get the query join clauses.
		 *
		 * @return string
		 * */
		private function get_query_join() {
			$join = ' INNER JOIN ' . $this->database->postmeta . ' AS pm ON ( pm.post_id = p.ID ) 
					INNER JOIN ' . $this->database->postmeta . ' AS pm1 ON ( pm1.post_id = p.ID ) 
					INNER JOIN ' . $this->database->postmeta . ' AS pm2 ON ( pm2.post_id = p.ID )';

			/**
			 * This hook is used to alter the lottery ticket query join fields.
			 *
			 * @since 1.0.0
			 */
			return apply_filters( $this->list_slug . '_query_join', $join );
		}

		/**
		 * Get the query where clauses.
		 *
		 * @since 1.0.0
		 * @global int $current_section Current section.
		 * @return string
		 * */
		private function get_query_where() {
			$current_status = 'all';
			if ( isset( $_GET['status'] ) && 'all' !== sanitize_title( $_GET['status'] ) ) {
				$current_status = sanitize_title( $_GET['status'] );
			}

			$where = " WHERE p.post_type = '" . $this->post_type . "' 
					AND p.post_status IN('" . $this->format_status( $current_status ) . "') 
					AND p.post_parent = '" . $this->product_id . "'";

			if ( $this->product->is_unlimited_scheduled_lottery( $this->list_count ) ) {
				$list_count = false === $this->list_count ? $this->product->get_current_relist_count() : $this->list_count;
				$where     .= " AND pm2.meta_key = 'lty_list_count' AND pm2.meta_value = '{$list_count}'";
			} else {
				$where .= " AND p.post_date_gmt BETWEEN '" . $this->from_date . "' AND '" . $this->to_date . "'";
			}

			// Search.
			$where = $this->get_custom_search_query( $where );
			// Filters.
			$where = $this->get_custom_filters_query( $where );

			/**
			 * This hook is used to alter the lottery ticket query where fields.
			 *
			 * @since 1.0
			 */
			return apply_filters( $this->list_slug . '_query_where', $where );
		}

		/**
		 * Get the query limit clauses.
		 *
		 * @return string
		 * */
		private function get_query_limit() {
			/**
			 * This hook is used to alter the lottery ticket query limit fields.
			 *
			 * @since 1.0.0
			 */
			return apply_filters( $this->list_slug . '_query_limit', $this->perpage );
		}

		/**
		 * Get the query offset clauses.
		 *
		 * @return string
		 * */
		private function get_query_offset() {
			/**
			 * This hook is used to alter the lottery ticket query offset fields.
			 *
			 * @since 1.0.0
			 */
			return apply_filters( $this->list_slug . '_query_offset', $this->offset );
		}

		/**
		 * Get the query order by clauses.
		 *
		 * @return string
		 * */
		private function get_query_orderby() {
			$order = 'DESC';
			if ( ! empty( $_REQUEST[ 'order' ] ) && is_string( $_REQUEST[ 'order' ] ) ) { // @codingStandardsIgnoreLine.
				if ( 'ASC' === strtoupper( wc_clean( wp_unslash( $_REQUEST[ 'order' ] ) ) ) ) { // @codingStandardsIgnoreLine.
					$order = 'ASC';
				}
			}

			// Order By.
			if ( isset( $_REQUEST['orderby'] ) ) {
				switch ( wc_clean( wp_unslash( $_REQUEST[ 'orderby' ] ) ) ) { // @codingStandardsIgnoreLine.
					case 'user_details':
						$this->orderby = " AND pm.meta_key='lty_user_name' ORDER BY pm.meta_value  " . $order;
						break;

					case 'ticket_number':
						if ( 'alpha_numeric' === $this->product->get_ticket_number_orderby() ) {
							$this->orderby = " AND pm.meta_key='lty_ticket_number' ORDER BY pm.meta_value " . $order;
						} else {
							$this->orderby = " AND pm.meta_key='lty_ticket_number' ORDER BY CAST(pm.meta_value AS SIGNED) " . $order;
						}

						break;

					case 'order_id':
						$this->orderby = " AND pm.meta_key='lty_order_id' ORDER BY pm.meta_value " . $order;
						break;

					case 'answer':
						$this->orderby = " AND pm.meta_key='lty_answer' ORDER BY pm.meta_value " . $order;
						break;

					case 'status':
						$this->orderby = ' ORDER BY p.post_status ' . $order;
						break;

					case 'date':
						$this->orderby = ' ORDER BY p.post_date ' . $order;
						break;
				}
			}

			/**
			 * This hook is used to alter the lottery ticket query order by fields.
			 *
			 * @since 1.0.0
			 */
			return apply_filters( $this->list_slug . '_query_orderby', $this->orderby );
		}

		/**
		 * Custom Search.
		 *
		 * @since 1.0.0
		 * @param string $where Query where.
		 * @return string
		 */
		public function get_custom_search_query( $where ) {
			if ( ! isset( $_REQUEST[ 's' ] ) || empty($_REQUEST[ 's' ]) ) { // @codingStandardsIgnoreLine.
				return $where;
			}

			$terms            = explode( ' , ', wc_clean( wp_unslash( $_REQUEST[ 's' ] ) ) ); // @codingStandardsIgnoreLine.
			$selected_filters = isset( $_REQUEST['lty_lottery_ticket_filters'] ) ? wc_clean( wp_unslash( $_REQUEST['lty_lottery_ticket_filters'] ) ) : array();
			$filter_values    = lty_get_formatted_lottery_ticket_filters( $selected_filters );
			foreach ( $terms as $term ) {
				$term       = $this->database->esc_like( ( $term ) );
				$post_query = new LTY_Query( $this->database->prefix . 'posts', 'p' );
				$post_query->select( 'DISTINCT `p`.ID' )
						->leftJoin( $this->database->prefix . 'postmeta', 'pm', '`p`.`ID` = `pm`.`post_id`' )
						->where( '`p`.post_type', $this->post_type )
						->whereIn( '`p`.post_status', array_merge( lty_get_ticket_statuses(), array( 'lty_ticket_canceled' ) ) )
						->where( '`p`.post_parent', $this->product_id )
						->whereBetween( '`p`.post_date_gmt', $this->from_date, $this->to_date )
						->whereIn( '`pm`.meta_key', $filter_values['search_columns'] )
						->whereLike( '`pm`.meta_value', ( '1' === $filter_values['search_columns_type'] ? '%' . $term . '%' : $term ) );

				$post_ids = $post_query->fetchCol( 'ID' );
			}

			$post_ids = lty_check_is_array( $post_ids ) ? $post_ids : array( 0 );
			$where   .= ' AND (id IN (' . implode( ' , ', $post_ids ) . '))';

			return $where;
		}

		/**
		 * Get custom search filters query.
		 *
		 * @since 10.2.0
		 * @param string $where Query where.
		 * @return string
		 */
		public function get_custom_filters_query( $where ) {
			if ( ! isset( $_REQUEST['lty_lottery_ticket_filters'] ) ) { // @codingStandardsIgnoreLine.
				return $where;
			}

			$selected_filters = isset( $_REQUEST['lty_lottery_ticket_filters'] ) ? wc_clean( wp_unslash( $_REQUEST['lty_lottery_ticket_filters'] ) ) : array();
			$filter_values    = lty_get_formatted_lottery_ticket_filters( $selected_filters );

			// User filter.
			if ( ! empty( $filter_values['user_type'] ) ) {
				$user_filter_query = '1' === $filter_values['user_type'] ? ' pm1.meta_value > 0' : ' pm1.meta_value = 0';
				$where            .= " AND pm1.meta_key = 'lty_user_id' AND" . $user_filter_query;
			}

			if ( ! empty( $filter_values['purchased_date_filter_type'] ) ) {
				$date_filter = lty_get_date_filter_values( $filter_values['purchased_date_filter_type'], $filter_values['purchased_from_date'], $filter_values['purchased_to_date'] );
				$where       = ! empty( $filter_values['purchased_from_date'] ) ? $where . " AND p.post_date_gmt > '" . $date_filter['from_date'] . "'" : $where;
				$where       = ! empty( $filter_values['purchased_to_date'] ) ? $where . " AND p.post_date_gmt < '" . $date_filter['to_date'] . "'" : $where;
			}

			return $where;
		}
	}

}
