<?php

namespace RelativeMarketing\Newsletter\Helpers;

/**
 * Check for the availablility of the sharpspring endpoints plugin
 * 
 * This is just a wrapper for rest_namespace_exists but provides the
 * namespace for the sharpspring endpoints plugin
 */
function has_sharpspring_endpoints_plugin() {
	return in_array( 'relativemarketing/sharpspring/v1', \rest_get_server()->get_namespaces() );
}