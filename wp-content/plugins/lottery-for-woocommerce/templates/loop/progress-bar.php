<?php
/**
 * This template is used for displaying the progress bar.
 *
 * This template can be overridden by copying it to yourtheme/lottery-for-woocommerce/loop/progress-bar.php
 *
 * To maintain compatibility, Giveaway(formerly Lottery) For WooCommerce will update the template files and you have to copy the updated files to your theme
 *
 * @since 1.0.0
 * @modified 9.1.0
 * @var object $product instanceof WC_Product_Lottery.
 * @var int $progress_bar_percentage Progress bar percentage.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>
<div class='lty-lottery-progress-bar-loop'>
	<div class='lty-progress-count-loop'>
		<span class='lty-progress-bar-start'><?php echo esc_html( 0 ); ?></span>
		<span class='lty-progress-bar-notice'><?php echo wp_kses_post( lty_get_progress_bar_notice( $product ) ); ?></span>
		<span class='lty-progress-bar-end'><?php echo esc_html( round( lty_get_progress_bar_maximum_tickets( $product ) ) ); ?></span>
	</div>
	<?php if ( 'yes' === get_option( 'lty_settings_display_progress_bar_percentage_shop_page' ) && '1' === get_option( 'lty_settings_progress_bar_percentage_display_type_shop_page', '1' ) ) : ?>
		<span class='lty-progress-bar-percentage' style="left:calc(<?php echo esc_attr( $progress_bar_percentage ); ?>% - 20px);"><?php echo esc_attr( $progress_bar_percentage ); ?>%</span>
	<?php endif; ?>
	<div class='lty-progress-bar'>
		<span style="width:<?php echo esc_attr( $progress_bar_percentage ); ?>%;clear: both;"> 
			<span class='lty-progress-fill'>
				<?php if ( '2' === get_option( 'lty_settings_progress_bar_percentage_display_type_shop_page', '1' ) ) : ?>
				<span class='lty-inner-percentage' style="left:calc(<?php echo esc_attr( $progress_bar_percentage > 80 ? 80 : $progress_bar_percentage ); ?>% + 5px);" ><?php echo esc_attr( $progress_bar_percentage ); ?>%</span>
				<?php endif; ?>
			</span>
		</span>
	</div>

	<?php if ( ! lty_hide_progress_bar_ticket_remaining_message() ) : ?>
		<p class='lty-progress-remaining-count'><?php echo wp_kses_post( lty_get_progress_bar_remaining_ticket_label( $product ) ); ?></p>
	<?php endif; ?>
</div>
<?php
