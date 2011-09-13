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

	// This framework now requires PHP5.3 for quite a lot of functionality. If we
	// are running anything less, terminate.
	if(PHP_VERSION_ID < 50300) {
		show_error(
			'This installation of PHP is running version ' . PHP_VERSION
			. ', but this framework requires version 5.3.0 or greater.'
		);
	}

	require_once "common/error_handler.php";

	// This is against standard practice, to set error reporting to full, especially
	// for production, but in truth, if you don't want errors coming up in your
	// applications, start writing better code!
	error_reporting(-1);
	ini_set('display_errors', 1);

	// Set PHP's error handler to the Eventing error handler.
	set_error_handler('eventing_error_handler');

	require_once "common/binary_parts.php";

	require_once "common/ns.php";

	require_once "common/load_class.php";

	require_once "common/getinstance.php";

	require_once "common/bool.php";

	require_once "common/bl.php";

	require_once "common/get_config.php";

	require_once "common/c.php";

	require_once "common/filter_path.php";

	require_once "common/show_error.php";

	require_once "common/show_404.php";

	require_once "common/show_deny.php";

	require_once "common/show_teapot.php";

	require_once "common/show_doc.php";

	require_once "common/uri.php";

	require_once "common/a.php";

	require_once "common/theme_path.php";

	require_once "common/get_themes.php";

	require_once "common/content.php";

	require_once "common/redirect.php";

	require_once "common/vardump.php";

	require_once "common/xplode.php";

	require_once "common/elapsed_time.php";

	require_once "common/copyright.php";

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