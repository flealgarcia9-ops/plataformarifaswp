<?php
/**
 * This template is used for displaying re-generate ticket lucky dip popup.
 *
 * This template can be overridden by copying it to yourtheme/lottery-for-woocommerce/single-product/regenerate-lucky-dip-popup.php
 *
 * To maintain compatibility, Giveaway(formerly Lottery) For WooCommerce will update the template files and you have to copy the updated files to your theme
 *
 * @since 10.3.0
 * @param object $product Product object.
 * @param array $quantity_args Quantity arguments.
 */

defined( 'ABSPATH' ) || exit;

?>
<div class='lty-regenerate-ticket-lucky-dip-popup-wrapper lty-lottery-ticket-lucky-dip-container'>
<input type='hidden' class='lty-lucky-dip-fixed-quantity' value='<?php echo esc_attr( isset($quantity_args['readonly']) && $quantity_args['readonly']  ? 'yes' : 'no' ); ?>'/>
<input type='hidden' class='lty-lucky-dip-quantity' value='<?php echo esc_attr( $quantity_args['input_value'] ); ?>'/>
	<h4><b><?php echo wp_kses_post( lty_get_single_product_lucky_dip_title_label() ); ?></b></h4>
	
	<div class='lty-regenerate-lucky-dip-quantity-field'>
		<label><?php echo wp_kses_post( lty_get_single_product_lucky_dip_quantity_label() ); ?></label>
		<?php woocommerce_quantity_input( $quantity_args, $product ); ?>
		<?php if ( 'add_to_cart' === $action ) : ?>
				<button type='button'
						title="<?php echo esc_attr( lty_lucky_dip_question_answer_hover_message( $product ) ); ?>"
						value="<?php echo esc_attr( $product->get_id() ); ?>" 
						class="button alt lty-regenerate-lucky-dip-button">
					<?php echo wp_kses_post( lty_get_single_product_generate_lucky_dip_button_label() ); ?>
				</button>
		<?php endif; ?>
	</div>

	<?php if ( 'regenerate' === $action ) : ?>
		<div class='lty-regenerate-lucky-dip-tickets-field'>
			<h5><?php echo wp_kses_post( lty_get_single_product_generated_lucky_dip_tickets_label() ); ?></h5>
			<div class='lty-regenerate-lucky-dip-tickets'>
				<?php echo esc_html( implode( ', ', $ticket_numbers ) ); ?>
			</div>
		</div>
	<?php endif; ?>
	<div>
		<?php if ( 'add_to_cart' === $action ) : ?>
			<div class='lty-regenerate-lucky-dip-tickets'>
				<?php echo wp_kses_post( lty_get_lucky_dip_added_to_cart_message( $ticket_numbers ) ); ?>
			</div>
			<a href="<?php echo esc_url( wc_get_cart_url() ); ?>" class='button alt lty-view-cart'><?php echo wp_kses_post( lty_get_single_product_lucky_dip_view_cart_button_label() ); ?></a>
			<?php
		endif;

		if ( 'regenerate' === $action ) :
			?>
			<button type='button'
					title="<?php echo esc_attr( lty_lucky_dip_question_answer_hover_message( $product ) ); ?>"
					value="<?php echo esc_attr( $product->get_id() ); ?>" 
					class="button alt lty-regenerate-lucky-dip-button">
				<?php echo wp_kses_post( lty_get_single_product_regenerate_lucky_dip_button_label() ); ?>
			</button>

			<button type='button'
					value="<?php echo esc_attr( $product->get_id() ); ?>"
					class='button alt lty-regenerate-lucky-dip-add-to-cart-button'
					data-tickets="<?php echo esc_attr( implode( ',', $ticket_numbers ) ); ?>">
					<?php echo wp_kses_post( lty_get_single_product_lucky_dip_add_to_cart_button_label() ); ?>
			</button>
		<?php endif; ?>
	</div>
</div>
<?php

