<?php
namespace Frontend_Admin\Module\Classes;

use Elementor\Controls_Manager;
use Elementor\Controls_Stack;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Box_Shadow;
use ElementorPro\Modules\QueryControl\Module as Query_Module;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


class ContentTab {


	public function fields_controls( $widget ) {
		$widget->add_control(
			'save_form_submissions',
			array(
				'label'        => __( 'Save Form Submissions', 'frontend-admin' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'yes', 'frontend-admin' ),
				'label_off'    => __( 'no', 'frontend-admin' ),
				'return_value' => 'true',
				'default'      => true,
				'condition'    => array(
					'admin_forms_select' => '',
				),
			)
		);
		$widget->add_control(
			'save_all_data',
			array(
				'label'     => __( 'Submission Requirements', 'frontend-admin' ),
				'type'      => Controls_Manager::SELECT2,
				'multiple'  => true,
				'default'   => '',
				'options'   => array(
					'require_approval' => __( 'Admin Approval', 'frontend-admin' ),
					'verify_email'     => __( 'Email is Verified', 'frontend-admin' ),
				),
				'condition' => array(
					'save_form_submissions' => 'true',
					'admin_forms_select'    => '',
				),
			)
		);

		do_action( 'frontend_admin/elementor/widget_controls', $widget, $this );

		$repeater = new \Elementor\Repeater();

		$repeater->add_control(
			'field_type',
			array(
				'label'       => __( 'Field Type', 'frontend-admin' ),
				'type'        => Controls_Manager::SELECT,
				'label_block' => true,
				'default'     => 'ACF_fields',
				'groups'      => $widget->get_field_type_options(),
			)
		);
		$repeater->add_control(
			'step_tab_text',
			array(
				'label'        => __( 'Step Tab Text', 'frontend-admin' ),
				'type'         => Controls_Manager::TEXT,
				'default'      => __( 'Step Text', 'frontend-admin' ),
				'dynamic'      => array(
					'active' => true,
				),
				'condition'   => array(
					'field_type' => 'step',
				),
			)
		);
		$repeater->add_control(
			'next_button_text',
			array(
				'label'        => __( 'Next button', 'frontend-admin' ),
				'type'         => Controls_Manager::TEXT,						
				'default'      => __( 'Next', 'frontend-admin' ),
				'dynamic'      => array(
					'active' => true,
				),
				'condition'   => array(
					'field_type' => 'step',
				),
			)
		);
		$repeater->add_control(
			'prev_button_text',
			array(
				'label'        => __( 'Prev button', 'frontend-admin' ),
				'type'         => Controls_Manager::TEXT,
				'default'      => __( 'Previous', 'frontend-admin' ),
				'dynamic'      => array(
					'active' => true,
				),
				'condition'   => array(
					'field_type' => 'step',
				),
			)
		);

		$repeater->add_control(
			'field_groups_select',
			array(
				'label'       => __( 'ACF Field Groups', 'frontend-admin' ),
				'type'        => Controls_Manager::SELECT2,
				'type'        => 'fea_select',
				'label_block' => true,
				'action'	  => 'acf-field-groups',
				'change_others' => [ 'fields_select_exclude' ],
				'options'     => [],
				'multiple'    => true,
				'condition'   => array(
					'field_type'          => 'ACF_field_groups',
				),
			)
		);
		$repeater->add_control(
			'fields_select',
			array(
				'label'       => __( 'ACF Fields', 'frontend-admin' ),
				'type'        => 'fea_select',
				'label_block' => true,
				'action'	  => 'acf-fields',
				'options'     => [],
				'multiple'    => true,
				'condition'   => array(
					'field_type' => 'ACF_fields',
				),
			)
		);

		$repeater->add_control(
			'fields_select_exclude',
			array(
				'label'       => __( 'Exclude Specific Fields', 'frontend-admin' ),
				'type'        => 'fea_select',
				'label_block' => true,
				'action'	  => 'acf-fields',
				'children_of' => 'field_groups_select',
				'multiple'    => true,
				'options'     => [],
				'condition'   => array(
					'field_type'          => array( 'ACF_field_groups' ),
				),
			)
		);


		$custom_layouts   = array( 'ACF_field_groups', 'ACF_fields', 'recaptcha', 'step', 'tab' );
		$base_text_fields = array(
			'term_name',
			'username',
			'email',
			'first_name',
			'last_name',
			'nickname',
			'display_name',
			'title',
			'sku',
			'product_title',
			'author',
			'author_email',
			'site_title',
			'site_tagline',
		);
		$text_fields      = array(
			'term_name',
			'username',
			'email',
			'first_name',
			'last_name',
			'nickname',
			'display_name',
			'bio',
			'title',
			'slug',
			'content',
			'excerpt',
			'sku',
			'product_title',
			'description',
			'short_description',
			'comment',
			'author',
			'author_email',
			'site_title',
			'site_tagline',
		);
		$number_fields    = array(
			'price',
			'sale_price',
		);

		$repeater->add_control(
			'field_label_on',
			array(
				'label'        => __( 'Show Label', 'frontend-admin' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'frontend-admin' ),
				'label_off'    => __( 'No', 'frontend-admin' ),
				'return_value' => 'true',
				'default'      => 'true',
				'condition'    => array(
					'field_type!' => $custom_layouts,
				),

			)
		);
		$repeater->add_control(
			'field_label',
			array(
				'label'       => __( 'Label', 'frontend-admin' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'placeholder' => __( 'Field Label', 'frontend-admin' ),
				'dynamic'     => array(
					'active' => true,
				),
				'condition'   => array(
					'field_type!'    => $custom_layouts,
					'field_label_on' => 'true',
				),
			)
		);

		$repeater->add_control(
			'field_placeholder',
			array(
				'label'       => __( 'Placeholder', 'frontend-admin' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'placeholder' => __( 'Field Placeholder', 'frontend-admin' ),
				'dynamic'     => array(
					'active' => true,
				),
				'condition'   => array(
					'field_type' => $base_text_fields,
				),
			)
		);
		$repeater->add_control(
			'field_default_value',
			array(
				'label'       => __( 'Default Value', 'frontend-admin' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'description' => __( 'This will populate a field if no value has been given yet. You can use shortcodes from other text fields. For example: [acf:field_name]', 'frontend-admin' ),
				'dynamic'     => array(
					'active' => true,
				),
				'condition'   => array(
					'field_type' => $text_fields,
				),
			)
		);

		$repeater->add_control(
			'number_placeholder',
			array(
				'label'       => __( 'Placeholder', 'frontend-admin' ),
				'type'        => Controls_Manager::NUMBER,
				'placeholder' => __( 'Field Placeholder', 'frontend-admin' ),
				'dynamic'     => array(
					'active' => true,
				),
				'condition'   => array(
					'field_type' => $number_fields,
				),
			)
		);
		$repeater->add_control(
			'number_default_value',
			array(
				'label'     => __( 'Default Value', 'frontend-admin' ),
				'type'      => Controls_Manager::NUMBER,
				'dynamic'   => array(
					'active' => true,
				),
				'condition' => array(
					'field_type' => $number_fields,
				),
			)
		);

		$repeater->add_control(
			'default_featured_image',
			array(
				'label'     => __( 'Default', 'frontend-admin' ),
				'type'      => \Elementor\Controls_Manager::MEDIA,
				'condition' => array(
					'field_type' => array( 'featured_image', 'main_image' ),
				),
			)
		);

		$repeater->add_control(
			'editor_type',
			array(
				'label'     => __( 'Type', 'frontend-admin' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => array(
					'wysiwyg'  => __( 'Text Editor', 'frontend-admin' ),
					'textarea' => __( 'Text Area', 'frontend-admin' ),
				),
				'default'   => 'wysiwyg',
				'condition' => array(
					'field_type' => array( 'content', 'description' ),
				),
			)
		);
		$repeater->add_control(
			'button_text',
			array(
				'label'     => __( 'Button Text', 'frontend-admin' ),
				'type'      => Controls_Manager::TEXT,
				'condition' => array(
					'field_type' => array( 'main_image', 'featured_image', 'images', 'variations', 'attributes' ),
				),
			)
		);
		$repeater->add_control(
			'save_button_text',
			array(
				'label'     => __( 'Save Changes Text', 'frontend-admin' ),
				'type'      => Controls_Manager::TEXT,
				'condition' => array(
					'field_type' => array( 'variations', 'attributes' ),
				),
			)
		);
		$repeater->add_control(
			'allow_add_terms',
			array(
				'label'     => __( 'Allow Adding Terms', 'frontend-admin' ),
				'type'      => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'frontend-admin' ),
				'label_off'    => __( 'No', 'frontend-admin' ),
				'description' => __('Check this box to allow adding terms to global attributes.', 'frontend-admin'),
				'return_value' => '1',
				'default'      => '1',
				'condition' => array(
					'field_type' => array( 'attributes' ),
				),
			)
		);
		$repeater->add_control(
			'exclude_custom_attributes',
			array(
				'label'     => __( 'Exclude Custom Product Attributes', 'frontend-admin' ),
				'type'      => Controls_Manager::SWITCHER,
				'description' => __('Check this box to exclude the Custom Product Attributes option.', 'frontend-admin'),
				'label_on'     => __( 'Yes', 'frontend-admin' ),
				'label_off'    => __( 'No', 'frontend-admin' ),
				'return_value' => '1',
				'condition' => array(
					'field_type' => array( 'attributes' ),
				),
			)
		);
		$repeater->add_control(
			'product_authors_to_filter',
			array(
				'label'       => __( 'Filter by Users', 'frontend-admin' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => __( '18, 12, 11', 'frontend-admin' ),
				'default'     => '[current_user]',
				'description' => __( 'Enter the a comma-seperated list of user ids. Dynamic Options: ', 'frontend-admin' ) . ' [current_user]',
				'condition'   => array(
					'field_type' => array( 'grouped_products', 'cross_sells', 'upsells' ),
				),
			)
		);
		$repeater->add_control(
			'add_edit_product',
			array(
				'label'        => __( 'Add/Edit Product', 'frontend-admin' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'frontend-admin' ),
				'label_off'    => __( 'No', 'frontend-admin' ),
				'return_value' => 'true',
				'condition'    => array(
					'field_type' => array( 'grouped_products', 'cross_sells', 'upsells' ),
				),
			)
		);
		$repeater->add_control(
			'new_product_text',
			array(
				'label'     => __( 'New Product Text', 'frontend-admin' ),
				'type'      => Controls_Manager::TEXT,
				'condition' => array(
					'field_type'       => array( 'grouped_products', 'cross_sells', 'upsells' ),
					'add_edit_product' => 'true',
				),
			)
		);
		$repeater->add_control(
			'no_value_msg',
			array(
				'label'       => __( 'No Value Message', 'frontend-admin' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'dynamic'     => array(
					'active' => true,
				),
				'condition'   => array(
					'field_type' => array( 'variations', 'attributes' ),
				),
				'render_type' => 'none',
			)
		);
		$repeater->add_control(
			'no_attrs_msg',
			array(
				'label'       => __( 'No Attributes Message', 'frontend-admin' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'dynamic'     => array(
					'active' => true,
				),
				'condition'   => array(
					'field_type' => array( 'variations' ),
				),
				'render_type' => 'none',
			)
		);

		$repeater->add_control(
			'field_instruction',
			array(
				'label'       => __( 'Instructions', 'frontend-admin' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'placeholder' => __( 'Field Instruction', 'frontend-admin' ),
				'dynamic'     => array(
					'active' => true,
				),
				'condition'   => array(
					'field_type!' => $custom_layouts,
				),
			)
		);
		$repeater->add_control(
			'prepend',
			array(
				'label'     => __( 'Prepend', 'frontend-admin' ),
				'type'      => Controls_Manager::TEXT,
				'dynamic'   => array(
					'active' => true,
				),
				'condition' => array(
					'field_type' => array_merge( $base_text_fields, $number_fields ),
				),
			)
		);

		$repeater->add_control(
			'append',
			array(
				'label'     => __( 'Append', 'frontend-admin' ),
				'type'      => Controls_Manager::TEXT,
				'dynamic'   => array(
					'active' => true,
				),
				'condition' => array(
					'field_type' => array_merge( $base_text_fields, $number_fields ),
				),
			)
		);

		/*
		 $repeater->add_control(
		'character_limit',
		[
		'label' => __( 'Character Limit', 'frontend-admin' ),
		'type' => Controls_Manager::NUMBER,
		'dynamic' => [
		'active' => true,
		],
		'condition' => [
		'field_type' => $text_fields,
		],
		]
		);         */
		$repeater->add_control(
			'minimum',
			array(
				'label'     => __( 'Minimum Value', 'frontend-admin' ),
				'type'      => Controls_Manager::NUMBER,
				'dynamic'   => array(
					'active' => true,
				),
				'condition' => array(
					'field_type' => $number_fields,
				),
			)
		);
		$repeater->add_control(
			'maximum',
			array(
				'label'     => __( 'Maximum Value', 'frontend-admin' ),
				'type'      => Controls_Manager::NUMBER,
				'dynamic'   => array(
					'active' => true,
				),
				'condition' => array(
					'field_type' => $number_fields,
				),
			)
		);
		$repeater->add_control(
			'field_required',
			array(
				'label'        => __( 'Required', 'frontend-admin' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'frontend-admin' ),
				'label_off'    => __( 'No', 'frontend-admin' ),
				'return_value' => 'true',
				'condition'    => array(
					'field_type!' => $custom_layouts,
				),
			)
		);
		$repeater->add_control(
			'field_hidden',
			array(
				'label'        => __( 'Hidden', 'frontend-admin' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'frontend-admin' ),
				'label_off'    => __( 'No', 'frontend-admin' ),
				'return_value' => 'true',
				'condition'    => array(
					'field_type!' => $custom_layouts,
				),
			)
		);
		$repeater->add_control(
			'field_disabled',
			array(
				'label'        => __( 'Disabled', 'frontend-admin' ),
				'type'         => Controls_Manager::SWITCHER,
				'description'  => __( 'This will prevent users from editing the field and the data will not be sent.', 'frontend-admin' ),
				'label_on'     => __( 'Yes', 'frontend-admin' ),
				'label_off'    => __( 'No', 'frontend-admin' ),
				'return_value' => 'true',
				'condition'    => array(
					'field_type!' => $custom_layouts,
				),
			)
		);

		$repeater->add_control(
			'field_readonly',
			array(
				'label'        => __( 'Readonly', 'frontend-admin' ),
				'type'         => Controls_Manager::SWITCHER,
				'description'  => __( 'This will prevent users from editing the field.', 'frontend-admin' ),
				'label_on'     => __( 'Yes', 'frontend-admin' ),
				'label_off'    => __( 'No', 'frontend-admin' ),
				'return_value' => 'true',
				'condition'    => array(
					'field_type' => $base_text_fields,
				),
			)
		);

		if ( class_exists( 'woocommerce' ) ) {
			$repeater->add_control(
				'default_product_type',
				array(
					'label'     => __( 'Default', 'frontend-admin' ),
					'type'      => Controls_Manager::SELECT,
					'options'   => wc_get_product_types(),
					'condition' => array(
						'field_type' => 'product_type',
					),
				)
			);
		}
		$repeater->add_control(
			'field_message',
			array(
				'label'       => __( 'Message', 'frontend-admin' ),
				'type'        => \Elementor\Controls_Manager::WYSIWYG,
				'default'     => __( 'You can add here text, images template shortcodes, and more', 'frontend-admin' ),
				'placeholder' => __( 'Type your message here', 'frontend-admin' ),
				'condition'   => array(
					'field_type' => 'message',
				),
			)
		);
		$repeater->add_control(
			'post_type_field_options',
			array(
				'label'       => __( 'Post Types to Choose From', 'frontend-admin' ),
				'type'        => Controls_Manager::SELECT2,
				'label_block' => true,
				'multiple'    => true,
				'default'     => array(
					'subscriber',
				),
				'options'     => acf_get_pretty_post_types(),
				'condition'   => array(
					'field_type' => 'post_type',
				),
			)
		);

		$repeater->add_control(
			'default_post_type',
			array(
				'label'       => __( 'Default Post Type Option', 'frontend-admin' ),
				'type'        => Controls_Manager::SELECT2,
				'label_block' => true,
				'default'     => array(
					'subscriber',
				),
				'options'     => acf_get_pretty_post_types(),
				'condition'   => array(
					'field_type' => 'post_type',
				),
			)
		);
		$repeater->add_control(
			'role_field_options',
			array(
				'label'       => __( 'Roles to Choose From', 'frontend-admin' ),
				'type'        => Controls_Manager::SELECT2,
				'label_block' => true,
				'multiple'    => true,
				'default'     => array(
					'subscriber',
				),
				'options'     => feadmin_get_user_roles(),
				'condition'   => array(
					'field_type' => 'role',
				),
			)
		);
		$repeater->add_control(
			'role_appearance',
			array(
				'label'     => __( 'Appearance', 'frontend-admin' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'radio',
				'options'   => array(
					'radio'  => __( 'Radio Buttons', 'frontend-admin' ),
					'select' => __( 'Select', 'frontend-admin' ),
				),
				'condition' => array(
					'field_type' => array( 'role', 'allow_backorders', 'stock_status', 'post_type', 'product_type' ),
				),
			)
		);
		$repeater->add_control(
			'role_radio_layout',
			array(
				'label'     => __( 'Layout', 'frontend-admin' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'vertical',
				'options'   => array(
					'vertical'   => __( 'Vertical', 'frontend-admin' ),
					'horizontal' => __( 'Horizontal', 'frontend-admin' ),
				),
				'condition' => array(
					'field_type'      => array( 'role', 'allow_backorders', 'stock_status', 'post_type' ),
					'role_appearance' => 'radio',
				),
			)
		);
		$repeater->add_control(
			'default_role',
			array(
				'label'       => __( 'Default Role Option', 'frontend-admin' ),
				'type'        => Controls_Manager::SELECT2,
				'label_block' => true,
				'default'     => array(
					'subscriber',
				),
				'options'     => feadmin_get_user_roles(),
				'condition'   => array(
					'field_type' => 'role',
				),
			)
		);
		$repeater->add_control(
			'password_strength',
			array(
				'label'       => __( 'Password Strength', 'frontend-admin' ),
				'type'        => Controls_Manager::SELECT,
				'label_block' => true,
				'default'     => '3',
				'options'     => array(
					'1' => __( 'Very Weak', 'frontend-admin' ),
					'2' => __( 'Weak', 'frontend-admin' ),
					'3' => __( 'Medium', 'frontend-admin' ),
					'4' => __( 'Strong', 'frontend-admin' ),
				),
				'condition'   => array(
					'field_type' => 'password',
				),
			)
		);
		if ( ! class_exists( 'ElementorPro\Modules\QueryControl\Module' ) ) {
			$repeater->add_control(
				'default_terms',
				array(
					'label'       => __( 'Default Terms', 'frontend-admin' ),
					'type'        => Controls_Manager::TEXT,
					'placeholder' => __( '18, 12, 11', 'frontend-admin' ),
					'description' => __( 'Enter the a comma-seperated list of term ids', 'frontend-admin' ),
					'condition'   => array(
						'field_type' => array( 'taxonomy', 'categories', 'tags', 'product_categories', 'product_tags' ),
					),
				)
			);
		} else {
			$repeater->add_control(
				'default_terms',
				array(
					'label'        => __( 'Default Terms', 'frontend-admin' ),
					'type'         => Query_Module::QUERY_CONTROL_ID,
					'label_block'  => true,
					'autocomplete' => array(
						'object'  => Query_Module::QUERY_OBJECT_TAX,
						'display' => 'detailed',
					),
					'multiple'     => true,
					'condition'    => array(
						'field_type' => array( 'taxonomy', 'categories', 'tags', 'product_categories', 'product_tags' ),
					),
				)
			);
		}

		$repeater->add_control(
			'field_taxonomy',
			array(
				'label'       => __( 'Taxonomy', 'frontend-admin' ),
				'type'        => Controls_Manager::SELECT,
				'label_block' => true,
				'default'     => 'category',
				'options'     => acf_get_taxonomy_labels(),
				'condition'   => array(
					'field_type' => 'taxonomy',
				),
			)
		);
		$repeater->add_control(
			'field_taxonomy_appearance',
			array(
				'label'     => __( 'Appearance', 'frontend-admin' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'checkbox',
				'groups'    => array(
					'multi'  => array(
						'label'   => __( 'Multiple Value', 'frontend-admin' ),
						'options' => array(
							'checkbox'     => __( 'Checkboxes', 'frontend-admin' ),
							'multi_select' => __( 'Multi Select', 'frontend-admin' ),
						),
					),
					'single' => array(
						'label'   => __( 'Single Value', 'frontend-admin' ),
						'options' => array(
							'radio'  => __( 'Radio Buttons', 'frontend-admin' ),
							'select' => __( 'Select', 'frontend-admin' ),
						),
					),
				),
				'condition' => array(
					'field_type' => array( 'taxonomy', 'categories', 'tags', 'product_categories', 'product_tags' ),
				),
			)
		);

		$repeater->add_control(
			'field_add_term',
			array(
				'label'        => __( 'Add Term', 'frontend-admin' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'frontend-admin' ),
				'label_off'    => __( 'No', 'frontend-admin' ),
				'return_value' => 'true',
				'condition'    => array(
					'field_type' => array( 'taxonomy', 'categories', 'tags', 'product_categories', 'product_tags' ),
				),
			)
		);
		$repeater->add_control(
			'set_as_username',
			array(
				'label'        => __( 'Set as username', 'frontend-admin' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'frontend-admin' ),
				'label_off'    => __( 'No', 'frontend-admin' ),
				'return_value' => 'true',
				'condition'    => array(
					'field_type' => 'email',
				),
			)
		);
		$repeater->add_control(
			'change_slug',
			array(
				'label'        => __( 'Change Slug', 'frontend-admin' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'frontend-admin' ),
				'label_off'    => __( 'No', 'frontend-admin' ),
				'description'  => __( 'WARNING: allowing your users to change term slugs might affect your existing urls and their SEO rating', 'frontend-admin' ),
				'return_value' => 'true',
				'condition'    => array(
					'field_type' => 'term_name',
				),
			)
		);
		$repeater->add_control(
			'allow_edit',
			array(
				'label'        => __( 'Allow Edit', 'frontend-admin' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'frontend-admin' ),
				'label_off'    => __( 'No', 'frontend-admin' ),
				'description'  => __( 'WARNING: allowing your users to change their username might affect your existing urls and their SEO rating', 'frontend-admin' ),
				'return_value' => 'true',
				'condition'    => array(
					'field_type' => 'username',
				),
			)
		);
		$repeater->add_control(
			'force_edit_password',
			array(
				'label'        => __( 'Force Edit', 'frontend-admin' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'frontend-admin' ),
				'label_off'    => __( 'No', 'frontend-admin' ),
				'return_value' => 'true',
				'condition'    => array(
					'field_type' => 'password',
				),
			)
		);
		$repeater->add_control(
			'edit_password',
			array(
				'label'       => __( 'Edit Password Button', 'frontend-admin' ),
				'label_block' => true,
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'Edit Password', 'frontend-admin' ),
				'placeholder' => __( 'Edit Password', 'frontend-admin' ),
				'dynamic'     => array(
					'active' => true,
				),
				'condition'   => array(
					'field_type'           => array( 'password' ),
					'force_edit_password!' => 'true',
				),
			)
		);
		$repeater->add_control(
			'cancel_edit_password',
			array(
				'label'       => __( 'Cancel Edit Button', 'frontend-admin' ),
				'label_block' => true,
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'Cancel', 'frontend-admin' ),
				'placeholder' => __( 'Cancel', 'frontend-admin' ),
				'dynamic'     => array(
					'active' => true,
				),
				'condition'   => array(
					'field_type'           => array( 'password' ),
					'force_edit_password!' => 'true',
				),
			)
		);

		if ( class_exists( 'woocommerce' ) ) {
			$this->inventory_controls( $repeater );
		}

		$repeater->add_control(
			'recaptcha_version',
			array(
				'label'     => __( 'Version', 'frontend-admin' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => array(
					'v2' => __( 'Version 2', 'frontend-admin' ),
					'v3' => __( 'Version 3', 'frontend-admin' ),
				),
				'default'   => 'v2',
				'condition' => array(
					'field_type' => 'recaptcha',
				),
			)
		);
		$repeater->add_control(
			'recaptcha_site_key',
			array(
				'label'       => __( 'Site Key', 'frontend-admin' ),
				'label_block' => true,
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => array(
					'active' => true,
				),
				'default'     => get_option( 'frontend_admin_google_recaptcha_site' ),
				'condition'   => array(
					'field_type' => 'recaptcha',
				),
			)
		);
		$repeater->add_control(
			'recaptcha_secret_key',
			array(
				'label'       => __( 'Secret Key', 'frontend-admin' ),
				'label_block' => true,
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => array(
					'active' => true,
				),
				'default'     => get_option( 'frontend_admin_google_recaptcha_secret' ),
				'condition'   => array(
					'field_type' => 'recaptcha',
				),
			)
		);
		$repeater->add_control(
			'recaptcha_note',
			array(
				'show_label' => false,
				'type'       => \Elementor\Controls_Manager::RAW_HTML,
				'raw'        => '<br>' . __( 'If you don\'t already have a site key and a secret, you may generate them here:', 'frontend-admin' ) . ' <a href="https://www.google.com/recaptcha/admin"> reCaptcha API Admin </a>',
				'condition'  => array(
					'field_type' => 'recaptcha',
				),
			)
		);
		
		$repeater->add_responsive_control(
			'field_width',
			array(
				'label'               => __( 'Width', 'frontend-admin' ) . ' (%)',
				'type'                => Controls_Manager::NUMBER,
				'min'                 => 10,
				'max'                 => 100,
				'default'             => 100,
				'required'            => true,
				'device_args'         => array(
					Controls_Stack::RESPONSIVE_TABLET => array(
						'max'      => 100,
						'required' => false,
					),
					Controls_Stack::RESPONSIVE_MOBILE => array(
						'default'  => 100,
						'required' => false,
					),
				),
				'min_affected_device' => array(
					Controls_Stack::RESPONSIVE_DESKTOP => Controls_Stack::RESPONSIVE_TABLET,
					Controls_Stack::RESPONSIVE_TABLET  => Controls_Stack::RESPONSIVE_TABLET,
				),
				'selectors'           => array(
					'{{WRAPPER}} {{CURRENT_ITEM}}' => 'width: {{VALUE}}%',
				),
				'condition'           => array(
					'field_type!' => array( 'step' ),
				),
			)
		);

		$repeater->add_responsive_control(
			'field_margin',
			array(
				'label'               => __( 'Margin', 'elementor-pro' ),
				'type'                => Controls_Manager::DIMENSIONS,
				'size_units'          => array( '%', 'px', 'em' ),
				'default'             => array(
					'unit'     => '%',
					'top'      => 'o',
					'bottom'   => 'o',
					'left'     => 'o',
					'right'    => 'o',
					'isLinked' => 'false',
				),
				'isLinked'            => 'false',
				'min_affected_device' => array(
					Controls_Stack::RESPONSIVE_DESKTOP => Controls_Stack::RESPONSIVE_TABLET,
					Controls_Stack::RESPONSIVE_TABLET  => Controls_Stack::RESPONSIVE_TABLET,
				),
				'selectors'           => array(
					'{{WRAPPER}} {{CURRENT_ITEM}}' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition'           => array(
					'field_type!' => array( 'step' ),
				),
			)
		);

		$repeater->add_responsive_control(
			'field_padding',
			array(
				'label'               => __( 'Padding', 'elementor-pro' ),
				'type'                => Controls_Manager::DIMENSIONS,
				'size_units'          => array( '%', 'px', 'em' ),
				'default'             => array(
					'top'      => 'o',
					'bottom'   => 'o',
					'left'     => 'o',
					'right'    => 'o',
					'isLinked' => 'false',
					'unit'     => '%',
				),
				'min'                 => 0,
				'isLinked'            => 'false',
				'min_affected_device' => array(
					Controls_Stack::RESPONSIVE_DESKTOP => Controls_Stack::RESPONSIVE_TABLET,
					Controls_Stack::RESPONSIVE_TABLET  => Controls_Stack::RESPONSIVE_TABLET,
				),
				'selectors'           => array(
					'{{WRAPPER}} {{CURRENT_ITEM}}' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition'           => array(
					'field_type!' => array( 'step' ),
				),
			)
		);

		$repeater->add_control(
			'recaptcha_theme',
			array(
				'label'     => __( 'Version', 'frontend-admin' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => array(
					'light' => __( 'Light', 'frontend-admin' ),
					'dark'  => __( 'Dark', 'frontend-admin' ),
				),
				'default'   => 'light',
				'condition' => array(
					'field_type'        => 'recaptcha',
					'recaptcha_version' => 'v2',
				),
			)
		);
		$repeater->add_control(
			'recaptcha_size',
			array(
				'label'     => __( 'Version', 'frontend-admin' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => array(
					'normal'  => __( 'Normal', 'frontend-admin' ),
					'compact' => __( 'Compact', 'frontend-admin' ),
				),
				'default'   => 'normal',
				'condition' => array(
					'field_type'        => 'recaptcha',
					'recaptcha_version' => 'v2',
				),
			)
		);
		$repeater->add_control(
			'recaptcha_hide_logo',
			array(
				'label'        => __( 'Hide Logo', 'frontend-admin' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'frontend-admin' ),
				'label_off'    => __( 'No', 'frontend-admin' ),
				'return_value' => 'true',
				'condition'    => array(
					'field_type'        => 'recaptcha',
					'recaptcha_version' => 'v3',
				),
			)
		);

		if ( class_exists( 'woocommerce' ) ) {

			$repeater->add_control(
				'attributes_sub_fields',
				array(
					'show_label' => false,
					'type'       => Controls_Manager::RAW_HTML,
					'raw'        => '<button class="sub-fields-open edit-icon" type="button" data-type="attribute">
						<span class="elementor-repeater__add-button__text">' . __( 'Manage Fields', 'frontend-admin' ) . '</span>
					</button>',
					'condition'  => array(
						'field_type' => 'attributes',
					),
				)
			);
			$repeater->add_control(
				'variations_sub_fields',
				array(
					'show_label' => false,
					'type'       => Controls_Manager::RAW_HTML,
					'raw'        => '<button class="sub-fields-open edit-icon" type="button" data-type="variable">
						<span class="elementor-repeater__add-button__text">' . __( 'Manage Fields', 'frontend-admin' ) . '</span>
					</button>',
					'condition'  => array(
						'field_type' => 'variations',
					),
				)
			);
		}

		$widget->add_control(
			'fields_selection',
			array(
				'show_label'  => false,
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'condition'   => array(
					'admin_forms_select' => '',
				),
				'title_field' => '<span style="text-transform: capitalize;">{{{ field_type.replace(/_/g, " ") }}}</span>',
				'default'     => $widget->form_defaults['fields'] ?? [ [ 'ACF_fields' ] ],
				'separator'   => 'after',
			)
		);

		if ( class_exists( 'woocommerce' ) && isset( $widget->form_defaults['custom_fields_save'] ) ) {

			$save_action = $widget->form_defaults['custom_fields_save'] ?? 'all';
			if ( $save_action == 'all' || $save_action == 'product' ) {
				$repeater = new \Elementor\Repeater();

				$repeater->add_control(
					'field_type',
					array(
						'type'    => Controls_Manager::HIDDEN,
						'default' => '',
					)
				);
				$repeater->add_control(
					'field_label_on',
					array(
						'label'        => __( 'Show Label', 'frontend-admin' ),
						'type'         => Controls_Manager::SWITCHER,
						'label_on'     => __( 'Yes', 'frontend-admin' ),
						'label_off'    => __( 'No', 'frontend-admin' ),
						'return_value' => 'true',
						'dynamic'      => array(
							'active' => true,
						),
					)
				);
				$repeater->add_control(
					'label',
					array(
						'label'     => __( 'Label', 'frontend-admin' ),
						'type'      => Controls_Manager::TEXT,
						'condition' => array(
							'field_label_on' => 'true',
						),
						'dynamic'   => array(
							'active' => true,
						),
					)
				);
				$repeater->add_control(
					'instructions',
					array(
						'label'   => __( 'Instructions', 'frontend-admin' ),
						'type'    => Controls_Manager::TEXTAREA,
						'dynamic' => array(
							'active' => true,
						),
					)
				);
				$repeater->add_control(
					'placeholder',
					array(
						'label'     => __( 'Placeholder', 'frontend-admin' ),
						'type'      => Controls_Manager::TEXT,
						'dynamic'   => array(
							'active' => true,
						),
						'condition' => array(
							'field_type' => 'name',
						),
					)
				);
				$repeater->add_control(
					'products_page',
					array(
						'label'     => __( 'Products Page', 'frontend-admin' ),
						'type'      => Controls_Manager::TEXT,
						'dynamic'   => array(
							'active' => true,
						),
						'condition' => array(
							'field_type' => 'locations',
						),
					)
				);
				$repeater->add_control(
					'for_variations',
					array(
						'label'     => __( 'Placeholder', 'frontend-admin' ),
						'type'      => Controls_Manager::TEXT,
						'dynamic'   => array(
							'active' => true,
						),
						'condition' => array(
							'field_type' => 'locations',
						),
					)
				);
				$repeater->add_control(
					'button_label',
					array(
						'label'     => __( 'Button Text', 'frontend-admin' ),
						'type'      => Controls_Manager::TEXT,
						'dynamic'   => array(
							'active' => true,
						),
						'condition' => array(
							'field_type' => 'custom_terms',
						),
					)
				);

				$widget->add_control(
					'attribute_fields',
					array(
						'show_label'    => false,
						'type'          => Controls_Manager::REPEATER,
						'fields'        => $repeater->get_controls(),
						'prevent_empty' => true,
						'item_actions'  => array(
							'add'       => false,
							'duplicate' => false,
							'remove'    => false,
							'sort'      => false,
						),
						'default'       => array(
							array(
								'field_type'     => 'name',
								'field_label_on' => 'true',
								'label'          => __( 'Name', 'frontend-admin' ),
								'instructions'   => '',
								'placeholder'    => __( 'Name', 'frontend-admin' ),
							),
							array(
								'field_type'     => 'locations',
								'field_label_on' => '',
								'label'          => __( 'Locations', 'frontend-admin' ),
								'instructions'   => '',
								'products_page'  => __( 'Visible on the product page', 'frontend-admin' ),
								'for_variations' => __( 'Used for variations', 'frontend-admin' ),
							),
							array(
								'field_type'     => 'custom_terms',
								'field_label_on' => 'true',
								'label'          => __( 'Value(s)', 'frontend-admin' ),
								'instructions'   => '',
								'button_label'   => __( 'Add Value', 'frontend-admin' ),
							),
							array(
								'field_type'     => 'global_terms',
								'field_label_on' => 'true',
								'label'          => __( 'Terms', 'frontend-admin' ),
								'instructions'   => '',
								'button_label'   => __( 'Add Value', 'frontend-admin' ),
							),
						),
						'title_field'   => '<span style="text-transform: capitalize;">{{{ field_type.replace(/_/g, " ") }}}</span>',
						'condition'     => array(
							'admin_forms_select' => '',
						),
					)
				);

				$repeater = new \Elementor\Repeater();

				$repeater->add_control(
					'field_type',
					array(
						'label'       => __( 'Field Type', 'frontend-admin' ),
						'type'        => Controls_Manager::SELECT,
						'label_block' => true,
						'placeholder' => __( 'Select Type', 'frontend-admin' ),
						'groups'      => array(
							'basic'     => array(
								'label'   => __( 'Product', 'frontend-admin' ),
								'options' => array(
									'description' => __( 'Description', 'frontend-admin' ),
									'image'       => __( 'Image', 'frontend-admin' ),
									'price'       => __( 'Price', 'frontend-admin' ),
									'sale_price'  => __( 'Sale Price', 'frontend-admin' ),
									'sku'         => __( 'SKU', 'frontend-admin' ),
								// 'tax_class' => __( 'Tax Class', 'frontend-admin' ),
								),
							),
							'inventory' => array(
								'label'   => __( 'Product Inventory', 'frontend-admin' ),
								'options' => array(
									'stock_status'     => __( 'Stock Status', 'frontend-admin' ),
									'manage_stock'     => __( 'Manage Stock', 'frontend-admin' ),
									'stock_quantity'   => __( 'Stock Quantity', 'frontend-admin' ),
									'allow_backorders' => __( 'Allow Backorders', 'frontend-admin' ),
								),
							),
						),
					)
				);
				
				$repeater->add_control(
					'field_label_on',
					array(
						'label'        => __( 'Show Label', 'frontend-admin' ),
						'type'         => Controls_Manager::SWITCHER,
						'label_on'     => __( 'Yes', 'frontend-admin' ),
						'label_off'    => __( 'No', 'frontend-admin' ),
						'return_value' => 'true',
						'default'      => 'true',
						'dynamic'      => array(
							'active' => true,
						),
					)
				);
				$repeater->add_control(
					'label',
					array(
						'label'     => __( 'Label', 'frontend-admin' ),
						'type'      => Controls_Manager::TEXT,
						'condition' => array(
							'field_label_on' => 'true',
						),
						'dynamic'   => array(
							'active' => true,
						),
					)
				);
				$repeater->add_control(
					'instructions',
					array(
						'label'   => __( 'Instructions', 'frontend-admin' ),
						'type'    => Controls_Manager::TEXTAREA,
						'dynamic' => array(
							'active' => true,
						),
					)
				);
				$repeater->add_control(
					'default_value',
					array(
						'label'     => __( 'Default Value', 'frontend-admin' ),
						'type'      => Controls_Manager::TEXT,
						'dynamic'   => array(
							'active' => true,
						),
						'condition' => array(
							'field_type' => array( 'sku', 'description' ),
						),
					)
				);
				$repeater->add_control(
					'default_number_value',
					array(
						'label'     => __( 'Default Value', 'frontend-admin' ),
						'type'      => Controls_Manager::NUMBER,
						'dynamic'   => array(
							'active' => true,
						),
						'condition' => array(
							'field_type' => array( 'price', 'sale_price' ),
						),
					)
				);
				$repeater->add_control(
					'default_image_value',
					array(
						'label'     => __( 'Default Featured Image', 'frontend-admin' ),
						'type'      => \Elementor\Controls_Manager::MEDIA,
						'condition' => array(
							'field_type' => array( 'image' ),
						),
					)
				);
				$repeater->add_control(
					'placeholder',
					array(
						'label'     => __( 'Placeholder', 'frontend-admin' ),
						'type'      => Controls_Manager::TEXT,
						'dynamic'   => array(
							'active' => true,
						),
						'condition' => array(
							'field_type' => array( 'sku', 'description' ),
						),
					)
				);
				$repeater->add_control(
					'number_placeholder',
					array(
						'label'     => __( 'Placeholder', 'frontend-admin' ),
						'type'      => Controls_Manager::NUMBER,
						'dynamic'   => array(
							'active' => true,
						),
						'condition' => array(
							'field_type' => array( 'price', 'sale_price' ),
						),
					)
				);

				$repeater->add_control(
					'prepend',
					array(
						'label'     => __( 'Prepend', 'frontend-admin' ),
						'type'      => Controls_Manager::TEXT,
						'dynamic'   => array(
							'active' => true,
						),
						'condition' => array(
							'field_type' => array( 'price', 'sale_price', 'sku' ),
						),
					)
				);

				$repeater->add_control(
					'append',
					array(
						'label'     => __( 'Append', 'frontend-admin' ),
						'type'      => Controls_Manager::TEXT,
						'dynamic'   => array(
							'active' => true,
						),
						'condition' => array(
							'field_type' => array( 'price', 'sale_price', 'sku' ),
						),
					)
				);

				$repeater->add_control(
					'minimum',
					array(
						'label'     => __( 'Minimum Value', 'frontend-admin' ),
						'type'      => Controls_Manager::NUMBER,
						'dynamic'   => array(
							'active' => true,
						),
						'condition' => array(
							'field_type' => array( 'price', 'sale_price' ),
						),
					)
				);
				$repeater->add_control(
					'maximum',
					array(
						'label'     => __( 'Maximum Value', 'frontend-admin' ),
						'type'      => Controls_Manager::NUMBER,
						'dynamic'   => array(
							'active' => true,
						),
						'condition' => array(
							'field_type' => array( 'price', 'sale_price' ),
						),
					)
				);

				$repeater->add_control(
					'required',
					array(
						'label'        => __( 'Required', 'frontend-admin' ),
						'type'         => Controls_Manager::SWITCHER,
						'label_on'     => __( 'Yes', 'frontend-admin' ),
						'label_off'    => __( 'No', 'frontend-admin' ),
						'return_value' => 'true',
						'dynamic'      => array(
							'active' => true,
						),
					)
				);
				$repeater->add_control(
					'hidden',
					array(
						'label'        => __( 'Hidden', 'frontend-admin' ),
						'type'         => Controls_Manager::SWITCHER,
						'label_on'     => __( 'Yes', 'frontend-admin' ),
						'label_off'    => __( 'No', 'frontend-admin' ),
						'return_value' => 'true',
					)
				);
				$repeater->add_control(
					'disabled',
					array(
						'label'        => __( 'Disabled', 'frontend-admin' ),
						'type'         => Controls_Manager::SWITCHER,
						'description'  => __( 'This will prevent users from editing the field and the data will not be sent.', 'frontend-admin' ),
						'label_on'     => __( 'Yes', 'frontend-admin' ),
						'label_off'    => __( 'No', 'frontend-admin' ),
						'return_value' => 'true',
					)
				);
				$this->inventory_controls( $repeater );

				$variable_fields = array(
					'description',
					'image',
					'price',
					'sale_price',
					'sku',
					'stock_status',
					'manage_stock',
					'stock_quantity',
					'allow_backorders',
				);
				$default_vfs     = array();
				foreach ( $variable_fields as $field_type ) {
					$field_label   = ucwords( str_replace( '_', ' ', $field_type ) );
					$default_vfs[] = array(
						'field_type'     => $field_type,
						'field_label_on' => 'true',
						'required'       => '',
						'label'          => __( $field_label, 'frontend-admin' ),
						'instructions'   => '',
					);
				}

				$widget->add_control(
					'variable_fields',
					array(
						'show_label'    => false,
						'type'          => Controls_Manager::REPEATER,
						'fields'        => $repeater->get_controls(),
						'prevent_empty' => true,
						'default'       => $default_vfs,
						'item_actions'  => array(
							'add'       => false,
							'duplicate' => true,
							'remove'    => true,
							'sort'      => true,
						),
						'title_field'   => '<span style="text-transform: capitalize;">{{{ field_type.replace(/_/g, " ") || \'' . __( 'Select Field Type', 'frontend-admin' ) . '\'}}}</span>',
						'condition'     => array(
							'admin_forms_select' => '',
						),
					)
				);
			}
		}
	}


	public function inventory_controls( $repeater ) {
		$repeater->add_control(
			'ui_on',
			array(
				'label'     => __( 'On Text', 'woocommerce' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'Yes', 'woocommerce' ),
				'dynamic'   => array(
					'active' => true,
				),
				'condition' => array(
					'field_type' => array( 'manage_stock', 'sold_individually', 'virtual', 'downloadable' ),
				),
			)
		);
		$repeater->add_control(
			'ui_off',
			array(
				'label'     => __( 'Off Text', 'woocommerce' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'No', 'woocommerce' ),
				'dynamic'   => array(
					'active' => true,
				),
				'condition' => array(
					'field_type' => array( 'manage_stock', 'sold_individually' ),
				),
			)
		);
		$repeater->add_control(
			'stock_choices',
			array(
				'show_label' => false,
				'type'       => Controls_Manager::RAW_HTML,
				'seperator'  => 'before',
				'raw'        => '<h3>Choices</h3>',
				'condition'  => array(
					'field_type' => 'stock_status',
				),
			)
		);
		$repeater->add_control(
			'instock',
			array(
				'label'     => __( 'In stock', 'woocommerce' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'In stock', 'woocommerce' ),
				'required'  => true,
				'dynamic'   => array(
					'active' => true,
				),
				'condition' => array(
					'field_type' => 'stock_status',
				),
			)
		);
		$repeater->add_control(
			'outofstock',
			array(
				'label'     => __( 'Out of stock', 'woocommerce' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'Out of stock', 'woocommerce' ),
				'required'  => true,
				'dynamic'   => array(
					'active' => true,
				),
				'condition' => array(
					'field_type' => 'stock_status',
				),
			)
		);
		$repeater->add_control(
			'backorder',
			array(
				'label'     => __( 'On backorder', 'woocommerce' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'On backorder', 'woocommerce' ),
				'required'  => true,
				'dynamic'   => array(
					'active' => true,
				),
				'condition' => array(
					'field_type' => 'stock_status',
				),
			)
		);
		$repeater->add_control(
			'backorder_choices',
			array(
				'show_label' => false,
				'type'       => Controls_Manager::RAW_HTML,
				'seperator'  => 'before',
				'raw'        => '<h4>Choices</h4>',
				'condition'  => array(
					'field_type' => 'allow_backorders',
				),
			)
		);
		$repeater->add_control(
			'do_not_allow',
			array(
				'label'     => __( 'Do not allow', 'woocommerce' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'Do not allow', 'woocommerce' ),
				'required'  => true,
				'dynamic'   => array(
					'active' => true,
				),
				'condition' => array(
					'field_type' => 'allow_backorders',
				),
			)
		);
		$repeater->add_control(
			'notify',
			array(
				'label'     => __( 'Notify', 'woocommerce' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'Allow, but notify customers', 'woocommerce' ),
				'required'  => true,
				'dynamic'   => array(
					'active' => true,
				),
				'condition' => array(
					'field_type' => 'allow_backorders',
				),
			)
		);
		$repeater->add_control(
			'allow',
			array(
				'label'     => __( 'Allow', 'woocommerce' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'Allow', 'woocommerce' ),
				'required'  => true,
				'dynamic'   => array(
					'active' => true,
				),
				'condition' => array(
					'field_type' => 'allow_backorders',
				),
			)
		);
	}

	

	public function submit_limit_setting( $widget ) {
		$widget->add_control(
			'limit_reached',
			array(
				'label'       => __( 'Limit Reached Message', 'frontend-admin' ),
				'type'        => Controls_Manager::SELECT,
				'label_block' => true,
				'default'     => 'show_message',
				'options'     => array(
					'show_message'   => __( 'Limit Message', 'frontend-admin' ),
					'custom_content' => __( 'Custom Content', 'frontend-admin' ),
					'show_nothing'   => __( 'Nothing', 'frontend-admin' ),
				),
			)
		);
		$widget->add_control(
			'limit_submit_message',
			array(
				'label'       => __( 'Reached Limit Message', 'frontend-admin' ),
				'type'        => Controls_Manager::TEXTAREA,
				'label_block' => true,
				'rows'        => 4,
				'default'     => __( 'You have already submitted this form the maximum amount of times that you are allowed', 'frontend-admin' ),
				'placeholder' => __( 'you have already submitted this form the maximum amount of times that you are allowed', 'frontend-admin' ),
				'condition'   => array(
					'limit_reached' => 'show_message',
				),
			)
		);
		$widget->add_control(
			'limit_submit_content',
			array(
				'label'       => __( 'Reached Limit Content', 'frontend-admin' ),
				'type'        => Controls_Manager::WYSIWYG,
				'placeholder' => 'You have already submitted this form the maximum amount of times that you are allowed',
				'label_block' => true,
				'render_type' => 'none',
				'condition'   => array(
					'limit_reached' => 'custom_content',
				),
			)
		);

		$repeater = new \Elementor\Repeater();

		$repeater->add_control(
			'rule_name',
			array(
				'label'       => __( 'Rule Name', 'frontend-admin' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => __( 'Rule Name', 'frontend-admin' ),
				'label_block' => true,
			)
		);

		$repeater->add_control(
			'allowed_submits',
			array(
				'label'   => __( 'Allowed Submissions', 'frontend-admin' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => '',
			)
		);

		$repeater->add_control(
			'limit_to_everyone',
			array(
				'label'        => __( 'Limit For Everyone', 'frontend-admin' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'frontend-admin' ),
				'label_off'    => __( 'No', 'frontend-admin' ),
				'return_value' => 'true',
			)
		);

		$user_roles = feadmin_get_user_roles();

		$repeater->add_control(
			'limit_by_role',
			array(
				'label'       => __( 'Limit By Role', 'frontend-admin' ),
				'type'        => Controls_Manager::SELECT2,
				'label_block' => true,
				'multiple'    => true,
				'default'     => 'subscriber',
				'options'     => $user_roles,
				'condition'   => array(
					'limit_to_everyone' => '',
				),
			)
		);
		if ( ! class_exists( 'ElementorPro\Modules\QueryControl\Module' ) ) {
			$repeater->add_control(
				'limit_by_user',
				array(
					'label'       => __( 'Limit By User', 'frontend-admin' ),
					'type'        => Controls_Manager::TEXT,
					'placeholder' => __( '18', 'frontend-admin' ),
					'description' => __( 'Enter a commma seperated list of user ids', 'frontend-admin' ),
					'condition'   => array(
						'limit_to_everyone' => '',
					),
				)
			);
		} else {
			$repeater->add_control(
				'limit_by_user',
				array(
					'label'        => __( 'Limit By User', 'frontend-admin' ),
					'type'         => Query_Module::QUERY_CONTROL_ID,
					'label_block'  => true,
					'autocomplete' => array(
						'object'  => Query_Module::QUERY_OBJECT_USER,
						'display' => 'detailed',
					),
					'multiple'     => true,
					'condition'    => array(
						'limit_to_everyone' => '',
					),
				)
			);
		}

		$widget->add_control(
			'limiting_rules',
			array(
				'label'         => __( 'Add Limiting Rules', 'frontend-admin' ),
				'type'          => Controls_Manager::REPEATER,
				'fields'        => $repeater->get_controls(),
				'prevent_empty' => false,
				'default'       => array(
					array(
						'rule_name' => __( 'Subscribers', 'frontend-admin' ),
					),
				),
				'title_field'   => '{{{ rule_name }}}',
			)
		);

	}


	public function register_actions_controls( $widget ) {
		global $fea_instance;
		$tab = Controls_Manager::TAB_CONTENT;
		//if is section, column, or container make tab Layout
		$name = $widget->get_name();
		if( in_array( $name, ['section', 'column', 'container'] ) ){
			$tab = Controls_Manager::TAB_LAYOUT;
		}
		$condition = $this->conditions( [], $widget );
		
		$widget->start_controls_section(
			'actions_section',
			array(
				'label'     => __( 'Actions', 'frontend-admin' ),
				'tab'       => $tab,
				'condition' => $condition,
			)
		);
		if ( isset( $fea_instance->remote_actions ) ) {
			$remote_actions = array();
			foreach ( $fea_instance->remote_actions as $name => $action ) {
				$remote_actions[ $name ] = $action->get_label();
				unset( $remote_actions['mailchimp'] );
			}
			$widget->add_control(
				'more_actions',
				array(
					'label'       => __( 'Submit Actions', 'frontend-admin' ),
					'type'        => Controls_Manager::SELECT2,
					'label_block' => true,
					'multiple'    => true,
					'options'     => $remote_actions,
					'render_type' => 'none',
				)
			);
		} else {
			$widget->add_control(
				'more_actions_promo',
				array(
					'type'            => Controls_Manager::RAW_HTML,
					'raw'             => __( '<p><a target="_blank" href="https://www.dynamiapps.com/"><b>Go pro</b></a> to unlock more actions.</p>', 'frontend-admin' ),
					'content_classes' => 'acf-fields-note',
				)
			);
		}

		$redirect_options = array(
			'current'     => __( 'Stay on Current Page/Post', 'frontend-admin' ),
			'custom_url'  => __( 'Custom Url', 'frontend-admin' ),
			'referer_url' => __( 'Referer', 'frontend-admin' ),
			'post_url'    => __( 'Post Url', 'frontend-admin' ),
			'none'       => __( 'None', 'frontend-admin' ),
		);

		$redirect_options = apply_filters( 'frontend_admin/forms/redirect_options', $redirect_options );

		$widget->add_control(
			'redirect',
			array(
				'label'       => __( 'Redirect After Submit', 'frontend-admin' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'current',
				'options'     => $redirect_options,
				'render_type' => 'none',
			)
		);
		
		$widget->add_control(
			'open_modal',
			array(
				'label'        => __( 'Leave Modal Open After Submit', 'frontend-admin' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'true',
				'condition'    => array(
					'show_in_modal' => 'true',
					'render_type'   => 'none',
				),
			)
		);
		$widget->add_control(
			'redirect_action',
			array(
				'label'       => __( 'After Reload', 'frontend-admin' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'clear',
				'options'     => array(
					''		=> __( 'Nothing', 'frontend-admin' ),
					'clear' => __( 'Clear Form', 'frontend-admin' ),
					'edit'  => __( 'Edit Content', 'frontend-admin' ),
				),
				'render_type' => 'none',
			)
		);
		$widget->add_control(
			'custom_url',
			array(
				'label'       => __( 'Custom Url', 'frontend-admin' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => __( 'Enter Url Here', 'frontend-admin' ),
				'options'     => false,
				'show_label'  => false,
				'condition'   => array(
					'redirect' => 'custom_url',
				),
				'dynamic'     => array(
					'active' => true,
				),
				'render_type' => 'none',
			)
		);

		$widget->add_control(
			'show_success_message',
			array(
				'label'        => __( 'Show Success Message', 'frontend-admin' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'frontend-admin' ),
				'label_off'    => __( 'No', 'frontend-admin' ),
				'default'      => 'true',
				'return_value' => 'true',
				'render_type'  => 'none',
			)
		);
		$success = $widget->form_defaults['success_message'] ?? __( 'Form has been submitted successfully.', 'frontend-admin' );
		$widget->add_control(
			'update_message',
			array(
				'label'       => __( 'Submit Message', 'frontend-admin' ),
				'type'        => Controls_Manager::TEXTAREA,
				'default'     => $success,
				'placeholder' => $success,
				'dynamic'     => array(
					'active' => true,
				),
				'condition'   => array(
					'show_success_message' => 'true',
				),
			)
		);
		$widget->add_control(
			'error_message',
			array(
				'label'       => __( 'Error Message', 'frontend-admin' ),
				'type'        => Controls_Manager::TEXTAREA,
				'description' => __( 'There shouldn\'t be any problems with the form submission, but if there are, this is what your users will see. If you are expeiencing issues, try and changing your cache settings and reach out to ', 'frontend-admin' ) . 'support@dynamiapps.com',
				'default'     => __( 'Please fix the form errors and try again.', 'frontend-admin' ),
				'dynamic'     => array(
					'active' => true,
				),
				'render_type' => 'none',
			)
		);
		//default required messaged
		$widget->add_control(
			'required_message',
			array(
				'label'       => __( 'Required Message', 'frontend-admin' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'This field is required.', 'frontend-admin' ),
				'dynamic'     => array(
					'active' => true,
				),
				'render_type' => 'none',
			)
		);
		//email veified message
		$widget->add_control(
			'email_verified_message',
			array(
				'label'       => __( 'Email Verified Message', 'frontend-admin' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'Email has been verified.', 'frontend-admin' ),
				'dynamic'     => array(
					'active' => true,
				),
				'render_type' => 'none',
				'condition'   => array(
					'save_all_data' => 'verify_email'
				),
			)
		);
		$widget->end_controls_section();
	}

	public function action_controls_section( $widget ) {
		global $fea_instance;
		if ( isset( $fea_instance->remote_actions ) ) {
			$remote_actions = $fea_instance->remote_actions;
			foreach ( $remote_actions as $action ) {
				$action->register_settings_section( $widget );
			}
		}
		
		$local_actions = $fea_instance->local_actions;
		
		foreach ( $local_actions as $name => $action ) {			
			$action->register_settings_section( $widget );
		}
		
	}

	public function control_tab( $tab, $element ) {
		$name = $element->get_name();
		if( in_array( $name, ['section', 'column', 'container'] ) ){
			$tab = \Elementor\Controls_Manager::TAB_LAYOUT;
		}
		return $tab;
	}

	public function conditions( $conditions, $element ) {
		$name = $element->get_name();

		if( ! $conditions ){
			$conditions = array();
		}

		if( in_array( $name, ['section', 'column', 'container'] ) ){
			$conditions['form_container'] = 'yes';
		}else{
			$conditions['admin_forms_select'] = '';
		}
		return $conditions;
	}


	public function __construct() {
		add_action( 'frontend_admin/display_section', array( $this, 'register_display_section' ) );
		add_action( 'frontend_admin/fields_controls', array( $this, 'fields_controls' ) );
		add_action( 'frontend_admin/elementor/action_controls', array( $this, 'register_actions_controls' ) );
		add_action( 'frontend_admin/elementor/actions_controls', array( $this, 'action_controls_section' ) );

		add_filter( 'frontend_admin/elementor/form_widget/control_tab', array( $this, 'control_tab' ), 10, 2 );
		add_filter( 'frontend_admin/elementor/form_widget/conditions', array( $this, 'conditions' ), 10, 2 );
		
	}

}

new ContentTab();
