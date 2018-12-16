<?php
/**
 * WP Multi Author Backend class.
 *
 * Handles all dashboard side functionality.
 *
 * @author   aagjalpankaj
 * @package  wp-multi-author/includes
 * @since    1.0.0
 */

namespace WP_Multi_Author;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit( 'This is not the way to call me.' );

/**
 * Class handles dashboard side functionality.
 */
class Backend {

	/**
	 * Class constructor.
	 */
	public function __construct() {
		add_action( 'add_meta_boxes', array( $this, 'add_metaboxes' ), 10, 2 );
		add_action( 'wp_ajax_get_contributors_list', array( $this, 'get_contributors_list' ) );
		add_action( 'save_post_post', array( $this, 'save_metabox_multiauthor' ) );
	}

	/**
	 * Add metaboxes.
	 */
	public function add_metaboxes() {

		add_meta_box(
			'wpmat-mb-multi-author',
			__( 'Contributors', 'wp-multi-author' ),
			array( $this, 'render_metabox_multiauthor' ),
			'post',
			'side',
			'high'
		);
	}

	/**
	 * Metabox Contributors content.
	 *
	 * @param object $post post.
	 */
	public function render_metabox_multiauthor( $post ) {

		$disabled = null;
		if ( ! count( array_intersect( get_allowed_roles( $post->ID ), (array) wp_get_current_user()->roles ) ) ) {
			// Current user is not allowed to manage contributors.
			$disabled = 'disabled';
		}

		// Required CSS and JS.
		wp_enqueue_style( 'wpmat-select2-css' );
		wp_enqueue_script( 'wpmat-select2-js' );
		wp_enqueue_script( 'wpmat-backend-js' );
		wp_localize_script( 'wpmat-backend-js', 'wpmatBackend', array() );

		$author_ids = array_filter( array_map( 'absint', (array) get_post_meta( $post->ID, 'wpmat_authors', true ) ) );

		$json_ids    = array();
		foreach ( $author_ids as $author_id ) {
			$author = get_user_by( 'id', $author_id );
			if ( is_object( $author ) ) {
				$json_ids[ $author_id ] = esc_html( $author->display_name ) . ' (#' . absint( $author->ID ) . ' &ndash; ' . esc_html( $author->user_email ) . ')';
			}
		}

		do_action( 'wpmat_metabox_multiauthor_before', $author_ids, $post );
		?>
		<input type="hidden" class="wpmat-select2" name="wpmat-authors" style="width: 100%" data-placeholder="<?php esc_attr_e( 'Add contributors&hellip;', 'wp-multi-author' ); ?>" data-action="get_contributors_list" data-multiple="true" data-allow_clear="true" data-selected="<?php echo esc_attr( json_encode( $json_ids ) ); ?>" value="<?php echo implode( ',', array_keys( $json_ids ) ); ?>" <?php echo $disabled; ?> />
		<?php
		if ( $disabled ) {
			?>
			<i><?php esc_html_e( 'You can\'t manage contributors of this post!', 'at-multiauthor' ); ?></i>
			<?php
		}
		do_action( 'wpmat_metabox_multiauthor_after', $author_ids, $post );
		wp_nonce_field( 'wpmat_save_settings', 'wpmat-nonce' );
	}

	public function get_contributors_list() {

		ob_start();

		// Security pass 1.
		check_ajax_referer( 'wpmat_save_settings', 'security' );

		// Security pass 2.
		if ( ! current_user_can( 'edit_posts' ) ) {
			die();
		}

		$term = sanitize_text_field( stripslashes( $_GET['term'] ) );

		// No input.
		if ( empty( $term ) ) {
			die();
		}

		$exclude = apply_filters( 'wpmat_get_exclude_contributors_ids', array() );

		$found_contributors = array();

		// Add search column `display_name`.
		add_filter( 'user_search_columns', function( $search_columns ) {
		    $search_columns[] = 'display_name';
		    return $search_columns;
		} );

		// WP user query.
		$contributors_query = new \WP_User_Query( apply_filters( 'wpmat_get_contributors_list_query', array(
			'fields'         => 'all',
			'orderby'        => 'display_name',
			'search'         => '*' . $term . '*',
			'search_columns' => array( 'ID', 'display_name', 'user_email' ),
			'role__in'		 => get_contributors_role_in(),
		) ) );

		$contributors = $contributors_query->get_results();

		if ( ! empty( $contributors ) ) {
			foreach ( $contributors as $contributor ) {
				if ( ! in_array( $contributor->ID, $exclude ) ) {
					$found_contributors[ $contributor->ID ] = $contributor->display_name . ' (#' . $contributor->ID . ' &ndash; ' . sanitize_email( $contributor->user_email ) . ')';
				}
			}
		}

		$found_contributors = apply_filters( 'wpmat_found_contributors', $found_contributors );
		wp_send_json( $found_contributors );
	}

	/**
	 * Save metabox Contributors data.
	 *
	 * @param int $post_id id of the post.
	 */
	public function save_metabox_multiauthor( $post_id ) {
		// Security pass 1 - Nonce verification.
		if ( ! isset( $_POST['wpmat-nonce'] ) || ! wp_verify_nonce( sanitize_text_field( $_POST['wpmat-nonce'] ), 'wpmat_save_settings' ) ) {
			return;
		}

		// Security pass 2 - Check if current user is allowed to manage contributors or not.
		if ( ! count( array_intersect( get_allowed_roles( $post_id ), (array) wp_get_current_user()->roles ) ) ) {
			// Current user is not allowed to manage contributors.
			return;
		}

		$authors = array();

		if ( isset( $_POST['wpmat-authors'] ) && ! empty( $_POST['wpmat-authors'] ) ) {
			$role_in = get_contributors_role_in( $post_id );
			$post_authors = explode( ',', sanitize_text_field( $_POST['wpmat-authors'] ) );
			// Security pass 3 - Validate contributors ID.
			foreach ( $post_authors as $contributor_id ) {
				$contributor_id = (int) $contributor_id;
				$contributor = get_userdata( $contributor_id );
				if ( count( array_intersect( $role_in, $contributor->roles ) ) ) {
					$authors[] = $contributor_id;
				}
			}
		}

		update_post_meta( $post_id, 'wpmat_authors', $authors );
	}
}
