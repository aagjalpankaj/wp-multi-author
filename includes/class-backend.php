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
		add_action( 'save_post_post', array( $this, 'save_post_post' ) );
	}

	/**
	 * Define all metaboxes.
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
	 * @param object $post posts.
	 */
	public function render_metabox_multiauthor( $post ) {
		wp_enqueue_style( 'atmat-select2-css' );
		wp_enqueue_script( 'atmat-select2-js' );
		wp_enqueue_script( 'atmat-backend-js' );
		
		$authors = get_post_meta( $post->ID, 'atmat_authors', true );
		$users = get_users(
			array(
				'orderby'      => 'login',
				'order'        => 'ASC',
			)
		); ?>
		<select id="atmat-authors" name="atmat-authors[]" multiple="multiple" style="width: 100%">
			<?php
			foreach ( $users as $user ) {
				$selected = in_array( $user->ID, (array) $authors ) ? 'selected' : null; ?>
				<option value="<?php echo esc_attr( $user->ID ); ?>" <?php echo esc_html( $selected ); ?> ><?php echo esc_html( $user->user_login ); ?></option>
				<?php
			} ?>
		</select>
	<?php
		wp_nonce_field( 'atmat_save_settings', 'atmat-nonce' );
	}

	/**
	 * Save metabox Contributors data.
	 *
	 * @param int $post_id id of the post.
	 */
	public function save_post_post( $post_id ) {
		// Security pass 1.
		if ( ! isset( $_POST['atmat-nonce'] ) || ! wp_verify_nonce( sanitize_text_field( $_POST['atmat-nonce'] ), 'atmat_save_settings' ) ) {
			return;
		}

		$allowed_roles = array( 'administrator', 'editor', 'author' );
		$user = wp_get_current_user();

		if ( ! count( array_intersect( $allowed_roles, (array) $user->roles ) ) ) {
			return;
		}

		$authors = array();

		if ( isset( $_POST['atmat-authors'] ) ) {
			$authors = array_map( 'esc_attr', (array) $_POST['atmat-authors'] );
		}

		update_post_meta( $post_id, 'atmat_authors', $authors );
	}
}
