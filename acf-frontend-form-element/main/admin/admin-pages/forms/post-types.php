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
		'public'            => true,
		'show_ui'           => true,
		'show_in_menu'      =>  'fea-settings',
		'menu_position'     => 80,
		'show_in_admin_bar' => true,
		'can_export'        => true,
		'rewrite'           => array(
			'with_front' => true,
			'slug'       => $dashboard_slug,
		),
		'capability_type'   => 'page',
		'query_var'         => false,
	);
	register_post_type( 'admin_form', $args );

	add_filter(
		'post_updated_messages',
		function ( $messages ) {
			$messages['admin_form'] = array(
				'',
				__( 'Form updated.' ),
				__( 'Custom field updated.' ),
				__( 'Custom field deleted.' ),
				__( 'Form updated.' ),
				'',
				__( 'Form published.' ),
				__( 'Form saved.' ),
				__( 'Form submitted.' ),
				'',
				__( 'Form draft updated.' ),
			);
			return $messages;
		}
	);


	/*
		 $labels = array(
		'name'                  => _x( 'Templates', 'Post Type General Name', 'frontend-admin' ),
		'singular_name'         => _x( 'Template', 'Post Type Singular Name', 'frontend-admin' ),
		'menu_name'             => __( 'Templates', 'frontend-admin' ),
		'name_admin_bar'        => __( 'Template', 'frontend-admin' ),
		'archives'              => __( 'Template Archives', 'frontend-admin' ),
		'all_items'             => __( 'Templates', 'frontend-admin' ),
		'add_new_item'          => __( 'Add New Template', 'frontend-admin' ),
		'add_new'               => __( 'Add New', 'frontend-admin' ),
		'new_item'              => __( 'New Template', 'frontend-admin' ),
		'edit_item'             => __( 'Edit Template', 'frontend-admin' ),
		'update_item'           => __( 'Update Template', 'frontend-admin' ),
		'view_item'             => __( 'View Template', 'frontend-admin' ),
		'search_items'          => __( 'Search Template', 'frontend-admin' ),
		'not_found'             => __( 'Not found', 'frontend-admin' ),
		'not_found_in_trash'    => __( 'Not found in Trash', 'frontend-admin' ),
		'items_list'            => __( 'Templates list', 'frontend-admin' ),
		'item_published'        => __( 'Settings Saved', 'frontend-admin' ),
		'item_updated'          => __( 'Settings Saved', 'frontend-admin' ),
		'items_list_navigation' => __( 'Templates list navigation', 'frontend-admin' ),
		'filter_items_list'     => __( 'Filter templates list', 'frontend-admin' ),
	);

	$args = array(
		'label'                 => __( 'Template', 'frontend-admin' ),
		'description'           => __( 'Template', 'frontend-admin' ),
		'labels'                => $labels,
		'supports'              => array( 'title', 'editor' ),
		'show_in_rest'          => true,
		'hierarchical'          => false,
		'public'                => false,
		'show_ui'               => true,
		'show_in_menu'          => 'frontend-admin-settings',
		'menu_position'         => 80,
		'show_in_admin_bar'     => false,
		'can_export'            => true,
		'capability_type'       => 'page',
		'query_var'                => false,
	);
	register_post_type( 'fea-template', $args ); */


	add_filter(
		'post_updated_messages',
		function ( $messages ) {
			$messages['admin_template'] = array(
				'',
				__( 'Template updated.' ),
				__( 'Custom field updated.' ),
				__( 'Custom field deleted.' ),
				__( 'Template updated.' ),
				'',
				__( 'Template published.' ),
				__( 'Template saved.' ),
				__( 'Template submitted.' ),
				'',
				__( 'Template draft updated.' ),
			);
			return $messages;
		}
	);

	do_action( 'frontend_admin/post_types' );

endif;
