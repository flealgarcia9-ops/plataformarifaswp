<?php
/**
 * This template is used for displaying the lottery instant win better luck message on order details page.
 *
 * This template can be overridden by copying it to yourtheme/lottery-for-woocommerce/order/instant-win-better-luck-message.php
 *
 * To maintain compatibility, Giveaway(formerly Lottery) For WooCommerce will update the template files and you have to copy the updated files to your theme
 *
 * @since 11.4.0
 * @param string $message Better luck message.
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.
?>
<div class='lty-instant-win-better-luck-message-wrapper'>
	<h4 class='lty-instant-win-better-luck-message lty-order-details-page-instant-win-better-luck-message'>
		<?php echo wp_kses_post( $message ); ?>
	</h4>
</div>