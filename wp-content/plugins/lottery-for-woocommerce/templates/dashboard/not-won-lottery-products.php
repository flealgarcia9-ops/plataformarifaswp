<?php
/**
 * This template is used for displaying the not won lottery products.
 *
 * This template can be overridden by copying it to yourtheme/lottery-for-woocommerce/dashboard/not-won-lottery-products.php
 *
 * To maintain compatibility, Giveaway(formerly Lottery) For WooCommerce will update the template files and you have to copy the updated files to your theme
 * 
 * @since 1.0.0
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

foreach ( $post_ids as $lost_ticket_id ) :
	$lost_ticket_obj = lty_get_lottery_ticket( $lost_ticket_id );
	$product         = wc_get_product( $lost_ticket_obj->get_product_id() );
	if ( ! is_object( $product ) ) :
		continue;
	endif;
	?>
	<tr>
		<?php foreach ( $columns as $column_key => $column_name ) : ?>

			<td data-title="<?php echo esc_attr( $column_name ); ?>">
				<?php
				switch ( $column_key ) :

					case 'product_name':
						printf( '<a href="%s">%s</a>', esc_url( $product->get_permalink() ), esc_html( $product->get_title() ) );
						break;

					case 'lottery_duration':
						echo esc_html( $product->get_lottery_scheduled_duration_details() );
						break;

					case 'ticket_number':
						echo esc_html( $lost_ticket_obj->get_lottery_ticket_number() );
						break;

					case 'answer':
						echo ! empty( $lost_ticket_obj->get_answer() ) ? wp_kses_post( $lost_ticket_obj->get_answer() ) : '-';
						break;

					default:
						/**
						 * This hook is used to display the Not won lottery product custom column content.
						 * 
						 * @since 1.0
						 */
						do_action( sanitize_key( $current_lottery_menu ) . '_dashboard_menu_column_' . $column_key, $column_key, $lost_ticket_obj, $product );
						break;
				endswitch;
				?>
			</td>

		<?php endforeach; ?>
	</tr>
<?php endforeach; ?>
	
