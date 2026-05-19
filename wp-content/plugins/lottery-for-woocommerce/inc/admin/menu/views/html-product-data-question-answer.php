<?php
/**
 * Product Lottery Question Answer.
 * */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$name = '_lty_questions[0][answers][{{data.answer_option_id}}]';
?>
<tr>
	<td><input type='text' class='lty-question-answer' name="<?php echo esc_attr( $name ); ?>[label]"></td>
	<td><input type='checkbox' class='lty-select-answer' name="<?php echo esc_attr( $name ); ?>[valid]"></td>
	<td>
		<input type='hidden' class='lty-question-answer-id' value='{{data.answer_option_id}}'/>
		<a class='lty-remove-answer button'><?php esc_html_e( 'Remove Option', 'lottery-for-woocommerce' ); ?></a>
	</td>
</tr>
<?php
