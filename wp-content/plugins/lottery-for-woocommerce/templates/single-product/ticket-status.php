<?php
/**
 * This template is used for displaying the ticket status.
 *
 * This template can be overridden by copying it to yourtheme/lottery-for-woocommerce/single-product/ticket-status.php
 *
 * To maintain compatibility, Giveaway(formerly Lottery) For WooCommerce will update the template files and you have to copy the updated files to your theme
 *
 * @since 1.0.0
 * @modified 10.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<p class="lty-lottery-status <?php echo esc_attr( $product->get_lty_lottery_status() ); ?>_status">
	<span><?php echo wp_kses_post( lty_display_status( $product->get_lty_lottery_status(), false ) ); ?></span>
</p>
<?php
