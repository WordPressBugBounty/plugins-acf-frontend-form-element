<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( function_exists( 'register_post_type' ) ) :
	$dashboard_slug = get_option( 'frontend_admin_dashboard_slug' );
	if ( ! $dashboard_slug ) {
		$dashboard_slug = 'frontend-dashboard';
	}

	$labels = array(
		'name'                  => _x( 'Forms', 'Post Type General Name', 'frontend-admin' ),
		'singular_name'         => _x( 'Form', 'Post Type Singular Name', 'frontend-admin' ),
		'menu_name'             => __( 'Forms', 'frontend-admin' ),
		'name_admin_bar'        => __( 'Form', 'frontend-admin' ),
		'archives'              => __( 'Form Archives', 'frontend-admin' ),
		'all_items'             => __( 'Forms', 'frontend-admin' ),
		'add_new_item'          => __( 'Add New Form', 'frontend-admin' ),
		'add_new'               => __( 'Add New', 'frontend-admin' ),
		'new_item'              => __( 'New Form', 'frontend-admin' ),
		'edit_item'             => __( 'Edit Form', 'frontend-admin' ),
		'update_item'           => __( 'Update Form', 'frontend-admin' ),
		'view_item'             => __( 'View Form', 'frontend-admin' ),
		'search_items'          => __( 'Search Form', 'frontend-admin' ),
		'not_found'             => __( 'Not found', 'frontend-admin' ),
		'not_found_in_trash'    => __( 'Not found in Trash', 'frontend-admin' ),
		'items_list'            => __( 'Forms list', 'frontend-admin' ),
		'item_published'        => __( 'Settings Saved', 'frontend-admin' ),
		'item_updated'          => __( 'Settings Saved', 'frontend-admin' ),
		'items_list_navigation' => __( 'Forms list navigation', 'frontend-admin' ),
		'filter_items_list'     => __( 'Filter forms list', 'frontend-admin' ),
	);

	$args = array(
		'label'             => __( 'Form', 'frontend-admin' ),
		'description'       => __( 'Form', 'frontend-admin' ),
		'labels'            => $labels,
		'supports'          => false,
		'show_in_rest'      => true,
		'hierarchical'      => false,
		'public'            => false,
		'show_ui'           => true,
		'show_in_menu'      =>  'fea-settings',
		'menu_position'     => 80,
		'show_in_admin_bar' => true,
		'can_export'        => true,
		'rewrite'           => array(
			'with_front' => false,
			'slug'       => $dashboard_slug,
		),
		    'capability_type' => 'post',
    	'map_meta_cap' => true,
		'capabilities' => [
			'create_posts' => 'manage_options',
			'edit_posts' => 'manage_options',
			'edit_others_posts' => 'manage_options',
			'publish_posts' => 'manage_options',
			'read_private_posts' => 'manage_options',
			'delete_posts' => 'manage_options',
		],
		'query_var'         => false,
	);
	register_post_type( 'admin_form', $args );

	add_filter(
		'post_updated_messages',
		function ( $messages ) {
			$messages['admin_form'] = array(
				'',
				__( 'Form updated.', 'frontend-admin' ),
				__( 'Custom field updated.', 'frontend-admin' ),
				__( 'Custom field deleted.', 'frontend-admin' ),
				__( 'Form updated.', 'frontend-admin' ),
				'',
				__( 'Form published.', 'frontend-admin' ),
				__( 'Form saved.', 'frontend-admin' ),
				__( 'Form submitted.', 'frontend-admin' ),
				'',
				__( 'Form draft updated.', 'frontend-admin' ),
			);
			return $messages;
		}
	);



	do_action( 'frontend_admin/post_types' );

endif;
