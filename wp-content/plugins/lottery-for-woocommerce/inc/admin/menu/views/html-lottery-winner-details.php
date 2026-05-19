<?php
/**
 * Winner Details.
 * */
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}
?>
<table class="lty-frontend-table lty-lottery-winners">
	<thead>
		<tr>
			<?php foreach ($_columns as $column_name) : ?>
				<th><?php echo esc_html($column_name); ?></th>
			<?php endforeach; ?>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($winner_tickets_ids as $winner_tickets_id) : ?>
			<tr>
				<?php
				$winner_obj = lty_get_lottery_winner($winner_tickets_id);

				foreach ($_columns as $key => $val) :
					?>
					<td data-title="<?php echo esc_html($val); ?>">
						<?php
						switch ($key) {
							case 'id':
								echo esc_html( $winner_obj->get_id() );
								break;

							case 'user_name':
								echo esc_html($winner_obj->get_user_name() . ' (' . $winner_obj->get_user_email() . ')');
								break;

							case 'ticket_number':
								echo esc_html($winner_obj->get_lottery_ticket_number());
								break;

							case 'answer':
								if ($winner_obj->get_answer()) {
									$class = ( 'yes' == $winner_obj->get_valid_answer() ) ? 'lty-correct-answer' : 'lty-wrong-answer';

									echo wp_kses_post('<p class="' . $class . '">' . $winner_obj->get_answer() . '</p>');
								} else {
									echo '-';
								}
								break;

							case 'order_id':
								$winner_order_id = $winner_obj->get_order_id();
								if (empty($winner_order_id)) :
									echo esc_html('-');
								else :
									printf('<a href="%s">%s</a>', esc_url(get_edit_post_link($winner_order_id)), esc_html('#' . $winner_order_id));
								endif;

								break;

							case 'gift_products':
								$product_title = array();
								$gift_products = $winner_obj->get_gift_products();
								if (lty_check_is_array($gift_products)) :
									echo esc_html($winner_obj->get_winning_details());
								else :
									echo esc_html('-');
								endif;

								break;

							default:
								echo esc_html($winner_obj->get_formatted_created_date());
								break;
						}
						?>
					</td>
				<?php endforeach; ?>
			</tr>
		<?php endforeach; ?>
	</tbody>
</table>
<?php
