<?php
/**
 * This template is used for displaying the instant winner prize group layout.
 *
 * This template can be overridden by copying it to yourtheme/lottery-for-woocommerce/single-product/tabs/instant-winner-prize-groups-layout.php
 *
 * To maintain compatibility, Giveaway(formerly Lottery) For WooCommerce will update the template files and you have to copy the updated files to your theme.
 *
 * @since 11.1.0
 * @param array  $post_ids Instant winner prize group ID's.
 * @param object $product Product object.
 * @param array  $pagination Pagination arguments.
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.
?>
<div class='lty-data-table-wrapper lty-instant-winner-prize-group-wrapper'>
	<div class='lty-instant-winners-reports-wrapper'>
		<span class='lty-price-available-count'><?php echo wp_kses_post( sprintf( '%s: %d', lty_get_instant_winner_available_prices_count_label(), $product->get_instant_winner_available_prizes_count() ) ); ?></span>
		<span class='lty-price-won-count'><?php echo wp_kses_post( sprintf( '%s: %d', lty_get_instant_winner_won_prices_count_label(), $product->get_instant_winner_won_prizes_count() ) ); ?></span>
	</div>
	<?php
	if ( lty_check_is_array( $post_ids ) ) :
		lty_get_template(
			'single-product/tabs/instant-winner-prize-groups.php',
			array(
				'prize_group_ids' => $post_ids,
				'product'         => $product,
			)
		);
	endif;
	?>
	<?php if ( $pagination['page_count'] > 1 ) : ?>
		<div class='lty-pagination-wrapper lty-instant-winner-prize-group-pagination' data-action_name='lty_instant_winner_prize_group' data-extra_data='<?php echo wp_json_encode( array( 'product_id' => $product->get_id() ) ); ?>'>
			<?php lty_get_template( 'pagination.php', $pagination ); ?>
		</div>
	<?php endif; ?>
</div>
<?php
