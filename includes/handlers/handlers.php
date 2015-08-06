<?php
// Built in feature handlers.

require_once 'class-enqueue-handler.php';
require_once 'class-theme-support-handler.php';

function wp_config_init_builtin_handlers() {
    $enqueue = new WP_Config_Enqueue_Handler;
    $theme_support = new WP_Config_Theme_Support_Handler;

    add_action('wp_config/nav_menus', 'wp_config_nav_menu_handler', 10, 2);
    add_action('wp_config/meta_tags', 'wp_config_meta_tag_handler', 10, 2);
}
//add_action('wp_config/init', 'wp_config_init_builtin_handlers');


// Simple Nav Menu location Handler
function wp_config_nav_menu_handler($config, $file) {

	if (!empty($config->nav_menus)) {
		foreach($config->nav_menus as $handle => $label) {
			register_nav_menu($handle, $label);
		}
	}
}

// Meta Tag Handler
function wp_config_meta_tag_handler($config, $file) {

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
}
?>
