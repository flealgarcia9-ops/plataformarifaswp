<?php
/**
 * This template is used for displaying the instant winner prize group.
 *
 * This template can be overridden by copying it to yourtheme/lottery-for-woocommerce/single-product/tabs/instant-winner-prize-groups.php
 *
 * To maintain compatibility, Giveaway(formerly Lottery) For WooCommerce will update the template files and you have to copy the updated files to your theme.
 *
 * @since 11.1.0
 * @param array  $prize_group_ids Instant winner prize group ID's.
 * @param object $product Product object.
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

foreach ( $prize_group_ids as $prize_group_id ) :
	$prize_group = lty_get_instant_winner_prize_group( $prize_group_id );
	if ( ! $prize_group->exists() || ! lty_check_is_array( $prize_group->get_instant_winner_log_ids() ) ) :
		continue;
	endif;
	?>
	<div class='lty-instant-winner-prize-group-item' data-extra_data='<?php echo wp_json_encode( array( 'product_id' => $product->get_id(), 'prize_group_id' => $prize_group_id ) ); ?>'>
		<div class='lty-instant-winner-prize-group-item-header'>
			<?php if ( '1' === $product->get_lty_display_instant_winner_image() ) : ?>
				<img src="<?php echo esc_url( $prize_group->get_image_url() ); ?>" alt="<?php esc_attr_e( 'Instant Win Prize Image', 'lottery-for-woocommerce' ); ?>">
			<?php endif; ?>

			<div class='lty-instant-winner-prize-group-item-header-content'>
				<span class='lty-instant-winner-group-prize-message'><?php echo wp_kses_post( $prize_group->get_prize_message() ); ?></span>
				<span class='lty-instant-winner-group-available-prize'>
					<?php
					/* translators: %1$s: Available prize count, %2$s: Total prize count */
					echo esc_attr( sprintf( '%1$s remaining out of %2$s', $product->get_group_instant_winner_available_prizes_count( $prize_group_id ), $prize_group->get_instant_winner_log_ids_count() ) );
					?>
				</span>
			</div>

			<span class="dashicons dashicons-arrow-down-alt2 lty-expand-prize-group"></span>
		</div>
		<?php
		if ( '2' !== lty_get_lottery_product_page_display_mode() ) : 
			lty_get_template( 'single-product/tabs/instant-winner-prize-group-tickets.php', lty_get_instant_winner_prize_group_ticket_logs_arguments( $product, $prize_group_id ) );
		endif;
		?>
	</div>
	<?php
endforeach;
