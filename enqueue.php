<?php
function basset_setup_library_enqueues($config, $file) {

    add_action('wp_enqueue_scripts', function() use ($config, $file) {
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
    });

}
add_action('basset/theme_config/register_styles', 'basset_setup_library_enqueues', 10, 2);
add_action('basset/theme_config/enqueue_styles', 'basset_setup_library_enqueues', 10, 2);
add_action('basset/theme_config/register_scripts', 'basset_setup_library_enqueues', 10, 2);
add_action('basset/theme_config/enqueue_scripts', 'basset_setup_library_enqueues', 10, 2);


function basset_enqueue($action = "enqueue", $type = 'style', $handle, $data = null) {

    // run conditional callback
    if (!empty($data->active_callback)) {

        $fn = array_shift($data->active_callback);
        if (is_callable($fn)) {
            $active = call_user_func_array($fn, $data->active_callback);
            if (!$active) return;
        }
    }

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

    if (!isset($data->dependancies)) {
        $dependancies = array();
    } else {
        $dependancies = $data->dependancies;
    }

    if (isset($data->version)) {
        $version = $data->version;
    } else {
        $version = false;
    }

    if ($type == 'style') {
        if (isset($data->media)) {
            $media_footer = $data->media;
        } else {
            $media_footer = false;
        }
    } else {
        if (isset($data->in_footer)) {
            $media_footer = $data->in_footer;
        } else {
            $media_footer = false;
        }
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
    if (isset($path_data['scheme'])) {
        if ($path_data['scheme'] == 'http' || $path_data['scheme'] == 'https') return true; // Scheme supports http or https, not //
    }
    return false;
}
?>
