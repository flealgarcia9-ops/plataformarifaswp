<?php
/**
 * This template is used to customize the lottery entry list template.
 *
 * This template can be overridden by copying it to yourtheme/lottery-for-woocommerce/pdf/entry-list.php
 *
 * To maintain compatibility, Giveaway(formerly Lottery) For WooCommerce will update the template files and you have to copy the updated files to your theme
 *
 * @since 9.5.0
 * @var object $product Product object.
 * @var array $ticket_ids Ticket IDs.
 * @var array $ticket_log_columns Ticket log columns.
 * @var array $winner_log_columns Winners log columns.
 * @var array $winner_ids winner IDs.
 */

defined( 'ABSPATH' ) || exit;
?>
<style>
	.lty-entry-list-summary-content-wrapper {
		font-size: small;
	}

	.lty-entry-list-header-wrapper {
		display: inline-block;
		margin-bottom: 15px;
		border-radius: 3px;
	}

	.lty-entry-list-ticket-logs-wrapper table th {
		background: <?php echo esc_html( get_option( 'lty_settings_entry_list_pdf_table_header_bg_color' ) ); ?>;
		color: <?php echo esc_html( get_option( 'lty_settings_entry_list_pdf_table_header_font_color' ) ); ?>;
		border: 1px solid <?php echo esc_html( get_option( 'lty_settings_single_product_progress_bar_bg_color' ) ); ?>;
		padding: 8px;
	}

	.lty-entry-list-ticket-logs-wrapper table td {
		border-right: 1px solid <?php echo esc_html( get_option( 'lty_settings_single_product_progress_bar_bg_color' ) ); ?>;
		padding: 8px;
	}

	.lty-entry-list-ticket-logs-wrapper table tr:nth-child(even) {
		background: #f2f2f2;
	}

	.lty-entry-list-ticket-logs-wrapper table {
		width: 100%;
		border-collapse: collapse;
		text-align: center;
		table-layout: fixed !important;
		border: 1px solid <?php echo esc_html( get_option( 'lty_settings_single_product_progress_bar_bg_color' ) ); ?>;
		padding: 8px;
		font-size: 12px !important;
	}
</style>
<div class='lty-entry-list-summary-content-wrapper'>
	<div class='lty-entry-list-header-wrapper'>
		<h2 class='lty-entry-list-title'><?php echo wp_kses_post( $product->get_name() ); ?></h2>
	</div>

	<table class='lty-entry-list-summary-content'>
		<tr>
			<td style="width: 50%;"><label><?php echo wp_kses_post( lty_get_entry_list_start_date_label() ); ?></label></td>
			<td><span><?php echo wp_kses_post( $product->get_fomatted_start_date_text() ); ?></span></td>
		</tr>
		<tr>
			<td><label><?php echo wp_kses_post( lty_get_entry_list_end_date_label() ); ?></label></td>
			<td><span><?php echo wp_kses_post( $product->get_fomatted_end_date_text() ); ?></span></td>
		</tr>
		<tr>
			<td><label><?php echo wp_kses_post( lty_get_entry_list_winner_count_label() ); ?></label></td>
			<td><span><?php echo esc_html( $product->get_lty_winners_count() ); ?></span></td>
		</tr>
		<tr>
			<td><label><?php echo wp_kses_post( lty_get_entry_list_maximum_tickets_count_label() ); ?></label></td>
			<td><span><?php echo esc_html( $product->get_lty_maximum_tickets() ); ?></span></td>
		</tr>
		<tr>
			<td><label><?php echo wp_kses_post( lty_get_entry_list_purchased_tickets_count_label() ); ?></label></td>
			<td><span><?php echo esc_html( $product->get_purchased_ticket_count() ); ?></span></td>
		</tr>
		<tr>
			<td><label><?php echo wp_kses_post( lty_get_entry_list_remaining_tickets_count_label() ); ?></label></td>
			<td><span><?php echo esc_html( $product->get_remaining_ticket_count() ); ?></span></td>
		</tr>
	</table>

	<div class='lty-entry-list-ticket-logs-wrapper'>
		<?php
		// Winner log.
		if ( isset( $winner_ids ) && isset( $winner_log_columns ) ) :
			lty_get_template(
				'single-product/winner-log.php',
				array(
					'product'         => $product,
					'_columns'        => $winner_log_columns,
					'lottery_winners' => $winner_ids,
				)
			);
		endif;

		// Ticket log.
		if ( isset( $ticket_ids ) && isset( $ticket_log_columns ) ) :
			?>
			<h3><b><?php esc_html_e( 'Participants', 'lottery-for-woocommerce' ); ?></b></h3>
			<table class='lty-frontend-table lty-ticket-logs-table' style="table-layout: fixed;">
				<thead>
					<tr>
						<?php foreach ( $ticket_log_columns as $column_name ) : ?>
							<th><?php echo esc_html( $column_name ); ?></th>
						<?php endforeach; ?>
					</tr>
				</thead>
				<tbody>
					<?php
						lty_get_template(
							'single-product/tabs/ticket-logs.php',
							array(
								'_columns'   => $ticket_log_columns,
								'ticket_ids' => $ticket_ids,
							)
						);
					?>
				</tbody>
			</table>
		<?php else : ?>
			<div class='lty_log_empty_container'><?php esc_html_e( 'No ticket found.', 'lottery-for-woocommerce' ); ?></div>
		<?php endif; ?>
	</div>
</div>
