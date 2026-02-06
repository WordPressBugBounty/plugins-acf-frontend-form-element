<?php

namespace Frontend_Admin\Elementor\Widgets;

use  Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
	// Exit if accessed directly
}

/**

 *
 * @since 1.0.0
 */
class Taxonomy_Field extends Base_Field {

	/**
	 * Get widget name.
	 *
	 * Retrieve acf ele form widget name.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'fea_taxonomy_field';
	}


	/**
	 * Get widget defaults.
	 *
	 * Retrieve field widget defaults.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @return string Widget defaults.
	 */
	public function get_field_defaults() {
		return array(
			'field_label_on'     => 'true',
			'field_label'        => '',
			'field_name'         => '',
			'field_placeholder'  => '',
			'field_default_value' => '',
			'field_instruction'  => '',
			'prepend'            => '',
			'append'             => '',
			'custom_fields_save' => 'post',
		);
	
	}

	/**
	 * Is meta field.
	 * 
	 * Check if the field is a meta field.
	 * 
	 * @since 1.0.0
	 */
	public function is_meta_field(){
		return true;
	}


	/**
	 * 
	 * Get meta name.
	 * 
	 * Retrieve the meta name of the field.
	 * 
	 * @since 1.0.0
	 */

	public function get_meta_name(){
		return 'taxonomy_field';
	}

	/**
	 * Get widget title.
	 *
	 * Retrieve acf ele form widget title.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( 'Taxonomy Terms Field', 'frontend-admin' );
	}

	 /**
	  * Get widget icon.
	  *
	  * Retrieve acf ele form widget icon.
	  *
	  * @since  1.0.0
	  * @access public
	  *
	  * @return string Widget icon.
	  */
	public function get_icon() {
		return 'eicon-form-horizontal frontend-icon';
	}

	/**
	 * Get widget keywords.
	 *
	 * Retrieve the list of keywords the widget belongs to.
	 *
	 * @since  2.1.0
	 * @access public
	 *
	 * @return array Widget keywords.
	 */
	public function get_keywords() {
		return array(
			'frontend editing',
			'fields',
			'acf',
			'acf form',
		);
	}


	public function field_specific_controls() {
        $this->add_control(
            'taxonomy',
            [
                'label' => __( 'Taxonomy', 'frontend-admin' ),
                'type' => Controls_Manager::SELECT,
                'options' => acf_get_taxonomy_labels(),
                'default' => 'category',
            ]
        );
		
		$this->add_control(
			'field_type', 
			[
				'label' => __( 'Appearance', 'frontend-admin' ),
				'type' => Controls_Manager::SELECT,
				'groups' => [
					[
						'label' => __( 'Single', 'frontend-admin' ),
						'options' => [
							'select' => __( 'Select', 'frontend-admin' ),
							'radio' => __( 'Radio', 'frontend-admin' ),
						],
					],
					[
						'label' => __( 'Multiple', 'frontend-admin' ),
						'options' => [
							'checkbox' => __( 'Checkbox', 'frontend-admin' ),
							'multi_select' => __( 'Multi Select', 'frontend-admin' ),
						],
					],
				],
				'default' => 'select',
			]
		);

		$this->add_control(
			'allow_null',
			[
				'label' => __( 'Allow Null?', 'frontend-admin' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Yes', 'frontend-admin' ),
				'label_off' => __( 'No', 'frontend-admin' ),
				'return_value' => '1',
				'default' => '',
			]
		);

		//save_terms
		$this->add_control(
			'save_terms',
			[
				'label' => __( 'Save Terms', 'frontend-admin' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Yes', 'frontend-admin' ),
				'label_off' => __( 'No', 'frontend-admin' ),
				'return_value' => '1',
				'default' => '1',
			]
		);

		$this->add_control(
			'load_post_terms',
			[
				'label' => __( 'Load Terms', 'frontend-admin' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Yes', 'frontend-admin' ),
				'label_off' => __( 'No', 'frontend-admin' ),
				'return_value' => '1',
				'default' => '1',
			]
		);

		$this->add_control(
			'add_term',
			[
				'label' => __( 'Add Term', 'frontend-admin' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Yes', 'frontend-admin' ),
				'label_off' => __( 'No', 'frontend-admin' ),
				'return_value' => '1',
				'default' => '',
			]
		);
	}

	
	/**
	 * Get field data.
	 *
	 * Retrieve the field data.
	 *
	 * @since  1.0.0
	 * @access protected
	 *
	 * @param array $field Field data.
	 *
	 * @return array Field data.
	 */
	protected function get_field_data( $field ) {
		$field['type'] = 'related_terms';
		$field['taxonomy'] = $this->get_settings( 'taxonomy' );
		$field['field_type'] = $this->get_settings( 'field_type' );
		$field['allow_null'] = $this->get_settings( 'allow_null' );
		$field['save_terms'] = $this->get_settings( 'save_terms' ) == '1' ? 1 : 0;
		$field['load_terms'] = $this->get_settings( 'load_post_terms' ) == '1' ? 1 : 0;	
		$field['add_term'] = $this->get_settings( 'add_term' ) == '1' ? 1 : 0;

		return $field;
	}





}
