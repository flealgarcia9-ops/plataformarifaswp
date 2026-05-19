<?php
/**
 * Lottery.
 * 
 * @since 1.0.0
 * */
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}
?>
<div class = "wrap <?php echo esc_attr(self::$plugin_slug); ?>_wrapper_cover woocommerce">
	<form method='post' id='lty_lottery_form' enctype='multipart/form-data'>
		<div class='lty_table_wrap'>
			<div class='lty-lottery-head-button'>
				<h1 class='wp-heading-inline'><?php echo esc_html__('Giveaways', 'lottery-for-woocommerce'); ?></h1>
				<a class='page-title-action' href="<?php echo esc_url(admin_url('post-new.php?post_type=product&lty_lottery_product=new')); ?>"><?php esc_html_e('Add New Giveaway', 'lottery-for-woocommerce'); ?></a>
				<button type='button' class='page-title-action lty-export-popup' data-export_type='lottery_tickets' 
						data-extra_data="<?php echo esc_attr(wp_json_encode(array( 'export_lottery' => 'all' ))); ?>">
							<?php esc_html_e('Export all giveaway tickets CSV', 'lottery-for-woocommerce'); ?>
				</button>
				<hr class='wp-header-end'>
			</div>

			<div><?php lty_render_lottery_list_table(); ?></div>
		</div>
	</form>
</div>
<?php
