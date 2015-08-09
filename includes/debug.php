<?php
// tests
add_filter('the_content', function($content) {
	global $wp_config_manager;

	ob_start();
	?>
	<pre>
    <?php print_r($wp_config_manager->handlers["enqueue"]->stylesheets) ?>
	<?php print_r($wp_config_manager->handlers["enqueue"]->scripts) ?>
	</pre>
    <style>
    pre {
        font-size: 11px;
    }
    </style>
	<?php
    print_theme_supports_table();
	$content = ob_get_clean();
	return $content;
});

function print_theme_supports_table() {
    ?>
    <table>
		<thead>
			<tr>
				<th colspan="2">Theme Support</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>Title Tag</td>
				<td><?php
				if (current_theme_supports('title-tag')) print "yes"; ?></td>
			</tr>
			<tr>
				<td>HTML5 Elements</td>
				<td><?php
				if (current_theme_supports('html5')) {
					print "yes";
				}
				?></td>
			</tr>
			<tr>
				<td>Automatic Feed Links</td>
				<td><?php
				if (current_theme_supports('automatic-feed-links')) {
					print "yes";
				}
				?></td>
			</tr>
            <tr>
				<td>Post Formats</td>
				<td><?php
				if (current_theme_supports('post-formats')) {
					print "yes";
				}
				?></td>
			</tr>
            <tr>
				<td>Post Thumbnails</td>
				<td><?php
				if (current_theme_supports('post-thumbnails')) {
					print "yes";
				}
				?></td>
			</tr>
			<tr>
				<td>Custom Background</td>
				<td><?php
				if (current_theme_supports('custom-background')) {
					print "yes";
				}
				?></td>
			</tr>
			<tr>
				<td>Custom Header</td>
				<td><?php
				if (current_theme_supports('custom-header')) {
					print "yes";
				}
				?></td>
			</tr>
			<tr>
				<td>Nav Menus</td>
				<td><?php
				if (current_theme_supports('menus')) {
					print "yes";
				}
				?></td>
			</tr>
            <tr>
				<td>Editor Stylesheets</td>
				<td><?php
				if (current_theme_supports('editor-style')) {
					print "yes";
				}
				?></td>
			</tr>
            <tr>
				<td>Sidebars &amp; Widgets</td>
				<td><?php
				if (current_theme_supports('widgets')) {
					print "yes";
				}
				?></td>
			</tr>
		</tbody>
	</table>
	<style>
    	table {
    		border-collapse: collapse;
    	}
    	td, th {
    		border:1px solid #eee;
    		padding:8px;
    	}
	</style>
    <?php
}
?>
