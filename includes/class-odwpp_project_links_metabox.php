<?php
/**
 * Meta box "Links".
 *
 * @author Ondřej Doněk, <ondrejd@gmail.com>
 * @license https://www.mozilla.org/MPL/2.0/ Mozilla Public License 2.0
 * @link https://github.com/ondrejd/odwp-projects for the canonical source repository
 * @package odwp-projects
 * @since 0.1.0
 */

if ( ! class_exists( 'Odwpp_Project_Links_Metabox' ) ):

/**
 * Meta box "Links" for {@see Odwpp_Project_Post_Type}.
 * @since 0.1.0
 */
class Odwpp_Project_Links_Metabox {
	const SLUG = 'odwp_project_links_metabox';
	const NONCE = 'odwpp-links-metabox-nonce';

	/**
	 * Hook for `admin_init` action. Initialize meta box.
	 * @since 0.1.0
	 * @uses add_meta_box()
	 * @uses add_action()
	 */
	public static function admin_init() {
		add_meta_box(
			self::SLUG,
			__( 'Odkazy', ODWPP_SLUG ),
			array( __CLASS__, 'render' ),
			Odwpp_Project_Post_Type::SLUG,
			'side',
			'low'
		);
		add_action( 'save_post', array( __CLASS__, 'save' ), 10, 3 );
	}

	/**
	 * Render meta box.
	 * @param WP_Post $project
	 * @since 0.1.0
	 * @uses apply_filters()
	 * @uses get_post_meta()
	 * @uses wp_create_nonce()
	 */
	public static function render( $project ) {
		// Variables used in template
		$value = get_post_meta( $project->ID, 'price', true );
		$nonce = wp_create_nonce( self::NONCE );

		ob_start();
		include( ODWPP_PATH . 'partials/metabox-project_links.php' );
		$output = ob_get_clean();

		/**
		 * Filter for project repository meta box.
		 *
		 * @since 0.1.0
		 *
		 * @param string $output Rendered HTML.
		 */
		$output = apply_filters( self::SLUG, $output, $project );
		echo $output;
	}

	/**
	 * Hook for `save_post` action. Save meta box values.
	 * @param integer $post_id
	 * @param WP_Post $post
	 * @param boolean $update
	 * @since 0.1.0
	 * @uses wp_verify_nonce()
	 * @uses current_user_can()
	 * @uses update_post_meta()
	 */
	public static function save( $post_id, $post, $update ) {
		$nonce = filter_input( INPUT_POST, self::NONCE );

		if ( ( bool ) wp_verify_nonce( $nonce, self::NONCE ) !== true ) {
			return $post_id;
		}

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return $post_id;
		}

		if ( defined( 'DOING_AUTOSAVE') && DOING_AUTOSAVE ) {
			return $post_id;
		}

		if ( $post->post_type != Odwpp_Project_Post_Type::SLUG ) {
			return $post_id;
		}

		$value = filter_input( INPUT_POST, 'dimensions' );
		update_post_meta( $post_id, 'dimensions', $value);

		return $post_id;
	}
}

endif; // Odwpp_Project_Links_Metabox

if ( is_admin() ) {
	add_action( 'admin_init', array( 'Odwpp_Project_Links_Metabox', 'admin_init' ) );
}
