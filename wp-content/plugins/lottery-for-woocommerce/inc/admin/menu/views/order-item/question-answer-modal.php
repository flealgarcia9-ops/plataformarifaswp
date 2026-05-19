<?php
/**
 * Question Answer.
 * 
 * @since 7.4 
 */
if (!defined('ABSPATH')) {
	exit;
}
?>
<div class='wc-backbone-modal'>
	<div class='wc-backbone-modal-content lty-question-answer-modal-content'>
		<section class='wc-backbone-modal-main' role='main'>
			<header class='wc-backbone-modal-header'>
				<h1><?php esc_html_e('Select Answer', 'lottery-for-woocommerce'); ?></h1>
				<button class='modal-close modal-close-link dashicons dashicons-no-alt'>
					<span class='screen-reader-text'>Close modal panel</span>
				</button>
			</header>
			<article>
				<div class='lty-question-answer-summary'>
					{{{ data.html }}} 
				</div>
			</article>
			<footer>
				<div class='inner'>
					<input type='hidden' class='lty-ticket-order-id'  value="<?php echo esc_attr($order->get_id()); ?>">
					<input type='hidden' class='lty-ticket-item-id'  value="<?php echo esc_attr($item_id); ?>">
					<input type='hidden' class='lty-ticket-product-id' value="<?php echo esc_attr($product->get_id()); ?>">
					<button id='btn-ok' class='button button-primary button-large'><?php esc_html_e('Proceed', 'lottery-for-woocommerce'); ?></button>
				</div>
			</footer>
		</section>
	</div>
</div>
<div class='wc-backbone-modal-backdrop modal-close'></div>
<?php

