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

define("BRJ_DEBUG", false);

require_once 'includes/class-wp-config-manager.php';

// Include Built-in feature handlers
require_once 'includes/handlers/handlers.php';

// On Init, Setup Manager Object.
function wp_config_init() {
	$manager = new WP_Config_Manager;

	// Fires to allow handlers to initialize.
	do_action('wp_config/init');
}
add_action('plugins_loaded', 'wp_config_init');


// Pretty print an object

function brj_print_r($var, $label = "") {
	if (!BRJ_DEBUG) return;
	print "<pre>";
	if (isset($label)) print $label . "\n";
	print_r($var);
	print "</pre>";
}
?>
