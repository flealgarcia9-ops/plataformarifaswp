<?php
/**
 * Product data question global.
 * */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$questions = get_option( 'lty_question_answers_global', array() );
?>
<table class = "form-table lty-question-answer-global-table">
	<thead>
		<tr>
			<th><?php esc_html_e( 'Options', 'lottery-for-woocommerce' ); ?></th>
			<th><?php esc_html_e( 'Correct Answer', 'lottery-for-woocommerce' ); ?></th>
			<th><?php esc_html_e( 'Remove Option', 'lottery-for-woocommerce' ); ?></th>
		</tr> 
	</thead>

	<tbody>
		<?php
		if ( isset( $questions[0]['answers'] ) && lty_check_is_array( $questions[0]['answers'] ) ) {
			foreach ( $questions[0]['answers'] as $answer_id => $answer ) {
				$name = "lty_question_answers_global[0][answers][{$answer_id}]";
				?>
				<tr>
					<td><input type='text' class='lty-answer-label-global' name="<?php echo esc_attr( $name ); ?>[label]" value='<?php echo esc_attr( $answer['label'] ); ?>'></td>
					<td><input type='checkbox' class='lty-select-answer-global' name="<?php echo esc_attr( $name ); ?>[valid]" <?php checked( $answer['valid'], 'yes' ); ?>></td>
					<td>
						<input type='hidden' class='lty-question-answer-id-global' value="<?php echo esc_attr( $answer_id ); ?>"/>
						<a class='lty-remove-answer-global button'><?php esc_html_e( 'Remove Option', 'lottery-for-woocommerce' ); ?></a>
					</td>
				</tr>
				<?php
			}
		}
		?>
	</tbody>
	<tfoot>
		<tr>
			<td colspan='4'>
				<a class="lty-add-answer-global button"><?php esc_html_e( 'Add Answer Options', 'lottery-for-woocommerce' ); ?></a>
			</td> 
		</tr>
	</tfoot>
</table>
<script type='text/html' id='tmpl-lty-question-answer-global'>
	<?php require LTY_ABSPATH . 'inc/admin/menu/views/html-product-data-question-answer-global.php'; ?>
</script>
<?php
