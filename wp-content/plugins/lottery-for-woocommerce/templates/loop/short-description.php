<?php
/**
 * This template is used for displaying the lottery short description. 
 *
 * This template can be overridden by copying it to yourtheme/lottery-for-woocommerce/loop/short-description.php
 *
 * To maintain compatibility, Giveaway(formerly Lottery) For WooCommerce will update the template files and you have to copy the updated files to your theme
 * 
 * @since 1.0.0
 */
if (!defined('ABSPATH')) {
	exit;
}
?>
<p class='lty-lottery-short-description'>
	<?php echo wp_kses_post($product->get_short_description()); ?>
</p>
