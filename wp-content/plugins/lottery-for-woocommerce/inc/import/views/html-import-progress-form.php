<?php
/**
 * Import - Progress form.
 *
 * @since 9.9.0
 */
defined('ABSPATH') || exit; // Exit if accessed directly.
?>
<div class='lty-import-progress-form-wrapper'>
	<header>
		<span class='spinner is-active'></span>
		<h2><?php esc_html_e('Importing', 'lottery-for-woocommerce'); ?></h2>
		<p><?php esc_html_e('Your instant winner rules are now being imported...', 'lottery-for-woocommerce'); ?></p>
	</header>
	<section>
		<progress class='lty-importer-progress' max='100' value='0'></progress>
	</section>
	<form id='lty_import_progress_form' method='POST'>
		<div class='lty-actions'>
			<input type='hidden' id='lty-import-file' name='file' value='<?php echo esc_attr($this->file); ?>'/>
			<input type='hidden' id='lty-import-position' name='position' value='<?php echo esc_attr($this->position); ?>'/>
			<input type='hidden' id='lty-imported-count' name='imported' value='<?php echo esc_attr($this->imported); ?>'/>
			<input type='hidden' id='lty-import-failed-count' name='failed' value='<?php echo esc_attr($this->failed); ?>'/>
			<input type='hidden' id='lty-import-updated-count' name='updated' value='<?php echo esc_attr($this->updated); ?>'/>
			<input type='hidden' id='lty-import-limit' name='limit' value='<?php echo esc_attr($this->limit); ?>'/>
		</div>
	</form>
</div>
<?php
