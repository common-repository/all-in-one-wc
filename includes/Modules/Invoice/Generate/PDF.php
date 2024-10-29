<?php
/**
 * Invoicing Module - PDF Invoice
 *
 * @package WordPress
 * @subpackage WooCommerce
 */

namespace AIOW\Modules\Invoice\Generate;

if ( ! class_exists( 'PDF' ) ) {

	/**
	 * Declare class `PDF` extends to `Invoice`.
	 */
	class PDF extends Invoice {

		/**
		 * Original onternal coding.
		 *
		 * @var string $original_internal_coding
		 */
		private $original_internal_coding = '';

		/**
		 * Constructor.
		 *
		 * @param int    $order_id Order ID.
		 * @param string $invoice_type Invoice_type.
		 */
		function __construct( $order_id, $invoice_type ) {
			parent::__construct( $order_id, $invoice_type );
		}

		/**
		 * Prepare PDF.
		 */
		function prepare_pdf() {

			aiow_check_and_maybe_download_tcpdf_fonts();

			$invoice_type = $this->invoice_type;

			$page_format = aiow_option( 'aiow_invoicing_' . $invoice_type . '_page_format', 'A4' );
			if ( 'custom' === $page_format ) {
				$page_format = array(
					get_option( 'aiow_invoicing_' . $invoice_type . '_page_format_custom_width',  0 ),
					get_option( 'aiow_invoicing_' . $invoice_type . '_page_format_custom_height', 0 ),
				);
			}

			// Create new PDF document
			$pdf = new TC_PDF(
				get_option( 'aiow_invoicing_' . $invoice_type . '_page_orientation', 'P' ),
				PDF_UNIT,
				$page_format,
				true,
				'UTF-8',
				false
			);

			$pdf->set_invoice_type( $invoice_type );

			// Set document information.
			$pdf->SetCreator( PDF_CREATOR );
			$invoice_title = $invoice_type;
			$invoice_types = aiow_get_invoice_types();
			foreach ( $invoice_types as $invoice_type_data ) {
				if ( $invoice_type === $invoice_type_data['id'] ) {
					$invoice_title = $invoice_type_data['title'];
					break;
				}
			}
			$pdf->SetTitle( $invoice_title );
			$pdf->SetSubject( 'Invoice PDF' );
			$pdf->SetKeywords( 'invoice, PDF' );

			// Header - set default header data
			if ( 'yes' === aiow_option( 'aiow_invoicing_' . $invoice_type . '_header_enabled', 'yes' ) ) {
				$the_logo = '';
				$the_logo_width_mm = 0;
				if ( '' != ( $header_image = do_shortcode( aiow_option( 'aiow_invoicing_' . $invoice_type . '_header_image', '' ) ) ) ) {
					$the_logo = parse_url( $header_image, PHP_URL_PATH );
					$the_logo_width_mm = aiow_option( 'aiow_invoicing_' . $invoice_type . '_header_image_width_mm', 50 );
					if ( ! file_exists( K_PATH_IMAGES . $the_logo ) ) {
						$the_logo = '';
						$the_logo_width_mm = 0;
					}
				}
				$pdf->SetHeaderData(
					$the_logo,
					$the_logo_width_mm,
					do_shortcode( aiow_option( 'aiow_invoicing_' . $invoice_type . '_header_title_text', $invoice_title ) ),
					do_shortcode( aiow_option( 'aiow_invoicing_' . $invoice_type . '_header_text'      , __( 'Company Name', 'all-in-one-wc' ) ) ),
					aiow_hex2rgb(  aiow_option( 'aiow_invoicing_' . $invoice_type . '_header_text_color', '#cccccc' ) ),
					aiow_hex2rgb(  aiow_option( 'aiow_invoicing_' . $invoice_type . '_header_line_color', '#cccccc' ) ) );
			} else {
				$pdf->SetPrintHeader( false );
			}

			// Footer
			if ( 'yes' === aiow_option( 'aiow_invoicing_' . $invoice_type . '_footer_enabled', 'yes' ) ) {
				$pdf->setFooterData(
					aiow_hex2rgb( aiow_option( 'aiow_invoicing_' . $invoice_type . '_footer_text_color', '#cccccc' ) ),
					aiow_hex2rgb( aiow_option( 'aiow_invoicing_' . $invoice_type . '_footer_line_color', '#cccccc' ) )
				);
			} else {
				$pdf->SetPrintFooter( false );
			}

			$tcpdf_font = aiow_get_tcpdf_font( $invoice_type );

			// Set Header and Footer fonts.
			$pdf->setHeaderFont( array( $tcpdf_font, '', PDF_FONT_SIZE_MAIN ) );
			$pdf->setFooterFont( array( $tcpdf_font, '', PDF_FONT_SIZE_DATA ) );

			// Set default monospaced font.
			$pdf->SetDefaultMonospacedFont( PDF_FONT_MONOSPACED );

			// Set margins.
			$pdf->SetMargins(
				get_option( 'aiow_invoicing_' . $invoice_type . '_margin_left',  15 ),
				get_option( 'aiow_invoicing_' . $invoice_type . '_margin_top',   27 ),
				get_option( 'aiow_invoicing_' . $invoice_type . '_margin_right', 15 )
			);
			$pdf->SetHeaderMargin( aiow_option( 'aiow_invoicing_' . $invoice_type . '_margin_header', 10 ) );
			$pdf->SetFooterMargin( aiow_option( 'aiow_invoicing_' . $invoice_type . '_margin_footer', 10 ) );

			// Set auto page breaks.
			$pdf->SetAutoPageBreak( true, aiow_option( 'aiow_invoicing_' . $invoice_type . '_margin_bottom', 10 ) );

			// Set image scale factor.
			$pdf->setImageScale( PDF_IMAGE_SCALE_RATIO );

			// Set default font subsetting mode.
			$pdf->setFontSubsetting( true );

			// Set font.
			$pdf->SetFont( $tcpdf_font, '', aiow_option( 'aiow_invoicing_' . $invoice_type . '_general_font_size', 8 ), '', true );

			// Add a page.
			$pdf->AddPage();

			// Set text shadow effect.
			if ( 'yes' === aiow_option( 'aiow_invoicing_' . $invoice_type . '_general_font_shadowed', 'no' ) ) {
				$pdf->setTextShadow( array( 'enabled' => true, 'depth_w' => 0.2, 'depth_h' => 0.2, 'color' => array( 196, 196, 196 ), 'opacity' => 1, 'blend_mode' => 'Normal' ) );
			}

			// Background image.
			if ( '' != ( $background_image = do_shortcode( aiow_option( 'aiow_invoicing_' . $invoice_type . '_background_image', '' ) ) ) ) {
				$background_image = 'yes' === ( $parse_bkg_image = aiow_option( 'aiow_invoicing_' . $invoice_type . '_background_image_parse', 'yes' ) ) ? $_SERVER['DOCUMENT_ROOT'] . parse_url( $background_image, PHP_URL_PATH ) : $background_image;
				$pdf->Image( $background_image, 0, 0, $pdf->getPageWidth(), $pdf->getPageHeight() );
			}

			return $pdf;
		}

		/**
		 * Maybe replace TCPDF method.
		 *
		 * @param string $html HTML.
		 * @param object $pdf PDF.
		 * @return string
		 */
		function maybe_replace_tcpdf_method_params( $html, $pdf ) {
			$start_str        = 'aiow_tcpdf_method_params_start';
			$end_str          = 'aiow_tcpdf_method_params_end';
			$start_str_length = strlen( $start_str );
			$end_str_length   = strlen( $end_str );
			while ( false !== ( $start = strpos( $html, $start_str ) ) ) {
				$params_start  = $start + $start_str_length;
				$params_length = strpos( $html, $end_str ) - $params_start;
				$params        = $pdf->serializeTCPDFtagParameters( unserialize( substr( $html, $params_start, $params_length ) ) );
				$html          = substr_replace( $html, 'params="' . $params . '"', $start, $start_str_length + $params_length + $end_str_length );
			}
			return $html;
		}

		/**
		 * Gets invoice content HTML.
		 *
		 * @param int    $order_id Order ID.
		 * @param object $pdf PDF object.
		 * @return string
		 */
		function get_html( $order_id, $pdf ) {
			$this->original_internal_coding = mb_internal_encoding();
			if ( ! empty( $internal_encoding = aiow_option( 'aiow_general_advanced_mb_internal_encoding', '' ) ) ) {
				mb_internal_encoding( $internal_encoding );
			}
			$_GET['order_id'] = $order_id;
			$the_order        = wc_get_order( $order_id );
			if ( ! isset( $_GET['billing_country'] ) ) {
				$_GET['billing_country'] = ( AIOW_IS_WC_VERSION_BELOW_3 ? $the_order->billing_country : $the_order->get_billing_country() );
			}
			if ( ! isset( $_GET['payment_method'] ) ) {
				$_GET['payment_method'] = aiow_order_get_payment_method( $the_order );
			}
			global $aiow_pdf_invoice_data;
			if ( ! isset( $aiow_pdf_invoice_data['user_id'] ) ) {
				$aiow_pdf_invoice_data['user_id'] = ( AIOW_IS_WC_VERSION_BELOW_3 ? $the_order->customer_user : $the_order->get_customer_id() );
			}
			$html = do_shortcode( aiow_option( 'aiow_invoicing_' . $this->invoice_type . '_template', AIOW()->modules['pdf_invoicing_templates']->get_default_template( $this->invoice_type ) ) );
			$html = $this->maybe_replace_tcpdf_method_params( $html, $pdf );
			$html = force_balance_tags( $html );
			mb_internal_encoding( $this->original_internal_coding );
			return $html;
		}

		/**
		 * Get PDF.
		 *
		 * @param string $dest Dest.
		 * @return string
		 */
		function get_pdf( $dest ) {
			error_reporting( 0 );
			ini_set( 'display_errors', 0 );
			$pdf     = $this->prepare_pdf();
			$html    = $this->get_html( $this->order_id, $pdf );
			$styling = '<style>' . aiow_option( 'aiow_invoicing_' . $this->invoice_type . '_css', AIOW()->modules['pdf_invoicing_styling']->get_default_css_template( $this->invoice_type ) ) . '</style>';
			$pdf->writeHTMLCell( 0, 0, '', '', $styling . $html, 0, 1, 0, true, '', true );
			$result_pdf = $pdf->Output( '', 'S' );
			$file_name  = $this->get_file_name();
			if ( 'F' === $dest ) {
				$file_path = aiow_get_invoicing_temp_dir() . '/' . $file_name;
				if ( ! file_put_contents( $file_path, $result_pdf ) ) {
					return null;
				}
				return $file_path;
			} elseif ( 'D' === $dest || 'I' === $dest ) {
				if ( 'D' === $dest ) {
					header( "Content-Type: application/octet-stream" );
					header( "Content-Disposition: attachment; filename=" . urlencode( $file_name ) );
					header( "Content-Type: application/octet-stream" );
					header( "Content-Type: application/download" );
					header( "Content-Description: File Transfer" );
				} elseif ( 'I' === $dest ) {
					header( "Content-type: application/pdf" );
					header( "Content-Disposition: inline; filename=" . urlencode( $file_name ) );
				}
				if ( 'yes' === aiow_option( 'aiow_general_advanced_disable_save_sys_temp_dir', 'no' ) ) {
					header( "Content-Length: " . strlen( $result_pdf ) );
					echo $result_pdf;
				} else {
					$file_path = aiow_get_invoicing_temp_dir() . '/' . $file_name;
					if ( ! file_put_contents( $file_path, $result_pdf ) ) {
						return null;
					}
					if ( apply_filters( 'aiow_invoicing_header_content_length', true ) ) {
						header( "Content-Length: " . filesize( $file_path ) );
					}
					flush(); // this doesn't really matter.
					if ( false !== ( $fp = fopen( $file_path, "r" ) ) ) {
						while ( ! feof( $fp ) ) {
							echo fread( $fp, 65536 );
							flush(); // this is essential for large downloads.
						}
						fclose( $fp );
					} else {
						die( __( 'Unexpected error', 'all-in-one-wc' ) );
					}
				}
			}
			return null;
		}
	}
}
