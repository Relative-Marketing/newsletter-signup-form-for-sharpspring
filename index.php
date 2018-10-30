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
* Plugin Name: Newsletter Signup form for Sharpspring
* Plugin URI:  https://relativemarketing.co.uk/
* Description: Manages the signup of newsletters by adding leads to a specific sharpspring campaign.
* Version:     1.0.0
* Author:      Relative Marketing
* Author URI:  https://relativemarketing.co.uk
* Text Domain: relative-newsletter
*/
namespace RelativeMarketing\Newsletter;

defined('ABSPATH') or die();

add_action( 'rest_api_init', __NAMESPACE__ . '\\init_endpoint' );

function init_endpoint() {
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
	$campaign = get_option( 'relative_newsletter_sharpspring_campaign_id' );
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

	return rest_ensure_response(['campaignId' => $campaign, 'img' => $images, 'notice' => $notices, 'popupDelay' => $popup_delay, 'copy' => $copy]);
}

/**
 * Add the options page to allow users to setup the plugin
 */
require plugin_dir_path( __FILE__ ) . 'inc/class-options-page.php';
Options_Page::get_instance();
