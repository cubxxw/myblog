<?php
/**
 * Plugin Name: HashBar - WordPress Notification Bar
 * Plugin URI:  http://demo.wphash.com/hashbar/
 * Description: Notification Bar plugin for WordPress
 * Version:     1.3.0
 * Author:      HasThemes
 * Author URI:  https://hasthemes.com
 * Text Domain: hashbar
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
*/

// define path
define( 'HASHBAR_WPNB_ROOT', __FILE__ );
define( 'HASHBAR_WPNB_URI', plugins_url('',HASHBAR_WPNB_ROOT) );
define( 'HASHBAR_WPNB_DIR', dirname(HASHBAR_WPNB_ROOT ) );
define( 'HASHBAR_WPNB_VERSION', '1.3.0');

$wordpress_version = (int)get_bloginfo( 'version' );
$hashbar_gutenberg_enable = $wordpress_version < 5 ? false : true;

// Include all files
if ( ! function_exists('is_plugin_active') ){
    include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
}
include_once( HASHBAR_WPNB_DIR. '/inc/custom-posts.php');

if(is_admin()){
    include_once( HASHBAR_WPNB_DIR. '/inc/recomendation/Class_Recommended_Plugins.php');
    include_once( HASHBAR_WPNB_DIR. '/inc/recomendation/hashbar-recomendation.php');
}

include_once( HASHBAR_WPNB_DIR. '/inc/functions.php');
include_once( HASHBAR_WPNB_DIR. '/inc/database-installer.php');
include_once( HASHBAR_WPNB_DIR. '/inc/manage-cash.php');
include_once( HASHBAR_WPNB_DIR. '/inc/analytical-store.php');

if(!is_plugin_active( 'hashbar-pro/init.php' )){
    include_once( HASHBAR_WPNB_DIR. '/inc/shortcode.php');
    include_once( HASHBAR_WPNB_DIR. '/admin/plugin-options.php');

    if( true === $hashbar_gutenberg_enable ){
        include_once( HASHBAR_WPNB_DIR. '/blocks/block-init.php' );
    }

    if ( ! class_exists( 'CSF' ) ) {
        require_once HASHBAR_WPNB_DIR .'/libs/codestar-framework/classes/setup.class.php';
    }

    include_once( HASHBAR_WPNB_DIR. '/inc/metabox.php');

	add_action( 'admin_enqueue_scripts','hashbar_wpnb_admin_enqueue_scripts');
}

// deactivate the pro version 
register_activation_hook( HASHBAR_WPNB_ROOT, 'hashbar_deactivate_pro_version' );
function hashbar_deactivate_pro_version(){
    if( is_plugin_active('hashbar-pro/init.php') ){
        deactivate_plugins('hashbar-pro/init.php');
    }

    \Hashbarfree\Analytics\Database_Installer::create_tables();

    $plugin_data = get_file_data( HASHBAR_WPNB_ROOT, array('Version'=>'Version'), 'plugin' );
    $vesion = $plugin_data['Version'];

    if(version_compare($vesion,'1.2.3','>')){
        $args = array( 'post_type' => 'wphash_ntf_bar', 'posts_per_page' => -1 );

        $ntf_query = new WP_Query($args);

        while( $ntf_query->have_posts() ){
            $ntf_query->the_post();
            $post_id  = get_the_id();

            $exclude_ids = get_post_meta( $post_id , '_wphash_exclusion_page_for_notification', true );
            // update_post_meta(2409, '_log', 'azad'); 

            if(!empty($exclude_ids) && is_array($exclude_ids)){
                $implode_page_ids = implode(",",$exclude_ids);
                update_post_meta( $post_id, '_wphash_exclusion_page_for_notification', $implode_page_ids);
            }
        }
        wp_reset_query(); wp_reset_postdata();
    }
}

//add settings in plugin action
add_filter('plugin_action_links_'.plugin_basename(__FILE__),function($links){

    $link = sprintf("<a href='%s'>%s</a>",esc_url(admin_url('edit.php?post_type=wphash_ntf_bar')),__('Settings','hashbar'));

    array_unshift($links,$link);

    return $links;

});

add_action( 'plugins_loaded', 'hashbar_wpnb_tablecreate' );

function hashbar_wpnb_tablecreate(){

    $analytics_table_exist =get_option( 'hthb_analyticstbl_exist', $default = false );
    $plugin_data = get_file_data( HASHBAR_WPNB_ROOT, array('Version'=>'Version'), 'plugin' );
    $vesion = $plugin_data['Version'];

    if($analytics_table_exist === false){
        if(version_compare($vesion,'1.2.3','>')){
            \Hashbarfree\Analytics\Database_Installer::create_tables();
        }
    }
}

add_action('init', 'hashbar_wpnb_upgrade_metadata');
function hashbar_wpnb_upgrade_metadata(){
    $plugin_data = get_file_data( HASHBAR_WPNB_ROOT, array('Version'=>'Version'), 'plugin' );
    $vesion      = $plugin_data['Version'];

    // Record the version number for future purpose
    $version_plain_number = str_replace('.', '', $vesion);
    if( !get_option('hashbar_'. $version_plain_number ) ){
        add_option('hashbar_'. $version_plain_number, true);
    }

    if( version_compare($vesion,'1.2.3','>') && get_option('hashbar_1st_upgrade_completed') ){
        return;
    }

    // Upgrade when a notification bar has BG image or Date field is set
    // Before upgrade take a backup of existing value
    $args = array( 
        'post_type'      => 'wphash_ntf_bar', 
        'posts_per_page' => '-1',
        'post_status'    => array('publish', 'pending', 'draft', 'auto-draft', 'future', 'private', 'inherit', 'trash')
    );
    $upgrade_query = new WP_Query( $args );

    while( $upgrade_query->have_posts() ){
        $upgrade_query->the_post();
        $post_id  = get_the_id();

        // Upgrade BG image field data
        $meta_values             = get_post_meta( $post_id );
        $old_bg_content_image    = get_post_meta( $post_id, '_wphash_notification_content_bg_image', true );
        $old_bg_content_image_id = get_post_meta( $post_id, '_wphash_notification_content_bg_image_id', true );
        
        if( $old_bg_content_image ){
            if( !is_array($old_bg_content_image) ){
                update_post_meta( $post_id, '__wphash_notification_content_bg_image', $old_bg_content_image );

                // replace old notfication bars meta value with new format
                // don't apply for the new notification bar which created by the current version
                update_post_meta( $post_id, '_wphash_notification_content_bg_image', array(
                    'url'       => $old_bg_content_image,
                    'id'        => $old_bg_content_image_id,
                    'thumbnail' => $old_bg_content_image
                ));
            }
        }
    }
    wp_reset_query(); wp_reset_postdata();

    add_option('hashbar_1st_upgrade_completed', true);
}

// define text domain path
function hashbar_wpnb_textdomain() {

    load_plugin_textdomain( 'hashbar', false, basename(HASHBAR_WPNB_URI) . '/languages/' );
}
add_action( 'init', 'hashbar_wpnb_textdomain' );

// enqueue scripts
add_action( 'wp_enqueue_scripts','hashbar_wpnb_enqueue_scripts');
function  hashbar_wpnb_enqueue_scripts(){
    $dev_mode = false;
    $version  = $dev_mode ? time() : HASHBAR_WPNB_VERSION;

    // enqueue styles
    wp_enqueue_style( 'hashbar-frontend', HASHBAR_WPNB_URI.'/assets/css/frontend.css','',$version);

    //register script
    wp_register_script( 'jquery-countdown', HASHBAR_WPNB_URI.'/assets/js/jquery.countdown.min.js', array('jquery'), HASHBAR_WPNB_VERSION, true);

    // enqueue js
    wp_enqueue_script( 'hashbar-frontend', HASHBAR_WPNB_URI.'/assets/js/frontend.js', array('jquery'),$version, false);
    wp_enqueue_script( 'hashbar-analytics', HASHBAR_WPNB_URI.'/assets/js/analytics.js', array('jquery'), $version, true );
    wp_enqueue_script( 'js-cookie', HASHBAR_WPNB_URI.'/assets/js/js.cookie.min.js',array('jquery'),HASHBAR_WPNB_VERSION, false);

    $checkbox_value            = hashbar_wpnb_get_opt('dont_show_bar_after_close');
    $bar_closed_checkbox_value = hashbar_wpnb_get_opt('keep_closed_bar');
    $localized_vars = array(
        'dont_show_bar_after_close' => $checkbox_value,
        'notification_display_time' => apply_filters("hashbar_wpnbp_dispaly_loading_time", 400 ),
        'bar_keep_closed'           => $bar_closed_checkbox_value,
    );

    $hashbar_localize_analytical_data = [
        'ajaxurl'          => admin_url( 'admin-ajax.php' ),
        'nonce_key'        => wp_create_nonce('hashbar_analytics'),
        'enable_analytics' => hashbar_wpnb_get_opt('enable_analytics')
    ];

    // Localize
    wp_localize_script( 'hashbar-frontend', 'hashbar_localize', $localized_vars );
    wp_localize_script( 'hashbar-analytics', 'hashbar_analytical', $hashbar_localize_analytical_data );
}

// admin enqueue scripts
function  hashbar_wpnb_admin_enqueue_scripts(){

    if((get_post_type() == 'wphash_ntf_bar' && isset($_GET['action']) && $_GET['action'] == 'edit') || (isset($_GET['post_type']) && $_GET['post_type'] == 'wphash_ntf_bar'))
    {
        // enqueue styles
        add_thickbox();
        wp_enqueue_style( 'wp-jquery-ui-dialog');
        wp_enqueue_style( 'jquery-ui-timepicker-addon', HASHBAR_WPNB_URI. '/admin/css/jquery-ui-timepicker-addon.min.css','',HASHBAR_WPNB_VERSION);
        wp_enqueue_style( 'tooltipster-bundle', HASHBAR_WPNB_URI.'/libs/tooltipster/css/tooltipster.bundle.min.css','',HASHBAR_WPNB_VERSION);
        wp_enqueue_style( 'tooltipster-sidetip-light', HASHBAR_WPNB_URI.'/libs/tooltipster/css/plugins/tooltipster/sideTip/themes/tooltipster-sideTip-light.min.css','',HASHBAR_WPNB_VERSION);
        wp_enqueue_style( 'hashbar-admin', HASHBAR_WPNB_URI.'/admin/css/admin.css', '' , time());

        // enqueue js
        wp_enqueue_script( 'tooltipster-bundle', HASHBAR_WPNB_URI.'/libs/tooltipster/js/tooltipster.bundle.min.js', array('jquery'), HASHBAR_WPNB_VERSION, false );
        wp_enqueue_script( 'jquery-ui-dialog');
        wp_enqueue_script( 'jquery-ui-timepicker-addon', HASHBAR_WPNB_URI. '/admin/js/jquery-ui-timepicker-addon.min.js', array('jquery', 'jquery-ui-datepicker'),HASHBAR_WPNB_VERSION );
        wp_enqueue_script( 'hashbar-admin', HASHBAR_WPNB_URI.'/admin/js/admin.js', array('jquery', 'jquery-ui-dialog'), time(), false);

        $hashbar_localize_data = [
            'ajaxurl'            => admin_url( 'admin-ajax.php' ),
            'hashbar_nonce'      => wp_create_nonce('hashbar_protected'),
            'hashbar_post_id'    => isset( $_GET['post'] ) ? $_GET['post'] : "",
            'hashbar_plugin_uri' => HASHBAR_WPNB_URI,
        ];

        wp_localize_script( 'hashbar-admin', 'hashbar_admin', $hashbar_localize_data);
    }
   
}

add_action('admin_footer', 'hashbar_wpnb_upgrade_popup');
function hashbar_wpnb_upgrade_popup(){
	?>
	<div id="ht_dialog" title="<?php echo esc_attr__( 'Go Premium!', 'hashbar' ); ?>" class="ht_dialog" style="display: none;">
		<div class="dashicons-before dashicons-warning"></div>
		<h3><?php esc_html_e( 'Purchase our', 'hashbar' ); ?> <a target="_blank" href="https://hasthemes.com/0lx0"><?php esc_html_e( 'Premium', 'hashbar' ); ?></a> <?php esc_html_e( 'version to unlock this feature!', 'hashbar' ); ?></h3>
	</div>
	<?php
}

add_action( 'wp_footer', 'hashbar_wpnb_load_notification_to_footer' );
function hashbar_wpnb_load_notification_to_footer(){

    $current_page_id = get_the_ID();

    $deagult_args = array('post_type' => 'wphash_ntf_bar', 'posts_per_page' => -1);
    $args = apply_filters( "hashbar_query_args", $deagult_args );

    $ntf_query = new WP_Query($args);

    while($ntf_query->have_posts()){
        $ntf_query->the_post();

        $post_id = get_the_id();

        $where_to_show = get_post_meta( $post_id , '_wphash_notification_where_to_show', true );

        if($where_to_show  == 'custom'){
            $where_to_show_custom =  get_post_meta( $post_id , '_wphash_notification_where_to_show_custom', true );

            if( !empty($where_to_show_custom) && is_array($where_to_show_custom) ){
                foreach( $where_to_show_custom as $item){
                    if(is_front_page() && $item == 'home'){
                       hashbar_wpnb_output($post_id);
                    }

                    if(is_single() && $item == 'posts'){
                        hashbar_wpnb_output($post_id);
                    }

                    if(is_page() && $item == 'page' ){
                       hashbar_wpnb_output($post_id);
                    }

                    if( function_exists('is_product') && is_product() && $item == 'products' ){
                       hashbar_wpnb_output($post_id);
                    }
                }
            }

        }elseif( is_single() && $where_to_show == 'post' ){

            $ids_arr = get_post_meta( $post_id , '_wphash_notification_where_to_show_Post', true );
            if($ids_arr && in_array($current_page_id, $ids_arr)){
                hashbar_wpnb_output($post_id);
            }
            
        }elseif( is_page() && $where_to_show == 'page' ){

            $ids_arr = get_post_meta( $post_id , '_wphash_notification_where_to_show_Page', true );

            if(function_exists( 'is_cart' ) && is_cart()){
                $get_cart_page_id = get_option( 'woocommerce_cart_page_id' );
                if( $ids_arr && in_array( $get_cart_page_id, $ids_arr ) ){
                    hashbar_wpnb_output( $post_id );
                } 
            }elseif(function_exists( 'is_checkout' ) && is_checkout()){
                $get_checkout_page_id = get_option( 'woocommerce_checkout_page_id' );
                if( $ids_arr && in_array( $get_checkout_page_id, $ids_arr ) ){
                    hashbar_wpnb_output( $post_id );
                } 
            }else{
                if( $ids_arr && in_array( $current_page_id, $ids_arr ) ){
                    hashbar_wpnb_output( $post_id );
                }
            }

        }elseif( function_exists( 'is_shop' ) && is_shop() && $where_to_show == 'page' ){

            $ids_arr = get_post_meta( $post_id , '_wphash_notification_where_to_show_Page', true );
            $get_shop_page_id = get_option( 'woocommerce_shop_page_id' );
            if( $ids_arr && in_array( $get_shop_page_id, $ids_arr ) ){
                hashbar_wpnb_output( $post_id );
            }

        }elseif( $where_to_show == 'product' && is_singular($post_types = 'product')){

            $ids_arr = get_post_meta( $post_id , '_wphash_notification_where_to_show_Product', true );
            if( $ids_arr && in_array( $current_page_id, $ids_arr ) ){
                hashbar_wpnb_output( $post_id );
            }

        }elseif ($where_to_show  == 'everywhere' ){
        	
            hashbar_wpnb_output($post_id);

        }elseif( $where_to_show == 'specific_ids' ){

            $ids_arr = get_post_meta( $post_id , '_wphash_specific_post_ids', true );
            $ids_arr = explode( ',', $ids_arr );

            if( $ids_arr && in_array( $current_page_id, $ids_arr ) ){
               hashbar_wpnb_output( $post_id );
            }

        }elseif( $where_to_show == 'url_param' ){
			$page_url_param = get_post_meta( $post_id, '_wphash_url_param', true );
			$url_param = isset($_GET['param'])  && $_GET['param'] ? $_GET['param'] : '';

			if($page_url_param == $url_param){
				hashbar_wpnb_output($post_id);
			}
        }
    }
    wp_reset_query(); wp_reset_postdata();
}

//notification bar output hashbar_wpnb_output
function hashbar_wpnb_output($post_id){
    // Don't load notification bar in admin
    if( is_admin() || is_customize_preview() ){
        return;
    }
    
    if($post_id):

        $hashbar_wpnb_opt = get_option( 'hashbar_wpnb_opt');
        $positon = get_post_meta( $post_id , '_wphash_notification_position', true );

        if( empty( $positon ) || $positon == 'ht-n-top' ){
            $positon =  'hthb-pos--top';
        } elseif( $positon == 'ht-n-bottom' ){
            $positon =  'hthb-pos--bottom';
        } elseif( $positon == 'ht-n-left' ){
            $positon = 'hthb-pos--left-wall';
        } elseif( $positon == 'ht-n-right' ){
            $positon = 'hthb-pos--right-wall';
        } elseif( $positon == 'ht-n_toppromo' ){
            $positon = 'hthb-pos--top-promo';
        } elseif( $positon == 'ht-n_bottompromo' ){
            $positon = 'hthb-pos--bottom-promo';
        }

        $where_to_show = get_post_meta( $post_id , '_wphash_show_hide_scroll', true );
        $scroll_trigger_status = get_post_meta($post_id, '_wphash_show_hide_scroll', true);

        $width = get_post_meta( $post_id , '_wphash_notification_width', true );
        $height = get_post_meta( $post_id , '_wphash_notification_height', true );
        $margin = get_post_meta($post_id,'_wphash_notification_content_margin');
        $padding = get_post_meta($post_id,'_wphash_notification_content_padding');
        $mobile_height = get_post_meta( $post_id , '_wphash_notification_mobile_height', true );
        $count_down = get_post_meta( $post_id , '_wphash_count_down', true );
        $count_position = get_post_meta( $post_id , '_wphash_countdown_position', true );

        $transparent_selector = '';

        $header_type = '';

        $sticky_hide          = get_post_meta( $post_id , '_wphash_sticky_on_hide_status', true );

        $on_desktop = get_post_meta( $post_id, '_wphash_notification_on_desktop', true );
        $on_mobile  = get_post_meta( $post_id, '_wphash_notification_on_mobile', true );

        // Notification state
        $display = get_post_meta( $post_id , '_wphash_notification_display', true );
        $keep_closed = '';
        $keep_closed_option = isset( $hashbar_wpnb_opt['keep_closed_bar'] ) ? $hashbar_wpnb_opt['keep_closed_bar'] : '';
        $keep_closed_cookie = isset($_COOKIE['keep_closed_bar']) ? $_COOKIE['keep_closed_bar'] : '';
        if( $keep_closed_option && $keep_closed_cookie ) {
            $keep_closed = true;
        }

        
        $scroll_to_show = get_post_meta($post_id, '_wphash_show_scroll_position', true);
        $scroll_to_hide = get_post_meta($post_id, '_wphash_hide_scroll_position', true);
        if( $scroll_trigger_status != 'show_hide_scroll_enable' ){
            $scroll_to_show = '';
            $scroll_to_hide = '';
        }

        $display = ($display == 'ht-n-open') ? 'hthb-state--open' : 'hthb-state--minimized';
        if( $keep_closed ){
            $display = 'hthb-state--minimized';
        } else{
            if( $scroll_trigger_status == 'show_hide_scroll_enable' && $scroll_to_show ){
                $display = 'hthb-state--minimized';
            }
        }

        $content_width = get_post_meta( $post_id, '_wphash_notification_content_width', true );

        $content_color = get_post_meta( $post_id, '_wphash_notification_content_text_color', true );
        $content_bg_color = get_post_meta( $post_id, '_wphash_notification_content_bg_color', true );
        $content_bg_image = get_post_meta( $post_id, '_wphash_notification_content_bg_image', true );
        $content_bg_opacity = get_post_meta( $post_id, '_wphash_notification_content_bg_opcacity', true );

        // Button options
        $close_button = get_post_meta( $post_id, '_wphash_notification_close_button', true );
        $close_button_class = '';
        $close_button_text = '';
        $open_button_text = '';
        if($close_button != 'off'){
            $close_button_class = 'hthb-has-close-button';
            $close_button_text  = get_post_meta( $post_id, '_wphash_notification_close_button_text', true );
            $open_button_text   = get_post_meta( $post_id, '_wphash_notification_open_button_text', true );
        }

        
        $button_margin = get_post_meta($post_id,'_wphash_notification_button_margin');
        $button_padding = get_post_meta($post_id,'_wphash_notification_button_padding');

        $close_button_bg_color = get_post_meta( $post_id, '_wphash_notification_close_button_bg_color', true );
        $close_button_color = get_post_meta( $post_id, '_wphash_notification_close_button_color', true );
        $close_button_hover_color = get_post_meta( $post_id, '_wphash_notification_close_button_hover_color', true );
        $close_button_hover_bg_color = get_post_meta( $post_id, '_wphash_notification_close_button_hover_bg_color', true );

        $arrow_color = get_post_meta( $post_id, '_wphash_notification_arrow_color', true );
        $arrow_bg_color = get_post_meta( $post_id, '_wphash_notification_arrow_bg_color', true );
        $arrow_hover_color = get_post_meta( $post_id, '_wphash_notification_arrow_hover_color', true );
        $arrow_hover_bg_color = get_post_meta( $post_id, '_wphash_notification_arrow_hover_bg_color', true );
        $prb_margin = get_post_meta($post_id,'_wphash_prb_margin');

        $css_style = '';
        if( !empty( $content_color ) ){
            $css_style .= "#notification-$post_id .hthb-notification-content,#notification-$post_id .hthb-notification-content p{color:$content_color}";
        }

        if( !empty( $content_bg_color ) ){
            $css_style .= "#notification-$post_id::before{background-color:$content_bg_color}";
        }

        if( !empty( $content_bg_image ) && isset($content_bg_image['url']) ){
            $content_bg_image = $content_bg_image['url'];
            $css_style .= "#notification-$post_id::before{background-image:url($content_bg_image)}";
        }

        if( !empty( $content_bg_opacity ) ){
            $css_style .= "#notification-$post_id::before{opacity:$content_bg_opacity}";
        }


        if($width){
            if( 'hthb-pos--bottom-promo' == $positon || 'hthb-pos--top-promo' == $positon ){
                $css_style .= "#notification-$post_id .hthb-notification-content .ht-promo-banner{width:$width}";
                $css_style .= "#notification-$post_id .hthb-notification-content .ht-promo-banner-image a img{width:$width !important}";
            }else{
                $css_style .= "#notification-$post_id{max-width:$width}";
            }
        }

        if($margin && is_array($margin[0])){
            $css_style .= "#notification-$post_id .hthb-notification-content{margin:".$margin[0]['margin_top']." ".$margin[0]['margin_right']." ".$margin[0]['margin_bottom']." ".$margin[0]['margin_left']."}";
        }

        if($padding && is_array($padding[0])){
            $css_style .= "#notification-$post_id .hthb-notification-content{padding:".$padding[0]['padding_top']." ".$padding[0]['padding_right']." ".$padding[0]['padding_bottom']." ".$padding[0]['padding_left']."}";
        }

        if($button_margin && is_array($button_margin[0])){
            $css_style .= "#notification-$post_id .hthb-notification-content .ht_btn{margin:".$button_margin[0]['button_margin_top']." ".$button_margin[0]['button_margin_right']." ".$button_margin[0]['button_margin_bottom']." ".$button_margin[0]['button_margin_left']."}";
        }

        if($button_padding && is_array($button_padding[0])){
            $css_style .= "#notification-$post_id .hthb-notification-content .ht_btn{padding:".$button_padding[0]['button_padding_top']." ".$button_padding[0]['button_padding_right']." ".$button_padding[0]['button_padding_bottom']." ".$button_padding[0]['button_padding_left']."}";
        }

        if( $positon == 'hthb-pos--top' || $positon == 'ht-n-bottom' ){
            $css_style .= "#notification-$post_id.hthb-state--open{height:{$height}px}";
        }

        //promo banner position
        $prb_margin_top    = $prb_margin && is_array($prb_margin[0]) && !empty($prb_margin[0]['margin_top']) ? $prb_margin[0]['margin_top'] : '';
        $prb_margin_right  = $prb_margin && is_array($prb_margin[0]) && !empty($prb_margin[0]['margin_right']) ? $prb_margin[0]['margin_right'] : '';
        $prb_margin_bottom = $prb_margin && is_array($prb_margin[0]) && !empty($prb_margin[0]['margin_bottom']) ? $prb_margin[0]['margin_bottom'] : '';
        $prb_margin_left   = $prb_margin && is_array($prb_margin[0]) && !empty($prb_margin[0]['margin_left']) ? $prb_margin[0]['margin_left'] : '';
        $promo_top_alignment = get_post_meta( $post_id, '_wphash_promo_banner_top_display', true );
        $promo_bottom_alignment = get_post_meta( $post_id, '_wphash_promo_banner_bottom_display', true );
        $promo_alignment_class = '';

        if($positon == 'hthb-pos--top-promo'){
            if ($promo_top_alignment == 'promo-top-left' ){
                $promo_alignment_class = 'hthb-promo-alignment--left';
                $css_style .= "#notification-$post_id.hthb-pos--top-promo{margin-left:{$prb_margin_left};margin-top:{$prb_margin_top}}";
            } else{
                $promo_alignment_class = 'hthb-promo-alignment--right';
                $css_style .= "#notification-$post_id.hthb-pos--top-promo{margin-right:{$prb_margin_right};margin-top:{$prb_margin_top}}";
            }
        } elseif($positon == 'hthb-pos--bottom-promo'){
            if ($promo_bottom_alignment == 'promo-bottom-left' ){
                $promo_alignment_class = 'hthb-promo-alignment--left';
                $css_style .= "#notification-$post_id.hthb-pos--bottom-promo{margin-left:{$prb_margin_left};margin-bottom:{$prb_margin_bottom}}";
            }else{
                $promo_alignment_class = 'hthb-promo-alignment--right';
                $css_style .= "#notification-$post_id.hthb-pos--bottom-promo{margin-right:{$prb_margin_right};margin-bottom:{$prb_margin_bottom}}";
            }
        }

        if($close_button_bg_color) $css_style .= "#notification-$post_id .hthb-close-toggle{background-color:$close_button_bg_color}";
        if($close_button_color) $css_style .= "#notification-$post_id .hthb-close-toggle,#notification-$post_id .hthb-close-toggle svg path{fill:$close_button_color}";
        if($close_button_hover_bg_color) $css_style .= "#notification-$post_id .hthb-close-toggle:hover{background-color:$close_button_hover_bg_color}";
        if($close_button_hover_color) $css_style .= "#notification-$post_id .hthb-close-toggle:hover{color:$close_button_hover_color}";
        if($close_button_hover_color) $css_style .= "#notification-$post_id .hthb-close-toggle:hover svg path{fill:$close_button_hover_color}";

        if($arrow_bg_color) $css_style .= "#notification-$post_id .hthb-open-toggle{background-color:$arrow_bg_color}";
        if($arrow_color) $css_style .= "#notification-$post_id .hthb-open-toggle{color:$arrow_color}";

        if($arrow_hover_color) $css_style .= "#notification-$post_id .hthb-open-toggle:hover i{color:$arrow_hover_color}";
        if($arrow_hover_bg_color) $css_style .= "#notification-$post_id .hthb-open-toggle:hover{background-color:$arrow_hover_bg_color}";

        if( $positon == 'hthb-pos--top' ){
            // If Sticky is off
            if( $sticky_hide == 'yes' && $header_type != 'none' ){
                $css_style .= "#notification-$post_id.hthb-state--open{position:absolute !important;}";
                $css_style .= "html body $transparent_selector.htnfix-header{top:0 !important; transition: background-color .4s,color .4s,transform .4s,opacity .4s ease-in-out,-webkit-transform .4s;}";

                $css_style .= ".admin-bar $transparent_selector.htnfix-header{top:32px !important; transition: background-color .4s,color .4s,transform .4s,opacity .4s ease-in-out,-webkit-transform .4s;}";
            }
        }

        //Notification open toggle button
        $ntf_open_toggle = get_post_meta( $post_id, '_wphash_hide_open_toggle', true );

        if('ntf_open_toggle_disable' == $ntf_open_toggle){
            $css_style .= "#notification-".$post_id." .hthb-open-toggle{display: none;}";
        }

        // Mobile device breakpoint
        $mobile_device_width  = isset( $hashbar_wpnb_opt['mobile_device_breakpoint'] ) ? $hashbar_wpnb_opt['mobile_device_breakpoint'] : '';
        $mobile_device_width  = empty( $mobile_device_width ) ? 768 : $mobile_device_width; 
        $desktop_device_width = $mobile_device_width + 1;

        $responsive_style = '#notification-$post_id{display:none !important;';
        
        if( $on_mobile == 'off' ){
            $margin_top = '';
            $padding_bottom = '';
            if($positon == 'hthb-pos--top'){
                $margin_top = 'margin-top:0 !important;';
            }elseif( $positon == 'ht-n-bottom' ){
                $padding_bottom = 'padding-bottom:0 !important;';
            }

            $responsive_style = "@media (max-width: ".$mobile_device_width."px){#notification-$post_id{display:none !important;} body.htnotification-mobile{ $margin_top $padding_bottom} #notification-$post_id.hthb-state--open{height:{$mobile_height}px} }";
        }else{
           $responsive_style = "@media (max-width: ".$mobile_device_width ."px){ #notification-$post_id.hthb-state--open{height:{$mobile_height}px;} }"; 
        }

        if( $on_desktop == 'off' ){
            $responsive_style = "@media (min-width: ". $desktop_device_width ."px){#notification-$post_id{display:none  !important}}";
        }

        if( $on_mobile == 'off' && $on_desktop == 'off'){
            $css_style .= "#notification-$post_id{display:none !important;}";
        }

        $dont_show_bar_after_close = isset( $hashbar_wpnb_opt['dont_show_bar_after_close'] ) ? $hashbar_wpnb_opt['dont_show_bar_after_close'] : '';

        // Get the number input of how many time this notifcation will show
        $how_many_time_to_show = get_post_meta( $post_id, '_wphash_notification_how_many_times_to_show', true );
        $how_many_time_to_show = (int) $how_many_time_to_show;

        // Dont show if dont_show_bar bar coockie value is 1
        if(
            ( $dont_show_bar_after_close == '' || !( isset($_COOKIE['dont_show_bar']) && $_COOKIE['dont_show_bar'] == '1' ) )
        ):  
            if( 'ht-n_bottompromo' == $positon ){
                $positon = 'ht-n-bottom ht-n_bottompromo';
            }

            if( 'ht-n_toppromo' == $positon ){
                $positon = 'hthb-pos--top ht-n_toppromo';
            }
        ?>

        <!--Notification Section-->
        <?php
            $open_button_text_class = '';
            $close_button_text_class = '';
            
            if($close_button_text){
                $close_button_text_class = 'hthb-has-close-button-text';
            }
            
            if($open_button_text){
                $open_button_text_class = 'hthb-has-open-button-text';
            }

            $notification_bar_classes_arr = array();
            $notification_bar_classes_arr[] = 'hthb-notification ht-notification-section';
            $notification_bar_classes_arr[] = 'hthb-'.$header_type;
            $notification_bar_classes_arr[] = $close_button_class;
            $notification_bar_classes_arr[] = $open_button_text_class;
            $notification_bar_classes_arr[] = $close_button_text_class;
            $notification_bar_classes_arr[] = $promo_alignment_class;
            $notification_bar_classes_arr[] = $positon;
            $notification_bar_classes_arr[] = $display;
            $notification_bar_classes_arr[] = $where_to_show == 'show_hide_scroll_enable' ? 'hthb-scroll' : '';
            $notification_bar_classes_arr[] = $count_down == 'ntf_countdown_enable' ? 'hthb-countdown' : ''; 
            $notification_bar_classes_arr[] = $count_position == 'center' ? 'hthb-countdown-center' : '';

            $notification_bar_classes = implode(' ', $notification_bar_classes_arr);
        ?>
        <div id="notification-<?php echo esc_attr( $post_id ); ?>"
            style="visibility: hidden;" 
            <?php hashbar_wpnb_render_html_attr('data-id', $post_id); ?>
            <?php hashbar_wpnb_render_html_attr('data-transparent_header_selector', $transparent_selector); ?>
            <?php hashbar_wpnb_render_html_attr('data-scroll_to_show', $scroll_to_show); ?>
            <?php hashbar_wpnb_render_html_attr('data-scroll_to_hide', $scroll_to_hide); ?>
            <?php hashbar_wpnb_render_html_attr('data-time_to_show', $how_many_time_to_show); ?>
            class="<?php echo esc_attr($notification_bar_classes); ?>">

            <!--Notification Open Buttons-->
            <?php if(empty($open_button_text)): ?>
                <span class="hthb-open-toggle">
                    <svg id="Layer" enable-background="new 0 0 64 64" height="25" viewBox="0 0 64 64"  xmlns="http://www.w3.org/2000/svg"><path d="m37.379 12.552c-.799-.761-2.066-.731-2.827.069-.762.8-.73 2.066.069 2.828l15.342 14.551h-39.963c-1.104 0-2 .896-2 2s.896 2 2 2h39.899l-15.278 14.552c-.8.762-.831 2.028-.069 2.828.393.412.92.62 1.448.62.496 0 .992-.183 1.379-.552l17.449-16.62c.756-.755 1.172-1.759 1.172-2.828s-.416-2.073-1.207-2.862z" fill="#ffffff"/></svg>
                </span>
            <?php else: ?>
                 <span class="hthb-open-toggle"><span><?php echo esc_html($open_button_text); ?></span></span>
            <?php endif; ?>

            <div class="hthb-row">
                <div class="<?php echo $content_width == 'ht-n-full-width' ? esc_attr( 'hthb-full-width' ) : esc_attr('hthb-container'); ?>">

                    <!--Notification Buttons-->
                    <div class="hthb-close-toggle-wrapper">
                        <span  class="hthb-close-toggle" data-text="<?php echo esc_html( $close_button_text ); ?>">
                            <svg version="1.1" width="15" height="25" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                                 viewBox="0 0 496.096 496.096" style="enable-background:new 0 0 496.096 496.096;" xml:space="preserve">
                                <path d="M259.41,247.998L493.754,13.654c3.123-3.124,3.123-8.188,0-11.312c-3.124-3.123-8.188-3.123-11.312,0L248.098,236.686
                                        L13.754,2.342C10.576-0.727,5.512-0.639,2.442,2.539c-2.994,3.1-2.994,8.015,0,11.115l234.344,234.344L2.442,482.342
                                        c-3.178,3.07-3.266,8.134-0.196,11.312s8.134,3.266,11.312,0.196c0.067-0.064,0.132-0.13,0.196-0.196L248.098,259.31
                                        l234.344,234.344c3.178,3.07,8.242,2.982,11.312-0.196c2.995-3.1,2.995-8.016,0-11.116L259.41,247.998z" fill="#ffffff" data-original="#000000"/>
                            </svg>
                            <span class="hthb-close-text"><?php echo esc_html( $close_button_text ); ?></span>
                        </sapn>
                    </div>

                    <!--Notification Text-->
                    <div class="hthb-notification-content ht-notification-text">
                        <?php 
                            if($count_down == 'ntf_countdown_enable' && $count_position != 'shortcode'){
                                echo hashbar_do_shortcode('hashbar_countdown');
                            }
                        ?>
                        <?php the_content(); ?>
                    </div>

                </div>
            </div>
        </div>

        <style type="text/css">
            <?php echo esc_html( $css_style.$responsive_style ); ?>
        </style>

        <?php

        endif;
    endif;
}

// add status column in hashbar post list
add_filter('manage_wphash_ntf_bar_posts_columns', 'hashbar_status_column');
if ( !function_exists( 'hashbar_status_column' ) ){
    function hashbar_status_column($columns){
        $offset = array_search('date', array_keys($columns));
        return array_merge(array_slice($columns, 0, $offset), ['status' => __('Where to show', 'hashbar')], array_slice($columns, $offset, null));
    }
}

// add status value in column
add_action('manage_wphash_ntf_bar_posts_custom_column', 'hashbar_status_value', 10, 2);
if ( !function_exists( 'hashbar_status_value' ) ){
    function hashbar_status_value($column_name, $post_ID){
        if ($column_name == 'status') {
            $hashabar_post_status = get_post_meta( $post_ID, '_wphash_notification_where_to_show', true );
                if ($hashabar_post_status) {
                    ?>
                        <p style="text-transform: capitalize; font-size: 15px;"><?php echo str_replace('_', ' ', $hashabar_post_status) ?></p>
                    <?php
                }
            
        }
    }
}

// page builder king composer and visual composer
add_action( 'init', 'hashbar_wpnb_page_builder_support' );
function hashbar_wpnb_page_builder_support(){
    //king composer support
    global $kc;

    if($kc){
        $kc->add_content_type( 'wphash_ntf_bar' );
    }

    //vc support
    if( class_exists( 'VC_Manager' ) ){
    	$default_post_types = vc_default_editor_post_types();

    	if(!in_array('wphash_ntf_bar', $default_post_types)){
    		$default_post_types[] = 'wphash_ntf_bar';
    	}
        
        vc_set_default_editor_post_types( $default_post_types );
    }
}


// set post view to 0 when update notification
// define the updated_post_meta callback
add_action( 'save_post', 'hashbar_wpnp_update_meta', 10, 3 );
function hashbar_wpnp_update_meta( $post_id, $post, $update ) {
    if($post->post_type == 'wphash_ntf_bar'){
        $count_key = 'post_'. $post_id .'_views_count';
        update_post_meta( $post_id, $count_key, 0 );
    }
};

//pro notice popup layout
add_action( 'admin_footer', 'pro_version_notice');

function pro_version_notice(){
    ?>
        <a href="#TB_inline?height=250&width=400&inlineId=hashbar_pro_notice" class="thickbox hashbar_trigger_pro_notice" style="display: none;"><?php echo esc_html__('Pro Notice', 'hashbar') ?></a> 
        <div id="hashbar_pro_notice" style="display: none;">
            <div class="hashbar_pro_notice_wrapper">
                <h3><?php echo esc_html__('Pro Version is Required!', 'hashbar') ?></h3>
                <p><?php echo esc_html__('This feature is available in the pro version.', 'hashbar') ?></p>
                <a target="_blank" href="<?php echo esc_url('https://hasthemes.com/plugins/wordpress-notification-bar-plugin/') ?>"><?php echo esc_html__('Buy Now', 'hashbar') ?></a>
            </div>
        </div>
    <?php
}
