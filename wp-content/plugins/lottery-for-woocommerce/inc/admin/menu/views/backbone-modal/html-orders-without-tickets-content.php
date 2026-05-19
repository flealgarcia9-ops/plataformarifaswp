<?php
/**
 * Orders Without Tickets Table Content.
 * */
defined('ABSPATH') || exit;
?>
<div class="lty-orders-without-tickets-popup-wrapper">

	<div class="lty-order-status-selection-wrapper">
		<ul class="subsubsub">
			<li class="all">
				<a href="#" class="current lty-order-status-action" data-status="all">
					<?php esc_html_e('All', 'lottery-for-woocommerce'); ?>
					<span class="count"><?php echo esc_attr(' (' . count($order_ids) . ')'); ?></span>
				</a>
				<?php echo ' | '; ?>
			</li>
			<?php
			$order_statuses = wc_get_order_statuses();
			$selected_order_statuses = !empty(get_option('lty_settings_lottery_complete_order_statuses')) ? get_option('lty_settings_lottery_complete_order_statuses') : array( 'processing', 'completed' );
			$i = 1;
			foreach ($selected_order_statuses as $selected_order_status) :
				$order_status_key = 'wc-' . $selected_order_status;
				$selected_status_count = count(get_posts(
								array(
									'post_type' => 'shop_order',
									'post_status' => $order_status_key,
									'post__in' => $order_ids,
									'fields' => 'ids',
								)
				));

				if (!$selected_status_count) :
					continue;
				endif;
				?>
				<li class="<?php echo esc_attr($order_status_key); ?>">
					<a href="#" class="lty-order-status-action" data-status="<?php echo esc_attr($order_status_key); ?>">
						<?php echo esc_html(isset($order_statuses[$order_status_key]) ? $order_statuses[$order_status_key] : '-'); ?>
						<span class="count"><?php echo esc_attr(' (' . $selected_status_count . ')'); ?></span>
					</a>
				</li>
				<?php
				if ($i <= ( count($selected_order_statuses) - 1 )) :
					echo ' | ';
				endif;

				$i++;
			endforeach;
			?>
			<input type="hidden" class="lty-orders-without-tickets-product-id" value="<?php echo esc_attr($product->get_id()); ?>">
		</ul>
	</div>

	<div class="lty-orders-without-tickets-table-popup-wrapper">
		<?php
		include LTY_ABSPATH . 'inc/admin/menu/views/backbone-modal/html-orders-without-tickets-table-content.php';
		?>
	</div>
</div>
<?php
