<?php
/**
* Setup Sidebars and Widgets.
*/
class WP_Config_Sidebars_Handler {

    public $sidebars = array();

    public $widgets = array();

    function __construct() {
        add_action('wp_config/sidebars', array($this, "prepare"), 10, 3);
        add_action('wp_config/widgets', array($this, "prepare"), 10, 3);
        add_action('widgets_init', array($this, "configure"));
    }

    function prepare($data, $path, $key) {
        if ($key == 'sidebars') {
            if (!empty($data)) {
                foreach($data as $id => $sidebar) {
                    $sidebar = (array) $sidebar;
                    if (!isset($sidebar['id'])) {
                        $sidebar['id'] = $id;
                    }
                    $this->sidebars[$id] = $sidebar;
                }
            }
        }
        if ($key == 'widgets') {
            if (!empty($data)) {
                foreach($data as $class_name) {
                    if (!in_array($class_name, $this->widgets)) {
                        $this->widgets[] = $class_name;
                    }
                }
            }
        }
    }

    function configure() {
        if (!empty($this->sidebars)) {
            foreach($this->sidebars as $sidebar) {
                register_sidebar($sidebar);
            }
        }
        if (!empty($this->widgets)) {
            foreach($this->widgets as $class_name) {
                if (class_exists($class_name)) {
                    register_widget($class_name);
                }
            }
        }
    }
}
?>
