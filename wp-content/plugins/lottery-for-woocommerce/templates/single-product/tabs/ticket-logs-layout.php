<?php
/**
 * This template is used for displaying the ticket logs.
 *
 * This template can be overridden by copying it to yourtheme/lottery-for-woocommerce/single-product/tabs/ticket-logs-layout.php
 *
 * To maintain compatibility, Giveaway(formerly Lottery) For WooCommerce will update the template files and you have to copy the updated files to your theme
 *
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

?>
<div class='lty-ticket-logs-wrapper lty-data-table-wrapper'>
	<?php
	if ( lty_show_ticket_logs_search( $page ) && ( lty_check_is_array( $ticket_ids ) || $search ) ) : 
		?>
		<p class='lty-ticket-logs-filters lty-frontend-filter'>
			<input type='text' class='lty-ticket-logs-search lty-frontend-search' value='<?php echo esc_attr( $search ); ?>' />
			<button type='button' class='lty-ticket-logs-search-btn lty-frontend-search-btn'><?php echo esc_html( lty_get_ticket_search_button_label() ); ?></button>
			<input type='hidden' class='lty-lottery-product-id' value='<?php echo esc_attr( $product->get_id() ); ?>'/>
		</p>
	<?php endif; ?>

	<div class='lty-ticket-logs-content-wrapper'>
		<?php if ( lty_check_is_array( $ticket_ids ) ) : ?>
			<table class='lty-frontend-table shop_table shop_table_responsive lty-ticket-logs-table'>
				<thead>
					<tr>
						<?php foreach ( $columns as $column_name ) : ?>
							<th><?php echo esc_html( $column_name ); ?></th>
						<?php endforeach; ?>
					</tr>
				</thead>
				<tbody>
					<?php
					lty_get_template(
						'single-product/tabs/ticket-logs.php',
						array(
							'_columns'   => $columns,
							'ticket_ids' => $ticket_ids,
						)
					);
					?>
				</tbody>

				<?php if ( $pagination['page_count'] > 1 ) : ?>
					<tfoot>
						<tr>
							<td colspan="3" data-action_name="lty_ticket_logs" class="footable-visible actions" data-product_id="<?php echo esc_attr( $product->get_id() ); ?>">
								<?php lty_get_template( 'pagination.php', $pagination ); ?>
							</td>
						</tr>
					</tfoot>
				<?php endif; ?>
			</table>
		<?php else : ?>
			<div class="lty_log_empty_container"><?php esc_html_e( 'No ticket found.', 'lottery-for-woocommerce' ); ?></div>
		<?php endif; ?>
	</div>
</div>
<?php
