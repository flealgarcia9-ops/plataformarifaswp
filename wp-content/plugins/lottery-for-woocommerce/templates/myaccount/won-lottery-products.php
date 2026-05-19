<?php
/**
 * This template is used for displaying the myaccount won lottery products.
 *
 * This template can be overridden by copying it to yourtheme/lottery-for-woocommerce/myaccount/won-lottery-products.php
 *
 * To maintain compatibility, Giveaway(formerly Lottery) For WooCommerce will update the template files and you have to copy the updated files to your theme
 *
 * @since 9.1.0
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

foreach ( $winner_ids as $winner_id ) :
	$winner_object = lty_get_lottery_winner( $winner_id );
	if ( ! is_object( $winner_object->get_product() ) || ! lty_is_lottery_product( $winner_object->get_product() ) ) :
		continue;
	endif; ?>
	<tr>
		<?php foreach ( $columns as $column_key => $column_name ) : ?>
			<td data-title="<?php echo esc_attr( $column_name ); ?>">
				<?php
				switch ( $column_key ) :
					case 'product_name':
						echo wp_kses_post( $winner_object->get_product_name( true ) );
						break;

					case 'lottery_duration':
						echo esc_html( $winner_object->get_product()->get_lottery_scheduled_duration_details() );
						break;

					case 'ticket_number':
						echo esc_html( $winner_object->get_lottery_ticket_number() );
						break;

					case 'gift_product':
						echo wp_kses_post( lty_get_winner_gift_products_title( array_unique( $winner_object->get_gift_products() ), $winner_object->get_product() ) );
						break;

					case 'order_id':
						if ( $winner_object->get_order_id() && is_object( $winner_object->get_order() ) ) :
							printf( '<a href="%s">%s</a>', esc_url( $winner_object->get_order()->get_view_order_url() ), esc_html( '#' . $winner_object->get_order_id() ) );
						else :
							echo '-';
						endif;

						break;

					case 'answer':
						echo ! empty( $winner_object->get_answer() ) ? wp_kses_post( $winner_object->get_answer() ) : '-';
						break;

					default:
						/**
						 * This hook is used to display the myaccount won lottery product custom column content.
						 *
						 * @since 1.0
						 */
						do_action( sanitize_key( $current_lottery_menu ) . '_myaccount_lottery_menu_column_' . $column_key, $column_key, $winner_object, $winner_object->get_product() );
						break;

				endswitch;
				?>
			</td>

		<?php endforeach; ?>
	</tr>
<?php endforeach; ?>
