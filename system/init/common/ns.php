<?php

/**
 * Common function
 *
 * @category	Eventing
 * @package		Common
 * @subpackage	NS
 * @see			/index.php
 */

	if(!defined('E_FRAMEWORK')) {
		headers_sent() || header('HTTP/1.1 404 Not Found', true, 404);
		exit('Direct script access is disallowed.');
	}

	if(!function_exists('ns')) {
		/**
		 * Namespace String
		 *
		 * @access public
		 * @params strings
		 * @return string
		 */
		function ns() {
			return '\\' . implode('\\', func_get_args()) . '\\';
		}
	}