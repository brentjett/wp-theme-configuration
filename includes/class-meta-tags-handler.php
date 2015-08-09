<?php
/**
* Class to handle configuration of the "meta_tag" property in JSON files.
*/
class WP_Config_Meta_Tag_Handler {

    /**
    * Array of all meta tag data collected
    * @var array
    */
    public $meta_tags = array();

    function __construct() {
        add_action('wp_config/meta-tags', array($this, "prepare"), 10, 3);
        add_action('wp_head', array($this, "configure"));
    }

    /**
    * Collect meta tag data and populate $this->meta_tags
    * @param object containing meta tag data
    * @param string path to file containing data
    * @param string meta-tags
    */
    function prepare($data, $path, $keyy) {
        foreach($data as $name => $tag) {
            $this->meta_tags[$name] = $tag;
        }
    }

    /**
    * On wp_head print meta tags from data in $this->meta_tags
    */
    function configure() {

        if (!empty($this->meta_tags)) {
            print "\n<!-- Enqueued Meta Tags -->\n";
            foreach($this->meta_tags as $name => $data) {

                $charset = null;
                $http_equiv = null;
                $content = null;

                if (isset($name)) {
                    $name = "name='$name' ";
                }
                if (is_string($data)) {
                    $content = "content='$data' ";
                }
                if (is_object($data) && !empty($data)) {
                    if (!empty($data->content)) {
                        $content = "content='$data->content' ";
                    }
                    if (!empty($data->charset)) {
                        $charset = "charset='$data->charset' ";
                    }
                    if (!empty($data->{'http-equiv'})) {
                        $http_equiv = "http-equiv='" . $data->{'http-equiv'} . "' ";
                    }
                }
                print "<meta " . $name . $charset . $http_equiv . $content . ">\n";
            }
            print "<!-- End Enqueued Meta Tags -->\n\n";
        }
    }
}
?>
