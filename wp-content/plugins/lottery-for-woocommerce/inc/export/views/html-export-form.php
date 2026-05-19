<?php
/**
 * Export - Form.
 *
 * @since 10.3.0
 */
defined( 'ABSPATH' ) || exit; // Exit if accessed directly.
?>
<div class='lty-export-form-wrapper'>
	<form id='lty_export_form' method='POST' enctype='multipart/form-data'>
		<input type='hidden' class='lty-export-type'  name='export_type' value='<?php echo esc_attr( $this->get_export_type() ); ?>'/>
		<input type='hidden' class='lty-extra-data'  name='extra_data' value='<?php echo wp_json_encode( $this->get_extra_data() ); ?>'/>
		<table class='lty-export-form-table'>
			<tr>
				<th><?php esc_html_e( 'File Name', 'lottery-for-woocommerce' ); ?></th>
				<td>
					<input type='text' class='lty-export-file-name'  name='filename' value='<?php echo esc_attr( $this->get_default_file_name() ); ?>'/>
					<p class='lty-export-field-description'><?php esc_html_e( 'You can customize the file name', 'lottery-for-woocommerce' ); ?></p>
				</td>
			</tr>
			<?php
				/**
				 * This hook is used to add extra fields in the export form.
				 *
				 * @since 11.9.0
				 */
				do_action( 'lty_export_form_' . $this->get_export_type() . '_field_content' );
			?>
			<tr class='lty-export-advanced-options-field hidden'>
				<th><?php esc_html_e( 'Starting Entry Count', 'lottery-for-woocommerce' ); ?></th>
				<td>
					<input type='number' class='lty-export-offset' min='1' step='1' name='offset' value='1'/>
					<p class='lty-export-field-description'><?php esc_html_e( 'Enter the value to start the entry count in export(Example: 1 or 100 or 1000, etc.)', 'lottery-for-woocommerce' ); ?></p>
				</td>
			</tr>
			<tr class='lty-export-advanced-options-field hidden'>
				<th><?php esc_html_e( 'Maximum Number of Entries to export', 'lottery-for-woocommerce' ); ?></th>
				<td>
					<input type='number' class='lty-export-total' min='1' step='1' name='total' value=''/>
					<p class='lty-export-field-description'><?php esc_html_e( 'Enter the value for the maximum number of entries to export', 'lottery-for-woocommerce' ); ?></p>
				</td>
			</tr>
			<tr class='lty-export-advanced-options-field hidden'>
				<th><?php esc_html_e( 'Export Chunk Count', 'lottery-for-woocommerce' ); ?></th>
				<td>
					<input type='number' class='lty-export-limit' min='1' step='1' name='limit' value='<?php echo esc_attr( $this->get_limit() ); ?>'/>
					<p class='lty-export-field-description'><?php esc_html_e( 'Enter the export chunk count(Background Process), you can increase the export chunk count based on maximum number of entries to export', 'lottery-for-woocommerce' ); ?></p>
				</td>
			</tr>
		</table>
		<p class='lty-export-advanced-options-field hidden'><?php echo wp_kses_post( __( '<b>Note :</b> Exporting large amount of data sometimes fails due to more loading time. Using the Advanced Filter option, you can export the large amount of data by splitting the export data using <b>Starting Entry Count & Maximum Number of Entries to export</b>.', 'lottery-for-woocommerce' ) ); ?></p>
	</form>

	<footer class='wc-backbone-modal-footer'>
		<a href='#' class='lty-export-advanced-options' 
			data-show_text='<?php esc_html_e( 'Show Advanced Options', 'lottery-for-woocommerce' ); ?>'
			data-hide_text='<?php esc_html_e( 'Hide Advanced Options', 'lottery-for-woocommerce' ); ?>'>
			<?php esc_html_e( 'Show Advanced Options', 'lottery-for-woocommerce' ); ?></a>
		<a class='lty-export button button-primary'><?php esc_html_e( 'Export', 'lottery-for-woocommerce' ); ?></a>
	</footer>
</div>
<?php
