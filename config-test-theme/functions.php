<?php

function brj_display_config_data() {
    ob_start();

    $paths = apply_filters('wp_config/paths', array());

    print "<div class='half-col'>";
    print "<table>";
    print "<thead><th colspan='2'>Imported Data</th></thead>";
    print "<tbody>";

    if (!empty($paths)) {
        foreach($paths as $path) {
            print "<td>Path</td>";
            print "<td>" . $path . "</td>";
        }
    }
    print "</table></div>";

    return ob_get_clean();
}

add_filter('the_content', 'brj_display_config_data');

?>
