<?php
/**
 * This template is used for displaying the instant winner prize group tickets.
 *
 * This template can be overridden by copying it to yourtheme/lottery-for-woocommerce/single-product/tabs/instant-winner-prize-group-tickets.php
 *
 * To maintain compatibility, Giveaway(formerly Lottery) For WooCommerce will update the template files and you have to copy the updated files to your theme.
 *
 * @since 12.0.0
 * @param array  $post_ids Instant winner prize group ticket ID's.
 * @param object $product Product object.
 * @param array  $pagination Pagination arguments.
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.
?>
<div class='lty-instant-winner-prize-group-item-content lty-hide'>
	<div class='lty-instant-winner-group-ticket-numbers-wrapper'>
		<div class='lty-instant-winner-group-ticket-numbers-header'>
			<p class='lty-instant-winner-group-ticket-numbers-title'><?php esc_html_e( 'Ticket Number(s):', 'lottery-for-woocommerce' ); ?></p>
		</div>
		<?php
		if ( lty_check_is_array( $post_ids ) ) :
			?>
			<div class='lty-instant-winner-group-ticket-numbers'>
			<?php
			foreach ( $post_ids as $instant_winner_log_id ) :
				$instant_winner_log = lty_get_instant_winner_log( $instant_winner_log_id );
				if ( ! $instant_winner_log->exists() ) :
					continue;
				endif;
				?>
				<div class='lty-ticket-number <?php echo esc_attr( $instant_winner_log->get_status() ); ?>_status'>
					<?php echo esc_html( $instant_winner_log->get_formatted_ticket_number() ); ?><br>
					<span class='lty-ticket-status'>
						<?php echo wp_kses_post( $instant_winner_log->get_instant_winner_prize_group_ticket_status_label() ); ?>
					</span>
				</div>
			<?php endforeach; ?>
		</div>
			<?php
		endif;
		?>
		<?php if ( $pagination['page_count'] > 1 ) : ?>
			<div class='lty-pagination-wrapper lty-instant-winner-prize-group-ticket-pagination' data-action_name='lty_instant_winner_prize_group_ticket' data-extra_data='<?php echo wp_json_encode( array( 'product_id' => $product->get_id(), 'prize_group_id' => $prize_group_id ) ); ?>'>
				<?php lty_get_template( 'pagination.php', $pagination ); ?>
			</div>
		<?php endif; ?>
	</div>
</div>
<?php

