<?php

/*=======================================================
*    Register Post type
* =======================================================*/
function ihafs_custom_posts() {
	// Headers and Footer Scripts
	$labels = array(
		'name'                  => _x( 'All Scripts', 'ihafs' ),
		'singular_name'         => _x( 'Script', 'ihafs' ),
		'menu_name'             => __( 'HT Script', 'ihafs' ),
		'name_admin_bar'        => __( 'ihafs', 'ihafs' ),
		'archives'              => __( 'Script Archives', 'ihafs' ),
		'parent_item_colon'     => __( 'Parent Script:', 'ihafs' ),
		'all_items'             => __( 'All Scripts', 'ihafs' ),
		'add_new_item'          => __( 'Add New Script', 'ihafs' ),
		'add_new'               => __( 'Add New Script', 'ihafs' ),
		'new_item'              => __( 'New Script', 'ihafs' ),
		'edit_item'             => __( 'Edit Script', 'ihafs' ),
		'update_item'           => __( 'Update Script', 'ihafs' ),
		'view_item'             => __( 'View Script', 'ihafs' ),
		'search_items'          => __( 'Search Script', 'ihafs' ),
		'not_found'             => __( 'Not found', 'ihafs' ),
		'not_found_in_trash'    => __( 'Not found in Trash', 'ihafs' ),
		'featured_image'        => __( 'Featured Image', 'ihafs' ),
		'set_featured_image'    => __( 'Set featured image', 'ihafs' ),
		'remove_featured_image' => __( 'Remove featured image', 'ihafs' ),
		'use_featured_image'    => __( 'Use as featured image', 'ihafs' ),
		'insert_into_item'      => __( 'Insert into item', 'ihafs' ),
		'uploaded_to_this_item' => __( 'Uploaded to this item', 'ihafs' ),
		'items_list'            => __( 'Scripts list', 'ihafs' ),
		'items_list_navigation' => __( 'Scripts list navigation', 'ihafs' ),
		'filter_items_list'     => __( 'Filter items list', 'ihafs' ),
	);
	$args = array(
		'label'                 => __( 'Scripts', 'ihafs' ),
		'labels'                => $labels,
		'supports'              => array('title' ),
		'hierarchical'          => false,
		'public'                => false,
		'show_ui'               => true,
		'show_in_menu'          => true,
		'menu_position'         => 5,
		'menu_icon'             => 'dashicons-edit',
		'show_in_admin_bar'     => true,
		'show_in_nav_menus'     => true,
		'can_export'            => true,
		'has_archive'           => true,		
		'exclude_from_search'   => true,
		'publicly_queryable'    => false,
		'capability_type'       => 'post',
	);
	register_post_type( 'ihafs_script', $args );
	
}
add_action( 'init', 'ihafs_custom_posts');