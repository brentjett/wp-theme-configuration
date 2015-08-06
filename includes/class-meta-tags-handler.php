<?php
class WP_Config_Meta_Tag_Handler {

    public $data = array();
    public $file = "";

    function __construct() {
        add_action('wp_config/meta_tags', array($this, "configure"), 10, 2);
        add_action('wp_head', array($this, "render"));
    }

    // Meta Tag Handler
    function configure($data, $file) {

        $this->data = $data->meta_tags;
        $this->file = $file;
    }

    function render() {

        print "\n<!-- Enqueued Meta Tags -->\n";
        foreach($this->data as $name => $data) {

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
