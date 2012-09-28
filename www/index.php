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
 * @license		MIT/X11 <http://j.mp/mit-license>
 * @link		http://github.com/lesshub/eventing
 * @version		$VERSION
 */

	/**
	 * Folder structure and preferences.
	 *
	 * Please edit the following settings depending on your folder structure and preferences.
	 * It is not advised that you follow these values. Your system and application directories should be outside of your
	 * web root, whereas the content folder should stay visible within the web root.
	 *
	 * If you are going to keep the default values here, it doesn't matter if you remove this array.
	 * All other editable system settings can be found in "<system_folder>/config/*.php".
	 */
	$user_config = array(
		// Can be "ini" or "array". Defaults to "array".
		'config_type'     => 'array',
		'content_folder'  => '../www/public',
		'default_app'     => '../eventing/app',
		'modules_folder'  => '../eventing/modules',
		'system_folder'   => '../eventing/system',
		'skeleton'        => false,
	);

	/* END OF USER CONFIGURABLE SETTINGS. */

	// Killing two birds with one stone. Define a constant for other files to check that they're not being called
	// independently, and to set the core benchmark. The E_FRAMEWORK constant is REQUIRED!
	defined('E_FRAMEWORK') || define('E_FRAMEWORK', microtime(true));

	// We have set the initial time in our E_FRAMEWORK constant, so why not set the initial memory consumption? It will
	// enable the Output library to make a more accurate calculation on how much memory the application used.
	// This is not required, but recommended for the Output library.
	defined('E_MEMORY') || define('E_MEMORY', memory_get_usage());

	// This is usually where a PHP framework will define its version number. However, the only reference to a  version
	// number will be in this files DocComment; there is no point declaring it in the code as there are no feature
	// dependancies in this framework at the moment. Instead, versioning will be handles by Git in this project.

	// Right! Let's get this party started!
	$init = isset($user_config['system_folder'])
		? $user_config['system_folder'] . '/init.php'
		: 'system/init.php';

	// For obvious reasons, let's check if the initialisation script exists... If it doesn't, we'll give the user a big
	// slap in the face of epic disappointment by calling the exit function. SAD TIMES!
	($init = realpath($init)) || exit('Initialisation script not found.');

	// Great... Everything working so far. What a boring life we live! Don't forget to say which file is calling the
	// script. We are not going to define this as a constant because we can just use BASEPATH.SELF; to get it again.
	$main_file = __FILE__;
	require_once $init;