<?php
/**
 * This template is used for displaying the admin lottery details.
 *
 * This template can be overridden by copying it to yourtheme/lottery-for-woocommerce/email-shortcodes/admin-lottery-details.php
 *
 * To maintain compatibility, Giveaway(formerly Lottery) For WooCommerce will update the template files and you have to copy the updated files to your theme
 * 
 * @since 1.0.0
 */
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}
?>
<table class="lty-frontend-table lty-admin-lottery-details">
	<tbody>
		<tr>
			<td><b><?php esc_html_e('Giveaway Start Date: ', 'lottery-for-woocommerce'); ?></b></td>
			<td><?php echo esc_html(LTY_Date_Time::get_wp_format_datetime_from_gmt($product->get_lty_start_date_gmt(), false, ' ', true)); ?></td>
		</tr>
		<tr>
			<td><b><?php esc_html_e('Giveaway End Date: ', 'lottery-for-woocommerce'); ?></b></td>
			<td><?php echo esc_html(LTY_Date_Time::get_wp_format_datetime_from_gmt($product->get_lty_end_date_gmt(), false, ' ', true)); ?></td>
		</tr>
		<tr>
			<td><b><?php esc_html_e('Minimum Tickets: ', 'lottery-for-woocommerce'); ?></b></td>
			<td><?php echo esc_html($product->get_lty_minimum_tickets()); ?></td>
		</tr>
		<tr>
			<td><b><?php esc_html_e('Maximum Tickets: ', 'lottery-for-woocommerce'); ?></b></td>
			<td><?php echo esc_html($product->get_lty_maximum_tickets()); ?></td>
		</tr>

		<?php if ('2' === get_option('lty_settings_quantity_selector_type') && '2' === $product->get_lty_ticket_range_slider_type()) : ?>
			<tr>
				<td><b><?php esc_html_e('Preset Tickets: ', 'lottery-for-woocommerce'); ?></b></td>
				<td><?php echo esc_html($product->get_lty_preset_tickets()); ?></td>
			</tr>
		<?php endif; ?>

		<tr>
			<td><b><?php esc_html_e('Maximum Tickets Per Order: ', 'lottery-for-woocommerce'); ?></b></td>
			<td><?php echo esc_html($product->get_lty_order_maximum_tickets()); ?></td>
		</tr>
		<?php if ('3' != get_option('lty_settings_guest_user_participate_type')) : ?>
			<tr>
				<td><b><?php esc_html_e('Maximum Tickets Per User: ', 'lottery-for-woocommerce'); ?></b></td>
				<td><?php echo esc_html($product->get_lty_user_maximum_tickets()); ?></td>
			</tr>
		<?php endif; ?>
		<tr>
			<td><b><?php esc_html_e('Number of Winner: ', 'lottery-for-woocommerce'); ?></b></td>
			<td><?php echo esc_html($product->get_lty_winners_count()); ?></td>
		</tr>
		<tr>
			<td><b><?php esc_html_e('Price Type: ', 'lottery-for-woocommerce'); ?></b></td>
			<td><?php echo esc_html(lty_get_lottery_price_type_name($product->get_lty_ticket_price_type())); ?></td>
		</tr>
		<?php
		if ('2' != $product->get_lty_ticket_price_type()) :
			?>
			<tr>
				<td><b><?php esc_html_e('Ticket Price: ', 'lottery-for-woocommerce'); ?></b></td>
				<td><?php echo esc_html(lty_price($product->get_price())); ?></td>
			</tr>
			<?php
		endif;
		?>
	</tbody>
</table>
