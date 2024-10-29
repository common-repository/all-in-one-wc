<?php
/**
 * Shipping - Custom Shipping with Shipping Zones
 *
 * @package WordPress
 * @subpackage WooCommerce
 */

namespace AIOW\Modules\Shipping;

if ( ! class_exists( 'Shipping_Zones' ) ) {

	/**
	 * Declare class `Shipping_Zones` extends to `\WC_Shipping_Method`.
	 */
	class Shipping_Zones extends \WC_Shipping_Method {

		/**
		 * Form field ID.
		 *
		 * @var array $form_field_ids
		 */
		public static $form_field_ids = array();

		/**
		 * Settings.
		 *
		 * @var array $aiow_settings
		 */
		public static $aiow_settings = array();

		/**
		 * Class Constructor.
		 *
		 * @param int $instance_id Instance ID.
		 */
		function __construct( $instance_id = 0 ) {
			$this->init( $instance_id );
		}

		/**
		 * Init settings
		 *
		 * @param int $instance_id Instance ID.
		 */
		function init( $instance_id ) {
			$this->id                 = 'aiow_custom_shipping_w_zones';
			$this->method_title       = aiow_option( 'aiow_shipping_custom_shipping_w_zones_admin_title', __( 'Custom Shipping', 'all-in-one-wc' ) );
			$this->method_description = __( 'Custom Shipping Method', 'all-in-one-wc' );
			$this->instance_id = absint( $instance_id );
			$this->supports    = array(
				'shipping-zones',
				'instance-settings',
				'instance-settings-modal',
			);
			// Load the settings.
			$this->init_instance_form_fields();
			// Define user set variables.
			$this->title                      = $this->get_option( 'title' );
			$this->cost                       = $this->get_option( 'cost' );
			$this->min_weight                 = $this->get_option( 'min_weight' );
			$this->max_weight                 = $this->get_option( 'max_weight' );
			$this->type                       = $this->get_option( 'type' );
			$this->apply_formula              = apply_filters( 'aiow_option', 'no', $this->get_option( 'apply_formula' ) );
			$this->cost_rounding              = apply_filters( 'aiow_option', 'no_round', $this->get_option( 'cost_rounding', 'no_round' ) );
			$this->weight_table_total_rows    = $this->get_option( 'weight_table_total_rows' );
			// Save settings in admin.
			add_action( 'woocommerce_update_options_shipping_' . $this->id, array( $this, 'process_admin_options' ) );
			// Sanitize Settings.
			add_filter( 'woocommerce_shipping_' . $this->id . '_instance_settings_values', array( $this, 'sanitize_settings' ), 10, 2 );
			// Add weight table rows.
			if ( apply_filters( 'aiow_custom_shipping_do_add_table_rows', true, $this ) ) {
				if ( ! has_filter( 'woocommerce_shipping_instance_form_fields_' . $this->id, array( $this, 'add_table_rows' ) ) ) {
					if ( ! in_array( $instance_id, self::$form_field_ids ) && $this->instance_id != 0 ) {
						self::$form_field_ids[] = $instance_id;
						add_filter( 'woocommerce_shipping_instance_form_fields_' . $this->id, array( $this, 'add_table_rows' ) );
					}
				}
			}
		}

		/**
		 * Sanitize settings.
		 *
		 * @param array $settings Settings.
		 * @param array $shipping_method Shipping method.
		 * @return array
		 */
		function sanitize_settings( $settings, $shipping_method ) {
			$settings       = array_filter( $settings );
			$total_rows     = isset( $settings['weight_table_total_rows'] ) ? $settings['weight_table_total_rows'] : 0;
			$keys_to_remove = array();
			foreach ( $settings as $key => $value ) {
				if (
					preg_match( '/(?<=row_)\d+/m', $key, $results ) &&
					$results[0] > $total_rows
				) {
					$keys_to_remove[] = $key;
				}
			}
			// Remove keys greater then 'total rows' amount
			if ( count( $keys_to_remove ) > 0 ) {
				$settings = array_diff_key( $settings, array_flip( $keys_to_remove ) );
			}
			return $settings;
		}

		/**
		 * Add table row.
		 *
		 * @param bool $instance_form_fields Instance fields.
		 * @return array
		 */
		function add_table_rows( $instance_form_fields ) {
			if ( $this->instance_id ) {
				$settings_name = 'woocommerce_' . $this->id . '_' . $this->instance_id . '_settings';
				if ( ! isset( self::$aiow_settings[ $settings_name ] ) ) {
					$settings = aiow_option( $settings_name );
					self::$aiow_settings[ $settings_name ] = $settings;
				} else {
					$settings = self::$aiow_settings[ $settings_name ];
				}
				$this->weight_table_total_rows = isset( $settings['weight_table_total_rows'] ) ? $settings['weight_table_total_rows'] : 1;
				for ( $i = 1; $i <= $this->weight_table_total_rows; $i ++ ) {
					if ( ! isset( $instance_form_fields[ 'weight_table_weight_row_' . $i ] ) ) {
						$instance_form_fields = array_merge( $instance_form_fields, array(
							'weight_table_weight_row_' . $i => array( // mislabeled, should be 'table_weight_row_'
								'title'             => __( 'Max Weight or Quantity', 'all-in-one-wc' ) . ' #' . $i,
								'type'              => 'number',
								'default'           => 0,
								'desc_tip'          => true,
								'css'               => 'width:100%',
								'custom_attributes' => array( 'step' => '0.000001', 'min' => '0' ),
							),
							'weight_table_cost_row_' . $i   => array(   // mislabeled, should be 'table_cost_row_'
								'title'    => __( 'Cost', 'all-in-one-wc' ) . ' #' . $i,
								'type'     => 'text',
								'default'  => 0,
								'desc_tip' => true,
								'css'      => 'width:100%',
							),
						) );
					}
				}
			}
			return $instance_form_fields;
		}

		/**
		 * Is this method available?
		 *
		 * @param   array $package Package.
		 * @return  bool
		 */
		function is_available( $package ) {
			$available = parent::is_available( $package );
			if ( $available ) {
				$total_weight = WC()->cart->get_cart_contents_weight();
				if ( 0 != $this->min_weight && $total_weight < $this->min_weight ) {
					$available = false;
				} elseif ( 0 != $this->max_weight && $total_weight > $this->max_weight ) {
					$available = false;
				}
			}
			return $available;
		}

		/**
		 * Initialise Settings Form Fields
		 */
		function init_instance_form_fields() {
			$type_options = array(
				'flat_rate'                    => __( 'Flat rate', 'all-in-one-wc' ),
				'by_total_cart_weight'         => __( 'By total cart weight', 'all-in-one-wc' ),
				'by_total_cart_weight_table'   => __( 'By total cart weight table', 'all-in-one-wc' ),
				'by_total_cart_quantity'       => __( 'By total cart quantity', 'all-in-one-wc' ),
			);
			$type_options = apply_filters( 'aiow_option', $type_options, array_merge( $type_options, array(
				'by_total_cart_quantity_table' => __( 'By total cart quantity table', 'all-in-one-wc' ),
			) ) );
			$this->instance_form_fields = array(
				'title' => array(
					'title'       => __( 'Title', 'all-in-one-wc' ),
					'type'        => 'text',
					'description' => __( 'This controls the title which the user sees during checkout.', 'all-in-one-wc' ),
					'default'     => __( 'Custom Shipping', 'all-in-one-wc' ),
					'desc_tip'    => true,
					'css'         => 'width:100%',
				),
				'type' => array(
					'title'       => __( 'Type', 'all-in-one-wc' ),
					'type'        => 'select',
					'description' => __( 'Cost calculation type.', 'all-in-one-wc' ) . ' ' .
					                 apply_filters( 'aiow_message', '', 'desc_advanced_no_link', array( 'option' => __( 'By Total Cart Quantity Table', 'all-in-one-wc' ) ) ),
					'default'     => 'flat_rate',
					'desc_tip'    => true,
					'options'     => $type_options,
					'css'         => 'width:100%',
				),
				'cost' => array(
					'title'       => __( 'Cost', 'all-in-one-wc' ),
					'type'        => 'text',
					'description' => __( 'Cost. If calculating by weight - then cost per one weight unit. If calculating by quantity - then cost per one piece.', 'all-in-one-wc' ),
					'default'     => 0,
					'desc_tip'    => true,
					'css'         => 'width:100%',
				),
				'cost_rounding' => array(
					'title'       => __( 'Cost Rounding', 'all-in-one-wc' ),
					'desc_tip'    => __( 'How the final cost will be rounded.', 'all-in-one-wc' ),
					'css'         => 'width:100%',
					'default'     => 'no_round',
					'type'        => 'select',
					'options'     => array(
						'no_round'   => __( 'No rounding', 'all-in-one-wc' ),
						'round'      => __( 'Round', 'all-in-one-wc' ),
						'round_up'   => __( 'Round up', 'all-in-one-wc' ),
						'round_down' => __( 'Round down', 'all-in-one-wc' ),
					),
				),
				'min_weight' => array(
					'title'       => __( 'Min Weight', 'all-in-one-wc' ),
					'type'        => 'number',
					'description' => __( 'Minimum total cart weight. Set zero to disable.', 'all-in-one-wc' ),
					'default'     => 0,
					'desc_tip'    => true,
					'css'         => 'width:100%',
					'custom_attributes' => array( 'step' => '0.000001', 'min' => '0' ),
				),
				'max_weight' => array(
					'title'       => __( 'Max Weight', 'all-in-one-wc' ),
					'type'        => 'number',
					'description' => __( 'Maximum total cart weight. Set zero to disable.', 'all-in-one-wc' ),
					'default'     => 0,
					'desc_tip'    => true,
					'css'         => 'width:100%',
					'custom_attributes' => array( 'step' => '0.000001', 'min' => '0' ),
				),
				'apply_formula' => array(
					'title'       => __( 'Apply Formula and Shortcodes to Costs', 'all-in-one-wc' ),
					'description' => sprintf( __( 'You can use %s and %s params in formula, e.g.: %s. Also you can use shortcodes, e.g.: %s.', 'all-in-one-wc' ),
							'<em>weight</em>', '<em>quantity</em>', '<em>2.5+weight</em>', '<em>[aiow_shipping_costs_table prop="weight" table="25-12.25|50-14.50|9999-29.148"]</em>' ) . '<br>' .
					                 apply_filters( 'aiow_message', '', 'desc_no_link' ),
					'desc_tip'    => true,
					'type'        => 'checkbox',
					'default'     => 'no',
					'custom_attributes' => apply_filters( 'aiow_message', '', 'disabled' ),
				),
				'weight_table_total_rows' => array( // mislabeled, should be 'table_total_rows'
					'title'       => __( 'Table Total Rows', 'all-in-one-wc' ),
					'type'        => 'number',
					'description' => __( 'Press "Save changes" and reload the page after you change this number.', 'all-in-one-wc' ),
					'default'     => 0,
					'desc_tip'    => true,
					'css'         => 'width:100%',
					'custom_attributes' => array( 'min' => '0' ),
				),
			);
		}

		/**
		 * Calculate shipping by table.
		 *
		 * @param mixed $weight Weight.
		 * @return mixed
		 */
		function calculate_shipping_by_table( $weight ) {
			if ( 0 == $this->weight_table_total_rows ) {
				return $this->cost * $weight; // fallback
			}
			$option_name_weight = $option_name_cost = '';
			for ( $i = 1; $i <= $this->weight_table_total_rows; $i++ ) {
				$option_name_weight = 'weight_table_weight_row_' . $i;
				$option_name_cost = 'weight_table_cost_row_' . $i;
				if ( $weight <= $this->get_option( $option_name_weight ) ) {
					return $this->get_option( $option_name_cost );
				}
			}
			return $this->get_option( $option_name_cost ); // fallback - last row
		}

		/**
		 * Maybe apply formula.
		 *
		 * @param string $formula Formula.
		 * @return string
		 */
		function maybe_apply_formula( $formula ) {
			if ( 'yes' !== $this->apply_formula ) {
				return $formula;
			}
			$formula = do_shortcode( $formula );
			require_once( aiow_plugin_path() . '/vendor/math-parser/Math_Parser.php' );
			$math = new \Math_Parser();
			$variables = array(
				'quantity' => $this->get_total_cart_quantity(),
				'weight'   => WC()->cart->get_cart_contents_weight(),
			);
			foreach ( $variables as $key => $value ) {
				$math->registerVariable( $key, $value );
				$formula = str_replace( $key, '$' . $key, $formula );
			}
			try {
				return $math->evaluate( $formula );
			} catch ( Exception $e ) {
				return $formula;
			}
		}

		/**
		 * Get total cart quantity.
		 *
		 * @return int
		 */
		function get_total_cart_quantity() {
			$cart_quantity = 0;
			foreach ( WC()->cart->get_cart() as $cart_item_key => $values ) {
				$cart_quantity += $values['quantity'];
			}
			return $cart_quantity;
		}

		/**
		 * Calculate shipping.
		 *
		 * @param   mixed $package Packages.
		 * @return  void
		 */
		function calculate_shipping( $package = array() ) {
			switch ( $this->type ) {
				case 'by_total_cart_quantity':
					$cost = $this->cost * $this->get_total_cart_quantity();
					break;
				case 'by_total_cart_weight':
					$cost = $this->cost * WC()->cart->get_cart_contents_weight();
					break;
				case 'by_total_cart_quantity_table':
					$cost = $this->calculate_shipping_by_table( $this->get_total_cart_quantity() );
					break;
				case 'by_total_cart_weight_table':
					$cost = $this->calculate_shipping_by_table( WC()->cart->get_cart_contents_weight() );
					break;
				default: // 'flat_rate'
					$cost = $this->cost;
					break;
			}
			$rate = array(
				'id'       => $this->get_rate_id(),
				'label'    => $this->title,
				'cost'     => $this->maybe_apply_formula( $cost ),
				'calc_tax' => 'per_order',
			);

			// Rounding.
			switch ( $this->cost_rounding ) {
				case 'round':
					$rate['cost'] = round( $rate['cost'], wc_get_rounding_precision() );
					break;
				case 'round_up':
					$rate['cost'] = ceil( $rate['cost'] );
					break;
				case 'round_down':
					$rate['cost'] = floor( $rate['cost'] );
					break;
			}
			// Register the rate.
			$this->add_rate( $rate );
		}
	}
}
