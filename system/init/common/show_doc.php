<?php

/**
 * Common function
 *
 * @category	Eventing
 * @package		Common
 * @subpackage	ShowDoc
 * @see			/index.php
 */

	if(!defined('E_FRAMEWORK')) {
		headers_sent() || header('HTTP/1.1 404 Not Found', true, 404);
		exit('Direct script access is disallowed.');
	}

	if(!function_exists('show_doc')) {
		/**
		 * Show Error Document
		 *
		 * Supply it with a HTTP Status Code integer, and it will go check if the user
		 * has defined a special error document for that status code.
		 * The function will return false if the document does not exist or headers
		 * have already been sent (the document will get mixed up with parts of the
		 * page that have already been served).
		 *
		 * @param int $error_number
		 * @return exit|false
		 */
		function show_doc($error_number) {
			$file = theme_path('errors') . (string) $error_number . EXT;
			if(headers_sent() || !file_exists($file)) {
				return false;
			}
			require $file;
			exit;
		}
	}