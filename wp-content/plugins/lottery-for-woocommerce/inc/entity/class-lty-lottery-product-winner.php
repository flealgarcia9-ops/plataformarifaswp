<?php

/**
 * Lottery Winner.
 * */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'LTY_Lottery_Product_Winner' ) ) {

	/**
	 * LTY_Lottery_Product_Winner Class.
	 * */
	class LTY_Lottery_Product_Winner extends LTY_Post {

		/**
		 * Post Type.
		 *
		 * @var string
		 * */
		protected $post_type = LTY_Register_Post_Types::LOTTERY_WINNER_POSTTYPE;

		/**
		 * Post Status.
		 *
		 * @var string
		 * */
		protected $post_status = 'lty_publish';

		/**
		 * User.
		 *
		 * @var object
		 * */
		protected $user;

		/**
		 * Product ID.
		 *
		 * @var string
		 * */
		protected $product_id;

		/**
		 * Product.
		 *
		 * @var object
		 * */
		protected $product;

		/**
		 * Order.
		 *
		 * @var object
		 * */
		protected $order;

		/**
		 * Created date.
		 *
		 * @var string
		 * */
		protected $created_date;

		/**
		 * Meta data keys.
		 * */
		protected $meta_data_keys = array(
			'lty_user_id'        => '',
			'lty_gift_products'  => array(),
			'lty_user_name'      => '',
			'lty_user_email'     => '',
			'lty_order_id'       => '',
			'lty_ticket_number'  => '',
			'lty_answer'         => '',
			'lty_answers'        => array(),
			'lty_valid_answer'   => '',
			'lty_start_date'     => '',
			'lty_start_date_gmt' => '',
			'lty_end_date'       => '',
			'lty_end_date_gmt'   => '',
			'lty_winning_method' => '',
			'lty_lottery_schedule_type'  => '',
			'lty_list_count'   => '',
		);

		/**
		 * Prepare extra post data.
		 */
		protected function load_extra_postdata() {
			$this->product_id   = $this->post->post_parent;
			$this->created_date = $this->post->post_date_gmt;
		}

		/**
		 * Display user name.
		 *
		 * @since 1.0.0
		 * @return string
		 * */
		public function display_user_name() {
			$display_type = get_option( 'lty_settings_single_product_tab_details_username_display_type', 1 );
			switch ( $display_type ) {
				case '3':
					$username = is_object( $this->get_ticket_order() ) ? $this->get_ticket_order()->get_billing_first_name() : $this->get_user_name();
					break;

				case '2':
					$username = is_object( $this->get_ticket_order() ) ? $this->get_ticket_order()->get_formatted_billing_full_name() : $this->get_user_name();
					break;

				default:
					if ( ! $this->get_user_id() ) {
						$username = is_object( $this->get_ticket_order() ) ? $this->get_ticket_order()->get_formatted_billing_full_name() : '';
					} else {
						$username = $this->get_user_name();
					}

					break;
			}

			if ( 'yes' == get_option( 'lty_settings_single_product_lottery_mask_winner_username' ) ) {
				$username = lty_mask_name( $username );
			}

			if ( ! $this->get_user_id() ) {
				$username = $username . '[' . __( 'Guest', 'lottery-for-woocommerce' ) . ']';
			}

			return $username;
		}

		/**
		 * Get the winning details.
		 *
		 * @since 8.0.0
		 * @return string
		 */
		public function get_winning_details() {
			if ( ! $this->get_product() || 'lottery' !== $this->get_product()->get_type() ) {
				return '';
			}

			$winning_details = array();
			if ( '1' === $this->get_product()->get_winner_product_selection_method() ) :
				$gift_products = array_unique( $this->get_gift_products() );
				foreach ( $gift_products as $product_id ) :
					$gift_product = wc_get_product( $product_id );
					if ( ! is_object( $gift_product ) ) {
						continue;
					}

					$winning_details[] = $gift_product->get_title();
				endforeach;
			else :
				$winning_details = array_unique( $this->get_gift_products() );
			endif;

			return esc_html( implode( ' , ', $winning_details ) );
		}

		/**
		 * Get the ticket order.
		 *
		 * @since 7.5.0
		 * @return object|false
		 */
		public function get_ticket_order() {
			if ( $this->is_unlimited_scheduled_lottery() ) {
				$ticket_ids = lty_check_is_ticket_number_exists( array( $this->get_lottery_ticket_number() ), $this->get_product_id(), false, false, intval( $this->get_list_count() ) );
			} else {
				$ticket_ids = lty_check_is_ticket_number_exists( array( $this->get_lottery_ticket_number() ), $this->get_product_id(), $this->get_lottery_start_date_gmt(), $this->get_lottery_end_date_gmt() );
			}

			if ( ! lty_check_is_array( $ticket_ids ) ) {
				return false;
			}

			foreach ( $ticket_ids as $ticket_id ) {
				$ticket = lty_get_lottery_ticket( $ticket_id );
				if ( ! $ticket->exists() || ! is_object( $ticket->get_order() ) ) {
					continue;
				}

				return $ticket->get_order();
			}

			return false;
		}

		/**
		 * Get formatted created datetime.
		 * */
		public function get_formatted_created_date() {
			return LTY_Date_Time::get_wp_format_datetime_from_gmt( $this->get_created_date() );
		}

		/**
		 * Get the User.
		 * */
		public function get_user() {
			if ( isset( $this->user ) ) {
				return $this->user;
			}

			$this->user = get_user_by( 'ID', $this->get_user_id() );

			return $this->user;
		}

		/**
		 * Get the Product.
		 *
		 * @since 8.0.0
		 * @param bool $linkable Whether to return the product name as linkable or not.
		 * @return string
		 * */
		public function get_product_name( $linkable = false ) {
			if ( ! is_object( $this->get_product() ) ) {
				return '';
			}
			if ( ! $linkable ) {
				return $this->get_product()->get_title();
			}

			return sprintf( '<a href="%s">%s</a>', esc_url( $this->get_product()->get_permalink() ), esc_html( $this->get_product()->get_title() ) );
		}

		/**
		 * Get the Product.
		 *
		 * @return object/boolean
		 * */
		public function get_product() {
			if ( isset( $this->product ) ) {
				return $this->product;
			}

			$this->product = wc_get_product( $this->get_product_id() );

			return $this->product;
		}

		/**
		 * Get the Order.
		 * */
		public function get_order() {
			if ( isset( $this->order ) ) {
				return $this->order;
			}

			$this->order = wc_get_order( $this->get_order_id() );

			return $this->order;
		}

		/**
		 * Is unlimited scheduled lottery?
		 *
		 * @since 11.7.0
		 * @return bool
		 */
		public function is_unlimited_scheduled_lottery() {
			return '2' === $this->get_lottery_schedule_type();
		}

		/**
		 * ----------------------------------------------------------------
		 * Setters.
		 * ----------------------------------------------------------------
		 * Functions to set the lottery winner data.
		 */

		/**
		 * Set Product ID.
		 * */
		public function set_product_id( $value ) {
			$this->product_id = $value;
		}

		/**
		 * Set created date
		 */
		public function set_created_date( $value ) {
			$this->created_date = $value;
		}

		/**
		 * Set User ID.
		 * */
		public function set_user_id( $value ) {
			$this->set_prop( 'lty_user_id', $value );
		}

		/**
		 * Set User Name.
		 * */
		public function set_user_name( $value ) {
			$this->set_prop( 'lty_user_name', $value );
		}

		/**
		 * Set User Email.
		 * */
		public function set_user_email( $value ) {
			$this->set_prop( 'lty_user_email', $value );
		}

		/**
		 * Set Gift products.
		 * */
		public function set_gift_products( $value ) {
			$this->set_prop( 'lty_gift_products', $value );
		}

		/**
		 * Set Order ID.
		 * */
		public function set_order_id( $value ) {
			$this->set_prop( 'lty_order_id', $value );
		}

		/**
		 * Set Answer.
		 * */
		public function set_answer( $value ) {
			$this->set_prop( 'lty_answer', $value );
		}

		/**
		 * Set Answers.
		 * */
		public function set_answers( $value ) {
			$this->set_prop( 'lty_answers', $value );
		}

		/**
		 * Set Valid Answer.
		 * */
		public function set_valid_answer( $value ) {
			$this->set_prop( 'lty_valid_answer', $value );
		}

		/**
		 * Set created date
		 */
		public function set_lottery_ticket_number( $value ) {
			$this->set_prop( 'lty_ticket_number', $value );
		}

		/**
		 * Set lottery start date
		 * */
		public function set_lottery_start_date( $value ) {
			$this->set_prop( 'lty_start_date', $value );
		}

		/**
		 * Set lottery start date GMT
		 * */
		public function set_lottery_start_date_gmt( $value ) {
			$this->set_prop( 'lty_start_date_gmt', $value );
		}

		/**
		 * Set lottery end date
		 * */
		public function set_lottery_end_date( $value ) {
			$this->set_prop( 'lty_end_date', $value );
		}

		/**
		 * Set lottery end date GMT
		 * */
		public function set_lottery_end_date_gmt( $value ) {
			$this->set_prop( 'lty_end_date_gmt', $value );
		}

		/**
		 * Set lottery winning method
		 * */
		public function set_lottery_winning_method( $value ) {
			$this->set_prop( 'lty_winning_method', $value );
		}

		/**
		 * Set lottery schedule type.
		 *
		 * @since 11.7.0
		 * @param string $value Schedule type.
		 * */
		public function set_lottery_schedule_type( $value ) {
			$this->set_prop( 'lty_lottery_schedule_type', $value );
		}

		/**
		 * Set list count.
		 *
		 * @since 11.7.0
		 * @param string $value List count.
		 * */
		public function set_list_count( $value ) {
			$this->set_prop( 'lty_list_count', $value );
		}

		/**
		 * Get Product ID.
		 * */
		public function get_product_id() {
			return $this->product_id;
		}

		/**
		 * Get created date.
		 * */
		public function get_created_date() {
			return $this->created_date;
		}

		/**
		 * Get User ID.
		 * */
		public function get_user_id() {
			return $this->get_prop( 'lty_user_id' );
		}

		/**
		 * Get User Name.
		 * */
		public function get_user_name() {
			return $this->get_prop( 'lty_user_name' );
		}

		/**
		 * Get User Email.
		 * */
		public function get_user_email() {
			return $this->get_prop( 'lty_user_email' );
		}

		/**
		 * Get Gift products.
		 * */
		public function get_gift_products() {
			return $this->get_prop( 'lty_gift_products' );
		}

		/**
		 * Get Order ID.
		 * */
		public function get_order_id() {
			return $this->get_prop( 'lty_order_id' );
		}

		/**
		 * Get Answer.
		 * */
		public function get_answer() {
			return $this->get_prop( 'lty_answer' );
		}

		/**
		 * Get Answers.
		 * */
		public function get_answers() {
			return $this->get_prop( 'lty_answers' );
		}

		/**
		 * Get Valid Answer.
		 * */
		public function get_valid_answer() {
			return $this->get_prop( 'lty_valid_answer' );
		}

		/**
		 * Get lottery ticket number
		 * */
		public function get_lottery_ticket_number() {
			return $this->get_prop( 'lty_ticket_number' );
		}

		/**
		 * Get lottery start date
		 * */
		public function get_lottery_start_date() {
			return $this->get_prop( 'lty_start_date' );
		}

		/**
		 * Get lottery start date GMT
		 * */
		public function get_lottery_start_date_gmt() {
			return $this->get_prop( 'lty_start_date_gmt' );
		}

		/**
		 * Get lottery end date
		 * */
		public function get_lottery_end_date() {
			return $this->get_prop( 'lty_end_date' );
		}

		/**
		 * Get lottery end date GMT
		 * */
		public function get_lottery_end_date_gmt() {
			return $this->get_prop( 'lty_end_date_gmt' );
		}

		/**
		 * Get lottery winning method
		 * */
		public function get_winning_method() {
			return $this->get_prop( 'lty_winning_method' );
		}

		/**
		 * Get lottery schedule type.
		 *
		 * @since 11.7.0
		 * @return string
		 * */
		public function get_lottery_schedule_type() {
			return $this->get_prop( 'lty_lottery_schedule_type' );
		}

		/**
		 * Get list count.
		 *
		 * @since 11.7.0
		 * @return string
		 * */
		public function get_list_count() {
			return $this->get_prop( 'lty_list_count' );
		}
	}

}
