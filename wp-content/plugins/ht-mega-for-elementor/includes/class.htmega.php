<?php

final class HTMega_Addons_Elementor {
    
    const MINIMUM_ELEMENTOR_VERSION = '2.5.0';
    const MINIMUM_PHP_VERSION = '7.0';

    /**
     * [$template_info]
     * @var array
     */
    public static $template_info = [];

    /**
     * [$_instance]
     * @var null
     */
    private static $_instance = null;

    /**
     * [instance] Initializes a singleton instance
     * @return [HTMega_Addons_Elementor]
     */
    public static function instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * [__construct] Class construcotr
     */
    private function __construct() {
        if ( ! function_exists('is_plugin_active') ){ include_once( ABSPATH . 'wp-admin/includes/plugin.php' ); }

        add_action( 'init', [ $this, 'i18n' ] );
        add_action( 'plugins_loaded', [ $this, 'init' ], 15 );

        // Register Plugin Active Hook
        register_activation_hook( HTMEGA_ADDONS_PL_ROOT, [ $this, 'plugin_activate_hook'] );
    }

    /**
     * [i18n] Load Text Domain
     * @return [void]
     */
    public function i18n() {
        load_plugin_textdomain( 'htmega-addons', false, dirname( plugin_basename( HTMEGA_ADDONS_PL_ROOT ) ) . '/languages/' );
    }

    /**
     * [init] Plugins Loaded Init Hook
     * @return [void]
     */
    public function init() {

        // Check if Elementor installed and activated
        if ( ! did_action( 'elementor/loaded' ) ) {
            add_action( 'admin_notices', [ $this, 'admin_notice_missing_main_plugin' ] );
            return;
        }

        // Check for required Elementor version
        if ( ! version_compare( ELEMENTOR_VERSION, self::MINIMUM_ELEMENTOR_VERSION, '>=' ) ) {
            add_action( 'admin_notices', [ $this, 'admin_notice_minimum_elementor_version' ] );
            return;
        }

        // Check for required PHP version
        if ( version_compare( PHP_VERSION, self::MINIMUM_PHP_VERSION, '<' ) ) {
            add_action( 'admin_notices', [ $this, 'admin_notice_minimum_php_version' ] );
            return;
        }

        // Plugins Required File
        $this->includes();

        // Add Image Size
        $this->add_image_size();

        // After Active Plugin then redirect to setting page
        $this->plugin_redirect_option_page();

        // Plugins Setting Page
        add_filter('plugin_action_links_'.HTMEGA_ADDONS_PLUGIN_BASE, [ $this, 'plugins_setting_links' ] );

        /**
         * [$template_info] Assign template data
         * @var [type]
         */
        if( is_admin() && class_exists('HTMega_Template_Library') ){
            self::$template_info = HTMega_Template_Library::instance()->get_templates_info();
        }
        
    }

    /**
     * [add_image_size]
     * @return [void]
     */
    public function add_image_size() {
        add_image_size( 'htmega_size_585x295', 585, 295, true );
        add_image_size( 'htmega_size_1170x536', 1170, 536, true );
        add_image_size( 'htmega_size_396x360', 396, 360, true );
    }

    /**
     * [is_plugins_active] Check Plugin installation status
     * @param  [string]  $pl_file_path plugin location
     * @return boolean  True | False
     */
    public function is_plugins_active( $pl_file_path = NULL ){
        $installed_plugins_list = get_plugins();
        return isset( $installed_plugins_list[$pl_file_path] );
    }

    /**
     * [admin_notice_missing_main_plugin] Admin Notice if elementor Deactive | Not Install
     * @return [void]
     */
    public function admin_notice_missing_main_plugin() {

        if ( isset( $_GET['activate'] ) ) unset( $_GET['activate'] );

        $elementor = 'elementor/elementor.php';
        if( $this->is_plugins_active( $elementor ) ) {
            if( ! current_user_can( 'activate_plugins' ) ) { return; }

            $activation_url = wp_nonce_url( 'plugins.php?action=activate&amp;plugin=' . $elementor . '&amp;plugin_status=all&amp;paged=1&amp;s', 'activate-plugin_' . $elementor );

            $message = '<p>' . __( 'HTMEGA Addons not working because you need to activate the Elementor plugin.', 'htmega-addons' ) . '</p>';
            $message .= '<p>' . sprintf( '<a href="%s" class="button-primary">%s</a>', $activation_url, __( 'Elementor Activate Now', 'htmega-addons' ) ) . '</p>';
        } else {
            if ( ! current_user_can( 'install_plugins' ) ) { return; }

            $install_url = wp_nonce_url( self_admin_url( 'update.php?action=install-plugin&plugin=elementor' ), 'install-plugin_elementor' );

            $message = '<p>' . __( 'HTMEGA Addons not working because you need to install the Elementor plugin', 'htmega-addons' ) . '</p>';

            $message .= '<p>' . sprintf( '<a href="%s" class="button-primary">%s</a>', $install_url, __( 'Elementor Install Now', 'htmega-addons' ) ) . '</p>';
        }
        echo '<div class="error"><p>' . $message . '</p></div>';
    }

    /**
     * [admin_notice_minimum_elementor_version]
     * @return [void] Elementor Required version check with current version
     */
    public function admin_notice_minimum_elementor_version() {
        if ( isset( $_GET['activate'] ) ) unset( $_GET['activate'] );
        $message = sprintf(
            __( '"%1$s" requires "%2$s" version %3$s or greater.', 'htmega-addons' ),
            '<strong>' . __( 'HTMega Addons', 'htmega-addons' ) . '</strong>',
            '<strong>' . __( 'Elementor', 'htmega-addons' ) . '</strong>',
             self::MINIMUM_ELEMENTOR_VERSION
        );
        printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );
    }

    /**
     * [admin_notice_minimum_php_version] Check PHP Version with required version
     * @return [void]
     */
    public function admin_notice_minimum_php_version() {
        if ( isset( $_GET['activate'] ) ) unset( $_GET['activate'] );
        $message = sprintf(
            __( '"%1$s" requires "%2$s" version %3$s or greater.', 'htmega-addons' ),
            '<strong>' . __( 'HTMega Addons', 'htmega-addons' ) . '</strong>',
            '<strong>' . __( 'PHP', 'htmega-addons' ) . '</strong>',
             self::MINIMUM_PHP_VERSION
        );
        printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );
    }

    /**
     * [plugins_setting_links]
     * @param  [array] $links plugin menu list.
     * @return [array] plugin menu list.
     */
    public function plugins_setting_links( $links ) {
        $htmega_settings_link = '<a href="admin.php?page=htmega_addons_options">'.esc_html__( 'Settings', 'htmega-addons' ).'</a>';
        array_unshift( $links, $htmega_settings_link );
        if( !is_plugin_active('htmega-pro/htmega_pro.php') ){
            $links['htmegago_pro'] = sprintf('<a href="https://hasthemes.com/plugins/ht-mega-pro/" target="_blank" style="color: #39b54a; font-weight: bold;">' . esc_html__('Go Pro','htmega-addons') . '</a>');
        }
        return $links; 
    }

    /**
     * [plugin_activate_hook] Plugin Activation Hook
     * @return [void]
     */
    public function plugin_activate_hook() {
        add_option('htmega_do_activation_redirect', true);
    }

    /**
     * [plugin_redirect_option_page] After Install plugin then redirect setting page
     * @return [void]
     */
    public function plugin_redirect_option_page() {
        if ( get_option( 'htmega_do_activation_redirect', false ) ) {
            delete_option('htmega_do_activation_redirect');
            if( !isset( $_GET['activate-multi'] ) ){
                wp_redirect( admin_url("admin.php?page=htmega-addons_extensions") );
            }
        }
    }

    /**
     * [include_files] Required Necessary file
     * @return [void]
     */
    public function includes() {
        require_once ( HTMEGA_ADDONS_PL_PATH . 'includes/helper-function.php' );
        require_once ( HTMEGA_ADDONS_PL_PATH . 'includes/class.assests.php' );
        require_once ( HTMEGA_ADDONS_PL_PATH . 'admin/admin-init.php' );
        require_once ( HTMEGA_ADDONS_PL_PATH . 'includes/widgets_control.php' );
        require_once ( HTMEGA_ADDONS_PL_PATH . 'includes/class.htmega-icon-manager.php' );

        // Admin Required File
        if( is_admin() ){

            // Post Duplicator
            if( htmega_get_option( 'postduplicator', 'htmega_advance_element_tabs', 'off' ) === 'on' ){
                require_once ( HTMEGA_ADDONS_PL_PATH . 'includes/class.post-duplicator.php' );
            }

            // Recommended plugins
            require_once ( HTMEGA_ADDONS_PL_PATH . 'includes/recommended-plugins/class.recommended-plugins.php' );
            require_once ( HTMEGA_ADDONS_PL_PATH . 'includes/recommended-plugins/recommended-plugins.php' );
            
        }

        // Extension Assest Management
        require_once( HTMEGA_ADDONS_PL_PATH . 'extensions/class.enqueue_scripts.php' );

        // HT Builder
        if( htmega_get_option( 'themebuilder', 'htmega_advance_element_tabs', 'off' ) === 'on' ){
            require_once( HTMEGA_ADDONS_PL_PATH . 'extensions/ht-builder/init.php' );
        }

        // WC Sales Notification
        if( htmega_get_option( 'salenotification', 'htmega_advance_element_tabs', 'off' ) === 'on' ){
            if( is_plugin_active('htmega-pro/htmega_pro.php') ){
                if( htmega_get_option( 'notification_content_type', 'htmegawcsales_setting_tabs', 'actual' ) == 'fakes' ){
                    require_once( HTMEGA_ADDONS_PL_PATH_PRO . 'extensions/wc-sales-notification/classes/class.sale_notification_fake.php' );
                }else{
                    require_once( HTMEGA_ADDONS_PL_PATH_PRO . 'extensions/wc-sales-notification/classes/class.sale_notification.php' );
                }
            }else{
                require_once( HTMEGA_ADDONS_PL_PATH . 'extensions/wc-sales-notification/classes/class.sale_notification.php' );
            }
        }

        // HT Menu
        if( htmega_get_option( 'megamenubuilder', 'htmega_advance_element_tabs', 'off' ) === 'on' ){
            if( is_plugin_active('htmega-pro/htmega_pro.php') ){
                require_once( HTMEGA_ADDONS_PL_PATH_PRO . 'extensions/ht-menu/classes/class.mega-menu.php' );
            }else{
                require_once( HTMEGA_ADDONS_PL_PATH . 'extensions/ht-menu/classes/class.mega-menu.php' );
            }
        }

        
    }

}

/**
 * Initializes the main plugin
 *
 * @return \HTMega_Addons_Elementor
 */
function htmega() {
    return HTMega_Addons_Elementor::instance();
}