<?php
/**
 * Content - Common short codes. 
 * 
 * @since 10.1.0
 */
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}
?>
<div id='lty-common-shortcode-content' class='lty-shortcode-tab-content'>
	<div class='lty-shortcode-description'><p><?php esc_html_e('You can use the shortcodes on any page.', 'lottery-for-woocommerce'); ?></p></div>
	
	<table class='form-table lty-form-table widefat striped lty-common-shortcode-table'>
		<thead>
			<tr>
				<th><?php esc_html_e('Shortcode', 'lottery-for-woocommerce'); ?></th>
				<th><?php esc_html_e('Parameter Support', 'lottery-for-woocommerce'); ?></th>
				<th><?php esc_html_e('Description', 'lottery-for-woocommerce'); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php
			$shortcodes = LTY_Shortcode_Tab::get_common_shortcodes();
			if (lty_check_is_array($shortcodes)) :
				foreach ($shortcodes as $shortcode => $shortcode_details) :
					?>
					<tr>
						<td><b><?php echo esc_html($shortcode); ?></b></td>
						<td><?php echo esc_html($shortcode_details['supported_parameters']); ?></td>
						<td><?php echo esc_html($shortcode_details['usage']); ?></td>
					</tr>
					<?php
				endforeach;
			endif;
			?>
		</tbody>
	</table>
</div>
<?php
