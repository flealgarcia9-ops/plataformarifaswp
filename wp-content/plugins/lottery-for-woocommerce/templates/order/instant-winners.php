<?php
/**
 * This template is used for displaying the lottery instant winner details for order.
 *
 * This template can be overridden by copying it to yourtheme/lottery-for-woocommerce/order/instant-winners.php
 *
 * To maintain compatibility, Giveaway(formerly Lottery) For WooCommerce will update the template files and you have to copy the updated files to your theme
 *
 * @since 10.9.0
 * @param array $columns Column names.
 * @param array $instant_winner_log_ids Instant winner log IDs.
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

foreach ( $instant_winner_log_ids as $instant_winner_log_id ) :
	$instant_winner_log = lty_get_instant_winner_log( $instant_winner_log_id );
	if ( ! $instant_winner_log->exists() ) :
		continue;
	endif;
	?>
	<tr>
		<?php foreach ( $columns as $column_key => $column_name ) : ?>
			<td data-title="<?php echo esc_attr( $column_name ); ?>">
				<?php
				switch ( $column_key ) :
					case 'product_name':
						echo wp_kses_post( $instant_winner_log->get_product_name( true ) );
						break;

					case 'ticket_number':
						echo esc_html( $instant_winner_log->get_ticket_number() );
						break;

					case 'image':
						echo wp_kses_post( $instant_winner_log->get_image() );
						break;

					case 'prize':
						echo wp_kses_post( $instant_winner_log->get_prize_message() );
						break;
				endswitch;
				?>
			</td>
		<?php endforeach; ?>
	</tr>
<?php endforeach; ?>
