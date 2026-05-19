<?php
/* Cron Info */

if ( ! defined( 'ABSPATH' ) ) {
	exit ; // Exit if accessed directly.
}
/**
 * This hook is used to do extra action before cron information.
 * 
 * @since 1.0
 */
do_action( 'lty_before_cron_information' ) ;
?>
<table class="form-table lty-server-cron-info widefat striped">
	<thead>
		<tr>
			<th><?php esc_html_e( 'Cron Name', 'lottery-for-woocommerce' ) ; ?></th>
			<th><?php esc_html_e( 'Last Updated', 'lottery-for-woocommerce' ) ; ?></th>
		</tr>
	</thead>
	<tbody>
		<?php
		if ( lty_check_is_array( $cron_info ) ) {
			foreach ( $cron_info as $key => $values ) {
				?>
				<tr>
					<td><?php echo esc_html( $values[ 'cron' ] ) ; ?></td>
					<td><?php echo esc_html( $values[ 'last_updated_date' ] ) ; ?></td>
				</tr>
				<?php
			}
		}
		?>
	</tbody>
</table>

<?php
/**
 * This hook is used to do extra action after cron information.
 * 
 * @since 1.0
 */
do_action( 'lty_after_cron_information' ) ;

