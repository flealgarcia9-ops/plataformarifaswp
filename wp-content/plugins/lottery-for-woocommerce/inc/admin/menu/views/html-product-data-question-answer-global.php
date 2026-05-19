<?php
/**
 * Product Lottery Question Answer global.
 * */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$name = 'lty_question_answers_global[0][answers][{{data.answer_option_id}}]';
?>
<tr>
	<td><input type='text' class='lty-answer-label-global' name="<?php echo esc_attr( $name ); ?>[label]"></td>
	<td><input type='checkbox' class='lty-select-answer-global' name="<?php echo esc_attr( $name ); ?>[valid]"></td>
	<td>
		<input type='hidden' class='lty-question-answer-id-global' value="{{data.answer_option_id}}"/>
		<a class='lty-remove-answer-global button'><?php esc_html_e( 'Remove Option', 'lottery-for-woocommerce' ); ?></a>
	</td>
</tr>
<?php
