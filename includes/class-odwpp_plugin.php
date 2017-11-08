<?php
/**
 * @author Ondřej Doněk, <ondrejd@gmail.com>
 * @license https://www.mozilla.org/MPL/2.0/ Mozilla Public License 2.0
 * @link https://github.com/ondrejd/odwp-projects for the canonical source repository
 * @package odwp-projects
 * @since 0.1.0
 */

if ( ! class_exists( 'Odwpp_Plugin' ) ):

/**
 * Main class of the plugin.
 * @since 0.1.0
 */
class Odwpp_Plugin {
	const SLUG = ODWPP_SLUG;
	const VERSION = ODWPP_VERSION;

	/**
	 * Default options of the plugin.
	 * @since 0.1.0
	 * @var array
	 */
	private static $default_options = array();

	/**
	 * Set up hooks.
	 * @since 0.1.0
	 * @uses add_action()
	 * @uses is_admin()
	 */
	public function __construct() {
		add_action( 'init', [$this, 'load_textdomain'], 90 );
		add_action( 'init', [$this, 'init'], 91 );

		if ( is_admin() ) {
			add_action( 'admin_enqueue_scripts', [$this, 'admin_enqueue_scripts'] );
		}
	}

	/**
	 * @access private
 	 * @internal Load the required dependencies for this plugin.
	 * @since 0.1.0
	 */
	private function load_dependencies() {
		Odwpp_Plugin::write_log( 'Odwpp_Plugin::load_dependencies' );

		/**
		 * @var array $files Array with paths of all required source files.
		 */
		$files = [
			ODWPP_PATH . 'includes/class-odwpp_project_post_type.php',
			ODWPP_PATH . 'includes/class-odwpp_project_repository_metabox.php',
			ODWPP_PATH . 'includes/class-odwpp_project_slug_metabox.php',
			ODWPP_PATH . 'includes/class-odwpp_project_status_metabox.php',
		];

		// Load all files
		foreach ( $files as $file ) {
			include( $file );
		}
	}

	/**
	 * Load text domain for translations.
	 * @since 0.1.0
	 * @uses load_plugin_textdomain()
	 */
	public function load_textdomain() {
		Odwpp_Plugin::write_log( 'Odwpp_Plugin::load_textdomain' );
		load_plugin_textdomain( ODWPP_SLUG, false, ODWPP_NAME . '/languages' );
	}

	/**
	 * Initialize plugin.
	 * @since 0.1.0
	 */
	public function init() {
		// Ensure that options are initialized
		self::get_options();

		// Load dependencies
		$this->load_dependencies();
	}

	/**
	 * Action for `admin_enqueue_scripts` hook.
	 * @since 0.1.0
	 */
	public function admin_enqueue_scripts() {
		wp_enqueue_style( 'odwpp-admin-style', plugins_url( 'assets/css/admin.css', ODWPP_FILE ), false );
		wp_enqueue_script( 'odwpp-admin-js', plugins_url( 'assets/js/admin.js', ODWPP_FILE ), false );
	}

	/**
	 * Returns plugin's options
	 * @return array
	 * @since 0.1.0
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
	 * @since 0.1.0
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
	 * Helper for writing to the `debug.log` file.
	 * @param mixed $log
	 * @return void
	 * @since 0.2.0
	 */
	public static function write_log( $log ) {
		if ( function_exists( 'odwpdl_write_log' ) ) {
			odwpdl_write_log( $log );
		}
	}
} // End of Odwpp_Plugin

endif;
