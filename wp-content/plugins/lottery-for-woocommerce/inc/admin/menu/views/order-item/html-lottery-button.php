<?php
/**
 * Generate ticket.
 * */
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}
?>
<tr class="item lty-generate-ticket">
	<td colspan="15" style="text-align:right;">
		<button type="button" class="lty-generate-ticket-action lty-item-id <?php echo esc_attr($manual_generation_args['class_name']); ?> button button-primary button-large" 
				data-item_id="<?php echo esc_attr($item_id); ?>">
					<?php echo esc_html($manual_generation_args['button_name']); ?>
		</button>
		<input type="hidden" class="lty-order-id" value="<?php echo esc_html($order->get_id()); ?>">
	</td>
</tr>

<?php if ($product->is_automatic_ticket()) : ?>

	<script type="text/template" id="tmpl-lty-automatic-tickets-popup">
	<?php
	include LTY_ABSPATH . 'inc/admin/menu/views/order-item/automatic-ticket-popup.php';
	?>
	</script>
	<?php
endif;

if ($product->is_manual_ticket()) :
	?>

	<script type="text/template" id="tmpl-lty-manual-tickets-popup">
	<?php
	include LTY_ABSPATH . 'inc/admin/menu/views/order-item/manual-ticket-popup.php';
	?>
	</script>
	<?php
endif;

if ($manual_generation_args['show_question_modal']) :
	?>
	<script type='text/template' id='tmpl-<?php echo esc_attr($manual_generation_args['answer_modal_id']); ?>'>
		<?php
		include LTY_ABSPATH . 'inc/admin/menu/views/order-item/question-answer-modal.php';
		?>
	</script>
	<?php



endif;
