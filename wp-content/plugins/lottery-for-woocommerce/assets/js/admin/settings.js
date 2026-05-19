/**
 * Lottery settings.
 * 
 * @since 1.0.0
 * @global lty_admin_settings_params
 */
jQuery(function ($) {
	'use strict';

	var LTY_Admin_Settings = {
		init: function () {
			this.trigger_on_page_load();
			// Upload batch image.
			$(document).on('click', '.lty_settings_upload_badge_image_url_button', this.upload_batch_image);
			// Toggle lottery batch.
			$(document).on('change', '#lty_settings_enable_lottery_badge', this.toggle_lottery_batch);
			// Toggle cron type selection.
			$(document).on('change', '#lty_settings_cron_type_selection', this.toggle_cron_type_selection);
			// Add a lottery question answer global.
			$(document).on('click', '.lty-add-answer-global', this.add_answer_globally);
			// Remove a lottery question answer.
			$(document).on('click', '.lty-remove-answer-global', this.remove_answer_globally);
			// Select a lottery question answer.
			$(document).on('change', '.lty-select-answer-global', this.select_answer_globally);
			// Toggle Manage question global setting.
			$(document).on('change', '#lty_settings_manage_question_global_setting', this.toggle_manage_question_global_setting);
			// Toggle force answer.
			$(document).on('change', '#lty_settings_force_answer_global_setting', this.toggle_force_answer);
			// Toggle incorrectly selected answer.
			$(document).on('change', '#lty_settings_restrict_incorrectly_selected_answer_global_setting', this.toggle_incorrectly_selected_answer);
			// Toggle verify answer.
			$(document).on('change', '#lty_settings_validate_correct_answer_global_setting', this.toggle_verify_answer);
			// Toggle verify answer type.
			$(document).on('change', '#lty_settings_verify_answer_type_global', this.toggle_verify_answer_type);
			// Toggle question answer time limit type type.
			$(document).on('change', '#lty_settings_question_answer_time_limit_type', this.toggle_question_answer_time_limit_type);
			$(document).on('change', '.lty-global-question-answer-display-type', this.toggle_question_answer_first_option_as_default_option);

			// Prevent save button.
			$('form#lty_lottery_settings_form').on('submit', this.prevent_save_button);
			// Toggle progress bar percentage fields.
			$(document).on('change', '#lty_settings_restrict_progress_bar_shop_page', this.toggle_progress_bar_percentage_shop_page);
			$(document).on('change', '#lty_settings_restrict_progress_bar_single_product_page', this.toggle_progress_bar_percentage_product_page);
			$(document).on('change', '#lty_settings_display_progress_bar_percentage_shop_page', this.toggle_progress_bar_percentage_type_shop_page);
			$(document).on('change', '#lty_settings_display_progress_bar_percentage_product_page', this.toggle_progress_bar_percentage_type_product_page);
			$(document).on('change', '#lty_settings_enable_myaccount_lottery_menu', this.toggle_myaccount_lottery_menu_fields);
			// Toggle single product page lottery details
			$(document).on('change', '#lty_settings_display_ticket_logs_search', this.toggle_lottery_ticket_logs);
			$(document).on('change', '#lty_settings_allow_entry_list_pdf_download', this.toggle_entry_list_pdf_download_fields);
			$(document).on('change', '#lty_settings_download_lottery_ticket_pdf', this.toggle_lottery_ticket_pdf_download_fields);

			$(document).on('lty-init-tabs', this.tabbed_tabs).trigger('lty-init-tabs');
		},

		trigger_on_page_load: function () {
			this.lottery_batch('#lty_settings_enable_lottery_badge');
			this.cron_type_selection('#lty_settings_cron_type_selection');
			this.force_answer('#lty_settings_force_answer_global_setting');
			this.incorrectly_selected_answer('#lty_settings_restrict_incorrectly_selected_answer_global_setting');
			this.manage_question_global_setting('#lty_settings_manage_question_global_setting');
			LTY_Admin_Settings.handle_progress_bar_percentage_shop_page('#lty_settings_restrict_progress_bar_shop_page');
			LTY_Admin_Settings.handle_progress_bar_percentage_product_page('#lty_settings_restrict_progress_bar_single_product_page');
			LTY_Admin_Settings.handle_myaccount_lottery_menu_fields('#lty_settings_enable_myaccount_lottery_menu');
			LTY_Admin_Settings.handle_lottery_ticket_logs('#lty_settings_display_ticket_logs_search');
			LTY_Admin_Settings.handle_entry_list_pdf_download_fields('#lty_settings_allow_entry_list_pdf_download');
			LTY_Admin_Settings.handle_lottery_ticket_pdf_download_fields('#lty_settings_download_lottery_ticket_pdf');
		},

		tabbed_tabs: function ( ) {
			// trigger the clicked link.
			$('.lty-shortcode-tab').on('click', function (event) {
				event.preventDefault();
				var $this = $(event.currentTarget),
						wrapper = $($this).closest('.lty-shortcode-wrapper'),
						tab_wrapper = wrapper.find('.lty-shortcode-tabs-wrapper');

				$('.lty-shortcode-tab', tab_wrapper).removeClass('lty-active');
				$($this).addClass('lty-active');

				$('div.lty-shortcode-tab-content', wrapper).hide();
				$($($this).attr('href')).show();
			});

			// Trigger the first link.
			$('div.lty-shortcode-tabs-wrapper').each(function ( ) {
				$(this).find('.lty-shortcode-tab').eq(0).click( );
			});
		},

		toggle_lottery_batch: function (event) {
			event.preventDefault();
			var $this = $(event.currentTarget);
			LTY_Admin_Settings.lottery_batch($this);
		},

		toggle_cron_type_selection: function (event) {
			event.preventDefault();
			LTY_Admin_Settings.cron_type_selection($(event.currentTarget));
		},

		toggle_force_answer: function (event) {
			event.preventDefault();
			LTY_Admin_Settings.force_answer($(event.currentTarget));
		},

		toggle_incorrectly_selected_answer: function (event) {
			event.preventDefault();
			LTY_Admin_Settings.incorrectly_selected_answer($(event.currentTarget));
		},

		toggle_verify_answer: function (event) {
			event.preventDefault();
			LTY_Admin_Settings.verify_answer($(event.currentTarget));
		},

		toggle_verify_answer_type: function (event) {
			event.preventDefault();
			LTY_Admin_Settings.verify_answer_type($(event.currentTarget));
		},

		toggle_question_answer_time_limit_type: function (event) {
			event.preventDefault();
			LTY_Admin_Settings.question_answer_time_limit_type($(event.currentTarget));
		},

		/**
		 * Toggle the question answer first dropdown option as default option.
		 * 
		 * @since 10.2.0
		 * @param {event} event 
		 */
		toggle_question_answer_first_option_as_default_option: function (event) {
			event.preventDefault();
			LTY_Admin_Settings.handle_question_answer_first_option_as_default_option($(event.currentTarget));
		},

		toggle_manage_question_global_setting: function (event) {
			event.preventDefault();
			LTY_Admin_Settings.manage_question_global_setting($(event.currentTarget));
		},

		/**
		 * Toggle shop page progress bar percentage fields.
		 * 
		 * @since 8.8.0
		 * @param {event} event 
		 */
		toggle_progress_bar_percentage_shop_page: function (event) {
			event.preventDefault();
			LTY_Admin_Settings.handle_progress_bar_percentage_shop_page($(event.currentTarget));
		},

		/**
		 * Toggle product page progress bar percentage fields.
		 * 
		 * @since 8.8.0
		 * @param {event} event 
		 */
		toggle_progress_bar_percentage_product_page: function (event) {
			event.preventDefault();
			LTY_Admin_Settings.handle_progress_bar_percentage_product_page($(event.currentTarget));
		},

		/**
		 * Toggle shop page progress bar percentage type.
		 * 
		 * @since 9.1.0
		 * @param {event} event
		 */
		toggle_progress_bar_percentage_type_shop_page: function (event) {
			event.preventDefault();
			LTY_Admin_Settings.handle_progress_bar_percentage_type_shop_page($(event.currentTarget));
		},

		/**
		 * Toggle product page progress bar percentage type.
		 * 
		 * @since 9.1.0
		 * @param {event} event
		 */
		toggle_progress_bar_percentage_type_product_page: function (event) {
			event.preventDefault();
			LTY_Admin_Settings.handle_progress_bar_percentage_type_product_page($(event.currentTarget));
		},

		/**
		 * Toggle the myaccount page lottery dashboard fields.
		 * 
		 * @since 9.1.0
		 * @param {event} event 
		 */
		toggle_myaccount_lottery_menu_fields: function (event) {
			event.preventDefault();
			LTY_Admin_Settings.handle_myaccount_lottery_menu_fields($(event.currentTarget));
		},

		/**
		 * Toggle the lottery ticket logs
		 * 
		 * @since 9.4.0
		 * @param {event} event 
		 */
		toggle_lottery_ticket_logs: function (event) {
			event.preventDefault();
			LTY_Admin_Settings.handle_lottery_ticket_logs($(event.currentTarget));
		},

		/**
		 * Toggle the lottery entry list pdf download fields.
		 * 
		 * @since 9.5.0
		 * @param {event} event 
		 */
		toggle_entry_list_pdf_download_fields: function (event) {
			event.preventDefault();
			LTY_Admin_Settings.handle_entry_list_pdf_download_fields($(event.currentTarget));
		},

		/**
		 * Toggle the lottery ticket pdf download fields.
		 * 
		 * @since 9.5.0
		 * @param {event} event 
		 */
		toggle_lottery_ticket_pdf_download_fields: function (event) {
			event.preventDefault();
			LTY_Admin_Settings.handle_lottery_ticket_pdf_download_fields($(event.currentTarget));
		},

		upload_batch_image: function (event) {
			event.preventDefault();
			// Upload Batch Image.
			var file_frame;
			var $button = $(this);
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
				title: $button.data('title'),
				multiple: false,
				library: {
					type: 'image'
				},
				button: {
					text: $button.data('button')
				}
			});

			// When an image is selected, run a callback.
			file_frame.on('select', function () {
				var file_path = '';
				var selection = file_frame.state().get('selection');
				selection.map(function (attachment) {
					attachment = attachment.toJSON();
					if (attachment.url) {
						file_path = attachment.url;
					}
				});
				formfield.val(file_path);
				var img = $('<img />');
				img.attr('src', file_path);

				// Replace previous image with new one if selected.
				$('#lty_settings_upload_badge_image_url_preview').empty().append(img);
			});

			// Finally, open the modal.
			file_frame.open();
		},

		lottery_batch: function ($this) {
			if ($($this).is(':checked')) {
				$('.lty_settings_upload_badge_image_url').closest('tr').show();
			} else {
				$('.lty_settings_upload_badge_image_url').closest('tr').hide();
			}
		},

		manage_question_global_setting: function ($this) {
			if ($($this).is(':checked')) {
				$('.lty_hide_question_answer_setting').closest('tr').show();
				$('.lty-question-answer-global-table').show();
				LTY_Admin_Settings.force_answer('#lty_settings_force_answer_global_setting');
				LTY_Admin_Settings.handle_question_answer_first_option_as_default_option('.lty-global-question-answer-display-type');
			} else {
				$('.lty_hide_question_answer_setting').closest('tr').hide();
				$('.lty-question-answer-global-table').hide();
				$('.lty_hide_verify_answer_setting_global').closest('tr').hide();
			}
		},

		/**
		 * Handle the question answer first dropdown option as default option field.
		 * 
		 * @since 10.2.0
		 * @param {object} $this 
		 */
		handle_question_answer_first_option_as_default_option: function ($this) {
			$('.lty-global-question-answer-first-option-as-default-option').closest('tr').hide();
			if ('2' === $($this).val()) {
				$('.lty-global-question-answer-first-option-as-default-option').closest('tr').show();
			}
		},

		add_answer_globally: function (event) {
			event.preventDefault();
			var $this = $(event.currentTarget),
				answer_options_wrapper = $($this).closest('.lty-question-answer-global-table'),
				answer_option_id = parseFloat(answer_options_wrapper.find('.lty-question-answer-id-global:last').val()),
				answer_option_template = wp.template('lty-question-answer-global');
			answer_option_id = answer_option_id + 1 || 0;

			answer_options_wrapper.find('tbody').append(answer_option_template({ answer_option_id: answer_option_id }));
		},

		remove_answer_globally: function (event) {
			event.preventDefault();
			var $this = $(event.currentTarget);
			$($this).closest('tr').remove();
		},

		select_answer_globally: function (event) {
			event.preventDefault();
			var $this = $(event.currentTarget);
			$($this).closest('tbody').find('.lty-select-answer-global').prop("checked", false);
			$($this).prop("checked", true);
		},

		cron_type_selection: function ($this) {
			if ('1' == $($this).val()) {
				$('.lty-wp-cron-field').closest('tr').hide();
			} else {
				$('.lty-wp-cron-field').closest('tr').show();
			}
		},

		force_answer: function ($this) {
			if ($($this).is(':checked')) {
				$('.lty-force-question-answer-field').closest('tr').show();
				LTY_Admin_Settings.verify_answer('#lty_settings_validate_correct_answer_global_setting');
				LTY_Admin_Settings.question_answer_time_limit_type('#lty_settings_question_answer_time_limit_type');
			} else {
				$('.lty-force-question-answer-field').closest('tr').hide();
				$('.lty_hide_verify_answer_setting_global').closest('tr').hide();
			}
		},

		incorrectly_selected_answer: function ($this) {
			if ($($this).is(':checked')) {
				$('#lty_settings_validate_correct_answer_global_setting').closest('tr').hide();
				$('.lty_hide_verify_answer_setting_global').closest('tr').hide();
				$('#lty_settings_question_answer_attempts_global').closest('tr').hide();
			} else {
				$('#lty_settings_validate_correct_answer_global_setting').closest('tr').show();
				$('.lty_hide_verify_answer_setting_global').closest('tr').show();
				LTY_Admin_Settings.verify_answer_type('#lty_settings_verify_answer_type_global');
			}
		},

		verify_answer: function ($this) {
			if ($($this).is(':checked')) {
				$('#lty_settings_restrict_incorrectly_selected_answer_global_setting').closest('tr').hide();
				$('.lty_hide_verify_answer_setting_global').closest('tr').show();
				LTY_Admin_Settings.verify_answer_type('#lty_settings_verify_answer_type_global');
			} else {
				$('#lty_settings_restrict_incorrectly_selected_answer_global_setting').closest('tr').show();
				$('.lty_hide_verify_answer_setting_global').closest('tr').hide();
			}
		},

		verify_answer_type: function ($this) {
			if ('1' == $($this).val()) {
				$('#lty_settings_question_answer_attempts_global').closest('tr').show();
			} else {
				$('#lty_settings_question_answer_attempts_global').closest('tr').hide();
			}
		},

		question_answer_time_limit_type: function ($this) {
			if ('2' == $($this).val()) {
				$('#lty_settings_question_answer_time_limit').closest('tr').show();
			} else {
				$('#lty_settings_question_answer_time_limit').closest('tr').hide();
			}
		},

		prevent_save_button: function (event) {
			var $error_message = false;
			var cron_type = $('#lty_settings_cron_type_selection'),
				cron_value = $('#lty_settings_wp_cron_time');

			// Prevent the WP cron.
			if ('2' == cron_type.val() && '' == cron_value.val()) {
				$error_message = lty_admin_settings_params.cron_validate_error_message;
			}

			if ($error_message) {
				alert($error_message);
				event.preventDefault();
				return false;
			}
		},

		/**
		 * Handle shop page progress bar percentage fields.
		 * 
		 * @since 8.8.0
		 * @param {object} $this 
		 */
		handle_progress_bar_percentage_shop_page: function ($this) {
			$($this).closest('table').find('.lty-progress-bar-percentage-fields').closest('tr').hide();
			if ($($this).is(':checked')) {
				$('#lty_settings_display_progress_bar_percentage_shop_page').closest('tr').show();
				LTY_Admin_Settings.handle_progress_bar_percentage_type_shop_page('#lty_settings_display_progress_bar_percentage_shop_page');
			}
		},

		/**
		 * Handle product page progress bar percentage fields.
		 * 
		 * @since 8.8.0
		 * @param {object} $this 
		 */
		handle_progress_bar_percentage_product_page: function ($this) {
			$($this).closest('table').find('.lty-progress-bar-percentage-fields').closest('tr').hide();
			if ($($this).is(':checked')) {
				$('#lty_settings_display_progress_bar_percentage_product_page').closest('tr').show();
				LTY_Admin_Settings.handle_progress_bar_percentage_type_product_page('#lty_settings_display_progress_bar_percentage_product_page');
			}
		},

		/**
		 * Handle shop page progress bar percentage type.
		 * 
		 * @since 9.1.0
		 * @param {object} $this 
		 */
		handle_progress_bar_percentage_type_shop_page: function ($this) {
			$('#lty_settings_progress_bar_percentage_type_shop_page').closest('tr').hide();
			$('#lty_settings_progress_bar_percentage_display_type_shop_page').closest('tr').hide();
			if ($($this).is(':checked')) {
				$('#lty_settings_progress_bar_percentage_type_shop_page').closest('tr').show();
				$('#lty_settings_progress_bar_percentage_display_type_shop_page').closest('tr').show();
			}
		},

		/**
		 * Handle product page progress bar percentage type.
		 * 
		 * @since 9.1.0
		 * @param {object} $this 
		 */
		handle_progress_bar_percentage_type_product_page: function ($this) {
			$('#lty_settings_progress_bar_percentage_type_product_page').closest('tr').hide();
			$('#lty_settings_progress_bar_percentage_display_type_product_page').closest('tr').hide();
			if ($($this).is(':checked')) {
				$('#lty_settings_progress_bar_percentage_type_product_page').closest('tr').show();
				$('#lty_settings_progress_bar_percentage_display_type_product_page').closest('tr').show();
			}
		},

		/**
		 * Handle the myaccount page lottery menu fields.
		 * 
		 * @since 9.1.0
		 * @param {object} $this 
		 */
		handle_myaccount_lottery_menu_fields: function ($this) {
			$('#lty_settings_myaccount_lottery_menu_position').closest('tr').hide();
			if ($($this).is(':checked')) {
				$('#lty_settings_myaccount_lottery_menu_position').closest('tr').show();
			}
		},

		/**
		 * Handle lottery ticket logs.
		 * 
		 * @since 9.4.0
		 * @param {object} $this 
		 */
		handle_lottery_ticket_logs: function ($this) {
			$('.lty-ticket-logs-search').closest('tr').hide();
			if ('1' === $($this).val()) {
				$('.lty-ticket-logs-search').closest('tr').show();
			}
		},

		/**
		 * Handle the lottery entry list pdf download fields.
		 * 
		 * @since 9.5.0
		 * @param {object} $this 
		 */
		handle_entry_list_pdf_download_fields: function ($this) {
			$('.lty-entry-list-pdf-field').closest('tr').hide();
			if ($($this).is(':checked')) {
				$('.lty-entry-list-pdf-field').closest('tr').show();
			}
		},

		/**
		 * Handle the lottery ticket pdf download fields.
		 * 
		 * @since 9.5.0
		 * @param {object} $this 
		 */
		handle_lottery_ticket_pdf_download_fields: function ($this) {
			$('#lty_settings_lottery_ticket_pdf_file_name').closest('tr').hide();
			if ($($this).is(':checked')) {
				$('#lty_settings_lottery_ticket_pdf_file_name').closest('tr').show();
			}
		},

		block: function (id) {
			$(id).block({
				message: null,
				overlayCSS: { background: '#fff', opacity: 0.7 }
			});
		},

		unblock: function (id) {
			$(id).unblock();
		}

	};

	LTY_Admin_Settings.init();
});
