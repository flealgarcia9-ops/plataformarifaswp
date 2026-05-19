<?php
/**
 * Lottery Instant Winner Log.
 *
 * @since 8.0.0
 * */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'LTY_Instant_Winner_Log' ) ) {

	/**
	 * Class.
	 * */
	class LTY_Instant_Winner_Log extends LTY_Post {

		/**
		 * Post Type.
		 *
		 * @var string
		 * @since 8.0.0
		 * */
		protected $post_type = LTY_Register_Post_Types::LOTTERY_INSTANT_WINNER_LOG_POSTTYPE;

		/**
		 * Post Status.
		 *
		 * @var string
		 * @since 8.0.0
		 * */
		protected $post_status = 'lty_available';

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
		 * Instant winner rule ID.
		 *
		 * @var string
		 * @since 8.0.0
		 * */
		public $rule_id;

		/**
		 * Instant winner rule.
		 *
		 * @var object
		 * @since 8.0.0
		 * */
		protected $instant_winner_rule;

		/**
		 * Lottery ticket.
		 *
		 * @var object
		 * @since 8.0.0
		 * */
		protected $ticket;

		/**
		 * Order.
		 *
		 * @var object
		 * @since 8.0.0
		 * */
		protected $order;

		/**
		 * Meta data keys.
		 *
		 * @since 8.0.0
		 * @var array $meta_data_keys
		 * */
		protected $meta_data_keys = array(
			'lty_lottery_id'            => '',
			'lty_image_id'              => '',
			'lty_ticket_number'         => '',
			'lty_prize_type'            => '',
			'lty_instant_winner_prize'  => '',
			'lty_prize_amount'          => '',
			'lty_prize_group_id'        => '',
			'lty_coupon_code'           => '',
			'lty_gift_product_id'       => '',
			'lty_gift_product_quantity' => '',
			'lty_prize_assigned'        => '',
			'lty_current_relist_count'  => '',
			'lty_ticket_id'             => '',
			'lty_start_date'            => '',
			'lty_start_date_gmt'        => '',
			'lty_end_date'              => '',
			'lty_end_date_gmt'          => '',
			'lty_order_id'              => '',
			'lty_user_id'               => '',
			'lty_user_name'             => '',
			'lty_user_email'            => '',
		);

		/**
		 * Prepare extra post data.
		 *
		 * @since 8.0.0
		 */
		protected function load_extra_postdata() {
			$this->rule_id      = $this->post->post_parent;
			$this->created_date = $this->post->post_date_gmt;
		}

		/**
		 * Get formatted created datetime.
		 *
		 * @since 8.0.0
		 * @return string.
		 * */
		public function get_formatted_created_date() {
			return LTY_Date_Time::get_wp_format_datetime_from_gmt( $this->get_created_date() );
		}

		/**
		 * Get the Product.
		 *
		 * @since 8.0.0
		 * @return object.
		 * */
		public function get_product() {
			if ( isset( $this->product ) ) {
				return $this->product;
			}

			$this->product = wc_get_product( $this->get_lottery_id() );

			return $this->product;
		}

		/**
		 * Get the product name.
		 *
		 * @since 9.1.0
		 * @param bool $linkable Whether to return the product name as a link or not.
		 * @return string|html
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
		 * Get instant winner details.
		 *
		 * @since 8.0.0
		 * @return string.
		 * */
		public function get_instant_winner_details() {
			if ( ! $this->has_status( 'lty_won' ) ) {
				return lty_get_instant_winners_prize_available_label();
			}

			return $this->display_user_name();
		}

		/**
		 * Get the instant winner prize group ticket status label.
		 *
		 * @since 11.1.0
		 * @return string
		 * */
		public function get_instant_winner_prize_group_ticket_status_label() {
			if ( $this->has_status( 'lty_won' ) ) {
				return str_replace( '{instant_winner_name}', $this->display_user_name(), lty_get_instant_winner_won_prize_label() );
			}

			return lty_get_instant_winners_prize_available_label();
		}

		/**
		 * Display user name.
		 *
		 * @since 8.6.0
		 * @return string
		 * */
		public function display_user_name() {
			$display_type = get_option( 'lty_settings_single_product_tab_details_username_display_type', 1 );
			switch ( $display_type ) {
				case '3':
					$username = is_object( $this->get_order() ) ? $this->get_order()->get_billing_first_name() : $this->get_user_name();
					break;

				case '2':
					$username = is_object( $this->get_order() ) ? $this->get_order()->get_formatted_billing_full_name() : $this->get_user_name();
					break;

				default:
					if ( ! $this->get_user_id() ) {
						$username = is_object( $this->get_order() ) ? $this->get_order()->get_formatted_billing_full_name() : '';
					} else {
						$username = $this->get_user_name();
					}

					break;
			}

			if ( 'yes' === get_option( 'lty_settings_single_product_lottery_mask_winner_username' ) ) {
				$username = lty_mask_name( $username );
			}

			if ( ! $this->get_user_id() ) {
				$username = $username . '[' . __( 'Guest', 'lottery-for-woocommerce' ) . ']';
			}

			return $username;
		}

		/**
		 * Get the ticket.
		 *
		 * @since 8.0.0
		 * @return object
		 * */
		public function get_ticket() {
			if ( isset( $this->ticket ) ) {
				return $this->ticket;
			}

			$this->ticket = lty_get_lottery_ticket( $this->get_ticket_id() );
			return $this->ticket;
		}

		/**
		 * Get the instant winner rule.
		 *
		 * @since 8.0.0
		 * @return object
		 * */
		public function get_instant_winner_rule() {
			if ( isset( $this->instant_winner_rule ) ) {
				return $this->instant_winner_rule;
			}

			$this->instant_winner_rule = lty_get_instant_winner_rule( $this->rule_id );
			return $this->instant_winner_rule;
		}

		/**
		 * Get the Order.
		 *
		 * @since 8.0.0
		 * @return object.
		 * */
		public function get_order() {
			if ( isset( $this->order ) ) {
				return $this->order;
			}

			$this->order = wc_get_order( $this->get_order_id() );

			return $this->order;
		}

		/**
		 * Get the order number.
		 *
		 * @since 10.6.0
		 * @param string $linkable Whether to return as linkable or not.
		 * @return string
		 */
		public function get_order_number( $linkable = 'view' ) {
			if ( empty( $this->get_order_id() ) ) {
				return '-';
			}

			if ( ! $linkable || ! is_object( $this->get_order() ) ) {
				return $this->get_order_id();
			}

			if ( 'edit' === $linkable ) {
				/* translators: %1$s: URL, %2$s: Order Number */
				return sprintf( '<a href="%1$s">#%2$s</a>', esc_url( get_edit_post_link( $this->get_order_id() ) ), esc_html( $this->get_order_id() ) );
			}

			/* translators: %1$s: URL, %2$s: Order Number */
			return sprintf( '<a href="%1$s">#%2$s</a>', esc_url( $this->get_order()->get_view_order_url() ), esc_html( $this->get_order_id() ) );
		}

		/**
		 * Get formatted ticket number.
		 *
		 * @since 9.2.0
		 * @return string
		 * */
		public function get_formatted_ticket_number() {
			if ( ! is_object( $this->get_product() ) || ! $this->get_product()->is_manual_ticket() || $this->has_status( 'lty_won' ) ) {
				return $this->get_ticket_number();
			}

			/**
			 * This hook is used to alter the masked instant winner ticket number.
			 *
			 * @since 9.2.0
			 */
			return apply_filters( 'lty_masked_instant_winner_ticket_number', '****', $this->get_ticket_number(), $this->get_product() );
		}

		/**
		 * Remove instant winner.
		 *
		 * @since 10.6.0
		 * */
		public function remove_instant_winner() {
			$instant_winner_metas = array(
				'lty_ticket_id',
				'lty_order_id',
				'lty_user_id',
				'lty_user_name',
				'lty_user_email',
			);

			// Remove won prize if already assigned.
			if ( $this->has_status( 'lty_won' ) ) {
				$this->remove_won_prize();
			}

			foreach ( $instant_winner_metas as $meta_key ) {
				delete_post_meta( $this->get_id(), $meta_key );
			}

			$this->update_status( 'lty_available' );
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
		 * Get image.
		 *
		 * @since 10.6.0
		 * @param string  $size Image size.
		 * @param array   $attr Attributes.
		 * @param boolean $placeholder Placeholder.
		 * @return HTML
		 */
		public function get_image( $size = 'woocommerce_thumbnail', $attr = array(), $placeholder = true ) {
			$image = ( $this->get_image_id() ) ? wp_get_attachment_image( $this->get_image_id(), $size, false, $attr ) : '';

			return ( ! $image && $placeholder ) ? wc_placeholder_img( $size, $attr ) : $image;
		}

		/**
		 * Assign winning prize.
		 *
		 * @since 10.6.0
		 * @param object $order
		 * @return bool
		 */
		public function assign_winning_prize( $order ) {
			$prize_assigned = false;
			$prize_type     = ! empty( $this->get_prize_type() ) ? $this->get_prize_type() : 'physical';
			switch ( $prize_type ) {
				case 'coupon':
					$coupon_code = LTY_Instant_Winner_Coupon_Handler::get_coupon_code( $this );
					if ( $coupon_code ) {
						$this->update_meta( 'lty_coupon_code', $coupon_code );
						$prize_assigned = true;
					}
					break;

				case 'product':
					if ( ! $this->get_gift_product_id() || ! is_object( $order ) ) {
						break;
					}

					$product = wc_get_product( $this->get_gift_product_id() );
					if ( ! is_object( $product ) ) {
						break;
					}

					$quantity = $this->get_gift_product_quantity() ? intval( $this->get_gift_product_quantity() ) : 1;
					if ( ! $this->is_valid_gift_product_add_to_order( $product, $quantity ) ) {
						break;
					}

					$item_id = $order->add_product( $product, $quantity, array( 'total' => 0, 'subtotal' => 0 ) );
					$item    = $order->get_item( $item_id );
					if ( ! is_object( $item ) ) {
						break;
					}

					$order->calculate_totals();
					$order->save();

					if ( lty_is_lottery_product( $product ) ) {
						if ( $this->maybe_assign_lottery_ticket_for_order( $item, $order ) ) {
							$prize_assigned = true;
						}
					} else {
						$item->add_meta_data( '_lty_is_instant_win_gift_product', 'yes' );
						$item->add_meta_data( '_lty_gift_product_instant_win_log_id', $this->get_id() );
						$item->save();

						// Add the order note.
						$gift_product_name = sprintf( '<a href="%s">%s</a>', esc_url( $product->get_permalink() ), esc_html( $product->get_title() ) );
						$order_note        = str_replace(
							array( '{gift_product_name}', '{ticket_number}', '{lottery_product_name}' ),
							array( $gift_product_name, $this->get_ticket_number(), $this->get_product_name() ),
							__( '{gift_product_name} is added to the order as an Instant win prize for purchasing ticket number ({ticket_number}) in the ({lottery_product_name}).', 'lottery-for-woocommerce' )
						);

						$order->add_order_note( $order_note );
						$order->save();
						$prize_assigned = true;
					}
					break;
			}

			/**
			 * This hook is used alter the instant winner prize assign.
			 *
			 * @since 10.6.0
			 */
			return apply_filters( 'lty_instant_winner_assign_' . $prize_type . '_prize', $prize_assigned, $this );
		}

		/**
		 * Is valid gift lottery add to order?
		 *
		 * @since 11.5.0
		 * @param object $product Product.
		 * @param int    $quantity Quantity.
		 * @throws Exception Throw exception if product is not valid.
		 * @return bool
		 */
		public function is_valid_gift_product_add_to_order( $product, $quantity ) {
			try {
				if ( lty_is_lottery_product( $product ) ) {
					if ( $product->is_closed() ) {
						throw new Exception( __( 'Instant win gift giveaway tickets is not added to the order due to gift giveaway({gift_product_name}) is closed(For purchasing instant win prize ticket number({ticket_number}) in the {lottery_product_name}).', 'lottery-for-woocommerce' ) );
					}

					// Check if the lottery product is out of stock.
					if ( $quantity > $product->get_remaining_ticket_count() ) {
						throw new Exception( __( 'Due to insufficient stock, instant win gift product({gift_product_name}) is not added to the order for purchasing instant win prize ticket number({ticket_number}) in the {lottery_product_name}.', 'lottery-for-woocommerce' ) );
					}
				} elseif ( $product->managing_stock() && ! $product->backorders_allowed() && ( ! $product->is_in_stock() || $quantity > $product->get_stock_quantity() ) ) {
					throw new Exception( __( 'Due to insufficient stock, instant win gift product({gift_product_name}) is not added to the order for purchasing instant win prize ticket number({ticket_number}) in the {lottery_product_name}.', 'lottery-for-woocommerce' ) );
				} elseif ( ! $product->is_in_stock() && ! $product->backorders_allowed() ) {
					throw new Exception( __( 'Due to insufficient stock, instant win gift product({gift_product_name}) is not added to the order for purchasing instant win prize ticket number({ticket_number}) in the {lottery_product_name}.', 'lottery-for-woocommerce' ) );
				}
			} catch ( Exception $ex ) {
				if ( ! empty( $ex->getMessage() ) ) {
					$gift_product_name = sprintf( '<a href="%s">%s</a>', esc_url( $product->get_permalink() ), esc_html( $product->get_title() ) );

					$order_note = str_replace(
						array( '{gift_product_name}', '{ticket_number}', '{lottery_product_name}' ),
						array( $gift_product_name, $this->get_ticket_number(), $this->get_product_name() ),
						$ex->getMessage()
					);

					$this->get_order()->add_order_note( $order_note );
					$this->get_order()->save();
				}

				return false;
			}

			return true;
		}

		/**
		 * Maybe assign lottery ticket for order item.
		 *
		 * @since 11.5.0
		 * @param object $item Order item object.
		 * @return bool|void
		 */
		public function maybe_assign_lottery_ticket_for_order( $item, $order ) {
			if ( ! is_object( $item ) ) {
				return false;
			}

			if ( $item->get_product()->is_manual_ticket() ) {
				$ticket_numbers = lty_get_random_user_chooses_ticket_numbers_by_quantity( $item->get_product(), $item->get_quantity() );
				if ( ! lty_check_is_array( $ticket_numbers ) ) {
					return false;
				}

				// Update order item meta.
				wc_add_order_item_meta( $item->get_id(), '_lty_lottery_tickets', $ticket_numbers );
				wc_add_order_item_meta( $item->get_id(), lty_get_order_item_ticket_number_name(), '<span class="notranslate">' . implode( ', ', $ticket_numbers ) . '</span>' );
			}

			$item->add_meta_data( '_lty_is_instant_win_gift_product', 'yes' );
			$item->add_meta_data( '_lty_gift_product_instant_win_log_id', $this->get_id() );
			$item->save();

			// Delete meta's.
			$order->delete_meta_data( 'lty_lottery_ticket_created_once' );
			$order->delete_meta_data( 'lty_lottery_ticket_updated_once' );

			// Add the order note.
			$gift_product_name = sprintf( '<a href="%s">%s</a>', esc_url( $item->get_product()->get_permalink() ), esc_html( $item->get_product()->get_title() ) );
			$order_note        = str_replace(
				array( '{gift_product_name}', '{ticket_number}', '{lottery_product_name}' ),
				array( $gift_product_name, $this->get_ticket_number(), $this->get_product_name() ),
				__( '{gift_product_name} is added to the order as an Instant win prize for purchasing ticket number ({ticket_number}) in the ({lottery_product_name}).', 'lottery-for-woocommerce' )
			);

			$order->add_order_note( $order_note );
			$order->save();
			
			LTY_Order_Handler::create_ticket_for_order_item( $order, $item->get_product_id(), false );
			// Update lottery ticket in order.
			LTY_Order_Handler::update_lottery_ticket_in_order( $order->get_id(), $order );

			return true;
		}

		/**
		 * Remove won prize.
		 *
		 * @since 10.6.0
		 * @return bool
		 */
		public function remove_won_prize() {
			$prize_type = ! empty( $this->get_prize_type() ) ? $this->get_prize_type() : 'physical';
			if ( 'coupon' === $prize_type ) {
				LTY_Instant_Winner_Coupon_Handler::delete_coupon( $this->get_id() );
			}

			/**
			 * This hook is used alter the instant winner prize remove.
			 *
			 * @since 10.6.0
			 */
			return apply_filters( 'lty_instant_winner_remove_won_' . $prize_type . '_prize', true, $this );
		}

		/**
		 * Get prize details.
		 *
		 * @since 10.6.0
		 * @return string
		 */
		public function get_prize_details() {
			$prize_details = $this->get_prize_message();
			if ( 'coupon' === $this->get_prize_type() ) {
				if ( ! empty( $this->get_coupon_code() ) ) {
					$coupon = new WC_Coupon( $this->get_coupon_code() );
					if ( is_object( $coupon ) ) {
						$coupon_expiry_date = ! empty( $coupon->get_date_expires() ) ? LTY_Date_Time::get_wp_format_datetime( $coupon->get_date_expires() ) : 'N/A';
						$prize_details      = $prize_details . sprintf(
							'<br/><b>%1$s: </b>%2$s<br/><b>%3$s: </b>%4$s',
							__( 'Coupon Code', 'lottery-for-woocommerce' ),
							esc_html( $coupon->get_code() ),
							__( 'Expires on', 'lottery-for-woocommerce' ),
							wp_kses_post( $coupon_expiry_date )
						);
					}
				}
			}

			/**
			 * This hook is used to alter the instant winner prize details.
			 *
			 * @since 10.6.0
			 * @param string Prize message.
			 * @param object Instant winner log.
			 */
			return apply_filters( 'lty_instant_winner_prize_details', $prize_details, $this );
		}

		/**
		 * Get coupon generation type.
		 *
		 * @since 10.6.0
		 * @return string
		 * */
		public function get_coupon_generation_type() {
			return is_object( $this->get_instant_winner_rule() ) ? $this->get_instant_winner_rule()->get_coupon_generation_type() : false;
		}

		/**
		 * Get coupon discount type.
		 *
		 * @since 10.6.0
		 * @return string
		 * */
		public function get_coupon_discount_type() {
			return is_object( $this->get_instant_winner_rule() ) ? $this->get_instant_winner_rule()->get_coupon_discount_type() : 'fixed_cart';
		}

		/**
		 * Get coupon generation type.
		 *
		 * @since 10.6.0
		 * @return string
		 * */
		public function get_formatted_coupon_value() {
			if ( empty( $this->get_coupon_code() ) ) {
				return '';
			}

			$coupon = new WC_Coupon( $this->get_coupon_code() );
			if ( ! is_object( $coupon ) ) {
				return '';
			}

			return 'percent' === $coupon->get_discount_type() ? $coupon->get_amount() . '%' : lty_price( $coupon->get_amount() );
		}

		/**
		 * Get the prize type label.
		 *
		 * @since 10.6.0
		 * @param bool $linkable Whether to return as linkable or not.
		 * @return string
		 */
		public function get_prize_type_label( $linkable = false ) {
			if ( 'coupon' === $this->get_prize_type() ) {
				$prize_type_label = __( 'Coupon', 'lottery-for-woocommerce' );
				if ( $linkable && $this->get_coupon_code() ) {
					$coupon = new WC_Coupon( $this->get_coupon_code() );
					if ( is_object( $coupon ) && $coupon->get_id() ) {
						/* translators: %1$s: URL, %2$s: label */
						$prize_type_label = sprintf( '<a href="%1$s">%2$s</a>', esc_url( get_edit_post_link( $coupon->get_id() ) ), $prize_type_label );
					}
				}
			} else {
				$prize_type_label = __( 'Physical', 'lottery-for-woocommerce' );
			}

			/**
			 * This hook is used to alter the instant winner prize details.
			 *
			 * @since 10.6.0
			 * @param string Prize type label.
			 * @param string Prize type.
			 */
			return apply_filters( 'lty_instant_winner_prize_type_label', $prize_type_label, $this->get_prize_type() );
		}

		/**
		 * Get the gift product name.
		 *
		 * @since 11.5.0
		 * @param bool $linkable Whether to return the product name as a link or not.
		 * @return string|html
		 * */
		public function get_gift_product_name( $linkable = false ) {
			if ( ! $this->get_gift_product_id() ) {
				return '';
			}

			$gift_product = wc_get_product( $this->get_gift_product_id() );
			if ( ! is_object( $gift_product ) ) {
				return '';
			}

			if ( ! $linkable ) {
				return $gift_product->get_title();
			}

			return sprintf( '<a href="%s">%s</a>', esc_url( $gift_product->get_permalink() ), esc_html( $gift_product->get_title() ) );
		}

		/**
		 * ----------------------------------------------------------------
		 * Setters.
		 * ----------------------------------------------------------------
		 * Functions to set the instant winner log data.
		 */

		/**
		 * Set lottery ID.
		 *
		 * @since 8.0.0
		 * @param string $value
		 */
		public function set_lottery_id( $value ) {
			$this->set_prop( 'lty_lottery_id', $value );
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
		 * Set instant winner prize message.
		 *
		 * @since 10.6.0
		 * @param string $value
		 * */
		public function set_prize_message( $value ) {
			$this->set_prop( 'lty_instant_winner_prize', $value );
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
		 * Set the coupon code.
		 *
		 * @since 10.6.0
		 * @param string $value Coupon code.
		 */
		public function set_coupon_code( $value ) {
			$this->set_prop( 'lty_coupon_code', $value );
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
		 * Set current relists count.
		 *
		 * @since 8.0.0
		 * @param string $value
		 * */
		public function set_current_relists_count( $value ) {
			$this->set_prop( 'lty_current_relist_count', $value );
		}

		/**
		 * Set ticket id.
		 *
		 * @since 8.0.0
		 * @param string $value
		 * */
		public function set_ticket_id( $value ) {
			$this->set_prop( 'lty_ticket_id', $value );
		}

		/**
		 * Set start date.
		 *
		 * @since 8.0.0
		 * @param string $start_date
		 */
		public function set_lty_start_date( $start_date ) {
			$this->set_prop( 'lty_start_date', $start_date );
		}

		/**
		 * Set start date gmt.
		 *
		 * @since 8.0.0
		 * @param string $start_date_gmt
		 */
		public function set_lty_start_date_gmt( $start_date_gmt ) {
			$this->set_prop( 'lty_start_date_gmt', $start_date_gmt );
		}

		/**
		 * Set end date.
		 *
		 * @since 8.0.0
		 * @param string $end_date
		 */
		public function set_lty_end_date( $end_date ) {
			$this->set_prop( 'lty_end_date', $end_date );
		}

		/**
		 * Set end date gmt.
		 *
		 * @since 8.0.0
		 * @param string $end_date_gmt
		 */
		public function set_lty_end_date_gmt( $end_date_gmt ) {
			$this->set_prop( 'lty_end_date_gmt', $end_date_gmt );
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
		 * Set rule id.
		 *
		 * @since 10.6.0
		 * @param string $value
		 */
		public function set_rule_id( $value ) {
			$this->rule_id = $value;
		}

		/**
		 * Set order ID.
		 *
		 * @since 8.0.0
		 * @param string $value
		 */
		public function set_order_id( $value ) {
			$this->set_prop( 'lty_order_id', $value );
		}

		/**
		 * Set user ID.
		 *
		 * @since 8.0.0
		 * @param string $value
		 */
		public function set_user_id( $value ) {
			$this->set_prop( 'lty_user_id', $value );
		}

		/**
		 * Set user name.
		 *
		 * @since 8.0.0
		 * @param string $value
		 */
		public function set_user_name( $value ) {
			$this->set_prop( 'lty_user_name', $value );
		}

		/**
		 * Set user email.
		 *
		 * @since 8.0.0
		 * @param string $value
		 */
		public function set_user_email( $value ) {
			$this->set_prop( 'lty_user_email', $value );
		}

		/**
		 * ----------------------------------------------------------------
		 * Getters.
		 * ----------------------------------------------------------------
		 * Functions to get the instant winner log data.
		 */

		/**
		 * Get lottery ID.
		 *
		 * @since 8.0.0
		 * @return string
		 * */
		public function get_lottery_id() {
			return $this->get_prop( 'lty_lottery_id' );
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
		 * Get Ticket Number.
		 *
		 * @since 8.0.0
		 * @return string
		 * */
		public function get_ticket_number() {
			return $this->get_prop( 'lty_ticket_number' );
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
		 * Get prize message.
		 *
		 * @since 10.6.0
		 * @return string
		 * */
		public function get_prize_message() {
			return $this->get_prop( 'lty_instant_winner_prize' );
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
		 * Get the coupon code.
		 *
		 * @since 10.6.0
		 * @return string
		 */
		public function get_coupon_code() {
			return $this->get_prop( 'lty_coupon_code' );
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
		 * Get current relists count.
		 *
		 * @since 8.0.0
		 * @return string.
		 * */
		public function get_current_relists_count() {
			return $this->get_prop( 'lty_current_relist_count' );
		}

		/**
		 * Get ticket id.
		 *
		 * @since 8.0.0
		 * @return string
		 * */
		public function get_ticket_id() {
			return $this->get_prop( 'lty_ticket_id' );
		}

		/**
		 * Get start date.
		 *
		 * @since 8.0.0
		 * @return string.
		 */
		public function get_lty_start_date() {
			return $this->get_prop( 'lty_start_date' );
		}

		/**
		 * Get start date gmt.
		 *
		 * @since 8.0.0
		 * @return string
		 */
		public function get_lty_start_date_gmt() {
			return $this->get_prop( 'lty_start_date_gmt' );
		}

		/**
		 * Get end date.
		 *
		 * @since 8.0.0
		 * @return string
		 */
		public function get_lty_end_date() {
			return $this->get_prop( 'lty_end_date' );
		}

		/**
		 * Get end date gmt.
		 *
		 * @since 8.0.0
		 * @return string
		 */
		public function get_lty_end_date_gmt() {
			return $this->get_prop( 'lty_end_date_gmt' );
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
		 * Get rule id.
		 *
		 * @since 10.6.0
		 * @return string
		 */
		public function get_rule_id() {
			return $this->rule_id;
		}

		/**
		 * Get order ID.
		 *
		 * @since 8.0.0
		 * @return string
		 */
		public function get_order_id() {
			return $this->get_prop( 'lty_order_id' );
		}

		/**
		 * Get user ID.
		 *
		 * @since 8.0.0
		 * @return string
		 */
		public function get_user_id() {
			return $this->get_prop( 'lty_user_id' );
		}

		/**
		 * Get user name.
		 *
		 * @since 8.0.0
		 * @return string
		 */
		public function get_user_name() {
			return $this->get_prop( 'lty_user_name' );
		}

		/**
		 * Get user email.
		 *
		 * @since 8.0.0
		 * @return string
		 */
		public function get_user_email() {
			return $this->get_prop( 'lty_user_email' );
		}
	}
}
