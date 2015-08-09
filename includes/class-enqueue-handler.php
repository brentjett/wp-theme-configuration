<?php
/**
* Setup Stylesheet & Script Queuing.
*/
class WP_Config_Enqueue_Handler {

    /**
    * All collected stylesheets
    * @var array
    */
    public $stylesheets = array();

    /**
    * All collected scripts
    * @var array
    */
    public $scripts = array();

    /**
    * Setup actions
    */
    function __construct() {
        add_action('wp_config/stylesheets', array($this, "prepare"), 10, 3);
        add_action('wp_config/scripts', array($this, "prepare"), 10, 3);
        add_action('wp_enqueue_scripts', array($this, 'configure'));
    }

    /**
	* Collect and store each file's script and stylesheet data
    * @param object containing data
    * @param string path to file containing data
    * @param string property name
    */
    function prepare($data, $path, $key) {

        if ($key == 'stylesheets') {
            foreach($data as $handle => $stylesheet) {
                $stylesheet = (array) $stylesheet;
                $stylesheet['handle'] = $handle;
                $stylesheet['origin-file'] = $path;
                $this->stylesheets[$handle] = $stylesheet;
            }
        }
        if ($key == 'scripts') {
            foreach($data as $handle => $script) {
                $script = (array) $script;
                $script['handle'] = $handle;
                $script['origin-file'] = $path;
                $this->scripts[$handle] = $script;
            }
        }
    }

    /**
    *
    */
    function configure() {

        if (!empty($this->stylesheets)) {
            foreach($this->stylesheets as $handle => $stylesheet) {
                if (is_bool($stylesheet) && $stylesheet) {
                    wp_enqueue_style($handle);
                } else {

                    $active_callback = $stylesheet['active-callback'];
                    if (isset($active_callback) && is_callable($active_callback)) {
                        $active = call_user_func($active_callback);
                        if (!$active) continue;
                    }

                    $src = $this->get_uri($stylesheet['path'], $stylesheet['origin-file']);
                    $dependencies = $stylesheet['dependencies'];
                    $version = $stylesheet['version'];
                    $media = $stylesheet['media'];
                    wp_enqueue_style($handle, $src, $dependencies, $version, $media);
                }
            }
        }
        if (!empty($this->scripts)) {
            foreach($this->scripts as $handle => $script) {
                if (is_bool($script) && $script) {
                    wp_enqueue_script($handle);
                } else {

                    $active_callback = $script['active-callback'];
                    if (isset($active_callback) && is_callable($active_callback)) {
                        $active = call_user_func($active_callback);
                        if (!$active) continue;
                    }

                    $src = $this->get_uri($script['path'], $script['origin-file']);
                    $dependencies = $script['dependencies'];
                    $version = $script['version'];
                    $in_footer = $script['in-footer'];
                    wp_enqueue_script($handle, $src, $dependences, $version, $in_footer);
                }
            }
        }
    }

    /**
    * Determine uri for script or stylesheet based on originating directory.
    */
    function get_uri($path, $origin) {
        if ($this->is_path_external($path)) {
            return $path;
        } else {
            if (dirname($origin) == get_template_directory()) {
                return get_template_directory_uri() . '/' . $path;
            }
            if (dirname($origin) == get_stylesheet_directory()) {
                return get_stylesheet_uri() . '/' . $path;
            }
            if ($path = plugins_url($path, $origin)) {
                return $path;
            }
            return $path;
        }
    }

    function is_path_external($path) {
        $path_data = parse_url($path);
        if (isset($path_data['scheme'])) {
            if ($path_data['scheme'] == 'http' || $path_data['scheme'] == 'https') return true;
            // Scheme supports http or https, not //
        }
        return false;
    }




    // Enqueue editor stylesheets
    function enqueue_editor_stylesheets($config, $file) {
        $styles = $config->editor_styles;
        if (!empty($styles)) {
            foreach($styles as $style) {
                add_editor_style($style);
            }
        }
    }

    function enqueue($action = "enqueue", $type = 'style', $handle, $data = null) {

        // run conditional callback
        if (!empty($data->active_callback)) {

            $fn = array_shift($data->active_callback);
            if (is_callable($fn)) {
                $active = call_user_func_array($fn, $data->active_callback);
                if (!$active) return;
            }
        }

        if (!$path = $data->path) return;

        if (!wp_config_path_is_external($data->path)) {

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
}
?>
