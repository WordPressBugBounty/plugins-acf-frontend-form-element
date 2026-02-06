<?php
namespace Frontend_Admin\Field_Types;

if ( ! class_exists( 'upload_image' ) ) :

	class upload_image extends upload_file {



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
			$this->name     = 'upload_image';
			$this->label    = __( 'Upload Image', 'frontend-admin' );
			$this->public   = false;
			$this->defaults = array(
				'return_format' => 'array',
				'preview_size'  => 'thumbnail',
				'library'       => 'all',
				'min_width'     => 0,
				'min_height'    => 0,
				'min_size'      => 0,
				'max_width'     => 0,
				'max_height'    => 0,
				'max_size'      => 0,
				'resize_file'   => 0,
				'mime_types'    => '',
				'show_preview'  => 1,
				'show_preview_in' => false,
				'button_text'   => __( 'Add Image', 'frontend-admin' ),
				'no_file_text'  => __( 'No Image selected', 'frontend-admin' ),
			);

			// filters
			add_filter( 'get_media_item_args', array( $this, 'get_media_item_args' ) );

		}

		/*
		*  get_media_item_args
		*
		*  description
		*
		*  @type    function
		*  @date    15/11/2022
		*  @since   3.9.16
		*
		*  @param   $vars (array)
		*  @return  $vars
		*/

		function get_media_item_args( $vars ) {
			$vars['send'] = true;
			return( $vars );

		}



		/*
		*  input_admin_enqueue_scripts
		*
		*  description
		*
		*  @type    function
		*  @date    16/12/2015
		*  @since    5.3.2
		*
		*  @param    $post_id (int)
		*  @return    $post_id (int)
		*/

		function input_admin_enqueue_scripts() {
			// localize
			acf_localize_text(
				array(
					'Select Image' => __( 'Select Image', 'frontend-admin' ),
					'Edit Image'   => __( 'Edit Image', 'frontend-admin' ),
					'Update Image' => __( 'Update Image', 'frontend-admin' ),
					'All images'   => __( 'All', 'frontend-admin' ),
				)
			);
		}

		function upload_button_text_setting( $field ) {
			acf_render_field_setting(
				$field,
				array(
					'label'       => __( 'Button Text' ),
					'name'        => 'button_text',
					'type'        => 'text',
					'placeholder' => __( 'Add Image', 'frontend-admin' ),
				)
			);
			acf_render_field_setting(
				$field,
				array(
					'label'       => __( 'No File Text' ),
					'name'        => 'no_file_text',
					'type'        => 'text',
					'placeholder' => __( 'No File Selected', 'frontend-admin' ),
				)
			);
		}

		/*
		* preapare_field
		*  This filter is appied to the $field before it is rendered
		*  @type    filter
		*  @since    3.6
		*  @date    23/01/13
		*  @param    $field (array) the field array holding all the field options
		*  @return    $field
		*/	
		function prepare_field( $field ) {
			// set defaults
			$field = array_merge( $this->defaults, $field );

			// return
			return $field;
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
			if ( empty( $field['field_type'] ) ) {
				$field['field_type'] = 'image';
			}
			if ( empty( $field['preview_size'] ) ) {
				$field['preview_size'] = 'thumbnail';
			}

			// vars
			$uploader = acf_get_setting( 'uploader' );

			$value = $field['value'];
			// enqueue
			if ( $uploader == 'wp' && ! feadmin_edit_mode() ) {
				acf_enqueue_uploader();
			}

			// vars
			$url = '';
			$alt = '';
			$div = array(
				'class'              => 'acf-' . $field['field_type'] . '-uploader',
				'data-preview_size'  => $field['preview_size'],
				'data-library'       => $field['library'],
				'data-mime_types'    => $field['mime_types'],
				'data-uploader'      => $uploader,
				'data-resize'        => $field['resize_file'] ?? 0,
				'data-min_size'      => $field['min_size'],
				'data-min_width'     => $field['min_width'],
				'data-min_height'    => $field['min_height'],
				'data-max_size'      => $field['max_size'],
				'data-max_width'     => $field['max_width'],
				'data-max_height'    => $field['max_height'],
			);

			
			$show_preview = $field['show_preview'] ?? true;
			$preview_element = $field['show_preview_in'] ?? false;

			if( $show_preview ){
				$div['class'] .= ' show-preview';
			}

			if( $preview_element ){
				$div['data-preview_element'] = $preview_element;
			}

			$div['data-preview_type'] = $field['preview_type'] ?? 'img';


			// has value?
			if ( $value ) {
				// update vars
				$url = wp_get_attachment_image_src( $value, $field['preview_size'] );
				$alt = get_post_meta( $value, '_wp_attachment_image_alt', true );

				// url exists
				if ( $url ) {
					$url = $url[0];
				}

				// url exists
				if ( $url ) {
					$div['class'] .= ' has-value';
				}
			} else {
				$url = wp_mime_type_icon( 'image/png' );
			}

			// get size of preview value
			$size = acf_get_image_size( $field['preview_size'] );

			?>
<div <?php acf_esc_attr_e( $div ); ?>>
			<?php
		
				acf_hidden_input(
					array(
						'data-name' => 'id',
						'name'      => $field['name'],
						'value'     => $value,
					)
				);
			?>

	<?php if( $show_preview ): ?>
	<div class="show-if-value image-wrap" 
			<?php
			if ( $size['width'] ) :
				?>
			style="<?php echo esc_attr( 'max-width: ' . $size['width'] . 'px' ); ?>"
																			<?php
			endif;
						?>
			>
			<?php
			if ( $uploader != 'basic' ) {
				$edit = 'edit';
			} else {
				$edit = 'edit-preview';
			}
			?>
		<img data-name="image" src="<?php echo esc_url( $url ); ?>" alt="<?php echo esc_attr( $alt ); ?>"/>
		<div class="acf-actions -hover">
			<a class="acf-icon -pencil dark" data-name="<?php esc_attr_e( $edit ); ?>" href="#" title="<?php esc_attr_e( 'Edit', 'frontend-admin' ); ?>"></a>
			<a class="acf-icon -cancel dark" data-name="remove" href="#" title="<?php esc_attr_e( 'Remove', 'frontend-admin' ); ?>"></a>
		</div>
	</div>
	<div class="hide-if-value">
			<?php
	endif;
			
			if ( $uploader == 'basic' ) :
				?>
			<label class="acf-basic-uploader file-drop">
				<?php
				$input_args = array(
					'name'  => 'file_input',
					'id'    => $field['id'],
					'class' => 'image-preview',
				);
				$input_args['accept'] = $this->get_accept_value( $field );
				
				acf_file_input( $input_args );
				?>
				<div class="file-custom">
				<?php $this->render_button( $field ); ?>
				</div>
			</label>
				<?php
				$prefix = 'acff[file_data][' . $field['key'] . ']';
				fea_instance()->form_display->render_meta_fields( $prefix, $value );
				?>
	<?php else : 
		$this->render_button( $field );		
	endif; ?>
			
	<?php if( $show_preview ): ?>
	</div>
	<?php endif; ?>
	<?php
	if ( 'basic' == $uploader ) {
		?>
		<div class="frontend-admin-hidden uploads-progress"><div class="percent">0%</div><div class="bar"></div></div>
		<?php
	}
	?>
</div>
			<?php

		}

		/* 
		* button display
		*
		*
		*/
		public function render_button( $field ){
			$empty_text = $field['no_file_text']?? __( 'No file selected', 'frontend-admin' );
			$button_text = $field['button_text'] ?? __( 'Add Image', 'frontend-admin' );

			if( ! $button_text ){
				$button_text = __( 'Add Image', 'frontend-admin' );
			}

			?>
			<p><?php echo esc_html_e( $empty_text ); ?>
				<a data-name="add" class="acf-button button" href="#">
					<?php esc_html_e( $button_text ); ?>
					<?php if( ! empty( $field['button_icon'] ) ){
						echo '<i class="' . esc_attr( $field['button_icon'] ) . '"></i>';
					} ?>
				</a>
			</p>
		<?php }


		/*
		*  render_field_settings()
		*
		*  Create extra options for your field. This is rendered when editing a field.
		*  The value of $field['name'] can be used (like bellow) to save extra data to the $field
		*
		*  @type    action
		*  @since    3.6
		*  @date    23/01/13
		*
		*  @param    $field    - an array holding all the field's data
		*/

		function render_field_settings( $field ) {
			// clear numeric settings
			$clear = array(
				'min_width',
				'min_height',
				'min_size',
				'max_width',
				'max_height',
				'max_size',
			);

			foreach ( $clear as $k ) {

				if ( empty( $field[ $k ] ) ) {

					$field[ $k ] = '';

				}
			}

			acf_render_field_setting(
				$field,
				array(
					'label'        => __( 'Default Value', 'frontend-admin' ),
					'instructions' => __( 'Appears when creating a new post', 'frontend-admin' ),
					'type'         => 'image',
					'name'         => 'default_value',
				)
			);
			// return_format
			acf_render_field_setting(
				$field,
				array(
					'label'        => __( 'Return Value', 'frontend-admin' ),
					'instructions' => __( 'Specify the returned value on front end', 'frontend-admin' ),
					'type'         => 'radio',
					'name'         => 'return_format',
					'layout'       => 'horizontal',
					'choices'      => array(
						'array' => __( 'Image Array', 'frontend-admin' ),
						'url'   => __( 'Image URL', 'frontend-admin' ),
						'id'    => __( 'Image ID', 'frontend-admin' ),
					),
				)
			);


			// show_preview
			acf_render_field_setting(
				$field,
				array(
					'label'        => __( 'Show Preview', 'frontend-admin' ),
					'instructions' => __( 'Show a preview of the image', 'frontend-admin' ),
					'type'         => 'true_false',
					'name'         => 'show_preview',
					'ui'           => 1,
				)
			);

			// show preview in element by selector
			acf_render_field_setting(
				$field,
				array(
					'label'        => __( 'Preview Image In...', 'frontend-admin' ),
					'instructions' => __( 'Show a preview of the image in the element with this selector (Include "." for css class or "#" for css ids)', 'frontend-admin' ),
					'type'         => 'text',
					'placeholder'  => '.my-image-preview',
					'name'         => 'show_preview_in',
				)
			);

			// img tag or background image
			acf_render_field_setting(
				$field,
				array(
					'label'        => __( 'Image Tag', 'frontend-admin' ),
					'instructions' => __( 'Choose how the image is displayed', 'frontend-admin' ),
					'type'         => 'radio',
					'name'         => 'preview_type',
					'choices'      => array(
						'img'       => __( 'Image Tag', 'frontend-admin' ),
						'background' => __( 'Background Image', 'frontend-admin' ),
					),
				)
			);

			// preview_size
			acf_render_field_setting(
				$field,
				array(
					'label'        => __( 'Preview Size', 'frontend-admin' ),
					'instructions' => __( 'Shown when entering data', 'frontend-admin' ),
					'type'         => 'select',
					'name'         => 'preview_size',
					'choices'      => acf_get_image_sizes(),
					'conditional_logic' => array(
						array(
							array(
								'field'    => 'show_preview',
								'operator' => '==',
								'value'    => true,
							),
						),
					),
				)
			);


			// library
			acf_render_field_setting(
				$field,
				array(
					'label'        => __( 'Library', 'frontend-admin' ),
					'instructions' => __( 'Limit the media library choice', 'frontend-admin' ),
					'type'         => 'radio',
					'name'         => 'library',
					'layout'       => 'horizontal',
					'choices'      => array(
						'all'        => __( 'All', 'frontend-admin' ),
						'uploadedTo' => __( 'Uploaded to post', 'frontend-admin' ),
						'uploadedUser' => __( 'Uploaded by current user', 'frontend-admin' ),
					),
				)
			);

			// min
			acf_render_field_setting(
				$field,
				array(
					'label'        => __( 'Minimum', 'frontend-admin' ),
					'instructions' => __( 'Restrict which images can be uploaded', 'frontend-admin' ),
					'type'         => 'text',
					'name'         => 'min_width',
					'prepend'      => __( 'Width', 'frontend-admin' ),
					'append'       => 'px',
				)
			);

			acf_render_field_setting(
				$field,
				array(
					'label'   => '',
					'type'    => 'text',
					'name'    => 'min_height',
					'prepend' => __( 'Height', 'frontend-admin' ),
					'append'  => 'px',
					'_append' => 'min_width',
				)
			);

			acf_render_field_setting(
				$field,
				array(
					'label'   => '',
					'type'    => 'text',
					'name'    => 'min_size',
					'prepend' => __( 'Image size', 'frontend-admin' ),
					'append'  => 'MB',
					'_append' => 'min_width',
				)
			);

			// max
			acf_render_field_setting(
				$field,
				array(
					'label'        => __( 'Maximum', 'frontend-admin' ),
					'instructions' => __( 'Restrict which images can be uploaded', 'frontend-admin' ),
					'type'         => 'text',
					'name'         => 'max_width',
					'prepend'      => __( 'Width', 'frontend-admin' ),
					'append'       => 'px',
				)
			);

			acf_render_field_setting(
				$field,
				array(
					'label'   => '',
					'type'    => 'text',
					'name'    => 'max_height',
					'prepend' => __( 'Height', 'frontend-admin' ),
					'append'  => 'px',
					'_append' => 'max_width',
				)
			);

			acf_render_field_setting(
				$field,
				array(
					'label'   => '',
					'type'    => 'text',
					'name'    => 'max_size',
					'prepend' => __( 'Image size', 'frontend-admin' ),
					'append'  => 'MB',
					'_append' => 'max_width',
				)
			);

			//resize or throw error
			acf_render_field_setting(
				$field,
				array(
					'label'        => __( 'Resize Image or Throw Error', 'frontend-admin' ),
					'instructions' => __( 'Resize the image to fit within the maximum dimensions', 'frontend-admin' ),
					'type'         => 'true_false',
					'name'         => 'resize_file',
					'ui'           => 1,
					'ui_on_text'   => __( 'Resize', 'frontend-admin' ),
					'ui_off_text'  => __( 'Throw Error', 'frontend-admin' ),
				)
			);

		/* 	$local_avatar = get_option('local_avatar','');

			//set as global Local Avatar field
			acf_render_field_setting(
				$field,
				array(
					'label' 	  => __( 'Local Avatar', 'frontend-admin' ),
					'name'		  => 'local_avatar',
					'type'        => 'true_false',
					'ui' 		  => 1,
					'value' 	  => $field['key'] == $local_avatar
				)
			);
		 */

		}


		/*
		*  format_value()
		*
		*  This filter is appied to the $value after it is loaded from the db and before it is returned to the template
		*
		*  @type    filter
		*  @since    3.6
		*  @date    23/01/13
		*
		*  @param    $value (mixed) the value which was loaded from the database
		*  @param    $post_id (mixed) the $post_id from which the value was loaded
		*  @param    $field (array) the field array holding all the field options
		*
		*  @return    $value (mixed) the modified value
		*/

		function format_value( $value, $post_id, $field ) {
			// bail early if no value
			if ( empty( $value ) ) {
				return false;
			}

			// bail early if not numeric (error message)
			if ( ! is_numeric( $value ) ) {
				return false;
			}

			// convert to int
			$value = intval( $value );

			// format
			if ( $field['return_format'] == 'url' ) {

				return wp_get_attachment_url( $value );

			} elseif ( $field['return_format'] == 'array' ) {

				return acf_get_attachment( $value );

			}

			// return
			return $value;

		}


	}




endif; // class_exists check

?>
