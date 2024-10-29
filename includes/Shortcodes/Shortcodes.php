<?php
/**
 * Register all Shortcodes
 *
 * @package WordPress
 * @subpackage WooCommerce
 */
namespace AIOW\Shortcodes;

if ( ! class_exists( 'Shortcodes' ) ) {

	class Shortcodes {

		/**
		 * Constructor.
		 */
		function __construct() {
			foreach( $this->the_shortcodes as $the_shortcode ) {
				add_shortcode( $the_shortcode, array( $this, 'aiow_shortcode' ) );
			}
			add_filter( 'aiow_shortcodes_list', array( $this, 'add_shortcodes_to_the_list' ) );
			add_filter( 'aiow_shortcode_result', array( $this, 'add_result_key_param_to_shortcode_result' ), 10, 4 );
		}

		/**
		 * Add shortcode result key.
		 *
		 * @param mixed  $result Shortcode result.
		 * @param array  $atts Shortcode argument.
		 * @param string $content Content.
		 * @param string $shortcode Shortcode.
		 * @return mixed
		 */
		function add_result_key_param_to_shortcode_result( $result, $atts, $content, $shortcode ) {
			if ( is_array( $result ) && isset( $atts['result_key'] ) && ! empty( $atts['result_key'] ) ) {
				$result = $result[ $atts['result_key'] ];
			}
			return $result;
		}

		/**
		 * Add extra shortcode attributes.
		 *
		 * @param array $atts Shortcode attributs.
		 * @return array
		 */
		function add_extra_atts( $atts ) {
			if ( ! isset( $this->the_atts ) ) {
				$this->the_atts = array();
			}
			$final_atts = array_merge( $this->the_atts, $atts );
			return $final_atts;
		}

		/**
		 * Load all attributs.
		 *
		 * @param array $atts Shortcode attributs.
		 * @return array 
		 */
		function init_atts( $atts ) {
			return $atts;
		}

		/**
		 * Add shortcode list.
		 *
		 * @param array $shortcodes_list Shortcode list.
		 * @return array
		 */
		function add_shortcodes_to_the_list( $shortcodes_list ) {
			foreach( $this->the_shortcodes as $the_shortcode ) {
				$shortcodes_list[] = $the_shortcode;
			}
			return $shortcodes_list;
		}

		/**
		 * Register shortcode.
		 *
		 * @param array  $atts Attributes.
		 * @param string $content shortcode content.
		 * @param string $shortcode shortcode.
		 * @return mixed
		 */
		function aiow_shortcode( $atts, $content, $shortcode ) {

			// Init
			if ( empty( $atts ) ) {
				$atts = array();
			}

			// Add child class specific atts
			$atts = $this->add_extra_atts( $atts );

			// Merge atts with global defaults
			$global_defaults = array(
				'plus'                             => 1,
				'before'                           => '',
				'after'                            => '',
				'visibility'                       => '', // user_visibility
				'wrong_user_text'                  => '', // '<p>' . __( 'Wrong user role!', 'all-in-one-wc' ) . '</p>',
				'wrong_user_text_not_logged_in'    => '',
				'site_visibility'                  => '',
				'location'                         => '', // user_location
				'not_location'                     => '', // user_location
				'wpml_language'                    => '',
				'wpml_not_language'                => '',
				'billing_country'                  => '',
				'not_billing_country'              => '',
				'payment_method'                   => '',
				'not_payment_method'               => '',
				'module'                           => '',
				'find'                             => '',
				'replace'                          => '',
				'strip_tags'                       => 'no',
				'on_empty'                         => '',
				'on_zero'                          => 0,
				'time'                             => '',
				'multiply'                         => 1,
			);
			$atts = array_merge( $global_defaults, $atts );

			// Check for required atts
			if ( false === ( $atts = $this->init_atts( $atts ) ) ) {
				return '';
			}

			if (
				false === filter_var( $atts['plus'], FILTER_VALIDATE_BOOLEAN )
				&& class_exists('AIOW_Plus')
			) {
				return '';
			}

			// Check for module enabled
			if ( '' != $atts['module'] && ! aiow_is_module_enabled( $atts['module'] ) ) {
				return '<p>' . sprintf( __( '"%s" module is not enabled!', 'all-in-one-wc' ), $atts['module_name'] ) . '</p>';
			}

			// Check if time is ok
			if ( '' != $atts['time'] && ! aiow_check_time( $atts['time'] ) ) {
				return '';
			}

			// Check if privileges are ok
			if ( '' != $atts['visibility'] ) {
				global $aiow_pdf_invoice_data;
				$visibilities = str_replace( ' ', '', $atts['visibility'] );
				$visibilities = explode( ',', $visibilities );
				$is_iser_visibility_ok = false;
				foreach ( $visibilities as $visibility ) {
					if ( 'admin' === $visibility ) {
						$visibility = 'administrator';
					}
					if ( isset( $aiow_pdf_invoice_data['user_id'] ) && 0 == $aiow_pdf_invoice_data['user_id'] ) {
						if ( 'guest' === $visibility ) {
							$is_iser_visibility_ok = true;
							break;
						}
					} else {
						$user_id = ( isset( $aiow_pdf_invoice_data['user_id'] ) ? $aiow_pdf_invoice_data['user_id'] : 0 );
						if ( aiow_is_user_role( $visibility, $user_id ) ) {
							$is_iser_visibility_ok = true;
							break;
						}
					}
				}
				if ( ! $is_iser_visibility_ok ) {
					if ( ! is_user_logged_in() ) {
						$login_form = '';
						$login_url  = '';
						if ( false !== strpos( $atts['wrong_user_text_not_logged_in'], '%login_form%' ) ) {
							ob_start();
							woocommerce_login_form();
							$login_form = ob_get_clean();
						}
						if ( false !== strpos( $atts['wrong_user_text_not_logged_in'], '%login_url%' ) ) {
							$login_url  = wp_login_url( get_permalink() );
						}
						return str_replace( array( '%login_form%', '%login_url%' ), array( $login_form, $login_url ), $atts['wrong_user_text_not_logged_in'] );
					} else {
						return $atts['wrong_user_text'];
					}
				}
			}

			// Check if site visibility is ok
			if ( '' != $atts['site_visibility'] ) {
				if (
					( 'single'     === $atts['site_visibility'] && ! is_single() ) ||
					( 'page'       === $atts['site_visibility'] && ! is_page() ) ||
					( 'archive'    === $atts['site_visibility'] && ! is_archive() ) ||
					( 'front_page' === $atts['site_visibility'] && ! is_front_page() )
				) {
					return '';
				}
			}

			// Check if location is ok
			if (
				'' != $atts['location'] &&
				'all' != $atts['location'] &&
				(
					false === strpos( $atts['location'], ',' ) && $atts['location'] != $this->aiow_get_user_location() ||
					false !== strpos( $atts['location'], ',' ) && ! in_array( $this->aiow_get_user_location(), array_map( 'trim', explode( ',', $atts['location'] ) ) )
				)
			) {
				return '';
			}
			if (
				'' != $atts['not_location'] &&
				(
					false === strpos( $atts['not_location'], ',' ) && $atts['not_location'] === $this->aiow_get_user_location() ||
					false !== strpos( $atts['not_location'], ',' ) && in_array( $this->aiow_get_user_location(), array_map( 'trim', explode( ',', $atts['not_location'] ) ) )
				)
			) {
				return '';
			}

			// Check if language is ok
			if ( 'aiow_wpml' === $shortcode || 'aiow_wpml_translate' === $shortcode ) {
				if ( isset( $atts['lang'] ) ) {
					$atts['wpml_language'] = $atts['lang'];
				}
				if ( isset( $atts['not_lang'] ) ) {
					$atts['wpml_not_language'] = $atts['not_lang'];
				}
			}
			if ( '' != $atts['wpml_language'] ) {
				if ( ! defined( 'ICL_LANGUAGE_CODE' ) ) {
					return '';
				}
				if ( ! in_array( ICL_LANGUAGE_CODE, $this->custom_explode( $atts['wpml_language'] ) ) ) {
					return '';
				}
			}
			// Check if language is ok (not in...)
			if ( '' != $atts['wpml_not_language'] ) {
				if ( defined( 'ICL_LANGUAGE_CODE' ) ) {
					if ( in_array( ICL_LANGUAGE_CODE, $this->custom_explode( $atts['wpml_not_language'] ) ) ) {
						return '';
					}
				}
			}

			// Check if billing country by arg is ok
			if ( '' != $atts['billing_country'] ) {
				if ( ! isset( $_GET['billing_country'] ) ) {
					return '';
				}
				if ( ! in_array( $_GET['billing_country'], $this->custom_explode( $atts['billing_country'] ) ) ) {
					return '';
				}
			}
			// Check if billing country by arg is ok (not in...)
			if ( '' != $atts['not_billing_country'] ) {
				if ( isset( $_GET['billing_country'] ) ) {
					if ( in_array( $_GET['billing_country'], $this->custom_explode( $atts['not_billing_country'] ) ) ) {
						return '';
					}
				}
			}

			// Check if payment method by arg is ok
			if ( '' != $atts['payment_method'] ) {
				if ( ! isset( $_GET['payment_method'] ) ) {
					return '';
				}
				if ( ! in_array( $_GET['payment_method'], $this->custom_explode( $atts['payment_method'] ) ) ) {
					return '';
				}
			}
			// Check if payment method by arg is ok (not in...)
			if ( '' != $atts['not_payment_method'] ) {
				if ( isset( $_GET['payment_method'] ) ) {
					if ( in_array( $_GET['payment_method'], $this->custom_explode( $atts['not_payment_method'] ) ) ) {
						return '';
					}
				}
			}

			// Additional (child class specific) checks
			if ( ! $this->extra_check( $atts ) ) {
				return '';
			}

			// Run the shortcode function
			$shortcode_function = $shortcode;
			if ( '' !== ( $result = $this->$shortcode_function( $atts, $content ) ) ) {
				if ( 0 === $result && 0 !== $atts['on_zero'] ) {
					return $atts['on_zero'];
				}
				if ( '' != $atts['find'] ) {
					if ( false !== strpos( $atts['find'], ',' ) && strlen( $atts['find'] ) > 2 ) {
						$find    = explode( ',', $atts['find'] );
						$replace = explode( ',', $atts['replace'] );
						if ( count( $find ) === count( $replace ) ) {
							$atts['find']    = $find;
							$atts['replace'] = $replace;
						}
					}
					$result = str_replace( $atts['find'], $atts['replace'], $result );
				}
				if ( 'yes' === $atts['strip_tags'] ) {
					$result = strip_tags( $result );
				}
				if ( 1 != $atts['multiply'] ) {
					$result = $result * $atts['multiply'];
				}
				return $atts['before'] . apply_filters( 'aiow_shortcode_result', $result, $atts, $content, $shortcode ) . $atts['after'];
			}
			return $atts['on_empty'];
		}

		/**
		 * Extra check.
		 *
		 * @param array $atts Shortcode attributes.
		 * @return bool
		 */
		function extra_check( $atts ) {
			return true;
		}

		/**
		 * Custom explode.
		 *
		 * @param string $string_to_explode  String explode.
		 * @return string
		 */
		function custom_explode( $string_to_explode ) {
			$string_to_explode = str_replace( ' ', '', $string_to_explode );
			$string_to_explode = trim( $string_to_explode, ',' );
			return explode( ',', $string_to_explode );
		}

		/**
		 * Get user location.
		 *
		 * @return bool
		 */
		function aiow_get_user_location() {
			return ( isset( $_GET['country'] ) && '' != $_GET['country'] && aiow_is_user_role( 'administrator' ) ? $_GET['country'] : aiow_get_country_by_ip() );
		}
	}
}
