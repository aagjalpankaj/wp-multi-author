<?php
/**
 * AT MultiAuthor uninstall
 *
 * Removes plugin's data.
 *
 * @author      thinkatat
 * @package     at-multiauthor
 * @since       1.0.3
 */

defined( 'WP_UNINSTALL_PLUGIN' ) || exit( 'This is not the way to call me.' );

global $wpdb;

// Delete postmeta `atmat_authors` - Contributors associated with posts.
$wpdb->query( "DELETE FROM {$wpdb->postmeta} WHERE meta_key='wpmat_authors';" );
