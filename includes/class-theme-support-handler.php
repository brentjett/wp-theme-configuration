<?php
/**
* Class to handle the configuration of "theme_support" and "nav_menus" properties in JSON files.
*/
class WP_Config_Theme_Support_Handler {

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
			'title-tag',
			'html5',
			'automatic-feed-links',
			'post-formats',
			'post-thumbnails',
			'custom-background',
			'custom-header'
		);

		// Collect data
		add_action('wp_config/theme-support', array($this, 'prepare'), 10, 3);
		add_action('wp_config/nav-menus', array($this, 'prepare'), 10, 3);

		// Configure
		add_action('after_setup_theme', array($this, "configure"));
	}

	/**
	* Collect and store each file's theme support and nav menu data
    * @param object containing theme support or nav menu data
    * @param string path to file containing data
    * @param string either theme-support or nav-menus
    */
	function prepare($data, $path, $key) {

		if ($key == 'theme-support'){
			foreach($data as $key => $args) {

				if ($key == 'title_tag' || $key == 'title-tag') {
					$this->theme_supports['title-tag'] = $args;
				}
				if ($key == 'html5') {
					$this->theme_supports['html5'] = $args;
				}
				if ($key == 'automatic-feed-links') {
					$this->theme_supports['automatic-feed-links'] = $args;
				}
				if ($key == 'post-thumbnails') {
					$this->theme_supports['post-thumbnails'] = $args;
				}
				if ($key == 'post-formats') {
					$this->theme_supports['post-formats'] = $args;
				}
				if ($key == 'custom-background') {
					$args = (array) $args;
					if (!empty($args)) {
						$this->theme_supports["custom-background"] = $args;
					}
				}
				if ($key == 'custom-header') {
					$args = (array) $args;
					if (!empty($args)) {
						$this->theme_supports["custom-header"] = $args;
					}
				}

				// Keep arbitrary keys
				if (!in_array($key, $this->known_supports)) {
					if (isset($args) && is_object($args)) {
						$args = (array) $args;
					}
					$this->theme_supports[$key] = $args;
				}
			}
		}

		if ($key == 'nav-menus') {
			if (!empty($data)) {
				foreach($data as $handle => $label) {
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

		// Configure Nav Menus
		if (!empty($this->nav_menus)) {
			register_nav_menus($this->nav_menus);
		}

		// Support Title Tag
		if (isset($this->theme_supports['title-tag'])) {
			add_theme_support('title-tag');
		}

		// Support html5 Elements
		if (isset($this->theme_supports["html5"])) {
			add_theme_support('html5', $this->theme_supports['html5']);
		}

		// Support Automatic Feed Links
		if (isset($this->theme_supports["automatic-feed-links"])) {
			add_theme_support('automatic-feed-links');
		}

		// Support Post Thumbnails
		if (isset($this->theme_supports["post-thumbnails"])) {
			add_theme_support('post-thumbnails');
		}

		// Support Auto Feed Links
		if (isset($this->theme_supports["post-formats"])) {
			add_theme_support('post-formats', $this->theme_supports["post-formats"]);
		}

		// Custom Background
		if (isset($this->theme_supports["custom-background"])) {
			add_theme_support('custom-background', $this->theme_supports['custom-background']);
		}

		// Custom Header
		if (isset($this->theme_supports["custom-header"])) {
			add_theme_support('custom-header', $this->theme_supports["custom-header"]);
		}

		/*
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
		*/
	}
}
?>
