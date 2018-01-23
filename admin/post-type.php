<?php
// No direct access, please
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'wp_show_posts_type' ) ) {
	add_action( 'init', 'wp_show_posts_type', 0 );
	/**
	 * Create our WP Show Posts post type
	 * @since 0.1
	 */
	function wp_show_posts_type() {

		$labels = array(
			'name'                  => _x( 'Post Lists', 'Post Type General Name', 'wp-show-posts' ),
			'singular_name'         => _x( 'Post List', 'Post Type Singular Name', 'wp-show-posts' ),
			'menu_name'             => __( 'WP Show Posts', 'wp-show-posts' ),
			'name_admin_bar'        => __( 'WP Show Posts', 'wp-show-posts' ),
			'archives'              => __( 'List Archives', 'wp-show-posts' ),
			'parent_item_colon'     => __( 'Parent List:', 'wp-show-posts' ),
			'all_items'             => __( 'All Lists', 'wp-show-posts' ),
			'add_new_item'          => __( 'Add New List', 'wp-show-posts' ),
			'add_new'               => __( 'Add New', 'wp-show-posts' ),
			'new_item'              => __( 'New List', 'wp-show-posts' ),
			'edit_item'             => __( 'Edit List', 'wp-show-posts' ),
			'update_item'           => __( 'Update List', 'wp-show-posts' ),
			'view_item'             => __( 'View List', 'wp-show-posts' ),
			'search_items'          => __( 'Search List', 'wp-show-posts' ),
			'not_found'             => __( 'Not found', 'wp-show-posts' ),
			'not_found_in_trash'    => __( 'Not found in Trash', 'wp-show-posts' ),
			'featured_image'        => __( 'Featured Image', 'wp-show-posts' ),
			'set_featured_image'    => __( 'Set featured image', 'wp-show-posts' ),
			'remove_featured_image' => __( 'Remove featured image', 'wp-show-posts' ),
			'use_featured_image'    => __( 'Use as featured image', 'wp-show-posts' ),
			'insert_into_item'      => __( 'Insert into list', 'wp-show-posts' ),
			'uploaded_to_this_item' => __( 'Uploaded to this list', 'wp-show-posts' ),
			'items_list'            => __( 'Items list', 'wp-show-posts' ),
			'items_list_navigation' => __( 'Items list navigation', 'wp-show-posts' ),
			'filter_items_list'     => __( 'Filter items list', 'wp-show-posts' ),
		);
		$args = array(
			'label'                 => __( 'Post List', 'wp-show-posts' ),
			'labels'                => $labels,
			'supports'              => array( 'title' ),
			'hierarchical'          => false,
			'public'                => false,
			'show_ui'               => true,
			'show_in_menu'          => true,
			'menu_position'         => 5,
			'show_in_admin_bar'     => false,
			'show_in_nav_menus'     => false,
			'can_export'            => true,
			'has_archive'           => false,
			'exclude_from_search'   => true,
			'publicly_queryable'    => false,
			'capability_type'       => 'page',
		);
		register_post_type( 'wp_show_posts', $args );

	}
}
