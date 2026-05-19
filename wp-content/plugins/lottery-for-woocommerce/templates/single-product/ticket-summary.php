<?php
/**
 * This template is used for displaying the ticket summary.
 *
 * This template can be overridden by copying it to yourtheme/lottery-for-woocommerce/single-product/ticket-summary.php
 *
 * To maintain compatibility, Giveaway(formerly Lottery) For WooCommerce will update the template files and you have to copy the updated files to your theme
 *
 * @since 1.0.0
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * This hook is used to do extra action before lottery ticket container.
 *
 * @since 1.0
 */
do_action( 'lty_before_lottery_ticket_container' );
?>
<div class ="lty-lottery-ticket-container" >

	<div class="lty-lottery-ticket-header">
		<h3><?php esc_html_e( 'Select your ticket(s)', 'lottery-for-woocommerce' ); ?></h3>
	</div>
	<?php
	/**
	 * This hook is used to do extra action before lottery ticket panel.
	 *
	 * @since 1.0
	 */
	do_action( 'lty_before_lottery_ticket_panel' );
	?>
	<div class ="lty-lottery-ticket-panel" >
		<?php
		/**
		 * This hook is used to do extra action before lottery ticket wrapper.
		 *
		 * @hooked LTY_Lottery_Single_Product_Templates::render_ticket_lucky_dip - 30
		 * @hooked LTY_Lottery_Single_Product_Templates::render_manual_ticket_search - 30
		 * @since 1.0
		 */
		do_action( 'lty_before_lottery_ticket' );
		?>
		<div class="lty-lottery-ticket-wrapper">
			<div class="lty-lottery-ticket-tab-wrapper">
				<?php
								$index = 0;
				foreach ( lty_get_ticket_tabs( $product ) as $tab_key => $label ) :
					?>
					<button class="lty-lottery-ticket-tab <?php echo esc_html( 0 === $index ? 'lty-active-tab' : '' ); ?>" data-index="<?php echo esc_attr( $index++ ); ?>" data-tab = "<?php echo esc_attr( $tab_key ); ?>">
						<?php echo esc_html( $label ); ?>
					</button>
				<?php endforeach; ?>
			</div>

			<div class="lty-lottery-ticket-tab-content">
				<?php
				/**
				 * This hook is used to display the ticket tab content.
				 *
				 * @hooked LTY_Lottery_Single_Product_Templates::render_ticket_tab_content - 30
				 * @since 1.0
				 */
				do_action( 'lty_lottery_ticket_tab_content', $product );
				?>
			</div>
		</div>
		<?php
		/**
		 * This hook is used to do extra action after lottery tickets.
		 *
		 * @since 1.0
		 */
		do_action( 'lty_after_lottery_ticket' );
		?>
	</div>

	<input type="hidden" name="quantity" class="lty-lottery-ticket-quantity">
	<input type="hidden" name="lty_lottery_ticket_numbers" class="lty-lottery-ticket-numbers">
	<input type="hidden" class="lty-ticket-product-id" value="<?php echo esc_attr( $product->get_id() ); ?>"/>
</div>
<?php
/**
 * This hook is used to do extra action after lottery ticket container.
 *
 * @since 1.0
 */
do_action( 'lty_after_lottery_ticket_container' );
?>
