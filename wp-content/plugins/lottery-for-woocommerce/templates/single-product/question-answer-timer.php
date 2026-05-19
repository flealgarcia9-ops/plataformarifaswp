<?php
/**
 * This template is used for displaying the question answer timer. 
 *
 * This template can be overridden by copying it to yourtheme/lottery-for-woocommerce/single-product/question-answer-timer.php
 *
 * To maintain compatibility, Giveaway(formerly Lottery) For WooCommerce will update the template files and you have to copy the updated files to your theme
 * 
 * @since 1.0.0
 */
if (!defined('ABSPATH')) {
	exit;
}
?>
<div class='lty-lottery-question-answer-timer-wrapper'>
	<p class='lty-lottery-question-answer-timer-label'><?php echo wp_kses_post(lty_get_single_product_question_answer_time_limit_label()); ?></p>
	<div class='lty-lottery-countdown-timer' data-time="<?php echo esc_attr($remaining_date); ?>">
		<span class='lty-lottery-timer'><span id='lty_lottery_hours' class='lty-lottery-timer-content'></span><span class='lty-lottery-timer-content'><?php echo wp_kses_post(lty_get_single_product_timer_hours_label()); ?></span></span>
		<span class='lty-lottery-timer'><span id='lty_lottery_minutes' class='lty-lottery-timer-content'></span><span class='lty-lottery-timer-content'><?php echo wp_kses_post(lty_get_single_product_timer_minutes_label()); ?></span></span>
		<span class='lty-lottery-timer'><span id='lty_lottery_seconds' class='lty-lottery-timer-content'></span><span class='lty-lottery-timer-content'><?php echo wp_kses_post(lty_get_single_product_timer_seconds_label()); ?></span></span>
	</div>
</div>
<?php


