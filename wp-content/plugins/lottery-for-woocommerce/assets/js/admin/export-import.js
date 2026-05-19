/* global lty_export_import_params */

/**
 * Import.
 * 
 * @since 9.9.0
 * @param {object} $
 */
jQuery(function ($) {
	'use strict';
	/**
	 * Import Handler class.
	 * 
	 * @since 9.9.0
	 * @type object
	 */
	var ImportHandler = {
		init: function () {
			// Fetch the respective actions import popup content then show it.
			$(document).on('click', '.lty-import-popup', this.fetch_import_popup_content);
			// Upload form.
			$(document).on('click', '.lty-upload-form', this.upload_form);
			// Done.
			$(document).on('click', '.lty-import-done-btn', this.reload_page);
			// Toggle advanced options
			$(document).on('click', '.lty-import-advanced-options', this.toggle_advanced_options);
		},
		/**
		 * Fetch the respective actions import popup content then show it.
		 * 
		 * @since 9.9.0
		 * @param {object} event
		 */
		fetch_import_popup_content: function (event) {
			event.preventDefault();
			var $this = $(event.currentTarget);
			if ($this.data('preview-data')) {
				$($this).WCBackboneModal({
					template: 'lty-import-popup-modal',
					variable: $this.data('preview-data')
				});
			} else {
				Block($this);
				var data = ({
					action: 'lty_fetch_import_popup_content',
					action_type: $this.data('action'),
					extra_data: $this.data('extra_data'),
					lty_security: lty_export_import_params.import_nonce
				});
				$.post(ajaxurl, data, function (res) {
					if (true === res.success) {
						$this.data('preview-data', res.data.html);
						$($this).WCBackboneModal({
							template: 'lty-import-popup-modal',
							variable: res.data.html
						});
					} else {
						alert(res.data.error);
					}
				});
				unBlock($this);
			}
		},

		upload_form: function (event) {
			event.preventDefault();
			var $this = $(event.currentTarget),
					popupWrapper = $this.closest('.lty-import-modal-wrapper'),
					form = popupWrapper.find('#lty_import_form'),
					uploadedFileField = popupWrapper.find('#lty_import_file'),
					maxFileSizeField = popupWrapper.find('#lty_import_max_file_size');

			// Validate the file exists.
			if (!uploadedFileField.val()) {
				alert(lty_export_import_params.upload_file_empty_error);
				return false;
			}

			// Validate if the uploaded file size is maximum of WordPress allowed upload size.
			if (uploadedFileField[0].files[0].size > maxFileSizeField.val()) {
				alert(lty_export_import_params.upload_file_max_size_error);
				return false;
			}

			Block(popupWrapper);

			var formData = new FormData(form[0]);
			formData.append('action', 'lty_upload_import_form');
			formData.append('action_type', popupWrapper.find('.lty-import-action-type').val());
			formData.append('extra_data', popupWrapper.find('.lty-import-extra-data').val());
			formData.append('lty_security', lty_export_import_params.import_nonce);

			$.ajax({
				url: ajaxurl,
				data: formData,
				method: 'POST',
				contentType: false,
				processData: false,
				dataType: 'json',
				success: function (res) {
					unBlock(popupWrapper);
					if (true === res.success) {
						// Remove popup if any exists.
						$('#wc-backbone-modal-dialog').remove();

						$($this).WCBackboneModal({
							template: 'lty-import-popup-modal',
							variable: res.data.html
						});

						Import.start();
					} else {
						alert(res.data.error);
					}
				}
			});
		},

		reload_page: function () {
			Block($('.lty-import-modal-wrapper'));

			window.location.reload(true);
		},

		/**
		 * Toggle advanced options.
		 * 
		 * @since 10.8.0
		 * @param {object} event
		 */
		toggle_advanced_options: function (event) {
			event.preventDefault();
			var $this = $(event.currentTarget),
				form = $($this).closest('.lty-import-form-wrapper');

			if (form.find('.lty-import-advanced-options-field').is('.hidden')) {
				form.find('.lty-import-advanced-options-field').removeClass('hidden');
				$($this).text($($this).data('hide_text'));
			} else {
				form.find('.lty-import-advanced-options-field').addClass('hidden');
				$($this).text($($this).data('show_text'));
			}
		}
	};

	/**
	 * Import class.
	 * 
	 * @since 9.9.0
	 * @type object
	 */
	var Import = {
		start: function () {
			this.run_import();
		},
		run_import: function () {
			var wrapper = $('.lty-import-modal-wrapper'),
					form = wrapper.find('#lty_import_progress_form'),
					formData = new FormData(form[0]);

			formData.append('action', 'lty_run_import');
			formData.append('action_type', wrapper.find('.lty-import-action-type').val());
			formData.append('extra_data', wrapper.find('.lty-import-extra-data').val());
			formData.append('lty_security', lty_export_import_params.import_nonce);

			$.ajax({
				type: 'POST',
				url: ajaxurl,
				data: formData,
				contentType: false,
				processData: false,
				dataType: 'json',
				success: function (res) {
					if (res.success) {
						if (res.data.html) {
							wrapper.find('.lty-importer-progress').val(100);
							setTimeout(function () {
								// Remove popup if any exists.
								$('#wc-backbone-modal-dialog').remove();

								$(wrapper).WCBackboneModal({
									template: 'lty-import-popup-modal',
									variable: res.data.html
								});
							}, 2000);

						} else {
							wrapper.find('#lty-import-position').val(res.data.position);
							wrapper.find('#lty-imported-count').val(res.data.imported);
							wrapper.find('#lty-import-failed-count').val(res.data.failed);
							wrapper.find('#lty-import-updated-count').val(res.data.updated);
							wrapper.find('.lty-importer-progress').val(res.data.percentage);

							Import.run_import();
						}
					}
				}
			}).fail(function (response) {
				window.console.log(response.responseText);
			});
		}
	};

	/**
	 * Export Handler class.
	 * 
	 * @since 9.9.0
	 * @type object
	 */
	var ExportHandler = {
		init: function () {
			// Fetch the respective action export popup content then show it.
			$(document).on('click', '.lty-export-popup', this.fetch_import_popup_content);
			// Start exporting.
			$(document).on('click', '.lty-export', this.start_export);
			// Toggle advanced options
			$(document).on('click', '.lty-export-advanced-options', this.toggle_advanced_options);
			// Done.
			$(document).on('click', '.lty-export-done-btn', this.reload_page);

		},
		/**
		 * Fetch the respective action export popup content then show it.
		 * 
		 * @since 10.3.0
		 * @param {object} event
		 */
		fetch_import_popup_content: function (event) {
			event.preventDefault();
			var $this = $(event.currentTarget);
			if ($this.data('preview-data')) {
				$($this).WCBackboneModal({
					template: 'lty-export-popup-modal',
					variable: $this.data('preview-data')
				});

				$(document.body).trigger('lty-enhanced-init');
			} else {
				Block($this);
				var data = ({
					action: 'lty_fetch_export_popup_content',
					export_type: $this.data('export_type'),
					extra_data: $this.data('extra_data'),
					lty_security: lty_export_import_params.export_nonce
				});
				$.post(ajaxurl, data, function (res) {
					if (true === res.success) {
						$this.data('preview-data', res.data.html);
						$($this).WCBackboneModal({
							template: 'lty-export-popup-modal',
							variable: res.data.html
						});

						$(document.body).trigger('lty-enhanced-init');
					} else {
						alert(res.data.error);
					}
				});
				unBlock($this);
			}
		},
		/**
		 * Start export.
		 * 
		 * @since 10.3.0
		 * @param {object} event
		 */
		start_export: function (event) {
			event.preventDefault();
			var $this = $(event.currentTarget),
					popupWrapper = $this.closest('.lty-export-modal-wrapper'),
					form = popupWrapper.find('#lty_export_form'),
					fileNameField = popupWrapper.find('.lty-export-file-name');

			// Validate the file name exists.
			if (!fileNameField.val()) {
				alert(lty_export_import_params.file_name_empty_error);
				return false;
			}

			Block(popupWrapper);

			var formData = new FormData(form[0]);
			formData.append('action', 'lty_fetch_export_popup_content');
			formData.append('step', 'export');
			formData.append('lty_security', lty_export_import_params.export_nonce);

			$.ajax({
				url: ajaxurl,
				data: formData,
				method: 'POST',
				contentType: false,
				processData: false,
				dataType: 'json',
				success: function (res) {
					unBlock(popupWrapper);
					if (true === res.success) {
						// Remove popup if any exists.
						$('#wc-backbone-modal-dialog').remove();

						$($this).WCBackboneModal({
							template: 'lty-export-popup-modal',
							variable: res.data.html
						});

						Export.start();
					} else {
						alert(res.data.error);
					}
				}
			});
		},
		/**
		 * Toggle advanced options.
		 * 
		 * @since 10.3.0
		 * @param {object} event
		 */
		toggle_advanced_options: function (event) {
			event.preventDefault();
			var $this = $(event.currentTarget),
					form = $($this).closest('.lty-export-form-wrapper');

			if (form.find('.lty-export-advanced-options-field').is('.hidden')) {
				form.find('.lty-export-advanced-options-field').removeClass('hidden');
				$($this).text($($this).data('hide_text'));
			} else {
				form.find('.lty-export-advanced-options-field').addClass('hidden');
				$($this).text($($this).data('show_text'));
			}
		},
		/**
		 * Reload page.
		 * 
		 * @since 10.3.0
		 * @param {object} event
		 */
		reload_page: function () {
			Block($('.lty-export-modal-wrapper'));

			window.location.reload(true);
		}

	};

	/**
	 * Export class.
	 * 
	 * @since 10.3.0
	 * @type object
	 */
	var Export = {
		start: function () {
			this.run_export();
		},
		run_export: function () {
			var wrapper = $('.lty-export-modal-wrapper'),
					form = wrapper.find('#lty_export_progress_form'),
					formData = new FormData(form[0]);

			formData.append('action', 'lty_run_export');
			formData.append('lty_security', lty_export_import_params.export_nonce);

			$.ajax({
				type: 'POST',
				url: ajaxurl,
				data: formData,
				contentType: false,
				processData: false,
				dataType: 'json',
				success: function (res) {
					if (res.success) {
						wrapper.find('.lty-exporter-progress').val(res.data.percentage);
						wrapper.find('.lty-export-progress-bar-left').text(res.data.exported_count);
						wrapper.find('.lty-export-progress-bar-percentage').text(res.data.percentage);

						if (res.data.html) {
							window.location = res.data.download_url;
							setTimeout(function () {
								// Remove popup if any exists.
								$('#wc-backbone-modal-dialog').remove();

								$(wrapper).WCBackboneModal({
									template: 'lty-export-popup-modal',
									variable: res.data.html
								});
							}, 3000);

						} else {
							wrapper.find('.lty-export-page').val(res.data.page);

							Export.run_export();
						}
					}
				}
			}).fail(function (response) {
				window.console.log(response.responseText);
			});
		}
	};

	/**
	 * Block the element.
	 * 
	 * @since 9.9.0
	 * @param {string} id
	 */
	function Block(id) {
		if (!isBlocked(id)) {
			$(id).addClass('processing').block({
				message: null,
				overlayCSS: {
					background: '#fff',
					opacity: 0.7
				}
			});
		}
	}

	/**
	 * Unblock the element.
	 * 
	 * @since 9.9.0
	 * @param {string} id
	 */
	function unBlock(id) {
		$(id).removeClass('processing').unblock();
	}

	/**
	 * Validate if the element already blocked.
	 * 
	 * @since 9.9.0
	 * @param {string} id
	 * @returns {boolean}
	 */
	function isBlocked(id) {
		return $(id).is('.processing') || $(id).parents('.processing').length;
	}

	ImportHandler.init();
	ExportHandler.init();
});
