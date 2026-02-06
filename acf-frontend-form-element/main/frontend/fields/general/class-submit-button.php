<?php
namespace Frontend_Admin\Field_Types;

if ( ! class_exists( 'submit_button' ) ) :

	class submit_button extends Field_Base {



		/*
		*  __construct
		*
		*  This function will setup the field type data
		*
		*  @type    function
		*  @date    5/03/2014
		*  @since    5.0.0
		*
		*  @param    n/a
		*  @return    n/a
		*/

		function initialize() {
			// vars
			$this->name     = 'submit_button';
			$this->label    = __( 'Submit Button', 'frontend-admin' );
			$this->category = __( 'Form', 'frontend-admin' );
			$this->defaults = array(
				'button_text'      => __( 'Submit', 'frontend-admin' ),
				'field_label_hide' => 1,
				'redirect'         => '',
				'custom_url'       => '',
			);

		}


		/*
		*  render_field()
		*
		*  Create the HTML interface for your field
		*
		*  @param    $field - an array holding all the field's data
		*
		*  @type    action
		*  @since    3.6
		*  @date    23/01/13
		*/

		function render_field( $field ) {
			$redirect = $field['redirect'] ?? '';
			
			if( 'custom_url' == $redirect ){
				$redirect = $field['custom_url'] ?? '';
			} 

			$state = $field['submit_type'] ?? 'publish';
			$success_message = $field['success_message'] ?? '';

			// vars
			$m = '<button 
				type="button" 
				class="fea-submit-button button button-primary" 
				data-state="' . $state . '"
				data-success="' . $success_message . '"
				data-redirect="' . $redirect . '"
			>' 
			. $field['button_text'] . 
			'</button>';

			// wptexturize (improves "quotes")
			$m = wptexturize( $m );

			echo wp_kses_post( $m );
		}


		/*
		*  load_field()
		*
		*  This filter is appied to the $field after it is loaded from the database
		*
		*  @type    filter
		*  @since    3.6
		*  @date    23/01/13
		*
		*  @param    $field - the field array holding all the field options
		*
		*  @return    $field - the field array holding all the field options
		*/
		function load_field( $field ) {
			 // remove name to avoid caching issue
			$field['name'] = '';

			// remove instructions
			$field['instructions'] = '';

			// remove required to avoid JS issues
			$field['required'] = 0;

			// set value other than 'null' to avoid ACF loading / caching issue
			$field['value'] = false;

			$field['field_label_hide'] = 1;

			if ( empty( $field['button_text'] ) ) {
				$field['button_text'] = $field['label'];
			}

			// return
			return $field;
		}

		function render_field_settings( $field ) {
			acf_render_field_setting(
				$field,
				array(
					'label' => __( 'Button Text', 'frontend-admin' ),
					'type'  => 'text',
					'name'  => 'button_text',
					'class' => 'update-label',
				)
			);
			// redirect
			$redirect_options = array(
				''			 => __( 'Form Default', 'frontend-admin' ),
				'current'    => __( 'Reload Current Page', 'frontend-admin' ),
				'custom_url' => __( 'Custom URL', 'frontend-admin' ),
				'referer'    => __( 'Referer', 'frontend-admin' ),
				'post_url'   => __( 'Post URL', 'frontend-admin' ),
				'none'       => __( 'None', 'frontend-admin' ),
			);
			$redirect_options = apply_filters( 'frontend_admin/forms/redirect_options', $redirect_options );

			acf_render_field_setting(
				$field,
				array(
					'label'         => __( 'Redirect After Submit', 'frontend-admin' ),
					'type'          => 'select',
					'name'          => 'redirect',
					'choices'       => $redirect_options,
					'allow_null'    => 0,
					'multiple'      => 0,
					'ui'            => 0,
					'return_format' => 'value',
					'ajax'          => 0,
					'placeholder'   => '',
				)
			);

			//custom url
			acf_render_field_setting(
				$field,
				array(
					'label'             => __( 'Custom Url', 'frontend-admin' ),
					'type'              => 'url',
					'name'              => 'custom_url',
					'conditional_logic' => array(
						array(
							array(
								'field'    => 'redirect',
								'operator' => '==',
								'value'    => 'custom_url',
							),
						),
					),
					'placeholder'       => '',
				)
			);

			//type
			acf_render_field_setting(
				$field,
				array(
					'label'        => __( 'Button Type', 'frontend-admin' ),
					'instructions' => __( 'The type of button to display.', 'frontend-admin' ),
					'type'         => 'select',
					'name'         => 'submit_type',
					'choices'      => array(
						'submit' => __( 'Submit', 'frontend-admin' ),
						'save'   => __( 'Save Progress', 'frontend-admin' ),
					),
					'default_value' => 'submit',
				)
			);

			//success message
			acf_render_field_setting(
				$field,
				array(
					'label'             => __( 'Success Message', 'frontend-admin' ),
					'type'              => 'text',
					'name'              => 'success_message',
					'placeholder'       => '',
				)
			);

		}

	}




endif; // class_exists check


