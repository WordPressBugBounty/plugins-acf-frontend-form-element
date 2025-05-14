<?php
namespace Frontend_Admin\Bricks\Elements;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class FrontendForm extends \Bricks\Element {
	public $category = 'general';
	public $name     = 'frontend-form';
	public $icon     = 'ti-layout-tab';
	public $scripts  = [];
	public $nestable = true;
	public $current_control_group = null;
	use Traits\Controls;

	public function get_label() {
		return esc_html__( 'Frontend Form', 'bricks' ) . ' (' . esc_html__( 'Nestable', 'bricks' ) . ')';
	}

	public function get_keywords() {
		return [ 'nestable' ];
	}


	public function set_controls() {
		$this->add_control_group(
			'form',
			array(
				'title' => esc_html__( 'Form', 'bricks' ),
				'tab'   => 'content',
			)
		);

		$this->custom_fields_control();

		$this->form_actions_controls();

		$this->controls['_conditions']['default'] = [
			[
				[
					'key' => 'user_role',
					'value' => ['administrator'],
				]
			],
			[
				[
					'key' => 'user_id',
					'value' => '{author_id}',
				]
			]
		];

		global $fea_instance;
		if ( isset( $fea_instance->remote_actions ) ) {
			$remote_actions = $fea_instance->remote_actions;
			foreach ( $remote_actions as $action ) {
				$action->bricks_settings_section( $this );
			}
		}
		
		$local_actions = $fea_instance->local_actions;
		
		foreach ( $local_actions as $name => $action ) {			
			$action->bricks_settings_section( $this );
		}
		

	
	}

	public function form_actions_controls() {
		$this->add_control_group(
			'form_actions',
			array(
				'title' => esc_html__( 'Actions', 'bricks' ),
				'tab'   => 'content',
			)
		);

		$redirect_options = array(
			'current'     => __( 'Stay on Current Page/Post', 'acf-frontend-form-element' ),
			'custom_url'  => __( 'Custom Url', 'acf-frontend-form-element' ),
			'referer_url' => __( 'Referer', 'acf-frontend-form-element' ),
			'post_url'    => __( 'Post Url', 'acf-frontend-form-element' ),
			'none'       => __( 'None', 'acf-frontend-form-element' ),
		);

		$redirect_options = apply_filters( 'frontend_admin/forms/redirect_options', $redirect_options );

		$this->add_control(
			'redirect',
			array(
				'label'       => __( 'Redirect After Submit', 'acf-frontend-form-element' ),
				'type'        => 'select',
				'default'     => 'current',
				'options'     => $redirect_options,
				'render_type' => 'none',
			)
		);
	
		$this->add_control(
			'redirect_action',
			array(
				'label'       => __( 'After Reload', 'acf-frontend-form-element' ),
				'type'        => 'select',
				'default'     => 'clear',
				'options'     => array(
					''		=> __( 'Nothing', 'acf-frontend-form-element' ),
					'clear' => __( 'Clear Form', 'acf-frontend-form-element' ),
					'edit'  => __( 'Edit Content', 'acf-frontend-form-element' ),
				),
				'render_type' => 'none',
			)
		);
		$this->add_control(
			'custom_url',
			array(
				'label'       => __( 'Custom Url', 'acf-frontend-form-element' ),
				'type'        => 'text',
				'placeholder' => __( 'Enter Url Here', 'acf-frontend-form-element' ),
				'options'     => false,
				'show_label'  => false,
				'required'   => array(
					'redirect', '=', 'custom_url',
				),
				'dynamic'     => array(
					'active' => true,
				),
				'render_type' => 'none',
			)
		);

		$this->add_control(
			'show_update_message',
			array(
				'label'        => __( 'Show Success Message', 'acf-frontend-form-element' ),
				'type' 		   => 'checkbox',
				'default'      => true,
				'render_type'  => 'none',
			)
		);
		$success = $this->form_defaults['success_message'] ?? __( 'Form has been submitted successfully.', 'acf-frontend-form-element' );
		$this->add_control(
			'update_message',
			array(
				'label'       => __( 'Submit Message', 'acf-frontend-form-element' ),
				'type'        => 'textarea',
				'default'     => $success,
				'placeholder' => $success,
				'dynamic'     => array(
					'active' => true,
				),
				'required'   => array(
					'show_update_message', '=', true,
				),
			)
		);
		$this->add_control(
			'error_message',
			array(
				'label'       => __( 'Error Message', 'acf-frontend-form-element' ),
				'type'        => 'textarea',
				'description' => __( 'There shouldn\'t be any problems with the form submission, but if there are, this is what your users will see. If you are expeiencing issues, try and changing your cache settings and reach out to ', 'acf-frontend-form-element' ) . 'support@dynamiapps.com',
				'default'     => __( 'Please fix the form errors and try again.', 'acf-frontend-form-element' ),
				'dynamic'     => array(
					'active' => true,
				),
				'render_type' => 'none',
			)
		);
		//default required messaged
		$this->add_control(
			'required_message',
			array(
				'label'       => __( 'Required Message', 'acf-frontend-form-element' ),
				'type'        => 'text',
				'default'     => __( 'This field is required.', 'acf-frontend-form-element' ),
				'dynamic'     => array(
					'active' => true,
				),
				'render_type' => 'none',
			)
		);
		//email veified message
		$this->add_control(
			'email_verified_message',
			array(
				'label'       => __( 'Email Verified Message', 'acf-frontend-form-element' ),
				'type'        => 'text',
				'default'     => __( 'Email has been verified.', 'acf-frontend-form-element' ),
				'dynamic'     => array(
					'active' => true,
				),
				'render_type' => 'none',
				'required'   => array(
					'save_all_data', '=', 'verify_email'
				),
			)
		);

	}

	/**
	 * Get child elements
	 *
	 * @return array Array of child elements.
	 *
	 * @since 1.5
	 */
	public function get_nestable_children() {
		/**
		 * NOTE: Required classes for element styling & script:
		 *
		 * .tab-menu
		 * .tab-title
		 * .tab-content
		 * .tab-pane
		 */
		return [
			// Content
			[
				'name'     => 'block',
				'label'    => esc_html__( 'Form content', 'bricks' ),
				'settings' => [
					'_hidden' => [
						'_cssClasses' => 'tab-content',
					],
				],
				'children' => [
					[
						'name'     => 'fea-text-field',
						'settings' => [
							'field_label' => esc_html__( 'Text Field', 'frontend-admin' ),
							'style' => 'primary'
						],
					],
					[
						'name'     => 'fea-submit-button',
						'settings' => [
							'text' => esc_html__( 'Submit Form', 'frontend-admin' ),
							'style' => 'primary'
						],
					],
				],
			],
		];
	}

	public function get_form_element( $form, $key ){

		if ( strpos( $key, '_bricks_' ) === false ) {
			return false;
		}

		// Get Template/page id and element id
		$ids = explode( '_bricks_', $key );

		// If there is no element id, there is no reason to continue 
		if( empty( $ids[1] ) ) return false; 

		$element = \Bricks\Helpers::get_element_data( $ids[0], $ids[1] );


		if( $element ){
			
			return $this->prepare_form( $element['element']['settings'], $key, $element['elements'] );
		}
		return false;

	}
	
	public function prepare_form( $form_data = array(), $id = null, $children = [] ) {
		global $fea_instance, $fea_form, $wp_query;
		$form_display = $fea_instance->form_display;

		$current_post_id = $wp_query->get_queried_object_id();

		$form_data['submit_actions'] = true;

		$form_data['post_to_edit'] = $post_to_edit = $form_data['post_to_edit'] ?? 'current_post';

		if( !$id ){
			$form_data['id'] = $form_data['ID'] = $current_post_id . '_bricks_' . $this->id;
		}else{
			$form_data['id'] = $form_data['ID'] = $id;
		}

		$id = $id ?? $this->id ?? null;

		$fea_form =  $form_display->validate_form( $form_data );

		if( $children ){
			$fields = [];
			foreach( $children as $child ){
				$name = $child['name'] ?? null;
				$settings = $child['settings'] ?? null;

				if( ! $name || ! $settings ) continue;

				if ( strpos( $name, 'fea-' ) === 0 && strpos( $name, '-field' ) !== false ) {
					$field_type = str_replace( [ 'fea-', '-field' ], '', $name );
					$field_type = str_replace( '-', '_', $field_type );

					$field = [
						'type' => $field_type,
						'key'   => $id . '_' . $child['id'],
						'builder' => 'bricks',
						'label' => $settings['field_label'] ?? '',
						'name' => $settings['field_name'] ?? '',
						'placeholder' => $settings['field_placeholder'] ?? '',
						'default_value' => $settings['field_default_value'] ?? '',
						'required' => $settings['field_required'] ?? false,
						'maxlength' => $settings['field_maxlength'] ?? 0,
					];
					$field = $form_display->get_field_data_type( $field, $fea_form );

					if( ! $field ) return false;

					if ( ! isset( $field['value'] )
						|| $field['value'] === null
					) {
						$field = $form_display->get_field_value( $field, $fea_form );
					}

					$fields[$id . '_' . $child['id']] = $field;

				}

			}

			$fea_form['fields'] = $fields;
		}


		return $fea_form;

		
	}

	public function render() {
		global $fea_instance, $fea_form;
		$settings = $this->settings;
		$form_display = $fea_instance->form_display;


		$fea_form = $this->prepare_form( $settings );
		$form_display->maybe_show_success_message( $fea_form );

		echo "<div {$this->render_attributes( '_root' )}>";

		$this->set_attribute( 'frontend-form', 'class', 'frontend-form' );

		echo "<form {$this->render_attributes( 'frontend-form' )}>";

		// Render children elements (= individual items)
		echo \Bricks\Frontend::render_children( $this );

		$fea_instance->form_display->form_render_data( $fea_form );

		echo '</form>';

		echo '</div>';


		$fea_form = null;
	}

	public function __construct( $settings = [] ) {
		parent::__construct( $settings );

		add_filter( 'frontend_admin/forms/get_form', [ $this, 'get_form_element' ], 10, 2 );

	}
 
}
