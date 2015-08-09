<?php
/*
Plugin Name: WP Theme Configuration API
Version: 0.1
Author: Brent Jett
Author URI: https://about.me/brent.jett
Description: A JSON theme configuration API that looks for a wp-config.json file in active theme and plugin directories and configures WordPress.
Text Domain: wp_config
License: GPL v2 or later
*/

define( 'WP_CONFIG_API_DIR', dirname( __FILE__ ));
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

require_once WP_CONFIG_API_DIR . '/includes/class-wp-config-manager.php';
require_once WP_CONFIG_API_DIR . '/includes/class-enqueue-handler.php';
require_once WP_CONFIG_API_DIR . '/includes/class-theme-support-handler.php';
require_once WP_CONFIG_API_DIR . '/includes/class-meta-tags-handler.php';

// On Init, Setup Manager Object.
function wp_config_init() {
	$GLOBALS['wp_config_manager'] = new WP_Config_Manager;
}
add_action('plugins_loaded', 'wp_config_init');
?>
