<?php

/**
 * Common function
 *
 * @category	Eventing
 * @package		Common
 * @subpackage	ShowDeny
 * @see			/index.php
 */

	if(!defined('E_FRAMEWORK')) {
		headers_sent() || header('HTTP/1.1 404 Not Found', true, 404);
		exit('Direct script access is disallowed.');
	}

	if(!function_exists('show_deny')) {
		/**
		 * Show Deny
		 *
		 * Shortcut function to call the show_error() function, to either display an error document, or if one does not
		 * exist, display an XML error.
		 *
		 * @access public
		 * @return void
		 */
		function show_deny() {
			show_error(
				'403 Forbidden',
				'You do not have sufficient clearance to view this page.'
			);
		}
	}