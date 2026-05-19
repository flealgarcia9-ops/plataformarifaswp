<?php
/**
 * Lottery instant winners table.
 *
 * @since 8.0.0
 * */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

if ( ! class_exists( 'LTY_Lottery_Instant_Winners_Table' ) ) {

	/**
	 * Class.
	 *
	 * @since 8.0.0
	 * */
	class LTY_Lottery_Instant_Winners_Table extends WP_List_Table {

		/**
		 * Per page count.
		 *
		 * @var int
		 * @since 8.0.0
		 * */
		private $perpage = 10;

		/**
		 * Database.
		 *
		 * @var object
		 * @since 8.0.0
		 * */
		private $database;

		/**
		 * Offset.
		 *
		 * @var int
		 * @since 8.0.0
		 * */
		private $offset;

		/**
		 * Order BY.
		 *
		 * @var string
		 * @since 8.0.0
		 * */
		private $orderby = 'ORDER BY ID ASC';

		/**
		 * Post type.
		 *
		 * @var string
		 * @since 8.0.0
		 * */
		private $post_type = LTY_Register_Post_Types::LOTTERY_INSTANT_WINNER_LOG_POSTTYPE;

		/**
		 * List Slug.
		 *
		 * @var string
		 * @since 8.0.0
		 * */
		private $list_slug = 'lty_instant_winners';

		/**
		 * Base URL.
		 *
		 * @var string
		 * @since 8.0.0
		 * */
		private $base_url;

		/**
		 * Current URL.
		 *
		 * @var string
		 * @since 8.0.0
		 * */
		private $current_url;

		/**
		 * Product ID.
		 *
		 * @var string|int
		 * @since 8.0.0
		 * */
		private $product_id;

		/**
		 * Product.
		 *
		 * @var object
		 * @since 8.0.0
		 * */
		private $product;

		/**
		 * Lottery Instant Winners IDs.
		 *
		 * @since 8.0.0
		 * @var array
		 * */
		private $instant_winner_ids;

		/**
		 * Constructor.
		 *
		 * @since 8.0.0
		 */
		public function __construct() {
			global $wpdb, $lottery_id, $lty_product, $current_tab, $current_section;

			$this->database   = &$wpdb;
			$this->product_id = $lottery_id;
			$this->product    = &$lty_product;

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
					'singular' => 'instant_winner',
					'plural'   => 'instant_winners',
					'ajax'     => false,
				)
			);
		}

		/**
		 * Prepares the list of items for displaying.
		 *
		 * @since 8.0.0
		 * */
		public function prepare_items() {
			// Prepare the current url.
			$this->current_url = add_query_arg( array( 'instant_winner_paged' => absint( $this->get_pagenum() ) ), $this->base_url );

			// Prepare the perpage.
			$this->perpage = lty_get_items_per_page( 'lottery_instant_winners' );

			// Prepare instant winners prizes ids.
			$this->prepare_instant_winner_ids();

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
		 * Sets all the necessary pagination arguments.
		 *
		 * @since 3.1.0
		 *
		 * @param array|string $args Array or string of arguments with information about the pagination.
		 */
		protected function set_pagination_args( $args ) {
			$args = wp_parse_args(
				$args,
				array(
					'total_items' => 0,
					'total_pages' => 0,
					'per_page'    => 0,
				)
			);

			if ( ! $args['total_pages'] && $args['per_page'] > 0 ) {
				$args['total_pages'] = ceil( $args['total_items'] / $args['per_page'] );
			}

			// Redirect if page number is invalid and headers are not already sent.
			if ( ! headers_sent() && ! wp_doing_ajax() && $args['total_pages'] > 0 && $this->get_pagenum() > $args['total_pages'] ) {
				wp_safe_redirect( add_query_arg( 'instant_winner_paged', $args['total_pages'] ) );
				exit;
			}

			$this->_pagination_args = $args;
		}

		/**
		 * Displays the pagination.
		 *
		 * @since 9.2.0
		 * @param string $which Top or bottom page to display.
		 */
		protected function pagination( $which ) {
			if ( empty( $this->_pagination_args ) ) {
				return;
			}

			if ( ! isset( $_SERVER['HTTP_HOST'] ) || ! isset( $_SERVER['REQUEST_URI'] ) ) {
				return;
			}

			$total_items     = $this->_pagination_args['total_items'];
			$total_pages     = $this->_pagination_args['total_pages'];
			$infinite_scroll = false;
			if ( isset( $this->_pagination_args['infinite_scroll'] ) ) {
				$infinite_scroll = $this->_pagination_args['infinite_scroll'];
			}

			if ( 'top' === $which && $total_pages > 1 ) {
				$this->screen->render_screen_reader_content( 'heading_pagination' );
			}

			$output = '<span class="displaying-num">' . sprintf(
			/* translators: %s: Number of items. */
				_n( '%s item', '%s items', $total_items ),
				number_format_i18n( $total_items )
			) . '</span>';

			$current              = $this->get_pagenum();
			$removable_query_args = wp_removable_query_args();

			$current_url = set_url_scheme( 'http://' . wc_clean( wp_unslash( $_SERVER['HTTP_HOST'] ) ) . wc_clean( wp_unslash( $_SERVER['REQUEST_URI'] ) ) );

			$current_url = remove_query_arg( $removable_query_args, $current_url );

			$page_links = array();

			$total_pages_before = '<span class="paging-input">';
			$total_pages_after  = '</span></span>';

			$disable_first = false;
			$disable_last  = false;
			$disable_prev  = false;
			$disable_next  = false;

			if ( 1 == $current ) {
				$disable_first = true;
				$disable_prev  = true;
			}
			if ( $total_pages == $current ) {
				$disable_last = true;
				$disable_next = true;
			}

			if ( $disable_first ) {
				$page_links[] = '<span class="tablenav-pages-navspan button disabled" aria-hidden="true">&laquo;</span>';
			} else {
				$page_links[] = sprintf(
					"<a class='first-page button' href='%s'>" .
					"<span class='screen-reader-text'>%s</span>" .
					"<span aria-hidden='true'>%s</span>" .
					'</a>',
					esc_url( remove_query_arg( 'instant_winner_paged', $current_url ) ),
					/* translators: Hidden accessibility text. */
					__( 'First page' ),
					'&laquo;'
				);
			}

			if ( $disable_prev ) {
				$page_links[] = '<span class="tablenav-pages-navspan button disabled" aria-hidden="true">&lsaquo;</span>';
			} else {
				$page_links[] = sprintf(
					"<a class='prev-page button' href='%s'>" .
					"<span class='screen-reader-text'>%s</span>" .
					"<span aria-hidden='true'>%s</span>" .
					'</a>',
					esc_url( add_query_arg( 'instant_winner_paged', max( 1, $current - 1 ), $current_url ) ),
					/* translators: Hidden accessibility text. */
					__( 'Previous page' ),
					'&lsaquo;'
				);
			}

			if ( 'bottom' === $which ) {
				$html_current_page  = $current;
				$total_pages_before = sprintf(
					'<span class="screen-reader-text">%s</span>' .
					'<span id="table-paging" class="paging-input">' .
					'<span class="tablenav-paging-text">',
					/* translators: Hidden accessibility text. */
					__( 'Current Page' )
				);
			} else {
				$html_current_page = sprintf(
					'<label for="current-page-selector" class="screen-reader-text">%s</label>' .
					"<input class='current-page' id='current-page-selector' type='text'
					name='instant_winner_paged' value='%s' size='%d' aria-describedby='table-paging' />" .
					"<span class='tablenav-paging-text'>",
					/* translators: Hidden accessibility text. */
					__( 'Current Page' ),
					$current,
					strlen( $total_pages )
				);
			}

			$html_total_pages = sprintf( "<span class='total-pages'>%s</span>", number_format_i18n( $total_pages ) );

			$page_links[] = $total_pages_before . sprintf(
			/* translators: 1: Current page, 2: Total pages. */
				_x( '%1$s of %2$s', 'paging' ),
				$html_current_page,
				$html_total_pages
			) . $total_pages_after;

			if ( $disable_next ) {
				$page_links[] = '<span class="tablenav-pages-navspan button disabled" aria-hidden="true">&rsaquo;</span>';
			} else {
				$page_links[] = sprintf(
					"<a class='next-page button' href='%s'>" .
					"<span class='screen-reader-text'>%s</span>" .
					"<span aria-hidden='true'>%s</span>" .
					'</a>',
					esc_url( add_query_arg( 'instant_winner_paged', min( $total_pages, $current + 1 ), $current_url ) ),
					/* translators: Hidden accessibility text. */
					__( 'Next page' ),
					'&rsaquo;'
				);
			}

			if ( $disable_last ) {
				$page_links[] = '<span class="tablenav-pages-navspan button disabled" aria-hidden="true">&raquo;</span>';
			} else {
				$page_links[] = sprintf(
					"<a class='last-page button' href='%s'>" .
					"<span class='screen-reader-text'>%s</span>" .
					"<span aria-hidden='true'>%s</span>" .
					'</a>',
					esc_url( add_query_arg( 'instant_winner_paged', $total_pages, $current_url ) ),
					/* translators: Hidden accessibility text. */
					__( 'Last page' ),
					'&raquo;'
				);
			}

			$pagination_links_class = 'pagination-links';
			if ( ! empty( $infinite_scroll ) ) {
				$pagination_links_class .= ' hide-if-js';
			}
			$output .= "\n<span class='$pagination_links_class'>" . implode( "\n", $page_links ) . '</span>';

			if ( $total_pages ) {
				$page_class = $total_pages < 2 ? ' one-page' : '';
			} else {
				$page_class = ' no-pages';
			}
			$this->_pagination = "<div class='tablenav-pages{$page_class}'>$output</div>";

			echo wp_kses_post( $this->_pagination );
		}

		/**
		 * Gets the current page number.
		 *
		 * @since 9.2.0
		 * @return int
		 */
		public function get_pagenum() {
			$pagenum = isset( $_REQUEST['instant_winner_paged'] ) ? absint( $_REQUEST['instant_winner_paged'] ) : 0;

			if ( isset( $this->_pagination_args['total_pages'] ) && $pagenum > $this->_pagination_args['total_pages'] ) {
				$pagenum = $this->_pagination_args['total_pages'];
			}

			return max( 1, $pagenum );
		}

		/**
		 * Render the table.
		 *
		 * @since 8.0.0
		 * */
		public function render() {
			if (isset($_REQUEST['instant_winner_s']) && strlen(wc_clean(wp_unslash($_REQUEST['instant_winner_s'])))) { // @codingStandardsIgnoreLine.
				/* translators: %s: search keywords */
				$search_results = sprintf( __( 'Search results for &#8220;%s&#8221;', 'lottery-for-woocommerce' ), wc_clean( wp_unslash( $_REQUEST['instant_winner_s'] ) ) ); // @codingStandardsIgnoreLine.
				echo '<span class="subtitle">' . esc_html( $search_results ) . '</span>';
			}

			// Output the table.
			$this->prepare_items();
			$this->views();
			$this->display();
		}

		/**
		 * Prepare instant winner IDs.
		 *
		 * @since 8.0.0
		 * */
		private function prepare_instant_winner_ids() {
			$this->instant_winner_ids = lty_get_instant_winner_log_ids( $this->product->get_id(), false, $this->get_current_relist_count() );
		}

		/**
		 * Get a list of columns.
		 *
		 * @since 8.0.0
		 * @return array
		 * */
		public function get_columns() {
			$columns = array(
				'cb'            => '<input type="checkbox" />',
				'id'            => __( 'Instant Winner ID', 'lottery-for-woocommerce' ),
				'ticket_number' => __( 'Ticket Number', 'lottery-for-woocommerce' ),
				'prize_type'    => __( 'Prize Type', 'lottery-for-woocommerce' ),
				'winning_prize' => __( 'Winning Prize', 'lottery-for-woocommerce' ),
				'user_details'  => __( 'Winner Name', 'lottery-for-woocommerce' ),
				'status'        => __( 'Status', 'lottery-for-woocommerce' ),
				'order_details' => __( 'Order Details', 'lottery-for-woocommerce' ),
				'action'        => __( 'Action', 'lottery-for-woocommerce' ),
			);

			return $columns;
		}

		/**
		 * Get a list of hidden columns.
		 *
		 * @since 8.0.0
		 * @return array
		 * */
		public function get_hidden_columns() {
			return array();
		}

		/**
		 * Get a list of sortable columns.
		 *
		 * @since 8.0.0
		 * @return array
		 * */
		public function get_sortable_columns() {
			return array(
				'ticket_number' => array( 'ticket_number', false ),
				'status'        => array( 'post_status', false ),
			);
		}

		/**
		 * Get a list of bulk actions.
		 *
		 * @since 10.6.0
		 * @return array
		 * */
		protected function get_bulk_actions() {
			/**
			 * This hook is used to alter the instant winner bulk actions.
			 *
			 * @since 10.6.0
			 * @param array Bulk actions.
			 */
			return apply_filters(
				$this->list_slug . '_lottery_instant_winners_bulk_actions',
				array(
					'delete' => __( 'Delete', 'lottery-for-woocommerce' ),
					'remove' => __( 'Remove Only Winner', 'lottery-for-woocommerce' ),
				)
			);
		}

		/**
		 * Displays the bulk actions dropdown.
		 *
		 * @since 11.5.0
		 *
		 * @param string $which The location of the bulk actions: Either 'top' or 'bottom'.
		 *                      This is designated as optional for backward compatibility.
		 */
		protected function bulk_actions( $which = '' ) {
			if ( is_null( $this->_actions ) ) {
				$this->_actions = $this->get_bulk_actions();

				/**
				 * Filters the items in the bulk actions menu of the list table.
				 *
				 * The dynamic portion of the hook name, `$this->screen->id`, refers
				 * to the ID of the current screen.
				 *
				 * @since 3.1.0
				 * @since 5.6.0 A bulk action can now contain an array of options in order to create an optgroup.
				 *
				 * @param array $actions An array of the available bulk actions.
				 */
				$this->_actions = apply_filters( "bulk_actions-{$this->screen->id}", $this->_actions ); // phpcs:ignore WordPress.NamingConventions.ValidHookName.UseUnderscores

				$two = '';
			} else {
				$two = '2';
			}

			if ( empty( $this->_actions ) ) {
				return;
			}

			echo '<label for="bulk-action-selector-' . esc_attr( $which ) . '" class="screen-reader-text">' .
			/* translators: Hidden accessibility text. */
			esc_html__( 'Select bulk action' ) . '</label>';
			echo '<select name="instant_winner_action' . esc_attr( $two ) . '" id="bulk-action-selector-' . esc_attr( $which ) . "\">\n";
			echo '<option value="-1">' . esc_html__( 'Bulk actions' ) . "</option>\n";

			foreach ( $this->_actions as $key => $value ) {
				if ( is_array( $value ) ) {
					echo "\t" . '<optgroup label="' . esc_attr( $key ) . '">' . "\n";

					foreach ( $value as $name => $title ) {
						$class = ( 'edit' === $name ) ? ' class="hide-if-no-js"' : '';

						echo "\t\t" . '<option value="' . esc_attr( $name ) . '"' . esc_attr( $class ) . '>' . esc_html( $title ) . "</option>\n";
					}
					echo "\t</optgroup>\n";
				} else {
					$class = ( 'edit' === $key ) ? ' class="hide-if-no-js"' : '';

					echo "\t" . '<option value="' . esc_attr( $key ) . '"' . esc_attr( $class ) . '>' . esc_html( $value ) . "</option>\n";
				}
			}

			echo "</select>\n";

			submit_button( __( 'Apply' ), 'action', 'instant_winner_bulk_action', false, array( 'id' => "doaction$two" ) );
			echo "\n";
		}

		/**
		 * Gets the current action selected from the bulk actions dropdown.
		 *
		 * @since 11.5.0
		 *
		 * @return string|false The action name. False if no action was selected.
		 */
		public function current_action() {
			if ( isset( $_REQUEST['filter_action'] ) && ! empty( $_REQUEST['filter_action'] ) ) {
				return false;
			}

			$instant_winner_action = isset( $_REQUEST['instant_winner_action'] ) ? wc_clean( wp_unslash( $_REQUEST['instant_winner_action'] ) ) : false;
			if ( $instant_winner_action && '-1' !== $instant_winner_action ) {
				return $instant_winner_action;
			}

			return false;
		}

		/**
		 * Processes the bulk action.
		 *
		 * @since 10.6.0
		 * */
		public function perform_bulk_action() {
			$ids = isset( $_REQUEST['instant_winner_id'] ) ? wc_clean( wp_unslash( $_REQUEST['instant_winner_id'] ) ) : array();
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
				switch ( $action ) {
					case 'delete':
						$instant_winner_log = lty_get_instant_winner_log( $id );
						if ( $instant_winner_log->exists() ) {
							lty_delete_instant_winner_rule( $instant_winner_log->get_rule_id() );
						}

						lty_delete_instant_winner_log( $id );
						break;

					case 'remove':
						lty_remove_lottery_instant_winner( $id );
						break;
				}
			}

			wp_safe_redirect( $this->current_url );
			exit();
		}

		/**
		 * Extra controls to be displayed between bulk actions and pagination.
		 *
		 * @since 9.8.0
		 * @param string $which Position to display.
		 * */
		protected function extra_tablenav( $which ) {
			if ( 'top' !== $which || ! $this->has_items() ) {
				return;
			}

			// Search Instant Winner lottery tickets.
			$instant_winner_orderby        = isset( $_REQUEST['instant_winner_orderby'] ) && ! empty( $_REQUEST['instant_winner_orderby'] ) ? wc_clean( wp_unslash( $_REQUEST['instant_winner_orderby'] ) ) : '';
			$instant_winner_order          = isset( $_REQUEST['instant_winner_order'] ) && ! empty( $_REQUEST['instant_winner_order'] ) ? wc_clean( wp_unslash( $_REQUEST['instant_winner_order'] ) ) : '';
			$instant_winner_post_mime_type = isset( $_REQUEST['instant_winner_post_mime_type'] ) && ! empty( $_REQUEST['instant_winner_post_mime_type'] ) ? wc_clean( wp_unslash( $_REQUEST['instant_winner_post_mime_type'] ) ) : '';
			$instant_winner_detached       = isset( $_REQUEST['instant_winner_detached'] ) && ! empty( $_REQUEST['instant_winner_detached'] ) ? wc_clean( wp_unslash( $_REQUEST['instant_winner_detached'] ) ) : '';

			$selected_filters = isset( $_REQUEST['lty_instant_winners_filters'] ) ? wc_clean( wp_unslash( $_REQUEST['lty_instant_winners_filters'] ) ) : array();
			$filter_values    = lty_get_formatted_instant_winners_filters( $selected_filters );

			include LTY_PLUGIN_PATH . '/inc/admin/menu/views/html-lottery-instant-winners-tickets-search.php';
		}

		/**
		 * Message to be displayed when there are no items.
		 *
		 * @since 8.0.0
		 */
		public function no_items() {
			esc_html_e( 'No data to show.', 'lottery-for-woocommerce' );
		}

		/**
		 * Display the list of views available on this table.
		 *
		 * @since 8.0.0
		 * @return array
		 * */
		protected function get_views() {
			$args         = array();
			$views        = array();
			$status_array = array(
				'all'           => __( 'All', 'lottery-instant-winners-prizes' ),
				'lty_available' => __( 'Available', 'lottery-instant-winners-prizes' ),
				'lty_pending'   => __( 'Pending', 'lottery-instant-winners-prizes' ),
				'lty_won'       => __( 'Won', 'lottery-instant-winners-prizes' ),
			);

			foreach ( $status_array as $status_name => $status_label ) {
				$status_count = $this->get_item_count_for_status( $status_name );
				if ( ! $status_count ) {
					continue;
				}

				$args['instant_winner_status'] = $status_name;
				$label                         = $status_label . ' (' . $status_count . ')';
				$class                         = array( strtolower( $status_name ) );
				if (isset($_GET['instant_winner_status']) && ( sanitize_title($_GET['instant_winner_status']) == $status_name )) { // @codingStandardsIgnoreLine.
					$class[] = 'current';
				}

				if (!isset($_GET['instant_winner_status']) && 'all' == $status_name) { // @codingStandardsIgnoreLine.
					$class[] = 'current';
				}

				$views[ $status_name ] = $this->get_edit_link( $args, $label, implode( ' ', $class ) );
			}

			return $views;
		}

		/**
		 * Get the current relist count.
		 *
		 * @since 8.1.0
		 * @global int $current_section
		 * @return int
		 */
		private function get_current_relist_count() {
			global $current_section;

			$relist_count = $this->product->get_current_relist_count();
			if ( $current_section ) {
				$relist_count = $relist_count - $current_section;
			}

			return $relist_count;
		}

		/**
		 * Get the item count for the status.
		 *
		 * @since 8.0.0
		 * @param string $status Status.
		 * @return int
		 */
		private function get_item_count_for_status( $status ) {
			$prepare_query = $this->database->prepare(
				'SELECT p.ID FROM ' . $this->database->posts . ' p
				INNER JOIN ' . $this->database->postmeta . ' pm1 ON p.ID = pm1.post_id
				INNER JOIN ' . $this->database->postmeta . " pm2 ON p.ID = pm2.post_id
				WHERE p.post_type = %s
				AND p.post_status IN('" . $this->format_status( $status ) . "')
				AND pm1.meta_key = %s AND pm1.meta_value = %s
				AND pm2.meta_key = %s AND pm2.meta_value = %s",
				$this->post_type,
				'lty_lottery_id',
				$this->product_id,
				'lty_current_relist_count',
				$this->get_current_relist_count()
			);

			$data = $this->database->get_results( $prepare_query, ARRAY_A );

			return count( $data );
		}

		/**
		 * Get the edit link for status.
		 *
		 * @since 8.0.0
		 * @param array  $args Arguments.
		 * @param string $label Label.
		 * @param string $class Class name.
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
		 * Prepare checkbox column data
		 *
		 * @since 10.6.0
		 * */
		protected function column_cb( $item ) {
			/* translators: %1$s: Ticket ID, %2$s: Ticket ID label */
			return sprintf( '<input type="checkbox" name="instant_winner_id[]" class="tips" value="%1$s" data-tip="%2$s: %1$s" />', $item->get_id(), __( 'Instant Winner ID', 'lottery-for-woocommerce' ) );
		}

		/**
		 * Prepare each column data.
		 *
		 * @since 8.0.0
		 * @param object $item instanceof LTY_Instant_Winner_Log.
		 * @param string $column_name Column name.
		 * @return string
		 * */
		protected function column_default( $item, $column_name ) {
			switch ( $column_name ) {
				case 'id':
					return $item->get_id();

				case 'ticket_number':
					return empty( $item->get_ticket_number() ) ? '-' : $item->get_ticket_number();

				case 'prize_type':
					return $item->get_prize_type_label( true );

				case 'winning_prize':
					return $item->get_prize_message();

				case 'user_details':
					if ( ! $item->exists() ) {
						return '-';
					}

					if ( ! $item->has_status( 'lty_won' ) || ! $item->get_ticket_id() ) {
						return '<span class="lty-instant-winner-prize-available">' . lty_get_instant_winners_prize_available_label() . '</span>';
					}

					return '<span class="lty-instant-winner-name">' . $item->get_user_name() . '</span><br/>(' . $item->get_user_email() . ')';

				case 'status':
					return lty_display_status( $item->get_status() );

				case 'order_details':
					if ( $item->has_status( 'lty_available' ) || ! $item->get_ticket_id() ) {
						return '-';
					}

					if ( empty( $item->get_order_id() ) || ! is_object( $item->get_order() ) ) {
						return '-';
					}

					$order_timestamp = '' !== $item->get_order()->get_date_created() ? $item->get_order()->get_date_created()->date( 'Y-m-d H:i:s' ) : '';
					if ( ! $order_timestamp ) {
						return '-';
					}

					$order_id     = sprintf( '<a href="%s">%s</a>', esc_url( get_edit_post_link( $item->get_order_id() ) ), esc_html( '#' . $item->get_order_id() ) );
					$order_status = ucfirst( $item->get_order()->get_status() );
					$order_date   = LTY_Date_Time::get_wp_format_datetime_from_gmt( $item->get_order()->get_date_created() );

					/* translators: %1s:order_id, %2s:order_status, %3s:order_date */
					return sprintf(
						'Order ID: %1s<br/>
						Order Status: %2s<br/>
						Order Date: %3s',
						$order_id,
						$order_status,
						$order_date
					);

				case 'action':
					$actions           = array();
					$actions['delete'] = lty_display_instant_winner_action_status( 'delete', $item->get_id(), $this->current_url, true );
					if ( $item->has_status( 'lty_won' ) ) {
						$actions['remove'] = lty_display_instant_winner_action_status( 'remove', $item->get_id(), $this->current_url, true );
					}

					echo wp_kses_post( implode( ' | ', $actions ) );
			}
		}

		/**
		 * Prepare the item Object.
		 *
		 * @since 8.0.0
		 * @param array $items Instance winner log items.
		 * @return void
		 * */
		private function prepare_item_object( $items ) {
			$prepare_items = array();
			if ( lty_check_is_array( $items ) ) {
				foreach ( $items as $item ) {
					$prepare_items[] = lty_get_instant_winner_log( $item['ID'] );
				}
			}

			$this->items = $prepare_items;
		}

		/**
		 * Get the query join clauses.
		 *
		 * @since 8.0.0
		 * @return string
		 * */
		private function get_query_join() {
			$join = ' INNER JOIN ' . $this->database->postmeta . ' AS pm ON ( pm.post_id = p.ID ) 
					INNER JOIN ' . $this->database->postmeta . ' AS pm1 ON ( pm1.post_id = p.ID ) 
					INNER JOIN ' . $this->database->postmeta . ' AS pm2 ON ( pm2.post_id = p.ID ) 
					INNER JOIN ' . $this->database->postmeta . ' AS pm3 ON ( pm3.post_id = p.ID )';

			/**
			 * This hook is used to alter the lottery ticket query join fields.
			 *
			 * @since 8.0.0
			 */
			return apply_filters( $this->list_slug . '_query_join', $join );
		}

		/**
		 * Get the query where clauses.
		 *
		 * @since 8.0.0
		 * @return string
		 * */
		private function get_query_where() {
			$current_status = 'all';
			if ( isset( $_GET['instant_winner_status'] ) && ( sanitize_title( $_GET['instant_winner_status'] ) !== 'all' ) ) {
				$current_status = sanitize_title( $_GET['instant_winner_status'] );
			}

			$where = " WHERE p.post_type = '" . $this->post_type . "'
					AND p.post_status IN('" . $this->format_status( $current_status ) . "')
					AND pm.meta_key = 'lty_lottery_id' AND pm.meta_value = '" . $this->product_id . "'
					AND pm1.meta_key = 'lty_current_relist_count' AND pm1.meta_value = '" . $this->get_current_relist_count() . "'";

			// Search.
			$where = $this->get_custom_search_query( $where );
			// Filters.
			$where = $this->get_custom_filters_query( $where );

			/**
			 * This hook is used to alter the lottery ticket query where fields.
			 *
			 * @since 8.0.0
			 */
			return apply_filters( $this->list_slug . '_query_where', $where );
		}

		/**
		 * Get the query limit clauses.
		 *
		 * @since 8.0.0
		 * @return string
		 * */
		private function get_query_limit() {
			/**
			 * This hook is used to alter the lottery ticket query limit fields.
			 *
			 * @since 8.0.0
			 */
			return apply_filters( $this->list_slug . '_query_limit', $this->perpage );
		}

		/**
		 * Get the query offset clauses.
		 *
		 * @since 8.0.0
		 * @return string
		 * */
		private function get_query_offset() {
			/**
			 * This hook is used to alter the lottery ticket query offset fields.
			 *
			 * @since 8.0.0
			 */
			return apply_filters( $this->list_slug . '_query_offset', $this->offset );
		}

		/**
		 * Get the query order by clauses.
		 *
		 * @since 8.0.0
		 * @return string
		 * */
		private function get_query_orderby() {
			$order = 'DESC';
			if (!empty($_REQUEST['instant_winner_order']) && is_string($_REQUEST['instant_winner_order'])) { // @codingStandardsIgnoreLine.
				if ('ASC' === strtoupper(wc_clean(wp_unslash($_REQUEST['instant_winner_order'])))) { // @codingStandardsIgnoreLine.
					$order = 'ASC';
				}
			}

			// Order By.
			if ( isset( $_REQUEST['instant_winner_orderby'] ) ) {
				switch (wc_clean(wp_unslash($_REQUEST['instant_winner_orderby']))) { // @codingStandardsIgnoreLine.
					case 'user_details':
						$this->orderby = " AND pm.meta_key='lty_user_name' ORDER BY pm.meta_value " . $order;
						break;

					case 'ticket_number':
						if ( 'alpha_numeric' === $this->product->get_ticket_number_orderby() ) {
							$this->orderby = " AND pm.meta_key='lty_ticket_number' ORDER BY pm.meta_value " . $order;
						} else {
							$this->orderby = " AND pm.meta_key='lty_ticket_number' ORDER BY CAST(pm.meta_value AS SIGNED) " . $order;
						}
						break;

					case 'status':
						$this->orderby = ' ORDER BY p.post_status ' . $order;
						break;
				}
			}

			/**
			 * This hook is used to alter the lottery ticket query order by fields.
			 *
			 * @since 8.0.0
			 */
			return apply_filters( $this->list_slug . '_query_orderby', $this->orderby );
		}

		/**
		 * Get custom search query.
		 *
		 * @since 8.0.0
		 * @param string $where Query WHERE.
		 * @return string
		 */
		public function get_custom_search_query( $where ) {
			if (! isset($_REQUEST['instant_winner_s']) || empty($_REQUEST['instant_winner_s'])) { // @codingStandardsIgnoreLine.
				return $where;
			}

			$terms            = explode(' , ', wc_clean(wp_unslash($_REQUEST['instant_winner_s']))); // @codingStandardsIgnoreLine.
			$selected_filters = isset( $_REQUEST['lty_instant_winners_filters'] ) ? wc_clean( wp_unslash( $_REQUEST['lty_instant_winners_filters'] ) ) : array();
			$filter_values    = lty_get_formatted_instant_winners_filters( $selected_filters );
			foreach ( $terms as $term ) {
				$term       = $this->database->esc_like( ( $term ) );
				$post_query = new LTY_Query( $this->database->prefix . 'posts', 'p' );
				$post_query->select( 'DISTINCT `p`.ID' )
						->leftJoin( $this->database->prefix . 'postmeta', 'pm', '`p`.`ID` = `pm`.`post_id`' )
						->where( '`p`.post_type', $this->post_type )
						->whereIn( '`p`.post_status', lty_get_instant_winner_log_statuses() )
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
			if ( ! isset( $_REQUEST['lty_instant_winners_filters'] ) ) { // @codingStandardsIgnoreLine.
				return $where;
			}

			$selected_filters = isset( $_REQUEST['lty_instant_winners_filters'] ) ? wc_clean( wp_unslash( $_REQUEST['lty_instant_winners_filters'] ) ) : array();
			$filter_values    = lty_get_formatted_instant_winners_filters( $selected_filters );
			// User filter.
			if ( ! empty( $filter_values['user_type'] ) ) {
				$user_filter_query = '1' === $filter_values['user_type'] ? ' pm3.meta_value > 0' : ' pm3.meta_value = 0';
				$where            .= " AND pm3.meta_key = 'lty_user_id' AND" . $user_filter_query;
			}

			if ( ! empty( $filter_values['purchased_date_filter_type'] ) ) {
				$date_filter = lty_get_date_filter_values( $filter_values['purchased_date_filter_type'], $filter_values['purchased_from_date'], $filter_values['purchased_to_date'] );
				$where       = ! empty( $filter_values['purchased_from_date'] ) ? $where . " AND p.post_date_gmt > '" . $date_filter['from_date'] . "'" : $where;
				$where       = ! empty( $filter_values['purchased_to_date'] ) ? $where . " AND p.post_date_gmt < '" . $date_filter['to_date'] . "'" : $where;
			}

			return $where;
		}

		/**
		 * Print column headers.
		 *
		 * @since 8.0.0
		 * @param bool $with_id Whether to print with ID or not.
		 */
		public function print_column_headers( $with_id = true ) {
			list( $columns, $hidden, $sortable, $primary ) = $this->get_column_info();
			$host            = isset( $_SERVER['HTTP_HOST'] ) ? wc_clean( $_SERVER['HTTP_HOST'] ) : '';
			$uri             = isset( $_SERVER['REQUEST_URI'] ) ? wc_clean( $_SERVER['REQUEST_URI'] ) : '';
			$current_url     = set_url_scheme( 'http://' . $host . $uri );
			$current_url     = remove_query_arg( 'paged', $current_url );
			$current_orderby = isset( $_GET['instant_winner_orderby'] ) ? wc_clean( wp_unslash( $_GET['instant_winner_orderby'] ) ) : '';
			$current_order   = 'asc';
			if ( isset( $_GET['instant_winner_order'] ) && 'desc' === $_GET['instant_winner_order'] ) {
				$current_order = 'desc';
			}

			if ( ! empty( $columns['cb'] ) ) {
				static $cb_counter = 1;
				$columns['cb']     = '<label class="screen-reader-text" for="cb-select-all-' . $cb_counter . '">' .
					/* translators: Hidden accessibility text. */
					__( 'Select All' ) .
					'</label>' .
					'<input id="cb-select-all-' . $cb_counter . '" type="checkbox" />';
				++$cb_counter;
			}

			foreach ( $columns as $column_key => $column_display_name ) {
				$class = array( 'manage-column', "column-$column_key" );
				if ( in_array( $column_key, $hidden, true ) ) {
					$class[] = 'hidden';
				}

				if ( 'cb' === $column_key ) {
					$class[] = 'check-column';
				} elseif ( in_array( $column_key, array( 'posts', 'comments', 'links' ), true ) ) {
					$class[] = 'num';
				}

				if ( $column_key === $primary ) {
					$class[] = 'column-primary';
				}

				if ( isset( $sortable[ $column_key ] ) ) {
					list( $instant_winner_orderby, $desc_first ) = $sortable[ $column_key ];

					if ( $current_orderby === $instant_winner_orderby ) {
						$instant_winner_order = 'asc' === $current_order ? 'desc' : 'asc';

						$class[] = 'sorted';
						$class[] = $current_order;
					} else {
						$instant_winner_order = strtolower( $desc_first );

						if ( ! in_array( $instant_winner_order, array( 'desc', 'asc' ), true ) ) {
							$instant_winner_order = $desc_first ? 'desc' : 'asc';
						}

						$class[] = 'sortable';
						$class[] = 'desc' === $instant_winner_order ? 'asc' : 'desc';
					}

					$column_display_name = sprintf(
						'<a href="%s"><span>%s</span><span class="sorting-indicator"></span></a>',
						esc_url( add_query_arg( compact( 'instant_winner_orderby', 'instant_winner_order' ), $current_url ) ),
						$column_display_name
					);
				}

				$tag   = ( 'cb' === $column_key ) ? 'td' : 'th';
				$scope = ( 'th' === $tag ) ? 'scope="col"' : '';
				$id    = $with_id ? "id='$column_key'" : '';

				if ( ! empty( $class ) ) {
					$class = "class='" . implode( ' ', $class ) . "'";
				}

				/* translators: %1s:tag, %2s:scope, %3s:id, %4s:class, %5s:column_display_name, %6s:tag */
				$html = sprintf(
					'<%1s %2s %3s %4s>%5s</%6s>',
					$tag,
					$scope,
					$id,
					$class,
					$column_display_name,
					$tag
				);

				echo do_shortcode( $html );
			}
		}

		/**
		 * Format the status.
		 *
		 * @since 9.2.0
		 * @param string|array $status Status to be formatted.
		 * @return array
		 */
		private function format_status( $status ) {
			if ( 'all' === $status ) {
				return implode( "', '", lty_get_instant_winner_log_statuses() );
			}

			return $status;
		}
	}

}
