<?php
/**
 * This template is used displaying the entry list winner logs.
 *
 * This template can be overridden by copying it to yourtheme/lottery-for-woocommerce/single-entry-list/winner-logs.php
 *
 * To maintain compatibility, Giveaway(formerly Lottery) For WooCommerce will update the template files and you have to copy the updated files to your theme
 *
 * @since 9.2.0
 * @var array $columns Winner log columns.
 * @var array $lottery_winners Lottery winners.
 * @var object $product Product object.
 */

defined( 'ABSPATH' ) || exit;
?>
<div class='lty-entry-list-winners-logs-content-wrapper'>
	<h3><?php echo wp_kses_post( lty_get_single_product_lottery_winner_label() ); ?></h3>
	<table class='lty-frontend-table lty-winner-logs-table'>
		<thead>
			<tr>
				<?php foreach ( $columns as $column_name ) : ?>
					<th><?php echo esc_html( $column_name ); ?></th>
				<?php endforeach; ?>
			</tr>
		</thead>
		<tbody>
			<?php foreach ( $lottery_winners as $key => $lottery_winner_id ) : ?>
				<tr>
					<?php
					$winner_log = lty_get_lottery_winner( $lottery_winner_id );
					foreach ( $columns as $key => $val ) :
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
							?>
						</td>
					<?php endforeach; ?>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
</div>
