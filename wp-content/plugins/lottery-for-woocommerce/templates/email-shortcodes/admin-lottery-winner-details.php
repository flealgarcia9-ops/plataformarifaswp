<?php
/**
 * This template is used for displaying the admin lottery winner details.
 *
 * This template can be overridden by copying it to yourtheme/lottery-for-woocommerce/email-shortcodes/admin-lottery-winner-details.php
 *
 * To maintain compatibility, Giveaway(formerly Lottery) For WooCommerce will update the template files and you have to copy the updated files to your theme
 * 
 * @since 1.0.0
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit ; // Exit if accessed directly.
}
?>
<table class="lty-frontend-table lty-lottery-winners">
	<thead>
		<tr>
			<?php foreach ( $_columns as $column_name ) : ?>
				<th><?php echo esc_html( $column_name ) ; ?></th>
			<?php endforeach ; ?>
		</tr>
	</thead>
	<tbody>
		<?php foreach ( $winner_ids as $winner_id ) : ?>
			<tr>
				<?php
				$winner = lty_get_lottery_winner($winner_id) ;
				
				foreach ( $_columns as $key => $val ) :
					?>
					<td data-title="<?php echo esc_html( $val ) ; ?>">
						<?php
						switch ($key) :
							case 'user_name':
								echo esc_html( $winner->get_user_name() . ' (' . $winner->get_user_email() . ')' ) ;
								break ;
							case 'ticket_number':
								echo esc_html( $winner->get_lottery_ticket_number() , true ) ;
								break ;
							case 'answer':
								if ( $winner->get_answer() ) :
									$class = ( 'yes' == $winner->get_valid_answer() ) ? 'lty-correct-answer' : 'lty-wrong-answer' ;

									echo wp_kses_post( '<p class="' . $class . '">' . $winner->get_answer() . '</p>' ) ;
								else :
									echo '-' ;
								endif;
								break ;
							case 'order_id':
								if ( empty( $winner->get_order_id() ) ) :
									echo esc_html( '-' ) ;
								else :
									printf( '<a href="%s">%s</a>' , esc_url( get_edit_post_link( $winner->get_order_id() ) ) , esc_html( '#' . $winner->get_order_id() ) ) ;
								endif ;
								break ;
							case 'gift_products':
								echo wp_kses_post( lty_get_winner_gift_products_title( array_unique( $winner->get_gift_products() ) , $product ) ) ;
								break ;
							case 'product_name':
								printf( '<a href="%s">%s</a>' , esc_url( $product->get_permalink() ) , esc_html( $product->get_title() ) );
								break;
							default:
								echo esc_html( $winner->get_formatted_created_date() ) ;
								break ;
						endswitch;
						?>
					</td>
				<?php endforeach ; ?>
			</tr>
		<?php endforeach ; ?>
	</tbody>
</table>
