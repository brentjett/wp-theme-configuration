<?php
/*
Plugin Name: WP Theme Configuration API
Version: 0.8
Author: Brent Jett
Description: A fast theme configuration API that looks for a config.json file in specified directories and initializes your WordPress theme.
*/

require_once 'theme_supports.php'; // Handle adding theme support features
require_once 'enqueue.php'; // Handle Register/Enqueue Scripts & Stylesheets

// On Init, Get Paths and configure.
add_action('init', function() {
	$paths = apply_filters('config/paths', array()); // Get all paths to look for config files in.

	if (!empty($paths)) {
		foreach($paths as $path) {
			basset_config($path);
		}
	} else {
		// No Paths Were Returned
	}
});

function basset_config($file) {
	global $basset;

	if (file_exists($file)) {
		$contents = file_get_contents($file);
		$data = json_decode($contents);
		if (!is_null($data)) {
			// Kick off config tasks
			do_action('basset/theme_config', $data, $file);
			// add data to cache
			update_option('basset_config/last_json', $data);
		} else {
			// json couldn't decode. check the cache
			$basset ? $basset->add_issue("JSON couldn't decode. Check Syntax.") : null ;
			$data = get_option('basset_config/last_json');
			do_action('basset/theme_config', $data, $file);
		}
	} else {
		// File doesn't exist. @TODO: Report non-failing error.
	}
}

// Use Object to setup config tasks
add_action('basset/theme_config', function($config, $file) {

	if ($config) {
		foreach($config as $key => $value) {
			//do_action('basset/theme_config/theme_supports', $config, $file);
		}
	}


	do_action('basset/theme_config/theme_supports', $config, $file);
	// add image sizes
	do_action('basset/theme_config/nav_menus', $config, $file);
	// register sidebars
	// register custom post types
	// register custom taxonomies


	add_action('wp_enqueue_scripts', function() use ($config, $file) {
		// Enqueue/Register stylesheets and scripts
		do_action('basset/theme_config/styles', $config, $file);
	}, 10, 2);

	add_action('admin_init', function() use ($config, $file) {
		do_action('basset/theme_config/editor_styles', $config, $file);
	});

	add_action('wp_head', function() use($config, $file) {
		do_action('basset/theme_config/meta_tags', $config, $file);
	}, 10, 2);

}, 0, 2);


// Add Meta Tags To <head>
add_action('basset/theme_config/meta_tags', function($config, $file) {
	if (!empty($config->meta_tags)) {

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

		print "<!-- End Basset Enqueued Meta Tags -->\n\n";
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

// Add config admin menu item
add_action( 'wp_before_admin_bar_render', function() {
	global $wp_admin_bar, $template;

	$args = array(
		'id'     => 'basset_config',
		'title'  => __( 'Configuration', 'basset' ),
		'meta'   => array(),
	);
	//$wp_admin_bar->add_menu( $args );
});
?>
