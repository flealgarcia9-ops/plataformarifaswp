<?php
/**
 * This template is used for displaying the maximum tickets per user notice.
 *
 * This template can be overridden by copying it to yourtheme/lottery-for-woocommerce/single-product/maximum-tickets-per-user-notice.php
 *
 * To maintain compatibility, Giveaway(formerly Lottery) For WooCommerce will update the template files and you have to copy the updated files to your theme
 *
 * @since 1.0.0
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>
<p class="lty-maximum-tickets-per-user-notice"><?php echo wp_kses_post( lty_get_lottery_maximum_tickets_per_user_message( $product ) ); ?></p>
<?php
