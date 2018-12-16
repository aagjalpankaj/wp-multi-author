<?php
/**
 * The file contains class Main - Primary entry point of the plugin.
 *
 * @author      aagjalpankaj
 * @package     wp-multi-author
 * @since     	1.0.0
 */

namespace WP_Multi_Author;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit( 'This is not the way to call me.' );

/**
 * Class Main - Entry point of the plugin.
 */
class Main {

	/**
	 * Singleton instance of the class.
	 *
	 * @var object Main
	 */
	private static $instance = null;

	/**
	 * Version of the plugin.
	 *
	 * @var string
	 */
	public static $version;

	/**
	 * Backend instance of the plugin.
	 *
	 * @var obejct Backend
	 */
	public $backend;

	/**
	 * Frontend instance of the plugin.
	 *
	 * @var object Frontend
	 */
	public $frontend;


	/**
	 * Contructor of the class.
	 *
	 * @param   string $version Version of the plugin.
	 */
	public function __construct( $version = '1.0.0' ) {

		self::$version = $version;

		add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'register_backend_scripts' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'register_frontend_scripts' ) );

		require_once( WPMAT_DIR . '/includes/functions.php' );
		require_once( WPMAT_DIR . '/includes/class-backend.php' );
		require_once( WPMAT_DIR . '/includes/class-frontend.php' );

		$this->backend  = new \WP_Multi_Author\Backend();
		$this->frontend = new \WP_Multi_Author\Frontend();
	}

	/**
	 * Create and return singleton instance of the class.
	 *
	 * @return  object  $instance  Singleton instance of Main.
	 */
	public static function get_instance() {

		if ( is_null( self::$instance ) ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Load textdomain of the plugin.
	 */
	public function load_textdomain() {
		load_plugin_textdomain( 'wp-multi-author', false, WPMAT_DIR . '/languages' );
	}

	/**
	 * Register admin side scripts.
	 */
	public function register_backend_scripts() {

		// Select2 CSS.
		wp_register_style(
			'wpmat-select2-css',
			WPMAT_URL . '/lib/select2/select2.css',
			array(),
			WPMAT_VERSION
		);

		// Select2 JS.
		wp_register_script(
			'wpmat-select2-js',
			WPMAT_URL . '/lib/select2/select2.min.js',
			array( 'jquery' ),
			WPMAT_VERSION,
			false
		);

		// Admin side main CSS.
		wp_register_style(
			'wpmat-backend-css',
			WPMAT_URL . '/assets/css/backend.css',
			array(),
			WPMAT_VERSION
		);

		// Admin side main JS.
		wp_register_script(
			'wpmat-backend-js',
			WPMAT_URL . '/assets/js/backend.min.js',
			array( 'jquery', 'wpmat-select2-js' ),
			WPMAT_VERSION,
			false
		);
	}

	/**
	 * Register frontend side scripts.
	 */
	public function register_frontend_scripts() {
		wp_register_style(
			'wpmat-frontend-css',
			WPMAT_URL . '/assets/css/frontend.css',
			array(),
			WPMAT_VERSION
		);
	}
}
