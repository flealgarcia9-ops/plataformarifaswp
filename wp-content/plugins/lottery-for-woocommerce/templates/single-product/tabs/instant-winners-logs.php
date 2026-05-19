<?php
/**
 * This template is used for displaying the instant winners logs table.
 *
 * This template can be overridden by copying it to yourtheme/lottery-for-woocommerce/single-product/tabs/instant-winners-logs.php
 *
 * To maintain compatibility, Giveaway(formerly Lottery) For WooCommerce will update the template files and you have to copy the updated files to your theme.
 *
 * @since 10.1.0
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

?>
<table class='lty-frontend-table shop_table shop_table_responsive lty-instant-winners-table lty-data-table-wrapper'>
	<thead>
		<tr>
			<?php foreach ( $columns as $column_name ) : ?>
				<th><?php echo wp_kses_post( $column_name ); ?></th>
			<?php endforeach; ?>
		</tr>
	</thead>
	<tbody>
		<?php
		lty_get_template(
			'single-product/tabs/instant-winners-logs-data.php',
			array(
				'instant_winner_ids' => $post_ids,
				'product'            => $product,
				'columns'            => $columns,
			)
		);
		?>
	</tbody>
	<?php
	if ( $pagination['page_count'] > 1 ) : 
		?>
		<tfoot>
			<tr>
				<td colspan='3' data-action_name='lty_instant_winner_logs' class='footable-visible actions' data-product_id="<?php echo esc_attr( $product->get_id() ); ?>">
					<?php lty_get_template( 'pagination.php', $pagination ); ?>
				</td>
			</tr>
		</tfoot>
	<?php endif; ?>
</table>
<?php
