<?php
/**
 * Manual ticket summary.
 * */
if ( ! defined( 'ABSPATH' ) ) {
	exit ; // Exit if accessed directly.
}

/**
 * This hook is used to do extra action before lottery ticket order item container.
 * 
 * @since 1.0
 */
do_action( 'lty_before_lottery_ticket_container_order_item', $product ) ;
?>
<div class ="lty-lottery-ticket-container lty-lottery-ticket-container-order-item" >

	<?php
	/**
	 * This hook is used to do extra action before lottery ticket panel order item.
	 * 
	 * @since 1.0
	 */
	do_action( 'lty_before_lottery_ticket_panel_order_item', $product ) ;
	?>

	<div class ="lty-lottery-ticket-panel lty-lottery-ticket-panel-order-item" >
		<?php
		/**
		 * This hook is used to do extra action before lottery ticket order item.
		 * 
		 * @since 1.0
		 */
		do_action( 'lty_before_lottery_ticket_order_item', $product ) ;
		?>
		<div class="lty-lottery-ticket-wrapper">
			<div class="lty-lottery-ticket-tab-wrapper">
				<?php 
								$index = 0;
				foreach ( lty_get_ticket_tabs( $product ) as $tab_key => $label ) : 
					?>
					<button class="lty-lottery-ticket-tab" data-index="<?php echo esc_attr($index++); ?>" data-tab = "<?php echo esc_attr( $tab_key ) ; ?>">
						<?php echo esc_html( $label ) ; ?>
					</button>
				<?php endforeach ; ?>
			</div>

			<div class="lty-lottery-ticket-tab-content lty-lottery-ticket-tab-content-order-item">
				<?php
				/**
				 * This hook is used to display the lottery ticket tab order item content.
				 * 
				 * @since 1.0
				 */
				do_action( 'lty_lottery_ticket_tab_content_order_item', $product ) ;
				?>
			</div>
		</div>

		<?php
		/**
		 * This hook is used to do extra action after lottery ticket order item.
		 * 
		 * @since 1.0
		 */
		do_action( 'lty_after_lottery_ticket_order_item', $product ) ;
		?>
	</div>

	<input type="hidden" class="lty-lottery-ticket-numbers">
	<input type="hidden" class="lty-ticket-order-id"  value="<?php echo esc_attr( $order->get_id() ) ; ?>">
	<input type="hidden" class="lty-ticket-item-id"  value="<?php echo esc_attr( $item_id ) ; ?>">
	<input type="hidden" class="lty-lottery-ticket-quantity">
	<input type="hidden" class="lty-ticket-product-id" value="<?php echo esc_attr( $product->get_id() ) ; ?>">
</div>
<?php
/**
 * This hook is used to do extra action after lottery ticket order item container.
 * 
 * @since 1.0
 */
do_action( 'lty_after_lottery_ticket_container_order_item' ) ;
?>
