<?php
/**
 * Popup - Instant winner prize group.
 *
 * @since 11.1.0
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

?>
<div class='wc-backbone-modal'>
	<div class='wc-backbone-modal-content lty-instant-winner-prize-group-wrapper lty-instant-winner-prize-group-popup-wrapper'>
		<section class='wc-backbone-modal-main' role='main'>
			<header class='wc-backbone-modal-header'>
				<h1><?php esc_html_e( 'New Instant Win Prize Group', 'lottery-for-woocommerce' ); ?></h1>
				<button class='modal-close modal-close-link dashicons dashicons-no-alt'>
					<span class='screen-reader-text'><?php esc_html_e( 'Close modal panel', 'lottery-for-woocommerce' ); ?></span>
				</button>
			</header>
			<article>
				<div class='lty-instant-winner-prize-group-popup-content-wrapper'>
					<span class='lty-instant-winner-prize-group-error lty-error'></span>
					<p class='form-field'>
						<label><b><?php esc_html_e( 'Group Title', 'lottery-for-woocommerce' ); ?></b><span class='required'>*</span></label>
						<input type='text' class='lty-instant-winner-prize-group-title' id='lty_instant_winner_prize_group_title'>
						<?php echo wp_kses_post( wc_help_tip( __( "The entered title will be displayed on the 'Prize Group' dropdown within the 'Instant Win Prizes' settings", 'lottery-for-woocommerce' ) ) ); ?>
					</p>
					<p class='form-field'>
						<label><b><?php esc_html_e( 'Image', 'lottery-for-woocommerce' ); ?></b></label>
						<span class='lty-instant-winner-prize-group-image-preview'><img src="<?php echo esc_url( wc_placeholder_img_src() ); ?>" height='45px' width='45px' /></span>
						<input type='hidden' class='lty-instant-winner-prize-group-image-id' value=''/>
						<button class='button lty-select-image'><?php esc_html_e( 'Choose Image', 'lottery-for-woocommerce' ); ?></button>
						<button class='button lty-remove-image' style='display: none;'><?php esc_html_e( 'Remove', 'lottery-for-woocommerce' ); ?></button>
					</p>
					<p class='form-field'>
						<label><b><?php esc_html_e( 'Prize Type', 'lottery-for-woocommerce' ); ?></b><span class='required'>*</span></label>
						<select class='lty-instant-winner-prize-group-prize-type'>
							<?php foreach ( lty_get_instant_winner_prize_type_options() as $option_key => $option_label ) : ?>	
								<option value="<?php echo esc_attr( $option_key ); ?>"><?php echo esc_html( $option_label ); ?></option>
							<?php endforeach; ?>
						</select>
					</p>
					<p class='form-field'>
						<label><b><?php esc_html_e( 'Coupon Generation Type', 'lottery-for-woocommerce' ); ?></b></label>
						<select class='lty-instant-winner-prize-group-prize-field lty-instant-winner-prize-group-coupon-generation-type'>
							<?php foreach ( lty_get_instant_winner_coupon_generation_type_options() as $option_key => $option_label ) : ?>	
								<option value="<?php echo esc_attr( $option_key ); ?>"><?php echo esc_html( $option_label ); ?></option>
							<?php endforeach; ?>
						</select>
						<?php echo wp_kses_post( wc_help_tip( __( 'Selecting the New Coupon option will create a new coupon according to the Instant Winner Coupon Creation Settings (Giveaway -> Settings -> General -> Instant Win). Coupons will only be created when instant winners are assigned.', 'lottery-for-woocommerce' ) ) ); ?>
					</p>
					<p class='form-field'>
						<label><b><?php esc_html_e( 'Coupon Discount Type', 'lottery-for-woocommerce' ); ?></b></label>
						<select class='lty-instant-winner-prize-group-prize-field lty-instant-winner-prize-group-coupon-field lty-instant-winner-prize-group-coupon-discount-type'>
							<?php foreach ( lty_get_instant_winner_coupon_discount_type_options() as $option_key => $option_label ) : ?>	
								<option value="<?php echo esc_attr( $option_key ); ?>"><?php echo esc_html( $option_label ); ?></option>
							<?php endforeach; ?>
						</select>
					</p>
					<p class='form-field'>
						<label><b><?php esc_html_e( 'Select Coupon', 'lottery-for-woocommerce' ); ?></b><span class='required'>*</span></label>
						<?php
						lty_select2_html(
							array(
								'id'          => 'instant_winner_prize_group_coupon_id',
								'class'       => 'lty-instant-winner-prize-group-prize-field lty-instant-winner-prize-group-coupon-field lty-instant-winner-prize-group-coupon-id',
								'action'      => 'lty_json_search_coupons',
								'placeholder' => __( 'Search for a coupon&hellip;', 'lottery-for-woocommerce' ),
								'multiple'    => false,
								'options'     => array(),
							)
						);
						?>
					</p>
					<p class='form-field'>
						<?php
						lty_select2_html(
							array(
								'id'                       => 'lty_instant_winner_prize_group_gift_product_id',
								'class'                    => 'lty-instant-winner-rule lty-instant-winner-prize-group-prize-field lty-instant-winner-prize-group-gift-product-field lty-instant-winner-gift-product-id',
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
					<p class='form-field'>
						<label><b><?php esc_html_e( 'Quantity', 'lottery-for-woocommerce' ); ?></b><span class='required'>*</span></label>
						<input type='number' min='1' step='1' class='lty-instant-winner-rule lty-instant-winner-prize-group-prize-field lty-instant-winner-prize-group-gift-product-field lty-instant-winner-gift-product-quantity' value='1' placeholder="<?php esc_attr_e( 'Enter the quantity', 'lottery-for-woocommerce' ); ?>">
					</p>
					<p class='form-field'>
						<label><b><?php esc_html_e( 'Prize Value', 'lottery-for-woocommerce' ); ?></b><span class='required'>*</span></label>
						<input type='text' class='wc_input_price lty-instant-winner-prize-group-prize-field lty-instant-winner-prize-group-coupon-field lty-instant-winner-prize-group-amount' placeholder="<?php esc_attr_e( 'Enter the value', 'lottery-for-woocommerce' ); ?>" value=''>
					</p>
					<p class='form-field'>
						<label><b><?php esc_html_e( 'Group Prize Message', 'lottery-for-woocommerce' ); ?></b><span class='required'>*</span></label>				
						<textarea class='lty-instant-winner-prize-group-message'></textarea>
						<?php echo wp_kses_post( wc_help_tip( __( 'The entered message will be displayed on the instant win prize tab(frontend).', 'lottery-for-woocommerce' ) ) ); ?>
					</p>
					<?php
						/**
						 * This hook is used to do extra actions in the instant winner prize groups table column data.
						 *
						 * @since 11.1.0
						 * @param object $product Product object.
						 */
						do_action( 'lty_instant_winner_prize_group_popup_column_data', $product );
					?>
				</div>
			</article>
			<footer>
				<div class='inner'>
					<button class='button button-primary button-large lty-create-instant-winner-prize-group'><?php esc_html_e( 'Create', 'lottery-for-woocommerce' ); ?></button>
				</div>
			</footer>
		</section>
	</div>
</div>
<div class='wc-backbone-modal-backdrop modal-close'></div>
<?php
