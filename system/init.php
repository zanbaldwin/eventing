<?php

/**
 * Initialisation Script
 *
 * This is where we start all our settings, libraries and other odd-jobs to get
 * the ball rolling...
 *
 * @category	Eventing
 * @package		Init
 * @subpackage	Core
 * @see			/index.php
 */

	/*
	 * Prerequisites
	 * =============
	 * Make sure we have everything to start initialising the framework with, so we can run the application. This is
	 * mainly just the index file reference, the correct PHP version, error reporting, etc.
	 */

	defined('E_FRAMEWORK') || trigger_error(
		'E_FRAMEWORK has not been defined.',
		E_USER_ERROR
	);
	isset($main_file) || trigger_error(
		'Main file is not specified.',
		E_USER_ERROR
	);
	file_exists($main_file) || trigger_error(
		'The main file does not exist.',
		E_USER_ERROR
	);

	// This framework now requires PHP5.3 for quite a lot of functionality. If we are running anything less, terminate.
	if(PHP_VERSION_ID < 50300) {
		trigger_error('This installation of PHP is running version ' . PHP_VERSION . ', but this framework requires version 5.3.0 or greater.', E_USER_ERROR);
	}

	// This is against standard practice, to set error reporting to full, especially for production. However, the truth
	// of the matter is, if you don't want errors coming up in your applications, start writing better code!
	error_reporting(-1);
	ini_set('display_errors', 1);

	/*
	 * Directory structure and preferences
	 * ===================================
	 * Merge the users index configuration with the defaults defined here and extract for use later. Well, not later,
	 * we're actually going to be using them next...
	 * We supply defaults because we don't trust the user to be able to keep the settings, or to not add any of their own.
	 */

	$main_config = array(
		'config_type'     => 'array',
		'content_folder'  => 'public',
		'default_app'     => 'app',
		'modules_folder'  => 'modules',
		'system_folder'   => 'system',
		'skeleton'        => false,
	);
	if(is_array($user_config)) {
		foreach ($user_config as $key => $value) {
			if(array_key_exists($key, $main_config)) {
				$main_config[$key] = is_string($value) ? strtolower($value) : $value;
			}
		}
	}

	// Bring them... ALIVE!!!
	@extract($main_config);

	/*
	 * Constants
	 * =========
	 * Calculate the system-side constants based upon environmental variables and the values previously extracted from
	 * the index configuration.
	 */

	$constants = rtrim(str_replace('\\', '/', dirname(__FILE__)), '/') . '/init/constants.php';
	file_exists($constants) || trigger_error('Constant declarations could not be loaded.', E_USER_ERROR);
	require_once $constants;

	/*
	 * Common functions
	 * ================
	 * Since we have all our constants defined with any loose variables floating around dealt with, load the small-ish
	 * set of common functions into the global namespace so they can be used throught the entire framework and
	 * application.
	 */

	$common = SYS . 'init/common' . EXT;
	file_exists($common) || trigger_error('Common functions could not be loaded.', E_USER_ERROR);
	require_once $common;

	/*
	 * Unset variables
	 * ===============
	 * Remove any variables that we are no longer going to use. They will just clog up the global namespace.
	 */

	unset(
		$common, $common_functions, $constants, $default_timezone, $function, $function_file, $init, $len, $main_file,
		$s, $skeleton
	);

	/*
	 * Libraries
	 * =========
	 * We have constants and functions. Now we need some higher-level, advanced functionality. We need libraries; big,
	 * juicy libraries. The ones loaded here are the ones required for any application request. If you wish to go into
	 * specific functionality, load the extra libraries in your controllers or autoload them.
	 * Unless we want to assign the library to a variable to use, such as the router, specify bool(false) as the second
	 * parameter to prevent the function from creating an instance of the class. This would prevent the framework from
	 * working in most cases.
	 */

	// Define the core libraries that have to be loaded, the library library MUST be the FIRST library declared - all
	// others depend on it.
	$sponge = array('library', 'controller', 'model');
	// Define the libraries that are required, but are not necessary for the bare minimum to run the framework.
	$jam = array('module', 'database');
	// Imagine the first lot of libraries as Victoria sponge; it's not a cake without cake. The next lot of arrays are
	// the jam, they just make it that much nicer to eat, but isn't a necessity. Any libraries loaded from within the
	// application controllers themselves are the icing and the cherry on top.
	foreach($sponge as $lib) {
		load_class($lib, false);
	}
	if(!SKELETON) {
		foreach($jam as $lib) {
			load_class($lib, false);
		}
	}

	// Load the Router library and grab an instance. This library performs the job of URI and Router library in one.
	$r = load_class('router');

	/*
	 * Load required modules
	 * =====================
	 * Load any modules that will be used by the applications for either controllers or pages. If they are specific to
	 * one or the other, load them when we have figured out what is being loaded.
	 */

	// Load the registry module.

	/*
	 * Valid request
	 * =============
	 * If the application request is not valid (eg. malformed URL), they show a 404 error immediately.
	 */

	if(!is_object($r) || !$r->valid) {
		show_404();
	}

	/*
	 * Static or dynamic?
	 * ==================
	 * We need to determine whether the request is for a controller (dynamic) or a potential page (static). We
	 * automatically assume that it is for a controller and then fallback to the Pages module when any of the following
	 * criteria are not met:
	 *	- The Router is an object.
	 *	- The route is valid.
	 *	- The request is not for a module.
	 *	- The path, controller and method are all strings.
	 *	- The path exists, as does the controller class.
	 *	- The method is a public method of the controller class.
	 */

	$controller = !$r->module()
		&& is_string($r->path())
		&& is_string($r->controller())
		&& is_string($r->method())
		&& file_exists($r->path());
	if($controller) {
		require_once $r->path();
		$c = $r->controller();
		$m = $r->method();
		$controller = class_exists($c) && (method_exists($c, $m) || in_array($m, get_class_methods($c, true)));
		if($controller) {
			$reflection = new \ReflectionMethod($c, $m);
			$controller = $reflection->isPublic();
		}
	}

	/*
	 * Load controller
	 * ===============
	 * If the application request was for a controller, the class would have already been loaded into PHP's memory. Grab
	 * an instance of it and run the method.
	 * IMPORTANT! The global getInstance() function MUST NOT be called before the getInstance() method has been called
	 * on the controller. This will create two seperate instances; one for the controller, and another that the
	 * libraries will be loaded onto!
	 */

	if($controller) {
		$c = $c::getInstance();
		$c->$m();
	}

	/*
	 * Load page
	 * =========
	 * The application request points to a controller that does not exist. Attempt to use the Pages module to load a
	 * static page. If a page does not exist (the Pages module returns false when trying to load the re-routed URI),
	 * show a 404 page.
	 */

	elseif(!SKELETON) {
		// Can you load modules?
		// Does the Pages module exist?
		// Load the Pages module: Pages = getInstance()->load->module('pages');
		// Does the Pages::load() method exist?
		// If so, load the re-routed URI string: Pages::load($r->ruri_string());
		// Else show a 404 page.
		# For now, the pages module does not exist, so just show a 404 page.
		show_404();
	}
	else {
		show_404();
	}

	/*
	 * Display output
	 * ==============
	 * Now that we have either shown a controller, or a page, flush the output from the Output library to the browser as
	 * the last thing to do before terminating.
	 */

	getInstance()->output->display();

	# =================================================== #
	#   _  _________ _    ___   ______          _____ _   #
	#  | |/ /__   __| |  | \ \ / /  _ \   /\   |_   _| |  #
	#  | ' /   | |  | |__| |\ V /| |_) | /  \    | | | |  #
	#  |  <    | |  |  __  | > < |  _ < / /\ \   | | | |  #
	#  | . \   | |  | |  | |/ . \| |_) / ____ \ _| |_|_|  #
	#  |_|\_\  |_|  |_|  |_/_/ \_\____/_/    \_\_____(_)  #
	#                                                     #
	# =================================================== #