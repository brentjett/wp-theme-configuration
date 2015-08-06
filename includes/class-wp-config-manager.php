<?php
/**
* WP_Config_Manager Class
*
* This is the primary class that handles importing and looping over config file data.
* Process:
* 1. Collect all $filenames inside $paths of active plugins and themes.
* 2. Import each individual JSON file (and cache it) or fallback to it's previously cached version.
* 4. Run handlers for each dataset.
*
*/
class WP_Config_Manager {

    /**
    * The collected paths to JSON files that will be imported.
    * @var array
    */
    public $paths = array();

    /**
    * The objects created from JSON data
    * @var array
    */
    public $datasets = array();

    /**
    * Array of built-in handler class instances.
    * @var array
    */
    public $handlers = array();

    /**
    * Init the class. Setup path gathering.
    */
    function __construct() {
        $this->init_handlers();
        $this->paths = $this->collect_paths();
        $this->configure();
    }

    /**
    * Setup built-in handler classes and allow opportunity for external handlers to init.
    */
    function init_handlers() {

        $this->handlers["enqueue"] => new WP_Config_Enqueue_Handler;
    	$this->handlers["theme_support"] => new WP_Config_Theme_Support_Handler;
    	$this->handlers["meta_tags"] => new WP_Config_Meta_Tag_Handler;
    	
        // Fires to allow handlers to initialize.
    	do_action('wp_config/init_handlers');
    }

    /**
    * Collect Paths or Directories to Search.
    *
    * @return array $paths The paths found after searching themes and plugins.
    */
    function collect_paths() {

        // Filenames to search for.
        $filenames = array("wp-config.json");
        $directories = array();

        // Get paths for all filenames in active plugin roots
        $active_plugins = get_option('active_plugins');

        if (get_template_directory() != get_stylesheet_directory()) {
            $directories[] = get_template_directory();
        }
        $directories[] = get_stylesheet_directory();

        if (count($directories) > 1) {
            $dir_pattern  = '{' . implode(',', $directories) . '}';
        } else {
            $dir_pattern = $directories[0];
        }

        if (count($filenames) > 1) {
            $filenames_pattern  = '{' . implode(',', $filenames) . '}';
        } else {
            $filenames_pattern = $filenames[0];
        }

        $pattern = "$dir_pattern/$filenames_pattern";
        $paths = glob($pattern, GLOB_BRACE | GLOB_NOSORT);
        $additional_paths = apply_filters('wp_config/paths', array());
        $paths = array_merge($paths, $additional_paths);

        return $paths;
    }

    /**
    * Loop over all paths, import JSON data, and fire actions to pass data to handlers.
    */
    function configure() {

        if (isset($this->paths) && !empty($this->paths)) {
    		foreach($this->paths as $path) {
                if (file_exists($path)) {
            		$contents = file_get_contents($path);
            		$data = json_decode($contents);

            		if (isset($data)) {
            			$cache_updated = $this->cache($path, $data);
            		} else {
            			// json couldn't decode. check the cache
            			$data = $this->get_cache($path);
            		}

                    foreach($data as $key => $value) {
                        do_action("wp_config/key", $key);
            			do_action("wp_config/{$key}", $data, $file);
            		}

            	} else {
            		// File doesn't exist. @TODO: Report error.
            		return false;
            	}
    		}
    	}
    }

    function cache($path, $data)) {
        return update_option('wp_config/last_json', $data);
    }

    function get_last_cache($path) {
        return get_option('wp_config/last_json');
    }
}
?>
