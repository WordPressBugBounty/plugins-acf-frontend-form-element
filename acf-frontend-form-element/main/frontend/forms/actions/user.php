<?php
namespace Frontend_Admin\Actions;

use Frontend_Admin\Plugin;
use Frontend_Admin;
use Frontend_Admin\Classes\ActionBase;
use Frontend_Admin\Forms\Actions;
use Elementor\Controls_Manager;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
if ( ! class_exists( 'ActionUser' ) ) :

	class ActionUser extends ActionBase {


		public function get_name() {
			return 'user';
		}

		public function get_label() {
			return __( 'User', 'frontend-admin' );
		}

		public function get_fields_display( $form_field, $local_field ) {
			switch ( $form_field['field_type'] ) {
				case 'username';
					$local_field['type']            = 'username';
					$local_field['allow_edit']      = $form_field['allow_edit'] ?? 0;
					break;
				case 'password':
					$local_field['type']                 = 'user_password';
					$local_field['edit_password']        = isset( $form_field['edit_password'] ) ? $form_field['edit_password'] : 'Edit Password';
					$local_field['cancel_edit_password'] = isset( $form_field['cancel_edit_password'] ) ? $form_field['cancel_edit_password'] : 'Cancel';
					$local_field['force_edit']           = isset( $form_field['force_edit_password'] ) ? $form_field['force_edit_password'] : 0;
					$local_field['password_strength']    = isset( $form_field['password_strength'] ) ? $form_field['password_strength'] : 3;
					break;
				case 'confirm_password':
					$local_field['type'] = 'user_password_confirm';
					break;
				case 'email':
					$local_field['type'] = 'user_email';
					if ( ! empty( $form_field['set_as_username'] ) ) {
						$local_field['set_as_username'] = true;
					} else {
						$local_field['set_as_username'] = false;
					}
					break;
				case 'bio':
					$local_field['type'] = 'user_bio';
					break;
				case 'role':
					$local_field['type'] = 'role';
					if ( isset( $form_field['role_field_options'] ) ) {
						$local_field['role_options'] = $form_field['role_field_options'];
					}
					$local_field['field_type']    = isset( $form_field['role_appearance'] ) ? $form_field['role_appearance'] : 'radio';
					$local_field['layout']        = isset( $form_field['role_radio_layout'] ) ? $form_field['role_radio_layout'] : 'vertical';
					$local_field['default_value'] = isset( $form_field['default_role'] ) ? $form_field['default_role'] : 'subscriber';
					break;
				default:
					$local_field['type'] = $form_field['field_type'];

			}
			return $local_field;
		}

		public function get_default_fields( $form, $action = '' ) {
			switch ( $action ) {
				case 'delete':
					$default_fields = array(
						'delete_user',
					);
					break;
				default:
					$default_fields = array(
						'username',
						'user_email',
						'user_password',
						'first_name',
						'last_name',
						'role',
						'submit_button',
					);
			}

			return $this->get_valid_defaults( $default_fields, $form );
		}

		
		public function get_form_builder_options( $form ) {
			$roles = feadmin_get_user_roles( array( 'administrator' ) );

			 return array(
				 array(
					 'key'               => 'save_to_user',
					 'field_label_hide'  => 0,
					 'type'              => 'select',
					 'instructions'      => '',
					 'required'          => 0,
					 'conditional_logic' => 0,
					 'choices'           => array(
						 'edit_user' => __( 'Edit User', 'frontend-admin' ),
						 'new_user'  => __( 'New User', 'frontend-admin' ),
					 ),
					 'allow_null'        => 0,
					 'multiple'          => 0,
					 'ui'                => 0,
					 'return_format'     => 'value',
					 'ajax'              => 0,
					 'placeholder'       => '',
				 ),
				 array(
					 'key'               => 'user_to_edit',
					 'label'             => __( 'User to Edit', 'frontend-admin' ),
					 'type'              => 'select',
					 'instructions'      => '',
					 'required'          => 0,
					 'conditional_logic' => array(
						 array(
							 array(
								 'field'    => 'save_to_user',
								 'operator' => '==',
								 'value'    => 'edit_user',
							 ),
						 ),
					 ),
					 'choices'           => array(
						 'current_user'   => __( 'Current User', 'frontend-admin' ),
						 'current_author' => __( 'Current Author', 'frontend-admin' ),
						 'post_author'    => __( 'Form Post Author', 'frontend-admin' ),
						 'url_query'      => __( 'URL Query', 'frontend-admin' ),
						 'select_user'    => __( 'Specific User', 'frontend-admin' ),
					 ),
					 'default_value'     => false,
					 'allow_null'        => 0,
					 'multiple'          => 0,
					 'ui'                => 0,
					 'return_format'     => 'value',
					 'ajax'              => 0,
					 'placeholder'       => '',
				 ),
				 array(
					 'key'               => 'url_query_user',
					 'label'             => __( 'URL Query Key', 'frontend-admin' ),
					 'type'              => 'text',
					 'instructions'      => '',
					 'required'          => 0,
					 'conditional_logic' => array(
						 array(
							 array(
								 'field'    => 'save_to_user',
								 'operator' => '==',
								 'value'    => 'edit_user',
							 ),
							 array(
								 'field'    => 'user_to_edit',
								 'operator' => '==',
								 'value'    => 'url_query',
							 ),
						 ),
					 ),
					 'placeholder'       => '',
				 ),
				 array(
					 'key'               => 'select_user',
					 'label'             => __( 'Specific User', 'frontend-admin' ),
					 'name'              => 'select_user',
					 'prefix'            => 'form',
					 'type'              => 'user',
					 'instructions'      => '',
					 'required'          => 0,
					 'conditional_logic' => array(
						 array(
							 array(
								 'field'    => 'save_to_user',
								 'operator' => '==',
								 'value'    => 'edit_user',
							 ),
							 array(
								 'field'    => 'user_to_edit',
								 'operator' => '==',
								 'value'    => 'select_user',
							 ),
						 ),
					 ),
					 'role'              => '',
					 'allow_null'        => 0,
					 'multiple'          => 0,
					 'return_format'     => 'object',
					 'ui'                => 1,
				 ),
				array(
					'key'               => 'roles',
					'label'             => __( 'Role', 'frontend-admin' ),
					'name'              => 'role',
					'prefix'            => 'form',
					'type'              => 'select',
					'instructions'      => __( 'Limit roles that can be edited', 'frontend-admin' ),
					'required'          => 0,
					'conditional_logic' => array(
						array(
							array(
								'field'    => 'save_to_user',
								'operator' => '==',
								'value'    => 'edit_user',
							),
						),
					),
					'choices'           => $roles,
					'default_value'     => [ 'subscriber', 'author', 'contributor' ],
					'allow_null'        => 0,
					'multiple'          => 1,
					'return_format'     => 'value',
					'ui'                => 1,
				),
				array(
					'key'               => 'new_user_role',
					'label'             => __( 'Role for New User', 'frontend-admin' ),
					'name'              => 'new_user_role',
					'prefix'            => 'form',
					'type'              => 'select',
					'instructions'      => '',
					'required'          => 0,
					'conditional_logic' => array(
						array(
							array(
								'field'    => 'save_to_user',
								'operator' => '==',
								'value'    => 'new_user',
							),
						),
					),
					'default_value'     => 'subscriber',
					'choices'           => $roles,
					'allow_null'        => 0,
					'multiple'          => 0,
					'return_format'     => 'value',
					'ui'                => 1,
				),
			 );
		}

		public function before_users_query() {
			$fields = array(
				array(
					'key'           => 'select_user',
					'label'         => __( 'Select By User', 'frontend-admin' ),
					'type'          => 'user',
					'instructions'  => '',
					'allow_null'    => 0,
					'multiple'      => 1,
					'ajax'          => 1,
					'ui'            => 1,
					'return_format' => 'id',
				),
				array(
					'key'           => 'by_user_id',
					'label'         => __( 'Select By User', 'frontend-admin' ),
					'type'          => 'user',
					'instructions'  => '',
					'allow_null'    => 0,
					'multiple'      => 1,
					'return_format' => 'id',
				),
			);

			foreach ( $fields as $field ) {
				$field['prefix'] = 'form';
				$field['name']   = $field['key'];
				acf_add_local_field( $field );
			}

		}

		public function register_settings_section( $widget ) {
			$tab = apply_filters( 'frontend_admin/elementor/form_widget/control_tab', Controls_Manager::TAB_CONTENT, $widget );
			$condition = apply_filters( 'frontend_admin/elementor/form_widget/conditions', false, $widget );

			$widget->start_controls_section(
				'section_edit_user',
				array(
					'label'     => $this->get_label(),
					'tab'       => $tab,
					'condition' => $condition,
				)
			);

			$this->action_controls( $widget );

			$widget->end_controls_section();
		}


		public function action_controls( $widget ) {
					
			$type = $widget->form_defaults['save_to_user'] ?? 'edit_user';


			$condition = array();

			if( 'delete_user' !== $widget->get_name() ){

				$widget->add_control( 'save_to_user', array(
					'label'   => __( 'User', 'frontend-admin' ),
					'type'    => Controls_Manager::SELECT,
					'options' => array(
						'edit_user' => __( 'Edit User', 'frontend-admin' ),
						'new_user'  => __( 'New User', 'frontend-admin' ),
					),
					'default' => $widget->get_name(),
				) );
			}else{
				$widget->add_control( 'save_to_user', array(
					'label'   => __( 'User', 'frontend-admin' ),
					'type'    => Controls_Manager::HIDDEN,
					'default' => $widget->get_name(),
				) );
			}

			$condition['save_to_user'] = array( 'edit_user', 'delete_user' );

			$widget->add_control(
				'user_to_edit',
				array(
					'label'     => __( 'Specific User', 'frontend-admin' ),
					'type'      => \Elementor\Controls_Manager::SELECT,
					'default'   => 'current_user',
					'options'   => array(
						'current_user'   => __( 'Current User', 'frontend-admin' ),
						'current_author' => __( 'Current Author', 'frontend-admin' ),
						'url_query'      => __( 'URL Query', 'frontend-admin' ),
						'select_user'    => __( 'Specific User', 'frontend-admin' ),
					),
					'condition' => $condition,
				)
			);
			$condition['user_to_edit'] = 'url_query';
			$widget->add_control(
				'url_query_user',
				array(
					'label'       => __( 'URL Query', 'frontend-admin' ),
					'type'        => Controls_Manager::TEXT,
					'placeholder' => __( 'user_id', 'frontend-admin' ),
					'default'     => __( 'user_id', 'frontend-admin' ),
					'description' => __( 'Enter the URL query parameter containing the id of the user you want to edit', 'frontend-admin' ),
					'condition'   => $condition,
				)
			);
			$condition['user_to_edit'] = 'select_user';
			$widget->add_control(
				'user_select',
				array(
					'label'       => __( 'User', 'frontend-admin' ),
					'type'        => Controls_Manager::TEXT,
					'placeholder' => __( '18', 'frontend-admin' ),
					'default'     => get_current_user_id(),
					'description' => __( 'Enter user id', 'frontend-admin' ),
					'condition'   => $condition,
				)
			);

			unset( $condition['user_to_edit'] );
			$condition['save_to_user'] = 'new_user';

			$widget->add_control(
				'new_user_role',
				array(
					'label'       => __( 'New User Role', 'frontend-admin' ),
					'type'        => \Elementor\Controls_Manager::SELECT2,
					'label_block' => true,
					'default'     => 'subscriber',
					'options'     => feadmin_get_user_roles( array( 'administrator' ) ),
					'condition'   => $condition,
				)
			);

			$widget->add_control(
				'hide_admin_bar',
				array(
					'label'        => __( 'Hide WordPress Admin Area?', 'frontend-admin' ),
					'type'         => \Elementor\Controls_Manager::SWITCHER,
					'label_on'     => __( 'Hide', 'frontend-admin' ),
					'label_off'    => __( 'Show', 'frontend-admin' ),
					'return_value' => 'true',
					'condition'    => $condition,
				)
			);

				$widget->add_control(
					'login_user',
					array(
						'label'        => __( 'Log in as new user?', 'frontend-admin' ),
						'type'         => Controls_Manager::SWITCHER,
						'label_on'     => __( 'Yes', 'frontend-admin' ),
						'label_off'    => __( 'No', 'frontend-admin' ),
						'return_value' => 'true',
						'condition'    => $condition,
					)
				);

				$roles = feadmin_get_user_roles( array( 'administrator' ) );

			$widget->add_control(
				'roles',
				array(
					'label'       => __( 'Role', 'frontend-admin' ),
					'type'        => \Elementor\Controls_Manager::SELECT2,
					'description' => __( 'Limit roles that can be edited', 'frontend-admin' ), 
					'label_block' => true,
					'default'     => [ 'subscriber', 'author', 'contributor' ],
					'multiple'    => true,
					'options'     => $roles,
					'condition'   => array(
						'save_to_user' => 'edit_user',
					),
				)
			);

		}

		public function run( $form, $step = false ) {
			
			$record = $form['record'];
			if ( empty( $record['user'] ) || empty( $record['fields']['user'] ) ) {
				return $form;
			}

			$user_id = $record['user'];

			// allow for custom save
			$user_id = apply_filters( 'acf/pre_save_user', $user_id, $form );

			// 'add_user' is the only legitimate non-numeric target (new user creation);
			// anything else must resolve to a real user id the current requester can edit,
			// independent of the show_form/special_permissions gating that ran earlier.
			if ( 'add_user' !== $user_id ) {
				if ( ! is_numeric( $user_id ) || ! current_user_can( 'edit_user', (int) $user_id ) ) {
					return $form;
				}
				$user_id = (int) $user_id;
			}

			$username_generated = false;
			$user_to_insert     = array();
			$metas              = array();

			$core_fields = array(
				'username',
				'user_password',
				'user_email',
				'display_name',
				'user_url',
				'first_name',
				'last_name',
				'user_bio',
			);

			if ( ! empty( $record['fields']['user'] ) ) {
				foreach ( $record['fields']['user'] as $name => $_field ) {
					if ( ! isset( $_field['key'] ) ) {
						continue;
					}
					$field = fea_instance()->frontend->get_field( $_field['key'] );

					if ( ! $field ) {
						continue;
					}

					$field_type      = $field['type'];
					$field['value']  = $_field['_input'];
					$field['_input'] = $_field['_input'];

					if ( ! in_array( $field_type, $core_fields ) ) {
						$metas[ $field['key'] ] = $field;
						continue;
					}

					if ( ! empty( $field['set_as_username'] ) ) {
						$user_to_insert['user_login'] = $field['value'];
					}

					switch ( $field_type ) {
						case 'username':
							$user_to_insert['user_login'] = $field['value'];
							if ( empty( $user_to_insert['user_nicename'] ) ) {
								  $user_to_insert['user_nicename'] = mb_substr( $field['value'], 0, 50 );
							}
							break;
						case 'user_password':
							$field['value'] = fea_decrypt( $field['value'] );
							if ( ( is_numeric( $user_id ) && empty( $_POST['edit_user_password'] ) ) || empty( $field['value'] ) ) {
								continue 2;
							}
							$user_to_insert['user_pass'] = $field['value'];
							break;
						case 'user_bio':
							$user_to_insert['description'] = $field['value'];
							break;
						default:
							$user_to_insert[ $field_type ] = $field['value'];
					}
				}
			}
			global $wpdb;

			$is_new_user_registration = false;
			$is_public_registration   = false;

			if ( $user_id == 'add_user' ) {
				if ( empty( $user_to_insert['user_login'] ) ) {
					$user_to_insert['user_login'] = $this->generate_username();
				}

				$user_to_insert['user_registered'] = gmdate( 'Y-m-d H:i:s' );

				if ( empty( $user_to_insert['user_pass'] ) ) {
					$user_to_insert['user_pass'] = wp_generate_password();
				}

				if ( empty( $user_to_insert['user_nicename'] ) ) {
					$user_to_insert['user_nicename'] = $user_to_insert['user_login'];
				}

				$is_public_registration = ! is_user_logged_in();

				$default_role = get_option( 'default_role' );

				// The form's new_user_role is a setting saved by whoever built the form,
				// never submitted input, so it is honored for every submitter the form's
				// own permissions let through. The one exception is an administrator-level
				// role, which still needs a submitter who may promote users or who was
				// given the "Assign Privileged User Roles" special permission.
				if ( ! empty( $form['new_user_role'] ) ) {
					$user_to_insert['role'] = $form['new_user_role'];
					$new_role               = get_role( $user_to_insert['role'] );
					$is_admin_role          = 'administrator' === $user_to_insert['role'] || ( $new_role && ! empty( $new_role->capabilities['manage_options'] ) );
					if ( ! $new_role || ( $is_admin_role && ! feadmin_has_special_permission( 'assign_roles', 'promote_users', $form ) ) ) {
						$user_to_insert['role'] = $default_role;
					}
				} else {
					$user_to_insert['role'] = $default_role;
				}

				$user_id                  = wp_insert_user( $user_to_insert );
				$is_new_user_registration = ! is_wp_error( $user_id );

				if ( is_wp_error( $user_id ) ) {
					return $form;
				}

				$form['record']['user'] = $user_id;

				if ( isset( $form['hide_admin_bar'] ) ) {
					update_user_meta( $user_id, 'hide_admin_area', $form['hide_admin_bar'] );
				}

				if ( empty( $record['fields']['user']['nickname']['_input'] ) ) {
					$nickname = $user_to_insert['user_login'];
					if ( ! empty( $record['fields']['user']['first_name']['_input'] ) ) {
						$nickname = $record['fields']['user']['first_name']['_input'];
					}
					update_user_meta( $user_id, 'nickname', $nickname );
				}
				
				do_action( 'user_register', $user_id, $user_to_insert );
			} else {
				if ( $user_to_insert ) {
					$old_user_data = get_userdata( $user_id );
					if ( isset( $user_to_insert['user_login'] ) ){
						if( $user_to_insert['user_login'] == $old_user_data->user_login ) {
							unset( $user_to_insert['user_login'] );
						} else {
							$wpdb->update( $wpdb->users, array( 'user_login' => $user_to_insert['user_login'] ), array( 'ID' => $user_id ) );
							$log_back_in = true;
						}
					}


					if ( isset( $user_to_insert['user_pass'] ) ) {
						$log_back_in = true;
					}

					
					if ( $user_to_insert ) {
						$user_to_insert['ID'] = $user_id;
						wp_update_user(  $user_to_insert );
					}
					do_action( 'profile_update', $user_id, $old_user_data, $user_to_insert );
				}
			}

			if ( isset( $form['user_manager'] ) ) {
				update_user_meta( $user_id, 'frontend_admin_manager', $form['user_manager'] );
			}

			if ( isset( $form['hide_admin_bar'] ) ) {
				update_user_meta( $user_id, 'show_admin_bar_front', ! $form['hide_admin_bar'] );
			}

			if ( ! empty( $metas ) ) {
				foreach ( $metas as $meta ) {
				
					// a field's name is its meta key: never let one write the user's
					// roles, level or sessions (also enforced on acf/pre_update_metadata)
					if ( feadmin_is_protected_meta_key( $meta['name'] ?? '', 'user' ) ) {
						continue;
					}

					acf_update_value( $meta['_input'], 'user_' . $user_id, $meta );
				}
			}

			// Check the role the new account really ended up with, not the one the
			// form asked for: a field saved above may have changed it. Unless the
			// submitter may promote users, anything beyond the assigned role or a
			// non-privileged role chosen through a Role field is undone, and the
			// account is not logged in.
			$role_escalated = false;
			if ( $is_new_user_registration && ! feadmin_has_special_permission( 'assign_roles', 'promote_users', $form ) ) {
				clean_user_cache( $user_id );
				$created_user = get_userdata( $user_id );
				$role_field   = function_exists( 'acf_get_field_type' ) ? acf_get_field_type( 'role' ) : false;

				foreach ( array_keys( array_filter( (array) $created_user->caps ) ) as $cap ) {
					if ( $cap === $user_to_insert['role'] ) {
						continue;
					}
					$is_safe_role = get_role( $cap ) && $role_field && method_exists( $role_field, 'has_sensitive_capability' ) && ! $role_field->has_sensitive_capability( $cap );
					if ( ! $is_safe_role ) {
						$role_escalated = true;
					}
				}

				if ( $role_escalated ) {
					$created_user->remove_all_caps();
					$created_user->set_role( $user_to_insert['role'] );
				}
			}


			// Auto-login must only fire for a user actually created by this request's
			// add_user branch, and only when that creation was either genuine public
			// self-registration or performed by a submitter who holds create_users -
			// never for an edit_user
			// save, which would otherwise let anyone with edit access to another
			// account's form switch the current session into that account.
			$legitimate_registration = $is_new_user_registration && ( $is_public_registration || current_user_can( 'create_users' ) );

			if ( ! empty( $form['login_user'] ) && $legitimate_registration && ! $role_escalated ) {
				$user = get_user_by( 'id', $user_id );
				if ( ! empty( $user->user_login ) ) {
					wp_set_current_user( $user_id, $user->user_login );
					wp_set_auth_cookie( $user_id );
				}
			}

			do_action( 'frontend_admin/save_user', $form, $user_id );
			do_action( 'acf_frontend/save_user', $form, $user_id );

			return $form;
		}

		public function load_data( $form ) {
			if ( empty( $form['save_to_user'] ) ) {
				return $form;
			}
			switch ( $form['save_to_user'] ) {
				case 'new_user':
					
					$form['user_id'] = 'add_user';
					break;
				case 'edit_user':
				case 'delete_user':
					if ( empty( $form['user_to_edit'] ) ) {
						$form['user_to_edit'] = 'current_user';
					}

					if ( 'current_author' == $form['user_to_edit'] ) {
						if ( is_author() ) {
							$author_id = get_queried_object_id();
						} else {
							$author_id = get_the_author_meta( 'ID' );
						}
						$form['user_id'] = $author_id;
					}

					if ( 'select_user' == $form['user_to_edit'] ) {
						if ( ! empty( $form['select_user'] ) ) {
							$form['user_id'] = $form['select_user'];
						} else {
							if ( isset( $form['user_select'] ) ) {
								$form['user_id'] = $form['user_select'];
							}
						}
					}
					if ( 'url_query' == $form['user_to_edit'] ) {
						if ( isset( $_GET[ $form['url_query_user'] ] ) ) {
							$form['user_id'] = absint( $_GET[ $form['url_query_user'] ] );
						}
					}
					if ( 'current_user' == $form['user_to_edit'] ) {
						$form['user_id'] = get_current_user_id();
					}
				

					if ( empty( $form['user_id'] ) || ! feadmin_user_exists( $form['user_id'] ) ) {
						$form['user_id'] = 'none';
						return $form;
					}
					$user = get_userdata( $form['user_id'] );
					if( ! $user ){
						$form['user_id'] = 'none';
					}else{
						$current_user = get_current_user_id();
						if( ! empty( $form['roles'] ) && $current_user != $form['user_id'] ){
							$roles = $user->roles;
							//check if the roles are valid
							$roles = array_intersect( $roles, $form['roles'] );
							if( empty( $roles ) ){
								$form['user_id'] = 'none';
							}
							
						}
					}
					
			}
			return $form;
		}

		public function generate_username( $prefix = '' ) {
			$new_username = uniqid( $prefix );
			if ( ! username_exists( $new_username ) ) {
				return $new_username;
			} else {
				return $this->generate_username( $prefix );
			}
		}

		public function conditions_logic( $settings, $condition, $user ){
			$user_id = $settings['user_id'] ?? 'none';

			// 'none' and 'add_user' are the only legitimate non-numeric states;
			// anything else is unexpected input and must not bypass the capability check below.
			if ( in_array( $user_id, array( 'none', 'add_user' ), true ) ) {
				return $settings;
			}

			if( ! is_numeric( $user_id ) ){
				$settings['user_id'] = 'none';
				return $settings;
			}

			if( ! current_user_can( 'edit_user', $user_id ) ){
				if( ! in_array( 'edit_users', $condition['special_permissions'] ) )
					{
						$settings['user_id'] = 'none';
					}
			}

			return $settings;
		}


		public function __construct() {
			add_filter( 'frontend_admin/special_permissions', array( $this, 'conditions_logic' ), 10, 3 );
			add_action( 'wp_ajax_acf/fields/user/query', array( $this, 'before_users_query' ), 4 );
		}
	}
	fea_instance()->local_actions['user'] = new ActionUser();

endif;
