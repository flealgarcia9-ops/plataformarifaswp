<?php
/**
 * This template is used for displaying the myaccount participated lottery tickets.
 *
 * This template can be overridden by copying it to yourtheme/lottery-for-woocommerce/myaccount/participated-lottery-products.php
 *
 * To maintain compatibility, Giveaway(formerly Lottery) For WooCommerce will update the template files and you have to copy the updated files to your theme
 *
 * @since 9.1.0
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

foreach ( $lottery_ids as $lottery_id ) :
	$product = wc_get_product( $lottery_id );
	if ( ! is_object( $product ) ) :
		continue;
	endif ?>
	<tr>
		<?php foreach ( $columns as $column_key => $column_name ) : ?>
			<td data-title="<?php echo esc_attr( $column_name ); ?>">
				<?php
				switch ( $column_key ) :
					case 'product_name':
						echo wp_kses_post($product->get_product_name( true ));
						break;

					case 'lottery_duration':
						echo esc_html( $product->get_lottery_scheduled_duration_details() );
						break;

					case 'status':
						echo wp_kses_post( lty_display_status( $product->get_lty_lottery_status() ) );
						break;

					case 'ticket_number':
						$purchased_ticket_numbers = $product->get_user_purchased_tickets();
						if ( lty_check_is_array( $purchased_ticket_numbers ) ) :
							echo esc_html( implode( ', ', array_reverse( array_slice( $purchased_ticket_numbers, -5 ) ) ) );
							lty_get_template( 'popup/participated-lottery-tickets.php', array( 'product' => $product ) );
						endif;
						break;

					default:
						/**
						 * This hook is used to display the my lottery product custom column content.
						 *
						 * @since 9.1.0
						 */
						do_action( sanitize_key( $current_lottery_menu ) . '_myaccount_lottery_menu_column_' . $column_key, $column_key, $product );
						break;
				endswitch;
				?>
			</td>
		<?php endforeach; ?>
	</tr>
<?php endforeach; ?>
