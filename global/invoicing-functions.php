<?php
/**
 * Module Invoicing - Functions.
 *
 * @package WordPress
 * @subpackage WooCommerce
 */

if ( ! function_exists( 'aiow_get_invoicing_temp_dir' ) ) {
	/**
	 * Get invoicing temp dir.
	 */
	function aiow_get_invoicing_temp_dir() {
		return ( '' === ( $tmp_dir = aiow_option( 'aiow_invoicing_general_tmp_dir', '' ) ) ? sys_get_temp_dir() : $tmp_dir );
	}
}

if ( ! function_exists( 'aiow_get_invoicing_current_image_path_desc' ) ) {
	/**
	 * Get current invoice image path.
	 *
	 * @param string $option_name Option name.
	 * @return string
	 */
	function aiow_get_invoicing_current_image_path_desc( $option_name ) {
		if ( '' != ( $current_image = aiow_option( $option_name, '' ) ) ) {
			if ( false !== ( $default_images_directory = aiow_get_invoicing_default_images_directory() ) ) {
				$image_path = $default_images_directory . parse_url( $current_image, PHP_URL_PATH );
				$style      = ( file_exists( $image_path ) ? ' style="color:green;"' : '' );
				$current_image = '<br>' . sprintf( __( 'Current image path: %s.', 'all-in-one-wc' ), '<code' . $style . '>' . $image_path . '</code>' );
			} else {
				$current_image = '';
			}
		}
		return $current_image;
	}
}

if ( ! function_exists( 'aiow_get_invoicing_default_images_directory' ) ) {
	/**
	 * Get default image dir.
	 */
	function aiow_get_invoicing_default_images_directory() {
		switch ( aiow_option( 'aiow_invoicing_general_header_images_path', 'document_root' ) ) {
			case 'empty':
				return '';
			case 'document_root':
				return $_SERVER['DOCUMENT_ROOT'];
			case 'abspath':
				return ABSPATH;
			default: // 'tcpdf_default'
				return false;
		}
	}
}

if ( ! function_exists( 'aiow_get_fonts_list' ) ) {
	/**
	 * PDF font list.
	 *
	 * @return array
	 */
	function aiow_get_fonts_list() {
		return array(
			'angsanaupc.ctg.z',
			'angsanaupc.php',
			'angsanaupc.z',
			'angsanaupcb.ctg.z',
			'angsanaupcb.php',
			'angsanaupcb.z',
			'angsanaupcbi.ctg.z',
			'angsanaupcbi.php',
			'angsanaupcbi.z',
			'angsanaupci.ctg.z',
			'angsanaupci.php',
			'angsanaupci.z',
			'cid0ct.php',
			'cordiaupc.ctg.z',
			'cordiaupc.php',
			'cordiaupc.z',
			'cordiaupcb.ctg.z',
			'cordiaupcb.php',
			'cordiaupcb.z',
			'cordiaupcbi.ctg.z',
			'cordiaupcbi.php',
			'cordiaupcbi.z',
			'cordiaupci.ctg.z',
			'cordiaupci.php',
			'cordiaupci.z',
			'courier.php',
			'courierb.php',
			'courierbi.php',
			'courieri.php',
			'dejavusans.ctg.z',
			'dejavusans.php',
			'dejavusans.z',
			'dejavusansb.ctg.z',
			'dejavusansb.php',
			'dejavusansb.z',
			'dejavusansbi.ctg.z',
			'dejavusansbi.php',
			'dejavusansbi.z',
			'droidsansfallback.ctg.z',
			'droidsansfallback.php',
			'droidsansfallback.z',
			'helvetica.php',
			'helveticab.php',
			'helveticabi.php',
			'helveticai.php',
			'stsongstdlight.php',
			'symbol.php',
			'thsarabun.ctg.z',
			'thsarabun.php',
			'thsarabun.z',
			'thsarabunb.ctg.z',
			'thsarabunb.php',
			'thsarabunb.z',
			'thsarabunbi.ctg.z',
			'thsarabunbi.php',
			'thsarabunbi.z',
			'thsarabuni.ctg.z',
			'thsarabuni.php',
			'thsarabuni.z',
			'times.php',
			'timesb.php',
			'timesbi.php',
			'timesi.php',
			'uni2cid_aj16.php',
			'zapfdingbats.php',
		);
	}
}

if ( ! function_exists( 'aiow_get_tcpdf_font' ) ) {
	/**
	 * Get PDF font.
	 *
	 * @param string $invoice_type Invoice type.
	 * @return string
	 */
	function aiow_get_tcpdf_font( $invoice_type ) {
		return (  aiow_check_tcpdf_fonts_version( true ) ?
			get_option( 'aiow_invoicing_' . $invoice_type . '_general_font_family', 'helvetica' ) :
			get_option( 'aiow_invoicing_' . $invoice_type . '_general_font_family_fallback', 'helvetica' )
		);
	}
}

if ( ! function_exists( 'aiow_get_tcpdf_fonts_version' ) ) {
	/**
	 * Get PDF font version.
	 *
	 * @return string
	 */
	function aiow_get_tcpdf_fonts_version() {
		return '2.9.0';
	}
}

if ( ! function_exists( 'aiow_check_tcpdf_fonts_version' ) ) {
	/**
	 * Check PDF font version.
	 *
	 * @package bool $force_file_check  Force file check.
	 * @return bool
	 */
	function aiow_check_tcpdf_fonts_version( $force_file_check = false ) {
		if ( 'yes' === aiow_option( 'aiow_invoicing_fonts_manager_do_not_download', 'no' ) ) {
			return false;
		}
		$result = ( 0 == version_compare( aiow_option( 'aiow_invoicing_fonts_version', null ), aiow_get_tcpdf_fonts_version() ) );
		if ( $result && $force_file_check ) {
			$tcpdf_fonts_dir       = aiow_get_aiow_uploads_dir( 'tcpdf_fonts' ) . '/';
			$tcpdf_fonts_dir_files = scandir( $tcpdf_fonts_dir );
			$tcpdf_fonts_files     = aiow_get_fonts_list();
			foreach ( $tcpdf_fonts_files as $tcpdf_fonts_file ) {
				if ( ! in_array( $tcpdf_fonts_file, $tcpdf_fonts_dir_files ) ) {
					return false;
				}
			}
		}
		return $result;
	}
}

if ( ! function_exists( 'aiow_check_and_maybe_download_tcpdf_fonts' ) ) {
	/**
	 * Check for maybe download fonts.
	 *
	 * @package bool $force_download Force download.
	 */
	function aiow_check_and_maybe_download_tcpdf_fonts( $force_download = false ) {
		return $force_download;
	}
}

if ( ! function_exists( 'aiow_get_invoice_types' ) ) {
	/**
	 * Get invoice type.
	 */
	function aiow_get_invoice_types() {
		$invoice_types = array(
			array(
				'id'       => 'aiow_invoice',
				'title'    => aiow_option( 'aiow_invoicing_' . 'invoice' . '_admin_title', __( 'Invoice', 'all-in-one-wc' ) ),
				'defaults' => array( 'init' => 'disabled' ),
				'color'    => 'green',
			),
			array(
				'id'       => 'aiow_proforma_invoice',
				'title'    => aiow_option( 'aiow_invoicing_' . 'proforma_invoice' . '_admin_title', __( 'Proforma Invoice', 'all-in-one-wc' ) ),
				'defaults' => array( 'init' => 'disabled' ),
				'color'    => 'orange',
			),
			array(
				'id'       => 'aiow_packing_slip',
				'title'    => aiow_option( 'aiow_invoicing_' . 'packing_slip' . '_admin_title', __( 'Packing Slip', 'all-in-one-wc' ) ),
				'defaults' => array( 'init' => 'disabled' ),
				'color'    => 'blue',
			),
			array(
				'id'       => 'aiow_credit_note',
				'title'    => aiow_option( 'aiow_invoicing_' . 'credit_note' . '_admin_title', __( 'Credit Note', 'all-in-one-wc' ) ),
				'defaults' => array( 'init' => 'disabled' ),
				'color'    => 'red',
			),
		);
		$total_custom_docs = min( aiow_option( 'aiow_invoicing_custom_doc_total_number', 1 ), 100 );
		for ( $i = 1; $i <= $total_custom_docs; $i++ ) {
			$invoice_types[] = array(
				'id'       => ( 1 == $i ? 'custom_doc' : 'custom_doc' . '_' . $i ),
				'title'    => aiow_option( 'aiow_invoicing_' . ( 1 == $i ? 'custom_doc' : 'custom_doc' . '_' . $i ) . '_admin_title',
					__( 'Custom Document', 'all-in-one-wc' ) . ' #' . $i ),
				'defaults' => array( 'init' => 'disabled' ),
				'color'    => 'gray',
				'is_custom_doc' => true,
				'custom_doc_nr' => $i,
			);
		}
		return $invoice_types;
	}
}

if ( ! function_exists( 'aiow_get_invoice_create_on' ) ) {
	/**
	 * Get create on invoice.
	 *
	 * @param string $invoice_type Invoice type.
	 * @return array
	 */
	function aiow_get_invoice_create_on( $invoice_type ) {
		$create_on = aiow_option( 'aiow_invoicing_' . $invoice_type . '_create_on', '' );
		if ( empty( $create_on ) ) {
			return array();
		}
		if ( ! is_array( $create_on ) ) {
			if ( 'disabled' === $create_on ) {
				update_option( 'aiow_invoicing_' . $invoice_type . '_create_on', '' );
				return array();
			} elseif ( 'aiow_pdf_invoicing_create_on_any_refund' === $create_on ) {
				$create_on = array( 'woocommerce_order_status_refunded', 'woocommerce_order_partially_refunded_notification' );
				update_option( 'aiow_invoicing_' . $invoice_type . '_create_on', $create_on );
				return $create_on;
			} else {
				$create_on = array( $create_on );
				update_option( 'aiow_invoicing_' . $invoice_type . '_create_on', $create_on );
				return $create_on;
			}
		}
		return $create_on;
	}
}

if ( ! function_exists( 'aiow_get_enabled_invoice_types' ) ) {
	/**
	 * Get enabled invoice types.
	 *
	 * @return array
	 */
	function aiow_get_enabled_invoice_types() {
		$invoice_types = aiow_get_invoice_types();
		$enabled_invoice_types = array();
		foreach ( $invoice_types as $k => $invoice_type ) {
			$z = ( 0 === $k ) ? aiow_get_invoice_create_on( $invoice_type['id'] ) : apply_filters( 'aiow_option', '', aiow_get_invoice_create_on( $invoice_type['id'] ) );
			if ( empty( $z ) ) {
				continue;
			}
			$enabled_invoice_types[] = $invoice_type;
		}
		return $enabled_invoice_types;
	}
}

if ( ! function_exists( 'aiow_get_enabled_invoice_types_ids' ) ) {
	/**
	 * Get enabled invoice types IDs.
	 *
	 * @return array
	 */
	function aiow_get_enabled_invoice_types_ids() {
		$invoice_types = aiow_get_enabled_invoice_types();
		$invoice_types_ids = array();
		foreach( $invoice_types as $invoice_type ) {
			$invoice_types_ids[] = $invoice_type['id'];
		}
		return $invoice_types_ids;
	}
}

if ( ! function_exists( 'aiow_get_pdf_invoice' ) ) {
	/**
	 * Get PDF invoice.
	 *
	 * @package int    $order_id Order ID.
	 * @package string $invoice_type_id Invoice Type ID.
	 * @return object
	 */
	function aiow_get_pdf_invoice( $order_id, $invoice_type_id ) {
		$the_invoice = new AIOW\Modules\Invoice\Generate\PDF( $order_id, $invoice_type_id );
		return $the_invoice;
	}
}

if ( ! function_exists( 'aiow_get_invoice' ) ) {
	/**
	 * Get PDF invoice.
	 *
	 * @package int    $order_id Order ID.
	 * @package string $invoice_type_id Invoice Type ID.
	 * @return object
	 */
	function aiow_get_invoice( $order_id, $invoice_type_id ) {
		$the_invoice = new AIOW\Modules\Invoice\Generate\Invoice( $order_id, $invoice_type_id );
		return $the_invoice;
	}
}

if ( ! function_exists( 'aiow_get_invoice_date' ) ) {
	/**
	 * Get PDF invoice date.
	 *
	 * @package int    $order_id Order ID.
	 * @package string $invoice_type_id Invoice Type ID.
	 * @package string $extra_days Extra days.
	 * @package string $date_format Date format.
	 * @return  mixed
	 */
	function aiow_get_invoice_date( $order_id, $invoice_type_id, $extra_days, $date_format ) {
		$the_invoice = aiow_get_invoice( $order_id, $invoice_type_id );
		if ( $invoice_date_timestamp = $the_invoice->get_invoice_date() ) {
			$extra_days_in_sec = $extra_days * 24 * 60 * 60;
			return date_i18n( $date_format, $invoice_date_timestamp + $extra_days_in_sec );
		} else {
			return '';
		}
	}
}

if ( ! function_exists( 'aiow_get_invoice_number' ) ) {
	/**
	 * Get invoice number.
	 *
	 * @param int    $order_id Order ID.
	 * @param string $invoice_type_id Invoice Type ID.
	 * @return mixed
	 */
	function aiow_get_invoice_number( $order_id, $invoice_type_id ) {
		$the_invoice = aiow_get_invoice( $order_id, $invoice_type_id );
		return $the_invoice->get_invoice_number();
	}
}

if ( ! function_exists( 'aiow_delete_invoice' ) ) {
	/**
	 * Delete invoice.
	 *
	 * @param int    $order_id Order ID.
	 * @param string $invoice_type_id Invoice type ID. 
	 */
	function aiow_delete_invoice( $order_id, $invoice_type_id ) {
		$the_invoice = aiow_get_invoice( $order_id, $invoice_type_id );
		$the_invoice->delete();
	}
}

if ( ! function_exists( 'aiow_create_invoice' ) ) {
	/**
	 * Create invoice.
	 *
	 * @param int    $order_id Order ID.
	 * @param string $invoice_type_id Invoice type ID.
	 * @param string $date Create date.
	 */
	function aiow_create_invoice( $order_id, $invoice_type_id, $date = '' ) {
		$the_invoice = aiow_get_invoice( $order_id, $invoice_type_id );
		$the_invoice->create( $date );
	}
}

if ( ! function_exists( 'aiow_is_invoice_created' ) ) {
	/**
	 * Check is invoice created.
	 *
	 * @param int    $order_id Order ID.
	 * @param string $invoice_type_id Invoice type ID.
	 * @return object|array.
	 */
	function aiow_is_invoice_created( $order_id, $invoice_type_id ) {
		$the_invoice = aiow_get_invoice( $order_id, $invoice_type_id );
		return $the_invoice->is_created();
	}
}
