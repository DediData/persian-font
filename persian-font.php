<?php
/*
Plugin Name: فارسی ساز رایگان قالب و پوسته وردپرس
Plugin URI: https://parsmizban.com/persian-font
Description: به صورت رایگان و سریع، قالب وردپرس دانلود نمائید و آن را فارسی کنید.
Version: 2.0
Author: فرهاد سخائی (پارس میزبان)
Author URI: https://parsmizban.com
*/

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

class PersianFont {

	protected $plugin_name;
	protected $plugin_slug;
	protected $plugin_url;
	protected $plugin_version;

	function __construct() {
		$this->set_plugin_info();
		register_activation_hook( __FILE__, [$this, 'activate'] );
		register_deactivation_hook( __FILE__, [$this, 'deactivate'] );
		register_uninstall_hook( __FILE__, 'PersianFont::uninstall' );
		add_action( 'plugins_loaded', [$this, 'load_languages'] );
		add_action( 'wp_enqueue_scripts', [$this, 'load_frontend_scripts'], 11 );
		if( is_admin() ){
			add_action( 'admin_enqueue_scripts', [$this, 'load_admin_scripts'], 11 );
			$this->admin();
		}else{
			$this->run();
		}

	}

	function set_plugin_info(){
		$this->plugin_slug = basename(__FILE__, ".php");
		$this->plugin_url = plugins_url( NULL, __FILE__ );
		if ( ! function_exists( 'get_plugins' ) ){
			require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		}
		$plugin_folder = get_plugins( '/' . plugin_basename( dirname( __FILE__ ) ) );
		$plugin_file = basename( ( __FILE__ ) );
		$this->plugin_version = $plugin_folder[$plugin_file]['Version'];
		$this->plugin_name = $plugin_folder[$plugin_file]['Name'];
	}

	function activate(){
		
	}
	
	function deactivate(){
		
	}

	static function uninstall(){
		delete_option('persianfont');
	}

	function load_languages() {
		load_plugin_textdomain( $this->plugin_slug, FALSE, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}

	function load_frontend_scripts(){
		$frontend_font = get_option('persianfont')['frontend-font'];
		if($frontend_font === true or !isset($frontend_font)){
			wp_enqueue_style( $this->plugin_slug , $this->plugin_url . '/css/style.css', NULL , $this->plugin_version );
			//wp_enqueue_script( $this->plugin_slug , $this->plugin_url . '/js/script.js', [], $this->plugin_version, true );
		}
		// Load Persian fonts and RTL optimized styles for editor
		add_editor_style( 'css/editor-style-rtl.css', $this->plugin_url . '/css' );
	}

	// Load Persian fonts and RTL optimized styles for admin
	function load_admin_scripts(){
		$backend_font = get_option('persianfont')['backend-font'];
		if($backend_font === true or !isset($backend_font)){
			wp_enqueue_style( $this->plugin_slug , $this->plugin_url . '/css/admin-rtl.css', NULL, $this->plugin_version );
		}
	}

	function admin(){
		include( plugin_dir_path( __FILE__ ) . 'options.php');
	}
	
	function run(){
		
	}
}
new PersianFont;