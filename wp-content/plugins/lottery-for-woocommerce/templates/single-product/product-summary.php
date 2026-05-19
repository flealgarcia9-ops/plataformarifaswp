<?php
/**
 * This template is used for displaying the product summary.
 *
 * This template can be overridden by copying it to yourtheme/lottery-for-woocommerce/single-product/product-summary.php
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

/**
 * This hook is used to do extra action before lottery single product summary.
 *
 * @since 1.0.0
 */
do_action( 'lty_lottery_before_single_product_summary' );
?>
<div class="lty-lottery-product-summary">
	<?php
	/**
	 * This hook is used to display the lottery single product content.
	 *
	 * @hooked LTY_Lottery_Single_Product_Templates::render_tickets_status_template - 5.
	 * @hooked LTY_Lottery_Single_Product_Templates::render_date_ranges_template - 10.
	 * @hooked LTY_Lottery_Single_Product_Templates::render_failed_reason_notice_template - 10.
	 * @hooked LTY_Lottery_Single_Product_Templates::render_minimum_tickets_notice_template - 15.
	 * @hooked LTY_Lottery_Single_Product_Templates::render_maximum_tickets_notice_template - 15.
	 * @hooked LTY_Lottery_Single_Product_Templates::render_minimum_tickets_per_user_notice_template - 15.
	 * @hooked LTY_Lottery_Single_Product_Templates::render_maximum_tickets_per_user_notice_template - 15.
	 * @hooked LTY_Lottery_Single_Product_Templates::render_winner_message_template - 15.
	 * @hooked LTY_Lottery_Single_Product_Templates::render_loser_message_template - 15.
	 * @hooked LTY_Lottery_Single_Product_Templates::render_gift_product_notice_template - 20.
	 * @hooked LTY_Lottery_Single_Product_Templates::render_waiting_for_result_template - 20.
	 * @hooked LTY_Lottery_Single_Product_Templates::render_winner_log_template - 20.
	 * @hooked LTY_Lottery_Single_Product_Templates::render_tickets_sold_notice_template - 25.
	 * @hooked LTY_Lottery_Single_Product_Templates::render_progress_bar_template - 30.
	 * @hooked LTY_Lottery_Single_Product_Templates::render_guest_error_notice_template - 35.
	 * @hooked LTY_Lottery_Single_Product_Templates::render_winners_count_template - 40.
	 * @since 1.0.0
	 */
	do_action( 'lty_lottery_single_product_content', $product );
	?>
</div>
<div class="lty-clear"></div>
<?php
/**
 * This hook is used to do extra action after lottery single product summary.
 *
 * @since 1.0
 */
do_action( 'lty_lottery_after_single_product_summary' );
