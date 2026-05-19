<?php

/**
 * Lottery Ticket.
 * */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'LTY_Lottery_Ticket' ) ) {

	/**
	 * Class.
	 * */
	class LTY_Lottery_Ticket extends LTY_Post {

		/**
		 * Post Type.
		 *
		 * @var string
		 * */
		protected $post_type = LTY_Register_Post_Types::LOTTERY_TICKET_POSTTYPE;

		/**
		 * Post Status.
		 *
		 * @var string
		 * */
		protected $post_status = 'lty_ticket_buyer';

		/**
		 * User.
		 *
		 * @var object
		 * */
		protected $user;

		/**
		 * Product ID.
		 *
		 * @var int
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
			'lty_user_id'       => '',
			'lty_product_id'    => '',
			'lty_amount'        => '',
			'lty_user_name'     => '',
			'lty_user_email'    => '',
			'lty_currency'      => '',
			'lty_order_id'      => '',
			'lty_ticket_number' => '',
			'lty_answer'        => '',
			'lty_answers'       => array(),
			'lty_valid_answer'  => '',
			'lty_ip_address'    => '',
			'lty_list_count'    => '',
		);

		/**
		 * Prepare extra post data
		 */
		protected function load_extra_postdata() {
			$this->product_id   = $this->post->post_parent;
			$this->created_date = $this->post->post_date_gmt;
		}

		/**
		 * Display the user details.
		 *
		 * @return 1.0.0
		 * */
		public function display_user_name_by() {
			$display_type = get_option( 'lty_settings_single_product_tab_details_username_display_type', 1 );
			switch ( $display_type ) {
				case '3':
					if ( ! $this->get_user_id() ) {
						$username  = is_object( $this->get_order() ) ? $this->get_order()->get_billing_first_name() : '';
						$user_name = $this->display_first_name_and_last_name( $username ) . '[' . __( 'Guest', 'lottery-for-woocommerce' ) . ']';
					} else {
						$user_name = $this->display_first_name_and_last_name( $this->get_first_name() );
					}
					break;

				case '2':
					if ( ! $this->get_user_id() ) {
						$username  = is_object( $this->get_order() ) ? $this->get_order()->get_formatted_billing_full_name() : '';
						$user_name = $this->display_first_name_and_last_name( $username ) . '[' . __( 'Guest', 'lottery-for-woocommerce' ) . ']';
					} else {
						$username  = $this->get_first_name() . ' ' . $this->get_last_name();
						$user_name = $this->display_first_name_and_last_name( $username );
					}

					break;

				default:
					if ( ! $this->get_user_id() ) {
						$username  = is_object( $this->get_order() ) ? $this->get_order()->get_formatted_billing_full_name() : '';
						$user_name = $this->display_first_name_and_last_name( $username ) . '[' . __( 'Guest', 'lottery-for-woocommerce' ) . ']';
					} else {
						$user_name = $this->display_user_name();
					}

					break;
			}

			return $user_name;
		}

		/**
		 * Display user name.
		 * */
		public function display_user_name() {
			if ( 'yes' !== get_option( 'lty_settings_hide_user_name_in_ticket_logs' ) ) {
				return $this->get_user_name();
			}

			return lty_mask_name( $this->get_user_name() );
		}

		/**
		 * Display user name.
		 * */
		public function display_first_name_and_last_name( $username ) {
			if ( 'yes' === get_option( 'lty_settings_hide_user_name_in_ticket_logs' ) ) {
				return lty_mask_name( $username );
			}

			return $username;
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
		 * Get the first name.
		 *
		 * @return string
		 * */
		public function get_first_name() {
			if ( ! $this->get_user_id() && $this->get_order() ) {
				$first_name = $this->get_order()->get_billing_first_name();
			} else {
				$first_name = $this->get_user()->first_name;
			}

			return $first_name;
		}

		/**
		 * Get the last name.
		 *
		 * @return string
		 * */
		public function get_last_name() {
			if ( ! $this->get_user_id() && $this->get_order() ) {
				$last_name = $this->get_order()->get_billing_last_name();
			} else {
				$last_name = $this->get_user()->last_name;
			}

			return $last_name;
		}

		/**
		 * Get the Product.
		 * */
		public function get_product() {
			if ( isset( $this->product ) ) {
				return $this->product;
			}

			$this->product = wc_get_product( $this->get_product_id() );

			return $this->product;
		}

		/**
		 * Get the product name.
		 *
		 * @since 8.5.0
		 * @param bool $linkable Whether to return the product name as a link or not.
		 * @return string|HTML
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
		 * Get the view order link.
		 *
		 * @since 10.2.0
		 * @param string $prefix The prefix of the view order link.
		 * @return string|HTML
		 * */
		public function get_view_order_link( $prefix = '' ) {
			if ( ! is_object( $this->get_order() ) ) {
				return '';
			}

			return sprintf( '<a href="%1$s">%2$s%3$s</a>', esc_url( $this->get_order()->get_view_order_url() ), esc_html( $prefix ), esc_html( $this->get_order_id() ) );
		}

		/**
		 * Get instant winner ticket price
		 * 
		 * @since 10.4.0
		 * @return string
		 * */
		public function get_instant_winner_ticket_price() {
			$instant_winner_log_id = lty_get_instant_winner_log_id_by_ticket_id( $this->get_id() );
			$instant_winner_log    = lty_get_instant_winner_log( $instant_winner_log_id );
			if (!is_object($instant_winner_log) ) {
				return;
			}

			return $instant_winner_log->get_prize_message();
		}

		/*********************************
		 *     Setters and Getters.      *
		 *********************************/

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
		 * Set Amount.
		 * */
		public function set_amount( $value ) {
			$this->set_prop( 'lty_amount', $value );
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
		 * Set Currency.
		 * */
		public function set_lty_currency( $value ) {
			$this->set_prop( 'lty_currency', $value );
		}

		/**
		 * Set Order ID.
		 * */
		public function set_order_id( $value ) {
			$this->set_prop( 'lty_order_id', $value );
		}

		/**
		 * Set lottery ticket number.
		 * */
		public function set_lottery_ticket_number( $value ) {
			$this->set_prop( 'lty_ticket_number', $value );
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
		 * Set IP address
		 */
		public function set_ip_address( $value ) {
			$this->set_prop( 'lty_ip_address', $value );
		}

		/**
		 * Set list count.
		 *
		 * @since 11.7.0
		 * @param int $value List count.
		 */
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
		 * Get Amount.
		 * */
		public function get_amount() {
			return $this->get_prop( 'lty_amount' );
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
		 * Get Currency.
		 * */
		public function get_currency() {
			return $this->get_prop( 'lty_currency' );
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
		 * Get IP Address.
		 * */
		public function get_ip_address() {
			return $this->get_prop( 'lty_ip_address' );
		}

		/**
		 * Get list count.
		 *
		 * @since 11.7.0
		 * @return int
		 */
		public function get_list_count() {
			return $this->get_prop( 'lty_list_count' );
		}

		/**
		 * Get lottery ticket number
		 * */
		public function get_lottery_ticket_number() {
			return $this->get_prop( 'lty_ticket_number' );
		}
	}
}
