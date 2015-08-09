# Theme Configuration API for WordPress

## The Big Refactor
This project has just gone through a complete refactor that should make it a lot more robust. The flow of operations is now controlled by one centralized [WP_Config_Manager](includes/class-wp-config-manager.php) object. Feature support is implemented through handler classes. To start with, I have support for theme supports, nav menu locations (both in [WP_Config_Theme_Support_Handler](includes/class-theme-support-handler.php)), Script and Stylesheet enqueuing ([WP_Config_Enqueue_Handler](includes/class-enqueue-handler.php)) and meta tags ([WP_Config_Meta_Tag_Handler](includes/class-meta-tags-handler.php)). See the [Spec doc](docs/spec.md) for an update to the syntax of each feature. All of these features need plenty of testing and unit testing isn't far off.

Feel free to try the plugin. Just add wp-config.json files to the root of your themes or plugins. If you run into unexpected behavior, describe the situation in a new ticket. I'm also happy to hear suggestions how to best streamline the JSON syntax for various current and future features.

## Configuration vs. Programming

Here's the premise: You shouldn't need to write programmatic code to configure a theme. Logical programming is for just that...logic. Also it's extremely prone to simple mistakes like missing semicolons at the end of lines or mismatched quotation marks. Instead, the vast majority of theme configuration is simply declared, without any conditions, and this plugin/library is intended to make that faster, less combersome, and easier to understand.

The plugin detects wp-config.json files with configuration data and passes each section of that data through a series of filters that do the work of configuring WordPress. This means you can simply write JSON to setup your theme supports, enqueue scripts and stylesheets, declare nav menu locations, etc...

See [Spec](docs/spec.md) for support details

## Benefits
A declarative syntax for configuration has several benefits to the developer:
* It's faster, using fewer characters to convey the same information as its PHP counterpart while offering greater clarity into what all is happening inside a theme or plugin. JSON is very human-readable and offers labels for each value where PHP functions do not.
* It relieves the burden of needing to understand the WordPress event model. Themers don't need to be aware that stylesheets are enqueued on wp_enqueue_scripts and theme supports are declared after_setup_theme. The API can handle declaring things at the proper event.
* In addition to being a very human and machine readable format, JSON is also an easily writable format. This enables the potential for config data to be created through a user interface and written to the appropriate place. Writing well-formed PHP files programattically is much more clumsy and prone to hackery.
* JSON files move with the theme. Unlike information stored in the database (theme_mods, options), config data stored inside the theme itself will travel with that theme (as it should) whenever it is moved (staging to production, downloaded by end user) without having to create an export -> import migration process.
* JSON files can store virtually any kind of small-form data. This API is written in such a way that arbitrary properties that do not match any WordPress API are still processed the same as built-in ones, so a developer has the freedom to create new ways to streamline their own theming and plugin development efforts. All one has to do is write a handler for the property you wish you import. See the included handler classes and the wp_config/init_handlers action for details on that.

## Example

This is how you typically enqueue a stylesheet, most likely in your functions.php file.
```php
add_action('wp_enqueue_scripts', function() {

    wp_enqueue_style('my-stylesheet', get_stylesheet_uri(), array('open-sans'), false, 'screen');

});
```

Not only is this verbose, but that's the short version if you simply want to include your style.css file. It's even longer if you're including a file from a deeper directory, and you have to remember the difference between get_stylesheet_uri(), get_stylesheet_directory_uri() and get_template_directory_uri() to even begin your path properly. This should be simply.

In a config.json file this would simply be:

```JSON
{
    "stylesheets" : {
        "my-stylesheet" : {
            "path" : "style.css",
            "dependancies" : ["open-sans"],
            "media" : "screen"
        }
    }
}
```

It's much simpler and much easier to read. Also, it's labeled. Since the config file is in the root of your theme, lets just assume that's the directory we're going to looking in. It's easy to see that dependencies has an array of other stylesheet references, and now we know what that "screen" is for, media. This means we can come back to our theme in 6 months and know exactly what's happening without having to remember. PHP Functions do not offer any labels for their arguments. Instead we're constantly referencing documentation to remember the order of arguments.

## Configuration

This API detects files named wp-config.json inside the root of active theme and plugin directories. It converts them to objects, and loops over their properties to configure. To give the system arbitrary paths to files in other locations or named differently, use the wp_config/paths filter.

```php
add_filter('wp_config/paths', function($paths) {
    $paths[] = get_stylesheet_directory() . 'my_sub_folder/my-config.json';
    return $paths;
});
```

As long as the file exists, the reader will import it. If for any reason the JSON in the file is malformed, the system will fallback to the last successful imported data that is cached in the database.

## Extending the API

Because the system loops over the properties of the JSON object and calls a corresponding filter with that property's name, it is very easy to extend the config API to support your own custom properties. Simply add your custom JSON to the config file, and the use the key for that property to specify a filter that handles configuration.

```JSON
{
    "my_custom_property" : {
        "prop_1" : "Value 1",
        "prop_2" : "Value 2"
    }
}
```

Once you've specified your custom property on the JSON object, you can define a filter in PHP to handle it.

```php
add_filter('wp_config/my_custom_property', function($data, $file_path, $key) {

    // Do some code here to handle the $data object. We also pass the file path in case you need to inspect that file or determine which file it is before performing the configuration. The key argument is the property you are listening for (my_custom_property). This allows for the same function to handle multiple properties.

}, 10, 3);
```

That's all for now. Let me know what you think.
