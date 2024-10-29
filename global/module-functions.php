<?php
/**
 * All In One For WooCommerce - Core functions
 *
 * @package WordPress
 * @subpackage WooCommerce
 */


if ( ! function_exists( 'aiow_is_rest' ) ) {
	/**
	 * Checks if the current request is a WP REST API request.
	 */
	function aiow_is_rest() {
		$prefix = rest_get_url_prefix();
		if (
			defined( 'REST_REQUEST' ) && REST_REQUEST || // After WP_REST_Request initialisation
			isset( $_GET['rest_route'] ) && 0 === strpos( trim( $_GET['rest_route'], '\\/' ), $prefix , 0 ) // Support "plain" permalink settings
		) {
			return true;
		}
		// URL Path begins with wp-json/ (your REST prefix)
		// Also supports WP installations in subfolders
		$rest_url    = wp_parse_url( site_url( $prefix ) );
		$current_url = wp_parse_url( add_query_arg( array() ) );
		return ( 0 === strpos( $current_url['path'], $rest_url['path'], 0 ) );
	}
}

if ( ! function_exists( 'aiow_check_modules_by_user_roles' ) ) {
	/**
	 * Check modules by user roles.
	 *
	 * @param string $module_id Module ID.
	 * @return bool
	 */
	function aiow_check_modules_by_user_roles( $module_id ) {
		global $aiow_modules_by_user_roles_data;
		if ( ! isset( $aiow_modules_by_user_roles_data ) ) {
			if ( ! function_exists( 'wp_get_current_user' ) ) {
				require_once( ABSPATH . 'wp-includes/pluggable.php' );
			}
			$current_user = wp_get_current_user();
			$aiow_modules_by_user_roles_data['role'] = ( isset( $current_user->roles ) && is_array( $current_user->roles ) && ! empty( $current_user->roles ) ?
				reset( $current_user->roles ) : 'guest' );
			$aiow_modules_by_user_roles_data['role'] = ( '' != $aiow_modules_by_user_roles_data['role'] ? $aiow_modules_by_user_roles_data['role'] : 'guest' );
			$aiow_modules_by_user_roles_data['modules_incl'] = aiow_get_option( 'aiow_modules_by_user_roles_incl_' . $aiow_modules_by_user_roles_data['role'], '' );
			$aiow_modules_by_user_roles_data['modules_excl'] = aiow_get_option( 'aiow_modules_by_user_roles_excl_' . $aiow_modules_by_user_roles_data['role'], '' );
		}
		return (
			( ! empty( $aiow_modules_by_user_roles_data['modules_incl'] ) && ! in_array( $module_id, $aiow_modules_by_user_roles_data['modules_incl'] ) ) ||
			( ! empty( $aiow_modules_by_user_roles_data['modules_excl'] ) &&   in_array( $module_id, $aiow_modules_by_user_roles_data['modules_excl'] ) )
		) ? false : true;
	}
}

if ( ! function_exists( 'aiow_is_module_enabled' ) ) {
	/**
	 * Check is module enabled.
	 *
	 * @param string|int $module_id Module ID.
	 * @return bool
	 */
	function aiow_is_module_enabled( $module_id ) {
		return ( 'modules_by_user_roles' != $module_id && aiow_is_module_enabled( 'modules_by_user_roles' ) && ! aiow_is_rest() && ! aiow_check_modules_by_user_roles( $module_id ) ?
			false : ( 'yes' === aiow_option( 'aiow_' . $module_id . '_enabled', 'no' ) ) );
	}
}

if ( ! function_exists( 'aiow_plugin_url' ) ) {
	/**
	 * Plugin URL.
	 */
	function aiow_plugin_url() {
		return untrailingslashit( plugin_dir_url( realpath( dirname( __FILE__ ) ) ) );
	}
}

if ( ! function_exists( 'aiow_plugin_path' ) ) {
	/**
	 * Get the plugin path.
	 */
	function aiow_plugin_path() {
		return untrailingslashit( realpath( plugin_dir_path( __FILE__ ) . '/..' ) );
	}
}
