<?php
/**
 * This template is used for displaying the lottery tickets.
 *
 * This template can be overridden by copying it to yourtheme/lottery-for-woocommerce/dashboard/won-lottery-products-layout.php
 *
 * To maintain compatibility, Giveaway(formerly Lottery) For WooCommerce will update the template files and you have to copy the updated files to your theme
 * 
 * @since 1.0.0
 */
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}


$columns = lty_dashboard_menu_columns($current_lottery_menu);
?>
<div class="lty-won-lottery-products-wrapper lty-data-table-wrapper">
	<?php
	if (!isset($args['post_ids']) || !lty_check_is_array($args['post_ids'])) :
		echo '<span class="lty-no-product-notice">' . esc_html__('No Products Found.', 'lottery-for-woocommerce') . '</span>';
	else :
		?>

		<table class = "lty-frontend-table lty-dashboard-<?php echo esc_attr(str_replace('_', '-', $current_lottery_menu)); ?>">

			<thead>
				<tr>
					<?php foreach ($columns as $column_name) : ?>
						<th><?php echo esc_html($column_name); ?></th>
					<?php endforeach; ?>
				</tr>
			</thead>

			<tbody>
				<?php
				lty_get_template('dashboard/won-lottery-products.php', array(
					'columns' => $columns,
					'post_ids' => $post_ids,
					'current_lottery_menu' => $current_lottery_menu,
				));
				?>
			</tbody>

			<?php if ($pagination['page_count'] > 1) : ?>
				<tfoot>
					<tr>
						<td colspan="<?php echo esc_attr(count($columns)); ?>" class="footable-visible actions" data-action_name="lty_won_lottery_products">
							<?php lty_get_template('pagination.php', $pagination); ?>
						</td>
					</tr>
				</tfoot>
			<?php endif; ?>
		</table>
	</div>
		<?php
endif;
