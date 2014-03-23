=== Strava Ride Details ===
Contributors: endocreative
Donate link: http://endocreative.com/
Tags: biking, strava, shortcode
Requires at least: 3.4
Tested up to: 3.5
Stable tag: 1.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

This plugin allows you to display Strava ride details from a specific ride in your posts and pages using a shortcode.

== Description ==

** This plugin no longer works due to Strava removing support for their V2 API. I'll update it when/if they open V3 of their API. **

This plugin allows you to display Strava ride details from a specific ride in your posts and pages using a shortcode.

The details displayed by default are:

*   Ride Name
*   Distance
*   Elevation Gain
*   Moving Time
*   Location

Each of the details can be turned on or off. The details are displayed in an unordered list so you can style the elements however you would like using CSS.

By default all details are display using the shortcode `[strava id="ride_id"]`, where ride_id is the string of digits at the end of the URL when viewing a single ride on Strava.

If you would like to remove a detail, just set it to false in the shortcode. For example, if you would like to show only the distance, then use this shortcode:

`[strava id="ride_id" name="false" elevation="false" moving_time="false" location="false"]`

== Installation ==

1. Upload the folder `strava-ride-details` to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Insert `[strava id="ride_id"]` in your post or page


== Screenshots ==

1. Adding the shortcode
2. Ride details styled using CSS

== Changelog ==

= 1.0 =
* Plugin released.