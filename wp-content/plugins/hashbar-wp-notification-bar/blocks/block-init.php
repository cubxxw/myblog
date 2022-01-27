<?php

namespace Hashbar\Block;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Hashbar_Block {
	/**
     * [$_instance]
     * @var null
     */
    private static $_instance = null;

    /**
     * [instance] Initializes a singleton instance
     * @return [Actions]
     */
    public static function instance() {
        if ( is_null( self::$_instance ) ){
            self::$_instance = new self();
            self::$_instance->dynamic_blocks_include();
        }
        return self::$_instance;
    }


    /**
	 * The Constructor.
	 */
	public function __construct() {
		$this->define_constants();
		add_action( 'init', [ $this, 'init' ] );
		add_action( 'enqueue_block_assets', [ $this, 'block_assets' ] );
		add_action( 'enqueue_block_editor_assets', [ $this, 'block_editor_assets' ] );
	}

	/**
	 * Define the required plugin constants
	 *
	 * @return void
	 */
	public function define_constants() {
		$this->define( 'HASHBAR_BLOCK_FILE', __FILE__ );
		$this->define( 'HASHBAR_BLOCK_PATH', __DIR__ );
		$this->define( 'HASHBAR_BLOCK_URL', plugins_url( '', HASHBAR_BLOCK_FILE ) );
	}

	/**
     * Define constant if not already set
     *
     * @param  string $name
     * @param  string|bool $value
     * @return type
     */
    private function define( $name, $value ) {
        if ( ! defined( $name ) ) {
            define( $name, $value );
        }
    }

	public function init(){

		// Return early if this function does not exist.
		if ( ! function_exists( 'register_block_type' ) ) {
			return;
		}

		$this->register_category_pattern();
	}

	/**
     * Load Dynamic blocks
     */
    public function dynamic_blocks_include(){
    	$blockList = $this->block_list();
		$blockDir  = HASHBAR_BLOCK_PATH . '/src/block/';

		foreach ( $blockList as $key => $block ) {

			$blockFilepath = $blockDir . $block['file-name'] . '/index.php';
            if( file_exists( $blockFilepath ) ){
                require_once ( $blockFilepath );
            }

		}

    }

    /**
     * Block List
     */
    public function block_list(){
    	$blockList = [
            'hashbar_ntf' => array(
                'label'  	  => 'Hashbar Notification',
                'file-name'   => 'hashbar-notification'
            ),
            'hashbar_prmbnr' => array(
                'label'  	  => 'Hashbar Promobanner',
                'file-name'   => 'hashbar-promobanner'
            ),
            'hashbar_prmbnrimg' => array(
                'label'  	  => 'Hashbar Promobanner Image',
                'file-name'   => 'promo-banner-img'
            ),
            'hashbar_countdown' => array(
                'label'  	  => 'Hahsbar Countdown',
                'file-name'   => 'hashbar-countdown'
            )
        ];

        return $blockList;
    }

	public function block_assets(){

		wp_enqueue_style(
		    'hashabr-block-style',
		    HASHBAR_BLOCK_URL . '/src/assets/css/style-index.css',
		    array(),
		    HASHBAR_WPNB_VERSION
		);

		// WP Localized globals. Use dynamic PHP stuff in JavaScript via `rocketGlobal` object.
		wp_localize_script(
			'hashbar-block-js',
			'hashbarGlobal',
			[
				'pluginDirPath'   	=>  plugin_dir_path( __DIR__ ),
				'pluginDirUrl'    	=> 'https://demo.wphash.com/hashbar/wp-content/uploads/2018/01/promo-image-1.png'
			]
		);

	}

	/**
	 * Block editor assets.
	 */
	public function block_editor_assets() {

		$dependencies = require_once( HASHBAR_BLOCK_PATH . '/build/hashbar-block.asset.php' );

		wp_enqueue_script(
		    'hashbar-block-js',
		    HASHBAR_BLOCK_URL . '/build/hashbar-block.js',
		    $dependencies['dependencies'],
		    $dependencies['version'],
		    true
		);
		
		wp_enqueue_style( 'hashbar-block-editor-style', HASHBAR_BLOCK_URL . '/src/assets/css/editor-style.css', false, HASHBAR_WPNB_VERSION, 'all' );
		wp_enqueue_style( 'hashbar-pro-frontend', HASHBAR_WPNB_URI.'/assets/css/frontend.css', '', time());
		wp_enqueue_script( 'jquery-countdown', HASHBAR_WPNB_URI.'/assets/js/jquery.countdown.min.js', array('jquery'),HASHBAR_WPNB_VERSION, true);

		wp_localize_script(
			'jquery-countdown',
			'hashbarNtf',
			[
				'ntfId'   	=>  get_the_id()
			]
		);
	}

	private function register_category_pattern(){
		
		if(function_exists('register_block_pattern_category')){
			register_block_pattern_category(
			    'hashbar',
			    array( 'label' => __( 'Hashbar', 'hashbar' ) )
			);
		}

		if(function_exists('register_block_pattern')){
			register_block_pattern(
			    'hashbar-pro/hashbar-promo-banner',
			    array(
			        'title'       => __( 'Hashbar Promo Banner','hashbar' ),
			        'description' => __( 'Promo Title, Promo content and button for a link.', 'hashbar' ),
			        'categories'  => array('hashbar'),
			        'content'     => "<!-- wp:hashbar/hashbar-promo-banner -->\n<div class=\"wp-block-hashbar-hashbar-promo-banner ht-promo-banner\" style=\"background-color:#FB3555;background-image:url(undefined);background-position:center;background-repeat:no-repeat;background-size:cover;border-radius:6px\"><div class=\"ht-content\"><h4 class=\"promo-title\" style=\"font-size:22px;color:#fff\">Add Promo Title</h4><p class=\"promo-summery\" style=\"font-size:17px;color:#fff\">Add Promo Content</p></div><div class=\"ht-promo-button\"><a href=\"#\" style=\"background-color:#fff;color:#1D1E22\">Button</a></div></div>\n<!-- /wp:hashbar/hashbar-promo-banner -->",
			    )
			);

			register_block_pattern(
			    'hashbar-pro/hashbar-promo-banner-image',
			    array(
			        'title'       => __( 'Hashbar Promo Banner Image','hashbar' ),
			        'description' => __( 'Promo Banner Image', 'hashbar' ),
			        'categories'  => array('hashbar'),
			        'content'     =>"<!-- wp:hashbar/hashbar-promo-banner-image -->\n<div class=\"wp-block-hashbar-hashbar-promo-banner-image ht-promo-banner-image\"><a href=\"#\"><img src=\"https://demo.wphash.com/hashbar/wp-content/uploads/2018/01/promo-image-1.png\" style=\"height:auto;width:250px\"/></a></div>\n<!-- /wp:hashbar/hashbar-promo-banner-image -->",
			    )
			);
		}
	}
}

Hashbar_Block::instance();