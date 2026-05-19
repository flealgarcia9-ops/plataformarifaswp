<?php
/**
 * Instant winner coupon handler.
 *
 * @since 10.6.0
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

if ( ! class_exists( 'LTY_Instant_Winner_Coupon_Handler' ) ) {

	/**
	 * Class.
	 *
	 * @since 10.6.0
	 */
	class LTY_Instant_Winner_Coupon_Handler {

		/**
		 * Coupon.
		 *
		 * @since 10.6.0
		 * @var object
		 */
		protected $coupon;

		/**
		 * Instant winner log.
		 *
		 * @since 10.6.0
		 * @var object
		 */
		protected $instant_winner_log;

		/**
		 * Class initialization.
		 *
		 * @since 10.6.0
		 * @param object $instant_winner_log instance of LTY_Instant_Winner_Log.
		 */
		public function __construct( $instant_winner_log ) {
			$this->instant_winner_log = ! is_object( $instant_winner_log ) ? lty_get_instant_winner_log( $instant_winner_log ) : $instant_winner_log;

			$this->prepare();
		}

		/**
		 * Get the coupon code.
		 *
		 * @since 10.6.0
		 * @param object $instant_winner_log instance of LTY_Instant_Winner_Log.
		 * @return string
		 */
		public static function get_coupon_code( $instant_winner_log ) {
			$self = new self( $instant_winner_log );

			return is_object( $self->get_coupon() ) ? $self->get_coupon()->get_code() : '';
		}

		/**
		 * Get the coupon.
		 *
		 * @since 10.6.0
		 * @return object
		 */
		protected function get_coupon() {
			return $this->coupon;
		}

		/**
		 * Prepare the coupon for the user.
		 *
		 * @since 10.6.0
		 */
		protected function prepare() {
			if ( ! is_object( $this->instant_winner_log ) ) {
				return;
			}

			$instant_winner_rule = $this->instant_winner_log->get_instant_winner_rule();
			if ( ! is_object( $instant_winner_rule ) ) {
				return;
			}

			if ( '2' === $instant_winner_rule->get_coupon_generation_type() ) {
				$coupon_id = $instant_winner_rule->get_coupon_id();
			} else {
				$coupon_id = $this->maybe_get_coupon_id_by_instant_winner_id( $this->instant_winner_log->get_id() );
				if ( ! $coupon_id ) {
					$coupon_id = $this->create_coupon();
				}
			}

			if ( ! $coupon_id ) {
				return;
			}

			$this->coupon = new WC_Coupon( $coupon_id );
		}

		/**
		 * Create a coupon.
		 *
		 * @since 10.6.0
		 * @return int
		 */
		protected function create_coupon() {
			$coupon_args = array(
				'post_title'  => $this->generate_coupon_code(),
				'post_status' => 'publish',
				'post_author' => 1,
				'post_type'   => 'shop_coupon',
			);

			$coupon_id = wp_insert_post( $coupon_args );

			$meta_data = array(
				'discount_type'              => $this->instant_winner_log->get_coupon_discount_type(),
				'coupon_amount'              => $this->instant_winner_log->get_prize_amount(),
				'usage_limit'                => '1',
				'free_shipping'              => get_option( 'lty_settings_instant_win_coupon_allow_free_shipping', 'no' ),
				'minimum_amount'             => get_option( 'lty_settings_instant_win_coupon_minimum_amount' ),
				'maximum_amount'             => get_option( 'lty_settings_instant_win_coupon_maximum_amount' ),
				'individual_use'             => get_option( 'lty_settings_instant_win_coupon_individual_use' ),
				'exclude_sale_items'         => get_option( 'lty_settings_instant_win_coupon_exclude_sale_items' ),
				'date_expires'               => get_option( 'lty_settings_instant_win_coupon_validity' ),
				'apply_before_tax'           => 'yes',
				'lty_coupon'                 => 'yes',
				'lty_user_email'             => $this->instant_winner_log->get_user_email(),
				'lty_user_id'                => $this->instant_winner_log->get_user_id(),
				'lty_instant_winner_id'      => $this->instant_winner_log->get_id(),
				'customer_email'             => $this->instant_winner_log->get_user_email(),
				'product_ids'                => implode( ', ', lty_get_instant_winner_coupon_include_products() ),
				'exclude_product_ids'        => implode( ', ', lty_get_instant_winner_coupon_exclude_products() ),
				'product_categories'         => lty_get_instant_winner_coupon_include_categories(),
				'exclude_product_categories' => lty_get_instant_winner_coupon_exclude_categories(),
			);

			foreach ( $meta_data as $meta_key => $meta_value ) {
				update_post_meta( $coupon_id, $meta_key, $meta_value );
			}

			return $coupon_id;
		}

		/**
		 * Delete a coupon.
		 *
		 * @since 10.6.0
		 * @param int $instant_winner_log_id Instant winner log ID.
		 * @return bool
		 */
		public static function delete_coupon( $instant_winner_log_id ) {
			if ( ! $instant_winner_log_id ) {
				return false;
			}

			$coupon_id = self::maybe_get_coupon_id_by_instant_winner_id( $instant_winner_log_id );
			$coupon    = new WC_Coupon( $coupon_id );
			if ( ! is_object( $coupon ) ) {
				return false;
			}

			wp_delete_post( $coupon_id, true );
			delete_post_meta( $instant_winner_log_id, 'lty_coupon_code' );

			return true;
		}

		/**
		 * Generate the coupon code.
		 *
		 * @since 10.6.0
		 * @return string
		 */
		protected function generate_coupon_code() {
			do {
				$coupon_code = lty_generate_instant_winner_random_coupon_code();
			} while ( wc_get_coupon_id_by_code( $coupon_code ) );

			return $coupon_code;
		}

		/**
		 * May be get the coupon ID by user ID/email.
		 *
		 * @since 10.6.0
		 * @param int $instant_winner_log_id Instant winner log ID.
		 * @return int|bool
		 */
		protected static function maybe_get_coupon_id_by_instant_winner_id( $instant_winner_log_id ) {
			$coupon_id = get_posts(
				array(
					'posts_per_page' => 1,
					'post_type'      => 'shop_coupon',
					'post_status'    => 'publish',
					'meta_key'       => 'lty_instant_winner_id',
					'meta_value'     => $instant_winner_log_id,
					'fields'         => 'ids',
				)
			);

			return lty_check_is_array( $coupon_id ) ? reset( $coupon_id ) : false;
		}
	}

}
