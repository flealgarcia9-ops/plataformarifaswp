<?php
/**
 * This template is used for displaying the Manual Ticket Search. 
 *
 * This template can be overridden by copying it to yourtheme/lottery-for-woocommerce/single-product/manual-ticket-search.php
 *
 * To maintain compatibility, Giveaway(formerly Lottery) For WooCommerce will update the template files and you have to copy the updated files to your theme
 * 
 * @since 1.0.0
 */
if (!defined('ABSPATH')) {
	exit;
}

global $product;

/**
 * This hook is used to do extra action before manual ticket search container.
 * 
 * @since 1.0
 */
do_action('lty_before_manual_ticket_search_container');
?>
<div class ="lty-lottery-manual-ticket-search-container">

	<input type="text"
		   class="lty-manual-ticket-search"
		   placeholder="<?php esc_html_e('Search for any ticket', 'lottery-for-woocommerce'); ?>">
	<button type="button"
			value="<?php echo esc_attr($product->get_id()); ?>" 
			class="lty-manual-ticket-search-action">
				<?php echo wp_kses_post(get_option('lty_settings_single_product_ticket_search_button_label', 'Search Ticket')); ?>
	</button>
	<button type="button"
			class="lty-manual-ticket-click-to-back-action">
		<?php echo wp_kses_post(get_option('lty_settings_single_product_click_to_back_button_label', 'Go Back')); ?>
	</button>
</div>
<?php
/**
 * This hook is used to do extra action after manual ticket search container.
 * 
 * @since 1.0
 */
do_action('lty_after_manual_ticket_search_container');

