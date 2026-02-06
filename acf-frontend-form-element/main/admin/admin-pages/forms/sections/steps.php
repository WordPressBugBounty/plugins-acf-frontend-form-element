<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


$tabs    = array(
	'field'    => 'steps_display',
	'operator' => '==contains',
	'value'    => 'tabs',
);
$counter = array(
	'field'    => 'steps_display',
	'operator' => '==contains',
	'value'    => 'counter',
);
$fields  = array(
	array(
		'key'          => 'validate_steps',
		'label'        => __( 'Validate Each Step', 'frontend-admin' ),
		'type'         => 'true_false',
		'instructions' => '',
		'required'     => 0,
		'message'      => '',
		'ui'           => 1,
		'ui_on_text'   => '',
		'ui_off_text'  => '',
		'wrapper'      => array(
			'width' => '50',
			'class' => '',
			'id'    => '',
		),
	),
	array(
		'key'          => 'steps_display',
		'label'        => __( 'Steps Display', 'frontend-admin' ),
		'type'         => 'select',
		'instructions' => '',
		'required'     => 0,
		'wrapper'      => array(
			'width' => '',
			'class' => '',
			'id'    => '',
		),
		'multiple'     => 1,
		'ui'           => 1,
		'allow_null'   => 1,
		'choices'      => array(
			'tabs'    => __( 'Tabs', 'frontend-admin' ),
			'counter' => __( 'Counter', 'frontend-admin' ),
		),
		'layout'       => 'horizontal',
	),
	array(
		'key'               => 'steps_tabs_display',
		'label'             => __( 'Display Tabs On...', 'frontend-admin' ),
		'type'              => 'checkbox',
		'instructions'      => '',
		'required'          => 0,
		'wrapper'           => array(
			'width' => '',
			'class' => '',
			'id'    => '',
		),
		'choices'           => array(
			'desktop' => __( 'Desktop', 'frontend-admin' ),
			'tablet'  => __( 'Tablet', 'frontend-admin' ),
			'phone'   => __( 'Mobile', 'frontend-admin' ),
		),
		'layout'            => 'horizontal',
		'conditional_logic' => array(
			array(
				$tabs,
			),
		),
	),
	array(
		'key'               => 'steps_counter_display',
		'label'             => __( 'Display Counter On...', 'frontend-admin' ),
		'type'              => 'checkbox',
		'instructions'      => '',
		'required'          => 0,
		'wrapper'           => array(
			'width' => '',
			'class' => '',
			'id'    => '',
		),
		'choices'           => array(
			'desktop' => __( 'Desktop', 'frontend-admin' ),
			'tablet'  => __( 'Tablet', 'frontend-admin' ),
			'phone'   => __( 'Mobile', 'frontend-admin' ),
		),
		'layout'            => 'horizontal',
		'conditional_logic' => array(
			array(
				$counter,
			),
		),
	),
	array(
		'key'               => 'tabs_align',
		'label'             => __( 'Tabs Align', 'frontend-admin' ),
		'type'              => 'radio',
		'instructions'      => '',
		'required'          => 0,
		'wrapper'           => array(
			'width' => '',
			'class' => '',
			'id'    => '',
		),
		'choices'           => array(
			'horizontal' => __( 'Top', 'elementor' ),
			'vertical'   => __( 'Side', 'elementor' ),
		),
		'layout'            => 'horizontal',
		'conditional_logic' => array(
			array(
				$tabs,
			),
		),
	),
	array(
		'key'               => 'counter_text',
		'label'             => __( 'Counter Text', 'frontend-admin' ),
		'type'              => 'text',
		'instructions'      => __( 'Use [current_step] to display the current step and [total_steps] to show the total amount of steps', 'frontend-admin' ),
		'required'          => 0,
		'wrapper'           => array(
			'width' => '',
			'class' => '',
			'id'    => '',
		),
		'placeholder'       => '',
		'conditional_logic' => array(
			array(
				$counter,
			),
		),
	),
	array(
		'key'               => 'step_number',
		'label'             => __( 'Step Number In Tabs', 'frontend-admin' ),
		'type'              => 'true_false',
		'instructions'      => '',
		'required'          => 0,
		'conditional_logic' => array(
			array(
				$tabs,
			),
		),
		'message'           => '',
		'ui'                => 1,
		'ui_on_text'        => '',
		'ui_off_text'       => '',
	),
	array(
		'key'               => 'tab_links',
		'label'             => __( 'Link to Step in Tabs', 'frontend-admin' ),
		'type'              => 'true_false',
		'instructions'      => '',
		'required'          => 0,
		'conditional_logic' => array(
			array(
				$tabs,
			),
		),
		'message'           => '',
		'ui'                => 1,
		'ui_on_text'        => '',
		'ui_off_text'       => '',
	),
);

return $fields;
