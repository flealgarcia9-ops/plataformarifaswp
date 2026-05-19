/* global lty_frontend_params */

jQuery(function ($) {
	'use strict';
	var ticket_numbers = [];
	var LTY_Frontend = {
		init: function () {
			this.trigger_on_page_load();
			// Choose the ticket tab.
			$(document).on('click', '.lty-lottery-ticket-tab', this.lottery_ticket_tab_selection);
			// Select the ticket.
			$(document).on('click tap', '.lty-ticket', this.lottery_ticket_selection);
			// Unselect the ticket.
			$(document).on('click tap', '.lty-selected-ticket', this.lottery_ticket_unselection);
			// Select the question answer.
			$(document).on('click', 'ul.lty-lottery-answers li', this.select_question_answer);
			// Select the predefined button.
			$(document).on('click', 'ul.lty-predefined-buttons li', this.select_predefined_button);
			// Increase the quantity.
			$(document).on('click', '.lty-lottery-range-slider-increment', this.increase_rangeslider_quantity);
			// Decrease the quantity.
			$(document).on('click', '.lty-lottery-range-slider-decrement', this.decrease_rangeslider_quantity);
			// Validate the participate now button. 
			$(document).on('click', '.lty-participate-now-button', this.validate_participate);
			// Process the ticket lucky dip.
			$(document).on('click', '.lty-add-to-cart-lucky-dip-button', this.process_lucky_dip);
			// Process the add more ticket lucky dip.
			$(document).on('click', '.lty-add-more-lucky-tip', this.add_more_lucky_dip);
			// Trigger the regenerate lucky dip popup.
			$(document).on('click', '.lty-regenerate-lucky-dip-button', this.trigger_regenerate_lucky_dip_popup);
			// Trigger the regenerate lucky dip add to cart.
			$(document).on('click', '.lty-regenerate-lucky-dip-add-to-cart-button', this.trigger_regenerate_lucky_dip_add_to_cart);
			// Sync the regenerate lucky dip quantity.
			$(document).on('change', '.lty-lucky-dip-quantity', this.sync_regenerate_lucky_dip_quantity);
			// Process the manual search action.
			$(document).on('click', '.lty-manual-ticket-search-action', this.process_manual_ticket_search_action);
			// Process the click to back action.
			$(document).on('click', '.lty-manual-ticket-click-to-back-action', this.process_manual_ticket_click_to_back_action);
			// Process the lottery price based on qty.
			$(document).on('change keyup', '.lty-participate-now .qty', this.process_lottery_price_based_on_qty);
			// Pagination.
			$(document).on('click', '.lty-frontend-table .lty-pagination', this.pagination);
			$(document).on('click', '.lty-lottery-winners-by-date-wrapper .lty-pagination', this.winners_by_date_pagination);
			$(document).on('click', '.lty-lottery-instant-winners-by-date-wrapper .lty-pagination', this.instant_winners_by_date_pagination);
			// Search ticket logs.
			$(document).on('click', '.lty-ticket-logs-search-btn', this.search_ticket_logs);
			$(document).on('click', '.lty-toggle-lottery-tickets', this.toggle_lottery_tickets_per_tab);
			// Popup dashboard lottery tickets.
			$(document).on('click', '.lty-view-all-tickets', this.display_customer_popup_lottery_tickets);
			$(document).on('click', '.lty-order-instant-winners-wrapper .lty-pagination', this.order_instant_winners_pagination);
			$(document).on('click', '.lty-instant-winner-prize-group-item-header', this.toggle_instant_winner_prize_group);
			$(document).on('click', '.lty-instant-winner-prize-group-pagination .lty-pagination', this.instant_winner_prize_group_pagination);
			$(document).on('click', '.lty-instant-winner-prize-group-ticket-pagination .lty-pagination', this.instant_winner_prize_group_ticket_pagination);
		},

		trigger_on_page_load: function () {
			LTY_Frontend.set_price_amount($('.lty-participate-now .qty').val());

			if ('yes' == lty_frontend_params.disable_participate_now_button) {
				$('.lty_manual_add_to_cart').attr('disabled', true);
			}

			$('.lty-manual-ticket-click-to-back-action').hide();

			LTY_Frontend.handle_add_cart_button();
			LTY_Frontend.initialize_range_slider();
			LTY_Frontend.hide_lottery_tickets();

			$('input[name="quantity"]').each(function () {
				var product_id = $(this).closest('.product').find('.add_to_cart_button').data('product_id') || $(this).closest('form').find('input[name="add-to-cart"]').val();
				LTY_Frontend.maybe_select_predefined_button($(this).val(), product_id);
			});
		},

		lottery_ticket_tab_selection: function (event) {
			event.preventDefault();
			LTY_Frontend.handle_lottery_ticket_tab_selection($(event.currentTarget));
		},

		select_predefined_button: function (event) {
			event.preventDefault();
			LTY_Frontend.handle_select_predefined_button($(event.currentTarget));
		},

		process_lottery_price_based_on_qty: function (event) {
			event.preventDefault();
			LTY_Frontend.set_price_amount($(event.currentTarget).val());
			LTY_Frontend.maybe_select_predefined_button($(event.currentTarget).val(), $(event.currentTarget).closest('form').find('input[name="add-to-cart"]').val());
		},

		initialize_range_slider: function () {
			var fields = $('.lty-quantity-range-slider:not(.ui-slider)');
			if (!fields.length) {
				return false;
			}

			fields.each(function () {
				var field = $(this);
				field.slider({
					range: 'min',
					min: field.data('min'),
					max: field.data('max'),
					value: field.data('preset'),
					slide: function (event, ui) {
						var wrapper = $(this).closest('.lty-lottery-range-slider-wrapper');

						wrapper.find('.lty-quantity-selector').val(ui.value);
						wrapper.find('.lty-range-slider-current-value').text(ui.value);

						var percentage = ((ui.value - field.data('min')) / (field.data('max') - field.data('min'))) * 100;
						wrapper.find('.lty-lottery-range-value').css({ 'left': percentage + '%' });

						LTY_Frontend.set_price_amount(ui.value);
					},
					change: function (event, ui) {
						var wrapper = $(this).closest('.lty-lottery-range-slider-wrapper');

						wrapper.find('.lty-quantity-selector').val(ui.value);
						wrapper.find('.lty-range-slider-current-value').text(ui.value);

						var percentage = ((ui.value - field.data('min')) / (field.data('max') - field.data('min'))) * 100;
						wrapper.find('.lty-lottery-range-value').css({ 'left': percentage + '%' });

						wrapper.find('.lty-range-slider-discount').removeClass('lty-range-slider-discount-active');
						wrapper.find('.lty-range-slider-discount-' + ui.value).addClass('lty-range-slider-discount-active');

						LTY_Frontend.maybe_select_predefined_button(ui.value, field.data('product_id'));
						LTY_Frontend.set_price_amount(ui.value);
					}
				});

				var preset = field.data('preset');
				field.slider('value', preset);
			});
		},

		select_question_answer: function (e) {
			e.preventDefault();
			var $this = $(e.currentTarget),
				answers_wrapper = $($this).closest('.lty-lottery-answers'),
				answer_id = $($this).data('answer-id');
			answers_wrapper.find('li').removeClass('lty-selected');
			$($this).addClass('lty-selected');
			$('.lty-question-answer-id').val(answer_id);
			LTY_Frontend.handle_add_cart_button();
		},

		handle_select_predefined_button: function ($this) {
			var predefined_button_wrapper = $($this).closest('.lty-predefined-buttons'),
				predefined_button_id = $($this).data('predefined-button-id'),
				ticket_quantity = $($this).data('ticket-quantity'),
				per_ticket_amount = $($this).data('per-ticket-amount'),
				product_id = $($this).data('product_id'),
				product_quantity = 0 < $('.lty-product-quantity-' + product_id).length ? $('.lty-product-quantity-' + product_id) : $('.product.post-' + product_id).find('input[name="quantity"]'),
				is_predefined_button_selected = $($this).hasClass('lty-selected-button');

			// Return if the predefined button is already selected.
			if (is_predefined_button_selected && ticket_quantity == product_quantity.val()) {
				return;
			}

			predefined_button_wrapper.find('li').removeClass('lty-selected-button');

			// Deselect the predefined button.
			if (is_predefined_button_selected && ticket_quantity != product_quantity.val()) {
				predefined_button_id = '';
				per_ticket_amount = '';
			} else {
				// Select the predefined button.
				$($this).addClass('lty-selected-button');
				product_quantity.val(ticket_quantity);
			}

			$('.lty-predefined-button-id').val(predefined_button_id);
			$('.lty-per-ticket-amount').val(per_ticket_amount);
			LTY_Frontend.handle_range_slider_quantity(product_id);
			LTY_Frontend.handle_add_cart_button();
			LTY_Frontend.set_price_amount(ticket_quantity);
		},

		increase_rangeslider_quantity: function (event) {
			event.preventDefault();
			var $this = $(event.currentTarget),
				wrapper = $($this).closest('.lty-lottery-range-slider-wrapper'),
				current_value = wrapper.find('.lty-quantity-selector').val(),
				range_slider = wrapper.find('.lty-quantity-range-slider');

			if (range_slider.data('max') > current_value) {
				current_value++;
				range_slider.slider('value', current_value);
				wrapper.find('.lty-quantity-selector').val(current_value);
			}

			LTY_Frontend.maybe_select_predefined_button(current_value, range_slider.data('product_id'));
		},

		decrease_rangeslider_quantity: function (event) {
			event.preventDefault();
			var $this = $(event.currentTarget),
				wrapper = $($this).closest('.lty-lottery-range-slider-wrapper'),
				current_value = wrapper.find('.lty-quantity-selector').val(),
				range_slider = wrapper.find('.lty-quantity-range-slider');

			if (range_slider.data('min') < current_value) {
				current_value--;
				range_slider.slider('value', current_value);
				wrapper.find('.lty-quantity-selector').val(current_value);
			}

			LTY_Frontend.maybe_select_predefined_button(current_value, range_slider.data('product_id'));
		},

		/**
		 * Handle the range slider quantity.
		 * 
		 * @since 10.0.0
		 */
		handle_range_slider_quantity: function (product_id) {
			var range_sliders = $('.lty-lottery-range-slider-wrapper');

			range_sliders.each(function () {
				var wrapper = $(this),
					range_slider = wrapper.find('.lty-quantity-range-slider[data-product_id="' + product_id + '"]');

				if (!range_slider.length) {
					return;
				}

				var quantity = $('.lty-product-quantity-' + product_id).val();
				if (range_slider.data('min') > quantity || range_slider.data('max') < quantity) {
					quantity = range_slider.data('min');
				}

				if (range_slider.hasClass('ui-slider')) {
					range_slider.slider('value', quantity);
				}

				wrapper.find('.lty-quantity-selector').val(quantity);
			});
		},

		handle_add_cart_button: function () {
			var show_button = true,
				ticket_container = $('.lty-lottery-ticket-container'),
				question_container = $('.lty-lottery-question-answer-container'),
				lucky_dip_container = $('.lty-lottery-ticket-lucky-dip-container'),
				predefined_button_container = $('.lty-lottery-predefined-buttons-container');

			if (ticket_container.length && '' == ticket_container.find('.lty-lottery-ticket-numbers').val()) {
				$(".lty_manual_add_to_cart").attr("title", lty_frontend_params.ticket_selection_alert_message);
				show_button = false;
			}

			if (question_container.length && 'yes' == question_container.data('force') && '' == question_container.find('.lty-question-answer-id').val()) {
				$(".lty_manual_add_to_cart").attr("title", lty_frontend_params.question_answer_alert_message);
				show_button = false;
			}

			if (predefined_button_container.length && true == lty_frontend_params.is_predefined_buttons_enabled && '' == predefined_button_container.find('.lty-predefined-button-id').val() && false == lty_frontend_params.can_display_predefined_with_quantity_selector) {
				$(".lty_manual_add_to_cart").attr("title", lty_frontend_params.predefined_buttons_alert_message);
				show_button = false;
			}

			// Show manual add to cart button.
			if (show_button) {
				$('.lty_manual_add_to_cart').removeAttr('disabled');
				$('.lty_manual_add_to_cart').removeAttr('title');
			}

			// Show Lucky dip Button. 
			if (question_container.length && 'yes' == question_container.data('force') && '' != question_container.find('.lty-question-answer-id').val()) {
				if (lucky_dip_container.length) {
					lucky_dip_container.find('.lty-lucky-dip-button').removeAttr('disabled');
					lucky_dip_container.find('.lty-lucky-dip-button').removeAttr('title');
				}
			}
		},

		validate_participate: function (event) {
			var error_message = null;
			if ('yes' == lty_frontend_params.guest_user) {
				error_message = lty_frontend_params.guest_error_msg;
			}

			var ticket_container = $('.lty-lottery-ticket-container'),
				question_container = $('.lty-lottery-question-answer-container'),
				predefined_button_container = $('.lty-lottery-predefined-buttons-container'),
				lucky_dip_container = $('.lty-lottery-ticket-lucky-dip-container');

			if (!error_message && ticket_container.length && '' == ticket_container.find('.lty-lottery-ticket-numbers').val()) {
				error_message = lty_frontend_params.ticket_selection_alert_message;
			}

			if (!error_message && question_container.length && 'yes' == question_container.data('force') && '' == question_container.find('.lty-question-answer-id').val()) {
				error_message = lty_frontend_params.question_answer_alert_message;
			}

			if (!error_message && predefined_button_container.length && true == lty_frontend_params.is_predefined_buttons_enabled && '' == predefined_button_container.find('.lty-predefined-button-id').val() && false == lty_frontend_params.can_display_predefined_with_quantity_selector) {
				error_message = lty_frontend_params.predefined_buttons_alert_message;
			}

			if (!error_message && question_container.length && 'yes' == question_container.data('force') && '' != question_container.find('.lty-question-answer-id').val() && 'yes' != lty_frontend_params.incorrectly_selected_answer_restriction && 'yes' == lty_frontend_params.validate_correct_answer) {
				if ('yes' == lty_frontend_params.disable_answer_verification_alert || confirm(lty_frontend_params.verify_question_answer_alert_message)) {
					$(event.currentTarget).submit();
					return true;
				} else {
					event.preventDefault();
					return false;
				}
			}

			if (error_message) {
				event.preventDefault();
				$.alertable.alert(error_message);
				return false;
			}

			return true;
		},

		handle_lottery_ticket_tab_selection: function ($this) {
			var tickets_container = $($this).closest('.lty-lottery-ticket-container');
			LTY_Frontend.block(tickets_container);
			var data = ({
				action: 'lty_ticket_tab_selection',
				product_id: tickets_container.find('.lty-ticket-product-id').val(),
				tab: $this.data('tab') ? $this.data('tab') : 1,
				index: $this.data('index') ? $this.data('index') : 0,
				ticket_numbers: $('.lty-lottery-ticket-numbers').val(),
				lty_security: lty_frontend_params.lottery_tickets_nonce,
			});

			$.post(lty_frontend_params.ajaxurl, data, function (res) {
				if (true === res.success) {

					tickets_container.find('.lty-lottery-ticket-tab').removeClass('lty-active-tab');
					$($this).addClass('lty-active-tab');
					tickets_container.find('.lty-lottery-ticket-tab-content').html(res.data.html);

					// class 'lty-selected-ticket' added when ticket tab selection is clicked. 
					var $selected_ticket_numbers = res.data.ticket_numbers;
					for (var i = 0; i < $selected_ticket_numbers.length; i++) {
						$('.lty-ticket[data-ticket="' + $selected_ticket_numbers[i] + '"]').addClass('lty-selected-ticket');
					}

					LTY_Frontend.hide_lottery_tickets();
				} else {
					$.alertable.alert(res.data.error);
				}

				LTY_Frontend.unblock(tickets_container);
			});
		},

		lottery_ticket_selection: function (event) {
			event.preventDefault();
			var $this = $(event.currentTarget),
				tickets_container = $($this).closest('.lty-lottery-ticket-container'),
				selected_tickets = tickets_container.find('.lty-lottery-ticket-numbers'),
				quantity = tickets_container.find('.lty-lottery-ticket-quantity');

			if ($this.hasClass("lty-booked-ticket") || $this.hasClass("lty-processing-ticket") || $this.hasClass("lty-selected-ticket")) {
				return;
			}

			$($this).removeClass('lty-unselected-ticket');
			$($this).addClass('lty-selected-ticket');
			// Push selected ticket numbers in array.
			ticket_numbers.push($($this).data('ticket'));
			selected_tickets.val(ticket_numbers);
			quantity.val(ticket_numbers.length);
			LTY_Frontend.set_price_amount(quantity.val());
			LTY_Frontend.handle_add_cart_button();

		},

		lottery_ticket_unselection: function (event) {
			event.preventDefault();
			var $this = $(event.currentTarget),
				tickets_container = $($this).closest('.lty-lottery-ticket-container'),
				selected_tickets = tickets_container.find('.lty-lottery-ticket-numbers'),
				quantity = tickets_container.find('.lty-lottery-ticket-quantity');

			var $ticket = $($this).data('ticket');
			$($this).removeClass('lty-selected-ticket');
			$($this).addClass('lty-unselected-ticket');

			// Splice index number , delete count.
			ticket_numbers.splice(ticket_numbers.indexOf($ticket), 1);
			selected_tickets.val(ticket_numbers);
			quantity.val(ticket_numbers.length);
			LTY_Frontend.set_price_amount(quantity.val());
		},

		process_lucky_dip: function (event) {
			event.preventDefault();
			var $this = $(event.currentTarget),
				$validate_lucky_tip = LTY_Frontend.validate_lucky_dip(event);
			if (!$validate_lucky_tip) {
				return false;
			}

			LTY_Frontend.lucky_dip_action($this);
		},

		lucky_dip_action: function ($this) {
			LTY_Frontend.block($this);
			var container = $this.closest('.lty-lottery-ticket-lucky-dip-container');

			var data = ({
				action: 'lty_process_lucky_dip',
				product_id: $('.lty-ticket-product-id').val(),
				qty: container.find('.lty-lucky-dip-quantity').val(),
				answer: $('.lty-question-answer-id').val(),
				lty_security: lty_frontend_params.lottery_tickets_nonce,
			});

			$.post(lty_frontend_params.ajaxurl, data, function (res) {
				if (true === res.success) {
					// Redirect to cart option.
					if ('yes' == lty_frontend_params.enable_cart_redirection) {
						window.location = lty_frontend_params.cart_url;
						return;
					} else {
						$(res.data.html).modal();
					}
				} else {
					$.alertable.alert(res.data.error);
				}

				LTY_Frontend.unblock($this);
			});
		},

		validate_lucky_dip: function ($this) {
			var error_message = null;
			if ('yes' == lty_frontend_params.guest_user) {
				error_message = lty_frontend_params.guest_error_msg;
			}

			var ticket_container = $('.lty-lottery-ticket-container'),
				question_container = $('.lty-lottery-question-answer-container'),
				lucky_dip_container = $('.lty-lottery-ticket-lucky-dip-container');

			if (!error_message && question_container.length && 'yes' == question_container.data('force') && '' == question_container.find('.lty-question-answer-id').val()) {
				if (lucky_dip_container.length) {
					error_message = lty_frontend_params.question_answer_alert_message;
				}
			}

			if (!error_message && question_container.length && 'yes' == question_container.data('force') && '' != question_container.find('.lty-question-answer-id').val() && 'yes' != lty_frontend_params.incorrectly_selected_answer_restriction && 'yes' == lty_frontend_params.validate_correct_answer) {
				if ('yes' != lty_frontend_params.disable_answer_verification_alert && !confirm(lty_frontend_params.verify_question_answer_alert_message)) {
					$this.preventDefault();
					return false;
				}
			}

			if (error_message) {
				$this.preventDefault();
				$.alertable.alert(error_message);
				return false;
			}

			return true;
		},

		add_more_lucky_dip: function (event) {
			event.preventDefault();
			var $this = $(event.currentTarget),
				tickets_container = $('.lty-lottery-ticket-container');

			var $validate_lucky_tip = LTY_Frontend.validate_lucky_dip(event);
			if (!$validate_lucky_tip) {
				return false;
			}

			LTY_Frontend.lucky_dip_action($this, tickets_container);
		},

		/**
		 * Trigger re-generate ticket lucky dip popup.
		 * 
		 * @since 10.4.0
		 * @param {object} event 
		 */
		trigger_regenerate_lucky_dip_popup: function (event) {
			event.preventDefault();
			var $this = $(event.currentTarget);

			if (!LTY_Frontend.validate_lucky_dip(event)) {
				return false;
			}

			LTY_Frontend.block($this);
			var container = $this.closest('.lty-lottery-ticket-lucky-dip-container');
			var data = ({
				action: 'lty_process_regenerate_lucky_dip',
				product_id: $('.lty-ticket-product-id').val(),
				quantity: container.find('.lty-lucky-dip-quantity').val(),
				fixed_quantity: 'yes' === container.find('.lty-lucky-dip-fixed-quantity').val() ? 'yes' : 'no',
				lty_security: lty_frontend_params.lottery_tickets_nonce,
			});

			$.post(lty_frontend_params.ajaxurl, data, function (res) {
				if (true === res.success) {
					// Remove the existing popup if any.
					$('.lty-regenerate-ticket-lucky-dip-popup-wrapper').remove();
					$(res.data.html).modal();
				} else {
					$.alertable.alert(res.data.error);
				}

				LTY_Frontend.unblock($this);
			});
		},

		/**
		 * Trigger regenerate lucky dip add to cart.
		 * 
		 * @since 10.4.0
		 * @param {object} event 
		 */
		trigger_regenerate_lucky_dip_add_to_cart: function (event) {
			event.preventDefault();
			var $this = $(event.currentTarget),
				wrapper = $('.lty-regenerate-ticket-lucky-dip-popup-wrapper');

			if (!LTY_Frontend.validate_lucky_dip(event)) {
				return false;
			}

			LTY_Frontend.block(wrapper);
			var container = $this.closest('.lty-lottery-ticket-lucky-dip-container');
			var data = ({
				action: 'lty_regenerate_lucky_dip_add_to_cart',
				product_id: $('.lty-ticket-product-id').val(),
				quantity: container.find('.lty-lucky-dip-quantity').val(),
				answer: $('.lty-question-answer-id').val(),
				tickets: $($this).data('tickets'),
				lty_security: lty_frontend_params.lottery_tickets_nonce,
			});

			$.post(lty_frontend_params.ajaxurl, data, function (res) {
				if (true === res.success) {
					// Redirect to cart option.
					if ('yes' === lty_frontend_params.enable_cart_redirection) {
						window.location = lty_frontend_params.cart_url;
						return;
					} else {
						// Remove the existing popup if any.
						$('.lty-regenerate-ticket-lucky-dip-popup-wrapper').remove();
						$(res.data.html).modal();
					}
				} else {
					$.alertable.alert(res.data.error);
				}

				LTY_Frontend.unblock(wrapper);
			});
		},

		/**
		 * Synchronize the regenerate lucky dip quantity.
		 * 
		 * @since 10.4.0 
		 * @param {object} event 
		 */
		sync_regenerate_lucky_dip_quantity: function (event) {
			event.preventDefault();

			$('.lty-lucky-dip-quantity').val($(event.currentTarget).val());
		},

		set_price_amount: function (quantity) {
			quantity = quantity > 1 ? quantity : 1;

			var price = LTY_Frontend.format_number(LTY_Frontend.get_per_ticket_price(), quantity),
				price_html = LTY_Frontend.get_formatted_price(price);

			$('.lty-lottery-price').html(price_html);
			$('.lty-ticket-quantity').html(quantity);
		},

		format_number: function (price, quantity) {
			return accounting.formatNumber(parseFloat(price) * quantity, lty_frontend_params.decimals, lty_frontend_params.thousand_separator, lty_frontend_params.decimal_separator);
		},

		/**
		 * Get the formatted price html.
		 * 
		 * @since 10.9.0
		 * @param {float} price 
		 * @return {html}
		 */
		get_formatted_price: function (price) {
			switch (lty_frontend_params.currency_pos) {
				case 'right_space':
					return '<span class="woocommerce-Price-amount amount">' + price + '<span class="woocommerce-Price-currencySymbol"> ' + lty_frontend_params.currency + '</span></span>';

				case 'right':
					return '<span class="woocommerce-Price-amount amount">' + price + '<span class="woocommerce-Price-currencySymbol">' + lty_frontend_params.currency + '</span></span>';

				case 'left_space':
					return '<span class="woocommerce-Price-amount amount"><span class="woocommerce-Price-currencySymbol">' + lty_frontend_params.currency + '</span> ' + price + '</span>';

				default:
					return '<span class="woocommerce-Price-amount amount"><span class="woocommerce-Price-currencySymbol">' + lty_frontend_params.currency + '</span>' + price + '</span>';
			}
		},

		/**
		 * Get per ticket price amount.
		 * 
		 * @since 10.9.0
		 * @return float
		 */
		get_per_ticket_price: function () {
			var predefined_buttons_wrapper = $('.lty-predefined-buttons');

			// Check if any predefined button is selected.
			if (0 < predefined_buttons_wrapper.length && 0 < predefined_buttons_wrapper.find('.lty-selected-button').length) {
				return predefined_buttons_wrapper.find('.lty-selected-button').data('per-ticket-amount');
			}

			return $('.lty-lottery-price').data('price-amount');
		},

		process_manual_ticket_search_action: function (event) {
			event.preventDefault();
			var $this = $(event.currentTarget),
				tickets_container = $($this).closest('.lty-lottery-ticket-container'),
				$searched_value = $('.lty-manual-ticket-search').val();
			if (!$searched_value) {
				$.alertable.alert(lty_frontend_params.manual_ticket_search_empty_error);
				return false;
			}

			LTY_Frontend.block(tickets_container);

			var data = ({
				action: 'lty_manual_ticket_search_action',
				searched_value: $searched_value,
				product_id: tickets_container.find('.lty-ticket-product-id').val(),
				lty_security: lty_frontend_params.lottery_manual_ticket_search_action_nonce,
			});

			$.post(lty_frontend_params.ajaxurl, data, function (res) {
				if (true === res.success) {
					if (res.data.html) {
						tickets_container.find('.lty-lottery-ticket-tab-wrapper').hide();
						tickets_container.find('.lty-ticket-number-wrapper').hide();
						tickets_container.find('.lty-lottery-ticket-tab-content').html(res.data.html);
						tickets_container.find('.lty-manual-ticket-click-to-back-action').show();
					} else {
						tickets_container.find('.lty-lottery-ticket-tab-wrapper').show();
						tickets_container.find('.lty-ticket-number-wrapper').show();
					}

					LTY_Frontend.unblock(tickets_container);
				} else {
					$.alertable.alert(res.data.error);

					LTY_Frontend.unblock(tickets_container);
				}
			});
		},

		process_manual_ticket_click_to_back_action: function (event) {
			event.preventDefault();
			var $this = $(event.currentTarget),
				tickets_container = $($this).closest('.lty-lottery-ticket-container');

			tickets_container.find('.lty-ticket-number-content-search').hide();
			tickets_container.find('.lty-lottery-ticket-tab-wrapper').show();
			tickets_container.find('.lty-ticket-number-wrapper').show();
			tickets_container.find('.lty-manual-ticket-click-to-back-action').hide();
			tickets_container.find('.lty-manual-ticket-search').val('');

			LTY_Frontend.handle_lottery_ticket_tab_selection(event);
		},

		pagination: function (event) {
			event.preventDefault();
			var $this = $(event.currentTarget),
				wrapper = $this.closest('.lty-data-table-wrapper'),
				filter = wrapper.find('.lty-frontend-filter'),
				selected_page = $this.data('page'),
				table_action_name = $this.closest('td').data('action_name'),
				product_id = $this.closest('td').data('product_id');

			LTY_Frontend.block(wrapper);

			var data = ({
				action: 'lty_pagination_action',
				selected_page: selected_page,
				product_id: '' != product_id ? product_id : 0,
				table_action_name: table_action_name,
				s: filter.find('.lty-frontend-search').val(),
				lty_security: lty_frontend_params.pagination_action_nonce
			});

			$.post(lty_frontend_params.ajaxurl, data, function (res) {
				if (true === res.success) {
					wrapper.html(res.data.html);
				} else {
					alert(res.data.error);
				}

				LTY_Frontend.unblock(wrapper);
			});
		},

		winners_by_date_pagination: function (event) {
			event.preventDefault();
			var $this = $(event.currentTarget),
				wrapper = $this.closest('.lty-lottery-winners-by-date-wrapper'),
				selected_page = $this.data('page');

			LTY_Frontend.block(wrapper);

			var data = ({
				action: 'lty_pagination_action',
				selected_page: selected_page,
				per_page: wrapper.find('.lty-pagination-per-page').val(),
				order: wrapper.find('.lty-pagination-order').val(),
				date_filter_number: wrapper.find('.lty-pagination-date-filter-number').val(),
				date_filter_unit: wrapper.find('.lty-pagination-date-filter-unit').val(),
				table_action_name: 'winners_by_date',
				lty_security: lty_frontend_params.pagination_action_nonce
			});

			$.post(lty_frontend_params.ajaxurl, data, function (res) {
				if (true === res.success) {
					wrapper.replaceWith(res.data.html);
				} else {
					alert(res.data.error);
				}

				LTY_Frontend.unblock(wrapper);
			});
		},

		/**
		 * Trigger instant winners by date pagination.
		 * 
		 * @since 10.4.0
		 * @param {object} event 
		 */
		instant_winners_by_date_pagination: function (event) {
			event.preventDefault();
			var $this = $(event.currentTarget),
				wrapper = $this.closest('.lty-lottery-instant-winners-by-date-wrapper'),
				selected_page = $this.data('page');

			LTY_Frontend.block(wrapper);

			var data = ({
				action: 'lty_pagination_action',
				selected_page: selected_page,
				per_page: wrapper.find('.lty-pagination-per-page').val(),
				order: wrapper.find('.lty-pagination-order').val(),
				date_filter_number: wrapper.find('.lty-pagination-date-filter-number').val(),
				date_filter_unit: wrapper.find('.lty-pagination-date-filter-unit').val(),
				table_action_name: 'instant_winners_by_date',
				lty_security: lty_frontend_params.pagination_action_nonce
			});

			$.post(lty_frontend_params.ajaxurl, data, function (res) {
				if (true === res.success) {
					wrapper.replaceWith(res.data.html);
				} else {
					alert(res.data.error);
				}

				LTY_Frontend.unblock(wrapper);
			});
		},

		search_ticket_logs: function (event) {
			event.preventDefault();
			var $this = $(event.currentTarget),
				wrapper = $this.closest('.lty-ticket-logs-wrapper');

			LTY_Frontend.block(wrapper);

			var data = ({
				action: 'lty_search_ticket_logs',
				s: wrapper.find('.lty-ticket-logs-search').val(),
				product_id: wrapper.find('.lty-lottery-product-id').val(),
				lty_security: lty_frontend_params.search_nonce
			});

			$.post(lty_frontend_params.ajaxurl, data, function (res) {
				if (true === res.success) {
					wrapper.replaceWith(res.data.html);
				} else {
					alert(res.data.error);
				}

				LTY_Frontend.unblock(wrapper);
			});
		},

		/**
		 * Hide lottery tickets if view more option enabled.
		 * 
		 * @since 8.6.0
		 */
		hide_lottery_tickets: function () {
			if (0 < $('.lty-lottery-ticket-tab-content').length && 0 < $('.lty-hidden-ticket').length) {
				$('.lty-lottery-ticket-tab-content').find('.lty-hidden-ticket').hide();
				var $button = $('.lty-lottery-ticket-tab-content').find('.lty-toggle-lottery-tickets');

				$($button).data('action', 'view_more');
				$($button).data('step', 1);
				$($button).text(lty_frontend_params.view_more_ticket_label);
			}
		},

		/**
		 * Toggle lottery tickets per tab.
		 * 
		 * @since 8.6.0
		 * @param {event} event 
		 */
		toggle_lottery_tickets_per_tab: function (event) {
			event.preventDefault();
			var $this = $(event.currentTarget),
				$wrapper = $($this).closest('.lty-ticket-number-wrapper'),
				$step = parseInt($($this).data('step'));

			if ('view_less' === $($this).data('action')) {
				LTY_Frontend.hide_lottery_tickets();
				$('html, body').animate({ scrollTop: $('.lty-lottery-ticket-tab-content').offset().top }, 1000);
				return;
			}

			if (0 < $($wrapper).find('.lty-step-' + $step).length) {
				$($wrapper).find('.lty-step-' + $step).show();
			}

			if (0 < $($wrapper).find('.lty-step-' + ($step + 1)).length) {
				$($this).data('step', ($step + 1));
			} else {
				$($this).data('action', 'view_less');
				$($this).text(lty_frontend_params.view_less_ticket_label);
			}
		},

		/**
		 * Display customer popup dashboard lottery tickets 
		 * 
		 * @since 8.7.0
		 * @param {event} event 
		 */
		display_customer_popup_lottery_tickets: function (event) {
			event.preventDefault();
			var product_id = $(event.currentTarget).data('product_id');

			$('.lty-customer-lottery-tickets-modal-wrapper-' + product_id).modal();
		},

		/**
		 * Maybe select predefined button if the quantity matches with predefined button.
		 * 
		 * @since 10.0.0
		 * @param {int} quantity
		 */
		maybe_select_predefined_button: function (quantity, product_id) {
			var predefined_buttons_wrapper = $('.lty-predefined-buttons');
			if (!predefined_buttons_wrapper.length) {
				return;
			}

			predefined_buttons_wrapper.each(function () {
				var wrapper = $(this);

				if (!wrapper.find('.lty-predefined-button[data-product_id="' + product_id + '"]').length) {
					return;
				}

				var predefined_button = wrapper.find('.lty-predefined-button[data-ticket-quantity="' + quantity + '"]:first');
				// Unselect the predefined button, if already selected when predefined button not exists with the quantity.
				if (!predefined_button.length) {
					predefined_button = wrapper.find('.lty-selected-button');
				}

				if (0 < predefined_button.length) {
					LTY_Frontend.handle_select_predefined_button(predefined_button);
				}
			});
		},

		/**
		 * Handles the instant winner details pagination for the order.
		 * 
		 * @since 10.9.0 
		 * @param {object} event 
		 */
		order_instant_winners_pagination: function (event) {
			event.preventDefault();
			var $this = $(event.currentTarget),
				wrapper = $this.closest('.lty-order-instant-winners-wrapper'),
				pagination_wrapper = wrapper.find('.lty-pagination-wrapper');

			LTY_Frontend.block(wrapper);
			var data = ({
				action: 'lty_pagination_action',
				selected_page: $this.data('page'),
				table_action_name: pagination_wrapper.data('action_name'),
				extra_data: pagination_wrapper.data('extra_data'),
				lty_security: lty_frontend_params.pagination_action_nonce
			});

			$.post(lty_frontend_params.ajaxurl, data, function (res) {
				if (true === res.success) {
					wrapper.replaceWith(res.data.html);
				} else {
					alert(res.data.error);
				}

				LTY_Frontend.unblock(wrapper);
			});
		},

		/**
		 * Toggle the instant winner prize group.
		 * 
		 * @since 11.1.0
		 * @param {object} event 
		 */
		toggle_instant_winner_prize_group: function (event) {
			event.preventDefault();

			var $this = $(event.currentTarget),
			group_item = $this.closest('.lty-instant-winner-prize-group-item'),
			content_wrapper = group_item.find('.lty-instant-winner-prize-group-item-content');

			// Hide all other prize groups except current one
			$('.lty-instant-winner-prize-group-item-content').not(content_wrapper).addClass('lty-hide');

			if( '2' === lty_frontend_params.product_page_loading_mode && content_wrapper.length === 0 ){
				LTY_Frontend.block(group_item);

				var data = {
					action: 'lty_get_instant_winner_prize_group_tickets_html',
					extra_data: group_item.data('extra_data'),
					lty_security: lty_frontend_params.instant_win_prize_group_tickets_nonce
				};

				$.post(lty_frontend_params.ajaxurl, data, function (res) {
					if (true === res.success) {
						var $html = $(res.data.html);
						$html.removeClass('lty-hide');
						group_item.append($html);
					} else {
						alert(res.data.error);
					}
					LTY_Frontend.unblock(group_item);
				});
			} else {
				content_wrapper.toggleClass('lty-hide');
			}
			
		},

		/**
		 * Handles the instant winner prize group pagination action.
		 * 
		 * @since 11.1.0
		 * @param {object} event 
		 */
		instant_winner_prize_group_pagination: function (event) {
			event.preventDefault();
			var $this = $(event.currentTarget),
				wrapper = $this.closest('.lty-data-table-wrapper'),
				pagination_wrapper = $this.closest('.lty-pagination-wrapper');

			LTY_Frontend.block(wrapper);
			var data = ({
				action: 'lty_pagination_action',
				selected_page: $this.data('page'),
				table_action_name: pagination_wrapper.data('action_name'),
				extra_data: pagination_wrapper.data('extra_data'),
				lty_security: lty_frontend_params.pagination_action_nonce
			});
			$.post(lty_frontend_params.ajaxurl, data, function (res) {
				if (true === res.success) {
					wrapper.replaceWith(res.data.html);
				} else {
					alert(res.data.error);
				}

				LTY_Frontend.unblock(wrapper);
			});
		},

		/**
		 * Handles the instant winner prize group ticket pagination action.
		 * 
		 * @since 12.0.0
		 * @param {object} event 
		 */
		instant_winner_prize_group_ticket_pagination: function (event) {
			event.preventDefault();
			var $this = $(event.currentTarget),
				wrapper = $this.closest('.lty-instant-winner-group-ticket-numbers-wrapper'),
				pagination_wrapper = wrapper.find('.lty-pagination-wrapper');

			LTY_Frontend.block(wrapper);
			var data = ({
				action: 'lty_pagination_action',
				selected_page: $this.data('page'),
				table_action_name: pagination_wrapper.data('action_name'),
				extra_data: pagination_wrapper.data('extra_data'),
				lty_security: lty_frontend_params.pagination_action_nonce
			});
			$.post(lty_frontend_params.ajaxurl, data, function (res) {
				if (true === res.success) {
					wrapper.html($(res.data.html).html());
				} else {
					alert(res.data.error);
				}

				LTY_Frontend.unblock(wrapper);
			});
		},

		block: function (id) {
			$(id).block({
				message: null,
				overlayCSS: { background: '#fff', opacity: 0.7 }
			});
		},

		unblock: function (id) {
			$(id).unblock();
		},
	};

	LTY_Frontend.init();
});
