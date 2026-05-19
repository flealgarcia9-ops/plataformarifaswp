<?php
/**
 * This template is used for displaying the winners count notice. 
 *
 * This template can be overridden by copying it to yourtheme/lottery-for-woocommerce/single-product/winners-count-notice.php
 *
 * To maintain compatibility, Giveaway(formerly Lottery) For WooCommerce will update the template files and you have to copy the updated files to your theme
 * 
 * @since 1.0.0
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit ; // Exit if accessed directly.
}

global $product ;

if ( ! $product->is_started() || $product->is_closed() ) {
	return ;
}
?>
<p class="lty-winners-count"><?php echo wp_kses_post( lty_get_single_product_page_winners_count_label( $product ) ) ; ?></p>
<?php

