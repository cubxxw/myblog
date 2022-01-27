<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Hashbar_Notification{

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
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
	 * The Constructor.
	 */
	public function __construct() {
		add_action( 'init', [ $this, 'init' ] );
	}

	public function init(){

		// Return early if this function does not exist.
		if ( ! function_exists( 'register_block_type' ) ) {
			return;
		}

		// Load attributes from block.json.
		ob_start();
		include HASHBAR_BLOCK_PATH . '/src/block/hashbar-notification/block.json';
		$attributes = json_decode( ob_get_clean(), true );

		register_block_type(
			'hashbar/hashbar-button',
			array(
				'attributes'  	  => $attributes,
			)
		);

	}

}

Hashbar_Notification::instance();