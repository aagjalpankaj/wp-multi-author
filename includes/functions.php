<?php
/**
 * General purpose reusable functions.
 *
 * @author      aagjalpankaj
 * @package     wp-multi-author
 * @since     	1.0.0
 */

namespace WP_Multi_Author;

/**
 * Function returns user roles having access to manage contributors.
 *
 * @param  int $post_id Post ID for which want.
 * @return array $allowed_roles Allowed roles.
 * @since 1.0.0
 */
function get_allowed_roles( $post_id = null ) {
	return apply_filters( 'wpmat_get_allowed_roles', array( 'administrator', 'editor', 'author' ), $post_id );
}


/**
 * Function returns user roles which have to be in the contributors list.
 *
 * @param  int $post_id Post ID for which roles want.
 * @return array $include_roles Roles which have to include in the contributors list.
 * @since 1.0.0
 */
function get_contributors_role_in( $post_id = null ) {
	return apply_filters( 'wpmat_get_contributors_role_in', array( 'administrator', 'editor', 'author', 'contributor' ), $post_id );
}
