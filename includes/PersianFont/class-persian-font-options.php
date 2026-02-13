<?php
/**
 * Persian Font Settings Page Class
 *
 * @package Persian_Font
 */

namespace PersianFont;

/**
 * Class for registering settings page under Settings.
 */
final class Persian_Font_Options extends \DediData\Singleton {

	/**
	 * Page Title
	 *
	 * @var string $page_title
	 */
	protected $page_title;

	/**
	 * Menu Title
	 *
	 * @var string $menu_title
	 */
	protected $menu_title;

	/**
	 * Plugin Slug
	 *
	 * @var string $plugin_slug
	 */
	protected $plugin_slug;

	/**
	 * Plugin Hook
	 *
	 * @var string $plugin_hook
	 */
	protected $plugin_hook;

	/**
	 * Constructor
	 *
	 * @param mixed $plugin_slug Plugin Slug String.
	 */
	public function __construct( $plugin_slug = null ) {
		$this->plugin_slug = $plugin_slug;
		$this->page_title  = esc_html__( 'RTL Localization & Fonts', 'persian-font' );
		$this->menu_title  = esc_html__( 'RTL & Fonts', 'persian-font' );
		add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
		// Here you can check if plugin is configured (e.g. check if some option is set). If not, add new hook.
		if ( '' === get_option( $this->plugin_slug ) ) {
			add_action( 'admin_notices', array( $this, 'add_admin_notices' ) );
		}
		add_action( 'admin_init',  array( $this, 'register_settings' ) );
	}

	/**
	 * Sanitize each setting field as needed
	 *
	 * @param array<bool> $input Contains all settings fields as array keys.
	 * @return array<bool>
	 */
	public function process_inputs( $input ) {
		// sanitize functions:
		// sanitize_email(), sanitize_file_name(), sanitize_html_class(), sanitize_key(), sanitize_meta(), sanitize_mime_type(),
		// sanitize_option(), sanitize_sql_orderby(), sanitize_text_field(), sanitize_textarea_field(), sanitize_title(),
		// sanitize_title_for_query(), sanitize_title_with_dashes(), sanitize_user()
		$options                  = array();
		$options['frontend-font'] = isset( $input['frontend-font'] ) && true === boolval( $input['frontend-font'] );
		$options['backend-font']  = isset( $input['backend-font'] ) && true === boolval( $input['backend-font'] );

		// add error/update messages
		// check if the user have submitted the settings

		/*
		add_settings_error(
			$this->plugin_slug . '_messages',
			// Slug title of setting
			'wporg_message',
			// Slug-name , Used as part of 'id' attribute in HTML output.
			esc_html__('The entered information is not correct.', 'persian-font'),
			// message text, will be shown inside styled <div> and <p> tags
			'error'
			// Message type, controls HTML class. Accepts 'error' or 'updated'.
		);
		*/

		return $options;
	}

	/**
	 * Registers a new settings page under Settings.
	 *
	 * @return void
	 */
	public function add_admin_menu() {
		$this->plugin_hook = add_options_page(
			$this->page_title,
			$this->menu_title,
			// Capability
			'manage_options',
			$this->plugin_slug,
			// Output the content for this page
			array( $this, 'settings_page_content' )
		);

		if ( ! $this->plugin_hook ) {
			return;
		}
		add_action( 'load-' . $this->plugin_hook, array( $this, 'on_plugin_page_load' ) );
	}

	/**
	 * Adds a help tab and a help sidebar to the current screen in the WordPress admin area.
	 *
	 * @return void
	 */
	public function on_plugin_page_load() {
		remove_action( 'admin_notices', array( $this, 'add_admin_notices' ) );
		// We are in the correct screen because we are taking advantage of the load-* action (below)
		$help_content = '<p>' . esc_html__( 'Use this page to configure RTL localization plugin', 'persian-font' ) . '</p>';
		$screen       = get_current_screen();
		// $screen->remove_help_tabs();
		$screen->add_help_tab(
			array(
				'id'      => $this->plugin_slug . '-default',
				'title'   => esc_html__( 'Help', 'persian-font' ),
				'content' => $help_content,
			)
		);
		// add more help tabs as needed with unique id's

		// Help sidebars are optional
		$screen->set_help_sidebar(
			'<p><strong>' . esc_html__( 'For more information:', 'persian-font' ) . '</strong></p>'
			. '<p><a href="' . esc_url( esc_html__( 'https://dedidata.com', 'persian-font' ) ) . '" target="_blank">' . esc_html__( 'Visit Our Website!', 'persian-font' ) . '</a></p>'
		);
	}

	/**
	 * Display an admin notice on the WordPress admin dashboard if the plugin is not configured.
	 *
	 * @return void
	 */
	public function add_admin_notices() {
		?>
		<div id="notice" class="update-nag">
			<?php esc_html_e( 'The RTL localization plugin is not configured.', 'persian-font' ); ?>
			<a href="<?php menu_page_url( $this->plugin_slug, true ); ?>"><?php esc_html_e( 'Please configure it now !', 'persian-font' ); ?></a>
		</div>
		<?php
	}

	/**
	 * Register the settings for the plugin.
	 *
	 * @return void
	 */
	public function register_settings() {
		// whitelist options
		register_setting(
			$this->plugin_slug,
			// option_group
			$this->plugin_slug,
			// option_name, for name property of tags
			array( $this, 'process_inputs' )
			// sanitize_callback
		);
		add_settings_section(
			'load-font-setting',
			// id attribute of tags
			esc_html__( 'Font Loading Settings', 'persian-font' ),
			// title heading for the section
			static function ( $args ) {
				// callback function to display content at the top of the section
				?>
		<p id="<?php echo esc_attr( $args['id'] ); ?>"><?php esc_html_e( 'By using the following settings, specify where the fonts should be loaded for different sections of the site.', 'persian-font' ); ?></p>
				<?php
			},
			$this->plugin_slug
			// plugin slug, created by add_options_page()
		);
				add_settings_field(
					'frontend-font',
					// id attribute of tag
					esc_html__( 'Loading RTL localization and fonts for the front-end of the site', 'persian-font' ),
					// Title as label for field
					function () {
						$persian_font        = get_option( $this->plugin_slug );
						$check_frontend_font = $persian_font['frontend-font'] ?? true;
						?>
						<input type="checkbox" name="persian-font[frontend-font]" id="frontend-font" value="true" <?php checked( true, $check_frontend_font ); ?> />
						<?php
					}, // Callback function to echo input tag
					$this->plugin_slug,
					// plugin slug, created by add_options_page()
					'load-font-setting',
					// slug-name of the section
					array(
						'label_for' => 'frontend-font',
						// label for => tag id
						'class'     => 'frontend-font',
					// class for <tr>
					)
				);
				add_settings_field(
					'backend-font',
					// id attribute of tag
					esc_html__( 'Loading RTL localization and fonts for the back-end of the site', 'persian-font' ),
					// Title as label for field
					function () {
						$persian_font       = get_option( $this->plugin_slug );
						$check_backend_font = $persian_font['backend-font'] ?? true;
						?>
						<input type="checkbox" name="persian-font[backend-font]" id="backend-font" value="true" <?php checked( true, $check_backend_font ); ?> />
						<?php
					}, // Callback function to echo input tag
					$this->plugin_slug,
					// plugin slug, created by add_options_page()
					'load-font-setting',
					// slug-name of the section
					array(
						'label_for' => 'backend-font',
						// label for => tag id
						'class'     => 'backend-font',
					// class for <tr>
					)
				);
	}

	/**
	 * Settings page display callback.
	 *
	 * @return void
	 */
	public function settings_page_content() {
		// check user capabilities
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		// var_dump( wp_load_alloptions() ); // print all options

		// show error/update messages
		// settings_errors( $this->plugin_slug . '_messages' ); // no need, WordPress automatically call this
		?>
		<div class="wrap">
			<h1 class="wp-heading-inline"><?php echo esc_html( $this->page_title ); ?></h1>
			<form method="post" action="options.php">
				<p><?php esc_html_e( 'This plugin maximizes the RTL localization of your website and utilizes a proper font for its display.', 'persian-font' ); ?></p>
				<p><?php esc_html_e( 'Note that your website theme must support RTL; in this case, you can use many free themes which are available on wordpress.org site for your website.', 'persian-font' ); ?></p>
				<p><?php esc_html_e( 'Also, this plugin has the capability of better optimizing the administration for RTL and provides better fonts in the administration and editor.', 'persian-font' ); ?></p>
			<?php
				submit_button();
				settings_fields( $this->plugin_slug );
				// This prints out all hidden setting fields
				do_settings_sections( $this->plugin_slug );
				submit_button();
			?>
			</form>
		</div>
		<?php
	}
}
