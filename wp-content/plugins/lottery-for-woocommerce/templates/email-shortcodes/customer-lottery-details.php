<?php
/**
 * This template is used for displaying the customer lottery details.
 *
 * This template can be overridden by copying it to yourtheme/lottery-for-woocommerce/email-shortcodes/customer-lottery-details.php
 *
 * To maintain compatibility, Giveaway(formerly Lottery) For WooCommerce will update the template files and you have to copy the updated files to your theme
 * 
 * @since 1.0.0
 */
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}
?>
<table class="lty-frontend-table lty-customer-lottery-details">
	<tbody>
		<tr>
			<td><b><?php esc_html_e('Ticket Number: ', 'lottery-for-woocommerce'); ?></b></td>
			<td><?php echo esc_html(implode(' , ', (array) $ticket_numbers)); ?></td>
		</tr>
		<tr>
			<td><b><?php esc_html_e('Quantity: ', 'lottery-for-woocommerce'); ?></b></td>
			<td><?php echo esc_html(count((array) $ticket_numbers)); ?></td>
		</tr>
		<tr>
			<td><b><?php esc_html_e('Order ID: ', 'lottery-for-woocommerce'); ?></b></td>
			<td><?php printf('<a href="%s">#%s</a>', esc_url(wc_get_endpoint_url('view-order', $order_id, get_permalink(wc_get_page_id('myaccount')))), esc_attr($order_id)); ?></td>
		</tr>
	</tbody>
</table>
