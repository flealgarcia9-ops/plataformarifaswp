/* global lty_admin_params */

jQuery(function ($) {
	'use strict';

	var LTY_Admin = {
		init: function () {
			this.trigger_on_page_load();
			$(document).on('change', '#lty_lottery_schedule_type', this.toggle_lottery_schedule_type);
			$(document).on('click', '.lty-lottery-manual-relist', this.manual_relist);
			$(document).on('change', '#_lty_ticket_generation_type', this.toggle_ticket_generation_type);
			$(document).on('change', '#_lty_ticket_number_type', this.toggle_ticket_number_type);
			$(document).on('change', '#product-type', this.toggle_product_type);
			$(document).on('change', '#_lty_winning_product_selection', this.toggle_winner_product_selection);
			$(document).on('change', '#_lty_ticket_price_type', this.toggle_lottery_price);
			$(document).on('change', '#_lty_manage_question', this.toggle_manage_question);
			// Question Answer.
			$(document).on('change', '#_lty_force_answer', this.toggle_force_answer);
			$(document).on('change', '#_lty_restrict_incorrectly_selected_answer', this.toggle_incorrectly_selected_answer);
			$(document).on('change', '#_lty_validate_correct_answer', this.toggle_validate_correct_answer);
			$(document).on('change', '#_lty_verify_answer_type', this.toggle_verify_answer_type);
			$(document).on('change', '#_lty_question_answer_time_limit_type', this.toggle_question_answer_time_limit_type);
			$(document).on('change', '#_lty_question_answer_display_type', this.toggle_question_answer_first_option_as_default_option);

			$(document).on('change', '#_lty_hide_countdown_timer_selection_type', this.toggle_hide_countdown_timer_selection_type);
			$(document).on('change', '#_lty_hide_progress_bar_selection_type', this.toggle_hide_progress_bar_selection_type);
			$(document).on('change', '#_lty_alphabet_with_sequence_nos_enabled', this.toggle_alphabet_with_sequence_nos_enabled);
			$(document).on('click', '.lty_delete_data', this.prevent_action_delete);
			$(document).on('click', '.lty_remove_instant_winner_data', this.prevent_remove_instant_winner_action);
			$(document).on('click', '.lty_delete_instant_winner_data', this.prevent_delete_instant_winner_action);
			$(document).on('click', 'a.lty_manual_winner_data', this.click_lottery_winner_action);
			$(document).on('click', '.lty-lottery-extend', this.extend_lottery);
			// Automatic finished relisting lottery
			$(document).on('change', '#_lty_relist_finished_lottery', this.toggle_relist_finished_lottery);
			$(document).on('change', '#_lty_finished_lottery_relist_pause', this.toggle_finished_lottery_relist_pause);
			$(document).on('change', '#_lty_finished_lottery_relist_count_type', this.toggle_finished_lottery_relist_count_type);

			// Automatic failed relisting lottery
			$(document).on('change', '#_lty_relist_failed_lottery', this.toggle_relist_failed_lottery);
			$(document).on('change', '#_lty_failed_lottery_relist_pause', this.toggle_failed_lottery_relist_pause);
			$(document).on('change', '#_lty_failed_lottery_relist_count_type', this.toggle_failed_lottery_relist_count_type);

			// Add a lottery question answer.
			$(document).on('click', '.lty-add-answer', this.add_answer);
			// Remove a lottery question answer.
			$(document).on('click', '.lty-remove-answer', this.remove_answer);
			// Select a lottery question answer.
			$(document).on('change', '.lty-select-answer', this.select_answer);
			// Question answer selection type.
			$(document).on('change', '#_lty_question_answer_selection_type', this.toggle_question_answer_selection_type);

			// Add a predefined button.
			$(document).on('click', '.lty-add-predefined-button-rule', this.add_predefined_button);
			// Remove a predefined button.
			$(document).on('click', '.lty-remove-predefined-button-rule', this.remove_predefined_button);
			// Toggle predefined button checkbox.
			$(document).on('change', '#_lty_enable_predefined_buttons', this.toggle_enable_predefined_button);
			// Toggle predefined button selection type.
			$(document).on('change', '._lty_predefined_buttons_selection_type', this.toggle_predefined_button_selection_type);
			// Validate Predefined buttons.
			$(document).on('change', '.lty-predefined-buttons-wrapper .lty-predefined-button-ticket-quantity', this.validate_predefined_button);
			$(document).on('change', '#_lty_predefined_with_quantity_selector', this.toggle_predefined_button_with_quantity_selector);

			// Alert message for lottery button action post table.
			$(document).on('click', '.lty_lottery_button', this.lottery_button_alert_message);
			$(document).on('click', '.lty_wrapper_cover #doaction', this.bulk_action_confirmation);

			// Prevent lottery product save in functionality.
			$('form#post').on('submit', this.prevent_lottery_product_save);
			// Orders without tickets popup.
			$(document).on('click', '.lty-orders-without-tickets-popup-action', this.orders_without_tickets_popup);
			// Orders status action.
			$(document).on('click', '.lty-order-status-action', this.order_status_action);
			// Guest user participation type alert message.
			$(document).on('change', '#lty_settings_guest_user_participate_type', this.guest_user_participate_type_alert_message);
			$(document).on('click', '.lty-select-image', this.select_image);
			$(document).on('click', '.lty-remove-image', this.remove_image);
			// Handle view lottery configuration info
			$(document).on('click', '.lty-toggle-lottery-configuration-info', this.toggle_lottery_configuration_info);
			// Toggle view more lottery tickets per tab field.
			$(document).on('change', '#_lty_view_more_tickets_per_tab', this.toggle_lottery_tickets_per_tab_field);
			$(document).on('click', '.lty-search-field-button', this.toggle_list_table_search_filter_fields);
			$(document).on('click', '.lty-search-fields-wrapper #lty-search-submit', this.validate_search_fields);
			$(document).on('change', '.lty-lottery-tickets-purchased-date-filter', this.toggle_lottery_tickets_purchased_date_filter_fields);
			$(document).on('change', '.lty-lucky-dip', this.toggle_lucky_dip_fields);

			// Lottery instant winners rules.
			$(document).on('change', '#lty_instant_winners', this.toggle_instant_winners_rules_fields);
			$(document).on('change', '.lty-select-all-instant-winners-rules', this.select_all_instant_winners_rules);
			$(document).on('click', '.lty-instant-winners-rules-bulk-action-apply-button', this.handle_instant_winners_rules_bulk_action);
			$(document).on('click', '.lty-add-instant-winner-rule', this.add_instant_winner_rule);
			$(document).on('click', '.lty-save-instant-winners-rules', this.save_instant_winners_rules);
			$(document).on('click', '.lty-lottery-instant-winners-rules-pagination-action', this.trigger_lottery_instant_winners_rules_pagination_content);
			$(document).on('change', '.lty-lottery-instant-winners-rules-pagination-wrapper .lty-current-page', this.trigger_lottery_instant_winners_rules_pagination_content);
			$(document).on('click', '.lty-remove-instant-winner-rule', this.remove_instant_winner_rule);
			$(document).on('click change', '.lty-instant-winner-rule', this.allow_instant_winners_rules_save);
			$(document).on('change', '.lty-instant-winner-prize-type', this.toggle_instant_winner_prize_type);
			$(document).on('change', '.lty-instant-winner-coupon-generation-type', this.toggle_instant_winner_coupon_field);
			$(document).on('change', '#lty_instant_winner_display_mode', this.toggle_instant_winner_prize_display_mode);
			// Instant winner prize groups.
			$(document).on('change', '.lty-select-all-instant-winner-prize-groups', this.select_all_instant_winner_prize_groups);
			$(document).on('click', '.lty-add-new-instant-winner-prize-group', this.display_new_instant_winner_prize_group_popup);
			$(document).on('change', '.lty-instant-winner-prize-group-prize-type', this.toggle_instant_winner_prize_group_prize_type);
			$(document).on('change', '.lty-instant-winner-prize-group-coupon-generation-type', this.toggle_instant_winner_prize_group_coupon_field);
			$(document).on('click', '.lty-create-instant-winner-prize-group', this.create_instant_winner_prize_group);
			$(document).on('click', '.lty-save-instant-winner-prize-groups', this.save_instant_winner_prize_groups);
			$(document).on('click change', '.lty-instant-winner-prize-group', this.allow_instant_winner_prize_groups_save);
			$(document).on('click', '.lty-remove-instant-winner-prize-group', this.remove_instant_winner_prize_group);
			$(document).on('click', '.lty-instant-winner-prize-groups-bulk-action-apply-btn', this.handle_instant_winner_prize_groups_bulk_action);
			$(document).on('click', '.lty-instant-winner-prize-groups-pagination-action', this.trigger_instant_winner_prize_groups_pagination_action);
			$(document).on('change', '.lty-instant-winner-prize-groups-pagination-wrapper .lty-current-page', this.trigger_instant_winner_prize_groups_pagination_action);
			// Trigger the manual notification popup.
			$(document).on('click', '.lty-manual-lottery-notification', this.trigger_manual_lottery_notification_popup);
			// Send the manual notification.
			$(document).on('click', '.lty-send-manual-lottery-notification-button', this.send_manual_lottery_notification);
			
		},

		trigger_on_page_load: function () {
			if ('3' == lty_admin_params.guest_participation_type) {
				$('._lty_user_minimum_tickets_field').hide();
				$('._lty_user_maximum_tickets_field').hide();
			}

			// Set the product type as lottery when clicking the add new lottery button.
			if ('yes' == lty_admin_params.is_new_lottery_product) {
				$('#product-type').val('lottery').trigger('change');
			}

			LTY_Admin.handle_product_type('#product-type');

			// Hide lottery configuration info on page load.
			$('.lty-lottery-configuration').find('.lty-hidden-content').hide();
			// Toggle the specific date range filter fields.
			$('.lty-lottery-tickets-purchased-date-filter').each(function () {
				LTY_Admin.handle_lottery_tickets_purchased_date_filter_fields(this);
			});
		},

		/**
		 * Toggle the lottery schedule type fields.
		 * 
		 * @since 11.7.0
		 * @param {object} event The event object.
		 */
		toggle_lottery_schedule_type: function (event) {
			event.preventDefault();
			LTY_Admin.handle_lottery_schedule_type($(event.currentTarget));
		},

		toggle_instant_winners_rules_fields: function (event) {
			event.preventDefault();
			LTY_Admin.handle_instant_winners($(event.currentTarget));
		},

		toggle_product_type: function (event) {
			event.preventDefault();
			LTY_Admin.handle_product_type($(event.currentTarget));
		},

		toggle_lottery_price: function (event) {
			event.preventDefault();
			LTY_Admin.lottery_price($(event.currentTarget));
		},

		toggle_manage_question: function (event) {
			event.preventDefault();
			LTY_Admin.manage_question($(event.currentTarget));
		},

		toggle_force_answer: function (event) {
			event.preventDefault();
			LTY_Admin.force_answer($(event.currentTarget));
		},

		toggle_incorrectly_selected_answer: function (event) {
			event.preventDefault();
			LTY_Admin.incorrectly_selected_answer($(event.currentTarget));
		},

		toggle_validate_correct_answer: function (event) {
			event.preventDefault();
			LTY_Admin.validate_correct_answer($(event.currentTarget));
		},

		toggle_verify_answer_type: function (event) {
			event.preventDefault();
			LTY_Admin.verify_answer_type($(event.currentTarget));
		},

		toggle_question_answer_time_limit_type: function (event) {
			event.preventDefault();
			LTY_Admin.question_answer_time_limit_type($(event.currentTarget));
		},

		/**
		 * Toggle the question answer first dropdown option as default option.
		 * 
		 * @since 10.2.0
		 * @param {event} event 
		 */
		toggle_question_answer_first_option_as_default_option: function (event) {
			event.preventDefault();
			LTY_Admin.handle_question_answer_first_option_as_default_option($(event.currentTarget));
		},

		toggle_ticket_generation_type: function (event) {
			event.preventDefault();
			LTY_Admin.ticket_generation_type($(event.currentTarget));
		},

		toggle_ticket_number_type: function (event) {
			event.preventDefault();
			LTY_Admin.ticket_number_type($(event.currentTarget));
		},

		toggle_winner_product_selection: function (event) {
			event.preventDefault();
			LTY_Admin.winner_product_selection($(event.currentTarget));
		},

		toggle_hide_countdown_timer_selection_type: function (event) {
			event.preventDefault();
			LTY_Admin.hide_countdown_timer_selection_type($(event.currentTarget));
		},

		toggle_hide_progress_bar_selection_type: function (event) {
			event.preventDefault();
			LTY_Admin.hide_progress_bar_selection_type($(event.currentTarget));
		},

		toggle_question_answer_selection_type: function (event) {
			event.preventDefault();
			LTY_Admin.question_answer_selection_type($(event.currentTarget));
		},

		toggle_enable_predefined_button: function (event) {
			event.preventDefault();
			LTY_Admin.enable_predefined_button($(event.currentTarget));
		},

		toggle_predefined_button_selection_type: function (event) {
			event.preventDefault();
			LTY_Admin.predefined_button_selection_type($(event.currentTarget));
		},

		toggle_alphabet_with_sequence_nos_enabled: function (event) {
			event.preventDefault();
			LTY_Admin.alphabet_with_sequence_nos_enabled($(event.currentTarget));
		},

		toggle_relist_finished_lottery: function (event) {
			event.preventDefault();
			LTY_Admin.relist_finished_lottery($(event.currentTarget));
		},

		toggle_finished_lottery_relist_pause: function (event) {
			event.preventDefault();
			LTY_Admin.finished_lottery_relist_pause($(event.currentTarget));
		},

		toggle_finished_lottery_relist_count_type: function (event) {
			event.preventDefault();
			LTY_Admin.finished_lottery_relist_count_type($(event.currentTarget));
		},

		toggle_relist_failed_lottery: function (event) {
			event.preventDefault();
			LTY_Admin.relist_failed_lottery($(event.currentTarget));
		},

		toggle_failed_lottery_relist_pause: function (event) {
			event.preventDefault();
			LTY_Admin.failed_lottery_relist_pause($(event.currentTarget));
		},

		toggle_failed_lottery_relist_count_type: function (event) {
			event.preventDefault();
			LTY_Admin.failed_lottery_relist_count_type($(event.currentTarget));
		},

		click_lottery_winner_action: function (event) {
			if (!confirm(lty_admin_params.lty_confirm_message)) {
				event.preventDefault();
				return;
			}
		},

		/**
		 * Toggle lottery tickets per tab field.
		 * 
		 * @since 8.6.0
		 * @param {event} event 
		 */
		toggle_lottery_tickets_per_tab_field(event) {
			event.preventDefault();
			LTY_Admin.handle_view_more_lottery_tickets_per_tab_field($(event.currentTarget));
		},

		/**
		 * Trigger lottery instant winners rules pagination content.
		 * 
		 * @since 9.6.0
		 * @param {event} event 
		 */
		trigger_lottery_instant_winners_rules_pagination_content: function (event) {
			event.preventDefault();
			if ($('.lty-unsaved-instant-winner-rules').val()) {
				if (confirm(lty_admin_params.instant_winner_rules.save_alert_msg)) {
					return false;
				}
			}

			var $this = $(event.currentTarget),
				current_page = $this.hasClass('lty-current-page') ? $this.val() : $this.data('page');

			LTY_Admin.handle_lottery_instant_winners_rules_pagination_content(current_page);
		},

		/**
		 * Toggle lottery tickets purchased date by range filter fields.
		 * 
		 * @since 10.2.0
		 * @param {event} event 
		 */
		toggle_lottery_tickets_purchased_date_filter_fields: function (event) {
			event.preventDefault();
			LTY_Admin.handle_lottery_tickets_purchased_date_filter_fields($(event.currentTarget));
		},

		/**
		 * Toggle the list table search filter fields.
		 * 
		 * @since 10.2.0
		 * @param {object} event 
		 */
		toggle_list_table_search_filter_fields: function (event) {
			event.preventDefault();
			LTY_Admin.handle_list_table_search_filter_fields($(event.currentTarget));
		},

		/**
		 * Toggle the lucky dip fields.
		 * 
		 * @since 10.4.0
		 * @param {object} event 
		 */
		toggle_lucky_dip_fields: function (event) {
			event.preventDefault();
			LTY_Admin.handle_lucky_dip_fields($(event.currentTarget));
		},

		/**
		 * Toggle the predefined buttons with quantity selector fields.
		 * 
		 * @since 10.6.0
		 * @param {object} event 
		 */
		toggle_predefined_button_with_quantity_selector: function (event) {
			event.preventDefault();
			LTY_Admin.handle_predefined_button_with_quantity_selector($(event.currentTarget));
		},

		/**
		 * Toggle the instant winner prize type fields.
		 * 
		 * @since 10.6.0
		 * @param {object} event 
		 */
		toggle_instant_winner_prize_type: function (event) {
			event.preventDefault();
			LTY_Admin.handle_instant_winner_prize_type($(event.currentTarget));
		},

		/**
		 * Toggle the instant winner coupon fields.
		 * 
		 * @since 10.6.0
		 * @param {object} event 
		 */
		toggle_instant_winner_coupon_field: function (event) {
			event.preventDefault();
			LTY_Admin.handle_instant_winner_coupon_field($(event.currentTarget));
		},

		/**
		 * Toggle the instant winner prize display mode.
		 * 
		 * @since 11.1.0
		 * @param {object} event 
		 */
		toggle_instant_winner_prize_display_mode: function (event) {
			event.preventDefault();
			LTY_Admin.handle_instant_winner_prize_display_mode($(event.currentTarget));
		},

		/**
		 * Toggle the instant winner prize group prize type fields.
		 * 
		 * @since 11.1.0
		 * @param {object} event 
		 */
		toggle_instant_winner_prize_group_prize_type: function (event) {
			event.preventDefault();
			LTY_Admin.handle_instant_winner_prize_group_prize_type($(event.currentTarget));
		},

		/**
		 * Toggle the instant winner prize group coupon fields.
		 * 
		 * @since 11.1.0
		 * @param {object} event 
		 */
		toggle_instant_winner_prize_group_coupon_field: function (event) {
			event.preventDefault();
			LTY_Admin.handle_instant_winner_prize_group_coupon_field($(event.currentTarget));
		},

		/**
		 * Trigger instant winner prize groups pagination content.
		 * 
		 * @since 11.1.0
		 * @param {event} event
		 */
		trigger_instant_winner_prize_groups_pagination_action: function (event) {
			event.preventDefault();
			if ($('.lty-unsaved-instant-winner-prize-groups').val()) {
				if (confirm(lty_admin_params.instant_winner_prize_groups.save_alert_msg)) {
					return false;
				}
			}

			LTY_Admin.handle_instant_winner_prize_groups_pagination_action($(event.currentTarget));
		},

		handle_instant_winners: function ($this) {
			if ($($this).is(':checked')) {
				$('.lty-instant-winners-rules-wrapper').show();
				$('.lty-instant-winner-rule-field').closest('p').show();
			} else {
				$('.lty-instant-winners-rules-wrapper').hide();
				$('.lty-instant-winner-rule-field').closest('p').hide();
			}
		},

		handle_product_type: function ($this) {
			if ('lottery' == $($this).val()) {
				$('input[name="_virtual"]').attr('checked', 'checked').trigger('change');
				LTY_Admin.handle_lottery_schedule_type('#lty_lottery_schedule_type');
				LTY_Admin.winner_product_selection('#_lty_winning_product_selection');
				LTY_Admin.ticket_generation_type('#_lty_ticket_generation_type');
				LTY_Admin.lottery_price('#_lty_ticket_price_type');
				LTY_Admin.verify_answer_type('#_lty_verify_answer_type');
				LTY_Admin.validate_correct_answer('#_lty_validate_correct_answer');
				LTY_Admin.incorrectly_selected_answer('#_lty_restrict_incorrectly_selected_answer');
				LTY_Admin.force_answer('#_lty_force_answer');
				LTY_Admin.manage_question('#_lty_manage_question');
				LTY_Admin.question_answer_selection_type('#_lty_question_answer_selection_type');
				LTY_Admin.hide_countdown_timer_selection_type('#_lty_hide_countdown_timer_selection_type');
				LTY_Admin.hide_progress_bar_selection_type('#_lty_hide_progress_bar_selection_type');
				LTY_Admin.enable_predefined_button('#_lty_enable_predefined_buttons');
				LTY_Admin.predefined_button_selection_type('._lty_predefined_buttons_selection_type');
				LTY_Admin.alphabet_with_sequence_nos_enabled('#_lty_alphabet_with_sequence_nos_enabled');
				LTY_Admin.relist_finished_lottery('#_lty_relist_finished_lottery');
				LTY_Admin.relist_failed_lottery('#_lty_relist_failed_lottery');
				LTY_Admin.handle_instant_winners('#lty_instant_winners');
				// Instant winner rules. 
				$('.lty-instant-winners-rules-wrapper').find('.lty-instant-winner-prize-type').each(function () {
					LTY_Admin.handle_instant_winner_prize_type(this);
				});
				LTY_Admin.handle_instant_winner_prize_display_mode($('#lty_instant_winner_display_mode'));

				// Instant winner prize groups.
				$('.lty-instant-winner-prize-groups-wrapper').find('.lty-instant-winner-prize-group-prize-type').each(function () {
					LTY_Admin.handle_instant_winner_prize_group_prize_type(this);
				});
			}
		},

		remove_image: function (event) {
			event.preventDefault();
			var $this = $(event.currentTarget);

			LTY_Admin.block($this.closest('tr, p'));
			$this.closest('tr, p').find('.lty-selected-image-id').val('');
			$this.closest('tr, p').find('img').attr('src', lty_admin_params.placeholder_image_url);
			$this.hide();

			LTY_Admin.unblock($this.closest('tr, p'));
		},

		select_image: function (event) {
			event.preventDefault();
			// Upload Batch Image.
			var file_frame;
			var button = $(this);
			var formfield = $(this).prev();
			// If the media frame already exists, reopen it.
			if (file_frame) {
				file_frame.open();
				return;
			}

			// Create the media frame.
			file_frame = wp.media.frames.file_frame = wp.media({
				frame: 'select',
				// Set the title of the modal.
				title: button.data('title'),
				multiple: false,
				library: { type: 'image' },
				button: { text: button.data('button') }
			});

			// When an image is selected, run a callback.
			file_frame.on('select', function () {
				var file_id = '';
				var file_path = '';
				var selection = file_frame.state().get('selection');
				selection.map(function (attachment) {
					attachment = attachment.toJSON();
					if (attachment.id) {
						file_id = attachment.id;
						file_path = attachment.url;
					}
				});

				formfield.val(file_id);
				button.closest('tr, p').find('img').attr('src', file_path);
				button.closest('tr, p').find('.lty-remove-image').show();
			});

			// Finally, open the modal.
			file_frame.open();
		},

		add_instant_winner_rule: function (event) {
			event.preventDefault();
			var $this = $(event.currentTarget),
				wrapper = $this.closest('.lty-lottery-instant-winners-rule-modal-wrapper'),
				display_mode = $('#lty_instant_winner_display_mode').val(),
				instant_winner_rule = {
					'image_id': wrapper.find('.lty-instant-winner-image-id').val(),
					'ticket_number': wrapper.find('.lty-ticket-number').val(),
					'prize_type': wrapper.find('.lty-instant-winner-prize-type').val(),
					'coupon_generation_type': wrapper.find('.lty-instant-winner-coupon-generation-type').val(),
					'coupon_discount_type': wrapper.find('.lty-instant-winner-coupon-discount-type').val(),
					'coupon_id': wrapper.find('.lty-instant-winner-coupon-id').val(),
					'gift_product_id': wrapper.find('.lty-instant-winner-gift-product-id').val(),
					'gift_product_quantity': wrapper.find('.lty-instant-winner-gift-product-quantity').val(),
					'prize_message': wrapper.find('.lty-instant-winner-prize-message').val(),
					'prize_amount': wrapper.find('.lty-instant-winner-prize-amount').val(),
					'prize_group_id': wrapper.find('.lty-instant-winner-prize-group-id').val(),
			};

			if (!instant_winner_rule.ticket_number) {
				LTY_Admin.error_notice(wrapper, lty_admin_params.instant_winner_rules.ticket_number_empty_error_msg);
				return false;
			}

			if ('1' === display_mode) {
				switch (instant_winner_rule.prize_type) {
					case 'coupon':
						// Return if coupon value is empty, when selecting new coupon generation type.
						if ('1' === instant_winner_rule.coupon_generation_type && !instant_winner_rule.prize_amount) {
							LTY_Admin.error_notice(wrapper, lty_admin_params.instant_winner_rules.amount_empty_error_msg);
							return false;
						}
	
						// Return if coupon ID is empty, when selecting existing coupon generation type.
						if ('2' === instant_winner_rule.coupon_generation_type && !instant_winner_rule.coupon_id) {
							LTY_Admin.error_notice(wrapper, lty_admin_params.instant_winner_rules.coupon_id_empty_error_msg);
							return false;
						}
						break;
	
					case 'product':
						// Return if Gift Product ID is empty.
						if (!instant_winner_rule.gift_product_id) {
							LTY_Admin.error_notice(wrapper, lty_admin_params.instant_winner_rules.gift_product_id_empty_error_msg);
							return false;
						}

						// Return if Gift Product quantity is empty.
						if (!instant_winner_rule.gift_product_quantity) {
							LTY_Admin.error_notice(wrapper, lty_admin_params.instant_winner_rules.gift_product_quantity_empty_error_msg);
							return false;
						}
						break;

					case 'smart_coupon':
					case 'wallet':
					case 'woo_wallet':
					case 'credit':
						// Return if wallet value is empty.
						if (!instant_winner_rule.prize_amount) {
							LTY_Admin.error_notice(wrapper, lty_admin_params.instant_winner_rules.amount_empty_error_msg);
							return false;
						}
						break;
				}

				if (!instant_winner_rule.prize_message) {
					LTY_Admin.error_notice(wrapper, lty_admin_params.instant_winner_rules.prize_message_empty_error_msg);
					return false;
				}
			}

			if ('2' === display_mode && !instant_winner_rule.prize_group_id) {
				LTY_Admin.error_notice(wrapper, lty_admin_params.instant_winner_rules.prize_group_empty_error_msg);
				return false;
			}

			LTY_Admin.block(wrapper);
			var data = ({
				action: 'lty_add_instant_winner_rule',
				instant_winner_rule: instant_winner_rule,
				display_mode: display_mode,
				product_id: $('#post_ID').val(),
				lty_security: lty_admin_params.instant_winner_nonce,
			});

			$.post(ajaxurl, data, function (res) {
				if (true === res.success) {
					$(wrapper).find('.close-modal').click();

					var last_page = $('.lty-instant-winners-rules-wrapper').find('.lty-last-page'),
						current_page = last_page.hasClass('lty-current-page') ? last_page.val() : last_page.data('page');

					LTY_Admin.handle_lottery_instant_winners_rules_pagination_content(current_page);
				} else {
					alert(res.data.error);
				}

				LTY_Admin.unblock(wrapper);
			});
		},

		remove_instant_winner_rule: function (event) {
			event.preventDefault();
			var $this = $(event.currentTarget);

			if (!confirm(lty_admin_params.instant_winner_rules.remove_rule_alert_msg)) {
				return false;
			}

			LTY_Admin.handle_remove_instant_winner_rule([$($this).data('instant_winner_rule_id')], $('.lty-instant-winners-rules-wrapper').find('.lty-product-id').val());
		},

		/**
		 * Handle remove instant winner rules
		 * 
		 * @since 9.6.0
		 * @param {array} instant_winner_rule_ids
		 * @param {int} product_id
		 */
		handle_remove_instant_winner_rule: function (instant_winner_rule_ids, product_id) {
			if (!instant_winner_rule_ids || !product_id) {
				return false;
			}

			var data = ({
				action: 'lty_remove_instant_winner_rule',
				instant_winner_rule_ids: instant_winner_rule_ids,
				product_id: product_id,
				lty_security: lty_admin_params.instant_winner_nonce
			});

			$.post(ajaxurl, data, function (res) {
				if (true === res.success) {
					var current_page = 0 < $('.lty-instant-winners-rules-wrapper').find('.lty-current-page').length ? $('.lty-instant-winners-rules-wrapper').find('.lty-current-page').val() : 1;

					LTY_Admin.handle_lottery_instant_winners_rules_pagination_content(current_page);
				} else {
					alert(res.data.error);
				}
			});
		},

		lottery_price: function ($this) {
			if ('2' == $($this).val()) {
				$('#_lty_regular_price').closest('p').hide();
				$('#_lty_sale_price').closest('p').hide();
			} else {
				$('#_lty_regular_price').closest('p').show();
				$('#_lty_sale_price').closest('p').show();
			}
		},

		question_answer_selection_type: function ($this) {
			if ('1' == $($this).val()) {
				$('.lty-question-answer-product-field').closest('p').show();
				LTY_Admin.manage_question('#_lty_manage_question');
			} else {
				$('.lty-question-answer-product-field').closest('p').hide();
			}
		},

		manage_question: function ($this) {
			if ($($this).is(':checked')) {
				$('.lty-question-answer-field').show();
				$('.lty-question-answer-field').closest('p').show();
				LTY_Admin.force_answer('#_lty_force_answer');
				LTY_Admin.handle_question_answer_first_option_as_default_option('#_lty_question_answer_display_type');
			} else {
				$('.lty-question-answer-field').hide();
				$('.lty-question-answer-field').closest('p').hide();
			}
		},

		force_answer: function ($this) {
			if ($($this).is(':checked')) {
				$('.lty-force-question-answer-field').closest('p').show();
				LTY_Admin.validate_correct_answer('#_lty_validate_correct_answer');
				LTY_Admin.question_answer_time_limit_type('#_lty_question_answer_time_limit_type');
			} else {
				$('.lty-force-question-answer-field').closest('p').hide();
			}
		},

		incorrectly_selected_answer: function ($this) {
			if ($($this).is(':checked')) {
				$('#_lty_validate_correct_answer').closest('p').hide();
				$('.lty-verify-answer-field').closest('p').hide();
				$('#_lty_question_answer_attempts').closest('p').hide();
			} else {
				$('#_lty_validate_correct_answer').closest('p').show();
				LTY_Admin.validate_correct_answer('#_lty_validate_correct_answer');
			}
		},

		validate_correct_answer: function ($this) {
			if ($($this).is(':checked')) {
				$('#_lty_restrict_incorrectly_selected_answer').closest('p').hide();
				$('.lty-verify-answer-field').closest('p').show();
				LTY_Admin.verify_answer_type('#_lty_verify_answer_type');
			} else {
				$('.lty-verify-answer-field').closest('p').hide();
				$('#_lty_restrict_incorrectly_selected_answer').closest('p').show();
			}
		},

		verify_answer_type: function ($this) {
			if ('1' == $($this).val()) {
				$('#_lty_question_answer_attempts').closest('p').show();
			} else {
				$('#_lty_question_answer_attempts').closest('p').hide();
			}
		},

		question_answer_time_limit_type: function ($this) {
			if ('2' == $($this).val()) {
				$('.lty-question-answer-time-limit-number').closest('p').show();
			} else {
				$('.lty-question-answer-time-limit-number').closest('p').hide();
			}
		},

		/**
		 * Handle the question answer first dropdown option as default option.
		 * 
		 * @since 10.2.0
		 * @param {object} $this 
		 */
		handle_question_answer_first_option_as_default_option: function ($this) {
			$('.lty-question-answer-first-option-as-default-option').closest('p').hide();
			if ('2' === $($this).val()) {
				$('.lty-question-answer-first-option-as-default-option').closest('p').show();
			}
		},

		ticket_generation_type: function ($this) {
			if ('1' == $($this).val()) {
				$('.lty_user_selection_ticket_fields').closest('p').hide();
				$('#_lty_ticket_number_type').closest('p').show();
				LTY_Admin.ticket_number_type('#_lty_ticket_number_type');

				$('li.lty_predefined_buttons_tab').show();
				$('li.lty_predefined_buttons_tab').addClass('show_if_lottery').removeClass('hide_if_lottery');
				$('#_lty_alphabet_with_sequence_nos_enabled').closest('p').hide();
				$('#_lty_alphabet_with_sequence_nos_type').closest('p').hide();
				$('#_lty_ticket_range_slider_type').closest('p').show();
				$('.lty-preset-tickets-fields').closest('p').show();
				$('.lty-lucky-dip-fields').closest('p').hide();
			} else {
				$('#_lty_ticket_number_type').closest('p').hide();
				$('._lty_automatic_type_start_number').closest('p').hide();
				$('.lty_user_selection_ticket_fields').closest('p').show();
				$('#_lty_alphabet_with_sequence_nos_enabled').closest('p').show();
				LTY_Admin.alphabet_with_sequence_nos_enabled('#_lty_alphabet_with_sequence_nos_enabled');
				$('#_lty_ticket_range_slider_type').closest('p').hide();
				$('.lty-preset-tickets-fields').closest('p').hide();
				$('li.lty_predefined_buttons_tab').hide();
				$('li.lty_predefined_buttons_tab').addClass('hide_if_lottery').removeClass('show_if_lottery');
				LTY_Admin.handle_view_more_lottery_tickets_per_tab_field('#_lty_view_more_tickets_per_tab');
				LTY_Admin.handle_lucky_dip_fields('.lty-lucky-dip');
			}

			$(document.body).trigger('lty_ticket_generation_type_change');
		},

		ticket_number_type: function ($this) {
			if ('2' == $($this).val()) {
				$('._lty_ticket_prefix').closest('p').show();
				$('._lty_ticket_suffix').closest('p').show();
				$('._lty_automatic_type_start_number').closest('p').hide();
				$('._lty_ticket_sequential_start_number').closest('p').show();
			} else if ('3' == $($this).val()) {
				$('._lty_ticket_prefix').closest('p').show();
				$('._lty_ticket_suffix').closest('p').show();
				$('._lty_automatic_type_start_number').closest('p').hide();
				$('._lty_ticket_shuffled_start_number').closest('p').show();
			} else {
				$('._lty_ticket_prefix').closest('p').hide();
				$('._lty_ticket_suffix').closest('p').hide();
				$('._lty_automatic_type_start_number').closest('p').hide();
			}
		},

		winner_product_selection: function ($this) {
			if ('1' == $($this).val()) {
				$('#_lty_selected_gift_products').closest('p').show();
				$('#_lty_winner_outside_gift_items').closest('p').hide();
			} else {
				$('#_lty_selected_gift_products').closest('p').hide();
				$('#_lty_winner_outside_gift_items').closest('p').show();
			}
		},

		hide_countdown_timer_selection_type: function ($this) {
			if ('1' == $($this).val()) {
				$('#_lty_hide_countdown_timer_in_shop').closest('p').hide();
				$('#_lty_hide_countdown_timer_in_single_product').closest('p').hide();
			} else {
				$('#_lty_hide_countdown_timer_in_shop').closest('p').show();
				$('#_lty_hide_countdown_timer_in_single_product').closest('p').show();
			}
		},

		hide_progress_bar_selection_type: function ($this) {
			if ('1' == $($this).val()) {
				$('#_lty_hide_progress_bar_in_shop').closest('p').hide();
				$('#_lty_hide_progress_bar_in_single_product').closest('p').hide();
			} else {
				$('#_lty_hide_progress_bar_in_shop').closest('p').show();
				$('#_lty_hide_progress_bar_in_single_product').closest('p').show();
			}
		},

		relist_finished_lottery: function ($this) {
			$('.lty-relist-finished-lottery').closest('p').hide();
			$('.lty-finished-lottery-relist-pause-duration').closest('p').hide();
			$('.lty-finished-lottery-relist-count').closest('p').hide();
			if ($($this).is(':checked')) {
				$('.lty-relist-finished-lottery').closest('p').show();
				LTY_Admin.finished_lottery_relist_pause('#_lty_finished_lottery_relist_pause');
				LTY_Admin.finished_lottery_relist_count_type('#_lty_finished_lottery_relist_count_type');
				if ('2' === $('#lty_lottery_schedule_type').val()) {
					$('#_lty_finished_lottery_relist_duration').closest('p').hide();
				}
			}
		},

		finished_lottery_relist_pause: function ($this) {
			$('.lty-finished-lottery-relist-pause-duration').closest('p').hide();
			if ($($this).is(':checked')) {
				$('.lty-finished-lottery-relist-pause-duration').closest('p').show();
			}
		},

		finished_lottery_relist_count_type: function ($this) {
			$('.lty-finished-lottery-relist-count').closest('p').hide();
			if ('2' === $($this).val()) {
				$('.lty-finished-lottery-relist-count').closest('p').show();
			}
		},

		relist_failed_lottery: function ($this) {
			$('.lty-relist-failed-lottery').closest('p').hide();
			$('.lty-failed-lottery-relist-pause-duration').closest('p').hide();
			$('.lty-failed-lottery-relist-count').closest('p').hide();
			if ($($this).is(':checked')) {
				$('.lty-relist-failed-lottery').closest('p').show();
				LTY_Admin.failed_lottery_relist_pause('#_lty_failed_lottery_relist_pause');
				LTY_Admin.failed_lottery_relist_count_type('#_lty_failed_lottery_relist_count_type');
				if ('2' === $('#lty_lottery_schedule_type').val()) {
					$('#_lty_failed_lottery_relist_duration').closest('p').hide();
				}
			}
		},

		failed_lottery_relist_pause: function ($this) {
			$('.lty-failed-lottery-relist-pause-duration').closest('p').hide();
			if ($($this).is(':checked')) {
				$('.lty-failed-lottery-relist-pause-duration').closest('p').show();
			}
		},

		failed_lottery_relist_count_type: function ($this) {
			$('.lty-failed-lottery-relist-count').closest('p').hide();
			if ('2' === $($this).val()) {
				$('.lty-failed-lottery-relist-count').closest('p').show();
			}
		},

		enable_predefined_button: function ($this) {
			if ($($this).is(':checked')) {
				$('.lty-predefined-buttons-label').closest('p').show();
				$('.lty-hide-predefined-buttons-data').show();
				$('._lty_predefined_buttons_selection_type').closest('p').show();
				$('.lty-predefined-buttons-field').closest('p').show();
				$('#lty_predefined_buttons_discount_tag').closest('p').show();
				LTY_Admin.handle_predefined_button_with_quantity_selector('#_lty_predefined_with_quantity_selector');
				LTY_Admin.predefined_button_selection_type('._lty_predefined_buttons_selection_type');
			} else {
				$('.lty-predefined-buttons-label').closest('p').hide();
				$('._lty_predefined_buttons_selection_type').closest('p').hide();
				$('.lty-hide-predefined-buttons-data').hide();
				$('.lty-predefined-buttons-field').closest('p').hide();
				$('#lty_predefined_buttons_discount_tag').closest('p').hide();
			}
		},

		predefined_button_selection_type: function ($this) {
			if ('1' == $($this).val()) {
				$('.lty-predefined-buttons-table').find('.lty-discount-percentage').show();
				$('.lty-predefined-buttons-table').find('.lty-fixed-price').hide();
			} else {
				$('.lty-predefined-buttons-table').find('.lty-discount-percentage').hide();
				$('.lty-predefined-buttons-table').find('.lty-fixed-price').show();
			}
		},

		/**
		 * Validate predefined buttons.
		 * 
		 * @since 10.0.0 
		 * @param {event} event 
		 */
		validate_predefined_button: function (event) {
			if (!$('.lty-enable-predefined-buttons').is(':checked')) {
				return;
			}

			$('.lty-predefined-buttons-table').find('.lty-error').remove();
			var $this = $(event.currentTarget);
			var ticket_quantities = [];
			$('.lty-predefined-button-ticket-quantity').each(function () {
				var ticket_quantity = parseInt($(this).val());
				if (isNaN(ticket_quantity)) {
					return;
				}

				if (0 < $.inArray(parseInt($this.val()), ticket_quantities)) {
					$this.closest('td').append('<span class="lty-error">' + lty_admin_params.duplicate_predefined_button_quantity_message.replace('%quantity%', $this.val()) + '</span>');
					return;
				} else {
					ticket_quantities.push(ticket_quantity);
				}
			});
		},

		alphabet_with_sequence_nos_enabled: function ($this) {
			if ($($this).is(':checked')) {
				$('#_lty_alphabet_with_sequence_nos_type').closest('p').show();
			} else {
				$('#_lty_alphabet_with_sequence_nos_type').closest('p').hide();
			}
		},

		manual_winner_select: function (event) {
			event.preventDefault();
			var $this = $(event.currentTarget);
			var winners_count = $this.data('winners_count');
			if (0 == $('#lty_choose_winners').select2('data').length) {
				alert(lty_admin_params.manual_winner_empty_msg);
				return false;
			}

			if ($('#lty_choose_winners').select2('data').length != winners_count) {
				if (!confirm(lty_admin_params.lty_winner_count_msg)) {
					return false;
				}
			}

			LTY_Admin.block($this);
			var data = ({
				action: 'lty_manual_winner_select',
				product_id: $($this).data('product_id'),
				ticket_numbers: $('#lty_choose_winners').val(),
				lty_security: lty_admin_params.lty_manual_winner_select,
			});

			$.post(ajaxurl, data, function (res) {
				if (true === res.success) {
					alert(res.data.msg);
				} else {
					alert(res.data.error);
				}

				LTY_Admin.unblock($this);
			});
		},

		manual_relist: function (event) {
			event.preventDefault();
			var $this = $(event.currentTarget),
				tab = $($this).closest('#lty_lottery_tab');
			if (!confirm(lty_admin_params.lty_confirm_message)) {
				return false;
			}

			LTY_Admin.block(tab);
			var data = ({
				action: 'lty_lottery_manual_relist',
				product_id: $('#post_ID').val(),
				lty_security: lty_admin_params.lty_manual_relist_nonce,
			});

			$.post(ajaxurl, data, function (res) {
				if (true === res.success) {
					$('#_lty_start_date').val("");
					$('#_lty_end_date').val("");
					$($this).closest('p').hide();
					$('.lty_lottery_product_tab').removeClass('lty_lottery_closed_product_tab');
				} else {
					alert(res.data.error);
				}

				LTY_Admin.unblock(tab);
			});
		},
		extend_lottery: function (event) {
			event.preventDefault();
			var $this = $(event.currentTarget),
				tab = $($this).closest('#lty_lottery_tab');
			if (!confirm(lty_admin_params.lty_confirm_message)) {
				return false;
			}

			LTY_Admin.block(tab);
			var data = ({
				action: 'lty_lottery_extend',
				product_id: $('#post_ID').val(),
				lty_security: lty_admin_params.lty_extend_nonce,
			});

			$.post(ajaxurl, data, function (res) {
				if (true === res.success) {
					$($this).closest('p').hide();
					$('#_lty_end_date').val("");
					$('.lty_lottery_product_tab').removeClass('lty_lottery_closed_product_tab');
				} else {
					alert(res.data.error);
				}

				LTY_Admin.unblock(tab);
			});
		},

		prevent_action_delete: function (event) {
			// Prevent deleting ticket post entries.
			if (!confirm(lty_admin_params.lty_confirm_message)) {
				event.preventDefault();
				return false;
			}
		},

		/**
		 * Prevent remove instant winner action.
		 * 
		 * @since 10.6.0  
		 * @param {object} event
		 */
		prevent_remove_instant_winner_action: function (event) {
			if (!confirm(lty_admin_params.lty_remove_instant_winner_message)) {
				event.preventDefault();
				return false;
			}
		},

		/**
		 * Prevent delete instant winner action.
		 * 
		 * @since 10.6.0  
		 * @param {object} event
		 */
		prevent_delete_instant_winner_action: function (event) {
			if (!confirm(lty_admin_params.lty_delete_instant_winner_message)) {
				event.preventDefault();
				return false;
			}
		},

		add_answer: function (event) {
			event.preventDefault();
			var $this = $(event.currentTarget),
				answer_options_wrapper = $($this).closest('.lty-question-answer-wrapper'),
				answer_option_id = parseFloat(answer_options_wrapper.find('.lty-question-answer-id:last').val()),
				answer_option_template = wp.template('lty-question-answer');

			answer_option_id = answer_option_id + 1 || 0;
			answer_options_wrapper.find('tbody').append(answer_option_template({ answer_option_id: answer_option_id }));
		},

		remove_answer: function (event) {
			event.preventDefault();
			var $this = $(event.currentTarget);
			$($this).closest('tr').remove();
		},

		select_answer: function (event) {
			event.preventDefault();
			var $this = $(event.currentTarget);
			$($this).closest('tbody').find('.lty-select-answer').prop("checked", false);
			$($this).prop("checked", true);
		},

		add_predefined_button: function (event) {
			event.preventDefault();
			var $this = $(event.currentTarget),
				predefined_buttons_wrapper = $($this).closest('.lty-predefined-buttons-wrapper'),
				predefined_button_id = parseFloat(predefined_buttons_wrapper.find('.lty-predefined-rule-id:last').val()),
				predefined_button_template = wp.template('lty-predefined-button');

			predefined_button_id = predefined_button_id + 1 || 0;
			predefined_buttons_wrapper.find('tbody').append(predefined_button_template({ predefined_button_id: predefined_button_id }));
			LTY_Admin.predefined_button_selection_type('._lty_predefined_buttons_selection_type');
		},

		remove_predefined_button: function (event) {
			event.preventDefault();
			$($(event.currentTarget)).closest('tr').remove();
		},

		lottery_button_alert_message: function (event) {
			if (!confirm(lty_admin_params.lty_confirm_message)) {
				return false;
			}
		},

		/**
		 * Handles the confirmation alert for bulk actions.
		 * 
		 * @since 10.2.0
		 * @param {object} event 
		 */
		bulk_action_confirmation: function (event) {
			if (!confirm(lty_admin_params.lty_confirm_message)) {
				return false;
			}
		},

		prevent_lottery_product_save: function (event) {
			if ('lottery' === $('#product-type').val()) {
				// Return if the lottery is not extended or relisted.
				if (0 < $('input[name="lty_lottery_extend"]').length || 0 < $('input[name="lty_lottery_manual_relist"]').length) {
					return true;
				}

				var error_message = false;
				var start_date = $('#_lty_start_date'),
					end_date = $('#_lty_end_date'),
					minimum_tickets = $('#_lty_minimum_tickets'),
					maximum_tickets = $('#_lty_maximum_tickets'),
					min_tickets_per_user = $('#_lty_user_minimum_tickets'),
					max_tickets_per_user = $('#_lty_user_maximum_tickets'),
					max_tickets_per_order = $('#_lty_order_maximum_tickets'),
					winner_count = $('#_lty_winners_count'),
					ticket_price_type = $('#_lty_ticket_price_type'),
					regular_price = $('#_lty_regular_price'),
					item_selection_method = $('#_lty_winning_product_selection'),
					selected_gift_products = $('#_lty_selected_gift_products'),
					ticket_selection = $('#_lty_choose_ticket_numbers_for_users'),
					ticket_generation_type = $('#_lty_ticket_generation_type'),
					tickets_per_tab = $('#_lty_tickets_per_tab'),
					relist_finished_lottery = $('#_lty_relist_finished_lottery'),
					finished_relist_duration = $('#_lty_finished_lottery_relist_duration'),
					finished_relist_pause = $('#_lty_finished_lottery_relist_pause'),
					finished_relist_pause_duration = $('#_lty_finished_lottery_relist_pause_duration'),
					finished_relist_count_type = $('#_lty_finished_lottery_relist_count_type'),
					finished_relist_count = $('#_lty_finished_lottery_relist_count'),
					relist_failed_lottery = $('#_lty_relist_failed_lottery'),
					failed_relist_duration = $('#_lty_failed_lottery_relist_duration'),
					failed_relist_pause = $('#_lty_failed_lottery_relist_pause'),
					failed_relist_pause_duration = $('#_lty_failed_lottery_relist_pause_duration'),
					failed_relist_count_type = $('#_lty_failed_lottery_relist_count_type'),
					failed_relist_count = $('#_lty_failed_lottery_relist_count'),
					range_slider_type = $('#_lty_ticket_range_slider_type'),
					preset_tickets = $('#_lty_preset_tickets'),
					lottery_status = $('.lty-lottery-status').val();

				if ('2' !== $('#lty_lottery_schedule_type').val()) {
					// Prevent start date value is empty.
					if ('' === start_date.val()) {
						error_message = start_date.data('error');
					}

					// Prevent end date value is empty.
					if ('' === end_date.val() && !error_message) {
						error_message = end_date.data('error');
					}

					var start_time = new Date(start_date.val()).getTime(),
						end_time = new Date(end_date.val()).getTime(),
						local_time = new Date();
					// Convert local time to UTC time
					var current_time = new Date(local_time.getUTCFullYear(), local_time.getUTCMonth(), local_time.getUTCDate(),
						local_time.getUTCHours(), local_time.getUTCMinutes(), local_time.getUTCSeconds()).getTime();

					// Prevent end date value not less than start date.
					if (parseInt(start_time) > parseInt(end_time) && !error_message) {
						error_message = lty_admin_params.lty_date_error_message;
					}

					if (parseInt(end_time) < parseInt(current_time) && 'lty_lottery_closed' !== lottery_status && !error_message) {
						error_message = lty_admin_params.end_date_error_message;
					}
				}

				// Prevent minimum ticket value is empty.
				if ('' === minimum_tickets.val() && !error_message) {
					error_message = minimum_tickets.data('error');
				}

				// Prevent maximum ticket value is empty.
				if ('' === maximum_tickets.val() && !error_message) {
					error_message = maximum_tickets.data('error');
				}

				// Prevent maximum ticket per value is empty.
				if ('3' !== lty_admin_params.guest_participation_type && '' == max_tickets_per_user.val() && !error_message) {
					error_message = max_tickets_per_user.data('error');
				}

				// Prevent winner count value is empty.
				if ('' === winner_count.val() && !error_message) {
					error_message = winner_count.data('error');
				}

				if (parseInt(minimum_tickets.val()) > parseInt(maximum_tickets.val()) && !error_message) {
					error_message = lty_admin_params.lty_max_ticket_error_message;
				}

				if (max_tickets_per_order.val() && parseInt(maximum_tickets.val()) < parseInt(max_tickets_per_order.val()) && !error_message) {
					error_message = lty_admin_params.lty_max_ticket_per_order_error_message;
				}

				if ('3' !== lty_admin_params.guest_participation_type && parseInt(min_tickets_per_user.val()) > parseInt(max_tickets_per_user.val())) {
					error_message = lty_admin_params.lty_min_ticket_per_user_error_message;
				}

				if (parseInt(winner_count.val()) > parseInt(maximum_tickets.val()) && !error_message) {
					error_message = lty_admin_params.lty_winner_count_error_message;
				}

				if (parseInt(winner_count.val()) > parseInt(minimum_tickets.val()) && !error_message) {
					error_message = lty_admin_params.lty_min_ticket_error_message;
				}

				if ('1' === ticket_generation_type.val() && '' !== parseInt(preset_tickets.val()) && !error_message) {
					if ('3' !== lty_admin_params.guest_participation_type && parseInt(preset_tickets.val()) < parseInt(min_tickets_per_user.val())) {
						error_message = lty_admin_params.preset_min_qty_per_user_error_message;
					} else if ('3' !== lty_admin_params.guest_participation_type && '1' === range_slider_type.val() && parseInt(preset_tickets.val()) > parseInt(max_tickets_per_user.val())) {
						error_message = lty_admin_params.preset_max_qty_per_user_error_message;
					} else if (parseInt(preset_tickets.val()) > parseInt(maximum_tickets.val())) {
						error_message = lty_admin_params.preset_max_qty_error_message;
					} else if ('' !== parseInt(max_tickets_per_order.val()) && parseInt(preset_tickets.val()) > parseInt(max_tickets_per_order.val())) {
						error_message = lty_admin_params.preset_max_qty_per_order_error_message;
					}
				}

				// Prevent Regular value is empty.
				if ('1' === ticket_price_type.val() && (null === regular_price.val() || '' === regular_price.val()) && !error_message) {
					error_message = regular_price.data('error');
				}

				if ('2' === ticket_generation_type.val() && '' === tickets_per_tab.val() && !error_message) {
					error_message = lty_admin_params.no_of_tickets_per_tab_empty_error;
				}

				if ('1' === item_selection_method.val() && null === selected_gift_products.val() && !error_message) {
					// Gift product field value is empty.
					error_message = selected_gift_products.data('error');
				} else if ('2' === item_selection_method.val() && !$('#_lty_winner_outside_gift_items').val() && !error_message) {
					// Gift outside product field value is empty.
					error_message = lty_admin_params.lty_winner_outside_gift_error_message;
				}

				// Prevent the correct answer is empty.
				if ($("#_lty_manage_question").is(':checked') && $('.lty-select-answer').length && !error_message) {

					var checked = true;
					$('.lty-select-answer').each(function () {
						if ($(this).is(":checked")) {
							checked = false;
						}
					});
					if (checked) {
						error_message = lty_admin_params.lty_pick_answer_error_message;
					}
				}

				if (true === ticket_selection.is(':checked')) {
					if (isNaN($('#_lty_ticket_start_number').val()) && !error_message) {
						error_message = lty_admin_params.lty_ticket_start_number_error_message;
					}
				}

				if (!error_message && 'yes' !== lty_admin_params.woo_stock_management_enabled) {
					error_message = lty_admin_params.woo_stock_management_error_message;
				}

				if (!error_message && '1' === ticket_generation_type.val() && '1' === $('._lty_ticket_number_type').val() && parseInt(maximum_tickets.val()) > lty_admin_params.random_max_ticket) {
					error_message = lty_admin_params.random_ticket_length_error;
				}

				if ('2' !== $('#lty_lottery_schedule_type').val()) { // Limited.
					if (!error_message && relist_finished_lottery.is(':checked') && '' === finished_relist_duration.val()) {
						error_message = finished_relist_duration.data('error');
					}

					if (!error_message && relist_finished_lottery.is(':checked') && finished_relist_pause.is(':checked') && '' === finished_relist_pause_duration.val()) {
						error_message = finished_relist_pause_duration.data('error');
					}

					if (!error_message && relist_failed_lottery.is(':checked') && '' === failed_relist_duration.val()) {
						error_message = failed_relist_duration.data('error');
					}

					if (!error_message && relist_failed_lottery.is(':checked') && failed_relist_pause.is(':checked') && '' === failed_relist_pause_duration.val()) {
						error_message = failed_relist_pause_duration.data('error');
					}
				}

				if (!error_message && '2' === finished_relist_count_type.val() && '' === finished_relist_count.val()) {
					error_message = finished_relist_count.data('error');
				}

				if (!error_message && '2' === failed_relist_count_type.val() && '' === failed_relist_count.val()) {
					error_message = failed_relist_count.data('error');
				}

				// Check is instant winner rules are saved.
				if ($('.lty-unsaved-instant-winner-rules').val()) {
					error_message = lty_admin_params.instant_winner_rules.save_alert_msg;
				}

				// Check is instant winner prize groups are saved.
				if ($('.lty-unsaved-instant-winner-prize-groups').val()) {
					error_message = lty_admin_params.instant_winner_prize_groups.save_alert_msg;
				}

				// Check is duplicate ticket quantities entered in the predefined buttons.
				if ($('.lty-enable-predefined-buttons').is(':checked')) {
					var ticket_quantities = $('.lty-predefined-button-ticket-quantity').map(function () {
						return parseInt($(this).val());
					}).get();

					var unique_ticket_quantity = [...new Set(ticket_quantities)];
					if (ticket_quantities.length !== unique_ticket_quantity.length) {
						error_message = lty_admin_params.predefined_button_quantity_error_message;
					}
				}

				if (error_message) {
					alert(error_message);
					event.preventDefault();
					return false;
				}

			}
		},

		orders_without_tickets_popup: function (event) {
			event.preventDefault();
			var $this = $(event.currentTarget);

			LTY_Admin.block($($this));
			var data = ({
				action: 'lty_orders_without_tickets_popup',
				product_id: $this.data('product_id'),
				lty_security: lty_admin_params.lty_orders_without_tickets_nonce
			});

			$.post(ajaxurl, data, function (res) {
				if (true === res.success) {
					// Backbone Modal for display popup.
					$(this).WCBackboneModal({
						template: 'lty-orders-without-tickets-backbone-modal',
						variable: res.data
					});

					LTY_Admin.unblock($($this));

					return false;
				} else {
					alert(res.data.error);
					LTY_Admin.unblock($($this));
				}
			});

			return false;
		},

		order_status_action: function (event) {
			event.preventDefault();
			var $this = $(event.currentTarget);

			LTY_Admin.block($($this));
			var data = ({
				action: 'lty_order_status_action',
				product_id: $this.closest('.lty-order-status-selection-wrapper').find('.lty-orders-without-tickets-product-id').val(),
				status: $this.data('status'),
				lty_security: lty_admin_params.lty_orders_status_action_nonce
			});

			$.post(ajaxurl, data, function (res) {
				if (true === res.success) {
					$($this).closest('.lty-orders-without-tickets-popup-wrapper').find('.lty-orders-without-tickets-table-popup-wrapper').html(res.data.html);
					$($this).closest('.lty-order-status-selection-wrapper').find('.subsubsub li').each(function () {
						$(this).find('.lty-order-status-action').removeClass('current');
					});

					$($this).addClass('current');

					LTY_Admin.unblock($($this));
				} else {
					LTY_Admin.unblock($($this));
					alert(res.data.error)
				}
			});

			return false;
		},

		guest_user_participate_type_alert_message: function (event) {
			event.preventDefault();
			var $this = $(event.currentTarget);
			if ('3' == $this.val()) {
				alert(lty_admin_params.allow_guest_alert_message);
			}
		},

		/**
		 * Toggle lottery configuration info.
		 * 
		 * @since 8.6.0
		 * @param {event} event
		 */
		toggle_lottery_configuration_info: function (event) {
			event.preventDefault();
			var $this = $(event.currentTarget);
			$($this).closest('.lty-lottery-configuration').find('.lty-hidden-content').toggle();
			$($this).text(('view_more' === $($this).data('action')) ? lty_admin_params.view_less_label : lty_admin_params.view_more_label);
			$($this).data('action', ('view_more' === $($this).data('action') ? 'view_less' : 'view_more'));

			$('html, body').animate({ scrollTop: $('.lty-lottery-configuration').offset().top }, 1000);
		},

		/**
		 * Handle view more lottery tickets per tab field.
		 * 
		 * @since 8.6.0
		 * @param {Element} $this 
		 */
		handle_view_more_lottery_tickets_per_tab_field: function ($this) {
			$('#_lty_tickets_per_tab_view_more_count').closest('p').hide();
			if ($($this).is(':checked')) {
				$('#_lty_tickets_per_tab_view_more_count').closest('p').show();
			}
		},

		/**
		 * Select all instant winners rules.
		 * 
		 * @since 9.6.0
		 * @param {event} event 
		 */
		select_all_instant_winners_rules: function (event) {
			event.preventDefault();
			var $this = $(event.currentTarget);

			$('.lty-instant-winners-rules-contents').find('.lty-select-instant-winner-rule').prop('checked', $($this).is(':checked'));
		},

		/**
		 * Handle instant winners rules bulk action.
		 * 
		 * @since 9.6.0
		 * @param {event} event 
		 */
		handle_instant_winners_rules_bulk_action: function (event) {
			event.preventDefault();
			var $this = $(event.currentTarget),
				action = $($this).closest('.lty-instant-winners-rules-bulk-actions-wrapper').find('.lty-instant-winners-rules-bulk-action :selected').val(),
				product_id = $('.lty-instant-winners-rules-wrapper').find('.lty-product-id').val();

			if ('delete' !== action || !confirm(lty_admin_params.lty_delete_instant_winner_message)) {
				return false;
			}

			var instant_winners_rule_ids = [];
			$('.lty-instant-winners-rules-contents').find('.lty-select-instant-winner-rule:checked').each(function () {
				var instant_winner_rule_id = $(this).closest('tr').find('.lty-remove-instant-winner-rule').data('instant_winner_rule_id');
				instant_winners_rule_ids.push(instant_winner_rule_id);
			});

			LTY_Admin.handle_remove_instant_winner_rule(instant_winners_rule_ids, product_id);
		},

		/**
		 * Save the instant winners rules contents.
		 * 
		 * @since 9.6.0
		 * @param {event} event 
		 */
		save_instant_winners_rules: function (event) {
			event.preventDefault();
			var wrapper = $('.lty-instant-winners-rules-wrapper');

			LTY_Admin.block(wrapper);
			var instant_winners_rules = {};
			wrapper.find('.lty-select-instant-winner-rule').each(function () {
				var rule_wrapper = $(this).closest('tr'),
					rule_id = rule_wrapper.find('.lty-remove-instant-winner-rule').data('instant_winner_rule_id');
				
				instant_winners_rules[rule_id] = {
					'image_id': rule_wrapper.find('.lty-instant-winner-image-id').val(),
					'ticket_number': rule_wrapper.find('.lty-ticket-number').val(),
					'prize_type': rule_wrapper.find('.lty-instant-winner-prize-type').val(),
					'coupon_generation_type': rule_wrapper.find('.lty-instant-winner-coupon-generation-type').val(),
					'coupon_discount_type': rule_wrapper.find('.lty-instant-winner-coupon-discount-type').val(),
					'coupon_id': rule_wrapper.find('.lty-instant-winner-coupon-id').val(),
					'prize_amount': rule_wrapper.find('.lty-instant-winner-prize-amount').val(),
					'prize_group_id': rule_wrapper.find('.lty-instant-winner-prize-group-id').val(),
					'gift_product_id': rule_wrapper.find('.lty-instant-winner-gift-product-id').val(),
					'gift_product_quantity': rule_wrapper.find('.lty-instant-winner-gift-product-quantity').val(),
					'prize_message': rule_wrapper.find('.lty-instant-winner-prize-message').val(),
				};
			});

			var data = ({
				action: 'lty_save_instant_winners_rules',
				product_id: wrapper.find('.lty-product-id').val(),
				instant_winners_rules: instant_winners_rules,
				display_mode: $('#lty_instant_winner_display_mode').val(),
				lty_security: lty_admin_params.instant_winner_nonce
			});

			$.post(ajaxurl, data, function (res) {
				if (true === res.success) {
					alert(res.data.success);
					$('.lty-save-instant-winners-rules').prop('disabled', true);
					$('.lty-unsaved-instant-winner-rules').val('');
				} else {
					alert(res.data.error);
				}

				LTY_Admin.unblock(wrapper);
			});
		},

		/**
		 * Handle the lottery instant winners rules pagination content.
		 * 
		 * @since 9.6.0
		 * @param {int} current_page
		 */
		handle_lottery_instant_winners_rules_pagination_content: function (current_page) {
			var wrapper = $('.lty-instant-winners-rules-wrapper');

			LTY_Admin.block(wrapper);
			var data = ({
				action: 'lty_instant_winners_rules_pagination_content',
				product_id: $('#post_ID').val(),
				current_page: current_page,
				lty_security: lty_admin_params.instant_winner_nonce
			});

			$.post(ajaxurl, data, function (res) {
				if (true === res.success) {
					wrapper.replaceWith(res.data.html);
					$('.lty-instant-winners-rules-wrapper').find('.lty-instant-winner-prize-type').each(function () {
						LTY_Admin.handle_instant_winner_prize_type(this);
					});

					lty_enhanced_init();
					lty_tooltips_init();
					LTY_Admin.handle_instant_winner_prize_display_mode($('#lty_instant_winner_display_mode'));
				} else {
					alert(res.data.error);
				}
				LTY_Admin.unblock(wrapper);
			});
		},

		/**
		 * Allow instant winners rules to save.
		 * 
		 * @since 9.6.0
		 * @param {event} event 
		 */
		allow_instant_winners_rules_save: function (event) {
			event.preventDefault();
			$('.lty-save-instant-winners-rules').prop('disabled', false);
			$('.lty-unsaved-instant-winner-rules').val(1);
		},

		/**
		 * Handle the list table search filter fields.
		 * 
		 * @since 9.8.0
		 * @param {event} event 
		 */
		handle_list_table_search_filter_fields: function ($this) {
			$($this).closest('div').find('.lty-search-fields-wrapper').toggle();
		},

		/**
		 * Validate the lottery search fields.
		 * 
		 * @since 9.8.0
		 * @param {event} event 
		 */
		validate_search_fields: function (event) {
			var $this = $(event.currentTarget);
			if ('' == $this.closest('.tablenav').find('.lty-tickets-search-columns').val()) {
				alert(lty_admin_params.search_filter_empty_error_message);
				return false;
			}
		},

		/**
		 * Handle the lottery list table date filter fields.
		 * 
		 * @since 10.2.0
		 * @param {object} $this 
		 */
		handle_lottery_tickets_purchased_date_filter_fields: function ($this) {
			$($this).closest('.lty-list-table-date-filter-fields').find('.lty-list-table-filter-date-range-field').hide();
			if ('5' == $($this).val()) { // Specific date range.
				$($this).closest('.lty-list-table-date-filter-fields').find('.lty-list-table-filter-date-range-field').show();
			}
		},

		/**
		 * Handle the lucky dip fields.
		 * 
		 * @since 10.4.0 
		 * @param {object} $this 
		 */
		handle_lucky_dip_fields: function ($this) {
			$('.lty-lucky-dip-fields').closest('p').hide();
			if ($($this).is(':checked')) {
				$('.lty-lucky-dip-fields').closest('p').show();
			}
		},
		
		/**
		 * Handle the instant winner prize type
		 * 
		 * @since 10.6.0
		 * @param {object} $this 
		 */
		handle_instant_winner_prize_type: function ($this) {
			var wrapper = $($this).closest('tr');
			wrapper.find('.lty-instant-winner-prize-field').closest('p').hide();

			switch ($($this).val()) {
				case 'coupon':
					wrapper.find('.lty-instant-winner-coupon-generation-type').closest('p').show();
					LTY_Admin.handle_instant_winner_coupon_field(wrapper.find('.lty-instant-winner-coupon-generation-type'));
					break;

				case 'product':
					wrapper.find('.lty-instant-winner-gift-product-field').closest('p').show();
					break;

				case 'wallet':
				case 'woo_wallet':
				case 'credit':
				case 'smart_coupon':
					wrapper.find('.lty-instant-winner-prize-amount').closest('p').show();
					break;
			}
		},

		/**
		 * Handle the instant winner coupon prize fields.
		 * 
		 * @since 10.6.0
		 * @param {object} $this 
		 */
		handle_instant_winner_coupon_field: function ($this) {
			var wrapper = $($this).closest('tr');
			wrapper.find('.lty-instant-winner-coupon-field').closest('p').hide();
			if ( '1' === $($this).val() ) { // New coupon.
				wrapper.find('.lty-instant-winner-coupon-discount-type').closest('p').show();
				wrapper.find('.lty-instant-winner-prize-amount').closest('p').show();
			} else { // Existing coupon.
				wrapper.find('.lty-instant-winner-coupon-id').closest('p').show();
			}
		},

		/**
		 * Handle the predefined buttons with quantity selector fields.
		 * 
		 * @since 10.6.0
		 * @param {object} $this 
		 */
		handle_predefined_button_with_quantity_selector: function ($this) {
			$('#lty_range_slider_predefined_discount_tag').closest('p').hide();
			$('textarea[name="lty_range_slider_predefined_discount_label"]').closest('p').hide();
			if ($($this).is(':checked')) {
				$('#lty_range_slider_predefined_discount_tag').closest('p').show();
				$('textarea[name="lty_range_slider_predefined_discount_label"]').closest('p').show();
			}
		},

		/**
		 * Handle the instant winner prize display mode.
		 * 
		 * @since 11.1.0
		 * @param {object} $this 
		 */
		handle_instant_winner_prize_display_mode: function ($this) {
			var wrapper = $('.lty-instant-winners-rules-wrapper, .lty-lottery-instant-winners-rule-modal-wrapper');
			if ('2' === $this.val()) {
				$('.lty_instant_winner_prize_groups_tab').show();
				wrapper.find('.lty-instant-winner-rule-column').hide();
				wrapper.find('.lty-instant-winner-prize-group-column').show();
			} else {
				$('.lty_instant_winner_prize_groups_tab').hide();
				wrapper.find('.lty-instant-winner-rule-column').show();
				wrapper.find('.lty-instant-winner-prize-group-column').hide();
			}

			var import_button = $('.lty-import-instant-winner-rule-btn'),
				extra_data = import_button.data('extra_data');

			if (typeof extra_data === 'string') {
				extra_data = JSON.parse(extra_data);
			}

			extra_data.display_mode = $this.val();
			import_button.attr('data-extra_data', JSON.stringify(extra_data));
		},

		/**
		 * Handle instant winner prize groups bulk action.
		 * 
		 * @since 11.1.0
		 * @param {event} event 
		 */
		handle_instant_winner_prize_groups_bulk_action: function (event) {
			event.preventDefault();
			var $this = $(event.currentTarget),
				action = $($this).closest('.lty-instant-winner-prize-groups-bulk-actions-wrapper').find('.lty-instant-winner-prize-groups-bulk-action :selected').val();

			if ('delete' !== action || !confirm(lty_admin_params.lty_delete_instant_winner_message)) {
				return false;
			}

			var prize_group_ids = [];
			$('.lty-instant-winner-prize-groups-contents').find('.lty-select-instant-winner-prize-group:checked').each(function () {
				var prize_group_id = $(this).closest('tr').find('.lty-remove-instant-winner-prize-group').data('prize_group_id');
				prize_group_ids.push(prize_group_id);
			});

			LTY_Admin.handle_remove_instant_winner_prize_group(prize_group_ids);
		},

		/**
		 * Select all instant winners prize groups.
		 * 
		 * @since 11.5.0
		 * @param {object} event 
		 */
		select_all_instant_winner_prize_groups: function (event) {
			event.preventDefault();
			var $this = $(event.currentTarget);

			$('.lty-instant-winner-prize-groups-contents').find('.lty-select-instant-winner-prize-group').prop('checked', $($this).is(':checked'));
		},

		/**
		 * Display the new instant winner prize group on popup.
		 * 
		 * @since 11.1.0
		 * @param {object} event 
		 */
		display_new_instant_winner_prize_group_popup: function (event) {
			event.preventDefault();
			// Backbone Modal for displaying the popup.
			$(this).WCBackboneModal({
				template: 'lty-instant-winner-prize-group',
			});

			LTY_Admin.handle_instant_winner_prize_group_prize_type($('.lty-instant-winner-prize-group-popup-wrapper').find('.lty-instant-winner-prize-group-prize-type'));
			lty_enhanced_init();
			lty_tooltips_init();
		},

		/**
		 * Handle the instant winner prize group prize type.
		 * 
		 * @since 11.1.0
		 * @param {object} $this 
		 */
		handle_instant_winner_prize_group_prize_type: function ($this) {
			var wrapper = $($this).closest('.lty-instant-winner-prize-group-wrapper');
			wrapper.find('.lty-instant-winner-prize-group-prize-field').closest('p').hide();

			switch ($($this).val()) {
				case 'coupon':
					wrapper.find('.lty-instant-winner-prize-group-coupon-generation-type').closest('p').show();
					LTY_Admin.handle_instant_winner_prize_group_coupon_field(wrapper.find('.lty-instant-winner-prize-group-coupon-generation-type'));
					break;

				case 'product':
					wrapper.find('.lty-instant-winner-prize-group-gift-product-field').closest('p').show();
					break;

				case 'wallet':
				case 'woo_wallet':
				case 'credit':
				case 'smart_coupon':
					wrapper.find('.lty-instant-winner-prize-group-amount').closest('p').show();
					break;
			}
		},

		/**
		 * Handle the instant winner prize group coupon fields.
		 * 
		 * @since 11.1.0
		 * @param {object} $this 
		 */
		handle_instant_winner_prize_group_coupon_field: function ($this) {
			var wrapper = $($this).closest('.lty-instant-winner-prize-group-wrapper');
			wrapper.find('.lty-instant-winner-prize-group-coupon-field').closest('p').hide();
			if ( '1' === $($this).val() ) { // New coupon.
				wrapper.find('.lty-instant-winner-prize-group-coupon-discount-type').closest('p').show();
				wrapper.find('.lty-instant-winner-prize-group-amount').closest('p').show();
			} else { // Existing coupon.
				wrapper.find('.lty-instant-winner-prize-group-coupon-id').closest('p').show();
			}
		},

		/**
		 * Create a new instant winner prize group.
		 * 
		 * @since 11.1.0
		 * @param {object} event 
		 */
		create_instant_winner_prize_group: function (event) {
			event.preventDefault();
			var $this = $(event.currentTarget),
				wrapper = $($this).closest('.lty-instant-winner-prize-group-popup-wrapper'),
				prize_group = {
					'title': wrapper.find('.lty-instant-winner-prize-group-title').val(),
					'image_id': wrapper.find('.lty-instant-winner-prize-group-image-id').val(),
					'prize_type': wrapper.find('.lty-instant-winner-prize-group-prize-type').val(),
					'coupon_generation_type': wrapper.find('.lty-instant-winner-prize-group-coupon-generation-type').val(), 
					'coupon_discount_type': wrapper.find('.lty-instant-winner-prize-group-coupon-discount-type').val(),
					'prize_amount': wrapper.find('.lty-instant-winner-prize-group-amount').val(),
					'coupon_id': wrapper.find('.lty-instant-winner-prize-group-coupon-id').val(),
					'gift_product_id': wrapper.find('.lty-instant-winner-gift-product-id').val(),
					'gift_product_quantity': wrapper.find('.lty-instant-winner-gift-product-quantity').val(),
					'prize_message': wrapper.find('.lty-instant-winner-prize-group-message').val(),
			};

			// Return if the title field is empty.
			if (!prize_group.title) {
				LTY_Admin.error_notice(wrapper, lty_admin_params.instant_winner_prize_groups.title_empty_error_msg);
				return false;
			}

			switch (prize_group.prize_type) {
				case 'coupon':
					// Return if coupon value is empty, when selecting new coupon generation type.
					if ('1' === prize_group.coupon_generation_type && !prize_group.prize_amount) {
						LTY_Admin.error_notice(wrapper, lty_admin_params.instant_winner_prize_groups.amount_empty_error_msg);
						return false;
					}

					// Return if coupon ID is empty, when selecting existing coupon generation type.
					if ('2' === prize_group.coupon_generation_type && !prize_group.coupon_id) {
						LTY_Admin.error_notice(wrapper, lty_admin_params.instant_winner_prize_groups.coupon_id_empty_error_msg);
						return false;
					}
					break;

				case 'product':
					// Return if Gift Product ID is empty.
					if (!prize_group.gift_product_id) {
						LTY_Admin.error_notice(wrapper, lty_admin_params.instant_winner_prize_groups.gift_product_id_empty_error_msg);
						return false;
					}

					// Return if Gift Product quantity is empty.
					if (!prize_group.gift_product_quantity) {
						LTY_Admin.error_notice(wrapper, lty_admin_params.instant_winner_prize_groups.gift_product_quantity_empty_error_msg);
						return false;
					}
					break;

				case 'smart_coupon':
				case 'wallet':
				case 'woo_wallet':
				case 'credit':
					// Return if wallet value is empty.
					if (!prize_group.prize_amount) {
						LTY_Admin.error_notice(wrapper, lty_admin_params.instant_winner_prize_groups.amount_empty_error_msg);
						return false;
					}
					break;
			}

			// Return if the prize message field is empty.
			if (!prize_group.prize_message) {
				LTY_Admin.error_notice(wrapper, lty_admin_params.instant_winner_prize_groups.prize_message_empty_error_msg);
				return false;
			}

			LTY_Admin.block(wrapper);
			var data = ({
				action: 'lty_create_instant_winner_prize_group',
				prize_group: prize_group,
				product_id: $('#post_ID').val(),
				lty_security: lty_admin_params.instant_winner_nonce,
			});

			$.post(ajaxurl, data, function (res) {
				if (true === res.success) {
					$('.modal-close').click();
					LTY_Admin.handle_instant_winner_prize_groups_pagination_action($('.lty-instant-winner-prize-groups-wrapper').find('.lty-last-page'));

					var current_instant_winner_rules_page = 0 < $('.lty-instant-winners-rules-wrapper').find('.lty-current-page').length ? $('.lty-instant-winners-rules-wrapper').find('.lty-current-page').val() : 1;
					LTY_Admin.handle_lottery_instant_winners_rules_pagination_content(current_instant_winner_rules_page);
				} else {
					alert(res.data.error);
				}

				LTY_Admin.unblock(wrapper);
			});
		},

		/**
		 * Allow instant winner prize groups to save.
		 * 
		 * @since 11.1.0
		 * @param {event} event 
		 */
		allow_instant_winner_prize_groups_save: function (event) {
			event.preventDefault();
			$('.lty-save-instant-winner-prize-groups').prop('disabled', false);
			$('.lty-unsaved-instant-winner-prize-groups').val(1);
		},

		/**
		 * Save the instant winner prize groups.
		 * 
		 * @since 11.1.0
		 * @param {event} event 
		 */
		save_instant_winner_prize_groups: function (event) {
			event.preventDefault();
			var wrapper = $('.lty-instant-winner-prize-groups-wrapper');

			LTY_Admin.block(wrapper);
			var prize_groups = {};
			$(wrapper).find('.lty-select-instant-winner-prize-group').each(function () {
				var prize_group_wrapper = $(this).closest('tr'),
					prize_group_id = prize_group_wrapper.find('.lty-remove-instant-winner-prize-group').data('prize_group_id');
	
				prize_groups[prize_group_id] = {
					'title': prize_group_wrapper.find('.lty-instant-winner-prize-group-title').val(),
					'image_id': prize_group_wrapper.find('.lty-instant-winner-prize-group-image-id').val(),
					'prize_type': prize_group_wrapper.find('.lty-instant-winner-prize-group-prize-type').val(),
					'coupon_generation_type': prize_group_wrapper.find('.lty-instant-winner-prize-group-coupon-generation-type').val(), 
					'coupon_discount_type': prize_group_wrapper.find('.lty-instant-winner-prize-group-coupon-discount-type').val(),
					'prize_amount': prize_group_wrapper.find('.lty-instant-winner-prize-group-amount').val(),
					'coupon_id': prize_group_wrapper.find('.lty-instant-winner-prize-group-coupon-id').val(),
					'gift_product_id': prize_group_wrapper.find('.lty-instant-winner-prize-group-gift-product-id').val(),
					'gift_product_quantity': prize_group_wrapper.find('.lty-instant-winner-prize-group-gift-product-quantity').val(),
					'prize_message': prize_group_wrapper.find('.lty-instant-winner-prize-group-message').val(),
				};
			});

			var data = ({
				action: 'lty_save_instant_winner_prize_groups',
				product_id: $('#post_ID').val(),
				prize_groups: prize_groups,
				lty_security: lty_admin_params.instant_winner_nonce
			});

			$.post(ajaxurl, data, function (res) {
				if (true === res.success) {
					alert(res.data.success);
					$('.lty-save-instant-winner-prize-groups').prop('disabled', true);
					$('.lty-unsaved-instant-winner-prize-groups').val('');
				} else {
					alert(res.data.error);
				}

				LTY_Admin.unblock(wrapper);
			});
		},

		/**
		 * Remove the instant winner prize group.
		 * 
		 * @since 11.1.0
		 * @param {object} event
		 */
		remove_instant_winner_prize_group: function (event) {
			event.preventDefault();
			var $this = $(event.currentTarget);

			if (!confirm(lty_admin_params.instant_winner_prize_groups.remove_group_alert_msg)) {
				return false;
			}

			LTY_Admin.handle_remove_instant_winner_prize_group([$($this).data('prize_group_id')]);
		},

		/**
		 * Handle remove instant winner prize groups.
		 * 
		 * @since 11.1.0
		 * @param {array} prize_group_ids
		 */
		handle_remove_instant_winner_prize_group: function (prize_group_ids) {
			if (!prize_group_ids) {
				return false;
			}

			var wrapper = $('.lty-instant-winner-prize-groups-wrapper');
			LTY_Admin.block(wrapper);
			var data = ({
				action: 'lty_remove_instant_winner_prize_group',
				prize_group_ids: prize_group_ids,
				product_id: $('#post_ID').val(),
				lty_security: lty_admin_params.instant_winner_nonce
			});

			$.post(ajaxurl, data, function (res) {
				if (true === res.success) {
					LTY_Admin.handle_instant_winner_prize_groups_pagination_action($('.lty-instant-winner-prize-groups-wrapper').find('.lty-last-page'));
					var current_instant_winner_rules_page = 0 < $('.lty-instant-winners-rules-wrapper').find('.lty-current-page').length ? $('.lty-instant-winners-rules-wrapper').find('.lty-current-page').val() : 1;
					LTY_Admin.handle_lottery_instant_winners_rules_pagination_content(current_instant_winner_rules_page);
				} else {
					alert(res.data.error);
				}

				LTY_Admin.unblock(wrapper);
			});
		},

		/**
		 * Handle the instant winner prize groups pagination action.
		 * 
		 * @since 11.1.0
		 * @param {object} $this 
		 */
		handle_instant_winner_prize_groups_pagination_action: function ($this) {
			var wrapper = $('.lty-instant-winner-prize-groups-wrapper');

			LTY_Admin.block(wrapper);
			var data = ({
				action: 'lty_instant_winner_prize_groups_pagination_action',
				product_id: $('#post_ID').val(),
				current_page: $($this).hasClass('lty-current-page') ? $($this).val() : $($this).data('page'),
				lty_security: lty_admin_params.instant_winner_nonce
			});

			$.post(ajaxurl, data, function (res) {
				if (true === res.success) {
					$(wrapper).replaceWith(res.data.html);
					$('.lty-instant-winner-prize-groups-wrapper').find('.lty-instant-winner-prize-group-prize-type').each(function () {
						LTY_Admin.handle_instant_winner_prize_group_prize_type(this);
					});

					lty_enhanced_init();
					lty_tooltips_init();
				} else {
					alert(res.data.error);
				}
				LTY_Admin.unblock(wrapper);
			});
		},

		/**
		 * Handle the lottery schedule type fields toggle.
		 * 
		 * @since 11.7.0
		 * @param {object} $this
		 */
		handle_lottery_schedule_type: function ($this) {
			if ( '2' === $($this).val() ) { // Unlimited.
				$('#_lty_end_date').closest('p').hide();
			} else { // Limited.
				$('#_lty_end_date').closest('p').show();
			}

			LTY_Admin.relist_finished_lottery('#_lty_relist_finished_lottery');
			LTY_Admin.relist_failed_lottery('#_lty_relist_failed_lottery');
		},

		/**
		 * Trigger manual lottery notification popup.
		 * 
		 * @since 12.4.0
		 * @param {object} event 
		 */
		trigger_manual_lottery_notification_popup: function (event) {
			event.preventDefault();
			var $this = $(event.currentTarget);

			if ($this.data('preview-data')) {
				$($this).WCBackboneModal({
					template: 'lty-manual-lottery-notification-modal',
					variable: { html: $this.data('preview-data'), product_id: $this.data('product_id') } 
				});
			} else {
				LTY_Admin.block($this);
				var data = ({
					action: 'lty_manual_lottery_notification_popup_content',
					product_id: $this.data('product_id'),
					lty_security: lty_admin_params.manual_lottery_notification_nonce
				});
				$.post(ajaxurl, data, function (res) {
					if (true === res.success) {
						$this.data('preview-data', res.data.html);
						$($this).WCBackboneModal({
							template: 'lty-manual-lottery-notification-modal',
							variable: { html: res.data.html, product_id: $this.data('product_id') }
						});
					} else {
						alert(res.data.error);
					}
				});
				LTY_Admin.unblock($this);
			}
		},

		/**
		 * Send manual lottery notification.
		 * 
		 * @since 12.4.0
		 * @param {object} event 
		 */
		send_manual_lottery_notification: function (event) {
			event.preventDefault();
			var wrapper = $(event.currentTarget).closest('.wc-backbone-modal');
			wrapper.find('.lty-notice').removeClass('notice-success notice-error').empty().hide();

			LTY_Admin.block(wrapper.find('.wc-backbone-modal-content'));
			var data = ({
				action: 'lty_manual_lottery_notification',
				product_id: wrapper.find('.lty-product-id').val(),
				notification_id: wrapper.find('.lty-manual-lottery-notification-id').val(),
				lty_security: lty_admin_params.manual_lottery_notification_nonce
			});
			$.post(ajaxurl, data, function (res) {
				if (true === res.success) {
					wrapper.find('.lty-notice').addClass('notice-success').html( res.data.notice ).show();
				} else {
					wrapper.find('.lty-notice').addClass('notice-error').html( res.data.error ).show();
				}

				LTY_Admin.unblock(wrapper.find('.wc-backbone-modal-content'));
			});
		},

		/**
		 * Displays the error message.
		 * 
		 * @since 11.1.0
		 * @param {object} wrapper
		 * @param {string} message
		 */
		error_notice: function (wrapper, message) {
			var error = wrapper.find('.lty-error');
			error.html(message);
			if (!error.hasClass('lty-error-notice')) {
				error.addClass('lty-error-notice');
			}
		},

		block: function (id) {
			if (!$(id).hasClass('processing')) {
				$(id).addClass('processing').block({
					message: null,
					overlayCSS: {
						background: '#fff',
						opacity: 0.7
					}
				});
			}
		},

		unblock: function (id) {
			$(id).removeClass('processing').unblock();
		},
	};

	/**
	 * Trigger the enhanced init.
	 * 
	 * @since 11.1.0
	 */
	function lty_enhanced_init() {
		$(document.body).trigger('lty-enhanced-init');
	}

	/**
	 * Trigger the tooltips init.
	 * 
	 * @since 11.1.0
	 */
	function lty_tooltips_init() {
		$(document.body).trigger('init_tooltips');
	}

	LTY_Admin.init();
});
