<?php
/**
 * This template is used for displaying the predefined buttons.
 *
 * This template can be overridden by copying it to yourtheme/lottery-for-woocommerce/shortcodes/predefined-buttons.php
 *
 * To maintain compatibility, Giveaway(formerly Lottery) For WooCommerce will update the template files and you have to copy the updated files to your theme
 *
 * @since 11.0.0
 * @var object $product instanceof WC_Product_Lottery.
 * @var array $buttons_rule Buttons rule.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * This hook is used to do extra action before predefined buttons container.
 *
 * @since 1.0.0
 */
do_action( 'lty_before_lottery_predefined_buttons_container' );
?>
<div class='lty-lottery-predefined-buttons-container lty-lottery-shortcodes-predefined-buttons-container'>
	<h3><?php echo wp_kses_post( lty_get_predefined_buttons_heading() ); ?></h3>

	<ul class='lty-predefined-buttons lty-shortcodes-predefined-buttons'>
		<?php
		foreach ( $buttons_rule as $predefined_button_id => $button_data ) :
			if ( ! $product->is_valid_to_display_predefined_button( $predefined_button_id ) ) :
				continue;
			endif;

			$ticket_quantity = $product->get_predefined_buttons_ticket_quantity( $predefined_button_id );
			if ( ! $ticket_quantity ) :
				continue;
			endif;

			$per_ticket_amount = $product->get_predefined_buttons_per_ticket_amount( $predefined_button_id );
			$badge_label       = $product->get_predefined_button_badge_label( $predefined_button_id, $ticket_quantity );
			?>
			<li class='lty-predefined-button' 
				data-predefined-button-id="<?php echo esc_attr( $predefined_button_id ); ?>"
				data-ticket-quantity ="<?php echo esc_attr( $ticket_quantity ); ?>"
				data-per-ticket-amount ="<?php echo esc_attr( $per_ticket_amount ); ?>"
				data-product_id="<?php echo esc_attr( $product->get_id() ); ?>">
				<?php echo wp_kses_post( $product->get_predefined_button_label( $predefined_button_id, $ticket_quantity ) ); ?>
				
				<?php if ( $product->can_display_predefined_buttons_discount_tag() && ! empty( $badge_label ) ) : ?>
					<span class='lty-predefined-button-badge lty-shortcodes-predefined-button-badge'><?php echo wp_kses_post( $badge_label ); ?></span>
				<?php endif; ?>
			</li>
		<?php endforeach; ?>
	</ul>

	<input type='hidden' class='lty-predefined-button-id' name='lty_predefined_button_id'/>
	<input type='hidden' class='lty-per-ticket-amount' name='lty_per_ticket_amount'>

	<?php if ( ! $product->is_predefined_with_quantity_selector() ) : ?>
		<input type='hidden' class="lty-ticket-quantity lty-product-quantity-<?php echo esc_attr( $product->get_id() ); ?>" name='quantity' value="<?php echo esc_attr( $product->get_preset_tickets() ); ?>"/>
	<?php endif; ?>
</div>

<?php
/**
 * This hook is used to do extra action after predefined buttons container.
 *
 * @since 1.0.0
 */
do_action( 'lty_after_lottery_predefined_buttons_container' );

