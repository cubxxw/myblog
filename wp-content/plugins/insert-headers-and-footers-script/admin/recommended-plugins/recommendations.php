<?php
/**
 * Constructor Parameters
 *
 * @param string    $text_domain your plugin text domain.
 * @param string    $parent_menu_slug the menu slug name where the "Recommendations" submenu will appear.
 * @param string    $submenu_label To change the submenu name.
 * @param string    $submenu_page_name an unique page name for the submenu.
 * @param int       $priority Submenu priority adjust.
 * @param string    $hook_suffix use it to load this library assets only to the recommedded plugins page. Not into the whol admin area.
 *
 */

require(  __DIR__ .'/class.recommended-plugins.php' );

if( class_exists('Hasthemes\HTScript\HTRP_Recommended_Plugins') ){
    $recommendations = new Hasthemes\HTScript\HTRP_Recommended_Plugins(
        array( 
            'text_domain'       => 'ihafs',
            'parent_menu_slug'  => 'edit.php?post_type=ihafs_script', 
            'menu_capability'   => 'manage_options', 
            'menu_page_slug'    => '',
            'priority'          => '999',
            'assets_url'        => '',
            'hook_suffix'       => 'ihafs_script_page_ihafs_extensions',
        )
    );

    $recommendations->add_new_tab(array(
        'title' => esc_html__( 'Recommended Plugins', 'ihafs' ),
        'plugins_type' => 'free',
        'active' => true,
        'plugins' => array(
            array(
                'slug'      => 'hashbar-wp-notification-bar',
                'location'  => 'init.php',
                'name'      => esc_html__( 'Notification Bar for WordPress', 'ihafs' )
            ),
            array(
                'slug'      => 'extensions-for-cf7',
                'location'  => 'extensions-for-cf7.php',
                'name'      => esc_html__( 'Contact form 7 Extensions', 'ihafs' )
            ),
            array(
                'slug'      => 'wp-plugin-manager',
                'location'  => 'plugin-main.php',
                'name'      => esc_html__( 'WP Plugin Manager', 'ihafs' )
            ),
            array(
                'slug'      => 'ht-instagram',
                'location'  => 'ht-instagram.php',
                'name'      => esc_html__( 'Instagram Feed for WordPress', 'ihafs' )
            ),
        )
    ));

    $recommendations->add_new_tab(array(
        'title' => esc_html__( 'You May Also Like', 'ihafs' ),
        'plugins_type' => 'pro',
        'plugins' => array(
            array(
                'slug'      => 'just-tables-pro',
                'location'  => 'just-tables-pro.php',
                'name'      => esc_html__( 'JustTables Pro', 'ihafs' ),
                'link'      => 'https://hasthemes.com/wp/justtables/',
                'author_link'=> 'https://hasthemes.com/',
                'description'=> esc_html__( 'JustTables is an incredible WordPress plugin that lets you showcase all your WooCommerce products in a sortable and filterable table view. It allows your customers to easily navigate through different attributes of the products and compare them on a single page. This plugin will be of great help if you are looking for an easy solution that increases the chances of landing a sale on your online store.', 'ihafs' ),
            ),

            array(
                'slug'      => 'wc-builder-pro',
                'location'  => 'wc-builder-pro.php',
                'name'      => esc_html__( 'WC Builder Pro', 'wpbforwpbakery' ),
                'link'      => 'https://hasthemes.com/plugins/wc-builder-woocoomerce-page-builder-for-wpbakery/',
                'author_link'=> 'https://hasthemes.com/',
                'description'=> esc_html__( 'WC Builder Pro allows you to build Shop, Product Details, Cart, Checkout, My Account and Thank You page without even touching a single line of code! This plugin has 34+ custom addons for WooCommerce to build the page using WPBakery page builder. ', 'wpbforwpbakery' ),
            ),

            array(
                'slug'      => 'multicurrencypro',
                'location'  => 'multicurrencypro.php',
                'name'      => esc_html__( 'Multi Currency Pro for WooCommerce', 'ihafs' ),
                'link'      => 'https://hasthemes.com/plugins/multi-currency-pro-for-woocommerce/',
                'author_link'=> 'https://hasthemes.com/',
                'description'=> esc_html__( 'Multi-Currency Pro for WooCommerce is a prominent currency switcher plugin for WooCommerce. This plugin allows your website or online store visitors to switch to their preferred currency or their country’s currency.', 'ihafs' ),
            ),

            array(
                'slug'      => 'email-candy-pro',
                'location'  => 'email-candy-pro.php',
                'name'      => esc_html__( 'Email Candy Pro - Email customizer for WooCommerce', 'ihafs' ),
                'link'      => 'https://hasthemes.com/plugins/email-candy-pro/',
                'author_link'=> 'https://hasthemes.com/',
                'description'=> esc_html__( 'Email Candy is an outstanding WordPress plugin that allows you to customize the default WooCommerce email templates and give a professional look to your WooCommerce emails. If you are tired of using the boring design of WooCommerce emails and want to create customized emails, then this plugin will come in handy.', 'ihafs' ),
            ),

        )
    ));
}