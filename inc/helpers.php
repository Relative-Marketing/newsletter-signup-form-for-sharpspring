<?php

namespace RelativeMarketing\Newsletter\Helpers;

/**
 * Check for the existance of a given namespace
 *
 * Adapted from https://wpscholar.com/blog/internal-wp-rest-api-calls/
 *
 * @param  string $namespace The namespace to check for e.g 'examplenamespace/v1'
 * @return bool              Whether the given namespace exists
 */
function rest_namespace_exists( string $namespace ) {
  // Create a rest request for the current rest url of the site
	$request = \WP_REST_Request::from_url( get_rest_url( null, '' ) );
	$response = rest_do_request( $request );
	$server = rest_get_server();
	$data = $server->response_to_data($response, false);
	return array_search( $namespace, $data['namespaces'] ) !== false;
}

/**
 * Check for the availablility of the sharpspring endpoints plugin
 * 
 * This is just a wrapper for rest_namespace_exists but provides the
 * namespace for the sharpspring endpoints plugin
 */
function has_sharpspring_endpoints_plugin() {
	return rest_namespace_exists( 'relativemarketing/sharpspring/v1' );
}