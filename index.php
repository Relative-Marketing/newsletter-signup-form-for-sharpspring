<?php
/**
* Plugin Name
*
* @package     RelativeMarketing\Newsletter
* @author      Daniel Gregory
* @copyright   2018 Relative Marketing
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

include 'inc/helpers.php';
use RelativeMarketing\Newsletter\Helpers as Helpers;

/**
 * Add the options page to allow users to setup the plugin
 */
// include plugin_dir_path( __FILE__ ) . 'inc/class-options-page.php';
// include plugin_dir_path( __FILE__ ) . 'inc/class-form-generator.php';


add_action( 'plugins_loaded', __NAMESPACE__ .'\\register_settings' );

function register_settings() {
	/**
	 * Check to make sure that relative options plugin is what provides the 
	 * settings page for this plugin. Make sure it is available before we
	 * try to use it.
	 */
	if ( ! class_exists( '\RelativeMarketing\Options\Page' ) ) {
		add_missing_dependency_error( __('<strong>Newsletter Signup for Sharpspring</strong> requires <a href="https://github.com/Relative-Marketing/relative-options">Relative options plugin</a> please install or activate it.', 'relative-newsletter') );
		return;
	}
	$page_arguments = [
		'parent' => 'options-general.php',
		'page_title' => 'Relative newsletter',
		'page_description' => 'Please add the relevant information for the newsletter popup',
		'menu_title' => 'Relative newsletter',
		'menu_slug' => 'relative-newsletter',
		'capability' => 'manage_options',
	];

	$sections = [
		'sharpspring-settings' => [
			'title' => 'Sharpspring specific settings',
			'fields' => [
				'relative_newsletter_sharpspring_campaign_id' => ['heading' => 'Sharpspring Campaign Id', 'desc' => 'Note: This is the campaign the user will be added to when they are signed up', 'type' => 'input'],
			]
		],
		'initial-popup-copy' => [
			'title' => 'Initial popup copy',
			'fields' => [
				'relative_newsletter_heading'                 => ['heading' => 'Heading', 'type' => 'input'],
				'relative_newsletter_paragraph'               => ['heading' => 'paragraph', 'type' => 'textarea'],
			],
		],
		'error-copy' => [
			'title' => 'Error copy',
			'fields' => [
				'relative_newsletter_error_heading'           => ['heading' => 'Heading', 'type' => 'input'],
				'relative_newsletter_error_message'           => ['heading' => 'message', 'type' => 'input'],
			],
		],
		'success-copy' => [
			'title' => 'Success copy',
			'fields' => [
				'relative_newsletter_success_heading'         => ['heading' => 'Heading', 'type' => 'input'],
				'relative_newsletter_success_message'         => ['heading' => 'message', 'type' => 'input'],
			],
		],
		'image-settings' => [
			'title' => 'Popup Image',
			'fields' => [
				'relative_newsletter_img_x1'                  => ['heading' => 'Image @1x resolution', 'type' => 'input'],
				'relative_newsletter_img_x2'                  => ['heading' => 'Image @2x resolution', 'type' => 'input'],
				'relative_newsletter_img_x3'                  => ['heading' => 'Image @3x resolution', 'type' => 'input'],
				'relative_newsletter_img_alt'                 => ['heading' => 'Image alt', 'type' => 'input'],
			],
		],
		'popup-visibility' => [
			'title' => 'Popup visibility',
			'fields' => [
				'relative_newsletter_popup_delay'             => ['heading' => 'Popup Delay', 'type' => 'input'],
			],
		],
	];

	$settings_page = new \RelativeMarketing\Options\Page($page_arguments, $sections);
	$settings_page->render();
}
add_action( 'admin_init', __NAMESPACE__ . '\\check_sharpspring_endpoints_plugin_active' );

function check_sharpspring_endpoints_plugin_active() {
	if( Helpers\has_sharpspring_endpoints_plugin() ) {
		return;
	}

	add_action( 'admin_notices', __NAMESPACE__ . '\\add_missing_sharpspring_dependency_error' );
}

function add_missing_sharpspring_dependency_error() {
	add_missing_dependency_error( __( '<strong>Newsletter signup form for Sharpspring</strong> requires <a href="https://github.com/Relative-Marketing/Endpoints-For-Sharpspring/">Endpoints for Sharpspring</a> please install or activate it', 'relative-newsletter' ) );
}

function add_missing_dependency_error( $message ) {
	$class   = 'notice notice-error';

	printf( '<div class="%1$s"><p>%2$s</p></div>', $class, $message );
}

add_action( 'rest_api_init', __NAMESPACE__ . '\\init_endpoint' );

function init_endpoint() {
	register_rest_route( 
		'relativemarketing/newsletter/v1',
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

	/**
	 * Currently we only want to show the popup on single posts
	 * only (excluding single custom post type posts)
	 */
	if ( ! is_singular( 'post' ) ) return;

	echo '<div id="relative-newsletter-signup"></div>';
}

/**
 * Load the scripts and styles for this plugin
 */
add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\\load_newsletter_scripts' );

function load_newsletter_scripts() {
	if ( ! is_singular( 'post' ) || ! Helpers\has_sharpspring_endpoints_plugin() )
		return;

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
