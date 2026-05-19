<?php
/**
 * This template is used for displaying the user billing details
 *
 * This template can be overridden by copying it to yourtheme/lottery-for-woocommerce/email-shortcodes/user-billing-details.php
 *
 * To maintain compatibility, Giveaway(formerly Lottery) For WooCommerce will update the template files and you have to copy the updated files to your theme
 *
 * @since 8.5.0
 */

if (! defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}

if (! is_object($order)) {
	return;
}

$address = $order->get_formatted_billing_address();
$shipping = $order->get_formatted_shipping_address();
?>
<table id='lty_addresses' style="width: 100%; vertical-align: top; margin-bottom: 40px; padding:0;">
	<tr>
		<td style="padding: 0" valign="top" width="50%">
			<b><?php esc_html_e( 'Billing address', 'lottery-for-woocommerce' ); ?></b>

			<address class='lty-address'>
				<?php echo wp_kses_post( $address ? $address : esc_html__( 'N/A', 'lottery-for-woocommerce' ) ); ?>
				<?php if ( $order->get_billing_phone() ) : ?>
					<br/><?php echo wc_make_phone_clickable( $order->get_billing_phone() ); ?>
				<?php endif; ?>
				<?php if ( $order->get_billing_email() ) : ?>
					<br/><?php echo esc_html( $order->get_billing_email() ); ?>
				<?php endif; ?>
			</address>
		</td>
		<?php if (! wc_ship_to_billing_address_only() && $order->needs_shipping_address() && $shipping) : ?>
			<td style="padding: 0" valign="top" width="50%">
				<b><?php esc_html_e('Shipping address', 'lottery-for-woocommerce'); ?></b>

				<address class="address">
					<?php echo wp_kses_post($shipping); ?>
					<?php if ($order->get_shipping_phone()) : ?>
						<br /><?php echo wc_make_phone_clickable($order->get_shipping_phone()); ?>
					<?php endif; ?>
				</address>
			</td>
		<?php endif; ?>
	</tr>
</table>
