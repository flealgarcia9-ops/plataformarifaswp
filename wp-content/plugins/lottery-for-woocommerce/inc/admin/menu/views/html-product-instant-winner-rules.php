<?php
/**
 * Lottery instant winners rules.
 *
 * @since 9.5.0
 * @var object $product Product object.
 * @var array $instant_winner_ids Instant winner IDs.
 * @var int $current_page Current page.
 * @var int $page_count Page count.
 * @var int $items_per_page Number of items per page.
 * @var int $prize_group_options_count Prize group options count.
 * */
defined( 'ABSPATH' ) || exit; // Exit if accessed directly.
?>
<div class='lty-instant-winners-rules-wrapper show_if_lottery'>
	<div class='lty-instant-winner-rules-note-wrapper'>
		<p><b><?php esc_html_e( 'Note: ', 'lottery-for-woocommerce' ); ?></b></p>
		<p>* <?php esc_html_e( "If you selected the 'Display Prizes by Group' option, please configure the 'Instant Win Prize Groups' settings", 'lottery-for-woocommerce' ); ?></p>
		<p>* <?php esc_html_e( "If you make any changes in instant win fields, then please save the instant win settings using 'Save' button. If you don't save the instant win settings, the changes will not reflect.", 'lottery-for-woocommerce' ); ?></p>
	</div>
	<button type='button' class='lty-import-popup lty-import-instant-winner-rule-btn button-primary' 
			data-action='instant-winner-rule' data-extra_data='
			<?php
			echo wp_json_encode(
				array(
					'product_id'   => $product->get_id(),
					'display_mode' => is_callable( array( $product, 'get_lty_instant_winner_display_mode' ) ) ? $product->get_lty_instant_winner_display_mode( 'edit' ) : '1',
				)
			);
			?>
			'>
			<?php esc_html_e( 'Import', 'lottery-for-woocommerce' ); ?></button>
	<button type='button' class='button lty-export-instant-winner-rules lty-export-popup button-primary' data-export_type='instant_winner_rules' data-extra_data="<?php echo esc_attr( wp_json_encode( array( 'product_id' => $product->get_id() ) ) ); ?>"><?php esc_html_e( 'Export', 'lottery-for-woocommerce' ); ?></button>
	<?php require LTY_ABSPATH . 'inc/admin/menu/views/html-lottery-instant-winners-rules-actions.php'; ?>
	<table class='lty-instant-winners-rules-contents wp-list-table widefat striped'>
		<thead>
			<tr>
				<th><input type='checkbox' class='lty-select-all-instant-winners-rules' title="<?php esc_html_e( 'Select All', 'lottery-for-woocommerce' ); ?>"></th>
				<th class='lty-instant-winner-image-column lty-instant-winner-rule-column'><b><?php esc_html_e( 'Image', 'lottery-for-woocommerce' ); ?></b></th>
				<th class='lty-instant-winner-ticket-number-column'><b><?php esc_html_e( 'Ticket Number', 'lottery-for-woocommerce' ); ?></b></th>
				<th class='lty-instant-winner-prize-type-column lty-instant-winner-rule-column'><b><?php esc_html_e( 'Prize Type', 'lottery-for-woocommerce' ); ?></b></th>
				<th class='lty-instant-winner-prize-message-column lty-instant-winner-rule-column'><b><?php esc_html_e( 'Prize', 'lottery-for-woocommerce' ); ?></b><?php echo wp_kses_post( wc_help_tip( __( 'Here, you can customize the instant win prize message of each ticket number', 'lottery-for-woocommerce' ) ) ); ?></th>
				<th class='lty-instant-winner-prize-group-column'><b><?php esc_html_e( 'Prize Group', 'lottery-for-woocommerce' ); ?></b></th>
				<th class='lty-instant-winner-action-column'><b><?php esc_html_e( 'Action', 'lottery-for-woocommerce' ); ?></b></th>
				<?php
					/**
					 * This hook is used to do extra actions in the instant winner rules table column.
					 *
					 * @since 11.1.0
					 * @param object $product Product object.
					 */
					do_action( 'lty_instant_winner_rule_column', $product );
				?>
			</tr>
		</thead>
		<tbody>
			<?php
			if ( lty_check_is_array( $instant_winner_ids ) ) :
				foreach ( $instant_winner_ids as $rule_id ) :
					$instant_winner = lty_get_instant_winner_rule( $rule_id );
					if ( ! is_object( $instant_winner ) ) :
						continue;
					endif;
					$name = 'lty_instant_winner_rules[' . $rule_id . ']';
					?>
					<tr>
						<td>
							<input type='checkbox' name='lty_select_instant_winner_rule' class='lty-select-instant-winner-rule' />
							<br>
							<small>
								<b><?php esc_html_e( 'ID: ', 'lottery-for-woocommerce' ); ?></b><?php echo esc_html( $rule_id ); ?>
							</small>
						</td>
						<td class='lty-instant-winner-image-column lty-instant-winner-rule-column'>
							<p class='lty-instant-winner-image-preview'>
								<img src="<?php echo esc_url( $instant_winner->get_image_url() ); ?>" />
							</p>
							<input type='hidden' name="<?php echo esc_attr( $name ); ?>[image_id]" class='lty-instant-winner-image-id' value="<?php echo esc_attr( $instant_winner->get_image_id() ); ?>"/>
							<span class='dashicons dashicons-upload lty-instant-winner-rule lty-select-image' title="<?php esc_attr_e( 'Choose Image', 'lottery-for-woocommerce' ); ?>" ></span>
							<span class='dashicons dashicons-trash lty-instant-winner-rule lty-remove-image' title="<?php esc_attr_e( 'Remove', 'lottery-for-woocommerce' ); ?>" style="<?php echo empty( $instant_winner->get_image_id() ) ? 'display: none;' : ''; ?>" ></span>
						</td>
						<td class='lty-instant-winner-ticket-number-column'><input type='text' class='lty-instant-winner-rule lty-ticket-number' name="<?php echo esc_attr( $name ); ?>[ticket_number]" value="<?php echo wp_kses_post( $instant_winner->get_ticket_number() ); ?>"></td>
						<td class='lty-instant-winner-prize-type-column lty-instant-winner-rule-column'>
							<p>
								<select name="<?php echo esc_attr( $name ); ?>[prize_type]" class='lty-instant-winner-rule lty-instant-winner-prize-type'>
									<?php foreach ( lty_get_instant_winner_prize_type_options() as $option_key => $option_label ) : ?>	
										<option value="<?php echo esc_attr( $option_key ); ?>" <?php selected( $option_key, $instant_winner->get_prize_type(), true ); ?> ><?php echo esc_html( $option_label ); ?></option>
									<?php endforeach; ?>
								</select>
							</p>
							<p>
								<select name="<?php echo esc_attr( $name ); ?>[coupon_generation_type]" class='lty-instant-winner-rule lty-instant-winner-prize-field lty-instant-winner-coupon-generation-type'>
									<?php foreach ( lty_get_instant_winner_coupon_generation_type_options() as $option_key => $option_label ) : ?>	
										<option value="<?php echo esc_attr( $option_key ); ?>" <?php selected( $option_key, $instant_winner->get_coupon_generation_type(), true ); ?>><?php echo esc_html( $option_label ); ?></option>
									<?php endforeach; ?>
								</select>
								<?php echo wp_kses_post( wc_help_tip( __( 'If you selected New Coupon option, new coupon will be created based on Instant Winner Coupon Creation Settings(Giveaway -> Settings -> General -> Instant Win). Coupon will be created only when the instant winner are assigned.', 'lottery-for-woocommerce' ) ) ); ?>
							</p>
							<p>
								<select name="<?php echo esc_attr( $name ); ?>[coupon_discount_type]" class='lty-instant-winner-rule lty-instant-winner-prize-field lty-instant-winner-coupon-field lty-instant-winner-coupon-discount-type'>
									<?php foreach ( lty_get_instant_winner_coupon_discount_type_options() as $option_key => $option_label ) : ?>	
										<option value="<?php echo esc_attr( $option_key ); ?>" <?php selected( $option_key, $instant_winner->get_coupon_discount_type(), true ); ?>><?php echo esc_html( $option_label ); ?></option>
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
										'options'     => array( $instant_winner->get_coupon_id() ),
									)
								);
								?>
							</p>
							<p>
								<?php
								lty_select2_html(
									array(
										'id'          => $name . '[gift_product_id]',
										'class'       => 'lty-instant-winner-rule lty-instant-winner-prize-field lty-instant-winner-gift-product-field lty-instant-winner-gift-product-id',
										'list_type'   => 'products',
										'action'      => 'lty_json_search_products_and_variations',
										'placeholder' => __( 'Search for a product&hellip;' ),
										'exclude_out_of_stock' => 'yes',
										'include_lottery_statuses' => array( 'lty_lottery_started' ),
										'multiple'    => false,
										'options'     => array( $instant_winner->get_gift_product_id() ),
									)
								);
								?>
							</p>
							<p>
								<input type='number' min='1' step='1' class='lty-instant-winner-rule lty-instant-winner-prize-field lty-instant-winner-gift-product-field lty-instant-winner-gift-product-quantity' name="<?php echo esc_attr( $name ); ?>[gift_product_quantity]" value="<?php echo wp_kses_post( $instant_winner->get_gift_product_quantity() ); ?>" placeholder="<?php esc_attr_e( 'Enter the quantity', 'lottery-for-woocommerce' ); ?>">
							</p>
							<p>
								<input type='text' class='wc_input_price lty-instant-winner-rule lty-instant-winner-prize-field lty-instant-winner-coupon-field lty-instant-winner-prize-amount' name="<?php echo esc_attr( $name ); ?>[prize_amount]" value="<?php echo wp_kses_post( $instant_winner->get_prize_amount() ); ?>" placeholder="<?php esc_attr_e( 'Enter the value', 'lottery-for-woocommerce' ); ?>">
							</p>
							<?php
							/**
							 * This hook is used to do extra actions in the instant winner rules table prize type column data.
							 *
							 * @since 11.1.0
							 * @param object $instant_winner Instant winner rule object.
							 * @param object $product Product object.
							 */
							do_action( 'lty_instant_winner_rule_prize_type_column_data', $instant_winner, $product );
							?>
						</td>
						<td class='lty-instant-winner-prize-message-column lty-instant-winner-rule-column'>
							<textarea class='lty-instant-winner-rule lty-instant-winner-prize-message' name="<?php echo esc_attr( $name ); ?>[prize_message]"><?php echo wp_kses_post( $instant_winner->get_prize_message() ); ?></textarea>
						</td>
						<td class='lty-instant-winner-prize-group-column'>
							<select name="<?php echo esc_attr( $name ); ?>[group_id]" class='lty_select2 lty-instant-winner-rule lty-instant-winner-prize-group-id'>
								<?php foreach ( $prize_group_options as $prize_group_option ) : ?>
									<option value="<?php echo esc_attr( $prize_group_option['prize_group_id'] ); ?>" <?php selected( $prize_group_option['prize_group_id'], $instant_winner->get_prize_group_id(), true ); ?>><?php echo wp_kses_post( $prize_group_option['title'] ); ?></option>
								<?php endforeach; ?>
							</select>
						</td>
						<td class='lty-instant-winner-action-column'><span class='lty-remove-instant-winner-rule dashicons dashicons-dismiss' data-instant_winner_rule_id='<?php echo esc_attr( $rule_id ); ?>' title="<?php esc_html_e( 'Remove rule', 'lottery-for-woocommerce' ); ?>"></span></td>
						<?php
						/**
						 * This hook is used to do extra actions in the instant winner rules table column data.
						 *
						 * @since 11.1.0
						 * @param object $instant_winner Instant winner rule object.
						 * @param object $product Product object.
						 */
						do_action( 'lty_instant_winner_rule_column_data', $instant_winner, $product );
						?>
					</tr>
					<?php
				endforeach;
			else :
				?>
				<tr>
					<td colspan='5'><?php esc_html_e( 'Click the "Add New Rule" Button to create new Instant Winner rule', 'lottery-for-woocommerce' ); ?></td>
				</tr>
			<?php endif; ?>
		</tbody>
		<input type='hidden' class='lty-product-id' value="<?php echo esc_attr( $product->get_id() ); ?>"/>
		<input type='hidden' class='lty-per-page' value="<?php echo esc_attr( $items_per_page ); ?>" />
	</table>
	<?php require LTY_ABSPATH . 'inc/admin/menu/views/html-lottery-instant-winners-rules-actions.php'; ?>
	<input type='hidden' class='lty-unsaved-instant-winner-rules' value=''/>
</div>
<?php

