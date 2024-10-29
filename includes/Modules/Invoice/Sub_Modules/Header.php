<?php
/**
 * Submodule PDF Invoicing - Header.
 *
 * @package WordPress
 * @subpackage WooCommerce
 */

namespace AIOW\Modules\Invoice\Sub_Modules;

if ( ! class_exists( 'Header' ) ) {

	/**
	 * Declare class `Footer` extends to `\AIOW\Modules\Register_Modules`.
	 */
	class Header extends \AIOW\Modules\Register_Modules {

		/**
		 * Constructor.
		 */
		function __construct() {
			$this->id         = 'pdf_invoicing_header';
			$this->parent_id  = 'pdf_invoicing';
			$this->short_desc = __( 'Header', 'all-in-one-wc' );
			$this->desc       = '';
			parent::__construct( 'submodule' );
		}

	}
}
