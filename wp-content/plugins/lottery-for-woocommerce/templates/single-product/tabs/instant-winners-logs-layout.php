<?php
/**
 * This template is used for displaying the instant winners logs layout.
 *
 * This template can be overridden by copying it to yourtheme/lottery-for-woocommerce/single-product/tabs/instant-winners-logs-layout.php
 *
 * To maintain compatibility, Giveaway(formerly Lottery) For WooCommerce will update the template files and you have to copy the updated files to your theme.
 *
 * @since 8.0.0
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>
<div class='lty-instant-winners-wrapper'>
	<div class='lty-instant-winners-reports-wrapper'>
		<span class='lty-price-available-count'><?php echo wp_kses_post( sprintf( '%s: %d', lty_get_instant_winner_available_prices_count_label(), $product->get_instant_winner_available_prizes_count() ) ); ?></span>
		<span class='lty-price-won-count'><?php echo wp_kses_post( sprintf( '%s: %d', lty_get_instant_winner_won_prices_count_label(), $product->get_instant_winner_won_prizes_count() ) ); ?></span>
	</div>
	<?php
	if ( lty_check_is_array( $post_ids ) ) :
		lty_get_template(
			'single-product/tabs/instant-winners-logs.php',
			array(
				'post_ids'   => $post_ids,
				'product'    => $product,
				'columns'    => $columns,
				'pagination' => $pagination,
			)
		);
	endif;
	?>
</div>
<?php
