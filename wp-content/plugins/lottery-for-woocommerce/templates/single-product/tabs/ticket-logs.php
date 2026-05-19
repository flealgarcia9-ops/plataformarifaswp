<?php
/**
 * This template is used for displaying the ticket logs.
 *
 * This template can be overridden by copying it to yourtheme/lottery-for-woocommerce/single-product/tabs/ticket-logs.php
 *
 * To maintain compatibility, Giveaway(formerly Lottery) For WooCommerce will update the template files and you have to copy the updated files to your theme
 * 
 * @since 1.0.0
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

foreach ( $ticket_ids as $ticket_id ) :
	?>
	<tr>
		<?php
		$ticket = lty_get_lottery_ticket( $ticket_id );
		foreach ( $_columns as $key => $val ) :
			?>
			<td data-title="<?php echo esc_html( $val ); ?>">
				<?php
				switch ( $key ) {
					case 'date':
						echo esc_html( $ticket->get_formatted_created_date() );
						break;

					case 'answer':
						echo esc_html( $ticket->get_answer() );
						break;

					case 'user_name':
						echo esc_html( $ticket->display_user_name_by() );
						break;

					case 'ticket_number':
						echo esc_html( $ticket->get_lottery_ticket_number() );
						break;

					default:
						/**
						 * This hook is used to display the lottery ticket log custom column content.
						 * 
						 * @since 1.0
						 */
						do_action( 'lty_lottery_ticket_log_' . $key, $ticket_id, $ticket );
						break;
				}
				?>
			</td>
		<?php endforeach; ?>
	</tr>
	<?php
endforeach;
