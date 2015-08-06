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

    private $filenames = array();
    private $directories = array();
    private $paths = array();
    private $datasets = array();

    /**
    * Init the class. Setup path gathering.
    */
    function __construct() {
        $this->collect_paths();
        $this->configure_paths();
    }

    /**
    * Collect Paths or Directories to Search.
    */
    function collect_paths() {

        // Filenames to search for
        $filenames = array("config.json", "wp-config.json");
        $this->filenames = apply_filters('wp_config/filenames', $filenames);

        // Look in the active theme root.
        $directories = array();

        if (get_template_directory() != get_stylesheet_directory()) {
            $directories[] = get_template_directory();
        }
        $directories[] = get_stylesheet_directory();
        $this->directories = apply_filters('wp_config/directories', $directories);


        // Get paths for all filenames in active plugin roots
        $active_plugins = get_option('active_plugins');
        brj_print_r($active_plugins, "Active Plugins");

        // Get paths to files in the active theme root
        // If active theme has parent theme, check for files in parent theme root

        // Build search pattern from arrays $directories and $filenames.
        $directories_pattern  = '{' . implode(',', $this->directories) . '}';
        $filenames_pattern = '{' . implode(',', $this->filenames) . '}';
        $pattern = "$directories_pattern/$filenames_pattern";
        brj_print_r($pattern, "Pattern");

        $paths = glob($pattern, GLOB_BRACE | GLOB_NOSORT);
        $this->paths = apply_filters('wp_config/paths', $paths);
        brj_print_r($this->paths, "Found Paths");
    }

    function configure_paths() {

        if (isset($this->paths) && !empty($this->paths)) {
    		foreach($this->paths as $path) {

    			/*
    			@TODO: Test full paths vs. directories. If it's a directory, look for config.json files inside.
    			*/
    			$this->configure_path($path);
    		}
    	}
    }

    function configure_path($path) {
        if (file_exists($path)) {
    		$contents = file_get_contents($path);
    		$data = json_decode($contents);

    		if (isset($data)) {
    			// Kick off config tasks
    			$this->configure_by_key($data, $path);
    			// add data to cache
    			update_option('wp_config/last_json', $data);
    			return $data;
    		} else {
    			// json couldn't decode. check the cache
    			$data = get_option('wp_config/last_json');
    			$this->configure_by_key($data, $path);
    			return $data;
    		}
    	} else {
    		// File doesn't exist. @TODO: Report error.
    		return false;
    	}
    	return;
    }

    function configure_by_key($data, $path) {

        if (!empty($data)) {
    		foreach($data as $key => $value) {
                do_action("wp_config/key", $key);
    			do_action("wp_config/{$key}", $data, $file);
    		}
    	}
    }

}
?>
