<?php
/**
* Class to handle the configuration of "theme_support" and "nav_menus" properties in JSON files.
*/
class WP_Config_Theme_Support_Handler {

	/**
	* @var array
	*/
	public $datasets = array();

	/**
	* Array of handle => label for each nav menu
	* @var array
	*/
	public $nav_menus = array();

	/**
	* Array of theme support features
	* @var array
	*/
	public $theme_supports = array();

	/**
	* Array of built-in support keys - This is to allow for arbitrary keys later
	* @var array
	*/
	public $known_supports = array();

	/**
	* Setup datasets array and actions to listen for config data
	*/
	function __construct() {

		$this->known_supports = array(
			'title_tag',
			'html5',
			'automatic_feed_links',
			'post_formats',
			'post_thumbnails',
			'custom_background',
			'custom_header'
		);

		$this->datasets["theme_support"] = array();
		$this->datasets["nav_menus"] = array();

		// Collect data
		add_action('wp_config/theme_support', array($this, 'prepare'), 10, 3);
		add_action('wp_config/nav_menus', array($this, 'prepare'), 10, 3);

		// Configure
		add_action('after_setup_theme', array($this, "configure"));
	}

	/**
	* Collect and store each file's theme support and nav menu data.
	*/
	function prepare($data, $path, $key) {

		if ($key == 'theme_support'){
			$this->datasets["theme_support"][] = array(
				"data" => $data->theme_support,
				"path" => $path
			);
			foreach($data->theme_support as $key => $args) {

				// Handle data and prepare $this->theme_supports
			}
		}

		if ($key == 'nav_menus') {
			$this->datasets["nav_menus"][] = array(
				"data" => $data->nav_menus,
				"path" => $path
			);
			if (!empty($data->nav_menus)) {
				foreach($data->nav_menus as $handle => $label) {
					$this->nav_menus[$handle] = $label;
				}
			}
		}
	}

	/**
	* Called on after_setup_theme to configure data collected in
	* collect_theme_support_data() and collect_nav_menus_data()
	*/
	function configure() {

		$datasets = $this->datasets;

		// Configure Nav Menus
		if (!empty($this->nav_menus)) {
			register_nav_menus($this->nav_menus);
		}

		return;

		if (!empty($datasets["theme_support"])) {
			foreach($datasets["theme_support"] as $dataset) {
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
		}
	}
}
?>
