<?php
/**
 * Shipping Module - Shipping by Condition.
 *
 * @package WordPress
 * @subpackage WooCommerce
 */

namespace AIOW\Modules\Shipping;

if ( ! class_exists( 'Condition' ) ) {

	/**
	 * Declare abstract class `Address_Formats` extends to `\AIOW\Modules\Register_Modules`.
	 */
	abstract class Condition extends \AIOW\Modules\Register_Modules {

		/**
		 * Class Constructor.
		 *
		 * @param string $type Module type.
		 */
		function __construct( $type = 'module' ) {
			parent::__construct( $type );
			if ( $this->is_enabled() ) {
				$this->use_shipping_instances = ( 'yes' === aiow_option( 'aiow_' . $this->id . '_use_shipping_instance', 'no' ) );
				add_filter( 'woocommerce_package_rates', array( $this, 'available_shipping_methods' ), aiow_get_woocommerce_package_rates_module_filter_priority( $this->id ), 2 );
			}
		}

		/**
		 * Multiple roles options.
		 *
		 * @return bool
		 */
		function add_multiple_roles_option() {
			return false;
		}

		/**
		 * Available shipping methods.
		 *
		 * @param array  $rates Rates.
		 * @param string $package Packages.
		 * @return array
		 */
		function available_shipping_methods( $rates, $package ) {
			$include_arr = array();
			$exclude_arr = array();
			foreach ( $rates as $rate_key => $rate ) {
				foreach ( $this->condition_options as $options_id => $options_data ) {
					if ( 'no' === aiow_option( 'aiow_shipping_by_' . $options_id . '_section_enabled', 'yes' ) ) {
						continue;
					}
					$include = ( $this->use_shipping_instances ?
						get_option( 'aiow_shipping_' . $options_id . '_include_' . 'instance_' . $rate->instance_id, '' ) :
						get_option( 'aiow_shipping_' . $options_id . '_include_' . $rate->method_id, '' )
					);
					if ( ! empty( $include ) ) {
						if ( $this->check( $options_id, $include, 'include', $package ) ) {
						} else {
							unset( $rates[ $rate_key ] );
						}
					}
					$exclude = ( $this->use_shipping_instances ?
						get_option( 'aiow_shipping_' . $options_id . '_exclude_' . 'instance_' . $rate->instance_id, '' ) :
						get_option( 'aiow_shipping_' . $options_id . '_exclude_' . $rate->method_id, '' )
					);
					if ( ! empty( $exclude ) && $this->check( $options_id, $exclude, 'exclude', $package ) ) {
						$exclude_arr[] = $rate_key;
					}
				}
			}
			foreach ( $rates as $rate_key => $rate ) {
				if (
					( ! empty( $exclude_arr ) && in_array( $rate_key, $exclude_arr ) )
				) {
					unset( $rates[ $rate_key ] );
				}
			}
			return $rates;
		}

		/**
		 * Add settings from file.
		 *
		 * @param array $settings Settings.
		 * @return bool
		 */
		function add_settings_from_file( $settings ) {
			return $this->maybe_fix_settings( require( aiow_plugin_path() . '/includes/Modules/custom-fields/shipping/shipping-by-condition.php' ) );
		}

		/**
		 * Condition check.
		 *
		 * @param string $options_id Option ID.
		 * @param array  $args Argument.
		 * @param bool   $include_or_exclude Include OR exclude.
		 * @param string $package Package name.
		 */
		abstract function check( $options_id, $args, $include_or_exclude, $package );

		/**
		 * Get condition options.
		 *
		 * @param string $options_id Option ID.
		 */
		abstract function get_condition_options( $options_id );

		/**
		 * Get additional section settings.
		 *
		 * @param string $options_id Option ID.
		 * @return array
		 */
		function get_additional_section_settings( $options_id ) {
			return array();
		}

		/**
		 * Get extra option desc.
		 *
		 * @param string $option_id Option ID.
		 * @return null
		 */
		function get_extra_option_desc( $option_id ) {
			return '';
		}
	}
}
