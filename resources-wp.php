<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://www.dystrick.com
 * @since             1.0.0
 * @package           Resources_Wp
 *
 * @wordpress-plugin
 * Plugin Name:       Resources WP
 * Plugin URI:        https://www.wp-resources.com
 * Description:       A plugin and play resource library
 * Version:           1.0.0
 * Author:            dystrick
 * Author URI:        https://www.dystrick.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       resources-wp
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'RESOURCES_WP_VERSION', '1.0.0' );
define( 'RESOURCES_WP_KEY', 'rwp' );  

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-resources-wp-activator.php
 */
function resources_wp_activate() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-resources-wp-activator.php';
	Resources_Wp_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-resources-wp-deactivator.php
 */
function resources_wp_deactivate() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-resources-wp-deactivator.php';
	Resources_Wp_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'resources_wp_activate' );
register_deactivation_hook( __FILE__, 'resources_wp_deactivate' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-resources-wp.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function resources_wp_run() {

	$plugin = new Resources_Wp();
	$plugin->run();

}
resources_wp_run();
