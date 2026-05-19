<?php
/**
 * This template is used for displaying the lottery winners by date.
 *
 * This template can be overridden by copying it to yourtheme/lottery-for-woocommerce/shortcodes/lottery-winners-by-date.php
 *
 * To maintain compatibility, Giveaway(formerly Lottery) For WooCommerce will update the template files and you have to copy the updated files to your theme
 * 
 * @since 8.0.0
 */
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}
?>
<div class='lty-lottery-winners-by-date-content'>
	<p class='lty-lottery-winners-date'><?php echo esc_html(LTY_Date_Time::get_wp_format_datetime($date, 'date')); ?></p>
	<?php
	foreach (lty_get_lottery_winner_ids_by_date($date) as $winner_id) :
		$winner = lty_get_lottery_winner($winner_id);
		if (!$winner->exists()) :
			continue;
		endif;

		/* translators: %1$s - Product name, %2$s - User name, %3$s - Ticket Number */
		echo '<p>' . wp_kses_post(sprintf(__('<b>%1$s</b>: %2$s - Ticket #%3$s', 'lottery-for-woocommerce'), $winner->get_product_name(), $winner->display_user_name(), $winner->get_lottery_ticket_number())) . '</p>';
	endforeach;
	?>
</div>



