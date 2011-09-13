<?php

/**
 * Common function
 *
 * @category	Eventing
 * @package		Common
 * @subpackage	LoadClass
 * @see			/index.php
 */

	if (!function_exists('load_class')) {
		/**
		 * Load Class
		 *
		 * For loading classes/libraries. Objects in PHP are returned by reference
		 * anyway, so we don't need to declare that part. This also saves on the
		 * amount of strict (2048) warnings when returning a boolean (eg. the
		 * library doesn't exist).
		 *
		 * @param string $lib
		 * @param bool $return
		 * @return boolean|object
		 */
		function load_class($identifier, $return = true) {
			// Create a static array to house our loaded files in - we don't want to
			// include a class definition twice now, do we?
			static $files = array();
			// No point continuing with the function if no library has been specified.
			if(!is_string($identifier)) {
				return false;
			}
			$return = bool($return);
			// Check if the specified library is an empty string and that it adheres
			// to the "module:path/to/library" syntax.
			$regex = '#^(([a-zA-Z_][a-zA-Z0-9_]*)?@)?([a-zA-Z_][a-zA-Z0-9_/]*)$#';
			if(!$identifier || !preg_match($regex, $identifier, $parts)) {
				return false;
			}
			// Assign our parts to something a little more human-readable.
			$module = $parts[2] ? strtolower($parts[2]) : false;
			// Filter our library string to all lowercase, no separators on the ends
			// of the string or doubled up.
			$lib = trim(filter_path(strtolower($parts[3])), '/');
			// Since the identifier string check out? Let's rebuild it so that the
			// string will be identical to other calls to this function.
			$identifier = $module ? $module . ':' . $lib : $lib;
			// Now that we have a usable identifier string, the only useful
			// extractable information in the library string is the class name. Make
			// sure xplode() and end() are called as two separate statements as to
			// avoid an E_STRICT error.
			$class = xplode('/', $lib);
			$class = end($class);
			// Transform the library name to an absolute namespace and class
			// reference.
			$class = $module
				? ns(NS, NSMODULE, $module, NSLIBRARY) . $class
				: ns(NS, NSLIBRARY)                    . $class;
			// If we have already loaded the file in question, then return the
			// appropriate - we do not want to load the file again.
			if(isset($files[$identifier])) {
				// If load_class() failed the first time, then it's not going to work a
				// second time, is it?
				if(!$files[$identifier]) {
					return false;
				}
				// load_class() was successful last time! Now, did the function callee
				// want an instance returned?
				return $return ? $class::getInstance() : true;
			}
			// This must be the first call this function specifying this library.
			// Determine the file path, and include the file.
			$file = $module
				? MOD . $module . '/libraries/' . $lib . EXT
				: SYS . 'libraries/' . $lib . EXT;
			// If the file exists, return false, remembering to set the $files array
			// first, so that we can save ourselves the trouble of querying the
			// filesystem for every call to this library.
			if(!file_exists($file)) {
				$files[$identifier] = false;
				return false;
			}
			// Whack it in, baby!
			// This is where we hope the file actually doesn't contain anything to
			// screw up the framework, like inline HTML, or worse,
			require_once $file;
			// Check that the library class exists.
			if(!class_exists($class)) {
				$files[$identifier] = false;
				return false;
			}
			// If the function callee has specified that they do not want an instance
			// returned, just return true here.
			// Do NOT add anything to the $files array, because the library they
			// specified may not implement the getInstance() method!
			if(!$return) {
				return true;
			}
			if(!method_exists($class, 'getInstance')) {
				$files[$identifier] = false;
				return false;
			}
			// Well done! We have determined the library, the file it is contained in,
			// that the class exists, and that it implements a method called
			// getInstance()!
			//Add a true boolean to the $files array, and return an instance.
			$files[$identifier] = true;
			return $class::getInstance();
		}
	}