# Spec (WORKING)

The API is designed to intake one or more JSON files and process their contents.

## Premises
1. There may be multiple config files with a variety of settings inside. Some idea of priority must be established to deal with duplicate settings.
2. Base paths needed to find file references such as stylesheets and scripts should be determined starting with the directory of the config file specifying the setting. Additional lookup paths might be added through a filter.


## API Syntax

### Theme supports (Supported, In Testing)
Anything declared with add_theme_support() can be added through the API. Custom/Non-standard support options have not yet been added. Apart from using underscores instead of dashes (for the sake of PHP properties later) each item should match the internal theme support spec (ex: the html5 property expects an array of components to support html5 output on).

```JSON
{
    "theme_support" : {
        "title_tag" : true,
        "html5" : [
			"comment-list",
			"comment-form",
			"search-form",
			"gallery",
			"caption"
		],
        "automatic_feed_links" : true,
        "post_thumbnails" : true
    }
}
```

### Stylesheets & Scripts
Stylesheets and scripts can be registered and/or enqueued using the API.

```JSON
{

}
```

### Nav Menu Locations (In Testing)
Adding nav menu locations is very simple. The key is the menu handle and the value is the label, just like the locations array passed to [register_nav_menus()](https://codex.wordpress.org/Function_Reference/register_nav_menus)

```JSON
"nav_menus" : {
    "header" : "Header Menu",
    "footer" : "Footer Menu"
}
```
