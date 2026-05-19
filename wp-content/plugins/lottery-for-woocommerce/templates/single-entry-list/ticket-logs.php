<?php
/**
 * This template is used displaying the entry list overview.
 *
 * This template can be overridden by copying it to yourtheme/lottery-for-woocommerce/single-entry-list/ticket-logs.php
 *
 * To maintain compatibility, Giveaway(formerly Lottery) For WooCommerce will update the template files and you have to copy the updated files to your theme
 *
 * @since 9.0.0
 * @var object $product Product object.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>
<div class='lty-entry-list-ticket-logs-content-wrapper'>
	<h3><?php esc_html_e( 'Ticket Logs', 'lottery-for-woocommerce' ); ?></h3>
	<?php lty_get_template( 'single-product/tabs/ticket-logs-layout.php', lty_prepare_lottery_entry_list_ticket_log_arguments( $product ) ); ?>
</div>
<?php
/* Omit closing PHP tag at the end of PHP files to avoid "headers already sent" issues. */
