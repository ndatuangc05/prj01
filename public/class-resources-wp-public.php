<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://www.dystrick.com
 * @since      1.0.0
 *
 * @package    Resources_Wp
 * @subpackage Resources_Wp/public
 */

use Carbon_Fields\Container;  
use Carbon_Fields\Field;

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Resources_Wp
 * @subpackage Resources_Wp/public
 * @author     dystrick <contact@dystrick.com>
 */
class Resources_Wp_Public {

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

	private $post_type;
	private $taxonomy;
	private $build_dir_path; 
	private $build_dir_url;
	private $local_dev;

	private static $field_prefix = RESOURCES_WP_KEY . '_';  

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->post_type = RESOURCES_WP_KEY . '-resource';
		$this->taxonomy = RESOURCES_WP_KEY . '-category';
		$this->build_dir_path = plugin_dir_path( __FILE__ ) . 'app/build/';
		$this->build_dir_url = plugin_dir_url( __FILE__ ) . 'app/build/';  
		$this->local_dev = false; 

		add_action( 'init', array( $this, 'register_custom_post_types' ) );  
		add_action( 'init', array( $this, 'register_custom_taxonomies' ) );
		add_action( 'init', array( $this, 'register_shortcodes' ) ); 
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );  
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_filter( 'template_include', array( $this, 'get_archive_templates' ), PHP_INT_MAX ); 
		add_filter( 'template_include', array( $this, 'get_single_templates' ), PHP_INT_MAX );
        add_action( 'carbon_fields_register_fields', array( $this, 'register_custom_fields' ) );
	} 

	/**
	 * Register the stylesheets for the public-facing side of the site.
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

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/resources-wp-public.css', array(), $this->version, 'all' );

		if ( !$this->local_dev ) { 
			$assets = $this->get_react_assets( $this->build_dir_path );  

			// Load css files.
			foreach ( $assets['css'] as $index => $css_file ) {
				wp_enqueue_style( $this->plugin_name . '-' . $index, $this->build_dir_url . $css_file, array(), wp_rand(10, 1000), 'all' ); 
			}
		}

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
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

		if ( class_exists('Resources_Wp_Admin') ) {
			$settings = Resources_Wp_Admin::get_settings();
		} 

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/resources-wp-public.js', array( 'jquery' ), $this->version, false );

		if ( $this->local_dev ) { 
			$bundle_file = $this->build_dir_path . 'static/js/bundle.js'; 

			wp_enqueue_script( $this->plugin_name . '-0', $bundle_file, array(), wp_rand(10, 1000), true ); 
		} else {  
			$assets = $this->get_react_assets( $this->build_dir_path ); 

			foreach ( $assets['js'] as $index => $js_file ) {
				wp_enqueue_script( $this->plugin_name . '-' . $index, $this->build_dir_url . $js_file, array(), wp_rand(10, 1000), true ); 
			}   
		}
		
		wp_localize_script($this->plugin_name . '-0', $this->dashes_to_camel_case( $this->plugin_name ), array( 
			'appSelector' 	=> '#' . RESOURCES_WP_KEY . '-resources-app',    
			'version' 		=> $this->version, 
			'nonce' 		=> wp_create_nonce( 'resources_wp' ),  
			'apiUrl' 		=> rest_url( $this->plugin_name . '/v1' ),
			'slug' 			=> !empty( $settings['custom_slug'] ) ? $settings['custom_slug'] : 'resources'
		)); 

	}

	private function get_react_assets( $build_dir ) {  
        if( !$build_dir ) return; 
    
        // Setting path variables
        $build_manifest = $build_dir . '/asset-manifest.json';
    
        $request = file_get_contents( $build_manifest );
    
        // If the remote request fails, wp_remote_get() will return a WP_Error, 
        // so let’s check if the $request variable is an error:
        if( !$request ) return false;
    
        // Convert json to php array.
        $files_data = json_decode($request);
        
        if( $files_data === null ) return;
    
        if( !property_exists( $files_data, 'entrypoints' ) ) return false;
    
        // Get assets links.
        $assets_files = $files_data->entrypoints;
    
        $js_files = $this->filter_js_files( $assets_files );
        $css_files = $this->filter_css_files( $assets_files ); 
    
        return array(
            'css' => $css_files,
            'js' => $js_files
        );
    }
    
    private function filter_js_files( $assets_files ) {
        return array_values( array_filter( $assets_files, function( $file_string ) {
            return pathinfo( $file_string, PATHINFO_EXTENSION ) === 'js';
        } ) );
    } 
      
    private function filter_css_files( $assets_files ) {
        return array_values( array_filter( $assets_files, function( $file_string ) { 
            return pathinfo( $file_string, PATHINFO_EXTENSION ) === 'css';
        } ) ); 
    }

    public function register_shortcodes() { 
        add_shortcode( 'wp_resources', function( $atts ) { 
			$default_atts = ['color' => 'black']; 
			$args = shortcode_atts( $default_atts, $atts );
			
			return "<div id='" . RESOURCES_WP_KEY . "-resources-app'></div>";   
		});  
    }

	public function register_custom_post_types() {
		$labels = array(
			'name'                  => _x( 'Resources', 'Post Type General Name', 'resources-wp' ),
			'singular_name'         => _x( 'Resource', 'Post Type Singular Name', 'resources-wp' ),
			'menu_name'             => __( 'Resources', 'resources-wp' ),
			'name_admin_bar'        => __( 'Resource', 'resources-wp' ),
			'archives'              => __( 'Resource Archives', 'resources-wp' ),
			'attributes'            => __( 'Resource Attributes', 'resources-wp' ),
			'parent_item_colon'     => __( 'Parent Resource:', 'resources-wp' ),
			'all_items'             => __( 'All Resources', 'resources-wp' ),
			'add_new_item'          => __( 'Add New Resource', 'resources-wp' ),
			'add_new'               => __( 'Add New', 'resources-wp' ),
			'new_item'              => __( 'New Resource', 'resources-wp' ),
			'edit_item'             => __( 'Edit Resource', 'resources-wp' ),
			'update_item'           => __( 'Update Resource', 'resources-wp' ),
			'view_item'             => __( 'View Resource', 'resources-wp' ),
			'view_items'            => __( 'View Resources', 'resources-wp' ),
			'search_items'          => __( 'Search Resource', 'resources-wp' ),
			'not_found'             => __( 'Not found', 'resources-wp' ),
			'not_found_in_trash'    => __( 'Not found in Trash', 'resources-wp' ),
			'featured_image'        => __( 'Featured Image', 'resources-wp' ),
			'set_featured_image'    => __( 'Set featured image', 'resources-wp' ),
			'remove_featured_image' => __( 'Remove featured image', 'resources-wp' ),
			'use_featured_image'    => __( 'Use as featured image', 'resources-wp' ),
			'insert_into_item'      => __( 'Insert into resource', 'resources-wp' ),
			'uploaded_to_this_item' => __( 'Uploaded to this resource', 'resources-wp' ),
			'items_list'            => __( 'Resources list', 'resources-wp' ),
			'items_list_navigation' => __( 'Resources list navigation', 'resources-wp' ),
			'filter_items_list'     => __( 'Filter resources list', 'resources-wp' ),
		);

		$args = array(
			'label'                 => __( 'Resource', 'resources-wp' ),
			'labels'                => $labels,
			'supports'              => array( 'title' ),
			'taxonomies'            => array( '' ),
			'hierarchical'          => false,
			'public'                => true,
			'show_ui'               => true,
			'show_in_menu'          => true,
			'menu_position'         => 5,
			'show_in_admin_bar'     => true,
			'show_in_nav_menus'     => true,
			'can_export'            => true,
			'has_archive'           => true,
			'exclude_from_search'   => false,
			'publicly_queryable'    => true,
			'capability_type'       => 'page',
			"rewrite" 				=> array( 
				"slug" 			=> $this->get_rewrite_slug(), 
				"with_front" 	=> false 
			),
		);

		register_post_type( $this->post_type, $args ); 

		flush_rewrite_rules();
	}

	public function register_custom_taxonomies() {
		$labels = array(
			'name'                       => _x( 'Categories', 'Taxonomy General Name', 'resources-wp' ),
			'singular_name'              => _x( 'Category', 'Taxonomy Singular Name', 'resources-wp' ),
			'menu_name'                  => __( 'Categories', 'resources-wp' ),
			'all_items'                  => __( 'All Items', 'resources-wp' ),
			'parent_item'                => __( 'Parent Item', 'resources-wp' ),
			'parent_item_colon'          => __( 'Parent Item:', 'resources-wp' ),
			'new_item_name'              => __( 'New Item Name', 'resources-wp' ),
			'add_new_item'               => __( 'Add New Item', 'resources-wp' ),
			'edit_item'                  => __( 'Edit Item', 'resources-wp' ), 
			'update_item'                => __( 'Update Item', 'resources-wp' ),
			'view_item'                  => __( 'View Item', 'resources-wp' ),
			'separate_items_with_commas' => __( 'Separate items with commas', 'resources-wp' ),
			'add_or_remove_items'        => __( 'Add or remove items', 'resources-wp' ),
			'choose_from_most_used'      => __( 'Choose from the most used', 'resources-wp' ),
			'popular_items'              => __( 'Popular Items', 'resources-wp' ),
			'search_items'               => __( 'Search Items', 'resources-wp' ),
			'not_found'                  => __( 'Not Found', 'resources-wp' ),
			'no_terms'                   => __( 'No items', 'resources-wp' ),
			'items_list'                 => __( 'Items list', 'resources-wp' ),
			'items_list_navigation'      => __( 'Items list navigation', 'resources-wp' ),
		); 
		
		$args = array(
			'labels'                     => $labels,
			'hierarchical'               => true,
			'public'                     => true,
			'show_ui'                    => true,
			'show_admin_column'          => true,
			'show_in_nav_menus'          => true,
			'show_tagcloud'              => true,
		);

		register_taxonomy( $this->taxonomy, array( $this->post_type ), $args );
	}

	public function register_custom_fields() {  
		Container::make( 'post_meta', __( 'Resource Meta', 'resources-wp' ) )
			->where( 'post_type', '=', $this->post_type )   
			->add_tab( __( 'Content' ), array( 
				Field::make( 'textarea', self::$field_prefix . 'description', __( 'Description', 'resources-wp' ) )
					->set_required( true )
					->set_help_text( 'Displayed on the archive page and used as form wall preview content.' ),
				
				Field::make( 'image', self::$field_prefix . 'image', __( 'Image', 'resources-wp' ) ),
				
				Field::make( 'select', self::$field_prefix . 'background_size', __( 'Image Fit', 'resources-wp' ) )
					->set_options( array(
						'cover' => "Cover", 
						'contain' => "Contain",
						'auto' => "Auto",
					) ), 

				Field::make( 'complex', self::$field_prefix . 'assets', __( 'Asset(s)', 'resources-wp' ) )
					->set_layout( 'tabbed-horizontal' ) 
					->set_required( true )
					->add_fields( 'content', __( 'Content', 'resources-wp' ), array(  
						Field::make( 'rich_text', 'content', __( 'content', 'resources-wp' ) )
							->set_required( true )
					) )
					->add_fields( 'file', __( 'File', 'resources-wp' ), array(  
						Field::make( 'file', 'file', __( 'file', 'resources-wp' ) )
							->set_required( true )
					) )
					->add_fields( 'image', __( 'Image', 'resources-wp' ), array( 
						Field::make( 'image', 'image', __( 'image', 'resources-wp' ) )  
							->set_required( true )
					) )
					->add_fields( 'video', __( 'Video', 'resources-wp' ), array( 
						Field::make( 'oembed', 'embed', __( 'embed', 'resources-wp' ) )
							->set_required( true )
							->set_help_text( 'Only YouTube video embeds are supported. e.g. https://www.youtube.com/embed/xxxxxxxxxxxx' ),
					) ),
			) );
			
	}

	private function locate_template( $template, $settings, $page_type ) {
		$theme_files = array(
			$page_type . '-' . $settings['custom_post_type'] . '.php',
			$this->plugin_name . DIRECTORY_SEPARATOR . $page_type . '-' . $settings['custom_post_type'] . '.php',
		);

		$exists_in_theme = locate_template( $theme_files, false );
	
		if ( $exists_in_theme != '' ) {
			// Try to locate in theme first
			return $template;
		} else {
			// Try to locate in plugin base folder,
			// try to locate in plugin $settings['templates'] folder,
			// return $template if non of above exist
			$locations = array(
				join( DIRECTORY_SEPARATOR, array( WP_PLUGIN_DIR, $this->plugin_name, '' ) ),
				join( DIRECTORY_SEPARATOR, array( WP_PLUGIN_DIR, $this->plugin_name, $settings['templates_dir'], '' ) ), // plugin $settings['templates'] folder
			);
	
			foreach ( $locations as $location ) {
				if ( file_exists( $location . $theme_files[0] ) ) {
					return $location . $theme_files[0];
				}
			}
	
			return $template;
		}
	}

	public function get_archive_templates( $template ) { 
		global $wp;

		$current_path = explode('/', $wp->request);
		$rewrite_slug = $this->get_rewrite_slug();  
	
		$settings = array(
			'custom_post_type' => $this->post_type,
			'templates_dir' => 'templates',
		);

		// if ( $settings['custom_post_type'] == get_post_type() && is_archive() ) { 
		if ( !empty($current_path) && $current_path[0] == $rewrite_slug && count($current_path) == 1 ) {
			// $this->console_log($this->locate_template( $template, $settings, 'archive' ));
			return $this->locate_template( $template, $settings, 'archive' ); 
		}
	
		return $template;

	}

	public function get_single_templates( $template ) { 
		global $wp;

		$current_path = explode('/', $wp->request);
		$rewrite_slug = $this->get_rewrite_slug();  

		$settings = array(
			'custom_post_type' => RESOURCES_WP_KEY . '_resource',
			'templates_dir' => 'templates', 
		);
	
		// if ( $settings['custom_post_type'] == get_post_type() && is_single() ) {
		if ( !empty($current_path) && $current_path[0] == $rewrite_slug && count($current_path) >= 2 ) {
			return $this->locate_template( $template, $settings, 'single' );
		}
	
		return $template;
	}

	public function get_rewrite_slug() {
		if ( class_exists('Resources_Wp_Admin') ) {
			$settings = Resources_Wp_Admin::get_settings();
		} 

		return !empty( $settings['custom_slug'] ) ? trim( $settings['custom_slug'], '/' ) : 'resources';
	}

	public static function dashes_to_camel_case( $string, $capitalizeFirstCharacter = false ) { 
        $str = str_replace('-', '', ucwords($string, '-'));

        if ( !$capitalizeFirstCharacter ) {
            $str = lcfirst($str);
        }

        return $str;
    }

}
