<?php
/**
 * AT MultiAuthor Backend class.
 *
 * Handles all dashboard side functionality.
 *
 * @author   thinkatat
 * @category API
 * @package  at-multiauthor/includes
 * @since    1.0.0
 */

namespace AT\MultiAuthor;

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
		add_action( 'save_post_post', array( $this, 'save_metabox_multiauthor' ) );
	}

	/**
	 * Add metaboxes.
	 */
	public function add_metaboxes() {
		add_meta_box(
			'atmat-mb-multi-author',
			__( 'Contributors', 'at-multiauthor' ),
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
		wp_enqueue_style( 'atmat-select2-css' );
		wp_enqueue_script( 'atmat-select2-js' );
		wp_enqueue_script( 'atmat-backend-js' );
		wp_localize_script(
			'atmat-backend-js',
			'atmatStrings',
			array(
				'placeholder' => __( 'Select Contributor(s)', 'at-multiauthor' ),
			)
		);
		$authors = get_post_meta( $post->ID, 'atmat_authors', true );
		$users = get_users(
			array(
				'orderby'      => 'login',
				'order'        => 'ASC',
				'role__in' => get_contributors_role_in( $post->ID ),
			)
		);
		do_action( 'atmat_metabox_multiauthor_before', $authors, $post );
		?>
		<select id="atmat-authors" name="atmat-authors[]" multiple="multiple" style="width: 100%" <?php echo $disabled; ?> >
			<?php
			foreach ( $users as $user ) {
				$selected = in_array( $user->ID, (array) $authors ) ? 'selected' : null; ?>
				<option value="<?php echo esc_attr( $user->ID ); ?>" <?php echo esc_html( $selected ); ?> ><?php echo esc_html( $user->user_login ); ?></option>
				<?php
			} ?>
		</select>
		
		<?php
		if ( $disabled ) {
			?>
			<i><?php esc_html_e( 'You can\'t manage contributors of this post!', 'at-multiauthor' ); ?></i>
			<?php
		}
		do_action( 'atmat_metabox_multiauthor_after', $authors, $post );
		wp_nonce_field( 'atmat_save_settings', 'atmat-nonce' );
	}

	/**
	 * Save metabox Contributors data.
	 *
	 * @param int $post_id id of the post.
	 */
	public function save_metabox_multiauthor( $post_id ) {
		// Security pass 1 - Nonce verification.
		if ( ! isset( $_POST['atmat-nonce'] ) || ! wp_verify_nonce( sanitize_text_field( $_POST['atmat-nonce'] ), 'atmat_save_settings' ) ) {
			return;
		}

		// Security pass 2 - Check if current user is allowed to manage contributors or not.
		if ( ! count( array_intersect( get_allowed_roles( $post_id ), (array) wp_get_current_user()->roles ) ) ) {
			// Current user is not allowed to manage contributors.
			return;
		}

		$authors = array();

		if ( isset( $_POST['atmat-authors'] ) ) {
			$role_in = get_contributors_role_in( $post_id );
			// Security pass 3 - Validate contributors ID.
			foreach ( (array) $_POST['atmat-authors'] as $contributor_id ) {
				$contributor_id = (int) $contributor_id;
				$contributor = get_userdata( $contributor_id );
				if ( count( array_intersect( $role_in, $contributor->roles ) ) ) {
					$authors[] = $contributor_id;
				}
			}
		}

		update_post_meta( $post_id, 'atmat_authors', $authors );
	}
}
