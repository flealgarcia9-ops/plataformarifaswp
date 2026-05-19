<?php
/**
 * Question Answer.
 * 
 * @since 7.4 
 */
if (!defined('ABSPATH')) {
	exit;
}
?>
<div class ='lty-lottery-question-answer-container' data-force='<?php echo esc_attr($product->is_force_answer_enabled()); ?>'>
	<h3><?php esc_html_e(__('Question Answers', 'lottery-for-woocommerce')); ?></h3>

	<p class='lty-lottery-question-label'><?php echo esc_html($question['question']); ?></p>

	<ul class="lty-lottery-answers-wrapper">
		<?php
		foreach ($question['answers'] as $answer_id => $answer) :
			?>
			<li class='lty-lottery-answer' data-answer-id="<?php echo esc_attr($answer_id); ?>"><?php echo esc_html($answer['label']); ?></li>
		<?php endforeach; ?>
	</ul>

	<input type='hidden' class='lty-question-answer-id' />
</div>
<?php

