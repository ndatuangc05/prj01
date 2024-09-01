<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://www.dystrick.com
 * @since      1.0.0
 *
 * @package    Resources_Wp
 * @subpackage Resources_Wp/admin 
 */

use Carbon_Fields\Container;  
use Carbon_Fields\Field;

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Resources_Wp
 * @subpackage Resources_Wp/admin
 * @author     dystrick <contact@dystrick.com>
 */
class Resources_Wp_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	private static $field_prefix = RESOURCES_WP_KEY . '_settings_';  
	private static $defaults = array(
		'archive_header_enabled'			=> true,  
		'archive_header_kicker'				=> '',  
		'archive_header_title'				=> '', 
		'archive_header_content'			=> '',
		'archive_header_font_color'			=> '', 
		'archive_header_background_color'	=> '',
		'archive_header_background_image'	=> null,
		'single_header_enabled'				=> false,  
		'single_header_kicker'				=> '',  
		'single_header_title'				=> '', 
		'single_header_content'				=> '',
		'single_header_font_color'			=> '', 
		'single_header_background_color'	=> '',
		'single_header_background_image'	=> null,
		'single_social_share_buttons'		=> [],
		'theme_body_font_family'			=> null,
		'theme_heading_font_family'			=> null,
		'theme_mono_font_family'			=> null,
		'theme_background_color'			=> null,
		'theme_color_scheme' 				=> 'gray',
		'theme_json' 						=> null,
		'custom_slug'						=> 'resources',
		'custom_header_name'				=> null,
		'custom_footer_name'				=> null,
	);

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

		add_action( 'after_setup_theme', function() { 
            require_once( plugin_dir_path( dirname( __FILE__ ) ) . 'vendor/autoload.php' );
            \Carbon_Fields\Carbon_Fields::boot();   
        } );
		add_action( 'carbon_fields_register_fields', array( $this, 'register_fields' ) );
		add_filter( 'plugin_action_links_' . plugin_basename( plugin_dir_path( __DIR__ ) . $this->plugin_name . '.php' ), array( $this, 'add_action_links' ) );   

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Resources_Wp_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Resources_Wp_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/resources-wp-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Resources_Wp_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Resources_Wp_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/resources-wp-admin.js', array( 'jquery' ), $this->version, false );

	}

	public function add_action_links( $links ) {  
		// $settings_link = array( '<a href="' . admin_url( 'options-general.php?page=' . $this->plugin_name ) . '">' . __( 'Settings', 'resources-wp' ) . '</a>', );
		$settings_link = array( '<a href="' . admin_url( 'options-general.php?page=resources-wp.php' ) . '">' . __( 'Settings', 'resources-wp' ) . '</a>', );
	
		return array_merge( $settings_link, $links ); 
	}


	public function register_fields() {  
		$options_container = Container::make( 'theme_options', __( 'Resources WP Settings', 'resources-wp' ) )
			->set_page_menu_title( 'Resources WP' )

			->set_page_file( 'resources-wp.php' )
			
			->set_icon( 'dashicons-admin-settings' )

			->add_fields( array (
				Field::make( 'html', self::$field_prefix . 'overview' )
    				->set_html( '
						<img src="' . plugin_dir_url( __FILE__ ) . 'images/header.png' . '" alt="Resources WP admin title" style="width: 100%; max-width: 100%;" />
						<p style="text-align: center; max-width: 600px; margin: 15px auto;">With our multi-purpose resource plugin, all of your assets are shared and organized in one place. Our plugin is widely extensive and supports a wide variety of content, all with friendly search functionality that enables users to find content with speed and relevance. Its fully customizable themes, fonts and color palettes empowers you to be as dynamic and bold as you wish.</p>
						<h4>Table of Contents</h4>
						<ul style="list-style-type: disc; padding-left: 40px;">
							<li>
								<a href="/wp-admin/admin.php?page=resources-wp-archive-page.php">Archive Page</a>
								<p style="max-width: 400px; margin-top: 0;">Settings for the resource archive page. You can configure things like the header image, background color, font color, etc.</p>
							</li>
							<li>
								<a href="/wp-admin/admin.php?page=resources-wp-single-page.php">Single Pages</a>
								<p style="max-width: 400px; margin-top: 0;">Settings for the resource single page(s). You can configure things like the header image, background color, font color, etc.</p>
							</li>
							<li>
								<a href="/wp-admin/admin.php?page=resources-wp-theme.php">Theme</a>
								<p style="max-width: 400px; margin-top: 0;">Defind global styles and theme configuration. Including color scheme, fonts, and even custom theme JSON.</p>
							</li>
							<li>
								<a href="/wp-admin/admin.php?page=resources-wp-integration.php">Integration</a>
								<p style="max-width: 400px; margin-top: 0;">Integration settings are for more fine-grain control over the Resources WP content type and templates. You may define custom header and footer templates, and inject custom header and footer scripts.</p>
							</li>
						</ul>
						<h4>Version <span style="font-weight: normal;">' . $this->version . '</span></h4>
					' )
			));

		Container::make( 'theme_options', __( 'Resources WP Settings &middot; Archive Page', 'resources-wp' ) )
			->set_page_parent( $options_container )

			->set_page_menu_title( 'Archive Page' )

			->set_page_file( 'resources-wp-archive-page.php' )

			->add_fields( array(
				Field::make( 'checkbox', self::$field_prefix . 'archive_header_enabled', __( 'Enable Header', 'resources-wp' ) )
					->set_default_value( true ),

				Field::make( 'text', self::$field_prefix . 'archive_header_kicker', __( 'Header Kicker', 'resources-wp' ) )
					->set_default_value( 'Header kicker goes here' ),

				Field::make( 'text', self::$field_prefix . 'archive_header_title', __( 'Header Title', 'resources-wp' ) )
					->set_default_value( 'Header title goes here' ),

				Field::make( 'rich_text', self::$field_prefix . 'archive_header_content', __( 'Header Content', 'resources-wp' ) )
					->set_default_value( 'Integer consectetur non condimentum erat quis urna habitant feugiat mollis egestas at viverra quisque vestibulum vel eu blandit consectetur suspendisse ad parturient.' ),

				Field::make( 'color', self::$field_prefix . 'archive_header_font_color', __( 'Header Font Color', 'resources-wp' ) ),

				Field::make( 'color', self::$field_prefix . 'archive_header_background_color', __( 'Header Background Color', 'resources-wp' ) ),

				Field::make( 'image', self::$field_prefix . 'archive_header_background_image', __( 'Header Background Image', 'resources-wp' ) ),
			) );

		Container::make( 'theme_options', __( 'Resources WP Settings &middot; Single Pages', 'resources-wp' ) )
			->set_page_parent( $options_container )
			
			->set_page_menu_title( 'Single Pages' )

			->set_page_file( 'resources-wp-single-page.php' )

			->add_fields( array(
				Field::make( 'checkbox', self::$field_prefix . 'single_header_enabled', __( 'Enable Header', 'resources-wp' ) )
					->set_default_value( true ),

				Field::make( 'text', self::$field_prefix . 'single_header_kicker', __( 'Header Kicker', 'resources-wp' ) )
					->set_default_value( 'Header kicker goes here' ),

				Field::make( 'text', self::$field_prefix . 'single_header_title', __( 'Header Title', 'resources-wp' ) )
					->set_default_value( 'Header title goes here' ),

				Field::make( 'rich_text', self::$field_prefix . 'single_header_content', __( 'Header Content', 'resources-wp' ) )
					->set_default_value( 'Integer consectetur non condimentum erat quis urna habitant feugiat mollis egestas at viverra quisque vestibulum vel eu blandit consectetur suspendisse ad parturient.' ),
				Field::make( 'color', self::$field_prefix . 'single_header_font_color', __( 'Header Font Color', 'resources-wp' ) ),

				Field::make( 'color', self::$field_prefix . 'single_header_background_color', __( 'Header Background Color', 'resources-wp' ) ),

				Field::make( 'image', self::$field_prefix . 'single_header_background_image', __( 'Header Background Image', 'resources-wp' ) ),

				Field::make( 'set', self::$field_prefix . 'single_social_share_buttons', __( 'Social Share Buttons', 'resources-wp' ) )
					->set_options( array(
						'email' 	=> __( 'Email', 'resources-wp' ),
						'twitter' 	=> __( 'Twitter', 'resources-wp' ),
						'linkedin' 	=> __( 'LinkedIn', 'resources-wp' ),
						'facebook' 	=> __( 'Facebook', 'resources-wp' ),
					) )
					->set_default_value( array( 
						'email',
						'twitter',
						'linkedin',
						'facebook'
					) ),
			) );

		Container::make( 'theme_options', __( 'Resources WP Settings &middot; Theme', 'resources-wp' ) )
			->set_page_parent( $options_container )

			->set_page_menu_title( 'Theme' )

			->set_page_file( 'resources-wp-theme.php' )

			->add_fields( array(
				Field::make( 'text', self::$field_prefix . 'theme_body_font_family', __( 'Body Font Family', 'resources-wp' ) ),

				Field::make( 'text', self::$field_prefix . 'theme_heading_font_family', __( 'Heading Font Family', 'resources-wp' ) ),

				Field::make( 'text', self::$field_prefix . 'theme_mono_font_family', __( 'Mono Font Family', 'resources-wp' ) ),

				Field::make( 'color', self::$field_prefix . 'theme_background_color', __( 'Background Color', 'resources-wp' ) ),

				Field::make( 'select', self::$field_prefix . 'theme_color_scheme', __( 'Color Scheme', 'resources-wp' ) )
					->add_options( array(
						'gray' 		=> __( 'Gray', 'resources-wp' ),
						'red' 		=> __( 'Red', 'resources-wp' ),
						'orange' 	=> __( 'Orange', 'resources-wp' ),
						'yellow' 	=> __( 'Yellow', 'resources-wp' ),
						'green' 	=> __( 'Green', 'resources-wp' ),
						'teal' 		=> __( 'Teal', 'resources-wp' ),
						'cyan' 		=> __( 'Cyan', 'resources-wp'),
						'blue' 		=> __( 'Blue', 'resources-wp' ),
						'purple' 	=> __( 'Purple', 'resources-wp' ),
						'pink' 		=> __( 'Pink', 'resources-wp' ), 
					) )
					->set_default_value( 'gray' ), 
				
				Field::make( 'textarea', self::$field_prefix . 'theme_json', __( 'Theme JSON', 'resources-wp' ) )
					->set_help_text( 'Generate custom theme color palettes using tools like <a href="https://themera.vercel.app/" target="_blank">Themera</a>, <a href="https://smart-swatch.netlify.app/" target="_blank">Smart Swatch</a>, <a href="https://coolors.co/app" target="_blank">Coolors</a> or <a href="https://palx.jxnblk.com" target="_blank">Palx</a>' ),
			) );

		Container::make( 'theme_options', __( 'Resources WP Settings &middot; Integration', 'resources-wp' ) )
			->set_page_parent( $options_container )

			->set_page_menu_title( 'Integration' )

			->set_page_file( 'resources-wp-integration.php' )

			->add_fields( array(
				Field::make( 'text', self::$field_prefix . 'custom_slug', __( 'Custom Slug (Optional)', 'resources-wp' ) )
					->set_help_text( "Enter a custom slug to override default '/resources'." ),
					
				Field::make( 'text', self::$field_prefix . 'custom_header_name', __( 'Custom Header Name (Optional)', 'resources-wp' ) )
					->set_help_text( "The name of the specialised header." ),

				Field::make( 'text', self::$field_prefix . 'custom_footer_name', __( 'Custom Footer Name (Optional)', 'resources-wp' ) )
					->set_help_text( "The name of the specialised footer." ),

				Field::make( 'header_scripts', self::$field_prefix . 'custom_header_scripts', __( 'Custom Header Scripts (Optional)', 'resources-wp' ) )
					->set_hook_priority( PHP_INT_MAX )
					->set_help_text( 'Add custom CSS to override default styles. Accepts <script>, <style>, and <meta> tags' ),

				Field::make( 'footer_scripts', self::$field_prefix . 'custom_footer_scripts', __( 'Custom Footer Scripts (Optional)', 'resources-wp' ) ),
			) );

		Container::make( 'theme_options', __( 'Resources WP Settings &middot; Tools', 'resources-wp' ) )
			->set_page_parent( $options_container )
			
			->set_page_menu_title( 'Tools' )

			->set_page_file( 'resources-wp-tools.php' )

			->where( 'current_user_capability', 'CUSTOM', function( $current_user_id ) {
				$current_user_data = get_userdata( $current_user_id );
				$current_user_email = $current_user_data->data->user_email;
				$current_user_email_domain = explode('@', $current_user_email)[1];

				return ( $current_user_email_domain == 'dystrick.com' );
			} )

			->add_fields( array(
				Field::make( 'html', self::$field_prefix . 'tools', __( 'tools', 'resources-wp' ) )
					->set_html( '<div>
									<div style="display: flex; align-items: center;">
										<a href="#TB_inline?&inlineId=convert-posts-to-resources-dialog" id="convert-posts-to-resources-button" class="button button-secondary thickbox" style="margin: 0.5rem 0.5rem 0.5rem 0;">Convert Posts to Resources</a>
										<a href="#TB_inline?&inlineId=delete-all-resources-dialog" id="delete-all-resources-button" class="button button-primary thickbox" style="margin: 0.5rem 0.5rem 0.5rem 0;">Delete all Resources</a>
									</div> 

									<div id="convert-posts-to-resources-dialog" style="display: none;">
										<div style="display: flex; justify-content: center; align-items: center; width: 100%; height: 100%;">

											<div class="convert-posts-to-resources-content" style="max-width: 400px;">
												<h3>CONVERT POSTS TO RESOURCES</h3>
												<p>This will convert all existing posts to resources. Please type CONVERT in the field below to confirm this action.</p>
												<form>
													<input type="text" name="convert" placeholder="CONVERT" />
													<input type="submit" value="Confirm" class="button button-primary" />
													<button class="button" onclick="tb_remove();">Cancel</button>
												</form>
												<div class="convert-posts-to-resources-notice"><p style="font-weight: bold;"></p></div> 
											</div>

											<img class="convert-posts-to-resources-spinner" src="' . esc_url( get_admin_url() . 'images/wpspin_light-2x.gif' ) . '" style="display: none;" />

										</div>
									</div>
									
									<div id="delete-all-resources-dialog" style="display: none;">
										<div style="display: flex; justify-content: center; align-items: center; width: 100%; height: 100%;">
											<div class="delete-all-resources-content" style="max-width: 400px;">
												<h3>DELETE ALL RESOURCES</h3>
												<p>This will permanently delete all existing resources and categories. Please type DELETE in the field below to confirm this action.</p>
												<form>
													<input type="text" name="delete" placeholder="DELETE" />
													<input type="submit" value="Confirm" class="button button-primary" />
													<button class="button" onclick="tb_remove();">Cancel</button>
												</form>
												<div class="delete-all-resources-notice"><p style="font-weight: bold;"></p></div>
											</div>

											<img class="delete-all-resources-spinner" src="' . esc_url( get_admin_url() . 'images/wpspin_light-2x.gif' ) . '" style="display: none;" />

										</div>
									</div>
								</div>' ),
			) );
	}

	public static function get_settings() {
		$all_saved = new stdClass();

		foreach ( self::$defaults as $key => $value ){
			$saved = carbon_get_theme_option( self::$field_prefix . $key );  

			if( $saved || empty( $saved )) {  
				$all_saved->$key = $saved; 
			} else {
				$all_saved->$key = $value;
			}
		}

		return wp_parse_args( $all_saved, self::$defaults ); 
	}
	
	public static function save_settings( array  $settings ) {
		foreach ( $settings as $key => $value ){
			if( array_key_exists( $key, self::$defaults ) ) { 
				carbon_set_theme_option( self::$field_prefix . $key, $value );
			}
		}
	}

}
