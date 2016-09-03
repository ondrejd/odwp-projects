<?php
/**
 * Screen prototype.
 *
 * @author Ondřej Doněk, <ondrejd@gmail.com>
 * @license https://www.mozilla.org/MPL/2.0/ Mozilla Public License 2.0
 * @link https://github.com/ondrejd/odwp-projects for the canonical source repository
 * @package odwp-projects
 * @since 1.0
 */

if ( ! class_exists( 'Odwpp_Screen_Prototype' ) ):

/**
 * Screen class prototype.
 * @since 1.0
 */
class Odwpp_Screen_Prototype {
	/**
	 * @since 1.0
	 * @var string $slug
	 */
	protected $slug;

	/**
	 * @since 1.0
	 * @var string $page_title
	 */
	protected $page_title;

	/**
	 * @since 1.0
	 * @var string $menu_title
	 */
	protected $menu_title;

	/**
	 * @since 1.0
	 * @var array
	 */
	protected $help_tabs = array();

	/**
	 * @since 1.0
	 * @var array
	 */
	protected $help_sidebars = array();

	/**
	 * @internal
	 * @since 1.0
	 * @var string $hookname Name of the admin menu page hook.
	 */
	protected $hookname;

	/**
	 * @access private
	 * @since 1.0
	 * @var WP_Screen $screen
	 */
	private $screen;

	/**
	 * @param WP_Screen $screen Optional.
	 * @since 1.0
	 */
	public function __construct( WP_Screen $screen = null ) {
		$this->screen = $screen;
		/*$this->help_tabs[] = array(
			'id'      => $this->slug . '-options_help_tab',
			'title'   => __( 'Screen options', Odwp_Projects_Plugin::SLUG ),
			'content' => __( '<h4>Lorem Ipsum</h4><p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Praesent urna sapien, vulputate at suscipit in, tristique et nisi. Ut quis vulputate arcu. Phasellus metus neque, lacinia vel luctus quis, sagittis et est. Aenean tincidunt purus quis lectus molestie auctor. Mauris aliquam risus in risus tincidunt vulputate. Proin nec vulputate neque, sit amet pretium dui. Maecenas a dolor dapibus, iaculis justo sed, vehicula purus. Sed vulputate purus nec lorem suscipit, rhoncus dapibus sapien pellentesque. Fusce commodo leo sed tincidunt varius. Nulla imperdiet ligula at lectus posuere, vitae malesuada purus euismod.</p>', Odwp_Projects_Plugin::SLUG ),
		);*/
	}

	/**
	 * @since 1.0
	 * @return string
	 */
	public function get_slug() {
		return $this->slug;
	}

	/**
	 * @since 1.0
	 * @return string
	 */
	public function get_page_title() {
		return $this->page_title;
	}

	/**
	 * @since 1.0
	 * @return string
	 */
	public function get_menu_title() {
		return $this->menu_title;
	}

	/**
	 * @since 1.0
	 * @return WP_Screen
	 * @uses get_current_screen()
	 */
	public function get_screen() {
		if ( ! ( $this->screen instanceof WP_Screen ) ) {
			$this->screen = get_current_screen();
		}

		return $this->screen;
	}

	/**
	 * Returns current screen options.
	 * @return array
	 * @since 1.0
	 * @uses get_current_user_id()
	 * @uses get_user_meta()
	 * @uses WP_Screen::get_option()
	 */
	public function get_screen_options() {
		$screen = $this->get_screen();
		$user = get_current_user_id();

		$display_description = get_user_meta( $user, $this->slug . '-display_description', true );
		if ( strlen( $display_description ) == 0 ) {
			$display_description = $screen->get_option( $this->slug . '-display_description', 'default' );
		}

		return array(
			'display_description' => (bool) $display_description,
		);

		return $options;
	}

	/**
	 * @internal
	 * @param string $key
	 * @param mixed $value
	 * @since 1.0
	 * @uses update_option()
	 */
	protected function update_option( $key, $value ) {
		$options = Odwp_Projects_Plugin::get_options();
		$need_update = false;

		if ( ! array_key_exists( $key, $options ) ) {
			$need_update = true;
		}

		if ( ! $need_update && $options[$key] != $value ) {
			$need_update = true;
		}

		if ( $need_update === true ) {
			$options[$key] = $value;
			update_option( $key, $value );
		}
	}

	/**
	 * Action for `init` hook (see {@see Odwp_Projects_Plugin::init} for more details).
	 * @since 1.0
	 * @uses add_filter()
	 */
	public function init() {
		add_action( 'admin_init', array( $this, 'save_screen_options' ) );
	}

	/**
	 * Action for `admin_init` hook (see {@see Odwp_Projects_Plugin::admin_init} for more details).
	 * @since 1.0
	 */
	public function admin_init() {
		// ...
	}

	/**
	 * Action for `init` hook (see {@see Odwp_Projects_Plugin::admin_enqueue_scripts} for more details).
	 * @since 1.0
	 * @uses plugins_url()
	 * @uses wp_register_script()
	 * @uses wp_enqueue_script()
	 */
	public function admin_enqueue_scripts() {
		// ...
	}

	/**
	 * Action for `admin_head` hook (see {@see Odwp_Projects_Plugin::admin_head} for more details).
	 * @since 1.0
	 */
	public function admin_head() {
		// ...
	}

	/**
	 * Action for `admin_menu` hook (see {@see Odwp_Projects_Plugin::admin_menu} for more details).
	 * @since 1.0
	 * @uses add_submenu_page()
	 * @uses add_action()
	 */
	public function admin_menu() {
		/*$this->hookname = add_submenu_page(
			'edit.php?post_type=wizard',
			$this->page_title,
			$this->menu_title,
			'manage_options',
			$this->slug,
			array( $this, 'render' )
		);

		add_action( 'load-' . $this->hookname, array( $this, 'screen_load' ) );*/
	}

	/**
	 * Action for `load-{$hookname}` hook (see {@see Odwpp_Screen_Prototype::admin_menu} for more details).
	 *
	 * Creates screen help and add filter for screen options.
	 *
	 * @since 1.0
	 * @uses add_filter()
	 * @uses WP_Screen::add_help_tab()
	 * @uses WP_Screen::set_help_sidebar()
	 * @uses WP_Screen::add_option()
	 */
	public function screen_load() {
		$screen = $this->get_screen();

		// Screen help
		foreach ( $this->help_tabs as $tab ) {
			$screen->add_help_tab( $tab );
		}

		foreach ( $this->help_sidebars as $sidebar ) {
			$screen->set_help_sidebar( $sidebar );
		}

		// Screen options
		add_filter( 'screen_layout_columns', array( $this, 'screen_options' ) );

		$screen->add_option( $this->slug . '-display_description', array(
			'label' => __( 'Display detail descriptions?', Odwp_Projects_Plugin::SLUG ),
			'default' => 1,
			'option' => $this->slug . '-display_description'
		) );
	}

	/**
	 * Renders screen options form. Handler for `screen_layout_columns` filter
	 * (see {@see Odwpp_Screen_Prototype::screen_load}).
	 * @since 1.0
	 * @uses plugin_dir_path()
	 * @uses update_option()
	 * @uses apply_filters()
	 */
	public function screen_options() {
		// These are used in the template:
		$slug = $this->slug;
		$screen = $this->get_screen();
		extract( $this->get_screen_options() );
		$templates = $this->get_source_templates();

		ob_start();
		include( Odwp_Projects_Plugin::plugin_path( 'partials/screen-options_form.php' ) );
		$output = ob_get_clean();

		/**
		 * Filter for screen options form.
		 *
		 * @since 1.0
		 *
		 * @param string $output Rendered HTML.
		 */
		$output = apply_filters( "odwpp_{$this->slug}_screen_options_form", $output );
		echo $output;
	}

	/**
	 * Action for `admin_init` hook (see {@see Odwpp_Screen_Prototype::init} for more details).
	 *
	 * Saves screen options.
	 *
	 * @since 1.0
	 * @uses get_current_user_id()
	 * @uses wp_verify_nonce()
	 * @uses update_user_meta()
	 */
	public function save_screen_options() {
		$user = get_current_user_id();

		if (
			filter_input( INPUT_POST, $this->slug . '-submit' ) &&
			( bool ) wp_verify_nonce( filter_input( INPUT_POST, $this->slug . '-nonce' ) ) === true
		) {
			$display_description = ( string ) filter_input( INPUT_POST, $this->slug . '-checkbox1' );
			$display_description = ( strtolower( $display_description ) == 'on' ) ? 1 : 0;
			update_user_meta( $user, $this->slug . '-display_description', $display_description );
		}
	}

	/**
	 * Render page self.
	 * @param array $args (Optional.) Arguments for rendered template.
	 * @since 1.0
	 * @uses apply_filters()
	 */
	public function render( $args = array() ) {
		// These are used in the template:
		$slug = $this->slug;
		$screen = $this->get_screen();
		$wizard = $this;
		extract( $this->get_screen_options() );
		extract( $args );

		ob_start();
		include( Odwp_Projects_Plugin::plugin_path( 'partials/screen-' . $this->slug . '.phtml' ) );
		$output = ob_get_clean();

		/**
		 * Filter for the screen.
		 *
		 * @since 1.0
		 *
		 * @param string $output Rendered HTML.
		 */
		$output = apply_filters( "odwpp_{$this->slug}_form", $output );
		echo $output;
	}
}

endif;
