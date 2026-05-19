<?php
/* Email Shortcodes */

if ( ! defined( 'ABSPATH' ) ) {
	exit ; // Exit if accessed directly.
}
/**
 * This hook is used to display extra content before email shortcode content.
 * 
 * @since 1.0
 */
do_action( 'lty_before_email_shortcode_contents_' . sanitize_title( $this->id ) ) ;
?>
<table class="form-table lty-email-shortcodes-info lty_<?php echo esc_attr( $this->id ) ; ?> widefat striped">
	<thead>
		<tr>
			<th><?php esc_html_e( 'Shortcode', 'lottery-for-woocommerce' ) ; ?></th>
			<th><?php esc_html_e( 'Description', 'lottery-for-woocommerce' ) ; ?></th>
		</tr>
	</thead>
	<tbody>
		<?php
		if ( lty_check_is_array( $shortcodes_info ) ) :
			foreach ( $shortcodes_info as $key => $values ) :
				?>
				<tr>
					<td><?php echo esc_html( $key ) ; ?></td>
					<td><?php echo esc_html( $values[ 'description' ] ) ; ?></td>
				</tr>
				<?php
			endforeach ;
		endif ;
		?>
	</tbody>
</table>

<?php
/**
 * This hook is used to display extra content after email shortcode content.
 * 
 * @since 1.0
 */
do_action( 'lty_after_email_shortcodes_contents_' . sanitize_title( $this->id ) ) ;

