<?php
/**
 * Plugin Name: Insert Headers and Footers Code - HT Script
 * Description: This plugin allow allow you to insert script in headers and footers
 * Version: 1.0.7
 * Author: HasThemes
 * Author URI: https://hasthemes.com/
 * Text Domain: ihafs
 * Domain Path: /languages
*/

// define path
define( 'IHAFS_URI', plugins_url('', __FILE__) );
define( 'IHAFS_DIR', dirname( __FILE__ ) );

include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
$ihafs_pro_active = false;
if(is_plugin_active( 'insert-headers-and-footers-script-pro/init.php' )){
	$ihafs_pro_active = true;
}

// include all files
include_once( IHAFS_DIR. '/inc/custom-posts.php');
include_once( IHAFS_DIR. '/admin/cmb2/init.php');
include_once( IHAFS_DIR. '/admin/functions.php');

if(is_admin()){
	include_once( IHAFS_DIR. '/admin/recommended-plugins/recommendations.php');
}

if(!$ihafs_pro_active){
	add_action( 'cmb2_admin_init', 'ihafs_add_metabox' );
	function ihafs_add_metabox(){
		include_once( IHAFS_DIR. '/inc/metabox-multiple-select.php');
	    include_once( IHAFS_DIR. '/inc/metabox.php');
	}
}

// define text domain path
add_action( 'init', 'ihafs_load_textdomain' );
function ihafs_load_textdomain() {
    load_plugin_textdomain( 'ihafs', false, basename(IHAFS_URI) . '/languages/' );
}

//add settings in plugin action
add_filter('plugin_action_links_'.plugin_basename(__FILE__),function($links){

    $link = sprintf("<a href='%s'>%s</a>",esc_url(admin_url('edit.php?post_type=ihafs_script')),__('Settings','ihafs'));

    array_unshift($links,$link);

    return $links;

});

// admin enqueue scripts
add_action( 'admin_enqueue_scripts','ihafs_enqueue_scripts');
function  ihafs_enqueue_scripts( $hook ){
    global $ihafs_pro_active;

    if(!$ihafs_pro_active){
    	//enqueue styles
    	wp_enqueue_style( 'ihafs-admin', IHAFS_URI.'/admin/css/admin.css');
    	wp_enqueue_style( 'wp-jquery-ui-dialog');

    	//enqueue js
    	wp_enqueue_script( 'jquery-ui-dialog');
    	wp_enqueue_script( 'ihafs-admin', IHAFS_URI.'/admin/js/admin.js', array('jquery'), '', true);
    } 
}

// upgrade notice
add_action('admin_footer', 'ihafs_upgrade_popup');
function ihafs_upgrade_popup(){
	?>
	<div id="ihafs_dialog" title="<?php echo esc_attr__( 'Go Premium!', 'ihafs' ); ?>" class="ihafs_dialog" style="display: none;">
		<div class="dashicons-before dashicons-warning"></div>
		<h3><?php echo esc_html__( 'Upgrade to premium version to unlock this feature!', 'ihafs' ) ?></h3>
		<a class="buy_now" target="_blank" href="https://hasthemes.com/plugins/insert-headers-and-footers-code-ht-script/"><?php echo esc_html__('Buy Now', 'ihafs'); ?></a>
	</div>
	<?php
}

// load header scripts
add_action( 'wp_head', 'ihafs_load_script_to_header' );
function ihafs_load_script_to_header(){
	$args = array(
		'post_type' => 'ihafs_script',
		'meta_query' => array(
			array(
				'key'     => '_ihafs_condition',
				'value'   => 'in_header',
				'compare' => 'IN',
			),
		),
	);

	$current_page_id = get_the_ID();

	$query = new WP_Query($args);

	while($query->have_posts()){
	    $query->the_post();

	    $post_id = get_the_id();
	    echo ihafs_output_script($post_id, $current_page_id);
	}
	wp_reset_postdata();
}

// load footer scripts
add_action( 'wp_footer', 'ihafs_load_script_to_footer' );
function ihafs_load_script_to_footer(){
	$args = array(
		'post_type' => 'ihafs_script',
		'meta_query' => array(
			array(
				'key'     => '_ihafs_condition',
				'value'   => 'in_footer',
				'compare' => 'IN',
			),
		),
	);

	$current_page_id = get_the_ID();

	$query = new WP_Query($args);

	while($query->have_posts()){
	    $query->the_post();

	    $post_id = get_the_id();
	    echo ihafs_output_script($post_id, $current_page_id);
	}
	wp_reset_postdata();
}

// load footer scripts
add_action( 'wp_body_open', 'ihafs_load_script_to_after_body', -1 );
function ihafs_load_script_to_after_body(){
	$args = array(
		'post_type' => 'ihafs_script',
		'meta_query' => array(
			array(
				'key'     => '_ihafs_condition',
				'value'   => 'after_body',
				'compare' => 'IN',
			),
		),
	);

	$current_page_id = get_the_ID();

	$query = new WP_Query($args);

	while($query->have_posts()){
	    $query->the_post();

	    $post_id = get_the_id();
	    echo ihafs_output_script($post_id, $current_page_id);
	}
	wp_reset_postdata();
}

// after check the conditions return script output 
function ihafs_output_script($post_id, $current_page_id){
	$status = get_post_meta($post_id, '_ihafs_status', true);
	$snippet = get_post_meta($post_id, '_ihafs_code', true);

	if(defined('IHAFS_PRO_URI')){
		global $wp_query;

		$show_on = get_post_meta($post_id, '_ihafs_show_on', true);
		$pages_list = get_post_meta($post_id, '_ihafs_pages_list', true);
		$posts_list = get_post_meta($post_id, '_ihafs_posts_list', true);
		$categories_list = get_post_meta($post_id, '_ihafs_categories_list', true);
		$tags_list = get_post_meta($post_id, '_ihafs_tags_list', true);

		is_archive() ? $current_category_id = $wp_query->get_queried_object_id() : $current_category_id = '';
		is_archive() ? $current_tag_id = $wp_query->get_queried_object_id() : $current_tag_id = '';

		if($status != 'active' || is_admin()){
			return;
		}

		if($show_on == 'only_pages' && (empty($pages_list) || !in_array($current_page_id, $pages_list)) ){
			return;
		}

		if($show_on == 'only_posts' && (empty($posts_list) || !in_array($current_page_id, $posts_list)) ){
			return;
		}

		if($show_on == 'only_categories' && (empty($categories_list) || !in_array($current_category_id, $categories_list)) ){
			return;
		}

		if($show_on == 'only_tags' && (empty($tags_list) || !in_array($current_tag_id, $tags_list)) ){
			return;
		}
	}

	return wp_unslash($snippet);
}