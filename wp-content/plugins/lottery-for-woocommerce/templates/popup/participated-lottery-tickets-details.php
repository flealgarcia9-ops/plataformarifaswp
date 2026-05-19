<?php
/**
 * This template is used for displaying customer participated lottery tickets details on popup.
 *
 * This template can be overridden by copying it to yourtheme/lottery-for-woocommerce/popup/participated-lottery-tickets-details.php
 *
 * To maintain compatibility, Giveaway(formerly Lottery) For WooCommerce will update the template files and you have to copy the updated files to your theme
 *
 * @since 10.2.0
 * @var array  $columns Column names.
 * @var object $product instanceof WC_Product_Lottery
 * @var array  $ticket_id Purchased lottery ticket IDs.
 * @var array  $pagination Pagination arguments.
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.
?>
<table class='lty-customer-lottery-tickets lty-frontend-table'>
	<thead>
		<tr>
			<?php foreach ( $columns as $column_name ) : ?>
				<th><?php echo esc_html( $column_name ); ?></th>
			<?php endforeach; ?>
		</tr>
	</thead>
	<tbody>
		<?php
		foreach ( $ticket_ids as $ticket_id ) :
			$ticket = lty_get_lottery_ticket( $ticket_id );
			if ( ! is_object( $ticket ) ) :
				continue;
			endif;
			?>
			<tr>
				<?php foreach ( $columns as $column_key => $column_name ) : ?>
					<td data-title="<?php echo esc_html( $column_name ); ?>">
						<?php
						switch ( $column_key ) :
							case 'ticket_number':
								echo esc_html( $ticket->get_lottery_ticket_number() );
								break;

							case 'order_id':
								echo ! empty( $ticket->get_order_id() ) ? wp_kses_post( $ticket->get_view_order_link('#')) : '-';
								break;

							case 'answer':
								echo ! empty( $ticket->get_answer() ) ? wp_kses_post( $ticket->get_answer() ) : '-';
								break;

							default:
								/**
								 * This hook is used to display the participated lotteries custom column content.
								 *
								 * @since 10.2.0
								 */
								do_action( 'lty_participated_lotteries_' . $key, $ticket_id, $ticket );
								break;
						endswitch;
						?>
					</td>
				<?php endforeach; ?>
			</tr>
		<?php endforeach; ?>
	</tbody>
	<?php if ( $pagination && ( $pagination['page_count'] > 1 ) ) : ?>
	<tfoot>
		<tr>
			<td colspan="<?php echo esc_attr( count( $columns ) ); ?>" class="footable-visible actions" data-product_id="<?php echo esc_attr( $product->get_id() ); ?>" data-action_name="lty_participated_lotteries_popup">
				<?php lty_get_template( 'pagination.php', $pagination ); ?>
			</td>
		</tr>
	</tfoot>
	<?php endif; ?>
</table>