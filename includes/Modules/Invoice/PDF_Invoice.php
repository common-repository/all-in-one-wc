<?php
/**
 * Invoicing Module - PDF Invoicing
 *
 * @package WordPress
 * @subpackage WooCommerce
 */

namespace AIOW\Modules\Invoice;

if ( ! class_exists( 'PDF_Invoice' ) ) {

	/**
	 * Declare class `PDF_Invoice` extends to `\AIOW\Modules\Register_Modules`.
	 */
	class PDF_Invoice extends \AIOW\Modules\Register_Modules {

		/**
		 * Class Constructor.
		 */
		function __construct() {

			$this->id            = 'pdf_invoicing';
			$this->short_desc    = __( 'PDF Invoices', 'all-in-one-wc' );
			$this->section_title = __( 'General', 'all-in-one-wc' );
			$this->desc          = __( 'Invoices, Proforma Invoices, Credit Notes and Packing Slips.', 'all-in-one-wc' );
			$this->desc_pro      = __( 'Invoices, Proforma Invoices, Credit Notes and Packing Slips.', 'all-in-one-wc' );
			$this->link_slug     = 'woocommerce-pdf-invoicing-and-packing-slips';
			parent::__construct();

			$this->add_tools( array(
				'renumerate_invoices' => array(
					'title' => __( 'Invoices Renumerate', 'all-in-one-wc' ),
					'desc'  => __( 'Tool renumerates all invoices, proforma invoices, credit notes and packing slips.', 'all-in-one-wc' ),
				),
				'invoices_report' => array(
					'title' => __( 'Invoices Report', 'all-in-one-wc' ),
					'desc'  => __( 'Invoices Monthly Reports.', 'all-in-one-wc' ),
				),
			) );

			if ( $this->is_enabled() ) {
				if ( 'init' === current_filter() ) {
					$this->catch_args();
					$this->generate_pdf_on_init();
				} else {
					add_action( 'init', array( $this, 'catch_args' ) );
					add_action( 'init', array( $this, 'generate_pdf_on_init' ) );
				}

				// Bulk actions
				//add_filter( 'bulk_actions-edit-' . 'shop_order', array( $this, 'bulk_actions_register' ) );
				//add_filter( 'handle_bulk_actions-edit-' . 'shop_order', array( $this, 'bulk_actions_handle' ), 10, 3 );
				add_action( 'admin_notices',          array( $this, 'bulk_actions_pdfs_notices' ) );

				$invoice_types = aiow_get_enabled_invoice_types();
				foreach ( $invoice_types as $invoice_type ) {
					$the_hooks = aiow_get_invoice_create_on( $invoice_type['id'] );
					foreach ( $the_hooks as $the_hook ) {
						if ( 'manual' != $the_hook ) {
							add_action( $the_hook, array( $this, 'create_document_hook' ) );
							if ( 'woocommerce_new_order' === $the_hook ) {
								add_action( 'woocommerce_api_create_order',         array( $this, 'create_document_hook' ) );
								add_action( 'woocommerce_cli_create_order',         array( $this, 'create_document_hook' ) );
								add_action( 'kco_before_confirm_order',             array( $this, 'create_document_hook' ) );
								add_action( 'woocommerce_checkout_order_processed', array( $this, 'create_document_hook' ) );
							}
						}
					}
				}

				// Editable numbers in meta box
				if ( 'yes' === aiow_option( 'aiow_invoicing_add_order_meta_box_numbering', 'yes' ) ) {
					add_action( 'save_post_shop_order', array( $this, 'save_meta_box' ), PHP_INT_MAX, 2 );
				}

			}
		}

		/**
		 * Adds extra bulk action options to generate/download documents.
		 *
		 * @param array $actions Register actions.
		 * @return array
		 */
		function bulk_actions_register( $actions ) {
			$invoice_types      = aiow_get_enabled_invoice_types();
			$new_actions_source = array(
				'generate' => __( 'Generate', 'all-in-one-wc' ),
				'download' => __( 'Download (Zip)', 'all-in-one-wc' ),
				'merge'    => __( 'Merge (Print)', 'all-in-one-wc' ),
			);
			$new_actions        = array();
			foreach ( $new_actions_source as $source_key => $source_value ) {
				foreach ( $invoice_types as $type_key => $type_value ) {
					$new_actions[ $source_key . '_' . $type_value['id'] ] = $source_value . ' ' . $type_value['title'];
				}
			}
			$actions = array_merge( $actions, $new_actions );
			return $actions;
		}

		/**
		 * Bulk PDF notices.
		 */
		function bulk_actions_pdfs_notices() {
			global $post_type, $pagenow;
			if ( $pagenow == 'edit.php' && 'shop_order' == $post_type ) {
				if ( isset( $_REQUEST['generated'] ) && (int) $_REQUEST['generated'] ) {
					$message = sprintf( _n( 'Document generated.', '%s documents generated.', $_REQUEST['generated'] ), number_format_i18n( $_REQUEST['generated'] ) );
					echo "<div class='updated'><p>{$message}</p></div>";
				}
				if ( isset( $_GET['aiow_notice'] ) ) {
					switch ( $_GET['aiow_notice'] ) {
						case 'ziparchive_class_missing':
							echo '<div class="notice notice-error"><p><strong>' .
								sprintf( __( '%s class is not accessible on your server. Please contact your hosting provider.', 'all-in-one-wc' ),
									'<a target="_blank" href="http://php.net/manual/en/class.ziparchive.php">PHP ZipArchive</a>' ) .
							'</strong></p></div>';
							break;
						case 'ziparchive_error':
							echo '<div class="notice notice-error"><p>' .
								__( 'ZipArchive error.', 'all-in-one-wc' ) .
							'</p></div>';
							break;
						case 'merge_pdfs_no_files':
							echo '<div class="notice notice-error"><p>' .
								__( 'Merge PDFs: No files.', 'all-in-one-wc' ) .
							'</p></div>';
							break;
						case 'merge_pdfs_php_version':
							echo '<div class="notice notice-error"><p>' .
								sprintf( __( 'Merge PDFs: Command requires PHP version 5.3.0 at least. You have PHP version %s installed.', 'all-in-one-wc' ), PHP_VERSION ) .
							'</p></div>';
							break;
						default:
							echo '<div class="notice notice-error"><p>' .
								sprintf( __( '%s.', 'all-in-one-wc' ), '<code>' . $_GET['aiow_notice'] . '</code>' ) .
							'</p></div>';
							break;
					}
				}
			}
		}

		/**
		 * Processes the PDF bulk actions.
		 *
		 * @param string $redirect_to Redirect TO.
		 * @param string $action Action.
		 * @param array  $post_ids Post ID.
		 * @return string
		 */
		function bulk_actions_handle( $redirect_to, $action, $post_ids ) {
			if (
				false === preg_match( '(generate|download|merge)', $action ) ||
				false === preg_match( '(invoice|packing_slip|credit_note)', $action ) ||
				false === check_admin_referer( 'bulk-posts' )
			) {
				return $redirect_to;
			}

			// Validate the action.
			$action_exploded = explode( '_', $action, 2 );

			// Perform the action.
			$the_action = $action_exploded[0];
			$the_type   = $action_exploded[1];

			switch( $the_action ) {
				case 'generate':
					$generated = 0;
					foreach( $post_ids as $post_id ) {
						if ( $this->create_document( $post_id, $the_type ) ) {
							$generated++;
						}
					}
					// Build the redirect url
					$redirect_to = add_query_arg(
						array(
							'generated'              => $generated,
							'generated_type'         => $the_type,
							'generated_' . $the_type => 1,
							'ids'                    => join( ',', $post_ids ),
							'post_status'            => $_GET['post_status'],
						),
						$redirect_to
					);
					break;
				case 'download':
					if ( '' != ( $result = $this->get_invoices_zip( $the_type, $post_ids ) ) ) {
						// Build the redirect url
						$redirect_to = add_query_arg(
							array(
								'post_status'        => $_GET['post_status'],
								'aiow_notice'         => $result,
							),
							$redirect_to
						);
					}
					break;
				case 'merge':
					$merge_ids = array();
					foreach( $post_ids as $post_id ) {
						if ( aiow_is_invoice_created( $post_id, $the_type ) ) {
							$merge_ids[] = $post_id;
						}
					}
					if ( ! empty( $merge_ids ) ) {
						if ( '' != ( $result = $this->merge_pdfs( $the_type, $merge_ids ) ) ) {
							// Build the redirect url
							$redirect_to = add_query_arg(
								array(
									'post_status'        => $_GET['post_status'],
									'aiow_notice'         => $result,
								),
								$redirect_to
							);
						}
					}
					break;
				default:
					return $redirect_to;
			}
			return $redirect_to;
		}

		/**
		 * Merge PDFs.
		 *
		 * @param string    $invoice_type_id Invoice ID.
		 * @param int|array $post_ids Post ID.
		 * @return array
		 */
		function merge_pdfs( $invoice_type_id, $post_ids ) {
			if ( version_compare( PHP_VERSION, '5.3.0', '<' ) ) {
				return 'merge_pdfs_php_version';
			}
			$files = array();
			foreach( $post_ids as $post_id ) {
				$the_invoice = aiow_get_pdf_invoice( $post_id, $invoice_type_id );
				$files[]     = $the_invoice->get_pdf( 'F' );
			}
			if ( empty( $files ) ) {
				return 'merge_pdfs_no_files';
			}
			require_once( aiow_plugin_path() . '/includes/lib/FPDI/src/autoload.php' );
			$fpdi_pdf = require_once( aiow_plugin_path() . '/includes/pdf-invoices/tcpdffpdi.php' );
			$fpdi_pdf->SetTitle( 'docs.pdf' );
			$fpdi_pdf->setPrintHeader( false );
			$fpdi_pdf->setPrintFooter( false );
			foreach( $files as $file ) {
				$page_count = $fpdi_pdf->setSourceFile( $file );
				for ( $page_nr = 1; $page_nr <= $page_count; $page_nr++ ) {
					$page_id = $fpdi_pdf->ImportPage( $page_nr );
					$s = $fpdi_pdf->getTemplatesize( $page_id );
					$fpdi_pdf->AddPage( $s['orientation'], $s );
					$fpdi_pdf->useImportedPage( $page_id );
				}
			}
			$fpdi_pdf->Output( 'docs.pdf', ( 'yes' === aiow_option( 'aiow_invoicing_' . $invoice_type_id . '_save_as_enabled', 'no' ) ? 'D' : 'I' ) );
			die();
		}

		/**
		 * Get invoices ZIP.
		 *
		 * @param string    $invoice_type_id Invoice ID.
		 * @param array|int $post_ids Post ID.
		 * @return mixed
		 */
		function get_invoices_zip( $invoice_type_id, $post_ids ) {
			if ( ! class_exists( 'ZipArchive' ) ) {
				return 'ziparchive_class_missing';
			}
			// Creating Zip
			$zip           = new ZipArchive();
			$zip_file_name = $invoice_type_id . '-' .
				sanitize_title( str_replace( array( 'http://', 'https://' ), '', site_url() ) ) . '-' .
				min( $post_ids )  . '-' . max( $post_ids ) .
				'.zip';
			$zip_file_path = aiow_get_invoicing_temp_dir() . '/' . $zip_file_name;
			if ( file_exists( $zip_file_path ) ) {
				@unlink( $zip_file_path );
			}
			if ( $zip->open( $zip_file_path, ZipArchive::CREATE | ZIPARCHIVE::OVERWRITE ) !== TRUE ) {
				return 'ziparchive_error';
			}
			foreach( $post_ids as $post_id ) {
				if ( aiow_is_invoice_created( $post_id, $invoice_type_id ) ) {
					$the_invoice = aiow_get_pdf_invoice( $post_id, $invoice_type_id );
					$file_name   = $the_invoice->get_pdf( 'F' );
					$zip->addFile( $file_name, $the_invoice->get_file_name() );
				}
			}
			$zip->close();
			// Sending Zip
			aiow_send_file( $zip_file_name, $zip_file_path, 'zip', true );
			return '';
		}

		/**
		 * Create document.
		 *
		 * @param int $order_id Order ID.
		 */
		function create_document_hook( $order_id ) {
			$current_filter = current_filter();
			if ( in_array( $current_filter,
					array( 'woocommerce_api_create_order', 'woocommerce_cli_create_order', 'kco_before_confirm_order', 'woocommerce_checkout_order_processed' ) )
			) {
				$current_filter = 'woocommerce_new_order';
			}
			$invoice_types = aiow_get_enabled_invoice_types();
			foreach ( $invoice_types as $invoice_type ) {
				$the_hooks = aiow_get_invoice_create_on( $invoice_type['id'] );
				foreach ( $the_hooks as $the_hook ) {
					if ( 'manual' != $the_hook ) {
						if ( $current_filter === $the_hook ) {
							$this->create_document( $order_id, $invoice_type['id'] );
						}
					}
				}
			}
		}

		/**
		 * Create document.
		 *
		 * @param int    $order_id Order ID.
		 * @param string $invoice_type invoice type.
		 * @return bool
		 */
		function create_document( $order_id, $invoice_type ) {
			if ( false == aiow_is_invoice_created( $order_id, $invoice_type ) ) {
				aiow_create_invoice( $order_id, $invoice_type );
				return true;
			}
			return false;
		}

		/**
		 * Delete document.
		 *
		 * @param int    $order_id Order ID.
		 * @param string $invoice_type Invoice type.
		 */
		function delete_document( $order_id, $invoice_type ) {
			if ( true == aiow_is_invoice_created( $order_id, $invoice_type ) ) {
				aiow_delete_invoice( $order_id, $invoice_type );
			}
		}

		/**
		 * Get request data.
		 */
		function catch_args() {
			$this->order_id        = ( isset( $_GET['order_id'] ) )        ? $_GET['order_id']        : 0;
			$this->invoice_type_id = ( isset( $_GET['invoice_type_id'] ) ) ? $_GET['invoice_type_id'] : '';
			$this->save_as_pdf     = ( isset( $_GET['save_pdf_invoice'] ) && '1' == $_GET['save_pdf_invoice'] );
			$this->get_invoice     = ( isset( $_GET['get_invoice'] ) && '1' == $_GET['get_invoice'] );

			if ( isset( $_GET['create_invoice_for_order_id'] ) && $this->check_user_roles( false ) ) {
				$this->create_document( $_GET['create_invoice_for_order_id'], $this->invoice_type_id );
			}
			if ( isset( $_GET['delete_invoice_for_order_id'] ) && $this->check_user_roles( false ) ) {
				$this->delete_document( $_GET['delete_invoice_for_order_id'], $this->invoice_type_id );
			}
		}

		/**
		 * Check current user roles.
		 *
		 * @param bool $allow_order_owner Allow order.
		 * @return bool
		 */
		function check_user_roles( $allow_order_owner = true ) {
			if ( $allow_order_owner && get_current_user_id() == intval( get_post_meta( $this->order_id, '_customer_user', true ) ) ) {
				return true;
			}
			$allowed_user_roles = aiow_option( 'aiow_invoicing_' . $this->invoice_type_id . '_roles', array( 'administrator', 'shop_manager' ) );
			if ( empty( $allowed_user_roles ) ) {
				$allowed_user_roles = array( 'administrator' );
			}
			if ( aiow_is_user_role( $allowed_user_roles ) ) {
				return true;
			} else {
				add_action( 'admin_notices', array( $this, 'wrong_user_role_notice' ) );
				return false;
			}
		}

		/**
		 * Display wrong user role notice.
		 */
		function wrong_user_role_notice() {
			echo '<div class="notice notice-error is-dismissible"><p>' . __( 'You are not allowed to view the invoice.', 'all-in-one-wc' ) . '</p></div>';
		}

		/**
		 * Generate PDF in onload.
		 */
		function generate_pdf_on_init() {
			// Check if all is OK
			if ( true !== $this->get_invoice || 0 == $this->order_id || ! is_user_logged_in() || ! $this->check_user_roles() ) {
				return;
			}
			// Get PDF
			$the_invoice = aiow_get_pdf_invoice( $this->order_id, $this->invoice_type_id );
			$dest        = ( true === $this->save_as_pdf ? 'D' : 'I' );
			if ( 'yes' === aiow_option( 'aiow_general_advanced_disable_output_buffer', 'no' ) ) {
				ob_clean();
				ob_flush();
				$the_invoice->get_pdf( $dest );
				ob_end_flush();
				if ( ob_get_contents() ) {
					ob_end_clean();
				}
			} else {
				$the_invoice->get_pdf( $dest );
			}
			die();
		}

	}
}
