<?php
/**
 * This template is used for lottery product search.
 *
 * This template can be overridden by copying it to yourtheme/lottery-for-woocommerce/widgets/lottery-products-search.php
 *
 * To maintain compatibility, Giveaway(formerly Lottery) For WooCommerce will update the template files and you have to copy the updated files to your theme
 * 
 * @since 1.0.0
 */
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}
?>
<form role="search" method="get" class="lty-product-search" action="<?php echo esc_url(home_url('/')); ?>">
	<label><?php esc_html_e('Search for:', 'lottery-for-woocommerce'); ?></label>
	<input type="search" id="lty-product-search-field" 
		   class="search-field lty-product-search-field" 
		   placeholder="<?php echo esc_html__('Search products&hellip;', 'lottery-for-woocommerce'); ?>" 
		   value="<?php echo get_search_query(); ?>" 
		   name="s" />
	<button type="submit" 
			value="<?php echo esc_html__('Search', 'lottery-for-woocommerce'); ?>">
				<?php echo esc_html__('Search', 'lottery-for-woocommerce'); ?>
	</button>
	<input type="hidden" name="lty_product_search"/>
	<input type="hidden" name="post_type" value="product" />
</form>

<?php
