<?php

/**
 * Eventing Framework
 *
 * Eventing PHP Framework by Zander Baldwin <mynameiszanders@gmail.com>.
 * The Eventing Framework is an object-orientated PHP Framework, designed to rapidly build applications. Since this is
 * now part of the LessHub project, it will most like end up as a mashup; an application framework with a content management system fallback.
 *
 * Many concepts and ideas of this framework are inspired from CodeIgniter. All code is rewritten from scratch, but
 * please see their license: http://codeigniter.com/user_guide/license.html
 *
 * This is the file that is called by the server when a request is made to your application.
 *
 * @category	Eventing
 * @package		Core
 * @author		Zander Baldwin
 * @copyright	(c) 2009 - 2011 Zander Baldwin
 * @license		MIT/X11 License
 * @version		v0.4
 * @link		http://github.com/mynameiszanders/eventing
 * @since		v0.1
 */

	# ========================================================================== #
	# Please edit the following settings depending on your folder structure and  #
	# preferences. If you are going to keep the default values here, you might   #
	# as well remove this array. All other editable system settings can be found #
	# in "<system_folder>/config/".                                              #
	# ========================================================================== #

	$user_config = array(
		// Can be "ini" or "array". Defaults to "array".
		'config_type'     => 'array',
		'content_folder'  => 'public',
		'default_app'     => 'app',
		'modules_folder'  => 'modules',
		'system_folder'   => 'system',
		'skeleton'        => false,
	);

	# ========================================================================== #
	# End of user configurable settings.                                         #
	# ========================================================================== #

	// Killing two birds with one stone. Define a constant for other files to
	// check that they're not being called independently, and to set the core
	// benchmark. The E_FRAMEWORK constant is REQUIRED!
	defined('E_FRAMEWORK') || define('E_FRAMEWORK', microtime(true));

	// We have set the initial time in our E_FRAMEWORK constant, so why not set
	// the initial memory consumption? It will enable the Output library to make a
	// more accurate calculation on how much memory the application used.
	defined('E_MEMORY') || define('E_MEMORY', memory_get_usage());

	// However, the version number doesn't really have any use in the framework at
	// all. You may discard it if you want. I'm not very good at versions. I never
	// remember to update the version number, so I'm just going to stick with
	// "pre" or "post" public releases.
	defined('E_VERSION') || define('E_VERSION', 'Alpha1-PrePublicRelease');

	// Right! Let's get this party started!
	$init = isset($user_config['system_folder'])
		? $user_config['system_folder'] . '/init.php'
		: 'system/init.php';

	// For obvious reasons, let's check if the initialisation script exists...
	// If it doesn't, we'll give the user a big slap in the face of epic
	// disappointment by calling the exit function. SAD TIMES!
	($init = realpath($init)) || exit('Initialisation script not found.');

	// Great... Everything working so far. What a boring life we live!
	// Don't forget to say which file is calling the script.
	// We are not going to define this as a constant because we can just use:
	// $main_file = BASEPATH . SELF;
	// to get it again.
	$main_file = __FILE__;
	require_once $init;
