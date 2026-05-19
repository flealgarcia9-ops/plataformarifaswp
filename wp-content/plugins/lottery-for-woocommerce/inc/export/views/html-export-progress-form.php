<?php
/**
 * Export - Progress form.
 *
 * @since 10.3.0
 */
defined('ABSPATH') || exit; // Exit if accessed directly.
?>
<div class='lty-export-progress-form-wrapper'>
	<header>
		<span class='spinner is-active'></span>
		<h2><?php esc_html_e('Exporting', 'lottery-for-woocommerce'); ?></h2>
		<p><?php echo esc_html($this->get_exporting_description()); ?></p>
	</header>
	<section>
		<p class='lty-export-progress-bar-details'>
			<span class='lty-export-progress-bar-right'><span class='lty-export-progress-bar-percentage'>0</span>%</span>
		</p>
		<progress class='lty-exporter-progress' max='100' value='0'></progress>
		<p class='lty-export-progress-bar-details'>
			<span class='lty-export-progress-bar-left'>0</span>
			<span class='lty-export-progress-bar-right'><?php echo esc_attr($this->total_rows); ?></span>
		</p>
	</section>
	<form id='lty_export_progress_form' method='POST'>
		<div class='lty-actions'>
			<input type='hidden' id='lty-export-file-name' name='filename' value='<?php echo esc_attr($this->filename); ?>'/>
			<input type='hidden' class='lty-export-page'  name='page' value='<?php echo esc_attr($this->get_page()); ?>'/>
			<input type='hidden' class='lty-export-offset'  name='offset' value='<?php echo esc_attr($this->get_offset()); ?>'/>
			<input type='hidden' class='lty-export-total'  name='total' value='<?php echo esc_attr($this->get_total()); ?>'/>
			<input type='hidden' class='lty-export-limit'  name='limit' value='<?php echo esc_attr($this->get_limit()); ?>'/>
			<input type='hidden' class='lty-export-type'  name='export_type' value='<?php echo esc_attr($this->get_export_type()); ?>'/>
			<input type='hidden' class='lty-extra-data'  name='extra_data' value='<?php echo wp_json_encode($this->get_extra_data()); ?>'/>
			<input type='hidden' class='lty-custom-field-data'  name='custom_field_data' value='<?php echo wp_json_encode( $this->get_custom_field_data() ); ?>'/>
		</div>
	</form>
</div>
<?php
