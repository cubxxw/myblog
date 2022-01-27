<?php
/**
 * Constructor args
 *
 * @param string    $text_domain your plugin text domain.
 * @param string    $parent_menu_slug the menu slug name where the "Recommendations" submenu will appear.
 * @param string    $menu_page_slug an unique page name for the submenu.
 * @param string    $menu_capability this is takes a capability which will be used to determine whether or not a page is included in the menu.
 * @param int       $priority Submenu priority adjust.
 * @param string    $assets_url your plugin assets url.
 * @param string    $hook_suffix use it to load this library assets only to the recommedded plugins page. Not into the whol admin area.
 *
 */

if( class_exists('Hasthemes\Hashbar\HTRP_Recommended_Plugins') ){
    $get_instance = new Hasthemes\Hashbar\HTRP_Recommended_Plugins(
        array( 
            'text_domain'       => 'hashbar', 
            'parent_menu_slug'  => 'edit.php?post_type=wphash_ntf_bar', 
            'menu_capability'   => 'manage_options', 
            'menu_page_slug'    => 'recommendations',
            'priority'          => '',
            'hook_suffix'       => 'wphash_ntf_bar_page_recommendations'
        )
    );


    // Free Plugins
    $get_instance->add_new_tab( array(

        'title' => esc_html__( 'Recommended', 'hashbar' ),
        'active' => true,
        'plugins' => array(
            array(
                'slug'      => 'extensions-for-cf7',
                'location'  => 'extensions-for-cf7.php',
                'name'      => esc_html__( 'Extensions For CF7 (Contact form 7 Database, Conditional Field and Redirection)', 'hashbar' )
            ),
            array(
                'slug'      => 'wp-plugin-manager',
                'location'  => 'plugin-main.php',
                'name'      => esc_html__( 'WP Plugin Manager', 'hashbar' )
            ),
            array(
                'slug'      => 'docus',
                'location'  => 'docus.php',
                'name'      => esc_html__( 'Docus – YouTube Video Playlist', 'hashbar' )
            ),
            array(
                'slug'      => 'insert-headers-and-footers-script',
                'location'  => 'init.php',
                'name'      => esc_html__( 'Insert Headers and Footers Code – HT Script', 'hashbar' )
            )
        )

    ) );

    // Free and pro plugins combile
    $get_instance->add_new_tab(array(
        'title' => esc_html__( 'Others', 'hashbar' ),
        'plugins' => array(

            array(
                'slug'      => 'woolentor-addons',
                'location'  => 'woolentor_addons_elementor.php',
                'name'      => esc_html__( 'WooLentor - WooCommerce Elementor Addons + Builder', 'hashbar' )
            ),
            array(
                'slug'      => 'ht-mega-for-elementor',
                'location'  => 'htmega_addons_elementor.php',
                'name'      => esc_html__( 'HT Mega - Absolute Addons for Elementor Page Builder', 'hashbar' )
            ),
            array(
                'slug'      => 'quickswish',
                'location'  => 'quickswish.php',
                'name'      => esc_html__( 'QuickSwish', 'hashbar' )
            ),
            array(
                'slug'      => 'wishsuite',
                'location'  => 'wishsuite.php',
                'name'      => esc_html__( 'WishSuite', 'hashbar' )
            ),
            array(
                'slug'      => 'ever-compare',
                'location'  => 'ever-compare.php',
                'name'      => esc_html__( 'EverCompare', 'hashbar' )
            ),
            array(
                'slug'      => 'swatchly',
                'location'  => 'swatchly.php',
                'name'      => esc_html__( 'Swatchly – Variation Swatches for WooCommerce Products', 'hashbar' )
            ),
            array(
                'slug'      => 'whols',
                'location'  => 'whols.php',
                'name'      => esc_html__( 'Whols', 'hashbar' )
            ),
            array(
                'slug'      => 'wc-builder',
                'location'  => 'wc-builder.php',
                'name'      => esc_html__( 'WC Builder – WooCommerce Page Builder for WPBakery', 'hashbar' )
            ),
            array(
                'slug'      => 'just-tables',
                'location'  => 'just-tables.php',
                'name'      => esc_html__( 'JustTables', 'hashbar' )
            ),
            array(
                'slug'      => 'wc-multi-currency',
                'location'  => 'wcmilticurrency.php',
                'name'      => esc_html__( 'Multi Currency', 'hashbar' )
            )
        )
    ));
}