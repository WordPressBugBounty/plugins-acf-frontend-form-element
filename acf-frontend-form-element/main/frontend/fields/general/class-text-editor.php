<?php
namespace Frontend_Admin\Field_Types;

if ( ! class_exists( 'text_editor' ) ) :

	class text_editor extends Field_Base {



		/*
		*  __construct
		*
		*  This function will setup the field type data
		*
		*  @type    function
		*  @date    5/03/2014
		*  @since   5.0.0
		*
		*  @param   n/a
		*  @return  n/a
		*/

		function initialize() {
			// vars
			$this->name     = 'text_editor';
			$this->label    = __( 'Text Editor', 'frontend-admin' );
			$this->category = 'content';
			$this->public   = true;
			$this->defaults = array(
				'tabs'          => 'all',
				'toolbar'       => 'full',
				'media_upload'  => 1,
				'default_value' => '',
				'delay'         => 0,
			);

	

			add_action( 'acf/prepare_field/type=wysiwyg', array( $this, 'prepare_field' ) );
		}

		/*
		*  prepare_field()
		*
		*  Prepares field setting prior to rendering field in form
		*
		*  @param    $field - an array holding all the field's data
		*  @return    $field
		*
		*  @type    action
		*  @since    3.6
		*  @date    23/01/13
		*/

		function prepare_field( $field ) {
			$field = array_merge( $this->defaults, $field );

			if( feadmin_edit_mode() ){
				$field['type'] = 'textarea';
				$field['rows'] = 14;
			}
			return $field;
		}


	
		

	
		/**
		 * Create the HTML interface for your field
		 *
		 * @param array $field An array holding all the field's data
		 *
		 * @type  action
		 * @since 3.6
		 * @date  23/01/13
		 */
		function render_field( $field ) {

			$js_deps = include_once FEA_DIR . '/assets/build/frontend-text-editor/index.asset.php';
			$js_url = FEA_URL . 'assets/build/frontend-text-editor/index.js';

			if( is_array( $js_deps ) ){
			
				$js_deps['dependencies'][] = 'wp-tinymce';

				wp_enqueue_script( 'fea-text-editor', $js_url, $js_deps['dependencies'], $js_deps['version'], true );
				wp_enqueue_style( 'fea-text-editor', FEA_URL . '/assets/build/frontend-text-editor.css', array(), $js_deps['version'] );
				
				wp_add_inline_script( 'fea-text-editor', 'window.feaTextEditorSettings = ' . wp_json_encode( array(
					'content' => $field['value'],
					//'toolbar' => $field['toolbar'],
					'rows'    => isset( $field['rows'] ) ? $field['rows'] : '',
					'maxlength'    => isset( $field['maxlength'] ) ? $field['maxlength'] : '',
				) ) . ';', 'before' );
			}

			echo '
			<div
			class="fea-wysiwyg-frontend">
				'. $field['value'] .'
			</div>
			<textarea class="fea-wysiwyg-frontend-textarea" name="' . esc_attr( $field['name'] ) . '" style="display:none;">' . esc_textarea( $field['value'] ) . '</textarea>
			';

		
		}


		/*
		*  render_field_settings()
		*
		*  Create extra options for your field. This is rendered when editing a field.
		*  The value of $field['name'] can be used (like bellow) to save extra data to the $field
		*
		*  @type    action
		*  @since   3.6
		*  @date    23/01/13
		*
		*  @param   $field  - an array holding all the field's data
		*/
		function render_field_settings( $field ) {
			acf_render_field_setting(
				$field,
				array(
					'label'        => __( 'Default Value', 'acf' ),
					'instructions' => __( 'Appears when creating a new post', 'acf' ),
					'type'         => 'textarea',
					'name'         => 'default_value',
				)
			);

			acf_render_field_setting(
				$field,
				array(
					'label'        => __( 'Delay initialization?', 'acf' ),
					'instructions' => __( 'TinyMCE will not be initialized until field is clicked', 'acf' ),
					'name'         => 'delay',
					'type'         => 'true_false',
					'ui'           => 1,
					'conditions'   => array(
						'field'    => 'tabs',
						'operator' => '!=',
						'value'    => 'text',
					),
				)
			);

		}

	
		/**
		 * This filter is applied to the $value after it is loaded from the db, and before it is returned to the template
		 *
		 * @type  filter
		 * @since 3.6
		 * @date  23/01/13
		 *
		 * @param mixed $value   The value which was loaded from the database
		 * @param mixed $post_id The $post_id from which the value was loaded
		 * @param array $field   The field array holding all the field options
		 *
		 * @return mixed $value The modified value
		 */
		function format_value( $value, $post_id, $field ) {
			 // Bail early if no value or not a string.
			if ( empty( $value ) || ! is_string( $value ) ) {
				return $value;
			}

			$value = apply_filters( 'acf_the_content', $value );

			// Follow the_content function in /wp-includes/post-template.php
			return str_replace( ']]>', ']]&gt;', $value );
		}

	}




endif; // class_exists check

?>
