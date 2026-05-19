<?php

/**
 * Lottery Instant Winner Rule.
 *
 * @since 8.0.0
 * */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'LTY_Instant_Winner_Rule' ) ) {

	/**
	 * Class.
	 *
	 * @since 8.0.0
	 * */
	class LTY_Instant_Winner_Rule extends LTY_Post {

		/**
		 * Post Type.
		 *
		 * @var string
		 * @since 8.0.0
		 * */
		protected $post_type = LTY_Register_Post_Types::LOTTERY_INSTANT_WINNER_RULE_POSTTYPE;

		/**
		 * Post Status.
		 *
		 * @var string
		 * @since 8.0.0
		 * */
		protected $post_status = 'publish';

		/**
		 * Product ID.
		 *
		 * @var string
		 * @since 8.0.0
		 * */
		protected $product_id;

		/**
		 * Product.
		 *
		 * @var object
		 * @since 8.0.0
		 * */
		protected $product;

		/**
		 * Created date.
		 *
		 * @var string
		 * @since 8.0.0
		 * */
		protected $created_date;

		/**
		 * Meta data keys.
		 *
		 * @since 8.0.0
		 * */
		protected $meta_data_keys = array(
			'lty_image_id'               => '',
			'lty_ticket_number'          => '',
			'lty_prize_type'             => '',
			'lty_coupon_generation_type' => '',
			'lty_coupon_discount_type'   => '',
			'lty_coupon_id'              => '',
			'lty_gift_product_id'        => '',
			'lty_gift_product_quantity'  => '',
			'lty_prize_amount'           => '',
			'lty_prize_group_id'         => '',
			'lty_instant_winner_prize'   => '',
		);

		/**
		 * Duplicate meta data keys.
		 *
		 * @since 11.1.0
		 * @var array
		 */
		protected $duplicate_meta_keys = array(
			'lty_image_id'               => '',
			'lty_ticket_number'          => '',
			'lty_prize_type'             => '',
			'lty_coupon_generation_type' => '',
			'lty_coupon_discount_type'   => '',
			'lty_coupon_id'              => '',
			'lty_gift_product_id'        => '',
			'lty_gift_product_quantity'  => '',
			'lty_prize_amount'           => '',
			'lty_prize_group_id'         => '',
			'lty_instant_winner_prize'   => '',
		);

		/**
		 * Prepare extra post data.
		 *
		 * @since 8.0.0
		 */
		protected function load_extra_postdata() {
			$this->product_id   = $this->post->post_parent;
			$this->created_date = $this->post->post_date_gmt;
		}

		/**
		 * Get formatted created datetime.
		 *
		 * @since 8.0.0
		 * @return string
		 * */
		public function get_formatted_created_date() {
			return LTY_Date_Time::get_wp_format_datetime_from_gmt( $this->get_created_date() );
		}

		/**
		 * Get the Product.
		 *
		 * @since 8.0.0
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
		 * @since 10.6.0
		 * @return string
		 * */
		public function get_image_url() {
			return empty( $this->get_image_id() ) ? wc_placeholder_img_src() : wp_get_attachment_url( $this->get_image_id() );
		}

		/**
		 * Get the prize group title.
		 *
		 * @since 11.1.0
		 * @return string
		 * */
		public function get_prize_group_title() {
			if ( ! $this->get_prize_group_id() ) {
				return '';
			}

			$prize_group = lty_get_instant_winner_prize_group( $this->get_prize_group_id() );
			if ( ! $prize_group->exists() ) {
				return '';
			}

			return $prize_group->get_title(); 
		}

		/**
		 * ----------------------------------------------------------------
		 * Setters.
		 * ----------------------------------------------------------------
		 * Functions to set the instant winner rule data.
		 */

		/**
		 * Set Product ID.
		 *
		 * @since 8.0.0
		 * @param string $value
		 * */
		public function set_product_id( $value ) {
			$this->product_id = $value;
		}

		/**
		 * Set created date.
		 *
		 * @since 8.0.0
		 * @param string $value
		 */
		public function set_created_date( $value ) {
			$this->created_date = $value;
		}

		/**
		 * Set image ID.
		 *
		 * @since 10.6.0
		 * @param string $value Image ID.
		 * */
		public function set_image_id( $value ) {
			$this->set_prop( 'lty_image_id', $value );
		}

		/**
		 * Set Ticket Number.
		 *
		 * @since 8.0.0
		 * @param string $value
		 * */
		public function set_ticket_number( $value ) {
			$this->set_prop( 'lty_ticket_number', $value );
		}

		/**
		 * Set prize type.
		 *
		 * @since 10.6.0
		 * @param string $value Prize type.
		 * */
		public function set_prize_type( $value ) {
			$this->set_prop( 'lty_prize_type', $value );
		}

		/**
		 * Set coupon generation type.
		 *
		 * @since 10.6.0
		 * @param string $value Coupon generation type.
		 * */
		public function set_coupon_generation_type( $value ) {
			$this->set_prop( 'lty_coupon_generation_type', $value );
		}

		/**
		 * Set coupon discount type.
		 *
		 * @since 10.6.0
		 * @param string $value Coupon discount type.
		 * */
		public function set_coupon_discount_type( $value ) {
			$this->set_prop( 'lty_coupon_discount_type', $value );
		}

		/**
		 * Set coupon ID.
		 *
		 * @since 10.6.0
		 * @param string $coupon_id Coupon ID.
		 * */
		public function set_coupon_id( $coupon_id ) {
			$this->set_prop( 'lty_coupon_id', $coupon_id );
		}

		/**
		 * Set gift product ID.
		 *
		 * @since 11.5.0
		 * @param int $value Gift product ID.
		 * */
		public function set_gift_product_id( $value ) {
			$this->set_prop( 'lty_gift_product_id', $value );
		}

		/**
		 * Set gift product quantity.
		 *
		 * @since 11.5.0
		 * @param int $value Gift product quantity.
		 * */
		public function set_gift_product_quantity( $value ) {
			$this->set_prop( 'lty_gift_product_quantity', $value );
		}

		/**
		 * Set prize amount.
		 *
		 * @since 10.6.0
		 * @param string $value Prize amount.
		 * */
		public function set_prize_amount( $value ) {
			$this->set_prop( 'lty_prize_amount', $value );
		}

		/**
		 * Set prize group ID.
		 *
		 * @since 11.1.0
		 * @param int $value Prize group ID.
		 * */
		public function set_prize_group_id( $value ) {
			$this->set_prop( 'lty_prize_group_id', $value );
		}

		/**
		 * Set prize message.
		 *
		 * @since 10.6.0
		 * @param string $value Prize message.
		 * */
		public function set_prize_message( $value ) {
			$this->set_prop( 'lty_instant_winner_prize', $value );
		}

		/**
		 * ----------------------------------------------------------------
		 * Getters.
		 * ----------------------------------------------------------------
		 * Functions to get the instant winner rule data.
		 */

		/**
		 * Get Product ID.
		 *
		 * @since 8.0.0
		 * @return string
		 * */
		public function get_product_id() {
			return $this->product_id;
		}

		/**
		 * Get created date.
		 *
		 * @since 8.0.0
		 * @return string
		 * */
		public function get_created_date() {
			return $this->created_date;
		}

		/**
		 * Get Ticket Number.
		 *
		 * @since 8.0.0
		 * @return string
		 * */
		public function get_ticket_number() {
			return $this->get_prop( 'lty_ticket_number' );
		}

		/**
		 * Get image ID.
		 *
		 * @since 10.6.0
		 * @return string
		 * */
		public function get_image_id() {
			return $this->get_prop( 'lty_image_id' );
		}

		/**
		 * Get prize type.
		 *
		 * @since 10.6.0
		 * @return string
		 * */
		public function get_prize_type() {
			return $this->get_prop( 'lty_prize_type' );
		}

		/**
		 * Get coupon generation type.
		 *
		 * @since 10.6.0
		 * @return string
		 * */
		public function get_coupon_generation_type() {
			return $this->get_prop( 'lty_coupon_generation_type' );
		}

		/**
		 * Get coupon discount type.
		 *
		 * @since 10.6.0
		 * @return string
		 * */
		public function get_coupon_discount_type() {
			return $this->get_prop( 'lty_coupon_discount_type' );
		}

		/**
		 * Get coupon ID.
		 *
		 * @since 10.6.0
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
		 * @since 10.6.0
		 * @return string
		 * */
		public function get_prize_amount() {
			return $this->get_prop( 'lty_prize_amount' );
		}

		/**
		 * Set prize group ID.
		 *
		 * @since 11.1.0
		 * @return int
		 * */
		public function get_prize_group_id() {
			return $this->get_prop( 'lty_prize_group_id' );
		}

		/**
		 * Get prize message.
		 *
		 * @since 10.6.0
		 * @return string
		 * */
		public function get_prize_message() {
			return $this->get_prop( 'lty_instant_winner_prize' );
		}
	}
}
