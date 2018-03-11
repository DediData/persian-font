<?php

/**
 * Class for registering persian font settings page under Settings.
 */
class PersianFont_Options_Page {
	
	public $page_title;
	public $menu_title;
	public $plugin_slug;
	public $plugin_hook;
	
    /**
     * Constructor.
     */
    function __construct() {
		
		$this->plugin_slug = 'persianfont';
		$this->page_title = __('تنظیمات افزونه فارسی ساز قالب', 'persianfont');
		$this->menu_title = __('فارسی ساز قالب', 'persianfont');
		
        add_action( 'admin_menu', [ $this, 'add_admin_menu' ] );

		// Here you can check if plugin is configured (e.g. check if some option is set). If not, add new hook.
		if(empty(get_option($this->plugin_slug))){
			add_action( 'admin_notices', [ $this, 'add_admin_notices' ] );
		}
		
		add_action( 'admin_init',  [ $this, 'register_settings' ] );
	}
	
	/**
	* Registers a new settings page under Settings.
	*/
	function add_admin_menu() {
		$this->plugin_hook = 
			add_options_page(
				$this->page_title,
				$this->menu_title,
				'manage_options', // capability
				$this->plugin_slug,
				[ $this, 'settings_page_content' ] //output the content for this page
		);

		//var_dump($this->plugin_hook); // -> string(26) "settings_page_persian_font"
		if($this->plugin_hook){
			add_action( 'load-' . $this->plugin_hook, [ $this, 'on_plugin_page_load' ] );
		}

	}

	function on_plugin_page_load(){
		remove_action( 'admin_notices', [ $this, 'add_admin_notices' ] );
		$this->add_setting_page_help();
	}

	function add_admin_notices() {
		?>
		<div id="notice" class="update-nag">
			<?php _e('افزونه فارسی ساز قالب پیکربندی نشده است. ', 'persianfont'); ?>
			<a href="<?php menu_page_url( $this->plugin_slug, true ); ?>"><?php _e('لطفا هم اکنون پیکربندی نمائید.', 'persianfont'); ?></a>
		</div>
		<?php
	}
 
 	function add_setting_page_help(){
		// We are in the correct screen because we are taking advantage of the load-* action (below)
		$help_content = 
			'<p>' . __( 'از این صفحه برای تنظیمات افزونه فارسی ساز قالب استفاده نمائید.', 'persianfont' ) . '</p>' .
			'';

		$screen = get_current_screen();
		//$screen->remove_help_tabs();
		$screen->add_help_tab(
		[
			'id'			=> 'persianfont-default',
			'title'			=> __( 'Help' ),
			'content'	=> $help_content,
		]);
		//add more help tabs as needed with unique id's

		// Help sidebars are optional
		$screen->set_help_sidebar(
			'<p><strong>' . __( 'For more information:' ) . '</strong></p>' .
			'<p><a href="https://parsmizban.com" target="_blank">' . _( 'بازدید از پارس میزبان' ) . '</a></p>'
		);
	}

	function register_settings() { // whitelist options
		register_setting(
			$this->plugin_slug, // option_group
			$this->plugin_slug, // option_name, for name property of tags
			[$this, 'process_inputs'] // sanitize_callback
		);
			add_settings_section(
				'load-font-setting', // id attribute of tags
				__('تنظیمات مربوط به بارگذاری فونت ها', 'persianfont'), // title heading for the section
				function($args){ ?>
					<p id="<?php echo esc_attr( $args['id'] ); ?>"><?php _e('با استفاده از تنظیمات زیر، تعیین نمائید که فونت ها برای کدام بخش های سایت بارگذاری شوند.', 'persianfont'); ?></p>
				<?php
				}, // callback function to display content at the top of the section
				$this->plugin_slug // plugin slug, created by add_options_page()
			);
				add_settings_field(
					'frontend-font', // id attribute of tag
					__('بارگذاری فونت و فارسی سازی برای بخش اصلی سایت', 'persianfont'), // Title as lable for field
					function($args){
						$persian_font = get_option('persianfont');
						$check_frontend_font = isset( $persian_font['frontend-font'] ) ? $persian_font['frontend-font'] : true;
						?>
						<input type="checkbox" name="persianfont[frontend-font]" id="frontend-font" value="true" <?php checked( true, $check_frontend_font ); ?> />
						<?php
					}, // Callback function to echo input tag
					$this->plugin_slug, // plugin slug, created by add_options_page()
					'load-font-setting', // slug-name of the section
					[
						'label_for'	=> 'frontend-font', // label for => tag id
						'class'		=> 'frontend-font',	// class for <tr>
					]
				);
				add_settings_field(
					'backend-font', // id attribute of tag
					__('بارگذاری فونت و فارسی سازی برای بخش مدیریت', 'persianfont'), // Title as lable for field
					function($args){
						$persian_font = get_option('persianfont');
						$check_backend_font = isset( $persian_font['backend-font'] ) ? $persian_font['backend-font'] : true;
						?>
						<input type="checkbox" name="persianfont[backend-font]" id="backend-font" value="true" <?php checked( true, $check_backend_font ); ?> />
					<?php
					}, // Callback function to echo input tag
					$this->plugin_slug, // plugin slug, created by add_options_page()
					'load-font-setting', // slug-name of the section
					[
						'label_for'	=> 'backend-font', // label for => tag id
						'class'		=> 'backend-font',	// class for <tr>
					]
				);
				
	}

	/**
	 * Sanitize each setting field as needed
	 *
	 * @param array $input Contains all settings fields as array keys
	 */
	public function process_inputs( $input ){
		
		// sanitize functions:
		// sanitize_email(), sanitize_file_name(), sanitize_html_class(), sanitize_key(), sanitize_meta(), sanitize_mime_type(),
		// sanitize_option(), sanitize_sql_orderby(), sanitize_text_field(), sanitize_textarea_field(), sanitize_title(),
		// sanitize_title_for_query(), sanitize_title_with_dashes(), sanitize_user()
		$options = [];
		$options['frontend-font'] = boolval( isset( $input['frontend-font'] ) and $input['frontend-font'] == true );
		$options['backend-font'] = boolval( isset( $input['backend-font'] ) and $input['backend-font'] == true );

		// add error/update messages
		// check if the user have submitted the settings

		if(false){
			add_settings_error(
				'persianfont_messages', // Slug title of setting
				'wporg_message', // Slug-name , Used as part of 'id' attribute in HTML output.
				__( 'اطلاعات وارد شده صحیح نمی باشد.', 'persianfont' ), // message text, will be shown inside styled <div> and <p> tags
				'error' // Message type, controls HTML class. Accepts 'error' or 'updated'.
			);
		}

		return $options;
	}

    /**
     * Settings page display callback.
     */
    function settings_page_content() {
		// check user capabilities
		if ( ! current_user_can( 'manage_options' ) ) { return; }
		
		//var_dump( wp_load_alloptions() ); // print all options

		// show error/update messages
		//settings_errors( 'persianfont_messages' ); // no need, wordpress automatically call this
		?>
		<div class="wrap">
			<h1 class="wp-heading-inline"><?php echo esc_html($this->page_title); ?></h1>
			<form method="post" action="options.php">
			<p>این افزونه، قالب وب سایت شما را تا حد امکان فارسی سازی کرده و از فونت فارسی برای نمایش آن استفاده می کند،<br />
			 دقت داشته باشید که قالب وب سایت شما باید از RTL پشتیبانی کند، در این صورت می توانید از بسیاری از قالب های غیر ایرانی، در وب سایت خود استفاده نمائید.</p>
			 <p>همچنین این افزونه قابلیت بهینه سازی بهتر بخش مدیریت را دارد و این امکان را فراهم می کند تا از فونت بهتری در بخش مدیریت و ویرایشگر استفاده نمائید.</p>
			<?php
				submit_button();
				settings_fields( $this->plugin_slug ); // This prints out all hidden setting fields
				do_settings_sections( $this->plugin_slug );
				submit_button();
			?>
			</form>
		</div>
		<?php
    }
	
} // class
 
new PersianFont_Options_Page;
