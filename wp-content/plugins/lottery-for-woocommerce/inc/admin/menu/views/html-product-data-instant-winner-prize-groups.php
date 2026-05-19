<?php
/**
 * Instant winner prize groups tab.
 *
 * @since 11.1.0
 * @param int    $thepostid Product ID.
 * @param object $product_object Product object.
 * */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.
?>
<div id='lty_instant_winner_prize_groups_tab' class='panel woocommerce_options_panel lty_lottery_product_tab'>
	<div class="<?php echo esc_attr( $wrapper_class_name ); ?>">
		<?php lty_render_instant_winner_prize_groups( $product_object ); ?>
	</div>
</div>
<?php
