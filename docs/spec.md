# Spec (WORKING)

The API is designed to intake one or more JSON files and process their contents.

## Premises
1. There may be multiple config files with a variety of settings inside. Some idea of priority must be established to deal with duplicate settings.


## API Syntax

### Theme supports
Anything declared with add_theme_support() can be added through the API. Custom/Non-standard support options have not yet been added.

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
