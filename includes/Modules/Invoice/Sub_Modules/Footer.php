<?php
/**
 * Submodule PDF Invoicing - Footer.
 *
 * @package WordPress
 * @subpackage WooCommerce
 */

namespace AIOW\Modules\Invoice\Sub_Modules;

if ( ! class_exists( 'Footer' ) ) {

	/**
	 * Declare class `Footer` extends to `\AIOW\Modules\Register_Modules`.
	 */
	class Footer extends \AIOW\Modules\Register_Modules {

		/**
		 * Constructor.
		 */
		function __construct() {
			$this->id         = 'pdf_invoicing_footer';
			$this->parent_id  = 'pdf_invoicing';
			$this->short_desc = __( 'Footer', 'all-in-one-wc' );
			$this->desc       = '';
			parent::__construct( 'submodule' );
		}

	}
}
