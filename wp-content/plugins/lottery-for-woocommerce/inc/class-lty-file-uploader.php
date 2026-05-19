<?php
/**
 * File Uploader.
 *
 * @since 9.5.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'LTY_File_Uploader' ) ) {

	/**
	 * Class.
	 *
	 * @since 9.5.0
	 */
	class LTY_File_Uploader {

		/**
		 * Folder name.
		 *
		 * @since 9.5.0
		 * @var string
		 */
		protected $folder_name = 'lottery-uploads';

		/**
		 * File name.
		 *
		 * @since 9.5.0
		 * @var string
		 */
		protected $file_name;

		/**
		 * Data.
		 *
		 * @since 9.5.0
		 * @var string
		 */
		protected $data;

		/**
		 * Extension.
		 *
		 * @since 9.5.0
		 * @var string
		 */
		protected $extension;

		/**
		 * Current file name.
		 *
		 * @since 9.5.0
		 * @var string
		 */
		protected $current_file_name;

		/**
		 * File name count.
		 *
		 * @since 9.5.0
		 * @var int
		 */
		protected $file_name_count = 0;

		/**
		 * Folder path.
		 *
		 * @since 9.5.0
		 * @var string
		 */
		protected $folder_path;

		/**
		 * Folder URL.
		 *
		 * @since 9.5.0
		 * @var string
		 */
		protected $folder_url;

		/**
		 * Folder type.
		 *
		 * @since 9.5.0
		 * @var string
		 */
		protected $folder_type;

		/**
		 * Constructor
		 *
		 * @since 9.5.0
		 * @param string $file_name File name.
		 * @param string $folder_type Folder type.
		 */
		public function __construct( $file_name, $folder_type ) {
			$this->file_name         = $file_name;
			$this->current_file_name = $file_name;
			$this->folder_type       = $folder_type;
		}

		/**
		 * Upload the data URI as a file.
		 *
		 * @since 9.5.0
		 * @param string $file_name File name.
		 * @param string $data_uri Data URI.
		 * @param string $folder_type Folder type.
		 * @return string|URL
		 */
		public static function upload_data_uri_as_file( $file_name, $data_uri, $folder_type = 'default' ) {
			$uploader = new self( $file_name, $folder_type );

			return $uploader->upload_file( $data_uri );
		}

		/**
		 * Prepare PDF File Name.
		 *
		 * @since 9.5.0
		 * @param string $file_name File name.
		 * @param string $folder_type Folder type.
		 * @return string File path
		 */
		public static function prepare_pdf_file_name( $file_name, $folder_type = 'default' ) {
			$uploader = new self( $file_name, $folder_type );
			$uploader->make_directory();

			return $uploader->prepare_file_path();
		}

		/**
		 * Get the folder path.
		 *
		 * @since 9.5.0
		 * @return string
		 */
		protected function get_folder_path() {
			$upload_dir  = wp_upload_dir();
			$base_folder = $upload_dir['basedir'] . '/' . $this->get_folder_name();

			return $base_folder . $this->get_subfolder_name();
		}

		/**
		 * Get the folder URL.
		 *
		 * @since 9.5.0
		 * @return string
		 */
		protected function get_folder_url() {
			$upload_dir  = wp_upload_dir();
			$base_folder = $upload_dir['baseurl'] . '/' . $this->get_folder_name();

			return $base_folder . $this->get_subfolder_name();
		}

		/**
		 * Get the subfolder name.
		 *
		 * @since 9.5.0
		 * @return string
		 */
		protected function get_subfolder_name() {
			if ( 'pdf' === $this->folder_type ) {
				return '/pdf';
			}

			return '/' . gmdate( 'Y' ) . '/' . gmdate( 'j' );
		}

		/**
		 * Make a folder to upload files.
		 *
		 * @since 9.5.0
		 * @return bool
		 */
		protected function make_directory() {
			if ( ! file_exists( $this->get_folder_path() ) ) {
				return wp_mkdir_p( $this->get_folder_path() );
			}

			return true;
		}

		/**
		 * Prepare the file URL.
		 *
		 * @since 9.5.0
		 * @return string
		 */
		protected function prepare_file_url() {
			return $this->get_folder_url() . '/' . $this->get_current_file_name() . '.' . $this->get_extension();
		}

		/**
		 * Prepare the file path.
		 *
		 * @since 9.5.0
		 * @return string
		 */
		protected function prepare_file_path() {
			return $this->get_folder_path() . '/' . $this->get_current_file_name() . '.' . $this->get_extension();
		}

		/**
		 * Get the folder name.
		 *
		 * @since 9.5.0
		 * @return string
		 */
		protected function get_folder_name() {
			return $this->folder_name;
		}

		/**
		 * Get the file extension.
		 *
		 * @since 9.5.0
		 * @return string
		 */
		protected function get_extension() {
			if ( 'pdf' === $this->folder_type ) {
				$this->extension = 'pdf';
			} else {
				$this->extension = 'jpg';
			}

			return $this->extension;
		}

		/**
		 * Get the data.
		 *
		 * @since 9.5.0
		 * @return string
		 */
		protected function get_data() {
			return $this->data;
		}

		/**
		 * Get the file name.
		 *
		 * @since 9.5.0
		 * @return string
		 */
		protected function get_file_name() {
			return sanitize_file_name( $this->file_name );
		}

		/**
		 * Get the current file name.
		 *
		 * @since 9.5.0
		 * @return string
		 */
		protected function get_current_file_name() {
			return sanitize_file_name( $this->current_file_name );
		}

		/**
		 * Change the file name.
		 *
		 * @since 9.5.0
		 * @return void
		 */
		protected function change_file_name() {
			$this->current_file_name = sanitize_file_name( $this->file_name ) . '-' . ( $this->file_name_count + 1 );
		}

		/**
		 * Upload the file in the folder.
		 *
		 * @since 9.5.0
		 * @param string $data_uri Image URL.
		 * @return string
		 */
		protected function upload_file( $data_uri ) {
			// Make a directory if it does not exists.
			$this->make_directory();

			// Extract data uri.
			$this->extract_data_uri( $data_uri );
			$file_path = $this->prepare_file_path();
			if ( file_exists( $file_path ) && 'default' !== $this->folder_type ) {
				return $this->prepare_file_url();
			}

			while ( file_exists( $file_path ) ) {
				$this->change_file_name();
				$file_path = $this->prepare_file_path();
			}

			// Move the content into the new file.
			$bytes_count = file_put_contents( $file_path, $this->get_data() );
			if ( ! $bytes_count ) {
				return '';
			}

			return $this->prepare_file_url();
		}

		/**
		 * Extract data URI as file extension and data.
		 *
		 * @since 9.5.0
		 * @param string $data_uri URL.
		 */
		protected function extract_data_uri( $data_uri ) {
			$data_uri = explode( ',', $data_uri, 2 );

			// Extension.
			$extension       = explode( ';', $data_uri[0] );
			$this->extension = str_replace( 'data:image/', '', $extension[0] );

			// Data.
			$encoded_data = str_replace( ' ', '+', $data_uri[1] );
			$this->data   = base64_decode( $encoded_data );
		}
	}

}
