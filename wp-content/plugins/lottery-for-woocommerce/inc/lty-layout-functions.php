<?php
/**
 * Layout functions
 * */
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}

if (!function_exists('lty_select2_html')) {

	/**
	 * Return or display Select2 HTML.
	 *
	 * @return string
	 * */
	function lty_select2_html( $args, $echo = true ) {
		$args = wp_parse_args(
				$args, array(
			'class' => '',
			'id' => '',
			'name' => '',
			'list_type' => 'post',
			'action' => '',
			'placeholder' => '',
			'exclude_global_variable' => 'no',
			'exclude_type' => 'no',
			'exclude_out_of_stock' => 'no',
			'include_lottery_statuses' => array(),
			'lty_product_id' => '',
			'custom_attributes' => array(),
			'multiple' => true,
			'allow_clear' => true,
			'selected' => true,
			'error' => '',
			'options' => array(),
			'css' => 'width:50%',
				)
				);

		$multiple = $args['multiple'] ? 'multiple="multiple"' : '';
		$name = esc_attr('' !== $args['name'] ? $args['name'] : $args['id']) . '[]';
		$options = array_filter(lty_check_is_array($args['options']) ? $args['options'] : array());

		$allowed_html = array(
			'select' => array(
				'id' => array(),
				'class' => array(),
				'data-placeholder' => array(),
				'data-allow_clear' => array(),
				'data-error' => array(),
				'data-exclude-global-variable' => array(),
				'data-lty-product-id' => array(),
				'data-exclude-type' => array(),
				'data-exclude-out-of-stock' => array(),
				'data-include-lottery-statuses' => array(),
				'data-action' => array(),
				'data-nonce' => array(),
				'multiple' => array(),
				'name' => array(),
				'style' => array(),
			),
			'option' => array(
				'value' => array(),
				'selected' => array(),
			),
				);

		// Custom attribute handling.
		$custom_attributes = lty_format_custom_attributes($args);
		$data_nonce = ( 'products' == $args['list_type'] ) ? 'data-nonce="' . wp_create_nonce('search-products') . '"' : '';

		ob_start();
		?><select <?php echo esc_attr($multiple); ?> 
			name="<?php echo esc_attr($name); ?>" 
			id="<?php echo esc_attr($args['id']); ?>" 
			data-action="<?php echo esc_attr($args['action']); ?>" 
			data-exclude-global-variable="<?php echo esc_attr($args['exclude_global_variable']); ?>" 
			data-lty-product-id="<?php echo esc_attr($args['lty_product_id']); ?>"
			class="lty_select2_search <?php echo esc_attr($args['class']); ?>" 
			data-placeholder="<?php echo esc_attr($args['placeholder']); ?>" 
			data-error="<?php echo esc_attr($args['error']); ?>"
			data-exclude-type="<?php echo esc_attr($args['exclude_type']); ?>"
			data-exclude-out-of-stock="<?php echo esc_attr($args['exclude_out_of_stock']); ?>"
			data-include-lottery-statuses="<?php echo esc_attr( implode( ',', $args['include_lottery_statuses'] ) ); ?>"
			style ="<?php echo esc_attr($args['css']); ?>"
			<?php echo wp_kses(implode(' ', $custom_attributes), $allowed_html); ?>
			<?php echo wp_kses($data_nonce, $allowed_html); ?>
			<?php echo $args['allow_clear'] ? 'data-allow_clear="true"' : ''; ?> >
				<?php
				if (is_array($args['options'])) {
					foreach ($args['options'] as $option_id) {
						$option_value = '';
						switch ($args['list_type']) {
							case 'post':
								$option_value = get_the_title($option_id);
								break;
							case 'products':
								$option_title = get_the_title($option_id);
								if ($option_title) {
									$option_value = $option_title . ' (#' . absint($option_id) . ')';
								}
								break;
							case 'customers':
								$user = get_user_by('id', $option_id);
								if ($user) {
									$option_value = $user->display_name . '(#' . absint($user->ID) . ' &ndash; ' . $user->user_email . ')';
								}
								break;
						}

						if ($option_value) {
							?>
						<option value="<?php echo esc_attr($option_id); ?>" <?php echo $args['selected'] ? selected(true, true, false) : ''; // WPCS: XSS ok. ?>><?php echo esc_html($option_value); ?></option>
							<?php
						}
					}
				}
				?>
		</select>
		<?php
		$html = ob_get_clean();

		if ($echo) {
			echo wp_kses($html, $allowed_html);
		}

		return $html;
	}

}

if (!function_exists('lty_format_custom_attributes')) {

	/**
	 * Format Custom Attributes.
	 *
	 * @return array
	 * */
	function lty_format_custom_attributes( $value ) {
		$custom_attributes = array();

		if (!empty($value['custom_attributes']) && is_array($value['custom_attributes'])) {
			foreach ($value['custom_attributes'] as $attribute => $attribute_value) {
				$custom_attributes[] = esc_attr($attribute) . '=' . esc_attr($attribute_value) . '';
			}
		}

		return $custom_attributes;
	}

}

if (!function_exists('lty_get_datepicker_html')) {

	/**
	 * Return or display Datepicker/DateTimepicker HTML.
	 *
	 * @return string
	 * */
	function lty_get_datepicker_html( $args, $echo = true ) {
		$args = wp_parse_args(
				$args, array(
			'class' => '',
			'id' => '',
			'name' => '',
			'placeholder' => '',
			'custom_attributes' => array(),
			'value' => '',
			'wp_zone' => true,
			'with_time' => false,
			'error' => '',
				)
				);

		$name = ( '' !== $args['name'] ) ? $args['name'] : $args['id'];

		$allowed_html = array(
			'input' => array(
				'id' => array(),
				'type' => array(),
				'placeholder' => array(),
				'class' => array(),
				'value' => array(),
				'name' => array(),
				'min' => array(),
				'max' => array(),
				'data-error' => array(),
				'style' => array(),
			),
				);

		$class_name = ( $args['with_time'] ) ? 'lty_datetimepicker ' : 'lty_datepicker ';
		$format = ( $args['with_time'] ) ? 'Y-m-d H:i' : 'date';

		// Custom attribute handling.
		$custom_attributes = lty_format_custom_attributes($args);
		$value = !empty($args['value']) ? LTY_Date_Time::get_wp_format_datetime($args['value'], $format, $args['wp_zone']) : '';
		ob_start();
		?>
		<input type = "text" 
			   id="<?php echo esc_attr($args['id']); ?>"
			   value = "<?php echo esc_attr($value); ?>"
			   class="lty-disabled <?php echo esc_attr($class_name . $args['class']); ?>" 
			   placeholder="<?php echo esc_attr($args['placeholder']); ?>" 
			   data-error="<?php echo esc_attr($args['error']); ?>" 
			   <?php echo wp_kses(implode(' ', $custom_attributes), $allowed_html); ?>
			   />

		<input type = "hidden" 
			   class="lty_alter_datepicker_value" 
			   name="<?php echo esc_attr($name); ?>"
			   value = "<?php echo esc_attr($args['value']); ?>"
			   /> 
		<?php
		$html = ob_get_clean();

		if ($echo) {
			echo wp_kses($html, $allowed_html);
		}

		return $html;
	}

}

if (!function_exists('lty_display_status')) {

	/**
	 * Display formatted status.
	 *
	 * @return string
	 */
	function lty_display_status( $status, $html = true ) {

		$statuses = lty_get_lottery_statuses();

		$status_label = false;
		if (array_key_exists($status, $statuses)) {
			$status_label = $statuses[$status];
		} else {
			$status_object = get_post_status_object($status);
			if (isset($status_object)) {
				$status_label = $status_object->label;
			}
		}

		if (!$status_label) {
			return '';
		}

		return $html ? '<mark class="lty_status_label ' . esc_attr($status) . '_status"><span >' . esc_html($status_label) . '</span></mark>' : esc_html($status_label);
	}

}

if (!function_exists('lty_display_failed_reason')) {

	/**
	 * Display formatted status
	 *
	 * @return string
	 */
	function lty_display_failed_reason( $reason, $html = true ) {

		$reasons = lty_get_failed_reasons();

		if (!array_key_exists($reason, $reasons)) {
			return '';
		}

		return $html ? '<mark class="lty_lottery_reason_label ' . esc_attr( $reason ) . '_status"><span >' . wp_kses_post( $reasons[ $reason ] ) . '</span></mark>' : wp_kses_post( $reasons[ $reason ] );
	}

}

if (!function_exists('lty_price')) {

	/**
	 *  Display Price based wc_price function
	 *
	 *  @return string
	 */
	function lty_price( $price, $echo = false ) {

		$allowed_html = array(
			'span' => array(
				'class' => array(),
			),
				);

		if ($echo) {
			echo wp_kses(wc_price($price), $allowed_html);
		}

		return wc_price($price);
	}

}

if (!function_exists('lty_lottery_relative_date_selector')) {

	/**
	 * Output a relative date selector.
	 *
	 * @since 7.5.0
	 * @param array $field
	 *
	 * @return void
	 */
	function lty_lottery_relative_date_selector( $field ) {
		global $thepostid, $post;

		$thepostid = empty($thepostid) ? $post->ID : $thepostid;
		$field['placeholder'] = isset($field['placeholder']) ? $field['placeholder'] : '';
		$field['class'] = isset($field['class']) ? $field['class'] : 'short';
		$field['wrapper_class'] = isset($field['wrapper_class']) ? $field['wrapper_class'] : '';
		$field['value'] = isset($field['value']) ? $field['value'] : get_post_meta($thepostid, $field['id'], true);
		$field['name'] = isset($field['name']) ? $field['name'] : $field['id'];
		$field['desc_tip'] = isset($field['desc_tip']) ? $field['desc_tip'] : false;
		$field['option_type'] = isset($field['option_type']) ? $field['option_type'] : 'full';

		$field['options'] = lty_relative_date_picker_options($field['option_type']);

		$field['value'] = wp_parse_args(
				(array) $field['value'], array(
			'number' => '',
			'unit' => reset($field['options']),
				)
		);
		// Custom attribute handling
		$custom_attributes = array();

		if (!empty($field['custom_attributes']) && is_array($field['custom_attributes'])) {
			foreach ($field['custom_attributes'] as $attribute => $value) {
				$custom_attributes[] = esc_attr($attribute) . '="' . esc_attr($value) . '"';
			}
		}

		echo '<p class="form-field ' . esc_attr($field['id']) . '_field ' . esc_attr($field['wrapper_class']) . '">
		<label for="' . esc_attr($field['id']) . '">' . wp_kses_post($field['label']) . '</label>';

		if (!empty($field['description']) && false !== $field['desc_tip']) {
			echo wc_help_tip($field['description']);
		}

		echo '<input type="number" class="' . esc_attr($field['class']) . '" name="' . esc_attr($field['name']) . '[number]" id="' . esc_attr($field['id']) . '" value="' . esc_attr($field['value']['number']) . '" placeholder="' . esc_attr($field['placeholder']) . '" ' . wp_kses_post(implode(' ', $custom_attributes)) . ' /> ';

		echo '<select name="' . esc_attr($field['name']) . '[unit]">';
		foreach ($field['options'] as $key => $value) {
			echo '<option value="' . esc_attr($key) . '"' . selected($key, $field['value']['unit']) . '>' . esc_html($value) . '</option>';
		}
		echo '</select>';

		if (!empty($field['description']) && false === $field['desc_tip']) {
			echo '<span class="description">' . wp_kses_post($field['description']) . '</span>';
		}

		echo '</p>';
	}

}
