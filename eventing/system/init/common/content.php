<?php

/**
 * Common function
 *
 * @category	Eventing
 * @package		Common
 * @subpackage	Content
 * @see			/index.php
 */

	if(!defined('E_FRAMEWORK')) {
		headers_sent() || header('HTTP/1.1 404 Not Found', true, 404);
		exit('Direct script access is disallowed.');
	}

	if(!function_exists('content')) {
		/**
		 * Content URL
		 *
		 * Takes a file path, relative to the content folder, checks that it exists,
		 * and returns the absolute URL. If $force is set to true, it will return
		 * the path regardless of whether the file exists (content path still needs
		 * to be set).
		 *
		 * @access public
		 * @param string $file
		 * @param boolean $force
		 * @return false|string
		 */
		function content($file, $force = false) {
			if(is_null(CONTENTPATH) || is_null(CONTENT)) {
				return false;
			}
			$file = trim(preg_replace('|/+|', '/', str_replace('\\', '/', $file)), '/');
			$url = CONTENT . $file;
			if($force) {
				return $url;
			}
			$path = CONTENTPATH . $file;
			return file_exists($path) ? $url : false;
		}
	}