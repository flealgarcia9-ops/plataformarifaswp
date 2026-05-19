<?php
/**
 * This template is used for displaying the won lottery products.
 *
 * This template can be overridden by copying it to yourtheme/lottery-for-woocommerce/dashboard/won-lottery-products.php
 *
 * To maintain compatibility, Giveaway(formerly Lottery) For WooCommerce will update the template files and you have to copy the updated files to your theme
 * 
 * @since 1.0.0
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

foreach ( $post_ids as $winner_id ) :
	$winner_obj = lty_get_lottery_winner( $winner_id );
	if ( ! lty_is_lottery_product( $winner_obj->get_product() ) ) :
		continue;
	endif;
	?>
	<tr>
		<?php foreach ( $columns as $column_key => $column_name ) : ?>

			<td data-title="<?php echo esc_attr( $column_name ); ?>">
				<?php
				switch ( $column_key ) :
					case 'product_name':
						printf( '<a href="%s">%s</a>', esc_url( $winner_obj->get_product()->get_permalink() ), esc_html( $winner_obj->get_product()->get_title() ) );
						break;

					case 'lottery_duration':
						echo esc_html( $winner_obj->get_product()->get_lottery_scheduled_duration_details() );
						break;

					case 'ticket_number':
						echo esc_html( $winner_obj->get_lottery_ticket_number() );
						break;

					case 'gift_product':
						echo wp_kses_post( lty_get_winner_gift_products_title( array_unique( $winner_obj->get_gift_products() ), $winner_obj->get_product() ) );
						break;

					case 'order_id':
						if ( empty( $winner_obj->get_order_id() ) ) :
							echo esc_html( '-' );
						endif;

						if ( is_object( $winner_obj->get_order() ) ) :
							printf( '<a href="%s">%s</a>', esc_url( $winner_obj->get_order()->get_view_order_url() ), esc_html( '#' . $winner_obj->get_order_id() ) );
						else :
							echo esc_html( '-' );
						endif;

						break;

					case 'answer':
						echo ! empty( $winner_obj->get_answer() ) ? wp_kses_post( $winner_obj->get_answer() ) : '-';
						break;

					default:
						/**
						 * This hook is used to display the won lottery product custom column content.
						 * 
						 * @since 1.0
						 */
						do_action( sanitize_key( $current_lottery_menu ) . '_dashboard_menu_column_' . $column_key, $column_key, $winner_obj, $winner_obj->get_product() );
						break;

				endswitch;
				?>
			</td>

		<?php endforeach; ?>
	</tr>
<?php endforeach; ?>
		
