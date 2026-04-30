<?php
namespace Frontend_Admin\Field_Types;

if ( ! class_exists( 'post_content' ) ) :

	class post_content extends Field_Base {



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
			$this->name       = 'post_content';
			$this->label      = __( 'Content', 'frontend-admin' );
			  $this->category = __( 'Post', 'frontend-admin' );

			$this->defaults   = array(
				'data_name'     => 'content',
				'field_type'    => 'wysiwyg',
				'tabs'          => 'all',
				'toolbar'       => 'full',
				'media_upload'  => 1,
				'default_value' => '',
				'delay'         => 0,
				'new_lines'     => '',
				'maxlength'     => '',
				'placeholder'   => '',
				'rows'          => '',
			);
			add_filter( 'acf/pre_update_value/type=' . $this->name, array( $this, 'pre_update_value' ), 9, 4 );
	// add acf_the_content filters
			$this->add_filters();

			// actions
			add_action( 'acf/enqueue_uploader', array( $this, 'acf_enqueue_uploader' ) );
		}

	/**
		 * This function will add filters to 'acf_the_content'
		 *
		 * @type    function
		 * @date    20/09/2016
		 * @since   5.4.0
		 *
		 * @param   n/a
		 * @return  n/a
		 */
		function add_filters() {

			// WordPress 5.5 introduced new function for applying image tags.
			$wp_filter_content_tags = function_exists( 'wp_filter_content_tags' ) ? 'wp_filter_content_tags' : 'wp_make_content_images_responsive';

			// Mimic filters added to "the_content" in "wp-includes/default-filters.php".
			add_filter( 'acf_the_content', 'capital_P_dangit', 11 );
			// add_filter( 'acf_the_content', 'do_blocks', 9 ); Not yet supported.
			add_filter( 'acf_the_content', 'wptexturize' );
			add_filter( 'acf_the_content', 'convert_smilies', 20 );
			add_filter( 'acf_the_content', 'wpautop' );
			add_filter( 'acf_the_content', 'shortcode_unautop' );
			// add_filter( 'acf_the_content', 'prepend_attachment' ); Causes double image on attachment page.
			add_filter( 'acf_the_content', $wp_filter_content_tags );
			add_filter( 'acf_the_content', 'do_shortcode', 11 );

			// Mimic filters added to "the_content" in "wp-includes/class-wp-embed.php"
			if ( isset( $GLOBALS['wp_embed'] ) ) {
				add_filter( 'acf_the_content', array( $GLOBALS['wp_embed'], 'run_shortcode' ), 8 );
				add_filter( 'acf_the_content', array( $GLOBALS['wp_embed'], 'autoembed' ), 8 );
			}
		}


		/**
		 * This function will return an array of toolbars for the WYSIWYG field
		 *
		 * @type    function
		 * @date    18/04/2014
		 * @since   5.0.0
		 *
		 * @param   n/a
		 * @return  (array)
		 */
		function get_toolbars() {

			// vars
			$editor_id = 'acf_content';
			$toolbars  = array();

			// mce buttons (Full)
			$mce_buttons   = array( 'formatselect', 'bold', 'italic', 'bullist', 'numlist', 'blockquote', 'alignleft', 'aligncenter', 'alignright', 'link', 'wp_more', 'spellchecker', 'fullscreen', 'wp_adv' );
			$mce_buttons_2 = array( 'strikethrough', 'hr', 'forecolor', 'pastetext', 'removeformat', 'charmap', 'outdent', 'indent', 'undo', 'redo', 'wp_help' );

			// mce buttons (Basic)
			$teeny_mce_buttons = array( 'bold', 'italic', 'underline', 'blockquote', 'strikethrough', 'bullist', 'numlist', 'alignleft', 'aligncenter', 'alignright', 'undo', 'redo', 'link', 'fullscreen' );

			// Full
			$toolbars['Full'] = array(
				1 => apply_filters( 'mce_buttons', $mce_buttons, $editor_id ),
				2 => apply_filters( 'mce_buttons_2', $mce_buttons_2, $editor_id ),
				3 => apply_filters( 'mce_buttons_3', array(), $editor_id ),
				4 => apply_filters( 'mce_buttons_4', array(), $editor_id ),
			);

			// Basic
			$toolbars['Basic'] = array(
				1 => apply_filters( 'teeny_mce_buttons', $teeny_mce_buttons, $editor_id ),
			);

			// Filter for 3rd party
			$toolbars = apply_filters( 'acf/fields/wysiwyg/toolbars', $toolbars );

			// return
			return $toolbars;
		}


		/**
		 * Registers toolbars data for the WYSIWYG field.
		 *
		 * @type    function
		 * @date    16/12/2015
		 * @since   5.3.2
		 *
		 * @param   void
		 * @return  void
		 */
		function acf_enqueue_uploader() {

			// vars
			$data     = array();
			$toolbars = $this->get_toolbars();

			// loop
			if ( $toolbars ) {
				foreach ( $toolbars as $label => $rows ) {

					// vars
					$key = $label;
					$key = sanitize_title( $key );
					$key = str_replace( '-', '_', $key );

					// append
					$data[ $key ] = array();

					if ( $rows ) {
						foreach ( $rows as $i => $row ) {
							$data[ $key ][ $i ] = implode( ',', $row );
						}
					}
				}
			}

			// localize
			acf_localize_data(
				array(
					'toolbars' => $data,
				)
			);
		}

		/**
		 * Create the HTML interface for your field
		 *
		 * @param array $field An array holding all the field's data
		 *
		 * @type    action
		 * @since   3.6
		 * @date    23/01/13
		 */
		function render_field( $field ) {

			// enqueue
			acf_enqueue_uploader();

			// vars
			$id             = uniqid( 'acf-editor-' );
			$default_editor = 'html';
			$show_tabs      = true;

			// get height
			$height = acf_get_user_setting( 'wysiwyg_height', 300 );
			$height = max( $height, 300 ); // minimum height is 300

			// detect mode
			if ( ! user_can_richedit() ) {
				$show_tabs = false;
			} elseif ( $field['tabs'] == 'visual' ) {

				// case: visual tab only
				$default_editor = 'tinymce';
				$show_tabs      = false;
			} elseif ( $field['tabs'] == 'text' ) {

				// case: text tab only
				$show_tabs = false;
			} elseif ( wp_default_editor() == 'tinymce' ) {

				// case: both tabs
				$default_editor = 'tinymce';
			}

			// must be logged in to upload
			if ( ! current_user_can( 'upload_files' ) ) {
				$field['media_upload'] = 0;
			}

			// mode
			$switch_class = ( $default_editor === 'html' ) ? 'html-active' : 'tmce-active';

			// filter
			add_filter( 'acf_the_editor_content', 'format_for_editor', 10, 2 );

			$field['value'] = is_string( $field['value'] ) ? $field['value'] : '';
			$field['value'] = apply_filters( 'acf_the_editor_content', $field['value'], $default_editor );

			// attr
			$wrap = array(
				'id'           => 'wp-' . $id . '-wrap',
				'class'        => 'acf-editor-wrap wp-core-ui wp-editor-wrap ' . $switch_class,
				'data-toolbar' => $field['toolbar'],
			);

			// delay
			if ( $field['delay'] ) {
				$wrap['class'] .= ' delay';
			}

			// vars
			$textarea = acf_get_textarea_input(
				array(
					'id'    => $id,
					'class' => 'wp-editor-area',
					'name'  => $field['name'],
					'style' => $height ? "height:{$height}px;" : '',
					'value' => '%s',
				)
			);

			?>
		<div <?php echo acf_esc_attrs( $wrap ); ?>>
			<div id="wp-<?php echo esc_attr( $id ); ?>-editor-tools" class="wp-editor-tools hide-if-no-js">
				<?php if ( $field['media_upload'] ) : ?>
				<div id="wp-<?php echo esc_attr( $id ); ?>-media-buttons" class="wp-media-buttons">
					<?php
					if ( ! function_exists( 'media_buttons' ) ) {
						require ABSPATH . 'wp-admin/includes/media.php';
					}
					do_action( 'media_buttons', $id );
					?>
				</div>
				<?php endif; ?>
					<?php if ( user_can_richedit() && $show_tabs ) : ?>
					<div class="wp-editor-tabs">
						<button id="<?php echo esc_attr( $id ); ?>-tmce" class="wp-switch-editor switch-tmce" data-wp-editor-id="<?php echo esc_attr( $id ); ?>" type="button"><?php esc_html_e( 'Visual', 'acf' ); ?></button>
						<button id="<?php echo esc_attr( $id ); ?>-html" class="wp-switch-editor switch-html" data-wp-editor-id="<?php echo esc_attr( $id ); ?>" type="button"><?php echo esc_html_x( 'Text', 'Name for the Text editor tab (formerly HTML)', 'acf' ); ?></button>
					</div>
				<?php endif; ?>
			</div>
			<div id="wp-<?php echo esc_attr( $id ); ?>-editor-container" class="wp-editor-container">
					<?php if ( $field['delay'] ) : ?>
					<div class="acf-editor-toolbar"><?php esc_html_e( 'Click to initialize TinyMCE', 'acf' ); ?></div>
				<?php endif; ?>
					<?php printf( $textarea, $field['value'] ); //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by format_for_editor(). ?>
			</div>
		</div>
			<?php
		}


		/**
		 * This filter is applied to the $value after it is loaded from the db, and before it is returned to the template
		 *
		 * @type    filter
		 * @since   3.6
		 *
		 * @param  mixed   $value       The value which was loaded from the database.
		 * @param  mixed   $post_id     The $post_id from which the value was loaded.
		 * @param  array   $field       The field array holding all the field options.
		 * @param  boolean $escape_html Should the field return a HTML safe formatted value.
		 * @return mixed   $value       The modified value
		 */
		public function format_value( $value, $post_id, $field, $escape_html ) {
			// Bail early if no value or not a string.
			if ( empty( $value ) || ! is_string( $value ) ) {
				return $value;
			}

			if ( $escape_html ) {
				add_filter( 'acf_the_content', 'acf_esc_html', 1 );
			}

			$value = apply_filters( 'acf_the_content', $value );

			if ( $escape_html ) {
				remove_filter( 'acf_the_content', 'acf_esc_html', 1 );
			}

			// Follow the_content function in /wp-includes/post-template.php
			return str_replace( ']]>', ']]&gt;', $value );
		}

		public function load_value( $value, $post_id = false, $field = false ) {
			if ( feadmin_edit_mode() && get_the_ID() == $post_id ) {
				return '';
			}
			
			if ( ! $value && $post_id && is_numeric( $post_id ) ) {
				$edit_post = get_post( $post_id );
				if( $edit_post && ! is_wp_error( $edit_post ) ) 
				$value     = $edit_post->post_content;
			}
			return $value;
		}

		function pre_update_value( $checked, $value, $post_id, $field ) {
			if( $this->name !== $field['type'] ){
				return $checked;
			}if ( $post_id && is_numeric( $post_id ) ) {
				$post_to_edit                 = array(
					'ID' => $post_id,
				);
				$post_to_edit['post_content'] = $value;
				remove_action( 'acf/save_post', '_acf_do_save_post' );
				wp_update_post( $post_to_edit );
				add_action( 'acf/save_post', '_acf_do_save_post' );

			}
			return true;
		}

		

		function render_field_settings( $field ) {
			acf_render_field_setting(
				$field,
				array(
					'label'   => __( 'Appearance', 'frontend-admin' ),
					'name'    => 'field_type',
					'type'    => 'radio',
					'choices' => array(
						'wysiwyg'  => __( 'Wysiwyg', 'frontend-admin' ),
						//'blocks_editor'  => __( 'Blocks Editor (NEW!)', 'frontend-admin' ),
						'textarea' => __( 'Text Area', 'frontend-admin' ),
					),
				)
			);

			// vars
			$toolbars = $this->get_toolbars();
			$choices  = array();

			if ( ! empty( $toolbars ) ) {

				foreach ( $toolbars as $k => $v ) {

					$label = $k;
					$name  = sanitize_title( $label );
					$name  = str_replace( '-', '_', $name );

					$choices[ $name ] = $label;
				}
			}

			// default_value
			acf_render_field_setting(
				$field,
				array(
					'label'        => __( 'Default Value', 'frontend-admin' ),
					'instructions' => __( 'Appears when creating a new post', 'frontend-admin' ),
					'type'         => 'textarea',
					'name'         => 'default_value',
				)
			);

			// tabs
			acf_render_field_setting(
				$field,
				array(
					'label'        => __( 'Tabs', 'frontend-admin' ),
					'instructions' => '',
					'type'         => 'select',
					'name'         => 'tabs',
					'choices'      => array(
						'all'    => __( 'Visual & Text', 'frontend-admin' ),
						'visual' => __( 'Visual Only', 'frontend-admin' ),
						'text'   => __( 'Text Only', 'frontend-admin' ),
					),
					'conditions'   => array(
						array(
							array(
								'field'    => 'field_type',
								'operator' => '==',
								'value'    => 'wysiwyg',
							),
						),
					),
				)
			);

			// toolbar
			acf_render_field_setting(
				$field,
				array(
					'label'        => __( 'Toolbar', 'frontend-admin' ),
					'instructions' => '',
					'type'         => 'select',
					'name'         => 'toolbar',
					'choices'      => $choices,
					'conditions'   => array(
						array(
							array(
								'field'    => 'field_type',
								'operator' => '==',
								'value'    => 'wysiwyg',
							),
							array(
								'field'    => 'tabs',
								'operator' => '!=',
								'value'    => 'text',
							),
						),
					),
				)
			);

			// media_upload
			acf_render_field_setting(
				$field,
				array(
					'label'        => __( 'Show Media Upload Buttons?', 'frontend-admin' ),
					'instructions' => '',
					'name'         => 'media_upload',
					'type'         => 'true_false',
					'ui'           => 1,
					'conditions'   => array(
						array(
							array(
								'field'    => 'field_type',
								'operator' => '==',
								'value'    => 'wysiwyg',
							),
						),
					),
				)
			);

			// delay
			acf_render_field_setting(
				$field,
				array(
					'label'        => __( 'Delay initialization?', 'frontend-admin' ),
					'instructions' => __( 'TinyMCE will not be initialized until field is clicked', 'frontend-admin' ),
					'name'         => 'delay',
					'type'         => 'true_false',
					'ui'           => 1,
					'conditions'   => array(
						array(
							array(
								'field'    => 'field_type',
								'operator' => '==',
								'value'    => 'wysiwyg',
							),
							array(
								'field'    => 'tabs',
								'operator' => '!=',
								'value'    => 'text',
							),
						),
					),
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
					'conditions'   => array(
						array(
							array(
								'field'    => 'field_type',
								'operator' => '==',
								'value'    => 'textarea',
							),
						),
					),
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
					'conditions'   => array(
						array(
							array(
								'field'    => 'field_type',
								'operator' => '==',
								'value'    => 'textarea',
							),
						),
					),
				)
			);

			// rows
			acf_render_field_setting(
				$field,
				array(
					'label'        => __( 'Rows', 'frontend-admin' ),
					'instructions' => __( 'Sets the textarea height', 'frontend-admin' ),
					'type'         => 'number',
					'name'         => 'rows',
					'placeholder'  => 8,
					'conditions'   => array(
						array(
							array(
								'field'    => 'field_type',
								'operator' => '==',
								'value'    => 'textarea',
							),
						),
					),
				)
			);

			// formatting
			acf_render_field_setting(
				$field,
				array(
					'label'        => __( 'New Lines', 'frontend-admin' ),
					'instructions' => __( 'Controls how new lines are rendered', 'frontend-admin' ),
					'type'         => 'select',
					'name'         => 'new_lines',
					'choices'      => array(
						'wpautop' => __( 'Automatically add paragraphs', 'frontend-admin' ),
						'br'      => __( 'Automatically add &lt;br&gt;', 'frontend-admin' ),
						''        => __( 'No Formatting', 'frontend-admin' ),
					),
					'conditions'   => array(
						array(
							array(
								'field'    => 'field_type',
								'operator' => '==',
								'value'    => 'textarea',
							),
						),
					),
				)
			);

		}


	}



endif;


