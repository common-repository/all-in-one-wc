<?php
/**
 * Shipping - Custom Shipping
 *
 * @package WordPress
 * @subpackage WooCommerce
 */

namespace AIOW\Modules\Shipping;

if ( ! class_exists( 'Custom_Template' ) ) {

	/**
	 * Declare class `Custom_Template` extends to `\WC_Shipping_Method`.
	 */
	class Custom_Template extends \WC_Shipping_Method {

		/**
		 * Class Constructor.
		 */
		function __construct() {
			return true;
		}

		/**
		 * Init settings
		 *
		 * @param int $id_count ID Count.
		 */
		function init( $id_count ) {
			$this->id                 = 'aiow_custom_shipping_' . $id_count;
			$this->method_title       = aiow_option( 'aiow_shipping_custom_shipping_admin_title_' . $id_count, __( 'Custom', 'all-in-one-wc' ) . ' #' . $id_count );
			$this->method_description = __( 'aiow: Custom Shipping Method', 'all-in-one-wc' ) . ' #' . $id_count;

			// Load the settings.
			$this->init_form_fields();
			$this->init_settings();

			// Define user set variables.
			$this->enabled    = $this->get_option( 'enabled' );
			$this->title      = $this->get_option( 'title' );
			$this->cost       = $this->get_option( 'cost' );
			$this->min_weight = $this->get_option( 'min_weight' );
			$this->max_weight = $this->get_option( 'max_weight' );
			$this->type       = $this->get_option( 'type' );
			$this->weight_table_total_rows = $this->get_option( 'weight_table_total_rows' );
			for ( $i = 1; $i <= $this->weight_table_total_rows; $i++ ) {
				$option_name = 'weight_table_weight_row_' . $i;
				$this->{$option_name} = $this->get_option( $option_name );
				$option_name = 'weight_table_cost_row_' . $i;
				$this->{$option_name} = $this->get_option( $option_name );
			}
			// Save settings in admin.
			add_action( 'woocommerce_update_options_shipping_' . $this->id, array( $this, 'process_admin_options' ) );
		}

		/**
		 * Is this method available.
		 *
		 * @param array $package Package.
		 * @return bool
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
		function init_form_fields() {
			$type_options = array(
				'flat_rate'                    => __( 'Flat Rate', 'all-in-one-wc' ),
				'by_total_cart_weight'         => __( 'By Total Cart Weight', 'all-in-one-wc' ),
				'by_total_cart_weight_table'   => __( 'By Total Cart Weight Table', 'all-in-one-wc' ),
				'by_total_cart_quantity'       => __( 'By Total Cart Quantity', 'all-in-one-wc' ),
			);
			$type_options = apply_filters( 'aiow_option', $type_options, array_merge( $type_options, array(
				'by_total_cart_quantity_table' => __( 'By Total Cart Quantity Table', 'all-in-one-wc' ),
			) ) );
			$this->form_fields = array(
				'enabled' => array(
					'title'       => __( 'Enable/Disable', 'all-in-one-wc' ),
					'type'        => 'checkbox',
					'label'       => __( 'Enable Custom Shipping', 'all-in-one-wc' ),
					'default'     => 'no',
				),
				'title' => array(
					'title'       => __( 'Title', 'all-in-one-wc' ),
					'type'        => 'text',
					'description' => __( 'This controls the title which the user sees during checkout.', 'all-in-one-wc' ),
					'default'     => __( 'Custom Shipping', 'all-in-one-wc' ),
					'desc_tip'    => true,
				),
				'type' => array(
					'title'       => __( 'Type', 'all-in-one-wc' ),
					'type'        => 'select',
					'description' => __( 'Cost calculation type.', 'all-in-one-wc' ) . ' ' . apply_filters( 'aiow_message', '', 'desc_advanced_no_link', array( 'option' => __( 'By Total Cart Quantity Table', 'all-in-one-wc' ) ) ),
					'default'     => 'flat_rate',
					'desc_tip'    => true,
					'options'     => $type_options,
				),
				'cost' => array(
					'title'       => __( 'Cost', 'all-in-one-wc' ),
					'type'        => 'number',
					'description' => __( 'Cost. If calculating by weight - then cost per one weight unit. If calculating by quantity - then cost per one piece.', 'all-in-one-wc' ),
					'default'     => 0,
					'desc_tip'    => true,
					'custom_attributes' => array( 'step' => '0.000001', 'min'  => '0', ),
				),
				'min_weight' => array(
					'title'       => __( 'Min Weight', 'all-in-one-wc' ),
					'type'        => 'number',
					'description' => __( 'Minimum total cart weight. Set zero to disable.', 'all-in-one-wc' ),
					'default'     => 0,
					'desc_tip'    => true,
					'custom_attributes' => array( 'step' => '0.000001', 'min'  => '0', ),
				),
				'max_weight' => array(
					'title'       => __( 'Max Weight', 'all-in-one-wc' ),
					'type'        => 'number',
					'description' => __( 'Maximum total cart weight. Set zero to disable.', 'all-in-one-wc' ),
					'default'     => 0,
					'desc_tip'    => true,
					'custom_attributes' => array( 'step' => '0.000001', 'min'  => '0', ),
				),
				'weight_table_total_rows' => array(
					'title'       => __( 'Table Total Rows', 'all-in-one-wc' ),
					'type'        => 'number',
					'description' => __( 'Press Save changes after you change this number.', 'all-in-one-wc' ),
					'default'     => 0,
					'desc_tip'    => true,
					'custom_attributes' => array( 'min'  => '0', ),
				),
			);
			for ( $i = 1; $i <= $this->get_option( 'weight_table_total_rows' ); $i++ ) {
				$this->form_fields = array_merge( $this->form_fields, array(
					'weight_table_weight_row_' . $i => array(
						'title'       => __( 'Max Weight or Quantity', 'all-in-one-wc' ) . ' #' . $i,
						'type'        => 'number',
						'default'     => 0,
						'desc_tip'    => true,
						'custom_attributes' => array( 'step' => '0.000001', 'min'  => '0', ),
					),
					'weight_table_cost_row_' . $i => array(
						'title'       => __( 'Cost', 'all-in-one-wc' ) . ' #' . $i,
						'type'        => 'number',
						'default'     => 0,
						'desc_tip'    => true,
						'custom_attributes' => array( 'step' => '0.000001', 'min'  => '0', ),
					),
				) );
			}
		}

		/**
		 * Calculate shipping by table.
		 *
		 * @param mixed $weight Weight.
		 * @return mixed
		 */
		function calculate_shipping_by_table( $weight ) {
			if ( 0 == $this->weight_table_total_rows ) {
				return $this->cost * $weight;
			}
			$option_name_weight = $option_name_cost = '';
			for ( $i = 1; $i <= $this->weight_table_total_rows; $i++ ) {
				$option_name_weight = 'weight_table_weight_row_' . $i;
				$option_name_cost = 'weight_table_cost_row_' . $i;
				if ( $weight <= $this->{$option_name_weight} ) {
					return $this->{$option_name_cost};
				}
			}
			return $this->{$option_name_cost};
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
		 * @param array $package Packages.
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
				default:
					$cost = $this->cost;
					break;
			}
			$rate = array(
				'id'       => $this->id,
				'label'    => $this->title,
				'cost'     => $cost,
				'calc_tax' => 'per_order',
			);
			// Register the rate.
			$this->add_rate( $rate );
		}
	}
}
