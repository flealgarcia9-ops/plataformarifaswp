<?php
/**
 * Import - Done.
 *
 * @since 9.9.0
 */
defined('ABSPATH') || exit; // Exit if accessed directly.
$error_log = $this->get_user_error_log();
?>
<div class='wc-progress-form-content woocommerce-importer woocommerce-importer__importing'>
	<header>
		<p><?php esc_html_e('Import Completed', 'lottery-for-woocommerce'); ?></p>
	</header>
	<div class='lty-import-done-status-wrapper'>
		<h3><?php esc_html_e('Imported Details', 'lottery-for-woocommerce'); ?></h3>
		<table clas='lty-import-done-status-table'>
			<tbody>
				<tr>
					<th><?php esc_html_e('Imported', 'lottery-for-woocommerce'); ?></th>
					<td><?php echo esc_attr($this->get_imported_count()); ?></td>
				</tr>
				<tr>
					<th><?php esc_html_e('Updated', 'lottery-for-woocommerce'); ?></th>
					<td><?php echo esc_attr($this->get_updated_count()); ?></td>
				</tr>
				<tr>
					<th><?php esc_html_e('Failed', 'lottery-for-woocommerce'); ?></th>
					<td><?php echo esc_attr($this->get_failed_count()); ?> 
						<?php if ($this->get_failed_count()) : ?>
							<a href="#lty-import-error-logs-wrapper"><?php esc_html_e('(View Reasons)', 'lottery-for-woocommerce'); ?></a>
						<?php endif; ?>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	<?php if (lty_check_is_array($error_log)) : ?>
		<div class='lty-import-error-logs-wrapper' id='lty-import-error-logs-wrapper'>
			<h3><?php esc_html_e('Reason for Failed', 'lottery-for-woocommerce'); ?></h3>
			<table class='lty-import-error-logs-table'>
				<thead>
					<tr>
						<th><?php esc_html_e('Value', 'lottery-for-woocommerce'); ?></th>
						<th><?php esc_html_e('Reasons', 'lottery-for-woocommerce'); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php
					foreach ($error_log as $log) :
						if (!is_wp_error($log)) :
							continue;
						endif;
						?>
						<tr>
							<td><?php echo wp_kses_post($log->get_error_data()); ?></td>
							<td><?php echo wp_kses_post($log->get_error_message()); ?></td>
						</tr>
						<?php
					endforeach;
					?>
				</tbody>
			</table>
		</div>
	<?php endif; ?>
	<footer>
		<button type='button' class='lty-import-done-btn button button-primary'><?php esc_html_e('Done', 'lottery-for-woocommerce'); ?></button>
	</footer>
</div>
<?php
