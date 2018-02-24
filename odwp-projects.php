<?php
/**
 * Plugin Name: Freelance projects
 * Plugin URI: https://github.com/ondrejd/odwp-projects
 * Description: Plugin for managing and publishing projects on your WordPress sites. It was created to fit my requirements as a freelancer developer but I tried to make it usable for more people.
 * Version: 0.2.1
 * Author: Ondřej Doněk
 * Author URI: http://ondrejd.com/
 * Requires at least: 4.8
 * Tested up to: 4.8.3
 *
 * Text Domain: odwpp
 * Domain Path: /languages/
 *
 * @author Ondřej Doněk, <ondrejd@gmail.com>
 * @license https://www.mozilla.org/MPL/2.0/ Mozilla Public License 2.0
 * @link https://github.com/ondrejd/odwp-projects for the canonical source repository
 * @package odwp-projects
 * @since 1.0
 */

/**
 * This file is just a bootstrap. It checks if requirements of plugins
 * are met and accordingly either allow activating the plugin or stops
 * the activation process.
 *
 * Requirements can be specified either for PHP interperter or for
 * the WordPress self. In both cases you can specify minimal required
 * version and required extensions/plugins.
 *
 * If you are using copy of original file in your plugin you should change
 * prefix "odwpp" and name "odwp-projects" to your own values.
 *
 * To set the requirements go down to line 200 and define array that
 * is used as a parameter for `odwpp_check_requirements` function.
 */

if( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Some constants
defined( 'ODWPP_SLUG' )     || define( 'ODWPP_SLUG', 'odwpp' );
defined( 'ODWPP_NAME' )     || define( 'ODWPP_NAME', 'odwp-projects' );
defined( 'ODWPP_PATH' )     || define( 'ODWPP_PATH', dirname( __FILE__ ) . '/' );
defined( 'ODWPP_FILE' )     || define( 'ODWPP_FILE', __FILE__ );
defined( 'ODWPP_VERSION' )  || define( 'ODWPP_VERSION', '0.1.0' );


if( ! function_exists( 'odwpp_check_requirements' ) ) :
    /**
     * Checks requirements of our plugin.
     * @global string $wp_version
     * @param array $requirements
     * @return array
     * @since 0.1.0
     */
    function odwpp_check_requirements( array $requirements ) {
        global $wp_version;

        // Initialize locales
        load_plugin_textdomain( ODWPP_SLUG, false, ODWPP_NAME . '/languages' );

        /**
         * @var array Hold requirement errors
         */
        $errors = [];

        // Check PHP version
        if( ! empty( $requirements['php']['version'] ) ) {
            if( version_compare( phpversion(), $requirements['php']['version'], '<' ) ) {
                $errors[] = sprintf(
                        __( 'PHP nesplňuje nároky pluginu na minimální verzi (vyžadována nejméně <b>%s</b>)!', ODWPP_SLUG ),
                        $requirements['php']['version']
                );
            }
        }

        // Check PHP extensions
        if( count( $requirements['php']['extensions'] ) > 0 ) {
            foreach( $requirements['php']['extensions'] as $req_ext ) {
                if( ! extension_loaded( $req_ext ) ) {
                    $errors[] = sprintf(
                            __( 'Je vyžadováno rozšíření PHP <b>%s</b>, to ale není nainstalováno!', ODWPP_SLUG ),
                            $req_ext
                    );
                }
            }
        }

        // Check WP version
        if( ! empty( $requirements['wp']['version'] ) ) {
            if( version_compare( $wp_version, $requirements['wp']['version'], '<' ) ) {
                $errors[] = sprintf(
                        __( 'Plugin vyžaduje vyšší verzi platformy <b>WordPress</b> (minimálně <b>%s</b>)!', ODWPP_SLUG ),
                        $requirements['wp']['version']
                );
            }
        }

        // Check WP plugins
        if( count( $requirements['wp']['plugins'] ) > 0 ) {
            $active_plugins = (array) get_option( 'active_plugins', [] );
            foreach( $requirements['wp']['plugins'] as $req_plugin ) {
                if( ! in_array( $req_plugin, $active_plugins ) ) {
                    $errors[] = sprintf(
                            __( 'Je vyžadován plugin <b>%s</b>, ten ale není nainstalován!', ODWPP_SLUG ),
                            $req_plugin
                    );
                }
            }
        }

        return $errors;
    }
endif;


if( ! function_exists( 'odwpp_deactivate_raw' ) ) :
    /**
     * Deactivate plugin by the raw way (it updates directly WP options).
     * @return void
     * @since 1.0.0
     */
    function odwpp_deactivate_raw() {
        $active_plugins = get_option( 'active_plugins' );
        $out = [];
        foreach( $active_plugins as $key => $val ) {
            if( $val != ODWPP_NAME . '/' . ODWPP_NAME . '.php' ) {
                $out[$key] = $val;
            }
        }
        update_option( 'active_plugins', $out );
    }
endif;


/**
 * Errors from the requirements check
 * @var array $odwpp_errs
 */
$odwpp_errs = odwpp_check_requirements( [
    'php' => [
        // Enter minimum PHP version you needs
        'version' => '7.0',
        // Enter extensions that your plugin needs
        'extensions' => [
            //'gd',
        ],
    ],
    'wp' => [
        // Enter minimum WP version you need
        'version' => '4.8',
        // Enter WP plugins that your plugin needs
        'plugins' => [
            //'woocommerce/woocommerce.php',
        ],
    ],
] );


if( ! function_exists( 'odwpp_print_requirements_errors' ) ) :
    /**
     * @internal Prints requirements errors.
     * @global array $odwpp_errs
     * @since 0.2.0
     */
    function odwpp_print_requirements_errors() {
        global $odwpp_errs;

        $err_head = sprintf( __( '<b>%s</b>: ', ODWPP_SLUG ), ODWPP_NAME );

        foreach( $odwpp_errs as $err ) {
            $msg = $err_head . $err;
            printf( '<div class="notice notice-error is-dismissible"><p>%s</p></div>', $msg );
        }
    }
endif;


// Check if requirements are met or not
if( count( $odwpp_errs ) > 0 ) {
    // Requirements are not met - print errors if we are in WP admin
    odwpp_deactivate_raw();

    if( is_admin() ) {
        add_action( 'admin_notices', 'odwpp_print_requirements_errors' );
    }
} else {
    // Requirements are met - initialize the plugin
    include( ODWPP_PATH . 'includes/class-odwpp_plugin.php' );

	/**
	 * @since 0.1.0
	 * @var Odwpp_Plugin $odwpp_plugin
	 */
	$odwpp_plugin = new Odwpp_Plugin();
}
