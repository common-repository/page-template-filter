=== Plugin Name ===
Contributors: t31os_
Tags: page, template, filter, administration, dropdown
Requires at least: 3.2
Tested up to: 3.2.1
Stable tag: 1.0

Filter pages or hierarchal custom types by page template. 

== Description ==

Adds an additional dropdown menu to the page listing(also works for other hierarchal post types that support page templates) to filter by page template.

== Installation ==

1. Upload the `page-template-filter` folder to the `/wp-content/plugins/` directory
1. Activate the plugin through the Plugins menu in WordPress
1. Enjoy the new filter dropdown on the admin page listing

== Frequently Asked Questions ==

= Where can i find the settings page for this plugin? =

You won't find one, there's simply no need to provide a configuration page for such a simple plugin. 
However, filter and action hooks will be available for developers to modify functionality.

= Ok so what are the current hooks available? =

Currently there just's the one, a filter for determing whether or not to add a page template column to the page listing. 
Pass the filter a bool value of true or false, true is default and false disables the additional column.

**Example:**
`
add_filter( 'ptf_add_template_column', '__return_false' );
`

== Changelog ==

= 1.0.0 =
* Initial release
