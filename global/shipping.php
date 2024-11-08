<?php
/**
 * Functions - Shipping
 *
 * @package WordPress
 * @subpackage WooCommerce
 */

if ( ! function_exists( 'aiow_get_shipping_time_table' ) ) {
	/**
	 * Get shipping time table.
	 *
	 * @param bool   $do_use_shipping_instances Shipping instances.
	 * @param string $option_id_shipping_class Option ID.
	 * @return string
	 */
	function aiow_get_shipping_time_table( $do_use_shipping_instances, $option_id_shipping_class ) {
		$shipping_methods = ( $do_use_shipping_instances ? aiow_get_shipping_methods_instances( true ) : WC()->shipping()->load_shipping_methods() );
		$matching_zone_id = aiow_get_customer_shipping_matching_zone_id();
		$table_data       = array();
		foreach ( $shipping_methods as $method ) {
			if ( $do_use_shipping_instances && $method['zone_id'] != $matching_zone_id ) {
				continue;
			}
			$option_id_shipping_method = ( $do_use_shipping_instances ? 'instance_' . $method['shipping_method_instance_id'] : $method->id );
			$option_id                 = 'aiow_shipping_time_' . $option_id_shipping_method . $option_id_shipping_class;
			if ( '' !== ( $time = aiow_option( $option_id, '' ) ) ) {
				$method_title = ( $do_use_shipping_instances ? $method['zone_name'] . ': ' . $method['shipping_method_title']: $method->get_method_title() );
				$table_data[] = array( $method_title, sprintf( __( '%s day(s)' ), $time ) );
			}
		}
		return ( empty( $table_data ) ? '' : aiow_get_table_html( $table_data, array( 'table_heading_type' => 'vertical' ) ) );
	}
}

if ( ! function_exists( 'aiow_get_customer_shipping_matching_zone_id' ) ) {
	/**
	 * Get customer shipping matching zone id.
	 *
	 * @return array
	 */
	function aiow_get_customer_shipping_matching_zone_id() {
		$package = false;
		if ( is_user_logged_in() ) {
			$current_user = wp_get_current_user();
			if ( '' != ( $meta = get_user_meta( $current_user->ID, 'shipping_country', true ) ) ) {
				$package['destination']['country']  = $meta;
				$package['destination']['state']    = get_user_meta( $current_user->ID, 'shipping_state', true );
				$package['destination']['postcode'] = '';
			}
		}
		if ( false === $package ) {
			$package['destination'] = wc_get_customer_default_location();
			$package['destination']['postcode'] = '';
		}
		$data_store = WC_Data_Store::load( 'shipping-zone' );
		return $data_store->get_zone_id_from_package( $package );
	}
}

if ( ! function_exists( 'aiow_get_product_shipping_class_term_id' ) ) {
	/**
	 * Get product shipping class term id.
	 *
	 * @param object $_product Product data.
	 * @return mixed
	 */
	function aiow_get_product_shipping_class_term_id( $_product ) {
		$product_shipping_class = $_product->get_shipping_class();
		if ( '' != $product_shipping_class ) {
			foreach ( WC()->shipping->get_shipping_classes() as $shipping_class ) {
				if ( $product_shipping_class === $shipping_class->slug ) {
					return $shipping_class->term_id;
				}
			}
		}
		return 0;
	}
}

if ( ! function_exists( 'aiow_get_shipping_classes' ) ) {
	/**
	 * Get shipping classes.
	 *
	 * @param bool $include_empty_shipping_class Include empty shipping class.
	 * @return array
	 */
	function aiow_get_shipping_classes( $include_empty_shipping_class = true ) {
		$shipping_classes = WC()->shipping->get_shipping_classes();
		$shipping_classes_data = array();
		foreach ( $shipping_classes as $shipping_class ) {
			$shipping_classes_data[ $shipping_class->term_id ] = $shipping_class->name;
		}
		if ( $include_empty_shipping_class ) {
			$shipping_classes_data[0] = __( 'No shipping class', 'woocommerce' );
		}
		return $shipping_classes_data;
	}
}

if ( ! function_exists( 'aiow_get_shipping_methods' ) ) {
	/**
	 * Get shipping methods.
	 *
	 * @return array $shipping_methods Shipping methods.
	 */
	function aiow_get_shipping_methods() {
		$shipping_methods = array();
		foreach ( WC()->shipping()->load_shipping_methods() as $method ) {
			$shipping_methods[ $method->id ] = $method->get_method_title();
		}
		return $shipping_methods;
	}
}

if ( ! function_exists( 'aiow_get_shipping_zones' ) ) {
	/**
	 * Get shipping zones.
	 *
	 * @param bool $include_empty_zone Include empty zone.
	 * @return array
	 */
	function aiow_get_shipping_zones( $include_empty_zone = true ) {
		$zones = WC_Shipping_Zones::get_zones();
		if ( $include_empty_zone ) {
			$zone                                                = new WC_Shipping_Zone( 0 );
			$zones[ $zone->get_id() ]                            = $zone->get_data();
			$zones[ $zone->get_id() ]['zone_id']                 = $zone->get_id();
			$zones[ $zone->get_id() ]['formatted_zone_location'] = $zone->get_formatted_location();
			$zones[ $zone->get_id() ]['shipping_methods']        = $zone->get_shipping_methods();
		}
		return $zones;
	}
}

if ( ! function_exists( 'aiow_get_shipping_methods_instances' ) ) {
	/**
	 * Get shipping methods instances.
	 *
	 * @param bool $full_data Full data.
	 * @return array
	 */
	function aiow_get_shipping_methods_instances( $full_data = false ) {
		$shipping_methods = array();
		foreach ( aiow_get_shipping_zones() as $zone_id => $zone_data ) {
			foreach ( $zone_data['shipping_methods'] as $shipping_method ) {
				if ( $full_data ) {
					$shipping_methods[ $shipping_method->instance_id ] = array(
						'zone_id'                     => $zone_id,
						'zone_name'                   => $zone_data['zone_name'],
						'formatted_zone_location'     => $zone_data['formatted_zone_location'],
						'shipping_method_title'       => $shipping_method->title,
						'shipping_method_id'          => $shipping_method->id,
						'shipping_method_instance_id' => $shipping_method->instance_id,
					);
				} else {
					$shipping_methods[ $shipping_method->instance_id ] = $zone_data['zone_name'] . ': ' . $shipping_method->title;
				}
			}
		}
		return $shipping_methods;
	}
}

if ( ! function_exists( 'aiow_get_woocommerce_package_rates_module_filter_priority' ) ) {
	/**
	 * Get woocommerce package rates module filter priority.
	 *
	 * @param string $module_id Module ID.
	 * @return bool
	 */
	function aiow_get_woocommerce_package_rates_module_filter_priority( $module_id ) {
		$modules_priorities = array(
			'shipping_options_hide_free_shipping'  => PHP_INT_MAX,
			'shipping_by_products'                 => PHP_INT_MAX - 100,
			'shipping_by_user_role'                => PHP_INT_MAX - 100,
			'shipping_by_cities'                   => PHP_INT_MAX - 100,
		);
		return ( 0 != ( $priority = aiow_option( 'aiow_' . $module_id . '_filter_priority', 0 ) ) ?
			$priority :
			( isset( $modules_priorities[ $module_id ] ) ? $modules_priorities[ $module_id ] : PHP_INT_MAX )
		);
	}
}

if ( ! function_exists( 'aiow_get_left_to_free_shipping' ) ) {
	/**
	 * Get left to free shipping.
	 *
	 * @param String $content Content.
	 * @param String $multiply_by Multiply By.
	 * @return mixed
	 */
	function aiow_get_left_to_free_shipping( $content, $multiply_by = 1 ) {
		// "You have Free delivery"
		if ( function_exists( 'WC' ) && ( WC()->shipping ) && ( $packages = WC()->shipping->get_packages() ) ) {
			foreach ( $packages as $i => $package ) {
				$available_shipping_methods = $package['rates'];
				if ( aiow_is_module_enabled( 'shipping_by_user_role' ) ) {
					$available_shipping_methods = AIOW()->modules['shipping_by_user_role']->available_shipping_methods( $available_shipping_methods, false );
				}
				if ( is_array( $available_shipping_methods ) ) {
					foreach ( $available_shipping_methods as $available_shipping_method ) {
						$method_id = ( AIOW_IS_WC_VERSION_BELOW_3_2_0 ? $available_shipping_method->method_id : $available_shipping_method->get_method_id() );
						if ( 'free_shipping' === $method_id ) {
							return do_shortcode( aiow_option( 'aiow_shipping_left_to_free_info_content_reached', __( 'You have Free delivery', 'woocommerce-jetpack' ) ) );
						}
					}
				}
			}
		}
		$min_free_shipping_amount = 0;
		if ( version_compare( AIOW_WC_VERSION, '2.6.0', '<' ) ) {
			$free_shipping = new WC_Shipping_Free_Shipping();
			if ( in_array( $free_shipping->requires, array( 'min_amount', 'either', 'both' ) ) ) {
				$min_free_shipping_amount = $free_shipping->min_amount;
			}
		} else {
			$legacy_free_shipping = new WC_Shipping_Legacy_Free_Shipping();
			if ( 'yes' === $legacy_free_shipping->enabled ) {
				if ( in_array( $legacy_free_shipping->requires, array( 'min_amount', 'either', 'both' ) ) ) {
					$min_free_shipping_amount = $legacy_free_shipping->min_amount;
				}
			}
			if ( 0 == $min_free_shipping_amount ) {
				if ( function_exists( 'WC' ) && ( $wc_shipping = WC()->shipping ) && ( $wc_cart = WC()->cart ) ) {
					if ( $wc_shipping->enabled ) {
						if ( $packages = $wc_cart->get_shipping_packages() ) {
							$shipping_methods = $wc_shipping->load_shipping_methods( $packages[0] );
							if ( aiow_is_module_enabled( 'shipping_by_user_role' ) ) {
								$shipping_methods = AIOW()->modules['shipping_by_user_role']->available_shipping_methods( $shipping_methods, false );
							}
							foreach ( $shipping_methods as $shipping_method ) {
								if ( 'yes' === $shipping_method->enabled && 0 != $shipping_method->instance_id ) {
									if ( 'WC_Shipping_Free_Shipping' === get_class( $shipping_method ) ) {
										if ( in_array( $shipping_method->requires, array( 'min_amount', 'either', 'both' ) ) ) {
											$min_free_shipping_amount = $shipping_method->min_amount;
											break;
										}
									}
								}
							}
						}
					}
				}
			}
		}
		if ( 0 != $min_free_shipping_amount ) {
			if ( isset( WC()->cart ) ) {
				// Getting cart total
				$total = WC()->cart->get_displayed_subtotal();
				$is_cart_display_prices_including_tax = ( AIOW_IS_WC_VERSION_BELOW_3_3_0 ?
					( 'incl' === WC()->cart->tax_display_cart ) : WC()->cart->display_prices_including_tax() );
				if ( $is_cart_display_prices_including_tax ) {
					$total = round( $total - ( WC()->cart->get_discount_total() + WC()->cart->get_discount_tax() ), wc_get_price_decimals() );
				} else {
					$total = round( $total - WC()->cart->get_discount_total(), wc_get_price_decimals() );
				}
				// Final message
				if ( $total >= $min_free_shipping_amount ) {
					return do_shortcode( aiow_option( 'aiow_shipping_left_to_free_info_content_reached', __( 'You have Free delivery', 'woocommerce-jetpack' ) ) );
				} else {
					return aiow_handle_replacements( array(
						'%left_to_free%'             => wc_price( ( $min_free_shipping_amount - $total ) * $multiply_by ),
						'%free_shipping_min_amount%' => wc_price( ( $min_free_shipping_amount )          * $multiply_by ),
						'%cart_total%'               => wc_price( ( $total )                             * $multiply_by ),
					), ( '' == $content ? __( '%left_to_free% left to free shipping', 'woocommerce-jetpack' ) : $content ) );
				}
			}
		}
	}
}
