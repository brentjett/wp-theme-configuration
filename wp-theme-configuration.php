<?php
/*
Plugin Name: WP Theme Configuration API
Version: 0.1
Author: Brent Jett
Author URI: https://about.me/brent.jett
Description: A fast theme configuration API that looks for a config.json file in specified directories and initializes your WordPress theme.
Text Domain: wp_config
License: GPL v2 or later
*/

define( 'WP_CONFIG_API_DIR', dirname( __FILE__ ));

require_once WP_CONFIG_API_DIR . 'includes/class-wp-config-manager.php';
require_once WP_CONFIG_API_DIR . 'includes/class-enqueue-handler.php';
require_once WP_CONFIG_API_DIR . 'includes/class-theme-support-handler.php';
require_once WP_CONFIG_API_DIR . 'includes/class-meta-tags-handler.php';

// On Init, Setup Manager Object.
function wp_config_init() {
	$GLOBALS['wp_config_manager'] = new WP_Config_Manager;
}
add_action('init', 'wp_config_init');
?>
