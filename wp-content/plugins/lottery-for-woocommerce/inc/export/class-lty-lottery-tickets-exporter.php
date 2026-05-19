<?php

/**
 * Exporter - Lottery Tickets.
 *
 * @since 10.3.0
 */
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'LTY_Lottery_Tickets_Exporter' ) ) {

	/**
	 * Class.
	 *
	 * @since 10.3.0
	 */
	class LTY_Lottery_Tickets_Exporter extends LTY_Exporter {

		/**
		 * Filename to export to.
		 *
		 * @since 10.3.0
		 * @var string
		 */
		protected $filename = 'lty-ticket-lottery';

		/**
		 * Type of export used in filter names.
		 *
		 * @since 10.3.0
		 * @var string
		 */
		protected $export_type = 'lottery_tickets';

		/**
		 * Default custom field data.
		 *
		 * @since 11.9.0
		 * @var array
		 */
		protected $default_custom_field_data = array(
			'lottery_status' => false,
			'ticket_status'  => false,
		);

		/**
		 * Render custom field content.
		 *
		 * @since 11.9.0
		 */
		public function render_custom_field_content() {
			$export_tickets_type    = $this->get_extra_data_value( 'export_lottery' );
			$selected_ticket_status = $this->get_extra_data_value( 'status' ) ? array_filter( (array) $this->get_extra_data_value( 'status' ) ) : array();
			include __DIR__ . '/views/html-export-tickets-custom-fields.php';
		}

		/**
		 * Get the popup header label.
		 *
		 * @since 10.3.0
		 * @return string
		 */
		public function get_popup_header_label() {
			return __( 'Export for Giveaway Tickets', 'lottery-for-woocommerce' );
		}

		/**
		 * Get the exporting description.
		 *
		 * @since 10.3.0
		 * @return string
		 */
		public function get_exporting_description() {
			return __( 'Your giveaway tickets are now being exported...', 'lottery-for-woocommerce' );
		}

		/**
		 * Return default columns.
		 *
		 * @since 10.3.0
		 * @return array
		 */
		public function get_default_column_names() {
			$headings = array(
				'username'              => __( 'Username', 'lottery-for-woocommerce' ),
				'firstname'             => __( 'First Name', 'lottery-for-woocommerce' ),
				'lastname'              => __( 'Last Name', 'lottery-for-woocommerce' ),
				'email_id'              => __( 'User Email ID', 'lottery-for-woocommerce' ),
				'phone_number'          => __( 'Phone Number', 'lottery-for-woocommerce' ),
				'product_name'          => __( 'Product Name', 'lottery-for-woocommerce' ),
				'currency'              => __( 'Currency', 'lottery-for-woocommerce' ),
				'ticket_price'          => __( 'Ticket Price', 'lottery-for-woocommerce' ),
				'ticket_number'         => __( 'Ticket Number', 'lottery-for-woocommerce' ),
				'answer'                => __( 'Answer', 'lottery-for-woocommerce' ),
				'orderid'               => __( 'Order ID', 'lottery-for-woocommerce' ),
				'order_status'          => __( 'Order Status', 'lottery-for-woocommerce' ),
				'order_amount'          => __( 'Order Amount', 'lottery-for-woocommerce' ),
				'ticket_purchased_date' => __( 'Ticket Purchased Date', 'lottery-for-woocommerce' ),
				'status'                => __( 'Status', 'lottery-for-woocommerce' ),
				'billing_firstname'     => __( 'User Billing Fist Name', 'lottery-for-woocommerce' ),
				'billing_lastname'      => __( 'User Billing Last name', 'lottery-for-woocommerce' ),
				'billing_email_id'      => __( 'Billing Email address', 'lottery-for-woocommerce' ),
				'billing_phone_number'  => __( 'Billing Phone Number', 'lottery-for-woocommerce' ),
				'billing_company'       => __( 'Billing Company Name', 'lottery-for-woocommerce' ),
				'shipping_company'      => __( 'Shipping Company Name', 'lottery-for-woocommerce' ),
				'billing_country'       => __( 'Billing Country / Region', 'lottery-for-woocommerce' ),
				'billing_address1'      => __( 'Billing Address Line 1', 'lottery-for-woocommerce' ),
				'billing_address2'      => __( 'Billing Address Line 2', 'lottery-for-woocommerce' ),
				'billing_city'          => __( 'Billing Town / City', 'lottery-for-woocommerce' ),
				'billing_state'         => __( 'Billing State', 'lottery-for-woocommerce' ),
				'billing_postcode'      => __( 'Billing PIN Code', 'lottery-for-woocommerce' ),
			);

			if ( $this->get_lottery_product() && ! $this->get_lottery_product()->is_valid_question_answer() ) {
				unset( $headings['answer'] );
			}

			/**
			 * This hook is used to alter the lottery ticket export heading.
			 *
			 * @since 1.0.0
			 */
			return apply_filters( 'lty_lottery_ticket_export_heading', $headings );
		}

		/**
		 * Prepare overall data.
		 *
		 * @since 10.3.0
		 * @return array
		 */
		protected function prepare_overall_data() {
			return $this->get_lottery_ticket_ids();
		}

		/**
		 * Format data that will be exported.
		 *
		 * @since 10.3.0
		 */
		protected function format_data_to_export() {
			foreach ( $this->get_chunked_data() as $ticket_id ) {
				$lottery_ticket = lty_get_lottery_ticket( $ticket_id );
				/**
				 * This hook is used to alter the export lottery ticket custom data.
				 *
				 * @since 10.6.0
				 * @param bool Custom data.
				 * @param int Ticket ID.
				 * @param object Ticket object.
				 * @param object Product object.
				 */
				$custom_row_data = apply_filters( 'lty_export_lottery_ticket_custom_data', false, $ticket_id, $lottery_ticket, $this->get_lottery_product() );
				if ( $custom_row_data ) {
					$this->row_data[] = $custom_row_data;
					continue;
				}

				if ( ! is_object( $lottery_ticket ) ) {
					continue;
				}

				$this->row_data[] = self::generate_row_data( $lottery_ticket );
			}
		}

		/**
		 * Get lottery ticket data.
		 *
		 * @return array
		 */
		protected function generate_row_data( $lottery_ticket ) {
			// Order Status.
			$order         = wc_get_order( $lottery_ticket->get_order_id() );
			$orderstatus   = is_object( $order ) ? $order->get_status() : '-';
			$ticket_number = (string) $lottery_ticket->get_lottery_ticket_number();
			$row           = array(
				'username'              => esc_html( $lottery_ticket->get_user_name() ),
				'firstname'             => esc_html( $lottery_ticket->get_first_name() ),
				'lastname'              => esc_html( $lottery_ticket->get_last_name() ),
				'email_id'              => esc_html( $lottery_ticket->get_user_email() ),
				'phone_number'          => is_object( $order ) ? esc_html( $order->get_billing_phone() ) : '',
				'product_name'          => $lottery_ticket->get_product_id() ? esc_html( get_the_title( $lottery_ticket->get_product_id() ) ) : '',
				'currency'              => esc_html( $lottery_ticket->get_currency() ),
				'ticket_price'          => esc_html( $lottery_ticket->get_amount() ),
				'ticket_number'         => esc_html( $ticket_number ),
				'answer'                => esc_html( $lottery_ticket->get_answer() ),
				'orderid'               => esc_html( '#' . $lottery_ticket->get_order_id() ),
				'order_status'          => esc_html( ucfirst( $orderstatus ) ),
				'order_amount'          => is_object( $order ) ? esc_html( $order->get_total() ) : '',
				'ticket_purchased_date' => esc_html( $lottery_ticket->get_formatted_created_date() ),
				'status'                => esc_html( lty_display_status( $lottery_ticket->get_status(), false ) ),
				'billing_firstname'     => is_object( $order ) ? esc_html( $order->get_billing_first_name() ) : '',
				'billing_lastname'      => is_object( $order ) ? esc_html( $order->get_billing_last_name() ) : '',
				'billing_email_id'      => is_object( $order ) ? esc_html( $order->get_billing_email() ) : '',
				'billing_phone_number'  => is_object( $order ) ? esc_html( $order->get_billing_phone() ) : '',
				'billing_company'       => is_object( $order ) ? esc_html( $order->get_billing_company() ) : '',
				'shipping_company'      => is_object( $order ) ? esc_html( $order->get_shipping_company() ) : '',
				'billing_country'       => is_object( $order ) ? esc_html( $order->get_billing_country() ) : '',
				'billing_address1'      => is_object( $order ) ? esc_html( $order->get_billing_address_1() ) : '',
				'billing_address2'      => is_object( $order ) ? esc_html( $order->get_billing_address_2() ) : '',
				'billing_city'          => is_object( $order ) ? esc_html( $order->get_billing_city() ) : '',
				'billing_state'         => is_object( $order ) ? esc_html( $order->get_billing_state() ) : '',
				'billing_postcode'      => is_object( $order ) ? esc_html( $order->get_billing_postcode() ) : '',
			);

			if ( $this->get_lottery_product() && ! $this->get_lottery_product()->is_valid_question_answer() ) {
				unset( $row['answer'] );
			}

			/**
			 * This hook is used to alter the lottery ticket export row data.
			 *
			 * @since 1.0
			 */
			return apply_filters( 'lty_lottery_ticket_export_row_data', $row, $lottery_ticket );
		}

		/**
		 * Get the lottery product.
		 *
		 * @since 10.3.0
		 * @return bool/object WC_Product
		 */
		public function get_lottery_product() {
			if ( ! $this->get_extra_data_value( 'product_id' ) ) {
				return false;
			}

			return wc_get_product( $this->get_extra_data_value( 'product_id' ) );
		}

		/**
		 * Get the lottery ticket ids.
		 *
		 * @since 1.0.0
		 * @return array
		 */
		protected function get_lottery_ticket_ids() {
			$args = array( 'post_status' => $this->get_custom_field_data_value( 'ticket_status' ) ? array_filter( (array) $this->get_custom_field_data_value( 'ticket_status' ) ) : array_merge( lty_get_ticket_statuses(), array( 'lty_ticket_canceled' ) ) );

			switch ( $this->get_extra_data_value( 'export_lottery' ) ) {
				case 'section':
					$args['product_id'] = $this->get_lottery_product()->get_id();
					if ( ! empty( $this->get_extra_data_value( 'section' ) ) ) {
						$relist_data = array_reverse( $this->get_lottery_product()->get_lty_relists() );
						$list_count  = count( $relist_data ) - intval( $this->get_section() );
						if ( $this->get_lottery_product()->is_unlimited_scheduled_lottery( $list_count ) ) {
							$args['list_count'] = $relist_data[ $this->get_section() - 1 ]['list_count'];
						} else {
							$args['start_date'] = $relist_data[ $this->get_section() - 1 ]['start_date_gmt'];
							$args['end_date']   = $relist_data[ $this->get_section() - 1 ]['end_date_gmt'];
						}
					} elseif ( $this->get_lottery_product()->is_unlimited_scheduled_lottery() ) {
						$args['list_count'] = $this->get_lottery_product()->get_current_relist_count();
					} else {
						$args['start_date'] = $this->get_lottery_product()->get_current_start_date_gmt();
					}
					break;

				case 'single':
					$args['product_id'] = $this->get_lottery_product()->get_id();
					break;

				case 'all':
					$args['product_id'] = $this->get_lottery_ids();
					break;
			}

			/**
			 * This hook is used to alter the export lottery ticket IDs.
			 *
			 * @since 10.6.0
			 * @param array Ticket ID's.
			 * @param int|array   Product ID.
			 */
			return apply_filters( 'lty_export_lottery_ticket_ids', lty_get_ticket_ids( $args ), $args['product_id'] );
		}

		/**
		 * Get the lottery statuses.
		 *
		 * @since 11.9.0
		 * @return array
		 */
		private function get_lottery_statuses() {
			$lottery_status = $this->get_custom_field_data_value( 'lottery_status' );
			if ( ! $lottery_status ) {
				return array_keys( lty_get_lottery_statuses() );
			}

			return lty_check_is_array( $lottery_status ) ? $lottery_status : array( $lottery_status );
		}

		/**
		 * Get the lottery IDs.
		 *
		 * @since 11.9.0
		 * @return array
		 */
		private function get_lottery_ids() {
			return get_posts(
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
					'meta_query' => array(
						array(
							'key'     => '_lty_lottery_status',
							'value'   => $this->get_lottery_statuses(),
							'compare' => 'IN',
						),
					),
				)
			);
		}
	}

}
