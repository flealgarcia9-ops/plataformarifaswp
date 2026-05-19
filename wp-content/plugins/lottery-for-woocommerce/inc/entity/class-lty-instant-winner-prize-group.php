<?php
/**
 * Lottery instant winner prize group.
 *
 * @since 11.1.0
 * */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

if ( ! class_exists( 'LTY_Instant_Winner_Prize_Group' ) ) {

	/**
	 * Class.
	 *
	 * @since 11.1.0
	 * */
	class LTY_Instant_Winner_Prize_Group extends LTY_Post {

		/**
		 * Post type.
		 *
		 * @since 11.1.0
		 * @var string
		 * */
		protected $post_type = LTY_Register_Post_Types::LOTTERY_INSTANT_WINNER_PRIZE_GROUP_POST_TYPE;

		/**
		 * Post status.
		 *
		 * @since 11.1.0
		 * @var string
		 * */
		protected $post_status = 'publish';

		/**
		 * Title.
		 *
		 * @since 11.1.0
		 * @var string
		 * */
		protected $title;

		/**
		 * Product ID.
		 *
		 * @since 11.1.0
		 * @var string
		 * */
		protected $product_id;

		/**
		 * Product object.
		 *
		 * @since 11.1.0
		 * @var object
		 * */
		protected $product;

		/**
		 * Created date.
		 *
		 * @since 11.1.0
		 * @var string
		 * */
		protected $created_date;

		/**
		 * Meta data keys.
		 *
		 * @since 11.1.0
		 * @var array
		 * */
		protected $meta_data_keys = array(
			'lty_image_id'               => '',
			'lty_prize_type'             => '',
			'lty_coupon_generation_type' => '',
			'lty_coupon_discount_type'   => '',
			'lty_coupon_id'              => '',
			'lty_gift_product_id'        => '',
			'lty_gift_product_quantity'  => '',
			'lty_prize_amount'           => '',
			'lty_prize_message'          => '',
		);

		/**
		 * Duplicate meta data keys.
		 *
		 * @since 11.1.0
		 * @var array
		 */
		protected $duplicate_meta_keys = array(
			'lty_image_id'               => '',
			'lty_prize_type'             => '',
			'lty_coupon_generation_type' => '',
			'lty_coupon_discount_type'   => '',
			'lty_coupon_id'              => '',
			'lty_gift_product_id'        => '',
			'lty_gift_product_quantity'  => '',
			'lty_prize_amount'           => '',
			'lty_prize_message'          => '',
		);

		/**
		 * Prepare extra post data.
		 *
		 * @since 11.1.0
		 */
		protected function load_extra_postdata() {
			$this->title        = $this->post->post_title;
			$this->product_id   = $this->post->post_parent;
			$this->created_date = $this->post->post_date_gmt;
		}

		/**
		 * Get the title.
		 *
		 * @since 11.1.0
		 * @return string
		 * */
		public function get_title() {
			return $this->title;
		}

		/**
		 * Get formatted created datetime.
		 *
		 * @since 11.1.0
		 * @return string
		 * */
		public function get_formatted_created_date() {
			return LTY_Date_Time::get_wp_format_datetime_from_gmt( $this->get_created_date() );
		}

		/**
		 * Get the Product.
		 *
		 * @since 11.1.0
		 * @return object
		 * */
		public function get_product() {
			if ( isset( $this->product ) ) {
				return $this->product;
			}

			$this->product = wc_get_product( $this->get_product_id() );

			return $this->product;
		}

		/**
		 * Get image URL.
		 *
		 * @since 11.1.0
		 * @return string
		 * */
		public function get_image_url() {
			return empty( $this->get_image_id() ) ? wc_placeholder_img_src() : wp_get_attachment_url( $this->get_image_id() );
		}

		/**
		 * Get instant winner log IDs.
		 *
		 * @since 11.9.0
		 * @return array
		 * */
		public function get_instant_winner_log_ids() {
			return lty_get_instant_winner_log_ids_by_group_id( $this->get_id(), $this->get_product()->get_current_relist_count() );
		}

		/**
		 * Get instant winner log IDs count.
		 *
		 * @since 11.9.0
		 * @return int
		 * */
		public function get_instant_winner_log_ids_count() {
			return lty_check_is_array( $this->get_instant_winner_log_ids() ) ? count( $this->get_instant_winner_log_ids() ) : 0;
		}

		/**
		 * ----------------------------------------------------------------
		 * Setters.
		 * ----------------------------------------------------------------
		 * Functions to set the instant winner prize group data.
		 */

		/**
		 * Set product ID.
		 *
		 * @since 11.1.0
		 * @param string $value Product ID.
		 * */
		public function set_product_id( $value ) {
			$this->product_id = $value;
		}

		/**
		 * Set created date.
		 *
		 * @since 11.1.0
		 * @param string $value Created date.
		 */
		public function set_created_date( $value ) {
			$this->created_date = $value;
		}

		/**
		 * Set image ID.
		 *
		 * @since 11.1.0
		 * @param string $value Image ID.
		 * */
		public function set_image_id( $value ) {
			$this->set_prop( 'lty_image_id', $value );
		}

		/**
		 * Set prize type.
		 *
		 * @since 11.1.0
		 * @param string $value Prize type.
		 * */
		public function set_prize_type( $value ) {
			$this->set_prop( 'lty_prize_type', $value );
		}

		/**
		 * Set coupon generation type.
		 *
		 * @since 11.1.0
		 * @param string $value Coupon generation type.
		 * */
		public function set_coupon_generation_type( $value ) {
			$this->set_prop( 'lty_coupon_generation_type', $value );
		}

		/**
		 * Set coupon discount type.
		 *
		 * @since 11.1.0
		 * @param string $value Coupon discount type.
		 * */
		public function set_coupon_discount_type( $value ) {
			$this->set_prop( 'lty_coupon_discount_type', $value );
		}

		/**
		 * Set coupon ID.
		 *
		 * @since 11.1.0
		 * @param string $coupon_id Coupon ID.
		 * */
		public function set_coupon_id( $coupon_id ) {
			$this->set_prop( 'lty_coupon_id', $coupon_id );
		}

		/**
		 * Set prize amount.
		 *
		 * @since 11.1.0
		 * @param string $value Prize amount.
		 * */
		public function set_prize_amount( $value ) {
			$this->set_prop( 'lty_prize_amount', $value );
		}

		/**
		 * Set prize message.
		 *
		 * @since 11.1.0
		 * @param string $value Prize message.
		 * */
		public function set_prize_message( $value ) {
			$this->set_prop( 'lty_prize_message', $value );
		}

		/**
		 * ----------------------------------------------------------------
		 * Getters.
		 * ----------------------------------------------------------------
		 * Functions to get the instant winner prize group data.
		 */

		/**
		 * Get Product ID.
		 *
		 * @since 11.1.0
		 * @return string
		 * */
		public function get_product_id() {
			return $this->product_id;
		}

		/**
		 * Get created date.
		 *
		 * @since 11.1.0
		 * @return string
		 * */
		public function get_created_date() {
			return $this->created_date;
		}

		/**
		 * Get image ID.
		 *
		 * @since 11.1.0
		 * @return string
		 * */
		public function get_image_id() {
			return $this->get_prop( 'lty_image_id' );
		}

		/**
		 * Get prize type.
		 *
		 * @since 11.1.0
		 * @return string
		 * */
		public function get_prize_type() {
			return $this->get_prop( 'lty_prize_type' );
		}

		/**
		 * Get coupon generation type.
		 *
		 * @since 11.1.0
		 * @return string
		 * */
		public function get_coupon_generation_type() {
			return $this->get_prop( 'lty_coupon_generation_type' );
		}

		/**
		 * Get coupon discount type.
		 *
		 * @since 11.1.0
		 * @return string
		 * */
		public function get_coupon_discount_type() {
			return $this->get_prop( 'lty_coupon_discount_type' );
		}

		/**
		 * Get coupon ID.
		 *
		 * @since 11.1.0
		 * @return string
		 * */
		public function get_coupon_id() {
			return $this->get_prop( 'lty_coupon_id' );
		}

		/**
		 * Get gift product ID.
		 *
		 * @since 11.5.0
		 * @return int
		 * */
		public function get_gift_product_id() {
			return $this->get_prop( 'lty_gift_product_id' );
		}

		/**
		 * Get gift product quantity.
		 *
		 * @since 11.5.0
		 * @return int
		 * */
		public function get_gift_product_quantity() {
			return $this->get_prop( 'lty_gift_product_quantity' );
		}

		/**
		 * Get prize amount.
		 *
		 * @since 11.1.0
		 * @return string
		 * */
		public function get_prize_amount() {
			return $this->get_prop( 'lty_prize_amount' );
		}

		/**
		 * Get prize message.
		 *
		 * @since 11.1.0
		 * @return string
		 * */
		public function get_prize_message() {
			return $this->get_prop( 'lty_prize_message' );
		}
	}
}
