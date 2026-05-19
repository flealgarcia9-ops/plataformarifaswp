<?php
/**
 * This template is used for displaying the ticket tab content.
 *
 * This template can be overridden by copying it to yourtheme/lottery-for-woocommerce/single-product/ticket-tab-content.php
 *
 * To maintain compatibility, Giveaway(formerly Lottery) For WooCommerce will update the template files and you have to copy the updated files to your theme
 *
 * @since 1.0.0
 * @modified 8.9.0
 * @var object $product Instanceof WC_Product_Lottery
 * @var array $sold_tickets Sold lottery tickets.
 * @var array $cart_tickets Tickets alreay in cart.
 * @var array $reserved_tickets Reserved lottery tickets.
 * @var int|string $index Current tab index
 * @var array $ticket_numbers Ticket numbers
 * @var int $view_more number of tickets to be displayed when click view more.
 */

defined( 'ABSPATH' ) || exit;
?>

<div class='lty-ticket-number-wrapper'>
	<div class='lty-ticket-number-content'>
	<ul>
		<?php
		$tickets_count = 1;
		$step          = 0;
		foreach ( $ticket_numbers as $ticket_number ) :
			$formatted_ticket_number = $product->format_ticket_number( $ticket_number, $index );
			$class_name              = 'lty-ticket';
			$list_title              = '';
			if ( $view_more && $tickets_count >= $view_more ) :
				$step = ( 0 === ( $tickets_count - 1 ) % $view_more ) ? $step + 1 : $step;
			endif;

			if ( $product->is_valid_to_display_ticket_number( $formatted_ticket_number ) ) :
				continue;
			endif;

			// Add class to hide lottery tickets.
			$class_name .= $step ? ( ' lty-hidden-ticket lty-step-' . $step ) : '';
			if ( in_array( $formatted_ticket_number, $cart_tickets ) ) :
				$class_name .= ' lty-processing-ticket';
				$list_title  = __( 'Ticket in Cart', 'lottery-for-woocommerce' );
			elseif ( in_array( $formatted_ticket_number, $sold_tickets ) ) :
				$class_name .= ' lty-booked-ticket';
				$list_title  = __( 'Sold!', 'lottery-for-woocommerce' );
			elseif ( in_array( $formatted_ticket_number, $reserved_tickets ) ) :
				$class_name .= ' lty-reserved-ticket';
				$list_title  = __( 'Reserved Ticket!', 'lottery-for-woocommerce' );
			endif;
			?>
			<li class="<?php echo esc_attr( $class_name ); ?>" data-ticket="<?php echo esc_attr( $formatted_ticket_number ); ?>" title="<?php echo esc_attr( $list_title ); ?>"><?php echo esc_attr( $formatted_ticket_number ); ?></li>
			<?php
			$tickets_count++;
		endforeach;
		?>
	</ul>
	</div>
	<?php if ( $view_more ) : ?>
		<div class='lty-toggle-view-button'>
			<a href='#' class='lty-toggle-lottery-tickets' data-action='view_more' data-step='1'></a> 
		</div>
	<?php endif; ?>
</div>
