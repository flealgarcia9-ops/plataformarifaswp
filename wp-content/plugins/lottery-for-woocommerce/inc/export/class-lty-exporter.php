<?php

/**
 * Exporter.
 *
 * @since 10.3.0
 */
defined('ABSPATH') || exit;

if (!class_exists('LTY_Exporter')) {

	/**
	 * Class.
	 *
	 * @since 10.3.0
	 */
	class LTY_Exporter extends WC_CSV_Batch_Exporter {

		/**
		 * Filename to export to.
		 *
		 * @since 10.3.0
		 * @var string
		 */
		protected $filename = 'lty-lottery';

		/**
		 * Type of export used in filter names.
		 *
		 * @since 10.3.0
		 * @var string
		 */
		protected $export_type = '';

		/**
		 * The current export step.
		 *
		 * @since 10.3.0
		 * @var string
		 */
		private $step = '';

		/**
		 * Export offset.
		 *
		 * @since 10.3.0
		 * @var int
		 */
		private $offset = 0;

		/**
		 * Export total.
		 *
		 * @since 10.3.0
		 * @var int
		 */
		private $total = 0;

		/**
		 * Batch limit.
		 *
		 * @var integer
		 */
		protected $limit = 1000;

		/**
		 * Progress steps.
		 *
		 * @since 10.3.0
		 * @var array
		 */
		private $steps;

		/**
		 * Errors.
		 *
		 * @since 10.3.0
		 * @var array
		 */
		protected $errors = array();

		/**
		 * Extra data.
		 *
		 * @since 10.3.0
		 * @var array
		 */
		protected $extra_data = array();

		/**
		 * Parsed data.
		 *
		 * @since 10.3.0
		 * @var array
		 */
		protected $parsed_data = array();

		/**
		 * Current overall data.
		 *
		 * @since 10.3.0
		 * @var array/null
		 */
		protected $overall_data;

		/**
		 * Current chunked data.
		 *
		 * @since 10.3.0
		 * @var array/null
		 */
		protected $chunked_data;

		/**
		 * Custom field data.
		 *
		 * @since 11.9.0
		 * @var array
		 */
		protected $custom_field_data = array();

		/**
		 * Default custom field data.
		 *
		 * @since 11.9.0
		 * @var array
		 */
		protected $default_custom_field_data = array();

		/**
		 * Constructor
		 *
		 * @since 10.3.0
		 */
		public function __construct() {
			parent::__construct();

			$this->prepare_data();

			add_action( 'lty_export_form_' . $this->get_export_type() . '_field_content', array( $this, 'render_custom_field_content' ) );
		}

		/**
		 * Prepare the data
		 *
		 * @since 10.3.0
		 */
		protected function prepare_data() {
			$this->steps = array(
				'form' => array( $this, 'output_form' ),
				'export' => array( $this, 'output_export_form' ),
				'done' => array( $this, 'output_done' ),
			);

			$this->step = current(array_keys($this->get_steps()));
			$this->set_filename(isset($_REQUEST['filename']) ? wc_clean(wp_unslash($_REQUEST['filename'])) : $this->filename );
			$this->set_page(isset($_REQUEST['page']) ? intval($_REQUEST['page']) : $this->page);
			$this->set_limit(isset($_REQUEST['limit']) ? intval($_REQUEST['limit']) : $this->limit);
			$this->offset = isset($_REQUEST['offset']) ? intval($_REQUEST['offset']) : $this->offset;
			$this->total = isset($_REQUEST['total']) ? intval($_REQUEST['total']) : $this->total;
			$this->extra_data = isset($_REQUEST['extra_data']) ? wc_clean(wp_unslash($_REQUEST['extra_data'])) : $this->extra_data;

			$this->set_custom_field_data();
		}

		/**
		 * Set custom field data.
		 *
		 * @since 11.9.0
		 */
		protected function set_custom_field_data() {
			if ( ! lty_check_is_array( $this->default_custom_field_data ) ) {
				return;
			}

			$custom_field_data = isset( $_REQUEST['custom_field_data'] ) ? (array) json_decode( wc_clean( wp_unslash( $_REQUEST['custom_field_data'] ) ) ) : array();
			foreach ( $this->default_custom_field_data as $custom_field_data_key => $default_value ) {
				if ( isset( $_REQUEST[ $custom_field_data_key ] ) ) {
					$this->custom_field_data[ $custom_field_data_key ] = wc_clean( wp_unslash( $_REQUEST[ $custom_field_data_key ] ) );
				} elseif ( lty_check_is_array( $custom_field_data ) && isset( $custom_field_data[ $custom_field_data_key ] ) ) {
					$this->custom_field_data[ $custom_field_data_key ] = wc_clean( wp_unslash( $custom_field_data[ $custom_field_data_key ] ) );
				} else {
					$this->custom_field_data[ $custom_field_data_key ] = $default_value;
				}
			}
		}

		/**
		 * Render custom field content.
		 *
		 * @since 11.9.0
		 */
		public function render_custom_field_content() {
		}

		/**
		 * Get the offset value.
		 *
		 * @since 10.3.0
		 * @retrun int
		 */
		public function get_offset() {
			return $this->offset;
		}

		/**
		 * Get the total value.
		 *
		 * @since 10.3.0
		 * @retrun int
		 */
		public function get_total() {
			return $this->total;
		}

		/**
		 * Get the current offset value.
		 *
		 * @since 10.3.0
		 * @retrun int
		 */
		public function get_current_offset() {
			return ( $this->get_offset() - 1 ) + ( ( $this->get_page() - 1 ) * $this->get_limit() );
		}

		/**
		 * Get the current limit value.
		 *
		 * @since 10.3.0
		 * @retrun int
		 */
		public function get_current_limit() {
			return ( $this->get_total() && $this->get_limit() > $this->get_total() ) ? $this->get_total() : $this->get_limit();
		}

		/**
		 * Get the default file name.
		 *
		 * @since 10.3.0
		 * @return string
		 */
		public function get_default_file_name() {
			return $this->filename;
		}

		/**
		 * Get the export type.
		 *
		 * @since 10.3.0
		 * @retrun string
		 */
		public function get_export_type() {
			return $this->export_type;
		}

		/**
		 * Get the extra data.
		 *
		 * @since 10.3.0
		 * @retrun array
		 */
		public function get_extra_data() {
			return is_array($this->extra_data) ? $this->extra_data : (array) json_decode($this->extra_data);
		}

		/**
		 * Get the extra data value.
		 *
		 * @since 10.3.0
		 * @param string $key 
		 * @retrun Mixed
		 */
		public function get_extra_data_value( $key ) {
			$extra_data = $this->get_extra_data();
			if (!lty_check_is_array($extra_data) || !isset($extra_data[$key])) {
				return '';
			}

			return $extra_data[$key];
		}

		/**
		 * Get the custom field data.
		 *
		 * @since 11.9.0
		 * @return array
		 */
		public function get_custom_field_data() {
			return is_array( $this->custom_field_data ) ? $this->custom_field_data : (array) json_decode( $this->custom_field_data );
		}

		/**
		 * Get the custom field data value.
		 *
		 * @since 11.9.0
		 * @param string $key Data key.
		 * @return array
		 */
		public function get_custom_field_data_value( $key ) {
			$custom_field_data = $this->get_custom_field_data();
			if ( ! lty_check_is_array( $custom_field_data ) || ! isset( $custom_field_data[ $key ] ) ) {
				return '';
			}

			return $custom_field_data[ $key ];
		}

		/**
		 * Get the popup header label.
		 *
		 * @since 10.3.0
		 * @return string
		 */
		public function get_popup_header_label() {
			return __('Export', 'lottery-for-woocommerce');
		}

		/**
		 * Get the exporting description.
		 *
		 * @since 10.3.0
		 * @return string
		 */
		public function get_exporting_description() {
			return __('Your data are now being exported...', 'lottery-for-woocommerce');
		}

		/**
		 * Output the layout.
		 *
		 * @since 10.3.0
		 */
		public function output() {
			include __DIR__ . '/views/html-export-popup.php';
		}

		/**
		 * Output header.
		 *
		 * @since 10.3.0
		 */
		protected function output_header() {
			include __DIR__ . '/views/html-export-header.php';
		}

		/**
		 * Output errors.
		 *
		 * @since 10.3.0
		 */
		protected function output_errors() {
			include __DIR__ . '/views/html-export-errors.php';
		}

		/**
		 * Output form.
		 *
		 * @since 10.3.0
		 */
		protected function output_form() {
			include __DIR__ . '/views/html-export-form.php';
		}

		/**
		 * Export form.
		 *
		 * @since 10.3.0
		 */
		protected function output_export_form() {
			include __DIR__ . '/views/html-export-progress-form.php';
		}

		/**
		 * Done.
		 *
		 * @since 10.3.0
		 */
		protected function output_done() {
			include __DIR__ . '/views/html-export-done.php';
		}

		/**
		 * Set step.
		 *
		 * @since 10.3.0
		 * @param string $step
		 */
		public function set_step( $step ) {
			$this->step = $step;
		}

		/**
		 * Get the steps.
		 *
		 * @since 10.3.0
		 * @return array
		 */
		protected function get_steps() {
			return $this->steps;
		}

		/**
		 * Get the errors.
		 *
		 * @since 10.3.0
		 * @return array
		 */
		protected function get_errors() {
			return $this->errors;
		}

		/**
		 * Add a error message.
		 *
		 * @since 10.3.0
		 * @param string $message
		 */
		protected function add_error( $message ) {
			$this->errors[] = $message;
		}

		/**
		 * Get the current step view.
		 *
		 * @since 10.3.0
		 * @return string/array
		 */
		protected function get_current_step_view() {
			return $this->steps[$this->step];
		}

		/**
		 * Get the overall data.
		 *
		 * @since 10.3.0
		 * @retrun array
		 */
		protected function get_overall_data() {
			if (isset($this->overall_data)) {
				return $this->overall_data;
			}

			$this->overall_data = $this->prepare_overall_data();

			return $this->overall_data;
		}

		/**
		 * Prepare overall data.
		 *
		 * @since 10.3.0
		 * @retrun array
		 */
		protected function prepare_overall_data() {
			return array();
		}

		/**
		 * Get the chunked data.
		 *
		 * @since 10.3.0
		 * @retrun array
		 */
		protected function get_chunked_data() {
			if (isset($this->chunked_data)) {
				return $this->chunked_data;
			}

			$this->chunked_data = array_slice($this->get_overall_data(), $this->get_current_offset(), $this->get_current_limit());

			return $this->chunked_data;
		}

		/**
		 * Set total rows count.
		 * 
		 * @since 10.3.0
		 * @return void.
		 */
		public function set_total_rows() {
			if ((float) WC()->version > (float) '6.2.0') {
				$total_count = count($this->get_overall_data());
				$current_offset = ( $this->get_offset() - 1 );
				if ($total_count > $current_offset) {
					$total_count = $total_count - $current_offset;
				}

				if ($this->get_total() && $this->get_total() < $total_count) {
					$total_count = $this->get_total();
				}

				// Total rows count.
				$this->total_rows = $total_count;
			} else {
				// Per limit rows count.
				$this->total_rows = count($this->get_chunked_data());
			}
		}

		/**
		 * Prepare data that will be exported.
		 * 
		 * @since 10.3.0
		 * @return void.
		 */
		public function prepare_data_to_export() {
			// Prepare column names.
			$this->column_names = $this->get_default_column_names();
			// Format data to export.
			$this->format_data_to_export();
			// Set total rows.
			$this->set_total_rows();
		}

		/**
		 * Format data that will be exported.
		 * 
		 * @since 10.3.0
		 * @return void.
		 */
		protected function format_data_to_export() {
		}

		/**
		 * Download link.
		 * 
		 * @since 10.3.0
		 * @return string
		 */
		public function download_link() {
			/**
			 * This hook is used to alter the export action query arguments.
			 *
			 * @since 10.3.0
			 */
			return add_query_arg(apply_filters(
							'lty_export_action_query_args',
							array(
								'lty_action' => 'view',
								'filename' => $this->get_filename(),
								'export_type' => $this->get_export_type(),
								'lty_nonce' => wp_create_nonce('lty-export'),
							)
					), admin_url());
		}
	}

}
