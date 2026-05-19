<?php
/**
 * This template is used for displaying the remaining lottery tickets message.
 *
 * This template can be overridden by copying it to yourtheme/lottery-for-woocommerce/loop/remaining-tickets.php
 *
 * To maintain compatibility, Giveaway(formerly Lottery) For WooCommerce will update the template files and you have to copy the updated files to your theme
 *
 * @since 10.3.0
 * @param object $product Product object.
 */

defined( 'ABSPATH' ) || exit;

?>
<p class='lty-loop-remaining-tickets-message'><?php echo wp_kses_post( lty_get_shop_remaining_tickets_message( $product ) ); ?></p>
