<?php

/**
 * Common function
 *
 * @category	Eventing
 * @package		Common
 * @subpackage	ElapsedTime
 * @see			/index.php
 */

	if (!defined('E_FRAMEWORK')) {
		headers_sent() || header('HTTP/1.1 404 Not Found', true, 404);
		exit('Direct script access is disallowed.');
	}

	if(!function_exists('elapsed_time')) {
		/**
		 * Elapsed Time
		 *
		 * Return the elapsed time in seconds, between the time specified time
		 * passed to the function (must be the return of the function microtime)
		 * and now.
		 *
		 * @param  string|float $start
		 * @return false|float
		 */
		function elapsed_time($start = E_FRAMEWORK) {
			// Grab the time now, so we can compare.
			$end = microtime(true);
			// The user probably passed the microtime as a string.
			$regex = '/^0\\.([0-9]+) ([0-9]+)$/';
			if (is_string($start)) {
				$start = preg_match($regex, $start)
					? (float) preg_replace($regex, '$2.$1', $start)
					: false;
			}
			// We should also check the end time, because microtime(true) will
			// return a string is PHP is less than 5.
			if (is_string($end)) {
				$end = preg_match($regex, $end)
					? (float) preg_replace($regex, '$2.$1', $end)
					: false;
			}
			if (!is_float($start) || !is_float($end)) {
				return false;
			}
			$elapsed_time = round($end - $start, 3);
			return $elapsed_time;
		}
	}