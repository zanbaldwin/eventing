<?php

/**
 * Initialise constants
 *
 * Calculate and define all the constants that the framework will be using in this file.
 *
 * @category	Eventing
 * @package		Init
 * @subpackage	Constants
 * @see			/index.php
 */

	if(!defined('E_FRAMEWORK')) {
		headers_sent() || header('HTTP/1.1 404 Not Found', true, 404);
		exit('Direct script access is disallowed.');
	}

	$c = array();

	// We have a dependant on $_SERVER['DOCUMENT_ROOT']. Unfortunately, some OS's don't set this *cough* Windows *cough*.
	if(!isset($_SERVER['DOCUMENT_ROOT'])) {
		if(isset($_SERVER['SERVER_SOFTWARE'])
		   && strpos($_SERVER['SERVER_SOFTWARE'], 'Microsoft-IIS') === 0
		) {
			$path_length = strlen($_SERVER['PATH_TRANSLATED'])
						 - strlen($_SERVER['SCRIPT_NAME']);
			$_SERVER['DOCUMENT_ROOT'] = rtrim(
				substr($_SERVER['PATH_TRANSLATED'], 0, $path_length),
				'\\'
			);
		}
	}

	// File and System Constants.
	$c['config'] = strtolower($config_type) == 'ini' ? 'ini' : 'array';
	$c['self'] = basename($main_file);
	$c['ext'] = explode('.', $c['self']);
	$c['ext'] = '.' . end($c['ext']);

	// URL Constants.
	$c['server'] = (isset($_SERVER['HTTPS']) || $_SERVER['SERVER_PORT'] == 443)
		? 'https://'.$_SERVER['SERVER_NAME']
		: 'http://'.$_SERVER['SERVER_NAME'];
	$c['url'] = preg_replace(
		'|/+|',
		'/',
		'/' . trim(
			str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])),
			'/'
		) . '/'
	);
	$c['baseurl']  = $c['server'].$c['url'];

	// Directory Constants.
	$c['basepath'] = rtrim(
		str_replace('\\', '/', realpath(dirname($main_file))),
		'/'
	) . '/';
	$c['sys'] = rtrim(
		str_replace('\\', '/', realpath($system_folder)),
		'/'
	) . '/';
	$c['app'] = rtrim(str_replace('\\', '/', realpath($default_app)), '/') . '/';
	$c['mod'] = realpath($modules_folder);
	$c['mod'] = is_string($c['mod'])
		? rtrim(str_replace('\\', '/', $c['mod']), '/') . '/'
		: null;
	$c['contentpath'] = rtrim(
		str_replace('\\', '/', realpath($content_folder)),
		'/'
	) . '/';
	// Check that the content directory is a sub-directory of the web root. If it
	// is not, set it as null.
	$c['content'] = null;
	if(is_string($_SERVER['DOCUMENT_ROOT'])) {
		$len = strlen($_SERVER['DOCUMENT_ROOT']);
		if(substr($c['contentpath'], 0, $len) == $_SERVER['DOCUMENT_ROOT']) {
			$c['content'] = trim(substr($c['contentpath'], $len), '/');
			$c['content'] = $c['content']
				? '/' . $c['content'] . '/'
				: '/';
			$c['content'] = $c['server'] . $c['content'];
		}
	}
	// If the contenturl cannot be established, or it is outside the web root,
	// there is no point having the content path.
	if(is_null($c['content'])) {
		$c['contentpath'] = null;
	}

	// Define our list of namespaces used throughout our framework.
	$c['ns']			= 'Eventing';
	$c['nslibrary']		= 'Library';
	$c['nscontroller']	= 'Application';
	$c['nsmodel']		= 'Model';
	$c['nsmodule']		= 'Module';

	// Define a PCRE RegEx for a valid label in PHP. This check a string to make
	// sure that it follows the same syntax as variables, functions and class
	// names.
	$c['validlabel']	= '[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*';

	// Do we want to run an instance of the framework with stripped down
	// functionality, to make it faster and use less resources?
	$c['skeleton']		= $skeleton ? true : false;

	// All our constants are really great, but they're a little soft at the
	// moment... Shall we make them hardcore?
	foreach ($c as $name => $const) {
		$name = strtoupper($name);
		defined($name) || define($name, $const);
	}

	// You know what? I've had enough of you lot... Yeah, you heard me! Get lost!
	unset(
		$main_config, $user_config, $key, $value, $system_folder, $default_app,
		$content_folder, $skeleton_mode, $config_type, $c, $name, $const,
		$modules_folder
	);