<?php
/**
 * Export - Popup.
 *
 * @since 10.3.0
 */
defined('ABSPATH') || exit; // Exit if accessed directly.
?>
<div class='wc-backbone-modal'>
	<div class='wc-backbone-modal-content lty-export-modal-wrapper'>
		<section class='wc-backbone-modal-main' role='main'>
			<?php $this->output_header(); ?>
			<?php $this->output_errors(); ?>
			<div class='lty-export-content-wrapper'>
				<?php call_user_func($this->get_current_step_view(), $this); ?>
			</div>
		</section>
	</div>
</div>
<div class='wc-backbone-modal-backdrop modal-close'></div>
<?php
