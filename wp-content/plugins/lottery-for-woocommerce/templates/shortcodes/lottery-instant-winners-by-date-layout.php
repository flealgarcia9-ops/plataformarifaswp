<?php
/**
 * This template is used for displaying the lottery instant winners by date layout.
 *
 * This template can be overridden by copying it to yourtheme/lottery-for-woocommerce/shortcodes/lottery-instant-winners-by-date-layout.php
 *
 * To maintain compatibility, Giveaway(formerly Lottery) For WooCommerce will update the template files and you have to copy the updated files to your theme
 *
 * @since 9.2.0
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

?>
<div class='lty-lottery-instant-winners-by-date-wrapper'>
	<div class='lty-lottery-instant-winners-by-date-inner-wrapper'>
		<?php foreach ( $lottery_instant_winning_dates as $date ) : ?>
			<?php lty_get_template( 'shortcodes/lottery-instant-winners-by-date.php', array( 'date' => $date ) ); ?>
		<?php endforeach; ?>
	</div>

	<?php if ( $paginate && $pagination['page_count'] > 1 ) : ?>
		<div class='lty-lottery-instant-winners-by-date-footer'>
			<input type='hidden' class='lty-pagination-per-page' value='<?php echo esc_html( $per_page ); ?>'/>
			<input type='hidden' class='lty-pagination-date-filter-number' value='<?php echo esc_html( $date_filter_number ); ?>'/>
			<input type='hidden' class='lty-pagination-date-filter-unit' value='<?php echo esc_html( $date_filter_unit ); ?>'/>
			<input type='hidden' class='lty-pagination-order' value='<?php echo esc_html( $order ); ?>'/>
			<?php lty_get_template( 'pagination.php', $pagination ); ?>
		</div>
	<?php endif; ?>
</div>



