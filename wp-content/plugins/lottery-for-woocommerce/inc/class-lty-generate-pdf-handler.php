<?php
/**
 * PDF Handler.
 *
 * @since 9.5.0
 */

defined( 'ABSPATH' ) || exit;

require_once dirname( LTY_PLUGIN_FILE ) . '/vendor/mpdf/autoload.php';

if ( ! class_exists( 'LTY_Generate_PDF_Handler' ) ) {

	/**
	 * Class.
	 *
	 * @since 9.5.0
	 */
	class LTY_Generate_PDF_Handler {

		/**
		 * Mode.
		 *
		 * @since 9.5.0
		 * @var string
		 */
		private $mode = 'utf-8';

		/**
		 * Format.
		 *
		 * @since 9.5.0
		 * @var string
		 */
		private $format = 'a4';

		/**
		 * Orientation.
		 *
		 * @since 9.5.0
		 * @var string
		 */
		private $orientation = 'P';

		/**
		 * PDF.
		 *
		 * @since 9.5.0
		 * @var object
		 * */
		private $pdf;

		/**
		 * File name.
		 *
		 * @since 9.5.0
		 * @var string
		 */
		protected $file_name;

		/**
		 * Type.
		 *
		 * @since 9.5.0
		 * @var string
		 */
		protected $type;

		/**
		 * Product ID.
		 *
		 * @since 9.5.0
		 * @var int
		 */
		protected $product;

		/**
		 * Product ID.
		 *
		 * @since 9.5.0
		 * @var int
		 */
		protected $product_id;

		/**
		 * Ticket IDs.
		 *
		 * @since 9.5.0
		 * @var array
		 */
		protected $ticket_ids;

		/**
		 * Order ID.
		 *
		 * @since 9.5.0
		 * @var int|string
		 */
		protected $order_id;

		/**
		 * Class initialization.
		 *
		 * @since 9.5.0
		 * @param string $type Type.
		 * @param object $product Product object.
		 * @param int    $order_id Order ID.
		 * @param array  $ticket_ids Ticket IDs.
		 * @return void
		 */
		public function __construct( $type, $product = false, $order_id = false, $ticket_ids = array() ) {
			$this->type = $type;
			if ( $product ) {
				$product = is_object( $product ) ? $product : wc_get_product( $product );
				if ( is_object( $product ) ) {
					$this->product    = $product;
					$this->product_id = $product->get_id();
				}
			}

			if ( $order_id ) {
				$this->order_id = $order_id;
			}

			if ( lty_check_is_array( $ticket_ids ) ) {
				$this->ticket_ids = $ticket_ids;
			}

			$this->pdf = new \Mpdf\Mpdf(
				array(
					'mode'                => $this->mode,
					'orientation'         => $this->orientation,
					'format'              => $this->format,
					'setAutoTopMargin'    => 'stretch',
					'setAutoBottomMargin' => 'stretch',
				)
			);

			$this->pdf->SetHTMLHeader( $this->get_header() );
			$this->pdf->SetHTMLFooter( $this->get_footer() );
		}

		/**
		 * Get file name.
		 *
		 * @since 9.5.0
		 * @param bool $extension Whether to return with the file extension or not.
		 * @return string
		 */
		private function get_file_name( $extension = false ) {
			switch ( $this->type ) {
				case 'lottery_ticket':
					$file_name = str_replace(
						array( '{order_id}', '{tickets_count}', '{date}' ),
						array( $this->order_id, count( $this->ticket_ids ), gmdate( 'Ymd' ) ),
						get_option( 'lty_settings_lottery_ticket_pdf_file_name', __( 'Giveaway Ticket for {order_id}{tickets_count}', 'lottery-for-woocommerce' ) )
					);
					break;

				case 'entry_list':
					$file_name = str_replace(
						array( '{product_name}', '{date}' ),
						array( $this->product->get_product_name(), gmdate( 'Ymd' ) ),
						get_option( 'lty_settings_entry_list_pdf_file_name', __( 'Entry list for {product_name}', 'lottery-for-woocommerce' ) )
					);
					break;
			}

			if ( ! $extension ) {
				return $file_name;
			}

			return $file_name . '.pdf';
		}

		/**
		 * Get pdf header.
		 *
		 * @since 9.5.0
		 * @return string|HTML
		 */
		private function get_header() {
			switch ( $this->type ) {
				case 'lottery_ticket':
					return '<div class="lty-lottery-ticket-pdf-header" style="
					background: ' . get_option( 'lty_settings_ticket_pdf_header_bg_color' ) . '; 
					color: ' . get_option( 'lty_settings_ticket_pdf_header_font_color' ) . '; 
					padding: 10px; display: flex; vertical-align: middle;">' . lty_get_lottery_ticket_pdf_header_details() . '</div>';

				case 'entry_list':
					return '<div class="lty-lottery-entry-list-pdf-header" style="
					background: ' . get_option( 'lty_settings_entry_list_pdf_header_bg_color' ) . '; 
					color: ' . get_option( 'lty_settings_entry_list_pdf_header_font_color' ) . '; 
					padding: 10px; display: flex; vertical-align: middle;">' . lty_get_lottery_entry_list_pdf_header_details( $this->product ) . '</div>';
			}

			return '';
		}

		/**
		 * Get pdf footer.
		 *
		 * @since 9.5.0
		 * @return string|HTML
		 */
		private function get_footer() {
			switch ( $this->type ) {
				case 'lottery_ticket':
					return '<div class="lty-lottery-ticket-pdf-footer" style="
					background: ' . get_option( 'lty_settings_ticket_pdf_footer_bg_color' ) . '; 
					color: ' . get_option( 'lty_settings_ticket_pdf_footer_font_color' ) . '; 
					padding: 10px; font-size: 10px;">' . lty_get_lottery_ticket_pdf_footer_details() . '</div>';

				case 'entry_list':
					return '<div class="lty-lottery-entry-list-pdf-footer" style="
					background: ' . get_option( 'lty_settings_entry_list_pdf_footer_bg_color' ) . '; 
					color: ' . get_option( 'lty_settings_entry_list_pdf_footer_font_color' ) . '; 
					padding: 10px; font-size: 10px;">' . lty_get_lottery_entry_list_pdf_footer_details( $this->product ) . '</div>';
			}

			return '';
		}

		/**
		 * Generate lottery entry list.
		 *
		 * @since 9.5.0
		 * @param int $product_id Product ID.
		 * @return mixed
		 */
		public static function generate_lottery_entry_list( $product_id ) {
			$self = new self( 'entry_list', $product_id );

			return $self->prepare_lottery_entry_list_pdf();
		}

		/**
		 * Generate lottery tickets.
		 *
		 * @since 9.5.0
		 * @param int $ticket_ids Ticket IDs.
		 * @param int $order_id Order ID.
		 * @return mixed
		 */
		public static function generate_lottery_ticket( $ticket_ids, $order_id ) {
			$self = new self( 'lottery_ticket', false, $order_id, $ticket_ids );

			return $self->prepare_lottery_ticket_pdf();
		}

		/**
		 * Download lottery entry list.
		 *
		 * @since 9.5.0
		 * @param int $product_id Product ID.
		 * @return mixed
		 */
		public static function download_lottery_entry_list( $product_id ) {
			$self = new self( 'entry_list', $product_id );

			return $self->download_lottery_entry_list_pdf();
		}

		/**
		 * Download lottery ticket(s).
		 *
		 * @since 9.5.0
		 * @param array      $ticket_ids Ticket IDs.
		 * @param int|string $order_id Order ID.
		 * @return mixed
		 */
		public static function download_lottery_ticket( $ticket_ids, $order_id ) {
			$self = new self( 'lottery_ticket', false, $order_id, $ticket_ids );

			return $self->download_lottery_ticket_pdf();
		}

		/**
		 * Prepare lottery entry list pdf.
		 *
		 * @since 9.5.0
		 * @return void
		 * */
		private function prepare_lottery_entry_list_pdf() {
			$design = $this->get_lottery_entry_list_design( $this->product );
			$this->pdf->SetDisplayMode( 'fullpage' );
			$this->pdf->WriteHTML( $design );

			$this->pdf->Output( LTY_File_Uploader::prepare_pdf_file_name( $this->get_file_name(), 'pdf' ), 'F' );
		}

		/**
		 * Prepare lottery ticket.
		 *
		 * @since 9.5.0
		 * @return void
		 * */
		private function prepare_lottery_ticket_pdf() {
			$design = $this->get_lottery_ticket_design( $this->ticket_ids );

			$this->pdf->SetDisplayMode( 'fullpage' );
			$this->pdf->WriteHTML( $design );

			$this->pdf->Output( LTY_File_Uploader::prepare_pdf_file_name( $this->get_file_name(), 'pdf' ), 'F' );
		}

		/**
		 * Download lottery entry list PDF.
		 *
		 * @since 9.5.0
		 * */
		private function download_lottery_entry_list_pdf() {
			$design = $this->get_lottery_entry_list_design( $this->product );
			$this->pdf->WriteHTML( $design );
			$file_name = __( 'Entry_list_for_', 'lottery-for-woocommerce' ) . $this->product->get_title() . '.pdf';

			$this->pdf->Output( $this->get_file_name( true ), 'D' );
		}

		/**
		 * Download lottery ticket PDF.
		 *
		 * @since 9.5.0
		 * @return void
		 * */
		private function download_lottery_ticket_pdf() {
			$design = $this->get_lottery_ticket_design( $this->ticket_ids );
			$this->pdf->WriteHTML( $design );

			$this->pdf->Output( $this->get_file_name( true ), 'D' );
		}

		/**
		 * Get the lottery entry list design.
		 *
		 * @since 9.5.0
		 * @param object $product Product object.
		 * @return string
		 */
		public static function get_lottery_entry_list_design( $product ) {
			return lty_get_template_html( 'pdf/entry-list.php', lty_prepare_entry_list_pdf_arguments( $product ) );
		}

		/**
		 * Get the lottery ticket design.
		 *
		 * @since 9.5.0
		 * @param array $ticket_ids Ticket IDs.
		 * @return string
		 */
		public static function get_lottery_ticket_design( $ticket_ids ) {
			$design     = lty_get_template_html( 'pdf/lottery-tickets.php', array( 'ticket_ids' => $ticket_ids ) );
			$custom_css = lty_get_template_html( 'pdf/custom-css.php' );

			return lty_add_html_inline_style( $design, $custom_css );
		}
	}

}
