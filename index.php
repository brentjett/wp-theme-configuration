<?php
/*
Plugin Name: WP Theme Configuration API
Version: 0.9
Author: Brent Jett
Description: A fast theme configuration API that looks for a config.json file in specified directories and initializes your WordPress theme.
*/

require_once 'theme_support.php'; // Handle adding theme support features
require_once 'enqueue.php'; // Handle Register/Enqueue Scripts & Stylesheets
require_once 'admin/tools-config.php'; // Testing admin page

// On Init, Get Paths and configure.
add_action('init', function() {
	$paths = apply_filters('wp_config/paths', array()); // Get all paths to look for config files in.
	if (!empty($paths)) {
		foreach($paths as $path) {

			/*
			@TODO: Test full paths vs. directories. If it's a directory, look for config.json files inside.
			*/

			// init configuration
			wp_json_config($path);
		}
	}
});

add_filter('wp_config/paths', function($paths) {
	$paths = array(get_stylesheet_directory() . '/config.json');
	return $paths;
});

function wp_json_config($file) {

	if (file_exists($file)) {
		$contents = file_get_contents($file);
		$data = json_decode($contents);

		if (isset($data)) {
			// Kick off config tasks
			do_action('wp_config', $data, $file);
			// add data to cache
			update_option('wp_config/last_json', $data);
			return $data;
		} else {
			// json couldn't decode. check the cache
			$data = get_option('wp_config/last_json');
			do_action('wp_config', $data, $file);
			return $data;
		}
	} else {
		// File doesn't exist. @TODO: Report error.
		return false;
	}
	return;
}

// Use Object to setup config tasks
add_action('wp_config', function($config, $file) {

	if (!empty($config)) {
		foreach($config as $key => $value) {
			do_action("wp_config/{$key}", $config, $file);
		}
	}

}, 0, 2);


// Add Meta Tags To <head>
add_action('wp_config/meta_tags', function($config, $file) {

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
add_action('wp_config/nav_menus', function($config, $file) {

	if (!empty($config->nav_menus)) {
		foreach($config->nav_menus as $handle => $label) {
			register_nav_menu($handle, $label);
		}
	}
}, 10, 2);
?>
