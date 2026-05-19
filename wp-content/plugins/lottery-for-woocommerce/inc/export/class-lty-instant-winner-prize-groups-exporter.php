<?php
/**
 * Exporter - Instant winner prize groups.
 *
 * @since 11.1.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'LTY_Instant_Winner_Prize_Groups_Exporter' ) ) {

	/**
	 * Class.
	 *
	 * @since 11.1.0
	 */
	class LTY_Instant_Winner_Prize_Groups_Exporter extends LTY_Exporter {

		/**
		 * Filename to export to.
		 *
		 * @since 11.1.0
		 * @var string
		 */
		protected $filename = 'instant-winner-prize-groups';

		/**
		 * Type of export used in filter names.
		 *
		 * @since 11.1.0
		 * @var string
		 */
		protected $export_type = 'instant_winner_prize_groups';

		/**
		 * Get the popup header label.
		 *
		 * @since 11.1.0
		 * @return string
		 */
		public function get_popup_header_label() {
			return __( 'Exporting Instant Winner Prize Groups', 'lottery-for-woocommerce' );
		}

		/**
		 * Get the exporting description.
		 *
		 * @since 11.1.0
		 * @return string
		 */
		public function get_exporting_description() {
			return __( 'Your Instant winner prize groups are now being exported...', 'lottery-for-woocommerce' );
		}

		/**
		 * Return default columns.
		 *
		 * @since 11.1.0
		 * @return array
		 */
		public function get_default_column_names() {
			/**
			 * This hook is used to alter the instant winner prize groups export heading.
			 *
			 * @since 11.1.0
			 */
			return apply_filters(
				'lty_instant_winner_prize_groups_export_heading',
				array(
					'id'                     => __( 'ID', 'lottery-for-woocommerce' ),
					'product_id'             => __( 'Product ID', 'lottery-for-woocommerce' ),
					'title'                  => __( 'Group Prize Title', 'lottery-for-woocommerce' ),
					'image_id'               => __( 'Image ID', 'lottery-for-woocommerce' ),
					'prize_type'             => __( 'Prize Type', 'lottery-for-woocommerce' ),
					'coupon_generation_type' => __( 'Coupon Generation Type', 'lottery-for-woocommerce' ),
					'coupon_discount_type'   => __( 'Coupon Discount Type', 'lottery-for-woocommerce' ),
					'coupon_id'              => __( 'Coupon ID', 'lottery-for-woocommerce' ),
					'prize_amount'           => __( 'Prize Amount', 'lottery-for-woocommerce' ),
					'prize_message'          => __( 'Prize Message', 'lottery-for-woocommerce' ),
				)
			);
		}

		/**
		 * Prepare overall data.
		 *
		 * @since 11.1.0
		 * @return array
		 */
		protected function prepare_overall_data() {
			if ( ! lty_is_lottery_product( $this->get_product_id() ) ) {
				return false;
			}

			return lty_get_instant_winner_prize_group_ids( $this->get_product_id() );
		}

		/**
		 * Format data that will be exported.
		 *
		 * @since 11.1.0
		 */
		protected function format_data_to_export() {
			foreach ( $this->get_chunked_data() as $prize_group_id ) {
				$prize_group = lty_get_instant_winner_prize_group( $prize_group_id );
				if ( ! $prize_group->exists() ) {
					continue;
				}

				$this->row_data[] = self::generate_row_data( $prize_group );
			}
		}

		/**
		 * Get the instant winner prize group data.
		 *
		 * @since 11.1.0
		 * @param object $prize_group Instant winner prize group object.
		 * @return array
		 */
		protected function generate_row_data( $prize_group ) {
			/**
			 * This hook is used to alter the instant winner prize group export row data.
			 *
			 * @since 11.1.0
			 */
			return apply_filters(
				'lty_instant_winner_prize_group_export_row_data',
				array(
					'id'                     => esc_html( $prize_group->get_id() ),
					'product_id'             => esc_html( $prize_group->get_product_id() ),
					'title'                  => esc_html( $prize_group->get_title() ),
					'image_id'               => esc_html( $prize_group->get_image_id() ),
					'prize_type'             => esc_html( $prize_group->get_prize_type() ),
					'coupon_generation_type' => esc_html( $prize_group->get_coupon_generation_type() ),
					'coupon_discount_type'   => esc_html( $prize_group->get_coupon_discount_type() ),
					'coupon_id'              => esc_html( $prize_group->get_coupon_id() ),
					'prize_amount'           => esc_html( $prize_group->get_prize_amount() ),
					'prize_message'          => esc_html( $prize_group->get_prize_message() ),
				),
				$prize_group
			);
		}

		/**
		 * Get the product ID.
		 *
		 * @since 11.1.0
		 * @return string
		 */
		private function get_product_id() {
			if ( ! $this->get_extra_data_value( 'product_id' ) ) {
				return false;
			}

			return $this->get_extra_data_value( 'product_id' );
		}
	}
}
