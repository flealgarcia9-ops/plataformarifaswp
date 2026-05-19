<?php
/**
 * Content - Example short codes. 
 * 
 * @since 10.1.0
 */
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}
?>
<div id='lty-example-shortcode-content' class='lty-shortcode-tab-content'>
	<div class='lty-common-shortcode-examples-wrapper lty-shortcode-examples-wrapper'>
		<h3><?php esc_html_e('Common Shortcodes', 'lottery-for-woocommerce'); ?></h3>
		<div class='lty-shortcode-details'>
			<p class='lty-shortcode-title'><?php printf('<b>%s :</b> [lty_dashboard]', esc_html__('Shortcode', 'lottery-for-woocommerce')); ?></p>
			<p class='lty-shortcode-example'><b>Eg:</b> [lty_dashboard]</p>
		</div>

		<div class='lty-shortcode-details'>
			<p class='lty-shortcode-title'><?php printf('<b>%s :</b> [lty_lottery_winners_by_date]', esc_html__('Shortcode', 'lottery-for-woocommerce')); ?></p>
			<p><?php printf('<b>%s :</b> order, posts, paginate, date_filter_number, date_filter_unit', esc_html__('Paramaters', 'lottery-for-woocommerce')); ?></p>
			<p><b>Eg:</b> [lty_lottery_winners_by_date order ="ASC" posts ="3" paginate ="5" date_filter_number ="5" date_filter_unit ="days"]</p>
		</div>

		<div class='lty-shortcode-details'>
			<p class='lty-shortcode-title'><?php printf('<b>%s :</b> [lty_all_lottery_products]', esc_html__('Shortcode', 'lottery-for-woocommerce')); ?></p>
			<p><?php printf('<b>%s :</b> order, posts, paginate, category', esc_html__('Paramaters', 'lottery-for-woocommerce')); ?></p>
			<p><b>Eg:</b> [lty_all_lottery_products orderby ="end_date" posts ="3" paginate ="5" category ="lottery"]</p>
		</div>
	</div>
	<div class='lty-product-page-shortcode-examples-wrapper lty-shortcode-examples-wrapper'>
		<h3><?php esc_html_e('Single Product Page', 'lottery-for-woocommerce'); ?></h3>
		<div class='lty-shortcode-details'>
			<p class='lty-shortcode-title'><?php printf('<b>%s :</b> [lty_lottery_start_date]', esc_html__('Shortcode', 'lottery-for-woocommerce')); ?></p>
			<p><?php printf('<b>%s :</b> product_id, display_timezone', esc_html__('Paramaters', 'lottery-for-woocommerce')); ?></p>
			<p><b>Eg:</b> [lty_lottery_start_date product_id = "10639" display_timezone = "true"]</p>
		</div>

		<div class='lty-shortcode-details'>
			<p class='lty-shortcode-title'><?php printf('<b>%s :</b> [lty_lottery_tickets_sold_percentage]', esc_html__('Shortcode', 'lottery-for-woocommerce')); ?></p>
			<p><?php printf('<b>%s :</b> product_id, decimal_count', esc_html__('Paramaters', 'lottery-for-woocommerce')); ?></p>
			<p>Eg: [lty_lottery_tickets_sold_percentage product_id = "10639" decimal_count ="2"]</p>
		</div>

		<div class='lty-shortcode-details'>
			<p class='lty-shortcode-title'><?php printf('<b>%s :</b> [lty_lottery_participate_button]', esc_html__('Shortcode', 'lottery-for-woocommerce')); ?></p>
			<p><?php printf('<b>%s :</b> order, posts, paginate, category', esc_html__('Paramaters', 'lottery-for-woocommerce')); ?></p>
			<p><?php printf('<b>%1$s :</b> %2$s', esc_html__('Required Form', 'lottery-for-woocommerce'), esc_html__('Yes', 'lottery-for-woocommerce')); ?></p>
			<p><b>Eg:</b> <?php echo esc_html('<form method="POST" enctype="multipart/form-data"> [lty_lottery_participate_button product_id = "10639"] </form>'); ?></p>
		</div>

		<div class='lty-shortcode-details'>
			<p class='lty-shortcode-title'><?php printf('<b>%s :</b> [lty_lottery_predefined_button_url]', esc_html__('Shortcode', 'lottery-for-woocommerce')); ?></p>
			<p><?php printf('<b>%s :</b> product_id, button_key(Predefinded button key)', esc_html__('Paramaters', 'lottery-for-woocommerce')); ?></p>
			<p><b>Eg:</b> [lty_lottery_predefined_button_url product_id = "10639" button_key ="1"]</p>
		</div>


	</div>

	<div class='lty-required-form-shortcode-examples-wrapper lty-shortcode-examples-wrapper'>
		<h3><?php esc_html_e('Form Required Shortcodes', 'lottery-for-woocommerce'); ?></h3>
		<div class='lty-shortcode-details'>
			<p><?php esc_html_e('Form Required Shortcodes should be placed within HTML form tag to work(Perform the action). Here, we were given some examples for the Form Required Shortcodes.', 'lottery-for-woocommerce'); ?></p>
			<p><b><?php esc_html_e('Single shortode in form tag', 'lottery-for-woocommerce'); ?></b></p>
			<p><b>Eg:</b> 
			<?php
				echo esc_html('<form method="POST" enctype="multipart/form-data">[lty_lottery_participate_button product_id = "10639"]</form>');
			?>
				</p>
			<p><b><?php esc_html_e('Multiple shortcodes in form tag', 'lottery-for-woocommerce'); ?></b></p>
			<p><b>Eg:</b> 
			<?php
				echo esc_html('<form method="POST" enctype="multipart/form-data">'
						. '[lty_user_chooses_ticket product_id = "10639"]'
						. '[lty_lottery_question_answer product_id = "10639"]'
						. '[lty_lottery_quantity_selector product_id = "10639"]'
						. '[lty_lottery_predefined_buttons product_id = "10639"]'
						. '[lty_lottery_participate_button product_id = "10639"]'
						. '</form>');
				?>
				</p>
		</div>
	</div>
</div>
<?php
