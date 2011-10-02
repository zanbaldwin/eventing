<?php

/**
 * Common function
 *
 * @category	Eventing
 * @package		Common
 * @subpackage	Is AJAX?
 * @see			/index.php
 */

	if (!defined('E_FRAMEWORK')) {
		headers_sent() || header('HTTP/1.1 404 Not Found', true, 404);
		exit('Direct script access is disallowed.');
	}

	if(!function_exists('is_ajax')) {
		/**
		 * Is AJAX?
		 *
		 * Returns a boolean value depending on whether the request was via an AJAX call or not. AJAX is now common
		 * practice in modern web applications, and knowing the difference between an AJAX or Browser request is
		 * extremely important in transparent URL fetching.
		 *
		 * @access public
		 * @return boolean
		 */
		function is_ajax($path, $title = false, $options = array()) {
			static $is_ajax = null;
			if(is_null($is_ajax)) {
				$is_ajax = isset($_SERVER['HTTP_X_REQUESTED_WITH'])
					&& $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest';
			}
			return $is_ajax;
		}
	}