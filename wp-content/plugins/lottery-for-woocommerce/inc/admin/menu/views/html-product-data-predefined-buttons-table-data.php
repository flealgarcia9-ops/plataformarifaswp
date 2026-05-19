<?php
/**
 * Predefined buttons table data.
 * */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$name = '_lty_predefined_buttons_rule[predefined_buttons][{{data.predefined_button_id}}]';
?>
<tr>
	<td><span class='lty-rule-id'>#{{data.predefined_button_id}}</span></td>
	<td><input type='number' class='lty-predefined-button-ticket-quantity' name="<?php echo esc_attr( $name ); ?>[ticket_quantity]"></td>
	<td>
		<input type='text' class='lty-discount-percentage wc_input_price' name="<?php echo esc_attr( $name ); ?>[discount_percentage]">
		<input type='text' class='lty-fixed-price wc_input_price' name="<?php echo esc_attr( $name ); ?>[fixed_price]">
	<td>
		<input type='hidden' class='lty-predefined-rule-id' value="{{data.predefined_button_id}}"/>
		<a class='lty-remove-predefined-button-rule button'><?php esc_html_e( 'Remove', 'lottery-for-woocommerce' ); ?></a>
	</td>
</tr>
<?php
