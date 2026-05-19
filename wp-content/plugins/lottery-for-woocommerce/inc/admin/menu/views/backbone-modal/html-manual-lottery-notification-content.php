<?php
/**
 * Popup - Manual lottery notification content.
 *
 * @since 12.4.0
 * @var WC_Product_Lottery $product Product object.
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

?>
<div class='lty-manual-lottery-notification-wrapper'>
	<p class='lty-notice notice inline notice-alt' style='display: none;'></p>
	<div class='lty-manual-lottery-notification-fields'>
		<label for='lty-manual-lottery-notification-id'><?php esc_html_e( 'Select Email: ', 'lottery-for-woocommerce' ); ?></label>
		<select class='lty-manual-lottery-notification-id'>
			<?php foreach ( lty_get_manual_lottery_notification_options( $product ) as $notification_id => $notification_title ) : ?>
				<option value='<?php echo esc_attr( $notification_id ); ?>'><?php echo esc_html( $notification_title ); ?></option>
			<?php endforeach; ?>
		</select>
	</div>
</div>
<?php
