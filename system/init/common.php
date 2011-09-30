<?php

/**
 * Common functions
 *
 * Define common helper functions that will be used throughout the framework.
 *
 * @category	Eventing
 * @package		Init
 * @subpackage	Common
 * @see			/index.php
 */

	if (!defined('E_FRAMEWORK')) {
		headers_sent() || header('HTTP/1.1 404 Not Found', true, 404);
		exit('Direct script access is disallowed.');
	}

	// Define a list of functions that should be loaded from the common functions directory.
	$common_functions = glob(SYS . 'init/common/*' . EXT);
	// Iterate through the list, and terminate the application if any of them cannot be loaded.
	foreach($common_functions as $function) {
		file_exists($function) || !is_readable($function) || trigger_error(
			'Unable to load common function "' . basename($function, EXT) . '". Terminating application.',
			E_USER_ERROR
		);
		require_once $function;
	}

	/**
	 * Post loading logic
	 *
	 * Any logic that requires the common functions to be loaded should be declared here. Anything that is not to do
	 * with the loading or configuration of common functions should go back into the initialisation script.
	 */

	// Set PHP's error handler to the Eventing error handler.
	set_error_handler('eventing_error_handler');

	// Define the default suffix, so that we know what to use incase one isn't
	// given in the application URI or any eURI's.
	defined('DEFAULTSUFFIX') || define(
		'DEFAULTSUFFIX',
		is_string($s = c('default_suffix'))
		&& preg_match('/^\.[a-zA-Z0-9]+$/', $s)
			? strtolower($s)
			: '/'
	);

	// If we don't do this, PHP (we use versions above 5.2 remember?) will throw a
	// little tantrum. Let's keep it happy :)
	// You can change this in your controller, or a future library (hopefully!)
	$default_timezone = c('default_timezone')
		? c('default_timezone')
		: 'Europe/London';
	date_default_timezone_set($default_timezone);