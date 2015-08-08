<?php
/**
* Class to handle configuration of the "meta_tag" property in JSON files.
*/
class WP_Config_Meta_Tag_Handler {

    public $datasets = array();

    function __construct() {
        add_action('wp_config/meta_tags', array($this, "prepare"), 10, 2);
        add_action('wp_head', array($this, "configure"));
    }

    // Meta Tag Handler
    function prepare($data, $file) {

        foreach($data->meta_tags as $key => $tag) {
            // @TODO: This needs to assign each property individually.
            $this->datasets[$key] = $tag;
        }
    }

    function configure() {

        print "\n<!-- Enqueued Meta Tags -->\n";
        foreach($this->datasets as $name => $data) {

            $charset = $http_equiv = $content = null;

            if ($name) {
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
?>
