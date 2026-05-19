<?php
/**
 * This template is used for displaying the question answer. 
 *
 * This template can be overridden by copying it to yourtheme/lottery-for-woocommerce/single-product/question-answer.php
 *
 * To maintain compatibility, Giveaway(formerly Lottery) For WooCommerce will update the template files and you have to copy the updated files to your theme
 * 
 * @since 1.0.0
 */
if (!defined('ABSPATH')) {
	exit;
}

if (!$product->is_started() || $product->is_closed()) {
	return;
}

/**
 * This hook is used to do extra action before lottery question answer container.
 * 
 * @since 1.0
 */
do_action('lty_before_lottery_question_answer_container', $product);
?>
<div class ="lty-lottery-question-answer-container" data-force="<?php echo esc_attr( $product->is_force_answer_enabled() ); ?>">
	<h3><?php echo wp_kses_post( get_option( 'lty_settings_single_product_question_answer_heading_label', __( 'Question Answers', 'lottery-for-woocommerce' ) ) ); ?></h3>

	<?php
	/**
	 * This hook is used to do extra action before lottery question content.
	 * 
	 * @since 6.7
	 */
	do_action('lty_before_lottery_question_content', $product);
	?>
	<p class='lty-lottery-question'><?php echo esc_html($question['question']); ?></p>
	<?php
	if ('1' === $product->get_question_answer_display_type()) :
		?>
	<ul class="lty-lottery-answers">
		<?php
		foreach ($question['answers'] as $answer_id => $answer) :
			$class_name = $cart_answer_id == $answer_id ? 'lty-selected' : '';
			?>
			<li class="<?php echo esc_attr($class_name); ?>" data-answer-id="<?php echo esc_attr($answer_id); ?>"><?php echo esc_html($answer['label']); ?></li>
		<?php endforeach; ?>
		<input type="hidden" class="lty-question-answer-id" name="lty_question_answer_id" value ="<?php echo esc_html($cart_answer_id); ?>"/>
	</ul>
		<?php
	else : 
		?>
	<p class='lty-lottery-options-field-wrapper'>
		<select id='lty_lottery_options_field' class='lty-lottery-answers lty-question-answer-id' name="lty_question_answer_id">
			<?php if ( ! $product->is_question_answer_first_option_as_default_option() ) : ?>
				<option value=''><?php echo wp_kses_post(lty_get_question_answer_dropdown_default_label()); ?></option>
				<?php 
			endif;
			foreach ($question['answers'] as $answer_id => $answer) :
				$class_name = $cart_answer_id == $answer_id ? 'lty-selected' : '';
				?>
				<option value="<?php echo esc_attr($answer_id); ?>"
						class="<?php echo esc_attr($class_name); ?>" 
						<?php echo selected('lty-selected', $class_name); ?>
						data-answer-id="<?php echo esc_attr($answer_id); ?>">
						<?php echo esc_html($answer['label']); ?>
				</option>
				<?php
			endforeach; 
			?>
		</select>
	</p>
		<?php
	endif;

	/**
	 * This hook is used to do extra action after lottery question content.
	 * 
	 * @since 6.7
	 */
	do_action('lty_after_lottery_question_content', $product);
	?>
</div>

<?php
/**
 * This hook is used to do extra action after lottery question answer container.
 * 
 * @since 1.0
 */
do_action('lty_after_lottery_question_answer_container', $product);

