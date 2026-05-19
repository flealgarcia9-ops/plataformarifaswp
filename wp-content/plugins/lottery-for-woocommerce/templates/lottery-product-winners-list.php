<?php
/**
 * This template is used for displaying the lottery product winners list.
 *
 * This template can be overridden by copying it to yourtheme/lottery-for-woocommerce/lottery-product-winners-list.php
 *
 * To maintain compatibility, Giveaway(formerly Lottery) For WooCommerce will update the template files and you have to copy the updated files to your theme
 */
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}

$sno = $offset + 1;
foreach ($winner_ids as $winner_id) :
	$winner_object = lty_get_lottery_winner($winner_id);
	if (!is_object($winner_object->get_product())) :
		continue;
	endif;

	if ('lottery' != $winner_object->get_product()->get_type()) :
		continue;
	endif;
	?>
	<tr>
		<?php foreach ($columns as $column_key => $column_name) : ?>
			<td data-title="<?php echo esc_attr($column_name); ?>">
				<?php
				switch ($column_key) :

					case 'sno':
						echo esc_html($sno++);
						break;

					case 'winners_name':
						echo esc_html($winner_object->display_user_name());
						break;

					case 'ticket_number':
						echo esc_html($winner_object->get_lottery_ticket_number());
						break;

					case 'lottery_product_name':
						printf('<a href="%s">%s</a>', esc_url($winner_object->get_product()->get_permalink()), esc_html($winner_object->get_product()->get_title()));
						break;

					case 'lottery_start_date':
						$start_date = LTY_Date_Time::get_wp_format_datetime_from_gmt($winner_object->get_product()->get_lty_start_date_gmt(), false, ' ', false);
						echo esc_html($start_date);
						break;

					case 'lottery_end_date':
						$end_date = LTY_Date_Time::get_wp_format_datetime_from_gmt($winner_object->get_product()->get_lty_end_date_gmt(), false, ' ', false);
						echo esc_html($end_date);
						break;

					case 'gift_products':
						echo wp_kses_post(lty_get_winner_gift_products_title(array_unique($winner_object->get_gift_products()), $winner_object->get_product()));
						break;

				endswitch;
				/**
				 * This hook is used to display the lottery product winner custom column content.
				 * 
				 * @since 1.0
				 */
				do_action('lty_lottery_product_winners_lists_column_' . $column_key, $winner_id, $winner_object);
				?>
			</td>
		<?php endforeach; ?>
	</tr> 
	<?php
endforeach;

