<?php

add_action('basset/theme_config/theme_supports', function($config) {
	$theme_supports = $config->theme_support;

	// Add Title Tag Support
	if ($theme_supports->title_tag) {
		add_theme_support('title-tag');
	}

	// HTML5 Support
	if (!empty($theme_supports->html5)) {

		$supports = $theme_supports->html5;
		add_theme_support('html5', $supports);
		//foreach($supports as $support) { print "added support for $support\n"; }
	}

	// Automatic Feed Links
	if ($theme_supports->automatic_feed_links) {
		add_theme_support('automatic-feed-links');
	}

	// Post Formats
	if (!empty($theme_supports->post_formats)) {
		add_theme_support('post-formats', $theme_supports->post_formats);
	}

	// Featured Images
	if ($args = $theme_supports->post_thumbnails) {
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
});

?>
