<?php
/**
 * Instant winners rule popup.
 *
 * @since 9.5.0
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.
$name = 'lty_instant_winner_rules[new]';
?>
<div class='lty-hide lty-lottery-instant-winners-rule-modal-wrapper' id='lty_lottery_instant_winners_rule_modal'>
	<div class='lty-instant-winners-rule-header'>
		<span class='lty-instant-winners-rule-title'><h3><b><?php esc_html_e( 'Instant Win Prizes', 'lottery-for-woocommerce' ); ?></b></h3></span>
	</div>
	<span class='lty-instant-winners-rule-error lty-error'></span>

	<table class='lty-instant-winners-rule-content'>
		<tr>
			<td>
				<p class='lty-lottery-ticket-number lty-instant-winner-ticket-number-column'>
					<label><b><?php esc_html_e( 'Ticket Number', 'lottery-for-woocommerce' ); ?></b><span class='required'>*</span></label>
					<input type='text' class='lty-ticket-number' id='lty_instant_winner_rule_ticket_number' name="<?php echo esc_attr( $name ); ?>[ticket_number]">
				</p>
				<div class='lty-ticket-prize-type lty-instant-winner-rule-column'>
					<p class='lty-instant-winner-prize-type-column'>
						<label><b><?php esc_html_e( 'Prize Type', 'lottery-for-woocommerce' ); ?></b><span class='required'>*</span></label>
						<select name="<?php echo esc_attr( $name ); ?>[prize_type]" class='lty-instant-winner-prize-type'>
							<?php foreach ( lty_get_instant_winner_prize_type_options() as $option_key => $option_label ) : ?>	
								<option value="<?php echo esc_attr( $option_key ); ?>"><?php echo esc_html( $option_label ); ?></option>
							<?php endforeach; ?>
						</select>
					</p>
					<p>
						<select name="<?php echo esc_attr( $name ); ?>[coupon_generation_type]" class='lty-instant-winner-rule lty-instant-winner-prize-field lty-instant-winner-coupon-generation-type'>
							<?php foreach ( lty_get_instant_winner_coupon_generation_type_options() as $option_key => $option_label ) : ?>	
								<option value="<?php echo esc_attr( $option_key ); ?>"><?php echo esc_html( $option_label ); ?></option>
							<?php endforeach; ?>
						</select>
						<?php echo wp_kses_post( wc_help_tip( __( 'If you selected New Coupon option, new coupon will be created based on Instant Winner Coupon Creation Settings(Giveaway -> Settings -> General -> Instant Win). Coupon will be created only when the instant winner are assigned.', 'lottery-for-woocommerce' ) ) ); ?>
					</p>
					<p>
						<select name="<?php echo esc_attr( $name ); ?>[coupon_discount_type]" class='lty-instant-winner-rule lty-instant-winner-prize-field lty-instant-winner-coupon-field lty-instant-winner-coupon-discount-type'>
							<?php foreach ( lty_get_instant_winner_coupon_discount_type_options() as $option_key => $option_label ) : ?>	
								<option value="<?php echo esc_attr( $option_key ); ?>"><?php echo esc_html( $option_label ); ?></option>
							<?php endforeach; ?>
						</select>
					</p>
					<p>
						<?php
						lty_select2_html(
							array(
								'id'          => $name . '[coupon_id]',
								'class'       => 'lty-instant-winner-rule lty-instant-winner-prize-field lty-instant-winner-coupon-field lty-instant-winner-coupon-id',
								'action'      => 'lty_json_search_coupons',
								'placeholder' => __( 'Search for a coupon&hellip;', 'lottery-for-woocommerce' ),
								'multiple'    => false,
								'options'     => array(),
							)
						);
						?>
					</p>
					<p>
						<?php
						lty_select2_html(
							array(
								'id'                       => $name . '[gift_product_id]',
								'class'                    => 'lty-instant-winner-rule lty-instant-winner-prize-field lty-instant-winner-gift-product-field lty-instant-winner-gift-product-id',
								'list_type'                => 'products',
								'action'                   => 'lty_json_search_products_and_variations',
								'placeholder'              => __( 'Search for a product&hellip;' ),
								'exclude_out_of_stock'     => 'yes',
								'include_lottery_statuses' => array( 'lty_lottery_started' ),
								'multiple'                 => false,
								'options'                  => array(),
							)
						);
						?>
					</p>
					<p>
						<label><b><?php esc_html_e( 'Quantity', 'lottery-for-woocommerce' ); ?></b><span class='required'>*</span></label>
						<input type='number' min='1' step='1' class='lty-instant-winner-rule lty-instant-winner-prize-field lty-instant-winner-gift-product-field lty-instant-winner-gift-product-quantity' name="<?php echo esc_attr( $name ); ?>[gift_product_quantity]" value='1' placeholder="<?php esc_attr_e( 'Enter the quantity', 'lottery-for-woocommerce' ); ?>">
					</p>
					<p>
						<input type='text' class='wc_input_price lty-instant-winner-rule lty-instant-winner-prize-field lty-instant-winner-coupon-field lty-instant-winner-prize-amount' name="<?php echo esc_attr( $name ); ?>[prize_amount]" placeholder="<?php esc_attr_e( 'Enter the value', 'lottery-for-woocommerce' ); ?>" value='' />
					</p>
					<?php
					/**
					 * This hook is used to do extra actions in the instant winner rules table prize type column data.
					 *
					 * @since 11.1.0
					 * @param object $product Product object.
					 */
					do_action( 'lty_instant_winner_rule_popup_prize_type_column_data', $product );
					?>
				</div>
				<p class='lty-instant-winner-prize-group-column'>
					<label><b><?php esc_html_e( 'Group Prize', 'lottery-for-woocommerce' ); ?></b><span class='required'>*</span></label>
					<select class='lty_select2 lty-instant-winner-prize-group-id'>
						<?php foreach ( $prize_group_options as $prize_group_option ) : ?>
							<option value="<?php echo esc_attr( $prize_group_option['prize_group_id'] ); ?>"><?php echo wp_kses_post( $prize_group_option['title'] ); ?></option>
						<?php endforeach; ?>
					</select>
					<?php if ( ! $prize_group_options_count ) : ?>
						<span class='lty-instant-winner-prize-group-empty-message'><b><?php esc_html_e( 'Note: ', 'lottery-for-woocommerce' ); ?></b><?php esc_html_e( "Create the Prize Group on 'Instant Win Prize Groups' tab to configure the instant win prize rule settings.", 'lottery-for-woocommerce' ); ?></span>
					<?php endif; ?>
				</p>
				<p class='lty-lottery-ticket-prize lty-instant-winner-prize-message-column lty-instant-winner-rule-column'>
					<label><b><?php esc_html_e( 'Prize', 'lottery-for-woocommerce' ); ?></b><span class='required'>*</span><?php echo wp_kses_post( wc_help_tip( __( 'Here, you can customize the instant win prize message of each ticket number', 'lottery-for-woocommerce' ) ) ); ?></label>				
					<textarea class='lty-instant-winner-rule lty-instant-winner-prize-message' name="<?php echo esc_attr( $name ); ?>[prize_message]"></textarea>
				</p>
				<p class='lty-instant-winner-image lty-instant-winner-image-column lty-instant-winner-rule-column'>
					<label><b><?php esc_html_e( 'Image', 'lottery-for-woocommerce' ); ?></b></label>
					<span class='lty-instant-winner-image-preview'><img src="<?php echo esc_url( wc_placeholder_img_src() ); ?>" /></span>
					<input type='hidden' name="<?php echo esc_attr( $name ); ?>[image_id]" class='lty-instant-winner-image-id' value=''/>
					<button class='button lty-instant-winner-rule lty-select-image'><?php esc_attr_e( 'Choose Image', 'lottery-for-woocommerce' ); ?></button>
					<button class='button lty-instant-winner-rule lty-remove-image' style='display: none;'><?php esc_attr_e( 'Remove', 'lottery-for-woocommerce' ); ?></button>
				</p>
				<?php
					/**
					 * This hook is used to do extra actions in the instant winner rules table column data.
					 *
					 * @since 11.1.0
					 * @param object $product Product object.
					 */
					do_action( 'lty_instant_winner_rule_popup_column_data', $product );
				?>
			</td>
		</tr>
	</table>
	<div class='lty-instant-winners-rule-footer'>
		<input type='button' class='button button-primary lty-add-instant-winner-rule' value="<?php esc_html_e( 'Create', 'lottery-for-woocommerce' ); ?>">
	</div>
</div>
<?php
