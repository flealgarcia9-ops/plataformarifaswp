<?php
/**
 * Importer - Instant winner prize group.
 *
 * @since 11.1.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'LTY_Instant_Winner_Prize_Group_Importer' ) ) {

	/**
	 * Class.
	 *
	 * @since 11.1.0
	 */
	class LTY_Instant_Winner_Prize_Group_Importer extends LTY_Importer {

		/**
		 * Action type.
		 *
		 * @since 11.1.0
		 * @var string
		 */
		protected $action_type = 'instant_winner_prize_groups';

		/**
		 * Product.
		 *
		 * @since 11.1.0
		 * @var object
		 */
		private $product;

		/**
		 * Prize group.
		 *
		 * @since 11.1.0
		 * @var object
		 */
		private $prize_group;

		/**
		 * Get the popup header label.
		 *
		 * @since 11.1.0
		 * @return string
		 */
		public function get_popup_header_label() {
			return __( 'Importing Instant Win Prize Groups', 'lottery-for-woocommerce' );
		}

		/**
		 * Get upload file description.
		 *
		 * @since 11.1.0
		 * @return string
		 */
		protected function get_upload_file_description() {
			return __( 'If you are importing new Instant Win Prize Groups, leave the "ID" column empty. The "ID" is only required for updating existing prize groups.', 'lottery-for-woocommerce' );
		}

		/**
		 * Get the map columns.
		 *
		 * @since 11.1.0
		 * @return array
		 */
		public function get_map_columns() {
			return array(
				__( 'ID', 'lottery-for-woocommerce' ) => 'id',
				__( 'Group Prize Title', 'lottery-for-woocommerce' ) => 'title',
				__( 'Image ID', 'lottery-for-woocommerce' ) => 'image_id',
				__( 'Prize Type', 'lottery-for-woocommerce' ) => 'prize_type',
				__( 'Coupon Generation Type', 'lottery-for-woocommerce' ) => 'coupon_generation_type',
				__( 'Coupon Discount Type', 'lottery-for-woocommerce' ) => 'coupon_discount_type',
				__( 'Coupon ID', 'lottery-for-woocommerce' ) => 'coupon_id',
				__( 'Prize Amount', 'lottery-for-woocommerce' ) => 'prize_amount',
				__( 'Gift Product ID', 'lottery-for-woocommerce' ) => 'gift_product_id',
				__( 'Gift Product Quantity', 'lottery-for-woocommerce' ) => 'gift_product_quantity',
				__( 'Prize Message', 'lottery-for-woocommerce' ) => 'prize_message',
			);
		}

		/**
		 * Process item.
		 *
		 * @since 11.1.0
		 * @param array $item Item to be processed.
		 * @throws Exception If processing fails.
		 */
		protected function process_item( $item ) {
			$wp_error = $this->validate_item( $item );
			if ( ! empty( $wp_error ) ) {
				return $wp_error;
			}

			$result = array();

			try {
				$prize_group_data = array(
					'lty_image_id'               => $item['image_id'],
					'lty_prize_type'             => $item['prize_type'],
					'lty_coupon_generation_type' => $item['coupon_generation_type'],
					'lty_coupon_discount_type'   => $item['coupon_discount_type'],
					'lty_coupon_id'              => $item['coupon_id'],
					'lty_prize_amount'           => $item['prize_amount'],
					'lty_gift_product_id'        => $item['gift_product_id'],
					'lty_gift_product_quantity'  => $item['gift_product_quantity'],
					'lty_prize_message'          => $item['prize_message'],
				);

				$prize_group_id = isset( $item['id'] ) ? absint( $item['id'] ) : '';
				if ( $prize_group_id && $this->prize_group->exists() ) {
					lty_update_instant_winner_prize_group( $prize_group_id, $prize_group_data, array( 'post_title' => $item['title'] ) );
					$result['updated'] = $prize_group_id;
				} else {
					$prize_group_id     = lty_create_new_instant_winner_prize_group(
						$prize_group_data,
						array(
							'post_parent' => $this->get_product_id(),
							'post_title'  => $item['title'],
						)
					);
					$result['imported'] = $prize_group_id;
				}
			} catch ( Exception $ex ) {
				$result = new WP_Error( 'lty_instant_winner_prize_group_importer_error', $ex->getMessage() );
			}

			return $result;
		}

		/**
		 * Validate item.
		 *
		 * @since 11.1.0
		 * @param array $item Item to be validated.
		 * @return object|bool
		 */
		protected function validate_item( $item ) {
			if ( ! $this->get_product() ) {
				return new WP_Error( 'lty_instant_winner_prize_group_importer_error', esc_html__( 'Product ID does not valid', 'lottery-for-woocommerce' ) );
			}

			$prize_group_id    = isset( $item['id'] ) ? absint( $item['id'] ) : '';
			$this->prize_group = lty_get_instant_winner_prize_group( $prize_group_id );
			// Return if the ID does not exists when enabled update existing data.
			if ( ! empty( $prize_group_id ) && ! $this->prize_group->exists() ) {
				return new WP_Error( 'lty_instant_winner_prize_group_importer_error', esc_html__( 'Instant Win Id is not match', 'lottery-for-woocommerce' ) );
			}

			// Return if the title is empty.
			if ( empty( $item['title'] ) ) {
				return new WP_Error( 'lty_instant_winner_prize_group_importer_error', esc_html__( 'Title is empty', 'lottery-for-woocommerce' ) );
			}

			// Return if the prize type is empty.
			if ( empty( $item['prize_type'] ) ) {
				return new WP_Error( 'lty_instant_winner_prize_group_importer_error', esc_html__( 'Prize Type value is empty', 'lottery-for-woocommerce' ) );
			}

			switch ( $item['prize_type'] ) {
				case 'coupon':
					// Return if the new Coupon value is empty.
					if ( '1' === $item['coupon_generation_type'] && empty( $item['prize_amount'] ) ) {
						return new WP_Error( 'lty_instant_winner_prize_group_importer_error', esc_html__( 'Prize Amount is empty', 'lottery-for-woocommerce' ) );
					}

					// Return if the existing Coupon ID is empty.
					if ( '2' === $item['coupon_generation_type'] && empty( $item['coupon_id'] ) ) {
						return new WP_Error( 'lty_instant_winner_prize_group_importer_error', esc_html__( 'Coupon ID is empty', 'lottery-for-woocommerce' ) );
					}
					break;

				case 'product':
					// Return if the gift product ID is empty.
					if ( '' === $item['gift_product_id'] ) {
						return new WP_Error( 'lty_instant_winner_prize_group_importer_error', esc_html__( 'Gift Product ID is empty', 'lottery-for-woocommerce' ) );
					}

					// Return if the gift product quantity is empty.
					if ( '' === $item['gift_product_quantity'] ) {
						return new WP_Error( 'lty_instant_winner_prize_group_importer_error', esc_html__( 'Gift Product Quantity is empty', 'lottery-for-woocommerce' ) );
					}

					$gift_product = wc_get_product( $item['gift_product_id'] );
					if ( ! is_object( $gift_product ) ) {
						return new WP_Error( 'lty_instant_winner_prize_group_importer_error', esc_html__( 'Invalid Gift Product ID', 'lottery-for-woocommerce' ) );
					}

					// Return if the gift product is out of stock.
					if ( $gift_product->managing_stock() && ! $gift_product->backorders_allowed() && ( ! $gift_product->is_in_stock() || $item['gift_product_quantity'] > $gift_product->get_stock_quantity() ) ) {
						return new WP_Error( 'lty_instant_winner_prize_group_importer_error', str_replace( '{product_name}', $gift_product->get_title(), __( 'Gift Product({product_name}) is out of stock', 'lottery-for-woocommerce' ) ) );
					} elseif ( ! $gift_product->is_in_stock() && ! $gift_product->backorders_allowed() ) {
						return new WP_Error( 'lty_instant_winner_prize_group_importer_error', str_replace( '{product_name}', $gift_product->get_title(), __( 'Gift Product({product_name}) is out of stock', 'lottery-for-woocommerce' ) ) );
					}
					break;

				default:
					// Return if the prize amount is empty.
					if ( 'physical' !== $item['prize_type'] && empty( $item['prize_amount'] ) ) {
						return new WP_Error( 'lty_instant_winner_prize_group_importer_error', esc_html__( 'Prize Amount is empty', 'lottery-for-woocommerce' ) );
					}
					break;
			}

			// Return if the prize message is empty.
			if ( empty( $item['prize_message'] ) ) {
				return new WP_Error( 'lty_instant_winner_prize_group_importer_error', esc_html__( 'Prize Message is empty', 'lottery-for-woocommerce' ) );
			}

			/**
			 * This hook is used to do extra action after the instant winner prize group import item validation.
			 *
			 * @since 11.1.0
			 * @param bool Whether is valid or not.
			 * @param array $item Import item.
			 */
			return apply_filters( 'lty_validate_instant_winner_prize_group_import_item', false, $item );
		}

		/**
		 * Get the product.
		 *
		 * @since 11.1.0
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
		 * @since 11.1.0
		 * @return string
		 */
		private function get_product_id() {
			$extra_data = $this->get_extra_data();

			return isset( $extra_data['product_id'] ) ? $extra_data['product_id'] : '';
		}
	}
}
