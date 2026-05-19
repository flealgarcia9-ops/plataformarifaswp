<?php
/**
 * This template is used for displaying the customer lottery winner details for recent order.
 *
 * This template can be overridden by copying it to yourtheme/lottery-for-woocommerce/email-shortcodes/admin-lottery-winner-details-order.php
 *
 * To maintain compatibility, Giveaway(formerly Lottery) For WooCommerce will update the template files and you have to copy the updated files to your theme
 * 
 * @since 8.3.0
 */
if (! defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}
?>

<table class="lty-frontend-table lty-admin-lottery-details">
	<tbody>
		<tr>
			<th style='padding-right: 15px; text-align: left;'><b><?php esc_html_e('Product Name', 'lottery-for-woocommerce'); ?></b></td>
			<th style='text-align: left;'><b><?php esc_html_e('Ticket Numbers', 'lottery-for-woocommerce'); ?></b></td>
		</tr>
		<?php
		foreach ($tickets_data as $product_id => $ticket_numbers) :
			$product = wc_get_product($product_id);
			
			if (! is_object($product) || 'lottery' !== $product->get_type()) :
				continue;
			endif;

			if (! lty_check_is_array($ticket_numbers)) :
				continue;
			endif;
			?>
			<tr>
				<td style='padding-right: 15px;'><?php printf('<a href="%s">%s</a>', esc_url($product->get_permalink()), esc_html($product->get_name())); ?></td>
				<td><?php echo esc_html(implode(', ', $ticket_numbers)); ?></td>
			</tr>
			<?php
		endforeach;
		?>
	</tbody>
</table>
