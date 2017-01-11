<?php
/**
 * Plugin Name: AT MultiAuthor
 * Plugin URI: http://thinkatat.com/
 * Description: One post, multiple contributors!
 * Version: 1.0.3
 * Author: thinkatat
 * Author URI: http://thinkatat.com/
 * Text Domain: at-multiauthor
 * Domain Path: /languages/
 *
 * @package at-multiauthor
 * @author thinkatat
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit( 'This is not the way to call me.' );

// Plugin setup - Basic constants.
define( 'ATMAT_VERSION', '1.0.3' );
define( 'ATMAT_DIR', untrailingslashit( plugin_dir_path( __FILE__ ) ) );
define( 'ATMAT_URL', untrailingslashit( plugins_url( basename( plugin_dir_path( __FILE__ ) ), basename( __FILE__ ) ) ) );

// Plugin setup - class Main singleton instance.
require_once( ATMAT_DIR . '/includes/class-main.php' );
$GLOBALS['AT_MultiAuthor'] = new \AT\MultiAuthor\Main( ATMAT_VERSION );
