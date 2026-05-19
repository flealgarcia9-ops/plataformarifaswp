<?php
/**
 * Lottery instant winner prize groups.
 *
 * @since 11.1.0
 * @var object $product Product object.
 * @var array  $prize_group_ids Instant prize group IDs.
 * @var int    $total_prize_group_ids_count All the instant winner prize group ID's count.
 * @var int    $current_page Current page.
 * @var int    $page_count Page count.
 * @var int    $items_per_page Number of items per page.
 * */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.
?>

<div class='lty-instant-winner-prize-groups-wrapper show_if_lottery'>
	<p><b><?php esc_html_e( 'Note: ', 'lottery-for-woocommerce' ); ?></b><?php esc_html_e( "If you make any changes in the instant win fields, please save the instant win settings using the 'Save' button. The changes will not be reflected if you do not save the instant win settings.", 'lottery-for-woocommerce' ); ?></p>
	<button type='button' class='button button-primary lty-import-popup lty-import-instant-winner-prize-groups-btn' data-action='instant_winner_prize_groups' data-extra_data='<?php echo wp_json_encode( array( 'product_id' => $product->get_id() ) ); ?>'><?php esc_html_e( 'Import', 'lottery-for-woocommerce' ); ?></button>
	<button type='button' class='button button-primary lty-export-popup lty-export-instant-winner-prize-groups-btn' data-export_type='instant_winner_prize_groups' data-extra_data="<?php echo esc_attr( wp_json_encode( array( 'product_id' => $product->get_id() ) ) ); ?>"><?php esc_html_e( 'Export', 'lottery-for-woocommerce' ); ?></button>
	<?php require LTY_ABSPATH . 'inc/admin/menu/views/html-instant-winner-prize-groups-actions.php'; ?>
	<table class='lty-instant-winner-prize-groups-contents wp-list-table widefat striped'>
		<thead>
			<tr>
				<th><input type='checkbox' class='lty-select-all-instant-winner-prize-groups' title="<?php esc_html_e( 'Select All', 'lottery-for-woocommerce' ); ?>"></th>
				<th><b><?php esc_html_e( 'Group Prize Title', 'lottery-for-woocommerce' ); ?></b><?php echo wp_kses_post( wc_help_tip( __( "The entered title will be displayed on the 'Prize Group' dropdown within the 'Instant Win Prizes' settings", 'lottery-for-woocommerce' ) ) ); ?></th>
				<th><b><?php esc_html_e( 'Image', 'lottery-for-woocommerce' ); ?></b></th>
				<th><b><?php esc_html_e( 'Prize Type', 'lottery-for-woocommerce' ); ?></b></th>
				<th><b><?php esc_html_e( 'Group Prize Message', 'lottery-for-woocommerce' ); ?></b><?php echo wp_kses_post( wc_help_tip( __( 'The entered message will be displayed on the instant win prize tab(frontend).', 'lottery-for-woocommerce' ) ) ); ?></th>
				<th><b><?php esc_html_e( 'Action', 'lottery-for-woocommerce' ); ?></b></th>
				<?php
					/**
					 * This hook is used to do extra actions in the instant winner prize groups table column.
					 *
					 * @since 11.1.0
					 * @param object $product Product object.
					 */
					do_action( 'lty_instant_winner_prize_group_column_data', $product );
				?>
			</tr>
		</thead>
		<tbody>
			<?php if ( lty_check_is_array( $prize_group_ids ) ) : ?>
				<?php
				foreach ( $prize_group_ids as $prize_group_id ) :
					$prize_group = lty_get_instant_winner_prize_group( $prize_group_id );
					if ( ! $prize_group->exists() ) :
						continue;
					endif;

					$name = 'lty_instant_winner_prize_groups[' . $prize_group_id . ']';
					?>
					<tr class='lty-instant-winner-prize-group-wrapper'>
						<td>
							<input type='checkbox' name='lty_select_instant_winner_prize_group' class='lty-select-instant-winner-prize-group' />
							<br>
							<small>
								<b><?php esc_html_e( 'ID: ', 'lottery-for-woocommerce' ); ?></b><?php echo esc_html( $prize_group_id ); ?>
							</small>
						</td>
						<td><textarea class='lty-instant-winner-prize-group lty-instant-winner-prize-group-title' name="<?php echo esc_attr( $name ); ?>[ticket_number]"><?php echo wp_kses_post( $prize_group->get_title() ); ?></textarea></td>
						<td>
							<p class='lty-instant-winner-prize-group-image-preview'>
								<img src="<?php echo esc_url( $prize_group->get_image_url() ); ?>" />
							</p>
							<input type='hidden' name="<?php echo esc_attr( $name ); ?>[image_id]" class='lty-instant-winner-prize-group-image-id' value="<?php echo esc_attr( $prize_group->get_image_id() ); ?>"/>
							<span class='dashicons dashicons-upload lty-select-image lty-instant-winner-prize-group' title="<?php esc_attr_e( 'Choose Image', 'lottery-for-woocommerce' ); ?>" ></span>
							<span class='dashicons dashicons-trash lty-remove-image lty-instant-winner-prize-group' title="<?php esc_attr_e( 'Remove', 'lottery-for-woocommerce' ); ?>" style="<?php echo empty( $prize_group->get_image_id() ) ? 'display: none;' : ''; ?>" ></span>
						</td>
						<td>
							<p>
								<select name="<?php echo esc_attr( $name ); ?>[prize_type]" class='lty-instant-winner-prize-group lty-instant-winner-prize-group-prize-type'>
									<?php foreach ( lty_get_instant_winner_prize_type_options() as $option_key => $option_label ) : ?>	
										<option value="<?php echo esc_attr( $option_key ); ?>" <?php selected( $option_key, $prize_group->get_prize_type(), true ); ?> ><?php echo esc_html( $option_label ); ?></option>
									<?php endforeach; ?>
								</select>
							</p>
							<p>
								<select name="<?php echo esc_attr( $name ); ?>[coupon_generation_type]" class='lty-instant-winner-prize-group lty-instant-winner-prize-group-prize-field lty-instant-winner-prize-group-coupon-generation-type'>
									<?php foreach ( lty_get_instant_winner_coupon_generation_type_options() as $option_key => $option_label ) : ?>	
										<option value="<?php echo esc_attr( $option_key ); ?>" <?php selected( $option_key, $prize_group->get_coupon_generation_type(), true ); ?>><?php echo esc_html( $option_label ); ?></option>
									<?php endforeach; ?>
								</select>
								<?php echo wp_kses_post( wc_help_tip( __( 'Selecting the New Coupon option will create a new coupon according to the Instant Winner Coupon Creation Settings (Giveaway -> Settings -> General -> Instant Win). Coupons will only be created when instant winners are assigned.', 'lottery-for-woocommerce' ) ) ); ?>
							</p>
							<p>
								<select name="<?php echo esc_attr( $name ); ?>[coupon_discount_type]" class='lty-instant-winner-prize-group lty-instant-winner-prize-group-prize-field lty-instant-winner-prize-group-coupon-field lty-instant-winner-prize-group-coupon-discount-type'>
									<?php foreach ( lty_get_instant_winner_coupon_discount_type_options() as $option_key => $option_label ) : ?>	
										<option value="<?php echo esc_attr( $option_key ); ?>" <?php selected( $option_key, $prize_group->get_coupon_discount_type(), true ); ?>><?php echo esc_html( $option_label ); ?></option>
									<?php endforeach; ?>
								</select>
							</p>
							<p>
								<?php
								lty_select2_html(
									array(
										'id'          => $name . '[coupon_id]',
										'class'       => 'lty-instant-winner-prize-group lty-instant-winner-prize-group-prize-field lty-instant-winner-prize-group-coupon-field lty-instant-winner-prize-group-coupon-id',
										'action'      => 'lty_json_search_coupons',
										'placeholder' => __( 'Search for a coupon&hellip;', 'lottery-for-woocommerce' ),
										'multiple'    => false,
										'options'     => array( $prize_group->get_coupon_id() ),
									)
								);
								?>
							</p>
							<p>
								<?php
								lty_select2_html(
									array(
										'id'          => $name . '[gift_product_id]',
										'class'       => 'lty-instant-winner-prize-group lty-instant-winner-prize-group-prize-field lty-instant-winner-prize-group-gift-product-field lty-instant-winner-prize-group-gift-product-id',
										'list_type'   => 'products',
										'action'      => 'lty_json_search_products_and_variations',
										'placeholder' => __( 'Search for a product&hellip;' ),
										'exclude_out_of_stock' => 'yes',
										'include_lottery_statuses' => array( 'lty_lottery_started' ),
										'multiple'    => false,
										'options'     => array( $prize_group->get_gift_product_id() ),
									)
								);
								?>
							</p>
							<p>
								<input type='number' min='1' step='1' class='lty-instant-winner-prize-group lty-instant-winner-prize-group-prize-field lty-instant-winner-prize-group-gift-product-field lty-instant-winner-prize-group-gift-product-quantity' name="<?php echo esc_attr( $name ); ?>[gift_product_quantity]" value="<?php echo wp_kses_post( $prize_group->get_gift_product_quantity() ); ?>" placeholder="<?php esc_attr_e( 'Enter the quantity', 'lottery-for-woocommerce' ); ?>">
							</p>
							<p>
								<input type='text' class='wc_input_price lty-instant-winner-prize-group lty-instant-winner-prize-group-prize-field lty-instant-winner-prize-group-coupon-field lty-instant-winner-prize-group-amount' name="<?php echo esc_attr( $name ); ?>[prize_amount]" value="<?php echo wp_kses_post( $prize_group->get_prize_amount() ); ?>" placeholder="<?php esc_attr_e( 'Enter the value', 'lottery-for-woocommerce' ); ?>">
							</p>
							<?php
							/**
							 * This hook is used to do extra actions in the instant winner prize groups table prize type column data.
							 *
							 * @since 11.1.0
							 * @param object $prize_group Instant winner prize group object.
							 * @param object $product Product object.
							 */
							do_action( 'lty_instant_winner_prize_group_prize_type_column_data', $prize_group, $product );
							?>
						</td>
						<td>
							<textarea class='lty-instant-winner-prize-group lty-instant-winner-prize-group-message' name="<?php echo esc_attr( $name ); ?>[prize_message]"><?php echo wp_kses_post( $prize_group->get_prize_message() ); ?></textarea>
						</td>
						<td><span class='lty-remove-instant-winner-prize-group dashicons dashicons-dismiss' data-prize_group_id='<?php echo esc_attr( $prize_group_id ); ?>' title="<?php esc_html_e( 'Remove Group', 'lottery-for-woocommerce' ); ?>"></span></td>
						<?php
							/**
							 * This hook is used to do extra actions in the instant winner prize groups table column data.
							 *
							 * @since 11.1.0
							 * @param object $prize_group Instant winner prize group object.
							 * @param object $product Product object.
							 */
							do_action( 'lty_instant_winner_prize_group_column_data', $prize_group, $product );
						?>
					</tr>
				<?php endforeach; ?>
			<?php else : ?>
				<tr>
					<td colspan='6'><?php esc_html_e( 'Click the "Add New Group" Button to create a new Instant Winner Prize Group', 'lottery-for-woocommerce' ); ?></td>
				</tr>
			<?php endif; ?>
		</tbody>
		<input type='hidden' class='lty-per-page' value="<?php echo esc_attr( $items_per_page ); ?>" />
	</table>
	<input type='hidden' class='lty-unsaved-instant-winner-prize-groups' value=''/>
	<?php require LTY_ABSPATH . 'inc/admin/menu/views/html-instant-winner-prize-groups-actions.php'; ?>
	<script type="text/template" id='tmpl-lty-instant-winner-prize-group'>
		<?php require LTY_ABSPATH . 'inc/admin/menu/views/html-instant-winner-prize-group-popup.php'; ?>
	</script>
</div>
<?php

