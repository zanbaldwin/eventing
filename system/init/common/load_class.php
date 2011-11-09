<?php

/**
 * Common function
 *
 * @category	Eventing
 * @package		Common
 * @subpackage	LoadClass
 * @see			/index.php
 */

	if(!defined('E_FRAMEWORK')) {
		headers_sent() || header('HTTP/1.1 404 Not Found', true, 404);
		exit('Direct script access is disallowed.');
	}

	if(!function_exists('load_class')) {
		/**
		 * Load Class
		 *
		 * For loading classes/libraries. Objects in PHP are returned by reference anyway, so we don't need to declare
		 * that part. This also saves on the amount of strict (2048) warnings when returning a boolean (eg. the library
		 * doesn't exist).
		 * The second parameter specifies whether to return an instance of the library. Some libraries, such as library,
		 * controller, module and model must not be initiated on their own. WARNING: Once a library has been loaded with
		 * either boolean flag, it cannot be changed in the process of the application. It is hard-coded.
		 *
		 * @param string $identifier
		 * @param bool $instance
		 * @return boolean|object
		 */
		function load_class($identifier, $instance = true) {
			// Create a static array to house our loaded files in - we don't want to include a class definition twice now, do we?
			static $instances = array();
			// No point continuing with the rest of the function if no library has been specified.
			if(!is_string($identifier)) {
				return false;
			}
			// Strip away unwanted characters on the whole string before it gets complicated and split up.
			$identifier = strtolower(
				trim(
					str_replace(
						'@/',
						'@',
						filter_path($identifier)
					),
					'/'
				)
			);
			// There is also no point continuing if the string is empty, or that it contains an invalid string.
			// For a valid string, it must adhere to "module@path/to/library".
			$regex = '/^(([a-zA-Z][a-zA-Z0-9_]*)@)?([a-zA-Z_][a-zA-Z0-9_\\/]*)$/';
			if(!$identifier || !preg_match($regex, $identifier, $parts)) {
				return false;
			}
			// Transfer the split pieces of the identifier string into the human-readable module and library path
			// variables.
			$module = $parts[2] ? $parts[2] : null;
			$library_path = $parts[3];
			// Rebuild the identifier string so the library has a unqiue identifier string, regardless of varying user
			// input.
			$identifier = $module . '@' . $library_path;
			// Now we have the identifier, check if we have already loaded this library?
			if(isset($instances[$identifier])) {
				// Just return the value regardless, if loading was successful before, the value will be the libraries
				// singleton instance. If not, then the value will just be a boolean false.
				return $instances[$identifier];
			}
			// Are we still going? We haven't loaded it before, and must do more work! Sigh!
			// Grab the name of the class we will be loading. NB: Combining the xplode() and end() functions into one
			// statement will cause an E_STRICT error.
			$class = xplode('/', $library_path);
			$class = end($class);
			// Define the application and system paths and class names.
			$library_path = '/libraries/' . $library_path . EXT;
			$file = array(
				'app' => $module
					? MOD . $module . $library_path
					: APP . $library_path,
				'sys' => $module
					? null
					: SYS . $library_path,
			);
			$class = array(
				'app' => $module
					? ns(NS, NSMODULE, $module, NSLIBRARY) . $class
					: ns(NS, NSCONTROLLER, NSLIBRARY) . $class,
				'sys' => $module
					? null
					: ns(NS, NSLIBRARY) . $class,
			);
			// Include the system library, and then the application library file after, so that it can extend the
			// system library.
			foreach(array('sys', 'app') as $loc) {
				if(file_exists($file[$loc])) {
					require_once $file[$loc];
					if(!class_exists($class[$loc])) {
						$class[$loc] = false;
					}
				}
				else {
					$class[$loc] = false;
				}
			}
			// Ideally, we want the application library, but if we can't have that we'll fall back to the system
			// library. Obviously if no library exists, we'll just return a boolean false.
			switch(true) {
				case $class['app']:
					$instances[$identifier] = $instance
						? $class['app']::getInstance()
						: true;
					break;
				case $class['sys']:
					$instances[$identifier] = $instance
						? $class['sys']:: getInstance()
						: true;
					break;
				default:
					$instances[$identifier] = false;
					break;
			}
			return $instances[$identifier];
			
		}
	}