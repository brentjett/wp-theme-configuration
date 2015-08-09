# Change Log

## 0.1 (08/08/2015)
* BIG Refactor - Reset the version number
* Auto detect wp-config.json files in theme and active plugin roots. Additional paths to arbitrary files can be included using the wp_config/paths filter.
* Streamlined script and stylesheet syntax. Use "scripts" and "stylesheets" keys. See spec.
* Allow for dashes inside property names instead of underscores to match WP APIs more closely. See spec for syntax details of each feature supported.
* Added support for arbitrary theme support keys
* Detects the base directory for scripts and stylesheets based on config file location - NEEDS TESTING.

Next Steps:
* Update caching to caching individual files separately instead of one big array of file data.
* PHP Unit testing
* Support for Sidebars
