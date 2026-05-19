<?php
/**
 * This template is used for displaying the myaccount not won lottery products.
 *
 * This template can be overridden by copying it to yourtheme/lottery-for-woocommerce/myaccount/not-won-lottery-products.php
 *
 * To maintain compatibility, Giveaway(formerly Lottery) For WooCommerce will update the template files and you have to copy the updated files to your theme
 *
 * @since 9.1.0
 * @var array $columns Column names.
 * @var array $ticket_ids Ticket IDs.
 * @var string $current_lottery_menu Current lottery menu.
 */
defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

foreach ( $ticket_ids as $lost_ticket_id ) :
	$lost_ticket_object = lty_get_lottery_ticket( $lost_ticket_id );
	$product            = wc_get_product( $lost_ticket_object->get_product_id() );
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
						echo wp_kses_post( $product->get_product_name( true ) );
						break;

					case 'lottery_duration':
						echo esc_html( $product->get_lottery_scheduled_duration_details() );
						break;

					case 'ticket_number':
						echo esc_html( $lost_ticket_object->get_lottery_ticket_number() );
						break;

					case 'answer':
						echo '' !== $lost_ticket_object->get_answer() ? wp_kses_post( $lost_ticket_object->get_answer() ) : '-';
						break;

					default:
						/**
						 * This hook is used to display the myaccount not won lottery product custom column content.
						 *
						 * @since 9.1.0
						 */
						do_action( sanitize_key( $current_lottery_menu ) . '_myaccount_lottery_menu_column_' . $column_key, $column_key, $lost_ticket_object, $product );
						break;
				endswitch;
				?>
			</td>

		<?php endforeach; ?>
	</tr>
<?php endforeach; ?>
