<?php
/**
 * This template is used displaying the entry list overview.
 *
 * This template can be overridden by copying it to yourtheme/lottery-for-woocommerce/single-entry-list/summary.php
 *
 * To maintain compatibility, Giveaway(formerly Lottery) For WooCommerce will update the template files and you have to copy the updated files to your theme
 *
 * @since 9.0.0
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

?>
<div class='lty-entry-list-summary-content-wrapper'>
	<div class='lty-entry-list-header-wrapper'>
		<h2 class='lty-entry-list-title'><a href='<?php echo esc_url( lty_get_entry_list_product_permalink( $product ) ); ?>'><?php echo wp_kses_post( $product->get_name() ); ?></a></h2>
		<?php if ( isset( $pdf_download_button_url ) && ! empty( $pdf_download_button_url ) ) : ?>
			<a href="<?php echo esc_url( $pdf_download_button_url ); ?>" class='button lty-lottery-entry-list-pdf-download-button'><?php esc_html_e( 'Download PDF', 'lottery-for-woocommerce' ); ?></a>
			<?php endif; ?>
	</div>

	<?php if ( lty_can_display_lottery_entry_list_summary() ) : ?>
		<div class='lty-entry-list-summary-content'>
			<div class='lty-entry-list-summary-left-content'>
				<p>
					<label><?php echo wp_kses_post( lty_get_entry_list_status_label() ); ?></label>
					<span class='lty-entry-list-status'><?php echo wp_kses_post( lty_display_status( $product->get_lty_lottery_status() ) ); ?></span>
				</p>
				<p>
					<label><?php echo wp_kses_post( lty_get_entry_list_start_date_label() ); ?></label>
					<span><?php echo wp_kses_post( $product->get_fomatted_start_date_text() ); ?></span>
				</p>
				<p>
					<label><?php echo wp_kses_post( lty_get_entry_list_end_date_label() ); ?></label>
					<span><?php echo wp_kses_post( $product->get_fomatted_end_date_text() ); ?></span>
				</p>
				<p>
					<label><?php echo wp_kses_post( lty_get_entry_list_winner_count_label() ); ?></label>
					<span><?php echo esc_html( $product->get_lty_winners_count() ); ?></span>
				</p>
			</div>
			<div class='lty-entry-list-summary-right-content'>
				<p>
					<label><?php echo wp_kses_post( lty_get_entry_list_maximum_tickets_count_label() ); ?></label>
					<span><?php echo esc_html( $product->get_lty_maximum_tickets() ); ?></span>
				</p>
				<p>
					<label><?php echo wp_kses_post( lty_get_entry_list_purchased_tickets_count_label() ); ?></label>
					<span><?php echo esc_html( $product->get_purchased_ticket_count() ); ?></span>
				</p>
				<p>
					<label><?php echo wp_kses_post( lty_get_entry_list_remaining_tickets_count_label() ); ?></label>
					<span><?php echo esc_html( $product->get_remaining_ticket_count() ); ?></span>
				</p>
			</div>
		</div>
	<?php endif; ?>
</div>
<?php
/* Omit closing PHP tag at the end of PHP files to avoid "headers already sent" issues. */
