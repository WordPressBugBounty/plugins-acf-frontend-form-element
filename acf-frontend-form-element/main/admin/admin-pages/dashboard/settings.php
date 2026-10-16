<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
/**
 * Handles the admin part of the dashboard pages
 *
 * @since 1.0.0
 */
class Dashboard_Pages {

	
	function __construct() {
		add_action( 'init', array( $this, 'register_post_types' ) );
	}

	public function register_post_types() {
		if ( function_exists( 'register_post_type' ) ) :
			$dashboard_slug = get_option( 'frontend_admin_dashboard_slug' );
			if ( ! $dashboard_slug ):
				$dashboard_slug = 'dashboard';
			endif;

			$labels = array(
				'name'                  => _x( 'Dashboard Pages', 'Post Type General Name', 'frontend-admin' ),
				'singular_name'         => _x( 'Dashboard Page', 'Post Type Singular Name', 'frontend-admin' ),
				'menu_name'             => __( 'Dashboard Pages', 'frontend-admin' ),
				'name_admin_bar'        => __( 'Dashboard Page', 'frontend-admin' ),
				'archives'              => __( 'Dashboard Page Archives', 'frontend-admin' ),
				'all_items'             => __( 'Dashboard Pages', 'frontend-admin' ),
				'add_new_item'          => __( 'Add New Dashboard Page', 'frontend-admin' ),
				'add_new'               => __( 'Add New', 'frontend-admin' ),
				'new_item'              => __( 'New Dashboard Page', 'frontend-admin' ),
				'edit_item'             => __( 'Edit Dashboard Page', 'frontend-admin' ),
				'update_item'           => __( 'Update Dashboard Page', 'frontend-admin' ),
				'view_item'             => __( 'View Dashboard Page', 'frontend-admin' ),
				'search_items'          => __( 'Search Dashboard Page', 'frontend-admin' ),
				'not_found'             => __( 'Not found', 'frontend-admin' ),
				'not_found_in_trash'    => __( 'Not found in Trash', 'frontend-admin' ),
				'items_list'            => __( 'Dashboard Pages list', 'frontend-admin' ),
				'item_published'        => __( 'Settings Saved', 'frontend-admin' ),
				'item_updated'          => __( 'Settings Saved', 'frontend-admin' ),
				'items_list_navigation' => __( 'Dashboard Pages list navigation', 'frontend-admin' ),
				'filter_items_list'     => __( 'Filter Dashboard Pages list', 'frontend-admin' ),
			);

			$args = array(
				'label'             => __( 'Dashboard Page', 'frontend-admin' ),
				'description'       => __( 'Dashboard Page', 'frontend-admin' ),
				'labels'            => $labels,
				'supports'          => ['title', 'editor', 'thumbnail', 'revisions' ],
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
				'template'          => array(
					array( 'frontend-admin/form', array() ),
				),
			);
			register_post_type( 'dashboard_page', $args );

			add_filter(
				'post_updated_messages',
				function ( $messages ) {
					$messages['dashboard_page'] = array(
						'',
						__( 'Dashboard Page updated.', 'frontend-admin' ),
						__( 'Custom field updated.', 'frontend-admin' ),
						__( 'Custom field deleted.', 'frontend-admin' ),
						__( 'Dashboard Page updated.', 'frontend-admin' ),
						'',
						__( 'Dashboard Page published.', 'frontend-admin' ),
						__( 'Dashboard Page saved.', 'frontend-admin' ),
						__( 'Dashboard Page submitted.', 'frontend-admin' ),
						'',
						__( 'Dashboard Page draft updated.', 'frontend-admin' ),
					);
					return $messages;
				}
			);



			do_action( 'frontend_admin/post_types' );
		endif;
	}
	

}

fea_instance()->dashboard_pages = new Dashboard_Pages();
