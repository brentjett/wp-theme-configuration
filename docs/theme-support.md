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
