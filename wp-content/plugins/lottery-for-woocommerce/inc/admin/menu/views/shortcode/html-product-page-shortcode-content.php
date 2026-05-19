<?php
/**
 * Content - Product page short codes. 
 * 
 * @since 10.1.0
 */
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}
?>
<div id='lty-product-page-shortcode-content' class='lty-shortcode-tab-content'>
	<div class='lty-shortcode-description'>
		<div class='notice'>
		<p><?php esc_html_e('Single Product shortcodes require the product_id parameter if you place the shortcode on a separate page.', 'lottery-for-woocommerce'); ?></p>
		</div>
		<div class='notice'>
			<p><?php echo wp_kses_post(__('If the <b>Form Required Shortcodes</b> are not performing the action, we suggest you to place the shortcodes within the HTML form tag(Make sure the edit page supports HTML text).', 'lottery-for-woocommerce')); ?></p>
			<p><b>Eg:</b> <?php echo esc_html('<form method="POST" enctype="multipart/form-data"> shortcode </form>'); ?></p>
		</div>
	</div>

	<table class='form-table lty-form-table widefat striped lty-product-page-shortcode-table'>
		<thead>
			<tr>
				<th><?php esc_html_e('Shortcode', 'lottery-for-woocommerce'); ?></th>
				<th><?php esc_html_e('Parameter Support', 'lottery-for-woocommerce'); ?></th>
				<th><?php esc_html_e('Required Form', 'lottery-for-woocommerce'); ?></th>
				<th><?php esc_html_e('Description', 'lottery-for-woocommerce'); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php
			$shortcodes = LTY_Shortcode_Tab::get_product_page_shortcodes();
			if (lty_check_is_array($shortcodes)) :
				foreach ($shortcodes as $shortcode => $shortcode_details) :
					?>
					<tr>
						<td><b><?php echo esc_html($shortcode); ?></b></td>
						<td><?php echo esc_html($shortcode_details['supported_parameters']); ?></td>
						<td>
							<?php
							if ($shortcode_details['required_form']) :
								echo '<span class="lty-valid-data">' . esc_html__('Yes', 'lottery-for-woocommerce') . '</span>';
							else :
								echo '<span class="lty-non-valid-data">' . esc_html__('No', 'lottery-for-woocommerce') . '</span>';
							endif;
							?>
						</td>
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
