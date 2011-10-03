<?php

/**
 * Common function
 *
 * @category	Eventing
 * @package		Common
 * @subpackage	Show404
 * @see			/index.php
 */

	if(!defined('E_FRAMEWORK')) {
		headers_sent() || header('HTTP/1.1 404 Not Found', true, 404);
		exit('Direct script access is disallowed.');
	}

	if(!function_exists('show_404')) {
		/**
		 * Show 404
		 *
		 * Shortcut function to the show_error() function.
		 *
		 * @access public
		 * @return void
		 */
		function show_404() {
			show_error(
				'404 Not Found',
				'The page you requested does not exist.'
			);
		}
	}