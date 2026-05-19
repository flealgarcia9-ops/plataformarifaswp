<?php
/**
 * Import Popup.
 *
 * @since 9.8.0
 */
defined('ABSPATH') || exit; // Exit if accessed directly.
?>
<div class='wc-backbone-modal'>
	<div class='wc-backbone-modal-content lty-import-modal-wrapper'>
		<section class='wc-backbone-modal-main' role='main'>
			<?php $this->output_header(); ?>
			<section>
				<?php $this->output_steps(); ?>
			</section>
			<?php $this->output_errors(); ?>
			<div class='lty-import-content-wrapper'>
				<?php call_user_func($this->get_current_step_view(), $this); ?>
			</div>
			<input type='hidden' class='lty-import-action-type' value='<?php echo esc_attr($this->get_action_type()); ?>' />
			<input type='hidden' class='lty-import-extra-data' value='<?php echo wp_json_encode($this->get_extra_data()); ?>' />
		</section>
	</div>
</div>
<div class='wc-backbone-modal-backdrop modal-close'></div>
<?php
