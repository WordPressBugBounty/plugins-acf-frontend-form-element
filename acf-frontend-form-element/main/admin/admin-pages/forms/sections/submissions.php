<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
global $post;
$form_shortcode = '[frontend_admin submissions="' . $post->ID . '"]';
$icon_path      = '<span class="dashicons dashicons-admin-page"></span>';

$save_submissions = array(
	array(
		array(
			'field'    => 'save_form_submissions',
			'operator' => '==',
			'value'    => '1',
		),
	),
);

$data_types = array(
	'submission'    => __( 'Submission Only', 'frontend-admin' ),
	'post'    => __( 'Post', 'frontend-admin' ),
	'user'    => __( 'User', 'frontend-admin' ),
	'term'    => __( 'Term', 'frontend-admin' ),
	'options' => __( 'Site Options', 'frontend-admin' ),
);
$requirments = array(
	'require_approval' => __( 'Admin Approval', 'frontend-admin' ),
	'verify_email'     => __( 'Email is Verified', 'frontend-admin' ),
);

if ( class_exists( 'woocommerce' ) ) {
	$data_types['product'] = __( 'Product', 'frontend-admin' );

	//$requirments['woo_checkout'] = __( 'Woocommerce Checkout', 'frontend-admin' );

}


$fields = array(
	array(
		'key'              => 'custom_fields_save',
		'label'            => __( 'Save Custom Fields to...', 'frontend-admin' ),
		'field_label_hide' => 0,
		'type'             => 'select',
		'instructions'     => '',
		'required'         => 0,
		'choices'          => $data_types,
		'allow_null'       => 0,
		'multiple'         => 0,
		'ui'               => 0,
		'return_format'    => 'value',
		'ajax'             => 0,
		'placeholder'      => '',
	),
	array(
		'key'               => 'save_form_submissions',
		'label'             => __( 'Save Form Submissions', 'frontend-admin' ),
		'type'              => 'true_false',
		'instructions'      => '',
		'required'          => 0,
		'conditional_logic' => 0,
		'wrapper'           => array(
			'width' => '',
			'class' => '',
			'id'    => '',
		),
		'default_value'     => get_option( 'frontend_admin_save_submissions' ),
		'message'           => '',
		'ui'                => 1,
		'ui_on_text'        => '',
		'ui_off_text'       => '',
	),
	array(
		'key'                   => 'submission_title',
		'label'                 => __( 'Submission Title', 'frontend-admin' ),
		'type'                  => 'text',
		'instructions'          => __( 'By default, the submission title will be the first string value in the form. Dynamically set this to something more descriptive.', 'frontend-admin' ),
		'required'              => 0,
		'placeholder'           => __( 'New Post Submitted: [post:title]', 'frontend-admin' ),
		'conditional_logic'     => $save_submissions,
		'dynamic_value_choices' => 1,
	),
	array(
		'key'               => 'save_all_data',
		'label'             => __( 'Submission Requirements', 'frontend-admin' ),
		'type'              => 'select',
		'instructions'      => __( 'Data will not be saved until these requirements are met.', 'frontend-admin' ),
		'required'          => 0,
		'conditional_logic' => $save_submissions,
		'choices'           => $requirments,
		'allow_null'        => 1,
		'multiple'          => 1,
		'ui'                => 1,
		'return_format'     => 'value',
		'ajax'              => 0,
		'placeholder'       => __( 'None', 'frontend-admin' ),
	),
	array(
		'key'               => 'submissions_list_shortcode',
		'label'             => __( 'Submissions Approval Shortcode', 'frontend-admin' ),
		'type'              => 'message',
		'instructions'      => __( 'Use this shortcode to show a list of this form\'s submissions.', 'frontend-admin' ),
		'message'           => sprintf( '<code>%s</code> ', $form_shortcode ) . '<button type="button" data-prefix="frontend_admin submissions" data-value="' . $post->ID . '" class="copy-shortcode"> ' . $icon_path .
		' ' . __( 'Copy Code', 'frontend-admin' ) . '</button>',
		'conditional_logic' => $save_submissions,
	),
	array(
		'key'               => 'no_submissions_message',
		'label'             => __( 'No Submissions Message', 'frontend-admin' ),
		'type'              => 'textarea',
		'instructions'      => __( 'Show a message if no submissions have been received yet. Leave blank for no message.', 'frontend-admin' ),
		'required'          => 0,
		'rows'              => 3,
		'placeholder'       => __( 'There are no submissions for this form.', 'frontend-admin' ),
		'conditional_logic' => $save_submissions,
	),
	array(
		'key'               => 'total_submissions',
		'label'             => __( 'Total Submissions', 'frontend-admin' ),
		'type'              => 'number',
		'instructions'      => __( 'Limit the amount of shown in total.', 'frontend-admin' ),
		'conditional_logic' => $save_submissions,
		'placeholder'       => __( 'All', 'frontend-admin' ),
		'min'               => 1,
	),
	array(
		'key'               => 'submissions_per_page',
		'label'             => __( 'Number of Submissions Per Load', 'frontend-admin' ),
		'type'              => 'number',
		'instructions'      => __( 'Limit the amount of submissions loaded each time. Default is 10', 'frontend-admin' ),
		'conditional_logic' => $save_submissions,
		'placeholder'       => 10,
		'min'               => 1,
	),
);


return $fields;
