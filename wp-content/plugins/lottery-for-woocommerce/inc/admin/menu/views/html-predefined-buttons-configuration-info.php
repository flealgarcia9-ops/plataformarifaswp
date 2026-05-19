<?php
/* Predefined buttons Configuration info.  */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>
<span class="lty-predefined-buttons-configuration-info">
	<span class="lty-table-row">
		<span class="lty-table-head"><?php esc_html_e( 'ID', 'lottery-for-woocommerce' ); ?></span>
		<span class="lty-table-head"><?php esc_html_e( 'Ticket Quantity', 'lottery-for-woocommerce' ); ?></span>
		<span class="lty-table-head"><?php esc_html_e( 'Discount in % / Fixed Price', 'lottery-for-woocommerce' ); ?></span>
	</span>

	<?php
	$button_rule    = $product->get_predefined_buttons_rule();
	$selection_type = $product->get_predefined_buttons_selection_type();

	if ( lty_check_is_array( $button_rule ) ) :
		foreach ( $button_rule as $key => $button_data ) :
			$ticket_quantity = ! empty( $button_data['ticket_quantity'] ) && 0 != absint( $button_data['ticket_quantity'] ) ? absint( $button_data['ticket_quantity'] ) : 0;
			$discount        = ! empty( $button_data['discount_percentage'] ) ? absint( $button_data['discount_percentage'] ) : 0;
			$fixed_price     = ! empty( $button_data['fixed_price'] ) ? absint( $button_data['fixed_price'] ) : 0;
			?>
			<span class="lty-table-row">
				<span class="lty-table-data">#<?php echo esc_html( $key ); ?></span>
				<span class="lty-table-data"><?php echo esc_html( $ticket_quantity ); ?></span>
				<?php if ( '1' == $selection_type ) : ?>
					<span class="lty-table-data"><?php echo esc_html( $discount ); ?></span>
				<?php else : ?>
					<span class="lty-table-data"><?php echo esc_html( $fixed_price ); ?></span>
				<?php endif; ?>
			</span>
			<?php
		endforeach;
	endif;

