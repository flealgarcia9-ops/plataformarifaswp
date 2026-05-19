<?php
/**
 * Product panel - relist.
 * 
 * @since 7.5.0
 * */
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}
?>
<div id='lty_lottery_relist_tab' class='panel woocommerce_options_panel'>
	<div class='<?php echo esc_attr($wrapper_class_name); ?>'>		
		<div class='options_group lty-finished-lottery-relist'>
			<h4><?php esc_html_e('Finished Giveaway Relisting', 'lottery-for-woocommerce'); ?></h4>
			<?php
			woocommerce_wp_checkbox(
					array(
						'id' => '_lty_relist_finished_lottery',
						'class' => 'lty-relist-finished-lottery-automatic',
						'value' => is_callable(array( $product_object, 'get_lty_relist_finished_lottery' )) ? $product_object->get_lty_relist_finished_lottery('edit') : '',
						'label' => __('Enable Finished Giveaway Relisting', 'lottery-for-woocommerce'),
					)
			);
			lty_lottery_relative_date_selector(
					array(
						'id' => '_lty_finished_lottery_relist_duration',
						'option_type' => '1',
						'value' => is_callable(array( $product_object, 'get_lty_finished_lottery_relist_duration' )) ? $product_object->get_lty_finished_lottery_relist_duration('edit') : '',
						'class' => 'lty-relist-finished-lottery lty-lottery-relative-date-selector',
						'label' => __('Set Relist Duration', 'lottery-for-woocommerce') . "<span class='required'>*</span>",
						'type' => 'number',
						'custom_attributes' => array(
							'step' => 'any',
							'min' => '1',
							'data-error' => __('Relist duration cannot be empty.', 'lottery-for-woocommerce'),
						),
					)
			);
			woocommerce_wp_checkbox(
					array(
						'id' => '_lty_finished_lottery_relist_pause',
						'value' => is_callable(array( $product_object, 'get_lty_finished_lottery_relist_pause' )) ? $product_object->get_lty_finished_lottery_relist_pause('edit') : '',
						'class' => 'lty-relist-finished-lottery',
						'label' => __('Enable Pause Time to Relist', 'lottery-for-woocommerce'),
					)
			);
			lty_lottery_relative_date_selector(
					array(
						'option_type' => '1',
						'id' => '_lty_finished_lottery_relist_pause_duration',
						'value' => is_callable(array( $product_object, 'get_lty_finished_lottery_relist_pause_duration' )) ? $product_object->get_lty_finished_lottery_relist_pause_duration('edit') : '',
						'class' => 'lty-relist-finished-lottery lty-finished-lottery-relist-pause-duration lty-lottery-relative-date-selector',
						'label' => __('Set Pause Time to Relist', 'lottery-for-woocommerce') . "<span class='required'>*</span>",
						'type' => 'number',
						'custom_attributes' => array(
							'step' => 'any',
							'min' => '1',
							'data-error' => __('Giveaway Finished Relist Delay Duration cannot be empty.', 'lottery-for-woocommerce'),
						),
					)
			);
			woocommerce_wp_select(
					array(
						'id' => '_lty_finished_lottery_relist_count_type',
						'class' => 'lty-relist-finished-lottery',
						'label' => __('Finished Relist Type', 'lottery-for-woocommerce'),
						'value' => is_callable(array( $product_object, 'get_lty_finished_lottery_relist_count_type' )) ? $product_object->get_lty_finished_lottery_relist_count_type('edit') : '',
						'options' => array(
							'1' => __('Unlimited', 'lottery-for-woocommerce'),
							'2' => __('Limited', 'lottery-for-woocommerce'),
						),
					)
			);
			woocommerce_wp_text_input(
					array(
						'id' => '_lty_finished_lottery_relist_count',
						'label' => __('Number of time to Relist(Only for Limited) ', 'lottery-for-woocommerce') . "<span class='required'>*</span>",
						'value' => is_callable(array( $product_object, 'get_lty_finished_lottery_relist_count' )) ? $product_object->get_lty_finished_lottery_relist_count('edit') : '',
						'class' => 'lty-relist-finished-lottery lty-finished-lottery-relist-count',
						'type' => 'number',
						'custom_attributes' => array(
							'step' => 'any',
							'min' => '1',
							'data-error' => __('Giveaway Finished Relist Count cannot be empty.', 'lottery-for-woocommerce'),
						),
						'desc_tip' => 'true',
						'description' => __('Number of time to relist .', 'lottery-for-woocommerce'),
					)
			);
			?>
		</div>

		<div class='options_group lty_failed_lottery_relist'>
			<h4><?php esc_html_e('Failed Giveaway Relist', 'lottery-for-woocommerce'); ?></h4>
			<?php
			woocommerce_wp_checkbox(
					array(
						'id' => '_lty_relist_failed_lottery',
						'class' => 'lty-relist-failed-lottery-automatic',
						'value' => is_callable(array( $product_object, 'get_lty_relist_failed_lottery' )) ? $product_object->get_lty_relist_failed_lottery('edit') : '',
						'label' => __('Enable Relist For Failed Giveaway', 'lottery-for-woocommerce'),
					)
			);
			lty_lottery_relative_date_selector(
					array(
						'option_type' => '1',
						'id' => '_lty_failed_lottery_relist_duration',
						'value' => is_callable(array( $product_object, 'get_lty_failed_lottery_relist_duration' )) ? $product_object->get_lty_failed_lottery_relist_duration('edit') : '',
						'class' => 'lty-relist-failed-lottery lty-lottery-relative-date-selector',
						'label' => __('Set Relist Duration', 'lottery-for-woocommerce') . "<span class='required'>*</span>",
						'type' => 'number',
						'custom_attributes' => array(
							'step' => 'any',
							'min' => '1',
							'data-error' => __('Giveaway Failed Relist Duration cannot be empty.', 'lottery-for-woocommerce'),
						),
					)
			);
			woocommerce_wp_checkbox(
					array(
						'id' => '_lty_failed_lottery_relist_pause',
						'value' => is_callable(array( $product_object, 'get_lty_failed_lottery_relist_pause' )) ? $product_object->get_lty_failed_lottery_relist_pause('edit') : '',
						'class' => 'lty-relist-failed-lottery',
						'label' => __('Enable Pause Time to Relist', 'lottery-for-woocommerce'),
					)
			);
			lty_lottery_relative_date_selector(
					array(
						'option_type' => '1',
						'id' => '_lty_failed_lottery_relist_pause_duration',
						'value' => is_callable(array( $product_object, 'get_lty_failed_lottery_relist_pause_duration' )) ? $product_object->get_lty_failed_lottery_relist_pause_duration('edit') : '',
						'class' => 'lty-relist-failed-lottery lty-failed-lottery-relist-pause-duration lty-lottery-relative-date-selector',
						'label' => __('Set Pause Time to Relist', 'lottery-for-woocommerce') . "<span class='required'>*</span>",
						'type' => 'number',
						'custom_attributes' => array(
							'step' => 'any',
							'min' => '1',
							'data-error' => __('Giveaway Failed Relist Duration cannot be empty.', 'lottery-for-woocommerce'),
						),
					)
			);
			woocommerce_wp_select(
					array(
						'id' => '_lty_failed_lottery_relist_count_type',
						'class' => 'lty-relist-failed-lottery',
						'label' => __('Failed Relist Type', 'lottery-for-woocommerce'),
						'value' => is_callable(array( $product_object, 'get_lty_failed_lottery_relist_count_type' )) ? $product_object->get_lty_failed_lottery_relist_count_type('edit') : '',
						'options' => array(
							'1' => __('Unlimited', 'lottery-for-woocommerce'),
							'2' => __('Limited', 'lottery-for-woocommerce'),
						),
					)
			);
			woocommerce_wp_text_input(
					array(
						'id' => '_lty_failed_lottery_relist_count',
						'label' => __('Number of time to Relist(Only for Limited) ', 'lottery-for-woocommerce') . "<span class='required'>*</span>",
						'value' => is_callable(array( $product_object, 'get_lty_failed_lottery_relist_count' )) ? $product_object->get_lty_failed_lottery_relist_count('edit') : '',
						'class' => 'lty-relist-failed-lottery lty-failed-lottery-relist-count',
						'type' => 'number',
						'custom_attributes' => array(
							'step' => 'any',
							'min' => '1',
							'data-error' => __('Giveaway Failed Relist Limited Count cannot be empty.', 'lottery-for-woocommerce'),
						),
						'desc_tip' => 'true',
						'description' => __('Number of time to relist .', 'lottery-for-woocommerce'),
					)
			);

			/**
			 * The hook is used to display lottery relist product data.
			 * 
			 * @since 7.5.0
			 */
			do_action('woocommerce_product_options_lottery_relist_product_data');
			?>
		</div>
	</div>
</div>
<?php
