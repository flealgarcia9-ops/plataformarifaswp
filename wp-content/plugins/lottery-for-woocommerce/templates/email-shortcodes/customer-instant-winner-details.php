<?php
/**
 * This template is used for displaying the customer lottery instant winner details.
 *
 * This template can be overridden by copying it to yourtheme/lottery-for-woocommerce/email-shortcodes/customer-instant-winner-details.php
 *
 * To maintain compatibility, Giveaway(formerly Lottery) For WooCommerce will update the template files and you have to copy the updated files to your theme.
 *
 * @since 8.0.0
 * @modified 10.6.0
 * @var string $ticket_number Ticket number.
 * @var int $order_id Order ID.
 * @var array $columns Column labels.
 * @var object $instant_winner_log Instant winner log object.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>
<table class='lty-customer-email-instant-winner-details'>
	<tbody>
		<?php foreach ( $columns as $column_key => $column_label ) : ?>
		<tr>
			<td><b><?php echo esc_html( $column_label ); ?>:</b></td>
			<td>
				<?php
				switch ( $column_key ) :
					case 'order_id':
						printf( '<a href="%s">#%s</a>', esc_url( wc_get_endpoint_url( 'view-order', $order_id, get_permalink( wc_get_page_id( 'myaccount' ) ) ) ), esc_attr( $order_id ) );
						break;

					case 'ticket_number':
						echo esc_html( $ticket_number );
						break;

					case 'prize':
						echo wp_kses_post( $instant_winner_log->get_prize_message() );
						break;

					case 'coupon_code':
						echo wp_kses_post( $instant_winner_log->get_coupon_code() );
						break;

					case 'coupon_value':
						echo wp_kses_post( $instant_winner_log->get_formatted_coupon_value() );
						break;

					case 'coupon_expiry':
						$coupon             = new WC_Coupon( $instant_winner_log->get_coupon_code() );
						$coupon_expiry_date = 'N/A';
						if ( is_object( $coupon ) ) {
							$coupon_expiry_date = ! empty( $coupon->get_date_expires() ) ? LTY_Date_Time::get_wp_format_datetime( $coupon->get_date_expires() ) : 'N/A';
						}

						echo wp_kses_post( $coupon_expiry_date );
						break;

					case 'credit_value':
						lty_price( $instant_winner_log->get_prize_amount(), true );
						break;
				endswitch;
				?>
			</td>
		</tr>
		<?php endforeach; ?>
	</tbody>
</table>
