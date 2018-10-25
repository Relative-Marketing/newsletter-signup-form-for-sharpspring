<?php
/**
* Plugin Name
*
* @package     RelativeMarketing\Newsletter
* @author      Daniel Gregory
* @copyright   2016 Relative Marketing
* @license     GPL-2.0+
*
* @wordpress-plugin
* Plugin Name: Relative Marketing Newsletter
* Plugin URI:  https://relativemarketing.co.uk/plugin-name
* Description: Manages the signup of newsletters.
* Version:     1.0.0
* Author:      Relative Marketing
* Author URI:  https://relativemarketing.co.uk
* Text Domain: relative-newsletter
*/
namespace RelativeMarketing\Newsletter;

defined('ABSPATH') or die();

/**
 * Registers a new endpoint to allow us to connect to sharsprings
 * serverside and keep our secret key secret
 * 
 * url should take the form of:
 * 
 * www.example.com/wp-json/relativemarketing/v1/email/$email/name/$name
 * 
 */
add_action( 'rest_api_init', __NAMESPACE__ . '\\init_endpoint' );

function init_endpoint() {
	register_rest_route( 
		'relativemarketing/v1/newsletter',
		'/email/(?P<email>[.\@a-zA-Z0-9]+)/name/(?P<name>[a-z0-9 %.\-]+)',
		[
			'methods' => 'GET',
			'callback' => __NAMESPACE__ . '\\handle_newsletter_subscribe',
			'args' => [
				'name' => [
					'validate_callback' => __NAMESPACE__ . '\\validate_name_string'
				],
				'email' => [
					'validate_callback' => function( $param ) {
						return is_email( $param );
					}
				],
			]
		] 
	);

	register_rest_route( 
		'relativemarketing/v1/newsletter',
		'/data',
		[
			'methods' => 'GET',
			'callback' => __NAMESPACE__ . '\\handle_get_data',
		] 
	);
}

/**
 * Attempt to add the email subscription to sharspring as a new lead
 */
function handle_newsletter_subscribe( $params ) {

	// Sort out name and email params
	$split_name = explode( '%20', $params->get_param( 'name' ) );
	$first_name = $split_name[0];
	$last_name  = $split_name[ count( $split_name ) - 1 ];
	$email      = $params->get_param('email');

	// Data for the request
	$account_id  = get_option( 'ss_api_key' );
	$secret_key  = get_option( 'ss_secret_key' );

	if ( empty($secret_key) || empty($account_id) ) {
		return new \WP_Error('Either account ID or Secret Key is not set');
	}

	$request_id  = session_id();
	$method      = 'createLeads';
	
	// If we have sharsprings(ss) tracking cookie then make sure it is attached
	// to the lead when the lead is created
	$tracking_id = array_key_exists( '__ss_tk', $_COOKIE ) ? $_COOKIE['__ss_tk'] : '';
	$campaign_id = get_options( 'ss_campaign_id' );

	// Format the params according to ss api requirements
	$paramData = [ 
		"objects" => [
			[
				"firstName"     => $first_name,
				"lastName"      => $last_name,
				"emailAddress"  => $email,
				"trackingID"    => $tracking_id,
				"campaignID"    => $campaign_id,
			]
		]
	];

	$data = json_encode( [
		'method' => $method,
		'params' => $paramData,
		'id'     => $request_id,
	] );

	$query_string = http_build_query( ['accountID' => $account_id, 'secretKey' => $secret_key] );
	$url = "https://api.sharpspring.com/pubapi/v1/?$query_string";

	$ch = curl_init( $url );

	curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, "POST" );
	curl_setopt( $ch, CURLOPT_POSTFIELDS, $data );
	curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
	curl_setopt( $ch, CURLOPT_HTTPHEADER, [
		'Content-Type: application/json',
		'Content-Length: ' . strlen( $data )
	] );

	$result = curl_exec( $ch );
	curl_close( $ch );

	return json_decode( $result  );

}

/**
 * Validates a given full name string
 */
function validate_name_string( $name ) {
	/**
	 * ^          - Beginning of the line
	 * [a-zA-Z- ] - Allowed characters
	 * +          - At least one or more of the preceding
	 * $          - End of line
	 * 
	 * Everything must match from the start and end to be true
	 */
	return (bool) preg_match( "/^[a-zA-Z- ]+$/", urldecode( $name ) );
}

/**
 * Output an element for react to render components into
 */
add_action( 'wp_footer', __NAMESPACE__ . '\\output_newsletter_holder' );

function output_newsletter_holder() {

	// Currently we only want to show the popup on single posts only
	if ( ! is_single() ) return;

	echo '<div id="relative-newsletter-signup"></div>';
}

/**
 * Load the scripts and styles for this plugin
 */
add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\\load_newsletter_scripts' );

function load_newsletter_scripts() {
	if ( ! is_single() ) return;
	wp_enqueue_script( 'relative-newletter', plugins_url( 'dist/index.js', __FILE__ ), [], filemtime( plugin_dir_path( __FILE__ ) . 'dist/index.js' ), true );
	wp_enqueue_style( 'relative-newsletter', plugins_url( 'dist/index.css', __FILE__ ), false, filemtime( plugin_dir_path( __FILE__ ) . 'dist/index.css' ) );
}

function handle_get_data() {
	$copy = [
		'heading'   => get_option( 'relative_newsletter_heading' ),
		'paragraph' => get_option( 'relative_newsletter_paragraph' ),
	];

	$notices = [
		'success' => [
			'heading' => get_option('relative_newsletter_success_heading'),
			'message' => get_option('relative_newsletter_success_paragraph'),
			'success' => true,
		],
		'error'=> [
			'heading' => get_option('relative_newsletter_success_heading'),
			'message' => get_option('relative_newsletter_success_paragraph'),
			'success' => false,
		],
	];

	$images = [
		'x1'  => get_option( 'relative_newsletter_img_x1' ),
		'x2'  => get_option( 'relative_newsletter_img_x2' ),
		'x3'  => get_option( 'relative_newsletter_img_x3' ),
		'alt' => get_option( 'relative_newsletter_img_alt' ),
	];

	$popup_delay = get_option( 'relative_newsletter_popup_delay' );

	return rest_ensure_response(['img' => $images, 'notice' => $notices, 'popupDelay' => $popup_delay, 'copy' => $copy]);
}

/**
 * Add the options page to allow users to setup the plugin
 */
require plugin_dir_path( __FILE__ ) . 'inc/class-options-page.php';
Options_Page::get_instance();
