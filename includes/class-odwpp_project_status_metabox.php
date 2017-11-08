<?php
/**
 * @author Ondřej Doněk, <ondrejd@gmail.com>
 * @license https://www.mozilla.org/MPL/2.0/ Mozilla Public License 2.0
 * @link https://github.com/ondrejd/odwp-projects for the canonical source repository
 * @package odwp-projects
 * @since 0.1.0
 */

if( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'Odwpp_Project_Status_Metabox' ) ):

/**
 * Meta box "Slug" for {@see Odwpp_Project_Post_Type}.
 * @since 0.1.0
 */
class Odwpp_Project_Status_Metabox {
	const SLUG  = ODWPP_SLUG . '_status_metabox';
	const NONCE = 'odwpp-status-metabox-nonce';

	/**
	 * @internal Hook for `load-post.php` and `load-post-new.php`. Initializes meta box.
	 * @since 0.1.0
	 * @uses add_meta_box
	 */
	public static function init() {
		add_meta_box(
			self::SLUG,
			__( 'Stav projektu', ODWPP_SLUG ),
			[__CLASS__, 'render'],
			Odwpp_Project_Post_Type::SLUG,
			'side',//'normal','side','advanced'
			'high'//'high','low'
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
		include( ODWPP_PATH . 'partials/metabox-project_status.phtml' );
		$output = ob_get_clean();

		/**
		 * Filter for project status meta box.
		 *
		 * @since 0.1.0
		 *
		 * @param string $output Rendered HTML.
		 * @param WP_Post $project
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

		$value = filter_input( INPUT_POST, 'project_status' );
		update_post_meta( $post_id, self::SLUG, $value);

		return $post_id;
	}

	/**
	 * @internal Hook for `manage_project_posts_columns` filter.
	 * @param array $columns
	 * @return array
	 * @since 0.1.0
	 */
	public static function column_head( $columns ) {
		$columns[self::SLUG] = __( 'Stav projektu', ODWPP_SLUG );
		return $columns;
	}

	/**
	 * @internal Hook for `manage_project_posts_custom_column` action.
	 * @param array $columns
	 * @return array
	 * @since 0.1.0
	 * @uses get_post_meta
	 */
	public static function column_body( $column, $post_id ) {
		if ( $column != self::SLUG ) {
			return;
		}

		$status = get_post_meta( $post_id , self::SLUG , true );
		$label = '';
		switch( $status ) {
			case 'active'    : $label = __( 'Aktivní', ODWPP_SLUG ); break;
			case 'nonactive' : $label = __( 'Neaktivní', ODWPP_SLUG ); break;
			case 'finished'  : $label = __( 'Dokončený', ODWPP_SLUG ); break;
			case 'cancelled' : $label = __( 'Zrušený', ODWPP_SLUG ); break;
		}

		printf(
			'<span id="%s" class="%s" data-project_status="%s">%s</span>',
			ODWPP_SLUG . '-project_status-' . $post_id,
			'project-status project-status-' . $status,
			$status, $label
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

	/**
	 * @internal Hook for actions `quick_edit_custom_box` and `bulk_edit_custom_box`.
	 * @param string $column_name
	 * @param string $post_type
	 * @return void
	 * @since 0.2.0
	 * @todo Finish NONCE field!
	 */
	public static function quick_edit( $column_name, $post_type ) {
		if ( $column_name != self::SLUG || $post_type != Odwpp_Project_Post_Type::SLUG ) {
			return;
		}

		$nonce = wp_create_nonce( self::NONCE );

		ob_start();
		include( ODWPP_PATH . 'partials/metabox-project_status_quickedit.phtml' );
		$output = ob_get_clean();

		/**
		 * Filter for project status quick edit box.
		 *
		 * @since 0.2.0
		 *
		 * @param string $output Rendered HTML.
		 */
		$output = apply_filters( self::SLUG . '_quickedit', $output );
		echo $output;
	}

	/**
	 * @internal Hook for `save_post` action (quickedit/bulkactions in list table).
	 * @param integer $post_id
	 * @param WP_Post $post
	 * @return void
	 * @since 0.2.0
	 */
	public static function quick_edit_save_post( $post_id, $post ) {
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( $post->post_type != Odwpp_Project_Post_Type::SLUG ) {
			return;
		}

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		$value = filter_input( INPUT_POST, 'project_status' );
		if ( ! empty( $value ) ) {
			update_post_meta( $post_id, self::SLUG, $value );
		}
	}

	/**
	 * @internal Hook for `admin_print_scripts-edit.php` action.
	 * @return void
	 * @since 0.2.0
	 */
	public static function enqueue_edit_scripts() {
		wp_enqueue_script(
			ODWPP_SLUG . '-admin-edit-project_status',
			plugins_url( 'assets/js/admin-status_quickedit.js', ODWPP_FILE ),
			['jquery', 'inline-edit-post'],
			'',
			true
		);
	}
}

endif;

if ( is_admin() ) {
	add_action( 'load-post.php', ['Odwpp_Project_Status_Metabox', 'init'] );
	add_action( 'load-post-new.php', ['Odwpp_Project_Status_Metabox', 'init'] );
	add_action( 'save_post', ['Odwpp_Project_Status_Metabox', 'save'], 10, 3 );
	add_filter( 'manage_' . Odwpp_Project_Post_Type::SLUG . '_posts_columns', ['Odwpp_Project_Status_Metabox', 'column_head'] );
	add_action( 'manage_' . Odwpp_Project_Post_Type::SLUG . '_posts_custom_column' , ['Odwpp_Project_Status_Metabox', 'column_body'], 10, 2 );
	add_filter( 'manage_edit-' . Odwpp_Project_Post_Type::SLUG . '_sortable_columns', ['Odwpp_Project_Status_Metabox', 'column_sortable'] );
	add_action( 'load-edit.php', ['Odwpp_Project_Status_Metabox', 'column_sort_request'] );
	add_action( 'quick_edit_custom_box',  ['Odwpp_Project_Status_Metabox', 'quick_edit'], 10, 2 );
	add_action( 'bulk_edit_custom_box', ['Odwpp_Project_Status_Metabox', 'quick_edit'], 10, 2 );
	add_action( 'save_post', ['Odwpp_Project_Status_Metabox', 'quick_edit_save_post'], 10, 2 );
	add_action( 'admin_print_scripts-edit.php', ['Odwpp_Project_Status_Metabox', 'enqueue_edit_scripts'] );
}
