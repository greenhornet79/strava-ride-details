<?php 

/*
Plugin Name: Strava Ride Details
Plugin URI: http://www.endocreative.com
Description: Display Strava ride details in your pages and posts with a shortcode
Version: 1.1
Author: Endo Creative
Author URI: http://www.endocreative.com
*/

/*  Copyright 2013 Endo Creative (email : info@endocreative.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

function endo_strava( $atts ){
	extract( shortcode_atts( array(
		'id' => 'none',
		'name' => 'true',
		'distance' => 'true',
		'elevation' => 'true',
		'moving_time' => 'true',
		'location' => 'true'
	), $atts ) );

	// Define the ride id
	define( 'ENDO_STRAVA_RIDE_ID', $id );

	if ( ENDO_STRAVA_RIDE_ID == 'none' ) {
		return;
	}

	echo '<ul id="strava-ride-details">';

	if ( $name == 'true' ) {
		do_action( 'endo_ride_name' );
	}

	if ( $distance == 'true' ) {
		do_action( 'endo_ride_distance' );
	}

	if ( $elevation == 'true' ) {
		do_action( 'endo_ride_elevation' );
	}

	if ( $moving_time == 'true' ) {
		do_action( 'endo_ride_moving_time' );
	}

	if ( $location == 'true' ) {
		do_action( 'endo_ride_location' );
	}

	echo '</ul>';

}
add_shortcode( 'strava', 'endo_strava' );

// Poll Strava API
// Return array of details, or false on error
function endo_ask_strava() {

	// Send GET request to Strava API
	$api_url = 'http://www.strava.com/api/v2/rides/';
	$api_response = wp_remote_get( $api_url . urlencode( ENDO_STRAVA_RIDE_ID ) );

	// Get the JSON object
	$json = wp_remote_retrieve_body( $api_response );

	// Make sure the request was successful or return false
	if ( empty( $json ) )
		return false;

	// Decode the JSON object
	// Return an array with ride name, distance, location
	$json = json_decode( $json );

	return array(
		'ride_name' => $json->ride->name,
		'distance' => $json->ride->distance,
		'elevation' => $json->ride->elevation_gain,
		'moving_time' => $json->ride->moving_time,
		'location' => $json->ride->location
	);
}

// Return array of details
function endo_get_infos( $info ) {

	$val = endo_ask_strava();

	return $val[$info];
}

// Echo ride name
function endo_ride_name() {
	$ride = endo_get_infos( 'ride_name' );
	echo "<li><span>Ride Name:</span> $ride</li>";
}

// Echo distance
function endo_ride_distance() {
	$dis_meters = endo_get_infos( 'distance' );
	$dis_miles = $dis_meters * .000621371;

	echo "<li><span>Distance:</span> " . round( $dis_miles, 2) . " miles</li>";
}

// Echo elevation
function endo_ride_elevation() {
	$elev_meters = endo_get_infos( 'elevation' );
	$elev_ft = $elev_meters * 3.28084;

	echo "<li><span>Elevation Gain:</span> " . round( $elev_ft ) . " ft</li>";
}

// Echo moving time
function endo_ride_moving_time() {
	$time_sec = endo_get_infos( 'moving_time' );
	$time = gmdate("H:i:s", $time_sec);

	echo "<li><span>Moving Time:</span> " . $time . "</li>";
}

// Echo location
function endo_ride_location() {
	$location = endo_get_infos( 'location' );
	echo "<li><span>Location:</span> $location</li>";
}

// Register custom actions
add_action( 'endo_ride_name', 'endo_ride_name' );
add_action( 'endo_ride_distance', 'endo_ride_distance' );
add_action( 'endo_ride_elevation', 'endo_ride_elevation' );
add_action( 'endo_ride_moving_time', 'endo_ride_moving_time' );
add_action( 'endo_ride_location', 'endo_ride_location' );