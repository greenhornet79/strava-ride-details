=== Strava Ride Details ===
Contributors: endocreative
Donate link: http://endocreative.com/
Tags: biking, strava, shortcode
Requires at least: 3.4
Tested up to: 3.8.1
Stable tag: 1.2.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

This plugin allows you to display Strava ride details from a specific ride in your posts and pages using a shortcode.

== Description ==

Strava Ride Details allows you to display Strava ride details from a specific ride in your posts and pages using a shortcode. It uses v3 of Strava's API, and uses OAuth for authentication. 

The details displayed by default are:

*   Ride Name
*   Distance
*   Elevation Gain
*   Moving Time
*   Location

Each of the details can be turned on or off. The details are displayed in an unordered list so you can style the elements however you would like using CSS.

You can also choose to display units in mi/ft or km/m.

While Strava does offer their own embed widget, this plugin:

* does not require an iframe
* allows you to style the results to match your site
* will work in responsive themes


By default all details are display using the shortcode `[strava id="ride_id"]`, where ride_id is the string of digits at the end of the URL when viewing a single ride on Strava.

If you would like to remove a detail, just set it to false in the shortcode. For example, if you would like to show only the distance, then use this shortcode:

`[strava id="ride_id" name="false" elevation="false" moving_time="false" location="false"]`

== Installation ==

1. Upload the folder `strava-ride-details` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress

## Plugin Setup
1. Log in to your Strava account and create an application [http://www.strava.com/developers]
3. Make sure the redirect URI of your application is the same domain as your site
2. Copy your client ID and client secret from your application into the Strava Ride Details settings page
3. Click "Connect with Strava" on the settings page to generate an access token
4. Save settings

## Add to page/post
1. Insert `[strava id="ride_id"]` in your post or page


== Screenshots ==

1. Adding the shortcode
2. Ride details styled using CSS

== Changelog ==

= 1.2.1 =
* Added link to settings page on Plugins page
* Added instructions for plugin setup on settings page

= 1.2 =
* Updated to use version 3 of the Strava API.
* Added settings page
* Added authentication with Strava via OAuth.
* Added ability to choose either English or Metric display units in settings.

= 1.0 =
* Plugin released.