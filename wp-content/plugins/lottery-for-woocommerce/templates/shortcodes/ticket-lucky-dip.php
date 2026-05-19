<?php
/**
 * This template is used for displaying the ticket lucky dip predefined shortcodes. 
 *
 * This template can be overridden by copying it to yourtheme/lottery-for-woocommerce/shortcodes/ticket-lucky-dip.php
 *
 * To maintain compatibility, Giveaway(formerly Lottery) For WooCommerce will update the template files and you have to copy the updated files to your theme
 * 
 * @since 11.4.0
 */
if (!defined('ABSPATH')) {
	exit;
}

/**
 * This hook is used to do extra action before lottery lucky dip container.
 * 
 * @since 1.0
 */
do_action('lty_before_lottery_ticket_lucky_dip_container');
?>
<div class ='lty-lottery-ticket-lucky-dip-container lty-shortcode-lottery-ticket-lucky-dip-container'>
<input type='hidden' class='lty-lucky-dip-quantity' value='<?php echo esc_attr( $quantity ); ?>'/>
<input type='hidden' class='lty-lucky-dip-fixed-quantity' value='yes' />
	<button type='button' title='<?php echo esc_attr(lty_lucky_dip_question_answer_hover_message($product)); ?>' value='<?php echo esc_attr($product->get_id()); ?>' 
			class='<?php echo esc_attr(implode(' ', lty_get_lucky_dip_button_classes($product))); ?>'>
				<?php echo wp_kses_post($product->get_lucky_dip_fixed_quantity_text($quantity)); ?>
	</button>
	<input type='hidden' class='lty-ticket-product-id' value='<?php echo esc_attr( $product->get_id() ); ?>'/>
</div>
<?php
/**
 * This hook is used to do extra action after lottery lucky dip container.
 * 
 * @since 1.0
 */
do_action('lty_after_lottery_ticket_lucky_dip_container');
