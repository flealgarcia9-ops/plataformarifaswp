<?php
/**
 * This template is used for displaying the myaccount not won lottery tickets.
 *
 * This template can be overridden by copying it to yourtheme/lottery-for-woocommerce/myaccount/not-won-lottery-products-layout.php
 *
 * To maintain compatibility, Giveaway(formerly Lottery) For WooCommerce will update the template files and you have to copy the updated files to your theme
 *
 * @since 9.1.0
 * @var array $columns Column names.
 * @var array $post_ids Ticket IDs.
 * @var string $current_lottery_menu Current lottery menu.
 * @var array $pagination Pagination arguments.
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

if ( ! wp_get_current_user()->ID ) :
	return;
endif;

$columns = lty_dashboard_menu_columns( $current_lottery_menu );
if ( ! isset( $post_ids ) || ! lty_check_is_array( $post_ids ) ) :
		echo '<span class="lty-no-product-notice">' . esc_html__( 'No Products Found.', 'lottery-for-woocommerce' ) . '</span>';
	return;
endif;
?>
<div class='lty-myaccount-not-won-lottery-products-wrapper lty-data-table-wrapper'>
	<table class="lty-frontend-table lty-myaccount-lottery-<?php echo esc_attr( str_replace( '_', '-', $current_lottery_menu ) ); ?>">
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
				'myaccount/not-won-lottery-products.php',
				array(
					'columns'              => $columns,
					'ticket_ids'           => $post_ids,
					'current_lottery_menu' => $current_lottery_menu,
				)
			);
			?>
		</tbody>

		<?php if ( $pagination['page_count'] > 1 ) : ?>
			<tfoot>
				<tr>
					<td colspan="<?php echo esc_attr( count( $columns ) ); ?>" class='footable-visible' data-action_name='lty_myaccount_lottery_not_won_lottery_products'>
						<?php lty_get_template( 'pagination.php', $pagination ); ?>
					</td>
				</tr>
			</tfoot>
		<?php endif; ?>
	</table>
</div>
<?php
