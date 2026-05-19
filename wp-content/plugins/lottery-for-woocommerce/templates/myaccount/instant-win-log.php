<?php
/**
 * This template is used for displaying the myaccount instant winner log.
 *
 * This template can be overridden by copying it to yourtheme/lottery-for-woocommerce/myaccount/instant-win-log.php
 *
 * To maintain compatibility, Giveaway(formerly Lottery) For WooCommerce will update the template files and you have to copy the updated files to your theme
 *
 * @since 10.6.0
 * @var array $columns Column names.
 * @var array $post_ids Instant winner log IDs.
 * @var string $current_lottery_menu Current lottery menu.
 */
defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

foreach ( $post_ids as $instant_winner_log_id ) :
	$instant_winner_log = lty_get_instant_winner_log( $instant_winner_log_id );
	$product            = wc_get_product( $instant_winner_log->get_lottery_id() );

	if ( ! is_object( $instant_winner_log ) || ! lty_is_lottery_product( $product ) ) :
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

				case 'lottery_duration':
					echo esc_html( $product->get_lottery_scheduled_duration_details() );
					break;

				case 'ticket_number':
					echo esc_html( $instant_winner_log->get_ticket_number() );
					break;

				case 'order_id':
					echo wp_kses_post( $instant_winner_log->get_order_number() );
					break;

				case 'prize_details':
					echo wp_kses_post( $instant_winner_log->get_prize_details() );
					break;

				default:
					/**
					 * This hook is used to display the myaccount instant win custom column content.
					 *
					 * @since 10.6.0
					 */
					do_action( sanitize_key( $current_lottery_menu ) . '_myaccount_lottery_menu_column_' . $column_key, $column_key, $instant_winner_log, $product );
					break;
				endswitch;
			?>
			</td>
		<?php endforeach; ?>
	</tr>
<?php endforeach; ?>
