<?php
/**
 * Custom post type "Project".
 *
 * @author Ondřej Doněk, <ondrejd@gmail.com>
 * @license https://www.mozilla.org/MPL/2.0/ Mozilla Public License 2.0
 * @link https://github.com/ondrejd/odwp-projects for the canonical source repository
 * @package odwp-projects
 * @since 1.0
 */

if ( ! class_exists( 'Odwpp_Project_Post_Type' ) ):

/**
 * Custom post type "Project".
 * @since 1.0
 */
class Odwpp_Project_Post_Type {
	/**
	 * @var string
	 * @since 1.0
	 */
	const SLUG = 'project';

	/**
	 * Holds instance of self (part of singleton implementation).
	 * @access private
	 * @since 1.0
	 * @var Odwpp_Project_Post_Type $instance
	 */
	private static $instance;

	/**
	 * Returns instance of self (part of singleton implementation).
	 * @return Odwpp_Project_Post_Type
	 * @since 1.0
	 */
	public static function get_instance() {
		if ( ! ( self::$instance instanceof self ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Constructor.
	 * @access private
	 * @since 1.0
	 * @uses apply_filters()
	 * @uses register_post_type()
	 */
	private function __construct() {
		$labels = array(
			'name' => _x( 'Projekty', 'post type general name', Odwp_Projects_Plugin::SLUG ),
			'singular_name' => _x( 'Vytvořit projekt', 'post type singular name', Odwp_Projects_Plugin::SLUG ),
			'add_new' => __( 'Nový projekt', Odwp_Projects_Plugin::SLUG ),
			'add_new_item' => __( 'Vytvořit nový projekt', Odwp_Projects_Plugin::SLUG ),
			'edit_item' => __( 'Upravit projekt', Odwp_Projects_Plugin::SLUG ),
			'new_item' => __( 'Nový projekt', Odwp_Projects_Plugin::SLUG ),
			'view_item' => __( 'Zobrazit projekt', Odwp_Projects_Plugin::SLUG ),
			'search_items' => __( 'Prohledat projekty', Odwp_Projects_Plugin::SLUG ),
			'not_found' => __( 'Žádné projekty nebyly nalezeny.', Odwp_Projects_Plugin::SLUG ),
			'not_found_in_trash' => __( 'Žádné projekty nebyly v koši nalezeny.', Odwp_Projects_Plugin::SLUG ),
			'all_items' => __( 'Všechny projekty', Odwp_Projects_Plugin::SLUG ),
			'archives' => __( 'Archív projektů', Odwp_Projects_Plugin::SLUG ),
			'menu_name' => __( 'Projekty', Odwp_Projects_Plugin::SLUG ),
			'parent_item_colon' => __( 'Nadřazený projekt:', Odwp_Projects_Plugin::SLUG ),
		);

		$args = array(
			'labels' => $labels,
			'hierarchical' => true,
			'description' => __( 'Projekty...', Odwp_Projects_Plugin::SLUG ),
			'supports' => array( 'title', 'editor', 'thumbnail', 'comments', 'revisions'/*, 'custom-fields'*/, 'page-attributes' ),
			'taxonomies' => array( 'post_tag' ),
			'show_ui' => true,
			'show_in_menu' => true,
			'menu_position' => 5,
			'menu_icon' => 'dashicons-clock',
			'show_in_nav_menus' => true,
			'publicly_queryable' => true,
			'exclude_from_search' => false,
			'query_var' => true,
			'can_export' => true,
	        'public' => true,
	        'has_archive' => true,
	        'capability_type' => 'post',
		);

		/**
		 * Filter "Project" post type arguments.
		 *
		 * @since 0.1.0
		 *
		 * @param array $arguments "Project" post type arguments.
		 */
		$args = apply_filters( 'odwpp_' . self::SLUG . '_post_type_arguments', $args );

		register_post_type( self::SLUG, $args );
	}
}

endif; // Odwpp_Project_Post_Type

// Initialize custom post type
Odwpp_Project_Post_Type::get_instance();
