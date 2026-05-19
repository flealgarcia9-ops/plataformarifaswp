<?php
/**
 * Predefined buttons tab.
 * */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$predefined_buttons_rule = is_callable( array( $product_object, 'get_lty_predefined_buttons_rule' ) ) ? $product_object->get_lty_predefined_buttons_rule() : '';
?>
<div id='lty_predefined_buttons_tab' class='panel woocommerce_options_panel'>
	<div class="<?php echo esc_attr( $wrapper_class_name ); ?>">
		<div class='options_group show_if_lottery'>
			<?php
			woocommerce_wp_checkbox(
				array(
					'id'          => '_lty_enable_predefined_buttons',
					'value'       => is_callable( array( $product_object, 'get_lty_enable_predefined_buttons' ) ) ? $product_object->get_lty_enable_predefined_buttons() : '',
					'class'       => 'lty-enable-predefined-buttons',
					'label'       => __( 'Enable Predefined Buttons', 'lottery-for-woocommerce' ),
					'description' => __( 'When enabled, you can define the number of tickets which can be purchased by your users.', 'lottery-for-woocommerce' ),
				)
			);
			woocommerce_wp_checkbox(
				array(
					'label' => __( 'Display Discount Tag for Predefined Buttons', 'lottery-for-woocommerce' ),
					'id'    => 'lty_predefined_buttons_discount_tag',
					'class' => 'lty-predefined-buttons-field',
					'value' => is_callable( array( $product_object, 'get_lty_predefined_buttons_discount_tag' ) ) ? $product_object->get_lty_predefined_buttons_discount_tag() : '',
				)
			);
			woocommerce_wp_checkbox(
				array(
					'label'       => __( 'Display Quantity Selector(Allow user to update quantity)', 'lottery-for-woocommerce' ),
					'id'          => '_lty_predefined_with_quantity_selector',
					'value'       => is_callable( array( $product_object, 'get_lty_predefined_with_quantity_selector' ) ) ? $product_object->get_lty_predefined_with_quantity_selector() : 'no',
					'class'       => 'lty-predefined-buttons-field',
					'description' => __( "When enabled, quantity selector & predefined buttons both are displayed in giveaway product page but users can't use both options simultaneously. Users can use any one(Predefined button or Quantity selector) at a time. Quantity Selector Works based on regular price, if the quantity value matches in predefined discount, then the price will be displayed based on the predefined discount. Otherwise, it displays the price based on the regular price.", 'lottery-for-woocommerce' ),
				)
			);
			woocommerce_wp_checkbox(
				array(
					'label' => __( 'Display Discount Tag for Range Slider', 'lottery-for-woocommerce' ),
					'id'    => 'lty_range_slider_predefined_discount_tag',
					'class' => 'lty-predefined-buttons-field',
					'value' => is_callable( array( $product_object, 'get_lty_range_slider_predefined_discount_tag' ) ) ? $product_object->get_lty_range_slider_predefined_discount_tag() : '',
				)
			);
			?>
		</div>
		<div class='options_group show_if_lottery lty-hide-predefined-buttons-data'>
		<h4><?php esc_html_e( 'Predefined Discount Localization', 'lottery-for-woocommerce' ); ?></h4>
			<p class='form-field'>
				<label><?php esc_html_e( 'Predefined buttons Label', 'lottery-for-woocommerce' ); ?><span class='required'>*</span></label>
				<textarea name='_lty_predefined_buttons_label' class='lty-predefined-buttons-label'><?php echo esc_html( is_callable( array( $product_object, 'get_lty_predefined_buttons_label' ) ) && '' != $product_object->get_lty_predefined_buttons_label() ? $product_object->get_lty_predefined_buttons_label() : 'Buy {ticket_quantity} ticket(s) for {price}' ); ?></textarea>
				<?php echo wp_kses_post( wc_help_tip( __( '<b>Supported Shortcodes<br/>{ticket_quantity}</b> - Displays the Ticket Quantity<br/><b>{price}</b> - Displays the Price<br/><b>{discount}</b> - Displays the Discount', 'lottery-for-woocommerce' ) ) ); ?>
			</p>
			<p class='form-field'>
				<label><?php esc_html_e( 'Predefined Buttons Discount Tag Label', 'lottery-for-woocommerce' ); ?><span class='required'>*</span></label>
				<textarea name='_lty_predefined_buttons_badge_label' class='lty-predefined-buttons-badge-label'><?php echo esc_html( is_callable( array( $product_object, 'get_lty_predefined_buttons_badge_label' ) ) ? $product_object->get_lty_predefined_buttons_badge_label() : __( '{discount} OFF', 'lottery-for-woocommerce' ) ); ?></textarea>
				<?php echo wp_kses_post( wc_help_tip( __( '<b>Supported Shortcodes<br/>{ticket_quantity}</b> - Displays the Ticket Quantity<br/><b>{price}</b> - Displays the Price<br/><b>{discount}</b> - Displays the Discount<br/>You can customize the tag label here(using percentage % & Fixed $)', 'lottery-for-woocommerce' ) ) ); ?>
			</p>
			
			<p class='form-field'>
				<label><?php esc_html_e( 'Range Slider Discount Tag Label', 'lottery-for-woocommerce' ); ?></label>
				<textarea name='lty_range_slider_predefined_discount_label'><?php echo esc_html( is_callable( array( $product_object, 'get_lty_range_slider_predefined_discount_label' ) ) ? $product_object->get_lty_range_slider_predefined_discount_label() : __( '{discount} OFF', 'lottery-for-woocommerce' ) ); ?></textarea>
				<?php echo wp_kses_post( wc_help_tip( __( '<b>Supported Shortcodes<br/>{ticket_quantity}</b> - Displays the Ticket Quantity<br/><b>{price}</b> - Displays the Price<br/><b>{discount}</b> - Displays the Discount<br/>You can customize the tag label here(using the symbols like percentage % or fixed $)', 'lottery-for-woocommerce' ) ) ); ?>
			</p>
		</div>
		<div class='lty-predefined-buttons-wrapper lty-hide-predefined-buttons-data'>
			<h4><?php esc_html_e( 'Predefined Discount Rule Settings', 'lottery-for-woocommerce' ); ?></h4>
			<?php
			woocommerce_wp_select(
				array(
					'id'      => '_lty_predefined_buttons_selection_type',
					'class'   => '_lty_predefined_buttons_selection_type',
					'label'   => __( 'Select Discount Type', 'lottery-for-woocommerce' ),
					'value'   => is_callable( array( $product_object, 'get_lty_predefined_buttons_selection_type' ) ) ? $product_object->get_lty_predefined_buttons_selection_type() : '1',
					'options' => array(
						'1' => __( 'Percentage', 'lottery-for-woocommerce' ),
						'2' => __( 'Fixed Price', 'lottery-for-woocommerce' ),
					),
				)
			);
			?>
			<table class='lty-predefined-buttons-table lty-backend-table'>
				<thead>
					<tr>
						<th><?php esc_html_e( 'ID', 'lottery-for-woocommerce' ); ?></th>
						<th><?php esc_html_e( 'Ticket Quantity', 'lottery-for-woocommerce' ); ?></th>
						<th>
							<?php esc_html_e( 'Discount in % / Fixed Price', 'lottery-for-woocommerce' ); ?>
							<?php echo wc_help_tip( __( 'If left empty, then the product price will be used for price calculation', 'lottery-for-woocommerce' ) ); ?>
						</th>
						<th><?php esc_html_e( 'Remove', 'lottery-for-woocommerce' ); ?></th>
					</tr> 
				</thead>

				<tbody>
					<?php
					if ( isset( $predefined_buttons_rule['predefined_buttons'] ) && lty_check_is_array( $predefined_buttons_rule['predefined_buttons'] ) ) {
						foreach ( $predefined_buttons_rule['predefined_buttons'] as $predefined_button_id => $button_data ) {
							$name = "_lty_predefined_buttons_rule[predefined_buttons][{$predefined_button_id}]";
							?>
							<tr>
								<td><span class='lty-rule-id'>#<?php echo esc_attr( $predefined_button_id ); ?></span></td>
								<td><input type='number' class='lty-predefined-button-ticket-quantity' name="<?php echo esc_attr( $name ); ?>[ticket_quantity]" value="<?php echo esc_attr( $button_data['ticket_quantity'] ); ?>"></td>
								<td>
									<input type='text' class='lty-discount-percentage wc_input_price' name="<?php echo esc_attr( $name ); ?>[discount_percentage]" value="<?php echo esc_attr( $button_data['discount_percentage'] ); ?>">
									<input type='text' class='lty-fixed-price wc_input_price' name="<?php echo esc_attr( $name ); ?>[fixed_price]" value="<?php echo esc_attr( $button_data['fixed_price'] ); ?>">
								<td>
									<input type='hidden' class='lty-predefined-rule-id' value="<?php echo esc_attr( $predefined_button_id ); ?>"/>
									<a class='lty-remove-predefined-button-rule button'><?php esc_html_e( 'Remove', 'lottery-for-woocommerce' ); ?></a>
								</td>
							</tr>
							<?php
						}
					}
					?>
				</tbody>
				<tfoot>
					<tr>
						<td colspan='4'>
							<a class='lty-add-predefined-button-rule button'><?php esc_html_e( 'Add button', 'lottery-for-woocommerce' ); ?></a>
						</td> 
					</tr>
				</tfoot>
			</table>
			<script type='text/html' id='tmpl-lty-predefined-button'>
				<?php require LTY_ABSPATH . 'inc/admin/menu/views/html-product-data-predefined-buttons-table-data.php'; ?>
			</script>
		</div>
	</div>
</div>
<?php
