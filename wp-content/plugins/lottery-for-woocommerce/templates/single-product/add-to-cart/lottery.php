<?php
/**
 * This template is used for displaying the add to cart button.
 *
 * This template can be overridden by copying it to yourtheme/lottery-for-woocommerce/single-product/add-to-cart/lottery.php
 *
 * To maintain compatibility, Giveaway(formerly Lottery) For WooCommerce will update the template files and you have to copy the updated files to your theme
 *
 * @since 1.0.0
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $product;

if ( ! $product->is_purchasable() || ! $product->is_started() || $product->is_closed() || $product->user_purchase_limit_exists() ) {
	return;
}

if ( ! $product->is_in_stock() ) {
	return;
}

$customer_id = lty_get_current_user_cart_session_value();
// Return if the user selected incorrect answer.
if ( $product->validate_user_incorrect_answer( $customer_id ) || $product->is_customer_question_answer_time_limit_exists( $customer_id ) ) {
	return;
}

/**
 * This hook is used to do extra action before WooCommecre add to cart form.
 *
 * @since 1.0
 */
do_action( 'woocommerce_before_add_to_cart_form' );

/**
 * This hook is used to do extra action before lottery add to cart form.
 *
 * @since 1.0
 */
do_action( 'lty_before_add_to_cart_form' );
?>
<form class="cart lty-participate-now" action="
		<?php
		echo esc_url(
				/**
				* This hook is used to alter the WooCommerce add to cart form action.
				*
				* @since 1.0
				*/
			apply_filters( 'woocommerce_add_to_cart_form_action', $product->get_permalink() )
		);
		?>
		" method="post" enctype='multipart/form-data'>
			<?php
			/**
			 * This hook is used to do extra action before WooCommerce add to cart button.
			 *
			 * @hooked LTY_Lottery_Single_Product_Templates::render_question_answer_template - 10
			 * @hooked LTY_Lottery_Single_Product_Templates::render_ticket_summary_template - 20
			 * @hooked LTY_Lottery_Single_Product_Templates::render_predefined_buttons_template - 30
			 * @since 1.0
			 */
			do_action( 'woocommerce_before_add_to_cart_button' );

			if ( ! $product->is_manual_ticket() ) :

				/**
				 * This hook is used to do extra action before lottery add to cart quantity.
				 *
				 * @since 1.0
				 */
				do_action( 'lty_before_add_to_cart_quantity' );

				/**
				 * This hook is used to do extra action before WooCommerce add to cart quantity.
				 *
				 * @since 1.0
				 */
				do_action( 'woocommerce_before_add_to_cart_quantity' );

				// Render the quantity input fields.
				lty_render_quantity_field( $product );
				?>
				<?php
				/**
				 * This hook is used to do extra action after WooCommerce add to cart quantity.
				 *
				 * @since 1.0
				 */
				do_action( 'woocommerce_after_add_to_cart_quantity' );

				/**
				 * This hook is used to do extra action after lottery add to cart quantity.
				 *
				 * @since 1.0
				 */
				do_action( 'lty_after_add_to_cart_quantity' );

			endif;
			?>
	<input type="hidden" name="add-to-cart" value="<?php echo esc_attr( $product->get_id() ); ?>">

	<button type="submit" name="add-to-cart" value="<?php echo esc_attr( $product->get_id() ); ?>" class="<?php echo esc_attr( implode( ' ', lty_get_add_to_cart_button_classes( $product ) ) ); ?>"><?php echo wp_kses_post( $product->get_participate_now_text() ); ?></button>
	<?php
	/**
	 * This hook is used to do extra action after WooCommerce add to cart button.
	 *
	 * @since 1.0
	 */
	do_action( 'woocommerce_after_add_to_cart_button' );

	/**
	 * This hook is used to do extra action after lottery add to cart button.
	 *
	 * @since 1.0
	 */
	do_action( 'lty_after_add_to_cart_button' );
	?>
</form>

<?php
/**
 * This hook is used to do extra action after lottery add to cart form.
 *
 * @since 1.0
 */
do_action( 'lty_after_add_to_cart_form' );

/**
 * This hook is used to do extra action after WooCommerce add to cart form.
 *
 * @since 1.0
 */
do_action( 'woocommerce_after_add_to_cart_form' );
?>
