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
    * Enqueue scripts and stylesheets on wp_enqueue_scripts
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
    * @param string relative path to the file
    * @param string absolute path to the json file that declares the script or stylesheet
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

    /**
    * Test if a path is an external url
    * @param string path
    */
    function is_path_external($path) {
        $path_data = parse_url($path);
        if (isset($path_data['scheme'])) {
            if ($path_data['scheme'] == 'http' || $path_data['scheme'] == 'https') return true;
            // Scheme supports http or https, not //
        }
        return false;
    }
}
?>
