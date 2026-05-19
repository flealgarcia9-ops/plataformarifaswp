<?php
/**
 * This template is used for displaying the batch.
 *
 * This template can be overridden by copying it to yourtheme/lottery-for-woocommerce/loop/badge.php
 *
 * To maintain compatibility, Giveaway(formerly Lottery) For WooCommerce will update the template files and you have to copy the updated files to your theme
 * 
 * @since 1.0.0
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit ; // Exit if accessed directly.
}

global $product ;

if ( ! lty_is_lottery_product( $product ) ) {
	return ;
}
?>
<span class="lty-badge"><img src="<?php echo esc_url( lty_get_badge_image() ) ; ?>"></span>


