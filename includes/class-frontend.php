<?php
/**
 * WP Multi Author Frontent class.
 *
 * Handles all frontend side functionality.
 *
 * @author   aagjalpankaj
 * @package  wp-multi-author/includes
 * @since    1.0.0
 */

namespace WP_Multi_Author;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit( 'This is not the way to call me.' );

/**
 * Class handles frontend side functionality.
 */
class Frontend {


	/**
	 * Class constructor.
	 */
	public function __construct() {
		add_filter( 'the_content', array( $this, 'the_content' ) );
	}

	/**
	 * Metabox Contributors content.
	 *
	 * @param 	string $content Post content.
	 * @return 	string $content Post content.
	 */
	public function the_content( $content ) {

		if ( is_single() ) {
			global $post;

			$authors = apply_filters( 'wpmat_get_contributors_list', get_post_meta( $post->ID,  'wpmat_authors', true ), $post->ID );

			if ( is_array( $authors ) && count( $authors ) ) {

				ob_start();
				wp_enqueue_style( 'wpmat-frontend-css' );
				?>
				<div class="wpmat-contributors-wrapper">
					<strong><?php esc_html_e( 'Contributors', 'wp-multi-author' ); ?></strong>
					<ul>
					<?php
					foreach ( (array) $authors as $author_id ) {
						$user_info = get_userdata( $author_id );

						if ( $user_info ) {
							?>
							<li>
								<span>
									<?php echo get_avatar( $user_info->ID, apply_filters( 'wpmat_author_bio_avatar_size', 42 ) ); ?>
								</span>
								<span>
									<a href="<?php echo esc_url( get_author_posts_url( $user_info->ID ) ); ?>">
										<?php echo esc_html( $user_info->display_name ); ?>
									</a>
								</span>
							</li>
							<?php
						}
					}
					?>
					</ul>
				</div>
				<?php

				$content .= ob_get_clean();
			}
		}

		return $content;
	}
}
