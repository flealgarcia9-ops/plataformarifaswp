<?php
/**
 * This template is used for displaying the lottery instant win gift product message on thank you page.
 *
 * This template can be overridden by copying it to yourtheme/lottery-for-woocommerce/order/instant-win-gift-product-message.php
 *
 * To maintain compatibility, Giveaway(formerly Lottery) For WooCommerce will update the template files and you have to copy the updated files to your theme
 *
 * @since 11.5.0
 * @param string $message Instant win gift product message.
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.
?>
<div class='lty-instant-win-gift-product-message-wrapper'>
	<span class='lty-instant-win-gift-product-message lty-thankyou-page-instant-win-gift-product-message'>
		<?php echo wp_kses_post( $message ); ?>
	</span>
</div>