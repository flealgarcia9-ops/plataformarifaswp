<?php
/**
 * Import - Errors.
 *
 * @since 9.9.0
 */
defined('ABSPATH') || exit; // Exit if accessed directly.
?>
<div class='lty-import-errors'>
	<?php foreach ($this->get_errors() as $message) : ?>
		<span class='lty-import-error'><?php echo wp_kses_post($message); ?></span>
	<?php endforeach; ?>
</div>
<?php
