<?php
/**
 * Initial Class for WordPress Customizer Search
 *
 * @since  1.0.0
 * @package  Customizer_Search
 */

/**
 * Handles Customizer logic for the theme builder.
 *
 * @since 1.0
 */
class Customizer_Search {

	/**
	 * Instance of Customizer_Search
	 *
	 * @var Customizer_Search
	 */
	private static $instance;

	/**
	 *  Initiator
	 */
	public static function instance() {

		if ( ! isset( self::$instance ) ) {
			self::$instance = new Customizer_Search();

			self::$instance->hooks();
		}

		return self::$instance;
	}

	/**
	 * Initialize hooks.
	 *
	 * @since 1.0
	 * @return void
	 */
	private function hooks() {
		add_action( 'customize_controls_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'customize_controls_print_footer_scripts', array( $this, 'footer_scripts' ) );
		add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );
	}

	/**
	 * Load the plugin textdomain.
	 *
	 * @return void
	 */
	public function load_textdomain() {
		load_plugin_textdomain( 'wp-customizer-search', false, basename( dirname( __FILE__ ) ) . '/languages/' );
	}

	/**
	 * Enqueues scripts for the Customizer.
	 *
	 * @since 1.0
	 * @return void
	 */
	public function enqueue_scripts() {
		wp_enqueue_style( 'wp-customizer-search-admin', BSFCS_URL . 'assets/css/wp-customizer-search-admin.css', array(), BSFCS_VER );
		wp_enqueue_script( 'wp-customizer-search-admin', BSFCS_URL . 'assets/js/wp-customizer-search-admin.compiled.js', array(), filemtime( BSFCS_DIR . 'assets/js/wp-customizer-search-admin.compiled.js' ), true );
	}

	/**
	 * Renders the Customizer footer scripts.
	 *
	 * @since 1.0
	 * @return void
	 */
	public function footer_scripts() {
		include BSFCS_DIR . 'templates/admin-customize-js-templates.php';
	}
}

Customizer_Search::instance();
