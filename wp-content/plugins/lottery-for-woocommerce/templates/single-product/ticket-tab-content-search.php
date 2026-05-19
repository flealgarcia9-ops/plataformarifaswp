<?php
/**
 * This template is used for displaying the ticket tab content search. 
 *
 * This template can be overridden by copying it to yourtheme/lottery-for-woocommerce/single-product/ticket-tab-content-search.php
 *
 * To maintain compatibility, Giveaway(formerly Lottery) For WooCommerce will update the template files and you have to copy the updated files to your theme
 * 
 * @since 1.0.0
 */
if (!defined('ABSPATH')) {
	exit;
}
?>

<div class="lty-ticket-number-wrapper">
	<ul class="lty-ticket-number-content-search">
		<?php
		foreach ($matched_ticket_numbers as $matched_ticket_number) :
			$class_name = 'lty-ticket';
			$list_title = '';

			$ticket_number = $product->format_ticket_number($matched_ticket_number);
			if (in_array($ticket_number, $cart_tickets)) :
				$class_name .= ' lty-processing-ticket';
				$list_title = __('Ticket in Cart', 'lottery-for-woocommerce');
			elseif (in_array($ticket_number, $sold_tickets)) :
				$class_name .= ' lty-booked-ticket';
				$list_title = __('Sold!', 'lottery-for-woocommerce');
			elseif (in_array($ticket_number, $reserved_tickets)) :
				$class_name .= ' lty-reserved-ticket';
				$list_title = __('Reserved Ticket!', 'lottery-for-woocommerce');
			endif;
			?>
			<li class="<?php echo esc_attr($class_name); ?>" data-ticket="<?php echo esc_attr($ticket_number); ?>" title="<?php echo esc_attr($list_title); ?>"><?php echo esc_attr($ticket_number); ?></li>
			<?php
		endforeach;
		?>
	</ul>
</div>
<?php
