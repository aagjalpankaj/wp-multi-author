<?php
/**
 * Plugin Name: WP Multi Author
 * Plugin URI: https://github.com/aagjalpankaj/wp-multi-author/
 * Description: One post, multiple contributors!
 * Version: 1.0.0
 * Author: aagjalpankaj
 * Author URI: https://www.linkedin.com/in/aagjalpankaj/
 * Text Domain: wp-multi-author
 * Domain Path: /languages/
 *
 * @package wp-multi-author
 * @author aagjalpankaj
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit( 'This is not the way to call me.' );

// Plugin setup - Basic constants.
define( 'WPMAT_VERSION', '1.0.0' );
define( 'WPMAT_DIR', untrailingslashit( plugin_dir_path( __FILE__ ) ) );
define( 'WPMAT_URL', untrailingslashit( plugins_url( basename( plugin_dir_path( __FILE__ ) ), basename( __FILE__ ) ) ) );

// Plugin setup - class Main singleton instance.
require_once( WPMAT_DIR . '/includes/class-main.php' );
$GLOBALS['WP_Multi_Author'] = new \WP_Multi_Author\Main( WPMAT_VERSION );
