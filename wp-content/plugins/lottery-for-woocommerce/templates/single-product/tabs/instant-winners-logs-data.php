<?php
/**
 * This template is used for displaying the instant winners logs data.
 *
 * This template can be overridden by copying it to yourtheme/lottery-for-woocommerce/single-product/tabs/instant-winners-logs-data.php
 *
 * To maintain compatibility, Giveaway(formerly Lottery) For WooCommerce will update the template files and you have to copy the updated files to your theme.
 *
 * @since 10.1.0
 * @var array $instant_winner_ids Instant winner IDs.
 * @var object $product Product object.
 * @var array $columns Columns to display.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

foreach ( $instant_winner_ids as $instant_winner_id ) :
	?>
	<tr>
		<?php
		$instant_winner = lty_get_instant_winner_log( $instant_winner_id );
		if ( ! is_object( $instant_winner ) ) :
			continue;
		endif;
		foreach ( $columns as $column_key => $column_name ) :
			?>
			<td data-title="<?php echo esc_html( $column_name ); ?>">
				<?php
				switch ( $column_key ) :
					case 'image':
						echo wp_kses_post( $instant_winner->get_image() );
						break;

					case 'ticket_number':
						echo esc_html( $instant_winner->get_formatted_ticket_number() );
						break;

					case 'prize':
						?>
						<span class='lty-instant-winner-prizes'>
							<?php echo wp_kses_post( $instant_winner->get_prize_message() ); ?>
						</span> 
						<?php
						break;

					case 'winner':
						if ( $instant_winner->has_status( 'lty_won' ) ) :
							?>
							<span class='lty-instant-winner'>
								<?php echo wp_kses_post( $instant_winner->get_instant_winner_details() ); ?>
							</span>
							<?php
						else :
							?>
							<span class='lty-prize-available'>
								<?php echo esc_html( lty_get_instant_winners_prize_available_label() ); ?>
							</span>
							<?php
						endif;
						break;

					default:
						/**
						 * This hook is used to display the lottery instant winner log custom column content.
						 *
						 * @since 8.0.0
						 */
						do_action( 'lty_lottery_instant_winner_log_' . $column_key, $instant_winner_id, $instant_winner );
						break;
				endswitch;
				?>
			</td> 
			<?php
		endforeach;
		?>
	</tr>
	<?php
endforeach;
