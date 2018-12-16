<?php
/**
 * WP Multi Author uninstall
 *
 * Removes plugin's data.
 *
 * @author      aagjalpankaj
 * @package     wp-multi-author
 * @since       1.0.0
 */

defined( 'WP_UNINSTALL_PLUGIN' ) || exit( 'This is not the way to call me.' );

global $wpdb;

// Delete postmeta `wpmat_authors` - Contributors associated with posts.
$wpdb->query( "DELETE FROM {$wpdb->postmeta} WHERE meta_key='wpmat_authors';" );
