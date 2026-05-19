<?php
/**
 * Manual ticket popup.
 * */
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}
?>
<div class="wc-backbone-modal">
	<div class="wc-backbone-modal-content lty-manual-ticket-popup-wrapper">
		<section class="wc-backbone-modal-main" role="main">
			<header class="wc-backbone-modal-header">
				<h1><?php esc_html_e('Select Ticket(s)', 'lottery-for-woocommerce'); ?></h1>
				<button class="modal-close modal-close-link dashicons dashicons-no-alt">
					<span class="screen-reader-text">Close modal panel</span>
				</button>
			</header>
			<article>
				<div class="lty-manual-ticket-summary">
					{{{ data.html }}} 
				</div>
			</article>
			<footer>
				<div class="inner">
					<button id="btn-ok" class="button button-primary button-large lty-generate-manual-ticket"><?php esc_html_e('Proceed', 'lottery-for-woocommerce'); ?></button>
				</div>
			</footer>
		</section>
	</div>
</div>
<div class="wc-backbone-modal-backdrop modal-close"></div>

<?php
