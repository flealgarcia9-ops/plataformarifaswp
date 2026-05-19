<?php
/**
 * This template is used for displaying customer participated lottery tickets on popup.
 *
 * This template can be overridden by copying it to yourtheme/lottery-for-woocommerce/popup/participated-lottery-tickets.php
 *
 * To maintain compatibility, Giveaway(formerly Lottery) For WooCommerce will update the template files and you have to copy the updated files to your theme
 *
 * @since 8.7.0
 * @modified 10.2.0
 * @var object $product instanceof WC_Product_Lottery
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.
?>
<div class='lty-customer-popup-lottery-tickets'>
	<a class='lty-view-all-tickets' data-product_id="<?php echo esc_attr( $product->get_id() ); ?>"><?php esc_html_e( 'View more details', 'lottery-for-woocommerce' ); ?></a>
	<div class="lty-hide lty-customer-lottery-tickets-modal-wrapper-<?php echo esc_attr( $product->get_id() ); ?>" id='lty_customer_lottery_tickets_modal'>
		<div class='lty-lottery-tickets-modal-header'>
			<span class='lty-lottery-tickets-modal-title'><b>
			<?php
			/* translators: %s: Product name */
			printf( esc_html__( 'Purchased Tickets Details for %s', 'lottery-for-woocommerce' ), wp_kses_post( $product->get_product_name( true ) ) ); 
			?>
			</b></span><br/>
			<span class='lty-user-purchased-ticket-count'>
				<?php echo wp_kses_post( lty_get_lottery_dashboard_user_purchased_ticket_count_label( $product ) ); ?>
			</span>
		</div>
		<div class='lty-lottery-tickets-modal-content lty-data-table-wrapper'>
			<?php
			lty_get_template(
				'popup/participated-lottery-tickets-details.php',
				lty_prepare_participated_lottery_tickets_details_arguments( $product )
			);
			?>
		</div>
	</div>
</div>

