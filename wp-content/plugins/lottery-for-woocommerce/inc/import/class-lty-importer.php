<?php

/**
 * Importer.
 *
 * @since 9.9.0
 */
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'LTY_Importer' ) ) {

	/**
	 * Class.
	 *
	 * @since 9.9.0
	 */
	class LTY_Importer {

		/**
		 * File.
		 *
		 * @since 9.9.0
		 * @var string
		 */
		protected $file = '';

		/**
		 * Action type.
		 *
		 * @since 9.9.0
		 * @var string
		 */
		protected $action_type;

		/**
		 * The current import step.
		 *
		 * @since 9.9.0
		 * @var string
		 */
		protected $step = '';

		/**
		 * Progress steps.
		 *
		 * @since 9.9.0
		 * @var array
		 */
		protected $steps;

		/**
		 * Errors.
		 *
		 * @since 9.9.0
		 * @var array
		 */
		protected $errors = array();

		/**
		 * The current delimiter for the file being read.
		 *
		 * @since 9.9.0
		 * @var string
		 */
		protected $delimiter = ',';

		/**
		 * The character encoding to use to interpret the input file, or empty string for auto detect.
		 *
		 * @since 9.9.0
		 * @var string
		 */
		protected $character_encoding = 'UTF-8';

		/**
		 * Enclosure.
		 *
		 * @since 9.9.0
		 * @var string
		 */
		protected $enclosure = '"';

		/**
		 * Limit.
		 *
		 * @since 9.9.0
		 * @var int
		 */
		protected $limit = 100;

		/**
		 * Escape.
		 *
		 * @since 9.9.0
		 * @var string
		 */
		protected $escape = "\0";

		/**
		 * Position of importing file.
		 *
		 * @since 9.9.0
		 * @var bool
		 */
		protected $position = 0;

		/**
		 * Imported count of while importing.
		 *
		 * @since 9.9.0
		 * @var bool
		 */
		protected $imported = 0;

		/**
		 * Failed count of while importing.
		 *
		 * @since 9.9.0
		 * @var bool
		 */
		protected $failed = 0;

		/**
		 * Updated count of while importing.
		 *
		 * @since 9.9.0
		 * @var bool
		 */
		protected $updated = 0;

		/**
		 * File headers.
		 *
		 * @since 9.9.0
		 * @var array
		 */
		protected $file_headers = array();

		/**
		 * Extra data.
		 *
		 * @since 9.9.0
		 * @var array
		 */
		protected $extra_data = array();

		/**
		 * Parsed data.
		 *
		 * @since 9.9.0
		 * @var array
		 */
		protected $parsed_data = array();

		/**
		 * Constructor
		 *
		 * @since 9.9.0
		 */
		public function __construct() {
			$this->prepare_data();
		}

		/**
		 * Prepare the data
		 *
		 * @since 9.9.0
		 */
		protected function prepare_data() {
			$this->steps = array(
				'upload' => array(
					'name' => __( 'Upload a CSV', 'lottery-for-woocommerce' ),
					'view' => array( $this, 'upload_form' ),
				),
				'import' => array(
					'name' => __( 'Import', 'lottery-for-woocommerce' ),
					'view' => array( $this, 'import' ),
				),
				'done'   => array(
					'name' => __( 'Done', 'lottery-for-woocommerce' ),
					'view' => array( $this, 'done' ),
				),
			);

			$this->step       = current( array_keys( $this->get_steps() ) );
			$this->file       = isset( $_REQUEST['file'] ) ? wc_clean( wp_unslash( $_REQUEST['file'] ) ) : $this->file;
			$this->position   = isset( $_REQUEST['position'] ) ? intval( $_REQUEST['position'] ) : $this->position;
			$this->imported   = isset( $_REQUEST['imported'] ) ? intval( $_REQUEST['imported'] ) : $this->imported;
			$this->updated    = isset( $_REQUEST['updated'] ) ? intval( $_REQUEST['updated'] ) : $this->updated;
			$this->failed     = isset( $_REQUEST['failed'] ) ? intval( $_REQUEST['failed'] ) : $this->failed;
			$this->extra_data = isset( $_REQUEST['extra_data'] ) ? wc_clean( wp_unslash( $_REQUEST['extra_data'] ) ) : $this->extra_data;
			$this->limit      = isset( $_REQUEST['limit'] ) ? intval( $_REQUEST['limit'] ) : $this->limit;
		}

		/**
		 * Get upload file description.
		 *
		 * @since 9.9.0
		 * @return string
		 */
		protected function get_upload_file_description() {
			return '';
		}

		/**
		 * Get the action type.
		 *
		 * @since 9.9.0
		 * @retrun string
		 */
		public function get_action_type() {
			return $this->action_type;
		}

		/**
		 * Get the extra data.
		 *
		 * @since 9.9.0
		 * @retrun array
		 */
		public function get_extra_data() {
			return is_array( $this->extra_data ) ? $this->extra_data : (array) json_decode( $this->extra_data );
		}

		/**
		 * Get the position count.
		 *
		 * @since 9.9.0
		 * @retrun int
		 */
		public function get_position_count() {
			return $this->position;
		}

		/**
		 * Get the imported count.
		 *
		 * @since 9.9.0
		 * @retrun int
		 */
		public function get_imported_count() {
			return $this->imported;
		}

		/**
		 * Get the updated count.
		 *
		 * @since 9.9.0
		 * @retrun int
		 */
		public function get_updated_count() {
			return $this->updated;
		}

		/**
		 * Get the failed count.
		 *
		 * @since 9.9.0
		 * @retrun int
		 */
		public function get_failed_count() {
			return $this->failed;
		}

		/**
		 * Get file pointer position as a percentage of file size.
		 *
		 * @since 9.9.0
		 * @return int
		 */
		public function get_percent_complete() {
			$size = filesize( $this->file );
			if ( ! $size ) {
				return 0;
			}

			return absint( min( floor( ( $this->position / $size ) * 100 ), 100 ) );
		}

		/**
		 * Get the popup header label.
		 *
		 * @since 9.9.0
		 * @return string
		 */
		public function get_popup_header_label() {
			return __( 'Import', 'lottery-for-woocommerce' );
		}

		/**
		 * Output the layout.
		 *
		 * @since 9.9.0
		 */
		public function output() {
			include __DIR__ . '/views/html-import-popup.php';
		}

		/**
		 * Output header.
		 *
		 * @since 9.9.0
		 */
		protected function output_header() {
			include __DIR__ . '/views/html-import-header.php';
		}

		/**
		 * Output steps.
		 *
		 * @since 9.9.0
		 */
		protected function output_steps() {
			include __DIR__ . '/views/html-import-steps.php';
		}

		/**
		 * Output errors.
		 *
		 * @since 9.9.0
		 */
		protected function output_errors() {
			include __DIR__ . '/views/html-import-errors.php';
		}

		/**
		 * Upload form.
		 *
		 * @since 9.9.0
		 */
		protected function upload_form() {
			include __DIR__ . '/views/html-upload-form.php';
		}

		/**
		 * Import.
		 *
		 * @since 9.9.0
		 */
		protected function import() {
			include __DIR__ . '/views/html-import-progress-form.php';
		}

		/**
		 * Done.
		 *
		 * @since 9.9.0
		 */
		protected function done() {
			include __DIR__ . '/views/html-import-done.php';
		}

		/**
		 * Get the steps.
		 *
		 * @since 9.9.0
		 * @return string
		 */
		public function get_steps() {
			return $this->steps;
		}

		/**
		 * Get the errors.
		 *
		 * @since 9.9.0
		 * @return array
		 */
		public function get_errors() {
			return $this->errors;
		}

		/**
		 * Add a error message.
		 *
		 * @since 9.9.0
		 * @param string $message
		 */
		public function add_error( $message ) {
			$this->errors[] = $message;
		}

		/**
		 * Get the current step view.
		 *
		 * @since 9.9.0
		 * @return string/array
		 */
		public function get_current_step_view() {
			return $this->steps[ $this->step ]['view'];
		}

		/**
		 * Get the step classes.
		 *
		 * @since 9.9.0
		 * @param string $step_key
		 * @return string
		 */
		public function get_step_classes( $step_key ) {
			$classes = array( 'lty-import-step' );
			if ( $this->step === $step_key ) {
				$classes[] = 'lty-active';
			} elseif ( array_search( $this->step, array_keys( $this->steps ), true ) > array_search( $step_key, array_keys( $this->steps ), true ) ) {
				$classes[] = 'lty-done';
			}

			return implode( ' ', $classes );
		}

		/**
		 * Get the move next step.
		 *
		 * @since 9.9.0
		 * @param string $current_step
		 *
		 * @return string.
		 */
		public function get_move_next_step( $current_step = 'upload' ) {
			$keys       = array_keys( $this->steps );
			$step_index = array_search( $current_step, $keys, true );

			$this->step = $keys[ $step_index + 1 ];
		}

		/**
		 * Process the upload files.
		 *
		 * @since 9.9.0
		 * @param array $files
		 */
		public function process_upload( $files ) {
			$file = $this->handle_upload( $files );
			if ( is_wp_error( $file ) && $file->has_errors() ) {
				$this->add_error( $file->get_error_message() );
				return;
			}

			$this->file = $file;
			$this->get_move_next_step();
		}

		/**
		 * Handle the upload files.
		 *
		 * @since 9.9.0
		 * @param array $files
		 */
		public function handle_upload( $files ) {
			if ( ! isset( $files['import'] ) ) {
				return new WP_Error( 'lty_importer_upload_file_empty', __( 'File is empty. Please upload something more substantial. This error could also be caused by uploads being disabled in your php.ini or by post_max_size being defined as smaller than upload_max_filesize in php.ini.', 'lottery-for-woocommerce' ) );
			}

			if ( ! self::is_file_valid( wc_clean( wp_unslash( $files['import']['name'] ) ), false ) ) {
				return new WP_Error( 'lty_importer_upload_file_invalid', __( 'Invalid file type. The importer supports CSV and TXT file formats.', 'lottery-for-woocommerce' ) );
			}

			$overrides = array(
				'test_form' => false,
				'mimes'     => self::get_valid_filetypes(),
			);

			$import = $files['import'];
			$upload = wp_handle_upload( $import, $overrides );

			if ( isset( $upload['error'] ) ) {
				return new WP_Error( 'lty_importer_upload_error', $upload['error'] );
			}

			// Construct the object array.
			$object = array(
				'post_title'     => basename( $upload['file'] ),
				'post_content'   => $upload['url'],
				'post_mime_type' => $upload['type'],
				'guid'           => $upload['url'],
				'context'        => 'import',
				'post_status'    => 'private',
			);

			// Save the data.
			$id = wp_insert_attachment( $object, $upload['file'] );

			/*
			 * Schedule a cleanup for one day from now in case of failed
			 * import or missing wp_import_cleanup() call.
			 */
			wp_schedule_single_event( time() + DAY_IN_SECONDS, 'importer_scheduled_cleanup', array( $id ) );

			return $upload['file'];
		}

		/**
		 * Get the valid file types.
		 *
		 * @since 9.9.0
		 * @return array
		 */
		public function get_valid_filetypes() {
			return array(
				'csv' => 'text/csv',
				'txt' => 'text/plain',
			);
		}

		/**
		 * Check whether a file is a valid file.
		 *
		 * @since 9.9.0
		 * @param string $file
		 * @param bool   $check_path Whether to also check the file is located in a valid location (Default: true).
		 * @return bool
		 */
		public static function is_file_valid( $file, $check_path = true ) {
			return wc_is_file_valid_csv( $file, $check_path );
		}

		/**
		 * Get the WP maximum upload size.
		 *
		 * @since 9.9.0
		 * @return float
		 */
		public function get_wp_max_upload_size() {
			return wp_max_upload_size();
		}

		/**
		 * Convert a string from the input encoding to UTF-8.
		 *
		 * @since 9.9.0
		 * @param string $value The string to convert.
		 * @return string The converted string.
		 */
		private function adjust_character_encoding( $value ) {
			return 'UTF-8' === $this->character_encoding ? $value : mb_convert_encoding( $value, 'UTF-8', $this->character_encoding );
		}

		/**
		 * Read file.
		 *
		 * @since 9.9.0
		 */
		public function read_file() {
			if ( ! self::is_file_valid( $this->file ) ) {
				wp_die( esc_html__( 'Invalid file type. The importer supports CSV and TXT file formats.', 'lottery-for-woocommerce' ) );
			}

			$handle = fopen( $this->file, 'r' );

			if ( false !== $handle ) {
				$this->file_headers = array_map( 'trim', fgetcsv( $handle, 0, $this->delimiter, $this->enclosure, $this->escape ) );

				if ( $this->character_encoding ) {
					$this->file_headers = array_map( array( $this, 'adjust_character_encoding' ), $this->file_headers );
				}

				// Remove line breaks in keys, to avoid mismatch mapping of keys.
				$this->file_headers = wc_clean( wp_unslash( $this->file_headers ) );

				// Remove BOM signature from the first item.
				if ( isset( $this->file_headers[0] ) ) {
					$this->file_headers[0] = $this->remove_utf8_bom( $this->file_headers[0] );
				}

				if ( 0 !== $this->position ) {
					fseek( $handle, (int) $this->position );
				}

				$index       = 1;
				$map_columns = $this->get_map_columns();
				while ( $index < $this->limit ) {
					$row = fgetcsv( $handle, 0, $this->delimiter, $this->enclosure, $this->escape );

					if ( false !== $row ) {
						if ( $this->character_encoding ) {
							$row = array_map( array( $this, 'adjust_character_encoding' ), $row );
						}

						$data = array();
						foreach ( $row as $id => $value ) {
							if ( ! empty( $this->file_headers[ $id ] ) && isset( $map_columns[ $this->file_headers[ $id ] ] ) ) {
								$data[ $map_columns[ $this->file_headers[ $id ] ] ] = $value;
							}
						}

						$this->parsed_data[] = $data;

						++$index;
					} else {
						break;
					}
				}

				$this->position = ftell( $handle );
			}
		}

		/**
		 * Run import.
		 *
		 * @since 9.9.0
		 */
		public function run_import() {
			$this->read_file();

			$data = array(
				'imported' => array(),
				'updated'  => array(),
				'failed'   => array(),
			);

			if ( ! defined( 'WP_IMPORTING' ) ) {
				define( 'WP_IMPORTING', true );
			}

			wp_defer_term_counting( true );
			wp_defer_comment_counting( true );

			foreach ( $this->parsed_data as $item ) {
				$result = $this->process_item( $item );

				if ( is_wp_error( $result ) ) {
					$result->add_data( $this->get_row_id( $item ) );
					$data['failed'][] = $result;
					++$this->failed;
				} elseif ( isset( $result['updated'] ) ) {
					$data['updated'][] = $result['updated'];
					++$this->updated;
				} else {
					$data['imported'][] = $result['imported'];
					++$this->imported;
				}
			}

			wp_defer_term_counting( false );
			wp_defer_comment_counting( false );

			$this->update_user_error_log( $data );

			/**
			 * This hook is used to do extra action after import completed.
			 *
			 * @since 11.9.0
			 * @param LTY_Importer $this Importer object.
			 */
			do_action( 'lty_lottery_' . $this->get_action_type() . '_imported', $this );

			return $data;
		}

		/**
		 * Get a string to identify the row from parsed data.
		 *
		 * @since 9.9.0
		 * @param array $parsed_data Parsed data.
		 * @return string
		 */
		protected function get_row_id( $parsed_data ) {
			$id            = isset( $parsed_data['id'] ) ? absint( $parsed_data['id'] ) : 0;
			$ticket_number = isset( $parsed_data['ticket_number'] ) ? esc_attr( $parsed_data['ticket_number'] ) : '';
			$row_data      = array();

			if ( $id ) {
				/* translators: %d: rule ID */
				$row_data[] = sprintf( __( 'ID - %d', 'lottery-for-woocommerce' ), $id );
			}

			if ( $ticket_number ) {
				/* translators: %s: Ticket Number */
				$row_data[] = sprintf( __( 'Ticket Number - %s', 'lottery-for-woocommerce' ), $ticket_number );
			}

			return implode( ', ', $row_data );
		}

		/**
		 * Gat user error log.
		 *
		 * @since 9.9.0
		 * @return array
		 */
		protected function get_user_error_log() {
			return array_filter( (array) get_user_option( 'lty_import_error_log' ) );
		}

		/**
		 * Update user error log.
		 *
		 * @since 9.9.0
		 * @param array $data
		 */
		protected function update_user_error_log( $data ) {
			$error_log = ( isset( $_REQUEST['position'] ) && 0 !== absint( $_REQUEST['position'] ) ) ? $this->get_user_error_log() : array();
			$error_log = array_merge( $error_log, $data['failed'] );

			update_user_option( get_current_user_id(), 'lty_import_error_log', $error_log );
		}

		/**
		 * Remove UTF-8 BOM signature.
		 *
		 * @since 9.9.0
		 * @param string $string
		 * @return string
		 */
		protected function remove_utf8_bom( $string ) {
			return ( 'efbbbf' === substr( bin2hex( $string ), 0, 6 ) ) ? substr( $string, 3 ) : $string;
		}

		/**
		 * Memory exceeded
		 *
		 * Ensures the batch process never exceeds 90% of the maximum WordPress memory.
		 *
		 * @since 9.9.0
		 * @return bool
		 */
		protected function memory_exceeded() {
			return ( memory_get_usage( true ) >= ( $this->get_memory_limit() * 0.9 ) ) ? true : false;
		}

		/**
		 * Get memory limit
		 *
		 * @since 9.9.0
		 * @return int
		 */
		protected function get_memory_limit() {
			if ( function_exists( 'ini_get' ) ) {
				$memory_limit = ini_get( 'memory_limit' );
			} else {
				// Sensible default.
				$memory_limit = '128M';
			}

			if ( ! $memory_limit || -1 === intval( $memory_limit ) ) {
				// Unlimited, set to 32GB.
				$memory_limit = '32000M';
			}

			return intval( $memory_limit ) * 1024 * 1024;
		}

		/**
		 * Get the limit.
		 *
		 * @since 10.8.0
		 * @return int
		 */
		public function get_limit() {
			return $this->limit;
		}
	}

}
