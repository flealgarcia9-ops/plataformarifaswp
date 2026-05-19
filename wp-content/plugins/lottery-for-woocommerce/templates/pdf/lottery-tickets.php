<?php
/**
 * This template is used to customize the lottery tickets template.
 *
 * This template can be overridden by copying it to yourtheme/lottery-for-woocommerce/pdf/lottery-tickets.php
 *
 * To maintain compatibility, Giveaway(formerly Lottery) For WooCommerce will update the template files and you have to copy the updated files to your theme
 *
 * @since 9.5.0
 * @var array $ticket_ids Ticket IDs.
 */

defined( 'ABSPATH' ) || exit;
?>
<div class='lty-lottery-tickets-wrapper'>
<?php
foreach ( $ticket_ids as $ticket_id ) :
	$ticket = lty_get_lottery_ticket( $ticket_id );
	if ( ! is_object( $ticket ) || ! is_object( $ticket->get_order() ) ) :
		continue;
	endif;
	?>
	<table class='lty-lottery-ticket-wrapper' style='width: 100%; border: 1px dashed #ddd;
		background: linear-gradient(<?php echo esc_attr( lty_get_ticket_pdf_bg_color_ratio() ); ?>deg, <?php echo esc_attr( lty_get_ticket_pdf_bg_color_left() ); ?> 50%,<?php echo esc_attr( lty_get_ticket_pdf_bg_color_right() ); ?> 50%);'>
		<tr>
			<td style='width: 57%;'>
				<table class='lty-lottery-ticket-content'>
					<tr><td><h4 class='lty-lottery-ticket-title' ><strong><?php esc_html_e( 'Giveaway Ticket', 'lottery-for-woocommerce' ); ?></strong></h4></td></tr>
					<tr><td><span class='lty-product-name' ><b><?php echo esc_html( $ticket->get_product_name() ); ?></b></span></td></tr>
					<tr><td><span class='lty-ticket-number' ><b><?php esc_html_e( 'Ticket Number : ', 'lottery-for-woocommerce' ); ?></b><?php echo esc_html( $ticket->get_lottery_ticket_number() ); ?></span></td></tr>
					<tr><td><span class='lty-purchased-date' ><b><?php esc_html_e( 'Date : ', 'lottery-for-woocommerce' ); ?></b><?php echo esc_html( $ticket->get_formatted_created_date() ); ?></span></td></tr>
				</table>
			</td>
			<td style='width: 43%;'>
				<table class='lty-lottery-ticket-content' style="border-left: 2px dashed #fff">	
					<tr><td><span class='lty-user-name' ><b><?php esc_html_e( 'Name : ' ); ?></b><small><?php echo esc_html( $ticket->get_user_name() ); ?></small></span></td></tr>
					<tr><td><span class='lty-phone-number' ><b><?php esc_html_e( 'Phone : ' ); ?></b><small><?php echo esc_html( $ticket->get_order()->get_billing_phone() ); ?></small></span></td></tr>
					<tr><td><span class='lty-user-email' ><b><?php esc_html_e( 'Email : ' ); ?></b><small><?php echo esc_html( $ticket->get_user_email() ); ?></small></span></td></tr>
					<tr><td><span class='lty-billing-address' ><b><?php esc_html_e( 'Address : ' ); ?></b><small><?php echo wp_kses_post( $ticket->get_order()->get_formatted_billing_address() ); ?></small></span></td></tr>
				</table>
			</td>
		</tr>
	</table><br/><br/>
<?php endforeach; ?>
</div>

