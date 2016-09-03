<?php
/**
 * Plugin Name: odwp-projects
 * Plugin URI: https://github.com/ondrejd/odwp-projects
 * Description: Plugin for managing and publishing projects on-line.
 * Version: 1.0
 * Author: Ondřej Doněk
 * Author URI: http://ondrejd.info/
 * Requires at least: 4.6
 * Tested up to: 4.6
 *
 * Text Domain: odwp-projects
 * Domain Path: /languages/
 *
 * @author Ondřej Doněk, <ondrejd@gmail.com>
 * @license https://www.mozilla.org/MPL/2.0/ Mozilla Public License 2.0
 * @link https://github.com/ondrejd/odwp-projects for the canonical source repository
 * @package odwp-projects
 * @since 1.0
 */

if ( ! class_exists( 'Odwp_Projects_Plugin' ) ):

/**
 * Main class of the plugin.
 * @since 1.0
 */
class Odwp_Projects_Plugin {
	const SLUG = 'odwp-projects';
	const VERSION = '1.0';

	/**
	 * Screens added.
	 * @since 1.0
	 * @var array
	 */
	private static $screens = array();

	/**
	 * Default options of the plugin.
	 * @since 1.0
	 * @var array
	 */
	private static $default_options = array();

	/**
	 * Holds plugin's path.
	 * @since 1.0
	 * @var string
	 */
	private static $_plugin_path;

	/**
	 * Set up hooks.
	 * @since 1.0
	 * @uses add_action()
	 * @uses is_admin()
	 */
	public function __construct() {
		add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );
		add_action( 'init', array( $this, 'init' ) );

		if ( is_admin() ) {
			add_action( 'admin_init', array( $this, 'admin_init' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
			add_action( 'admin_head', array( $this, 'admin_head' ) );
			add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		}
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - {@see Odwpp_Screen_Prototype}. ...
	 * - {@see Odwpp_Project_Post_Type}. Defines custom post type.
	 * - {@see Odwpp_Project_Repository_Metabox}. ...
	 * - {@see Odwpp_Project_Links_Metabox}. ...
	 * - {@see Odwpp_Project_Slug_Metabox}. ...
	 *
	 * @access private
	 * @since 1.0
	 */
	private function load_dependencies() {
		/**
		 * @var array $files Array with paths of all required source files.
		 */
		$files = array(
			$this->plugin_path( 'includes/class-odwpp_screen_prototype.php' ),
			$this->plugin_path( 'includes/class-odwpp_project_post_type.php' ),
			$this->plugin_path( 'includes/class-odwpp_project_repository_metabox.php' ),
			$this->plugin_path( 'includes/class-odwpp_project_links_metabox.php' ),
			$this->plugin_path( 'includes/class-odwpp_project_slug_metabox.php' ),
		);

		// Load all files (it throws error when file failed to be included but
		// that is intentional).
		foreach ( $files as $file ) {
			require_once $file;
		}
	}

	/**
	 * On all screens call method with given name.
	 *
	 * Used for calling hook's actions of the existing screens.
	 * See {@see Odwp_Projects_Plugin::admin_init} for an example how is used.
	 *
	 * If method doesn't exist in the screen object it means that screen
	 * do not provide action for the hook.
	 *
	 * @access private
	 * @param string $method
	 * @since 0.1.0
	 */
	private function screens_call_method( $method ) {
		foreach ( self::$screens as $slug => $screen ) {
			if ( method_exists( $screen, $method) ) {
				call_user_func( array( $screen, $method ) );
			}
		}
	}

	/**
	 * Load text domain for translations.
	 * @since 0.1.0
	 * @uses load_plugin_textdomain()
	 */
	public function load_textdomain() {
		load_plugin_textdomain( self::SLUG, false, self::SLUG . '/languages' );
	}

	/**
	 * Initialize plugin.
	 * @since 1.0
	 */
	public function init() {
		// Ensure that options are initialized
		self::get_options();

		// Load dependencies
		$this->load_dependencies();

		// Call action for `init` hook on all screens.
		$this->screens_call_method( 'init' );
	}

	/**
	 * Action for `admin_init` hook.
	 * @since 1.0
	 */
	public function admin_init() {
		// Call action for `admin_init` hook on all screens.
		$this->screens_call_method( 'admin_init' );
	}

	/**
	 * Action for `admin_enqueue_scripts` hook.
	 * @since 1.0
	 */
	public function admin_enqueue_scripts() {
		wp_enqueue_style( 'odwpshp-admin-style', plugins_url( 'css/admin.css', __FILE__ ), false );
		wp_enqueue_script( 'odwpshp-admin-js', plugins_url( 'js/admin.js', __FILE__ ), false );

		// Call action for `admin_enqueue_scripts` hook on all screens.
		$this->screens_call_method( 'admin_enqueue_scripts' );
	}

	/**
	 * Action for `admin_head` hook.
	 * @since 1.0
	 */
	public function admin_head() {
		// Call action for `admin_head` hook on all screens.
		$this->screens_call_method( 'admin_head' );
	}

	/**
	 * Action for `admin_menu` hook.
	 * @since 1.0
	 */
	public function admin_menu() {
		// Call action for `admin_menu` hook on all screens.
		$this->screens_call_method( 'admin_menu' );
	}

	/**
	 * Returns plugin's options
	 * @return array
	 * @since 1.0
	 * @static
	 * @uses get_option()
	 * @uses update_option()
	 */
	public static function get_options() {
		$options = get_option( self::SLUG . '-options' );
		$need_update = false;

		if ( !is_array( $options) ) {
			$need_update = true;
			$options = array();
		}

		foreach ( self::$default_options as $key => $value ) {
			if ( !array_key_exists( $key, $options ) ) {
				$options[$key] = $value;
				$need_update = true;
			}
		}

		if ( !array_key_exists( 'latest_used_version', $options ) ) {
			$options['latest_used_version'] = self::VERSION;
			$need_update = true;
		}

		if ( $need_update === true ) {
			update_option( self::SLUG . '-options', $options );
		}

		return $options;
	}

	/**
	 * Returns value of option with given key. If key doesn't exist
	 * returns empty string or NULL if `$null_if_not_exist` is set on TRUE.
	 * @param string $key
	 * @param boolean $null_if_not_exist Optional. Default TRUE.
	 * @return mixed Returns empty string if option with given key was not found.
	 * @since 1.0
	 * @static
	 * @uses get_option()
	 */
	public static function get_option( $key, $null_if_not_exist = false ) {
		$options = get_option( self::SLUG . '-options' );

		if ( array_key_exists( $key, $options ) ) {
			return $options[$key];
		}

		if ( $null_if_not_exist === true ) {
			return NULL;
		}

		return '';
	}

	/**
	 * Add/register new screen. Is called from the end of screens source files.
	 * @param Odwpp_Screen_Prototype $creen
	 * @since 0.1.0
	 * @static
	 */
	public static function add_screen( Odwpp_Screen_Prototype $screen ) {
		self::$screens[$screen->get_slug()] = $screen;
	}

	/**
	 * Returns screen with given slug (`NULL` if screen wasn't found).
	 * @param string $slug
	 * @return Odwpp_Screen_Prototype
	 * @since 0.1.0
	 * @static
	 */
	public static function get_screen( $slug ) {
		if ( array_key_exists( $slug, self::$screens ) ) {
			return self::$screens[$slug];
		}

		return null;
	}

	/**
	 * Returns path to file within plugin's directory.
	 * @param string $file
	 * @return string
	 * @since 0.1.0
	 * @static
	 */
	public static function plugin_path( $file ) {
		if ( ! isset( self::$_plugin_path ) ) {
			self::$_plugin_path = plugin_dir_path( __FILE__ );
		}

		return self::$_plugin_path . $file;
	}
} // End of Odwp_Projects_Plugin

endif;

/**
 * @var Odwp_Projects_Plugin $odwp_hodiny
 */
$odwp_hodiny = new Odwp_Projects_Plugin();
