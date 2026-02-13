<?php
/**
 * Persian Font Main Class
 *
 * @package Persian_Font
 */

namespace PersianFont;

/**
 * Class Persian_Font
 */
final class Persian_Font extends \DediData\Singleton {

	/**
	 * Plugin URL
	 *
	 * @var string $plugin_url
	 */
	protected $plugin_url;

	/**
	 * Plugin Folder
	 *
	 * @var string $plugin_folder
	 */
	protected $plugin_folder;

	/**
	 * Plugin Name
	 *
	 * @var string $plugin_name
	 */
	protected $plugin_name;

	/**
	 * Plugin Version
	 *
	 * @var string $plugin_version
	 */
	protected $plugin_version;

	/**
	 * Plugin Slug
	 *
	 * @var string $plugin_slug
	 */
	protected $plugin_slug;

	/**
	 * Plugin File
	 *
	 * @var string $plugin_file
	 */
	protected $plugin_file;

	/**
	 * Constructor
	 *
	 * @param mixed $plugin_file Plugin File Name.
	 * @see https://developer.wordpress.org/reference/functions/register_activation_hook
	 * @see https://developer.wordpress.org/reference/functions/register_deactivation_hook
	 * @see https://developer.wordpress.org/reference/functions/register_uninstall_hook
	 * @SuppressWarnings(PHPMD.ElseExpression)
	 */
	protected function __construct( $plugin_file = null ) {
		$this->plugin_file = $plugin_file;
		$this->set_plugin_info();
		register_activation_hook( $plugin_file, array( $this, 'activate' ) );
		register_deactivation_hook( $plugin_file, array( $this, 'deactivate' ) );
		register_uninstall_hook( $plugin_file, self::class . '::uninstall' );
		if ( is_admin() ) {
			add_action( 'admin_enqueue_scripts', array( $this, 'load_admin_scripts' ), 11 );
			$this->admin();
		} else {
			add_action( 'wp_enqueue_scripts', array( $this, 'load_frontend_scripts' ), 11 );
			$this->run();
		}
	}

	/**
	 * The function is used to load frontend scripts and styles in a WordPress plugin, with support for
	 * RTL (right-to-left) languages.
	 *
	 * @return void
	 */
	public function load_frontend_scripts() {
		$current_locale = get_locale();
		if ( 'fa_IR' === $current_locale || 'fa_AF' === $current_locale ) {
			// Persian RTL
			// Persian (Afghanistan) RTL
			$option = get_option( $this->plugin_slug );
			if ( is_array( $option ) && array_key_exists( 'frontend-font', $option ) ) {
				$frontend_font = $option['frontend-font'];
				if ( true === $frontend_font || ! isset( $frontend_font ) ) {
					wp_register_style( $this->plugin_slug, $this->plugin_url . '/assets/public/css/style-rtl.css', array(), $this->plugin_version );
					wp_enqueue_style( $this->plugin_slug );
					// wp_enqueue_script( $this->plugin_slug , $this->plugin_url . 'assets/public/js/script.js', array(), $this->plugin_version, true );
				}
			}
			// Load Persian fonts and RTL optimized styles for editor
			add_editor_style( $this->plugin_url . 'assets/public/css/editor-rtl.css' );
		}

		/*
		if ( is_rtl() ) {
			wp_register_style( $this->plugin_slug . '-rtl', $this->plugin_url . '/assets/public/css/style.rtl.css', array(), $this->plugin_version );
			wp_enqueue_style( $this->plugin_slug . '-rtl' );
		} else {
			wp_register_style( $this->plugin_slug, $this->plugin_url . '/assets/public/css/style.css', array(), $this->plugin_version );
			wp_enqueue_style( $this->plugin_slug );
		}

		wp_register_script( $this->plugin_slug, $this->plugin_url . '/assets/public/js/script.js', array(), $this->plugin_version, true );
		wp_enqueue_script( $this->plugin_slug );
		*/
	}

	/**
	 * Styles for Admin
	 *
	 * @return void
	 */
	public function load_admin_scripts() {
		$current_locale = get_locale();
		if ( 'fa_IR' === $current_locale || 'fa_AF' === $current_locale ) {
			// Persian RTL
			// Persian (Afghanistan) RTL
			$option = get_option( $this->plugin_slug );
			if ( is_array( $option ) && array_key_exists( 'backend-font', $option ) ) {
				$backend_font = get_option( $this->plugin_slug )['backend-font'];
				if ( true === $backend_font || ! isset( $backend_font ) ) {
					wp_register_style( $this->plugin_slug, $this->plugin_url . '/assets/admin/css/style-rtl.css', array(), $this->plugin_version );
					wp_enqueue_style( $this->plugin_slug );
				}
			}
		}

		/*
		if ( is_rtl() ) {
			wp_register_style( $this->plugin_slug . '-rtl', $this->plugin_url . '/assets/admin/css/style.rtl.css', array(), $this->plugin_version );
			wp_enqueue_style( $this->plugin_slug . '-rtl' );
		} else {
			wp_register_style( $this->plugin_slug, $this->plugin_url . '/assets/admin/css/style.css', array(), $this->plugin_version );
			wp_enqueue_style( $this->plugin_slug );
		}

		wp_register_script( $this->plugin_slug, $this->plugin_url . '/assets/admin/js/script.js', array(), $this->plugin_version, true );
		wp_enqueue_script( $this->plugin_slug );
		*/
	}

	/**
	 * Activate the plugin
	 *
	 * @return void
	 * @see https://developer.wordpress.org/reference/functions/add_option
	 */
	public function activate() {
		add_option( $this->plugin_slug );
	}

	/**
	 * Run when plugins deactivated
	 *
	 * @return void
	 */
	public function deactivate() {
		// Clear any temporary data stored by plugin.
		// Flush Cache/Temp.
		// Flush Permalinks.
	}

	/**
	 * Uninstall plugin
	 *
	 * @return void
	 * @see https://developer.wordpress.org/reference/functions/delete_option
	 */
	public static function uninstall() {
		delete_option( 'persian-font' );
		// Remove Tables from wpdb
		// global $wpdb;
		// $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}persian-font");
		// Clear any cached data that has been removed.
		wp_cache_flush();
	}

	/**
	 * Set Plugin Info
	 *
	 * @return void
	 */
	private function set_plugin_info() {
		$this->plugin_slug = basename( $this->plugin_file, '.php' );
		$this->plugin_url  = plugins_url( '', $this->plugin_file );

		if ( ! function_exists( 'get_plugins' ) ) {
			include_once \ABSPATH . 'wp-admin/includes/plugin.php';
		}

		$this->plugin_folder  = plugin_dir_path( $this->plugin_file );
		$plugin_info          = get_plugins( '/' . plugin_basename( $this->plugin_folder ) );
		$plugin_file_name     = basename( $this->plugin_file );
		$this->plugin_version = $plugin_info[ $plugin_file_name ]['Version'];
		$this->plugin_name    = $plugin_info[ $plugin_file_name ]['Name'];
	}

	/**
	 * The function "run" is a placeholder function in PHP with no code inside.
	 *
	 * @return void
	 */
	private function run() {
		// nothing for now
	}

	/**
	 * The admin function includes the options.php file and registers the admin menu.
	 *
	 * @return void
	 * @SuppressWarnings(PHPMD.StaticAccess)
	 */
	private function admin() {
		\PersianFont\Persian_Font_Options::get_instance( $this->plugin_slug );
	}
}
