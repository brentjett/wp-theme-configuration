<?php
/*
Plugin Name: WP Theme Configuration API
Version: 0.9
Author: Brent Jett
Description: A fast theme configuration API that looks for a config.json file in specified directories and initializes your WordPress theme.
*/

require_once 'theme_support.php'; // Handle adding theme support features
require_once 'enqueue.php'; // Handle Register/Enqueue Scripts & Stylesheets

// On Init, Get Paths and configure.
add_action('init', function() {
	$paths = apply_filters('basset/theme_config/paths', array()); // Get all paths to look for config files in.

	if (!empty($paths)) {
		foreach($paths as $path) {
			wp_json_config($path);
		}
	} else {
		// No Paths Were Returned
	}
});

function wp_json_config($file) {
	global $basset;

	if (file_exists($file)) {
		$contents = file_get_contents($file);
		$data = json_decode($contents);
		if (isset($data)) {
			// Kick off config tasks
			do_action('basset/theme_config', $data, $file);
			$basset ? $basset->config_data = $data : null;
			// add data to cache
			update_option('basset_config/last_json', $data);
		} else {
			// json couldn't decode. check the cache
			$basset ? $basset->add_issue("JSON couldn't decode. Check Syntax.") : null ;
			$data = get_option('basset_config/last_json');
			do_action('basset/theme_config', $data, $file);
		}
	} else {
		// File doesn't exist. @TODO: Report error.
		$basset ? $basset->add_issue("Config file doesn't exist at path") : null ;
	}
}

// Use Object to setup config tasks
add_action('basset/theme_config', function($config, $file) {

	if (!empty($config)) {
		foreach($config as $key => $value) {
			do_action("basset/theme_config/{$key}", $config, $file);
		}
	}

}, 0, 2);


// Add Meta Tags To <head>
add_action('basset/theme_config/meta_tags', function($config, $file) {

	if (!empty($config->meta_tags)) {
		add_action('wp_head', function() use($config, $file) {
			print "\n<!-- Basset Enqueued Meta Tags -->\n";
			foreach($config->meta_tags as $name => $data) {

				$charset = $http_equiv = $content = null;

				if ($name) {
					$name = "name='$name' ";
				}
				if (is_string($data)) {
					$content = "content='$data' ";
				}
				if (is_object($data) && !empty($data)) {
					if (!empty($data->content)) {
						$content = "content='$data->content' ";
					}
					if (!empty($data->charset)) {
						$charset = "charset='$data->charset' ";
					}
					if (!empty($data->{'http-equiv'})) {
						$http_equiv = "http-equiv='" . $data->{'http-equiv'} . "' ";
					}
				}
				print "<meta " . $name . $charset . $http_equiv . $content . ">\n";
			}

			print "<!-- End Enqueued Meta Tags -->\n\n";
		});
	}
}, 10, 2);

// Setup Nav Menu Locations
add_action('basset/theme_config/nav_menus', function($config, $file) {

	if (!empty($config->nav_menus)) {
		foreach($config->nav_menus as $handle => $label) {
			register_nav_menu($handle, $label);
		}
	}
}, 10, 2);


function basset_print_action() {
	return;
}
?>
