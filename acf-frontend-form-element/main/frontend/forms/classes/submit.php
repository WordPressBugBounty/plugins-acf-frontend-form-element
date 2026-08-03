<?php
namespace Frontend_Admin\Classes;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'Frontend_Admin\Classes\Submit_Form' ) ) :

	class Submit_Form {
		public function check_inline_field() {
			// validate
			if ( ! feadmin_verify_ajax() ) {
				wp_send_json_error( __( 'Authentication Error. Please try refreshing the page.', 'frontend-admin' ) );
			}
			do_action( 'frontend_admin/validate_field' );

			global $fea_instance;
			$form_validate = $fea_instance->form_validate;

			// vars
			$json = array(
				'valid'   => 1,
				'errors'  => 0,
				'updates' => array(),
			);

			if ( ! empty( $_POST['acff'] ) ) {
				if ( ! empty( $_POST['acff']['_validate_email'] ) ) {
					$form_validate->add_error( '', __( 'Spam Detected', 'acf' ) );
				}

				$_post = $_POST['acff']; 

				foreach ( $_post as $source => $fields ) {
					$source = fea_decrypt( $source );

					if ( ! $this->current_user_can_edit_object( $source ) ) {
						continue;
					}

					foreach ( $fields as $key => $value ) {
						$field = $fea_instance->frontend->get_field( $key );
						if( ! $field ) continue;

						if ( $field['type'] == 'fields_select' ) {
							$fields_select = $key;
						}

						$input = 'acff[' . $source . '][' . $key . ']';
						$value = apply_filters( 'frontend_admin/forms/sanitize_input', feadmin_sanitize_input( $value, $field ), $field );
							
						// validate
						$valid = $form_validate->validate_value( $value, $field, $input );
						if ( $valid ) {
							// sanitize input based on field settings
								
							acf_update_value( $value, $source, $field );

							if ( empty( $fields_select ) ) {
								//$object            = get_field_object( $key, $source );
								$field['value'] = $value;
								$json['updates'][] = array(
									'html' => $fea_instance->dynamic_values->display_field( $field ),
								);
							}
						}
					}
					if ( ! empty( $fields_select ) ) {
						$object            = get_field_object( $fields_select, $source );
						$json['updates'][] = array(
							'html' => $fea_instance->dynamic_values->display_field( $object ),
						);
					}
				}
			}

			// vars
			$errors = $form_validate->get_errors();

			// bail ealry if no errors
			if ( ! $errors ) {
				wp_send_json_success( $json );
			}

			// update vars
			$json['valid']  = 0;
			$json['errors'] = $errors;

			// return
			wp_send_json_success( $json );
		}

		/**
		 * Checks whether the current user may edit the object a decrypted
		 * inline-update source resolves to. The source string comes from a
		 * client-supplied token, so it must be re-authorized here rather than
		 * trusted as-is.
		 */
		public function current_user_can_edit_object( $source ) {
			if ( empty( $source ) ) {
				return false;
			}

			if ( is_numeric( $source ) ) {
				return current_user_can( 'edit_post', $source );
			}

			if ( ! is_string( $source ) ) {
				return false;
			}

			if ( 0 === strpos( $source, 'user_' ) ) {
				return current_user_can( 'edit_user', substr( $source, 5 ) );
			}

			if ( 0 === strpos( $source, 'term_' ) ) {
				return current_user_can( 'edit_term', substr( $source, 5 ) );
			}

			return false;
		}


		public function check_submit_form() {
			

			// bail ealry if form not submit
			if ( empty( $_POST['_acf_form'] ) ) {
				wp_send_json_error( __( 'No Form Data', 'frontend-admin' ) );
			}

			// load form
			global $fea_instance, $fea_form;
			
			$form = $fea_instance->form_display->get_form( $_POST['_acf_form'] );	
			
			// bail ealry if form is corrupt
			if ( empty( $form ) ) {
				wp_send_json_error( __( 'No Form Data', 'frontend-admin' ) );
			}
			
			// verify nonce
			if ( ! feadmin_verify_nonce( $form['id'] . '_form' ) ) {
				wp_send_json_error( __( 'Authentication Error. Please try refreshing the page.', 'frontend-admin' ) );
			}

		
			// permissions.php echoes a not-allowed message as a side effect when denying
			// access; buffer and discard it here so it can't leak raw HTML in front of
			// the JSON response body below and break the front-end's JSON.parse().
			ob_start();
			$form = apply_filters( 'frontend_admin/show_form', $form );
			ob_end_clean();
			if( empty( $form['display'] ) ) {
				wp_send_json_error( __( 'You do not have permission to submit this form.', 'frontend-admin' ) );
			}

			$form['is_preview'] = isset( $_POST['_acf_status'] ) && 'preview' == $_POST['_acf_status'];

			error_log( '[FEA_PREVIEW_DEBUG] check_submit_form: _acf_status=' . ( $_POST['_acf_status'] ?? '<unset>' ) . ' is_preview=' . var_export( $form['is_preview'], true ) . ' form_id=' . $form['id'] );

			// submit
			$this->submit_form( $form );

		}

		function create_record( $form, $save = false ) {

			// Retrieve all form fields and their values
			$form['record'] = array(
				'post'  => false,
				'user'  => false,
				'term'  => false,
				'product' => false,
				'emails_to_verify' => array(),
				'verified_emails' => array(),
			);
			

			if ( ! empty( $form['submission_title'] ) ) {
				$form['dynamic_title'] = true;
			}

			if ( ! empty( $_POST['acff']['_validate_email'] ) ) {
				wp_send_json_error( __( 'Spam Detected', 'acf' ) );
			}

			if ( ! empty( $_POST['acff'] ) ) {
				foreach ( array_keys( $_POST['acff'] ) as $group ) {
					$group = sanitize_text_field( $group );
					$inputs = $_POST['acff'][$group];
					if ( is_array( $inputs ) ) {
						foreach ( $inputs as $key => $input ) {

							$form = $this->add_value_to_record( $form, $key, $input, $group );
						}
					}

				}
					
			}
			global $fea_form, $fea_instance;

			if ( ! empty( $form['dynamic_title'] ) ) {
				$form['submission_title'] = sanitize_text_field( $fea_instance->dynamic_values->get_dynamic_values( $form['submission_title'], $form ) );
			}	

			// add global for backwards compatibility
			$GLOBALS['admin_form'] = $form;
			$fea_form = $form;

			$form = $fea_instance->dynamic_values->get_form_dynamic_values( $form );

			if ( $save ) {
				$save = get_option( 'frontend_admin_save_submissions' );
				if ( isset( $form['no_record'] ) ) {
					$save = false;
				}
				if ( isset( $form['save_form_submissions'] ) ) {
					$save = $form['save_form_submissions'];
				}

				$save = apply_filters( 'frontend_admin/save_submission', $save, $form );

				if ( $save ) {
					$form = $this->save_record( $form );
				}
			}


			if ( isset( $_POST['_acf_status'] ) && 'save' == $_POST['_acf_status'] ) {
				$this->reload_form( $form );
			} 

			// vars
			$errors = $fea_instance->form_validate->get_errors();

			// bail ealry if no errors
			if ( $errors ) {
				// update vars
				$json = array(
					'valid'  => 0,
					'errors' => $errors,
				);

				if( ! empty( $form['submission'] ) ){
					$json['submission'] = $form['submission'];
				}
				
				// return
				wp_send_json_error( $json );
			}

			return $form;
		}

		function get_sub_field( $key, $field ) {
			// Vars.
			$sub_field = false;

			// Search sub fields.
			if ( ! empty( $field['sub_fields'] ) ) {
				foreach ( $field['sub_fields'] as $sub_field ) {
					if ( $key == $sub_field['key'] ) {
						return $sub_field;
					}
				}
			}

			// return
			return $sub_field;

		}

		public function add_value_to_record( $form, $key, $input, $group ) {
			$record = $form['record'];
			global $fea_instance;
			$field = $fea_instance->frontend->get_field( $key );

			if ( empty( $field ) ) {
				return $form;
			}


			$text_based = [
				'post_title', 'text', 'username', 'term_name', 'product_title', 'user_email', 'email', 'mailchimp_email', 
			];
			if ( $input && in_array( $field['type'], $text_based ) && empty( $form['dynamic_title'] ) && empty( $form['submission_title'] ) ) {
				$form['submission_title'] = sanitize_text_field( $input );
			}


				$input = apply_filters( 'frontend_admin/submissions/add_value/type=' . $field['type'], $input, $field );

				$input_key = 'acff[' . $group . '][' . $key . ']';

				// validate
				/* $valid = acf_validate_value( $input, $field, $input_key );

				if ( ! $valid ) return $form; */

				if( $form['kses'] ){
					// sanitize input based on field settings
					$input = apply_filters( 'frontend_admin/forms/sanitize_input', feadmin_sanitize_input( $input, $field ), $field );			
				}

				if ( $field['type'] == 'fields_select' ) {
					return $form;
				}

				if ( in_array( $field['type'], array( 'email', 'user_email', 'mailchimp_email' ) ) ) {
					if ( $input ) {
						$emails_handler = $fea_instance->emails_handler ?? false;
						if ( $emails_handler ) {
							$verified = $emails_handler->is_email_verified( $input );
							if ( !$verified ) {
								$record['emails_to_verify'][ $input ] = $input;
							} else {
								$record['verified_emails'][ $input ] = $input;
							}
						}
					}
				}

				$field['_input'] = $input;

				if ( 'user_password' == $field['type'] ) {
					$field['value'] = $field['_input'] = fea_encrypt( $field['_input'] );
				} 

			
				$record = apply_filters( 'frontend_admin/add_to_record', $record, $group, $field );
				$record = apply_filters( 'frontend_admin/add_to_record/' . $field['type'], $record, $group, $field );
				$record = apply_filters( 'frontend_admin/add_to_record/' . $field['key'], $record, $group, $field );


				$value = array(
					'key'    => $field['key'],
					'_input' => $field['_input'],
				);

				if( 'submission' != $group && ! empty( $record[$group] ) && is_numeric( $record[$group] ) ){
					$object_id = 'post' == $group ? $record[$group] : $group . '_' . $record[$group];
					$prev_value = acf_get_value( $object_id, $field );
	
					if( $prev_value != $field['_input'] ){
						$value['prev'] = $prev_value;
					}
				}
				
				if ( isset( $field['default_value'] ) ) {
					$value['default_value'] = $field['default_value'];
				}

				if ( $group ) {
					if( 'file_data' == $group ){
						$record['fields'][ $group ][ $field['name'] ] = $input;
					}else{
						$record['fields'][ $group ][ $field['name'] ] = $value;
					}
				} else {
					$record['fields'][ $field['name'] ] = $value;
				}

			$form['record'] = $record;
			return $form;
		}

		public function should_save_content( $form ){
			global $fea_instance;

			$save = empty( $form['save_all_data'] ) || isset( $form['approval'] );

			if ( ! empty( $form['save_all_data'] ) ) {
				$form['submission_status'] = implode( ',', $form['save_all_data'] );

				if ( in_array( 'verify_email', $form['save_all_data'] ) ) {
					$current_user = wp_get_current_user();
					if ( ! empty( $current_user->ID ) ) {
						$emails_handler = $fea_instance->emails_handler ?? false;
						if ( $emails_handler ) {							
							$verified = $emails_handler->is_email_verified( $current_user->user_email ); 
							if ( ! $verified ) {
								$form['record']['emails_to_verify'][ $current_user->user_email ] = $current_user->user_email;
							}else{
								$form['record']['verified_emails'][ $current_user->user_email ] = $current_user->user_email;
							}
						}						
					}

					if ( ! empty( $form['record']['emails_to_verify'] ) ) {
						$this->send_verification_emails( $form );
					}
	
					if( empty( $form['record']['emails_to_verify'] ) ){
						unset( $form['save_all_data']['verify_email'] );
						$form['submission_status'] = str_replace( 'verify_email', 'email_verified', $form['submission_status'] );
					}
		
				} 
				
			}

			$requirments = apply_filters( 'frontend_admin/form/check_requirements', [], $form );
			
			if( $requirments ){
				$save = false;
				$form['submission_status'] = str_replace( ['approved', 'approved,'], '', $form['submission_status'] );
				foreach( $requirments as $requirment ){
					$form['submission_status'] .= $form['submission_status'] ? ',' . $requirment : $requirment;
				}
			}

			$form['save_data'] = $save;

			return $form;
		}

		public function submit_form( $form ) {
			global $fea_instance, $fea_form;
			$form = $this->create_record( $form, true );

			if ( empty( $form['approval'] ) ) {
				do_action( 'frontend_admin/form/on_submit', $form );
			}else{
				do_action( 'frontend_admin/form/on_approval', $form );
			}

			$form['submission_status'] = 'approved';

			$form = $this->should_save_content( $form );

			error_log( '[FEA_PREVIEW_DEBUG] submit_form: is_preview=' . var_export( ! empty( $form['is_preview'] ), true ) . ' save_data=' . var_export( $form['save_data'] ?? null, true ) . ' save_to_post=' . ( $form['save_to_post'] ?? '<unset>' ) . ' post_id=' . var_export( $form['post_id'] ?? null, true ) );

			if ( ! empty( $form['is_preview'] ) ) {
				$form = $this->prepare_preview( $form );
				error_log( '[FEA_PREVIEW_DEBUG] after prepare_preview: record.status=' . var_export( $form['record']['status'] ?? null, true ) . ' new_post_status=' . var_export( $form['new_post_status'] ?? null, true ) . ' new_product_status=' . var_export( $form['new_product_status'] ?? null, true ) );
			}

			foreach ( $fea_instance->local_actions as $name => $action ) {
				if ( $name != 'options' && isset( $form[ "{$name}_id" ] ) ) {
						$form['record'][ $name ] = $form[ "{$name}_id" ];
					}
				if ( $form['save_data'] ) {
					$form = $action->run( $form );
				} 
			}

			$fea_form = $form;

			error_log( '[FEA_PREVIEW_DEBUG] after local actions: record=' . wp_json_encode( $form['record'] ?? null ) );

			$form = $this->run_actions( $form );

			$this->return_form( $form );
		}

		/**
		 * Field group key each local action actually checks before writing - mirrors the
		 * bail conditions in ActionPost::run() / ActionProduct::run() / etc. A type's
		 * save_to_$type/{$type}_id can be non-empty (e.g. "current_post" defaulting to
		 * whatever page hosts the form) even though the submission never touches it.
		 */
		public function preview_active_types( $form ) {
			$fields_key = array(
				'post'    => 'post',
				'user'    => 'user',
				'term'    => 'term',
				'product' => 'woo_product',
			);
			$active = array();
			foreach ( $fields_key as $type => $key ) {
				if ( ! empty( $form['record']['fields'][ $key ] ) ) {
					$active[] = $type;
				}
			}
			return $active;
		}

		public function prepare_preview( $form ) {
			$active_types = $this->preview_active_types( $form );
			foreach ( array( 'post', 'product' ) as $type ) {
				if ( ! in_array( $type, $active_types, true ) ) {
					continue;
				}
				$save_to = $form[ "save_to_$type" ] ?? '';
				if ( ! $save_to && empty( $form[ "{$type}_id" ] ) ) {
					continue;
				}
				if ( "duplicate_$type" == $save_to ) {
					// duplicating creates a new object; the (possibly published) source is untouched
					$form['record']['status'] = 'draft';
					continue;
				}
				// key off the resolved target id, not save_to: a form rehydrated from
				// _acf_objects keeps save_to "new_*" while the id is already numeric
				$target = $form[ "{$type}_id" ] ?? '';
				if ( ! is_numeric( $target ) && ! empty( $form['record'][ $type ] ) ) {
					$target = $form['record'][ $type ];
				}
				if ( is_numeric( $target ) ) {
					if ( 'publish' == get_post_status( $target ) ) {
						wp_send_json_error( __( 'Preview is only available for unpublished content.', 'frontend-admin' ) );
					}
					// keep the current draft/pending status instead of applying the form's configured status
					$form[ "new_{$type}_status" ] = 'no_change';
				} else {
					$form['record']['status'] = 'draft';
				}
			}
			return $form;
		}

		public function return_preview( $form ) {
			global $fea_instance;

			$active_types = $this->preview_active_types( $form );

			$preview_id = false;
			foreach ( array( 'product', 'post' ) as $type ) {
				if ( in_array( $type, $active_types, true ) && ! empty( $form['record'][ $type ] ) && is_numeric( $form['record'][ $type ] ) ) {
					$preview_id = $form['record'][ $type ];
					break;
				}
			}

			error_log( '[FEA_PREVIEW_DEBUG] return_preview: preview_id=' . var_export( $preview_id, true ) . ' active_types=' . implode( ',', $active_types ) . ' record=' . wp_json_encode( $form['record'] ?? null ) );

			if ( ! $preview_id ) {
				wp_send_json_error( __( 'Nothing to preview.', 'frontend-admin' ) );
			}

			$objects = array();
			foreach ( array( 'post', 'user', 'term', 'product' ) as $type ) {
				if ( in_array( $type, $active_types, true ) && ! empty( $form['record'][ $type ] ) && is_numeric( $form['record'][ $type ] ) ) {
					if ( in_array( $type, array( 'post', 'product' ), true ) && "edit_$type" != ( $form[ "save_to_$type" ] ?? '' ) ) {
						// remember this draft only exists because of a preview, so the
						// real submit can still apply the form's new-object status logic
						update_post_meta( $form['record'][ $type ], '_fea_preview_draft', $form['id'] );
					}
					$form[ $type . '_id' ]   = $form['record'][ $type ];
					$form[ "save_to_$type" ] = "edit_$type";
					$objects[ $type ]        = $form['record'][ $type ];
				}
			}
			if ( ! empty( $form['submission'] ) ) {
				$objects['submission'] = $form['submission'];
			}

			$response = array(
				'location'     => 'current',
				'form_element' => $form['id'],
				'objects'      => fea_encrypt( json_encode( $objects ) ),
				'preview'      => $fea_instance->form_preview->get_preview_url( $preview_id ),
			);

			do_action( 'frontend_admin/form/after_preview', $form, $response );

			wp_send_json_success( $response );
		}

		public function run_actions( $form ) {
			global $fea_instance;
			$run_actions = apply_filters( 'frontend_admin/form/run_actions', true, $form );
			if ( ! $run_actions ) {
				return $form;
			}
			$types = array( 'post', 'user', 'term', 'product' );
			foreach ( $types as $type ) {
				if ( isset( $form['record'][ $type ] ) ) {
					$form[ $type . '_id' ] = $form['record'][ $type ];
				}
			}

			$GLOBALS['admin_form'] = $form;


			$remote_actions = $fea_instance->remote_actions ?? null;

			if ( ! empty( $remote_actions ) ) {

				if ( empty( $form['approval'] ) && empty( $form['is_preview'] ) ) {
					if ( ! empty( $form['submit_actions'] ) ) {
						foreach ( $remote_actions as $name => $action ) {
							$action->run( $form );
						}
					} elseif ( ! empty( $form['more_actions'] ) ) {
						foreach ( $remote_actions as $name => $action ) {
							if ( in_array( $name, $form['more_actions'] ) ) {
								$action->run( $form );
							}
						}
					}
				}
			}

			return $form;
		}



		public function send_verification_emails( $form ) {
			foreach ( $form['record']['emails_to_verify'] as $email_address ) {	
				$subject  = __( 'Please verify your email.', 'frontend-admin' );
				$message  = '<h1>' . $subject . '</h1>';
				$token    = wp_create_nonce( 'frontend-admin-verify-' . $email_address );
				$message .= '<p>' . sprintf(
					__( 'Please click <a href="%s">here</a> to verify your email. Thank you.', 'frontend-admin' ) . '</p>',
					add_query_arg(
						array(
							'submission'    => $form['submission'],
							'email-address' => $email_address,
							'token'         => $token,
							'redirect'		=> sanitize_url( $_POST['_acf_current_url'] )
						),
						home_url( 'fea/verify-email' )
					)
				);
				// Set the type of email to HTML.
				$headers[] = 'Content-type: text/html; charset=UTF-8';

				$header_string = implode( "\r\n", $headers );

				return wp_mail(
					$email_address,
					$subject,
					$message,
					$header_string
				);
			}
		}

		public function return_form( $form ) {
			global $fea_instance;
			$types = array( 'post', 'user', 'term', 'product' );

			$form = apply_filters( 'frontend_admin/form/return', $form );

			if ( ! empty( $form['is_preview'] ) ) {
				$this->return_preview( $form );
			}

			if( ! empty( $_POST['redirect'] ) ){
				$form['redirect'] = $_POST['redirect'];
			}

			if( 'none' == $form['redirect'] ){
				$form['ajax_submit'] = true;
			}

			$update_message = $form['update_message'];
			if ( is_string( $update_message ) && strpos( $update_message, '[' ) !== 'false' ) {
				$update_message = $fea_instance->dynamic_values->get_dynamic_values( $update_message, $form );
			}
			$response = array(
				'to_top' => true,
				'form_element' => $form['id'],
			);

			if ( $form['show_update_message'] ) {
				$response['success_message'] = $update_message;
			}

			$save = get_option( 'frontend_admin_save_submissions' );
			if ( isset( $form['save_form_submissions'] ) ) {
				$save = $form['save_form_submissions'];
			}
			if ( isset( $form['no_record'] ) ) {
				$save = false;
			}

			$save = apply_filters( 'frontend_admin/save_submission', $save, $form );

			if ( $save ) {
				$form = $this->save_record( $form, $form['submission_status'] );
			}
			if ( ! empty( $form['ajax_submit'] ) ) {
				$response['location'] = 'current';
			
				if ( 'submission_form' == $form['ajax_submit'] ) {
					$title  = $form['record']['submission_title'];
					if ( ! empty( $form['submission_title'] ) ) {
						$title = $fea_instance->dynamic_values->get_dynamic_values( $form['submission_title'], $form );
					}
					$response['submission']       = $form['submission'];
					$response['submission_title'] = sanitize_text_field( $title );
					$response['close_modal']      = 1;
					$submission_form              = true;
				} else {
					if ( isset( $form['form_attributes']['data-field'] ) ) {
						$response['post_id']   = $form['post_id'];
						$response['field_key'] = $form['form_attributes']['data-field'];
						$response['modal'] = true;

						$host_field = $fea_instance->frontend->get_field( $response['field_key'] );

						if ( ! $host_field ) {
							wp_send_json_error( __( 'Post Added. No Field found to update.', 'frontend-admin' ) );
						}

						$field_class = acf()->fields->get_field_type( $host_field['type'] );

						$title = $field_class->get_post_title( get_post( $form['post_id'] ), $host_field );

						$response['post_info'] = array(
							'id'         => $form['post_id'],
							'text'       => $title,
							'action'     => $form['save_to_post'] == 'edit_post' ? 'edit' : 'add',
							'field_type' => $host_field['type'],
						);
					}

					
				}
			} else {
				$form['return'] = $this->get_redirect_url( $form );

				$response['location'] = 'current' == $form['redirect'] && 'other' != $form['message_location'] ? 'current' : 'other';
				
				// vars
				$return = acf_maybe_get( $form, 'return', '' );
				// redirect
				if ( $return ) {
					// update %placeholders%

					if ( isset( $form['record']['post'] ) ) {
						$return = str_replace( array( '%post_id%', '[post:id]' ), $form['record']['post'], $return );
						$return = str_replace( array( '%post_url%', '[post:url]' ), get_permalink( $form['record']['post'] ), $return );

					}


					$return = $fea_instance->dynamic_values->get_dynamic_values( $return, $form, true );

					$return = remove_query_arg( array( 'pagename' ), $return );

					$response['redirect'] = $return;

					
					foreach ( $types as $type ) {
						$response[ $type ] = $form[ $type . '_id' ] ?? false;
					}

					$response['frontend-form-nonce'] = wp_create_nonce( 'frontend-form' );

					$expiration_time = time() + 600;
					setcookie( 'admin_form_success', json_encode( $response ), $expiration_time, '/' );
					unset($response['frontend-form-nonce']);
				}
			}

			if ( isset( $submission_form ) ) {
				$form = $fea_instance->submissions_handler->get_form(
					$form['submission'],
					array(),
					1
				);
			}

			if ( isset( $form['redirect_action'] ) ) {
				if ( 'clear' == $form['redirect_action'] ) {
					foreach ( $types as $type ) {
						if ( isset( $form[ "save_to_$type" ] ) && $form[ "save_to_$type" ] == "new_$type" ) {
							$form[ $type . '_id' ]   = "add_$type";
							$form[ "save_to_$type" ] = "new_$type";
						}
					}
					unset( $form['submission'] );
				}elseif ( 'edit' == $form['redirect_action'] ) {
					$objects = array();
					foreach ( $types as $type ) {
						if ( isset( $form[ "save_to_$type" ] ) && $form[ "save_to_$type" ] == "edit_$type" ) {
							$form[ $type . '_id' ]   = $form['record'][ $type ];
							$form[ "save_to_$type" ] = "edit_$type";
							$objects[ $type ] = $form['record'][ $type ];
						}
					}
					$objects = json_encode( $objects );
					$objects = fea_encrypt( $objects );
					$response['objects'] = $objects;
					
				}else{
					$as_is = true;
				}
			}

			if( ! empty( $form['ajax_submit'] ) && empty( $as_is ) ){
				ob_start();
				$form['scripts_loaded'] = true;
				if( $form['show_in_modal'] ){
					$form['show_in_modal'] = false;
				}
				$fea_instance->form_display->render_form( $form );
				$response['reload_form'] = ob_get_clean();
			}


			do_action( 'frontend_admin/form/after_submit', $form, $response );
			do_action( 'fea_after_submit', $form );

			wp_send_json_success( $response );
		}

		public function reload_form( $form ) {
			$types = array( 'post', 'user', 'term', 'product' );

			$objects = array();

			foreach ( $types as $type ) {
				if ( ! empty( $form['record'][ $type ] ) ) {
					$form[ $type . '_id' ]   = $form['record'][ $type ];
					$form[ "save_to_$type" ] = "edit_$type";
					$objects[ $type ] = $form['record'][ $type ];
				}
			}
			
			$form = $this->save_record( $form );

			$objects['submission'] = $form['submission'];

			$objects = json_encode( $objects );
			$objects = fea_encrypt( $objects );

			$preview = apply_filters( 'frontend_admin/form/submission_preview', false, $form );
	
			$form['return'] = $this->get_redirect_url( $form );
			$json                        = array(
				'location'     => 'current',
				'success_message' => __( 'Progress Saved', 'frontend-admin' ),				
				'form_element' => $form['id'],
				'objects' => $objects,
				'preview' => $preview,
			);
			$json['frontend-form-nonce'] = wp_create_nonce( 'frontend-form' );

			if ( isset( $_POST['_acf_message'] ) ) {
				$json['success_message'] = sanitize_textarea_field( $_POST['_acf_message'] );
			}
			if( empty( $form['show_update_message'] ) )
				$json['success_message'] = '';
		
			$expiration_time = time() + 600;
			setcookie( 'admin_form_success', json_encode( $json ), $expiration_time, '/' );
			wp_send_json_success( $json );
		}

		public function save_record( $form, $status = 'in_progress' ) {
			if ( isset( $form['no_cookies'] ) ) {
				unset( $form['no_cookies'] );
				$no_cookie = true;
			}
			if ( ! empty( $form['approval'] ) ) {
				$status = 'approved';
			}

			global $wpdb, $fea_instance;

			$title = isset( $form['submission_title'] ) ? $form['submission_title'] : $form['form_title'];

			$args = array(
				'fields' => fea_encrypt( json_encode( $form['record'] ) ),
				'user'   => get_current_user_id(),
				'status' => $status,
				'title'  => $title,
			);

			if ( empty( $form['submission'] ) ) {
				$args['created_at'] = current_time( 'mysql' );
				$args['form']       = 'admin_form' == get_post_type( $form['ID'] ) ? $form['ID'] : $form['id'];
				$form['submission'] = $fea_instance->submissions_handler->insert_submission( $args );
			} else {
				$fea_instance->submissions_handler->update_submission( $form['submission'], $args );
			}

			if ( empty( $no_cookie ) ) {
				$expiration_time = time() + 86400;
				setcookie( $form['id'], $form['submission'], $expiration_time, '/' );
			}
			return $form;

		}


		public function get_redirect_url( $form ) {
			if ( ! empty( $form['return'] ) ) {
				return $form['return'];
			}

			$redirect_url             = '';
			switch ( $form['redirect'] ) {
				case 'custom_url':
					if ( is_array( $form['custom_url'] ) ) {
						 $redirect_url = $form['custom_url']['url'];
					} else {
						$redirect_url = $form['custom_url'];
					}
					
					if( ! $redirect_url ) {
						$redirect_url = sanitize_url( $_POST['_acf_current_url'] ?? home_url() );
					}

					break;					
				case 'referer_url':
					$redirect_url = sanitize_url( $_POST['_acf_referer_url'] ?? home_url() );
					break;
				case 'post_url':
					$redirect_url = '%post_url%';
					break;
				case 'current':
					$redirect_url = sanitize_url( $_POST['_acf_current_url'] );
				break;
				default:
					$redirect_url = sanitize_url( $form['redirect'] );	
			}
			
			$redirect_url = apply_filters( 'frontend_admin/forms/redirect_url', $redirect_url, $form );

			return $redirect_url;
		}

		

		public function verify_email_address() {
			global $wp, $fea_instance;

            if( 'fea/verify-email' !== $wp->request  ) return;

			if ( isset( $_GET['submission'] ) && isset( $_GET['email-address'] ) && isset( $_GET['token'] ) ) {
				$request = feadmin_sanitize_array( $_GET );
			} else {
				echo __( 'Could not verify email at this time', 'frontend-admin' );
				exit;
			}

			$token = wp_verify_nonce( $request['token'], 'frontend-admin-verify-' . $request['email-address'] );

			if( ! $token ){
				echo __( 'Could not verify email at this time', 'frontend-admin' );
				exit;
			}

			$url = sanitize_url( $request['redirect'] );

			$address = $request['email-address'];


			$submission = $fea_instance->submissions_handler->get_submission( $request['submission'] );
			if ( empty( $submission->fields ) ) {
				wp_redirect( home_url( $url ) );
			}

			$record = json_decode( fea_decrypt( $submission->fields ), true );

			if ( isset( $record['emails_to_verify'][ $address ] ) ) {

				unset( $record['emails_to_verify'][ $address ] );
				$record['verified_emails'][ $address ] = $address;

				$emails_handler = $fea_instance->emails_handler ?? false;
				if ( $emails_handler ) {
					$emails_handler->approve_email( $address );
				}

				$args = array();
				if ( empty( $record['emails_to_verify'] ) ) {
					if ( $submission->status ) {
						$old_status = explode( ',', $submission->status );
						if ( ! in_array( 'require_approval', $old_status ) ) {
							   $form           = $fea_instance->form_display->get_form( $submission->form );
							   $form['record'] = $record;
							foreach ( $fea_instance->local_actions as $name => $action ) {
								$form = $action->run( $form );
							}
							$record = $form['record'];
						}
						$new_status = str_replace( 'verify_email', 'email_verified', $submission->status );
					}

					$args['status'] = $new_status;
				}

				$args['fields'] = fea_encrypt( json_encode( $record ) );
				$fea_instance->submissions_handler->update_submission( $request['submission'], $args );

				$response = array(
					'success_message' => __( 'Email Verified', 'frontend-admin' ),
					'location'        => 'other',
					'form_element'    => $submission->form,
					'frontend-form-nonce' => wp_create_nonce( 'frontend-form' ),
				);

				$expiration_time = time() + 600;
				setcookie( 'admin_form_success', json_encode( $response ), $expiration_time, '/' );

			}
			wp_redirect( $url );
			die;
		}

		public function change_submission_status( $submission_id, $status ) {
			global $fea_instance;

			$submission = $fea_instance->submissions_handler->get_submission( $submission_id );
			if ( ! $submission ) {
				return false;
			}

			$args = array(
				'status' => $status,
			);

			$fea_instance->submissions_handler->update_submission( $submission_id, $args );

			if ($status !== 'pending') {
				$form = $fea_instance->form_display->get_form($submission->form);
				$form['record'] = json_decode(fea_decrypt($submission->fields), true);
				foreach ($fea_instance->local_actions as $name => $action) {
					
					$form = $action->run($form);
					$submission->fields = fea_encrypt(json_encode($form['record']));
				}
				$fea_instance->submissions_handler->update_submission($submission_id, array('fields' => $submission->fields));

				$redirect_url = $this->get_redirect_url( $form );
				wp_redirect( $redirect_url );
			}

			return true;
		}

		public function __construct() {
			add_action( 'wp_ajax_frontend_admin/form_submit', array( $this, 'check_submit_form' ) );
			add_action( 'wp_ajax_nopriv_frontend_admin/form_submit', array( $this, 'check_submit_form' ) );
			add_action( 'wp_ajax_frontend_admin/forms/update_field', array( $this, 'check_inline_field' ) );
			add_action( 'wp_ajax_nopriv_frontend_admin/forms/update_field', array( $this, 'check_inline_field' ) );

			add_action( 'frontend_admin/submission/change_status', array( $this, 'change_submission_status' ), 10, 2 );

			add_action( 'wp', array( $this, 'verify_email_address' ) );
		}
	}

	fea_instance()->form_submit = new Submit_Form();

endif;



