<?php
/**
 * Short codes 
 * 
 * @since 10.1.0
 */
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}
$shortcode_tabs = LTY_Shortcode_Tab::get_shortcode_tabs();
?>
<div class='lty-shortcode-wrapper'>
	<h3><?php esc_html_e('General Syntax', 'lottery-for-woocommerce'); ?></h3>
	<p>[shortcode parameter1 = "value" parameter2 = "value" ]</p>

	<h3><?php esc_html_e('Shortcodes', 'lottery-for-woocommerce'); ?></h3>
	<div class='lty-shortcode-tabs-wrapper'>
		<?php foreach ($shortcode_tabs as $tab_key => $tab_name) : ?>
			<button class='lty-shortcode-tab active' href='#lty-<?php echo esc_attr($tab_key); ?>-shortcode-content'><?php echo esc_html($tab_name); ?></button>
		<?php endforeach; ?>
	</div>

	<?php
	foreach ($shortcode_tabs as $tab_key => $tab_name) :
		include_once LTY_ABSPATH . "inc/admin/menu/views/shortcode/html-{$tab_key}-shortcode-content.php";
	endforeach;
	?>
</div>
<?php
