<?php
/**
 * Exporter - Instant winner rules.
 *
 * @since 10.3.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'LTY_Instant_Winner_Rules_Exporter' ) ) {

	/**
	 * Class.
	 *
	 * @since 10.3.0
	 */
	class LTY_Instant_Winner_Rules_Exporter extends LTY_Exporter {

		/**
		 * Filename to export to.
		 *
		 * @since 10.3.0
		 * @var string
		 */
		protected $filename = 'instant-winner-rules';

		/**
		 * Type of export used in filter names.
		 *
		 * @since 10.3.0
		 * @var string
		 */
		protected $export_type = 'instant_winner_rules';

		/**
		 * Get the popup header label.
		 *
		 * @since 10.3.0
		 * @return string
		 */
		public function get_popup_header_label() {
			return __( 'Export for Instant Winner Rules', 'lottery-for-woocommerce' );
		}

		/**
		 * Get the exporting description.
		 *
		 * @since 10.3.0
		 * @return string
		 */
		public function get_exporting_description() {
			return __( 'Your Instant winner rules are now being exported...', 'lottery-for-woocommerce' );
		}

		/**
		 * Return default columns.
		 *
		 * @since 10.3.0
		 * @return array
		 */
		public function get_default_column_names() {
			/**
			 * This hook is used to alter the instant winner rules export heading.
			 *
			 * @since 10.3.0
			 */
			return apply_filters(
				'lty_instant_winner_rules_export_heading',
				array(
					'id'                     => __( 'ID', 'lottery-for-woocommerce' ),
					'product_id'             => __( 'Product ID', 'lottery-for-woocommerce' ),
					'image_id'               => __( 'Image ID', 'lottery-for-woocommerce' ),
					'ticket_number'          => __( 'Ticket Number', 'lottery-for-woocommerce' ),
					'prize_type'             => __( 'Prize Type', 'lottery-for-woocommerce' ),
					'coupon_generation_type' => __( 'Coupon Generation Type', 'lottery-for-woocommerce' ),
					'coupon_discount_type'   => __( 'Coupon Discount Type', 'lottery-for-woocommerce' ),
					'coupon_id'              => __( 'Coupon ID', 'lottery-for-woocommerce' ),
					'prize_amount'           => __( 'Prize Amount', 'lottery-for-woocommerce' ),
					'gift_product_id'        => __( 'Gift Product ID', 'lottery-for-woocommerce' ),
					'gift_product_quantity'  => __( 'Gift Product Quantity', 'lottery-for-woocommerce' ),
					'prize_group_title'      => __( 'Group Prize Title', 'lottery-for-woocommerce' ),
					'prize'                  => __( 'Prize', 'lottery-for-woocommerce' ),
				)
			);
		}

		/**
		 * Prepare overall data.
		 *
		 * @since 10.3.0
		 * @return array
		 */
		protected function prepare_overall_data() {
			return lty_get_instant_winner_rule_ids( $this->get_product_id() );
		}

		/**
		 * Format data that will be exported.
		 *
		 * @since 10.3.0
		 */
		protected function format_data_to_export() {
			foreach ( $this->get_chunked_data() as $instant_winner_rule_id ) {
				$instant_winner_rule = lty_get_instant_winner_rule( $instant_winner_rule_id );
				if ( ! is_object( $instant_winner_rule ) ) {
					continue;
				}

				$this->row_data[] = self::generate_row_data( $instant_winner_rule );
			}
		}

		/**
		 * Get the instant winner rule data.
		 *
		 * @since 10.3.0
		 * @param object $instant_winner_rule Instant winner rule object.
		 * @return array
		 */
		protected function generate_row_data( $instant_winner_rule ) {
			/**
			 * This hook is used to alter the instant winner rule export row data.
			 *
			 * @since 10.3.0
			 */
			return apply_filters(
				'lty_instant_winner_rule_export_row_data',
				array(
					'id'                     => esc_html( $instant_winner_rule->get_id() ),
					'product_id'             => esc_html( $instant_winner_rule->get_product_id() ),
					'image_id'               => esc_html( $instant_winner_rule->get_image_id() ),
					'ticket_number'          => esc_html( $instant_winner_rule->get_ticket_number() ),
					'prize_type'             => esc_html( $instant_winner_rule->get_prize_type() ),
					'coupon_generation_type' => esc_html( $instant_winner_rule->get_coupon_generation_type() ),
					'coupon_discount_type'   => esc_html( $instant_winner_rule->get_coupon_discount_type() ),
					'coupon_id'              => esc_html( $instant_winner_rule->get_coupon_id() ),
					'prize_amount'           => esc_html( $instant_winner_rule->get_prize_amount() ),
					'gift_product_id'        => esc_html( $instant_winner_rule->get_gift_product_id() ),
					'gift_product_quantity'  => esc_html( $instant_winner_rule->get_gift_product_quantity() ),
					'prize_group_title'      => esc_html( $instant_winner_rule->get_prize_group_title() ),
					'prize'                  => esc_html( $instant_winner_rule->get_prize_message() ),
				),
				$instant_winner_rule
			);
		}

		/**
		 * Get the lottery product ID.
		 *
		 * @since 10.3.0
		 * @return int
		 */
		public function get_product_id() {
			if ( ! $this->get_extra_data_value( 'product_id' ) ) {
				return false;
			}

			return $this->get_extra_data_value( 'product_id' );
		}
	}
}
