<?php
/**
 * This template is used for displaying the lottery instant winners by date.
 *
 * This template can be overridden by copying it to yourtheme/lottery-for-woocommerce/shortcodes/lottery-instant-winners-by-date.php
 *
 * To maintain compatibility, Giveaway(formerly Lottery) For WooCommerce will update the template files and you have to copy the updated files to your theme
 *
 * @since 9.2.0
 * @var string $date Date.
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.
?>
<div class='lty-lottery-instant-winners-by-date-content'>
	<p class='lty-lottery-instant-winners-date'><?php echo esc_html( LTY_Date_Time::get_wp_format_datetime( $date, 'date' ) ); ?></p>
	<?php
	foreach ( lty_get_lottery_instant_winner_log_ids_by_date( $date ) as $instant_winner_log_id ) :
		$instant_winner = lty_get_instant_winner_log( $instant_winner_log_id );
		if ( ! $instant_winner->exists() ) :
			continue;
		endif;

		/* translators: %1$s: Instant win prize, %2$s: User name, %3$s - Ticket Number */
		echo '<p>' . wp_kses_post( sprintf( __( '<b>%1$s</b>: %2$s - Ticket #%3$s', 'lottery-for-woocommerce' ), $instant_winner->get_prize_message(), $instant_winner->display_user_name(), $instant_winner->get_ticket_number() ) ) . '</p>';
	endforeach;
	?>
</div>



