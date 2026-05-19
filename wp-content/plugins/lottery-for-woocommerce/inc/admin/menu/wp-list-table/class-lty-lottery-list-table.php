<?php
/**
 * Lotteries list table.
 *
 * @since 1.0.0
 * */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

if ( ! class_exists( 'LTY_Lottery_List_Table' ) ) {

	/**
	 * LTY_Lottery_List_Table Class.
	 * */
	class LTY_Lottery_List_Table extends WP_List_Table {

		/**
		 * Per page count
		 *
		 * @var int
		 * */
		private $perpage = 10;

		/**
		 * Offset
		 *
		 * @var int
		 * */
		private $offset;

		/**
		 * Order BY
		 *
		 * @var string
		 * */
		private $orderby = 'ID';

		/**
		 * Order.
		 *
		 * @var string
		 * */
		private $order = 'DESC';

		/**
		 * Post type.
		 *
		 * @var string
		 * */
		private $post_type = 'product';

		/**
		 * Lottery IDs.
		 *
		 * @var array
		 * */
		private $lottery_ids;

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
		 * List slug.
		 *
		 * @since 9.4.0
		 * @var string
		 */
		private $list_slug;

		/**
		 * Constructor.
		 */
		public function __construct() {
			// Prepare the base url.
			$this->base_url = lty_get_lottery_page_url();

			parent::__construct(
				array(
					'singular' => 'lottery',
					'plural'   => 'lotteries',
					'ajax'     => false,
				)
			);

			add_action( 'admin_footer', array( $this, 'footer_content' ) );
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
		 * Prepares the list of items for displaying.
		 * */
		public function prepare_items() {
			// Prepare the current url.
			$this->current_url = add_query_arg( array( 'paged' => absint( $this->get_pagenum() ) ), $this->base_url );

			// Prepare the per page.
			$this->perpage = lty_get_items_per_page( 'lottery' );

			// Process the bulk actions.
			$this->perform_bulk_action();

			// Prepare the lottery ids.
			$this->prepare_lottery_ids();

			// Prepare the offset.
			$this->offset = $this->perpage * ( absint( $this->get_pagenum() ) - 1 );

			// Prepare the header columns.
			$this->_column_headers = array( $this->get_columns(), $this->get_hidden_columns(), $this->get_sortable_columns() );

			// Prepare the current page items.
			$items = $this->get_current_page_items();

			// Prepare the item object.
			$this->prepare_item_object( $items );

			// Prepare the pagination arguments.
			$this->set_pagination_args(
				array(
					'total_items' => $this->get_item_count(),
					'per_page'    => $this->perpage,
				)
			);
		}

		/**
		 * Message to be displayed when there are no items.
		 */
		public function no_items() {
			esc_html_e( 'No giveaway to show.', 'lottery-for-woocommerce' );
		}

		/**
		 * Get a list of columns.
		 *
		 * @return array
		 * */
		public function get_columns() {
			$columns = array(
				'cb'                     => '<input type="checkbox" />',
				'name'                   => __( 'Giveaway Name', 'lottery-for-woocommerce' ),
				'min_ticket_count'       => __( 'Minimum Ticket(s)', 'lottery-for-woocommerce' ),
				'max_ticket_count'       => __( 'Maximum Ticket(s)', 'lottery-for-woocommerce' ),
				'ticket_sold_count'      => __( 'Number of Tickets sold', 'lottery-for-woocommerce' ),
				'participant_count'      => __( 'Number of Participants', 'lottery-for-woocommerce' ),
				'ticket_generation_type' => __( 'Ticket Generation Type', 'lottery-for-woocommerce' ),
				'winner_method'          => __( 'Winner Selection Type', 'lottery-for-woocommerce' ),
				'winner_count'           => __( 'Number of Winner', 'lottery-for-woocommerce' ),
				'status'                 => __( 'Status', 'lottery-for-woocommerce' ),
				'actions'                => __( 'Actions', 'lottery-for-woocommerce' ),
			);

			return $columns;
		}

		/**
		 * Get a list of hidden columns.
		 *
		 * @return array
		 * */
		protected function get_hidden_columns() {
			return array();
		}

		/**
		 * Get a list of sortable columns.
		 *
		 * @return array
		 * */
		protected function get_sortable_columns() {
			return array(
				'name'   => array( 'name', false ),
				'status' => array( 'status', false ),
			);
		}

		/**
		 * Get a list of bulk actions.
		 *
		 * @return array
		 * */
		protected function get_bulk_actions() {
			$action = array();

			/**
			 * This hook is used to alter the lottery bulk actions.
			 *
			 * @since 1.0
			 */
			$action              = apply_filters( $this->list_slug . '_bulk_actions', $action );
			$action['start_now'] = __( 'Start Now', 'lottery-for-woocommerce' );
			$action['end_now']   = __( 'End Now', 'lottery-for-woocommerce' );

			return $action;
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
				throw new exception( esc_html__( "You don't have permission to do this action", 'lottery-for-woocommerce' ) );
			}

			foreach ( $ids as $id ) {
				$product = wc_get_product( $id );
				switch ( $this->current_action() ) {
					case 'start_now':
						LTY_Lottery_Handler::start_lottery( $id, $product, true );
						break;

					case 'end_now':
						LTY_Lottery_Handler::end_lottery( $id, $product, true );
						break;
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
			$args  = array();
			$views = array();

			$status_array = array(
				'all'                     => __( 'All', 'lottery-for-woocommerce' ),
				'lty_lottery_not_started' => __( 'Not Yet Started', 'lottery-for-woocommerce' ),
				'lty_lottery_started'     => __( 'On-going', 'lottery-for-woocommerce' ),
				'lty_lottery_closed'      => __( 'Closed', 'lottery-for-woocommerce' ),
				'lty_lottery_finished'    => __( 'Finished', 'lottery-for-woocommerce' ),
				'lty_lottery_failed'      => __( 'Failed', 'lottery-for-woocommerce' ),
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
		 * Get the edit link for status.
		 *
		 * @since 1.0.0
		 * @param array  $args Arguments.
		 * @param string $label Label.
		 * @param string $class
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
		 *
		 * @since 9.9.0
		 * @param string $which Position to display.
		 * */
		protected function extra_tablenav( $which ) {
			if ( 'top' !== $which ) {
				return;
			}

			$orderby          = isset( $_REQUEST['orderby'] ) && ! empty( $_REQUEST['orderby'] ) ? wc_clean( wp_unslash( $_REQUEST['orderby'] ) ) : '';
			$order            = isset( $_REQUEST['order'] ) && ! empty( $_REQUEST['order'] ) ? wc_clean( wp_unslash( $_REQUEST['order'] ) ) : '';
			$post_mime_type   = isset( $_REQUEST['post_mime_type'] ) && ! empty( $_REQUEST['post_mime_type'] ) ? wc_clean( wp_unslash( $_REQUEST['post_mime_type'] ) ) : '';
			$detached         = isset( $_REQUEST['detached'] ) && ! empty( $_REQUEST['detached'] ) ? wc_clean( wp_unslash( $_REQUEST['detached'] ) ) : '';
			$selected_filters = isset( $_REQUEST['lty_lottery_filters'] ) ? wc_clean( wp_unslash( $_REQUEST['lty_lottery_filters'] ) ) : array();
			$filter_values    = lty_get_formatted_lottery_filters( $selected_filters );

			// Lottery filters.
			include LTY_PLUGIN_PATH . '/inc/admin/menu/views/html-lottery-filters.php';

			/**
			 * Hook: lty_lottery_extra_tablenav
			 *
			 * @since 9.9.0
			 */
			do_action( 'lty_lottery_extra_tablenav' );
		}

		/**
		 * Prepare the CB column data.
		 *
		 * @return string
		 * */
		protected function column_cb( $item ) {
			return sprintf( '<input type="checkbox" name="id[]" value="%s" />', $item->get_id() );
		}

		/**
		 * Prepare the each column data.
		 *
		 * @return mixed
		 * */
		protected function column_default( $item, $column_name ) {
			switch ( $column_name ) {
				case 'name':
					$actions                 = array();
					$views                   = '<a href="' . esc_url( get_edit_post_link( $item->get_id() ) ) . '">' . esc_html( $item->get_name() ) . '</a><br/>';
					$actions['id']           = '<small><b>' . esc_html__( 'ID: ', 'lottery-for-woocommerce' ) . '</b>' . esc_html( $item->get_id() ) . '</small>';
					$actions['edit_product'] = lty_display_action_status( 'edit_product', '', get_edit_post_link( $item->get_id() ), true );
					$actions['view_product'] = lty_display_action_status( 'view_product', '', get_permalink( $item->get_id() ), true );
					foreach ( $actions as $key => $action ) {
						$views .= $action . ' | ';
					}

					return before_last_bar( $views );

				case 'min_ticket_count':
					return $item->get_lty_minimum_tickets();

				case 'max_ticket_count':
					return $item->get_lty_maximum_tickets();

				case 'ticket_sold_count':
					return '<a href="' . esc_url(
						add_query_arg(
							array(
								'lty_action' => 'view',
								'product_id' => $item->get_id(),
							),
							$this->current_url
						)
					) . '">' . esc_html( $item->get_purchased_ticket_count() ) . '</a>';

				case 'participant_count':
					$participants = lty_get_lottery_participant_count( $item->get_id(), true );
					return ! empty( $participants ) ? $participants : 0;

				case 'ticket_generation_type':
					return '1' == $item->get_lty_ticket_generation_type() ? __( 'Automatic', 'lottery-for-woocommerce' ) : __( 'User Chooses the Ticket', 'lottery-for-woocommerce' );

				case 'winner_method':
					return lty_get_winner_selection_method_name( $item->get_lty_winner_selection_method() );

				case 'winner_count':
					return $item->get_lty_winners_count();

				case 'status':
					return lty_display_status( $item->get_lty_lottery_status() );

				case 'actions':
					$actions                        = array();
					$url                            = add_query_arg(
						array(
							'lty_action' => 'view',
							'product_id' => $item->get_id(),
						),
						$this->current_url
					);
					$actions['view_lottery_ticket'] = lty_display_action_status( 'view', '', $url, true );
					$actions['manual_notification'] = lty_display_action_status( 'manual_lottery_notification', $item->get_id(), $url );

					$views = '';
					foreach ( $actions as $key => $action ) {
						$views .= $action . ' | ';
					}

					$views  = before_last_bar( $views );
					$views .= '<br/><br/>' . lty_get_lottery_action_button_url( $item->get_lty_lottery_status(), $item->get_id(), $this->current_url, true );

					return $views;
			}
		}

		/**
		 * Get the current page items.
		 *
		 * @return array.
		 * */
		private function get_current_page_items() {
			$args = array(
				'post_type'      => $this->post_type,
				'post_status'    => array( 'publish', 'private' ),
				'posts_per_page' => '-1',
				'fields'         => 'ids',
				'tax_query'      => array(
					array(
						'taxonomy' => 'product_type',
						'field'    => 'slug',
						'terms'    => 'lottery',
					),
				),
				'post__in'       => $this->lottery_ids,
			);

			$args['orderby']        = isset( $_GET['orderby'] ) ? sanitize_title( wp_unslash( $_GET['orderby'] ) ) : $this->orderby;
			$args['order']          = isset( $_GET['order'] ) ? sanitize_title( wp_unslash( $_GET['order'] ) ) : $this->order;
			$args['posts_per_page'] = $this->perpage;
			$args['offset']         = $this->offset;
			$search_term            = ( isset( $_REQUEST['s'] ) && strlen( wc_clean( wp_unslash( $_REQUEST['s'] ) ) ) ) ? wc_clean( wp_unslash( $_REQUEST['s'] ) ) : '';

			if ( $search_term ) {
				$args['s'] = $search_term;
			}

			$args = $this->get_search_filter_query_args( $args );

			return get_posts( $args );
		}

		/**
		 * Get the item count.
		 *
		 * @return array.
		 * */
		private function get_item_count() {
			$args = array(
				'post_type'      => $this->post_type,
				'post_status'    => array( 'publish', 'private' ),
				'posts_per_page' => '-1',
				'fields'         => 'ids',
				'tax_query'      => array(
					array(
						'taxonomy' => 'product_type',
						'field'    => 'slug',
						'terms'    => 'lottery',
					),
				),
				'post__in'       => $this->lottery_ids,
			);

			$status = isset( $_GET['status'] ) ? sanitize_title( wp_unslash( $_GET['status'] ) ) : '';

			if ( $status && 'all' !== $status ) {
				$args['meta_key']   = '_lty_lottery_status';
				$args['meta_value'] = $status;
			}

			return count( get_posts( $args ) );
		}

		/**
		 * Prepare the item Object.
		 *
		 * @return void
		 * */
		private function prepare_item_object( $items ) {
			$prepare_items = array();
			if ( lty_check_is_array( $items ) ) {
				foreach ( $items as $item_id ) {
					$prepare_items[] = wc_get_product( $item_id );
				}
			}

			$this->items = $prepare_items;
		}

		/**
		 * Get the item count for the status.
		 *
		 * @return int
		 * */
		private function get_item_count_for_status( $status ) {
			$args = array(
				'post_type'      => $this->post_type,
				'post_status'    => array( 'publish', 'private' ),
				'posts_per_page' => '-1',
				'fields'         => 'ids',
				'tax_query'      => array(
					array(
						'taxonomy' => 'product_type',
						'field'    => 'slug',
						'terms'    => 'lottery',
					),
				),
				'post__in'       => $this->lottery_ids,
			);

			if ( 'all' !== $status ) {
				$args['meta_key']   = '_lty_lottery_status';
				$args['meta_value'] = $status;
			}

			return count( get_posts( $args ) );
		}

		/**
		 * Prepare the Lottery IDs.
		 *
		 * @return void
		 * */
		private function prepare_lottery_ids() {
			$args = array(
				'post_type'      => $this->post_type,
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
				'orderby'        => $this->orderby,
			);

			$lottery_ids = get_posts( $args );

			/**
			 * This hook is used to alter the lottery product IDs.
			 *
			 * @since 1.0.0
			 */
			$this->lottery_ids = apply_filters( 'lty_lottery_product_ids_in_list_table', $lottery_ids );
		}

		/**
		 * Get search filters.
		 *
		 * @since 10.2.0
		 * @param array $query_args Query arguments.
		 * @return array
		 * */
		private function get_search_filter_query_args( $query_args ) {
			$meta_query = array( 'relation' => 'AND' );

			// Status filter.
			$status = isset( $_GET['status'] ) ? sanitize_title( wp_unslash( $_GET['status'] ) ) : '';
			if ( ! empty( $status ) && 'all' !== $status ) {
				$meta_query[] = array(
					'key'     => '_lty_lottery_status',
					'value'   => $status,
					'compare' => '=',
				);
			}

			$selected_filters = isset( $_REQUEST['lty_lottery_filters'] ) ? wc_clean( wp_unslash( $_REQUEST['lty_lottery_filters'] ) ) : array();
			$filter_values    = lty_get_formatted_lottery_filters( $selected_filters );
			// Ticket generation type filter.
			if ( ! empty( $filter_values['ticket_generation_type'] ) ) {
				$meta_query[] = array(
					'key'     => '_lty_ticket_generation_type',
					'value'   => $filter_values['ticket_generation_type'],
					'compare' => '=',
				);
			}

			// Winner selection method filter.
			if ( ! empty( $filter_values['winner_selection_type'] ) ) {
				$meta_query[] = array(
					'key'     => '_lty_winner_selection_method',
					'value'   => $filter_values['winner_selection_type'],
					'compare' => '=',
				);
			}

			if ( count( $meta_query ) > 1 ) {
				$query_args['meta_query'] = $meta_query;
			}

			return $query_args;
		}

		/**
		 * Display the footer contents.
		 *
		 * @since 12.4.0
		 */
		public function footer_content() {
			echo '<script type="text/template" id="tmpl-manual-lottery-notification-modal">{{{data}}}</script>';

			include_once LTY_PLUGIN_PATH . '/inc/admin/menu/views/backbone-modal/html-manual-lottery-notification.php';
		}
	}

}
