<?php
/**
 * Automatic ticket summary.
 * 
 * @since 11.1.0
 * */
if ( ! defined( 'ABSPATH' ) ) {
	exit ; // Exit if accessed directly.
}

?>
<div class ='lty-lottery-manual-ticket-container lty-lottery-ticket-container-order-item' >
<div class='lty-lottery-ticket-wrapper'>
	<div class='lty-lottery-ticket-tab-wrapper'>
		<label for='lty_ticket_generation_type' class='lty_ticket_generation_type'>
			<?php esc_html_e( 'Assign Ticket Number(s) Method:', 'lottery-for-woocommerce' ); ?>
		</label>
		<select id='lty_ticket_generation_mode' name='lty_ticket_generation_mode' class='lty_ticket_generation_mode'>
			<option value="1" selected>
				<?php esc_html_e( 'Automatic(Random)', 'lottery-for-woocommerce' ); ?>
			</option>
			<option value="2">
				<?php esc_html_e( 'Enter Ticket Number(s)', 'lottery-for-woocommerce' ); ?>
			</option>
		</select>
	</div>

	<div class='lty-manual-order-lottery-ticket-container' style="display: none;">
		<?php 
			echo '<label>' . esc_html__('Enter Ticket Number(s):', 'lottery-for-woocommerce') . '</label>';
			echo wc_help_tip( esc_html__('Here, you can enter the ticket number(s) manually. You can use "," comma separator for entering more than one ticket number.', 'lottery-for-woocommerce') );
		?>
		<textarea 
		class='lty-lottery-custom-ticket-field'
		name='lty-lottery-custom-ticket-field'
		placeholder="<?php echo esc_attr__('Enter ticket number(s) separated by commas', 'lottery-for-woocommerce'); ?>"
		></textarea>
		<span id='lty-remaining-ticket-count-message'><?php echo wp_kses_post( lty_get_lottery_remaining_ticket_count_message( $item->get_quantity() ) ); ?></span>
	</div>

	<?php
		/**
		 * This hook is used to do extra action after lottery ticket order item.
		 * 
		 * @since 1.0
		 */
		do_action( 'lty_after_lottery_ticket_order_item', $product ) ;
	?>

	<input type="hidden" class='lty-ticket-order-id'  value="<?php echo esc_attr( $order->get_id() ) ; ?>">
	<input type="hidden" class='lty-ticket-item-id'  value="<?php echo esc_attr( $item_id ) ; ?>">
	<input type="hidden" class='lty-lottery-ticket-quantity' value="<?php echo esc_attr( $item->get_quantity() ); ?>">
	<input type="hidden" class='lty-ticket-product-id' value="<?php echo esc_attr( $product->get_id() ) ; ?>">
</div>
</div>
<?php

?>
