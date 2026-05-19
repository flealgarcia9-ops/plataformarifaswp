<?php
/**
 * Import - Upload form.
 *
 * @since 9.9.0
 */
defined( 'ABSPATH' ) || exit; // Exit if accessed directly.
?>
<div class='lty-import-form-wrapper'>
	<form id='lty_import_form' method='POST' enctype='multipart/form-data'>
		<table class='lty-import-upload-table'>
			<tr>
				<th><?php esc_html_e( 'Choose a CSV file from your computer:', 'lottery-for-woocommerce' ); ?></th>
				<td>
					<input type='file' id='lty_import_file' name='import' accept='.csv,.txt'/>
					<input type='hidden' id='lty_import_max_file_size' value='<?php echo esc_attr( $this->get_wp_max_upload_size() ); ?>'/>
					<br>
					<small>
						<?php
						printf(
								/* translators: %s: maximum upload size */
							esc_html__( 'Maximum size: %s', 'lottery-for-woocommerce' ),
							esc_html( size_format( $this->get_wp_max_upload_size() ) )
						);
						?>
					</small>
				</td>
			</tr>
			<tr class='lty-import-advanced-options-field hidden'>
				<th><?php esc_html_e( 'Import Chunk Count', 'lottery-for-woocommerce' ); ?></th>
				<td>
					<input type='number' class='lty-import-limit' min='1' step='1' name='limit' value='<?php echo esc_attr( $this->get_limit() ); ?>'/>
					<p class='lty-import-field-description'><?php esc_html_e( 'Enter the import chunk count(Background Process), you can increase the import chunk count based on maximum number of entries to import', 'lottery-for-woocommerce' ); ?></p>
				</td>
			</tr>
		</table>
	</form>

	<?php if ( $this->get_upload_file_description() ) : ?>
		<p class='lty-upload-file-description'>
		<?php
			printf(
					/* translators: %s: description */
				wp_kses_post( __( '<b>Note :</b> %s', 'lottery-for-woocommerce' ) ),
				wp_kses_post( $this->get_upload_file_description() )
			);
		?>
		</p>
	<?php endif; ?>

	<footer class='wc-backbone-modal-footer'>
	<a href='#' class='lty-import-advanced-options' 
		data-show_text='<?php esc_html_e( 'Show Advanced Options', 'lottery-for-woocommerce' ); ?>'
		data-hide_text='<?php esc_html_e( 'Hide Advanced Options', 'lottery-for-woocommerce' ); ?>'>
		<?php esc_html_e( 'Show Advanced Options', 'lottery-for-woocommerce' ); ?></a>
		<a class='lty-upload-form button button-primary'><?php esc_html_e( 'Continue', 'lottery-for-woocommerce' ); ?></a>
	</footer>
</div>
<?php
