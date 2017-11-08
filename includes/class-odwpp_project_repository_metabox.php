<?php
/**
 * @author Ondřej Doněk, <ondrejd@gmail.com>
 * @license https://www.mozilla.org/MPL/2.0/ Mozilla Public License 2.0
 * @link https://github.com/ondrejd/odwp-projects for the canonical source repository
 * @package odwp-projects
 * @since 0.1.0
 */

if ( ! class_exists( 'Odwpp_Project_Repository_Metabox' ) ):

/**
 * Meta box "Repository" for {@see Odwpp_Project_Post_Type}.
 * @since 0.1.0
 */
class Odwpp_Project_Repository_Metabox {
	const SLUG = 'odwpp_project_repository_metabox';
	const NONCE = 'odwpp-repository-metabox-nonce';

	/**
	 * @internal Hook for `load-post.php` and `load-post-new.php`. Initializes meta box.
	 * @since 0.1.0
	 * @uses add_meta_box
	 */
	public static function init() {
		add_meta_box(
			self::SLUG,
			__( 'Repozitář', ODWPP_SLUG ),
			array( __CLASS__, 'render' ),
			Odwpp_Project_Post_Type::SLUG,
			'side',//'normal','side','advanced'
			'low'//'high','low'
		);
	}

	/**
	 * @internal Renders meta box.
	 * @param WP_Post $project
	 * @since 0.1.0
	 * @uses apply_filters
	 * @uses get_post_meta
	 * @uses wp_create_nonce
	 */
	public static function render( $project ) {
		// Variables used in template
		$value = get_post_meta( $project->ID, self::SLUG, true );
		$nonce = wp_create_nonce( self::NONCE );

		ob_start();
		include( ODWPP_PATH . 'partials/metabox-project_repository.php' );
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
	 * @internal Hook for `save_post` action. Saves meta box values.
	 * @param integer $post_id
	 * @param WP_Post $post
	 * @param boolean $update
	 * @since 0.1.0
	 * @uses wp_verify_nonce
	 * @uses current_user_can
	 * @uses update_post_meta
	 * @todo Finish NONCE implementation!
	 */
	public static function save( $post_id, $post, $update ) {
		$nonce = filter_input( INPUT_POST, self::NONCE );

		// XXX Finish NONCE implementation!
		//if ( ( bool ) wp_verify_nonce( $nonce, self::NONCE ) !== true ) {
		//	return $post_id;
		//}

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return $post_id;
		}

		if ( defined( 'DOING_AUTOSAVE') && DOING_AUTOSAVE ) {
			return $post_id;
		}

		if ( $post->post_type != Odwpp_Project_Post_Type::SLUG ) {
			return $post_id;
		}

		$value = filter_input( INPUT_POST, 'project_repository' );
		update_post_meta( $post_id, self::SLUG, $value);

		return $post_id;
	}

	/**
	 * @internal Hook for `manage_project_posts_columns`.
	 * @param array $columns
	 * @return array
	 * @since 0.1.0
	 */
	public static function column_head( $columns ) {
		$columns[self::SLUG] = __( 'Repozitář', ODWPP_SLUG );
		return $columns;
	}

	/**
	 * @internal Hook for `manage_project_posts_custom_column`.
	 * @param array $columns
	 * @return array
	 * @since 0.1.0
	 * @uses get_post_meta
	 */
	public static function column_body( $column, $post_id ) {
		if ( $column != self::SLUG ) {
			return;
		}
		printf(
			'<a href="%1$s" target="_blank" title="%2$s">%1$s</a>',
			get_post_meta( $post_id , self::SLUG , true ),
			__( 'Otevře URL v novém panelu', ODWPP_SLUG )
		);
	}

	/**
	 * @internal Hook for `manage_edit-project_sortable_columns` filter.
	 * @param array $columns
	 * @return array
	 * @since 0.1.0
	 */
	public static function column_sortable( $columns ) {
		$columns[self::SLUG] = self::SLUG;
		return $columns;
	}

	/**
	 * @internal Hook for `load-edit.php` action.
	 * @return void
	 * @since 0.1.0
	 * @uses add_filter
	 */
	public static function column_sort_request() {
		add_filter( 'request', [__CLASS__, 'column_sort'] );
	}

	/**
	 * @internal Hook for `request` filter.
	 * @param array $vars
	 * @return array
	 * @since 0.1.0
	 */
	public static function column_sort( $vars ) {
		if ( isset( $vars['post_type'] ) && Odwpp_Project_Post_Type::SLUG == $vars['post_type'] ) {
			if ( isset( $vars['orderby'] ) && self::SLUG == $vars['orderby'] ) {
				$vars = array_merge( $vars, [
					'meta_key' => self::SLUG,
					'orderby' => 'meta_value'
				] );
			}
		}

		return $vars;
	}
}

endif;

if ( is_admin() ) {
	add_action( 'load-post.php', ['Odwpp_Project_Repository_Metabox', 'init'] );
	add_action( 'load-post-new.php', ['Odwpp_Project_Repository_Metabox', 'init'] );
	add_action( 'save_post', ['Odwpp_Project_Repository_Metabox', 'save'], 10, 3 );
	add_filter( 'manage_' . Odwpp_Project_Post_Type::SLUG . '_posts_columns', ['Odwpp_Project_Repository_Metabox', 'column_head'] );
	add_action( 'manage_' . Odwpp_Project_Post_Type::SLUG . '_posts_custom_column' , ['Odwpp_Project_Repository_Metabox', 'column_body'], 10, 2 );
	add_filter( 'manage_edit-' . Odwpp_Project_Post_Type::SLUG . '_sortable_columns', ['Odwpp_Project_Repository_Metabox', 'column_sortable'] );
	add_action( 'load-edit.php', ['Odwpp_Project_Repository_Metabox', 'column_sort_request'] );
}
