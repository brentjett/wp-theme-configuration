# WP Config API Spec - DRAFT

This API identifies wp-config.json files within active themes and plugins and configures their data. The following sections shows the syntax used to configure each of the supported features.

## Assumptions
1. There may be multiple config files with a variety of settings inside. Some idea of priority must be established to deal with duplicate settings. Currently the system starts with arbirary files that have been filtered in with wp_config/paths, then proceeds to wp-config.json files in mu-plugins, active plugins, parent theme (if there is one) and finally child/active theme.
2. Base paths needed to find file references such as stylesheets and scripts should be determined starting with the directory of the config file specifying the setting. Additional lookup paths might be added through a filter in the future.

## API Syntax

### Theme support
Anything declared with add_theme_support() can be declared through the JSON API. Each key corresponds to the first argument of [add_theme_support()](https://codex.wordpress.org/Function_Reference/add_theme_support) and, if the value is an array or object, will be passed as the second parameter.

```json
{
    "theme-support" : {
        "title-tag" : true,
        "html5" : [
			"comment-list",
			"comment-form",
			"search-form",
			"gallery",
			"caption"
		],
        "automatic-feed-links" : true,
        "post-thumbnails" : true,
        "post-formats" : ["aside", "gallery", "quote"],
        "custom-background" : {
            "default-color" : "red",
            "default-image" : "",
            "default-repeat" : "",
            "default-position-x" : "",
            "default-attachment" : ""
        },
        "custom-header" : {
            "default-image" : "",
            "width" : 0,
            "height" : 0,
            "flex-height" : false,
            "flex-width" : false,
            "uploads" : true,
            "random-default" : false,
            "header-text" : true,
            "default-text-color" : ""
        }
    }
}
```
I'm making every effort to have this syntax match the add_theme_support arguments exactly. See the [theme support syntax reference](theme-support.md) for full details.

### Stylesheets & Scripts (Testing)
Stylesheets and scripts can be enqueued using the API. This matches very closely to the [wp_enqueue_script()](https://codex.wordpress.org/Function_Reference/wp_enqueue_script) and [wp_enqueue_style()](https://codex.wordpress.org/Function_Reference/wp_enqueue_style) functions. The ability to simply register a library without enqueuing it, as well as adding a stylesheet to the editor is coming.

```json
{
    "stylesheets" : {
        "main" : {
            "path" : "style.css",
            "dependencies" : ["dashicons"],
            "version" : false,
            "media" : "screen",
            "active_callback" : "is_front_page"
        },
        "homepage" : {
            "path" : "css/home.css",
            "active_callback" : "is_front_page"
        }
    },
    "scripts" : {
        "app" : {
            "path" : "js/app.js",
            "dependencies" : ["jquery"],
            "in_footer" : false
        }
    }
}
```

### Nav Menu Locations (Supported)
Adding nav menu locations is very simple. The key is the menu handle and the value is the label, just like the locations array passed to [register_nav_menus()](https://codex.wordpress.org/Function_Reference/register_nav_menus).

```json
{
    "nav-menus" : {
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

### Custom Post Types & Taxonomies (Planned)
Custom post types are two of the more complex objects to declare in WordPress. The JSON API will attempt to be as faithful to the existing API as possible to avoid confusion.
```json
{
    "post_types" : {
        "brj-review" : {
            "label" : "Customer Reviews",
            "labels" : {},
            "description" : "A review made by a customer",
            "public" : true,
            "exclude_from_search" : false,
            "publicly_queryable" : true,
            "show_ui" : true,
            "show_in_nav_menus" : true,
            "show_in_menu" : true
        }
    },
    "taxonomy" : {
        "handle" : "service",
        "object_types" : ["brj-review", "post"],
        "labels" : {
            "name" : "Services",
            "singular_name" : "Service"
        },
        "hierarchical" : true,
        "show_ui" : true,
        "show_admin_column" : true,
        "query_var" : true,
        "rewrite" : ["slug", ""]
    }
}
```
There are many more properties than this. See the [full API](https://codex.wordpress.org/Function_Reference/register_post_type) for all keys.

### Register Widgets (Planned)
Registering a widget only requires passing an array of WP_Widget subclasses you'd like to be registered. This API might be extended to allow widget fields to be specified here as well.
```json
{
    "widgets" : [
        "BRJ_CallToActionWidget",
        "BRJ_AuthorBioWidget"
    ]
}
```
### Register Shortcodes (Planned)
Generic shortcodes are simply a shortcode name and a callback function that returns the output, but with the new Shortcake UI plugin under consideration for core inclusion, we can declare the labels and fields as well.

```json
{
    "shortcodes" : {
        "brj_action" : {
            "tag" : "brj_action",
            "callback" : "brj_print_action_shortcode"
        },
        "brj_quote" : {
            "callback" : "brj_print_quote_shortcode",
            "label" : "BIG Quote",
            "listItemImage" : "dashicons-editor-insertmore",
            "inner_content" : {
                "label" : "Content"
            },
            "attrs" : [
                {
                    "label" : "Link To",
                    "attr" : "link_url",
                    "type" : "url",
                },
                {
                    "label" : "Additional Styles",
                    "attr" : "style",
                    "type" : "text",
                },
            ]
        }
    }
}
```

### Customizer API (Planned)
Customizer Settings, Panels, Sections, and Controls should certainly be declarable. There are a lot of changes taking place in this API at the moment regarding javascript templating and partial dom refresh, so I don't feel it's ready to be defined quite yet.

### Actions & Filters (Planned)
For the sake of completeness, I've planned the ability to declare action and filter functions. This is more useful as an explainer so that it's easy to see how the theme is connected moreso than helpful functionality. You'd still have to define the function in PHP.
```json
{
    "actions" : {
        "after_setup_theme" : ["brj_run_my_theme_setup"]
    },
    "filters" : {
        "the_content" : [ "brj_mangle_the_content" ]
    }
}
```

### Admin Pages & Settings (Planned)
### Admin Dashboard Widgets (Planned)
### Metaboxes

### Meta Tags (Testing, Experimental Non-WordPress API)
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

### Proposed Fields API (Experimental)
This is a quick & dirty version of what a JSON wrapper for the proposed(in-dev) Fields API might look like. This has the potential to ultimately replace multiple API like Admin page settings, customizer, post metaboxes, and add new outlets like users and taxonomies. Maybe I'll even be able to convince someone that nav menu items need custom fields :O!

Nested fields don't need to declare their section or panel in their properties. Parent/Child relationships can be inferred from structure. Settings like capabilities can be inherited as well. In addition to declaring new objects, you can also reference previously declared objects (like blogname) and modify their properties (like transport).

```json
"fields" : {
    "customizer" : [
        {
            "type" : "section",
            "handle" : "mytheme_options",
            "title" : "MyTheme Options",
            "priority" : 35,
            "capability" : "edit_theme_options",
            "description" : "Allows you to customize some example settings for MyTheme.",
            "fields" : [
                {
                    "handle" : "link_textcolor",
                    "default" : "#2BA6CB",
                    "type" : "theme_mod",
                    "capability" : "edit_theme_options",
                    "transport" : "postMessage",
                    "control" : {
                        "id" : "mytheme_link_textcolor",
                        "label" : "Link Color",
                        "priority" : 10,
                        "type" : "color"
                    }
                }
            ]
        },
        {
            "type" : "field",
            "handle" : "blogname",
            "transport" : "postMessage"
        },
        {
            "type" : "field",
            "handle" : "blogdescription",
            "transport" : "postMessage"
        },
        {
            "type" : "field",
            "handle" : "header_textcolor",
            "transport" : "postMessage"
        },
        {
            "type" : "field",
            "handle" : "background_color",
            "transport" : "postMessage"
        },
    ],
    "user" : [
        {
            "type" : "section",
            "handle" : "mytheme_user_social_fields",
            "title" : "Social Fields",
            "fields" : [
                {
                    "handle" : "twitter",
                    "control" : {
                        "id" : "mytheme_user_twitter",
                        "label" : "Twitter Username",
                        "type" : "text"
                    }
                },
                {
                    "handle" : "google_plus",
                    "control" : {
                        "id" : "mytheme_user_google_plus",
                        "label" : "Google+ Profile URL",
                        "type" : "text"
                    }
                }
            ]
        }
    ],
    "settings" : [
        {
            "type" : "screen",
            "handle" : "mytheme_settings_sharing",
            "title" : "Sharing",
            "page_title" : "MyTheme Sharing.",
            "capability" : "manage_options",
            "sections" : [
                {
                    "handle" : "mytheme_setting_sharing",
                    "title" : "Sharing",
                    "fields" : [
                        {
                            "handle" : "mytheme_sharing_buttons",
                            "default" : 1,
                            "control" : {
                                "label" : "Google+ Profile URL",
                                "description" : "This will show sharing buttons below blog posts on singular templates",
                                "type" : "checkbox"
                            }
                        }
                    ]
                }
            ]
        }
    ],
    "posts" :[
        {
            "type" : "section",
            "handle" : "my_meta_box",
            "object_name" : "my_cpt",
            "title" : "My Meta Box",
            "priority" : "high",
            "context" : "side",
            "capability" : "my_custom_capability",
            "fields" : [
                {
                    "handle" : "my_custom_field",
                    "default" : "All about that post",
                    "control" : {
                        "label" : "My Custom Field",
                        "type" : "text"
                    }
                }
            ]
        }
    ]
}
```
For details on this project, [check out the repo](https://github.com/sc0ttkclark/wordpress-fields-api).

## Theme Declaration Metadata
This is just something I'm pondering but can't actually do anything beyond documentation without a core change.

```json
{
    "theme" : {
        "name" : "My Super Theme",
        "description" : "This is a config file (that should be) in the root of a super cool theme.",
        "version" : 1.0,
        "uri" : "http://wordpress.org/themes/brentsupercooltheme",
        "author" : "Brent",
        "license" : "GNU General Public License v2 or later",
        "license_uri" : "http://www.gnu.org/licenses/gpl-2.0.html",
        "tags" : ["super", "cool", "theme"],
        "releases" : [
            {
                "version" : 1.0,
                "date" : "2015-08-03",
                "description" : "Here's the latest and greatest version",
                "changelog" : [
                    "FIXED - That weird thing that kept happening",
                    "FIXED - That thing you said happened but I could never reproduce"
                ]
            }
        ]

    }
}
```
