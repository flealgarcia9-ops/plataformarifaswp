<?php

/**
 * Importer - Instant winner Rule.
 *
 * @since 9.9.0
 */
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'LTY_Instant_Winner_Rule_Importer' ) ) {

	/**
	 * Class.
	 *
	 * @since 9.9.0
	 */
	class LTY_Instant_Winner_Rule_Importer extends LTY_Importer {

		/**
		 * Action type.
		 *
		 * @since 9.9.0
		 * @var string
		 */
		protected $action_type = 'instant-winner-rule';

		/**
		 * Product.
		 *
		 * @since 9.9.0
		 * @var object
		 */
		private $product;

		/**
		 * Instant winner rule ticket numbers.
		 *
		 * @since 10.9.0
		 * @var array
		 */
		private $instant_winner_rule_ticket_numbers;

		/**
		 * Instant winner rule object.
		 *
		 * @since 11.1.0
		 * @var object
		 */
		private $instant_winner_rule;

		/**
		 * Prize group object.
		 *
		 * @since 11.1.0
		 * @var object
		 */
		private $prize_group;

		/**
		 * Get the popup header label.
		 *
		 * @since 9.9.0
		 * @return string
		 */
		public function get_popup_header_label() {
			return __( 'Import for Instant Win Prizes Rules', 'lottery-for-woocommerce' );
		}

		/**
		 * Get upload file description.
		 *
		 * @since 9.9.0
		 * @return string
		 */
		protected function get_upload_file_description() {
			return __( 'If you import new instant win data, then no need to mention the instant win field id and product id in the CSV file. If you want to update the existing data, you should mention the instant win field id in the CSV file.', 'lottery-for-woocommerce' );
		}

		/**
		 * Get the map columns.
		 *
		 * @since 9.9.0
		 * @return array
		 */
		public function get_map_columns() {
			return array(
				__( 'ID', 'lottery-for-woocommerce' )    => 'id',
				__( 'Ticket Number', 'lottery-for-woocommerce' ) => 'ticket_number',
				__( 'Group Prize Title', 'lottery-for-woocommerce' ) => 'prize_group_title',
				__( 'Image ID', 'lottery-for-woocommerce' ) => 'image_id',
				__( 'Prize Type', 'lottery-for-woocommerce' ) => 'prize_type',
				__( 'Coupon Generation Type', 'lottery-for-woocommerce' ) => 'coupon_generation_type',
				__( 'Coupon Discount Type', 'lottery-for-woocommerce' ) => 'coupon_discount_type',
				__( 'Coupon ID', 'lottery-for-woocommerce' ) => 'coupon_id',
				__( 'Prize Amount', 'lottery-for-woocommerce' ) => 'prize_amount',
				__( 'Gift Product ID', 'lottery-for-woocommerce' ) => 'gift_product_id',
				__( 'Gift Product Quantity', 'lottery-for-woocommerce' ) => 'gift_product_quantity',
				__( 'Prize', 'lottery-for-woocommerce' ) => 'prize',
			);
		}

		/**
		 * Process item.
		 *
		 * @since 9.9.0
		 */
		protected function process_item( $item ) {
			$wp_error = $this->validate_item( $item );
			if ( ! empty( $wp_error ) ) {
				return $wp_error;
			}

			$result = array();

			try {
				if ( '2' === $this->get_display_mode() ) {
					if ( ! $this->prize_group->exists() ) {
						throw new Exception( esc_html__( 'Invalid Instant Win Group Prize', 'lottery-for-woocommerce' ) );
					}

					$rule_data = array(
						'lty_image_id'               => $this->prize_group->get_image_id(),
						'lty_prize_type'             => $this->prize_group->get_prize_type(),
						'lty_coupon_generation_type' => $this->prize_group->get_coupon_generation_type(),
						'lty_coupon_discount_type'   => $this->prize_group->get_coupon_discount_type(),
						'lty_coupon_id'              => $this->prize_group->get_coupon_id(),
						'lty_prize_amount'           => $this->prize_group->get_prize_amount(),
						'lty_gift_product_id'        => $this->prize_group->get_gift_product_id(),
						'lty_gift_product_quantity'  => $this->prize_group->get_gift_product_quantity(),
						'lty_instant_winner_prize'   => $this->prize_group->get_prize_message(),
						'lty_prize_group_id'         => $this->prize_group->get_id(),
					);
				} else {
					$rule_data = array(
						'lty_image_id'               => $item['image_id'],
						'lty_prize_type'             => $item['prize_type'],
						'lty_coupon_generation_type' => $item['coupon_generation_type'],
						'lty_coupon_discount_type'   => $item['coupon_discount_type'],
						'lty_coupon_id'              => $item['coupon_id'],
						'lty_prize_amount'           => $item['prize_amount'],
						'lty_gift_product_id'        => $item['gift_product_id'],
						'lty_gift_product_quantity'  => $item['gift_product_quantity'],
						'lty_instant_winner_prize'   => $item['prize'],
					);
				}

				$rule_data['lty_ticket_number'] = $item['ticket_number'];

				$instant_winner_rule_id = isset( $item['id'] ) ? absint( $item['id'] ) : '';
				$relist_count           = is_callable( array( $this->get_product(), 'get_current_relist_count' ) ) ? $this->get_product()->get_current_relist_count() : 0;
				if ( $this->instant_winner_rule->exists() ) {
					$instant_winner_log_id = lty_get_instant_winner_log_id_by_rule_id( $instant_winner_rule_id, $relist_count );
					if ( ! $instant_winner_log_id ) {
						throw new Exception( esc_html__( 'Instant Winner log does not exists', 'lottery-for-woocommerce' ) );
					}

					lty_update_instant_winner_rule( $instant_winner_rule_id, $rule_data );
					lty_update_instant_winner_log( $instant_winner_log_id, array_merge( $rule_data, array( 'lty_current_relist_count' => $relist_count ) ), array( 'post_parent' => $instant_winner_rule_id ) );

					$result['updated'] = $instant_winner_rule_id;
				} else {
					$instant_winner_rule_id = lty_create_new_instant_winner_rule( $rule_data, array( 'post_parent' => $this->get_product_id() ) );
					lty_create_new_instant_winner_log( array_merge( $rule_data, array( 'lty_current_relist_count' => $relist_count ) ), array( 'post_parent' => $instant_winner_rule_id ) );

					$result['imported'] = $instant_winner_rule_id;
				}
			} catch ( Exception $ex ) {
				$result = new WP_Error( 'lty_instant_winner_rule_importer_error', $ex->getMessage() );
			}

			return $result;
		}

		/**
		 * Validate item.
		 *
		 * @since 9.9.0
		 */
		protected function validate_item( $item ) {
			if ( ! $this->get_product() ) {
				return new WP_Error( 'lty_instant_winner_rule_importer_error', esc_html__( 'Product ID does not valid', 'lottery-for-woocommerce' ) );
			}

			$id                        = isset( $item['id'] ) ? absint( $item['id'] ) : '';
			$this->instant_winner_rule = lty_get_instant_winner_rule( $id );
			// Validate if the ID does not exists when enabled update existing data.
			if ( ! empty( $id ) && ! $this->instant_winner_rule->exists() ) {
				return new WP_Error( 'lty_instant_winner_rule_importer_error', esc_html__( 'Instant Win Id is not match', 'lottery-for-woocommerce' ) );
			}

			// Validate if the ticket number is required.
			if ( empty( $item['ticket_number'] ) ) {
				return new WP_Error( 'lty_instant_winner_rule_importer_error', esc_html__( 'Ticket Number is empty', 'lottery-for-woocommerce' ) );
			}

			$ticket_numbers = $this->get_instant_winner_rule_ticket_numbers();
			// Validate if the ticket number is already used.
			if ( empty( $id ) && in_array( $item['ticket_number'], $ticket_numbers ) ) {
				return new WP_Error( 'lty_instant_winner_rule_importer_error', esc_html__( 'Ticket Number is already exists', 'lottery-for-woocommerce' ) );
			}

			if ( '2' === $this->get_display_mode() ) {
				if ( empty( $item['prize_group_title'] ) ) {
					return new WP_Error( 'lty_instant_winner_rule_importer_error', esc_html__( 'Group Prize Title is empty', 'lottery-for-woocommerce' ) );
				}

				$prize_group_id    = lty_get_instant_winner_prize_group_id_by_title( $item['prize_group_title'], $this->get_product_id() );
				$this->prize_group = lty_get_instant_winner_prize_group( $prize_group_id );
				if ( ! $this->prize_group->exists() ) {
					$error_message = $this->validate_data( $item );
					if ( false !== $error_message ) {
						return new WP_Error( 'lty_instant_winner_rule_importer_error', $error_message );
					}

					$prize_group_data = array(
						'lty_image_id'               => ! empty( $item['image_id'] ) ? absint( $item['image_id'] ) : '',
						'lty_prize_type'             => $item['prize_type'],
						'lty_coupon_generation_type' => ! empty( $item['coupon_generation_type'] ) ? $item['coupon_generation_type'] : '',
						'lty_coupon_discount_type'   => ! empty( $item['coupon_discount_type'] ) ? $item['coupon_discount_type'] : '',
						'lty_coupon_id'              => ! empty( $item['coupon_id'] ) ? absint( $item['coupon_id'] ) : '',
						'lty_prize_amount'           => ! empty( $item['prize_amount'] ) ? wc_format_decimal( $item['prize_amount'] ) : '',
						'lty_gift_product_id'        => ! empty( $item['gift_product_id'] ) ? absint( $item['gift_product_id'] ) : '',
						'lty_gift_product_quantity'  => ! empty( $item['gift_product_quantity'] ) ? absint( $item['gift_product_quantity'] ) : '1',
						'lty_prize_message'          => ! empty( $item['prize'] ) ? $item['prize'] : '',
					);

					$new_prize_group_id = lty_create_new_instant_winner_prize_group(
						$prize_group_data,
						array(
							'post_title'  => ! empty( $item['prize_group_title'] ) ? $item['prize_group_title'] : 'Untitled',
							'post_parent' => $this->get_product_id(),
						)
					);

					$this->prize_group = lty_get_instant_winner_prize_group( $new_prize_group_id );
				}
			} else {
				$error_message = $this->validate_data( $item );
				if ( false !== $error_message ) {
					return new WP_Error( 'lty_instant_winner_rule_importer_error', $error_message );
				}
			}

			/**
			 * This hook is used to do extra action after the instant winner rule import item validation.
			 * 
			 * @since 11.1.0
			 * @param bool Whether is valid or not.
			 * @param array $item Import item.
			 */
			return apply_filters( 'lty_validate_instant_winner_rule_import_item', false, $item );
		}

		/**
		 * Validate the instant winner prize data.
		 * 
		 * @since 11.1.0
		 * @param array $item Item to be validated.
		 */
		private function validate_data( $item ) {
			// Return if the prize type is empty.
			if ( empty( $item['prize_type'] ) ) {
				return __( 'Prize Type value is empty', 'lottery-for-woocommerce' );
			}

			switch ( $item['prize_type'] ) {
				case 'coupon':
					// Return if the new coupon prize amount is empty.
					if ( '1' === $item['coupon_generation_type'] && empty( $item['prize_amount'] ) ) {
						return __( 'Prize Amount is empty', 'lottery-for-woocommerce' );
					}

					// Return if the existing coupon id is empty.
					if ( '2' === $item['coupon_generation_type'] && empty( $item['coupon_id'] ) ) {
						return __( 'Coupon ID is empty', 'lottery-for-woocommerce' );
					}
					break;

				case 'product':
					// Return if the gift product ID is empty.
					if ( '' === $item['gift_product_id'] ) {
						return __( 'Gift Product ID is empty', 'lottery-for-woocommerce' );
					}

					// Return if the gift product quantity is empty.
					if ( '' === $item['gift_product_quantity'] ) {
						return __( 'Gift Product Quantity is empty', 'lottery-for-woocommerce' );
					}

					$gift_product = wc_get_product( $item['gift_product_id'] );
					if ( ! is_object( $gift_product ) ) {
						return __( 'Invalid Gift Product ID', 'lottery-for-woocommerce' );
					}

					// Return if the gift product is out of stock.
					if ( $gift_product->managing_stock() && ! $gift_product->backorders_allowed() && ( ! $gift_product->is_in_stock() || $item['gift_product_quantity'] > $gift_product->get_stock_quantity() ) ) {
						return str_replace( '{product_name}', $gift_product->get_title(), __( 'Gift Product({product_name}) is out of stock', 'lottery-for-woocommerce' ) );
					} elseif ( ! $gift_product->is_in_stock() && ! $gift_product->backorders_allowed() ) {
						return str_replace( '{product_name}', $gift_product->get_title(), __( 'Gift Product({product_name}) is out of stock', 'lottery-for-woocommerce' ) );
					}
					break;

				default:
					// Return if the prize amount is empty.
					if ( 'physical' !== $item['prize_type'] && empty( $item['prize_amount'] ) ) {
						return __( 'Prize Amount is empty', 'lottery-for-woocommerce' );
					}
					break;
			}

			// Return if the prize message is empty.
			if ( empty( $item['prize'] ) ) {
				return __( 'Prize value is empty', 'lottery-for-woocommerce' );
			}

			/**
			 * This hook is used to do extra action after the instant winner rule import item data validation.
			 * 
			 * @since 11.1.0
			 * @param bool Whether is valid or not.
			 * @param array $item Import item.
			 * @param int Product ID.
			 * @param string Display mode.
			 */
			return apply_filters( 'lty_validate_instant_winner_rule_import_item_data', false, $item, $this->get_product_id(), $this->get_display_mode() );
		}

		/**
		 * Get the product.
		 *
		 * @since 9.9.0
		 * @return string
		 */
		private function get_product() {
			if ( isset( $this->product ) ) {
				return $this->product;
			}

			$this->product = wc_get_product( $this->get_product_id() );

			return $this->product;
		}

		/**
		 * Get the product ID.
		 *
		 * @since 9.9.0
		 * @return string
		 */
		public function get_product_id() {
			$extra_data = $this->get_extra_data();

			return isset( $extra_data['product_id'] ) ? $extra_data['product_id'] : '';
		}

		/**
		 * Get the instant winner rule ticket numbers.
		 *
		 * @since 10.9.0
		 * @return array
		 */
		private function get_instant_winner_rule_ticket_numbers() {
			if ( isset( $this->instant_winner_rule_ticket_numbers ) ) {
				return $this->instant_winner_rule_ticket_numbers;
			}

			$this->instant_winner_rule_ticket_numbers = lty_get_instant_winner_rule_ticket_numbers( $this->get_product_id() );

			return $this->instant_winner_rule_ticket_numbers;
		}

		/**
		 * Get the prize display mode.
		 *
		 * @since 11.1.0
		 * @return string
		 */
		private function get_display_mode() {
			$extra_data = $this->get_extra_data();

			return isset( $extra_data['display_mode'] ) ? $extra_data['display_mode'] : '1';
		}
	}
}
