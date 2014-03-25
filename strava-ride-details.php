<?php 

/*
Plugin Name: Strava Ride Details
Plugin URI: http://www.endocreative.com
Description: Display Strava ride details in your pages and posts with a shortcode
Version: 1.2.1
Author: Endo Creative
Author URI: http://www.endocreative.com
*/

/*  Copyright 2014 Endo Creative (email : info@endocreative.com)

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

// Add settings link on plugin page
function srd_settings_link($links) { 
  $settings_link = '<a href="options-general.php?page=strava-ride-details/strava-ride-details.php">Settings</a>'; 
  array_unshift($links, $settings_link); 
  return $links; 
}
 
$plugin = plugin_basename(__FILE__); 
add_filter("plugin_action_links_$plugin", 'srd_settings_link' );

// settings page
add_action( 'admin_menu', 'srd_create_options_page');

function srd_create_options_page() {
	add_options_page( 'Strava Ride Details', 'Strava Ride Details', 'manage_options', __FILE__, 'srd_settings_page');
}


function srd_settings_page() {

	?>
		<div class="wrap">
		<h2>Strava Ride Details</h2>
		<form method="post" action="options.php">
			<?php settings_fields('srd_options'); ?>
			<?php do_settings_sections('srd'); ?>
			<?php submit_button(); ?>
		</form>
		</div>
	<?php
}

add_action( 'admin_init', 'srd_admin_init' );

function srd_admin_init() {
	register_setting( 'srd_options', 'srd_options', 'srd_validate_options' );

	add_settings_section( 'srd_main', 'Connect to Your Strava Account', 'srd_section_text', 'srd' );

	add_settings_field( 'srd_client_id', 'Client ID', 'srd_client_id_input', 'srd', 'srd_main');

	add_settings_field( 'srd_client_secret', 'Client Secret', 'srd_client_secret_input', 'srd', 'srd_main');

	add_settings_field( 'srd_oauth', 'Access Token', 'srd_oauth_input', 'srd', 'srd_main');

	add_settings_field( 'srd_units', 'Display Units', 'srd_units_input', 'srd', 'srd_main');
}

function srd_validate_options( $input ) {
	return $input;
}

function srd_section_text() {
	?>
	<ol>
		<li><a target="_blank" href="http://www.strava.com/developers">Click here</a> to create an application on Strava. Make sure the redirect URI you enter is the same as your site.</li>
		<li>Insert your Client ID and Client Secret below.</li>
		<li>Click the button below. You will be sent to Strava to allow access to this plugin. After authorization, you will be redirected back to this settings page.</li>'
	</ol>

	<?php 
	echo '<p><a href="https://www.strava.com/oauth/authorize?client_id=340&response_type=code&redirect_uri=' . home_url() . '/wp-admin/options-general.php?page=strava-ride-details/strava-ride-details.php&approval_prompt=force"><img src="' . plugins_url( 'images/ConnectWithStrava.png', __FILE__) . '"></a></p>';

}

function srd_oauth_input() {

	$options = wp_parse_args( get_option( 'srd_options' ), array('srd_oauth' => ''));
	$access_token = $options['srd_oauth'];

	// get access token from Strava
	if ( empty($access_token) ) {

		if ( isset($_GET['code']) ) {

			$code = $_GET['code'];
			$client_id = $options['srd_client_id'];
			$client_secret = $options['srd_client_secret'];
			
			$curl = curl_init();

			curl_setopt_array($curl, array(
				CURLOPT_RETURNTRANSFER => 1,
				CURLOPT_FOLLOWLOCATION => 1,
				CURLOPT_POST => 1,
				CURLOPT_URL => 'https://www.strava.com/oauth/token',
				CURLOPT_POSTFIELDS => array(
					'client_id' => $client_id,
					'client_secret' => $client_secret,
					'code' => $code
				)

			));

			$resp = curl_exec($curl);
			curl_close($curl);
			$resp = json_decode($resp);
			$access_token = $resp->access_token;
		}
	}

	echo "<input id='srd_oauth' name='srd_options[srd_oauth]' value='$access_token'>";
}

function srd_client_id_input() {

	$options = wp_parse_args( get_option( 'srd_options' ), array('srd_client_id' => ''));
	$client_id = $options['srd_client_id'];
	echo "<input id='srd_client_id' name='srd_options[srd_client_id]' value='$client_id'>";
}

function srd_client_secret_input() {

	$options = wp_parse_args( get_option( 'srd_options' ), array('srd_client_secret' => ''));
	$client_secret = $options['srd_client_secret'];
	echo "<input id='srd_client_secret' name='srd_options[srd_client_secret]' value='$client_secret'>";
}


function srd_units_input() {
	
	$options = wp_parse_args( get_option( 'srd_options' ), array('srd_units' => ''));
	
	$units = $options['srd_units'];
	
	echo '<select name="srd_options[srd_units]">';
	echo '<option value="english" ' . selected( $options['srd_units'], 'english' ) . '>Miles/Feet</option>';
	echo '<option value="metric" ' . selected( $options['srd_units'], 'metric' ) . '>KM/Meters</option>';
	
}



// shortcodes
add_shortcode( 'strava', 'srd_strava_shortcode' );

function srd_strava_shortcode( $atts ){
	extract( shortcode_atts( array(
		'id' => 'none',
		'name' => 'true',
		'distance' => 'true',
		'elevation' => 'true',
		'moving_time' => 'true',
		'location' => 'true'
	), $atts ) );


	if ( $id == 'none' ) {
		return;
	}

	$ride_details = srd_get_activity( $id );

	$content = '<ul class="srd-details">';

	if ( $name == 'true' ) {
		$content .= '<li class="srd-name"><span>Ride Name: </span>' . $ride_details['name'] . '</li>';
	}
	
	if ( $distance == 'true' ) {
		$content .= srd_ride_distance( $ride_details['distance'] );
	}

	if ( $elevation == 'true' ) {
		$content .= srd_ride_elevation( $ride_details['elevation'] );
	}

	if ( $moving_time == 'true' ) {
		$content .= srd_ride_moving_time( $ride_details['moving_time'] );
	}

	if ( $location == 'true' ) {
		$content .= '<li class="srd-location"><span>Location:</span> ' . $ride_details['location'] . '</li>';
		
	}

	$content .= '</ul>';

	return $content;

}


// Poll Strava API
// Return array of details, or false on error
function srd_get_activity( $id ) {

	$options = get_option( 'srd_options' );
	$access_token = $options['srd_oauth'];

	// Send GET request to Strava API
	$api_url = 'https://www.strava.com/api/v3/activities/' . $id . '/?access_token=' . $access_token;
	$api_response = wp_remote_get( $api_url );

	
	$activity = wp_remote_retrieve_body( $api_response );

	$activity = json_decode( $activity );

	// Make sure the request was successful or return false
	if ( empty( $activity ) )
		return false;

	return array(
		'name' => $activity->name,
		'distance' => $activity->distance,
		'elevation' => $activity->total_elevation_gain,
		'moving_time' => $activity->moving_time,
		'location' => $activity->location_city . ' ' . $activity->location_state
	);
}

function srd_ride_distance( $dis_meters ) {

	$options = get_option( 'srd_options' );
	$units = $options['srd_units'];

	if ( $units == 'english' ) {
		$total_distance = round( $dis_meters * .000621371, 2 ) . ' mi';
	} else {
		$total_distance = round( $dis_meters * .001, 2 ) . ' km';
	}
	
	return '<li class="srd-distance"><span>Distance:</span> ' . $total_distance . '</li>';
}


function srd_ride_elevation( $elev_meters ) {

	$options = get_option( 'srd_options' );
	$units = $options['srd_units'];

	if ( $units == 'english' ) {
		$elevation = round( $elev_meters * 3.28084, 2 ) . ' ft';
	} else {
		$elevation = round( $elev_meters, 2) . ' m';
	}

	return '<li class="srd-elevation"><span>Elevation Gain:</span> ' . $elevation . '</li>';
}


function srd_ride_moving_time( $time_sec ) {
	$time = gmdate("H:i:s", $time_sec);
	return '<li class="srd-moving-time"><span>Moving Time:</span> ' . $time . '</li>';
}