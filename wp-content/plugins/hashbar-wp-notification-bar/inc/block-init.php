<?php
/**
 * Blocks Initializer
 *
 * Enqueue CSS/JS of all the blocks.
 * @package Hashbar Notification
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Enqueue Gutenberg block assets for both frontend + backend.
 *
 * Assets enqueued:
 * 1. blocks.style.build.css - Frontend + Backend.
 * 2. blocks.build.js - Backend.
 * 3. blocks.editor.build.css - Backend.
 *
 * @uses {wp-blocks} for block type registration & related functions.
 * @uses {wp-element} for WP Element abstraction — structure of blocks.
 * @uses {wp-i18n} to internationalize the block's text.
 * @uses {wp-editor} for WP editor styles.
 * @since 1.0.0
 */

function hashbar_wpnb_check_post(){

    $get_custom_post_type = isset($_GET['post']) ? get_post($_GET['post'])->post_type : '';

    if((isset($_GET['post_type']) && $_GET['post_type'] == 'wphash_ntf_bar') || ($get_custom_post_type!== '' && $get_custom_post_type == 'wphash_ntf_bar')){

        return true;
    }
    return false;
}

function hashbar_block_init() {

	// Register block styles for both frontend + backend.
	wp_register_style(
		'hashbar-block-style-css',
		plugins_url( 'blocks/dist/blocks.style.build.css', HASHBAR_WPNB_ROOT ),
		is_admin() ? array( 'wp-editor' ) : null,
		null
	);

	// Register block editor styles for backend.
	wp_register_style(
		'hashbar-block-editor-css', // Handle.
		plugins_url( 'blocks/dist/blocks.editor.build.css', HASHBAR_WPNB_ROOT ),
		array( 'wp-edit-blocks' ),
		null
	);

	// Register block editor script for backend.
	wp_register_script(
		'hashbar-block-js',
		plugins_url( 'blocks/dist/blocks.build.js', HASHBAR_WPNB_ROOT ), 
		array( 'wp-blocks', 'wp-i18n', 'wp-element', 'wp-editor' ), 
		null, 
		true
	);

	// WP Localized globals. Use dynamic PHP stuff in JavaScript via `rocketGlobal` object.
	wp_localize_script(
		'hashbar-block-js',
		'hashbarGlobal',
		[
			'pluginDirPath'   	=> plugin_dir_path( __DIR__ ),
			'pluginDirUrl'    	=> 'https://demo.wphash.com/hashbar/wp-content/uploads/2018/01/promo-image-1.png'
		]
	);

	/**
	 * Register Gutenberg block on server-side.
	 *
	 * Register the block on server-side to ensure that the block
	 * scripts and styles for both frontend and backend are
	 * enqueued when the editor loads.
	 */
	if(function_exists('register_block_type')){
		register_block_type(
			'hashbar/hashbar-button', array(
				// Enqueue blocks.style.build.css on both frontend & backend.
				'style'         => 'hashbar-block-style-css',
				// Enqueue blocks.build.js in the editor only.
				'editor_script' => 'hashbar-block-js',
				// Enqueue blocks.editor.build.css in the editor only.
				'editor_style'  => 'hashbar-block-editor-css',
			)
		);

		register_block_type(
			'hashbar/hashbar-promo-banner', array(
				// Enqueue blocks.style.build.css on both frontend & backend.
				'style'         => 'hashbar-block-style-css',
				// Enqueue blocks.build.js in the editor only.
				'editor_script' => 'hashbar-block-js',
				// Enqueue blocks.editor.build.css in the editor only.
				'editor_style'  => 'hashbar-block-editor-css',
			)
		);

		register_block_type(
			'hashbar/hashbar-promo-banner-image', array(
				// Enqueue blocks.style.build.css on both frontend & backend.
				'style'         => 'hashbar-block-style-css',
				// Enqueue blocks.build.js in the editor only.
				'editor_script' => 'hashbar-block-js',
				// Enqueue blocks.editor.build.css in the editor only.
				'editor_style'  => 'hashbar-block-editor-css',
			)
		);
	}

	/**
	 * Register Gutenberg block pattern category.
	 */

	if(function_exists('register_block_pattern_category')){
		register_block_pattern_category(
		    'hashbar',
		    array( 'label' => __( 'Hashbar', 'hashbar' ) )
		);
	}

	/**
	 * Register Gutenberg block pattern.
	*/
	if(function_exists('register_block_pattern')){
		register_block_pattern(
		    'hashbar/promo-banner-pattern',
		    array(
		        'title'       => __( 'Hashbar Promo Banner','hashbar' ),
		        'description' => __( 'Promo Title, Promo content and button for a link.', 'hashbar' ),
		        'categories'  => array('hashbar'),
		        'content'     => "<!-- wp:hashbar/hashbar-promo-banner {\"promoTitle\":\"\\u003cem\\u003eIFORMATIVE\\u003c/em\\u003e\",\"promoSummery\":\"\\u003cem\\u003eSubscribe For Exclusive Content\\u003c/em\\u003e\",\"promobtnTxt\":\"Subscribe Now\",\"promoTitleFontSize\":25,\"promoContentFontSize\":25} -->\n<div class=\"wp-block-hashbar-hashbar-promo-banner ht-promo-banner\" style=\"background-color:#FB3555;background-image:url(undefined);background-position:center;background-repeat:no-repeat;background-size:cover;border-radius:6px\"><div class=\"ht-content\"><h4 class=\"promo-title\" style=\"font-size:25px;color:#fff\"><em>IFORMATIVE</em></h4><p class=\"promo-summery\" style=\"font-size:25px;color:#fff\"><em>Subscribe For Exclusive Content</em></p></div><div class=\"ht-promo-button\"><a href=\"#\" style=\"background-color:#fff;color:#1D1E22\">Subscribe Now</a></div></div>\n<!-- /wp:hashbar/hashbar-promo-banner -->\n\n<!-- wp:paragraph -->\n<p></p>\n<!-- /wp:paragraph -->",
		    )
		);
		register_block_pattern(
		    'hashbar/promo-banner-image',
		    array(
		        'title'       => __( 'Hashbar Promo Banner Image','hashbar' ),
		        'description' => __( 'Promo Banner Image', 'hashbar' ),
		        'categories'  => array('hashbar'),
		        'content'     => "<!-- wp:hashbar/hashbar-promo-banner-image -->\n<div class=\"wp-block-hashbar-hashbar-promo-banner-image ht-promo-banner-image\"><a href=\"#\"><img src=\"https://demo.wphash.com/hashbar/wp-content/uploads/2018/01/promo-image-1.png\" style=\"height:auto;width:250px\"/></a></div>\n<!-- /wp:hashbar/hashbar-promo-banner-image -->",
		    )
		);
		register_block_pattern(
		    'hashbar/notification-pattern',
		    array(
		        'title'       => __( 'Hashbar Notification','hashbar' ),
		        'description' => __( 'Notification content, button for a link.', 'hashbar' ),
		        'categories'  => array('hashbar'),
		        'content'     => "<!-- wp:hashbar/hashbar-button {\"hashbarContent\":\"Add Your Text Here\"} -->\n<div class=\"wp-block-hashbar-hashbar-button  hashbar-free-wraper\"><p>Add Your Text Here</p><a class=\"ht_btn\" href=\"#\" style=\"background-color:#fdd835;margin-top:0px;margin-right:20px;margin-bottom:0px;margin-left:20px;padding-top:10px;padding-right:30px;padding-bottom:10px;padding-left:30px;border-radius:3px;font-size:18px\">Button</a></div>\n<!-- /wp:hashbar/hashbar-button -->",
		    )
		);
	}
}

// Hook: Block assets.
if(is_admin()){
	if(hashbar_wpnb_check_post()){
		add_action( 'init', 'hashbar_block_init' );
	}
}else{
	add_action( 'init', 'hashbar_block_init' );
}
