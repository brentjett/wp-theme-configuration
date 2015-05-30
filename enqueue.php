<?php
add_action('basset/theme_config/styles', function($config, $file) {

    if (!empty($config->register_styles)) {
        foreach($config->register_styles as $handle => $style) {
            basset_enqueue('register', 'style', $handle, $style);
        }
    }
    if (!empty($config->enqueue_styles)) {
        foreach($config->enqueue_styles as $handle => $style) {
            basset_enqueue('enqueue', 'style', $handle, $style);
        }
    }
    if (!empty($config->register_scripts)) {
        foreach($config->register_scripts as $handle => $script) {
            basset_enqueue('register', 'script', $handle, $script);
        }
    }
    if (!empty($config->enqueue_scripts)) {
        foreach($config->enqueue_scripts as $handle => $script) {
            basset_enqueue('enqueue', 'script', $handle, $script);
        }
    }

}, 10, 2);

function basset_enqueue($action = "enqueue", $type = 'style', $handle, $data = null) {

    // run conditional callback
    if (!empty($data->active_callback)) {

        $fn = array_shift($data->active_callback);
        if (is_callable($fn)) {
            $active = call_user_func_array($fn, $data->active_callback);
            if (!$active) return;
        }
    }

    // wp_register_style( $handle, $src, $deps, $ver, $media );
    // wp_enqueue_style( $handle, $src, $deps, $ver, $media );
    // wp_enqueue_script( $handle, $src, $deps, $ver, $in_footer );
    // wp_register_script( $handle, $src, $deps, $ver, $in_footer );

    if (!$path = $data->path) return;

    if (!basset_path_is_external($data->path)) {

        $path = get_stylesheet_directory_uri() . '/' . $data->path;

        if (file_exists(get_stylesheet_directory_uri() . '/' . $data->path)) {
            $path = get_stylesheet_directory_uri() . '/' . $data->path;

        } else if (file_exists(get_template_directory_uri() . '/' . $data->path)) {
            // check parent theme
            $path = get_template_directory_uri() . '/' . $data->path;

        }
    }

    if (!$dependancies = $data->dependancies) {
        $dependancies = array();
    }

    $version = $data->version;

    if ($type == 'style') {
        $media_footer = $data->media;
    } else {
        $media_footer = $data->in_footer;
    }

    $function_name = "wp_" . $action . "_" . $type;
    if (is_callable($function_name)) {
        call_user_func($function_name, $handle, $path, $dependancies, $version, $media_footer);
    }
}

// Enqueue editor stylesheets
add_action('basset/theme_config/editor_styles', function($config, $file) {
    $styles = $config->editor_styles;
    if (!empty($styles)) {
        foreach($styles as $style) {
            add_editor_style($style);
        }
    }
}, 10, 2);

function basset_path_is_external($path) {
    $path_data = parse_url($path);
    if ($path_data['scheme'] == 'http' || $path_data['scheme'] == 'https') return true; // Scheme supports http or https, not //
    return false;
}
?>
