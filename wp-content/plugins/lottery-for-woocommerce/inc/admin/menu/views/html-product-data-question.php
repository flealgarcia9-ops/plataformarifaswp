<?php
/**
 * Product Lottery Question data panel.
 * */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$questions = is_callable( array( $product_object, 'get_lty_questions' ) ) ? $product_object->get_lty_questions() : '';
$question  = isset( $questions[0]['question'] ) ? $questions[0]['question'] : '';
?>
<div id="lty_question_tab" class="panel woocommerce_options_panel">
	<div class="<?php echo esc_attr( $wrapper_class_name ); ?>">
		<div class="options_group show_if_lottery">
			<?php
			$is_valid_question_answer = is_callable( array( $product_object, 'is_valid_question_answer' ) ) ? $product_object->is_valid_question_answer() : false;
			$default_option           = $is_valid_question_answer ? 1 : 2;
			woocommerce_wp_select(
				array(
					'id'      => '_lty_question_answer_selection_type',
					'label'   => __( 'Question Answer Level Selection Type', 'lottery-for-woocommerce' ),
					'value'   => is_callable( array( $product_object, 'get_lty_question_answer_selection_type' ) ) ? $product_object->get_lty_question_answer_selection_type() : $default_option,
					'options' => array(
						'1' => __( 'Product Level', 'lottery-for-woocommerce' ),
						'2' => __( 'Global Level', 'lottery-for-woocommerce' ),
					),
				)
			);
			woocommerce_wp_checkbox(
				array(
					'id'          => '_lty_manage_question',
					'value'       => is_callable( array( $product_object, 'get_lty_manage_question' ) ) ? $product_object->get_lty_manage_question() : '',
					'class'       => 'lty-question-answer-product-field',
					'label'       => __( 'Ask a Question before Purchasing Tickets', 'lottery-for-woocommerce' ),
					'description' => __( 'When enabled, a question will be displayed to the user.', 'lottery-for-woocommerce' ),
				)
			);
			woocommerce_wp_checkbox(
				array(
					'id'          => '_lty_force_answer',
					'value'       => is_callable( array( $product_object, 'get_lty_force_answer' ) ) ? $product_object->get_lty_force_answer() : '',
					'class'       => 'lty-question-answer-product-field lty-question-answer-field',
					'label'       => __( 'Force Users to Answer the Question', 'lottery-for-woocommerce' ),
					'description' => __( 'When enabled, the users will not be allowed to purchase tickets unless they select an answer.', 'lottery-for-woocommerce' ),
				)
			);
			woocommerce_wp_select(
				array(
					'id'          => '_lty_question_answer_time_limit_type',
					'class'       => 'lty-question-answer-product-field lty-question-answer-field lty-force-question-answer-field',
					'label'       => __( 'Q/A Time Limit Type', 'lottery-for-woocommerce' ),
					'value'       => is_callable( array( $product_object, 'get_lty_question_answer_time_limit_type' ) ) ? $product_object->get_lty_question_answer_time_limit_type() : 1,
					'options'     => array(
						'1' => __( 'Unlimited', 'lottery-for-woocommerce' ),
						'2' => __( 'Limited', 'lottery-for-woocommerce' ),
					),
					'desc_tip'    => true,
					'description' => __( '"Unlimited" option, user can select the answer to the question without having any time limit. "Limited" option, you can set a time limit for the user to answer the question(if the time limit is  exceeded then the user cannot participate in the giveaway).', 'lottery-for-woocommerce' ),
				)
			);
			$time_limit         = is_callable( array( $product_object, 'get_formatted_question_answer_time_limit' ) ) ? $product_object->get_formatted_question_answer_time_limit() : array(
				'unit'   => 'minutes',
				'number' => '5',
			);
			$time_limit_options = lty_relative_date_picker_options( 3 );
			?>
			<p class="form-field">
				<label for="lty_question_answer_time_limit"><?php esc_html_e( 'Set Time Limit', 'lottery-for-woocommerce' ); ?><span class='required'>*</span></label>
				<input type='number' class="lty-question-answer-time-limit-number" name="_lty_question_answer_time_limit[number]" value='<?php echo esc_attr( $time_limit['number'] ); ?>'/>
				<select name="_lty_question_answer_time_limit[unit]" class="lty_question_answer_time_limit lty-question-answer-product-field lty-question-answer-field lty-force-question-answer-field">
					<?php foreach ( $time_limit_options as $key => $name ) { ?>
						<option value='<?php echo esc_attr( $key ); ?>' <?php selected( $key, $time_limit['unit'] ); ?>><?php echo esc_html( $name ); ?></option>
					<?php } ?>
				</select>
			</p>
			<?php
			woocommerce_wp_checkbox(
				array(
					'id'          => '_lty_restrict_incorrectly_selected_answer',
					'value'       => is_callable( array( $product_object, 'get_lty_restrict_incorrectly_selected_answer' ) ) ? $product_object->get_lty_restrict_incorrectly_selected_answer() : '',
					'class'       => 'lty-question-answer-product-field lty-question-answer-field lty-force-question-answer-field',
					'label'       => __( "Don't Generate Ticket Numbers for Incorrectly Answered Question", 'lottery-for-woocommerce' ),
					'description' => __( 'When enabled, the user will be allowed to complete the giveaway ticket purchase but, ticket will not be generated for the purchase.', 'lottery-for-woocommerce' ),
				)
			);
			woocommerce_wp_checkbox(
				array(
					'id'          => '_lty_validate_correct_answer',
					'value'       => is_callable( array( $product_object, 'get_lty_validate_correct_answer' ) ) ? $product_object->get_lty_validate_correct_answer() : '',
					'class'       => 'lty-question-answer-product-field lty-question-answer-field lty-force-question-answer-field',
					'label'       => __( 'Verify Answer Before Purchasing Giveaway', 'lottery-for-woocommerce' ),
					'description' => __( 'When enabled, only the users who answer the questions correctly will be allowed to participate in the giveaway.', 'lottery-for-woocommerce' ),
				)
			);
			woocommerce_wp_select(
				array(
					'id'      => '_lty_verify_answer_type',
					'class'   => 'lty-question-answer-product-field lty-question-answer-field lty-force-question-answer-field lty-verify-answer-field',
					'label'   => __( 'Select Verify Answer Type', 'lottery-for-woocommerce' ),
					'value'   => is_callable( array( $product_object, 'get_lty_verify_answer_type' ) ) ? $product_object->get_lty_verify_answer_type() : 1,
					'options' => array(
						'1' => __( 'Limited Attempts', 'lottery-for-woocommerce' ),
						'2' => __( 'Unlimited Attempts', 'lottery-for-woocommerce' ),
					),
				)
			);
			woocommerce_wp_text_input(
				array(
					'id'                => '_lty_question_answer_attempts',
					'label'             => __( 'Number of Attempts', 'lottery-for-woocommerce' ) . "<span class='required'>*</span>",
					'value'             => is_callable( array( $product_object, 'get_lty_question_answer_attempts' ) ) ? $product_object->get_lty_question_answer_attempts( 'edit' ) : 1,
					'type'              => 'number',
					'class'             => 'lty-question-answer-product-field lty-question-answer-field lty-force-question-answer-field lty-verify-answer-field',
					'custom_attributes' => array(
						'step' => 'any',
						'min'  => '1',
					),
				)
			);
			?>
		</div>
		<div class='options_group lty-question-wrapper show_if_lottery lty-question-answer-field'>
			<p class='form-field'>
				<label for="lty_question"><?php esc_html_e( 'Your Question', 'lottery-for-woocommerce' ); ?></label>
				<textarea name="_lty_questions[0][question]" class="lty-question"><?php echo esc_html( $question ); ?></textarea>
			</p>
			<?php
				woocommerce_wp_select(
					array(
						'id'      => '_lty_question_answer_display_type',
						'class'   => 'lty-question-answer-product-field lty-question-answer-field lty-question-answer-display-type-field',
						'label'   => __( 'Options Display type', 'lottery-for-woocommerce' ),
						'value'   => is_callable( array( $product_object, 'get_lty_question_answer_display_type' ) ) ? $product_object->get_lty_question_answer_display_type() : 1,
						'options' => array(
							'1' => __( 'Display all the Options to Choose', 'lottery-for-woocommerce' ),
							'2' => __( 'Use Dropdown for Options to Choose', 'lottery-for-woocommerce' ),
						),
					)
				);
				woocommerce_wp_checkbox(
					array(
						'id' => '_lty_question_answer_first_option_as_default_option',
						'value' => is_callable(array( $product_object, 'get_lty_question_answer_first_option_as_default_option' )) ? $product_object->get_lty_question_answer_first_option_as_default_option() : '',
						'class' => 'lty-question-answer-product-field lty-question-answer-field lty-question-answer-first-option-as-default-option',
						'label' => __('Display the first option as default in the Dropdown', 'lottery-for-woocommerce'),
						'description' => __('When enabled, it will remove the "Choose Answer" label and display the first option as default in the dropdown', 'lottery-for-woocommerce'),
					)
				);
				?>
			<div class="lty-question-answer-wrapper">
				<table class="lty-question-answer-table lty-backend-table">
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
								$name = "_lty_questions[0][answers][{$answer_id}]";
								?>
								<tr>
									<td><input type='text' class='lty-question-answer' name="<?php echo esc_attr( $name ); ?>[label]" value="<?php echo esc_attr( $answer['label'] ); ?>"></td>
									<td><input type='checkbox' class='lty-select-answer' name="<?php echo esc_attr( $name ); ?>[valid]" <?php checked( $answer['valid'], 'yes' ); ?>></td>
									<td>
										<input type='hidden' class='lty-question-answer-id' value="<?php echo esc_attr( $answer_id ); ?>"/>
										<a class='lty-remove-answer button'><?php esc_html_e( 'Remove Option', 'lottery-for-woocommerce' ); ?></a>
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
								<a class="lty-add-answer button"><?php esc_html_e( 'Add Answer Options', 'lottery-for-woocommerce' ); ?></a>
							</td> 
						</tr>
					</tfoot>
				</table>
			</div>
		</div>
	</div>
	<script type='text/html' id='tmpl-lty-question-answer'>
		<?php require LTY_ABSPATH . 'inc/admin/menu/views/html-product-data-question-answer.php'; ?>
	</script>
</div>
<?php
