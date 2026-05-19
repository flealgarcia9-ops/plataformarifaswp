<?php
/**
 * Content - Parameter short codes.  
 * 
 * @since 10.1.0
 */
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}
?>
<div id='lty-parameters-shortcode-content' class='lty-shortcode-tab-content'>
	<div class='lty-shortcode-description'><p><?php esc_html_e('You can use the below-listed parameters for the shortcodes which support the parameters.', 'lottery-for-woocommerce'); ?></p></div>
	<table class='form-table widefat striped lty-form-table lty-shortcode-parameter-table'>
		<thead>
			<tr>
				<th><?php esc_html_e('Parameters', 'lottery-for-woocommerce'); ?></th>
				<th><?php esc_html_e('Value', 'lottery-for-woocommerce'); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php
			$shortcodes = LTY_Shortcode_Tab::get_shortcode_parameter_values();
			if (lty_check_is_array($shortcodes)) :
				foreach ($shortcodes as $parameter => $parameter_value) :
					?>
					<tr>
						<td><b><?php echo esc_html($parameter); ?></b></td>
						<td><?php echo esc_html($parameter_value); ?></td>
					</tr>
					<?php
				endforeach;
			endif;
			?>
		</tbody>
	</table>
</div>   
<?php
