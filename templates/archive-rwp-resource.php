<?php
/**
 * Template part for displaying custom post type posts.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package Wp_Resources
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly 

if ( class_exists('Resources_Wp_Admin') ) {
    $settings = Resources_Wp_Admin::get_settings();
    $custom_header_name = $settings['custom_header_name'];
    $custom_footer_name = $settings['custom_footer_name'];  
}

get_header( $custom_header_name ); ?> 

<div id="<?php echo esc_html( RESOURCES_WP_KEY . '-resources-app' ); ?>"></div>   

<?php get_footer( $custom_footer_name ); ?>