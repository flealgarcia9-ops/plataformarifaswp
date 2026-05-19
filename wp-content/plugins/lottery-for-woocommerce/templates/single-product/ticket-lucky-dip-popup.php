<?php
/**
 * This template is used for displaying ticket lucky dip popup. 
 *
 * This template can be overridden by copying it to yourtheme/lottery-for-woocommerce/single-product/ticket-lucky-dip-popup.php
 *
 * To maintain compatibility, Giveaway(formerly Lottery) For WooCommerce will update the template files and you have to copy the updated files to your theme
 * 
 * @since 1.0.0
 * @modified 11.5.0
 * @param array $ticket_numbers Ticket numbers.
 * @param int $quantity Quantity.
 */
if (!defined('ABSPATH')) {
	exit;
}

/**
 * This hook is used to do extra action before lucky tip popup information.
 * 
 * @since 1.0
 */
do_action('lty_before_lottery_ticket_lucky_dip_popup_info');
?>
<div class='lty-ticket-lucky-dip-popup-wrapper lty-ticket-lucky-dip-popup-wrapper lty-lottery-ticket-lucky-dip-container'>
	<div class='lty-lucky-dip-tickets'>
		<?php echo wp_kses_post( lty_get_lucky_dip_added_to_cart_message( $ticket_numbers ) ); ?>
	</div>
	<a href="#" class="button alt lty-add-more-lucky-tip"><?php echo wp_kses_post( lty_get_single_product_add_more_lucky_dip_button_label() ); ?></a>

	<a href="<?php echo esc_url( wc_get_cart_url() ); ?>" class="button alt lty-view-cart"><?php echo wp_kses_post( lty_get_single_product_lucky_dip_view_cart_button_label() ); ?></a>
	<input type='hidden' class='lty-lucky-dip-quantity' value='<?php echo esc_attr( $quantity ); ?>'/>
</div>
<?php
/**
 * This hook is used to do extra action after lucky tip popup information.
 * 
 * @since 1.0
 */
do_action('lty_after_lottery_ticket_lucky_dip_popup_info');
