<?php
namespace Frontend_Admin\Field_Types;

if ( ! class_exists( 'text' ) ) :

	class text extends Field_Base {

		/*
		*  initialize
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
			$this->name     = 'text';
			$this->label    = __( 'Text', 'frontend-admin' );
			  $this->public = false;
			$this->defaults = array(
				'default_value' => '',
				'maxlength'     => '',
				'placeholder'   => '',
				'prepend'       => '',
				'append'        => '',
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
			$html = '';

			$field = wp_parse_args( $field, [
				'prepend' => '',
				'append' => '',
			] );

			// Prepend text.
			if ( $field['prepend'] !== '' ) {
				$field['class'] .= ' acf-is-prepended';
			}

			// Append text.
			if ( $field['append'] !== '' ) {
				$field['class'] .= ' acf-is-appended';
			}

			// Input.
			$input_attrs = array( 'type' => $this->name );
			$attr_keys   = array( 'id', 'class', 'value', 'placeholder', 'maxlength', 'pattern', 'readonly', 'disabled', 'required' );

			if ( empty( $field['sensitive'] ) ) {
				$attr_keys[] = 'name';
			}
			if ( ! empty( $field['no_autocomplete'] ) ) {
				$input_attrs['autocomplete'] = 'no';
			}

			if ( ! empty( $field['input_data'] ) ) {
				foreach ( $field['input_data'] as $k => $data ) {
					$input_attrs[ 'data-' . $k ] = $data;
				}
			}

			foreach ( $attr_keys as $k ) {
				if ( isset( $field[ $k ] ) ) {
					$input_attrs[ $k ] = $field[ $k ];
				}
			}

			if( $field['prepend'] ){
			?>
			<div class="acf-input-prepend"><?php echo acf_esc_html( $field['prepend'] ); ?></div>
			<?php } ?>
			<div class="acf-input-wrap"><?php echo acf_text_input( acf_filter_attrs( $input_attrs ) ); ?></div>
			<?php if( $field['append'] ){ ?>
			<div class="acf-input-append"><?php echo acf_esc_html( $field['append'] ); ?></div>
			<?php }
		}


		/*
		*  render_field_settings()
		*
		*  Create extra options for your field. This is rendered when editing a field.
		*  The value of $field['name'] can be used (like bellow) to save extra data to the $field
		*
		*  @param    $field    - an array holding all the field's data
		*
		*  @type    action
		*  @since    3.6
		*  @date    23/01/13
		*/

		function render_field_settings( $field ) {
			// default_value
			acf_render_field_setting(
				$field,
				array(
					'label'                 => __( 'Default Value', 'frontend-admin' ),
					'instructions'          => __( 'Appears when creating a new post', 'frontend-admin' ),
					'type'                  => 'text',
					'name'                  => 'default_value',
					'dynamic_value_choices' => 1,
				)
			);

			// placeholder
			acf_render_field_setting(
				$field,
				array(
					'label'        => __( 'Placeholder Text', 'frontend-admin' ),
					'instructions' => __( 'Appears within the input', 'frontend-admin' ),
					'type'         => 'text',
					'name'         => 'placeholder',
				)
			);

			// prepend
			acf_render_field_setting(
				$field,
				array(
					'label'        => __( 'Prepend', 'frontend-admin' ),
					'instructions' => __( 'Appears before the input', 'frontend-admin' ),
					'type'         => 'text',
					'name'         => 'prepend',
				)
			);

			// append
			acf_render_field_setting(
				$field,
				array(
					'label'        => __( 'Append', 'frontend-admin' ),
					'instructions' => __( 'Appears after the input', 'frontend-admin' ),
					'type'         => 'text',
					'name'         => 'append',
				)
			);

			// maxlength
			acf_render_field_setting(
				$field,
				array(
					'label'        => __( 'Character Limit', 'frontend-admin' ),
					'instructions' => __( 'Leave blank for no limit', 'frontend-admin' ),
					'type'         => 'number',
					'name'         => 'maxlength',
				)
			);

		}

		/**
		 * validate_value
		 *
		 * Validates a field's value.
		 *
		 * @date  29/1/19
		 * @since 5.7.11
		 *
		 * @param  (bool|string) Whether the value is vaid or not.
		 * @param  mixed                                          $value The field value.
		 * @param  array                                          $field The field array.
		 * @param  string                                         $input The HTML input name.
		 * @return (bool|string)
		 */
		function validate_value( $valid, $value, $field, $input ) {
			// Check maxlength
			if ( ! empty( $field['maxlength'] ) && ( acf_strlen( $value ) > $field['maxlength'] ) ) {
				return sprintf( __( 'Value must not exceed %d characters', 'frontend-admin' ), $field['maxlength'] );
			}

			// Return.
			return $valid;
		}
	}




endif; // class_exists check


