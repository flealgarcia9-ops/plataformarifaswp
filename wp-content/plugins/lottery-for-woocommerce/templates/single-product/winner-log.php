<?php
/**
 * This template is used for displaying the tickets sold notice.
 *
 * This template can be overridden by copying it to yourtheme/lottery-for-woocommerce/single-product/winner-log.php
 *
 * To maintain compatibility, Giveaway(formerly Lottery) For WooCommerce will update the template files and you have to copy the updated files to your theme
 *
 * @since 1.0.0
 * @var object $product Product object.
 * @var array $_columns Column names.
 * @var array $lottery_winners Lottery winners.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

?><h3><b><?php echo wp_kses_post( lty_get_single_product_lottery_winner_label() ); ?></b></h3>

<table class="lty-frontend-table shop_table shop_table_responsive lty-winner-logs-table">
	<thead>
		<tr>
			<?php
			foreach ( $_columns as $column_name ) :
				?>
				<th><?php echo esc_html( $column_name ); ?></th>
				<?php
			endforeach;
			?>
		</tr>
	</thead>
	<tbody>
		<?php foreach ( $lottery_winners as $key => $lottery_winner_id ) : ?>
			<tr>
				<?php
				$winner_log = lty_get_lottery_winner( $lottery_winner_id );

				foreach ( $_columns as $key => $val ) :
					?>
					<td data-title="<?php echo esc_attr( $val ); ?>">
						<?php
						switch ( $key ) :
							case 'username':
								echo esc_html( $winner_log->display_user_name() );
								break;

							case 'gift_product':
								echo wp_kses_post( lty_get_winner_gift_products_title( array_unique( $winner_log->get_gift_products() ), $product ) );
								break;

							case 'ticket_number':
								echo esc_html( $winner_log->get_lottery_ticket_number() );
								break;

							case 'answer':
								echo esc_html( $winner_log->get_answer() );
								break;

						endswitch;
						/**
						 * This hook is used to display the lottery winner log custom column content.
						 *
						 * @since 1.0
						 */
						do_action( 'lty_lottery_winner_log_' . $key, $lottery_winner_id, $winner_log );
						?>
					</td>
					<?php
				endforeach;
				?>
			</tr>
			<?php
		endforeach;
		?>
	</tbody>
</table>
<?php

