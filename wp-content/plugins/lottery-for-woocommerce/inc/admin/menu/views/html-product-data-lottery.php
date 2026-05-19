<?php
/**
 * Product Lottery data panel.
 * */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>
<div id='lty_lottery_tab' class='panel woocommerce_options_panel'>
	<?php if ( lty_is_lottery_product( $product_object ) && $product_object->is_closed() && ( $product_object->has_lottery_status( 'lty_lottery_failed' ) || $product_object->has_lottery_status( 'lty_lottery_closed' ) ) ) : ?>
		<p class='form-field lty-lottery-extend-button-field'>
			<input type='button' class='button lty-lottery-extend' value="<?php esc_attr_e( 'Extend', 'lottery-for-woocommerce' ); ?>"/>
			<span class='lty-lottery-extend-msg'></span>
			<input type='hidden' name='lty_lottery_extend' value='1'>
		</p>
		<?php
	endif;

	if ( lty_is_lottery_product( $product_object ) && $product_object->is_closed() && $product_object->has_lottery_status( array( 'lty_lottery_failed', 'lty_lottery_finished' ) )) :
		?>
		<p class='form-field lty-lottery-relist-button-field'>
			<input type='button' class='button lty-lottery-manual-relist' value="<?php esc_attr_e( 'Relist Giveaway Manually', 'lottery-for-woocommerce' ); ?>"/>
			<span class='lty-lottery-manual-relist-msg'></span>
			<input type='hidden' name='lty_lottery_manual_relist' value='1'>
		</p>
	<?php endif; ?>
	<input type='hidden' class='lty-lottery-status' value="<?php echo esc_attr( is_callable( array( $product_object, 'get_lty_lottery_status' ) ) ? $product_object->get_lty_lottery_status() : '' ); ?>" />
	<div class="<?php echo esc_attr( $wrapper_class_name ); ?> options_group show_if_lottery">
		<div class="options_group show_if_lottery">
			<h4><?php esc_html_e( 'General Settings', 'lottery-for-woocommerce' ); ?></h4>
			<?php
			woocommerce_wp_select(
				array(
					'id'          => 'lty_lottery_schedule_type',
					'label'       => __( 'Giveaway Schedule Type', 'lottery-for-woocommerce' ),
					'value'       => is_callable( array( $product_object, 'get_lty_lottery_schedule_type' ) ) ? $product_object->get_lty_lottery_schedule_type( 'edit' ) : '1',
					'options'     => array(
						'1' => __( 'Limited Duration', 'lottery-for-woocommerce' ),
						'2' => __( 'Unlimited Duration', 'lottery-for-woocommerce' ),
					),
					'default'     => '1',
					'description' => __( '<b>Limited Duration</b>: You must set a Start Date and End Date for the giveaway.<br/><b>Unlimited Duration</b>: You can run the giveaway for an unlimited duration(without end date). You can manually end it by clicking the "End Now" button from the giveaway post table(Giveaway -> Giveaway -> Ongoing giveaway -> End Now button) or enable the "Close Giveaway when All the Tickets have been Sold" option(Giveaway -> Settings -> General) to automatically end the giveaway once all tickets are sold.', 'lottery-for-woocommerce' ),
					'desc_tip'    => true,
				)
			);
			?>
			<p class='form-field lty-lottery-dates-field'>
				<label for="_lty_start_date"><?php esc_html_e( 'Start Date', 'lottery-for-woocommerce' ); ?><span class='required'>*</span></label>
				<?php
				lty_get_datepicker_html(
					array(
						'id'          => '_lty_start_date',
						'with_time'   => true,
						'wp_zone'     => false,
						'value'       => is_callable( array( $product_object, 'get_lty_start_date' ) ) ? $product_object->get_lty_start_date( 'edit' ) : '',
						'placeholder' => LTY_Date_Time::get_wp_datetime_format(),
						'error'       => __( 'Start date cannot be empty.', 'lottery-for-woocommerce' ),
					)
				);
				echo wp_kses_post( wc_help_tip( __( 'The date from which you want to start the giveaway.', 'lottery-for-woocommerce' ) ) );
				?>
			</p>
			<p class='form-field lty-lottery-dates-field'>
				<label for="_lty_end_date"><?php esc_html_e( 'End Date', 'lottery-for-woocommerce' ); ?><span class='required'>*</span></label>
				<?php
				lty_get_datepicker_html(
					array(
						'id'          => '_lty_end_date',
						'with_time'   => true,
						'wp_zone'     => false,
						'value'       => is_callable( array( $product_object, 'get_lty_end_date' ) ) ? $product_object->get_lty_end_date( 'edit' ) : '',
						'placeholder' => LTY_Date_Time::get_wp_datetime_format(),
						'error'       => __( 'End date cannot be empty.', 'lottery-for-woocommerce' ),
					)
				);
				echo wp_kses_post( wc_help_tip( __( 'The date from which you want to end the giveaway.', 'lottery-for-woocommerce' ) ) );
				?>
			</p>
			<?php
			woocommerce_wp_text_input(
				array(
					'id'                => '_lty_minimum_tickets',
					'label'             => __( 'Minimum Tickets', 'lottery-for-woocommerce' ) . "<span class='required'>*</span>",
					'value'             => is_callable( array( $product_object, 'get_lty_minimum_tickets' ) ) ? $product_object->get_lty_minimum_tickets( 'edit' ) : '',
					'type'              => 'number',
					'custom_attributes' => array(
						'step'       => 'any',
						'min'        => '1',
						'data-error' => __( 'Minimum ticket field is required', 'lottery-for-woocommerce' ),
					),
					'desc_tip'          => true,
					'description'       => __( 'The minimum number of tickets that has to be purchased in order to consider a giveaway as successful.', 'lottery-for-woocommerce' ),
				)
			);
			woocommerce_wp_text_input(
				array(
					'id'                => '_lty_maximum_tickets',
					'label'             => __( 'Maximum Tickets', 'lottery-for-woocommerce' ) . "<span class='required'>*</span>",
					'value'             => is_callable( array( $product_object, 'get_lty_maximum_tickets' ) ) ? $product_object->get_lty_maximum_tickets( 'edit' ) : '',
					'type'              => 'number',
					'custom_attributes' => array(
						'step'       => 'any',
						'min'        => '1',
						'data-error' => __( 'Maximum ticket field is required', 'lottery-for-woocommerce' ),
					),
					'desc_tip'          => true,
					'description'       => __( 'The maximum number of tickets that can be purchased in a giveaway.', 'lottery-for-woocommerce' ),
				)
			);
			woocommerce_wp_text_input(
				array(
					'id'                => '_lty_order_maximum_tickets',
					'label'             => __( 'Maximum Tickets per Order', 'lottery-for-woocommerce' ),
					'value'             => is_callable( array( $product_object, 'get_lty_order_maximum_tickets' ) ) ? $product_object->get_lty_order_maximum_tickets( 'edit' ) : '',
					'type'              => 'number',
					'custom_attributes' => array(
						'step' => 'any',
						'min'  => '1',
					),
					'desc_tip'          => true,
					'description'       => __( 'Set limit to purchase maximum tickets per order.', 'lottery-for-woocommerce' ),
				)
			);
			woocommerce_wp_text_input(
				array(
					'id'                => '_lty_user_minimum_tickets',
					'label'             => __( 'Minimum Tickets per User', 'lottery-for-woocommerce' ),
					'value'             => is_callable( array( $product_object, 'get_lty_user_minimum_tickets' ) ) ? $product_object->get_lty_user_minimum_tickets( 'edit' ) : '',
					'type'              => 'number',
					'custom_attributes' => array(
						'step'       => 'any',
						'min'        => '1',
						'data-error' => __( 'Minimum ticket per user field is required', 'lottery-for-woocommerce' ),
					),
					'desc_tip'          => true,
					'description'       => __( 'The minimum number of tickets that a user has to purchase to participate in the giveaway.', 'lottery-for-woocommerce' ),
				)
			);
			woocommerce_wp_text_input(
				array(
					'id'                => '_lty_user_maximum_tickets',
					'label'             => __( 'Maximum Tickets per User', 'lottery-for-woocommerce' ) . "<span class='required'>*</span>",
					'value'             => is_callable( array( $product_object, 'get_lty_user_maximum_tickets' ) ) ? $product_object->get_lty_user_maximum_tickets( 'edit' ) : '',
					'type'              => 'number',
					'custom_attributes' => array(
						'step'       => 'any',
						'min'        => '1',
						'data-error' => __( 'Maximum ticket per user field is required', 'lottery-for-woocommerce' ),
					),
					'desc_tip'          => true,
					'description'       => __( 'The maximum number of tickets that a user can purchase in the giveaway.', 'lottery-for-woocommerce' ),
				)
			);
			if ( '2' === get_option( 'lty_settings_quantity_selector_type' ) ) {
				woocommerce_wp_select(
					array(
						'id'          => '_lty_ticket_range_slider_type',
						'label'       => __( 'Display Range Slider Based On', 'lottery-for-woocommerce' ),
						'value'       => is_callable( array( $product_object, 'get_lty_ticket_range_slider_type' ) ) ? $product_object->get_lty_ticket_range_slider_type( 'edit' ) : '',
						'options'     => array(
							'2' => __( 'Maximum Tickets', 'lottery-for-woocommerce' ),
							'1' => __( 'Maximum Tickets per User', 'lottery-for-woocommerce' ),
						),
						'desc_tip'    => true,
						'default'     => '2',
						'description' => __( 'When "Maximum Tickets" option is selected, the Quantity Range Slider is displayed based on maximum tickets value. When "Maximum Tickets per User" option is selected, the Quantity Range Slider is displayed based on maximum tickets per user value.', 'lottery-for-woocommerce' ),
					)
				);
			}

			woocommerce_wp_text_input(
				array(
					'id'                => '_lty_preset_tickets',
					'label'             => __( 'Preset(Default) Quantity', 'lottery-for-woocommerce' ),
					'value'             => is_callable( array( $product_object, 'get_lty_preset_tickets' ) ) ? $product_object->get_lty_preset_tickets( 'edit' ) : '',
					'type'              => 'number',
					'class'             => 'lty-preset-tickets-fields',
					'custom_attributes' => array(
						'step' => 'any',
						'min'  => '1',
					),
					'desc_tip'          => true,
					'description'       => __( 'You can set default quantity value for "Participate Now" button or "Quantity Selector Range Slider", it is applicable only for "Automatic Ticket Generation Type.', 'lottery-for-woocommerce' ),
				)
			);
			woocommerce_wp_checkbox(
				array(
					'id'          => '_lty_lottery_unique_winners',
					'label'       => __( 'Enable Unique Winner(s)', 'lottery-for-woocommerce' ),
					'value'       => is_callable( array( $product_object, 'get_lty_lottery_unique_winners' ) ) ? $product_object->get_lty_lottery_unique_winners( 'edit' ) : '',
					'default'     => 'no',
					'description' => __( 'When enabled, winners are assigned uniquely and same user cannot win multiple prizes. Note: Participants count must be equal or greater than Number of Winners count. If participants count is lesser than the number of winners count, then giveaway will fail without generating the winners.', 'lottery-for-woocommerce' ),
					'desc_tip'    => true,
				)
			);
			woocommerce_wp_text_input(
				array(
					'id'                => '_lty_winners_count',
					'label'             => __( 'Number of Winners', 'lottery-for-woocommerce' ) . "<span class='required'>*</span>",
					'value'             => is_callable( array( $product_object, 'get_lty_winners_count' ) ) ? $product_object->get_lty_winners_count( 'edit' ) : '',
					'type'              => 'number',
					'custom_attributes' => array(
						'step'       => 'any',
						'min'        => '1',
						'data-error' => __( 'Winner count field is required', 'lottery-for-woocommerce' ),
					),
					'desc_tip'          => true,
					'description'       => __( 'The maximum Number of Winners for this giveaway.', 'lottery-for-woocommerce' ),
				)
			);
			woocommerce_wp_select(
				array(
					'id'          => '_lty_ticket_price_type',
					'label'       => __( 'Ticket Price Type', 'lottery-for-woocommerce' ),
					'value'       => is_callable( array( $product_object, 'get_lty_ticket_price_type' ) ) ? $product_object->get_lty_ticket_price_type( 'edit' ) : '',
					'options'     => array(
						'1' => __( 'Price', 'lottery-for-woocommerce' ),
						'2' => __( 'Free', 'lottery-for-woocommerce' ),
					),
					'desc_tip'    => true,
					'description' => __( "<b>Price:</b> User has to pay money to participate in the giveaway. <b>Free:</b> User doesn't have to pay any money to participate in the giveaway.", 'lottery-for-woocommerce' ),
				)
			);
			woocommerce_wp_text_input(
				array(
					'id'                => '_lty_regular_price',
					'label'             => __( 'Regular Price', 'lottery-for-woocommerce' ) . "<span class='required'>*</span>",
					'value'             => is_callable( array( $product_object, 'get_lty_regular_price' ) ) ? $product_object->get_lty_regular_price( 'edit' ) : '',
					'data_type'         => 'price',
					'type'              => 'text',
					'custom_attributes' => array(
						'data-error' => __( 'Regular price cannot be empty', 'lottery-for-woocommerce' ),
					),
					'desc_tip'          => true,
					'description'       => __( 'Regular Price of a giveaway ticket.', 'lottery-for-woocommerce' ),
				)
			);
			woocommerce_wp_text_input(
				array(
					'id'          => '_lty_sale_price',
					'label'       => __( 'Sale Price', 'lottery-for-woocommerce' ),
					'value'       => is_callable( array( $product_object, 'get_lty_sale_price' ) ) ? $product_object->get_lty_sale_price( 'edit' ) : '',
					'data_type'   => 'price',
					'type'        => 'text',
					'desc_tip'    => true,
					'description' => __( 'Sale Price of a giveaway ticket.', 'lottery-for-woocommerce' ),
				)
			);
			?>
		</div>
		<div class="options_group show_if_lottery">
			<h4><?php esc_html_e( 'Ticket Generation Settings', 'lottery-for-woocommerce' ); ?></h4>
			<?php
			$is_manual_selection    = is_callable( array( $product_object, 'get_lty_choose_ticket_numbers' ) ) ? $product_object->get_lty_choose_ticket_numbers() : '';
			$ticket_generation_type = is_callable( array( $product_object, 'get_lty_ticket_generation_type' ) ) ? $product_object->get_lty_ticket_generation_type() : '';
			$default_type           = '' == $ticket_generation_type ? ( ( 'yes' == $is_manual_selection ) ? '2' : '1' ) : $ticket_generation_type;
			woocommerce_wp_select(
				array(
					'id'      => '_lty_ticket_generation_type',
					'class'   => '_lty_ticket_generation_type',
					'label'   => __( 'Ticket Generation Type', 'lottery-for-woocommerce' ),
					'value'   => $default_type,
					'options' => array(
						'1' => __( 'Automatic', 'lottery-for-woocommerce' ),
						'2' => __( 'User Chooses the Ticket', 'lottery-for-woocommerce' ),
					),
				)
			);
			woocommerce_wp_select(
				array(
					'id'      => '_lty_tickets_per_tab_display_type',
					'class'   => 'lty_user_selection_ticket_fields',
					'label'   => __( 'Ticket Number Pattern', 'lottery-for-woocommerce' ),
					'value'   => is_callable( array( $product_object, 'get_lty_tickets_per_tab_display_type' ) ) ? $product_object->get_lty_tickets_per_tab_display_type() : '1',
					'options' => array(
						'1' => __( 'Sequential', 'lottery-for-woocommerce' ),
						'2' => __( 'Shuffled', 'lottery-for-woocommerce' ),
					),
				)
			);
			woocommerce_wp_select(
				array(
					'id'      => '_lty_ticket_number_type',
					'class'   => '_lty_ticket_number_type',
					'label'   => __( 'Ticket Number Pattern', 'lottery-for-woocommerce' ),
					'value'   => is_callable( array( $product_object, 'get_lty_ticket_number_type' ) ) ? $product_object->get_lty_ticket_number_type() : '3',
					'options' => array(
						'3' => __( 'Shuffled', 'lottery-for-woocommerce' ),
						'2' => __( 'Sequential', 'lottery-for-woocommerce' ),
						'1' => __( 'Random', 'lottery-for-woocommerce' ),
					),
				)
			);
			woocommerce_wp_text_input(
				array(
					'id'          => '_lty_ticket_sequential_start_number',
					'class'       => '_lty_ticket_sequential_start_number _lty_automatic_type_start_number',
					'label'       => __( 'Ticket Starting Number', 'lottery-for-woocommerce' ) . "<span class='required'>*</span>",
					'value'       => is_callable( array( $product_object, 'get_lty_ticket_sequential_start_number' ) ) ? $product_object->get_lty_ticket_sequential_start_number() : '1',
					'type'        => 'text',
					'desc_tip'    => true,
					'description' => __( 'Input the number from which the ticket number should start for this giveaway.', 'lottery-for-woocommerce' ),
				)
			);
			woocommerce_wp_text_input(
				array(
					'id'          => '_lty_ticket_shuffled_start_number',
					'class'       => '_lty_ticket_shuffled_start_number _lty_automatic_type_start_number',
					'label'       => __( 'Ticket Starting Number', 'lottery-for-woocommerce' ) . "<span class='required'>*</span>",
					'value'       => is_callable( array( $product_object, 'get_lty_ticket_shuffled_start_number' ) ) ? $product_object->get_lty_ticket_shuffled_start_number() : '1',
					'type'        => 'text',
					'desc_tip'    => true,
					'description' => __( 'Input the number from which the ticket number should start for this giveaway.', 'lottery-for-woocommerce' ),
				)
			);
			woocommerce_wp_text_input(
				array(
					'id'          => '_lty_ticket_start_number',
					'class'       => '_lty_ticket_start_number lty_user_selection_ticket_fields',
					'label'       => __( 'Ticket Starting Number', 'lottery-for-woocommerce' ) . "<span class='required'>*</span>",
					'value'       => is_callable( array( $product_object, 'get_lty_ticket_start_number' ) ) ? $product_object->get_lty_ticket_start_number() : '1',
					'type'        => 'text',
					'desc_tip'    => true,
					'description' => __( 'Tickets for this Giveaway will start with the number mentioned in this option. Only numbers are allowed in this field.', 'lottery-for-woocommerce' ),
				)
			);
			woocommerce_wp_text_input(
				array(
					'id'    => '_lty_ticket_prefix',
					'class' => '_lty_ticket_prefix lty_user_selection_ticket_fields',
					'label' => __( 'Ticket Prefix', 'lottery-for-woocommerce' ),
					'value' => is_callable( array( $product_object, 'get_lty_ticket_prefix' ) ) ? $product_object->get_lty_ticket_prefix() : '',
					'type'  => 'text',
				)
			);
			woocommerce_wp_text_input(
				array(
					'id'    => '_lty_ticket_suffix',
					'class' => '_lty_ticket_suffix lty_user_selection_ticket_fields',
					'label' => __( 'Ticket Suffix', 'lottery-for-woocommerce' ),
					'value' => is_callable( array( $product_object, 'get_lty_ticket_suffix' ) ) ? $product_object->get_lty_ticket_suffix() : '',
					'type'  => 'text',
				)
			);
			woocommerce_wp_checkbox(
				array(
					'id'    => '_lty_alphabet_with_sequence_nos_enabled',
					'value' => is_callable( array( $product_object, 'get_lty_alphabet_with_sequence_nos_enabled' ) ) ? $product_object->get_lty_alphabet_with_sequence_nos_enabled() : '',
					'class' => 'lty_user_selection_ticket_fields',
					'label' => __( 'Use Alphabet Ticket Numbers', 'lottery-for-woocommerce' ),
				)
			);
			woocommerce_wp_select(
				array(
					'id'      => '_lty_alphabet_with_sequence_nos_type',
					'class'   => 'lty_user_selection_ticket_fields',
					'label'   => __( 'Alphabet ticket Numbers Type', 'lottery-for-woocommerce' ),
					'value'   => is_callable( array( $product_object, 'get_lty_alphabet_with_sequence_nos_type' ) ) ? $product_object->get_lty_alphabet_with_sequence_nos_type() : '1',
					'options' => array(
						'1' => __( 'Alphabet with Numbers', 'lottery-for-woocommerce' ),
						'2' => __( 'Alphabet with Sequence Numbers', 'lottery-for-woocommerce' ),
					),
				)
			);
			woocommerce_wp_text_input(
				array(
					'id'                => '_lty_tickets_per_tab',
					'class'             => 'lty_user_selection_ticket_fields',
					'label'             => __( 'Number of Tickets per Tab', 'lottery-for-woocommerce' ) . "<span class='required'>*</span>",
					'value'             => is_callable( array( $product_object, 'get_lty_tickets_per_tab' ) ) ? $product_object->get_lty_tickets_per_tab() : '10',
					'type'              => 'number',
					'custom_attributes' => array(
						'step' => 'any',
						'min'  => '1',
					),
					'desc_tip'          => true,
					'description'       => __( 'The number of tickets which has to be displayed per tab.', 'lottery-for-woocommerce' ),
				)
			);
			woocommerce_wp_checkbox(
				array(
					'id'          => '_lty_view_more_tickets_per_tab',
					'value'       => is_callable( array( $product_object, 'get_lty_view_more_tickets_per_tab' ) ) ? $product_object->get_lty_view_more_tickets_per_tab() : '',
					'class'       => 'lty_user_selection_ticket_fields',
					'label'       => __( 'Display View More Tickets in per Tab', 'lottery-for-woocommerce' ),
					'description' => __( 'You can split the tickets display in per tab using "View More Tickets".', 'lottery-for-woocommerce' ),
				)
			);
			woocommerce_wp_text_input(
				array(
					'id'                => '_lty_tickets_per_tab_view_more_count',
					'class'             => 'lty_user_selection_ticket_fields',
					'label'             => __( 'Set Number of tickets in View More', 'lottery-for-woocommerce' ),
					'value'             => is_callable( array( $product_object, 'get_lty_tickets_per_tab_view_more_count' ) ) ? $product_object->get_lty_tickets_per_tab_view_more_count() : '',
					'type'              => 'number',
					'custom_attributes' => array( 'step' => 'any' ),
				)
			);
			woocommerce_wp_checkbox(
				array(
					'id'          => '_lty_lucky_dip',
					'value'       => is_callable( array( $product_object, 'get_lty_lucky_dip' ) ) ? $product_object->get_lty_lucky_dip() : '',
					'class'       => 'lty_user_selection_ticket_fields lty-lucky-dip',
					'label'       => __( 'Enable Lucky Dip', 'lottery-for-woocommerce' ),
					'description' => __( 'When enabled, users can allow the Giveaway System to randomly pick a ticket number for them by clicking a button.', 'lottery-for-woocommerce' ),
				)
			);
			woocommerce_wp_select(
				array(
					'id'          => '_lty_lucky_dip_method_type',
					'class'       => 'lty_user_selection_ticket_fields lty-lucky-dip-fields',
					'label'       => __( 'Lucky Dip Method', 'lottery-for-woocommerce' ),
					'value'       => is_callable( array( $product_object, 'get_lty_lucky_dip_method_type' ) ) ? $product_object->get_lty_lucky_dip_method_type() : '1',
					'options'     => lty_get_lucky_dip_method_type_options(),
					'description' => __( 'When selecting "Display & Add the Tickets to the Cart Directly" option, it will display the ticket number(s) and directly added to the cart. When selecting "Only Display the tickets" option, it will only display the ticket number(s) without adding to the cart.', 'lottery-for-woocommerce' ),
					'desc_tip'    => true,
				)
			);
			woocommerce_wp_checkbox(
				array(
					'id'    => '_lty_hide_sold_tickets',
					'value' => is_callable( array( $product_object, 'get_lty_hide_sold_tickets' ) ) ? $product_object->get_lty_hide_sold_tickets() : '',
					'class' => 'lty_user_selection_ticket_fields',
					'label' => __( 'Hide Sold Tickets in per Tab', 'lottery-for-woocommerce' ),
				)
			);
			?>
		</div>
		<div class="options_group show_if_lottery">
			<h4><?php esc_html_e( 'Winner Settings', 'lottery-for-woocommerce' ); ?></h4>
			<?php
			woocommerce_wp_select(
				array(
					'id'          => '_lty_winner_selection_method',
					'class'       => '_lty_winner_product_setting_fields',
					'label'       => __( 'Winner Selection Method', 'lottery-for-woocommerce' ),
					'value'       => is_callable( array( $product_object, 'get_lty_winner_selection_method' ) ) ? $product_object->get_lty_winner_selection_method() : '',
					'options'     =>
					/**
					 * This hook is used to alter the lottery winner selection method options.
					 *
					 * @since 10.6.0
					 */
					apply_filters(
						'lty_lottery_winner_selection_method_options',
						array(
							'1' => __( 'Automatic', 'lottery-for-woocommerce' ),
							'2' => __( 'Manual', 'lottery-for-woocommerce' ),
						)
					),
					'desc_tip'    => true,
					'description' => __( 'Automatic: The winner will be  automatically selected and both admin and winner will be notifed. Manual: Winner has to be manually selected by the admin.', 'lottery-for-woocommerce' ),
				)
			);
			woocommerce_wp_select(
				array(
					'id'          => '_lty_winning_product_selection',
					'class'       => '_lty_winner_product_setting_fields',
					'label'       => __( 'Winning Item Selection Method', 'lottery-for-woocommerce' ),
					'value'       => is_callable( array( $product_object, 'get_lty_winning_product_selection' ) ) ? $product_object->get_lty_winning_product_selection() : '1',
					'options'     => array(
						'1' => __( 'Products inside the site', 'lottery-for-woocommerce' ),
						'2' => __( 'Products outside the site', 'lottery-for-woocommerce' ),
					),
					'desc_tip'    => true,
					'description' => __( 'Products inside the Site: Once winner is selected, the selected product will be added to the user\'s account free of cost. Product outside the site: Sending Gifts to the winner has be managed outside the site.', 'lottery-for-woocommerce' ),
				)
			);
			?>
			<p class="form-field">
				<label><?php esc_html_e( 'Winning Item(s) for this Giveaway', 'lottery-for-woocommerce' ); ?></label>
				<?php
				$selected_gift_products = is_callable( array( $product_object, 'get_lty_selected_gift_products' ) ) ? $product_object->get_lty_selected_gift_products() : array();

				lty_select2_html(
					array(
						'id'                   => '_lty_selected_gift_products',
						'class'                => '_lty_selected_gift_products _lty_winner_product_setting_fields',
						'list_type'            => 'products',
						'action'               => 'lty_json_search_products_and_variations',
						'placeholder'          => __( 'Search for a product&hellip;' ),
						'exclude_out_of_stock' => 'yes',
						'multiple'             => true,
						'options'              => $selected_gift_products,
						'error'                => __( 'gift products field cannot be empty.', 'lottery-for-woocommerce' ),
					)
				);

				echo wp_kses_post( wc_help_tip( __( 'Choose the products which you want to give for giveaway winner(s).', 'lottery-for-woocommerce' ) ) );
				?>
			</p>

			<?php
			woocommerce_wp_textarea_input(
				array(
					'id'          => '_lty_winner_outside_gift_items',
					'class'       => '_lty_winner_product_setting_fields',
					'label'       => __( 'Winning Items Information URL or Custom Label', 'lottery-for-woocommerce' ) . "<span class='required'>*</span>",
					'value'       => is_callable( array( $product_object, 'get_lty_winner_outside_gift_items' ) ) ? $product_object->get_lty_winner_outside_gift_items() : '',
					'desc_tip'    => true,
					'description' => __( 'Please place the URL of the page where the giveaway gift information is available or use the custom labels about giveaway gift information.', 'lottery-for-woocommerce' ),
				)
			);
			?>

		</div>
		<div class="options_group show_if_lottery">
			<h4><?php esc_html_e( 'Display Settings', 'lottery-for-woocommerce' ); ?></h4>
			<?php
			woocommerce_wp_select(
				array(
					'id'      => '_lty_hide_countdown_timer_selection_type',
					'class'   => '_lty_hide_countdown_timer_selection_type lty-countdown-timer-fields',
					'label'   => __( 'Hide Countdown timer Level Selection Type', 'lottery-for-woocommerce' ),
					'value'   => is_callable( array( $product_object, 'get_lty_hide_countdown_timer_selection_type' ) ) ? $product_object->get_lty_hide_countdown_timer_selection_type() : '1',
					'options' => array(
						'1' => __( 'Global Level', 'lottery-for-woocommerce' ),
						'2' => __( 'Product Level', 'lottery-for-woocommerce' ),
					),
				)
			);
			woocommerce_wp_checkbox(
				array(
					'id'    => '_lty_hide_countdown_timer_in_shop',
					'value' => is_callable( array( $product_object, 'get_lty_hide_countdown_timer_in_shop' ) ) ? $product_object->get_lty_hide_countdown_timer_in_shop() : '',
					'class' => '_lty_hide_countdown_timer_in_shop lty-countdown-timer-fields',
					'label' => __( 'Hide Countdown Timer for Giveaway Products on Shop and Category Pages', 'lottery-for-woocommerce' ),
				)
			);
			woocommerce_wp_checkbox(
				array(
					'id'    => '_lty_hide_countdown_timer_in_single_product',
					'value' => is_callable( array( $product_object, 'get_lty_hide_countdown_timer_in_single_product' ) ) ? $product_object->get_lty_hide_countdown_timer_in_single_product() : '',
					'class' => '_lty_hide_countdown_timer_in_single_product lty-countdown-timer-fields',
					'label' => __( 'Hide Countdown Timer for Single Product Pages', 'lottery-for-woocommerce' ),
				)
			);
			woocommerce_wp_select(
				array(
					'id'      => '_lty_hide_progress_bar_selection_type',
					'class'   => '_lty_hide_progress_bar_selection_type',
					'label'   => __( 'Hide Progress Bar Selection Type', 'lottery-for-woocommerce' ),
					'value'   => is_callable( array( $product_object, 'get_lty_hide_progress_bar_selection_type' ) ) ? $product_object->get_lty_hide_progress_bar_selection_type() : '1',
					'options' => array(
						'1' => __( 'Global Level', 'lottery-for-woocommerce' ),
						'2' => __( 'Product Level', 'lottery-for-woocommerce' ),
					),
				)
			);
			woocommerce_wp_checkbox(
				array(
					'id'    => '_lty_hide_progress_bar_in_shop',
					'value' => is_callable( array( $product_object, 'get_lty_hide_progress_bar_in_shop' ) ) ? $product_object->get_lty_hide_progress_bar_in_shop() : '',
					'class' => '_lty_hide_progress_bar_in_shop',
					'label' => __( 'Hide Progress Bar in the Shop and Category Page(s)', 'lottery-for-woocommerce' ),
				)
			);
			woocommerce_wp_checkbox(
				array(
					'id'    => '_lty_hide_progress_bar_in_single_product',
					'value' => is_callable( array( $product_object, 'get_lty_hide_progress_bar_in_single_product' ) ) ? $product_object->get_lty_hide_progress_bar_in_single_product() : '',
					'class' => '_lty_hide_progress_bar_in_single_product',
					'label' => __( 'Hide Progress Bar in the Single Product Page', 'lottery-for-woocommerce' ),
				)
			);
			?>
		</div>
		<?php
		if ( wc_tax_enabled() ) {
			?>
			<div class="options_group show_if_lottery">
				<h4><?php esc_html_e( 'Tax Settings', 'lottery-for-woocommerce' ); ?></h4>
				<?php
					woocommerce_wp_select(
						array(
							'id'          => '_tax_status',
							'value'       => $product_object ? $product_object->get_tax_status( 'edit' ) : '',
							'label'       => __( 'Tax status', 'lottery-for-woocommerce' ),
							'options'     => array(
								'taxable'  => __( 'Taxable', 'lottery-for-woocommerce' ),
								'shipping' => __( 'Shipping only', 'lottery-for-woocommerce' ),
								'none'     => esc_html_x( 'None', 'Tax status', 'lottery-for-woocommerce' ),
							),
							'desc_tip'    => 'true',
							'description' => __( 'Define whether or not the entire product is taxable, or just the cost of shipping it.', 'lottery-for-woocommerce' ),
						)
					);

					woocommerce_wp_select(
						array(
							'id'          => '_tax_class',
							'value'       => $product_object ? $product_object->get_tax_class( 'edit' ) : '',
							'label'       => __( 'Tax class', 'lottery-for-woocommerce' ),
							'options'     => wc_get_product_tax_class_options(),
							'desc_tip'    => 'true',
							'description' => __( 'Choose a tax class for this product. Tax classes are used to apply different tax rates specific to certain types of product.', 'lottery-for-woocommerce' ),
						)
					);
				?>
			</div>
			<?php
		}

		/**
		 * This hook is used to display extra lottery product options.
		 *
		 * @since 1.0
		 */
		do_action( 'woocommerce_product_options_lottery_product_data' );
		?>
	</div>
</div>
<?php
