<?php
/**
 * All In One For WooCommerce - Core - Admin
 *
 * @package WordPress
 * @subpackage WooCommerce
 */

namespace AIOW\Admin;

if ( ! class_exists( 'Admin' ) ) :

class Admin {

	/**
	 * Constructor.
	 */
	function __construct() {
		if ( is_admin() ) {
			add_filter( 'aiow_message',                                           'aiow_get_plus_message', 100, 3 );

			if ( apply_filters( 'aiow_can_create_admin_interface', true ) ) {
				add_filter( 'woocommerce_get_settings_pages',                            array( $this, 'add_aiow_settings_tab' ), 1 );
				add_filter( 'plugin_action_links_' . plugin_basename( AIOW_PLUGIN_FILE ), array( $this, 'action_links' ) );
				add_action( 'admin_menu',                                                array( $this, 'aiow_menu' ), 100 );
				add_filter( 'admin_footer_text',                                         array( $this, 'admin_footer_text' ), 2 );
			}
		}
		add_filter( 'aiow_option',  array( $this, 'aiow_get_option' ),  101, 2 );
		add_action( 'admin_enqueue_scripts', array( $this, 'aiow_admin_script' ) );
	}

	/**
	 * Add admin script.
	 */
	function aiow_admin_script() {
		if ( isset( $_GET['tab'] ) && 'all_in_one_wc' === $_GET['tab'] ) {
			wp_enqueue_style( 'aiow-setting', plugin_dir_url( __FILE__ ) . '../assets/css/setting-page.css' );
		}
	}

	/**
	 * Get options.
	 *
	 * @param string $value1 Option value.
	 * @param string $value2 Option value.
	 * @return string
	 */
	function aiow_get_option( $value1, $value2 ) {
		return $value2;
	}

	/**
	 * Display footer text.
	 *
	 * @param string $footer_text Footer text.
	 * @return string
	 */
	function admin_footer_text( $footer_text ) {
		if ( isset( $_GET['page'] ) ) {
			if ( 'aiow-tools' === $_GET['page'] || ( 'wc-settings' === $_GET['page'] && isset( $_GET['tab'] ) && 'jetpack' === $_GET['tab'] ) ) {
				$rocket_icons = aiow_get_5_rocket_image();
				$rating_link = '<a href="https://wordpress.org/support/plugin/all-in-one-wc/reviews/?rate=5#new-post" target="_blank">' . $rocket_icons . '</a>';
				return sprintf(
					__( 'If you like <strong>All In One For WooCommerce</strong> please leave us a %s rating. Thank you, we couldn\'t have done it without you!', 'all-in-one-wc' ),
					$rating_link
				);
			}
		}
		return $footer_text;
	}

	/**
	 * Add menu item
	 */
	function aiow_menu() {
		add_submenu_page(
			'woocommerce',
			__( 'All In One For WooCommerce', 'all-in-one-wc' ),
			__( 'All In One For WooCommerce', 'all-in-one-wc' ) ,
			( 'yes' === aiow_option( 'aiow_' . 'admin_tools' . '_enabled', 'no' ) && 'yes' === aiow_option( 'aiow_admin_tools_show_menus_to_admin_only', 'no' ) ? 'manage_options' : 'manage_woocommerce' ),
			'admin.php?page=wc-settings&tab=all_in_one_wc'
		);
	}

	/**
	 * Show action links on the plugin screen
	 *
	 * @param   mixed $links
	 * @return  array
	 */
	function action_links( $links ) {
		$aiow_custom_links = array(
			'<a href="' . admin_url( 'admin.php?page=wc-settings&tab=all_in_one_wc' ) . '">' . __( 'Settings', 'woocommerce' ) . '</a>'
		);
		return array_merge( $aiow_custom_links, $links );
	}

	/**
	 * Add Jetpack settings tab to WooCommerce settings.
	 *
	 * @param array $settings Settings.
	 * @return array
	 */
	function add_aiow_settings_tab( $settings ) {
		$_settings = new Settings\Settings_Panel();
		$_settings->add_module_statuses( AIOW()->module_statuses );
		$settings[] = $_settings;
		return $settings;
	}

}

endif;
