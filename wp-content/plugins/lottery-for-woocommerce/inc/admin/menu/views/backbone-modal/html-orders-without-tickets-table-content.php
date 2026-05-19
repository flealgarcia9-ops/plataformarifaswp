<?php
/**
 * Orders Without Tickets Table Content.
 * */
defined('ABSPATH') || exit;
?>

<table class="lty-orders-without-tickets-popup striped widefat">
	<thead>
		<tr>
			<th><b><?php esc_html_e('Order ID', 'lottery-for-woocommerce'); ?></b></th>
			<th><b><?php esc_html_e('Order Status', 'lottery-for-woocommerce'); ?></b></th>
			<th><b><?php esc_html_e('Ticket Numbers', 'lottery-for-woocommerce'); ?></b></th>
			<th><b><?php esc_html_e('Order Date', 'lottery-for-woocommerce'); ?></b></th>
			<th><b><?php esc_html_e('Generate Ticket Numbers Manually', 'lottery-for-woocommerce'); ?></b></th>
		</tr>
	</thead>
	<tbody>
		<?php
		foreach ($order_ids as $order_id) :
			$_order = wc_get_order($order_id);
			if (!is_object($_order)) :
				continue;
			endif;
			?>
			<tr>
				<td>
					<?php
					echo wp_kses_post(sprintf('<a href = "%s" target = "_blank">%s</a > ', esc_url(get_edit_post_link($_order->get_id())), esc_html('#' . $_order->get_id())));
					?>
				</td>
				<td><?php echo wp_kses_post(sprintf('<mark class="order-status status-%s"><span>%s</span></mark>', $_order->get_status(), ucfirst($_order->get_status()))); ?></td>
				<td><?php echo '<span class="lty-no-ticket-numbers-column">-<span>'; ?></td>
				<td>
					<?php
					$order_timestamp = '' != $_order->get_date_created() ? $_order->get_date_created()->date('Y-m-d H:i:s') : '';
					echo wp_kses_post('' != $order_timestamp ? LTY_Date_Time::get_wp_format_datetime_from_gmt($_order->get_date_created()) : '-');
					?>
				</td>
				<td>
					<?php echo wp_kses_post(sprintf('<a href = "%s" target = "_blank">%s</a > ', esc_url(get_edit_post_link($_order->get_id())), __('Click Here', 'lottery-for-woocommerce'))); ?>
				</td>
			</tr>
		<?php endforeach; ?>
	</tbody>
</table>

<div class="lty-orders-without-tickets-message">
	<?php echo wp_kses_post(__('<b>Note:</b> Due to technical issue some order(s) are not having ticket number(s). You can <b>Manually Generate ticket number(s) for the issue occurred order(s).</b> You can see the <b>option in Edit Order Page</b>. This option will display only when the order status matches to the selected order status in giveaway settings(<b>Giveaway -> Settings -> General -> Order status</b>).', 'lottery-for-woocommerce')); ?>
</div>
<?php
