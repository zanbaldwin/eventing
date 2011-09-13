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

	// Make sure we have everything to start initialising the framework with, so we can run the application.
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
	//of the matter is, if you don't want errors coming up in your applications, start writing better code!
	error_reporting(-1);
	ini_set('display_errors', 1);

	// Set the defaults for the user index config array.
	$main_config = array(
		'config_type'     => 'array',
		'content_folder'  => 'public',
		'default_app'     => 'app',
		'modules_folder'  => 'modules',
		'system_folder'   => 'system',
		'skeleton'        => false,
	);

	// Incorporate the user's settings into the default settings. We trust the
	// user to have all the right stuff there... Well, almost...
	if(is_array($user_config)) {
		foreach ($user_config as $key => $value) {
			if(array_key_exists($key, $main_config)) {
				$main_config[$key] = is_string($value) ? strtolower($value) : $value;
			}
		}
	}

	// Bring them... ALIVE!!!
	@extract($main_config);

	// CONSTANTS.
	$constants = rtrim(str_replace('\\', '/', dirname(__FILE__)), '/') . '/init/constants.php';
	file_exists($constants) || trigger_error('Constant declarations could not be loaded.', E_USER_ERROR);
	require_once $constants;

	// Right, we have all out constants defined, with no loose variables floating
	// about... I think we're doing pretty well! Shall we load some common
	// functions? Let's!
	$common = SYS . 'init/common' . EXT;
	file_exists($common) || trigger_error('Common functions could not be loaded.', E_USER_ERROR);
	require_once $common;

	// Cool. We have functions. Now we want libraries! Big, fat, juicy libraries!

	// Firstly, we want the Singleton and Library libraries. These are the classes
	// that force you to grab existing instances of Eventing libraries, rather
	// than create new ones.
	load_class('library', false);
	// Load both Controller and Module class definitions, because we don't know at
	// this point whether we are loading a controller from the main application or
	// a module.
	load_class('controller', false);
	// If we are in skeleton mode, don't enable the use of modules.
	SKELETON || load_class('module', false);
	// Load the model library, so it can be extended when we load a model.
	load_class('model', false);
	// Load the Router library and grab an instance.
	$r = load_class('router');

	// We want to know what request this application is meant to serve!
	if(!is_object($r) || !$r->valid || $r->module()) {
		show_404();
	}

	// Make sure that the path has been set, and then include the controller file.
	$r->path() && require_once $r->path();

	// Make sure the controller class exists.
	class_exists($r->controller()) || show_404();

	// Check that the action we want to call exists.
	method_exists($r->controller(), $r->method())
		|| in_array($r->method(), get_class_methods($r->controller()), true)
		|| show_404();

	// Now check if the action we want to call is public. This requires the use of
	// PHP's Reflection extension. It's not essential, so carry on if it doesn't
	// exist. It's just a little more friendly to show our custom 404 page than
	// the user get a fatal error stating the ReflectionMethod class does not
	// exist.
	if(!SKELETON && class_exists('\\ReflectionMethod')) {
		$reflection = new \ReflectionMethod($r->controller(), $r->method());
		$reflection->isPublic() || show_404();
	}

	$c = $r->controller();
	$m = $r->method();

	// Unset all unecessary variables before we call the controller.
	unset(
		$common, $u, $modules, $mod, $uri_string, $uri, $segment, $r,
		$controller_path, $controller_file
	);

	// Everything checks out as far as we can tell here. Grab a new instance of
	// the controller, and then call the action.
	$c = $c::getInstance();
	$c->$m();

	// Right, that's everything done! Just dump the output to the client end
	// finish the script!
	$E =& getInstance();
	$E->output->display();

	//  _  _________ _    ___   ______          _____ _
	// | |/ /__   __| |  | \ \ / /  _ \   /\   |_   _| |
	// | ' /   | |  | |__| |\ V /| |_) | /  \    | | | |
	// |  <    | |  |  __  | > < |  _ < / /\ \   | | | |
	// | . \   | |  | |  | |/ . \| |_) / ____ \ _| |_|_|
	// |_|\_\  |_|  |_|  |_/_/ \_\____/_/    \_\_____(_)