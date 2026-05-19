<?php
/**
 * Admin Settings Class.
 * */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'LTY_Settings' ) ) {

	/**
	 * LTY_Settings Class.
	 * */
	class LTY_Settings {

		/**
		 * Setting pages.
		 *
		 * @var array
		 * */
		private static $settings = array();

		/**
		 * Error messages.
		 *
		 * @var array
		 * */
		private static $errors = array();

		/**
		 * Plugin slug.
		 *
		 * @var string
		 * */
		private static $plugin_slug = 'lty';

		/**
		 * Update messages.
		 *
		 * @var array
		 * */
		private static $messages = array();

		/**
		 * Include the settings page classes.
		 * */
		public static function get_settings_pages() {
			if ( ! empty( self::$settings ) ) {
				return self::$settings;
			}

			include_once LTY_PLUGIN_PATH . '/inc/abstracts/abstract-lty-settings-page.php';

			$settings = array();
			$tabs     = self::settings_page_tabs();

			foreach ( $tabs as $tab_name ) {
				$settings[ sanitize_key( $tab_name ) ] = include 'tabs/' . sanitize_key( $tab_name ) . '.php';
			}
			/**
			 * This hook is used to alter the settings pages.
			 *
			 * @since 1.0
			 */
			self::$settings = apply_filters( sanitize_key( self::$plugin_slug . '_get_settings_pages' ), $settings );

			return self::$settings;
		}

		/**
		 * Add a message.
		 * */
		public static function add_message( $text ) {
			self::$messages[] = $text;
		}

		/**
		 * Add an error.
		 * */
		public static function add_error( $text ) {
			self::$errors[] = $text;
		}

		/**
		 * Output messages + errors.
		 * */
		public static function show_messages() {
			if ( count( self::$errors ) > 0 ) {
				foreach ( self::$errors as $error ) {
					self::error_message( $error );
				}
			} elseif ( count( self::$messages ) > 0 ) {
				foreach ( self::$messages as $message ) {
					self::success_message( $message );
				}
			}
		}

		/**
		 * Show an success message.
		 * */
		public static function success_message( $text, $echo = true ) {
			ob_start();
			$contents = '<div id="message " class="updated inline ' . esc_html( self::$plugin_slug ) . '_save_msg"><p><strong>' . esc_html( $text ) . '</strong></p></div>';
			ob_end_clean();

			if ( $echo ) {
				echo wp_kses_post( $contents );
			} else {
				return $contents;
			}
		}

		/**
		 * Show an error message.
		 * */
		public static function error_message( $text, $echo = true ) {
			ob_start();
			$contents = '<div id="message" class="error inline"><p><strong>' . $text . '</strong></p></div>';
			ob_end_clean();

			if ( $echo ) {
				echo wp_kses_post( $contents );
			} else {
				return $contents;
			}
		}

		/**
		 * Settings page tabs.
		 * */
		public static function settings_page_tabs() {
			return array(
				'general',
				'advanced',
				'shortcodes',
				'notifications',
				'localizations',
				'messages',
			);
		}

		/**
		 * Handles the display of the settings page in admin.
		 * */
		public static function output() {
			global $current_section, $current_tab;

			$tabs = lty_get_allowed_setting_tabs();

			/* Include admin html settings. */
			include_once 'views/html-settings.php';
		}

		/**
		 * Handles the display of the settings page buttons in page.
		 * */
		public static function output_buttons( $reset = true ) {

			/* Include admin html settings buttons. */
			include_once 'views/html-settings-buttons.php';
		}

		/**
		 * Output admin fields.
		 * */
		public static function output_fields( $value ) {

			if ( ! isset( $value['type'] ) || 'lty_custom_fields' != $value['type'] ) {
				return;
			}

			$value['id']                = isset( $value['id'] ) ? $value['id'] : '';
			$value['css']               = isset( $value['css'] ) ? $value['css'] : '';
			$value['desc']              = isset( $value['desc'] ) ? $value['desc'] : '';
			$value['title']             = isset( $value['title'] ) ? $value['title'] : '';
			$value['class']             = isset( $value['class'] ) ? $value['class'] : '';
			$value['default']           = isset( $value['default'] ) ? $value['default'] : '';
			$value['name']              = isset( $value['name'] ) ? $value['name'] : $value['id'];
			$value['placeholder']       = isset( $value['placeholder'] ) ? $value['placeholder'] : '';
			$value['without_label']     = isset( $value['without_label'] ) ? $value['without_label'] : false;
			$value['custom_attributes'] = isset( $value['custom_attributes'] ) ? $value['custom_attributes'] : '';

			// Custom attribute handling.
			$custom_attributes = lty_format_custom_attributes( $value );

			// Description handling.
			$field_description = WC_Admin_Settings::get_field_description( $value );
			$description       = $field_description['description'];
			$tooltip_html      = $field_description['tooltip_html'];

			// Switch based on type.
			switch ( $value['lty_field'] ) {

				case 'subtitle':
					?>
					<tr valign="top" >
						<th scope="row" colspan="2">
							<?php echo esc_html( $value['title'] ); ?><?php echo wp_kses_post( $tooltip_html ); ?>
							<p><?php echo wp_kses_post( $description ); ?></p>
						</th>
					</tr>
					<?php
					break;

				case 'button':
					?>
					<tr valign="top">
						<?php if ( ! $value['without_label'] ) : ?>
							<th scope="row">
								<label for="<?php echo esc_attr( $value['id'] ); ?>"><?php echo esc_html( $value['title'] ); ?></label><?php echo wp_kses_post( $tooltip_html ); ?>
							</th>
						<?php endif; ?>
						<td>
							<button
								id="<?php echo esc_attr( $value['id'] ); ?>"
								type="<?php echo esc_attr( $value['lty_field'] ); ?>"
								class="<?php echo esc_attr( $value['class'] ); ?>"
								<?php echo wp_kses_post( implode( ' ', $custom_attributes ) ); ?>
								><?php echo esc_html( $value['default'] ); ?> </button>
								<?php echo wp_kses_post( $description ); ?>
						</td>
					</tr>
					<?php
					break;

				case 'ajaxmultiselect':
					$option_value = get_option( $value['id'], $value['default'] );
					?>
					<tr valign="top">
						<th scope="row">
							<label for="<?php echo esc_attr( $value['id'] ); ?>"><?php echo esc_html( $value['title'] ); ?></label><?php echo wp_kses_post( $tooltip_html ); ?>
						</th>
						<td>
							<?php
							$value['options'] = $option_value;
							lty_select2_html( $value );
							echo wp_kses_post( $description );
							?>
						</td>
					</tr>
					<?php
					break;

				case 'datepicker':
					$value['value'] = get_option( $value['id'], $value['default'] );
					if ( ! isset( $value['datepickergroup'] ) || 'start' == $value['datepickergroup'] ) {
						?>
						<tr valign="top">
							<th scope="row">
								<label for="<?php echo esc_attr( $value['id'] ); ?>"><?php echo esc_html( $value['title'] ); ?></label><?php echo wp_kses_post( $tooltip_html ); ?>
							</th>
							<td>
								<fieldset>
									<?php
					}
								echo isset( $value['label'] ) ? esc_html( $value['label'] ) : '';
								lty_get_datepicker_html( $value );
								echo wp_kses_post( $description );

					if ( ! isset( $value['datepickergroup'] ) || 'end' == $value['datepickergroup'] ) {
						?>
								</fieldset>
							</td>
						</tr>
					<?php } ?>
					<?php
					break;

				case 'wpeditor':
					$option_value = get_option( $value['id'], $value['default'] );
					?>
					<tr valign="top">
						<th scope="row">
							<label for="<?php echo esc_attr( $value['id'] ); ?>"><?php echo esc_html( $value['title'] ); ?></label><?php echo wp_kses_post( $tooltip_html ); ?>
						</th>
						<td>
							<?php
							wp_editor(
								$option_value,
								$value['id'],
								array(
									'media_buttons' => false,
									'editor_class'  => esc_attr( $value['class'] ),
								)
							);

							echo wp_kses_post( $description );
							?>
						</td>
					</tr>
					<?php
					break;

				// Days/months/years selector.
				case 'relative_date_selector':
					$option_value = get_option( $value['id'], $value['default'] );
					$periods      = lty_relative_date_picker_options( $value['option_type'] );
					$option_value = lty_parse_relative_date_option( $option_value, $value['option_type'] );
					?>
					<tr valign="top">
						<th scope="row" class="titledesc">
							<label for="<?php echo esc_attr( $value['id'] ); ?>"><?php echo esc_html( $value['title'] ); ?> <?php echo wp_kses_post( $tooltip_html ); // WPCS: XSS ok. ?></label>
						</th>
						<td class="forminp">
							<input
								name="<?php echo esc_attr( $value['id'] ); ?>[number]"
								id="<?php echo esc_attr( $value['id'] ); ?>"
								type="number"
								value="<?php echo esc_attr( $option_value['number'] ); ?>"
								class="<?php echo esc_attr( $value['class'] ); ?>"
								placeholder="<?php echo esc_attr( $value['placeholder'] ); ?>"
								step="1"
								min="1"
								<?php echo wp_kses_post( implode( ' ', $custom_attributes ) ); // WPCS: XSS ok. ?>
								/>&nbsp;
							<select name="<?php echo esc_attr( $value['id'] ); ?>[unit]">
								<?php
								foreach ( $periods as $value => $label ) {
									echo '<option value="' . esc_attr( $value ) . '"' . selected( $option_value['unit'], $value, false ) . '>' . esc_html( $label ) . '</option>';
								}
								?>
							</select> <?php echo wp_kses_post( $description ); // WPCS: XSS ok. ?>
						</td>
					</tr>
					<?php
					break;

				case 'file_upload':
					$option_value = get_option( $value['id'], $value['default'] );
					?>
					<tr valign = "top">
						<?php if ( ! $value['without_label'] ) : ?>
							<th scope="row">
								<label for="<?php echo esc_attr( $value['id'] ); ?>"><?php echo esc_html( $value['title'] ); ?></label>
							</th>
						<?php endif; ?>

						<td class="forminp formin-text">

							<input id="<?php echo esc_attr( $value['id'] ); ?>"
									class ="<?php echo esc_attr( $value['id'] ); ?>"
									type="text"
									name="<?php echo esc_attr( $value['id'] ); ?>"
									value = "<?php echo esc_url( $option_value ); ?>">

							<button id="<?php echo esc_attr( $value['id'] ); ?>_button"
									class ="button <?php echo esc_attr( $value['id'] ); ?>_button"
									type="button"
									name="<?php echo esc_attr( $value['id'] ); ?>_button">
										<?php esc_html_e( 'Choose Image', 'lottery-for-woocommerce' ); ?>
							</button>

							<div id = "<?php echo esc_attr( $value['id'] ); ?>_preview" class="lty_lottery_uploaded_image_preview">
								<img src="<?php echo esc_url( $option_value ); ?>" />
							</div>
						</td>
					</tr>
					<?php
					break;
				case 'image_upload':
					$image_url                 = get_option( $value['id'] ) && ! empty( get_option( $value['id'] ) ) ? wp_get_attachment_url( get_option( $value['id'] ) ) : '';
					$class                     = isset( $value['class'] ) ? $value['class'] : '';
					$add_image_button_label    = isset( $value['add-image-button-label'] ) ? $value['add-image-button-label'] : __( 'Choose Image', 'lottery-for-woocommerce' );
					$remove_image_button_label = isset( $value['remove-image-button-label'] ) ? $value['remove-image-button-label'] : __( 'Remove', 'lottery-for-woocommerce' );
					?>
					<tr valign='top'>
						<?php if ( ! $value['without_label'] ) : ?>
							<th scope='row'>
								<label for="<?php echo esc_attr( $value['id'] ); ?>"><?php echo esc_html( $value['title'] ); ?></label>
							</th>
						<?php endif; ?>

						<td class='forminp formin-text'>
							<img src="<?php echo esc_url( $image_url ); ?>" style='max-width: 90px; height: auto; padding: 5px'/>
							<p>
								<input type='hidden'
										class="lty-selected-image-id <?php echo esc_attr( $class ); ?>"
										id="<?php echo esc_attr( $value['id'] ); ?>"
										name="<?php echo esc_attr( $value['name'] ); ?>"
										value="<?php echo esc_attr( get_option( $value['id'] ) ); ?>" />
								<input type='button' class='lty-select-image button' value="<?php echo esc_attr( $add_image_button_label ); ?>" />
								<input type='button' class='lty-remove-image button' value="<?php echo esc_attr( $remove_image_button_label ); ?>" style="<?php echo empty( get_option( $value['id'] ) ) ? 'display: none;' : ''; ?>" />
							</p>
						</td>
					</tr>
					<?php
					break;
				case 'image_size':
					$image_sizes = get_option( $value['id'], $value['default'] );
					$sizes       = lty_parse_relative_image_size_option( $image_sizes );
					$class       = isset( $value['class'] ) ? $value['class'] : '';
					?>
					<tr valign="top">
						<?php if ( ! $value['without_label'] ) : ?>
							<th scope="row">
								<label for="<?php echo esc_attr( $value['id'] ); ?>"><?php echo esc_html( $value['title'] ); ?></label>
							</th>
						<?php endif; ?>

						<td class="forminp formin-text">
							<input type="number"
								class="lty-image-width <?php echo esc_attr( $class ); ?>"
								id="<?php echo esc_attr( $value['id'] ); ?>"
								name="<?php echo esc_attr( $value['name'] ); ?>[width]"
								value="<?php echo esc_attr( $sizes['width'] ); ?>"
								<?php echo wp_kses_post( implode( ' ', $custom_attributes ) ); ?> />
							
							<span>X</span>

							<input type="number"
								class="lty-image-height"
								id="<?php echo esc_attr( $value['id'] ); ?>"
								name="<?php echo esc_attr( $value['name'] ); ?>[height]"
								value="<?php echo esc_attr( $sizes['height'] ); ?>"
								<?php echo wp_kses_post( implode( ' ', $custom_attributes ) ); ?> />
						</td>
					</tr>
					<?php
					break;
			}
		}

		/**
		 * Save admin fields.
		 * */
		public static function save_fields( $value, $option, $raw_value ) {

			if ( ! isset( $option['type'] ) || 'lty_custom_fields' != $option['type'] ) {
				return $value;
			}

			// Format the value based on option type.
			switch ( $option['lty_field'] ) {
				case 'ajaxmultiselect':
					$value = array_filter( (array) $raw_value );
					break;
				case 'relative_date_selector':
					$value = lty_parse_relative_date_option( $raw_value, $option['option_type'] );
					break;
				case 'wpeditor':
					$value = $raw_value;
					break;
				default:
					$value = wc_clean( $raw_value );
					break;
			}

			return $value;
		}

		/**
		 * Reset admin fields.
		 * */
		public static function reset_fields( $options ) {
			if ( ! is_array( $options ) ) {
				return false;
			}

			// Loop options and get values to reset.
			foreach ( $options as $option ) {
				if ( ! isset( $option['id'] ) || ! isset( $option['type'] ) || ! isset( $option['default'] ) ) {
					continue;
				}

				update_option( $option['id'], $option['default'] );
			}
			return true;
		}
	}

}
