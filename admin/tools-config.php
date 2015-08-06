<?php
/*
NOT IN use

add_action('admin_menu', function() {
    add_management_page( 'JSON Config', 'JSON Config', 'manage_options', 'brj-wp-config-tests', 'wp_config_print_tests_page' );
});

function wp_config_print_tests_page() {
    ?>
    <div class="wrap">
    <h1>Configuration API Tests</h1>
    <p>This is a temporary page to check the import of config files.</p>
    <?php
    $paths = apply_filters('wp_config/paths', array());
    if (!empty($paths)) {
        foreach($paths as $path) {
            $data = wp_json_config($path);
            ?>
            <table class="wp-list-table widefat">
                <thead>
                    <tr>
                        <th colspan="2">Found Files</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Path</td>
                        <td><?php echo $path ?></td>
                    </tr>
                    <tr>
                        <td>Data</td>
                        <td><pre><?php print_r($data)?></pre></td>
                    </tr>
                </tbody>
            </table>
            <?
        }
    }
    ?>
    </div>
<?php
}
*/
?>
