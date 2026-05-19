<?php
/**
 * This template is used for displaying the participate button. 
 *
 * This template can be overridden by copying it to yourtheme/lottery-for-woocommerce/shortcodes/participate-button.php
 *
 * To maintain compatibility, Giveaway(formerly Lottery) For WooCommerce will update the template files and you have to copy the updated files to your theme
 * 
 * @since 1.0.0
 */
if (!defined('ABSPATH')) {
	exit;
}
?>
<button type="submit" name="add-to-cart" value="<?php echo esc_attr($product->get_id()); ?>" class="<?php echo esc_attr(implode(' ', lty_get_add_to_cart_button_classes($product))); ?>"><?php echo wp_kses_post($product->get_participate_now_text()); ?></button>
<?php
	
