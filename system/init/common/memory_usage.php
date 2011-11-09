<?php

/**
 * Common function
 *
 * @category	Eventing
 * @package		Common
 * @subpackage	MemoryUsage
 * @see			/index.php
 */

	if(!defined('E_FRAMEWORK')) {
		headers_sent() || header('HTTP/1.1 404 Not Found', true, 404);
		exit('Direct script access is disallowed.');
	}

	if(!function_exists('memory_usage')) {
		/**
		 * Memory Usage
		 *
		 * Return the 
		 *
		 * @param  string|float $start
		 * @return false|float
		 */
		function memory_usage($start = E_MEMORY, $raw = false) {
			if(!is_int($start)) {
				return false;
			}
			// Grab the memory usage since the beginning of the application.
			$memory_usage = memory_get_usage() - $start;
			if($raw) {
				return $memory_usage;
			}
			$shorthand = round(
				$memory_usage / pow(1024, 2),
				3
			) . 'Mb';
			return $shorthand;
		}
	}