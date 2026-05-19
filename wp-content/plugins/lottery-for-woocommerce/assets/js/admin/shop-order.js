/* global lty_shop_order_params */

jQuery(function ($) {
	'use strict';

	var ticket_numbers = [];
	var LTY_Shop_Order = {
		init: function () {
			// Generate the automatic ticket.
			$(document).on('click', '.lty-automatic-tickets-popup-btn', this.render_automatic_ticket_popup);
			$(document).on('change', '.lty_ticket_generation_mode', this.toggle_ticket_generation_mode);
			$(document).on('keyup , change', '.lty-lottery-custom-ticket-field', this.validate_custom_ticket_field);
			// Render the manual tickets popup.
			$(document).on('click', '.lty-manual-tickets-popup-btn', this.render_manual_ticket_popup);
			// Render the question answer popup.
			$(document).on('click', '.lty-question-answer-popup-btn', this.render_question_answer_popup);
			// Trigger Manual Ticket Popup.
			$(document.body).on('wc_backbone_modal_loaded', this.backbone.init);
			// Trigger Generate manual ticket button.
			$(document.body).on('wc_backbone_modal_response', this.backbone.response);
		},

		validate_custom_ticket_field: function (event) {
			var $this = $(event.currentTarget),
			ticket_quantity = $('.lty-lottery-ticket-quantity').val(),
			entered_ticket_numbers = $this.val() === '' ? [] : $this.val().split(',');

			if (entered_ticket_numbers.length > ticket_quantity) {
				$this.val(entered_ticket_numbers.slice(0, ticket_quantity).join(','));
				entered_ticket_numbers = $this.val().split(',');  
			}
		
			var remainingTickets = ticket_quantity - entered_ticket_numbers.length;
			if (remainingTickets < 0) {
				remainingTickets = 0;
			}

			// Update the lottery remaining ticket count
			$('#lty-remaining-ticket-count-message').find('.lty-remaining-ticket-count').html(remainingTickets);
		},

		toggle_ticket_generation_mode: function (event) {
			event.preventDefault();
			var $this = $(event.currentTarget);

			if ('2' === $this.val() ) {
				$('.lty-manual-order-lottery-ticket-container').show();
			} else {
				$('.lty-manual-order-lottery-ticket-container').hide();
			}
		},

		render_automatic_ticket_popup: function (event) {
			event.preventDefault();
			if (!confirm(lty_shop_order_params.lty_confirm_message)) {
				return false;
			}

			var $this = $(event.currentTarget);
			var data = ({
				action: 'lty_get_automatic_ticket_popup_html',
				item_id: $this.data('item_id'),
				order_id: $('.lty-order-id').val(),
				lty_security: lty_shop_order_params.lty_automatic_ticket_nonce,
			});

			$.post(ajaxurl, data, function (res) {
				if (true === res.success) {
					// Backbone Modal for display popup.
					$(this).WCBackboneModal({
						template: 'lty-automatic-tickets-popup',
						variable: res.data
					});

					return false;
				} else {
					alert(res.data.error);
				}
			});
		},

		render_manual_ticket_popup: function (event) {
			event.preventDefault();
			if (!confirm(lty_shop_order_params.lty_confirm_message)) {
				return false;
			}

			// Reset global ticket numbers variable.
			ticket_numbers = [];

			var $this = $(event.currentTarget);
			var data = ({
				action: 'lty_manual_ticket_popup',
				item_id: $this.data('item_id'),
				order_id: $('.lty-order-id').val(),
				lty_security: lty_shop_order_params.lty_manual_ticket_nonce,
			});

			$.post(ajaxurl, data, function (res) {
				if (true === res.success) {
					// Backbone Modal for display popup.
					$(this).WCBackboneModal({
						template: 'lty-manual-tickets-popup',
						variable: res.data
					});

					return false;
				} else {
					alert(res.data.error);
				}
			});
		},

		render_question_answer_popup: function (event) {
			event.preventDefault();
			if (!confirm(lty_shop_order_params.lty_confirm_message)) {
				return false;
			}

			var $this = $(event.currentTarget);
			var data = ({
				action: 'lty_question_answer_popup_content',
				item_id: $this.data('item_id'),
				order_id: $('.lty-order-id').val(),
				lty_security: lty_shop_order_params.lty_manual_ticket_nonce,
			});

			$.post(ajaxurl, data, function (res) {
				if (true === res.success) {
					// Backbone Modal.
					$(this).WCBackboneModal({
						template: 'lty-question-answer-modal',
						variable: res.data
					});

					return false;
				} else {
					alert(res.data.error);
				}
			});
		},

		backbone: {
			init: function (event, target) {
				var modals = ['lty-manual-tickets-popup', 'lty-automatic-tickets-popup', 'lty-question-answer-modal'];
				if (modals.includes(target)) {
					LTY_Shop_Order.backbone.on_render.init(event);
				}
			},
			response: function (event, target) {
				switch (target) {
					case 'lty-manual-tickets-popup':
						LTY_Shop_Order.backbone.generate_manual_ticket(event);
						break;
					
					case 'lty-automatic-tickets-popup':
						LTY_Shop_Order.backbone.generate_automatic_ticket(event);
						break;

					case 'lty-question-answer-modal':
						LTY_Shop_Order.backbone.generate_answer(event);
						break;
				}

			},
			on_render: {
				init: function (event) {
					// Ticket tab selection.
					$(document).on('click', '.lty-lottery-ticket-tab', this.ticket_tab_selection);
					// Select the ticket.
					$(document).on('click', '.lty-ticket', this.lottery_ticket_selection);
					// Unselect the ticket.
					$(document).on('click', '.lty-selected-ticket', this.lottery_ticket_unselection);
					// Select the answer.
					$(document).on('click', '.lty-lottery-answer', this.select_lottery_answer);
				},
				ticket_tab_selection: function (event) {
					event.preventDefault();
					var $this = $(event.currentTarget),
						tickets_container = $($this).closest('.lty-lottery-ticket-container');

					LTY_Shop_Order.block('.lty-lottery-ticket-container');
					var data = ({
						action: 'lty_ticket_tab_selection_edit_order',
						product_id: tickets_container.find('.lty-ticket-product-id').val(),
						tab: $this.data('tab'),
						index: $this.data('index') ? $this.data('index') : 0,
						ticket_numbers: $('.lty-lottery-ticket-numbers').val(),
						lty_security: lty_shop_order_params.lty_manual_ticket_nonce,
					});

					$.post(ajaxurl, data, function (res) {
						LTY_Shop_Order.unblock('.lty-lottery-ticket-container');
						if (true === res.success) {
							tickets_container.find('.lty-lottery-ticket-tab').removeClass('lty-active-tab');
							$($this).addClass('lty-active-tab');
							tickets_container.find('.lty-lottery-ticket-tab-content').html(res.data.html);

							// class 'lty-selected-ticket' added when ticket tab selection is clicked. 
							var $selected_ticket_numbers = res.data.ticket_numbers;
							for (var i = 0; i < $selected_ticket_numbers.length; i++) {
								$('.lty-ticket[data-ticket="' + $selected_ticket_numbers[i] + '"]').addClass('lty-selected-ticket');
							}
						} else {
							alert(res.data.error);
						}
					}
					);
				},
				lottery_ticket_selection: function (event) {
					event.preventDefault();
					var $this = $(event.currentTarget),
						tickets_container = $($this).closest('.lty-lottery-ticket-container'),
						selected_tickets = tickets_container.find('.lty-lottery-ticket-numbers'),
						quantity = tickets_container.find('.lty-lottery-ticket-quantity');

					// Return if booked/selected ticket.
					if ($this.hasClass("lty-booked-ticket") || $this.hasClass("lty-selected-ticket")) {
						return;
					}

					// Return if reserved ticket.
					if ($this.hasClass("lty-reserved-ticket")) {
						alert(lty_shop_order_params.lty_reserved_ticket_error_message);
						return false;
					}

					$($this).addClass('lty-selected-ticket');
					// Push selected ticket numbers in array.
					ticket_numbers.push($($this).data('ticket'));
					selected_tickets.val(ticket_numbers);
					quantity.val(ticket_numbers.length);

				},

				lottery_ticket_unselection: function (event) {
					event.preventDefault();
					var $this = $(event.currentTarget),
						tickets_container = $($this).closest('.lty-lottery-ticket-container'),
						selected_tickets = tickets_container.find('.lty-lottery-ticket-numbers'),
						quantity = tickets_container.find('.lty-lottery-ticket-quantity');

					var $ticket = $($this).data('ticket');
					$($this).removeClass('lty-selected-ticket');

					// Splice index number , delete count.
					ticket_numbers.splice(ticket_numbers.indexOf($ticket), 1);
					selected_tickets.val(ticket_numbers);
					quantity.val(ticket_numbers.length);

				},

				select_lottery_answer: function (event) {
					event.preventDefault();
					var $this = $(event.currentTarget),
						answers_wrapper = $($this).closest('.lty-lottery-question-answer-container');

					answers_wrapper.find('.lty-lottery-answer').removeClass('lty-selected-ticket');
					$($this).addClass('lty-selected-ticket');
					answers_wrapper.find('.lty-question-answer-id').val($($this).data('answer-id'));
				}
			},

			generate_automatic_ticket: function (event) {
				var $this = $(event.currentTarget),
					tickets_container = $('.lty-lottery-manual-ticket-container'),
					selected_tickets = tickets_container.find('.lty-lottery-custom-ticket-field').val(),
					mode = tickets_container.find('.lty_ticket_generation_mode').val(),
					question_answer_wrapper = tickets_container.find('.lty-lottery-question-answer-container'),
					answer_id = question_answer_wrapper.find('.lty-question-answer-id').val();

				// Validate if the answer is not selected when its required.
				if (question_answer_wrapper.length && 'yes' === question_answer_wrapper.data('force') && !answer_id) {
					alert(lty_shop_order_params.lty_answer_require_error_message);
					return false;
				}

				// Alert message displayed when ticket is left empty.
				if ('2' === mode && !selected_tickets) {
					alert(lty_shop_order_params.lty_ticket_number_empty_error_messages);
					return false;
				}

				LTY_Shop_Order.block($('.woocommerce_order_items').find('#order_line_items'));

				var data = {
					action: 'lty_generate_automatic_ticket_edit_order',
					product_id: tickets_container.find('.lty-ticket-product-id').val(),
					ticket_numbers: selected_tickets,
					answer_id: question_answer_wrapper.find('.lty-question-answer-id').val(),
					order_id: tickets_container.find('.lty-ticket-order-id').val(),
					item_id: tickets_container.find('.lty-ticket-item-id').val(),
					quantity: selected_tickets.split(',').length,
					mode: mode,
					lty_security: lty_shop_order_params.lty_automatic_ticket_nonce,
				};

				$.post(ajaxurl, data, function (res) {
					if (true === res.success) {
						alert(lty_shop_order_params.lty_success_message);
						location.reload();
					} else {
						LTY_Shop_Order.unblock($('.woocommerce_order_items').find('#order_line_items'));
						alert(res.data.error);
					}
				});
			},

			generate_manual_ticket: function (event) {
				var $this = $(event.currentTarget),
					tickets_container = $('.lty-lottery-ticket-container'),
					selected_tickets = tickets_container.find('.lty-lottery-ticket-numbers').val(),
					$quantity = tickets_container.find('.lty-lottery-ticket-quantity').val(),
					question_answer_wrapper = tickets_container.find('.lty-lottery-question-answer-container'),
					answer_id = question_answer_wrapper.find('.lty-question-answer-id').val();

				// Alert message displayed when ticket is left empty.
				if (!selected_tickets) {
					alert(lty_shop_order_params.lty_ticket_number_empty_error_messages);
					return false;
				}

				// Validate if the answer is not selected when its required.
				if (question_answer_wrapper.length && 'yes' === question_answer_wrapper.data('force') && !answer_id) {
					alert(lty_shop_order_params.lty_answer_require_error_message);
					return false;
				}

				LTY_Shop_Order.block($('.woocommerce_order_items').find('#order_line_items'));

				var data = {
					action: 'lty_generate_manual_ticket_edit_order',
					product_id: tickets_container.find('.lty-ticket-product-id').val(),
					ticket_numbers: selected_tickets,
					answer_id: question_answer_wrapper.find('.lty-question-answer-id').val(),
					order_id: tickets_container.find('.lty-ticket-order-id').val(),
					item_id: tickets_container.find('.lty-ticket-item-id').val(),
					quantity: $quantity,
					lty_security: lty_shop_order_params.lty_manual_ticket_nonce,
				};

				$.post(ajaxurl, data, function (res) {
					LTY_Shop_Order.unblock($('.woocommerce_order_items').find('#order_line_items'));
					if (true === res.success) {
						alert(lty_shop_order_params.lty_success_message);
						location.reload();
					} else {
						alert(res.data.error);
					}
				});
			},

			generate_answer: function (event) {
				var $this = $(event.currentTarget),
					modal = $('.lty-question-answer-modal-content'),
					answer_id = modal.find('.lty-question-answer-id').val();

				// Validate if the answer is not selected when its required.
				if (!answer_id) {
					alert(lty_shop_order_params.lty_answer_require_error_message);
					return false;
				}

				LTY_Shop_Order.block($('.woocommerce_order_items').find('#order_line_items'));
				var data = {
					action: 'lty_generate_order_item_product_answer',
					product_id: modal.find('.lty-ticket-product-id').val(),
					answer_id: answer_id,
					order_id: modal.find('.lty-ticket-order-id').val(),
					item_id: modal.find('.lty-ticket-item-id').val(),
					lty_security: lty_shop_order_params.lty_manual_ticket_nonce,
				};

				$.post(ajaxurl, data, function (res) {
					LTY_Shop_Order.unblock($('.woocommerce_order_items').find('#order_line_items'));
					if (true === res.success) {
						alert(res.data.msg);
						location.reload();
					} else {
						alert(res.data.error);
					}
				});
			}
		},
		block: function (id) {
			$(id).block({
				message: null,
				overlayCSS: {
					background: '#fff',
					opacity: 0.7
				}
			});
		},
		unblock: function (id) {
			$(id).unblock();
		}
	};
	LTY_Shop_Order.init();
});
