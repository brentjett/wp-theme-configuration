# Theme Support
The theme support syntax is meant to mirror the built-in theme support API closely.

## Title Tag
Title tag can be declared with the key "title-tag", and a value of true.
```json
{
    "theme-support" : {
        "title-tag" : true
    }
}
```

## Automatic Feed Links
Automatic feed links can be declared with the key "automatic-feed-links", and a value of true.
```json
{
    "theme-support" : {
        "automatic-feed-links" : true
    }
}
```

## HTML5 Element Support
HTML5 element output can be declared with the "html5" key and an array of the elements you wish to support as the value.

```json
{
    "theme-support" : {
        "html5" : ["comment-list", "comment-form", "search-form", "gallery", "caption"]
    }
}
```

## Post Thumbnails (Featured Image)
Post thumbnails can be declared on all content post types with the key "post-thumbnails" and a value of true, or selectively by passing an array of post types.

```json
{
    "theme-support" : {
        "post-thumbnails" : ["post", "review", "movie"]
    }
}
```

## Post Formats
Post formats can be declared with the key "post-formats" and an array of the supported formats. See [Post Formats(codex)](https://codex.wordpress.org/Post_Formats) for an explaination of each format.

```json
{
    "theme-support" : {
        "post-formats" : ["aside", "gallery", "link", "image", "quote", "status", "video", "chat", "audio"]
    }
}
```

## Custom Background
Custom background support can be declared with the "custom-background" key and an object containing it"s properties. See [Custom Backgrounds(codex)](https://codex.wordpress.org/Custom_Backgrounds) for an explaination of all properties.

```json
{
    "theme-support" : {
        "custom-background" : {
            "default-color" : "red",
            "default-image" : "",
            "default-repeat" : "no-repeat",
            "default-position-x" : "",
            "default-attachment" : "fixed",
            "wp-head-callback" : "my_custom_bg_callback",
        	"admin-head-callback" : "my_admin_bg_callback",
        	"admin-preview-callback" : "my_bg_preview_callback"
        }
    }
}
```

## Custom Header
Custom header support can be declared with the "custom-header" key and an object containing it"s properties. See [Custom Headers(codex)](https://codex.wordpress.org/Custom_Headers) for an explaination of all properties.

```json
{
    "theme-support" : {
        "custom-header" : {
            "default-image" : "",
        	"width" : 0,
        	"height" : 0,
        	"flex-height" : false,
        	"flex-width" : false,
        	"uploads" : true,
        	"random-default" : false,
        	"header-text" : true,
        	"default-text-color" : "",
        	"wp-head-callback" : "",
        	"admin-head-callback" : "",
        	"admin-preview-callback" : ""
        }
    }
}
```
