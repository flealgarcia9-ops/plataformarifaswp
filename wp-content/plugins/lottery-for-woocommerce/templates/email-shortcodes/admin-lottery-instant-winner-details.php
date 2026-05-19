<?php
/**
 * This template is used for displaying the admin lottery instant winner details.
 *
 * This template can be overridden by copying it to yourtheme/lottery-for-woocommerce/email-shortcodes/admin-lottery-instant-winner-details.php
 *
 * To maintain compatibility, Giveaway(formerly Lottery) For WooCommerce will update the template files and you have to copy the updated files to your theme.
 *
 * @since 8.0.0
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>
<table class="lty-admin-email-instant-winner-details">
	<thead>
		<tr>
			<?php foreach ( $_columns as $column_name ) : ?>
				<th><?php echo esc_html( $column_name ); ?></th>
			<?php endforeach; ?>
		</tr>
	</thead>
	<tbody>
		<?php
		foreach ( $winner_ids as $winner_id ) :
			$instant_winner_log_id = lty_get_instant_winner_log_id_by_ticket_id( $winner_id );
			$instant_winner_log    = lty_get_instant_winner_log( $instant_winner_log_id );

			if ( ! $instant_winner_log->exists() ) :
				continue;
			endif;
			?>
			<tr> 
			<?php
			foreach ( $_columns as $column_key => $column_name ) :
				?>
					<td data-title="<?php echo esc_html( $column_name ); ?>">
					<?php
					switch ( $column_key ) :
						case 'product_name':
							echo esc_html( $instant_winner_log->get_product_name( true ) );
							break;
						case 'ticket_number':
							echo esc_html( $instant_winner_log->get_ticket_number() );
							break;
						case 'prize_details':
							echo wp_kses_post( $instant_winner_log->get_prize_message() );
							break;
						default:
							echo esc_html( $instant_winner_log->get_formatted_created_date() );
							break;
						endswitch;
					?>
					</td>
				<?php endforeach; ?>
			</tr>
		<?php endforeach; ?>
	</tbody>
</table>
