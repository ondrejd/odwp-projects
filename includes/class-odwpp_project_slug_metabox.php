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

if ( ! class_exists( 'Odwpp_Project_Slug_Metabox' ) ):

/**
 * Meta box "Slug" for {@see Odwpp_Project_Post_Type}.
 * @since 0.1.0
 */
class Odwpp_Project_Slug_Metabox {
	const SLUG = 'odwpp_project_slug_metabox';
	const NONCE = 'odwpp-slug-metabox-nonce';

	/**
	 * Constructor.
	 * @since 0.2.1
	 */
	public function __construct() {
		if ( ! is_admin() ) {
			return;
		}

		add_action( 'load-post.php', [$this, 'init'] );
		add_action( 'load-post-new.php', [$this, 'init'] );
		// Save meta box value
		add_action( 'save_post', [$this, 'save'], 10, 3 );
		// Add list table column - head/body
		add_filter( 'manage_' . Odwpp_Project_Post_Type::SLUG . '_posts_columns', [$this, 'column_head'] );
		add_action( 'manage_' . Odwpp_Project_Post_Type::SLUG . '_posts_custom_column' , [$this, 'column_body'], 10, 2 );
		// Make our column sortable
		add_filter( 'manage_edit-' . Odwpp_Project_Post_Type::SLUG . '_sortable_columns', [$this, 'column_sortable'] );
		add_action( 'load-edit.php', [$this, 'column_sort_request'] );
		// Quick Edit
		add_action( 'quick_edit_custom_box',  [$this, 'quick_edit'], 10, 2 );
		add_action( 'save_post', [$this, 'quick_edit_save_post'], 10, 2 );
		add_action( 'admin_print_scripts-edit.php', [$this, 'enqueue_edit_scripts'] );
	}

	/**
	 * @internal Hook for `load-post.php` and `load-post-new.php`. Initializes meta box.
	 * @since 0.1.0
	 * @uses add_meta_box
	 */
	public function init() {
		add_meta_box(
			self::SLUG,
			__( 'Systémový název', ODWPP_SLUG ),
			[$this, 'render'],
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
	 * @todo Finish NONCE implementation!
	 */
	public function render( $project ) {
		$value = get_post_meta( $project->ID, self::SLUG, true );
		$nonce = wp_create_nonce( self::NONCE );
		$html  = '';

		$html .= '<div class="project_slug_metabox">';
		$html .= '<input type="hidden" name="' . Odwpp_Project_Status_Metabox::NONCE . '" value="' . $nonce . '">';
		$html .= '<label for="odwpp-project_slug" class="screen-reader-text">' . __( 'Systémový  název projektu:', ODWPP_SLUG ) . '</label>';
		$html .= '<input type="text" name="project_slug" id="odwpp-project_slug" value="' . $value . '">';
		$html .= '<p class="description">' . __( 'Měl by odpovídat trvalému odkazu a zároveň i názvu repozitáře (např. <strong>odwp-projects</strong>).', ODWPP_SLUG ) . '</p>';
		$html .= '</div>';

		/**
		 * Filter for project slug meta box.
		 *
		 * @param string $output Rendered HTML.
		 * @param WP_Post $project
		 * @since 0.1.0
		 */
		$output = apply_filters( self::SLUG, $html, $project );
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
	public function save( $post_id, $post, $update ) {
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

		$value = filter_input( INPUT_POST, 'project_slug' );
		update_post_meta( $post_id, self::SLUG, $value);

		return $post_id;
	}

	/**
	 * @internal Hook for `manage_project_posts_columns` filter.
	 * @param array $columns
	 * @return array
	 * @since 0.1.0
	 */
	public function column_head( $columns ) {
		$columns[self::SLUG] = __( 'Systémový název', ODWPP_SLUG );
		return $columns;
	}

	/**
	 * @internal Hook for `manage_project_posts_custom_column` action.
	 * @param array $columns
	 * @return array
	 * @since 0.1.0
	 * @uses get_post_meta
	 */
	public function column_body( $column, $post_id ) {
		if ( $column != self::SLUG ) {
			return;
		}

        $slug = get_post_meta( $post_id , self::SLUG , true );

        if ( empty( $slug ) ) {
            return;
        }

		printf( '<code id="%s">%s</code>', ODWPP_SLUG . '-project_slug-' . $post_id, $slug );
	}

	/**
	 * @internal Hook for `manage_edit-project_sortable_columns` filter.
	 * @param array $columns
	 * @return array
	 * @since 0.1.0
	 */
	public function column_sortable( $columns ) {
		$columns[self::SLUG] = self::SLUG;
		return $columns;
	}

	/**
	 * @internal Hook for `load-edit.php` action.
	 * @since 0.1.0
	 * @uses add_filter
	 */
	public function column_sort_request() {
		add_filter( 'request', [$this, 'column_sort'] );
	}

	/**
	 * @internal Hook for `request` filter.
	 * @param array $vars
	 * @return array
	 * @since 0.1.0
	 */
	public function column_sort( $vars ) {
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
	 * @internal Hook for actions `quick_edit_custom_box`.
	 * @param string $column_name
	 * @param string $post_type
	 * @since 0.2.0
	 * @todo Add nonce field!
	 */
	public function quick_edit( $column_name, $post_type ) {
		if ( $column_name != self::SLUG || $post_type != Odwpp_Project_Post_Type::SLUG ) {
			return;
		}

		$nonce = wp_create_nonce( self::NONCE );
		$html  = '';
		$html .= '<fieldset class="inline-edit-col-left">';
		$html .= '<div class="inline-edit-group">';
		$html .= '<input type="hidden" name="' . Odwpp_Project_Status_Metabox::NONCE . '" value="' . $nonce . '">';
		$html .= '<label>';
		$html .= '<span class="title"><abbr title="' . __( 'Systémový název projektu (tzv. `Slug`).', ODWPP_SLUG ) . '">' . __( 'Sys. název:', ODWPP_SLUG ) . '</abbr></span>';
		$html .= '<span class="input-text-wrap">';
		$html .= '<input class="regular-text" id="odwpp-project_slug" name="project_slug" placeholder="' . __( 'odwp-projects', ODWPP_SLUG ) . '" type="url" value="">';
		$html .= '</span>';
		$html .= '</label>';
		$html .= '</div>';
		$html .= '</fieldset>';

		/**
		 * Filter for project slug quick edit box.
		 *
		 * @param string $output Rendered HTML.
		 * @since 0.2.0
		 */
		$output = apply_filters( self::SLUG . '_quickedit', $html );
		echo $output;
	}

	/**
	 * @internal Hook for `save_post` action (quickedit in list table).
	 * @param integer $post_id
	 * @param WP_Post $post
	 * @since 0.2.0
	 */
	public function quick_edit_save_post( $post_id, $post ) {
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( $post->post_type != Odwpp_Project_Post_Type::SLUG ) {
			return;
		}

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		$value = filter_input( INPUT_POST, 'project_slug' );

		if ( ! empty( $value ) ) {
			update_post_meta( $post_id, self::SLUG, $value );
		}
	}

	/**
	 * @internal Hook for `admin_print_scripts-edit.php` action.
	 * @since 0.2.0
	 */
	public function enqueue_edit_scripts() {
		wp_enqueue_script(
			ODWPP_SLUG . '-admin-edit-project_slug',
			plugins_url( 'assets/js/admin-slug_quickedit.js', ODWPP_FILE ),
			['jquery', 'inline-edit-post'],
			'',
			true
		);
	}
}

endif;

/**
 * @var Odwpp_Project_Slug_Metabox $odwpp_project_slug_metabox
 */
$odwpp_project_slug_metabox = new Odwpp_Project_Slug_Metabox();
