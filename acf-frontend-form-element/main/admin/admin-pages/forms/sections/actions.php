<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$redirect_options = array(
	'current'    => __( 'Reload Current Page', 'frontend-admin' ),
	'custom_url' => __( 'Custom URL', 'frontend-admin' ),
	'referer'    => __( 'Referer', 'frontend-admin' ),
	'post_url'   => __( 'Post URL', 'frontend-admin' ),
	'none'       => __( 'None', 'frontend-admin' ),
);

$redirect_options = apply_filters( 'frontend_admin/forms/redirect_options', $redirect_options );

$fields = array(
	array(
		'key'           => 'redirect',
		'label'         => __( 'Redirect After Submit', 'frontend-admin' ),
		'type'          => 'select',
		'instructions'  => '',
		'required'      => 0,
		'wrapper'       => array(
			'width' => '',
			'class' => '',
			'id'    => '',
		),
		'choices'       => $redirect_options,
		'allow_null'    => 0,
		'multiple'      => 0,
		'ui'            => 0,
		'return_format' => 'value',
		'ajax'          => 0,
		'placeholder'   => '',
	),
	array( 
		'key'               => 'redirect_action',
		'label'             => __( 'After Reload', 'frontend-admin' ),
		'type'              => 'select',
		'instructions'      => '',
		'required'          => 0,
		'choices'           => array(
			'none' => __( 'None', 'frontend-admin' ),
			'clear' => __( 'Clear Form', 'frontend-admin' ),
			'edit' => __( 'Edit Content', 'frontend-admin' ),
		),
	),
	array(
		'key'               => 'custom_url',
		'label'             => __( 'Custom Url', 'frontend-admin' ),
		'type'              => 'url',
		'instructions'      => '',
		'required'          => 0,
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
	),
	array(
		'key'               => 'show_update_message',
		'label'             => __( 'Success Message', 'frontend-admin' ),
		'type'              => 'true_false',
		'instructions'      => '',
		'required'          => 0,
		'conditional_logic' => 0,
		'message'           => '',
		'ui'                => 1,
		'ui_on_text'        => '',
		'ui_off_text'       => '',
	),
	array(
		'key'               => 'update_message',
		'label'             => '',
		'field_label_hide'  => true,
		'type'              => 'textarea',
		'instructions'      => '',
		'required'          => 0,
		'conditional_logic' => array(
			array(
				array(
					'field'    => 'show_update_message',
					'operator' => '==',
					'value'    => '1',
				),
			),
		),
		'placeholder'       => '',
		'maxlength'         => '',
		'rows'              => '2',
		'new_lines'         => '',
	),
	array(
		'key'              => 'error_message',
		'label'            => __( 'Error Message', 'frontend-admin' ),
		'type'             => 'textarea',
		'instructions'     => __( 'Add a custom validation error message', 'frontend-admin' ),
		'required'         => 0,
		'placeholder'      => __( 'There has been an error. Please fix the fields that need attention', 'frontend-admin' ),
		'maxlength'        => '',
		'rows'             => '2',
		'new_lines'        => '',
	),
	array(
		'key'			   => 'default_required_message',
		'label'			   => __( 'Default Required Message', 'frontend-admin' ),
		'type'			   => 'text',
		'instructions'	   => __( 'This message will be used for all required fields if a custom message is not set', 'frontend-admin' ),
		'required'		   => 0,
		'placeholder'	   => __( 'This field is required', 'frontend-admin' ),
	),
	//email verified message if email verification is enabled
	array(
		'key'              => 'email_verified_message',
		'label'            => __( 'Email Verified Message', 'frontend-admin' ),
		'type'             => 'textarea',
		'instructions'     => __( 'Add a custom message for email verification', 'frontend-admin' ),
		'required'         => 0,
		'placeholder'      => __( 'Your email has been verified', 'frontend-admin' ),
		'default_value'    => __( 'Your email has been verified', 'frontend-admin' ),
		'conditional_logic' => array(
			array(
				array(
					'field'    => 'save_all_data',
					'operator' => '==contains',
					'value'    => 'verify_email',
				),
			),
		),
		'maxlength'        => '',
		'rows'             => '2',
		'new_lines'        => '',
	),
);

$fields = apply_filters( 'frontend_admin/forms/settings/submit_actions', $fields );

return $fields;
