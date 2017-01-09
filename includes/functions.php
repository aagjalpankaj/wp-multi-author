<?php
/**
 * General purpose reusable functions.
 *
 * @author      thinkatat
 * @package     at-multiauthor
 * @since     	1.0.1
 */

namespace AT\MultiAuthor;

/**
 * Function returns user roles having access to manage contributors.
 *
 * @param  int $post_id Post ID for which want.
 * @return array $allowed_roles Allowed roles.
 * @since 1.0.1
 */
function get_allowed_roles( $post_id = null ) {
	return apply_filters( 'atmat_get_allowed_roles', array( 'administrator', 'editor', 'author' ), $post_id );
}


/**
 * Function returns user roles which have to be in the contributors list.
 *
 * @param  int $post_id Post ID for which roles want.
 * @return array $include_roles Roles which have to include in the contributors list.
 * @since 1.0.2
 */
function get_contributors_role_in( $post_id = null ) {
	return apply_filters( 'atmat_get_contributors_role_in', array( 'administrator', 'editor', 'author', 'contributor' ), $post_id );
}
