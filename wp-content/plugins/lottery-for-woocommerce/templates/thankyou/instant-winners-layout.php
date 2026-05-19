<?php
/**
 * This template is used for displaying the instant winner details layout for order.
 *
 * This template can be overridden by copying it to yourtheme/lottery-for-woocommerce/thankyou/instant-winners-layout.php
 *
 * To maintain compatibility, Giveaway(formerly Lottery) For WooCommerce will update the template files and you have to copy the updated files to your theme
 *
 * @since 11.4.0
 * @param array $columns Columns.
 * @param array $instant_winner_log_ids Instant winner log IDs.
 * @param array $pagination Pagination arguments.
 * @param int   $order_id Order ID.
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.
?>
<div class='lty-order-instant-winners-wrapper'>
	<h4 class='lty-order-instant-winners-heading'><?php echo wp_kses_post( lty_get_order_instant_winners_heading() ); ?></h4>
	<table class='lty-data-table-wrapper lty-thankyou-page-instant-winners'>
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
				'order/instant-winners.php',
				array(
					'columns'                => $columns,
					'instant_winner_log_ids' => $instant_winner_log_ids,
				)
			);
			?>
		</tbody>

		<?php if ( $pagination['page_count'] > 1 ) : ?>
			<tfoot>
				<tr>
					<td colspan="<?php echo esc_attr( count( $columns ) ); ?>" class='footable-visible lty-pagination-wrapper' data-action_name='lty_order_instant_winners' data-extra_data='<?php echo wp_json_encode( array( 'order_id' => $order_id, 'template' => 'thankyou' ) ); ?>'>
						<?php lty_get_template( 'pagination.php', $pagination ); ?>
					</td>
				</tr>
			</tfoot>
		<?php endif; ?>
	</table>
</div>
<?php
