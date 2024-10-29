<?php
/**
 * Submodule PDF Invoicing - Templates.
 *
 * @package WordPress
 * @subpackage WooCommerce
 */

namespace AIOW\Modules\Invoice\Sub_Modules;

if ( ! class_exists( 'Templates' ) ) {

	/**
	 * Declare class `Templates` extends to `\AIOW\Modules\Register_Modules`.
	 */
	class Templates extends \AIOW\Modules\Register_Modules {

		/**
		 * Constructor.
		 */
		function __construct() {
			$this->id         = 'pdf_invoicing_templates';
			$this->parent_id  = 'pdf_invoicing';
			$this->short_desc = __( 'Templates', 'all-in-one-wc' );
			$this->desc       = '';
			parent::__construct( 'submodule' );
		}

		/**
		 * Get default template.
		 *
		 * @param string $invoice_type_id Invoice Type ID.
		 * @return string
		 */
		function get_default_template( $invoice_type_id ) {
			if ( ! isset( $this->default_template[ $invoice_type_id ] ) ) {
				$default_template_filename = ( false === strpos( $invoice_type_id, 'custom_doc_' ) ? $invoice_type_id : 'custom_doc' );
				$default_template_filename = aiow_plugin_path() . '/includes/Modules/Invoice/templates/' . $default_template_filename . '.php';
				if ( file_exists( $default_template_filename ) ) {
					ob_start();
					include( $default_template_filename );
					$this->default_template[ $invoice_type_id ] = ob_get_clean();
					if ( false !== strpos( $invoice_type_id, 'custom_doc' ) ) {
						$custom_doc_nr = ( 'custom_doc' === $invoice_type_id ) ? '1' : str_replace( 'custom_doc_', '', $invoice_type_id );
						$this->default_template[ $invoice_type_id ] = str_replace( '[aiow_custom_doc_number]', '[aiow_custom_doc_number doc_nr="' . $custom_doc_nr . '"]',
							$this->default_template[ $invoice_type_id ] );
						$this->default_template[ $invoice_type_id ] = str_replace( '[aiow_custom_doc_date]',   '[aiow_custom_doc_date doc_nr="'   . $custom_doc_nr . '"]',
							$this->default_template[ $invoice_type_id ] );
					}
				} else {
					$this->default_template[ $invoice_type_id ] = '';
				}
			}
			return $this->default_template[ $invoice_type_id ];
		}
	}
}
