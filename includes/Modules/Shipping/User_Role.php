<?php
/**
 * Shipping Module - Shipping by User Role.
 *
 * @package WordPress
 * @subpackage WooCommerce
 */

namespace AIOW\Modules\Shipping;

if ( ! class_exists( 'User_Role' ) ) {

	/**
	 * Declare class `User_Role` extends to `Condition`.
	 */
	class User_Role extends Condition {

		/**
		 * Class Constructor.
		 */
		function __construct() {
			$this->id         = 'shipping_by_user_role';
			$this->short_desc = __( 'Shipping Methods by Users', 'all-in-one-wc' );
			$this->desc       = __( 'Set user roles, users or membership plans to include/exclude for shipping methods to show up (Free shipping available in Plus).', 'all-in-one-wc' );
			$this->desc_pro   = __( 'Set user roles, users or membership plans to include/exclude for shipping methods to show up.', 'all-in-one-wc' );
			$this->link_slug  = 'woocommerce-shipping-methods-by-users';

			$this->condition_options = array(
				'user_roles' => array(
					'title' => __( 'User Roles', 'all-in-one-wc' ),
					'desc'  => sprintf(
						__( 'Custom roles can be added via "Add/Manage Custom Roles" tool in <a href="%s">General</a> module.', 'all-in-one-wc' ),
						admin_url( 'admin.php?page=wc-settings&tab=jetpack&aiow-cat=emails_and_misc&section=general' )
					),
				),
				'user_id' => array(
					'title' => __( 'Users', 'all-in-one-wc' ),
					'desc'  => '',
				),
				'user_membership' => array(
					'title' => __( 'User Membership Plans', 'all-in-one-wc' ),
					'desc'  => sprintf(
						__( 'This section requires <a target="_blank" href="%s">WooCommerce Memberships</a> plugin.', 'all-in-one-wc' ),
						'https://woocommerce.com/products/woocommerce-memberships/'
					),
				),
			);

			parent::__construct();

		}

		/**
		 * Add multiple role option.
		 */
		public function add_multiple_roles_option() {
			return true;
		}

		/**
		 * Check condition.
		 *
		 * @param string $options_id Option ID.
		 * @param array  $user_roles_or_ids_or_membership_plans values.
		 * @param bool   $include_or_exclude Include OR exclude.
		 * @param array  $package Packages.
		 * @return bool
		 */
		function check( $options_id, $user_roles_or_ids_or_membership_plans, $include_or_exclude, $package ) {
			switch( $options_id ) {
				case 'user_roles':
					if ( empty( $this->customer_roles ) ) {
						$this->customer_roles = 'no' === ( $multi_role_check = aiow_option( 'aiow_' . $this->id . '_check_multiple_roles', 'no' ) ) ? array( aiow_get_current_user_first_role() ) : aiow_get_current_user_all_roles();
					}
					return count( array_intersect( $this->customer_roles, $user_roles_or_ids_or_membership_plans ) ) > 0;
				case 'user_id':
					if ( ! isset( $this->user_id ) ) {
						$this->user_id = get_current_user_id();
					}
					return in_array( $this->user_id, $user_roles_or_ids_or_membership_plans );
				case 'user_membership':
					if ( ! isset( $this->user_id ) ) {
						$this->user_id = get_current_user_id();
					}
					if ( ! function_exists( 'wc_memberships_is_user_active_member' ) ) {
						return false;
					}
					foreach ( $user_roles_or_ids_or_membership_plans as $membership_plan ) {
						if ( wc_memberships_is_user_active_member( $this->user_id, $membership_plan ) ) {
							return true;
						}
					}
					return false;
			}
		}

		/**
		 * Get condition option.
		 *
		 * @param string $options_id Option ID.
		 * @return string
		 */
		function get_condition_options( $options_id ) {
			switch( $options_id ) {
				case 'user_roles':
				return aiow_get_user_roles_options();
				case 'user_membership':
				$membership_plans = array();
				$block_size       = 512;
				$offset           = 0;
				while( true ) {
					$args = array(
						'post_type'      => 'wc_membership_plan',
						'post_status'    => 'any',
						'posts_per_page' => $block_size,
						'offset'         => $offset,
						'orderby'        => 'title',
						'order'          => 'ASC',
						'fields'         => 'ids',
					);
					$loop = new \WP_Query( $args );
					if ( ! $loop->have_posts() ) {
						break;
					}
					foreach ( $loop->posts as $post_id ) {
						$membership_plans[ $post_id ] = get_the_title( $post_id );
					}
					$offset += $block_size;
				}
				return $membership_plans;
			}
		}
	}
}
