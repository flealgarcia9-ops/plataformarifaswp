<?php
/**
 * This template is used for displaying the countdown timer.
 *
 * This template can be overridden by copying it to yourtheme/lottery-for-woocommerce/loop/countdown-timer.php
 *
 * To maintain compatibility, Giveaway(formerly Lottery) For WooCommerce will update the template files and you have to copy the updated files to your theme
 * 
 * @since 1.0.0
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit ; // Exit if accessed directly.
}

global $product ;

if ( ! is_object( $product ) ) {
	return ;
}

if ( $product->is_closed() ) {
	return ;
}

if ( ! $product->is_started() ) :
	if ( lty_display_starts_on_label_in_shop() ) :
		?>
		<p class="lty-lottery-time-left-label-shop-page"> <?php echo wp_kses_post( lty_get_shop_page_start_label() ) ; ?></p>
		<?php
	endif ;
elseif ( lty_display_ends_on_label_in_shop() ) :
	?>
		<p class="lty-lottery-time-left-label-shop-page"> <?php echo wp_kses_post( lty_get_shop_page_end_label() ) ; ?></p>
		<?php

endif ;
?>

<div class='lty-shop-timer-wrapper'>
	<span class='lty-shop-timer-container lty-lottery-countdown-timer' data-time="<?php echo esc_attr( $product->get_countdown_timer_enddate() ) ; ?>">
		<span class='lty-shop-timer-section'><span id='lty_lottery_days' class='lty-shop-timer-content'></span><span class='lty-shop-timer-content'><?php echo wp_kses_post( lty_get_shop_page_timer_days_label() ) ; ?></span></span>
		<span class='lty-shop-timer-section'><span id='lty_lottery_hours' class='lty-shop-timer-content'></span><span class='lty-shop-timer-content'><?php echo wp_kses_post( lty_get_shop_page_timer_hours_label() ) ; ?></span></span>
		<span class='lty-shop-timer-section'><span id='lty_lottery_minutes' class='lty-shop-timer-content'></span><span class='lty-shop-timer-content'><?php echo wp_kses_post( lty_get_shop_page_timer_minutes_label() ) ; ?></span></span>
		<span class='lty-shop-timer-section'><span id='lty_lottery_seconds' class='lty-shop-timer-content'></span><span class='lty-shop-timer-content'><?php echo wp_kses_post( lty_get_shop_page_timer_seconds_label() ) ; ?></span></span>
	</span>
</div>


