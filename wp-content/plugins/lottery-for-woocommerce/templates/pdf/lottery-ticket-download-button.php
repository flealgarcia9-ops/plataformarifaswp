<?php
/**
 * This template is used to customize the lottery tickets pdf download button template.
 *
 * This template can be overridden by copying it to yourtheme/lottery-for-woocommerce/pdf/lottery-ticket-download-button.php
 *
 * To maintain compatibility, Giveaway(formerly Lottery) For WooCommerce will update the template files and you have to copy the updated files to your theme
 *
 * @since 9.5.0
 * @var string $url Button URL.
 */

defined( 'ABSPATH' ) || exit;
?>
<div class='lty-lottery-ticket-pdf-download'><a class='button' href="<?php echo esc_url( $url ); ?>" class='lty-lottery-ticket-pdf-download-btn'><?php esc_html_e( 'Download Giveaway Ticket(s) PDF', 'lottery-for-woocommerce' ); ?></a></div>
