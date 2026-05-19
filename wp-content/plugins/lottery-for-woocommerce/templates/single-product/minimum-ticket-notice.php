<?php
/**
 * This template is used for displaying the minimum tickets notice.
 *
 * This template can be overridden by copying it to yourtheme/lottery-for-woocommerce/single-product/minimum-ticket-notice.php
 *
 * To maintain compatibility, Giveaway(formerly Lottery) For WooCommerce will update the template files and you have to copy the updated files to your theme
 *
 * @since 1.0.0
 * @modified 9.2.0
 * @var object $product Product object.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

?>
<p class='lty-minimum-ticket-notice'><?php echo wp_kses_post( lty_get_lottery_ticket_minimum_message( $product ) ); ?></p>
<?php

