<?php
/**
 * All In One For Woocommerce - Register Modules
 *
 * @package WordPress
 */
namespace AIOW\Modules;

// If check class exists OR not.
if ( ! class_exists( 'Register_Modules' ) ) {

	/**
	 * Declare class
	 */
	class Register_Modules {

		/**
		 * Declare ID variable.
		 *
		 * @var string $id
		 */
		public $id;

		/**
		 * Declare description variable.
		 *
		 * @var string $short_desc
		 */
		public $short_desc;

		/**
		 * Declare description variable.
		 *
		 * @var string $desc
		 */
		public $desc;

		/**
		 * Declare pro version description variable.
		 *
		 * @var string $desc_pro
		 */
		public $desc_pro;

		/**
		 * Declare extra description variable.
		 *
		 * @var string $extra_desc
		 */
		public $extra_desc;

		/**
		 * Declare extra pro version description variable.
		 *
		 * @var string $extra_desc_pro
		 */
		public $extra_desc_pro;

		/**
		 * Declare parent ID variable.
		 *
		 * @var string $parent_id
		 */
		public $parent_id;

		/**
		 * Declare Type variable.
		 *
		 * @var string $type
		 */
		public $type;

		/**
		 * Declare Link variable.
		 *
		 * @var string $link
		 */
		public $link;

		/**
		 * Declare Options variable.
		 *
		 * @var string $options
		 */
		public $options = array();

		/**
		 * Calling class construct.
		 */
		public function __construct( $type = 'module' ) {
			$this->__init_modules( $type );
		}

		/**
		 * Init modules.
		 */
		public function __init_modules( $type = 'module' ) {
			add_filter( 'aiow_settings_sections',     array( $this, 'settings_section' ) );
			add_filter( 'aiow_settings_' . $this->id, array( $this, 'get_settings' ), 100 );
			$this->type = $type;
			if ( 'module' === $this->type ) {
				$this->parent_id = '';
			}

			if ( 'no' === aiow_option( 'aiow_load_modules_on_init', 'no' ) ) {
				add_action( 'init', array( $this, 'add_settings' ) );
				add_action( 'init', array( $this, 'reset_settings' ), PHP_INT_MAX );
			} else {
				if ( 'init' === current_filter() ) {
					$this->add_settings();
					$this->reset_settings();
				}
			}
			
			// Handle WPML hooks
			if ( $this->is_enabled() ) {
				add_action( 'aiow_before_get_terms', array( $this, 'remove_wpml_functions_before_get_terms' ) );
				add_action( 'aiow_after_get_terms', array( $this, 'restore_wpml_functions_after_get_terms' ) );
				add_action( 'aiow_before_get_products', array( $this, 'add_wpml_args_on_get_products' ) );
				add_action( 'aiow_after_get_products', array( $this, 'restore_wpml_args_on_get_products' ) );
				add_action( 'admin_init', array( $this, 'remove_wpml_hooks' ) );
			}

			// Handle Price Functions
			add_filter( 'wc_price', array( $this, 'handle_price' ), 10, 4 );
		}

		/**
		 * handle_price.
		 *
		 * @param $return
		 * @param $price
		 * @param $args
		 * @param $unformatted_price
		 *
		 * @return mixed
		 */
		function handle_price( $return, $price, $args, $unformatted_price ) {
			if ( isset( $args['add_html_on_price'] ) && ! filter_var( $args['add_html_on_price'], FILTER_VALIDATE_BOOLEAN ) ) {
				$return = $price;
			}
			return $return;
		}

		/**
		 * remove_wpml_hooks.
		 */
		function remove_wpml_hooks() {
			if ( 'no' === aiow_option( 'aiow_' . $this->id . '_wpml_get_products_all_lang', 'no' ) ) {
				return;
			}

			// Remove a WPML filter that filters products by language
			aiow_remove_class_filter( 'woocommerce_json_search_found_products', 'WCML_Products', 'filter_wc_searched_products_on_admin' );
		}

		/**
		 * restore_wpml_args_on_get_products.
		 *
		 * @param null $module_id
		 */
		function restore_wpml_args_on_get_products( $module_id = null ){
			if ( 'no' === aiow_option( 'aiow_' . $this->id . '_wpml_get_products_all_lang', 'no' ) ) {
				return;
			}
			remove_action( 'pre_get_posts', array( $this, 'suppress_filters' ) );
		}

		/**
		 * add_wpml_args_on_get_products.
		 *
		 * It's necessary to take 2 steps:
		 * 1. Add `do_action('aiow_before_get_products', $this->id )` before get_product, aiow_get_products, or wp_query.
		 * 2. Add a setting using `$this->get_wpml_products_in_all_languages_setting()`
		 *
		 * @param null $module_id
		 */
		function add_wpml_args_on_get_products( $module_id = null ) {
			if ( 'no' === aiow_option( 'aiow_' . $this->id . '_wpml_get_products_all_lang', 'no' ) ) {
				return;
			}
			add_action( 'pre_get_posts', array( $this, 'suppress_filters' ) );
		}

		/**
		 * Query filter.
		 *
		 * @param object $query WP_Query
		 */
		function suppress_filters( $query ) {
			$query->query_vars['suppress_filters'] = true;
		}

		/**
		 * Get all WPML product language setting.
		 *
		 * @return array
		 */
		function get_wpml_terms_in_all_languages_setting() {
			return array(
				'title'    => __( 'WPML: Get Terms in All Languages', 'all-in-one-wc' ),
				'desc'     => __( 'Enable', 'all-in-one-wc' ),
				'desc_tip' => __( 'Get tags and taxonomies in all languages', 'all-in-one-wc' ),
				'id'       => 'aiow_' . $this->id . '_wpml_get_terms_all_lang',
				'default'  => 'no',
				'type'     => 'checkbox',
			);
		}

		/**
		 * Get all WPML product language setting.
		 *
		 * @return array
		 */
		function get_wpml_products_in_all_languages_setting() {
			return array(
				'title'    => __( 'WPML: Get Products in All Languages', 'all-in-one-wc' ),
				'desc'     => __( 'Enable', 'all-in-one-wc' ),
				'desc_tip' => __( 'Get products in all languages', 'all-in-one-wc' ),
				'id'       => 'aiow_' . $this->id . '_wpml_get_products_all_lang',
				'default'  => 'no',
				'type'     => 'checkbox',
			);
		}

		/**
		 * Restore WPML function before get terms.
		 *
		 * @param string $module_id Module ID.
		 * @return string
		 */
		function remove_wpml_functions_before_get_terms( $module_id = null ) {
			if ( 'no' === aiow_option( 'aiow_' . $this->id . '_wpml_get_terms_all_lang', 'no' ) ) {
				return;
			}
			aiow_remove_wpml_terms_filters();
		}

		/**
		 * Restore WPML function after get terms.
		 *
		 * @param string $module_id Module ID.
		 * @return string
		 */
		function restore_wpml_functions_after_get_terms( $module_id = null ) {
			if ( 'no' === aiow_option( 'aiow_' . $this->id . '_wpml_get_terms_all_lang', 'no' ) ) {
				return;
			}
			aiow_add_wpml_terms_filters();
		}

		/**
		 * Get deprecated options.
		 *
		 * @return bool
		 */
		function get_deprecated_options() {
			return false;
		}

		/**
		 * Handle deprecated options.
		 */
		function handle_deprecated_options() {
			if ( $deprecated_options = $this->get_deprecated_options() ) {
				foreach ( $deprecated_options as $new_option => $old_options ) {
					$new_value = aiow_option( $new_option, array() );
					foreach ( $old_options as $new_key => $old_option ) {
						if ( null !== ( $old_value = aiow_option( $old_option, null ) ) ) {
							$new_value[ $new_key ] = $old_value;
							delete_option( $old_option );
						}
					}
					update_option( $new_option, $new_value );
				}
			}
		}

		/**
		 * Save metabox.
		 *
		 * @param mixed  $option_value Setting value.
		 * @param string $option_name Option name.
		 * @param string $module_id Module ID.
		 * @return string
		 */
		function save_meta_box_validate_value( $option_value, $option_name, $module_id ) {
			if ( true === apply_filters( 'aiow_option', false, true ) ) {
				return $option_value;
			}
			if ( 'no' === $option_value ) {
				return $option_value;
			}
			if ( $this->id === $module_id && $this->meta_box_validate_value === $option_name ) {
				$args = array(
					'post_type'      => 'product',
					'post_status'    => 'any',
					'posts_per_page' => 1,
					'meta_key'       => '_' . $this->meta_box_validate_value,
					'meta_value'     => 'yes',
					'post__not_in'   => array( get_the_ID() ),
				);
				$loop = new WP_Query( $args );
				$c = $loop->found_posts + 1;
				if ( $c >= 2 ) {
					add_filter( 'redirect_post_location', array( $this, 'validate_value_add_notice_query_var' ), 99 );
					return 'no';
				}
			}
			return $option_value;
		}

		/**
		 * Validate notice.
		 *
		 * @param string $location Location URL.
		 * @return string
		 */
		function validate_value_add_notice_query_var( $location ) {
			remove_filter( 'redirect_post_location', array( $this, 'validate_value_add_notice_query_var' ), 99 );
			return add_query_arg( array( 'aiow_' . $this->id . '_meta_box_admin_notice' => true ), $location );
		}

		/**
		 * Get metabox options.
		 */
		function get_meta_box_options() {
			$filename = aiow_plugin_path() . '/includes/settings/meta-box/aiow-settings-meta-box-' . str_replace( '_', '-', $this->id ) . '.php';
			return ( file_exists ( $filename ) ? require( $filename ) : array() );
		}

		/**
		 * Maybe fix settings.
		 *
		 * @param array $settings Setting.
		 * @return array
		 */
		function maybe_fix_settings( $settings ) {
			if ( ! AIOW_IS_WC_VERSION_BELOW_3_2_0 ) {
				foreach ( $settings as &$setting ) {
					if ( isset( $setting['type'] ) && 'select' === $setting['type'] ) {
						if (
							! isset( $setting['ignore_enhanced_select_class'] ) ||
							( isset( $setting['ignore_enhanced_select_class'] ) && false === $setting['ignore_enhanced_select_class'] )
						) {
							if ( ! isset( $setting['class'] ) || '' === $setting['class'] ) {
								$setting['class'] = 'wc-enhanced-select';
							} else {
								$setting['class'] .= ' ' . 'wc-enhanced-select';
							}
						}
					}
					if ( isset( $setting['type'] ) && 'text' === $setting['type'] && isset( $setting['class'] ) && 'widefat' === $setting['class'] ) {
						if ( ! isset( $setting['css'] ) || '' === $setting['css'] ) {
							$setting['css'] = 'width:100%;';
						} else {
							$setting['css'] .= ' ' . 'width:100%;';
						}
					}
				}
			}
			return $settings;
		}

		/**
		 * Add setting from file.
		 *
		 * @param array $settings Settings.
		 * @return array
		 */
		function add_settings_from_file( $settings ) {
			$filename = str_replace( '_', '-', $this->id );
			if ( isset( $_GET['aiow-cat'] ) && 'shipping_and_orders' === $_GET['aiow-cat'] ) {
				$filename = 'shipping/' . $filename;
			}
			$filename = aiow_plugin_path() . '/includes/Modules/custom-fields/' . $filename . '.php';
			$settings = ( file_exists ( $filename ) ? require( $filename ) : $settings );
			return $this->maybe_fix_settings( $settings );
		}

		/**
		 * Add setting panel.
		 */
		function add_settings() {
			add_filter( 'aiow_' . $this->id . '_settings', array( $this, 'add_settings_from_file' ) );
		}

		/**
		 * Save meta box.
		 *
		 * @param mixed  $option_value option value.
		 * @param string $option_name Option name.
		 * @param mixed  $module_id Module ID.
		 * @return string
		 */
		function save_meta_box_value( $option_value, $option_name, $module_id ) {
			if ( true === apply_filters( 'aiow_option', false, true ) ) {
				return $option_value;
			}
			if ( 'no' === $option_value ) {
				return $option_value;
			}
			if ( $this->id === $module_id && $this->co === $option_name ) {
				$args = array(
					'post_type'      => 'product',
					'post_status'    => 'any',
					'posts_per_page' => 3,
					'meta_key'       => '_' . $this->co,
					'meta_value'     => 'yes',
					'post__not_in'   => array( get_the_ID() ),
				);
				$loop = new WP_Query( $args );
				$c = $loop->found_posts + 1;
				if ( $c >= 4 ) {
					add_filter( 'redirect_post_location', array( $this, 'add_notice_query_var' ), 99 );
					return 'no';
				}
			}
			return $option_value;
		}

		/**
		 * Add notice query var.
		 *
		 * @param string $location Url location.
		 * @return string
		 */
		function add_notice_query_var( $location ) {
			remove_filter( 'redirect_post_location', array( $this, 'add_notice_query_var' ), 99 );
			return add_query_arg( array( 'aiow_' . $this->id . '_admin_notice' => true ), $location );
		}

		/**
		 * Display admin notice.
		 */
		function admin_notices() {
			if ( ! isset( $_GET[ 'aiow_' . $this->id . '_admin_notice' ] ) ) {
				return;
			}
			echo '<div class="error"><p><div class="message">' . $this->get_the_notice() . '</div></p></div>';
		}

		/**
		 * Reset setting.
		 */
		function reset_settings() {
			if ( isset( $_GET['aiow_reset_settings'] ) && $this->id === $_GET['aiow_reset_settings'] && aiow_is_user_role( 'administrator' ) && ! isset( $_POST['save'] ) ) {
				foreach ( $this->get_settings() as $settings ) {
					if ( false !== strpos( $settings['id'], '[' ) ) {
						$id = explode( '[', $settings['id'] );
						$id = $id[0];
						delete_option( $id );
					} else {
						$default_value = isset( $settings['default'] ) ? $settings['default'] : '';
						update_option( $settings['id'], $default_value );
					}
				}
				wp_safe_redirect( remove_query_arg( 'aiow_reset_settings' ) );
				exit();
			}
		}

		/**
		 * Add standard settings.
		 *
		 * @param array  $settings Settings.
		 * @param string $module_desc Module desc.
		 * @return array
		 */
		function add_standard_settings( $settings = array(), $module_desc = '' ) {
			if ( isset( $this->tools_array ) && ! empty( $this->tools_array ) ) {
				//$settings = $this->add_tools_list( $settings );
			}
			$settings = $this->add_reset_settings_button( $settings );
			$settings = $this->setup_default_autoload( $settings );
			return $this->add_enable_module_setting( $settings, $module_desc );
		}

		/**
		 * Setup default autoload.
		 *
		 * @param array $settings Setting.
		 *
		 * @return array
		 */
		function setup_default_autoload( $settings ) {
			$settings = array_map( function ( $item ) {
				if ( ! isset( $item['autoload'] ) ) {
					$item['autoload'] = false;
				}
				return $item;
			}, $settings );
			return $settings;
		}

		/**
		 * Get settings.
		 *
		 * @return array
		 */
		function get_settings() {
			return $this->add_standard_settings( apply_filters( 'aiow_' . $this->id . '_settings', array() ) );
		}

		/**
		 * Save metabox field.
		 *
		 * @param int    $post_id Post ID.
		 * @param object $__post Postdata.
		 * @return mixed
		 */
		function save_meta_box( $post_id, $__post ) {
			// Check that we are saving with current metabox displayed.
			if ( ! isset( $_POST[ 'wooaiow_' . $this->id . '_save_post' ] ) ) {
				return;
			}
			// Setup post (just in case...)
			global $post;
			$post = get_post( $post_id );
			setup_postdata( $post );
			// Save options
			foreach ( $this->get_meta_box_options() as $option ) {
				if ( 'title' === $option['type'] ) {
					continue;
				}
				$is_enabled = ( isset( $option['enabled'] ) && 'no' === $option['enabled'] ) ? false : true;
				if ( $is_enabled ) {
					$option_value  = ( isset( $_POST[ $option['name'] ] ) ) ? $_POST[ $option['name'] ] : ( isset( $option['default'] ) ? $option['default'] : '' );
					$the_post_id   = ( isset( $option['product_id'] )     ) ? $option['product_id']     : $post_id;
					$the_meta_name = ( isset( $option['meta_name'] ) )      ? $option['meta_name']      : '_' . $option['name'];
					if ( isset( $option['convert'] ) && 'from_date_to_timestamp' === $option['convert'] ) {
						$option_value = strtotime( $option_value );
						if ( empty( $option_value ) ) {
							continue;
						}
					}
					delete_post_meta( $the_post_id, $the_meta_name ); // solves lowercase/uppercase issue
					update_post_meta( $the_post_id, $the_meta_name, apply_filters( 'aiow_save_meta_box_value', $option_value, $option['name'], $this->id ) );
				}
			}
			// Reset post
			wp_reset_postdata();
		}

		/**
		 * Add meta box.
		 */
		function add_meta_box() {
			$screen   = ( isset( $this->meta_box_screen ) )   ? $this->meta_box_screen   : 'product';
			$context  = ( isset( $this->meta_box_context ) )  ? $this->meta_box_context  : 'normal';
			$priority = ( isset( $this->meta_box_priority ) ) ? $this->meta_box_priority : 'high';
			add_meta_box(
				'wc-jetpack-' . $this->id,
				$this->short_desc,
				array( $this, 'create_meta_box' ),
				$screen,
				$context,
				$priority
			);
		}

		/**
		 * Create meta box
		 */
		function create_meta_box() {
			$current_post_id = get_the_ID();
			$html = '';
			$html .= '<table class="widefat striped">';
			foreach ( $this->get_meta_box_options() as $option ) {
				$is_enabled = ( isset( $option['enabled'] ) && 'no' === $option['enabled'] ) ? false : true;
				if ( is_array( $option ) && $is_enabled ) {
					if ( 'title' === $option['type'] ) {
						$html .= '<tr>';
						$html .= '<th colspan="3" style="' . ( isset( $option['css'] ) ? $option['css'] : 'text-align:left;font-weight:bold;' ) . '">' . $option['title'] . '</th>';
						$html .= '</tr>';
					} else {
						$custom_attributes = '';
						$the_post_id   = ( isset( $option['product_id'] ) ) ? $option['product_id'] : $current_post_id;
						$the_meta_name = ( isset( $option['meta_name'] ) )  ? $option['meta_name']  : '_' . $option['name'];
						if ( get_post_meta( $the_post_id, $the_meta_name ) ) {
							$option_value = get_post_meta( $the_post_id, $the_meta_name, true );
						} else {
							$option_value = ( isset( $option['default'] ) ) ? $option['default'] : '';
						}
						$css          = ( isset( $option['css'] )   ? $option['css']   : '' );
						$class        = ( isset( $option['class'] ) ? $option['class'] : '' );
						$show_value   = ( isset( $option['show_value'] ) && $option['show_value'] );
						$input_ending = '';
						if ( 'select' === $option['type'] ) {
							if ( isset( $option['multiple'] ) ) {
								$custom_attributes = ' multiple';
								$option_name       = $option['name'] . '[]';
							} else {
								$option_name       = $option['name'];
							}
							if ( isset( $option['custom_attributes'] ) ) {
								$custom_attributes .= ' ' . $option['custom_attributes'];
							}
							$options = '';
							foreach ( $option['options'] as $select_option_key => $select_option_value ) {
								$selected = '';
								if ( is_array( $option_value ) ) {
									foreach ( $option_value as $single_option_value ) {
										if ( '' != ( $selected = selected( $single_option_value, $select_option_key, false ) ) ) {
											break;
										}
									}
								} else {
									$selected = selected( $option_value, $select_option_key, false );
								}
								$options .= '<option value="' . $select_option_key . '" ' . $selected . '>' . $select_option_value . '</option>';
							}
						} elseif ( 'textarea' === $option['type'] ) {
							if ( '' === $css ) {
								$css = 'min-width:300px;';
							}
						} else {
							$input_ending = ' id="' . $option['name'] . '" name="' . $option['name'] . '" value="' . $option_value . '">';
							if ( isset( $option['custom_attributes'] ) ) {
								$input_ending = ' ' . $option['custom_attributes'] . $input_ending;
							}
							if ( isset( $option['placeholder'] ) ) {
								$input_ending = ' placeholder="' . $option['placeholder'] . '"' . $input_ending;
							}
						}
						switch ( $option['type'] ) {
							case 'price':
								$field_html = '<input style="' . $css . '" class="short wc_input_price" type="number" step="' .
									apply_filters( 'aiow_get_meta_box_options_type_price_step', '0.0001' ) . '"' . $input_ending;
								break;
							case 'date':
								$field_html = '<input style="' . $css . '" class="input-text" display="date" type="text"' . $input_ending;
								break;
							case 'textarea':
								$field_html = '<textarea style="' . $css . '" id="' . $option['name'] . '" name="' . $option['name'] . '">' . $option_value . '</textarea>';
								break;
							case 'select':
								$field_html = '<select' . $custom_attributes . ' class="' . $class . '" style="' . $css . '" id="' . $option['name'] . '" name="' .
									$option_name . '">' . $options . '</select>' .
									( $show_value && ! empty( $option_value ) ? sprintf( '<em>' . __( 'Selected: %s.', 'all-in-one-wc' ), implode( ', ', $option_value ) ) . '</em>' : '' );
								break;
							default:
								$field_html = '<input style="' . $css . '" class="short" type="' . $option['type'] . '"' . $input_ending;
								break;
						}
						$html .= '<tr>';
						$maybe_tooltip = ( isset( $option['tooltip'] ) && '' != $option['tooltip'] ) ? '<span style="float:right;">' . wc_help_tip( $option['tooltip'], true ) . '</span>' : '';
						$html .= '<th style="text-align:left;width:25%;font-weight:bold;">' . $option['title'] . $maybe_tooltip . '</th>';
						if ( isset( $option['desc'] ) && '' != $option['desc'] ) {
							$html .= '<td style="font-style:italic;width:25%;">' . $option['desc'] . '</td>';
						}
						$html .= '<td>' . $field_html . '</td>';
						$html .= '</tr>';
					}
				}
			}
			$html .= '</table>';
			$html .= '<input type="hidden" name="wooaiow_' . $this->id . '_save_post" value="wooaiow_' . $this->id . '_save_post">';
			echo $html;
		}

		/**
		 * If check is enabled OR not.
		 *
		 * @return bool
		 */
		function is_enabled() {
			return aiow_is_module_enabled( ( 'module' === $this->type ? $this->id : $this->parent_id ) );
		}

		/**
		 * Setting section.
		 *
		 * @param array $sections Setting section.
		 * @return array
		 */
		function settings_section( $sections ) {
			$sections[ $this->id ] = isset( $this->section_title ) ? $this->section_title : $this->short_desc;
			return $sections;
		}

		/**
		 * Get section by category ID.
		 *
		 * @param string $section Section.
		 * @return mixed
		 */
		function get_cat_by_section( $section ) {
			$cats = aiow_admin_setting_tab();
			foreach ( $cats as $id => $label_info ) {
				if ( ( ! empty( $label_info['all_cat_ids'] ) ) &&
					 ( is_array( $label_info['all_cat_ids'] ) ) &&
					 ( in_array( $section, $label_info['all_cat_ids'] ) )
					) {
						return $id;
					}
			}
			return '';
		}

		/**
		 * Get back to link.
		 *
		 * @return string
		 */
		function get_back_to_settings_link_html() {
			$cat_id = $this->get_cat_by_section( $this->id );
			$the_link = admin_url( 'admin.php?page=wc-settings&tab=all_in_one_wc&aiow-cat=' . $cat_id . '&section=' . $this->id );
			return '<a href="' .  $the_link . '"><< ' . __( 'Back to Module Settings', 'all-in-one-wc' ) . '</a>';
		}

		/**
		 * Add tool list.
		 *
		 * @param array $settings Setting.
		 * @return array
		 */
		function add_tools_list( $settings ) {
			return array_merge( $settings, array(
				array(
					'title'    => __( 'Tools', 'all-in-one-wc' ),
					'type'     => 'title',
					'desc'     => '',
					'id'       => 'aiow_' . $this->id . '_tools_options'
				),
				array(
					'title'    => __( 'Module Tools', 'all-in-one-wc' ),
					'id'       => 'aiow_' . $this->id . '_module_tools',
					'type'     => 'module_tools',
				),
				array(
					'type'     => 'sectionend',
					'id'       => 'aiow_' . $this->id . '_tools_options'
				),
			) );
		}

		/**
		 * Get tool header html.
		 *
		 * @param int|string $tool_id Tool ID.
		 * @return array
		 */
		function get_tool_header_html( $tool_id ) {
			$html = '';
			if ( isset( $this->tools_array[ $tool_id ] ) ) {
				$html .= '<p>' .  $this->get_back_to_settings_link_html() . '</p>';
				$html .= '<h3>' . $this->tools_array[ $tool_id ]['title'] . '</h3>';
				$html .= '<p style="font-style:italic;">' . $this->tools_array[ $tool_id ]['desc']  . '</p>';
			}
			return $html;
		}

		/**
		 * Add module tool.
		 *
		 * @param array $tools_array Tools.
		 * @param array $args Arguments.
		 */
		function add_tools( $tools_array, $args = array() ) {
			$this->tools_array = $tools_array;
			add_action( 'aiow_module_tools_' . $this->id, array( $this, 'add_tool_link' ), PHP_INT_MAX );
			$hook_priority = isset( $args['tools_dashboard_hook_priority'] ) ? $args['tools_dashboard_hook_priority'] : 10;
			if ( $this->is_enabled() ) {
				add_filter( 'aiow_tools_tabs', array( $this, 'add_module_tools_tabs' ), $hook_priority );
				foreach ( $this->tools_array as $tool_id => $tool_data ) {
					add_action( 'aiow_tools_' . $tool_id, array( $this, 'create_' . $tool_id . '_tool' ) );
				}
			}
			add_action( 'aiow_tools_dashboard', array( $this, 'add_module_tools_info_to_tools_dashboard' ), $hook_priority );
		}

		/**
		 * Add module tab.
		 *
		 * @param arrau $tabs Setting tab.
		 * @return array
		 */
		function add_module_tools_tabs( $tabs ) {
			foreach ( $this->tools_array as $tool_id => $tool_data ) {
				$tool_title = ( isset( $tool_data['tab_title'] ) ) ?
					$tool_data['tab_title'] :
					$tool_data['title'];
				$tabs[] = array(
					'id'    => $tool_id,
					'title' => $tool_title,
				);
			}
			return $tabs;
		}

		/**
		 * Add module info in dashboard.
		 */
		function add_module_tools_info_to_tools_dashboard() {
			$is_enabled_html = ( $this->is_enabled() ) ?
				'<span style="color:green;font-style:italic;">' . __( 'enabled', 'all-in-one-wc' )  . '</span>' :
				'<span style="color:gray;font-style:italic;">'  . __( 'disabled', 'all-in-one-wc' ) . '</span>';
			foreach ( $this->tools_array as $tool_id => $tool_data ) {
				$tool_title = $tool_data['title'];
				$tool_desc  = $tool_data['desc'];
				$additional_style_html = '';
				$additional_info_html = '';
				if ( isset( $tool_data['deprecated'] ) && true === $tool_data['deprecated'] ) {
					$additional_style_html = 'color:gray;font-style:italic;';
					$additional_info_html  = ' - ' . __( 'Deprecated', 'all-in-one-wc' );
				}
				echo '<tr>';
				echo '<td style="' . $additional_style_html . '">' . $tool_title . $additional_info_html . '</td>';
				echo '<td style="' . $additional_style_html . '">' . $this->short_desc . '</td>';
				echo '<td style="' . $additional_style_html . '">' . $tool_desc . '</td>';
				echo '<td style="' . $additional_style_html . '">' . $is_enabled_html . '</td>';
				echo '</tr>';
			}
		}

		/**
		 * Add tool link
		 */
		function add_tool_link() {
			foreach ( $this->tools_array as $tool_id => $tool_data ) {
				$tool_title = $tool_data['title'];
				echo '<p>';
				echo ( $this->is_enabled() ) ?
					'<a href="' . admin_url( 'admin.php?page=aiow-tools&tab=' . $tool_id ) . '"><code>' . $tool_title . '</code></a>' :
					'<code>' . $tool_title . '</code>';
				echo '</p>';
			}
		}

		/**
		 * Add reset setting button.
		 *
		 * @param array $settings Settings.
		 * @return array
		 */
		function add_reset_settings_button( $settings ) {
			$reset_button_style = "background: red; border-color: red; box-shadow: 0 1px 0 red; text-shadow: 0 -1px 1px #a00,1px 0 1px #a00,0 1px 1px #a00,-1px 0 1px #a00;";
			$reset_settings_setting = array(
				array(
					'title' => __( 'Reset Settings', 'all-in-one-wc' ),
					'type'  => 'title',
					'id'    => 'aiow_' . $this->id . '_reset_settings_options',
				),
				array(
					'title'    => ( 'module' === $this->type ) ?
						__( 'Reset Module to Default Settings', 'all-in-one-wc' ) :
						__( 'Reset Submodule to Default Settings', 'all-in-one-wc' ),
					'id'       => 'aiow_' . $this->id . '_reset_settings',
					'type'     => 'aiow_custom_link',
					'link'     => '<a onclick="return confirm(\'' . __( 'Are you sure?', 'all-in-one-wc' ) . '\')" class="button-primary" style="' .
						$reset_button_style . '" href="' . add_query_arg( 'aiow_reset_settings', $this->id ) . '">' . __( 'Reset settings', 'all-in-one-wc' ) . '</a>',
				),
				array(
					'type' => 'sectionend',
					'id'   => 'aiow_' . $this->id . '_reset_settings_options',
				),
			);
			return array_merge( $settings, $reset_settings_setting );
		}

		/**
		 * Add enable module setting.
		 *
		 * @param array  $settings Module setting.
		 * @param string $module_desc Module description.
		 * @return array
		 */
		function add_enable_module_setting( $settings, $module_desc = '' ) {
			if ( 'module' != $this->type ) {
				return $settings;
			}
			if ( '' === $module_desc && ! empty( $this->get_extra_desc() ) ) {
				$module_desc = '<div style="padding: 15px; background-color: #ffffff; color: #000000;">' . $this->get_extra_desc() . '</div>';
			}
			if ( ! isset( $this->link ) && isset( $this->link_slug ) && '' != $this->link_slug ) {
				$this->link = '';
			}
			$the_link = '';
			$enable_module_setting = array(
				array(
					'title' => $this->short_desc . ' ' . __( 'Module Options', 'all-in-one-wc' ),
					'type'  => 'title',
					'desc'  => $module_desc,
					'id'    => 'aiow_' . $this->id . '_module_options',
				),
				array(
					'title'    => $this->short_desc,
					'desc'     => '<strong>' . __( 'Enable Module', 'all-in-one-wc' ) . '</strong>',
					'desc_tip' => $this->get_desc() . $the_link,
					'id'       => 'aiow_' . $this->id . '_enabled',
					'default'  => 'no',
					'type'     => 'checkbox',
					'aiow_desc' => $this->get_desc(),
					'aiow_link' => ( isset( $this->link ) ? $this->link : '' ),
				),
				array(
					'type' => 'sectionend',
					'id'   => 'aiow_' . $this->id . '_module_options',
				),
			);
			return array_merge( $enable_module_setting, $settings );
		}

		/**
		 * Get Description
		 */
		function get_desc() {
			if (
				empty( $this->desc_pro )
				|| ! class_exists( 'AIOW_Plus' )
			) {
				return $this->desc;
			}
			return $this->desc_pro;
		}

		/**
		 * Get extra desc.
		 *
		 * @return mixed
		 */
		function get_extra_desc() {
			if (
				empty( $this->extra_desc_pro )
				|| ! class_exists( 'AIOW_Plus' )
			) {
				return $this->extra_desc;
			}
			return $this->extra_desc_pro;
		}
	}
}
