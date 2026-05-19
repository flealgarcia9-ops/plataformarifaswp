<?php
/**
 * This template is used to customize the email lottery tickets pdf download button template.
 *
 * This template can be overridden by copying it to yourtheme/lottery-for-woocommerce/email-shortcodes/lottery-ticket-download-button.php
 *
 * To maintain compatibility, Giveaway(formerly Lottery) For WooCommerce will update the template files and you have to copy the updated files to your theme
 *
 * @since 10.5.0
 * @var string $button_url Button URL.
 */

defined( 'ABSPATH' ) || exit;
?>
<a class='button' href="<?php echo esc_url( $button_url ); ?>"><?php echo wp_kses_post( lty_get_email_lottery_ticket_pdf_download_button_label() ); ?></a>
