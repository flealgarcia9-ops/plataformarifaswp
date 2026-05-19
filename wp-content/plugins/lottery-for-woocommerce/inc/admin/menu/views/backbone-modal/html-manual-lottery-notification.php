<?php
/**
 * Popup - Manual lottery notification.
 *
 * @since 12.4.0
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

?>
<script type='text/template' id='tmpl-lty-manual-lottery-notification-modal'>
	<div class='wc-backbone-modal'>
		<div class='wc-backbone-modal-content'>
			<section class='wc-backbone-modal-main' role='main'>
				<header class='wc-backbone-modal-header'>
					<h1><?php esc_html_e( 'Send Email Notification Manually', 'lottery-for-woocommerce' ); ?></h1>
					<button class='modal-close modal-close-link dashicons dashicons-no-alt'>
						<span class='screen-reader-text'>Close modal panel</span>
					</button>
				</header>
				<article>
					{{{data.html}}}
				</article>
				<footer class='wc-backbone-modal-footer'>
					<div class='lty-manual-lottery-notification-actions'>
						<button type='button' class='button button-primary lty-send-manual-lottery-notification-button'><?php esc_html_e( 'Send Email', 'lottery-for-woocommerce' ); ?></button>
						<input type='hidden' class='lty-product-id' value='{{{data.product_id}}}' />
					</div>
				</footer>
			</section>
		</div>
	</div>
	<div class='wc-backbone-modal-backdrop modal-close'></div>
</script>
<?php
