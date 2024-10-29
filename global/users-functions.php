<?php
/**
 * Users - Functions
 *
 * @package WordPress
 * @subpackage WooCommerce
 */

if ( ! function_exists( 'aiow_current_user_can' ) ) {
	/**
	 * Get current user.
	 *
	 * @param string $capability User Cap.
	 * @return bool
	 */
	function aiow_current_user_can( $capability ) {
		if ( ! function_exists( 'wp_get_current_user' ) ) {
			require_once( ABSPATH . 'wp-includes/pluggable.php' );
		}
		return current_user_can( $capability );
	}
}

if ( ! function_exists( 'aiow_get_current_user_id' ) ) {
	/**
	 * Get current user ID.
	 *
	 * @return int
	 */
	function aiow_get_current_user_id() {
		if ( ! function_exists( 'get_current_user_id' ) ) {
			require_once( ABSPATH . 'wp-includes/pluggable.php' );
		}
		return get_current_user_id();
	}
}

if ( ! function_exists( 'aiow_get_users_as_options' ) ) {
	/**
	 * Get user options..
	 *
	 * @return array
	 */
	function aiow_get_users_as_options() {
		$users = array();
		foreach ( get_users( 'orderby=display_name' ) as $user ) {
			$users[ $user->ID ] = $user->display_name . ' ' . '[ID:' . $user->ID . ']';
		}
		return $users;
	}
}

if ( ! function_exists( 'is_shop_manager' ) ) {
	/**
	 * Check is shop manager.
	 *
	 * @param int $user_id User ID.
	 * @return bool
	 */
	function is_shop_manager( $user_id = 0 ) {
		$the_user = ( 0 == $user_id ) ? wp_get_current_user() : get_user_by( 'id', $user_id );
		return ( isset( $the_user->roles[0] ) && 'shop_manager' === $the_user->roles[0] );
	}
}

if ( ! function_exists( 'aiow_get_current_user_all_roles' ) ) {
	/**
	 * Get current user all roles.
	 *
	 * @return array
	 */
	function aiow_get_current_user_all_roles() {
		if ( ! function_exists( 'wp_get_current_user' ) ) {
			require_once( ABSPATH . 'wp-includes/pluggable.php' );
		}
		$current_user = wp_get_current_user();
		return ( ! empty( $current_user->roles ) ) ? $current_user->roles : array( 'guest' );
	}
}

if ( ! function_exists( 'aiow_is_user_logged_in' ) ) {
	/**
	 * Check is user logged OR not.
	 *
	 * @return bool
	 */
	function aiow_is_user_logged_in() {
		if ( ! function_exists( 'is_user_logged_in' ) ) {
			require_once( ABSPATH . 'wp-includes/pluggable.php' );
		}
		return is_user_logged_in();
	}
}

if ( ! function_exists( 'aiow_is_role_changer_enabled' ) ) {
	/**
	 * Check role changer enabled OR not.
	 *
	 * @return bool
	 */
	function aiow_is_role_changer_enabled() {
		return (
			'yes' === apply_filters( 'aiow_option', 'no', aiow_get_option( 'aiow_general_user_role_changer_enabled', 'no' ) ) &&
			aiow_is_user_logged_in() &&
			aiow_is_user_role( aiow_get_option( 'aiow_general_user_role_changer_enabled_for', array( 'administrator', 'shop_manager' ) ) )
		);
	}
}

if ( ! function_exists( 'aiow_get_current_user_first_role' ) ) {
	/**
	 * Get current first role.
	 *
	 * @return string
	 */
	function aiow_get_current_user_first_role() {
		if ( aiow_is_module_enabled( 'general' ) && aiow_is_role_changer_enabled() ) {
			$current_user_id = get_current_user_id();
			if ( '' != ( $role_by_meta = get_user_meta( $current_user_id, '_' . 'aiow_aiow_user_role', true ) ) ) {
				return $role_by_meta;
			}
		}
		$current_user = wp_get_current_user();
		$first_role   = ( isset( $current_user->roles ) && is_array( $current_user->roles ) && ! empty( $current_user->roles ) ? reset( $current_user->roles ) : 'guest' );
		return ( '' != $first_role ? $first_role : 'guest' );
	}
}

if ( ! function_exists( 'aiow_get_user_roles' ) ) {
	/**
	 * Get user role.
	 *
	 * @param array $args Argument.
	 * @return array
	 */
	function aiow_get_user_roles( $args = null ) {
		global $wp_roles;
		$args = wp_parse_args( $args, array(
			'skip_editable_roles_filter' => false
		) );
		$all_roles = ( isset( $wp_roles ) && is_object( $wp_roles ) ) ? $wp_roles->roles : array();
		$current_user_roles = array();
		if ( is_user_logged_in() ) {
			$user               = wp_get_current_user();
			$roles              = ( array ) $user->roles;
			$current_user_roles = array_filter( $all_roles, function ( $k ) use ( $roles ) {
				return in_array( $k, $roles );
			}, ARRAY_FILTER_USE_KEY );
		}
		if ( ! $args['skip_editable_roles_filter'] ) {
			$all_roles = apply_filters( 'editable_roles', $all_roles );
		}
		$all_roles = array_merge( array(
			'guest' => array(
				'name'         => __( 'Guest', 'all-in-one-wc' ),
				'capabilities' => array(),
			) ), $all_roles );
		if ( ! empty( $current_user_roles ) ) {
			$all_roles = array_merge( $current_user_roles, $all_roles );
		}
		return $all_roles;
	}
}

if ( ! function_exists( 'aiow_get_user_roles_options' ) ) {
	/**
	 * Get user role options.
	 *
	 * @param array $args Argument.
	 * @return array
	 */
	function aiow_get_user_roles_options( $args = null ) {
		global $wp_roles;
		$args = wp_parse_args( $args, array(
			'skip_editable_roles_filter' => false
		) );
		$all_roles = ( isset( $wp_roles ) && is_object( $wp_roles ) ) ? $wp_roles->roles : array();
		if ( ! $args['skip_editable_roles_filter'] ) {
			$all_roles = apply_filters( 'editable_roles', $all_roles );
		}
		$all_roles = array_merge( array(
			'guest' => array(
				'name'         => __( 'Guest', 'all-in-one-wc' ),
				'capabilities' => array(),
			) ), $all_roles );
		$all_roles_options = array();
		foreach ( $all_roles as $_role_key => $_role ) {
			$all_roles_options[ $_role_key ] = $_role['name'];
		}
		return $all_roles_options;
	}
}

if ( ! function_exists( 'aiow_is_user_role' ) ) {
	/**
	 * Check user role exists.
	 *
	 * @param array $user_role User roles.
	 * @param int   $user_id User ID.
	 * @return  bool
	 */
	function aiow_is_user_role( $user_role, $user_id = 0 ) {
		if ( ! function_exists( 'wp_get_current_user' ) ) {
			include( ABSPATH . 'wp-includes/pluggable.php' );
		}
		$_user = ( 0 == $user_id ? wp_get_current_user() : get_user_by( 'id', $user_id ) );
		if ( ! isset( $_user->roles ) || empty( $_user->roles ) ) {
			$_user->roles = array( 'guest' );
		}
		if ( ! is_array( $_user->roles ) ) {
			return false;
		}
		if ( is_array( $user_role ) ) {
			if ( in_array( 'administrator', $user_role ) ) {
				$user_role[] = 'super_admin';
			}
			$_intersect = array_intersect( $user_role, $_user->roles );
			return ( ! empty( $_intersect ) );
		} else {
			if ( 'administrator' == $user_role ) {
				return ( in_array( 'administrator', $_user->roles ) || in_array( 'super_admin', $_user->roles ) );
			} else {
				return ( in_array( $user_role, $_user->roles ) );
			}
		}
	}
}
