<?php
/**
 * Import - Steps.
 *
 * @since 9.9.0
 */
defined('ABSPATH') || exit; // Exit if accessed directly.
?>
<ol class='lty-import-steps-wrapper'>
	<?php foreach ($this->get_steps() as $step_key => $step) : ?>
		<li class='<?php echo esc_attr($this->get_step_classes($step_key)); ?>'><?php echo esc_html($step['name']); ?></li>
	<?php endforeach; ?>
</ol>
<?php
