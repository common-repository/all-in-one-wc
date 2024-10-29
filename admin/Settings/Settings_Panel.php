<?php
/**
 * All In One For WooCommerce - Settings
 *
 * @package WordPress
 * @subpackage WooCoomerce
 */

namespace AIOW\Admin\Settings;

use AIOW\Admin\Settings\Custom_Fields as CF;

if ( ! class_exists( 'Settings_Panel' ) ) :

class Settings_Panel extends \WC_Settings_Page {

	/**
	 * Constructor.
	 */
	function __construct() {

		$this->id    = 'all_in_one_wc';
		$this->label = __( 'All In One For WooCommerce', 'all-in-one-wc' );

		$this->cats  = aiow_admin_setting_tab();

		$this->custom_dashboard_modules = apply_filters( 'aiow_custom_dashboard_modules', array() );

		add_filter( 'woocommerce_settings_tabs_array',         array( $this, 'add_settings_page' ), 20 );
		add_action( 'woocommerce_settings_' . $this->id,       array( $this, 'output' ) );
		add_action( 'woocommerce_settings_save_' . $this->id,  array( $this, 'save' ) );
		add_action( 'woocommerce_sections_' . $this->id,       array( $this, 'output_cats_submenu' ) );
		add_action( 'woocommerce_sections_' . $this->id,       array( $this, 'output_sections_submenu' ) );

		// Create free version notices.
		add_action( 'woocommerce_after_settings_' . $this->id, array( $this, 'create_free_version_notice_about_reasons_to_upgrade' ) );
		
		// Display custom fields.
		$custom_fields = new CF();
	}

	/**
	 * Create free version notice.
	 */
	function create_free_version_notice_about_reasons_to_upgrade() {
		if ( 'all-in-one-wc.php' !== basename( AIOW_PLUGIN_FILE ) ) {
			return;
		}
		$notice_class  = 'notice notice-info inline';
		$texts         = array(
			'title'   => __( 'Upgrade today to unlock these popular premium features:', 'all-in-one-wc' ),
			'reasons' => array(
				__( 'Add ability to create Proforma Invoices, Credit Notes and Packing slips', 'all-in-one-wc' ),
				__( '<strong>Cart and checkout</strong> – add multiple – custom fields, custom info blocks, check out file uploads', 'all-in-one-wc' ),
				__( '<strong>Prices and currencies</strong> – add more unlimited number of currencies to WooCommerce', 'all-in-one-wc' ),
				__( '<strong>Add to cart</strong> – customize add to cart messages, Button labels - multiple category groups allowed', 'all-in-one-wc' ),
				__( '<strong>Empty Cart</strong> – customize empty cart button text, different button positions on cart page', 'all-in-one-wc' ),
				__( '<strong>Mini cart</strong> – More custom information options', 'all-in-one-wc' ),
				__( '<strong>Export options</strong> – more fields enabled', 'all-in-one-wc' ),
				__( 'More configuration options for payments and shipping', 'all-in-one-wc' ),
			)
		);
		$reasons_left  = array_slice( $texts['reasons'], 0, ceil( count( $texts['reasons'] ) / 2 ) );
		$reasons_right = array_slice( $texts['reasons'], count( $texts['reasons'] ) - floor( count( $texts['reasons'] ) / 2 ) );
		$template      = '<div class="aiow-notice-wrapper">{title}{reasons_left}{reasons_right}{upgrade_btn}</div>';
		$array_from_to = array(
			'{title}'         => '<h3 class="aiow-notice-title">' . $texts['title'] . '</h3>',
			'{reasons_left}'  => '<ul class="aiow-list aiow-list-left">' . '<li>' . implode( '</li><li>', $reasons_left ) . '</ul>',
			'{reasons_right}' => '<ul class="aiow-list aiow-list-right">' . '<li>' . implode( '</li><li>', $reasons_right ) . '</ul>',
			'{upgrade_btn}'   => '',
		);
		$html          = str_replace( array_keys( $array_from_to ), $array_from_to, $template );
		?>
		<style>
			.aiow-button.button {
				margin: 2px 0 14px 0;
			}

			.aiow-list {
				vertical-align: top;
				width: 47%;
				display: inline-block;
				margin: 0 3% 10px 0;
			}

			@media screen and (max-width: 782px) {
				.aiow-list {
					width: 100%;
				}

				.aiow-list {
					margin-bottom: 0;
				}

				.aiow-list-right {
					margin-bottom: 10px;
				}
			}

			.aiow-list li:before {
				content: '+';
				margin: 0 5px 0 0;
			}

			.aiow-list-right {

			}

			.aiow-notice-title {
				margin: 5px 0 15px 0;
			}

			.aiow-notice-wrapper {
				margin: 0.5em 0;
				padding: 2px;
				font-size: 13px;
				line-height: 1.5;
			}
		</style>
		<?php
		echo '<div class="' . $notice_class . '">' . $html . '</div>';
	}

	/**
	 * Output cats
	 */
	function output_cats_submenu() {
		$current_cat = empty( $_REQUEST['aiow-cat'] ) ? 'dashboard' : sanitize_title( $_REQUEST['aiow-cat'] );
		if ( empty( $this->cats ) ) {
			return;
		}
		echo '<ul class="ul_sub subsubsub" style="text-transform: uppercase !important; font-weight: bold; margin-bottom: 10px !important;">';
		$array_keys = array_keys( $this->cats );
		foreach ( $this->cats as $id => $label_info ) {
			echo '<li class="li_same ' . ( $current_cat == $id ? 'li_current' : '' ) . '"><a href="' . admin_url( 'admin.php?page=wc-settings&tab=' . $this->id . '&aiow-cat=' . sanitize_title( $id ) ) . '" class="' . ( $current_cat == $id ? 'current' : '' ) . '">' . $label_info['label'] . '</a> ' . ( end( $array_keys ) == $id ? '' : '' ) . ' </li>';
		}
		echo '</ul>' . '<br class="clear" />';
	}

	/**
	 * Output sections (modules) sub menu
	 */
	function output_sections_submenu() {
		global $current_section;
		$sections    = $this->get_sections();
		$current_cat = empty( $_REQUEST['aiow-cat'] ) ? 'dashboard' : sanitize_title( $_REQUEST['aiow-cat'] );
		if ( 'dashboard' === $current_cat ) {

			// Counting modules
			$all    = 0;
			$active = 0;
			foreach ( $this->module_statuses as $module_status ) {
				if ( isset( $module_status['id'] ) && isset( $module_status['default'] ) ) {
					if ( 'yes' === aiow_option( $module_status['id'], $module_status['default'] ) ) {
						$active++;
					} elseif ( aiow_is_module_deprecated( $module_status['id'], true ) ) {
						continue;
					}
					$all++;
				}
			}

			$sections['alphabetically'] = __( 'Alphabetically', 'all-in-one-wc' ) . ' <span class="count">(' . $all . ')</span>';
			$sections['by_category']    = __( 'By Category', 'all-in-one-wc' )    . ' <span class="count">(' . $all . ')</span>';
			$sections['active']         = __( 'Active', 'all-in-one-wc' )         . ' <span class="count">(' . $active . ')</span>';
			//$sections['manager']        = __( 'Manage Settings', 'all-in-one-wc' );
			if ( ! empty( $this->custom_dashboard_modules ) ) {
				foreach ( $this->custom_dashboard_modules as $custom_dashboard_module_id => $custom_dashboard_module_data ) {
					$sections[ $custom_dashboard_module_id ] = $custom_dashboard_module_data['title'];
				}
			}
			if ( '' == $current_section ) {
				$current_section = 'by_category';
			}
		}
		if ( ! empty( $this->cats[ $current_cat ]['all_cat_ids'] ) ) {
			foreach ( $sections as $id => $label ) {
				if ( ! in_array( $id, $this->cats[ $current_cat ]['all_cat_ids'] ) ) {
					unset( $sections[ $id ] );
				}
			}
		}
		if ( empty( $sections ) || 1 === count( $sections ) ) {
			return;
		}
		foreach ( $this->cats[ $current_cat ]['all_cat_ids'] as $key => $id ) {
			if ( aiow_is_module_deprecated( $id, false, true ) ) {
				unset( $this->cats[ $current_cat ]['all_cat_ids'][ $key ] );
			}
		}
		$menu = array();
		foreach ( $this->cats[ $current_cat ]['all_cat_ids'] as $id ) {
			$menu[ $id ] = $sections[ $id ];
		}
		if ( 'dashboard' !== $current_cat && 'pdf_invoicing' !== $current_cat ) {
			asort( $menu );
		}
		$menu_links = array();
		foreach ( $menu as $id => $label ) {
			$url = admin_url( 'admin.php?page=wc-settings&tab=' . $this->id . '&aiow-cat=' . $current_cat . '&section=' . sanitize_title( $id ) );
			$menu_links[] = '<a href="' . $url . '" class="' . ( $current_section == $id ? 'current' : '' ) . '">' . $label . '</a>';
		}
		echo '<ul class="subsubsub sub_module">' . '<li>' . implode( '</li> <li>', $menu_links ) . '</li>' . '</ul>' . '<br class="clear" />';
	}

	/**
	 * Get category by section.
	 *
	 * @param string $section Section.
	 * @return string
	 */
	function get_cat_by_section( $section ) {
		foreach ( $this->cats as $id => $label_info ) {
			if ( ! empty( $label_info['all_cat_ids'] ) ) {
				if ( in_array( $section, $label_info['all_cat_ids'] ) ) {
						return $id;
				}
			}
		}
		return '';
	}

	/**
	 * Get sections (modules)
	 *
	 * @return array
	 */
	function get_sections() {
		return apply_filters( 'aiow_settings_sections', array( '' => __( 'Dashboard', 'all-in-one-wc' ) ) );
	}

	/**
	 * Check is activate OR not.
	 *
	 * @param string $active Activate
	 */
	function active( $active ) {
		return ( 'yes' === $active ) ? 'active' : 'inactive';
	}

	/**
	 * Check is dashboard section.
	 *
	 * @param string $current_section Current section name.
	 * @return bool
	 */
	function is_dashboard_section( $current_section ) {
		return in_array( $current_section, array_merge( array( '', 'alphabetically', 'by_category', 'active', 'manager' ), array_keys( $this->custom_dashboard_modules ) ) );
	}

	/**
	 * Output the settings.
	 */
	function output() {

		global $current_section, $aiow_notice;

		if ( '' != $aiow_notice ) {
			echo '<div id="aiow_message" class="updated"><p><strong>' . $aiow_notice . '</strong></p></div>';
		}

		$is_dashboard = $this->is_dashboard_section( $current_section );

		// Deprecated message
		if ( $replacement_module = aiow_is_module_deprecated( $current_section ) ) {
			echo '<div id="aiow_message" class="error">';
			echo '<p>';
			echo '<strong>';
			echo sprintf(
				__( 'Please note that current <em>%s</em> module is deprecated and will be removed in future updates. Please use <em>%s</em> module instead.', 'all-in-one-wc' ),
				AIOW()->modules[ $current_section ]->short_desc,
				'<a href="' . admin_url( 'admin.php?page=wc-settings&tab=all_in_one_wc&aiow-cat=' . $replacement_module['cat'] . '&section=' . $replacement_module['module'] ) . '">' .
					$replacement_module['title'] . '</a>'
			);
			echo ' <span style="color:red;">' . __( 'Module will be removed from the module\'s list as soon as you disable it.', 'all-in-one-wc' ) . '</span>';
			echo '</strong>';
			echo '</p>';
			echo '</div>';
		}

		// "Under development" message
		if ( isset( AIOW()->modules[ $current_section ]->dev ) && true === AIOW()->modules[ $current_section ]->dev ) {
			echo '<div id="aiow_message" class="error">';
			echo '<p>';
			echo '<strong>';
			echo sprintf( __( 'Please note that <em>%s</em> module is currently under development. Until stable module version is released, options can be changed or some options can be moved to paid plugin version.', 'all-in-one-wc' ), AIOW()->modules[ $current_section ]->short_desc );
			echo '</strong>';
			echo '</p>';
			echo '</div>';
		}

		if ( 'yes' === aiow_option( 'aiow_debug_tools_enabled', 'no' ) && 'yes' === aiow_option( 'aiow_debuging_enabled', 'no' ) ) {
			// Breadcrumbs
			$breadcrumbs_html = '';
			$breadcrumbs_html .= '<p>';
			$breadcrumbs_html .= '<code>';
			$breadcrumbs_html .= __( 'WooCommerce', 'all-in-one-wc' );
			$breadcrumbs_html .= ' > ';
			$breadcrumbs_html .= __( 'Settings', 'all-in-one-wc' );
			$breadcrumbs_html .= ' > ';
			foreach ( $this->cats as $id => $label_info ) {
				if ( $this->get_cat_by_section( $current_section ) === $id ) {
					$breadcrumbs_html .= $label_info['label'];
					break;
				}
			}
			if ( $is_dashboard && isset( $_GET['aiow-cat'] ) && 'dashboard' != $_GET['aiow-cat'] ) {
				$breadcrumbs_html .= $this->cats[ $_GET['aiow-cat'] ]['label'];
			}
			if ( ! $is_dashboard ) {
				$breadcrumbs_html .= ' > ';
				$sections = $this->get_sections();
				$breadcrumbs_html .= $sections[ $current_section ];
			}
			$breadcrumbs_html .= '</code>';
			$breadcrumbs_html .= '</p>';
			echo $breadcrumbs_html;
		}

		$settings = $this->get_settings( $current_section );

		if ( ! $is_dashboard ) {
			\WC_Admin_Settings::output_fields( $settings );
		} else {
			$this->output_dashboard( $current_section );
		}
	}

	/**
	 * Dashboard outpur.
	 *
	 * @param string $current_section Current section.
	 * @return string
	 */
	function output_dashboard( $current_section ) {

		if ( '' == $current_section ) {
			$current_section = 'by_category';
		}

		$the_settings = $this->get_settings();

		echo '<h3>' . $the_settings[0]['title'] . '</h3>';
		if ( isset( $this->custom_dashboard_modules[ $current_section ] ) ) {
			echo '<p>' . $this->custom_dashboard_modules[ $current_section ]['desc'] . '</p>';
		} elseif ( 'manager' != $current_section ) {
			echo '<p>' . $the_settings[0]['desc'] . '</p>';
		} else {
			echo '<p>' . __( 'This section lets you export, import or reset all modules settings.', 'all-in-one-wc' ) . '</p>';
		}

		if ( 'alphabetically' === $current_section ) {
			$this->output_dashboard_modules( $the_settings );
		} elseif ( 'by_category' === $current_section ) {
			foreach ( $this->cats as $cat_id => $cat_label_info ) {
				if ( 'dashboard' === $cat_id ) {
					continue;
				}
				if ( isset( $_GET['aiow-cat'] ) && 'dashboard' != $_GET['aiow-cat'] ) {
					if ( $cat_id != $_GET['aiow-cat'] ) {
						continue;
					}
				} else {
					echo '<h4>' . $cat_label_info['label'] . '</h4>';
				}
				$this->output_dashboard_modules( $the_settings, $cat_id );
			}
		} elseif ( 'active' === $current_section ) {
			$this->output_dashboard_modules( $the_settings, 'active_modules_only' );
		} elseif ( 'manager' === $current_section ) {
			$table_data = array(
				array(
					'<button style="width:100px;" class="button-primary" type="submit" name="aiow_export_settings">' . __( 'Export', 'all-in-one-wc' ) . '</button>',
					'<em>' . __( 'Export all options to a file.', 'all-in-one-wc' ) . '</em>',
				),
				array(
					'<button style="width:100px;" class="button-primary" type="submit" name="aiow_import_settings">' . __( 'Import', 'all-in-one-wc' ) . '</button>' .
						' ' . '<input type="file" name="aiow_import_settings_file">',
					'<em>' . __( 'Import all options from a file.', 'all-in-one-wc' ) . '</em>',
				),
				array(
					'<button style="width:100px;" class="button-primary" type="submit" name="aiow_reset_settings"' .
						aiow_get_js_confirmation( __( 'This will reset settings to defaults for all modules. Are you sure?', 'all-in-one-wc' ) ) . '>' .
							__( 'Reset', 'all-in-one-wc' )  . '</button>',
					'<em>' . __( 'Reset all options.', 'all-in-one-wc' ) . '</em>',
				),
				array(
					'<button style="width:100px;" class="button-primary" type="submit" name="aiow_reset_settings_meta"' .
						aiow_get_js_confirmation( __( 'This will delete all meta. Are you sure?', 'all-in-one-wc' ) ) . '>'  .
							__( 'Reset meta', 'all-in-one-wc' )  . '</button>',
					'<em>' . __( 'Reset all meta.', 'all-in-one-wc' ) . '</em>',
				),
			);
			$manager_settings = $this->get_manager_settings();
			foreach ( $manager_settings as $manager_settings_field ) {
				$table_data[] = array(
					'<label for="' . $manager_settings_field['id'] . '">' .
						'<input name="' . $manager_settings_field['id'] . '" id="' . $manager_settings_field['id'] . '" type="' . $manager_settings_field['type'] . '"' .
							' class="" value="1" ' . checked( aiow_option( $manager_settings_field['id'], $manager_settings_field['default'] ), 'yes', false ) . '>' .
						' ' . '<strong>' . $manager_settings_field['title'] . '</strong>' .
					'</label>',
					'<em>' . $manager_settings_field['desc'] . '</em>',
				);
			}
			echo aiow_get_table_html( $table_data, array( 'table_class' => 'widefat striped', 'table_heading_type' => 'none' ) );
		}

		if ( isset( $this->custom_dashboard_modules[ $current_section ] ) ) {
			$table_data = array();
			foreach ( $this->custom_dashboard_modules[ $current_section ]['settings'] as $_settings ) {
				$table_data[] = array(
					$_settings['title'],
					'<label for="' . $_settings['id'] . '">' .
						'<input name="' . $_settings['id'] .
							'" id="'    . $_settings['id'] .
							'" type="'  . $_settings['type'] .
							'" class="' . $_settings['class'] .
							'" value="' . aiow_option( $_settings['id'], $_settings['default'] )
						. '">' . ' ' . '<em>' . $_settings['desc'] . '</em>' .
					'</label>',
				);
			}
			echo aiow_get_table_html( $table_data, array( 'table_class' => 'widefat striped', 'table_heading_type' => 'vertical' ) );
		}

		$plugin_data  = get_plugin_data( AIOW_PLUGIN_FILE );
		$plugin_title = ( isset( $plugin_data['Name'] ) ? '[' . $plugin_data['Name'] . '] ' : '' );
		echo '<p style="text-align:right;color:gray;font-size:x-small;font-style:italic;">' . $plugin_title .
			__( 'Version', 'all-in-one-wc' ) . ': ' . aiow_option( AIOW_VERSION_OPTION, 'N/A' ) . '</p>';

	}

	/**
	 * Compare array.
	 *
	 * @param array $a array.
	 * @param array $b array.
	 * @return bool
	 */
	private function compare_for_usort( $a, $b ) {
		return strcmp( $a['title'], $b['title'] );
	}

	/**
	 * Output dashboard module.
	 *
	 * @param array  $settings Setting data.
	 * @param string $cat_id Category ID.
	 */
	function output_dashboard_modules( $settings, $cat_id = '' ) {
		?>
		<table class="wp-list-table widefat plugins">
			<thead>
			<tr>
			<th scope="col" id="cb" class="manage-column column-cb check-column" style=""><label class="screen-reader-text" for="cb-select-all-1"><?php _e( 'Select All', 'all-in-one-wc' ); ?></label><input id="cb-select-all-1" type="checkbox" style="margin-top:15px;"></th>
			<th scope="col" id="name" class="manage-column column-name" style=""><?php _e( 'Module', 'all-in-one-wc' ); ?></th>
			<th scope="col" id="description" class="manage-column column-description" style=""><?php _e( 'Description', 'all-in-one-wc' ); ?></th>
			</tr>
			</thead>
			<tfoot>
			<tr>
			<th scope="col" class="manage-column column-cb check-column" style=""><label class="screen-reader-text" for="cb-select-all-2"><?php _e( 'Select All', 'all-in-one-wc' ); ?></label><input id="cb-select-all-2" type="checkbox" style="margin-top:15px;"></th>
			<th scope="col" class="manage-column column-name" style=""><?php _e( 'Module', 'all-in-one-wc' ); ?></th>
			<th scope="col" class="manage-column column-description" style=""><?php _e( 'Description', 'all-in-one-wc' ); ?></th>
			</tr>
			</tfoot>
			<tbody id="the-list"><?php
				$html = '';
				usort( $settings, array( $this, 'compare_for_usort' ) );
				$total_modules = 0;
				foreach ( $settings as $the_feature ) {
					if ( 'checkbox' !== $the_feature['type'] ) {
						continue;
					}
					$section = $the_feature['id'];
					$section = str_replace( 'aiow_', '', $section );
					$section = str_replace( '_enabled', '', $section );
					if ( aiow_is_module_deprecated( $section, false, true ) ) {
						continue;
					}
					if ( '' != $cat_id ) {
						if ( 'active_modules_only' === $cat_id ) {
							if ( 'no' === aiow_option( $the_feature['id'], 'no' ) ) {
								continue;
							}
						} elseif ( $cat_id != $this->get_cat_by_section( $section ) ) {
							continue;
						}
					}
					$total_modules++;
					$html .= '<tr id="' . $the_feature['id'] . '" ' . 'class="' . $this->active( aiow_option( $the_feature['id'] ) ) . '">';
					$html .= '<th scope="row" class="check-column">';
					$html .= '<label class="screen-reader-text" for="' . $the_feature['id'] . '">' . $the_feature['desc'] . '</label>';
					$html .= '<input type="checkbox" name="' . $the_feature['id'] . '" value="1" id="' . $the_feature['id'] . '" ' . checked( aiow_option( $the_feature['id'] ), 'yes', false ) . '>';
					$html .= '</th>';
					$html .= '<td class="plugin-title">' . '<strong>' . $the_feature['title'] . '</strong>';
					$html .= '<div class="row-actions visible">';
					$html .= '<span class="0"><a href="' . admin_url() . 'admin.php?page=wc-settings&tab=all_in_one_wc&aiow-cat=' . $this->get_cat_by_section( $section ) . '&section=' . $section . '">' . __( 'Settings', 'woocommerce' ) . '</a></span>';
					if ( isset( $the_feature['aiow_link'] ) && '' != $the_feature['aiow_link'] ) {
						$html .= ' | <span class="0"><a href="' . $the_feature['aiow_link'] . '?utm_source=module_documentation&utm_medium=dashboard_link&utm_campaign=aiow_documentation" target="_blank">' . __( 'Documentation', 'woocommerce' ) . '</a></span>';
					}
					$html .= '</div>';
					$html .= '</td>';
					$html .= '<td class="column-description desc">';
					$html .= '<div class="plugin-description"><p>' . ( ( isset( $the_feature['aiow_desc'] ) ) ? $the_feature['aiow_desc'] : $the_feature['desc_tip'] ) . '</p></div>';
					$html .= '</td>';
					$html .= '</tr>';
				}
				echo $html;
				if ( 0 == $total_modules && 'active_modules_only' === $cat_id ) {
					echo '<tr><td colspan="3">' . '<em>' . __( 'No active modules found.', 'all-in-one-wc' ) . '</em>' . '</td></tr>';
				}
			?></tbody>
		</table><p style="color:gray;font-size:x-small;font-style:italic;"><?php echo __( 'Total Modules:' ) . ' ' . $total_modules; ?></p><?php
	}

	/**
	 * Save settings.
	 */
	function save() {
		global $current_section;
		$settings = $this->get_settings( $current_section );
		\WC_Admin_Settings::save_fields( $settings );
		$this->disable_autoload_options_from_section( $settings );
		add_action( 'admin_notices', array( $this, 'aiow_message_global' ) );
		do_action( 'wooaiow_after_settings_save', $this->get_sections(), $current_section );
	}

	/**
	 * Disabled autoload setting.
	 *
	 * @param array $settings Setting data.
	 */
	function disable_autoload_options_from_section( $settings ) {
		$fields         = wp_list_filter( $settings, array( 'autoload' => false ) );
		$fields         = wp_list_filter( $fields, array( 'type' => 'title' ), 'NOT' );
		$fields         = wp_list_filter( $fields, array( 'type' => 'sectionend' ), 'NOT' );
		$field_ids      = wp_list_pluck( $fields, 'id' );
		$fields_ids_str = '\'' . implode( '\',\'', $field_ids ) . '\'';
		global $wpdb;
		$sql = "
			UPDATE {$wpdb->options} SET autoload = 'no'
			WHERE option_name IN ({$fields_ids_str}) AND autoload != 'no'
			";
		$wpdb->query( $sql );
	}

	/**
	 * aiow_message_global.
	 */
	function aiow_message_global() {
		if ( '' != ( $message = apply_filters( 'aiow_message', '', 'global' ) ) ) {
			echo $message;
		}
	}

	/**
	 * Get setting manager.
	 */
	function get_manager_settings() {
		return array(
			array(
				'title'   => __( 'Autoload Options', 'all-in-one-wc' ),
				'type'    => 'checkbox',
				'desc'    => __( 'Choose if you want options to be autoloaded when calling add_option. After saving this option, you need to Reset all settings. Leave default value (i.e. Enabled) if not sure.', 'all-in-one-wc' ),
				'id'      => 'aiow_autoload_options',
				'default' => 'yes',
			),
			array(
				'title'   => __( 'Load Modules on Init Hook', 'all-in-one-wc' ),
				'type'    => 'checkbox',
				'desc'    => __( 'Choose if you want to load Modules on Init hook.', 'all-in-one-wc' ).' '.__( 'It will load the locale appropriately if users change it from the profile page.', 'all-in-one-wc' ),
				'id'      => 'aiow_load_modules_on_init',
				'default' => 'no',
			),
			array(
				'title'   => __( 'Use List Instead of Comma Separated Text for Products in Settings', 'all-in-one-wc' ),
				'type'    => 'checkbox',
				'desc'    => sprintf( __( 'Supported modules: %s.', 'all-in-one-wc' ), implode( ', ', array(
					__( 'Gateways per Product or Category', 'all-in-one-wc' ),
					__( 'Global Discount', 'all-in-one-wc' ),
					__( 'Product Info', 'all-in-one-wc' ),
					__( 'Product Input Fields', 'all-in-one-wc' ),
					__( 'Products XML', 'all-in-one-wc' ),
					__( 'Related Products', 'all-in-one-wc' ),
				) ) ),
				'id'      => 'aiow_list_for_products',
				'default' => 'yes',
			),
			array(
				'title'   => __( 'Use List Instead of Comma Separated Text for Products Categories in Settings', 'all-in-one-wc' ),
				'type'    => 'checkbox',
				'desc'    => sprintf( __( 'Supported modules: %s.', 'all-in-one-wc' ), implode( ', ', array(
					__( 'Product Info', 'all-in-one-wc' ),
				) ) ),
				'id'      => 'aiow_list_for_products_cats',
				'default' => 'yes',
			),
			array(
				'title'   => __( 'Use List Instead of Comma Separated Text for Products Tags in Settings', 'all-in-one-wc' ),
				'type'    => 'checkbox',
				'desc'    => sprintf( __( 'Supported modules: %s.', 'all-in-one-wc' ), implode( ', ', array(
					__( 'Product Info', 'all-in-one-wc' ),
				) ) ),
				'id'      => 'aiow_list_for_products_tags',
				'default' => 'yes',
			),
		);
	}

	/**
	 * Get settings array
	 *
	 * @return  array
	 */
	function get_settings( $current_section = '' ) {
		if ( ! $this->is_dashboard_section( $current_section ) ) {
			return apply_filters( 'aiow_settings_' . $current_section, array() );
		} elseif ( 'manager' === $current_section ) {
			return $this->get_manager_settings();
		} elseif ( isset( $this->custom_dashboard_modules[ $current_section ] ) ) {
			return $this->custom_dashboard_modules[ $current_section ]['settings'];
		} else {
			$cat_id = ( isset( $_GET['aiow-cat'] ) && '' != $_GET['aiow-cat'] ) ? $_GET['aiow-cat'] : 'dashboard';
			$settings[] = array(
				'title' => __( 'All In One For WooCommerce', 'all-in-one-wc' ) . ' - ' . $this->cats[ $cat_id ]['label'],
				'type'  => 'title',
				'desc'  => $this->cats[ $cat_id ]['desc'],
				'id'    => 'aiow_' . $cat_id . '_options',
			);
			if ( 'dashboard' === $cat_id ) {
				$settings = array_merge( $settings, $this->module_statuses );
			} else {
				$cat_module_statuses = array();
				foreach ( $this->module_statuses as $module_status ) {
					$section = $module_status['id'];
					$section = str_replace( 'aiow_', '', $section );
					$section = str_replace( '_enabled', '', $section );
					if ( $cat_id === $this->get_cat_by_section( $section ) ) {
						$cat_module_statuses[] = $module_status;
					}
				}
				$settings = array_merge( $settings, $cat_module_statuses );
			}
			$settings[] = array(
				'type'  => 'sectionend',
				'id'    => 'aiow_' . $cat_id . '_options',
				'title' => '', // for usort
			);
			return $settings;
		}
	}

	/**
	 * Add module statuses
	 *
	 * @param array $statuses Status
	 */
	function add_module_statuses( $statuses ) {
		$this->module_statuses = $statuses;
	}
}

endif;
