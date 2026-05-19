<?php
/**
 * This template is used for displaying the date ranges.
 *
 * This template can be overridden by copying it to yourtheme/lottery-for-woocommerce/single-product/date-ranges.php
 *
 * To maintain compatibility, Giveaway(formerly Lottery) For WooCommerce will update the template files and you have to copy the updated files to your theme
 *
 * @since 1.0.0
 * @var object $product Product object.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>
<div class='lty-lottery-date-ranges'>
	<?php
	if ( ! $product->is_started() ) :
		if ( lty_display_date_starts_on_label_in_single_product() ) :
			?>
			<p class='lty-lottery-start-time-label'>
				<span><?php echo wp_kses_post( lty_get_single_product_page_start_label( $product->get_id(), $product ) ); ?></span>
			</p>
			<?php
		endif;
	elseif ( lty_display_date_ends_on_label_in_single_product() ) :
		?>
		<p class='lty-lottery-end-time-label'>
			<span><?php echo wp_kses_post( lty_get_single_product_page_end_label( $product ) ); ?></span>
		</p>
		<?php
	endif;
	?>

	<p class='lty-lottery-time-left-label'><?php echo wp_kses_post( $product->get_date_ranges_text() ); ?></p>
	<div class='lty-lottery-countdown-timer' data-time="<?php echo esc_attr( $product->get_countdown_timer_enddate() ); ?>" >
		<span class='lty-lottery-timer'><span id='lty_lottery_days' class='lty-lottery-timer-content'></span><span class='lty-lottery-timer-content'><?php echo wp_kses_post( lty_get_single_product_timer_days_label() ); ?></span></span>
		<span class='lty-lottery-timer'><span id='lty_lottery_hours' class='lty-lottery-timer-content'></span><span class='lty-lottery-timer-content'><?php echo wp_kses_post( lty_get_single_product_timer_hours_label() ); ?></span></span>
		<span class='lty-lottery-timer'><span id='lty_lottery_minutes' class='lty-lottery-timer-content'></span><span class='lty-lottery-timer-content'><?php echo wp_kses_post( lty_get_single_product_timer_minutes_label() ); ?></span></span>
		<span class='lty-lottery-timer'><span id='lty_lottery_seconds' class='lty-lottery-timer-content'></span><span class='lty-lottery-timer-content'><?php echo wp_kses_post( lty_get_single_product_timer_seconds_label() ); ?></span></span>
	</div>	
</div>
<?php
