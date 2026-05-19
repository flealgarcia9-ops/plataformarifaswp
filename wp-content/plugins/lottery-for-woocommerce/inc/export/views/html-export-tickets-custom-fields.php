<?php
/**
 * Export - Tickets custom fields.
 *
 * @since 11.9.0
 */
defined( 'ABSPATH' ) || exit; // Exit if accessed directly.
?>

<?php if ( 'all' === $export_tickets_type ) : ?>
	<tr>
		<th><?php esc_html_e( 'Include Giveaway Statuses', 'lottery-for-woocommerce' ); ?></th>
		<td>
			<select name='lottery_status[]' class='lty-export-lottery-status lty_select2' multiple='multiple'>
				<?php foreach ( lty_get_lottery_statuses() as $status_key => $status_label ) : ?>
					<option value="<?php echo esc_attr( $status_key ); ?>"><?php echo esc_html( $status_label ); ?></option>
				<?php endforeach; ?>
			</select>
			<p class='lty-export-field-description'><?php esc_html_e( 'Leave empty if you want to export tickets from all giveaway products.', 'lottery-for-woocommerce' ); ?></p>
		</td>
	</tr>
<?php endif; ?>
<tr>
	<th><?php esc_html_e( 'Include Giveaway Ticket Statuses', 'lottery-for-woocommerce' ); ?></th>
	<td>
		<select name='ticket_status[]' class='lty-export-ticket-status lty_select2' multiple='multiple'>
			<?php foreach ( lty_get_ticket_status_labels() as $status_key => $status_label ) : ?>
				<option value="<?php echo esc_attr( $status_key ); ?>" <?php echo lty_check_is_array( $selected_ticket_status ) && in_array( $status_key, $selected_ticket_status, true ) ? 'selected' : ''; ?>><?php echo esc_html( $status_label ); ?></option>
			<?php endforeach; ?>
		</select>
		<p class='lty-export-field-description'><?php esc_html_e( 'Leave empty if you want to export all statuses of giveaway tickets.', 'lottery-for-woocommerce' ); ?></p>
	</td>
</tr>
<?php
