<?php

// Exit if accessed directly
if (!defined('ABSPATH'))
    exit;

if (!function_exists('popularis_business_setup')) :

    /**
     * Global functions.
     */
    function popularis_business_setup() {
        
        // Child theme language
        load_child_theme_textdomain( 'popularis-business', get_stylesheet_directory() . '/languages' );
        
    }
    
endif;

add_action('after_setup_theme', 'popularis_business_setup');

if (!function_exists('popularis_business_parent_css')):

    /**
     * Enqueue CSS.
     */
    function popularis_business_parent_css() {
        $parent_style = 'popularis-stylesheet';
        
        $dep = array('bootstrap');
        if (class_exists('WooCommerce')) {
            $dep = array('bootstrap', 'popularis-woocommerce');
        }

        wp_enqueue_style($parent_style, get_template_directory_uri() . '/style.css', $dep);
        wp_enqueue_style('popularis-business',
                get_stylesheet_directory_uri() . '/style.css',
                array($parent_style),
                wp_get_theme()->get('Version')
        );
    }

endif;
add_action('wp_enqueue_scripts', 'popularis_business_parent_css');

add_action('widgets_init', 'popularis_business_widgets_init');

/**
 * Register the Sidebar(s)
 */
function popularis_business_widgets_init() {
    register_sidebar(
        array(
            'name' => esc_html__('Header', 'popularis-business'),
            'id' => 'popularis-business-header',
            'before_widget' => '<div id="%1$s" class="header-widget %2$s col-md-3">',
            'after_widget' => '</div>',
            'before_title' => '<div class="widget-title"><h3>',
            'after_title' => '</h3></div>',
        )
    );
}

add_action( 'init', 'popularis_customizer' );

/**
 * Move sidebar to left and make it larger.
 */
function popularis_main_content_width_columns() {

    $columns = '12';

    if (is_active_sidebar('sidebar-1')) {
        $columns = '8 col-md-push-4';
    }

    echo esc_attr($columns);
}

if (!function_exists('popularis_business_excerpt_length')) :

    /**
     * Limit the excerpt.
     */
    function popularis_business_excerpt_length($length) {
        if (is_home() || is_archive()) { // Make sure to not limit pagebuilders
            return '45';
        } else {
            return $length;
        }
    }

    add_filter('excerpt_length', 'popularis_business_excerpt_length', 999);

endif;
