<?php
namespace Frontend_Admin\Field_Types;

if ( ! class_exists( 'Field_Base' ) ) :

	class Field_Base extends \acf_field {

		// vars
		public $name          = '';
		public $label         = '';
		public $category      = 'basic';
		public $description   = '';
		public $doc_url       = null;
		public $tutorial_url  = null;
		public $preview_image = null;
		public $pro           = null;
		public $defaults      = array();
		public $l10n          = array();
		public $public        = true;
		public $show_in_rest  = true;
		public $supports      = array(
			'escaping_html' => false, // Set true when a field handles its own HTML escaping in format_value
			'required'      => true,
		);

		// Tracks whether the global remaining-characters hook has been registered (only needs to happen once).
		private static $remaining_characters_hooked = false;

		// Tracks whether the ACF native field type settings hooks have been registered.
		private static $acf_char_counter_hooked = false;

		/*
		*  __construct
		*
		*  This function will initialize the field type
		*
		*  @type    function
		*  @date    5/03/2014
		*  @since   5.0.0
		*
		*  @param   n/a
		*  @return  n/a
		*/

		function __construct( $data = false ) {

			
			$this->initialize();

				if ( ! self::$remaining_characters_hooked ) {
					self::$remaining_characters_hooked = true;
					add_filter( 'acf/prepare_field', array( __CLASS__, 'add_remaining_characters_attrs' ), 20 );
				}

				if ( ! self::$acf_char_counter_hooked ) {
					self::$acf_char_counter_hooked = true;
					// ACF native text/textarea already have maxlength — just append the toggle.
					add_action( 'acf/render_field_settings/type=text',     array( __CLASS__, 'inject_show_remaining_setting' ), 20 );
					add_action( 'acf/render_field_settings/type=textarea', array( __CLASS__, 'inject_show_remaining_setting' ), 20 );
					// ACF native email/url have no maxlength — add both settings.
					add_action( 'acf/render_field_settings/type=email', array( __CLASS__, 'inject_char_limit_and_remaining' ), 20 );
					add_action( 'acf/render_field_settings/type=url',   array( __CLASS__, 'inject_char_limit_and_remaining' ), 20 );
					// Server-side validation for types that don't natively enforce maxlength.
					add_filter( 'acf/validate_value/type=email', array( __CLASS__, 'validate_char_limit' ), 20, 4 );
					add_filter( 'acf/validate_value/type=url',   array( __CLASS__, 'validate_char_limit' ), 20, 4 );
				}


				if( $this->public && '' !== $this->name ) acf_register_field_type( $this );

				
				$this->add_field_action( 'frontend_admin/field_render', array( $this, 'render_field_interactive' ), 10, 1 );

				// value
				$this->add_field_filter( 'acf/load_value', array( $this, 'load_value' ), 10, 3 );
				$this->add_field_filter( 'acf/update_value', array( $this, 'update_value' ), 10, 3 );
				$this->add_field_filter( 'acf/format_value', array( $this, 'format_value' ), 10, 3 );
				$this->add_field_filter( 'acf/validate_value', array( $this, 'validate_value' ), 10, 4 );
				$this->add_field_action( 'acf/delete_value', array( $this, 'delete_value' ), 10, 3 );

				// field
				$this->add_field_filter( 'acf/validate_rest_value', array( $this, 'validate_rest_value' ), 10, 3 );
				$this->add_field_filter( 'acf/validate_field', array( $this, 'validate_field' ), 10, 1 );
				$this->add_field_filter( 'acf/load_field', array( $this, 'load_field' ), 10, 1 );
				$this->add_field_filter( 'acf/update_field', array( $this, 'update_field' ), 10, 1 );
				$this->add_field_filter( 'acf/duplicate_field', array( $this, 'duplicate_field' ), 10, 1 );
				$this->add_field_action( 'acf/delete_field', array( $this, 'delete_field' ), 10, 1 );
				$this->add_field_action( 'acf/render_field', array( $this, 'render_field' ), 9, 1 );
				$this->add_field_action( 'acf/render_field_settings', array( $this, 'render_field_settings' ), 9, 1 );
				/* $this->add_field_action( 'acf/render_field_general_settings', array( $this, 'render_field_general_settings' ), 9, 1 );
				$this->add_field_action( 'acf/render_field_validation_settings', array( $this, 'render_field_validation_settings' ), 9, 1 );
				$this->add_field_action( 'acf/render_field_presentation_settings', array( $this, 'render_field_presentation_settings' ), 9, 1 );
				$this->add_field_action( 'acf/render_field_conditional_logic_settings', array( $this, 'render_field_conditional_logic_settings' ), 9, 1 ); */
				$this->add_field_filter( 'acf/prepare_field', array( $this, 'prepare_field' ), 10, 1 );
				$this->add_field_filter( 'acf/translate_field', array( $this, 'translate_field' ), 10, 1 );

				// input actions
				$this->add_action( 'acf/input/admin_enqueue_scripts', array( $this, 'input_admin_enqueue_scripts' ), 10, 0 );
				$this->add_action( 'acf/input/admin_head', array( $this, 'input_admin_head' ), 10, 0 );
				$this->add_action( 'acf/input/form_data', array( $this, 'input_form_data' ), 10, 1 );
				$this->add_filter( 'acf/input/admin_l10n', array( $this, 'input_admin_l10n' ), 10, 1 );
				$this->add_action( 'acf/input/admin_footer', array( $this, 'input_admin_footer' ), 10, 1 );

				// field group actions
				$this->add_action( 'acf/field_group/admin_enqueue_scripts', array( $this, 'field_group_admin_enqueue_scripts' ), 10, 0 );
				$this->add_action( 'acf/field_group/admin_head', array( $this, 'field_group_admin_head' ), 10, 0 );
				$this->add_action( 'acf/field_group/admin_footer', array( $this, 'field_group_admin_footer' ), 10, 0 );
			
				// Most fields can use the "Required" validation setting as well as most presentation settings.
				$this->add_field_action( 'acf/field_group/render_field_settings_tab/validation', array( $this, 'render_required_setting' ), 5 );

				if( function_exists( 'acf_get_combined_field_type_settings_tabs' ) ) {
					// Add settings for each tab
					foreach ( acf_get_combined_field_type_settings_tabs() as $tab_key => $tab_label ) {
						$this->add_field_action( "acf/field_group/render_field_settings_tab/{$tab_key}", array( $this, "render_field_{$tab_key}_settings" ), 9, 1 );
					}
				}

				//enqueue scripts on frontend
				$this->add_action( 'frontend_admin/field/enqueue_scripts', array( $this, 'enqueue_scripts' ), 10, 0 );
			

		}

		public function render_field_interactive( $field ) {
			// do nothing by default - this is meant to be overridden by fields that want to provide an interactive rendering experience in the editor. By default, we'll just use the standard render_field method for both frontend and editor rendering.
			
		}


		/*
		*  initialize
		*
		*  This function will initialize the field type
		*
		*  @type    function
		*  @date    27/6/17
		*  @since   5.6.0
		*
		*  @param   n/a
		*  @return  n/a
		*/

		function initialize() {

			$this->doc_url  = '';
			$this->description = '';
			$this->preview_image = '';
			$this->pro = false;
			$this->tutorial_url = '';
			// Do Nothing

		}


		/*
		*  add_filter
		*
		*  This function checks if the function is_callable before adding the filter
		*
		*  @type    function
		*  @date    5/03/2014
		*  @since   5.0.0
		*
		*  @param   $tag (string)
		*  @param   $function_to_add (string)
		*  @param   $priority (int)
		*  @param   $accepted_args (int)
		*  @return  n/a
		*/

		function add_filter( $tag = '', $function_to_add = '', $priority = 10, $accepted_args = 1 ) {

			// bail early if no callable
			if ( ! is_callable( $function_to_add ) ) {
				return;
			}

			// add
			add_filter( $tag, $function_to_add, $priority, $accepted_args );

		}


		/*
		*  add_field_filter
		*
		*  This function will add a field type specific filter
		*
		*  @type    function
		*  @date    29/09/2016
		*  @since   5.4.0
		*
		*  @param   $tag (string)
		*  @param   $function_to_add (string)
		*  @param   $priority (int)
		*  @param   $accepted_args (int)
		*  @return  n/a
		*/

		function add_field_filter( $tag = '', $function_to_add = '', $priority = 10, $accepted_args = 1 ) {

			// append
			$tag .= '/type=' . $this->name;

			// add
			$this->add_filter( $tag, $function_to_add, $priority, $accepted_args );

		}


		/*
		*  add_action
		*
		*  This function checks if the function is_callable before adding the action
		*
		*  @type    function
		*  @date    5/03/2014
		*  @since   5.0.0
		*
		*  @param   $tag (string)
		*  @param   $function_to_add (string)
		*  @param   $priority (int)
		*  @param   $accepted_args (int)
		*  @return  n/a
		*/

		function add_action( $tag = '', $function_to_add = '', $priority = 10, $accepted_args = 1 ) {

			// bail early if no callable
			if ( ! is_callable( $function_to_add ) ) {
				return;
			}

			// add
			add_action( $tag, $function_to_add, $priority, $accepted_args );

		}


		/*
		*  add_field_action
		*
		*  This function will add a field type specific filter
		*
		*  @type    function
		*  @date    29/09/2016
		*  @since   5.4.0
		*
		*  @param   $tag (string)
		*  @param   $function_to_add (string)
		*  @param   $priority (int)
		*  @param   $accepted_args (int)
		*  @return  n/a
		*/

		function add_field_action( $tag = '', $function_to_add = '', $priority = 10, $accepted_args = 1 ) {

			// append
			$tag .= '/type=' . $this->name;

			// add
			$this->add_action( $tag, $function_to_add, $priority, $accepted_args );

		}


		/*
		*  validate_field
		*
		*  This function will append default settings to a field
		*
		*  @type    filter ("acf/validate_field/type={$this->name}")
		*  @since   3.6
		*  @date    23/01/13
		*
		*  @param   $field (array)
		*  @return  $field (array)
		*/

		function validate_field( $field ) {

			// bail early if no defaults
			if ( ! is_array( $this->defaults ) ) {
				return $field;
			}

			// merge in defaults but keep order of $field keys
			foreach ( $this->defaults as $k => $v ) {

				if ( ! isset( $field[ $k ] ) ) {
					$field[ $k ] = $v;
				}
			}

			// return
			return $field;

		}


		/*
		*  admin_l10n
		*
		*  This function will append l10n text translations to an array which is later passed to JS
		*
		*  @type    filter ("acf/input/admin_l10n")
		*  @since   3.6
		*  @date    23/01/13
		*
		*  @param   $l10n (array)
		*  @return  $l10n (array)
		*/

		function input_admin_l10n( $l10n ) {

			// bail early if no defaults
			if ( empty( $this->l10n ) ) {
				return $l10n;
			}

			// append
			$l10n[ $this->name ] = $this->l10n;

			// return
			return $l10n;

		}

		/**
		 * Add additional validation for fields being updated via the REST API.
		 *
		 * @param bool  $valid
		 * @param mixed $value
		 * @param array $field
		 *
		 * @return bool|WP_Error
		 */
		public function validate_rest_value( $valid, $value, $field ) {
			return $valid;
		}

		/**
		 * Return the schema array for the REST API.
		 *
		 * @param array $field
		 * @return array
		 */
		public function get_rest_schema( array $field ) {
			$schema = array(
				'type'     => array( 'string', 'null' ),
				'required' => ! empty( $field['required'] ),
			);

			if ( isset( $field['default_value'] ) && '' !== $field['default_value'] ) {
				$schema['default'] = $field['default_value'];
			}

			return $schema;
		}

		/**
		 * Return an array of links for addition to the REST API response. Each link is an array and must have both `rel` and
		 * `href` keys. The `href` key must be a REST API resource URL. If a link is marked as `embeddable`, the `_embed` URL
		 * parameter will trigger WordPress to dispatch an internal sub request and load the object within the same request
		 * under the `_embedded` response property.
		 *
		 * e.g;
		 *    [
		 *        [
		 *            'rel' => 'acf:post',
		 *            'href' => 'https://example.com/wp-json/wp/v2/posts/497',
		 *            'embeddable' => true,
		 *        ],
		 *        [
		 *            'rel' => 'acf:user',
		 *            'href' => 'https://example.com/wp-json/wp/v2/users/2',
		 *            'embeddable' => true,
		 *        ],
		 *    ]
		 *
		 * @param mixed      $value The raw (unformatted) field value.
		 * @param string|int $post_id
		 * @param array      $field
		 * @return array
		 */
		public function get_rest_links( $value, $post_id, array $field ) {
			return array();
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

		/**
		 * Adds the "remaining characters" wrapper data attributes to any field (regardless of
		 * type) that has a character limit and has opted in to showing the remaining count.
		 * Hooked once into the generic (non type-specific) "acf/prepare_field" filter so it
		 * applies to every text-based field type, not just textarea.
		 *
		 * @param array $field
		 * @return array
		 */
		public static function add_remaining_characters_attrs( $field ) {
			if ( empty( $field['maxlength'] ) || empty( $field['show_remaining_characters'] ) ) {
				return $field;
			}

			$field['wrapper']['data-char-remaining'] = 1;
			$field['wrapper']['data-remaining-text'] = $field['remaining_characters_text'] ?? '';
			$field['wrapper']['data-maxlength']      = $field['maxlength'];

			return $field;
		}

		/**
		 * Renders the "Show Remaining Characters" and "Remaining Characters Text" field settings.
		 * Shared by any field type that supports a character limit (text, text_editor, etc).
		 * Requires the field to also expose a "maxlength" setting.
		 *
		 * @param array $field
		 * @return void
		 */
		function remaining_characters_setting( $field ) {
			acf_render_field_setting(
				$field,
				array(
					'label'        => __( 'Show Remaining Characters', 'frontend-admin' ),
					'instructions' => __( 'Display the number of characters remaining based on the maxlength setting', 'frontend-admin' ),
					'type'         => 'true_false',
					'name'         => 'show_remaining_characters',
					'ui'           => 1,
					'conditions'   => array(
						array(
							'field'    => 'maxlength',
							'operator' => '!=',
							'value'    => '',
						),
					),
				)
			);

			// text to show when characters are remaining
			acf_render_field_setting(
				$field,
				array(
					'label'         => __( 'Remaining Characters Text', 'frontend-admin' ),
					'instructions'  => __( 'Text to show before the remaining character count. Use {count_down} or {count_up} as a placeholder for the number and {total} for the total', 'frontend-admin' ),
					'type'          => 'text',
					'name'          => 'remaining_characters_text',
					'default_value' => __( '{count_down} characters remaining. ({count_up} of {total})', 'frontend-admin' ),
					'conditions'    => array(
						array(
							'field'    => 'show_remaining_characters',
							'operator' => '==',
							'value'    => 1,
						),
						array(
							'field'    => 'maxlength',
							'operator' => '!=',
							'value'    => '',
						),
					),
				)
			);
		}

		/**
		 * Injected into ACF native text/textarea settings (which already have maxlength).
		 * Adds only the Show Remaining Characters toggle and text after the existing maxlength setting.
		 */
		public static function inject_show_remaining_setting( $field ) {
			self::render_remaining_characters_fields( $field );
		}

		/**
		 * Injected into ACF native email/url settings (which have no maxlength).
		 * Adds both a Character Limit number field and the Show Remaining Characters toggle.
		 */
		public static function inject_char_limit_and_remaining( $field ) {
			acf_render_field_setting( $field, array(
				'label'        => __( 'Character Limit', 'frontend-admin' ),
				'instructions' => __( 'Leave blank for no limit', 'frontend-admin' ),
				'type'         => 'number',
				'name'         => 'maxlength',
			) );
			self::render_remaining_characters_fields( $field );
		}

		/**
		 * Renders the Show Remaining Characters toggle and Remaining Characters Text setting.
		 * Static so it can be called from static hook callbacks without an instance.
		 */
		public static function render_remaining_characters_fields( $field ) {
			acf_render_field_setting(
				$field,
				array(
					'label'        => __( 'Show Remaining Characters', 'frontend-admin' ),
					'instructions' => __( 'Display the number of characters remaining based on the maxlength setting', 'frontend-admin' ),
					'type'         => 'true_false',
					'name'         => 'show_remaining_characters',
					'ui'           => 1,
					'conditions'   => array(
						array(
							'field'    => 'maxlength',
							'operator' => '!=',
							'value'    => '',
						),
					),
				)
			);
			acf_render_field_setting(
				$field,
				array(
					'label'         => __( 'Remaining Characters Text', 'frontend-admin' ),
					'instructions'  => __( 'Use {count_down} or {count_up} as a placeholder for the number and {total} for the total', 'frontend-admin' ),
					'type'          => 'text',
					'name'          => 'remaining_characters_text',
					'default_value' => __( '{count_down} characters remaining. ({count_up} of {total})', 'frontend-admin' ),
					'conditions'    => array(
						array(
							'field'    => 'show_remaining_characters',
							'operator' => '==',
							'value'    => 1,
						),
						array(
							'field'    => 'maxlength',
							'operator' => '!=',
							'value'    => '',
						),
					),
				)
			);
		}

		/**
		 * Server-side maxlength enforcement for ACF native types that don't validate it themselves
		 * (email, url). Fires via acf/validate_value/type=X filters.
		 */
		public static function validate_char_limit( $valid, $value, $field, $input ) {
			if ( $valid && ! empty( $field['maxlength'] ) && is_string( $value ) && acf_strlen( $value ) > (int) $field['maxlength'] ) {
				return sprintf( __( 'Value must not exceed %d characters', 'frontend-admin' ), $field['maxlength'] );
			}
			return $valid;
		}

		/**
		 * Apply basic formatting to prepare the value for default REST output.
		 *
		 * @param mixed      $value
		 * @param string|int $post_id
		 * @param array      $field
		 * @return mixed
		 */
		public function format_value_for_rest( $value, $post_id, array $field ) {
			return $value;
		}

	}

endif; // class_exists check


