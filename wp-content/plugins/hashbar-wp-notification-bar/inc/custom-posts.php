<?php

/*=======================================================
*    Register Post type
* =======================================================*/
function hashbar_wpnb_custom_posts() {
	global $hashbar_gutenberg_enable;
	// Notification bars
	$labels = array(
		'name'                  => _x( 'Notification bars', 'Post Type General Name', 'hashbar' ),
		'singular_name'         => _x( 'Notification bar', 'Post Type Singular Name', 'hashbar' ),
		'menu_name'             => __( 'HashBars', 'hashbar' ),
		'name_admin_bar'        => __( 'hashbar', 'hashbar' ),
		'archives'              => __( 'Notification Archives', 'hashbar' ),
		'parent_item_colon'     => __( 'Parent Notification:', 'hashbar' ),
		'all_items'             => __( 'All Notifications', 'hashbar' ),
		'add_new_item'          => __( 'Add New Notification', 'hashbar' ),
		'add_new'               => __( 'Add New Notification', 'hashbar' ),
		'new_item'              => __( 'New Notification', 'hashbar' ),
		'edit_item'             => __( 'Edit Notification', 'hashbar' ),
		'update_item'           => __( 'Update Notification', 'hashbar' ),
		'view_item'             => __( 'View Notification', 'hashbar' ),
		'search_items'          => __( 'Search Notification', 'hashbar' ),
		'not_found'             => __( 'Not found', 'hashbar' ),
		'not_found_in_trash'    => __( 'Not found in Trash', 'hashbar' ),
		'featured_image'        => __( 'Featured Image', 'hashbar' ),
		'set_featured_image'    => __( 'Set featured image', 'hashbar' ),
		'remove_featured_image' => __( 'Remove featured image', 'hashbar' ),
		'use_featured_image'    => __( 'Use as featured image', 'hashbar' ),
		'insert_into_item'      => __( 'Insert into item', 'hashbar' ),
		'uploaded_to_this_item' => __( 'Uploaded to this item', 'hashbar' ),
		'items_list'            => __( 'Notifications list', 'hashbar' ),
		'items_list_navigation' => __( 'Notifications list navigation', 'hashbar' ),
		'filter_items_list'     => __( 'Filter items list', 'hashbar' ),
	);
	$args = array(
		'label'                 => __( 'Notification bar', 'hashbar' ),
		'labels'                => $labels,
		'supports'              => array('title','editor', 'revisions'),
		'hierarchical'          => false,
		'public'                => true,
		'show_ui'               => true,
		'show_in_menu'          => true,
		'menu_position'         => 5,
		'menu_icon'             => 'dashicons-format-status',
		'show_in_rest'       	=> $hashbar_gutenberg_enable,
		'show_in_admin_bar'     => true,
		'show_in_nav_menus'     => true,
		'can_export'            => true,
		'has_archive'           => true,		
		'exclude_from_search'   => false,
		'publicly_queryable'    => true,
		'capability_type'       => 'post',
	);
	register_post_type( 'wphash_ntf_bar', $args );
	
}
add_action( 'init', 'hashbar_wpnb_custom_posts');