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
	 * Constructor.
	 * @since 0.2.0
	 */
	public function __construct() {
		if ( ! is_admin() ) {
			return;
		}

		$post_type = Odwpp_Project_Post_Type::SLUG;

		// Add/Edit page - add meta box
		add_action( 'load-post.php', [$this, 'init'] );
		add_action( 'load-post-new.php', [$this, 'init'] );
		// Save meta box value
		add_action( 'save_post', [$this, 'save'], 10, 3 );
		// List Table: add column (head and body)
		add_filter( 'manage_' . $post_type . '_posts_columns', [$this, 'column_head'] );
		add_action( 'manage_' . $post_type . '_posts_custom_column' , [$this, 'column_body'], 10, 2 );
		// List Table: make our column sortable
		add_filter( 'manage_edit-' . $post_type . '_sortable_columns', [$this, 'column_sortable'] );
		add_action( 'load-edit.php', [$this, 'column_sort_request'] );
		// List Table: quick edit
		add_action( 'quick_edit_custom_box',  [$this, 'quick_edit'], 10, 2 );
		add_action( 'bulk_edit_custom_box  ', [$this, 'quick_edit'], 10, 2 );
		add_action( 'save_post', [$this, 'quick_edit_save_post'], 10, 2 );
		add_action( 'admin_print_scripts-edit.php', [$this, 'enqueue_edit_scripts'] );
		// List Table: filtering
		add_action( 'restrict_manage_posts', [$this, 'filter_projects'], 10 );
		add_action( 'parse_query', [$this, 'filter_request_query'], 10 );	
	}

	/**
	 * Returns available project statuses.
	 * @return array
	 * @since 0.2.0
	 */
	protected function get_statuses() {
		return [
			'active'    => __( 'Aktivní', ODWPP_SLUG ),
			'nonactive' => __( 'Neaktivní', ODWPP_SLUG ),
			'finished'  => __( 'Dokončený', ODWPP_SLUG ),
			'cancelled' => __( 'Zrušený', ODWPP_SLUG ),
		];
	}

	/**
	 * Creates HTML with select box with project statuses.
	 * @param null|string $value
	 * @param boolean $add_empty (Optional.)
	 * @param string $id (Optional.)
	 * @param string $name (Optional.)
	 * @return string
	 * @since 0.2.0
	 * @since 0.2.1 Parameter `$add_empty` added.
	 */
	protected function create_select( $value = null, $add_empty = false, $id = ODWPP_SLUG . '-project_status', $name = 'project_status' ) {
		$statuses = $this->get_statuses();
		$html = '<select id="' . $id . '" name="' . $name . '" value="' . ( empty( $value ) ? '' : $value ) . '">';

		if ($add_empty == true) {
			$html .= '<option value="0" ' . selected( 0, empty( $value ) ? 0 : $value, false ) . '>' . __( '&mdash; Stav projektu &mdash;', ODWPP_SLUG ) . '</option>';
		}

		foreach( $statuses as $key => $label ) {
			$html .= '<option value="' . $key . '" ' . selected( $key, $value, false ) . '>' . $label . '</option>';
		}

		$html .= '</select>';
		return $html;
	}

	/**
	 * @internal Hook for `load-post.php` and `load-post-new.php`. Initializes meta box.
	 * @since 0.1.0
	 * @uses add_meta_box
	 */
	public function init() {
		add_meta_box(
			self::SLUG,
			__( 'Stav projektu', ODWPP_SLUG ),
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

		// Create output HTML
		$html = '<div class="project_status_metabox">';
		$html .= '<input type="hidden" name="' . Odwpp_Project_Status_Metabox::NONCE . '" value="' . $nonce . '">';
		$html .= '<label for="odwpp-project_status" class="screen-reader-text">' . __( 'Stav projektu:', ODWPP_SLUG ) . '</label>';
		$html .= $this->create_select( $value );
		$html .= '</div>';

		/**
		 * Filter for project status meta box.
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
	 * @return integer
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
	public function column_head( $columns ) {
		$columns[self::SLUG] = __( 'Stav projektu', ODWPP_SLUG );
		return $columns;
	}

	/**
	 * @internal Hook for `manage_project_posts_custom_column` action.
	 * @param string $column
	 * @param integer $post_id
	 * @since 0.1.0
	 * @uses get_post_meta
	 */
	public function column_body( $column, $post_id ) {
		if ( $column != self::SLUG ) {
			return;
		}

		$status = get_post_meta( $post_id , self::SLUG , true );

        if ( empty( $status ) ) {
            return;
        }

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
	 * @internal Hook for actions `quick_edit_custom_box` and `bulk_edit_custom_box`.
	 * @param string $column_name
	 * @param string $post_type
	 * @since 0.2.0
	 * @todo Finish NONCE implementation!
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
		$html .= '<span class="title"><abbr title="' . __( 'Aktuální stav projektu', ODWPP_SLUG ) . '">' . __( 'Stav:', ODWPP_SLUG ) . '</abbr></span>';
		$html .= '<span class="input-text-wrap">';
		$html .= $this->create_select( null );
		$html .= '</span>';
		$html .= '</div>';
		$html .= '</fieldset>';

		/**
		 * Filter for project status quick edit box.
		 *
		 * @param string $output Rendered HTML.
		 * @since 0.2.0
		 */
		$output = apply_filters( self::SLUG . '_quickedit', $html );
		echo $output;
	}

	/**
	 * @internal Hook for `save_post` action (quickedit/bulkactions in list table).
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

		$value = filter_input( INPUT_POST, 'project_status' );
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
			ODWPP_SLUG . '-admin-edit-project_status',
			plugins_url( 'assets/js/admin-status_quickedit.js', ODWPP_FILE ),
			['jquery', 'inline-edit-post'],
			'',
			true
		);
	}

	/**
	 * @internal Hook for `restrict_manage_posts` action.
	 * @param string $post_type
	 * @since 0.2.0
	 */
	public function filter_projects( $post_type ) {
		if ( Odwpp_Project_Post_Type::SLUG != $post_type ) {
			return;
		}

		$status = filter_input( INPUT_GET, 'project_status' );
		if ( empty( $status ) ) {
			$status = 0;
		}

		$html = '<label class="screen-reader-text" for="odwpp-projects_status">' . __( 'Filtrovat podle stavu projektu', ODWPP_SLUG ) . '</label>' . $this->create_select( $status, true );


		/**
		 * Filter for project status filter.
		 *
		 * @param string $output Rendered HTML.
		 * @param string $status
		 * @since 0.2.0
		 */
		$output = apply_filters( self::SLUG . '_filter', $html, $status );
		echo $output;
	}

	/**
	 * @internal Hook for `parse_query` action.
	 * @global string $pagenow
	 * @param WP_Query $query
	 * @since 0.2.0
	 */
	public function filter_request_query( $query ) {
		global $pagenow;

		$post_type      = filter_input( INPUT_GET, 'post_type' );
		$project_status = filter_input( INPUT_GET, 'project_status' );

		if ( Odwpp_Project_Post_Type::SLUG == $post_type &&
		     'edit.php' === $pagenow && ! empty( $project_status ) ) {

			$query->query_vars['meta_key']     = self::SLUG;
			$query->query_vars['meta_value']   = $project_status;
			$query->query_vars['meta_compare'] = '=';
		}
	}
}

endif;

/**
 * @var Odwpp_Project_Status_Metabox $odwpp_project_status_metabox
 */
$odwpp_project_status_metabox = new Odwpp_Project_Status_Metabox();
