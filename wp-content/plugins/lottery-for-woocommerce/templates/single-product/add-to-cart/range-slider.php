<?php
/**
 * This template is used for displaying the range slider.
 *
 * This template can be overridden by copying it to yourtheme/lottery-for-woocommerce/single-product/add-to-cart/range-slider.php
 *
 * To maintain compatibility, Giveaway(formerly Lottery) For WooCommerce will update the template files and you have to copy the updated files to your theme
 *
 * @since 1.0.0
 * @modified 9.3.0
 * @var int $min_value Minimum value.
 * @var int $max_value Maximum value.
 * @var int $preset_value Preset value.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>
<div class='lty-lottery-range-slider-wrapper'>
	<div class='lty-lottery-range-slider-content'>
		<input type='button' value='-' class='lty-lottery-range-slider-decrement lty-lottery-range-slider-btn'>

		<div class='lty-quantity-range-slider' 
			data-min='<?php echo esc_attr( $min_value ); ?>'
			data-max='<?php echo esc_attr( $max_value ); ?>'
			data-preset='<?php echo esc_attr( $preset_value ); ?>'
			data-product_id='<?php echo esc_attr( $product->get_id() ); ?>'>
			<?php
			if ( $product->is_predefined_button_enabled() && $product->can_display_range_slider_predefined_buttons_discount_tag() && ! empty( $product->get_lty_range_slider_predefined_discount_label() ) ) :
				foreach ( $product->get_predefined_buttons_rule() as $predefined_button_id => $button_data ) :
					if ( ! $product->is_valid_to_display_predefined_button( $predefined_button_id ) ) :
						continue;
					endif;

					$ticket_quantity = $product->get_predefined_buttons_ticket_quantity( $predefined_button_id );
					if ( ! $ticket_quantity ) :
						continue;
					endif;

					$percentage = round( ( $ticket_quantity - $min_value ) / ( $max_value - $min_value ) * 100, 2 );
					?>
					<span class="lty-range-slider-discount lty-range-slider-discount-<?php echo esc_attr( $ticket_quantity ); ?>" style="left:calc( <?php echo esc_attr( $percentage ); ?>% - 30px);"><?php echo wp_kses_post( $product->get_range_slider_predefined_discount_label( $predefined_button_id, $ticket_quantity ) ); ?></span>
					<?php
				endforeach;
			endif;
			?>
			<span class='lty-lottery-range-value'><?php echo wp_kses_post( lty_get_lottery_range_slider_message( $min_value ) ); ?></span>
		</div>

		<input type='button' value='+' class='lty-lottery-range-slider-increment lty-lottery-range-slider-btn'>
		<input type='hidden' class="lty-quantity-selector lty-product-quantity-<?php echo esc_attr( $product->get_id() ); ?>" name='quantity' value='<?php echo esc_attr( $min_value ); ?>'/>
	</div>    
</div>
<?php
