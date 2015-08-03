# WP Config API Spec - DRAFT

The API is designed to intake one or more JSON files and process their contents. The process is divided into 3 main parts:
* Locating the files
* Importing each file's data & handling misformatted data (fallback to cached last successful import)
* Loop over contents and allow filters to handle configuration.

## Benefits
A declarative syntax for configuration has several benefits to the themer:
* Offers a clear way to see exactly what is taking place inside a theme. The more information stored inside a config file, the better understanding someone can have when approaching a theme they've never worked with.
* Relieves the burden of needing to understand the WordPress event model. Themers no longer need to be aware that stylesheets are enqueued on wp_enqueue_scripts and theme supports are declared inside after_setup_theme. The API can handle declaring things at the proper event.
* In addition to being a very human and machine readable format, JSON is also an easily writable format. This enables config data to be created through a user interface and written to the appropriate place. Writing well-formed PHP files is a lot more clumsy and prone to hackery.
* JSON files move with the theme. Unlike information stored in the database (theme_mods, options), config data stored inside the theme itself will travel with that theme whenever it is moved (staging to production, downloaded by end user) without having to create an export -> import migration process.


## Premises
1. There may be multiple config files with a variety of settings inside. Some idea of priority must be established to deal with duplicate settings.
2. Base paths needed to find file references such as stylesheets and scripts should be determined starting with the directory of the config file specifying the setting. Additional lookup paths might be added through a filter.


## API Syntax

### Theme supports (Testing)
Anything declared with add_theme_support() can be added through the API. Custom/Non-standard support options have not yet been added. Apart from using underscores instead of dashes (for the sake of PHP properties later) each item should match the internal theme support spec (ex: the html5 property expects an array of components to support html5 output on).

```json
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

### Stylesheets & Scripts (Testing)
Stylesheets and scripts can be registered and/or enqueued using the API. This matches very closely to the [wp_enqueue_script()](https://codex.wordpress.org/Function_Reference/wp_enqueue_script) and [wp_enqueue_style()](https://codex.wordpress.org/Function_Reference/wp_enqueue_style) functions.

```json
{
    "enqueue_styles" : {
        "main" : {
            "path" : "style.css",
            "dependencies" : ["dashicons"],
            "version" : false,
            "media" : "screen"
        },
        "homepage" : {
            "path" : "css/home.css",
            "active_callback" : "is_front_page"
        }
    },
    "register_styles" : {},
    "enqueue_scripts" : {
        "app" : {
            "path" : "js/app.js",
            "dependencies" : ["jquery"],
            "in_footer" : false
        }
    },
    "register_scripts" : {},
    "editor_styles" : {
        "content" : {
            "path" : "css/content.css"
        }
    }
}
```

### Nav Menu Locations (Testing)
Adding nav menu locations is very simple. The key is the menu handle and the value is the label, just like the locations array passed to [register_nav_menus()](https://codex.wordpress.org/Function_Reference/register_nav_menus).

```json
{
    "nav_menus" : {
        "header" : "Header Menu",
        "footer" : "Footer Menu"
    }
}
```

### Sidebars (Planned)
Sidebars can be declared exactly like the array passed to [register_sidebar()](https://codex.wordpress.org/Function_Reference/register_sidebar) inside the "sidebars" key.
```json
{
    "sidebars" : {
        "default-sidebar" : {
            "name" : "Default Sidebar",
            "id" : "default-sidebar",
            "description" : "A sidebar that goes everywhere",
            "class" : "main-sidebar-wrap",
            "before_widget" : "<li id='%1$s' class='widget %2$s'>",
            "after_widget" : "</li>",
            "before_title" : "<h2 class='widgettitle'>",
            "after_title" : "</h2>"
        },
        "blog-sidebar" : {
            "name" : "Blog Sidebar",
            "id" : "blog-sidebar"
        }
    }
}
```

### Custom Post Types (Planned)
### Custom Taxonomies (Planned)

### Register Widgets (Planned)
### Register Shortcodes (Planned)

### Customizer API (Planned)

### Meta Tags (Testing, Non-WP API)
Meta tags is an example of how an arbitrary or non-wordpress handler might do some custom configuration. This handler takes data and prints the appropriate <meta> tag in the wp_head() call. I've including this because meta tags are the only thing that are not possible to enqueue in the wp_head() call at present. Stylesheets, scripts, and the title tag have all been incorporated into core API.

```json
{
    "meta_tags" : {
		"chars" : {
			"charset" : "UTF-8"
		},
		"ie-compat" : {
			"content" : "IE=edge",
			"http-equiv" : "X-UA-Compatible"
		},
		"viewport" : "width=device-width, initial-scale=1.0",
		"referrer" : "always"
	}
}
```
