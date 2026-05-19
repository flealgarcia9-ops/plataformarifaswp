<?php
/**
 * This template is used for displaying the lottery customer order instant winner details.
 *
 * This template can be overridden by copying it to yourtheme/lottery-for-woocommerce/email-shortcodes/customer-instant-winner-details-order.php
 *
 * To maintain compatibility, Giveaway(formerly Lottery) For WooCommerce will update the template files and you have to copy the updated files to your theme.
 * 
 * @since 10.4.0
 */
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}
?>
<table class='lty-customer-order-instant-winner-details' style='border-collapse: collapse;border: 1px solid #ccc;'>
	<tbody>
		<tr>
			<th class="lty-customer-order-instant-winner-head" style="padding: 10px; background:#f1f1f1; border-right: 1px solid #ccc; border-bottom: 1px solid #ccc;"><b><?php esc_html_e('Product Name', 'lottery-for-woocommerce'); ?></b></td>
			<th class="lty-customer-order-instant-winner-head" style="padding: 10px; background:#f1f1f1; border-right: 1px solid #ccc; border-bottom: 1px solid #ccc;"><b><?php esc_html_e('Ticket Number', 'lottery-for-woocommerce'); ?></b></td>
			<th class="lty-customer-order-instant-winner-head" style="padding: 10px; background:#f1f1f1; border-right: 1px solid #ccc; border-bottom: 1px solid #ccc;"><b><?php esc_html_e('Prize', 'lottery-for-woocommerce'); ?></b></td>
		</tr>
		<?php
		if ( ! lty_check_is_array( $ticket_ids ) ) {
			return;
		}
		foreach ($ticket_ids as $ticket_id) :
			$lottery_ticket = lty_get_lottery_ticket( $ticket_id );
			if ( ! is_object( $lottery_ticket ) ) {
				continue;
			}
			?>
			<tr>
				<td class='lty-customer-order-instant-winner-table-data' style="padding: 10px; border-right: 1px solid #ccc; border-bottom: 1px solid #ccc;"><?php echo wp_kses_post($lottery_ticket->get_product_name( true )); ?></td>
				<td class='lty-customer-order-instant-winner-table-data' style="padding: 10px; border-right: 1px solid #ccc; border-bottom: 1px solid #ccc;"><?php echo esc_html($lottery_ticket->get_lottery_ticket_number()); ?></td>
				<td class='lty-customer-order-instant-winner-table-data' style="padding: 10px; border-right: 1px solid #ccc; border-bottom: 1px solid #ccc;"><?php echo wp_kses_post($lottery_ticket->get_instant_winner_ticket_price()); ?></td>
			</tr>
			<?php
		endforeach;
		?>
		<tr>
			<td><b><?php esc_html_e('Order Number: ', 'lottery-for-woocommerce'); ?></b></td>
			<td><?php printf('<a href="%s">#%s</a>', esc_url(wc_get_endpoint_url('view-order', $order_id, get_permalink(wc_get_page_id('myaccount')))), esc_attr($order_id)); ?></td>
		</tr>
	</tbody>
</table>
