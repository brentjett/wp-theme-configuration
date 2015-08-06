<?php

class WP_Config_Theme_Support_Handler {

	/**
	* @var array
	*/
	public $datasets = array();

	function __construct() {

		$this->datasets["theme_support"] = array();
		$this->datasets["nav_menus"] = array();

		// Collect data
		add_action('wp_config/theme_support', array($this, 'add_theme_support_data'), 10, 2);
		add_action('wp_config/nav_menus', array($this, 'add_nav_menus_data'), 10, 2);

		// Configure
		add_action('after_setup_theme', array($this, "configure"));
	}

	function add_theme_support_data($data, $path) {

		$this->datasets["theme_support"][] = array(
			"data" => $data->theme_support,
			"path" => $path
		);
	}

	function add_nav_menus_data($data, $path) {
		$this->datasets["nav_menus"][] = array(
			"data" => $data->nav_menus,
			"path" => $path
		);
	}

	function configure() {
		$datasets = $this->datasets;

		foreach($datasets["theme_supports"] as $dataset) {
			$theme_supports = $dataset["data"];
			$path = $dataset["path"];

			// Add Title Tag Support
			if (isset($theme_supports->title_tag)) {
				add_theme_support('title-tag');
			}

			// HTML5 Support
			if (!empty($theme_supports->html5)) {

				$supports = $theme_supports->html5;
				add_theme_support('html5', $supports);
			}

			// Automatic Feed Links
			if (isset($theme_supports->automatic_feed_links)) {
				add_theme_support('automatic-feed-links');
			}

			// Post Formats
			if (!empty($theme_supports->post_formats)) {
				add_theme_support('post-formats', $theme_supports->post_formats);
			}

			// Featured Images
			$args = $theme_supports->post_thumbnails;
			if (isset($args)) {
				if (is_array($args)) {
					add_theme_support('post-thumbnails', $args);
				} else {
					add_theme_support('post-thumbnails');
				}
			}

			// Custom Background
			if ($bg = $theme_supports->custom_background) {
				$bg_args = array(
					'default-color'          => $bg->default_color,
					'default-image'          => $bg->default_image,
					'default-repeat'         => $bg->default_repeat,
					'default-position-x'     => $bg->default_position_x,
					'default-attachment'     => $bg->default_attachment
				);
				if (is_callable($bg->wp_head_callback)) {
					$bg_args['wp-head-callback'] = $bg->wp_head_callback;
				}
				if (is_callable($bg->admin_head_callback)) {
					$bg_args['admin-head-callback'] = $bg->admin_head_callback;
				}
				if (is_callable($bg->admin_preview_callback)) {
					$bg_args['admin-preview-callback'] = $bg->admin_preview_callback;
				}

				add_theme_support('custom-background', $bg_args);
			}

			// Custom Header
			if ($header = $theme_supports->custom_header) {
				array(
					'default-image'          => $header->default_image,
					'width'                  => $header->width,
					'height'                 => $header->height,
					'flex-height'            => $header->flex_height,
					'flex-width'             => $header->flex_width,
					'uploads'                => $header->uploads,
					'random-default'         => $header->random_default,
					'header-text'            => $header->header_text,
					'default-text-color'     => $header->default_text_color
				);
				if (is_callable($header->wp_head_callback)) {
					$bg_args['wp-head-callback'] = $header->wp_head_callback;
				}
				if (is_callable($header->admin_head_callback)) {
					$bg_args['admin-head-callback'] = $header->admin_head_callback;
				}
				if (is_callable($header->admin_preview_callback)) {
					$bg_args['admin-preview-callback'] = $header->admin_preview_callback;
				}
				add_theme_support( 'custom-header', $defaults );
			}

			//@TODO: Add support for arbitrary theme supports. If not a known key, simply call as add_theme_support($key) or add_theme_support($key, $data)
		}

		foreach($datasets["nav_menus"] as $dataset) {
			$nav_menus = $dataset["data"];
			$path = $dataset["path"];

			if (!empty($nav_menus)) {
				foreach($nav_menus as $handle => $label) {
					register_nav_menu($handle, $label);
				}
			}
		}
	}
}
?>
