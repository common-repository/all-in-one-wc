<?php
/**
 * Class for WooCommerce customization support.
 *
 * @package WordPress
 */
namespace AIOW;


// If check class exists.
if ( ! class_exists( 'Main' ) ) {

	/**
	 * Declare class and extends TO `AIOW_Modules`
	 */
	class Main extends \AIOW\Modules\Register_Modules {

		/**
		 * Define version.
		 *
		 * @var $version string
		 */
		public $version = '1.2';

		/**
		 * Store class object using instance method.
		 *
		 * @var $_instance object
		 */
		protected static $_instance = null;

		/**
		 * Store setting options.
		 *
		 * @var $options setting options
		 */
		public $options = array();

		/**
		 * Calling construct.
		 */
		public function __construct() {
			if ( ! function_exists( 'is_plugin_active' ) ) {
				include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
			}
			if ( is_plugin_active( 'booster-plus-for-woocommerce/booster-plus-for-woocommerce.php' ) ) {
				add_action( 'wcj_loaded', function() {
					require_once plugin_dir_path( __FILE__ ) . 'load.php';
				} );
			} else {
				require_once plugin_dir_path( __FILE__ ) . 'load.php';
			}
		}

		/**
		 * Store class object
		 */
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}
			return self::$_instance;
		}

		/**
		 * Load core modules.
		 */
		public function aiow_load_modules() {
			if ( is_admin() ) {
				foreach ( $this->modules as $module ) {
					// Modules statuses
					if ( '' == $module->parent_id ) { // i.e. not submodule
						$status_settings = $module->add_enable_module_setting( array() );
						$this->module_statuses[] = $status_settings[1];
					}
					if ( aiow_option( AIOW_VERSION_OPTION ) === $this->version ) {
						continue;
					}
					$values = $module->get_settings();
					// Adding options
					foreach ( $values as $value ) {
						if ( isset( $value['default'] ) && isset( $value['id'] ) ) {
							if ( 'yes' === aiow_option( 'aiow_autoload_options', 'yes' ) ) {
								$autoload = isset( $value['autoload'] ) ? (bool) $value['autoload'] : true;
							} else {
								$autoload = false;
							}
							add_option( $value['id'], $value['default'], '', $autoload );
						}
					}
				}
				if ( aiow_option( AIOW_VERSION_OPTION ) !== $this->version ) {
					// "Version updated" stuff...
					update_option( AIOW_VERSION_OPTION, $this->version );
					wp_schedule_single_event( time(), 'aiow_version_updated' );
				}
			}
		}
	}
}
