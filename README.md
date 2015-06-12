# Declarative Theme Configuration API

You shouldn't need to write programmatic code to configure a theme. Logical programming is for just that...logic. Also it's extremely prone to simple mistakes like missing semicolons at the end of lines or mismatched quotation marks. Instead, the vast majority of theme configuration is simply declared, without any conditions, and this library is designed to make that faster. The library takes a path to a .json file with configuration data and passes each section of that data through a series of configuration filters. This means you can simply write JSON to setup your theme supports, enqueue scripts and stylesheets, declare nav menu locations, and a lot more.

Example: This is how you typically enqueue a stylesheet, most likely in your functions.php file.
```php
add_action('wp_enqueue_scripts', function() {

    wp_enqueue_style('my-stylesheet', get_stylesheet_uri(), array('open-sans'), false, 'screen');

});
```

Not only is this verbose, but that's the short version if you simply want to include your style.css file. It's even longer if you're including a file from a deeper directory, and you have to remember the difference between get_stylesheet_uri(), get_stylesheet_directory_uri() and get_template_directory_uri() to even begin your path properly. This should be simply.

In a config.json file this would simply be:

```JSON
{
    "enqueue_styles" : {
        "my-stylesheet" : {
            "path" : "style.css", 
            "dependancies" : ["open-sans"],
            "media" : "screen"
        }
    }
}
```

It's much simpler and much easier to read. Also, it's labeled. Since the config file is in the root of your theme, lets just assume that's the directory we're going to start looking in. It's easy to see that depandancies has an array of other stylesheet references, and now we know what that "screen" is for, media. This means we can come back to our theme in 6 months and know exactly what's happening without having to remember.

## Configuration

This API takes an array of paths to json files, converts them to objects, and loops over their properties to configure. To give the system one or more paths, use the basset/theme_config/paths filter. 

```php
add_filter('basset/theme_config/paths', function($paths) {

    $paths[] = "path/to/my/config.json";
    return $paths;
});
```

As long as the file exists, the reader will import it. If for any reason the JSON in the file is malformed, the system will fallback to the last successful imported data that is cached in the database. ** For the moment, caching only supports one file.

## Extending the API

Because the system loops over the properties of the JSON object and calls a correstponding filter with that property's name, it is very easy to extend the config API to support your own custom properties. Simply add your custom JSON to the config file, and the use the key for that property to specify a filter that handles configuration.

```JSON
{
    "enqueue_styles" : {},
    "nav_menus" : {},

    "my_custom_property" : {
        "prop_1" : "Value 1",
        "prop_2" : "Value 2"
    }
}
```

Once you've specified your custom property on the JSON object, you can define a filter in PHP to handle it.

```php
add_filter('basset/theme_config/meta_tags', function($data, $file_path) {
    
    // Do some code here to handle the $data object. We also pass the file path in case you need to inspect that file or determin which file it is before performing the configuration.

}, 10, 2);
```

That's all.