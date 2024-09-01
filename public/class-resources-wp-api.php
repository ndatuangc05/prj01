<?php

class Resources_Wp_Api { 
	private $plugin_name;
	private $version; 
	private $post_type;
	private $taxonomy;

	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version = $version; 
		$this->post_type = RESOURCES_WP_KEY . '-resource'; 
		$this->taxonomy = RESOURCES_WP_KEY . '-category'; 

		add_action( 'rest_api_init', array( $this, 'register_routes') ); 
	}

	public function register_routes( ) {
		register_rest_route( $this->plugin_name . '/v1', '/settings',
			array(
				'methods'  => WP_REST_Server::READABLE,
				'callback'        => array( $this, 'get_settings' ),
				'args'            => array(),
			)
		);

		register_rest_route( $this->plugin_name . '/v1', '/resources',
			array(
				'methods'  => WP_REST_Server::READABLE,
				'callback'        => array( $this, 'get_archive' ),
				'args'            => array(
					'search' => array(
						'required' 			=> false,
						'type'				=> 'string',
						'sanitize_callback' => function($value) {
							return sanitize_text_field(wp_unslash($value));
						}, 
						'validate_callback' => function ( $value ) {
							return is_string($value);
						},
					),
					'categories' => array(
						'required' 	=> false,
						'type'		=> 'string',
						'sanitize_callback' => function($value) {
							return sanitize_text_field(wp_unslash($value));
						}, 
						'validate_callback' => function ( $value ) {
							return is_string($value);
						},
					),
					'paged' => array(
						'required' 	=> false,
						'type'		=> 'integer',
						'sanitize_callback' => function($value) {
							return sanitize_text_field(wp_unslash($value));
						},
						'validate_callback' => function ( $value ) {
							return is_numeric($value);
						}, 
					)
				),
			)
		);

		register_rest_route( $this->plugin_name . '/v1', '/resource/(?P<slug>[a-zA-Z0-9-]+)',    
			array(
				'methods'  => WP_REST_Server::READABLE,    
				'callback'        => array( $this, 'get_single' ),  
				'args'            => array(
					'slug'	=> array(
						'required' 			=> true,
						'type'				=> 'string',
						'sanitize_callback' => function($value) {
							return sanitize_text_field(wp_unslash($value));
						}, 
						'validate_callback' => function ( $value ) {
							return is_string($value);
						},
					)
				),
			)
		);

		register_rest_route( $this->plugin_name . '/v1', '/categories',    
			array(
				'methods'  => WP_REST_Server::READABLE,    
				'callback'        => array( $this, 'get_categories' ),  
				'args'            => array(),
			)
		);
	}

	public function permissions() {
		if (current_user_can('manage_options')) return true;
		return new WP_Error( 'rest_forbidden', esc_html__( 'You do not have permissions to view this data.', 'resources-wp' ), array( 'status' => 401 ) );
	}

	public function get_settings( WP_REST_Request $request ){
		if ( class_exists('Resources_Wp_Admin') ) {
			$settings = Resources_Wp_Admin::get_settings();
		}

		if( !empty( $settings['archive_header_background_image'] ) ) {
			$archive_header_bg_image_id = $settings['archive_header_background_image'];
			$archive_header_bg_image_src = wp_get_attachment_image_src( $archive_header_bg_image_id, 'full' ); 
			$settings['archive_header_background_image'] = array(
				'url' 				=> !empty($archive_header_bg_image_src[0]) ? $archive_header_bg_image_src[0] : null,
				'width' 			=> !empty($archive_header_bg_image_src[1]) ? $archive_header_bg_image_src[1] : null,
				'height'			=> !empty($archive_header_bg_image_src[2]) ? $archive_header_bg_image_src[2] : null,
				'alt'				=> '',
			);  
		}

		if( !empty( $settings['archive_header_background_image'] ) ) {
			$single_header_bg_image_id = $settings['single_header_background_image'];
			$single_header_bg_image_src = wp_get_attachment_image_src( $single_header_bg_image_id, 'full' ); 
			$settings['single_header_background_image'] = array(
				'url' 				=> !empty($single_header_bg_image_src[0]) ? $single_header_bg_image_src[0] : null,
				'width' 			=> !empty($single_header_bg_image_src[1]) ? $single_header_bg_image_src[1] : null,
				'height'			=> !empty($single_header_bg_image_src[2]) ? $single_header_bg_image_src[2] : null,
				'alt'				=> '',
			);  
		}		

		return rest_ensure_response( $settings );
	}

	public function get_archive( WP_REST_Request $request ) {
		$selected_keywords = ($request->get_param( 'search' )) ? $request->get_param( 'search' ) : false;
		$selected_categories = ($request->get_param( 'categories' )) ? explode(',', $request->get_param( 'categories' )) : false;   
		$paged = ($request->get_param( 'paged' )) ? $request->get_param( 'paged' ) : 1;

		$tax_query = ($selected_categories) ? array(
			array(
				'taxonomy' => $this->taxonomy, 
				'field' => 'id',
				'terms' => $selected_categories,  
			)
		) : false;

		$args = array(
            'post_type' 		=> $this->post_type,
			'post_status' 		=> 'publish',
			'posts_per_page'	=> 20,
			'orderby' 			=> 'modified',
			'order' 			=> 'DESC', 
			'tax_query' 		=> $tax_query,
			's' 				=> $selected_keywords,
			'paged' 			=> $paged,
        );

        $query = new WP_Query($args);  
        $resources = $query->posts; 
		$page_count = $query->max_num_pages; 

		if ( !empty($resources) ) { 

			foreach ($resources as $key => $resource) { 
				$this->normalize_post( $resources[$key] ); 

				$categories = wp_get_object_terms( $resource->id, $this->taxonomy );    
				$description = carbon_get_post_meta( $resource->id, RESOURCES_WP_KEY . '_description');
				$image_id = carbon_get_post_meta( $resource->id, RESOURCES_WP_KEY . '_image');
				$image_src = wp_get_attachment_image_src( $image_id, 'full' ); 
				$background_size = carbon_get_post_meta( $resource->id, RESOURCES_WP_KEY . '_background_size');
				$assets = carbon_get_post_meta( $resource->id, RESOURCES_WP_KEY . '_assets'); 	 		

				$resources[$key]->categories = !empty($categories) ? $categories : [];
				$resources[$key]->description = $description; 
				$resources[$key]->image = array(
					'url' 				=> !empty($image_src[0]) ? $image_src[0] : null,
					'width' 			=> !empty($image_src[1]) ? $image_src[1] : null,
					'height'			=> !empty($image_src[2]) ? $image_src[2] : null,
					'alt'				=> '',
					'background_size' 	=> !empty($background_size) ? $background_size : 'auto',
				);

				$resources[$key]->assets = $this->get_asset_values( $assets );  

			} 
		
		}

		return rest_ensure_response( array( 'resources' => $resources, 'page_count' => $page_count ) );
	}

	public function get_single( WP_REST_Request $request ) {
		$slug = $request->get_param( 'slug' ); 

		$args = array(
			'post_type' 		=> $this->post_type,
			'post_status' 		=> 'publish',
			'name'				=> $slug
		);
	
		$resources = get_posts( $args ); 

		if ( empty($resources) ) { 
			return false;
		}

		foreach ($resources as $key => $resource) { 
			$this->normalize_post( $resources[$key] );

			$categories = wp_get_object_terms( $resource->id, $this->taxonomy ); 
			$description = carbon_get_post_meta( $resource->id, RESOURCES_WP_KEY . '_description');
			$image_id = carbon_get_post_meta( $resource->id, RESOURCES_WP_KEY . '_image');
			$image_src = wp_get_attachment_image_src( $image_id, 'full' ); 
			$background_size = carbon_get_post_meta( $resource->id, RESOURCES_WP_KEY . '_background_size');
			$assets = carbon_get_post_meta( $resource->id, RESOURCES_WP_KEY . '_assets'); 			
			$social_share_buttons = carbon_get_theme_option( RESOURCES_WP_KEY . '_settings_single_social_share_buttons' );

			$resources[$key]->categories = !empty($categories) ? $categories : [];
			$resources[$key]->description = $description; 
			$resources[$key]->image = array(
				'url' 				=> !empty($image_src[0]) ? $image_src[0] : null,
				'width' 			=> !empty($image_src[1]) ? $image_src[1] : null,
				'height'			=> !empty($image_src[2]) ? $image_src[2] : null,
				'alt'				=> '',
				'background_size' 	=> !empty($background_size) ? $background_size : 'auto',
			);

			$resources[$key]->assets = $this->get_asset_values( $assets );   

			$resources[$key]->social_share_buttons = $social_share_buttons;
		} 
		
		$response = new WP_REST_Response( $resources[0] ); 
		$response->set_status( 200 );

		return $response; 
	}

	public function get_categories( WP_REST_Request $request ) { 
		$categories = get_terms([
			'taxonomy' => $this->taxonomy,  
			'hide_empty' => true, 
		]); 

		if ( empty($categories) ) { 
			return new WP_Error( 'no_resource', 'There are no categories to display', array( 'status' => 404 ) );
		}

		$responseData = []; 

		foreach ($categories as $key => $value) {  
			$this->normalize_term( $categories[$key] );

			$responseData[] = $categories[$key];
		}
	
		$response = new WP_REST_Response( $responseData ); 
		$response->set_status( 200 );

		return $response; 
	}

	private function normalize_post( $post ) { 
		$update = array(
			'guid' 				=> 'uri', 
			'ID' 				=> 'id',
			'post_name'			=> 'slug',
			'post_password'		=> 'password',
			'post_status'		=> 'status',
			'post_title' 		=> 'title',
			'post_date'			=> 'created_at',
			'post_date_gmt'		=> 'created_at_gmt',
			'post_modified'		=> 'updated_at',
			'post_modified_gmt'	=> 'updated_at_gmt',
		);

		$remove = array( 
			'post_author',
			'post_excerpt',
			'comment_status',
			'ping_status',
			'to_ping',
			'pinged',
			'post_content_filtered',
			'post_parent',
			'menu_order',
			'post_mime_type',
			'comment_count',
			'filter',
		);

		$normalize = array(
			'created_at',
			'created_at_gmt',
			'updated_at',
			'updated_at_gmt',
		);

		foreach ($post as $key => $value) {  
			if( in_array( $key, array_keys( $update ) ) ) {   
				$newKey = $update[ $key ]; 
				$post->$newKey = $post->$key;
				unset($post->$key);
			}

			if( in_array( $key, $remove ) ) {
				unset( $post->$key );	
			} 

			if( in_array( $key, $normalize ) ) {
				$post->$key = rest_parse_date( $post->$key );   
			} 

		}
	}

	private function normalize_term( $term ) { 
		$update = array(
			'term_id'	=> 'id',
		);

		$remove = array( 
			'term_group',
			'term_taxonomy_id',
			'taxonomy',
			'filter'
		);

		foreach ($term as $key => $value) {  
			if( in_array( $key, array_keys( $update ) ) ) {   
				$newKey = $update[ $key ]; 
				$term->$newKey = $term->$key;
				unset($term->$key);
			}

			if( in_array( $key, $remove ) ) {
				unset( $term->$key );	
			} 
		}
	}

	public function get_asset_values( $assets ) {
		$newAssets = [];

		foreach ($assets as $key => $value) {

			$newAssets[$key] = $value;

			if( $value['_type'] == 'content' ) { 

				$content = $value['content'];

				$newAssets[$key] = [
					'_type' 			=> $value['_type'],
					'content'			=> wpautop( $content )
				];

			}

			if( $value['_type'] == 'file' ) { 
				 
				$id = $value['file'];
				$url = wp_get_attachment_url( $id );
				$filename = basename( get_attached_file( $id ) );
				$filesize = filesize( get_attached_file( $id ) );
				$type = wp_check_filetype( $url );

				$newAssets[$key] = [
					'_type' 			=> $value['_type'],
					'file'				=> [
						'id' 			=> $id,
						'url' 			=> $url,
						'filename'		=> $filename,
						'filesize' 		=> $this->format_bytes( $filesize ),
						'type' 			=> $type['type'],
						'ext' 			=> $type['ext'] 
					]
				];

			}

			if( $value['_type'] == 'image' ) { 

				$id = $value['image'];
				$url = wp_get_attachment_url( $id ); 
				$meta = wp_get_attachment_metadata( $id, true );
				$filename = basename( get_attached_file( $id ) );
				$filesize = filesize( get_attached_file( $id ) );
				$type = wp_check_filetype( $url );

				$newAssets[$key] = [
					'_type' 			=> $value['_type'],
					'image'				=> [
						'id' 			=> $id,
						'filename'		=> $filename,
						'url' 			=> $url,
						'width' 		=> $meta['width'],
						'height' 		=> $meta['height'],
						'filesize' 		=> $this->format_bytes( $filesize ),
						'type' 			=> $type['type'],
						'ext' 			=> $type['ext'], 
						// 'meta'			=> $meta['image_meta']
					]
				];

			}

			if( $value['_type'] == 'video' ) { 

				$thumbnail_id = $value['thumbnail'];
				$thumbnail_url = wp_get_attachment_url( $thumbnail_id ); 

				$newAssets[$key] = [
					'_type' 			=> $value['_type'],
					'video'				=> [
						'url' 			=> $value['embed'],
						'thumbnail' 	=> [
							'id'		=> $thumbnail_id,
							'url'		=> $thumbnail_url
						]
					]
				];

			}

		}

		return $newAssets; 
	}

	private function format_bytes( $bytes ) { 
		$i = floor(log($bytes, 1024));
		return round($bytes / pow(1024, $i), [0,0,2,2,3][$i]).[' B',' kB',' MB',' GB',' TB'][$i];
	}

}