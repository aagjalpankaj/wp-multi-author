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
 * @return array $allowed_roles Allowed roles.
 * @since 1.0.1
 */
function get_allowed_roles() {
	return apply_filters( 'atmat_get_allowed_roles', array( 'administrator', 'editor', 'author' ) );
}
