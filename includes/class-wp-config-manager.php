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
    * Array containing all data from last import cycle.
    * @var array
    */
    public $last_datasets = array();

    /**
    * Array of built-in handler class instances.
    * @var array
    */
    public $handlers = array();

    /**
    * duration to use when caching JSON data
    * @var int - number of seconds to keep cache
    */
    public $cache_duration = 7 * DAY_IN_SECONDS;

    /**
    *
    */
    public $error_log = array();

    /**
    * Init the class. Setup path gathering.
    */
    function __construct() {
        $this->init_handlers();
        add_action('init', array($this, "collect_paths"));
        add_action('wp_config/configure', array($this, "configure"));
        add_action('admin_notices', array($this, 'print_admin_notices'));
    }

    /**
    * Setup built-in handler classes and allow opportunity for external handlers to init.
    */
    function init_handlers() {

        if (class_exists('WP_Config_Enqueue_Handler')) {
            $this->handlers["enqueue"] = new WP_Config_Enqueue_Handler;
        }
        if (class_exists('WP_Config_Theme_Support_Handler')) {
        	$this->handlers["theme_support"] = new WP_Config_Theme_Support_Handler;
        }
        if (class_exists('WP_Config_Meta_Tag_Handler')) {
        	$this->handlers["meta_tags"] = new WP_Config_Meta_Tag_Handler;
        }

        // Fires to allow handlers to initialize.
    	do_action('wp_config/init_handlers');
    }

    /**
    * Collect valid paths to wp-config.json files and populate $this->paths.
    */
    function collect_paths() {

        // Filenames to search for.
        $filenames = array("wp-config.json");

        // Check directories, starting with must use plugins (/wp-content/mu-plugins)
        $directories = array();
        $directories[] = WPMU_PLUGIN_DIR;

        // Check active plugin directories
        $active_plugin_dirs = get_option('active_plugins');
        $active_plugin_dirs = array_map(array($this, "format_plugin_path"), $active_plugin_dirs);
        $directories = array_merge($directories, $active_plugin_dirs);

        // Check active or parent theme directory
        $directories[] = get_template_directory();

        // If child theme, check there last.
        if (get_template_directory() != get_stylesheet_directory()) {
            $directories[] = get_stylesheet_directory();
        }

        // Assemble file detection pattern
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

        $this->paths = $paths;
        do_action('wp_config/configure');
    }

    /**
    * Loop over all paths, import JSON data, and fire actions to pass data to handlers.
    */
    function configure() {

        $this->last_datasets = get_transient('wp_config/last_all_json');

        if (isset($this->paths) && !empty($this->paths)) {
    		foreach($this->paths as $path) {
                if (file_exists($path)) {
            		$contents = file_get_contents($path);
            		$data = json_decode($contents);

                    if (isset($data)) {
                        $this->datasets[$path] = $data;
                    } elseif (isset($this->last_datasets[$path])) {
                        $data = $this->last_datasets[$path];
                        // Log that there was an error with this file
                        $this->error_log[] = array(
                            "message" => __('Import Error', 'wp_config'),
                            "reference" => $path,
                            "display_in_admin" => true
                        );
                    } else {
                        // Data could not be retrieved
                    }

                    /*
            		if (isset($data)) {
            			$cache_updated = $this->cache($path, $data);
            		} else {
            			// json couldn't decode. check the cache
            			$data = $this->get_cache($path);
            		}
                    */

                    foreach($data as $key => $value) {
                        // This is just a debug point to see what keys are coming in.
                        do_action("wp_config/keys", $key);

                        // Fires to allow handlers to listen for the key they need.
            			do_action("wp_config/{$key}", $data, $path, $key);
            		}

            	} else {
            		// File doesn't exist.
                    // @TODO: Report issue.
            	}
    		}
            $this->cache_all();
    	}
    }

    function cache($path, $data) {
        return update_option('wp_config/last_json', $data);
    }

    function cache_all() {
        return set_transient('wp_config/last_all_json', $this->datasets, $this->cache_duration);
    }

    function get_last_cache($path) {
        return get_option('wp_config/last_json');
    }

    function format_plugin_path($path) {
        return WP_PLUGIN_DIR . '/' . dirname($path);
    }

    function print_admin_notices() {
        if (!empty($this->error_log)) {
            foreach($this->error_log as $log) {
                if ($log["display_in_admin"]) {
                    ?>
                    <div class="error">
                        <p><?php echo $log["message"] . ": " . $log["reference"] ?></p>
                    </div>
                    <?php
                }
            }
        }
    }
}
?>
