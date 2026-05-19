<?php
/**
 * This template is used for displaying the lottery winner details.
 *
 * This template can be overridden by copying it to yourtheme/lottery-for-woocommerce/email-shortcodes/winner-details.php
 *
 * To maintain compatibility, Giveaway(formerly Lottery) For WooCommerce will update the template files and you have to copy the updated files to your theme
 *
 * @since 9.1.0
 * @var array $columns Columns.
 * @var array $lottery_winners Lottery winners.
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.
?>

<table class='lty-frontend-table lty-winner-logs-table'>
	<thead>
		<tr>
			<?php foreach ( $columns as $column_name ) : ?>
				<th style="padding-left: 15px;"><?php echo esc_html( $column_name ); ?></th>
			<?php endforeach; ?>
		</tr>
	</thead>
	<tbody>
		<?php foreach ( $lottery_winners as $key => $lottery_winner_id ) : ?>
			<tr>
				<?php
				$winner_log = lty_get_lottery_winner( $lottery_winner_id );
				foreach ( $columns as $column_key => $column_name ) :
					?>
					<td style="padding-left: 15px;" data-title="<?php echo esc_attr( $column_name ); ?>">
						<?php
						switch ( $column_key ) :
							case 'user_name':
								echo esc_html( $winner_log->display_user_name() );
								break;

							case 'ticket_number':
								echo esc_html( $winner_log->get_lottery_ticket_number() );
								break;
						endswitch;
						?>
					</td>
					<?php
				endforeach;
				?>
			</tr>
		<?php endforeach; ?>
	</tbody>
</table>
<?php

